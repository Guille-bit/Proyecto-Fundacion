<?php
$servername = "100.107.241.28"; // o IP Tailscale si usas otro PC
$username   = "equipo";       // usuario de MySQL (ajusta si usas otro)
$password   = "PassMuySegura_123";           // contraseña (vacío por defecto en XAMPP)
$dbname     = "login_db";   // nombre de la base de datos
$port       = 3306;

$conn = new mysqli($servername, $username, $password, $dbname, $port);
if ($conn->connect_error) {
    die("❌ Error de conexión: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
?>