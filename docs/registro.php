<?php
require 'connection.php';

if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email    = $_POST['email'];

    $sql = "INSERT INTO users (id, username, password, email) 
            VALUES (NULL, '$username', '$password', '$email')";
    $result = mysqli_query($connection, $sql);

    if ($result) {
        echo "Data inserted successfully.";
    } else {
        echo "Failed to insert data." . "<br>";
        echo "Error: " . $sql . "<br>" . mysqli_error($connection);
    }
}
?>
