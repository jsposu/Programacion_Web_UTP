<?php
// ============================================================
//  login.php
//  System login. Validates the username and password against
//  the usuarios table using password_verify().
// ============================================================
require_once 'conexion.php';
require_once 'sesion.php';
require_once 'funciones.php';

// An already authenticated user skips the form
if (isset($_SESSION['id_usuario'])) {
    header('Location: index.php');
    exit;
}

$error   = '';
$usuario = '';

// ------------------------------------------------------------
// 1. Only processed when the form is submitted with POST
// ------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario    = trim($_POST['usuario'] ?? '');
    $contrasena = $_POST['contrasena'] ?? '';

    if ($usuario === '' || $contrasena === '') {
        $error = 'Debe ingresar su usuario y su contraseña.';
    } else {
        // 2. Prepared query: look the user up in the database
        $sql = "SELECT id_usuario, nombre, usuario, contrasena
                FROM usuarios
                WHERE usuario = ?
                LIMIT 1";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$usuario]);

        // 3. Fetch the row (false when the username does not exist)
        $registro = $stmt->fetch();

        // 4. Compare the typed password with the stored hash
        if ($registro && password_verify($contrasena, $registro['contrasena'])) {

            // 5. Valid credentials: create the session
            session_regenerate_id(true);            // prevents session fixation
            $_SESSION['id_usuario'] = (int) $registro['id_usuario'];
            $_SESSION['usuario']    = $registro['usuario'];
            $_SESSION['nombre']     = $registro['nombre'];

            // 6. Redirect to the main page
            header('Location: index.php');
            exit;
        }

        // 7. Invalid credentials (same message for user and password)
        $error = 'Usuario o contraseña incorrectos. Verifique sus credenciales.';
    }
}

$titulo    = 'Iniciar Sesión';
$claseMain = 'main-login';
require 'encabezado.php';
?>

    <div class="auth-card">
        <h2>Acceso al Sistema</h2>
        <p class="auth-texto">Ingrese sus credenciales para administrar los registros de estudiantes.</p>

        <?php if ($error !== ''): ?>
            <div class="alert alert-danger"><?= limpiar($error) ?></div>
        <?php endif; ?>

        <!-- POST: credentials travel in the request body, not the URL -->
        <form method="POST" action="login.php">
            <div class="form-group">
                <label for="usuario">Nombre de usuario:</label>
                <input type="text" id="usuario" name="usuario" value="<?= limpiar($usuario) ?>"
                       autocomplete="username" required autofocus>
            </div>

            <div class="form-group">
                <label for="contrasena">Contraseña:</label>
                <input type="password" id="contrasena" name="contrasena"
                       autocomplete="current-password" required>
            </div>

            <button type="submit" class="btn btn-bloque">Iniciar Sesión</button>
        </form>
    </div>

<?php require 'pie.php'; ?>
