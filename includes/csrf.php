<?php
// includes/csrf.php

// Ensure session started before using CSRF (keep this here as a safeguard)
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

/**
 * Ensure a per-session CSRF token exists.
 */
function csrf_ensure_token(): void {
    if (empty($_SESSION['csrf_token'])) {
// 32 bytes -> 64 hex chars; cryptographically secure
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}

/**
 * Get the current session CSRF token (generates one if missing).
 */
function csrf_token(): string {
    csrf_ensure_token();
    return $_SESSION['csrf_token'];
}

/**
 * Get a hidden input field with the CSRF token for forms.
 */
function csrf_input(): string {
    $t = htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8');
    return '<input type="hidden" name="csrf_token" value="'.$t.'">';
}

function csrf_is_valid(): bool {
    csrf_ensure_token();

    $provided = null;

    // Prefer header for AJAX
    $headers = function_exists('getallheaders') ? getallheaders() : [];
    if (is_array($headers)) {
        foreach ($headers as $k => $v) {
            if (strcasecmp($k, 'X-CSRF-Token') === 0) {
                $provided = $v;
                break;
            }
        }
    }

    // Fallback to POST param
    if ($provided === null && isset($_POST['csrf_token'])) {
        $provided = $_POST['csrf_token'];
    }

    if (!is_string($provided) || $provided === '') {
        return false;
    }

    // Using hash_equals to prevent timing attacks
    return hash_equals($_SESSION['csrf_token'], $provided);
}


function csrf_require_or_fail(): void {
    if (!csrf_is_valid()) {
        http_response_code(403);
        // Refresh token for next render to avoid user being stuck
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        echo '<h1>Forbidden</h1><p>Session expired or invalid request. Please go back and try again.</p>';
        exit;
    }
}
