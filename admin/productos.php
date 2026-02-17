<?php
require_once "../config/conexion.php";
session_start();
if(!isset($_SESSION['admin'])) header("Location: login.php");
$db = (new Conexion())->conectar();

// eliminar si viene ?delete=id
if(isset($_GET['delete'])){
    $id = intval($_GET['delete']);
    $stm = $db->prepare("DELETE FROM productos WHERE id = ?");
    $stm->execute([$id]);
    header("Location: productos.php");
    exit;
}

$stm = $db->query("SELECT p.*, c.nombre AS categoria FROM productos p LEFT JOIN categorias c ON p.categoria_id = c.id ORDER BY p.id DESC");
$productos = $stm->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="es">
<head><meta charset="utf-8"><title>Admin - Productos</title>
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
    <h2>Productos</h2>
    <a href="producto_create.php" class="btn btn-primary">Nuevo producto</a>
    <table style="width:100%;margin-top:12px;border-collapse:collapse">
      <tr style="background:#f1f5f9">
        <th>Imagen</th><th>Nombre</th><th>Categoria</th><th>Precio</th><th>Acciones</th>
      </tr>
      <?php foreach($productos as $p): ?>
      <tr>
        <td><img src="../assets/img/<?= htmlspecialchars($p['imagen'])?>" style="width:80px;border-radius:6px"></td>
        <td><?= htmlspecialchars($p['nombre'])?></td>
        <td><?= htmlspecialchars($p['categoria'])?></td>
        <td>S/ <?= number_format($p['precio'],2)?></td>
        <td>
          <a href="producto_edit.php?id=<?= $p['id']?>" class="btn-ghost">Editar</a>
          <a href="productos.php?delete=<?= $p['id']?>" onclick="return confirm('Eliminar este producto?')" class="btn-ghost">Eliminar</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </table>
  </main>
</div>
</body>
</html>
