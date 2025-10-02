<?php
session_start();
require_once "conexion.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: login.html");
    exit;
}

$nombre = trim($_POST['nombre_user'] ?? '');
$pass   = $_POST['password_user'] ?? '';

if ($nombre === '' || $pass === '') {
    echo "❌ Usuario y contraseña requeridos";
    exit;
}

$sql = "SELECT id, nombre, password FROM usuarios WHERE nombre = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $nombre);
$stmt->execute();
$res = $stmt->get_result();

if ($res && $res->num_rows === 1) {
    $user = $res->fetch_assoc();
    if (password_verify($pass, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['nombre']  = $user['nombre'];
        echo "✅ Bienvenido " . htmlspecialchars($user['nombre']);
    } else {
        echo "❌ Contraseña incorrecta";
    }
} else {
    echo "❌ Usuario no encontrado";
}
?>
