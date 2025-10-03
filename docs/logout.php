<?php
session_start();
session_unset();
session_destroy();

// volver al login
header("Location: login.html");
exit;
