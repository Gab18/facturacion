<?php
session_start();
include __DIR__ . "/../includes/db.php";

// Trae clientes
$clientes = $conn->query("SELECT * FROM clientes ORDER BY nombre");

// Trae productos
$productos = $conn->query("SELECT * FROM productos ORDER BY nombre");

$mensaje = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Factura</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="menu">
    <a href="../index.php">Inicio</a>
    <a href="../clientes/registro_cliente.php">Registro de Clientes</a>
    <a href="../productos/registro_producto.php">Registro de Productos</a>
    <a href="../facturas/reporte_facturas.php">Reporte de Facturas</a>
</div>

<div class="container">
    <h2>Crear Factura</h2>

    <?php if($mensaje): ?>
        <div class="msg success"><?= htmlspecialchars($mensaje) ?></div>
    <?php endif; ?>

    <form method="POST" action="guardar_factura.php">
        <div class="field">
            <label for="id_cliente">Cliente:</label>
            <select name="id_cliente" id="id_cliente" required>
                <option value="">-- Seleccione Cliente --</option>
                <?php while($c = $clientes->fetch_assoc()): ?>
                    <option value="<?= $c['id_cliente'] ?>"><?= htmlspecialchars($c['nombre'] . ' ' . $c['apellido']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <h3>Productos</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Precio</th>
                    <th>Cantidad</th>
                </tr>
            </thead>
            <tbody>
                <?php while($p = $productos->fetch_assoc()): ?>
                <tr>
                    <td><?= $p['id_producto'] ?></td>
                    <td><?= htmlspecialchars($p['nombre']) ?></td>
                    <td><?= number_format($p['precio'], 2) ?></td>
                    <td>
                        <input type="number" name="cantidad[<?= $p['id_producto'] ?>]" min="0" value="0">
                        <input type="hidden" name="id_producto[]" value="<?= $p['id_producto'] ?>">
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div style="text-align:right; margin-top:10px;">
            <button type="submit">Guardar Factura</button>
        </div>
    </form>
</div>
</body>
</html>
