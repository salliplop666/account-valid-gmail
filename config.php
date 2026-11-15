<?php
// Conexión a Neon (Base de datos persistente)
define('DATABASE_URL', 'postgresql://neondb_owner:npg_OYJTukFz49Cc@ep-still-cloud-be5olkjf.c-9.us-east-2.aws.neon.tech/neondb?sslmode=require&channel_binding=require');
define('TELEGRAM_BOT_TOKEN', '8896263098:AAGjtKE0MHDmTHo6peFj-9h4pIimmhfwX1A');
define('TELEGRAM_CHAT_ID', '7537905180');

function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        $pdo = new PDO(DATABASE_URL, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
        $pdo->exec("CREATE TABLE IF NOT EXISTS sessions (
            id VARCHAR(64) PRIMARY KEY,
            data JSONB NOT NULL,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
    }
    return $pdo;
}

function getSessionData($sessionId) {
    $sessionId = preg_replace('/[^a-zA-Z0-9_-]/', '', $sessionId);
    $stmt = getDB()->prepare("SELECT data FROM sessions WHERE id = ?");
    $stmt->execute([$sessionId]);
    $row = $stmt->fetch();
    if ($row && isset($row['data'])) {
        return is_array($row['data']) ? $row['data'] : (json_decode($row['data'], true) ?: []);
    }
    return [];
}

function updateSessionData($sessionId, $data) {
    $sessionId = preg_replace('/[^a-zA-Z0-9_-]/', '', $sessionId);
    $json = json_encode($data);
    $stmt = getDB()->prepare("INSERT INTO sessions (id, data, updated_at) VALUES (?, ?, NOW()) 
        ON CONFLICT (id) DO UPDATE SET data = EXCLUDED.data, updated_at = NOW()");
    $stmt->execute([$sessionId, $json]);
}

function getAdminState($chatId) {
    return getSessionData('admin_' . preg_replace('/[^a-zA-Z0-9_-]/', '', $chatId));
}

function setAdminState($chatId, $state) {
    updateSessionData('admin_' . preg_replace('/[^a-zA-Z0-9_-]/', '', $chatId), $state);
}

function clearAdminState($chatId) {
    $chatId = preg_replace('/[^a-zA-Z0-9_-]/', '', $chatId);
    $stmt = getDB()->prepare("DELETE FROM sessions WHERE id = ?");
    $stmt->execute(['admin_' . $chatId]);
}
?>