<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Eventos | EventosApp</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
    <div class="container-fluid">
      <a class="navbar-brand fw-bold" href="#">EventosApp</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarMenu">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <?php if (!isset($_SESSION['usuario_nombre'])): ?>
            <li class="nav-item">
              <a class="nav-link" href="login.html">🔐 Iniciar sesión</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="registro.html">📝 Registrarse</a>
            </li>
          <?php endif; ?>

          <li class="nav-item">
            <a class="nav-link" href="#">📅 Mis reservas</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">❤️ Favoritos</a>
          </li>
        </ul>

        <?php if (isset($_SESSION['usuario_nombre'])): ?>
          <span class="navbar-text">
            Hola, <strong><?= htmlspecialchars($_SESSION['usuario_nombre']) ?></strong>
            <a href="logout.php" class="btn btn-sm btn-outline-danger ms-3">Cerrar sesión</a>
          </span>
        <?php endif; ?>

      </div>
    </div>
  </nav>

  <div class="container py-5">
    <h1 class="mb-4 text-center">🎉 Próximos Eventos</h1>
    <div class="row g-4">
      <!-- Tarjeta 1 -->
      <div class="col-md-4">
        <div class="card event-card">
          <img src="https://source.unsplash.com/400x200/?cinema" class="card-img-top event-img" alt="Festival de Cine">
          <div class="card-body">
            <h5 class="event-title">Festival de Cine Independiente</h5>
            <p class="mb-1"><strong>📅 Fecha:</strong> 12 Oct 2025</p>
            <p class="mb-1"><strong>📍 Lugar:</strong> Centro Cultural Móstoles</p>
            <span class="badge bg-primary">Cultura</span>
            <a href="#" class="btn btn-outline-dark w-100 mt-3">Inscribirse</a>
          </div>
        </div>
      </div>

      <!-- Tarjeta 2 -->
      <div class="col-md-4">
        <div class="card event-card">
          <img src="https://source.unsplash.com/400x200/?coding" class="card-img-top event-img" alt="Taller de Programación">
          <div class="card-body">
            <h5 class="event-title">Taller de Programación Web</h5>
            <p class="mb-1"><strong>📅 Fecha:</strong> 15 Oct 2025</p>
            <p class="mb-1"><strong>📍 Lugar:</strong> Aula Virtual Medac</p>
            <span class="badge bg-success">Tecnología</span>
            <a href="#" class="btn btn-outline-dark w-100 mt-3">Reservar</a>
          </div>
        </div>
      </div>

      <!-- Tarjeta 3 -->
      <div class="col-md-4">
        <div class="card event-card">
          <img src="https://source.unsplash.com/400x200/?market" class="card-img-top event-img" alt="Mercado Artesanal">
          <div class="card-body">
            <h5 class="event-title">Mercado Artesanal de Otoño</h5>
            <p class="mb-1"><strong>📅 Fecha:</strong> 18 Oct 2025</p>
            <p class="mb-1"><strong>📍 Lugar:</strong> Plaza del Ayuntamiento</p>
            <span class="badge bg-warning text-dark">Ferias</span>
            <a href="#" class="btn btn-outline-dark w-100 mt-3">Reservar</a>
          </div>
        </div>
      </div>

      <!-- Tarjeta 4 -->
      <div class="col-md-4">
        <div class="card event-card">
          <img src="https://source.unsplash.com/400x200/?yoga" class="card-img-top event-img" alt="Clase de Yoga">
          <div class="card-body">
            <h5 class="event-title">Clase Gratuita de Yoga</h5>
            <p class="mb-1"><strong>📅 Fecha:</strong> 20 Oct 2025</p>
            <p class="mb-1"><strong>📍 Lugar:</strong> Parque del Soto</p>
            <span class="badge bg-info text-dark">Bienestar</span>
            <a href="#" class="btn btn-outline-dark w-100 mt-3">Reservar</a>
          </div>
        </div>
      </div>

      <!-- Tarjeta 5 -->
      <div class="col-md-4">
        <div class="card event-card">
          <img src="https://source.unsplash.com/400x200/?book" class="card-img-top event-img" alt="Feria del Libro">
            <div class="card-body">
              <h5 class="event-title">Feria del Libro Local</h5>
              <p class="mb-1"><strong>📅 Fecha:</strong> 22 Oct 2025</p>
              <p class="mb-1"><strong>📍 Lugar:</strong> Biblioteca Central</p>
              <span class="badge bg-primary">Cultura</span>
              <a href="#" class="btn btn-outline-dark w-100 mt-3">Reservar</a>
            </div>
        </div>
      </div>

      <!-- Tarjeta 6 -->
      <div class="col-md-4">
        <div class="card event-card">
          <img src="https://source.unsplash.com/400x200/?art" class="card-img-top event-img" alt="Evento de Arte">
          <div class="card-body">
            <h5 class="event-title">Exhibición de Arte Local</h5>
            <p class="mb-1"><strong>📅 Fecha:</strong> 25 Oct 2025</p>
            <p class="mb-1"><strong>📍 Lugar:</strong> Galería Central</p>
            <span class="badge bg-secondary">Arte</span>
            <a href="#" class="btn btn-outline-dark w-100 mt-3">Reservar</a>
          </div>
        </div>
      </div>

    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

