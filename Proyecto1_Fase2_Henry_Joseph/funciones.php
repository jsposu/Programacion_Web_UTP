<?php
// ============================================================
//  funciones.php
//  Constants and custom functions for the academic portal.
//  Keeps the Project #1 functions (average and letter grade)
//  and adds the validations shared by the create and update
//  flows.
// ============================================================

// Constant required by the project statement
define("NOTA_MINIMA_APROBATORIA", 70);

// ------------------------------------------------------------
// 1. Average
//    Formula: (Nota1 + Nota2 + Nota3) / 3
// ------------------------------------------------------------
function calcularPromedio($nota1, $nota2, $nota3) {
    return round(($nota1 + $nota2 + $nota3) / 3, 2);
}

// ------------------------------------------------------------
// 2. Numeric average to letter grade
//    (ranges inherited from Project #1)
// ------------------------------------------------------------
function obtenerLetraCalificacion($promedio) {
    if ($promedio >= 91) return "A";
    if ($promedio >= 81) return "B";
    if ($promedio >= 71) return "C";
    if ($promedio >= 61) return "D";
    return "F";
}

// ------------------------------------------------------------
// 3. Academic result: 70 or more is a pass
// ------------------------------------------------------------
function determinarEstado($promedio) {
    return ($promedio >= NOTA_MINIMA_APROBATORIA) ? "Aprobado" : "Reprobado";
}

// CSS class that paints the status badge
function claseEstado($estado) {
    return ($estado === "Aprobado") ? "approved" : "failed";
}

// ------------------------------------------------------------
// 4. Escape data before printing it in HTML
//    Prevents a name containing tags from breaking the page (XSS).
// ------------------------------------------------------------
function limpiar($valor) {
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

// 5. Human-readable date (the database stores YYYY-MM-DD HH:MM:SS)
function formatearFecha($fecha) {
    if (empty($fecha)) {
        return "-";
    }
    return date("d/m/Y H:i", strtotime($fecha));
}

// ------------------------------------------------------------
// 6. Validations shared by CREATE (register) and UPDATE (edit)
//    Receives the form data and returns an array of errors.
//    An empty array means the data is valid.
//    $idExcluir is used on edit so the student's own cedula is
//    not reported as a duplicate.
// ------------------------------------------------------------
function validarEstudiante($datos, $conexion, $idExcluir = null) {
    $errores = [];

    // Required text fields
    if ($datos['nombre_completo'] === '') {
        $errores[] = "El nombre completo es obligatorio.";
    }
    if ($datos['carrera'] === '') {
        $errores[] = "Debe seleccionar una carrera.";
    }
    if ($datos['asignatura'] === '') {
        $errores[] = "Debe seleccionar una asignatura.";
    }

    // Age: required, integer and 15 or older
    if ($datos['edad'] === '') {
        $errores[] = "La edad es obligatoria.";
    } elseif (!ctype_digit((string) $datos['edad'])) {
        $errores[] = "La edad debe ser un número entero.";
    } elseif ((int) $datos['edad'] < 15) {
        $errores[] = "La edad debe ser mayor o igual a 15 años.";
    }

    // Grades: required, numeric and between 0 and 100
    for ($i = 1; $i <= 3; $i++) {
        $nota = $datos["nota$i"];
        if ($nota === '') {
            $errores[] = "La nota $i es obligatoria.";
        } elseif (!is_numeric($nota)) {
            $errores[] = "La nota $i debe ser un valor numérico.";
        } elseif ((float) $nota < 0 || (float) $nota > 100) {
            $errores[] = "La nota $i debe estar entre 0 y 100.";
        }
    }

    // Cedula: required and unique in the database
    if ($datos['cedula'] === '') {
        $errores[] = "La cédula es obligatoria.";
    } else {
        // 1. Prepared query to find out whether the cedula already exists
        $sql = "SELECT id_estudiante FROM estudiantes WHERE cedula = ? LIMIT 1";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$datos['cedula']]);
        $existente = $stmt->fetch();

        // 2. Only an error when it belongs to ANOTHER student
        if ($existente && (int) $existente['id_estudiante'] !== (int) $idExcluir) {
            $errores[] = "La cédula " . $datos['cedula'] . " ya se encuentra registrada.";
        }
    }

    return $errores;
}
