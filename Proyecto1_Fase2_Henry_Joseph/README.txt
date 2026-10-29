================================================================
 PORTAL DE REGISTRO Y CONSULTA DE ESTUDIANTES
 Programming Web - Project #1, Phase 2 (PHP + MySQL)
 Henry Ramirez & Joseph Gantes - Group 1S3221
================================================================

Quick installation guide (XAMPP on Linux).
For the full documentation see README.md and DOCUMENTATION.md.


1. COPY THE PROJECT
----------------------------------------------------------------
    sudo cp -r Proyecto1_Fase2_Henry_Joseph /opt/lampp/htdocs/


2. START XAMPP
----------------------------------------------------------------
    sudo /opt/lampp/lampp start


3. IMPORT THE DATABASE
----------------------------------------------------------------
Terminal:
    sudo /opt/lampp/bin/mysql -u root < /opt/lampp/htdocs/Proyecto1_Fase2_Henry_Joseph/database/registro_estudiantes.sql

phpMyAdmin:
    http://localhost/phpmyadmin -> Import -> database/registro_estudiantes.sql -> Go

The script creates the database "registro_estudiantes", the tables
"usuarios" and "estudiantes", two test users and five sample students.


4. CONFIGURE THE CONNECTION
----------------------------------------------------------------
Open conexion.php and check:

    $host = 'localhost';
    $db   = 'registro_estudiantes';
    $user = 'root';
    $pass = '';

These are the XAMPP defaults. If your MySQL has a password, set it in
$pass. If "localhost" cannot reach MySQL, use '127.0.0.1'.


5. OPEN THE SYSTEM
----------------------------------------------------------------
    http://localhost/Proyecto1_Fase2_Henry_Joseph/login.php


TEST USERS
----------------------------------------------------------------
    admin    / Admin2026      (Henry Ramirez)
    profesor / Profesor2026   (Joseph Gantes)

Both accounts have the same permissions: the statement requires
authentication, not roles. They are demo accounts only.

To regenerate them with password_hash():
    sudo /opt/lampp/bin/php /opt/lampp/htdocs/Proyecto1_Fase2_Henry_Joseph/database/seed_usuarios.php


FILES
----------------------------------------------------------------
    conexion.php              PDO connection reused by every page
    sesion.php                sessions, access control, flash messages
    funciones.php             average, letter grade, status, validations
    encabezado.php / pie.php  shared layout
    login.php / logout.php    authentication
    index.php                 dashboard
    registro_estudiantes.php  CREATE - form
    procesar_estudiantes.php  CREATE - validation + INSERT
    listar_estudiantes.php    READ - listing and search
    editar_estudiante.php     UPDATE
    eliminar_estudiante.php   DELETE with confirmation
    CSS/estilos.css           external stylesheet (responsive)
    database/                 SQL script and optional user loader
    README.md                 full guide in Markdown
    DOCUMENTATION.md          technical walkthrough of the code


QUICK CHECKS
----------------------------------------------------------------
    1. Sign in with admin / Admin2026          -> dashboard opens
    2. Sign in with a wrong password           -> error message
    3. Open a protected page without session   -> redirect to login
    4. Register a student                      -> saved with average
    5. Repeat the same cedula                  -> validation message
    6. Grade above 100 or age below 15         -> blocked
    7. Search by cedula or partial name        -> matching records
    8. Edit the grades                         -> average recalculated
    9. Delete and confirm                      -> record removed
   10. Delete and cancel                       -> record preserved
   11. Log out                                 -> session closed
