<?php
// Constante requerida por el proyecto
define("NOTA_MINIMA_APROBATORIA", 70);

// Funciones Personalizadas
function calcularPromedio($n1, $n2, $n3) {
    return ($n1 + $n2 + $n3) / 3;
}

function obtenerLetraCalificacion($promedio) {
    if ($promedio >= 91) return "A";
    if ($promedio >= 81) return "B";
    if ($promedio >= 71) return "C";
    if ($promedio >= 61) return "D";
    return "F";
}

// Simulación de base de datos para la búsqueda GET
$estudiantes_registrados = ["Juan", "Henry", "Joseph", "Maria"];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Procesamiento de Estudiante</title>
    <link rel="stylesheet" href="CSS/estilos.css">
</head>
<body>
    <header>
        <h1>Resultado del Procesamiento</h1>
        <nav>
            <a href="index.php">Inicio</a>
            <a href="registro_estudiantes.php">Registrar Estudiante</a>
        </nav>
    </header>

    <main>
        <?php
        // 1. Manejo de Consulta mediante GET
        if (isset($_GET['nombre'])) {
            $busqueda = trim($_GET['nombre']);
            echo "<h2>Consulta de Estudiante (Método GET)</h2>";
            
            $encontrado = false;
            foreach ($estudiantes_registrados as $estudiante) {
                if (strcasecmp($estudiante, $busqueda) == 0) {
                    $encontrado = true;
                    break;
                }
            }

            if ($encontrado) {
                echo "<div class='alert alert-info'>El estudiante <strong>" . htmlspecialchars($busqueda) . "</strong> se encuentra registrado en el sistema.</div>";
            } else {
                echo "<div class='alert alert-danger'>El estudiante <strong>" . htmlspecialchars($busqueda) . "</strong> no ha sido encontrado en los registros.</div>";
            }
            echo "<br><a href='registro_estudiantes.php' class='btn'>Volver al Formulario</a>";
        }
        
        // 2. Procesamiento de Formulario mediante POST
        elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
            $nombre = trim($_POST['nombre'] ?? '');
            $cedula = trim($_POST['cedula'] ?? '');
            $edad = intval($_POST['edad'] ?? 0);
            $carrera = $_POST['carrera'] ?? '';
            $asignatura = $_POST['asignatura'] ?? '';
            $nota1 = floatval($_POST['nota1'] ?? 0);
            $nota2 = floatval($_POST['nota2'] ?? 0);
            $nota3 = floatval($_POST['nota3'] ?? 0);

            // Validaciones del Servidor
            $errores = [];

            if (empty($nombre) || empty($cedula) || empty($carrera) || empty($asignatura)) {
                $errores[] = "Ningún campo debe quedar vacío.";
            }

            if ($edad < 15) {
                $errores[] = "La edad debe ser mayor o igual a 15 años.";
            }

            if ($nota1 < 0 || $nota1 > 100 || $nota2 < 0 || $nota2 > 100 || $nota3 < 0 || $nota3 > 100) {
                $errores[] = "Las notas deben estar estrictamente en el rango de 0 a 100.";
            }

            // Mostrar Errores si existen
            if (!empty($errores)) {
                echo "<div class='alert alert-danger'>";
                echo "<h3>Errores en la Validación:</h3><ul>";
                foreach ($errores as $error) {
                    echo "<li>$error</li>";
                }
                echo "</ul></div>";
                echo "<br><a href='javascript:history.back()' class='btn'>Corregir Datos</a>";
            } else {
                // Cálculos y funciones
                $promedio = calcularPromedio($nota1, $nota2, $nota3);
                $letra = obtenerLetraCalificacion($promedio);
                $estado = ($promedio >= NOTA_MINIMA_APROBATORIA) ? "Aprobado" : "Reprobado";
                $clase_estado = ($promedio >= NOTA_MINIMA_APROBATORIA) ? "approved" : "failed";
                $fecha_registro = date("d/m/Y H:i:s"); // Función predefinida date()
                ?>

                <h2>Expediente Académico</h2>
                <p style="margin-bottom: 1rem;"><strong>Fecha de Registro:</strong> <?php echo $fecha_registro; ?></p>
                
                <table>
                    <tr>
                        <th>Nombre Completo</th>
                        <td><?php echo htmlspecialchars($nombre); ?></td>
                    </tr>
                    <tr>
                        <th>Cédula</th>
                        <td><?php echo htmlspecialchars($cedula); ?></td>
                    </tr>
                    <tr>
                        <th>Edad</th>
                        <td><?php echo $edad; ?> años</td>
                    </tr>
                    <tr>
                        <th>Carrera</th>
                        <td><?php echo htmlspecialchars($carrera); ?></td>
                    </tr>
                    <tr>
                        <th>Asignatura</th>
                        <td><?php echo htmlspecialchars($asignatura); ?></td>
                    </tr>
                    <tr>
                        <th>Notas Ingresadas</th>
                        <td><?php echo "$nota1 | $nota2 | $nota3"; ?></td>
                    </tr>
                    <tr>
                        <th>Promedio Numérico</th>
                        <td><?php echo number_format($promedio, 2); ?></td>
                    </tr>
                    <tr>
                        <th>Calificación (Letra)</th>
                        <td><strong><?php echo $letra; ?></strong></td>
                    </tr>
                    <tr>
                        <th>Resultado Académico</th>
                        <td><span class="status-badge <?php echo $clase_estado; ?>"><?php echo $estado; ?></span></td>
                    </tr>
                </table>

                <div style="margin-top: 1.5rem; text-align: center;">
                    <a href="registro_estudiantes.php" class="btn">Registrar Otro Estudiante</a>
                </div>

                <?php
            }
        } else {
            echo "<p>No se han recibido datos para procesar.</p>";
            echo "<br><a href='registro_estudiantes.php' class='btn'>Ir al Formulario</a>";
        }
        ?>
    </main>

    <footer>
        <p>Desarrollado Por: Henry Ramirez y Joseph Gantes - Grupo 1S3221</p>
    </footer>
</body>
</html>