<?php
require_once "conexion.php"; // o conexion.php

function parse_birthdate_to_mysql(?string $raw): ?string {
    if (!$raw) return null;
    $raw = trim($raw);

    // Acepta 23/11/2000 o 23-11-2000
    $raw = str_replace('-', '/', $raw);

    // dd/mm/yyyy
    if (preg_match('#^(\d{1,2})/(\d{1,2})/(\d{4})$#', $raw, $m)) {
        [$all, $d, $mth, $y] = $m;
        if (checkdate((int)$mth, (int)$d, (int)$y)) {
            return sprintf('%04d-%02d-%02d', $y, $mth, $d); // YYYY-MM-DD
        }
        return null; // fecha inválida
    }

    // Como fallback, si viniera en YYYY-MM-DD (por si cambia el input)
    if (preg_match('#^\d{4}-\d{2}-\d{2}$#', $raw)) {
        return $raw;
    }

    return null; // formato no reconocido
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: registro.html");
    exit;
}

$username  = trim($_POST['nombre_user'] ?? '');
$email     = trim($_POST['email_user'] ?? '');
$password  = $_POST['password_user'] ?? '';
$phone     = trim($_POST['phone_user'] ?? '');
$birth_raw = trim($_POST['birthdate_user'] ?? '');

if ($username === '' || $email === '' || $password === '') {
    die("❌ Todos los campos obligatorios (usuario, email y contraseña).");
}

$birthdate = parse_birthdate_to_mysql($birth_raw);
if ($birth_raw !== '' && $birthdate === null) {
    die("❌ La fecha debe ser válida y con formato dd/mm/aaaa. Ej: 23/11/2000");
}

$hash = password_hash($password, PASSWORD_DEFAULT);

$sql  = "INSERT INTO users (username, email, password, phone, birthdate)
         VALUES (?, ?, ?, ?, ?)";
$stmt = $connection->prepare($sql);
$stmt->bind_param("sssss", $username, $email, $hash, $phone, $birthdate);
$stmt->execute();

echo "✅ Usuario registrado. <a href='login.html'>Inicia sesión</a>";
