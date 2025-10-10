<?php
// --- Configuración de sesión ---
ini_set('session.use_strict_mode', '1');
session_set_cookie_params([
  'lifetime' => 0,
  'path'     => '/',
  'httponly' => true,
  'samesite' => 'Lax',
]);
$__sess_dir = __DIR__ . '/sessions';
if (!is_dir($__sess_dir)) { @mkdir($__sess_dir, 0777, true); }
ini_set('session.save_path', $__sess_dir);
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }

include 'conexion.php';

// --- Obtener ID del evento ---
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
  die("<h2 class='text-center mt-5 text-danger'>ID de evento no válido.</h2>");
}

// --- Consulta SQL ---
$sql = "SELECT * FROM events WHERE id = $id AND is_public = 1 LIMIT 1";
$resultado = $connection->query($sql);
if (!$resultado || $resultado->num_rows === 0) {
  die("<h2 class='text-center mt-5 text-danger'>Evento no encontrado o no disponible.</h2>");
}
$evento = $resultado->fetch_assoc();

// Arreglar posibles barras invertidas en la ruta de imagen
$evento['image_path'] = str_replace('\\', '/', $evento['image_path']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($evento['title']) ?> - EventosApp</title>
  <link rel="shortcut icon" href="empresa.png" type="image/x-icon">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link rel="stylesheet" href="style.css?v=<?= filemtime(__DIR__.'/style.css') ?>">
</head>

<body class="bg-custom text-white">

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-custom-navbar shadow-sm">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="index.php">EventosApp</a>
  </div>
</nav>

<!-- CONTENIDO PRINCIPAL -->
<div class="container py-5">
  <div class="row g-5 align-items-start">

    <!-- Imagen -->
    <div class="col-md-6">
      <img src="<?= htmlspecialchars($evento['image_path'] ?: 'assets/default-event.jpg') ?>" 
           class="img-fluid rounded shadow"
           alt="<?= htmlspecialchars($evento['title']) ?>">
    </div>

    <!-- Información -->
    <div class="col-md-6">
      <h1 class="mb-3"><?= htmlspecialchars($evento['title']) ?></h1>

      <p><i class="bi bi-calendar-event"></i> <strong>Fecha:</strong> 
        <?= !empty($evento['start_at']) ? date('d/m/Y H:i', strtotime($evento['start_at'])) : 'No especificada' ?>
      </p>

      <p><i class="bi bi-geo-alt"></i> <strong>Lugar:</strong> 
        <?= htmlspecialchars($evento['location'] ?? 'No especificado') ?>
      </p>

      <p><i class="bi bi-tag"></i> <strong>Categoría:</strong> 
        <?= htmlspecialchars($evento['category'] ?? 'Sin categoría') ?>
      </p>

      <p><i class="bi bi-currency-euro"></i> <strong>Precio:</strong> 
        <?= isset($evento['price']) ? number_format($evento['price'], 2) . " €" : 'Gratis' ?>
      </p>

      <div class="mt-4">
        <h5><i class="bi bi-info-circle"></i> Descripción</h5>
        <p class="text-light">
          <?= !empty(trim($evento['description'])) 
              ? nl2br(htmlspecialchars($evento['description'])) 
              : 'Este evento no tiene una descripción disponible por el momento.' ?>
        </p>
      </div>

      <a href="reservar.php?id=<?= urlencode($evento['id']) ?>" class="btn btn-primary btn-lg mt-4">
        <i class="bi bi-ticket-perforated"></i> Reservar entrada
      </a>
    </div>
  </div>
</div>

<footer class="bg-custom-navbar text-white text-center py-4 mt-5">
  <div class="container">
    <p class="mb-1 fw-bold">EventosApp &copy; 2025</p>
    <p class="mb-0">Tu plataforma para descubrir y reservar eventos únicos</p>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
