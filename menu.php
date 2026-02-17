<?php
require_once "config/conexion.php";

$conexion = (new Conexion())->conectar();

$stm = $conexion->prepare("SELECT * FROM productos");
$stm->execute();
$productos = $stm->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Menú - Cevichería</title>
    <link rel="stylesheet" href="assets/css/estilo.css">
</head>

<body>

<header class="navbar">
    <div class="logo">El Sabor Marino</div>
    <nav>
        <a href="index.php">Inicio</a>
        <a href="menu.php" class="active">Menú</a>
    </nav>
</header>

<h1 class="titulo-menu">Nuestro Menú</h1>

<div class="productos">
    <?php foreach ($productos as $p): ?>
    <div class="producto">
        <img src="assets/img/<?= htmlspecialchars($p['imagen']) ?>" 
             alt="<?= htmlspecialchars($p['nombre']) ?>">

        <h3><?= htmlspecialchars($p['nombre']) ?></h3>
        <p><?= htmlspecialchars($p['descripcion']) ?></p>

        <strong>S/ <?= number_format($p['precio'], 2) ?></strong>

        <form method="POST" action="carrito.php">
            <input type="hidden" name="producto_id" value="<?= $p['id'] ?>">
            
            <input type="number" name="cantidad" value="1" min="1" class="cantidad">

            <button type="submit" name="agregar" class="btn-primary">
                Agregar al Carrito
            </button>
        </form>
    </div>
    <?php endforeach; ?>
</div>

</body>
</html>
