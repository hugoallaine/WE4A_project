<?php
require_once dirname(__FILE__).'/alreadyConnected.php';
session_start_secure();
require_once dirname(__FILE__).'/db.php';

/**
 * API to manage the like actions
 * 
 * Response:
 * - status (string): liked or unliked
 * - likeCount (int): the number of likes
 */
if (isset($_POST['post_id'])) {
    $postId = SecurizeString_ForSQL($_POST['post_id']);
    $userId = SecurizeString_ForSQL($_SESSION['id']);
    $req = $db->prepare("SELECT * FROM `likes` WHERE id_user = ? AND id_post = ?");
    $req->execute(array($userId, $postId));
    $like = $req->fetch();
    if ($like == false) {
        $req = $db->prepare("INSERT INTO likes (id_user, id_post) VALUES (?, ?)");
        $req->execute(array($userId, $postId));
        $status = "liked";
    } else {
        $req = $db->prepare("DELETE FROM likes WHERE id_user = ? AND id_post = ?");
        $req->execute(array($userId, $postId));
        $status = "unliked";
    }
    $req = $db->prepare("SELECT COUNT(*) AS likeCount FROM likes WHERE id_post = ?");
    $req->execute(array($postId));
    $result = $req->fetch();
    $likeCount = $result['likeCount'];
    echo json_encode(array("status" => $status, "likeCount" => $likeCount));
}
?>