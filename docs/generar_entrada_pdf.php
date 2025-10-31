<?php
// docs/generar_entrada_pdf.php
// Genera una entrada/ticket en PDF para una reserva (tabla `reservations`).
// Usa Dompdf.

require_once __DIR__ . '/conexion.php';

$vendor = __DIR__ . '/../vendor/autoload.php';
if (!file_exists($vendor)) {
    die('Dependencia faltante: ejecuta composer require dompdf/dompdf en la raíz del proyecto.');
}
require_once $vendor;

use Dompdf\Dompdf;
use Dompdf\Options;

// Parámetro: id de la reserva
$reservation_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($reservation_id <= 0) {
    die('ID de reserva no válido. Pasa ?id=NN al script.');
}

// Consultar reserva y evento
$sql = "SELECT r.id AS reservation_id, r.event_id, r.user_id, r.quantity, r.reservation_date, r.total_amount, r.transaction_id,
               e.title AS event_title, e.start_at AS event_start, e.location AS event_location
        FROM reservations r
        LEFT JOIN events e ON e.id = r.event_id
        WHERE r.id = ? LIMIT 1";

$data = null;
if ($stmt = $connection->prepare($sql)) {
    $stmt->bind_param('i', $reservation_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $stmt->close();
} else {
    die('Error en la consulta: ' . $connection->error);
}

if (!$data) {
    die('Reserva no encontrada.');
}

// Seguridad: permitir sólo al propietario si hay sesión
if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}
if (isset($_SESSION['user_id']) && $_SESSION['user_id'] != $data['user_id']) {
    die('No autorizado para ver esta entrada.');
}

// Preparar datos
$res_id = htmlspecialchars($data['reservation_id']);
$event_title = htmlspecialchars($data['event_title'] ?? 'Evento');
$event_date = htmlspecialchars($data['event_start'] ?? '');
$event_location = htmlspecialchars($data['event_location'] ?? '');
$quantity = (int)($data['quantity'] ?? 1);
$total = number_format((float)($data['total_amount'] ?? 0), 2, ',', '.');
$txn = htmlspecialchars($data['transaction_id'] ?? '');

// HTML del ticket
$html = <<<HTML
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Entrada #$res_id</title>
  <style>
    body { font-family: DejaVu Sans, Arial, sans-serif; color: #222; }
    .ticket { border: 2px dashed #333; padding: 20px; max-width: 700px; margin: 0 auto; }
    h1 { text-align: center; font-size: 20px; margin-bottom: 8px; }
    .meta { display:flex; justify-content:space-between; margin-bottom:10px; }
    .meta div { width:48%; }
    .footer { text-align:center; margin-top:18px; font-size:12px; color:#666; }
    .big { font-size:1.1em; font-weight:700; }
  </style>
</head>
<body>
  <div class="ticket">
    <h1>Entrada - {$event_title}</h1>
    <div class="meta">
      <div>
        <div><strong>Fecha:</strong> {$event_date}</div>
        <div><strong>Lugar:</strong> {$event_location}</div>
      </div>
      <div>
        <div><strong>Reserva #:</strong> {$res_id}</div>
        <div><strong>Entradas:</strong> {$quantity}</div>
      </div>
    </div>
    <hr>
    <p class="big">Total: {$total} €</p>
    <p>Transacción: {$txn}</p>
    <div class="footer">Presenta esta entrada en la entrada del evento.</div>
  </div>
</body>
</html>
HTML;

// Generar PDF
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('defaultFont', 'DejaVu Sans');

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$filename = 'entrada_' . $res_id . '.pdf';
$dompdf->stream($filename, ["Attachment" => 0]);

?>