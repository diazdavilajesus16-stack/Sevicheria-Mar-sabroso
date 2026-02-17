<?php
require_once "../config/conexion.php";
session_start();
if(!isset($_SESSION['admin'])) header("Location: login.php");
$db = (new Conexion())->conectar();

$id = intval($_GET['id'] ?? 0);
if(!$id) header("Location: pedidos.php");

$stm = $db->prepare("SELECT * FROM pedidos WHERE id = ?");
$stm->execute([$id]);
$pedido = $stm->fetch(PDO::FETCH_ASSOC);

$stm2 = $db->prepare("SELECT dp.*, p.nombre FROM detalle_pedidos dp JOIN productos p ON dp.producto_id = p.id WHERE dp.pedido_id = ?");
$stm2->execute([$id]);
$detalles = $stm2->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="es">
<head><meta charset="utf-8"><title>Detalle pedido</title>
<link rel="stylesheet" href="../assets/css/style.css"></head>
<body>
<div class="container">
  <h2>Pedido #<?= $pedido['id'] ?></h2>
  <p><strong>Cliente:</strong> <?= htmlspecialchars($pedido['cliente']) ?></p>
  <p><strong>Teléfono:</strong> <?= htmlspecialchars($pedido['telefono']) ?></p>
  <p><strong>Dirección:</strong> <?= htmlspecialchars($pedido['direccion']) ?></p>
  <p><strong>Fecha:</strong> <?= $pedido['fecha'] ?></p>
  <p><strong>Total:</strong> S/ <?= number_format($pedido['total'],2) ?></p>

  <h3>Detalle</h3>
  <ul>
    <?php foreach($detalles as $d): ?>
      <li><?= htmlspecialchars($d['nombre']) ?> — <?= $d['cantidad'] ?> x S/ <?= number_format($d['subtotal']/$d['cantidad'],2) ?> = S/ <?= number_format($d['subtotal'],2) ?></li>
    <?php endforeach; ?>
  </ul>

  <a class="btn-ghost" href="pedidos.php">Volver</a>
</div>
</body>
</html>
