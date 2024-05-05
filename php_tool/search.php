<?php
require_once dirname(__FILE__).'/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['q']) && !empty($_GET['q'])) {
        $q = SecurizeString_ForSQL(urldecode($_GET['q']));
        $reqUsers = $db->prepare("SELECT id,pseudo FROM users WHERE pseudo LIKE ?");
        $reqUsers->execute(array('%'.$q.'%'));
        $resUsers = $reqUsers->fetchAll();
        $reqPosts = $db->prepare("SELECT id,content FROM posts WHERE content LIKE ?");
        $reqPosts->execute(array('%'.$q.'%'));
        $resPosts = $reqPosts->fetchAll();
        $users = array();
        $posts = array();
        foreach ($resUsers as $user) {
            $users[] = array('id' => $user['id'], 'pseudo' => $user['pseudo']);
        }
        foreach ($resPosts as $post) {
            $posts[] = array('id' => $post['id'], 'content' => $post['content']);
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