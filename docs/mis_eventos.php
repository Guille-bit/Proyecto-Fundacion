<?php
require 'session_boot.php';
require 'conexion.php';    

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = (int)$_SESSION['user_id'];

$sql = "SELECT 
            id, title, location, start_at, image_path, is_public
        FROM 
            events
        WHERE 
            user_id = ?
        ORDER BY 
            start_at DESC"; 

$stmt = $connection->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$resultado = $stmt->get_result();

$eventos_creados = $resultado->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Mis Eventos Creados</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link rel="stylesheet" href="style.css?v=<?= filemtime(__DIR__.'/style.css') ?>">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-custom-navbar shadow-sm">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="index.php">EventosApp</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="index.php"><i class="bi bi-calendar3"></i> Ver Eventos</a></li>
        <li class="nav-item"><a class="nav-link" href="mis_reservas.php"><i class="bi bi-ticket-detailed"></i> Mis Reservas</a></li>
        <li class="nav-item"><a class="nav-link text-warning" href="logout.php"><i class="bi bi-box-arrow-right"></i> Cerrar sesión</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container py-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="mb-0"><i class="bi bi-pencil-square"></i> Mis Eventos Creados</h1>
    <a href="crear_eventos.php" class="btn btn-primary">
      <i class="bi bi-plus-circle-fill"></i> Crear Nuevo Evento
    </a>
  </div>
  <p class="lead mb-4">Aquí puedes administrar todos los eventos que has publicado en la plataforma.</p>

  <?php if (count($eventos_creados) > 0): ?>
    <div class="row g-4">
      <?php foreach ($eventos_creados as $evento): ?>
        <div class="col-md-4">
          <div class="card event-card h-100 shadow-sm">
            <img src="<?= htmlspecialchars($evento['image_path']) ?>" class="card-img-top event-img" alt="<?= htmlspecialchars($evento['title']) ?>">
            <div class="card-body">
              <h5 class="event-title"><?= htmlspecialchars($evento['title']) ?></h5>
              <p class="mb-1">
                <strong><i class="bi bi-calendar-event"></i> Fecha:</strong> <?= date('d M Y', strtotime($evento['start_at'])) ?>
              </p>
              <p class="mb-1">
                <strong><i class="bi bi-geo-alt"></i> Lugar:</strong> <?= htmlspecialchars($evento['location']) ?>
              </p>
              <span class="badge <?= $evento['is_public'] ? 'bg-success' : 'bg-secondary' ?>">
                <?= $evento['is_public'] ? 'Público' : 'Oculto' ?>
              </span>
              </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <div class="text-center p-5 border rounded-3 bg-white">
      <i class="bi bi-calendar-plus" style="font-size: 3rem;"></i>
      <h3 class="mt-3">Aún no has creado ningún evento</h3>
      <p>¿Tienes una idea para un evento? ¡Compártela con la comunidad!</p>
      <a href="crear_eventos.php" class="btn btn-success btn-lg mt-2">
        <i class="bi bi-plus-lg"></i> Publicar mi primer evento
      </a>
    </div>
  <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>