<?php
session_start();
require_once "../config/conexion.php";
$db = (new Conexion())->conectar();

// Si ya está logueado lo redirige
if(isset($_SESSION['admin']) && $_SESSION['admin'] === true){
    header("Location: dashboard.php");
    exit;
}

// Si el formulario se envía: login
$mensaje = '';
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['usuario'], $_POST['password'])){
    $usuario = trim($_POST['usuario']);
    $password = $_POST['password'];

    $stm = $db->prepare("SELECT id, usuario, contraseña, rol FROM usuarios WHERE usuario = ?");
    $stm->execute([$usuario]);
    $user = $stm->fetch(PDO::FETCH_ASSOC);

    if($user){
        // Soporte para MD5 antiguo o password_hash nuevo:
        $hash = $user['contraseña'];
        $valid = false;
        if(password_verify($password, $hash)) $valid = true;
        elseif(md5($password) === $hash) $valid = true; // retro-compatibilidad
        if($valid){
            $_SESSION['admin'] = true;
            $_SESSION['admin_user'] = $user['usuario'];
            header("Location: dashboard.php");
            exit;
        }
    }
    $mensaje = "Usuario o contraseña incorrectos.";
}
?>
<!doctype html>
<html lang="es">
<head><meta charset="utf-8"><title>Admin - Login</title>
<link rel="stylesheet" href="../assets/css/style.css"></head>
<body>
<div class="container" style="max-width:420px;margin-top:60px">
  <div class="admin-content">
    <h2>Iniciar sesión (Admin)</h2>
    <?php if($mensaje): ?><div style="color:#b91c1c;margin:8px 0"><?=htmlspecialchars($mensaje)?></div><?php endif; ?>
    <form method="post">
      <div class="field">
        <input class="input" name="usuario" placeholder="Usuario" required>
      </div>
      <div class="field">
        <input class="input" name="password" type="password" placeholder="Contraseña" required>
      </div>
      <div style="display:flex;gap:8px">
        <button class="btn-primary" type="submit">Ingresar</button>
        <a class="btn-ghost" href="../index.php">Volver</a>
      </div>
    </form>
  </div>
</div>
</body>
</html>
