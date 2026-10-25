<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');
ini_set('session.cookie_httponly', '1');

session_name('SECUREPORTALSESSID');

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => COOKIE_SECURE,
    'httponly' => true,
    'samesite' => 'Strict',
]);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: no-referrer');
header('Permissions-Policy: camera=(), microphone=(), geolocation=()');

header(
    "Content-Security-Policy: default-src 'self'; " .
    "img-src 'self' data:; media-src 'self'; " .
    "style-src 'self' 'unsafe-inline'; " .
    "script-src 'self'; frame-ancestors 'none'; " .
    "base-uri 'self'; form-action 'self'"
);

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf'];
}

function verify_csrf(): bool
{
    return isset($_POST['csrf'], $_SESSION['csrf'])
        && is_string($_POST['csrf'])
        && hash_equals($_SESSION['csrf'], $_POST['csrf']);
}

function require_login(): void
{
    if (
        !isset($_SESSION['user_id']) ||
        !is_int($_SESSION['user_id']) ||
        $_SESSION['user_id'] !== 1
    ) {
        header('Location: Login.php');
        exit;
    }

    if (
        !isset($_SESSION['last_activity']) ||
        time() - (int) $_SESSION['last_activity'] > 1200
    ) {
        $_SESSION = [];
        session_destroy();

        header('Location: Login.php?expired=1');
        exit;
    }

    $_SESSION['last_activity'] = time();
}
