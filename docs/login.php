<?php
session_start();
require_once "conexion.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: login.php");
    exit;
}

$username = trim($_POST['nombre_user'] ?? '');
$password = $_POST['password_user'] ?? '';

if ($username === '' || $password === '') {
    echo "❌ Usuario y contraseña requeridos";
    exit;
}

/*$sql = "SELECT id, nombre, password FROM usuarios WHERE nombre = ? LIMIT 1";*/
$sql = "SELECT id, username, password FROM users WHERE username = ? LIMIT 1";
$stmt = $connection->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$res = $stmt->get_result();

if ($res && $res->num_rows === 1) {
    $user = $res->fetch_assoc();
    if (password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username']  = $user['username'];
        echo "✅ Bienvenido " . htmlspecialchars($user['username']);
        echo ". <a href='index.php'>Inicio</a>";
    } else {
        echo "❌ Contraseña incorrecta";
    }
} else {
    echo "❌ Usuario no encontrado";
}

?>




