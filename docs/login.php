<?php
session_start();
require_once "conexion.php";

// Si no es POST, redirigir al login
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: login.php");
    exit;
}

// Recoger y validar datos del formulario
$username = trim($_POST['nombre_user'] ?? '');
$password = $_POST['password_user'] ?? '';

if ($username === '' || $password === '') {
    // Podrías redirigir con un mensaje de error en la URL si prefieres
    echo "❌ Usuario y contraseña requeridos";
    exit;
}

// Buscar usuario en la base de datos
$sql = "SELECT id, username, password FROM users WHERE username = ? LIMIT 1";
$stmt = $connection->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$res = $stmt->get_result();

// Verificar existencia del usuario y contraseña
if ($res && $res->num_rows === 1) {
    $user = $res->fetch_assoc();
    if (password_verify($password, $user['password'])) {
        // Guardar datos en la sesión
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];

        // Redirigir al inicio
        header("Location: index.php");
        exit;
    } else {
        echo "❌ Contraseña incorrecta";
        exit;
    }
} else {
    echo "❌ Usuario no encontrado";
    exit;
}
?>




