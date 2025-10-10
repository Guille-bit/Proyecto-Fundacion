<?php
// session_boot.php
ini_set('session.use_strict_mode','1');
session_set_cookie_params([
  'lifetime' => 0,
  'path'     => '/',      // MUY IMPORTANTE: visible en / y /docs
  'httponly' => true,
  'samesite' => 'Lax',
  // 'secure' => true,    // solo si usas HTTPS
]);

// Guarda sesiones en carpeta LOCAL del proyecto (evita problemas de XAMPP)
$__sess_dir = __DIR__ . '/sessions';
if (!is_dir($__sess_dir)) { @mkdir($__sess_dir, 0777, true); }
ini_set('session.save_path', $__sess_dir);

session_start();

include 'conexion.php';

// Destruir sesión si vienes de logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit;
}

// Captura búsqueda y filtro
$busqueda = isset($_GET['busqueda']) ? $_GET['busqueda'] : '';
$categoria = isset($_GET['categoria']) ? $_GET['categoria'] : '';

// Construir SQL
$sql = "SELECT * FROM events WHERE is_public = 1";

if (!empty($busqueda)) {
    $sql .= " AND title LIKE '%" . $connection->real_escape_string($busqueda) . "%'";
}

if (!empty($categoria)) {
    $sql .= " AND category = '" . $connection->real_escape_string($categoria) . "'";
}

$sql .= " ORDER BY start_at ASC";
$resultado = $connection->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Eventos | EventosApp</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="style.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="index.php">EventosApp</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarMenu">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="reservas.html"><i class="bi bi-calendar-event"></i> Mis reservas</a></li>
        <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-heart"></i> Favoritos</a></li>
      </ul>

      <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
        <li class="nav-item dropdown user-hover">
          <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userMenu" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-person-circle me-1"></i>
            <?= isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Invitado'; ?>
          </a>
          <ul class="dropdown-menu dropdown-menu-start" aria-labelledby="userMenu">
            <?php if (!isset($_SESSION['username'])): ?>
              <li><a class="dropdown-item" href="login.php"><i class="bi bi-box-arrow-in-right me-2"></i> Iniciar sesión</a></li>
              <li><a class="dropdown-item" href="registro.php"><i class="bi bi-pencil-square me-2"></i> Registrarse</a></li>
            <?php else: ?>
              <li><a class="dropdown-item" href="perfil.php"><i class="bi bi-person-circle me-2"></i> Mi perfil</a></li>
              <li><a class="dropdown-item" href="?logout=true"><i class="bi bi-box-arrow-right me-2"></i> Cerrar sesión</a></li>
              <li><a class="dropdown-item" href="eventos.php"><i class="bi bi-plus-lg me-2"></i> Crear Eventos</a></li>
            <?php endif; ?>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- Contenido principal -->
<div class="container py-5">
  <h1 class="mb-4 text-center"><i class="bi bi-calendar2-week"></i> Próximos Eventos</h1>

  <!-- Buscador y Filtro -->
  <form method="GET" class="row mb-4 align-items-end">
    <div class="col-md-6">
      <label class="form-label fw-semibold"><i class="bi bi-search"></i> Buscar eventos</label>
      <input type="text" name="busqueda" class="form-control" placeholder="Ej. Cine, Tecnología..." value="<?= htmlspecialchars($busqueda) ?>">
    </div>
    <div class="col-md-4">
      <label class="form-label fw-semibold"><i class="bi bi-sliders"></i> Categoría</label>
      <select name="categoria" class="form-select">
        <option value="">-- Selecciona una categoría --</option>
        <option value="Cultura" <?= $categoria == "Cultura" ? "selected" : "" ?>>Cultura</option>
        <option value="Tecnología" <?= $categoria == "Tecnología" ? "selected" : "" ?>>Tecnología</option>
        <option value="Ferias" <?= $categoria == "Ferias" ? "selected" : "" ?>>Ferias</option>
        <option value="Bienestar" <?= $categoria == "Bienestar" ? "selected" : "" ?>>Bienestar</option>
      </select>
    </div>
    <div class="col-md-2">
      <button type="submit" class="btn btn-dark w-100">
        <i class="bi bi-search"></i> Buscar
      </button>
    </div>
  </form>

  <!-- Resultados -->
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

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


