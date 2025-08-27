<?php
session_start();
include __DIR__ . "/../includes/db.php";

// Inicializar filtro
$nombreFiltro = $_GET['nombre'] ?? '';

// Construir consulta
$sql = "SELECT * FROM productos WHERE 1";
$params = [];
$types = "";

if ($nombreFiltro !== '') {
    $sql .= " AND nombre LIKE ?";
    $params[] = "%$nombreFiltro%";
    $types .= "s";
}

// Preparar statement
$stmt = $conn->prepare($sql);
if ($stmt && !empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Productos</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="menu">
    <a href="../index.php">Inicio</a>
    <a href="../clientes/registro_cliente.php">Registro de Clientes</a>
    <a href="../clientes/reporte_clientes.php">Reporte de Clientes</a>
    <a href="../productos/registro_producto.php">Registro de Productos</a>
    <a href="../facturas/crear_factura.php">Facturación</a>
</div>

<div class="container">
    <h2>Reporte de Productos</h2>

    <!-- Filtro por nombre -->
    <form method="GET" class="filter-form">
        <input type="text" name="nombre" placeholder="Buscar por nombre" value="<?= htmlspecialchars($nombreFiltro) ?>">
        <button type="submit">Filtrar</button>
    </form>

    <table>
    <thead>
    <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Descripción</th>
        <th>Precio</th>
        <th>Stock</th>
        <th>Acciones</th> <!-- Nueva columna -->
    </tr>
    </thead>
    <tbody>
    <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id_producto'] ?></td>
                <td><?= htmlspecialchars($row['nombre']) ?></td>
                <td><?= htmlspecialchars($row['descripcion']) ?></td>
                <td><?= number_format($row['precio'], 2) ?></td>
                <td><?= $row['stock'] ?></td>
                <td>
                    <a href="editar_producto.php?id=<?= $row['id_producto'] ?>">✏️ Editar</a>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="6" style="text-align:center;">No se encontraron productos</td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>

</div>
</body>
</html>
<?php
$stmt->close();
$conn->close();
?>
