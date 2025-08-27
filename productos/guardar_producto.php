<?php
include "../includes/db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre    = $_POST['nombre'] ?? '';
    $categoria = $_POST['categoria'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $precio    = $_POST['precio'] ?? 0;
    $stock     = $_POST['stock'] ?? 0;

    // Validación básica
    if (empty($nombre) || empty($categoria) || $precio <= 0 || $stock < 0) {
        die("Por favor complete todos los campos correctamente.");
    }

    // Preparar consulta segura
    $stmt = $conn->prepare("INSERT INTO productos (nombre, categoria, descripcion, precio, stock) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssdi", $nombre, $categoria, $descripcion, $precio, $stock);

    if ($stmt->execute()) {
        header("Location: reporte_producto.php");
        exit;
    } else {
        echo "Error al guardar el producto: " . $stmt->error;
    }

    $stmt->close();
}
$conn->close();
?>
