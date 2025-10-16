<?php
require 'session_boot.php';
require 'conexion.php';

// --- Verificar usuario logueado ---
$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
  header('Location: login.php');
  exit;
}

// --- Función helper para escapar HTML ---
function e(?string $s): string { 
  return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); 
}

// --- Consultar reservas + datos del evento ---
$sql = "
  SELECT
    r.id               AS reservation_id,
    r.quantity,
    r.reservation_date,
    e.id               AS event_id,
    e.title,
    e.start_at,
    e.location,
    e.image_path,
    e.price
  FROM reservations r
  JOIN events e ON e.id = r.event_id
  WHERE r.user_id = ?
  ORDER BY e.start_at ASC
";
$stmt = $connection->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$reservas = $result->fetch_all(MYSQLI_ASSOC);

// --- Helper para imágenes con fallback ---
function imgUrl(?string $path): string {
  $fallback = 'uploads/eventos/default-event.jpg';
  if (!$path) return $fallback;
  return $path;
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Mis reservas</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <link rel="stylesheet" href="style.css?v=<?= filemtime(__DIR__.'/style.css') ?>">
  <style>
    body { background-color: #f8f9fa; }
    .card-img-top { aspect-ratio: 16/9; object-fit: cover; }
    .price { font-weight: 600; color: #0d6efd; }
  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-custom-navbar shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php">EventosApp</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div id="nav" class="collapse navbar-collapse">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="eventos.php">Explorar</a></li>
        <li class="nav-item"><a class="nav-link active" href="mis_reservas.php">Mis reservas</a></li>
      </ul>
      <div class="d-flex align-items-center gap-3">
        <span class="text-secondary small"><?= htmlspecialchars($_SESSION['username'] ?? 'Desconocido') ?></span>
        <a class="btn btn-outline-light btn-sm" href="logout.php">Salir</a>
      </div>
    </div>
  </div>
</nav>

<main class="container py-4">
  <h1 class="h4 mb-4">Mis reservas</h1>

  <?php if (empty($reservas)): ?>
    <div class="alert alert-info">
      Aún no tienes reservas. <a href="index.php" class="alert-link">Explorar eventos</a>
    </div>
  <?php else: ?>
    <div class="row g-3">
      <?php foreach ($reservas as $r): ?>
        <?php
          $img     = imgUrl($r['image_path'] ?? null);
          $price   = is_null($r['price']) || $r['price']==='' ? null : (float)$r['price'];
          $total   = $price !== null ? $price * (int)$r['quantity'] : null;
          $startAt = !empty($r['start_at']) ? new DateTime($r['start_at']) : null;
          $resDate = new DateTime($r['reservation_date']);
        ?>
        <div class="col-md-6 col-lg-4">
          <div class="card h-100 shadow-sm">
            <img src="<?= e($img) ?>" class="card-img-top"
                 alt="<?= e($r['title'] ?? 'Evento') ?>"
                 onerror="this.onerror=null;this.src='uploads/eventos/default-event.jpg';">
            <div class="card-body d-flex flex-column">
              <h5 class="card-title mb-1">
                <?= e($r['title']) ?>
              </h5>

              <?php if ($startAt): ?>
                <div class="small text-muted mb-1">
                  <i class="bi bi-calendar-event"></i>
                  <?= $startAt->format('d/m/Y H:i') ?>
                </div>
              <?php endif; ?>
              <?php if (!empty($r['location'])): ?>
                <div class="small text-muted">
                  <i class="bi bi-geo-alt"></i> <?= e($r['location']) ?>
                </div>
              <?php endif; ?>

              <hr class="my-3">

              <div class="d-flex justify-content-between small">
                <span>Cantidad: <strong><?= (int)$r['quantity'] ?></strong></span>
                <?php if ($total !== null): ?>
                  <span class="price"><?= number_format($total, 2, ',', '.') ?> €</span>
                <?php else: ?>
                  <span class="badge text-bg-success">Gratis</span>
                <?php endif; ?>
              </div>

              <div class="text-muted small mt-2">
                Reservado el <?= $resDate->format('d/m/Y H:i') ?>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</main>

<footer class="bg-custom-navbar text-white text-center py-4 mt-5">
  <div class="container">
    <p class="mb-1 fw-bold">EventosApp &copy; 2025</p>
    <p class="mb-0">Tu plataforma para descubrir y reservar eventos únicos</p>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>