<?php
require_once dirname(__FILE__).'/alreadyConnected.php';
session_start_secure();
require_once dirname(__FILE__).'/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isConnected() && isAdmin()) {
        // Advertissement
        if (isset($_POST['notifMessage']) && !empty($_POST['notifMessage']) && isset($_POST['adminPostControlId']) && !empty($_POST['adminPostControlId']) && isset($_POST['adminActionType']) && !empty($_POST['adminActionType'])) {
            $content = SecurizeString_ForSQL($_POST['notifMessage']);
            $postId = SecurizeString_ForSQL($_POST['adminPostControlId']);
            $actionType = SecurizeString_ForSQL($_POST['adminActionType']);
            $req = $db->prepare('SELECT id_user FROM posts WHERE id = ?');
            $req->execute(array($postId));
            $userId = $req->fetch()['id_user'];
            $req = $db->prepare('INSERT INTO notifications (user_id, type, content, id_post) VALUES (?, ?, ?, ?)');
            $req->execute(array($userId, $actionType, $content, $postId));
            header('application/json');
            echo json_encode(array('error' => 'false'));
        } else {
            header('application/json');
            echo json_encode(array('error' => 'true', 'message' => 'Invalid parameters'));
        }
    } else {
        header('application/json');
        echo json_encode(array('error' => 'true', 'message' => 'You are not connected or you are not an admin'));
    }
} else {
    header('application/json');
    echo json_encode(array('error' => 'true', 'message' => 'Invalid request method'));
}
?>