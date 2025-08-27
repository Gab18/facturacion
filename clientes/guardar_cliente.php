<?php
session_start();
include __DIR__ . "/../includes/db.php";

// Inicializa el mensaje
$mensaje = "";

// Verificar que el formulario se haya enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger y sanitizar entradas
    $nombre = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');

    // Validaciones básicas
    if ($nombre === '' || $apellido === '') {
        $_SESSION['flash'] = "Nombre y apellido son obligatorios.";
    } else {
        // Prepared statement para insertar cliente
        $stmt = $conn->prepare("INSERT INTO clientes (nombre, apellido, email, telefono, direccion) VALUES (?, ?, ?, ?, ?)");
        if ($stmt === false) {
            $_SESSION['flash'] = "Error en la preparación: " . $conn->error;
        } else {
            $stmt->bind_param("sssss", $nombre, $apellido, $email, $telefono, $direccion);
            if ($stmt->execute()) {
                $_SESSION['flash'] = "Cliente registrado correctamente.";
            } else {
                $_SESSION['flash'] = "Error al guardar: " . $stmt->error;
            }
            $stmt->close();
        }
    }

    // Redirigir para evitar re-envío de formulario (Post/Redirect/Get)
    header("Location: registro_cliente.php");
    exit;
} else {
    // Si alguien entra directamente sin POST
    header("Location: registro_cliente.php");
    exit;
}

$conn->close();
?>
