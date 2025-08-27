<?php
include __DIR__ . "/../includes/db.php";

// Verificar si viene el id por GET
if (!isset($_GET['id'])) {
    die("Error: Falta ID del producto.");
}

$id = intval($_GET['id']);

// Si se envió el formulario (POST), actualizamos
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    $categoria = $_POST['categoria'];

    $sql = "UPDATE productos 
            SET nombre=?, descripcion=?, precio=?, stock=?, categoria=? 
            WHERE id_producto=?";

    $stmt = $conn->prepare($sql);

    // 5 campos string/decimal/int + id
    $stmt->bind_param("ssdisi", $nombre, $descripcion, $precio, $stock, $categoria, $id);

    if ($stmt->execute()) {
        header("Location: reporte_productos.php");
        exit;
    } else {
        echo "Error al actualizar: " . $stmt->error;
    }
}

// Si no es POST, cargamos el producto para editar
$sql = "SELECT * FROM productos WHERE id_producto=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Producto no encontrado.");
}

$producto = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Producto</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<h2>Editar Producto</h2>
<form method="POST">
    <label>Nombre:</label>
    <input type="text" name="nombre" value="<?= htmlspecialchars($producto['nombre']) ?>" required><br>

    <label>Descripción:</label>
    <textarea name="descripcion" required><?= htmlspecialchars($producto['descripcion']) ?></textarea><br>

    <label>Precio:</label>
    <input type="number" step="0.01" name="precio" value="<?= $producto['precio'] ?>" required><br>

    <label>Stock:</label>
    <input type="number" name="stock" value="<?= $producto['stock'] ?>" required><br>

    <label>Categoría:</label>
    <input type="text" name="categoria" value="<?= htmlspecialchars($producto['categoria']) ?>" required><br>

    <button type="submit">Guardar cambios</button>
</form>
</body>
</html>
