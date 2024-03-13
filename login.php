<?php
session_start();
require('./db.php');

if(isset($_SESSION['id'])){
    header('Location: index.php'); # changer la localisation de la page après
}

if(isset($_POST['form'])){
    $user = $_POST['user'];
    $password = $_POST['password'];
    if($user == 'admin' && $password == 'admin'){
        $_SESSION['user'] = $user;
        header('Location: index.php');
    }else{
        echo 'Usuario o contraseña incorrectos';
    }
}else{
    echo 'Debe ingresar usuario y contraseña';
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <title>Page Title</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel='stylesheet' type='text/css' href='css/login.css'>
</head>
<body>
    <h1>Login</h1>

</body>
</html>