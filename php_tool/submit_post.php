<?php
require_once dirname(__FILE__).'/alreadyConnected.php';
session_start_secure();

require_once dirname(__FILE__).'/db.php';

function sendPost($post, $parentId = null) {
    global $db;
    if ($parentId !== null) {
        $req = $db->prepare("INSERT INTO posts (id_user, content, id_parent) VALUES (?, ?, ?)");
        $req->execute(array($_SESSION['id'], $post, $parentId));
    } else {
        $req = $db->prepare("INSERT INTO posts (id_user, content) VALUES (?, ?)");
        $req->execute(array($_SESSION['id'], $post));
    }
}

if (isset($_POST['textAreaPostId'])) {
    $post = SecurizeString_ForSQL($_POST['textAreaPostId']);
    
    if (!empty($post)) {
        if (isset($_POST['id_parent']) && !empty($_POST['id_parent'])) {
            $parentId = SecurizeString_ForSQL($_POST['id_parent']);
        } else {
            $parentId = null;
        }
        sendPost($post, $parentId);
    } else {
        $error = "Le message est vide";
    }
}

?>