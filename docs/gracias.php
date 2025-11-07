<?php
require 'session_boot.php';
require 'conexion.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Si usas Composer (recomendado)
require __DIR__ . '/../vendor/autoload.php';

// -----------------------------------------------------------------------------
// 1) Obtener la última reserva del usuario logueado
// -----------------------------------------------------------------------------
$reserva_info = null;
if (isset($_SESSION['user_id'])) {
    $stmt = $connection->prepare("
        SELECT r.*, e.title, e.price, e.start_at, e.location, u.username, u.email
        FROM reservations r
        JOIN events e ON r.event_id = e.id
        JOIN users u ON r.user_id = u.id
        WHERE r.user_id = ?
        ORDER BY r.reservation_date DESC
        LIMIT 1
    ");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $reserva_info = $result->fetch_assoc();
}

// -----------------------------------------------------------------------------
// 2) Generar y enviar PDF si hay reserva
// -----------------------------------------------------------------------------
$enviado = false;
$pdf_path = null;

if ($reserva_info) {
    $correo = $reserva_info['email'];
    $nombre = $reserva_info['username'];
    $res_id = $reserva_info['id'];
    $transaction_id = !empty($reserva_info['transaction_id']) ? $reserva_info['transaction_id'] : ('R' . $res_id);

    // Crear directorio de entradas si no existe
    $dir = __DIR__ . '/entradas';
    if (!is_dir($dir)) mkdir($dir, 0777, true);
    $pdf_path = "$dir/entrada_{$res_id}.pdf";

    // Generar PDF (solo si no existe)
    if (!file_exists($pdf_path)) {
        $pdf = new \FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 20);
        $pdf->Cell(0, 10, utf8_decode('🎟️ Entrada de Reserva'), 0, 1, 'C');
        $pdf->Ln(8);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 8, utf8_decode('Evento: ') . utf8_decode($reserva_info['title']), 0, 1);
        $pdf->Cell(0, 8, utf8_decode('Fecha: ') . date('d/m/Y H:i', strtotime($reserva_info['start_at'])), 0, 1);
        $pdf->Cell(0, 8, utf8_decode('Lugar: ') . utf8_decode($reserva_info['location']), 0, 1);
        $pdf->Cell(0, 8, utf8_decode('Entradas: ') . ($reserva_info['quantity'] ?? 1), 0, 1);
        $pdf->Cell(0, 8, utf8_decode('Importe: ') . number_format($reserva_info['total_amount'] ?? $reserva_info['price'], 2) . ' €', 0, 1);
        $pdf->Ln(10);
        $pdf->Cell(0, 8, utf8_decode('Código de reserva: ') . $transaction_id, 0, 1);
        $pdf->Ln(10);
        $pdf->MultiCell(0, 8, utf8_decode("Gracias por tu compra, {$nombre}.\nPresenta este documento el día del evento.\n¡Disfruta!"));
        $pdf->Output('F', $pdf_path);
    }

    // Enviar correo con PHPMailer
    $asunto = "Tu entrada para el evento: " . $reserva_info['title'];
    $mensaje_html = "
    <p>Hola " . htmlspecialchars($nombre) . ",</p>
    <p>Gracias por tu reserva para <strong>" . htmlspecialchars($reserva_info['title']) . "</strong>.</p>
    <p>Adjuntamos tu entrada en formato PDF.</p>
    <p><strong>Detalles del evento:</strong><br>
    Fecha: " . date('d/m/Y H:i', strtotime($reserva_info['start_at'])) . "<br>
    Lugar: " . htmlspecialchars($reserva_info['location']) . "</p>
    <p>¡Disfruta del evento!<br>- Fundación XYZ</p>";

    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'adrianlijar02@gmail.com'; // Cambia por tu correo
        $mail->Password = 'prfw lyot xhsx ifug';     // Cambia por tu App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->setFrom('adrianlijar02@gmail.com', 'Fundación XYZ');
        $mail->addAddress($correo, $nombre);
        $mail->isHTML(true);
        $mail->Subject = $asunto;
        $mail->Body = $mensaje_html;
        $mail->AltBody = strip_tags($mensaje_html);
        if (file_exists($pdf_path)) $mail->addAttachment($pdf_path);
        $mail->send();
        $enviado = true;
    } catch (Exception $e) {
        error_log("Error al enviar correo: " . $mail->ErrorInfo);
    }
}
?>
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
    
    <?php if ($reserva_info): ?>
      <div class="card mt-4 text-start shadow-sm">
        <div class="card-header bg-primary text-white">
          <h5 class="mb-0"><i class="bi bi-ticket-perforated me-2"></i>Detalles de tu reserva</h5>
        </div>
        <div class="card-body">
          <h6 class="card-title"><?= htmlspecialchars($reserva_info['title']) ?></h6>
          <p class="card-text">
            <strong><i class="bi bi-calendar me-2"></i>Fecha:</strong> 
            <?= date('d/m/Y H:i', strtotime($reserva_info['start_at'])) ?><br>
            <strong><i class="bi bi-geo-alt me-2"></i>Lugar:</strong> 
            <?= htmlspecialchars($reserva_info['location']) ?><br>
            <strong><i class="bi bi-people me-2"></i>Entradas:</strong> 
            <?= $reserva_info['quantity'] ?><br>
            
            <?php if ((float)$reserva_info['total_amount'] > 0): ?>
              <strong><i class="bi bi-currency-euro me-2"></i>Total pagado:</strong> 
              <?= number_format($reserva_info['total_amount'], 2) ?> €<br>
              <?php if (!empty($reserva_info['transaction_id'])): ?>
                <strong><i class="bi bi-receipt me-2"></i>ID de transacción:</strong> 
                <code><?= htmlspecialchars($reserva_info['transaction_id']) ?></code><br>
              <?php endif; ?>
              <span class="badge bg-success mt-2">
                <i class="bi bi-check-circle me-1"></i>Pago confirmado
              </span>
            <?php else: ?>
              <span class="badge bg-info mt-2">
                <i class="bi bi-gift me-1"></i>Evento gratuito
              </span>
            <?php endif; ?>
          </p>
        </div>
      </div>
    <?php endif; ?>

    <p class="lead mt-4">
      Hemos registrado tu plaza correctamente. 
      <?php if ($reserva_info && (float)$reserva_info['total_amount'] > 0): ?>
        Tu pago ha sido procesado exitosamente.
      <?php endif; ?>
      <?php if ($enviado): ?>
        <br>✅ <strong>Se ha enviado tu entrada a <?= htmlspecialchars($reserva_info['email']) ?></strong>.
      <?php else: ?>
        <br>⚠️ <strong>No se pudo enviar el correo automáticamente.</strong>
      <?php endif; ?>
    </p>

    <hr>
    <p>¿Qué quieres hacer ahora?</p>
    <a href="index.php" class="btn btn-primary me-2">Ver más eventos</a>
    <a href="mis_reservas.php" class="btn btn-secondary me-2">Ver mis reservas</a>
    <?php if ($reserva_info): ?>
      <a href="generar_pdf_pago.php?id=<?= urlencode($reserva_info['id']) ?>" target="_blank" class="btn btn-success me-2">
        <i class="bi bi-download me-1"></i> Descargar recibo (PDF)
      </a>
      <a href="generar_entrada_pdf.php?id=<?= urlencode($reserva_info['id']) ?>" target="_blank" class="btn btn-outline-primary">
        <i class="bi bi-ticket-perforated me-1"></i> Descargar entrada (PDF)
      </a>
    <?php endif; ?>
  </div>
</div>

</body>
</html>
