<?php
require 'session_boot.php';
require 'conexion.php';

// Proteger: solo para usuarios logueados
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// 1. Verificar que se ha enviado el formulario por el método POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    // 2. Recoger y validar datos
    $event_id = isset($_POST['event_id']) ? (int)$_POST['event_id'] : 0;
    $user_id = (int)$_SESSION['user_id'];
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;

    // Validación básica
    if ($event_id > 0 && $user_id > 0 && $quantity > 0 && $quantity <= 10) {
        
        // (Opcional) Aquí podrías añadir una comprobación de aforo si tienes esa columna en tu tabla 'events'

        // 3. Preparar la consulta para insertar los datos de forma segura
        $stmt = $connection->prepare(
            "INSERT INTO reservations (event_id, user_id, quantity) VALUES (?, ?, ?)"
        );
        $stmt->bind_param("iii", $event_id, $user_id, $quantity);
        
        // 4. Ejecutar y verificar
        if ($stmt->execute()) {
            // Si todo va bien, redirigir a una página de éxito
            header("Location: gracias.php");
            exit();
        } else {
            // Manejar un posible error (p. ej. evento no existe)
            die("Error al procesar la reserva. Por favor, inténtalo de nuevo.");
        }
        
    } else {
        die("Datos inválidos. La cantidad debe ser entre 1 y 10.");
    }
} else {
    // Si alguien intenta acceder a este archivo directamente, lo redirigimos
    header("Location: index.php");
    exit();
}
?>
