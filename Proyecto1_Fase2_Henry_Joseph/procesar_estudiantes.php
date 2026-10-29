<?php
// ============================================================
//  procesar_estudiantes.php
//  Handles the registration form (CREATE):
//    1. Validate the data received by POST
//    2. Compute the average, letter grade and academic status
//    3. Store the record in MySQL with a prepared INSERT
// ============================================================
require_once 'conexion.php';
require_once 'sesion.php';
require_once 'funciones.php';

requerir_sesion();

// ------------------------------------------------------------
// 0. This file only accepts POST; a direct GET sends the user
//    back to the form.
// ------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: registro_estudiantes.php');
    exit;
}

// ------------------------------------------------------------
// 1. Read and trim the form data
// ------------------------------------------------------------
$datos = [
    'nombre_completo' => trim($_POST['nombre_completo'] ?? ''),
    'cedula'          => trim($_POST['cedula'] ?? ''),
    'edad'            => trim($_POST['edad'] ?? ''),
    'carrera'         => trim($_POST['carrera'] ?? ''),
    'asignatura'      => trim($_POST['asignatura'] ?? ''),
    'nota1'           => trim($_POST['nota1'] ?? ''),
    'nota2'           => trim($_POST['nota2'] ?? ''),
    'nota3'           => trim($_POST['nota3'] ?? ''),
];

// ------------------------------------------------------------
// 2. Validations (the same ones used when editing)
//    Returns an array of errors; empty means everything is valid.
// ------------------------------------------------------------
$errores = validarEstudiante($datos, $conexion);

if (!empty($errores)) {
    // Store the errors and the typed values, then go back to the form
    $_SESSION['errores_registro'] = $errores;
    $_SESSION['valores_registro'] = $datos;
    header('Location: registro_estudiantes.php');
    exit;
}

// ------------------------------------------------------------
// 3. Academic calculations using the custom functions
// ------------------------------------------------------------
$promedio = calcularPromedio((float) $datos['nota1'], (float) $datos['nota2'], (float) $datos['nota3']);
$letra    = obtenerLetraCalificacion($promedio);
$estado   = determinarEstado($promedio);
$fecha    = date('Y-m-d H:i:s');   // automatic registration date

// ------------------------------------------------------------
// 4. INSERT with placeholders (prepared statement)
// ------------------------------------------------------------
$sql = "INSERT INTO estudiantes
            (nombre_completo, cedula, edad, carrera, asignatura,
             nota1, nota2, nota3, promedio, fecha_registro)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

try {
    $stmt = $conexion->prepare($sql);
    $stmt->execute([
        $datos['nombre_completo'],
        $datos['cedula'],
        (int) $datos['edad'],
        $datos['carrera'],
        $datos['asignatura'],
        (float) $datos['nota1'],
        (float) $datos['nota2'],
        (float) $datos['nota3'],
        $promedio,
        $fecha,
    ]);

    // 5. Confirmation message and return to the listing
    setFlash('success', 'El estudiante ' . $datos['nombre_completo']
        . ' fue registrado correctamente. Promedio: ' . number_format($promedio, 2)
        . ' (' . $letra . ' - ' . $estado . ').');
} catch (PDOException $e) {
    // If the cedula was duplicated between validation and INSERT,
    // the UNIQUE index in MySQL protects the data as well.
    error_log('Error inserting student: ' . $e->getMessage());
    setFlash('danger', 'No se pudo guardar el registro. Verifique los datos e intente nuevamente.');
}

header('Location: listar_estudiantes.php');
exit;
