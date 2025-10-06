<?php
session_start();
require __DIR__ . '/conexion.php'; // $connection (mysqli)

date_default_timezone_set('Europe/Madrid');

function dtl_to_mysql(?string $s): ?string {
  if (!$s) return null;
  $s = str_replace('T', ' ', trim($s));
  try { return (new DateTime($s))->format('Y-m-d H:i:s'); }
  catch (Exception $e) { return null; }
}
function slug(string $s): string {
  if (function_exists('iconv')) $s = iconv('UTF-8','ASCII//TRANSLIT',$s);
  $s = strtolower($s);
  $s = preg_replace('/[^a-z0-9]+/','-',$s);
  $s = trim($s,'-');
  return $s ?: 'evento';
}

$errors = [];
$val = [
  'title' => '', 'description' => '', 'category' => 'Others', 'location' => '',
  'start_at' => '', 'end_at' => '', 'capacity' => '', 'price' => '0', 'is_public' => '1'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $val['title']       = trim($_POST['title'] ?? '');
  $val['description'] = trim($_POST['description'] ?? '');
  $val['category']    = trim($_POST['category'] ?? 'Others');
  $val['location']    = trim($_POST['location'] ?? '');
  $val['start_at']    = trim($_POST['start_at'] ?? '');
  $val['end_at']      = trim($_POST['end_at'] ?? '');
  $val['capacity']    = trim($_POST['capacity'] ?? '');
  $val['price']       = trim($_POST['price'] ?? '0');
  $val['is_public']   = isset($_POST['is_public']) ? '1' : '0';

  $start_at = dtl_to_mysql($val['start_at']);
  $end_at   = $val['end_at'] !== '' ? dtl_to_mysql($val['end_at']) : null;
  $capacity = $val['capacity'] !== '' ? (int)$val['capacity'] : null;
  $price    = is_numeric($val['price']) ? (float)$val['price'] : 0.0;
  $is_public = (int)$val['is_public'];

  if ($val['title'] === '')     $errors[] = 'El título es obligatorio.';
  if ($val['location'] === '')  $errors[] = 'La ubicación es obligatoria.';
  if (!$start_at)               $errors[] = 'La fecha/hora de inicio no es válida.';
  if ($capacity !== null && $capacity < 0) $errors[] = 'La capacidad no puede ser negativa.';
  if ($price < 0)               $errors[] = 'El precio no puede ser negativo.';

  $image_ok = false; $image_tmp = null; $ext = null;
  if (!empty($_FILES['poster']['name']) && ($_FILES['poster']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
    $image_tmp = $_FILES['poster']['tmp_name'];
    $size = (int)$_FILES['poster']['size'];
    if ($size > 2*1024*1024) {
      $errors[] = 'La imagen no puede superar 2MB.';
    } else {
      $finfo = new finfo(FILEINFO_MIME_TYPE);
      $mime  = $finfo->file($image_tmp);
      $allow = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
      if (!isset($allow[$mime])) {
        $errors[] = 'Formato no permitido (JPG, PNG o WebP).';
      } else {
        $ext = $allow[$mime];
        $image_ok = true;
      }
    }
  }

  if (!$errors) {
    $connection->begin_transaction();

    $stmt = $connection->prepare(
      "INSERT INTO events (user_id,title,description,category,location,start_at,end_at,capacity,price,image_path,is_public)
       VALUES (?,?,?,?,?,?,?,?,?,NULL,?)"
    );
    $uid = (int)$_SESSION['user_id'];
    $desc = $val['description'] !== '' ? $val['description'] : null;
    $stmt->bind_param(
      "issssssidi",
      $uid,
      $val['title'],
      $desc,
      $val['category'],
      $val['location'],
      $start_at,
      $end_at,
      $capacity,
      $price,
      $is_public
    );
    $ok_insert = $stmt->execute();

    if (!$ok_insert) {
      $connection->rollback();
      $errors[] = 'No se pudo guardar el evento.';
    } else {
      $event_id = $connection->insert_id;
      $image_path = null;

      if ($image_ok) {
        $dir = __DIR__ . '/uploads/eventos';
        if (!is_dir($dir)) { @mkdir($dir, 0775, true); }

        $filename = sprintf('ev_%d_%s_%s.%s',
          $event_id, slug($val['title']), substr(bin2hex(random_bytes(4)),0,8), $ext);
        $dest = $dir . '/' . $filename;

        if (move_uploaded_file($image_tmp, $dest)) {
          $image_path = 'uploads/eventos/' . $filename;
          $up = $connection->prepare("UPDATE events SET image_path=? WHERE id=?");
          $up->bind_param("si", $image_path, $event_id);
          if (!$up->execute()) {
            $connection->rollback();
            $errors[] = 'El evento se creó, pero no se pudo asociar la imagen.';
          }
        } else {
          $connection->rollback();
          $errors[] = 'No se pudo mover la imagen al servidor.';
        }
      }

      if (!$errors) {
        $connection->commit();
        $_SESSION['flash'] = '✅ Evento creado correctamente';
        header('Location: index.php'); exit;
      }
    }
  }
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Crear evento</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="index.php">EventosApp</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="mis_eventos.php">Mis eventos</a></li>
        <li class="nav-item"><a class="nav-link text-danger" href="logout.php">Cerrar sesión</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container py-4 py-md-5">
  <div class="mx-auto card border-0 shadow-sm rounded-4 p-4 p-md-5" style="max-width:760px;">
    <h2 class="mb-1">Crear evento</h2>
    <p class="text-muted mb-4">Publica un nuevo evento y añade una imagen (opcional).</p>

    <?php if (!empty($errors)): ?>
      <div class="alert alert-danger">
        <ul class="mb-0">
          <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" novalidate>
      <div class="row g-3">
        <div class="col-12">
          <label class="form-label">Título *</label>
          <input type="text" name="title" class="form-control form-control-lg rounded-3"
                 required maxlength="150" value="<?= htmlspecialchars($val['title']) ?>">
        </div>

        <div class="col-md-6">
          <label class="form-label">Categoría</label>
          <select name="category" class="form-select">
            <?php
              $cats = ['Cultura','Tecnología','Ferias','Bienestar','Others'];
              foreach ($cats as $c) {
                $sel = ($val['category'] === $c) ? 'selected' : '';
                echo "<option $sel>".htmlspecialchars($c)."</option>";
              }
            ?>
          </select>
        </div>

        <div class="col-md-6">
          <label class="form-label">Ubicación *</label>
          <input type="text" name="location" class="form-control" required
                 value="<?= htmlspecialchars($val['location']) ?>">
        </div>

        <div class="col-md-6">
          <label class="form-label">Inicio *</label>
          <input type="datetime-local" name="start_at" class="form-control" required
                 value="<?= htmlspecialchars($val['start_at']) ?>">
        </div>

        <div class="col-md-6">
          <label class="form-label">Fin</label>
          <input type="datetime-local" name="end_at" class="form-control"
                 value="<?= htmlspecialchars($val['end_at']) ?>">
        </div>

        <div class="col-md-4">
          <label class="form-label">Capacidad</label>
          <input type="number" name="capacity" min="0" class="form-control"
                 value="<?= htmlspecialchars($val['capacity']) ?>">
        </div>

        <div class="col-md-4">
          <label class="form-label">Precio (€)</label>
          <input type="number" name="price" min="0" step="0.01" class="form-control"
                 value="<?= htmlspecialchars($val['price']) ?>">
        </div>

        <div class="col-md-4 d-flex align-items-end">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="is_public" id="is_public"
                   <?= $val['is_public']==='1'?'checked':''; ?>>
            <label class="form-check-label" for="is_public">Evento público</label>
          </div>
        </div>

        <div class="col-12">
          <label class="form-label">Descripción</label>
          <textarea name="description" rows="4" class="form-control"
                    placeholder="Detalles del evento..."><?= htmlspecialchars($val['description']) ?></textarea>
        </div>

        <div class="col-12">
          <label class="form-label">Imagen (JPG/PNG/WebP, máx. 2MB)</label>
          <input type="file" name="poster" accept="image/jpeg,image/png,image/webp" class="form-control">
        </div>

        <div class="col-12">
          <button class="btn btn-primary w-100" type="submit">Crear evento</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
