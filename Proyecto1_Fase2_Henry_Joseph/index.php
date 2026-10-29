<?php
// ============================================================
//  index.php
//  System home page. Reachable only with an active session.
//  Shows a summary of the stored records and the options
//  available to the authenticated user.
// ============================================================
require_once 'conexion.php';
require_once 'sesion.php';
require_once 'funciones.php';

// Access control: no session -> login.php
requerir_sesion();

// ------------------------------------------------------------
// 1. Academic summary read directly from MySQL
// ------------------------------------------------------------
$total = (int) $conexion->query("SELECT COUNT(*) FROM estudiantes")->fetchColumn();

$sql = "SELECT COUNT(*) FROM estudiantes WHERE promedio >= ?";
$stmt = $conexion->prepare($sql);
$stmt->execute([NOTA_MINIMA_APROBATORIA]);
$aprobados = (int) $stmt->fetchColumn();

$reprobados = $total - $aprobados;

$titulo = 'Inicio';
require 'encabezado.php';
?>

    <h2>Bienvenido, <?= limpiar(nombreUsuarioActual()) ?></h2>
    <p class="texto-intro">
        Este sistema permite administrar los registros académicos de los estudiantes
        almacenados en MySQL: registrar, consultar, actualizar y eliminar información,
        calculando el promedio y el resultado académico de forma automática.
    </p>

    <!-- 2. Cards with the database summary -->
    <div class="cards-grid">
        <div class="card-stat">
            <span class="stat-num"><?= $total ?></span>
            <span class="stat-label">Estudiantes registrados</span>
        </div>
        <div class="card-stat">
            <span class="stat-num stat-ok"><?= $aprobados ?></span>
            <span class="stat-label">Aprobados</span>
        </div>
        <div class="card-stat">
            <span class="stat-num stat-fail"><?= $reprobados ?></span>
            <span class="stat-label">Reprobados</span>
        </div>
    </div>

    <!-- 3. Shortcuts to the available features -->
    <div class="cards-grid">
        <a href="registro_estudiantes.php" class="card-accion">
            <span class="card-icono">➕</span>
            <strong>Registrar Estudiante</strong>
            <span>Ingresar un nuevo expediente académico.</span>
        </a>
        <a href="listar_estudiantes.php" class="card-accion">
            <span class="card-icono">📋</span>
            <strong>Consultar Estudiantes</strong>
            <span>Ver, buscar, editar o eliminar registros.</span>
        </a>
        <a href="logout.php" class="card-accion">
            <span class="card-icono">🔒</span>
            <strong>Cerrar Sesión</strong>
            <span>Finalizar la sesión de <?= limpiar(usuarioActual()) ?>.</span>
        </a>
    </div>

<?php require 'pie.php'; ?>
