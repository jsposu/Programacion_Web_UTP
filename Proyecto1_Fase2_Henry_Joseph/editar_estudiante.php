<?php
// ============================================================
//  editar_estudiante.php
//  Updates an existing student (UPDATE).
//  GET  : loads the record and shows the form with current data
//  POST : validates, recalculates the average and saves changes
// ============================================================
require_once 'conexion.php';
require_once 'sesion.php';
require_once 'funciones.php';

requerir_sesion();

// ------------------------------------------------------------
// 1. The record id arrives by GET (from the listing) or by POST
//    (hidden field of the form). It must be a positive integer.
// ------------------------------------------------------------
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($id === null || $id === false) {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
}

if ($id === null || $id === false || $id < 1) {
    setFlash('danger', 'Identificador de estudiante no válido.');
    header('Location: listar_estudiantes.php');
    exit;
}

// ------------------------------------------------------------
// 2. Load the stored data of that record
// ------------------------------------------------------------
$sql = "SELECT id_estudiante, nombre_completo, cedula, edad, carrera, asignatura,
               nota1, nota2, nota3, promedio, fecha_registro
        FROM estudiantes
        WHERE id_estudiante = ?";
$stmt = $conexion->prepare($sql);
$stmt->execute([$id]);
$registro = $stmt->fetch();

// 3. A record that does not exist cannot be modified
if (!$registro) {
    setFlash('danger', 'El estudiante solicitado no existe.');
    header('Location: listar_estudiantes.php');
    exit;
}

$errores = [];
$valores = $registro;   // form starts with the current database values

// ------------------------------------------------------------
// 4. When the form is submitted: validate and update
// ------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 4.1 Read the new values sent by the form
    $valores = [
        'nombre_completo' => trim($_POST['nombre_completo'] ?? ''),
        'cedula'          => trim($_POST['cedula'] ?? ''),
        'edad'            => trim($_POST['edad'] ?? ''),
        'carrera'         => trim($_POST['carrera'] ?? ''),
        'asignatura'      => trim($_POST['asignatura'] ?? ''),
        'nota1'           => trim($_POST['nota1'] ?? ''),
        'nota2'           => trim($_POST['nota2'] ?? ''),
        'nota3'           => trim($_POST['nota3'] ?? ''),
    ];

    // 4.2 Same validations as the registration form.
    //     $id is excluded so the student's own cedula is allowed.
    $errores = validarEstudiante($valores, $conexion, $id);

    if (empty($errores)) {
        // 4.3 Recalculate the average whenever the grades change
        $promedio = calcularPromedio(
            (float) $valores['nota1'],
            (float) $valores['nota2'],
            (float) $valores['nota3']
        );

        // 4.4 UPDATE prepared statement.
        //     fecha_registro is intentionally NOT updated (it is preserved).
        $sql = "UPDATE estudiantes
                SET nombre_completo = ?, cedula = ?, edad = ?, carrera = ?, asignatura = ?,
                    nota1 = ?, nota2 = ?, nota3 = ?, promedio = ?
                WHERE id_estudiante = ?";
        $stmt = $conexion->prepare($sql);

        try {
            $stmt->execute([
                $valores['nombre_completo'],
                $valores['cedula'],
                (int) $valores['edad'],
                $valores['carrera'],
                $valores['asignatura'],
                (float) $valores['nota1'],
                (float) $valores['nota2'],
                (float) $valores['nota3'],
                $promedio,
                $id,
            ]);

            $estado = determinarEstado($promedio);
            setFlash('success', 'Los datos del estudiante fueron actualizados correctamente. '
                . 'Nuevo promedio: ' . number_format($promedio, 2)
                . ' (' . obtenerLetraCalificacion($promedio) . ' - ' . $estado . ').');

            header('Location: listar_estudiantes.php');
            exit;
        } catch (PDOException $e) {
            error_log('Error updating student: ' . $e->getMessage());
            $errores[] = 'No se pudo actualizar el registro. Intente nuevamente.';
        }
    }
}

// Helper to refill a field (typed value first, stored value otherwise)
$valor = function ($campo) use ($valores) {
    return htmlspecialchars((string) ($valores[$campo] ?? ''), ENT_QUOTES, 'UTF-8');
};

$titulo = 'Editar Estudiante';
require 'encabezado.php';
?>

    <h2>Editar Estudiante</h2>
    <p class="texto-intro">
        Cédula: <strong><?= limpiar($registro['cedula']) ?></strong> ·
        Registrado el: <strong><?= formatearFecha($registro['fecha_registro']) ?></strong>
        (la fecha original no se modifica).
    </p>

    <?php if (!empty($errores)): ?>
        <div class="alert alert-danger">
            <strong>No se pudieron guardar los cambios:</strong>
            <ul class="lista-errores">
                <?php foreach ($errores as $mensaje): ?>
                    <li><?= limpiar($mensaje) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- POST to the same page: the hidden id identifies the record -->
    <form action="editar_estudiante.php" method="POST">
        <input type="hidden" name="id" value="<?= (int) $id ?>">

        <div class="form-group">
            <label for="nombre_completo">Nombre Completo:</label>
            <input type="text" id="nombre_completo" name="nombre_completo"
                   maxlength="150" value="<?= $valor('nombre_completo') ?>" required>
        </div>

        <div class="form-group">
            <label for="cedula">Cédula:</label>
            <input type="text" id="cedula" name="cedula" maxlength="25"
                   value="<?= $valor('cedula') ?>" required>
        </div>

        <div class="form-group">
            <label for="edad">Edad (mínimo 15 años):</label>
            <input type="number" id="edad" name="edad" min="15" max="120"
                   value="<?= $valor('edad') ?>" required>
        </div>

        <div class="form-group">
            <label for="carrera">Carrera:</label>
            <select id="carrera" name="carrera" required>
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
            <button type="submit" class="btn">Actualizar Estudiante</button>
            <a href="listar_estudiantes.php" class="btn btn-secundario">Cancelar</a>
        </div>
    </form>

<?php require 'pie.php'; ?>
