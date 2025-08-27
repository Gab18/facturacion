<?php
session_start();
include __DIR__ . "/../includes/db.php";

// Inicializa el mensaje
$mensaje = "";

// Procesar formulario si se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger y sanitizar entradas
    $nombre = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');

    // Validaciones 
    if ($nombre === '' || $apellido === '') {
        $mensaje = "Nombre y apellido son obligatorios.";
    } else {
        // Prepared statement para insertar cliente
        $stmt = $conn->prepare("INSERT INTO clientes (nombre, apellido, email, telefono, direccion) VALUES (?, ?, ?, ?, ?)");
        if ($stmt === false) {
            $mensaje = "Error en la preparación: " . $conn->error;
        } else {
            $stmt->bind_param("sssss", $nombre, $apellido, $email, $telefono, $direccion);
            if ($stmt->execute()) {
                $_SESSION['flash'] = "Cliente registrado correctamente.";
                // Redirigir para evitar re-envío de formulario (Post/Redirect/Get)
                header("Location: registro_cliente.php");
                exit;
            } else {
                $mensaje = "Error al guardar: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}


$flash = $_SESSION['flash'] ?? '';
if ($flash) {
    unset($_SESSION['flash']);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Registro de Clientes</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .form-card { max-width: 700px; margin: 20px auto; padding: 20px; background:#fff; border-radius:8px; box-shadow:0 4px 12px rgba(0,0,0,0.08);}
        .field { margin-bottom:12px; }
        label { display:block; margin-bottom:6px; font-weight:600; }
        .msg { padding:10px; border-radius:6px; margin-bottom:12px; }
        .msg.success { background:#d4edda; color:#155724; border:1px solid #c3e6cb; }
        .msg.error { background:#f8d7da; color:#721c24; border:1px solid #f5c6cb; }
    </style>
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
        <div class="form-card">
            <h2>Registro de Clientes</h2>

            <?php if($flash): ?>
                <div class="msg success"><?= htmlspecialchars($flash) ?></div>
            <?php endif; ?>

            <?php if($mensaje): ?>
                <div class="msg error"><?= htmlspecialchars($mensaje) ?></div>
            <?php endif; ?>

            <form method="POST" action="registro_cliente.php" novalidate>
                <div class="field">
                    <label for="nombre">Nombre <span style="color:#c00">*</span></label>
                    <input id="nombre" name="nombre" type="text" required placeholder="Ej. Gabriel" value="<?= isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : '' ?>">
                </div>

                <div class="field">
                    <label for="apellido">Apellido <span style="color:#c00">*</span></label>
                    <input id="apellido" name="apellido" type="text" required placeholder="Ej. Arthur Nardi" value="<?= isset($_POST['apellido']) ? htmlspecialchars($_POST['apellido']) : '' ?>">
                </div>

                <div class="field">
                    <label for="email">Correo</label>
                    <input id="email" name="email" type="email" placeholder="correo@ejemplo.com" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                </div>

                <div class="field">
                    <label for="telefono">Teléfono</label>
                    <input id="telefono" name="telefono" type="text" placeholder="809-555-1234" value="<?= isset($_POST['telefono']) ? htmlspecialchars($_POST['telefono']) : '' ?>">
                </div>

                <div class="field">
                    <label for="direccion">Dirección</label>
                    <input id="direccion" name="direccion" type="text" placeholder="Dirección, ciudad" value="<?= isset($_POST['direccion']) ? htmlspecialchars($_POST['direccion']) : '' ?>">
                </div>

                <div style="text-align:right">
                    <button type="submit">Guardar Cliente</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
