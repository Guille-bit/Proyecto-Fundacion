<?php
require 'conexion.php';

if(isset($_POST['registro'])) {
  $usuario = $_POST['nombre_user'];
  $password = $_POST['password_user'];
  $email = $_POST['email_user'];

  $sql = "INSERT INTO usuarios (id_user, nombre_user, password_user, email_user) VALUES (null, '$usuario', '$password', '$email')";
  $resultado = mysqli_query($conexion,$sql);
    if($resultado){
      echo "Se insertaron los datos correctamente.";
    } else {
      echo "No se puede insertar la información." . "<br>";
      echo "Error: " . $sql . "<br>" . mysqli_error($conexion);
    }
}
?>