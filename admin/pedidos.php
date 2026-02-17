<?php
require_once "../config/conexion.php";
session_start();
if(!isset($_SESSION['admin'])) header("Location: login.php");
$db = (new Conexion())->conectar();

// cambiar estado
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pedido_id'], $_POST['estado'])){
    $stm = $db->prepare("UPDATE pedidos SET estado = ? WHERE id = ?");
    $stm->execute([$_POST['estado'], intval($_POST['pedido_id'])]);
    header("Location: pedidos.php");
    exit;
}

// obtener pedidos
$stm = $db->query("SELECT * FROM pedidos ORDER BY fecha DESC");
$pedidos = $stm->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="es">
<head><meta charset="utf-8"><title>Admin - Pedidos</title>
<link rel="stylesheet" href="../assets/css/style.css"></head>
<body>
<div class="container admin-wrap">
  <aside class="admin-nav">
    <h3>Admin</h3>
    <a href="dashboard.php">Dashboard</a><br>
    <a href="productos.php">Productos</a><br>
    <a href="pedidos.php">Pedidos</a><br>
    <a href="logout.php">Cerrar sesión</a>
  </aside>

  <main class="admin-content">
    <h2>Pedidos</h2>
    <table style="width:100%;border-collapse:collapse">
      <tr style="background:#f1f5f9"><th>ID</th><th>Cliente</th><th>Fecha</th><th>Total</th><th>Estado</th><th>Detalle</th></tr>
      <?php foreach($pedidos as $p): ?>
      <tr>
        <td><?= $p['id'] ?></td>
        <td><?= htmlspecialchars($p['cliente']) ?><br><?= htmlspecialchars($p['telefono']) ?></td>
        <td><?= $p['fecha'] ?></td>
        <td>S/ <?= number_format($p['total'],2) ?></td>
        <td>
          <form method="post" style="display:flex;gap:6px;align-items:center">
            <input type="hidden" name="pedido_id" value="<?= $p['id'] ?>">
            <select name="estado" class="input">
              <option value="pendiente" <?= $p['estado']=='pendiente'?'selected':'' ?>>pendiente</option>
              <option value="listo" <?= $p['estado']=='listo'?'selected':'' ?>>listo</option>
              <option value="entregado" <?= $p['estado']=='entregado'?'selected':'' ?>>entregado</option>
            </select>
            <button class="btn-ghost" type="submit">Guardar</button>
          </form>
        </td>
        <td>
          <a href="pedido_detalle.php?id=<?= $p['id'] ?>">Ver detalle</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </table>
  </main>
</div>
</body>
</html>
