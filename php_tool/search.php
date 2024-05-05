<?php
require_once dirname(__FILE__).'/db.php';

/**
 * API to search for users and posts
 * 
 * GET parameters:
 * - q: query string
 * 
 * Response:
 * - error: boolean
 * - message: string (if error is true)
 * - query: array of users and posts
 */
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['q']) && !empty($_GET['q'])) {
        $q = SecurizeString_ForSQL(urldecode($_GET['q']));
        $reqUsers = $db->prepare("SELECT id,pseudo,avatar FROM users WHERE pseudo LIKE ?");
        $reqUsers->execute(array('%'.$q.'%'));
        $resUsers = $reqUsers->fetchAll();
        $reqPosts = $db->prepare("SELECT posts.id,users.pseudo AS pseudo,posts.content FROM posts JOIN users ON users.id = posts.id_user WHERE posts.content LIKE ?");
        $reqPosts->execute(array('%'.$q.'%'));
        $resPosts = $reqPosts->fetchAll();
        $users = array();
        $posts = array();
        foreach ($resUsers as $user) {
            $users[] = array('id' => $user['id'], 'pseudo' => $user['pseudo'], 'avatar' => $user['avatar']);
        }
        foreach ($resPosts as $post) {
            $posts[] = array('id' => $post['id'], 'pseudo' => $post['pseudo'], 'content' => RestoreString_FromSQL($post['content']));
        }
        $query = array('users' => $users, 'posts' => $posts);
        header('Content-Type: application/json');
        echo json_encode(array('error' => false, 'query' => $query));
    } else {
        header('Content-Type: application/json');
        echo json_encode(array('error' => true, 'message' => 'Missing query'));
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(array('error' => true, 'message' => 'Invalid request method'));
}

?>