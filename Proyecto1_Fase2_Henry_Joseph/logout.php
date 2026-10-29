<?php
// ============================================================
//  logout.php
//  Ends the active session and returns to the login page.
// ============================================================
require_once 'sesion.php';

// 1. Empty the session array
$_SESSION = [];

// 2. Remove the session cookie from the browser
if (ini_get('session.use_cookies')) {
    $parametros = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $parametros['path'],
        $parametros['domain'],
        $parametros['secure'],
        $parametros['httponly']
    );
}

// 3. Destroy the session on the server
session_destroy();

// 4. Back to the login page
header('Location: login.php');
exit;
