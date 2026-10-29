<?php
// ============================================================
//  sesion.php
//  Starts the PHP session and exposes the access-control and
//  flash-message helpers.
//  Must be included at the top of every page, BEFORE any HTML.
// ============================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ------------------------------------------------------------
// 1. Access control
//    Without a valid session the visitor is sent to login.php
// ------------------------------------------------------------
function requerir_sesion() {
    if (!isset($_SESSION['id_usuario'])) {
        header('Location: login.php');
        exit;
    }
}

// 2. Data of the authenticated user
function nombreUsuarioActual() {
    return $_SESSION['nombre'] ?? '';
}

function usuarioActual() {
    return $_SESSION['usuario'] ?? '';
}

// ------------------------------------------------------------
// 3. Flash messages
//    They survive a header('Location') so pages can show a
//    result message (e.g. "student registered successfully").
// ------------------------------------------------------------
function setFlash($tipo, $mensaje) {
    $_SESSION['flash'] = ['tipo' => $tipo, 'mensaje' => $mensaje];
}

// Returns the pending message and removes it (shown only once)
function getFlash() {
    if (!isset($_SESSION['flash'])) {
        return null;
    }
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}

// Prints the pending message with its corresponding style
function mostrarFlash() {
    $flash = getFlash();
    if ($flash === null) {
        return;
    }
    $clase = 'alert-info';
    if ($flash['tipo'] === 'success') {
        $clase = 'alert-success';
    } elseif ($flash['tipo'] === 'danger') {
        $clase = 'alert-danger';
    }
    echo '<div class="alert ' . $clase . '">' . htmlspecialchars($flash['mensaje'], ENT_QUOTES, 'UTF-8') . '</div>';
}
