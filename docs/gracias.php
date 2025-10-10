<?php require 'session_boot.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>¡Reserva Confirmada!</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<div class="container text-center py-5">
  <div class="mx-auto" style="max-width: 600px;">
    <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
    <h1 class="display-4 mt-3">¡Gracias por tu reserva!</h1>
    <p class="lead">Hemos registrado tu plaza correctamente. Recibirás un correo electrónico con los detalles (función no implementada).</p>
    <hr>
    <p>¿Qué quieres hacer ahora?</p>
    <a href="index.php" class="btn btn-primary me-2">Ver más eventos</a>
    <a href="mis_reservas.php" class="btn btn-secondary">Ver mis reservas</a>
  </div>
</div>

</body>
</html>
