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
        'isAdmin' => $post['isAdmin'],
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
    
    $sql = "SELECT p.*, users.pseudo, users.avatar, users.isAdmin, ";
    $sql .= isset($_SESSION['id']) ? "likes.id as like_id, " : "NULL as like_id, ";
    $sql .= "(SELECT COUNT(*) FROM posts WHERE posts.id_parent = p.id) as comment_count, ";
    $sql .= "(SELECT COUNT(*) FROM likes WHERE likes.id_post = p.id) as like_count ";
    $sql .= "FROM posts p ";
    $sql .= "INNER JOIN users ON p.id_user = users.id ";
    $sql .= isset($_SESSION['id']) ? "LEFT JOIN likes ON p.id = likes.id_post AND likes.id_user = ? " : "";
    $sql .= "WHERE p.id = ?";

    $req = $db->prepare($sql);
    $params = isset($_SESSION['id']) ? [$_SESSION['id'], $postId] : [$postId];
    $req->execute($params);
    $post = $req->fetch();

    $post['content'] = RestoreString_FromSQL($post['content']);

    echo json_encode(echoPost($post));
}

function echoResponses($postId) {
    global $db;

    // Récupérer le post original
    $sql = "SELECT p.*, users.pseudo, users.avatar, users.isAdmin, ";
    $sql .= isset($_SESSION['id']) ? "likes.id as like_id, " : "NULL as like_id, ";
    $sql .= "(SELECT COUNT(*) FROM posts WHERE posts.id_parent = p.id) as comment_count, ";
    $sql .= "(SELECT COUNT(*) FROM likes WHERE likes.id_post = p.id) as like_count ";
    $sql .= "FROM posts p ";
    $sql .= "INNER JOIN users ON p.id_user = users.id ";
    $sql .= isset($_SESSION['id']) ? "LEFT JOIN likes ON p.id = likes.id_post AND likes.id_user = ? " : "";
    $sql .= "WHERE p.id = ?";

    $req = $db->prepare($sql);
    $params = isset($_SESSION['id']) ? [$_SESSION['id'], $postId] : [$postId];
    $req->execute($params);
    $originalPost = $req->fetch();
    $originalPost['content'] = RestoreString_FromSQL($originalPost['content']);

    // Récupérer les réponses
    $sql = "SELECT p.*, users.pseudo, users.avatar, users.isAdmin, ";
    $sql .= isset($_SESSION['id']) ? "likes.id as like_id, " : "NULL as like_id, ";
    $sql .= "(SELECT COUNT(*) FROM posts WHERE posts.id_parent = p.id) as comment_count, ";
    $sql .= "(SELECT COUNT(*) FROM likes WHERE likes.id_post = p.id) as like_count ";
    $sql .= "FROM posts p ";
    $sql .= "INNER JOIN users ON p.id_user = users.id ";
    $sql .= isset($_SESSION['id']) ? "LEFT JOIN likes ON p.id = likes.id_post AND likes.id_user = ? " : "";
    $sql .= "WHERE p.id_parent = ?";
    
    $req = $db->prepare($sql);
    $params = isset($_SESSION['id']) ? [$_SESSION['id'], $postId] : [$postId];
    $req->execute($params);
    $responses = $req->fetchAll();

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

    $sql = "SELECT p.*, users.pseudo, users.avatar, users.isAdmin, ";
    $sql .= isset($_SESSION['id']) ? "likes.id as like_id, " : "NULL as like_id, ";
    $sql .= "(SELECT COUNT(*) FROM posts WHERE posts.id_parent = p.id) as comment_count, ";
    $sql .= "(SELECT COUNT(*) FROM likes WHERE likes.id_post = p.id) as like_count ";
    $sql .= "FROM posts p ";
    $sql .= "INNER JOIN users ON p.id_user = users.id ";
    $sql .= isset($_SESSION['id']) ? "LEFT JOIN likes ON p.id = likes.id_post AND likes.id_user = :id " : "";
    $sql .= "WHERE p.id_parent IS NULL ";
    $sql .= "ORDER BY p.created_at DESC ";
    $sql .= "LIMIT 10 OFFSET :offset";

    $req = $db->prepare($sql);

    if (isset($_SESSION['id'])) {
        $req->bindValue(':id', $_SESSION['id'], PDO::PARAM_INT);
    }
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

    $sql = "SELECT p.*, users.pseudo, users.avatar, users.isAdmin, ";
    $sql .= "(SELECT COUNT(*) FROM posts WHERE posts.id_parent = p.id) as comment_count, ";
    $sql .= "(SELECT COUNT(*) FROM likes WHERE likes.id_post = p.id) as like_count, ";
    $sql .= isset($_SESSION['id']) ? "likes.id as like_id " : "NULL as like_id ";
    $sql .= "FROM posts p ";
    $sql .= "INNER JOIN users ON p.id_user = users.id ";
    $sql .= isset($_SESSION['id']) ? "LEFT JOIN likes ON p.id = likes.id_post AND likes.id_user = :id " : "";
    $sql .= "WHERE p.id_parent IS NULL ";
    $sql .= "ORDER BY like_count DESC ";
    $sql .= "LIMIT 10 OFFSET :offset";

    $req = $db->prepare($sql);

    if (isset($_SESSION['id'])) {
        $req->bindValue(':id', $_SESSION['id'], PDO::PARAM_INT);
    }

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

    $sql = "SELECT p.*, users.pseudo, users.avatar, users.isAdmin, ";
    $sql .= isset($_SESSION['id']) ? "likes.id as like_id, " : "NULL as like_id, ";
    $sql .= "(SELECT COUNT(*) FROM posts WHERE posts.id_parent = p.id) as comment_count, ";
    $sql .= "(SELECT COUNT(*) FROM likes WHERE likes.id_post = p.id) as like_count ";
    $sql .= "FROM posts p ";
    $sql .= "INNER JOIN users ON p.id_user = users.id ";
    $sql .= isset($_SESSION['id']) ? "LEFT JOIN likes ON p.id = likes.id_post AND likes.id_user = :id " : "";
    $sql .= "WHERE p.id_parent IS NULL ";
    $sql .= "ORDER BY RAND(:seed) ";
    $sql .= "LIMIT 10 OFFSET :offset";

    $req = $db->prepare($sql);

    if (isset($_SESSION['id'])) {
        $req->bindValue(':id', $_SESSION['id'], PDO::PARAM_INT);
    }

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
    $sql = "SELECT p.*, users.pseudo, users.avatar, users.isAdmin, ";
    $sql .= isset($_SESSION['id']) ? "likes.id as like_id, " : "NULL as like_id, ";
    $sql .= "(SELECT COUNT(*) FROM posts WHERE posts.id_parent = p.id) as comment_count, ";
    $sql .= "(SELECT COUNT(*) FROM likes WHERE likes.id_post = p.id) as like_count ";
    $sql .= "FROM posts p ";
    $sql .= "INNER JOIN users ON p.id_user = users.id ";
    $sql .= "INNER JOIN follows ON p.id_user = follows.id_user_followed ";
    $sql .= isset($_SESSION['id']) ? "LEFT JOIN likes ON p.id = likes.id_post AND likes.id_user = :id " : "";
    $sql .= "WHERE p.id_parent IS NULL AND follows.id_user_following = :id ";
    $sql .= "ORDER BY p.created_at DESC ";
    $sql .= "LIMIT 10 OFFSET :offset";
    
    $req = $db->prepare($sql);
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

function echoProfileAllGreg($start){
    global $db;
    $sql = "SELECT p.*, users.pseudo, users.avatar, users.isAdmin, ";
    $sql .= isset($_SESSION['id']) ? "likes.id as like_id, " : "NULL as like_id, ";
    $sql .= "(SELECT COUNT(*) FROM posts WHERE posts.id_parent = p.id) as comment_count, ";
    $sql .= "(SELECT COUNT(*) FROM likes WHERE likes.id_post = p.id) as like_count ";
    $sql .= "FROM posts p ";
    $sql .= "INNER JOIN users ON p.id_user = users.id ";
    $sql .= isset($_SESSION['id']) ? "LEFT JOIN likes ON p.id = likes.id_post AND likes.id_user = :id " : "";
    $sql .= "WHERE p.id_user = :userIdOfProfileViewed AND p.id_parent IS NULL ";
    $sql .= "ORDER BY p.created_at DESC ";
    $sql .= "LIMIT 5 OFFSET :offset";
    
    $req = $db->prepare($sql);
    
    if (isset($_SESSION['id'])) {
        $req->bindValue(':id', $_SESSION['id'], PDO::PARAM_INT);
    }
    
    $req->bindValue(':offset', $start, PDO::PARAM_INT);
    $req->bindValue(':userIdOfProfileViewed', $_GET['userIdOfProfileViewed'], PDO::PARAM_INT);
    $req->execute();
    $posts = $req->fetchAll();
    
    $listPosts = array();
    foreach ($posts as $post) {
        $post['content'] = RestoreString_FromSQL($post['content']);
        $listPosts[] = echoPost($post);
    }
    
    echo json_encode($listPosts);
}

function echoProfileAllResponse($start){
    global $db;
    $sql = "SELECT p.*, users.pseudo, users.avatar, users.isAdmin, ";
    $sql .= isset($_SESSION['id']) ? "likes.id as like_id, " : "NULL as like_id, ";
    $sql .= "(SELECT COUNT(*) FROM posts WHERE posts.id_parent = p.id) as comment_count, ";
    $sql .= "(SELECT COUNT(*) FROM likes WHERE likes.id_post = p.id) as like_count ";
    $sql .= "FROM posts p ";
    $sql .= "INNER JOIN users ON p.id_user = users.id ";
    $sql .= isset($_SESSION['id']) ? "LEFT JOIN likes ON p.id = likes.id_post AND likes.id_user = :id " : "";
    $sql .= "WHERE p.id_parent IS NOT NULL AND p.id_user = :userIdOfProfileViewed ";
    $sql .= "ORDER BY p.created_at DESC ";
    $sql .= "LIMIT 10 OFFSET :offset";

    $req = $db->prepare($sql);

    if (isset($_SESSION['id'])) {
        $req->bindValue(':id', $_SESSION['id'], PDO::PARAM_INT);
    }

    $req->bindValue(':offset', $start, PDO::PARAM_INT);
    $req->bindValue(':userIdOfProfileViewed', $_GET['userIdOfProfileViewed'], PDO::PARAM_INT);
    $req->execute();
    $posts = $req->fetchAll();

    $listPosts = array();
    foreach ($posts as $post) {
        $post['content'] = RestoreString_FromSQL($post['content']);
        $listPosts[] = echoPost($post);
    }

    echo json_encode($listPosts);
}

function echoProfileAllLikes($start){
    global $db;
    $sql = "SELECT p.*, users.pseudo, users.avatar, users.isAdmin, likes1.id as like_id, ";
    $sql .= "(SELECT COUNT(*) FROM posts WHERE posts.id_parent = p.id) as comment_count, ";
    $sql .= "(SELECT COUNT(*) FROM likes WHERE likes.id_post = p.id) as like_count ";
    $sql .= "FROM posts p ";
    $sql .= "INNER JOIN users ON p.id_user = users.id ";
    $sql .= "INNER JOIN likes as likes1 ON p.id = likes1.id_post ";
    $sql .= isset($_SESSION['id']) ? "LEFT JOIN likes as likes2 ON p.id = likes2.id_post AND likes2.id_user = :id " : "";
    $sql .= "WHERE likes1.id_user = :userIdOfProfileViewed ";
    $sql .= "ORDER BY p.created_at DESC ";
    $sql .= "LIMIT 10 OFFSET :offset";
    
    $req = $db->prepare($sql);
    
    if (isset($_SESSION['id'])) {
        $req->bindValue(':id', $_SESSION['id'], PDO::PARAM_INT);
    }
    
    $req->bindValue(':offset', $start, PDO::PARAM_INT);
    $req->bindValue(':userIdOfProfileViewed', $_GET['userIdOfProfileViewed'], PDO::PARAM_INT);
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
    $start = $_GET['start'] ?? null;
    $postId = $_GET['postId'] ?? null;
    $token = $_GET['token'] ?? null;

    switch (true) {
        case isset($_GET['echoResponses']) && $postId:
            echoResponses($postId);
            break;
        case isset($_GET['echoPostById']) && $postId:
            echoPostById($postId);
            break;
        case isset($_GET['echoListRandomPosts']) && $start:
            echoListRandomPosts($start, $token);
            break;
        case isset($_GET['echoLatestPosts']) && $start:
            echoLatestPosts($start);
            break;
        case isset($_GET['echoPopularPosts']) && $start:
            echoPopularPosts($start);
            break;
        case isset($_GET['echoFollowedPosts']) && $start:
            echoFollowedPosts($start);
            break;
        case isset($_GET['echoProfileAllGreg']) && $start:
            echoProfileAllGreg($start);
            break;
        case isset($_GET['echoProfileAllResponse']) && $start:
            echoProfileAllResponse($start);
            break;
        case isset($_GET['echoProfileAllLikes']) && $start:
            echoProfileAllLikes($start);
            break;
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['textAreaPostId']) && isset($_FILES['images'])) {
        sendPost();
    }
}

?>
