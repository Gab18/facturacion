<?php
include "../includes/db.php";

// Capturar búsqueda si se envió
$busqueda = "";
if (isset($_GET['buscar'])) {
    $busqueda = $conn->real_escape_string($_GET['buscar']);
}

// Consulta db adaptada a tu tabla
$sql = "SELECT * FROM clientes 
        WHERE nombre LIKE '%$busqueda%' 
           OR apellido LIKE '%$busqueda%' 
           OR email LIKE '%$busqueda%' 
        ORDER BY id_cliente ASC";

$result = $conn->query($sql);

// Validar consulta
if (!$result) {
    die("Error en la consulta: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Clientes</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="menu">
        <a href="../index.php">Inicio</a>
        <a href="../clientes/registro_cliente.php">Registro de Clientes</a>
        <a href="../clientes/reporte_clientes.php">Reporte de Clientes</a>
        <a href="../productos/registro_producto.php">Registro de Productos</a>
        <a href="../facturas/reporte_facturas.php">Reporte de Facturas</a>
    </div>
    <div class="container">
        <h2>Reporte de Clientes</h2>

        <form method="get">
            <input type="text" name="buscar" placeholder="Buscar por nombre, apellido o email" value="<?php echo htmlspecialchars($busqueda); ?>">
            <button type="submit">Buscar</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre Completo</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Dirección</th>
                    <th>Fecha Registro</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id_cliente']; ?></td>
                        <td><?php echo $row['nombre'] . ' ' . $row['apellido']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td><?php echo $row['telefono']; ?></td>
                        <td><?php echo $row['direccion']; ?></td>
                        <td><?php echo $row['fecha_registro']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
