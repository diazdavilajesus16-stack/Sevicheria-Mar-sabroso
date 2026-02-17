<?php
require_once "config/conexion.php";

// Crear objeto y conectarnos
$conexionObj = new Conexion();
$conexion = $conexionObj->conectar();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $mensaje = trim($_POST['mensaje']);

    try {
        $sql = "INSERT INTO Contactos (nombre, correo, mensaje) VALUES (:nombre, :correo, :mensaje)";

        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':correo', $correo);
        $stmt->bindParam(':mensaje', $mensaje);

        $stmt->execute();

        echo "<script>
                alert('Mensaje enviado correctamente 💙');
                window.location='index.php';
              </script>";
    } catch (PDOException $e) {
        echo "Error al enviar mensaje: " . $e->getMessage();
    }
}
?>

