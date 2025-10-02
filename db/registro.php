<?php
require_once "conexion.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: registro.html");
    exit;
}

$nombre = trim($_POST['nombre_user'] ?? '');
$email  = trim($_POST['email_user'] ?? '');
$pass   = $_POST['password_user'] ?? '';

if ($nombre === '' || $email === '' || $pass === '') {
    echo "❌ Todos los campos son obligatorios";
    exit;
}

$hash = password_hash($pass, PASSWORD_DEFAULT);
$sql = "INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $nombre, $email, $hash);

if ($stmt->execute()) {
    echo "✅ Usuario registrado correctamente. <a href='login.html'>Inicia sesión</a>";
} else {
    echo "❌ Error: " . $stmt->error;
}
?>