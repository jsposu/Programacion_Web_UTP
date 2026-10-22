<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de Estudiantes</title>
    <link rel="stylesheet" href="CSS/estilos.css">
</head>
<body>
    <header>
        <div class="header-brand">
            <img src="img/logo.png" alt="NetLinks Panama Logo" class="header-logo">
            <h1>Portal de Registro y Consulta</h1>
        </div>
        <nav>
            <a href="index.php">Inicio</a>
            <a href="registro_estudiantes.php">Registrar Estudiante</a>
            <a href="consultar_estudiantes.php">Consultar Estudiante</a>
        </nav>
    </header>

    <main>
        <h2>Consulta de Estudiantes (Método GET)</h2>
        <p style="margin-bottom: 1.5rem;">
            Busca en nuestra base de datos simulada ingresando uno de los 3 nombres de prueba (puedes escribir sin tildes): 
            <strong>Carlos Pérez, Ana Gómez, Luis Martínez</strong>.
        </p>
        
        <!-- Formulario con método GET -->
        <form method="GET" action="consultar_estudiantes.php" style="margin-bottom: 2rem;">
            <div class="form-group">
                <label for="nombre">Nombre a consultar:</label>
                <input type="text" id="nombre" name="nombre" placeholder="Ej. carlos perez" required>
            </div>
            <button type="submit" class="btn" style="width: 100%;">Buscar Estudiante</button>
        </form>

        <?php
        // Función para limpiar tildes y convertir a minúsculas para una búsqueda flexible
        function limpiarTexto($texto) {
            $unwanted = [
                'Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
                'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
                'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'s', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
                'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
                'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ü'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y'
            ];
            return strtolower(strtr($texto, $unwanted));
        }

        // Arreglo asociativo con datos simulados detallados de los 3 estudiantes
        $estudiantes_falsos = [
            [
                "nombre" => "Carlos Pérez",
                "cedula" => "8-900-1234",
                "edad" => 21,
                "carrera" => "Lic. en Ciberseguridad",
                "asignatura" => "Sistemas Operativos",
                "promedio" => 85.0,
                "letra" => "B",
                "estado" => "Aprobado"
            ],
            [
                "nombre" => "Ana Gómez",
                "cedula" => "8-850-4321",
                "edad" => 20,
                "carrera" => "Lic. en Desarrollo de Software",
                "asignatura" => "Programación Web",
                "promedio" => 95.0,
                "letra" => "A",
                "estado" => "Aprobado"
            ],
            [
                "nombre" => "Luis Martínez",
                "cedula" => "8-750-9876",
                "edad" => 22,
                "carrera" => "Ing. de Sistemas y Computación",
                "asignatura" => "Redes de Computadoras",
                "promedio" => 78.0,
                "letra" => "C",
                "estado" => "Aprobado"
            ]
        ];

        if (isset($_GET['nombre']) && !empty(trim($_GET['nombre']))) {
            $busqueda_limpia = limpiarTexto(trim($_GET['nombre']));
            echo "<h3>Resultado de la Consulta:</h3><br>";
            
            $estudiante_encontrado = null;
            foreach ($estudiantes_falsos as $est) {
                if (limpiarTexto($est['nombre']) == $busqueda_limpia) {
                    $estudiante_encontrado = $est;
                    break;
                }
            }

            if ($estudiante_encontrado) {
                echo "<div class='alert alert-info'>¡Estudiante encontrado en los registros del sistema!</div>";
                ?>
                <table>
                    <tr>
                        <th>Nombre Completo</th>
                        <td><?php echo htmlspecialchars($estudiante_encontrado['nombre']); ?></td>
                    </tr>
                    <tr>
                        <th>Cédula</th>
                        <td><?php echo htmlspecialchars($estudiante_encontrado['cedula']); ?></td>
                    </tr>
                    <tr>
                        <th>Edad</th>
                        <td><?php echo $estudiante_encontrado['edad']; ?> años</td>
                    </tr>
                    <tr>
                        <th>Carrera</th>
                        <td><?php echo htmlspecialchars($estudiante_encontrado['carrera']); ?></td>
                    </tr>
                    <tr>
                        <th>Asignatura</th>
                        <td><?php echo htmlspecialchars($estudiante_encontrado['asignatura']); ?></td>
                    </tr>
                    <tr>
                        <th>Promedio Numérico</th>
                        <td><?php echo number_format($estudiante_encontrado['promedio'], 2); ?></td>
                    </tr>
                    <tr>
                        <th>Calificación (Letra)</th>
                        <td><strong><?php echo $estudiante_encontrado['letra']; ?></strong></td>
                    </tr>
                    <tr>
                        <th>Resultado Académico</th>
                        <td><span class="status-badge approved"><?php echo $estudiante_encontrado['estado']; ?></span></td>
                    </tr>
                </table>
                <?php
            } else {
                echo "<div class='alert alert-danger'>El estudiante <strong>" . htmlspecialchars($_GET['nombre']) . "</strong> no se encuentra registrado en el sistema.</div>";
            }
        }
        ?>
    </main>

    <footer>
        <p>Desarrollado Por: Henry Ramirez y Joseph Gantes - Grupo 1S3221</p>
    </footer>
</body>
</html>