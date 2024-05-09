<?php
require_once dirname(__FILE__).'/db.php';

/**
 * Start a session if it's not already started
 */
function session_start_secure() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
}

session_start_secure();

/**
 * Check if the token is valid for the user
 * @return bool
 */
function isConnected() {
    global $db;
    if (isset($_SESSION['token']) && isset($_SESSION['id'])) {
        if (checkToken($_SESSION['token'], $_SESSION['id'])) {
            return true;
        }
    }
    return false;
}

/**
 * Check if the user is an administrator
 * @return bool
 */
function isAdmin() {
    global $db;
    if (isset($_SESSION['token']) && isset($_SESSION['id'])) {
        $req = $db->prepare("SELECT isAdmin FROM users WHERE id = ?");
        $req->execute(array($_SESSION['id']));
        $isAdmin = $req->fetch();
        if ($isAdmin['isAdmin'] == 1 && $isAdmin['isAdmin'] == $_SESSION['isAdmin']) {
            return true;
        }
    }
    return false;
}

/**
 * Redirect the user to a page if he is connected
 * @param string $url (default: profile.php)
 */
function redirectIfConnected($url = "profile.php") {
    if (isConnected()) {
        header("Location: $url");
    }
}

/**
 * Redirect the user to a page if he is not connected
 * @param string $url (default: index.php)
 */
function redirectIfNotConnected($url = "index.php") {
    if (!isConnected()) {
        header("Location: $url");
    }
}

/**
 * Check if the tfa is enabled for the user
 * @return bool
 */
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