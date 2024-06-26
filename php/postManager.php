<?php
require_once dirname(__FILE__).'/db.php';
require_once dirname(__FILE__).'/alreadyConnected.php';
require_once dirname(__FILE__).'/parser.php';
session_start_secure();

/**
 * Function to echo a post
 * @param array $post: the post
 * @return array
 */
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
        'isBan' => $post['isBan'],
        'content' => parsePseudoForProfile($post['content']),
        'date' => $formatted_date,
        'like_image' => $like_image,
        'picture' => $picture,
        'like_count' => $post['like_count'],
        'comment_count' => $post['comment_count'],
        'is_sensible' => $post['isSensible'],
        'is_removed' => $post['isRemoved']
    ];

    return $postInfo;
}

/**
 * Function to echo a post by its id
 * @param int $postId: the id of the post
 */
function echoPostById($postId) {
    global $db;
    
    $sql = "SELECT p.*, users.pseudo, users.avatar, users.isAdmin, users.isBan, ";
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

/**
 * Function to echo the responses of a post
 * @param int $postId: the id of the post
 */
function echoResponses($postId) {
    global $db;

    // Récupérer le post original
    $sql = "SELECT p.*, users.pseudo, users.avatar, users.isAdmin, users.isBan, ";
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
    $sql = "SELECT p.*, users.pseudo, users.avatar, users.isAdmin, users.isBan, ";
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

/**
 * Function to echo the posts
 * @param int $start: the start index
 * @param string $condition: the condition
 * @param string $order: the order
 * @param array $params: the parameters
 */
function echoPosts($start, $condition, $order, $params = []){
    global $db;

    $sql = "SELECT p.*, users.pseudo, users.avatar, users.isAdmin, users.isBan, ";
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

/**
 * Function to echo the latest posts
 */
function echoLatestPosts($start){
    echoPosts($start, "WHERE p.id_parent IS NULL ", "p.created_at DESC");
}

/**
 * Function to echo the popular posts
 */
function echoPopularPosts($start){
    echoPosts($start, "WHERE p.id_parent IS NULL ", "like_count DESC");
}

/**
 * Function to echo the list of random posts
 */
function echoListRandomPosts($start, $token){
    echoPosts($start, "WHERE p.id_parent IS NULL ", "RAND(:seed)", [':seed' => $token]);
}

/**
 * Function to echo the followed posts
 */
function echoFollowedPosts($start){
    echoPosts($start, "INNER JOIN follows ON p.id_user = follows.id_user_followed WHERE p.id_parent IS NULL AND follows.id_user_following = :id ", "p.created_at DESC", [':id' => $_SESSION['id']]);
}

/**
 * Function to echo the profile posts
 * @param int $start: the start index
 * @param string $condition: the condition
 */
function echoProfilePosts($start, $condition){
    global $db;
    $sql = "SELECT p.*, users.pseudo, users.avatar, users.isAdmin, users.isBan, ";
    $sql .= isset($_SESSION['id']) ? "likes.id as like_id, " : "NULL as like_id, ";
    $sql .= "(SELECT COUNT(*) FROM posts WHERE posts.id_parent = p.id) as comment_count, ";
    $sql .= "(SELECT COUNT(*) FROM likes WHERE likes.id_post = p.id) as like_count ";
    $sql .= "FROM posts p ";
    $sql .= "INNER JOIN users ON p.id_user = users.id ";
    $sql .= isset($_SESSION['id']) ? "LEFT JOIN likes ON p.id = likes.id_post AND likes.id_user = :id " : "";
    if ($condition === "likes.id_user = :userIdOfProfileViewed") {
        $sql .= "WHERE (p.id_user = :userIdOfProfileViewed OR likes.id_user = :userIdOfProfileViewed) AND $condition ";
    }
    else {
        $sql .= "WHERE p.id_user = :userIdOfProfileViewed AND $condition ";
    }
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

/**
 * Function to echo the profile posts
 */
function echoProfileAllGreg($start){
    echoProfilePosts($start, "p.id_parent IS NULL");
}

/**
 * Function to echo the profile posts
 */
function echoProfileAllResponse($start){
    echoProfilePosts($start, "p.id_parent IS NOT NULL");
}

/**
 * Function to echo the profile posts
 */
function echoProfileAllLikes($start){
    echoProfilePosts($start, "likes.id_user = :userIdOfProfileViewed");
}

/**
 * Function to send a post
 */
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

/**
 * API to manage the posts
 * 
 * GET Parameters:
 * - command (string): the command to execute
 * - start (int): the start index
 * - token (string): the token
 * - postId (int): the id of the post
 *
 * POST Parameters:
 * - textAreaPostId (string): the content of the post
 * 
 * Response:
 * - array: the posts
 * - error (string): the error message
 * - message (string): the message
 */
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