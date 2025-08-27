<?php
include "../includes/db.php";  // Conexión a la base de datos

$mensaje = "";

if (isset($_POST['guardar'])) {
    // Obtener datos del formulario
    $nombre      = $conn->real_escape_string($_POST['nombre']);
    $descripcion = $conn->real_escape_string($_POST['descripcion']);
    $precio      = $conn->real_escape_string($_POST['precio']);
    $stock       = $conn->real_escape_string($_POST['stock']);
    $categoria   = $conn->real_escape_string($_POST['categoria']);

    // Validación simple
    if (empty($nombre) || empty($precio)) {
        $mensaje = "El nombre y precio del producto son obligatorios.";
    } else {
        // Insertar en la base de datos
        $sql = "INSERT INTO productos (nombre, descripcion, precio, stock, categoria)
                VALUES ('$nombre', '$descripcion', '$precio', '$stock', '$categoria')";

        if ($conn->query($sql) === TRUE) {
            $mensaje = "Producto registrado exitosamente.";
        } else {
            $mensaje = "Error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Productos</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="menu">
        <a href="../index.php">Inicio</a>
        <a href="../clientes/registro_cliente.php">Registro de Clientes</a>
        <a href="../clientes/reporte_clientes.php">Reporte de Clientes</a>
        <a href="../productos/registro_producto.php">Registro de Productos</a>
        <a href="../productos/reporte_productos.php">Reporte de Productos</a>
        <a href="../facturas/crear_factura.php">Facturación</a>
    </div>
    <div class="container">
        <h2>Registro de Productos</h2>

        <?php if($mensaje != ""): ?>
            <div class="mensaje"><?php echo $mensaje; ?></div>
        <?php endif; ?>

        <form action="" method="post" class="formulario">
            <label>Nombre:</label>
            <input type="text" name="nombre" required>

            <label>Descripción:</label>
            <textarea name="descripcion"></textarea>

            <label>Precio:</label>
            <input type="number" step="0.01" name="precio" required>

            <label>Stock:</label>
            <input type="number" name="stock" value="0">

            <label>Categoría:</label>
            <input type="text" name="categoria">

            <button type="submit" name="guardar">Guardar Producto</button>
        </form>
    </div>
</body>
</html>
