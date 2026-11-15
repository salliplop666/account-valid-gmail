<?php
require_once __DIR__ . '/config.php';
$botToken = TELEGRAM_BOT_TOKEN;

$host = preg_replace('/:.*/', '', $_SERVER['HTTP_HOST']);
$webhookUrl = "https://$host/telegram_webhook.php";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.telegram.org/bot$botToken/setWebhook");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    'url' => $webhookUrl,
    'allowed_updates' => json_encode(['message', 'callback_query']),
    'drop_pending_updates' => 'true'
]));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$result = curl_exec($ch);
curl_close($ch);

echo "<h2>Webhook Setup</h2>";
echo "<p>URL: <code>$webhookUrl</code></p>";
echo "<pre>" . print_r(json_decode($result, true), true) . "</pre>";
?>