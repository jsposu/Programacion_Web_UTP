-- ============================================================
--  registro_estudiantes.sql
--  Database for the "Portal de Registro y Consulta de Estudiantes"
--  Programming Web - Project #1, Phase 2 (PHP + MySQL)
--  Henry Ramirez & Joseph Gantes - Group 1S3221
--
--  How to import (XAMPP on Linux):
--    sudo /opt/lampp/bin/mysql -u root < registro_estudiantes.sql
--  or with phpMyAdmin: Import tab -> choose this file -> Go
--
--  The script is re-runnable: tables use IF NOT EXISTS and the
--  sample rows use INSERT IGNORE (cedula/usuario are UNIQUE).
--  To start from scratch, run first:
--    DROP DATABASE IF EXISTS registro_estudiantes;
-- ============================================================

CREATE DATABASE IF NOT EXISTS registro_estudiantes
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE registro_estudiantes;

-- ------------------------------------------------------------
-- Table: usuarios
-- Stores the credentials used to sign in to the system.
-- Passwords are stored as a hash created with password_hash().
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS usuarios (
    id_usuario     INT           NOT NULL AUTO_INCREMENT,
    nombre         VARCHAR(100)  NOT NULL,
    usuario        VARCHAR(50)   NOT NULL,
    contrasena     VARCHAR(255)  NOT NULL,
    fecha_creacion DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_usuario),
    UNIQUE KEY uq_usuarios_usuario (usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table: estudiantes
-- Stores the academic record created by the registration form.
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS estudiantes (
    id_estudiante   INT           NOT NULL AUTO_INCREMENT,
    nombre_completo VARCHAR(150)  NOT NULL,
    cedula          VARCHAR(25)   NOT NULL,
    edad            INT           NOT NULL,
    carrera         VARCHAR(100)  NOT NULL,
    asignatura      VARCHAR(100)  NOT NULL,
    nota1           DECIMAL(5,2)  NOT NULL,
    nota2           DECIMAL(5,2)  NOT NULL,
    nota3           DECIMAL(5,2)  NOT NULL,
    promedio        DECIMAL(5,2)  NOT NULL,
    fecha_registro  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_estudiante),
    UNIQUE KEY uq_estudiantes_cedula (cedula),
    KEY idx_estudiantes_nombre (nombre_completo),
    CONSTRAINT chk_estudiantes_edad  CHECK (edad >= 15),
    CONSTRAINT chk_estudiantes_nota1 CHECK (nota1 BETWEEN 0 AND 100),
    CONSTRAINT chk_estudiantes_nota2 CHECK (nota2 BETWEEN 0 AND 100),
    CONSTRAINT chk_estudiantes_nota3 CHECK (nota3 BETWEEN 0 AND 100)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Test users
-- Both hashes were generated with password_hash(..., PASSWORD_DEFAULT):
--   admin    / Admin2026
--   profesor / Profesor2026
-- Documented test credentials only: never use real passwords here.
-- database/seed_usuarios.php regenerates these hashes if needed.
-- ------------------------------------------------------------
INSERT IGNORE INTO usuarios (id_usuario, nombre, usuario, contrasena, fecha_creacion) VALUES
(1, 'Henry Ramirez',  'admin',    '$2y$12$20rKuu/lmu1ZcmofnJMkVeFkobjvkf0c31.nSuD49i/zeO2nhUd8a', '2026-10-20 08:00:00'),
(2, 'Joseph Gantes', 'profesor', '$2y$12$KKHLmd.2zKqyn/IikLzHNuBzo6C6iv.K6PZkxO9VUCOmQq/Gst7u2', '2026-10-20 08:05:00');

-- ------------------------------------------------------------
-- Sample students (5 records).
-- promedio = (nota1 + nota2 + nota3) / 3, rounded to 2 decimals.
-- ------------------------------------------------------------
INSERT IGNORE INTO estudiantes
    (id_estudiante, nombre_completo, cedula, edad, carrera, asignatura, nota1, nota2, nota3, promedio, fecha_registro)
VALUES
(1, 'Juan Pérez',      '8-123-456',  20, 'Lic. en Desarrollo de Software', 'Programación Web',      85.00, 90.00, 80.00, 85.00, '2026-10-20 09:15:00'),
(2, 'María López',     '8-456-789',  22, 'Lic. en Ciberseguridad',         'Sistemas Operativos',   60.00, 65.00, 61.00, 62.00, '2026-10-21 10:30:00'),
(3, 'Carlos Rodríguez', '8-321-654', 19, 'Ing. de Sistemas y Computación', 'Estructura de Datos',   95.00, 88.00, 92.00, 91.67, '2026-10-22 11:45:00'),
(4, 'Ana Gómez',       '8-850-4321', 21, 'Lic. en Redes Informáticas',     'Redes de Computadoras', 70.00, 72.00, 68.00, 70.00, '2026-10-23 14:00:00'),
(5, 'Luis Martínez',   '8-750-9876', 23, 'Lic. en Desarrollo de Software', 'Programación Web',      55.00, 48.00, 60.00, 54.33, '2026-10-24 15:20:00');
