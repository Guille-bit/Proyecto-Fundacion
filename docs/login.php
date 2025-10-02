<?php 
require 'conexion.php';
 if (isset ($_POST['login'])) {
  $username = $_POST['nombre_user'];
  $password = $_POST['password_user'];

  $sql = "SELECT * FROM usuarios WHERE nombre_user = '$username' AND password_user = '$password'";
  $resultado = mysqli_query($conexion,$sql);
  $numero_registros = mysqli_num_rows($resultado);
    if($numero_registros != 0) {
      echo "Sesión iniciada con éxito, bienvenido " . $username . "!";
    } else {
      echo "Credenciales inválidas. Por favor, verifique su nombre de usuario y/o contraseña." . "<br>";
      echo "Error: " . $sql . "<br>" . mysqli_error($conexion);
    }
 }
?>
