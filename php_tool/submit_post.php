<?php
require_once dirname(__FILE__).'/alreadyConnected.php';
session_start_secure();

require_once dirname(__FILE__).'/db.php';

function sendPost($post, $parentId) {
    global $db;
    $req = $db->prepare("INSERT INTO posts (id_user, content, id_parent) VALUES (?, ?, ?)");
    $req->execute(array($_SESSION['id'], $post, $parentId));
}

if (isset($_POST['textAreaPostId'])) {
    $post = SecurizeString_ForSQL($_POST['textAreaPostId']);
    $parentId = isset($_POST['id_parent']) ? SecurizeString_ForSQL($_POST['id_parent']) : NULL;
    if (!empty($post)) {
        sendPost($post, $parentId);
    } else {
        $error = "Le message est vide";
    }
}
?>