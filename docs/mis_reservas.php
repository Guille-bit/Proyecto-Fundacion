<?php
require 'session_boot.php';
require 'conexion.php';

// --- Verificar usuario logueado ---
$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
  header('Location: login.php');
  exit;
}

// --- Procesar eliminación de reserva ---
if (isset($_POST['eliminar_reserva'])) {
  $reservationId = (int)$_POST['reservation_id'];
  
  // Verificar que la reserva pertenece al usuario
  $sql = "SELECT id FROM reservations WHERE id = ? AND user_id = ?";
  $stmt = $connection->prepare($sql);
  $stmt->bind_param("ii", $reservationId, $userId);
  $stmt->execute();
  $result = $stmt->get_result();
  
  if ($result->num_rows > 0) {
    $deleteSql = "DELETE FROM reservations WHERE id = ? AND user_id = ?";
    $deleteStmt = $connection->prepare($deleteSql);
    $deleteStmt->bind_param("ii", $reservationId, $userId);
    
    if ($deleteStmt->execute()) {
      $mensaje = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle"></i> Reserva eliminada correctamente.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                  </div>';
    } else {
      $mensaje = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle"></i> Error al eliminar la reserva.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                  </div>';
    }
  }
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
    e.end_at,
    e.location,
    e.image_path,
    e.price,
    CASE 
      WHEN e.end_at < NOW() THEN 1 
      ELSE 0 
    END AS is_past_event
  FROM reservations r
  JOIN events e ON e.id = r.event_id
  WHERE r.user_id = ?
  ORDER BY e.start_at DESC
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

// --- Helper para verificar si un evento ya pasó ---
function isPastEvent(?string $endAt): bool {
  if (!$endAt) return false;
  return new DateTime($endAt) < new DateTime();
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
    
    /* Estilos para eventos pasados */
    .past-event {
      opacity: 0.6;
      background-color: #f8f9fa;
    }
    
    .past-event .card-body {
      background-color: #e9ecef;
    }
    
    .past-event .card-title {
      color: #6c757d;
    }
    
    .past-event img {
      filter: grayscale(50%);
    }
    
    .badge-past {
      background-color: #6c757d !important;
    }
    
    /* Animación para eliminación */
    .deleting {
      animation: fadeOut 0.5s ease-out forwards;
    }
    
    @keyframes fadeOut {
      0% { opacity: 1; transform: scale(1); }
      100% { opacity: 0; transform: scale(0.8); }
    }
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
  <h1 class="h4 mb-4">
    <i class="bi bi-ticket-perforated"></i> Mis reservas
    <span class="badge bg-primary ms-2"><?= count($reservas) ?></span>
  </h1>

  <?php if (isset($mensaje)) echo $mensaje; ?>

  <?php if (empty($reservas)): ?>
    <div class="alert alert-info d-flex align-items-center">
      <i class="bi bi-info-circle me-2"></i>
      <div>
        Aún no tienes reservas. <a href="index.php" class="alert-link fw-bold">Explorar eventos</a>
      </div>
    </div>
  <?php else: ?>
    
    <!-- Filtros -->
    <div class="mb-4">
      <div class="btn-group" role="group">
        <input type="radio" class="btn-check" name="filter" id="all" autocomplete="off" checked>
        <label class="btn btn-outline-primary" for="all">Todos (<?= count($reservas) ?>)</label>
        
        <input type="radio" class="btn-check" name="filter" id="upcoming" autocomplete="off">
        <label class="btn btn-outline-success" for="upcoming">Próximos (<?= count(array_filter($reservas, fn($r) => !$r['is_past_event'])) ?>)</label>
        
        <input type="radio" class="btn-check" name="filter" id="past" autocomplete="off">
        <label class="btn btn-outline-secondary" for="past">Pasados (<?= count(array_filter($reservas, fn($r) => $r['is_past_event'])) ?>)</label>
      </div>
    </div>

    <div class="row g-3" id="reservas-container">
      <?php foreach ($reservas as $r): ?>
        <?php
          $img = imgUrl($r['image_path'] ?? null);
          $price = is_null($r['price']) || $r['price']==='' ? null : (float)$r['price'];
          $total = $price !== null ? $price * (int)$r['quantity'] : null;
          $startAt = !empty($r['start_at']) ? new DateTime($r['start_at']) : null;
          $endAt = !empty($r['end_at']) ? new DateTime($r['end_at']) : null;
          $resDate = new DateTime($r['reservation_date']);
          $isPast = (bool)$r['is_past_event'];
        ?>
        <div class="col-md-6 col-lg-4 reservation-item <?= $isPast ? 'past-event-item' : 'upcoming-event-item' ?>" 
             data-reservation-id="<?= $r['reservation_id'] ?>">
          <div class="card h-100 shadow-sm <?= $isPast ? 'past-event' : '' ?>">
            
            <!-- Badge de estado -->
            <?php if ($isPast): ?>
              <div class="position-absolute top-0 end-0 m-2">
                <span class="badge badge-past">
                  <i class="bi bi-clock-history"></i> Finalizado
                </span>
              </div>
            <?php else: ?>
              <div class="position-absolute top-0 end-0 m-2">
                <span class="badge bg-success">
                  <i class="bi bi-calendar-check"></i> Próximo
                </span>
              </div>
            <?php endif; ?>

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
                  <?php if ($endAt && $endAt->format('Y-m-d') !== $startAt->format('Y-m-d')): ?>
                    - <?= $endAt->format('d/m/Y H:i') ?>
                  <?php elseif ($endAt): ?>
                    - <?= $endAt->format('H:i') ?>
                  <?php endif; ?>
                </div>
              <?php endif; ?>
              
              <?php if (!empty($r['location'])): ?>
                <div class="small text-muted mb-2">
                  <i class="bi bi-geo-alt"></i> <?= e($r['location']) ?>
                </div>
              <?php endif; ?>

              <hr class="my-3">

              <div class="d-flex justify-content-between small mb-2">
                <span>Cantidad: <strong><?= (int)$r['quantity'] ?></strong></span>
                <?php if ($total !== null): ?>
                  <span class="price"><?= number_format($total, 2, ',', '.') ?> €</span>
                <?php else: ?>
                  <span class="badge text-bg-success">Gratis</span>
                <?php endif; ?>
              </div>

              <div class="text-muted small mb-3">
                Reservado el <?= $resDate->format('d/m/Y H:i') ?>
              </div>

              <!-- Botones de acción -->
              <div class="mt-auto">
                <?php if (!$isPast): ?>
                  <!-- Solo mostrar botón eliminar para eventos futuros -->
                  <form method="POST" class="d-inline w-100" onsubmit="return confirmarEliminacion(this, '<?= e($r['title']) ?>')">
                    <input type="hidden" name="reservation_id" value="<?= $r['reservation_id'] ?>">
                    <button type="submit" name="eliminar_reserva" class="btn btn-outline-danger btn-sm w-100">
                      <i class="bi bi-trash3"></i> Cancelar reserva
                    </button>
                  </form>
                <?php else: ?>
                  <button class="btn btn-secondary btn-sm w-100" disabled>
                    <i class="bi bi-check-circle"></i> Evento finalizado
                  </button>
                <?php endif; ?>
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
<script>
// Función para confirmar eliminación
function confirmarEliminacion(form, eventTitle) {
  if (confirm(`¿Estás seguro de que deseas cancelar tu reserva para "${eventTitle}"?\n\nEsta acción no se puede deshacer.`)) {
    // Agregar clase de animación
    const card = form.closest('.reservation-item');
    card.classList.add('deleting');
    
    // Enviar formulario después de un pequeño delay para la animación
    setTimeout(() => {
      form.submit();
    }, 200);
    
    return false; // Prevenir envío inmediato
  }
  return false;
}

// Filtros de eventos
document.addEventListener('DOMContentLoaded', function() {
  const filterButtons = document.querySelectorAll('input[name="filter"]');
  const reservationItems = document.querySelectorAll('.reservation-item');
  
  filterButtons.forEach(button => {
    button.addEventListener('change', function() {
      const filter = this.id;
      
      reservationItems.forEach(item => {
        switch(filter) {
          case 'all':
            item.style.display = 'block';
            break;
          case 'upcoming':
            item.style.display = item.classList.contains('upcoming-event-item') ? 'block' : 'none';
            break;
          case 'past':
            item.style.display = item.classList.contains('past-event-item') ? 'block' : 'none';
            break;
        }
      });
    });
  });
});

// Auto-ocultar alertas después de 5 segundos
document.addEventListener('DOMContentLoaded', function() {
  const alerts = document.querySelectorAll('.alert:not(.alert-info)');
  alerts.forEach(alert => {
    setTimeout(() => {
      const bsAlert = new bootstrap.Alert(alert);
      bsAlert.close();
    }, 5000);
  });
});
</script>
</body>
</html>
