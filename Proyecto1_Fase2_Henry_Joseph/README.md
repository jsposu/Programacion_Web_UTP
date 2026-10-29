<div align="center">

# Portal de Registro y Consulta de Estudiantes

**Programming Web · Project #1 — Phase 2**
Universidad Tecnológica de Panamá · Group 1S3221

`PHP 8` · `MySQL / MariaDB` · `PDO` · `HTML5` · `CSS3`

<sub>Phase 1 kept the records in PHP arrays. Phase 2 moves them into MySQL and puts a login in front.</sub>

</div>

---

## Overview

A small academic management system with:

- **Authentication** — hashed passwords, PHP sessions, protected pages, logout.
- **Full CRUD** — create, read, update and delete student records.
- **Academic logic** — average, letter grade and pass/fail status calculated in PHP.
- **Search** — by full name, part of the name, or cédula.
- **Responsive UI** — one external stylesheet, usable on a phone.

---

## Quick start (XAMPP on Linux)

**1 · Copy the project**

```bash
sudo cp -r Proyecto1_Fase2_Henry_Joseph /opt/lampp/htdocs/
```

**2 · Start XAMPP**

```bash
sudo /opt/lampp/lampp start
```

**3 · Import the database**

```bash
sudo /opt/lampp/bin/mysql -u root < /opt/lampp/htdocs/Proyecto1_Fase2_Henry_Joseph/database/registro_estudiantes.sql
```

Or open <http://localhost/phpmyadmin> → **Import** → choose `database/registro_estudiantes.sql` → **Go**.

**4 · Open the system**

```text
http://localhost/Proyecto1_Fase2_Henry_Joseph/login.php
```

> `conexion.php` ships with the XAMPP defaults (`localhost`, `root`, empty password).
> If your MySQL has a password, write it in `$pass`. If `localhost` cannot reach
> MySQL, change it to `127.0.0.1`.

---

## Test credentials

| Username | Password | Display name |
| :--- | :--- | :--- |
| `admin` | `Admin2026` | Henry Ramirez |
| `profesor` | `Profesor2026` | Joseph Gantes |

Both accounts have **identical permissions** — the statement requires authentication, not roles.
Demo accounts only; never use real credentials.

<details>
<summary>Regenerate the users</summary>

```bash
sudo /opt/lampp/bin/php /opt/lampp/htdocs/Proyecto1_Fase2_Henry_Joseph/database/seed_usuarios.php
```

</details>

---

## Modules and SQL

| Module | Page | Statement |
| :--- | :--- | :--- |
| Login | `login.php` | `SELECT … FROM usuarios WHERE usuario = ?` |
| Dashboard | `index.php` | `SELECT COUNT(*)` totals |
| **C**reate | `registro_estudiantes.php` → `procesar_estudiantes.php` | `INSERT INTO estudiantes …` |
| **R**ead | `listar_estudiantes.php` | `SELECT … WHERE nombre_completo LIKE ? OR cedula LIKE ? ORDER BY nombre_completo` |
| **U**pdate | `editar_estudiante.php` | `UPDATE estudiantes SET … WHERE id_estudiante = ?` |
| **D**elete | `eliminar_estudiante.php` | `DELETE FROM estudiantes WHERE id_estudiante = ?` (POST only) |
| Logout | `logout.php` | — |

Every query that receives user data uses **PDO prepared statements**.

---

## Structure

```text
Proyecto1_Fase2_Henry_Joseph/
├── conexion.php                 # single PDO connection ($conexion)
├── sesion.php                   # sessions, access control, flash messages
├── funciones.php                # average, letter grade, status, validations
├── encabezado.php / pie.php     # shared header and footer
│
├── login.php                    # authentication
├── logout.php                   # ends the session
├── index.php                    # dashboard
├── registro_estudiantes.php     # CREATE — form
├── procesar_estudiantes.php     # CREATE — validation + INSERT
├── listar_estudiantes.php       # READ — listing + search
├── editar_estudiante.php        # UPDATE
├── eliminar_estudiante.php      # DELETE with confirmation
│
├── CSS/estilos.css              # external stylesheet
├── img/logo.png
├── database/
│   ├── registro_estudiantes.sql  # schema, constraints, users, sample rows
│   └── seed_usuarios.php         # optional user loader
│
├── DOCUMENTATION.md             # technical walkthrough
├── README.md                    # this file
└── README.txt                   # plain-text install guide
```

---

## Academic rules

```text
promedio = (nota1 + nota2 + nota3) / 3

promedio >= 70  →  Aprobado
promedio <  70  →  Reprobado

A >= 91   B >= 81   C >= 71   D >= 61   F otherwise
```

The average is calculated in PHP before saving. The letter grade and status are
recalculated every time the listing is rendered, so editing the grades updates
them automatically.

---

## Validation

| Rule | Where |
| :--- | :--- |
| No empty required field | `validarEstudiante()` |
| Age ≥ 15 (integer) | PHP + `CHECK` constraint |
| Grades between 0 and 100 | PHP + `CHECK` constraints |
| Cédula unique | PHP check + `UNIQUE` index |
| Existing record required for edit/delete | `editar_estudiante.php`, `eliminar_estudiante.php` |

---

## Security

| Concern | Measure |
| :--- | :--- |
| SQL injection | Prepared statements (`ATTR_EMULATE_PREPARES = false`) |
| Password storage | `password_hash()` / `password_verify()` |
| Session fixation | `session_regenerate_id(true)` after login |
| Unauthorized access | `requerir_sesion()` before any output |
| XSS | All output escaped with `limpiar()` |
| Error disclosure | Details in `error_log()`, generic message to the user |
| Accidental deletes | GET only navigates; DELETE requires a confirmed POST |

---

## Verification

Tested against MariaDB with 36 HTTP assertions covering every case in section 10
of the statement: login success/failure, protected redirects, valid insert,
duplicate cédula, grade > 100, age < 15, listing, search by cédula and by partial
name, average recalculation, preserved `fecha_registro`, delete confirmation,
cancelled delete, logout, and XSS escaping.

---

## Troubleshooting

| Message / symptom | Fix |
| :--- | :--- |
| *No se pudo conectar con la base de datos* | MySQL is not running, or the SQL file was not imported |
| *Usuario o contraseña incorrectos* with the documented accounts | `usuarios` is empty — re-run the SQL file or `seed_usuarios.php` |
| PHP source code shown in the browser | Apache is not running, or the folder is not inside `htdocs` |
| *Access denied for user 'root'@'localhost'* | MySQL has a password — set it in `conexion.php` |

---

## Documentation

`DOCUMENTATION.md` walks through the request flow, each file, the database
design, the exact SQL, the security decisions and how to add fields or roles.
