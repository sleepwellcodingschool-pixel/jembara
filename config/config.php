<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'jembara_website');

// Site configuration
define('SITE_URL', 'http://localhost/jembararisetmedia');
define('ADMIN_URL', SITE_URL . '/admin');
define('UPLOAD_PATH', __DIR__ . '/../uploads/');
define('UPLOAD_URL', SITE_URL . '/uploads/');

// Security
define('SESSION_TIMEOUT', 3600); // 1 hour

// Include database class
require_once __DIR__ . '/database.php';

// Global database instance
$db = new Database();

// Helper functions
function redirect($url) {
    header("Location: " . $url);
    exit();
}

function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function isLoggedIn() {
    return isset($_SESSION['admin_id']) && isset($_SESSION['admin_username']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        redirect(ADMIN_URL . '/login.php');
    }
}

function generateSlug($string) {
    $string = strtolower($string);
    $string = preg_replace('/[^a-z0-9\s-]/', '', $string);
    $string = preg_replace('/[\s-]+/', '-', $string);
    return trim($string, '-');
}

function timeAgo($datetime) {
    $time = time() - strtotime($datetime);
    $time = ($time < 1) ? 1 : $time;
    $tokens = array (
        31536000 => 'tahun',
        2592000 => 'bulan',
        604800 => 'minggu',
        86400 => 'hari',
        3600 => 'jam',
        60 => 'menit',
        1 => 'detik'
    );

    foreach ($tokens as $unit => $text) {
        if ($time < $unit) continue;
        $numberOfUnits = floor($time / $unit);
        return $numberOfUnits.' '.$text.' yang lalu';
    }
}

function getSetting($key, $default = '') {
    global $db;
    $stmt = $db->prepare("SELECT setting_value FROM website_settings WHERE setting_key = ?");
    $stmt->bind_param("s", $key);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        return $row['setting_value'];
    }
    
    return $default;
}

function updateSetting($key, $value) {
    global $db;
    $stmt = $db->prepare("UPDATE website_settings SET setting_value = ? WHERE setting_key = ?");
    $stmt->bind_param("ss", $value, $key);
    return $stmt->execute();
}
?>
