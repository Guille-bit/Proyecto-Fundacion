<?php
require('libs/fpdf.php');
require('conexion.php'); // tu archivo de conexión

if (isset($_GET['id'])) {
  $id = (int)$_GET['id'];

  // Consulta segura
  $stmt = $connection->prepare("SELECT event_id, user_id  FROM reservations WHERE user_id= ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $stmt->bind_result($titulo, $fecha, $lugar);
  $stmt->fetch();
  $stmt->close();

  // Generar PDF
  $pdf = new FPDF();
  $pdf->AddPage();
  $pdf->SetFont('Arial','B',16);
  $pdf->Cell(0,10,'Entrada para el Evento',0,1,'C');
  $pdf->Ln(10);
  $pdf->SetFont('Arial','',12);
  $pdf->Cell(0,10,"Evento: $titulo",0,1);
  $pdf->Cell(0,10,"Fecha: " . date('d/m/Y H:i', strtotime($fecha)),0,1);
  $pdf->Cell(0,10,"Lugar: $lugar",0,1);
  $pdf->Cell(0,10,"ID de Reserva: $id",0,1);
  $pdf->Ln(10);
  $pdf->SetFont('Arial','I',10);
  $pdf->Cell(0,10,'Presenta esta entrada el día del evento',0,1,'C');

  $pdf->Output('I', "entrada_$id.pdf");
} else {
  echo "ID de reserva no válido.";
}
?>
