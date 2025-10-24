
<?php
// --- Configuración de sesión ---
ini_set('session.use_strict_mode', '1');
session_set_cookie_params([
  'lifetime' => 0,
  'path'     => '/',
  'httponly' => true,
  'samesite' => 'Lax',
  // 'secure' => true, // activar solo si usas HTTPS
]);

$__sess_dir = __DIR__ . '/sessions';
if (!is_dir($__sess_dir)) { @mkdir($__sess_dir, 0777, true); }
ini_set('session.save_path', $__sess_dir);

if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}
// --- Verificar si el usuario es administrador ---
$isAdmin = false;
if (!empty($_SESSION['is_admin']) && (int)$_SESSION['is_admin'] === 1) {
    $isAdmin = true;
} elseif (!empty($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    $isAdmin = true;
}
include 'conexion.php';

// --- Captura filtros ---
$busqueda = $_GET['busqueda'] ?? '';
$categoria = $_GET['categoria'] ?? '';
$lugar = $_GET['lugar'] ?? '';

// --- Consulta SQL ---
$sql = "SELECT * FROM events WHERE is_public = 1";
if ($busqueda !== '') {
  $sql .= " AND title LIKE '%" . $connection->real_escape_string($busqueda) . "%'";
}
if ($categoria !== '') {
  $sql .= " AND category = '" . $connection->real_escape_string($categoria) . "'";
}
if ($lugar !== '') {
  $sql .= " AND location LIKE '%" . $connection->real_escape_string($lugar) . "%'";
}
$sql .= " ORDER BY start_at ASC";
$resultado = $connection->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>EventosApp</title>
  <link rel="icon" type="image/png" href="uploads/eventos/logo5.png"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link rel="stylesheet" href="style.css?v=<?= filemtime(__DIR__.'/style.css') ?>">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body class="bg-custom text-white">

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-custom-navbar shadow-sm">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="index.php">
  <img src="uploads/eventos/logo4.png" alt="Inicio" class="logo-navbar">EventosApp
</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarMenu">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-4">
        <li class="nav-item"><a class="nav-link" href="mis_reservas.php"><i class="bi bi-calendar-check me-2"></i>Mis reservas</a></li>
        <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-heart"></i> Favoritos</a></li>
        <li class="nav-item"><a class="nav-link" href="eventos.php">Eventos</a></li>
      </ul>

      <!-- Usuario -->
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
        <li class="nav-item dropdown user-hover">
          <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userMenu" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <span class="me-1"><i class="bi bi-person-circle"></i></span>
            <?= isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Invitado'; ?>
          </a>
          <ul class="dropdown-menu dropdown-menu-start" aria-labelledby="userMenu">
            <?php if (!isset($_SESSION['username'])): ?>
              <li><a class="dropdown-item" href="login.php"><i class="bi bi-box-arrow-in-right me-2"></i> Iniciar sesión</a></li>
              <li><a class="dropdown-item" href="registro.php"><i class="bi bi-pencil-square me-2"></i> Registrarse</a></li>
            <?php else: ?>
              <li><a class="dropdown-item" href="perfil.php"><i class="bi bi-person-circle me-2"></i> Mi perfil</a></li>
              <li><a class="dropdown-item" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i> Cerrar sesión</a></li>
              <?php if ($isAdmin): ?>
                <li><a class="dropdown-item" href="crear_eventos.php"><i class="bi bi-plus-lg me-2"></i> Crear Eventos</a></li>
                <li><a class="dropdown-item" href="mis_eventos.php"><i class="bi bi-pencil-square me-2"></i> Mis eventos</a></li>
              <?php endif; ?>
            <?php endif; ?>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- HEADER EXPLICATIVO -->
<div class="hero-container">
  <div class="hero-text-wrapper">
  <div class="hero-text">
      <h1 class="display-4 fw-bold">Descubre Eventos Increíbles Cerca de Ti</h1>
  </div>
  </div>
</div>

<!-- SLIDER / CAROUSEL -->
<div id="eventCarousel" class="carousel slide" data-bs-ride="carousel">
  <div class="carousel-inner">
    <div class="carousel-item active">
      <img src="uploads/eventos/bernabeu.jpg" class="d-block w-100 img-fluid event-img" alt="Festival de Cine">
      <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded p-2">
        <h5>Tour Santiago Bernabeu</h5>
        <p>12 Oct 2025 - Estadio Santiago Bernabeu</p>
        <a href="#" class="btn btn-primary mt-2">Reservar</a>
      </div>
    </div>
    <div class="carousel-item">
      <img src="uploads/eventos/programar.jpg" class="d-block w-100 img-fluid event-img" alt="Taller de Programación">
      <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded p-2">
        <h5>Taller de Programación Web</h5>
        <p>15 Oct 2025 - Aula Virtual Medac</p>
        <a href="#" class="btn btn-primary mt-2">Reservar</a>
      </div>
    </div>
    <div class="carousel-item">
      <img src="uploads/eventos/artesanal.jpg" class="d-block w-100 img-fluid event-img" alt="Mercado Artesanal">
      <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded p-2">
        <h5>Mercado Artesanal de Otoño</h5>
        <p>18 Oct 2025 - Plaza Mayor</p>
        <a href="#" class="btn btn-primary mt-2">Reservar</a>
      </div>
    </div>
  </div>
  <button class="carousel-control-prev" type="button" data-bs-target="#eventCarousel" data-bs-slide="prev">
    <span class="carousel-control-prev-icon"></span>
  </button>
<button class="carousel-control-next" type="button" data-bs-target="#eventCarousel" data-bs-slide="next">
    <span class="carousel-control-next-icon"></span>
  </button>
</div>
<!-- BUSCADOR -->
<div class="container py-5">
  <h2>Buscador de Eventos</h2>
  <form method="GET" class="row align-items-end mb-4">
    <div class="col-md-4">
      <label class="form-label fw-semibold"><i class="bi bi-search"></i> Buscar evento</label>
      <input type="text" name="busqueda" class="form-control" placeholder="Ej. Cine, Tecnología..." value="<?= htmlspecialchars($busqueda) ?>">
    </div>
    <div class="col-md-3">
      <label class="form-label fw-semibold"><i class="bi bi-tags"></i> Categoría</label>
      <select name="categoria" class="form-select">
        <option value="">-- Selecciona una categoría --</option>
        <option value="Cultura" <?= $categoria == "Cultura" ? "selected" : "" ?>>Cultura</option>
        <option value="Tecnología" <?= $categoria == "Tecnología" ? "selected" : "" ?>>Tecnología</option>
        <option value="Ferias" <?= $categoria == "Ferias" ? "selected" : "" ?>>Ferias</option>
        <option value="Bienestar" <?= $categoria == "Bienestar" ? "selected" : "" ?>>Bienestar</option>
      </select>
    </div>
    <div class="col-md-3">
      <label class="form-label fw-semibold"><i class="bi bi-geo-alt-fill"></i> Lugar</label>
      <input type="text" name="lugar" class="form-control" placeholder="Ej. Madrid, Barcelona..." value="<?= htmlspecialchars($lugar) ?>">
    </div>
    <div class="col-md-2">
      <button type="submit" class="btn btn-light w-100"><i class="bi bi-search"></i> Buscar</button>
    </div>
  </form>
  <!-- RESULTADOS DE LA BASE DE DATOS -->
  <div class="row g-4">
    <?php if ($resultado && $resultado->num_rows > 0): ?>
      <?php while ($evento = $resultado->fetch_assoc()): ?>
        <div class="col-md-4">
          <div class="card event-card h-100 shadow-sm">
            <img src="<?= htmlspecialchars($evento['image_path']) ?>" class="card-img-top event-img" alt="<?= htmlspecialchars($evento['title']) ?>">
            <div class="card-body">
              <h5 class="event-title"><?= htmlspecialchars($evento['title']) ?></h5>
              <p class="mb-1"><strong><i class="bi bi-calendar-event"></i> Fecha:</strong> <?= date('d M Y', strtotime($evento['start_at'])) ?></p>
              <p class="mb-1"><strong><i class="bi bi-geo-alt"></i> Lugar:</strong> <?= htmlspecialchars($evento['location']) ?></p>
              <span class="badge bg-secondary"><?= htmlspecialchars($evento['category']) ?></span>
               <a href="reserva.php?id=<?= $evento['id'] ?>" class="btn btn-outline-dark w-100 mt-3">Reservar</a>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <div class="col-12">
        <div class="alert alert-warning text-center">
          <i class="bi bi-exclamation-triangle-fill"></i> No se encontraron eventos con esos filtros.
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>


<div class="container py-4">
  <h2>Próximos eventos</h2>
  <?php
  $sql = "SELECT id, title, description, category, location, start_at, end_at, price, image_path
          FROM events
          WHERE is_public = 1
            AND start_at >= NOW()
          ORDER BY start_at ASC
          LIMIT 3";
  $res = $connection->query($sql);
  ?>

  <div class="row g-4">
    <?php if ($res && $res->num_rows > 0): ?>
      <?php while ($evento = $res->fetch_assoc()): ?>
        <?php $img = $evento['image_path'] ?: 'assets/default-event.jpg'; ?>
        <div class="col-md-4">
          <div class="card event-card h-100 shadow-sm">
            <img src="<?= htmlspecialchars($img) ?>"
                 class="card-img-top event-img"
                 alt="<?= htmlspecialchars($evento['title']) ?>"
                 onerror="this.onerror=null;this.src='assets/default-event.jpg';">
            <div class="card-body">
              <h5 class="event-title"><?= htmlspecialchars($evento['title']) ?></h5>
              <p class="mb-1"><strong><i class="bi bi-calendar-event"></i> Fecha:</strong>
                <?= date('d M Y', strtotime($evento['start_at'])) ?>
              </p>
              <p class="mb-1"><strong><i class="bi bi-geo-alt"></i> Lugar:</strong>
                <?= htmlspecialchars($evento['location']) ?>
              </p>
              <span class="badge bg-secondary"><?= htmlspecialchars($evento['category']) ?></span>
               <a href="reserva.php?id=<?= $evento['id'] ?>" class="btn btn-outline-dark w-100 mt-3">Reservar</a>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <div class="col-12">
        <div class="alert alert-warning text-center">
          <i class="bi bi-exclamation-triangle-fill"></i> No hay eventos próximos.
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>
<footer class="bg-custom-navbar text-white text-center py-4 mt-5">
  <div class="container">
    <p class="mb-1 fw-bold">EventosApp &copy; 2025</p>
    <p class="mb-0">Tu plataforma para descubrir y reservar eventos únicos</p>
  </div>
</footer>

</body>
</html>


