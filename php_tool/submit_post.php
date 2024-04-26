<?php
require_once dirname(__FILE__).'/alreadyConnected.php';
session_start_secure();

require_once dirname(__FILE__).'/db.php';

function sendPost($post, $parentId = null) {
    global $db;
    if ($parentId !== null) {
        $req = $db->prepare("INSERT INTO posts (id_user, content, id_parent) VALUES (?, ?, ?)");
        $req->execute(array($_SESSION['id'], $post, $parentId));
    } else {
        $req = $db->prepare("INSERT INTO posts (id_user, content) VALUES (?, ?)");
        $req->execute(array($_SESSION['id'], $post));
    }
}

if (isset($_POST['textAreaPostId']) && isset($_FILES['images'])) {
    $post = SecurizeString_ForSQL($_POST['textAreaPostId']);
    
    if (!empty($post)) {
        if (isset($_POST['id_parent']) && !empty($_POST['id_parent'])) {
            $parentId = SecurizeString_ForSQL($_POST['id_parent']);
        } else {
            $parentId = null;
        }
        sendPost($post, $parentId);
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
                $error = "Votre image doit être au format jpg, jpeg, png ou gif et ne doit pas dépasser 2 Mo.";
            }
        }
    }
}

?>