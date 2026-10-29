<?php
// ============================================================
//  eliminar_estudiante.php
//  Deletes a student (DELETE).
//  GET  : shows a confirmation card with the student's name
//  POST : performs the DELETE after the user confirms
//  Deleting with GET is never allowed: a link could remove a
//  record by accident.
// ============================================================
require_once 'conexion.php';
require_once 'sesion.php';
require_once 'funciones.php';

requerir_sesion();

// ------------------------------------------------------------
// 1. Validate the record id (GET when opening, POST when confirming)
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
// 2. Load the record (also proves that it still exists)
// ------------------------------------------------------------
$sql = "SELECT id_estudiante, nombre_completo, cedula, carrera, asignatura, promedio
        FROM estudiantes
        WHERE id_estudiante = ?";
$stmt = $conexion->prepare($sql);
$stmt->execute([$id]);
$registro = $stmt->fetch();

if (!$registro) {
    setFlash('danger', 'El estudiante que intenta eliminar no existe.');
    header('Location: listar_estudiantes.php');
    exit;
}

// ------------------------------------------------------------
// 3. POST = the user confirmed: run the DELETE
// ------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $sql = "DELETE FROM estudiantes WHERE id_estudiante = ?";
    $stmt = $conexion->prepare($sql);

    try {
        $stmt->execute([$id]);

        if ($stmt->rowCount() > 0) {
            setFlash('success', 'El estudiante "' . $registro['nombre_completo']
                . '" fue eliminado correctamente.');
        } else {
            setFlash('danger', 'No se pudo eliminar el registro. Intente nuevamente.');
        }
    } catch (PDOException $e) {
        error_log('Error deleting student: ' . $e->getMessage());
        setFlash('danger', 'Ocurrió un error al eliminar el registro.');
    }

    // 4. Back to the listing, which already reflects the change
    header('Location: listar_estudiantes.php');
    exit;
}

$titulo = 'Eliminar Estudiante';
require 'encabezado.php';
?>

    <h2>Confirmar Eliminación</h2>

    <div class="confirm-card">
        <p class="confirm-texto">
            ¿Está seguro de que desea eliminar al estudiante
            <strong><?= limpiar($registro['nombre_completo']) ?></strong>?
            Esta acción no se puede deshacer.
        </p>

        <ul class="confirm-datos">
            <li><strong>Cédula:</strong> <?= limpiar($registro['cedula']) ?></li>
            <li><strong>Carrera:</strong> <?= limpiar($registro['carrera']) ?></li>
            <li><strong>Asignatura:</strong> <?= limpiar($registro['asignatura']) ?></li>
            <li><strong>Promedio:</strong> <?= number_format((float) $registro['promedio'], 2) ?></li>
        </ul>

        <!-- Only this POST performs the deletion -->
        <form action="eliminar_estudiante.php" method="POST" class="form-acciones">
            <input type="hidden" name="id" value="<?= (int) $id ?>">
            <button type="submit" class="btn btn-peligro">Sí, eliminar</button>
            <a href="listar_estudiantes.php" class="btn btn-secundario">Cancelar</a>
        </form>
    </div>

<?php require 'pie.php'; ?>
