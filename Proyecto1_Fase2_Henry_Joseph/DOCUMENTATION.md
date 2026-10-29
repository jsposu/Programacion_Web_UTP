# Code Documentation — Student Portal, Phase 2

Portal de Registro y Consulta de Estudiantes
Programming Web — Project #1, Phase 2 (PHP + MySQL)
Henry Ramirez & Joseph Gantes — Group 1S3221

This document explains **what every file does, how a request travels through the
system, which SQL is executed, and why each security decision was made**. The
interface is in Spanish; the code comments, this document and `README.txt` are in
English.

Related files: `README.txt` (installation on XAMPP) and
`database/registro_estudiantes.sql` (database script).

---

## 1. What the system does

A student record system backed by MySQL with a login gate in front of it.

```
login.php ──► index.php (dashboard) ──► registro_estudiantes.php ──► procesar_estudiantes.php
                    │                            (form)                      (INSERT)
                    │
                    ├──► listar_estudiantes.php (table + search) ──► editar_estudiante.php   (UPDATE)
                    │                                                     └──► eliminar_estudiante.php (DELETE)
                    │
                    └──► logout.php ──► login.php
```

Every arrow that leads back to a listing goes through a **redirect after the
write** (POST → redirect → GET). That way a browser refresh never re-sends the
form and never inserts/deletes twice.

---

## 2. Folder structure

```
Proyecto1_Fase2_Henry_Joseph/
├── conexion.php                 # PDO connection (single place)
├── sesion.php                   # session start, access control, flash messages
├── funciones.php                # custom academic + validation functions
├── encabezado.php               # shared <header>, <nav>, opens <main>
├── pie.php                      # closes </main>, shared <footer>
│
├── login.php                    # authentication form + password_verify
├── logout.php                   # ends the session
├── index.php                    # protected dashboard with statistics
│
├── registro_estudiantes.php     # CREATE — form
├── procesar_estudiantes.php     # CREATE — validation + INSERT
├── listar_estudiantes.php       # READ  — listing + search
├── editar_estudiante.php        # UPDATE — form + UPDATE
├── eliminar_estudiante.php      # DELETE — confirmation + DELETE
│
├── CSS/estilos.css              # external stylesheet, responsive
├── img/logo.png                 # institutional logo
└── database/
    ├── registro_estudiantes.sql # schema, constraints, test users, sample rows
    └── seed_usuarios.php        # optional PHP loader for the two users
```

---

## 3. How a request is processed

Every protected page starts with the same four lines:

```php
require_once 'conexion.php';   // 1. open the database connection ($conexion)
require_once 'sesion.php';     // 2. start the session + helpers
require_once 'funciones.php';  // 3. custom functions
requerir_sesion();             // 4. no session? -> header('Location: login.php')
```

`requerir_sesion()` runs **before any HTML is printed**, which is mandatory:
PHP can only send a `Location` header while nothing has been written to the
output yet.

A page then follows one of two paths:

```
GET  ── show the form / the listing (read-only)
POST ── validate → write to MySQL → setFlash() → header('Location: ...') → exit;
```

`exit;` after every redirect is required; without it PHP would keep executing
the rest of the page after the browser has already been told to leave.

---

## 4. File reference

### 4.1 `conexion.php` — database connection

Builds one PDO object named `$conexion`, reused by every page.

```php
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$opciones = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

$conexion = new PDO($dsn, $user, $pass, $opciones);
```

* `ERRMODE_EXCEPTION` — failures are catchable with `try/catch`.
* `FETCH_ASSOC` — rows arrive as `$row['cedula']`, not `$row[2]`.
* `EMULATE_PREPARES = false` — MySQL receives real prepared statements, not
  client-side string substitution.
* `charset=utf8mb4` — accented names like *Pérez* and *Rodríguez* store correctly.

If the connection fails, the real reason goes to the server log
(`error_log()`), and the visitor sees only:

> No se pudo conectar con la base de datos…

