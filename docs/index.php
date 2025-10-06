<?php
session_start();
session_destroy(); // Elimina toda la sesión
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Eventos | </title>
  <link rel="stylesheet" href="style.css">
  <script src="js/funciones.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

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
        <li class="nav-item">
          <a class="nav-link" href="reservas.html"> Mis reservas</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#"><i class="bi bi-heart"></i> Favoritos</a>
        </li>
      </ul>

      <!-- Menú de usuario con icono -->
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
        <li class="nav-item dropdown user-hover">
          <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userMenu" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <span class="me-1"><i class="bi bi-person-circle"></i></span>
            <?php
              if (isset($_SESSION['username'])) {
                echo htmlspecialchars($_SESSION['username']);
              } else {
                echo 'Invitado';
              }
            ?>
          </a>
          <ul class="dropdown-menu dropdown-menu-start" aria-labelledby="userMenu">
            <?php if (!isset($_SESSION['username'])): ?>
              <li><a class="dropdown-item" href="login.php"><i class="bi bi-box-arrow-in-right me-2"></i> Iniciar sesión</a></li>
              <li><a class="dropdown-item" href="registro.php"><i class="bi bi-pencil-square me-2"></i> Registrarse</a></li>
            <?php else: ?>
              <li><a class="dropdown-item" href="perfil.php"><i class="bi bi-person-circle me-2"></i> Mi perfil</a></li>
              <li><a class="dropdown-item" href="index.php"><i class="bi bi-box-arrow-right me-2"></i> Cerrar sesión</a></li>
              <li><a class="dropdown-item" href="crear_eventos.php"><i class="bi bi-plus-lg"></i> Crear Eventos</a></i>
            <?php endif; ?>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>
<div class="container py-5">
  <h1 class="mb-4 text-center"><i class="bi bi-watch"></i>Próximos Eventos</h1>
  <div class="row g-4">

    <!-- Tarjeta 1 -->
    <div class="col-md-4">
      <div class="card event-card">
        <img src="img/Sala_de_cine.jpg" class="card-img-top event-img" alt="Festival de Cine">
        <div class="card-body">
          <h5 class="event-title">Festival de Cine Independiente</h5>
          <p class="mb-1"><strong><i class="bi bi-calendar-fill"></i> Fecha:</strong> 12 Oct 2025</p>
          <p class="mb-1"><strong><i class="bi bi-geo-alt"></i> Lugar:</strong> Cines Callao</p>
          <span class="badge bg-primary">Cultura</span>
          <a href="#" class="btn btn-outline-dark w-100 mt-3 reservar-btn"
   data-titulo="Festival de Cine Independiente"
   data-fecha="12 Oct 2025"
   data-lugar="Centro Cultural Móstoles"
   data-imagen="img/Sala_de_cine.jpg"
   data-categoria="Cultura">Reservar</a>
        </div>
      </div>
    </div>

    <!-- Tarjeta 2 -->
    <div class="col-md-4">
      <div class="card event-card">
        <img src="img/programar.jpg" class="card-img-top event-img" alt="Taller de Programación">
        <div class="card-body">
          <h5 class="event-title">Taller de Programación Web</h5>
          <p class="mb-1"><strong><i class="bi bi-calendar-fill"></i> Fecha:</strong> 15 Oct 2025</p>
          <p class="mb-1"><strong><i class="bi bi-geo-alt"></i> Lugar:</strong> Aula Virtual Medac</p>
          <span class="badge bg-success">Tecnología</span>
          <a href="#" class="btn btn-outline-dark w-100 mt-3">Reservar</a>
        </div>
      </div>
    </div>

    <!-- Tarjeta 3 -->
    <div class="col-md-4">
      <div class="card event-card">
        <img src="img/artesanal.jpg" class="card-img-top event-img" alt="Mercado Artesanal">
        <div class="card-body">
          <h5 class="event-title">Mercado Artesanal de Otoño</h5>
          <p class="mb-1">
         <strong><i class="bi bi-calendar-fill"></i> Fecha:</strong> 18 Oct 2025</p>
          <p class="mb-1">
            <strong><i class="bi bi-geo-alt"></i> Lugar:</strong> Plaza Mayor</p>
          <span class="badge bg-warning text-dark">Ferias</span>
          <a href="#" class="btn btn-outline-dark w-100 mt-3">Reservar</a>
        </div>
      </div>
    </div>

    <!-- Tarjeta 4 -->
    <div class="col-md-4">
      <div class="card event-card">
        <img src="img/yoga.jpg" class="card-img-top event-img" alt="Clase de Yoga">
        <div class="card-body">
          <h5 class="event-title">Clase Gratuita de Yoga</h5>
          <p class="mb-1"><strong><i class="bi bi-calendar-fill"></i> Fecha:</strong> 20 Oct 2025</p>
          <p class="mb-1"><strong><i class="bi bi-geo-alt"></i> Lugar:</strong> Parque del Soto</p>
          <span class="badge bg-info text-dark">Bienestar</span>
          <a href="#" class="btn btn-outline-dark w-100 mt-3">Reservar</a>
        </div>
      </div>
    </div>

    <!-- Tarjeta 5 -->
    <div class="col-md-4">
      <div class="card event-card">
        <img src="img/lirbo.webp" class="card-img-top event-img" alt="Feria del Libro">
        <div class="card-body">
          <h5 class="event-title">Feria del Libro Local</h5>
          <p class="mb-1"><strong><i class="bi bi-calendar-fill"></i> Fecha:</strong> 22 Oct 2025</p>
          <p class="mb-1"><strong><i class="bi bi-geo-alt"></i> Lugar:</strong> Parque El Retiro</p>
          <span class="badge bg-primary">Cultura</span>
          <a href="#" class="btn btn-outline-dark w-100 mt-3">Reservar</a>
        </div>
      </div>
    </div>
  </body>
  </html>


