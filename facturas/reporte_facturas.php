<?php
include "../includes/db.php";

$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : '';
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : '';
$numero = isset($_GET['numero']) ? $_GET['numero'] : '';

$sql = "SELECT f.id_factura, f.numero_factura, f.fecha, f.total, 
               c.nombre, c.apellido 
        FROM facturas f
        JOIN clientes c ON f.id_cliente = c.id_cliente
        WHERE 1=1";

if ($fecha_inicio && $fecha_fin) {
    $sql .= " AND f.fecha BETWEEN '$fecha_inicio 00:00:00' AND '$fecha_fin 23:59:59'";
}

if ($numero) {
    $sql .= " AND f.numero_factura LIKE '%$numero%'";
}

$sql .= " ORDER BY f.fecha DESC";

$result = $conn->query($sql);
if (!$result) die("Error en la consulta: " . $conn->error);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Facturas</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        /* Modal básico */
        #modal-detalle {
            display: none;
            position: fixed; top:0; left:0; width:100%; height:100%;
            background: rgba(0,0,0,0.6);
            justify-content:center; align-items:center;
        }
        #modal-detalle .modal-contenido {
            background:#fff; padding:20px; border-radius:8px;
            max-width:700px; width:90%; position:relative;
            max-height:80%; overflow-y:auto;
        }
        #cerrar-modal { position:absolute; top:10px; right:15px; cursor:pointer; font-weight:bold; }
        table { width:100%; border-collapse: collapse; }
        th, td { border:1px solid #ccc; padding:8px; text-align:left; }
        th { background:#f2f2f2; }
        .btn-ver-detalle { padding:5px 10px; cursor:pointer; }
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
    <h2>Reporte de Facturas</h2>

    <form method="get">
        <label>Rango de Fecha:</label><br>
        <input type="date" name="fecha_inicio" value="<?= htmlspecialchars($fecha_inicio) ?>">
        <input type="date" name="fecha_fin" value="<?= htmlspecialchars($fecha_fin) ?>"><br><br>

        <label>Número de Factura:</label><br>
        <input type="text" name="numero" placeholder="Buscar por número" value="<?= htmlspecialchars($numero) ?>"><br><br>

        <button type="submit">Filtrar</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>ID Factura</th>
                <th>Número</th>
                <th>Fecha</th>
                <th>Cliente</th>
                <th>Total</th>
                <th>Detalle</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id_factura'] ?></td>
                    <td><?= $row['numero_factura'] ?></td>
                    <td><?= $row['fecha'] ?></td>
                    <td><?= htmlspecialchars($row['nombre'] . ' ' . $row['apellido']) ?></td>
                    <td>$<?= number_format($row['total'],2) ?></td>
                    <td>
                        <button class="btn-ver-detalle" data-id="<?= $row['id_factura'] ?>">Ver</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Modal -->
<div id="modal-detalle">
    <div class="modal-contenido">
        <span id="cerrar-modal">X</span>
        <div id="contenido-detalle"></div>
    </div>
</div>

<script>
const botones = document.querySelectorAll(".btn-ver-detalle");
const modal = document.getElementById("modal-detalle");
const contenido = document.getElementById("contenido-detalle");
const cerrar = document.getElementById("cerrar-modal");

botones.forEach(btn => {
    btn.addEventListener("click", () => {
        const id = btn.dataset.id;
        fetch(`detalle_factura_modal.php?id=${id}`)
            .then(res => res.text())
            .then(html => {
                contenido.innerHTML = html;
                modal.style.display = "flex";
            });
    });
});

cerrar.addEventListener("click", () => { modal.style.display = "none"; });

window.addEventListener("click", (e) => { if(e.target == modal){ modal.style.display = "none"; } });
</script>

</body>
</html>
