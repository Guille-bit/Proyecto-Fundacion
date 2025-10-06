<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css"
      integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T"
      crossorigin="anonymous"
    />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Página de registro</title>
  </head>

  <body>
    <?php
require_once "conexion.php"; // o conexion.php

function parse_birthdate_to_mysql(?string $raw): ?string {
    if (!$raw) return null;
    $raw = trim($raw);

    // Acepta 23/11/2000 o 23-11-2000
    $raw = str_replace('-', '/', $raw);

    // dd/mm/yyyy
    if (preg_match('#^(\d{1,2})/(\d{1,2})/(\d{4})$#', $raw, $m)) {
        [$all, $d, $mth, $y] = $m;
        if (checkdate((int)$mth, (int)$d, (int)$y)) {
            return sprintf('%04d-%02d-%02d', $y, $mth, $d); // YYYY-MM-DD
        }
        return null; // fecha inválida
    }

    // Como fallback, si viniera en YYYY-MM-DD (por si cambia el input)
    if (preg_match('#^\d{4}-\d{2}-\d{2}$#', $raw)) {
        return $raw;
    }

    return null; // formato no reconocido
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: registro.html");
    exit;
}

$username  = trim($_POST['nombre_user'] ?? '');
$email     = trim($_POST['email_user'] ?? '');
$password  = $_POST['password_user'] ?? '';
$phone     = trim($_POST['phone_user'] ?? '');
$birth_raw = trim($_POST['birthdate_user'] ?? '');

if ($username === '' || $email === '' || $password === '') {
    die("❌ Todos los campos obligatorios (usuario, email y contraseña).");
}

$birthdate = parse_birthdate_to_mysql($birth_raw);
if ($birth_raw !== '' && $birthdate === null) {
    die("❌ La fecha debe ser válida y con formato dd/mm/aaaa. Ej: 23/11/2000");
}

$hash = password_hash($password, PASSWORD_DEFAULT);

$sql  = "INSERT INTO users (username, email, password, phone, birthdate)
         VALUES (?, ?, ?, ?, ?)";
$stmt = $connection->prepare($sql);
$stmt->bind_param("sssss", $username, $email, $hash, $phone, $birthdate);
$stmt->execute();

echo "✅ Usuario registrado. <a href='login.html'>Inicia sesión</a>";
?>
 <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-6">
          <form action="registro.php" method="POST">
            <h2 class="mt-5 mb-4">Regístrate</h2>
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
            <button type="submit" class="btn btn-primary" name="registro">
              Registrarse
            </button>
          </form>
          <p class="mt-3">
            ¿Ya tienes una cuenta? <a href="login.php">Inicia sesión aquí</a>
          </p>
        </div>
      </div>
    </div>
    <script>
      document
        .querySelector('input[name="birthdate_user"]')
        .addEventListener("blur", (e) => {
          const v = e.target.value.trim();
          const m = v.match(/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/);
          if (!m) return; // formato no válido, el servidor validará
          const dd = String(m[1]).padStart(2, "0");
          const mm = String(m[2]).padStart(2, "0");
          const yyyy = m[3];
          e.target.value = `${dd}/${mm}/${yyyy}`;
        });
    </script>
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


