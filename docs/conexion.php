<?php
$servername = "100.107.241.28"; // o IP Tailscale si usas otro PC
$username   = "equipo";       // usuario de MySQL (ajusta si usas otro)
$password   = "PassMuySegura_123";           // contraseña (vacío por defecto en XAMPP)
$dbname     = "login_db";   // nombre de la base de datos
$port       = 3306;

$connection = new mysqli($servername, $username, $password, $dbname, $port);
if ($connection->connect_error) {
    die("❌ Error de conexión: " . $connection->connect_error);
}
$connection->set_charset("utf8mb4");

?>

