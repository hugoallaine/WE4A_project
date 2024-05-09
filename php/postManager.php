<?php
require_once dirname(__FILE__).'/db.php';
require_once dirname(__FILE__).'/alreadyConnected.php';
require_once dirname(__FILE__).'/parser.php';
session_start_secure();

function echoPost($post) {
    global $db;

    $date = date_create_from_format('Y-m-d H:i:s', $post['created_at']);
    $formatted_date = $date->format('d/m/Y H:i:s');
    $like_image = !is_null($post['like_id']) ? "img/icon/liked.png" : "img/icon/like.png";
    $avatar = !empty($post['avatar']) ? "img/user/".$post['id_user'].'/'.$post['avatar'] : "img/icon/utilisateur.png";

    $reqPic = $db->prepare("SELECT * FROM pictures WHERE id_post = ?");
    $reqPic->execute([$post['id']]);

    $picture = $reqPic->fetch();
    if($picture) {
        $picture = "img/user/".$post['id_user'].'/posts/'.$post['id'].'/'.$picture['path'];
    }

    $postInfo = [
        'id' => $post['id'],
        'id_user' => $post['id_user'],
        'id_parent' => $post['id_parent'],
        'pseudo' => $post['pseudo'],
        'avatar' => $avatar,
        'isAdmin' => $post['isAdmin'],
        'content' => parsePseudoForProfile($post['content']),
        'date' => $formatted_date,
        'like_image' => $like_image,
        'picture' => $picture,
        'like_count' => $post['like_count'],
        'comment_count' => $post['comment_count'],
        'is_sensible' => $post['isSensible']
    ];
    if ($post['isRemoved'] === 0) {
        return $postInfo;
    }
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

function echoPosts($start, $condition, $order, $params = []){
    global $db;

    $sql = "SELECT p.*, users.pseudo, users.avatar, users.isAdmin, ";
    $sql .= isset($_SESSION['id']) ? "likes.id as like_id, " : "NULL as like_id, ";
    $sql .= "(SELECT COUNT(*) FROM posts WHERE posts.id_parent = p.id) as comment_count, ";
    $sql .= "(SELECT COUNT(*) FROM likes WHERE likes.id_post = p.id) as like_count ";
    $sql .= "FROM posts p ";
    $sql .= "INNER JOIN users ON p.id_user = users.id ";
    $sql .= isset($_SESSION['id']) ? "LEFT JOIN likes ON p.id = likes.id_post AND likes.id_user = :id " : "";
    $sql .= $condition;
    $sql .= "ORDER BY $order ";
    $sql .= "LIMIT 10 OFFSET :offset";

    $req = $db->prepare($sql);

    if (isset($_SESSION['id'])) {
        $req->bindValue(':id', $_SESSION['id'], PDO::PARAM_INT);
    }

    $req->bindValue(':offset', $start, PDO::PARAM_INT);

    foreach ($params as $key => $value) {
        $req->bindValue($key, $value);
    }

    $req->execute();
    $posts = $req->fetchAll();

    $listPosts = array();
    foreach ($posts as $post) {
        $post['content'] = RestoreString_FromSQL($post['content']);
        $postResult = echoPost($post);
        if ($postResult !== null) {
            $listPosts[] = $postResult;
        }
    }

    echo json_encode($listPosts);
}

function echoLatestPosts($start){
    echoPosts($start, "WHERE p.id_parent IS NULL ", "p.created_at DESC");
}

function echoPopularPosts($start){
    echoPosts($start, "WHERE p.id_parent IS NULL ", "like_count DESC");
}

function echoListRandomPosts($start, $token){
    echoPosts($start, "WHERE p.id_parent IS NULL ", "RAND(:seed)", [':seed' => $token]);
}

function echoFollowedPosts($start){
    echoPosts($start, "INNER JOIN follows ON p.id_user = follows.id_user_followed WHERE p.id_parent IS NULL AND follows.id_user_following = :id ", "p.created_at DESC", [':id' => $_SESSION['id']]);
}

function echoProfilePosts($start, $condition){
    global $db;
    $sql = "SELECT p.*, users.pseudo, users.avatar, users.isAdmin, ";
    $sql .= isset($_SESSION['id']) ? "likes.id as like_id, " : "NULL as like_id, ";
    $sql .= "(SELECT COUNT(*) FROM posts WHERE posts.id_parent = p.id) as comment_count, ";
    $sql .= "(SELECT COUNT(*) FROM likes WHERE likes.id_post = p.id) as like_count ";
    $sql .= "FROM posts p ";
    $sql .= "INNER JOIN users ON p.id_user = users.id ";
    $sql .= isset($_SESSION['id']) ? "LEFT JOIN likes ON p.id = likes.id_post AND likes.id_user = :id " : "";
    $sql .= "WHERE p.id_user = :userIdOfProfileViewed AND $condition ";
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

function echoProfileAllGreg($start){
    echoProfilePosts($start, "p.id_parent IS NULL");
}

function echoProfileAllResponse($start){
    echoProfilePosts($start, "p.id_parent IS NOT NULL");
}

function echoProfileAllLikes($start){
    echoProfilePosts($start, "likes.id_user = :userIdOfProfileViewed");
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
    if (isset($_GET['command'])) {
        switch ($_GET['command']) {
            case 'echoLatestPosts':
                echoLatestPosts($_GET['start']);
                break;
            case 'echoPopularPosts':
                echoPopularPosts($_GET['start']);
                break;
            case 'echoRandomPosts':
                echoListRandomPosts($_GET['start'], $_GET['token']);
                break;
            case 'echoFollowedPosts':
                echoFollowedPosts($_GET['start']);
                break;
            case 'echoProfileAllGreg':
                echoProfileAllGreg($_GET['start']);
                break;
            case 'echoProfileAllResponse':
                echoProfileAllResponse($_GET['start']);
                break;
            case 'echoProfileAllLikes':
                echoProfileAllLikes($_GET['start']);
                break;
            case 'echoResponses':
                echoResponses($_GET['postId']);
                break;
            default:
                echo "Commande inconnue : ".$_GET['command'];
                break;
        }
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['textAreaPostId']) && isset($_FILES['images'])) {
        sendPost();
    }
}

?>
