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
        'id_parent' => $post['id_parent'],
        'pseudo' => $post['pseudo'],
        'avatar' => $avatar,
        'content' => $post['content'],
        'date' => $formatted_date,
        'like_image' => $like_image,
        'picture' => $picture,
        'like_count' => $post['like_count'],
        'comment_count' => $post['comment_count']
    ];
    
    return $postInfo;
}

function echoPostById($postId) {
    global $db;
    
    if (isset($_SESSION['id'])) {
        $req = $db->prepare("SELECT p.*, users.pseudo, users.avatar, likes.id as like_id,
        (SELECT COUNT(*) FROM posts WHERE posts.id_parent = p.id) as comment_count,
        (SELECT COUNT(*) FROM likes WHERE likes.id_post = p.id) as like_count
        FROM posts p
        INNER JOIN users ON p.id_user = users.id
        LEFT JOIN likes ON p.id = likes.id_post AND likes.id_user = ?
        WHERE p.id = ?");
        $req->execute([$_SESSION['id'], $postId]);
        $post = $req->fetch();
    } else {
        $req = $db->prepare("SELECT p.*, users.pseudo, users.avatar, NULL as like_id,
        (SELECT COUNT(*) FROM posts WHERE posts.id_parent = p.id) as comment_count,
        (SELECT COUNT(*) FROM likes WHERE likes.id_post = p.id) as like_count
        FROM posts p
        INNER JOIN users ON p.id_user = users.id
        WHERE p.id = ?");
        $req->execute([$postId]);
        $post = $req->fetch();
    }
    $post['content'] = RestoreString_FromSQL($post['content']);
    

    echo json_encode(echoPost($post));
}


function echoResponses($postId) {
    global $db;

    // Récupérer le post original
    $req = $db->prepare("SELECT p.*, users.pseudo, users.avatar, likes.id as like_id,
    (SELECT COUNT(*) FROM posts WHERE posts.id_parent = p.id) as comment_count,
    (SELECT COUNT(*) FROM likes WHERE likes.id_post = p.id) as like_count
    FROM posts p
    INNER JOIN users ON p.id_user = users.id 
    LEFT JOIN likes ON p.id = likes.id_post AND likes.id_user = ? 
    WHERE p.id = ?");
    $req->execute([$_SESSION['id'], $postId]);
    $originalPost = $req->fetch();
    $originalPost['content'] = RestoreString_FromSQL($originalPost['content']);

    // Récupérer les réponses
    if (isset($_SESSION['id'])) {
        $req = $db->prepare("SELECT p.*, users.pseudo, users.avatar, likes.id as like_id,
        (SELECT COUNT(*) FROM posts WHERE posts.id_parent = p.id) as comment_count,
        (SELECT COUNT(*) FROM likes WHERE likes.id_post = p.id) as like_count
        FROM posts p
        INNER JOIN users ON p.id_user = users.id 
        LEFT JOIN likes ON p.id = likes.id_post AND likes.id_user = ? 
        WHERE p.id_parent = ?");
        $req->execute([$_SESSION['id'], $postId]);
        $responses = $req->fetchAll();
    } else {
        $req = $db->prepare("SELECT p.*, users.pseudo, users.avatar, NULL as like_id,
        (SELECT COUNT(*) FROM posts WHERE posts.id_parent = p.id) as comment_count,
        (SELECT COUNT(*) FROM likes WHERE likes.id_post = p.id) as like_count
        FROM posts p
        INNER JOIN users ON p.id_user = users.id 
        WHERE p.id_parent = ?");
        $req->execute([$postId]);
        $responses = $req->fetchAll();
    }

    $listResponses = array();
    foreach ($responses as $response) {
        $response['content'] = RestoreString_FromSQL($response['content']);
        $listResponses[] = echoPost($response);
    }

    // Ajouter le post original au début de la liste
    array_unshift($listResponses, echoPost($originalPost));

    echo json_encode($listResponses);
}

function echoLatestPosts($start){
    global $db;
    $req = $db->prepare("SELECT p.*, users.pseudo, users.avatar, likes.id as like_id,
    (SELECT COUNT(*) FROM posts WHERE posts.id_parent = p.id) as comment_count,
    (SELECT COUNT(*) FROM likes WHERE likes.id_post = p.id) as like_count
    FROM posts p
    INNER JOIN users ON p.id_user = users.id 
    LEFT JOIN likes ON p.id = likes.id_post AND likes.id_user = :id 
    WHERE p.id_parent IS NULL
    ORDER BY p.created_at DESC
    LIMIT 10 OFFSET :offset");
    $req->bindValue(':id', $_SESSION['id'], PDO::PARAM_INT);
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

function echoPopularPosts($start){
    global $db;
    $req = $db->prepare("SELECT p.*, users.pseudo, users.avatar, likes.id as like_id,
    (SELECT COUNT(*) FROM posts WHERE posts.id_parent = p.id) as comment_count,
    (SELECT COUNT(*) FROM likes WHERE likes.id_post = p.id) as like_count
    FROM posts p
    INNER JOIN users ON p.id_user = users.id 
    LEFT JOIN likes ON p.id = likes.id_post AND likes.id_user = :id 
    WHERE p.id_parent IS NULL
    ORDER BY like_count DESC
    LIMIT 10 OFFSET :offset");
    $req->bindValue(':id', $_SESSION['id'], PDO::PARAM_INT);
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

function echoListRandomPosts($start, $token){
    global $db;
    $req = $db->prepare("SELECT p.*, users.pseudo, users.avatar, likes.id as like_id,
    (SELECT COUNT(*) FROM posts WHERE posts.id_parent = p.id) as comment_count,
    (SELECT COUNT(*) FROM likes WHERE likes.id_post = p.id) as like_count
    FROM posts p
    INNER JOIN users ON p.id_user = users.id 
    LEFT JOIN likes ON p.id = likes.id_post AND likes.id_user = :id 
    WHERE p.id_parent IS NULL
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

function echoFollowedPosts($start){
    global $db;
    $req = $db->prepare("SELECT p.*, users.pseudo, users.avatar, likes.id as like_id,
    (SELECT COUNT(*) FROM posts WHERE posts.id_parent = p.id) as comment_count,
    (SELECT COUNT(*) FROM likes WHERE likes.id_post = p.id) as like_count
    FROM posts p
    INNER JOIN users ON p.id_user = users.id 
    INNER JOIN follows ON p.id_user = follows.id_user_followed
    LEFT JOIN likes ON p.id = likes.id_post AND likes.id_user = :id 
    WHERE p.id_parent IS NULL AND follows.id_user_following = :id
    ORDER BY p.created_at DESC
    LIMIT 10 OFFSET :offset");
    $req->bindValue(':id', $_SESSION['id'], PDO::PARAM_INT);
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

function sendPost(){
    $post = SecurizeString_ForSQL($_POST['textAreaPostId']);
        
    if (!empty($post)) {
        if (isset($_POST['id_parent']) && !empty($_POST['id_parent'])) {
            $parentId = SecurizeString_ForSQL($_POST['id_parent']);
        } else {
            $parentId = null;
        }
        global $db;
        if ($parentId !== null) {
            $req = $db->prepare("INSERT INTO posts (id_user, content, id_parent) VALUES (?, ?, ?)");
            $req->execute(array($_SESSION['id'], $post, $parentId));
        } else {
            $req = $db->prepare("INSERT INTO posts (id_user, content) VALUES (?, ?)");
            $req->execute(array($_SESSION['id'], $post));
        }
        $postId = $db->lastInsertId();

    } else {
        $error = "Le message est vide";
    }

    if (isset($_FILES['images']) && $_FILES['images']['error'] === UPLOAD_ERR_OK) {
        if ($_FILES['images']['size'] <= 2097152) {
            $filename = $_FILES['images']['name'];
            $file_extension = pathinfo($filename, PATHINFO_EXTENSION);
            $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif');
            if (in_array($file_extension, $allowed_extensions) === true) {
                $newfilename = "image.".$file_extension;
                $tmp_name = $_FILES['images']['tmp_name'];

                $req = $db->prepare("SELECT id FROM posts WHERE id_user = ? AND created_at = (SELECT MAX(created_at) FROM posts WHERE id_user = ?)");
                $req->execute(array($_SESSION['id'], $_SESSION['id']));
                $post_id = $req->fetch();

                if(!file_exists('../img/user/'.$_SESSION['id'].'/posts/'.$post_id['id'].'/')) {
                    mkdir('../img/user/'.$_SESSION['id'].'/posts/'.$post_id['id'].'/', 0777, true);
                }

                $upload_directory = '../img/user/'.$_SESSION['id'].'/posts/'.$post_id['id'].'/';

                $path = $upload_directory.$newfilename;
                move_uploaded_file($tmp_name, $path);

                $req = $db->prepare("INSERT INTO pictures (id_post, path) VALUES (?, ?)");
                $req->execute(array($post_id['id'], $newfilename));
            } else {
                $error = "Le fichier n'est pas une image";
            }
        } else {
            $error = "Le fichier est trop lourd";
        } 
    }
    echoPostById($postId);
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
    if (isset($_GET['echoLatestPosts'])) {
        if (isset($_GET['start'])) {
            echoLatestPosts($_GET['start']);
        }
    }
    if (isset($_GET['echoPopularPosts'])) {
        if (isset($_GET['start'])) {
            echoPopularPosts($_GET['start']);
        }
    }
    if (isset($_GET['echoFollowedPosts'])) {
        if (isset($_GET['start'])) {
            echoFollowedPosts($_GET['start']);
        }
    }

} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['textAreaPostId']) && isset($_FILES['images'])) {
        sendPost();
    }
}

?>
