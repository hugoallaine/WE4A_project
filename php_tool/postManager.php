<?php
require_once dirname(__FILE__).'/db.php';
require_once dirname(__FILE__).'/alreadyConnected.php';
session_start_secure();

function echoPost($post) {
    global $db;
    $date = date_create_from_format('Y-m-d H:i:s', $post['created_at']);
    $formatted_date = $date->format('d/m/Y H:i:s');
    $like_image = !is_null($post['like_id']) ? "/WE4A_project/img/icon/liked.png" : "/WE4A_project/img/icon/like.png";
    $avatar = !empty($post['avatar']) ? "/WE4A_project/img/user/".$post['id_user'].'/'.$post['avatar'] : "/WE4A_project/img/icon/utilisateur.png";


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
    
    if (isset($_SESSION['id'])) {
        $req = $db->prepare("SELECT posts.*, users.pseudo, users.avatar, likes.id as like_id
        FROM posts
        INNER JOIN users ON posts.id_user = users.id
        LEFT JOIN likes ON posts.id = likes.id_post AND likes.id_user = ?
        WHERE posts.id = ?");
        $req->execute([$_SESSION['id'], $postId]);
        $post = $req->fetch();
    } else {
        $req = $db->prepare("SELECT posts.*, users.pseudo, users.avatar, NULL as like_id
        FROM posts
        INNER JOIN users ON posts.id_user = users.id
        WHERE posts.id = ?");
        $req->execute([$postId]);
        $post = $req->fetch();
    }
    $post['content'] = RestoreString_FromSQL($post['content']);
    

    echo json_encode(echoPost($post));
}


function echoResponses($postId) {
    global $db;

    if (isset($_SESSION['id'])) {
        $req = $db->prepare("SELECT posts.*, users.pseudo, users.avatar, likes.id as like_id 
        FROM posts 
        INNER JOIN users ON posts.id_user = users.id 
        LEFT JOIN likes ON posts.id = likes.id_post AND likes.id_user = ? 
        WHERE posts.id_parent = ?");
        $req->execute([$_SESSION['id'], $postId]);
        $responses = $req->fetchAll();
    } else {
        $req = $db->prepare("SELECT posts.*, users.pseudo, users.avatar, NULL as like_id 
        FROM posts 
        INNER JOIN users ON posts.id_user = users.id 
        WHERE posts.id_parent = ?");
        $req->execute([$postId]);
        $responses = $req->fetchAll();
    }

    $listResponses = array();
    foreach ($responses as $response) {
        $response['content'] = RestoreString_FromSQL($response['content']);
        $listResponses[] = echoPost($response);
    }

    echo json_encode($listResponses);
}

function echoListRandomPosts($start, $token){
    global $db;
    $req = $db->prepare("SELECT posts.*, users.pseudo, users.avatar, likes.id as like_id 
    FROM posts 
    INNER JOIN users ON posts.id_user = users.id 
    LEFT JOIN likes ON posts.id = likes.id_post AND likes.id_user = :id 
    ORDER BY RAND(:seed)
    LIMIT 10 OFFSET :offset");
    $req->bindValue(':id', $_SESSION['id'], PDO::PARAM_INT);
    $req->bindValue(':seed', $token);
    $req->bindValue(':offset', $start, PDO::PARAM_INT);
    $req->execute();
    $posts = $req->fetchAll();

    $listPosts = array();
    foreach ($posts as $post) {
        $post['content'] = RestoreString_FromSQL($post['content']);
        $listPosts[] = echoPost($post);
    }

    echo json_encode($listPosts);
}


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['echoResponses'])) {
        if (isset($_GET['postId'])) {
            echoResponses($_GET['postId']);
        }
    }
    if (isset($_GET['echoPostById'])) {
        if (isset($_GET['postId'])) {
            echoPostById($_GET['postId']);
        }
    }
    if (isset($_GET['echoListRandomPosts'])) {
        if (isset($_GET['start'])) {
            echoListRandomPosts($_GET['start'], $_GET['token']);
        }
    }
}
?>
