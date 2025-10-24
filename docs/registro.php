<?php
require_once "conexion.php";

function parse_birthdate_to_mysql(?string $raw): ?string {
    if (!$raw) return null;
    $raw = trim($raw);
    $raw = str_replace('-', '/', $raw);

    if (preg_match('#^(\d{1,2})/(\d{1,2})/(\d{4})$#', $raw, $m)) {
        [$all, $d, $mth, $y] = $m;
        if (checkdate((int)$mth, (int)$d, (int)$y)) {
            return sprintf('%04d-%02d-%02d', $y, $mth, $d);
        }
        return null;
    }

    if (preg_match('#^\d{4}-\d{2}-\d{2}$#', $raw)) {
        return $raw;
    }

    return null;
}

$mensaje = "";
$esExito = false;

// Si se envió el formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username  = trim($_POST['nombre_user'] ?? '');
    $email     = trim($_POST['email_user'] ?? '');
    $password  = $_POST['password_user'] ?? '';
    $phone     = trim($_POST['phone_user'] ?? '');
    $birth_raw = trim($_POST['birthdate_user'] ?? '');

    if ($username === '' || $email === '' || $password === '') {
        $mensaje = "❌ Todos los campos obligatorios deben estar completos.";
    } else {
        $birthdate = parse_birthdate_to_mysql($birth_raw);
        if ($birth_raw !== '' && $birthdate === null) {
            $mensaje = "❌ Fecha inválida. Usa el formato dd/mm/aaaa.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $sql  = "INSERT INTO users (username, email, password, phone, birthdate)
                     VALUES (?, ?, ?, ?, ?)";
            $stmt = $connection->prepare($sql);

            if (!$stmt) {
                $mensaje = "❌ Error al preparar la consulta.";
            } else {
                $stmt->bind_param("sssss", $username, $email, $hash, $phone, $birthdate);
                if ($stmt->execute()) {
                    $mensaje = "✅ Usuario registrado correctamente. <a href='login.php'>Inicia sesión aquí</a>.";
                    $esExito = true;
                } else {
                    $mensaje = "❌ Error al registrar el usuario. Puede que el correo o nombre ya estén en uso.";
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <title>Página de registro</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css"
      integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T"
      crossorigin="anonymous"
    />
  </head>
  <body>
    <div class="text-center py-4">
    <img src="uploads/eventos/logo6.png" alt="Logo EventosApp" style="width: 250px; height: auto;">
  </div>
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-6">
          <form action="registro.php" method="POST">
            <h2 class="mt-5 mb-4 text-center">Regístrate</h2>

            <!-- Mostrar mensajes -->
            <?php if ($mensaje): ?>
              <div class="alert <?= $esExito ? 'alert-success' : 'alert-danger' ?>" role="alert">
                <?= $mensaje ?>
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
            <div class="form-group">
              <input
                type="email"
                class="form-control"
                name="email_user"
                placeholder="Correo electrónico"
                required
              />
            </div>
            <div class="form-group">
              <input
                type="tel"
                class="form-control"
                name="phone_user"
                placeholder="Número de teléfono"
                required
              />
            </div>
            <div class="form-group">
              <input
                type="text"
                class="form-control"
                name="birthdate_user"
                placeholder="Fecha de nacimiento (dd/mm/aaaa)"
                pattern="^(0?[1-9]|[12]\d|3[01])/(0?[1-9]|1[0-2])/\d{4}$"
                required
              />
            </div>
            <button type="submit" class="btn btn-primary btn-block" name="registro">
              Registrarse
            </button>
          </form>
          <p class="mt-3 text-center">
            ¿Ya tienes una cuenta? <a href="login.php">Inicia sesión aquí</a>
          </p>
        </div>
      </div>
    </div>

    <!-- Script para formatear fecha -->
    <script>
      document
        .querySelector('input[name="birthdate_user"]')
        .addEventListener("blur", (e) => {
          const v = e.target.value.trim();
          const m = v.match(/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/);
          if (!m) return;
          const dd = String(m[1]).padStart(2, "0");
          const mm = String(m[2]).padStart(2, "0");
          const yyyy = m[3];
          e.target.value = `${dd}/${mm}/${yyyy}`;
        });
    </script>

    <!-- Bootstrap scripts -->
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
