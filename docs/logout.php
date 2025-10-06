<?php
session_start();
session_unset();
session_destroy();

// volver al login.html
header("Location: login.php");
exit;
