<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Estudiantes</title>
    <link rel="stylesheet" href="CSS/estilos.css">
</head>
<body>
    <header>
        <div class="header-brand">
            <img src="img/logo.png" alt="NetLinks Panama Logo" class="header-logo">
            <h1>Registro de Estudiantes</h1>
        </div>
        <nav>
            <a href="index.php">Inicio</a>
            <a href="registro_estudiantes.php">Registrar Estudiante</a>
            <a href="consultar_estudiantes.php">Consultar Estudiante</a>
        </nav>
    </header>

    <main>
        <h2>Formulario de Ingreso de Datos</h2>
        <form action="procesar_estudiantes.php" method="POST" style="margin-top: 1rem;">
            
            <div class="form-group">
                <label for="nombre">Nombre Completo:</label>
                <input type="text" id="nombre" name="nombre" required>
            </div>

            <div class="form-group">
                <label for="cedula">Cédula:</label>
                <input type="text" id="cedula" name="cedula" required>
            </div>

            <div class="form-group">
                <label for="edad">Edad (Mínimo 15 años):</label>
                <input type="number" id="edad" name="edad" min="15" required>
            </div>

            <div class="form-group">
                <label for="carrera">Carrera:</label>
                <select id="carrera" name="carrera" required>
                    <option value="">-- Seleccione una carrera --</option>
                    <option value="Lic. en Ciberseguridad">Lic. en Ciberseguridad</option>
                    <option value="Lic. en Desarrollo de Software">Lic. en Desarrollo de Software</option>
                    <option value="Ing. de Sistemas y Computación">Ing. de Sistemas y Computación</option>
                    <option value="Lic. en Redes Informáticas">Lic. en Redes Informáticas</option>
                </select>
            </div>

            <div class="form-group">
                <label for="asignatura">Asignatura:</label>
                <select id="asignatura" name="asignatura" required>
                    <option value="">-- Seleccione una asignatura --</option>
                    <option value="Programación Web">Programación Web</option>
                    <option value="Estructura de Datos">Estructura de Datos</option>
                    <option value="Sistemas Operativos">Sistemas Operativos</option>
                    <option value="Redes de Computadoras">Redes de Computadoras</option>
                </select>
            </div>

            <div class="form-group">
                <label for="nota1">Nota 1 (0-100):</label>
                <input type="number" id="nota1" name="nota1" min="0" max="100" step="0.1" required>
            </div>

            <div class="form-group">
                <label for="nota2">Nota 2 (0-100):</label>
                <input type="number" id="nota2" name="nota2" min="0" max="100" step="0.1" required>
            </div>

            <div class="form-group">
                <label for="nota3">Nota 3 (0-100):</label>
                <input type="number" id="nota3" name="nota3" min="0" max="100" step="0.1" required>
            </div>

            <button type="submit" class="btn" style="width: 100%;">Procesar Registro</button>
        </form>
    </main>

    <footer>
        <p>Desarrollado Por: Henry Ramirez y Joseph Gantes - Grupo 1S3221</p>
    </footer>
</body>
</html>