<?php
require_once dirname(__FILE__).'/alreadyConnected.php';
session_start_secure();
require_once dirname(__FILE__).'/db.php';

if (isset($_POST['pseudo']) && isset($_SESSION['id'])) {
    $pseudo = SecurizeString_ForSQL($_POST['pseudo']);
    $req = $db->prepare('SELECT id FROM users WHERE pseudo = ?');
    $req->execute(array($pseudo));
    if ($req->rowCount() > 0) {
        $userinfo = $req->fetch();
        $req = $db->prepare('SELECT Count(*) AS follow FROM follows WHERE id_user_following = ? AND id_user_followed = ?');
        $req->execute(array($_SESSION['id'], $userinfo['id']));
        $follow = $req->fetch();
        if($follow['follow'] == 0) {
            $req = $db->prepare('INSERT INTO follows (id_user_following, id_user_followed) VALUES (?, ?)');
            $req->execute(array($_SESSION['id'], $userinfo['id']));
            header('Content-Type: application/json');
            echo json_encode(array('error' => false, 'message' => 'followed'));
        } else {
            $req = $db->prepare('DELETE FROM follows WHERE id_user_following = ? AND id_user_followed = ?');
            $req->execute(array($_SESSION['id'], $userinfo['id']));
            header('Content-Type: application/json');
            echo json_encode(array('error' => false, 'message' => 'unfollowed'));
        }
    }
}

if (isset($_POST['id']) && isset($_SESSION['id'])) {
    $id = SecurizeString_ForSQL($_POST['id']);
    $req = $db->prepare('SELECT Count(*) AS follow FROM follows WHERE id_user_following = ? AND id_user_followed = ?');
    $req->execute(array($_SESSION['id'], $id));
    $follow = $req->fetch();
    if ($follow['follow'] == 1) {
        $req = $db->prepare('DELETE FROM follows WHERE id_user_following = ? AND id_user_followed = ?');
        $req->execute(array($_SESSION['id'], $id));
        header('Content-Type: application/json');
        echo json_encode(array('error' => false, 'message' => 'unfollowed'));
    } else {
        $req = $db->prepare('INSERT INTO follows (id_user_following, id_user_followed) VALUES (?, ?)');
        $req->execute(array($_SESSION['id'], $id));
        header('Content-Type: application/json');
        echo json_encode(array('error' => false, 'message' => 'followed'));
    }
}
?>