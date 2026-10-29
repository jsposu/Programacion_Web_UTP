================================================================
 PORTAL DE REGISTRO Y CONSULTA DE ESTUDIANTES
 Programming Web - Project #1, Phase 2 (PHP + MySQL)
 Henry Ramirez & Joseph Gantes - Group 1S3221
================================================================

CONTENTS
--------
1. What the system does
2. Requirements
3. Installation on XAMPP (Linux)
4. Test credentials
5. Project structure
6. How the security requirements are met
7. Quick test checklist
8. Troubleshooting
9. Technical documentation (DOCUMENTATION.md)

----------------------------------------------------------------
1. WHAT THE SYSTEM DOES
----------------------------------------------------------------
A web application that stores student academic records in MySQL
and is protected by a login.

  login.php                 signs the user in (sessions + password_verify)
  index.php                 dashboard with statistics and shortcuts
  registro_estudiantes.php  registration form (CREATE)
  procesar_estudiantes.php  validation, average calculation and INSERT
  listar_estudiantes.php    listing + search by name or cedula (READ)
  editar_estudiante.php     edit an existing record (UPDATE)
  eliminar_estudiante.php   delete with confirmation (DELETE)
  logout.php                ends the session

Academic rules kept from Phase 1:
  - promedio = (nota1 + nota2 + nota3) / 3
  - 70 or more  -> "Aprobado"
  - less than 70 -> "Reprobado"
  - letter grade: A >= 91, B >= 81, C >= 71, D >= 61, F otherwise

----------------------------------------------------------------
2. REQUIREMENTS
----------------------------------------------------------------
  - XAMPP (Apache + MySQL/MariaDB + PHP 7.4 or newer)
  - PHP extensions: pdo, pdo_mysql (both included in XAMPP)

----------------------------------------------------------------
3. INSTALLATION ON XAMPP (LINUX)
----------------------------------------------------------------
Step 1 - Copy the project
    sudo cp -r Proyecto1_Fase2_Henry_Joseph /opt/lampp/htdocs/

Step 2 - Start XAMPP
    sudo /opt/lampp/lampp start

Step 3 - Import the database
    Option A (terminal):
        sudo /opt/lampp/bin/mysql -u root < /opt/lampp/htdocs/Proyecto1_Fase2_Henry_Joseph/database/registro_estudiantes.sql

    Option B (phpMyAdmin):
        http://localhost/phpmyadmin -> Import -> choose
        database/registro_estudiantes.sql -> Go

    The script creates the database "registro_estudiantes", both
    tables, two test users and five sample students.

Step 4 - Check the connection settings
    Open conexion.php. The default values match a standard XAMPP
    installation:
        $host = 'localhost';
        $db   = 'registro_estudiantes';
        $user = 'root';
        $pass = '';
    If you set a MySQL password in XAMPP, write it in $pass.
    If "localhost" cannot reach MySQL, change it to '127.0.0.1'.

Step 5 - Open the system
    http://localhost/Proyecto1_Fase2_Henry_Joseph/login.php

----------------------------------------------------------------
4. TEST CREDENTIALS
----------------------------------------------------------------
    user: admin       password: Admin2026
    user: profesor    password: Profesor2026

These are demonstration accounts created with password_hash().
They must never be used as real credentials.

If you need to regenerate the users (for example after changing
the passwords), run:

    sudo /opt/lampp/bin/php /opt/lampp/htdocs/Proyecto1_Fase2_Henry_Joseph/database/seed_usuarios.php

----------------------------------------------------------------
5. PROJECT STRUCTURE
----------------------------------------------------------------
Proyecto1_Fase2_Henry_Joseph/
|
|-- conexion.php                 single PDO connection reused by every page
|-- sesion.php                   session start, access control, flash messages
|-- funciones.php                average, letter grade, status and validations
|-- encabezado.php               shared header and navigation
|-- pie.php                      shared footer
|
|-- login.php                    login form + credential check
|-- logout.php                   destroys the session
|-- index.php                    dashboard (protected)
|
|-- registro_estudiantes.php     CREATE - form
|-- procesar_estudiantes.php     CREATE - validation + INSERT
|-- listar_estudiantes.php       READ   - listing and search
|-- editar_estudiante.php        UPDATE - form + UPDATE statement
|-- eliminar_estudiante.php      DELETE - confirmation + DELETE statement
|
|-- CSS/estilos.css              external stylesheet (responsive)
|-- img/logo.png                 institutional logo
|-- database/
|    |-- registro_estudiantes.sql  database, tables, users and sample data
|    |-- seed_usuarios.php         optional PHP loader for the test users
|
|-- README.txt                   this file

----------------------------------------------------------------
6. HOW THE SECURITY REQUIREMENTS ARE MET
----------------------------------------------------------------
  - Passwords: password_hash() when creating users and
    password_verify() when signing in. Plain passwords are never
    stored.
  - SQL injection: every query that receives user data uses PDO
    prepared statements with "?" placeholders.
  - Sessions: requerir_sesion() runs at the top of every protected
    page and redirects to login.php when there is no valid session.
    session_regenerate_id(true) is called after a successful login.
  - Output escaping: data printed in HTML goes through limpiar()
    (htmlspecialchars) to prevent XSS.
  - Error handling: database errors are written to the server log
    with error_log(); visitors only see a generic message, never the
    connection credentials or the SQL error.
  - HTTP methods: GET is used for navigation and searches; POST is
    required for every operation that modifies data, including the
    DELETE, which also asks for confirmation first.
  - Database constraints: UNIQUE on usuario and cedula, NOT NULL on
    required fields, CHECK on edad (>= 15) and on the three grades
    (0 - 100).

----------------------------------------------------------------
7. QUICK TEST CHECKLIST
----------------------------------------------------------------
   1. Sign in with admin / Admin2026            -> dashboard opens
   2. Sign in with a wrong password             -> error message
   3. Open registro_estudiantes.php in a private window
      without signing in                        -> redirect to login
   4. Register a valid student                  -> saved, message shown
   5. Register the same cedula again            -> validation message
   6. Register a grade above 100                -> save is blocked
   7. Open listar_estudiantes.php               -> all records listed
   8. Search by cedula                          -> only that record
   9. Edit the grades of a student              -> average is recalculated
  10. Delete a student and confirm              -> record removed
  11. Delete a student and cancel               -> record preserved
  12. Click "Cerrar Sesión"                      -> login page, protected
                                                  pages no longer open

----------------------------------------------------------------
8. TROUBLESHOOTING
----------------------------------------------------------------
"No se pudo conectar con la base de datos"
    - MySQL is not running: sudo /opt/lampp/lampp start
    - The database was not imported: repeat Step 3
    - Wrong password or host in conexion.php

"Usuario o contraseña incorrectos" with the documented accounts
    - The SQL file was not imported, or the usuarios table is empty
    - Re-run: sudo /opt/lampp/bin/php database/seed_usuarios.php

The page shows PHP source code instead of the result
    - Apache is not running, or the files were not copied into
      /opt/lampp/htdocs

"Access denied for user 'root'@'localhost'"
    - XAMPP's MySQL has a root password: write it in conexion.php

----------------------------------------------------------------
9. TECHNICAL DOCUMENTATION
----------------------------------------------------------------
See DOCUMENTATION.md in the project root. It explains the folder
structure, the request flow of every page, the database design,
the custom functions, the exact SQL statements, the security
measures and how to extend the system with new fields or roles.
