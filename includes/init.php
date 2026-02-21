<?php
ob_start();

$cookieParams = session_get_cookie_params();
session_set_cookie_params([
    'lifetime' => 0,                                   // session ends when browser closes
    'path'     => $cookieParams['path'] ?? '/',
    'domain'   => $cookieParams['domain'] ?? '',
    'secure'   => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'),
    'httponly' => true,
    'samesite' => 'Lax',
]);

// Start the session if it hasn’t been started yet
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load database connection and helper functions
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/functions.php';

// Load CSRF utilities and generate a token if it doesn't exist
require_once __DIR__ . '/csrf.php';
csrf_ensure_token(); // creates $_SESSION['csrf_token']

// Session timeout handling (30 minutes of inactivity)
$timeout_duration = 1800;

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {

    // Clear the old session and start a new one
    session_unset();
    session_destroy();

    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => $cookieParams['path'] ?? '/',
        'domain'   => $cookieParams['domain'] ?? '',
        'secure'   => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    session_start();
    csrf_ensure_token();
}

$_SESSION['last_activity'] = time();

// Bind session to IP + User-Agent to make hijacking harder
if (!isset($_SESSION['user_ip']) || !isset($_SESSION['user_agent'])) {

    $_SESSION['user_ip'] = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';

} else {

    $current_ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $current_ua = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';

    if ($_SESSION['user_ip'] !== $current_ip || $_SESSION['user_agent'] !== $current_ua) {

        session_unset();
        session_destroy();

        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => $cookieParams['path'] ?? '/',
            'domain'   => $cookieParams['domain'] ?? '',
            'secure'   => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'),
            'httponly' => true,
            'samesite' => 'Lax',
        ]);

        session_start();
        csrf_ensure_token();
    }
}
