<?php
$host   = "127.0.0.1";
$user   = "root";
$pass   = "NuevaPasswordSegura123!";
$db     = "tienda";
$port   = 3306;
$socket = "/tmp/mysql.sock";

$conn = new mysqli($host, $user, $pass, $db, $port, $socket);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

?>
