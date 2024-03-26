<?php
require_once dirname(__FILE__).'/db.php';

function sendPost($post) {
    global $db;
    $req = $db->prepare("INSERT INTO posts (id_user, content) VALUES (?, ?)");
    $req->execute(array(1, $post));
}

if (isset($_POST['postSubmit'])) {
    $post = SecurizeString_ForSQL($_POST['post']);
    if (!empty($post)) {
        sendPost($post);
    } else {
        $error = "Le message ne peut pas être vide.";
    }
}
?>