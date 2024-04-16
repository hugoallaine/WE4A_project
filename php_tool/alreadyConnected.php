<?php
require_once dirname(__FILE__).'/db.php';

function session_start_secure() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
}

session_start_secure();

function isConnected() {
    global $db;
    if (isset($_SESSION['token']) && isset($_SESSION['id'])) {
        if (checkToken($_SESSION['token'], $_SESSION['id'])) {
            return true;
        }
    }
    return false;
}

function redirectIfConnected($url = "profile.php") {
    if (isConnected()) {
        header("Location: $url");
    }
}

function redirectIfNotConnected($url = "index.php") {
    if (!isConnected()) {
        header("Location: $url");
    }
}

function isTfaEnabled() {
    global $db;
    $req = $db->prepare("SELECT tfaKey FROM users WHERE id = ? AND tfaKey IS NULL");
    $req->execute(array($_SESSION['id']));
    if ($req->rowCount() == 0) {
        return true;
    } else {
        return false;
    }
}
?>