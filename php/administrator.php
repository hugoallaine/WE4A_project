<?php
require_once dirname(__FILE__).'/alreadyConnected.php';
session_start_secure();
require_once dirname(__FILE__).'/db.php';

/**
 * API to manage the administrator actions
 * 
 *  Response:
 * - error (boolean): true if an error occured
 * - message (string): the error message
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isConnected() && isAdmin()) {
        if (isset($_POST['notifMessage']) && !empty($_POST['notifMessage']) && isset($_POST['adminPostControlId']) && !empty($_POST['adminPostControlId']) && isset($_POST['adminActionType']) && !empty($_POST['adminActionType'])) {
            $content = SecurizeString_ForSQL($_POST['notifMessage']);
            $postId = SecurizeString_ForSQL($_POST['adminPostControlId']);
            $actionType = SecurizeString_ForSQL($_POST['adminActionType']);
            $req = $db->prepare('SELECT id_user FROM posts WHERE id = ?');
            $req->execute(array($postId));
            $userId = $req->fetch()['id_user'];
            if ($actionType == 'shock') {
                $req = $db->prepare('UPDATE posts SET isSensible = 1 WHERE id = ?');
                $req->execute(array($postId));
            } else if ($actionType == 'delete') {
                $req = $db->prepare('UPDATE posts SET isRemoved = 1 WHERE id = ?');
                $req->execute(array($postId));
            } else if ($actionType == 'ban') {
                if (isset($_POST['banDef'])) {
                    $req = $db->prepare('UPDATE users SET isBan = 1 WHERE id = ?');
                    $req->execute(array($userId));
                } else if (isset($_POST['banTime']) && !empty($_POST['banTime'])) {
                    $banTime = SecurizeString_ForSQL($_POST['banTime']);
                    $req = $db->prepare('UPDATE users SET isBan = 1, ban_time = ? WHERE id = ?');
                    $req->execute(array($banTime, $userId));
                } else {
                    header('application/json');
                    echo json_encode(array('error' => true, 'message' => 'Merci de renseigner une durée de bannissement'));
                    exit();
                }
            }
            $req = $db->prepare('INSERT INTO notifications (user_id, type, content, id_post) VALUES (?, ?, ?, ?)');
            $req->execute(array($userId, $actionType, $content, $postId));
            header('application/json');
            echo json_encode(array('error' => false));
        } else {
            header('application/json');
            echo json_encode(array('error' => true, 'message' => 'Invalid parameters'));
        }
    } else {
        header('application/json');
        echo json_encode(array('error' => true, 'message' => 'You are not connected or you are not an admin'));
    }
} else {
    header('application/json');
    echo json_encode(array('error' => true, 'message' => 'Invalid request method'));
}
?>