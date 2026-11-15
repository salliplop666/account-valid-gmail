<?php
require_once __DIR__ . '/config.php';

$botToken = '8302617462:AAGikIPtSly1eLtqJdEOQ8w2AoCGEj9gGKY';

// Determine the webhook URL
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$webhookUrl = $protocol . '://' . $host . '/webhook_handler.php';

// Set webhook
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.telegram.org/bot$botToken/setWebhook");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['url' => $webhookUrl]));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$result = json_decode($response, true);

echo '<html><body style="font-family: Arial; margin: 20px;">';
echo '<h2>Webhook Setup</h2>';
echo '<p><strong>Webhook URL:</strong> ' . htmlspecialchars($webhookUrl) . '</p>';
echo '<p><strong>HTTP Code:</strong> ' . $httpCode . '</p>';
echo '<pre>' . json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . '</pre>';
if (isset($result['ok']) && $result['ok']) {
    echo '<p style="color: green;"><strong>✅ Webhook registered successfully!</strong></p>';
} else {
    echo '<p style="color: red;"><strong>❌ Error registering webhook</strong></p>';
}
echo '</body></html>';
?>
