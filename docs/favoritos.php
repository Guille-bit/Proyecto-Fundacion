<?php
require 'session_boot.php';
require 'conexion.php';

$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
  header('Location: login.php');
  exit;
}

$sql = "
  SELECT
    f.id AS favorite_id, e.id AS event_id, e.title, e.location, e.start_at, e.image_path,
    CASE WHEN e.end_at < NOW() THEN 1 ELSE 0 END AS is_past
  FROM favorites f
  JOIN events e ON e.id = f.event_id
  WHERE f.user_id = ?
  ORDER BY e.start_at DESC";
  
$stmt = $connection->prepare($sql);
$stmt->bind_param('i', $userId);
$stmt->execute();
$favoritos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Mis Favoritos - EventosApp</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
   <link rel="icon" type="image/png" href="uploads/eventos/logo5.png"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css?v=<?= filemtime(__DIR__.'/style.css') ?>">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-custom-navbar shadow-sm">
  <div class="container">
    <a class="navbar-brand" href="index.php">EventosApp</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="index.php">Inicio</a></li>
        <li class="nav-item"><a class="nav-link" href="mis_reservas.php">Mis Reservas</a></li>
        <li class="nav-item"><a class="nav-link active" href="favoritos.php">Favoritos</a></li>
      </ul>
      <span class="navbar-text">
        <i class="bi bi-person-circle me-1"></i><?= h($_SESSION['username'] ?? 'Usuario') ?>
        <a class="btn btn-outline-light btn-sm ms-2" href="logout.php">Salir</a>
      </span>
    </div>
  </div>
</nav>

<div class="container py-5">
  <h1 class="mb-4"><i class="bi bi-heart-fill me-3"></i>Mis Favoritos</h1>
  
  <?php if (empty($favoritos)): ?>
    <div class="text-center p-5 bg-light rounded">
      <h2>No tienes eventos favoritos</h2>
      <p class="lead text-muted">Usa el icono del corazón en los eventos para guardarlos aquí.</p>
      <a href="index.php" class="btn btn-primary mt-3">Explorar Eventos</a>
    </div>
  <?php else: ?>
    <div class="row g-4">
      <?php foreach ($favoritos as $evento): ?>
        <div class="col-md-4">
          <div class="card event-card h-100">
            <img src="<?= h($evento['image_path']) ?>" class="card-img-top event-img" alt="<?= h($evento['title']) ?>">
            <div class="card-body">
              <h5 class="event-title"><?= h($evento['title']) ?></h5>
              <p class="mb-1"><i class="bi bi-calendar-event"></i> <?= date('d M Y', strtotime($evento['start_at'])) ?></p>
              <p class="mb-1"><i class="bi bi-geo-alt"></i> <?= h($evento['location']) ?></p>
              <a href="reserva.php?id=<?= $evento['event_id'] ?>" class="btn btn-outline-dark w-100 mt-3">Ver Evento</a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>