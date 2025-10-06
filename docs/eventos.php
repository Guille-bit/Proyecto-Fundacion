<?php
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
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="index.php">EventosApp</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarMenu">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="reservas.html">📅 Mis reservas</a></li>
        <li class="nav-item"><a class="nav-link" href="#">❤️ Favoritos</a></li>
      </ul>

      <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
        <li class="nav-item dropdown user-hover">
          <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userMenu" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <span class="me-1">👤</span>
            <?= isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Invitado'; ?>
          </a>
          <ul class="dropdown-menu dropdown-menu-start" aria-labelledby="userMenu">
            <?php if (!isset($_SESSION['username'])): ?>
              <li><a class="dropdown-item" href="login.html">🔐 Iniciar sesión</a></li>
              <li><a class="dropdown-item" href="registro.html">📝 Registrarse</a></li>
            <?php else: ?>
              <li><a class="dropdown-item" href="perfil.php">👤 Mi perfil</a></li>
              <li><a class="dropdown-item" href="?logout=true">🚪 Cerrar sesión</a></li>
            <?php endif; ?>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="container py-5">
  <h1 class="mb-4 text-center">🎉 Próximos Eventos</h1>

  <!-- Buscador y Filtro -->
  <form method="GET" class="row mb-4">
    <div class="col-md-6">
      <input type="text" name="busqueda" class="form-control" placeholder="🔍 Buscar eventos..." value="<?= htmlspecialchars($busqueda) ?>">
    </div>
    <div class="col-md-4">
      <select name="categoria" class="form-select">
        <option value="">🎯 Filtrar por categoría</option>
        <option value="Cultura" <?= $categoria == "Cultura" ? "selected" : "" ?>>Cultura</option>
        <option value="Tecnología" <?= $categoria == "Tecnología" ? "selected" : "" ?>>Tecnología</option>
        <option value="Ferias" <?= $categoria == "Ferias" ? "selected" : "" ?>>Ferias</option>
        <option value="Bienestar" <?= $categoria == "Bienestar" ? "selected" : "" ?>>Bienestar</option>
      </select>
    </div>
    <div class="col-md-2">
      <button type="submit" class="btn btn-dark w-100">Buscar</button>
    </div>
  </form>

  <div class="row g-4">
    <?php if ($resultado && $resultado->num_rows > 0): ?>
      <?php while ($evento = $resultado->fetch_assoc()): ?>
        <div class="col-md-4">
          <div class="card event-card h-100">
            <img src="<?= htmlspecialchars($evento['image_path']) ?>" class="card-img-top event-img" alt="<?= htmlspecialchars($evento['title']) ?>">
            <div class="card-body">
              <h5 class="event-title"><?= htmlspecialchars($evento['title']) ?></h5>
              <p class="mb-1"><strong>📅 Fecha:</strong> <?= date('d M Y', strtotime($evento['start_at'])) ?></p>
              <p class="mb-1"><strong>📍 Lugar:</strong> <?= htmlspecialchars($evento['location']) ?></p>
              <span class="badge bg-secondary"><?= htmlspecialchars($evento['category']) ?></span>
              <a href="#" class="btn btn-outline-dark w-100 mt-3">Reservar</a>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <div class="col-12">
        <div class="alert alert-warning text-center">😢 No se encontraron eventos con esos filtros.</div>
      </div>
    <?php endif; ?>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
