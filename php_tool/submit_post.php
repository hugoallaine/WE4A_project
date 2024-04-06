<?php
require_once dirname(__FILE__).'/db.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function sendPost($post) {
    global $db;
    $req = $db->prepare("INSERT INTO posts (id_user, content) VALUES (?, ?)");
    /* Change 2, by $_SESSION['id'] */
    $req->execute(array(6, $post));
}

if (isset($_POST['textAreaPostId'])) {
    $post = SecurizeString_ForSQL($_POST['textAreaPostId']);
    if (!empty($post)) {
        sendPost($post);
    } else {
        $error = "Le message est vide";
    }
}
?>