Credentials (`localhost`, `root`, empty password) are the XAMPP defaults and are
the only values to edit when deploying elsewhere.

### 4.2 `sesion.php` — sessions and messages

| Function | Purpose |
|---|---|
| `session_start()` | started once, guarded by `session_status()` |
| `requerir_sesion()` | redirects to `login.php` when `$_SESSION['id_usuario']` is absent |
| `nombreUsuarioActual()` | full name of the logged user (`$_SESSION['nombre']`) |
| `usuarioActual()` | login name (`$_SESSION['usuario']`) |
| `setFlash($tipo, $mensaje)` | stores a one-shot message in the session |
| `getFlash()` | returns and removes that message |
| `mostrarFlash()` | prints it with `alert-success` / `alert-danger` / `alert-info` |

Flash messages exist because `header('Location: ...')` throws away everything
already printed. The message waits in the session and is shown by the next page
(`encabezado.php` calls `mostrarFlash()` right after opening `<main>`).

### 4.3 `funciones.php` — academic logic and validation

| Function | Behaviour |
|---|---|
| `calcularPromedio($n1,$n2,$n3)` | `round((n1+n2+n3)/3, 2)` |
| `obtenerLetraCalificacion($p)` | A ≥ 91, B ≥ 81, C ≥ 71, D ≥ 61, otherwise F |
| `determinarEstado($p)` | `Aprobado` when `$p >= NOTA_MINIMA_APROBATORIA` (70), else `Reprobado` |
| `claseEstado($estado)` | returns `approved` / `failed` for the CSS badge |
| `limpiar($valor)` | `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')` — output escaping |
| `formatearFecha($fecha)` | `Y-m-d H:i:s` → `d/m/Y H:i` |
| `validarEstudiante($datos,$conexion,$idExcluir)` | returns an array of error strings |

`validarEstudiante()` is the single validation source for **both** the create and
the update flow, so the rules can never drift apart:

| Field | Rule |
|---|---|
| `nombre_completo` | not empty |
| `carrera` / `asignatura` | not empty (chosen from the `<select>`) |
| `edad` | not empty, integer (`ctype_digit`), ≥ 15 |
| `nota1..nota3` | not empty, numeric, between 0 and 100 |
| `cedula` | not empty, and `SELECT id_estudiante FROM estudiantes WHERE cedula = ?` must not return **another** student's id |

The `$idExcluir` parameter is what allows an edit to keep the student's own
cédula while still rejecting somebody else's:

```php
if ($existente && (int) $existente['id_estudiante'] !== (int) $idExcluir) {
    $errores[] = "La cédula ... ya se encuentra registrada.";
}
```

### 4.4 `encabezado.php` / `pie.php` — shared layout

`encabezado.php` prints the document head, the header with the logo, and the
navigation (only when a session exists, so the login page has no menu). It reads
two optional variables a page may define beforehand:

```php
$titulo    = 'Registrar Estudiante';  // tab + <h1>
$claseMain = 'main-login';            // container width: main-ancho | main-login
require 'encabezado.php';
```

It also emits the pending flash message. `pie.php` closes `</main>`, prints the
footer and closes the document.

### 4.5 `login.php` — authentication

```php
$sql = "SELECT id_usuario, nombre, usuario, contrasena
        FROM usuarios WHERE usuario = ? LIMIT 1";
$stmt = $conexion->prepare($sql);
$stmt->execute([$usuario]);
$registro = $stmt->fetch();

if ($registro && password_verify($contrasena, $registro['contrasena'])) {
    session_regenerate_id(true);
    $_SESSION['id_usuario'] = (int) $registro['id_usuario'];
    $_SESSION['usuario']    = $registro['usuario'];
    $_SESSION['nombre']     = $registro['nombre'];
    header('Location: index.php');
    exit;
}
```

* `password_verify()` compares the typed password against the bcrypt hash; the
  plain password is never stored or compared directly.
* `session_regenerate_id(true)` issues a new session id after login, preventing
  session fixation.
