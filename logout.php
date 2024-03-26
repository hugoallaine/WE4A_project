<?php
session_start();
$_SESSION = array();
session_destroy();
setcookie("email", NULL, -1);
setcookie("token", NULL, -1);
header("Location: /login.php");
?>