<?php
require_once '../config/config.php';

// Mulai sesi jika belum aktif
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Hapus semua variabel sesi
$_SESSION = [];

// Hancurkan sesi
session_destroy();

// Hapus cookie sesi (opsional, untuk benar-benar bersih)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Redirect ke halaman login admin
redirect(ADMIN_URL . '/login.php');
exit;
