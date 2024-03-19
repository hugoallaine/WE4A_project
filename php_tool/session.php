<?php
session_start();
if (isset($_SESSION['id'])){
    header('Location: index.php'); # changer la destination de la page après
}
?>