<?php
require_once dirname(__FILE__).'/db.php';
if (isset($_COOKIE['token']) && isset($_COOKIE['email'])){
    if(checkToken($_COOKIE['token'], $_COOKIE['email'])) {
        header('Location: /index.php'); # changer la destination de la page après
    } else {
        header('Location: /logout.php');
    }
}
?>