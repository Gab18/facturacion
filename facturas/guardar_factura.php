<?php
session_start();
include __DIR__ . "/../includes/db.php";

// Verificar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: crear_factura.php");
    exit;
}

// Recoger datos
$id_cliente = intval($_POST['id_cliente'] ?? 0);
$id_productos = $_POST['id_producto'] ?? [];
$cantidades = $_POST['cantidad'] ?? [];

// Validar cliente
if ($id_cliente <= 0) {
    $_SESSION['flash'] = "Debe seleccionar un cliente.";
    header("Location: crear_factura.php");
    exit;
}

// Validar que haya al menos un producto con cantidad > 0
$productoSeleccionado = false;
foreach ($cantidades as $id => $cantidad) {
    if ($cantidad > 0) {
        $productoSeleccionado = true;
        break;
    }
}
if (!$productoSeleccionado) {
    $_SESSION['flash'] = "Debe ingresar al menos una cantidad válida para los productos seleccionados.";
    header("Location: crear_factura.php");
    exit;
}

// Calcular total y preparar detalles
$total = 0;
$detalles = [];
foreach ($cantidades as $id_producto => $cantidad) {
    $cantidad = intval($cantidad);
    if ($cantidad <= 0) continue;

    // Traer precio del producto y stock actual
    $stmt = $conn->prepare("SELECT precio, stock FROM productos WHERE id_producto = ?");
    if (!$stmt) die("Error en prepare SELECT producto: " . $conn->error);
    $stmt->bind_param("i", $id_producto);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) die("Error: Producto con ID $id_producto no encontrado.");
    $producto = $result->fetch_assoc();
    $stmt->close();

    // Validar stock suficiente
    if ($cantidad > $producto['stock']) {
        $_SESSION['flash'] = "No hay suficiente stock para el producto: {$id_producto}. Stock disponible: {$producto['stock']}";
        header("Location: crear_factura.php");
        exit;
    }

    $subtotal = $cantidad * $producto['precio'];
    $total += $subtotal;
    $detalles[] = [
        'id_producto' => $id_producto,
        'cantidad' => $cantidad,
        'subtotal' => $subtotal
    ];
}

// Insertar factura sin número aún
$stmt = $conn->prepare("INSERT INTO facturas (id_cliente, total) VALUES (?, ?)");
if (!$stmt) die("Error en prepare INSERT factura: " . $conn->error);
$stmt->bind_param("id", $id_cliente, $total);
$stmt->execute();
$id_factura = $stmt->insert_id;
$stmt->close();

// Generar número de factura
$numero_factura = "FAC-" . str_pad($id_factura, 4, "0", STR_PAD_LEFT);

// Actualizar la factura con el número
$stmt = $conn->prepare("UPDATE facturas SET numero_factura = ? WHERE id_factura = ?");
if (!$stmt) die("Error en prepare UPDATE numero_factura: " . $conn->error);
$stmt->bind_param("si", $numero_factura, $id_factura);
$stmt->execute();
$stmt->close();

// Insertar detalles y restar stock
foreach ($detalles as $d) {
    // Insertar detalle
    $stmt = $conn->prepare("INSERT INTO detalle_factura (id_factura, id_producto, cantidad, subtotal) VALUES (?, ?, ?, ?)");
    if (!$stmt) die("Error en prepare INSERT detalle_factura: " . $conn->error);
    $stmt->bind_param("iiid", $id_factura, $d['id_producto'], $d['cantidad'], $d['subtotal']);
    if (!$stmt->execute()) die("Error en execute INSERT detalle_factura: " . $stmt->error);
    $stmt->close();

    // Restar stock del producto
    $stmt = $conn->prepare("UPDATE productos SET stock = stock - ? WHERE id_producto = ?");
    if (!$stmt) die("Error en prepare UPDATE stock: " . $conn->error);
    $stmt->bind_param("ii", $d['cantidad'], $d['id_producto']);
    if (!$stmt->execute()) die("Error en execute UPDATE stock: " . $stmt->error);
    $stmt->close();
}

// Mensaje de éxito y redirección
$_SESSION['flash'] = "Factura creada correctamente. Número de factura: $numero_factura";
header("Location: crear_factura.php");
exit;
?>
