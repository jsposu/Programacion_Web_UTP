<?php
// ============================================================
//  conexion.php
//  Creates ONE PDO connection to the registro_estudiantes
//  database. Every page reuses it with:
//      require_once 'conexion.php';
// ============================================================

$host    = 'localhost';              // XAMPP default host
$db      = 'registro_estudiantes';
$user    = 'root';                   // XAMPP default user
$pass    = '';                       // XAMPP root has no password by default
$charset = 'utf8mb4';                // Supports accents and special characters

// DSN: driver, host, database name and character set
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// PDO options (security and convenience)
$opciones = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,  // Failures throw exceptions
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,        // Rows come back as associative arrays
    PDO::ATTR_EMULATE_PREPARES   => false,                   // Real prepared statements (prevents SQL injection)
];

try {
    // 1. Attempt the connection
    $conexion = new PDO($dsn, $user, $pass, $opciones);
} catch (PDOException $e) {
    // 2. Log the real reason on the server only
    //    (never print it: it could expose the user or password)
    error_log('MySQL connection error: ' . $e->getMessage());

    // 3. The visitor only sees a generic message
    die('No se pudo conectar con la base de datos. Verifique que MySQL esté activo '
        . 'y que la base de datos "registro_estudiantes" haya sido importada.');
}
