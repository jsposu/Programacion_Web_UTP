<?php
// ============================================================
//  registro_estudiantes.php
//  Form to register a student (CREATE).
//  Data is submitted with POST to procesar_estudiantes.php
// ============================================================
require_once 'conexion.php';
require_once 'sesion.php';
require_once 'funciones.php';

requerir_sesion();

// ------------------------------------------------------------
// 1. If the previous submission failed, recover the errors and
//    the values typed by the user so nothing is lost.
// ------------------------------------------------------------
$errores = $_SESSION['errores_registro'] ?? [];
$valores = $_SESSION['valores_registro'] ?? [];
unset($_SESSION['errores_registro'], $_SESSION['valores_registro']);

// Small helper to refill one field with what the user typed
$valor = function ($campo) use ($valores) {
    return htmlspecialchars($valores[$campo] ?? '', ENT_QUOTES, 'UTF-8');
};

$titulo = 'Registrar Estudiante';
require 'encabezado.php';
?>

    <h2>Formulario de Ingreso de Datos</h2>
    <p class="texto-intro">
        Todos los campos son obligatorios. La edad debe ser mayor o igual a 15 años y las
        notas deben estar entre 0 y 100. El promedio se calcula automáticamente.
    </p>

    <?php if (!empty($errores)): ?>
        <div class="alert alert-danger">
            <strong>No se pudo registrar el estudiante:</strong>
            <ul class="lista-errores">
                <?php foreach ($errores as $mensaje): ?>
                    <li><?= limpiar($mensaje) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- POST: data is handled by procesar_estudiantes.php -->
    <form action="procesar_estudiantes.php" method="POST">

        <div class="form-group">
            <label for="nombre_completo">Nombre Completo:</label>
            <input type="text" id="nombre_completo" name="nombre_completo"
                   maxlength="150" value="<?= $valor('nombre_completo') ?>" required>
        </div>

        <div class="form-group">
            <label for="cedula">Cédula:</label>
            <input type="text" id="cedula" name="cedula" maxlength="25"
                   placeholder="Ej. 8-123-456" value="<?= $valor('cedula') ?>" required>
        </div>

        <div class="form-group">
            <label for="edad">Edad (mínimo 15 años):</label>
            <input type="number" id="edad" name="edad" min="15" max="120"
                   value="<?= $valor('edad') ?>" required>
        </div>

        <div class="form-group">
            <label for="carrera">Carrera:</label>
            <select id="carrera" name="carrera" required>
                <option value="">-- Seleccione una carrera --</option>
                <?php
                $carreras = [
                    'Lic. en Ciberseguridad',
                    'Lic. en Desarrollo de Software',
                    'Ing. de Sistemas y Computación',
                    'Lic. en Redes Informáticas',
                ];
                foreach ($carreras as $carrera):
                    $seleccion = ($valores['carrera'] ?? '') === $carrera ? 'selected' : '';
                ?>
                    <option value="<?= limpiar($carrera) ?>" <?= $seleccion ?>><?= limpiar($carrera) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="asignatura">Asignatura:</label>
            <select id="asignatura" name="asignatura" required>
                <option value="">-- Seleccione una asignatura --</option>
                <?php
                $asignaturas = [
                    'Programación Web',
                    'Estructura de Datos',
                    'Sistemas Operativos',
                    'Redes de Computadoras',
                ];
                foreach ($asignaturas as $asignatura):
                    $seleccion = ($valores['asignatura'] ?? '') === $asignatura ? 'selected' : '';
                ?>
                    <option value="<?= limpiar($asignatura) ?>" <?= $seleccion ?>><?= limpiar($asignatura) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="fila-notas">
            <div class="form-group">
                <label for="nota1">Nota 1 (0-100):</label>
                <input type="number" id="nota1" name="nota1" min="0" max="100" step="0.01"
                       value="<?= $valor('nota1') ?>" required>
            </div>

            <div class="form-group">
                <label for="nota2">Nota 2 (0-100):</label>
                <input type="number" id="nota2" name="nota2" min="0" max="100" step="0.01"
                       value="<?= $valor('nota2') ?>" required>
            </div>

            <div class="form-group">
                <label for="nota3">Nota 3 (0-100):</label>
                <input type="number" id="nota3" name="nota3" min="0" max="100" step="0.01"
                       value="<?= $valor('nota3') ?>" required>
            </div>
        </div>

        <div class="form-acciones">
            <button type="submit" class="btn">Guardar Estudiante</button>
            <a href="listar_estudiantes.php" class="btn btn-secundario">Ver Estudiantes</a>
        </div>
    </form>

<?php require 'pie.php'; ?>
