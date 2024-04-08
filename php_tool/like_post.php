<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
error_reporting(E_ERROR | E_PARSE);

require_once dirname(__FILE__).'/db.php';

if (isset($_POST['post_id'])) {
    $postId = SecurizeString_ForSQL($_POST['post_id']);
    $userId = SecurizeString_ForSQL($_SESSION['id']);

    $req = $db->prepare("SELECT * FROM `likes` WHERE id_user = ? AND id_post = ?");
    $req->execute(array($userId, $postId));
    $like = $req->fetch();
    if ($like[""] == "") {
        $req = $db->prepare("INSERT INTO likes (id_user, id_post) VALUES (?, ?)");
        $req->execute(array($userId, $postId));
    } else {
        $req = $db->prepare("DELETE FROM likes WHERE id_user = ? AND id_post = ?");
        $req->execute(array($userId, $postId));
    }
}
?>