* A wrong username and a wrong password produce **the same** message
  (*"Usuario o contraseña incorrectos. Verifique sus credenciales."*) so an
  attacker cannot discover which usernames exist.
* An already authenticated user is redirected to `index.php` instead of seeing
  the form again.

### 4.6 `logout.php`

Empties `$_SESSION`, expires the session cookie and calls `session_destroy()`,
then redirects to `login.php`.

### 4.7 `index.php` — dashboard

Runs two aggregate queries and shows the result as cards:

```php
$total      = (int) $conexion->query("SELECT COUNT(*) FROM estudiantes")->fetchColumn();
$aprobados  = /* SELECT COUNT(*) FROM estudiantes WHERE promedio >= ? */;
$reprobados = $total - $aprobados;
```

`fetchColumn()` is used because the query returns a single value. The threshold
comes from the constant `NOTA_MINIMA_APROBATORIA`, not from a hard-coded `70`.

### 4.8 `registro_estudiantes.php` — CREATE form

Pure presentation. It reads two session keys that `procesar_estudiantes.php` may
have written after a failed validation:

```php
$errores = $_SESSION['errores_registro'] ?? [];
$valores = $_SESSION['valores_registro'] ?? [];
unset($_SESSION['errores_registro'], $_SESSION['valores_registro']);
```

`$valores` refills every input (`value="<?= $valor('cedula') ?>"`) and marks the
right `<option selected>`, so a failed submission never loses the user's work.
The grades are grouped in `.fila-notas` (3 columns on desktop, 1 on mobile).

### 4.9 `procesar_estudiantes.php` — CREATE logic

```
1. Reject anything that is not POST      -> redirect to the form
2. Build $datos[] from $_POST (trim)
3. $errores = validarEstudiante($datos, $conexion)
4. If errors: store them in the session -> redirect back to the form
5. Calculate promedio, letra, estado, fecha = date('Y-m-d H:i:s')
6. Prepared INSERT
7. setFlash('success', '...fue registrado correctamente...')
8. header('Location: listar_estudiantes.php'); exit;
```

```php
$sql = "INSERT INTO estudiantes
            (nombre_completo, cedula, edad, carrera, asignatura,
             nota1, nota2, nota3, promedio, fecha_registro)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
```

The `try/catch (PDOException)` around the insert is a second safety net: even if
two requests passed the duplicate-cédula check at the same instant, the `UNIQUE`
index on `cedula` stops the second one.

### 4.10 `listar_estudiantes.php` — READ and search

```php
$busqueda = trim($_GET['buscar'] ?? '');

if ($busqueda !== '') {
    $patron = '%' . addcslashes($busqueda, '\\%_') . '%';

    $sql = "SELECT id_estudiante, nombre_completo, cedula, edad, carrera, asignatura,
                   nota1, nota2, nota3, promedio, fecha_registro
            FROM estudiantes
            WHERE nombre_completo LIKE ? OR cedula LIKE ?
            ORDER BY nombre_completo ASC";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([$patron, $patron]);
} else {
    $sql = "... FROM estudiantes ORDER BY nombre_completo ASC";
    $stmt = $conexion->query($sql);
}
$estudiantes = $stmt->fetchAll();
```

* **GET** is correct here: a search does not modify anything, and the URL can be
  bookmarked or shared.
* `addcslashes($busqueda, '\\%_')` escapes `%` and `_` so the user searches for
  literal characters instead of wildcards.
* `LIKE` is case-insensitive under the `utf8mb4_unicode_ci` collation, which is
  why `lopez` finds *María López*.
* The table shows **Cédula, Nombre, Carrera, Asignatura, Promedio, Letra,
  Estado, Acciones** — the academic result and letter grade are recalculated for
  every row (`determinarEstado`, `obtenerLetraCalificacion`).
* No rows → informative alert, different text when it is a search with no
  matches versus an empty database.

### 4.11 `editar_estudiante.php` — UPDATE

Two phases in one file:

