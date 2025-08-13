<?php
require_once '../config/config.php';

// Check if already logged in
if (isLoggedIn()) {
    redirect(ADMIN_URL . '/dashboard.php');
}

// Redirect to login
redirect(ADMIN_URL . '/login.php');
?>
