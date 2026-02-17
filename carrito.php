<?php
session_start();
require_once "config/conexion.php";
$db = (new Conexion())->conectar();

// inicializar carrito si no existe (estructura: [producto_id => cantidad])
if(!isset($_SESSION['carrito'])) $_SESSION['carrito'] = [];

// acciones: agregar (desde menu.php), actualizar cantidades, eliminar item
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    // agregar (desde producto)
    if(isset($_POST['agregar']) && isset($_POST['producto_id'])){
        $id = intval($_POST['producto_id']);
        $cantidad = max(1, intval($_POST['cantidad'] ?? 1));
        if(isset($_SESSION['carrito'][$id])) $_SESSION['carrito'][$id] += $cantidad;
        else $_SESSION['carrito'][$id] = $cantidad;
        header("Location: carrito.php");
        exit;
    }

    // actualizar cantidades
    if(isset($_POST['action']) && $_POST['action'] === 'update'){
        foreach($_POST['cantidad'] as $id => $cant){
            $id = intval($id);
            $cant = max(0, intval($cant));
            if($cant <= 0) unset($_SESSION['carrito'][$id]);
            else $_SESSION['carrito'][$id] = $cant;
        }
        header("Location: carrito.php");
        exit;
    }
}

// obtener items completos
$items = [];
$total = 0;
if(!empty($_SESSION['carrito'])){
    $ids = implode(',', array_map('intval', array_keys($_SESSION['carrito'])));
    $stmt = $db->query("SELECT * FROM productos WHERE id IN ($ids)");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach($rows as $r){
        $cantidad = $_SESSION['carrito'][$r['id']];
        $subtotal = $r['precio'] * $cantidad;
        $items[] = ['producto' => $r, 'cantidad' => $cantidad, 'subtotal' => $subtotal];
        $total += $subtotal;
    }
}
?>
<!doctype html>
<html lang="es">
<head><meta charset="utf-8"><title>Carrito</title>
<link rel="stylesheet" href="assets/css/style.css"></head>
<body>
<div class="container">
  <h1>Carrito</h1>

  <?php if(empty($items)): ?>
    <p>Tu carrito está vacío. <a href="menu.php">Ver menú</a></p>
  <?php else: ?>
    <form method="post">
      <input type="hidden" name="action" value="update">
      <table class="carrito">
        <tr><th>Producto</th><th>Cantidad</th><th>Precio</th><th>Subtotal</th></tr>
        <?php foreach($items as $it): ?>
          <tr>
            <td><?= htmlspecialchars($it['producto']['nombre']) ?></td>
            <td>
              <input type="number" name="cantidad[<?= $it['producto']['id'] ?>]" value="<?= $it['quantity'] ?? $it['cantidad'] ?>" min="0" style="width:80px">
              <div style="font-size:12px;color:#64748b">Poner 0 para eliminar</div>
            </td>
            <td>S/ <?= number_format($it['producto']['precio'],2) ?></td>
            <td>S/ <?= number_format($it['subtotal'],2) ?></td>
          </tr>
        <?php endforeach; ?>
        <tr><td colspan="3" class="total">Total</td><td class="total">S/ <?= number_format($total,2) ?></td></tr>
      </table>

      <div style="display:flex;gap:8px;margin-top:10px">
        <button class="btn-primary" type="submit">Actualizar carrito</button>
        <a class="btn-ghost" href="menu.php">Seguir comprando</a>
        <a class="btn-primary" href="finalizar.php" style="background:#10b981">Finalizar pedido</a>
      </div>
    </form>
  <?php endif; ?>
</div>
</body>
</html>