```php
// 1. read the id from GET (link) or POST (hidden field)
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($id === null || $id === false) {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
}
if ($id === null || $id === false || $id < 1) { /* flash + back to listing */ }

// 2. load the current row; a missing row cannot be edited
$stmt = $conexion->prepare("SELECT ... FROM estudiantes WHERE id_estudiante = ?");
$stmt->execute([$id]);
$registro = $stmt->fetch();
if (!$registro) { /* flash "no existe" + back to listing */ }

// 3. POST: validate, recalculate and UPDATE
$errores = validarEstudiante($valores, $conexion, $id);   // own cedula allowed
...
$sql = "UPDATE estudiantes
        SET nombre_completo = ?, cedula = ?, edad = ?, carrera = ?, asignatura = ?,
            nota1 = ?, nota2 = ?, nota3 = ?, promedio = ?
        WHERE id_estudiante = ?";
```

`fecha_registro` is deliberately **absent** from the `SET` list, so the original
date is preserved (verified by test: value before == value after). On validation
failure the page re-renders with the typed values and the error list instead of
redirecting.

### 4.12 `eliminar_estudiante.php` — DELETE with confirmation

```
GET  ?id=7  -> SELECT the row -> show "¿Está seguro...?" with name + data
POST   id=7 -> DELETE FROM estudiantes WHERE id_estudiante = ?
            -> rowCount() > 0 ? success : error
            -> redirect to listar_estudiantes.php
```

The mutation only exists inside the `if ($_SERVER['REQUEST_METHOD'] === 'POST')`
block, so a link, a prefetch or an accidental click on the button's URL cannot
delete anything. The PDF requires exactly this (confirmation + POST). "Cancelar"
is a plain link back to the listing and touches no data.

---

## 5. Database design

Database: **`registro_estudiantes`** (`utf8mb4` / `utf8mb4_unicode_ci`).

### `usuarios`

| Column | Type | Notes |
|---|---|---|
| `id_usuario` | INT | PK, AUTO_INCREMENT |
| `nombre` | VARCHAR(100) | NOT NULL |
| `usuario` | VARCHAR(50) | NOT NULL, **UNIQUE** |
| `contrasena` | VARCHAR(255) | NOT NULL, bcrypt hash |
| `fecha_creacion` | DATETIME | NOT NULL, default `CURRENT_TIMESTAMP` |

### `estudiantes`

| Column | Type | Notes |
|---|---|---|
| `id_estudiante` | INT | PK, AUTO_INCREMENT |
| `nombre_completo` | VARCHAR(150) | NOT NULL, indexed (`idx_estudiantes_nombre`) |
| `cedula` | VARCHAR(25) | NOT NULL, **UNIQUE** |
| `edad` | INT | NOT NULL, `CHECK (edad >= 15)` |
| `carrera` | VARCHAR(100) | NOT NULL |
| `asignatura` | VARCHAR(100) | NOT NULL |
| `nota1`, `nota2`, `nota3` | DECIMAL(5,2) | NOT NULL, `CHECK (0..100)` each |
| `promedio` | DECIMAL(5,2) | NOT NULL, computed by PHP before INSERT/UPDATE |
| `fecha_registro` | DATETIME | NOT NULL, default `CURRENT_TIMESTAMP` |

Two layers of protection on purpose: **MySQL constraints** block bad data even if
somebody bypasses the application, and **PHP validation** produces friendly
Spanish messages instead of a raw driver error.

Seed data: 2 users (`admin`/`Admin2026`, `profesor`/`Profesor2026`) and 5 sample
students whose `promedio` already matches the formula.

---

## 6. Security measures

