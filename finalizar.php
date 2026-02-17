<?php
session_start();
require_once "config/conexion.php";
$db = (new Conexion())->conectar();

// si carrito vacío => redirigir a menú
if(empty($_SESSION['carrito'])) {
    header("Location: menu.php");
    exit;
}

// obtener items para mostrar resumen y calcular total
$items = []; $total = 0;
$ids = implode(',', array_map('intval', array_keys($_SESSION['carrito'])));
$stmt = $db->query("SELECT * FROM productos WHERE id IN ($ids)");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach($rows as $r){
    $cantidad = $_SESSION['carrito'][$r['id']];
    $subtotal = $r['precio'] * $cantidad;
    $items[] = ['producto' => $r, 'cantidad' => $cantidad, 'subtotal' => $subtotal];
    $total += $subtotal;
}

$errors = [];
$success = false;
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $cliente = trim($_POST['cliente']) ?: null;
    $telefono = trim($_POST['telefono']) ?: null;
    $direccion = trim($_POST['direccion']) ?: null;

    if(!$cliente) $errors[] = "Ingresa el nombre del cliente.";

    if(empty($errors)){
        try{
            // iniciar transacción
            $db->beginTransaction();
            // insertar pedido
            $stm = $db->prepare("INSERT INTO pedidos (cliente, telefono, direccion, total, estado) VALUES (?, ?, ?, ?, 'pendiente')");
            $stm->execute([$cliente, $telefono, $direccion, $total]);
            $pedido_id = $db->lastInsertId();
            // insertar detalle
            $stmDet = $db->prepare("INSERT INTO detalle_pedidos (pedido_id, producto_id, cantidad, subtotal) VALUES (?, ?, ?, ?)");
            foreach($items as $it){
                $stmDet->execute([$pedido_id, $it['producto']['id'], $it['cantidad'], $it['subtotal']]);
            }
            $db->commit();
            // limpiar carrito
            unset($_SESSION['carrito']);
            $success = true;
        } catch(Exception $e){
            $db->rollBack();
            $errors[] = "No se pudo procesar el pedido: " . $e->getMessage();
        }
    }
}
?>
<!doctype html>
<html lang="es">
<head><meta charset="utf-8"><title>Finalizar pedido</title>
<link rel="stylesheet" href="assets/css/style.css"></head>
<body>
<div class="container">
  <h1>Confirmar pedido</h1>

  <?php if($success): ?>
    <div style="background:#ecfdf5;border:1px solid #bbf7d0;padding:14px;border-radius:8px">
      <h3>Pedido recibido ✅</h3>
      <p>Gracias, el pedido fue registrado correctamente. En breve lo prepararemos.</p>
      <a class="btn-ghost" href="index.php">Volver al inicio</a>
    </div>
  <?php else: ?>

    <?php if($errors): foreach($errors as $err): ?>
      <div style="color:#b91c1c;margin-bottom:8px"><?= htmlspecialchars($err) ?></div>
    <?php endforeach; endif; ?>

    <h3>Resumen</h3>
    <ul>
      <?php foreach($items as $it): ?>
        <li><?= htmlspecialchars($it['producto']['nombre']) ?> — <?= $it['cantidad'] ?> x S/ <?= number_format($it['producto']['precio'],2) ?> = S/ <?= number_format($it['subtotal'],2) ?></li>
      <?php endforeach; ?>
    </ul>
    <p><strong>Total: S/ <?= number_format($total,2) ?></strong></p>

    <form method="post" style="max-width:520px">
      <div class="field"><input class="input" name="cliente" placeholder="Nombre del cliente" required></div>
      <div class="field"><input class="input" name="telefono" placeholder="Teléfono"></div>
      <div class="field"><input class="input" name="direccion" placeholder="Dirección (si aplica)"></div>
      <div style="display:flex;gap:8px">
        <button class="btn-primary" type="submit">Confirmar pedido</button>
        <a class="btn-ghost" href="carrito.php">Volver al carrito</a>
      </div>
    </form>

  <?php endif; ?>
</div>
</body>
</html>
