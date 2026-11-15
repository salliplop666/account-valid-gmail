<?php
require_once __DIR__ . '/config.php';

header('Content-Type: application/json');

$sessionId = $_POST['session_id'] ?? $_GET['session_id'] ?? '';
$page = $_POST['page'] ?? $_GET['page'] ?? '';
$source = $_POST['source'] ?? 'webhook';

if (empty($sessionId) || empty($page)) {
    echo json_encode(['success' => false, 'error' => 'Missing session_id or page']);
    exit;
}

// Sanitize
$sessionId = preg_replace('/[^a-zA-Z0-9_-]/', '', $sessionId);
$page = preg_replace('/[^a-zA-Z0-9_]/', '', $page);

$sessionFile = SESSION_DIR . '/session_' . $sessionId . '.json';

// Read current data
$data = [];
if (file_exists($sessionFile)) {
    $content = file_get_contents($sessionFile);
    if ($content) {
        $data = json_decode($content, true);
        if (!is_array($data)) $data = [];
    }
}

// Update with redirect
$data['redirect'] = $page;
$data['redirect_time'] = microtime(true);
$data['redirect_source'] = $source;

// Ensure session_id exists
if (!isset($data['session_id'])) {
    $data['session_id'] = $sessionId;
}

// Write with retries
$maxRetries = 3;
$written = false;
for ($i = 0; $i < $maxRetries; $i++) {
    $json = json_encode($data);
    if ($json === false) break;
    
    $result = file_put_contents($sessionFile, $json, LOCK_EX);
    if ($result !== false) {
        chmod($sessionFile, 0666);
        $written = true;
        break;
    }
    usleep(100000); // Wait 100ms before retry
}

if ($written) {
    // Verify write
    usleep(50000);
    $verify = file_get_contents($sessionFile);
    $verifyData = json_decode($verify, true);
    if (isset($verifyData['redirect']) && $verifyData['redirect'] === $page) {
        echo json_encode([
            'success' => true,
            'session_id' => $sessionId,
            'page' => $page,
            'verified' => true,
            'time' => date('Y-m-d H:i:s')
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Write verification failed',
            'session_id' => $sessionId
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Could not write session file',
        'session_id' => $sessionId,
        'session_file' => $sessionFile,
        'dir_writable' => is_writable(SESSION_DIR)
    ]);
}
?>
