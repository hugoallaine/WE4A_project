<?php
require_once dirname(__FILE__).'/db.php';
require_once dirname(__FILE__).'/alreadyConnected.php';
session_start_secure();

function echoPost($post) {
    global $db;
    $date = date_create_from_format('Y-m-d H:i:s', $post['created_at']);
    $formatted_date = $date->format('d/m/Y');
    $like_image = !is_null($post['like_id']) ? "/WE4A_project/img/icon/liked.png" : "/WE4A_project/img/icon/like.png";
    if (!empty($post['avatar'])) {
        $avatar = "/WE4A_project/img/user/".$post['id_user'].'/'.$post['avatar'];
    } else {
        $avatar = "/WE4A_project/img/icon/utilisateur.png";
    }

    $reqPic = $db->prepare("SELECT * FROM pictures WHERE id_post = ?");
    $reqPic->execute([$post['id']]);
    $picture = $reqPic->fetch();

    if($picture) {
        $picture = "/WE4A_project/img/user/".$post['id_user'].'/posts/'.$post['id'].'/'.$picture['path'];
    }

    $postInfo = [
        'id' => $post['id'],
        'id_user' => $post['id_user'],
        'pseudo' => $post['pseudo'],
        'avatar' => $avatar,
        'content' => $post['content'],
        'date' => $formatted_date,
        'like_image' => $like_image,
        'picture' => $picture
    ];
    
    return $postInfo;
}

function echoPostById($postId) {
    global $db;
    $req = $db->prepare("SELECT posts.*, users.pseudo, users.avatar FROM posts INNER JOIN users ON posts.id_user = users.id WHERE posts.id = ?");
    $req->execute([$postId]);
    $post = $req->fetch();
    echoPost($post);
}

function echoResponses($postId) {
    global $db;
    $req = $db->prepare("SELECT posts.*, users.pseudo, users.avatar FROM posts INNER JOIN users ON posts.id_user = users.id WHERE posts.id_parent = ?");
    $req->execute([$postId]);
    $responses = $req->fetchAll();

    $listResponses = array();
    foreach ($responses as $response) {
        $listResponses[] = echoPost($response);
    }
    echo json_encode($listResponses);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['echoResponses'])) {
        if (isset($_GET['postId'])) {
            echoResponses($_GET['postId']);
        }
    }
}
?>
