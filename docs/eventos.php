<?php
session_start();

// Configuración de base de datos
$servername = "100.107.241.28"; // o IP Tailscale si usas otro PC
$username   = "equipo";       // usuario de MySQL (ajusta si usas otro)
$password   = "PassMuySegura_123";           // contraseña (vacío por defecto en XAMPP)
$base_datos = "login_db";
$port       = 3306;

// Crear conexión
$conn = new mysqli($servername, $username, $password, $base_datos, $port);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Consulta de eventos
$sql = "SELECT * FROM eventos ORDER BY fecha ASC";
$resultado = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Eventos | EventosApp</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
    <div class="container-fluid">
      <a class="navbar-brand fw-bold" href="#">EventosApp</a>
      <button
        class="navbar-toggler"
        type="button"
        data-bs-toggle="collapse"
        data-bs-target="#navbarMenu"
        aria-controls="navbarMenu"
        aria-expanded="false"
        aria-label="Toggle navigation"
      >
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

      <?php if ($resultado->num_rows > 0): ?>
        <?php while($evento = $resultado->fetch_assoc()): ?>
          <div class="col-md-4">
            <div class="card event-card shadow-sm">
              <?php if (!empty($evento['imagen_url'])): ?>
                <img src="<?= htmlspecialchars($evento['imagen_url']) ?>" class="card-img-top event-img" alt="Imagen del evento" />
              <?php else: ?>
                <img src="https://source.unsplash.com/400x200/?event" class="card-img-top event-img" alt="Imagen genérica de evento" />
              <?php endif; ?>

              <div class="card-body">
                <h5 class="event-title"><?= htmlspecialchars($evento['titulo']) ?></h5>
                <p class="mb-1"><strong>📅 Fecha:</strong> <?= htmlspecialchars($evento['fecha']) ?> a las <?= htmlspecialchars($evento['hora']) ?></p>
                <p class="mb-1"><strong>📍 Lugar:</strong> <?= htmlspecialchars($evento['lugar']) ?></p>
                <p><?= nl2br(htmlspecialchars($evento['descripcion'])) ?></p>
                <a href="#" class="btn btn-outline-dark w-100 mt-3">Reservar</a>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p>No hay eventos disponibles.</p>
      <?php endif; ?>

    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>
