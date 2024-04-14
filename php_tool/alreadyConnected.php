<?php
require_once dirname(__FILE__).'/db.php';

function isConnected() {
    global $db;
    if (isset($_SESSION['token']) && isset($_SESSION['id'])) {
        $req = $db->prepare("SELECT id FROM users WHERE token = ?");
        $req->execute(array($_SESSION['token']));
        $account = $req->fetch();
        if ($account['id'] == $_SESSION['id']) {
            return true;
        }
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