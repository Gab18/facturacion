<?php
include "../includes/db.php";

$id_factura = $_GET['id'] ?? 0;
if (!$id_factura) { 
    echo "Factura no válida"; 
    exit; 
}

// Datos de la factura y cliente
$sqlFactura = "SELECT f.id_factura, f.numero_factura, f.fecha, f.total, c.nombre, c.apellido, c.email
               FROM facturas f
               JOIN clientes c ON f.id_cliente = c.id_cliente
               WHERE f.id_factura = ?";
$stmt = $conn->prepare($sqlFactura);
$stmt->bind_param("i", $id_factura);
$stmt->execute();
$factura = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Detalle de productos con toda la info
$sqlDetalle = "SELECT p.nombre, p.categoria, p.descripcion, p.precio, df.cantidad, df.subtotal
               FROM detalle_factura df
               JOIN productos p ON df.id_producto = p.id_producto
               WHERE df.id_factura = ?";
$stmt2 = $conn->prepare($sqlDetalle);
$stmt2->bind_param("i", $id_factura);
$stmt2->execute();
$detalle = $stmt2->get_result();
$stmt2->close();
?>

<h3>Factura #<?= $factura['numero_factura'] ?></h3>
<p><strong>Cliente:</strong> <?= htmlspecialchars($factura['nombre'] . ' ' . $factura['apellido']) ?> (<?= htmlspecialchars($factura['email']) ?>)</p>
<p><strong>Fecha:</strong> <?= $factura['fecha'] ?></p>
<p><strong>Total Factura:</strong> $<?= number_format($factura['total'],2) ?></p>

<hr>

<h4>Detalle de Productos</h4>
<table style="width:100%; border-collapse:collapse; margin-top:10px;">
    <thead>
        <tr style="background:#f2f2f2;">
            <th style="border:1px solid #ccc; padding:8px;">Nombre</th>
            <th style="border:1px solid #ccc; padding:8px;">Categoría</th>
            <th style="border:1px solid #ccc; padding:8px;">Descripción</th>
            <th style="border:1px solid #ccc; padding:8px;">Precio Unitario</th>
            <th style="border:1px solid #ccc; padding:8px;">Cantidad</th>
            <th style="border:1px solid #ccc; padding:8px;">Subtotal</th>
        </tr>
    </thead>
    <tbody>
        <?php while($row = $detalle->fetch_assoc()): ?>
        <tr>
            <td style="border:1px solid #ccc; padding:8px;"><?= htmlspecialchars($row['nombre']) ?></td>
            <td style="border:1px solid #ccc; padding:8px;"><?= htmlspecialchars($row['categoria']) ?></td>
            <td style="border:1px solid #ccc; padding:8px;"><?= htmlspecialchars($row['descripcion']) ?></td>
            <td style="border:1px solid #ccc; padding:8px;">$<?= number_format($row['precio'],2) ?></td>
            <td style="border:1px solid #ccc; padding:8px;"><?= $row['cantidad'] ?></td>
            <td style="border:1px solid #ccc; padding:8px;">$<?= number_format($row['subtotal'],2) ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
