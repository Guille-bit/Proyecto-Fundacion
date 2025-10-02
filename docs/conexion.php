<?php
$servername = "100.107.241.28";
$username   = "equipo";        // usuario MySQL
$password   = "PassMuySegura_123";   // contraseña MySQL
$dbname     = "login_db";
$port       = 3306;

$conexion = mysqli_connect ($servername, $username, $password, $dbname);

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

