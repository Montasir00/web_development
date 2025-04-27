<?php
ob_start();

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database and function files
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/functions.php';

// Set session timeout duration (e.g., 30 minutes)
$timeout_duration = 1800;

// Check for session timeout
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
    session_unset();
    session_destroy();
    session_start(); // Start a new session after timeout
}
$_SESSION['last_activity'] = time();

// Validate user's IP address and User-Agent to prevent session hijacking
if (!isset($_SESSION['user_ip']) || !isset($_SESSION['user_agent'])) {
    $_SESSION['user_ip'] = $_SERVER['REMOTE_ADDR'];
    $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
} else {
    if ($_SESSION['user_ip'] !== $_SERVER['REMOTE_ADDR'] || $_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
        session_unset();
        session_destroy();
        session_start(); // Start a new session after validation failure
    }
}
?>
