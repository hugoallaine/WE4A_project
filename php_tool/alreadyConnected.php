<?php
require_once dirname(__FILE__).'/db.php';

function isConnected() {
    if (isset($_SESSION['id'])){
        return true;
    }
    return false;
}

function redirectIfConnected($url = "index.php") {
    if (isConnected()) {
        header("Location: $url");
    }
}

function redirectIfNotConnected($url = "login.php") {
    if (!isConnected()) {
        header("Location: $url");
    }
}
?>