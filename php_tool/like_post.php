<?php
require_once dirname(__FILE__).'/php_tool/alreadyConnected.php';
session_start_secure();

require_once dirname(__FILE__).'/db.php';

if (isset($_POST['post_id'])) {
    $postId = SecurizeString_ForSQL($_POST['post_id']);
    $userId = SecurizeString_ForSQL($_SESSION['id']);

    /* Check if the user has already liked the post */
    $req = $db->prepare("SELECT * FROM `likes` WHERE id_user = ? AND id_post = ?");
    $req->execute(array($userId, $postId));
    $like = $req->fetch();
    if ($like == false) {
        /* If the user has not liked the post, add a like */
        $req = $db->prepare("INSERT INTO likes (id_user, id_post) VALUES (?, ?)");
        $req->execute(array($userId, $postId));
        echo "liked";
    } else {
        /* If the user has already liked the post, remove the like */
        $req = $db->prepare("DELETE FROM likes WHERE id_user = ? AND id_post = ?");
        $req->execute(array($userId, $postId));
        echo "unliked";
    }   
}
?>