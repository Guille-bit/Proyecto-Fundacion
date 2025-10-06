<?php
session_start();
require_once "conexion.php";

// Variable para mostrar mensajes de error
$error = "";

// Procesar el formulario si se envió por POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['nombre_user'] ?? '');
    $password = $_POST['password_user'] ?? '';

    if ($username === '' || $password === '') {
        $error = "❌ Usuario y contraseña requeridos.";
    } else {
        $sql = "SELECT id, username, password FROM users WHERE username = ? LIMIT 1";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res && $res->num_rows === 1) {
            $user = $res->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                header("Location: index.php");
                exit;
            } else {
                $error = "❌ Contraseña incorrecta.";
            }
        } else {
            $error = "❌ Usuario no encontrado.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <title>Página de inicio de sesión</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css"
      integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T"
      crossorigin="anonymous"
    />
  </head>
  <body>
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-6">
          <form action="login.php" method="POST">
            <h2 class="mt-5 mb-4 text-center">Iniciar sesión</h2>

            <!-- Mostrar error si existe -->
            <?php if ($error): ?>
              <div class="alert alert-danger" role="alert">
                <?= htmlspecialchars($error) ?>
              </div>
            <?php endif; ?>

            <div class="form-group">
              <input
                type="text"
                class="form-control"
                name="nombre_user"
                placeholder="Nombre de usuario"
                required
              />
            </div>
            <div class="form-group">
              <input
                type="password"
                class="form-control"
                name="password_user"
                placeholder="Contraseña"
                required
              />
            </div>
            <button type="submit" class="btn btn-primary btn-block">
              Iniciar sesión
            </button>
          </form>
          <p class="mt-3 text-center">
            ¿No tienes una cuenta?
            <a href="registro.html">Haz click aquí para registrarte</a>
          </p>
        </div>
      </div>
    </div>

    <!-- Scripts de Bootstrap -->
    <script
      src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
      integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
      crossorigin="anonymous"
    ></script>
    <script
      src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js"
      integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"
      crossorigin="anonymous"
    ></script>
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js"
      integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
      crossorigin="anonymous"
    ></script>
  </body>
</html>
