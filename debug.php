<?php
require_once __DIR__ . '/config.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

if ($action === 'sessions') {
    $sessions = [];
    if (is_dir(SESSION_DIR)) {
        $files = glob(SESSION_DIR . '/session_*.json');
        foreach ($files as $file) {
            $name = basename($file);
            $content = json_decode(file_get_contents($file), true);
            $sessions[$name] = [
                'email' => $content['email'] ?? '',
                'redirect' => $content['redirect'] ?? '',
                'size' => filesize($file),
                'modified' => date('Y-m-d H:i:s', filemtime($file))
            ];
        }
    }
    echo json_encode(['sessions' => $sessions, 'session_dir' => SESSION_DIR]);
    exit;
}

if ($action === 'webhook') {
    $last_webhook = @file_get_contents(SESSION_DIR . '/webhook_log.json');
    $webhooks = json_decode($last_webhook, true) ?: [];
    echo json_encode(['last_webhooks' => array_slice($webhooks, -10)]);
    exit;
}

if ($action === 'test') {
    $test_session = md5('test@example.com' . '127.0.0.1' . date('Y-m-d-H'));
    $test_data = [
        'email' => 'test@example.com',
        'redirect' => 'sms_code',
        'ip' => '127.0.0.1',
        'created' => time()
    ];
    updateSessionData($test_session, $test_data);
    
    $read_back = getSessionData($test_session);
    echo json_encode([
        'test_session_id' => $test_session,
        'written' => $test_data,
        'read_back' => $read_back,
        'match' => ($test_data === $read_back),
        'session_file' => getSessionFile($test_session),
        'file_exists' => file_exists(getSessionFile($test_session))
    ]);
    exit;
}

echo json_encode(['error' => 'Invalid action']);
?>
