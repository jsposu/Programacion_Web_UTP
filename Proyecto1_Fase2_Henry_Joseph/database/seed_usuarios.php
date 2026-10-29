<?php
// ============================================================
//  database/seed_usuarios.php
//  Initial load of the test users.
//
//  The SQL file already ships two working users. Run this script
//  only if you need to recreate them or change their passwords:
//
//      /opt/lampp/bin/php database/seed_usuarios.php
//      (or)  php database/seed_usuarios.php
//
//  It uses password_hash(), as required by the project statement,
//  so the plain password is never stored in the database.
// ============================================================
require_once __DIR__ . '/../conexion.php';

// Test credentials. Replace them before a real deployment.
$usuarios = [
    ['nombre' => 'Henry Ramirez',  'usuario' => 'admin',    'contrasena' => 'Admin2026'],
    ['nombre' => 'Joseph Gantes',  'usuario' => 'profesor', 'contrasena' => 'Profesor2026'],
];

foreach ($usuarios as $u) {
    // 1. Generate a new bcrypt hash for each password
    $hash = password_hash($u['contrasena'], PASSWORD_DEFAULT);

    // 2. Insert the user, or update the hash if it already exists
    $sql = "INSERT INTO usuarios (nombre, usuario, contrasena, fecha_creacion)
            VALUES (?, ?, ?, NOW())
            ON DUPLICATE KEY UPDATE
                nombre     = VALUES(nombre),
                contrasena = VALUES(contrasena)";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([$u['nombre'], $u['usuario'], $hash]);

    echo "OK: usuario '{$u['usuario']}' listo (contraseña: {$u['contrasena']})." . PHP_EOL;
}

echo "Carga inicial de usuarios finalizada." . PHP_EOL;
