<?php
$servidor = "localhost";
$usuario = "tomas3c";
$contraseña = "Parafernalio98-";
$base_de_datos = "tomas3c";

$conexion = mysqli_connect ($servidor, $usuario, $contraseña, $base_de_datos);

if (!$conexion) {
  die ("Conexión fallida: " . mysqli_connect_error());
}

/*$sql= "SELECT * FROM usuarios";
$resultado = mysqli_query($conexion, $sql);

if (mysqli_num_rows($resultado) > 0){
  while($fila = mysqli_fetch_assoc ($resultado)){
    echo "ID: " . $fila["id_user"] . " - Nombre: " . $fila["nombre_user"] . " -Contraseña: " .  $fila["password_user"] . " -Email: " .  $fila["email_user"]."<br>";
    }
} else {
  echo "No se encontraron resultados.";
}*/

//mysqli_close($conexion);
?>