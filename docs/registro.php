<?php
require_once "conexion.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: registro.html");
    exit;
}

$username = trim($_POST['nombre_user'] ?? '');
$email  = trim($_POST['email_user'] ?? '');
$password   = $_POST['password_user'] ?? '';
$phone     = trim($_POST['phone_user'] ?? '');      // ← nuevo
$birthdate = trim($_POST['birthdate_user'] ?? '');  // ← nuevo

if ($username === '' || $email === '' || $password === '' || $phone === '' || $birthdate === '') {
    echo "❌ Todos los campos son obligatorios";
    exit;
}

$hash = password_hash($password, PASSWORD_DEFAULT);
$sql = "INSERT INTO users (username, email, password, phone, birthdate) 
        VALUES (?, ?, ?, ?, ?)";
$stmt = $connection->prepare($sql);
$stmt->bind_param("sssss", $username, $email, $hash, $phone, $birthdate);

if ($stmt->execute()) {
    echo "✅ Usuario registrado correctamente. <a href='login.html'>Inicia sesión</a>";
} else {
    echo "❌ Error: " . $stmt->error;
}
?>


