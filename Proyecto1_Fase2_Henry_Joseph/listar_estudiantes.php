<?php
// ============================================================
//  listar_estudiantes.php
//  Student listing and search (READ).
//  Queries the estudiantes table directly in MySQL.
// ============================================================
require_once 'conexion.php';
require_once 'sesion.php';
require_once 'funciones.php';

requerir_sesion();

// ------------------------------------------------------------
// 1. Search by name or cedula (GET method)
// ------------------------------------------------------------
$busqueda = trim($_GET['buscar'] ?? '');

if ($busqueda !== '') {
    // Escape wildcards so the user searches for literal text
    $patron = '%' . addcslashes($busqueda, '\\%_') . '%';

    // 2. SELECT with WHERE, LIKE and ORDER BY: prepared statement
    $sql = "SELECT id_estudiante, nombre_completo, cedula, edad, carrera, asignatura,
                   nota1, nota2, nota3, promedio, fecha_registro
            FROM estudiantes
            WHERE nombre_completo LIKE ? OR cedula LIKE ?
            ORDER BY nombre_completo ASC";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([$patron, $patron]);
} else {
    // 3. No search: every record sorted by name
    $sql = "SELECT id_estudiante, nombre_completo, cedula, edad, carrera, asignatura,
                   nota1, nota2, nota3, promedio, fecha_registro
            FROM estudiantes
            ORDER BY nombre_completo ASC";
    $stmt = $conexion->query($sql);
}

// 4. Fetch every row ($stmt is also iterable with foreach)
$estudiantes = $stmt->fetchAll();

$titulo = 'Consultar Estudiantes';
require 'encabezado.php';
?>

    <h2>Estudiantes Registrados</h2>

    <!-- Search: GET because it only reads information, never changes it -->
    <form method="GET" action="listar_estudiantes.php" class="buscador">
        <input type="search" name="buscar" placeholder="Buscar por nombre o cédula..."
               value="<?= limpiar($busqueda) ?>" aria-label="Buscar estudiante">
        <button type="submit" class="btn">Buscar</button>
        <?php if ($busqueda !== ''): ?>
            <a href="listar_estudiantes.php" class="btn btn-secundario">Limpiar</a>
        <?php endif; ?>
    </form>

    <p class="texto-conteo">
        <?php if ($busqueda !== ''): ?>
            Resultados para <strong>"<?= limpiar($busqueda) ?>"</strong>:
            <?= count($estudiantes) ?> estudiante(s).
        <?php else: ?>
            Total de estudiantes registrados: <strong><?= count($estudiantes) ?></strong>.
        <?php endif; ?>
    </p>

    <?php if (empty($estudiantes)): ?>

        <div class="alert alert-info">
            <?php if ($busqueda !== ''): ?>
                No se encontraron estudiantes que coincidan con la búsqueda.
            <?php else: ?>
                No existen estudiantes registrados todavía.
            <?php endif; ?>
        </div>

    <?php else: ?>

        <!-- Table with the data retrieved from MySQL -->
        <div class="tabla-responsive">
            <table class="tabla-listado">
                <thead>
                    <tr>
                        <th>Cédula</th>
                        <th>Nombre</th>
                        <th>Carrera</th>
                        <th>Asignatura</th>
                        <th>Promedio</th>
                        <th>Letra</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($estudiantes as $fila): ?>
                        <?php
                        // Status and letter are recalculated on every read
                        $promedio = (float) $fila['promedio'];
                        $letra    = obtenerLetraCalificacion($promedio);
                        $estado   = determinarEstado($promedio);
                        ?>
                        <tr>
                            <td><?= limpiar($fila['cedula']) ?></td>
                            <td><?= limpiar($fila['nombre_completo']) ?></td>
                            <td><?= limpiar($fila['carrera']) ?></td>
                            <td><?= limpiar($fila['asignatura']) ?></td>
                            <td class="celda-promedio"><?= number_format($promedio, 2) ?></td>
                            <td><?= limpiar($letra) ?></td>
                            <td>
                                <span class="status-badge <?= claseEstado($estado) ?>"><?= limpiar($estado) ?></span>
                            </td>
                            <td class="acciones">
                                <!-- Edit and delete navigate with GET (record id) -->
                                <a href="editar_estudiante.php?id=<?= (int) $fila['id_estudiante'] ?>"
                                   class="editar" title="Editar estudiante">✏️ Editar</a>
                                <a href="eliminar_estudiante.php?id=<?= (int) $fila['id_estudiante'] ?>"
                                   class="eliminar" title="Eliminar estudiante">🗑️ Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    <?php endif; ?>

    <div class="form-acciones">
        <a href="registro_estudiantes.php" class="btn">➕ Registrar Estudiante</a>
    </div>

<?php require 'pie.php'; ?>