| Risk | Measure | Where |
|---|---|---|
| SQL injection | PDO prepared statements with `?` on every query that receives input | all CRUD files |
| Plain-text passwords | `password_hash()` / `password_verify()` | SQL seed + `login.php` |
| Session fixation | `session_regenerate_id(true)` after login | `login.php` |
| Unauthenticated access | `requerir_sesion()` before any output | every protected page |
| XSS | `limpiar()` / `htmlspecialchars()` on all echoed data | views |
| Information leak | `error_log()` for details, generic message for the visitor | `conexion.php`, writes |
| Accidental deletion / replay | GET only reads; POST performs the DELETE; PRG after every write | CRUD files |
| Duplicate records | `UNIQUE` on `cedula` and `usuario` + PHP check | SQL + `funciones.php` |

Known limitation (documented, not required by the statement): there are **no
roles**. `admin` and `profesor` have identical permissions; only the displayed
name differs. There is also no CSRF token on the POST forms.

---

## 7. Stylesheet (`CSS/estilos.css`)

Kept from Phase 1: the blue NetLinks palette (`:root` variables), gradient
background, translucent card (`main`), `.btn`, `.alert`, `.status-badge`
(`.approved` / `.failed`) and the footer.

Added in Phase 2:

| Selector | Purpose |
|---|---|
| `.main-ancho` / `.main-login` | wide container for the table / narrow card for login |
| `.user-info`, `.logout-link` | logged user name and red logout button in the nav |
| `.fila-notas` | 3 grade inputs in a grid, 1 column under 700 px |
| `.tabla-responsive` | horizontal scroll wrapper on phones |
| `.tabla-listado` | overrides `th { width: 40% }` so the 8-column table stays readable |
| `.acciones` | edit/delete links, wrapped so they never break the row |
| `.alert-success` | green flash message |
| `.cards-grid`, `.card-stat`, `.card-accion` | dashboard statistics and shortcuts |
| `.buscador` | search input + buttons |
| `.confirm-card` | red-tinted delete confirmation box |
| `@media (max-width: 700px)` | mobile layout: full-width buttons, stacked grades, scrollable table |

---

## 8. Verification performed

The application was executed locally against MariaDB and exercised with HTTP
requests (36 assertions, all passing):

* login page loads; wrong password shows the required message; correct
  credentials redirect; dashboard greets the user by name
* every protected page redirects to `login.php` without a session, and again
  after `logout.php`
* valid student is inserted with the correct average; duplicate cédula, grade
  > 100 and age < 15 are rejected **and not stored**
* listing shows the 5 sample students with average and status
* search by cédula returns only that student; partial, lowercase name search
  works
* editing grades recalculates the average and keeps `fecha_registro`; another
  student's cédula is rejected; a nonexistent id redirects safely
* `GET eliminar_estudiante.php?id=X` shows the confirmation and does **not**
  delete; the POST deletes; an invalid id redirects
* a name containing `<script>` is rendered escaped, not executed
* `procesar_estudiantes.php` refuses GET
* `php -l` on every file: no syntax errors; no PHP warnings in the server log

---

## 9. How to extend it

**Add a field (e.g. `correo`)**

1. `ALTER TABLE estudiantes ADD correo VARCHAR(120) NOT NULL DEFAULT '';`
2. add the input to `registro_estudiantes.php` and `editar_estudiante.php`
3. add it to `$datos` in `procesar_estudiantes.php` and `editar_estudiante.php`
4. include it in the `INSERT`/`UPDATE` column lists and `execute([...])`
5. add the column to the `SELECT` in `listar_estudiantes.php` and the `<th>`/`<td>`
6. add the rule to `validarEstudiante()`

**Add roles**

1. `ALTER TABLE usuarios ADD rol ENUM('admin','profesor') NOT NULL DEFAULT 'profesor';`
2. select `rol` in `login.php` and store `$_SESSION['rol']`
3. add `requerir_admin()` to `sesion.php`
4. call it in `eliminar_estudiante.php` and hide the Eliminar link for
   `profesor` users in `listar_estudiantes.php`

---

## 10. Use of AI tools

Per section 14 of the statement, AI assistance was used to design the database
schema, review the CRUD structure and set up the local test environment.
All code was read, executed and verified by the team, and every member must be
able to explain any part of it during the presentation.
