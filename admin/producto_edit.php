<?php
require_once "../config/conexion.php";
session_start();
if(!isset($_SESSION['admin'])) header("Location: login.php");
$db = (new Conexion())->conectar();

$id = intval($_GET['id'] ?? 0);
if(!$id) header("Location: productos.php");

// traer producto
$stm = $db->prepare("SELECT * FROM productos WHERE id = ?");
$stm->execute([$id]);
$p = $stm->fetch(PDO::FETCH_ASSOC);
if(!$p) header("Location: productos.php");

// categorias
$cats = $db->query("SELECT * FROM categorias")->fetchAll(PDO::FETCH_ASSOC);

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    $precio = floatval($_POST['precio']);
    $categoria_id = ($_POST['categoria_id'] === '') ? null : intval($_POST['categoria_id']);
    $imagen = $p['imagen'];

    if(isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0){
        $targetDir = '../assets/img/';
        $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
        $filename = uniqid('p_', true) . "." . $ext;
        move_uploaded_file($_FILES['imagen']['tmp_name'], $targetDir.$filename);
        if($imagen && file_exists($targetDir.$imagen)) @unlink($targetDir.$imagen);
        $imagen = $filename;
    }

    $stm = $db->prepare("UPDATE productos SET nombre=?, descripcion=?, precio=?, imagen=?, categoria_id=? WHERE id=?");
    $stm->execute([$nombre,$descripcion,$precio,$imagen,$categoria_id,$id]);
    header("Location: productos.php");
    exit;
}
?>
<!doctype html>
<html lang="es">
<head><meta charset="utf-8"><title>Editar Producto</title>
<link rel="stylesheet" href="../assets/css/style.css"></head>
<body>
<div class="container">
  <h2>Editar producto</h2>
  <form method="post" enctype="multipart/form-data" style="max-width:600px">
    <div class="field">
      <input class="input" name="nombre" value="<?= htmlspecialchars($p['nombre']) ?>" required>
    </div>
    <div class="field">
      <textarea class="input" name="descripcion"><?= htmlspecialchars($p['descripcion']) ?></textarea>
    </div>
    <div class="field">
      <input class="input" name="precio" type="number" step="0.01" value="<?= $p['precio'] ?>" required>
    </div>
    <div class="field">
      <select name="categoria_id" class="input">
        <option value="">-- Sin categoría --</option>
        <?php foreach($cats as $c): ?>
          <option value="<?= $c['id'] ?>" <?= ($c['id']==$p['categoria_id'])?'selected':'' ?>><?= htmlspecialchars($c['nombre']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="field">
      <label>Imagen actual:</label><br>
      <?php if($p['imagen']): ?>
        <img src="../assets/img/<?= htmlspecialchars($p['imagen']) ?>" style="width:120px;border-radius:8px;margin:6px 0">
      <?php endif; ?>
      <input type="file" name="imagen" accept="image/*">
    </div>
    <div style="display:flex;gap:8px">
      <button class="btn-primary" type="submit">Guardar</button>
      <a class="btn-ghost" href="productos.php">Cancelar</a>
    </div>
  </form>
</div>
</body>
</html>
