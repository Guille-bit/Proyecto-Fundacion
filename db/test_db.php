<?php
require_once "conexion.php";
$result = $conn->query("SELECT COUNT(*) AS c FROM usuarios");
if ($result) {
    $row = $result->fetch_assoc();
    echo "✅ Conexión OK. Usuarios en BD: " . intval($row['c']);
} else {
    echo "❌ Error en la consulta: " . $conn->error;
}
?>