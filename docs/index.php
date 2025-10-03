<?php
session_start();
session_destroy();
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
        <li class="nav-item">
          <a class="nav-link" href="reservas.html">📅 Mis reservas</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">❤️ Favoritos</a>
        </li>
      </ul>

      <!-- Menú de usuario con icono -->
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
        <li class="nav-item dropdown user-hover">
  <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userMenu" role="button">
     <?php
      if (isset($_SESSION['username'])) {
        echo htmlspecialchars($_SESSION['username']);
      } else {
        echo 'Invitado';
      }
    ?>
    <span class="me-1">👤</span> 
  </a>
  <ul class="dropdown-menu dropdown-menu-start" aria-labelledby="userMenu">
    <li><a class="dropdown-item" href="login.html">🔐 Iniciar sesión</a></li>
    <li><a class="dropdown-item" href="registro.html">📝 Registrarse</a></li>
  </ul>
</li>
      </ul>
    </div>
  </div>
</nav>



<div class="container py-5">
  <h1 class="mb-4 text-center">🎉 Próximos Eventos</h1>
  <div class="row g-4">

    <!-- Tarjeta 1 -->
    <div class="col-md-4">
      <div class="card event-card">
        <img src="img/Sala_de_cine.jpg" class="card-img-top event-img" alt="Festival de Cine">
        <div class="card-body">
          <h5 class="event-title">Festival de Cine Independiente</h5>
          <p class="mb-1"><strong>📅 Fecha:</strong> 12 Oct 2025</p>
          <p class="mb-1"><strong>📍 Lugar:</strong> Centro Cultural Móstoles</p>
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
        <img src="img/artesanal.jpg" class="card-img-top event-img" alt="Mercado Artesanal">
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
        <img src="img/yoga.jpg" class="card-img-top event-img" alt="Clase de Yoga">
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
        <img src="img/lirbo.webp" class="card-img-top event-img" alt="Feria del Libro">
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

