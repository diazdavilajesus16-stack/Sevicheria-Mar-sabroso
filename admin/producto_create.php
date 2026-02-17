<?php
require_once "../config/conexion.php";
session_start();
if(!isset($_SESSION['admin'])) header("Location: login.php");
$db = (new Conexion())->conectar();

// obtener categorias
$cats = $db->query("SELECT * FROM categorias")->fetchAll(PDO::FETCH_ASSOC);
$mensaje = '';
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    $precio = floatval($_POST['precio']);
    $categoria_id = ($_POST['categoria_id'] === '') ? null : intval($_POST['categoria_id']);
    // manejo de imagen
    $imagen = null;
    if(isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0){
        $targetDir = '../assets/img/';
        if(!is_dir($targetDir)) mkdir($targetDir,0755,true);
        $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
        $filename = uniqid('p_', true) . "." . $ext;
        move_uploaded_file($_FILES['imagen']['tmp_name'], $targetDir.$filename);
        $imagen = $filename;
    }

    $stm = $db->prepare("INSERT INTO productos (nombre, descripcion, precio, imagen, categoria_id) VALUES (?, ?, ?, ?, ?)");
    $stm->execute([$nombre, $descripcion, $precio, $imagen, $categoria_id]);
    header("Location: productos.php");
    exit;
}
?>
<!doctype html>
<html lang="es">
<head><meta charset="utf-8"><title>Nuevo Producto</title>
<link rel="stylesheet" href="../assets/css/style.css"></head>
<body>
<div class="container">
  <h2>Crear producto</h2>
  <form method="post" enctype="multipart/form-data" style="max-width:600px">
    <div class="field">
      <input class="input" name="nombre" placeholder="Nombre" required>
    </div>
    <div class="field">
      <textarea class="input" name="descripcion" placeholder="Descripción"></textarea>
    </div>
    <div class="field">
      <input class="input" name="precio" type="number" step="0.01" placeholder="Precio" required>
    </div>
    <div class="field">
      <select name="categoria_id" class="input">
        <option value="">-- Sin categoría --</option>
        <?php foreach($cats as $c): ?>
          <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="field">
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
