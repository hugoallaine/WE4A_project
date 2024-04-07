<?php
session_start();
error_reporting(E_ERROR | E_PARSE);

require_once dirname(__FILE__).'/db.php';

function sendPost($post) {
    global $db;
    $req = $db->prepare("INSERT INTO posts (id_user, content) VALUES (?, ?)");
    $req->execute(array($_SESSION['id'], $post));
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