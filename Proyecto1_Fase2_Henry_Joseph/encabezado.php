<?php
// ============================================================
//  encabezado.php
//  Shared header, navigation and opening of the main content.
//  Included with:  require 'encabezado.php';
//
//  Optional variables a page may define BEFORE including it:
//    $titulo     -> browser tab / header text
//    $claseMain  -> container width (main-ancho, main-login, ...)
// ============================================================

$titulo    = $titulo    ?? 'Portal de Registro y Consulta de Estudiantes';
$claseMain = $claseMain ?? 'main-ancho';
$haySesion = isset($_SESSION['id_usuario']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titulo) ?> - NetLinks Panama</title>
    <link rel="stylesheet" href="CSS/estilos.css">
</head>
<body>
    <header>
        <div class="header-brand">
            <img src="img/logo.png" alt="NetLinks Panama Logo" class="header-logo">
            <h1><?= htmlspecialchars($titulo) ?></h1>
        </div>

        <?php if ($haySesion): ?>
        <nav>
            <a href="index.php">Inicio</a>
            <a href="registro_estudiantes.php">Registrar Estudiante</a>
            <a href="listar_estudiantes.php">Consultar Estudiantes</a>
            <span class="user-info">👤 <?= limpiar(nombreUsuarioActual()) ?></span>
            <a href="logout.php" class="logout-link">Cerrar Sesión</a>
        </nav>
        <?php endif; ?>
    </header>

    <main class="<?= $claseMain ?>">
        <?php if (function_exists('mostrarFlash')) { mostrarFlash(); } ?>
