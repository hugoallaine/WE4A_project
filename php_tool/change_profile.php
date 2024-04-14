<?php
session_start();
require_once dirname(__FILE__).'/db.php';

if (isset($_POST['pseudo-f'])) {
    $pseudo = SecurizeString_ForSQL($_POST['pseudo-f']);
    if (!empty($pseudo)) {
        if ($pseudo != $_SESSION['pseudo']) {
            if (strlen($pseudo) <= 32) {
                $req = $db->prepare("UPDATE users SET pseudo = ? WHERE id = ?");
                $req->execute(array($pseudo, $_SESSION['id']));
                $_SESSION['pseudo'] = $pseudo;
                $newpseudo = true;
            } else {
                $error = "Le pseudo ne doit pas dépasser 32 caractères.";
            }
        }
    } else {
        $error = "Le pseudo ne peut pas être vide.";
    }
}

if (isset($_POST['bio-f'])) {
    $bio = SecurizeString_ForSQL($_POST['bio-f']);
    if (empty($bio)) {
        $bio = null;
    }
    $req = $db->prepare("SELECT bio FROM users WHERE id = ?");
    $req->execute(array($_SESSION['id']));
    $oldbio = $req->fetch();
    if ($oldbio['bio'] != $bio) {
        if (strlen($bio) <= 128) {
            $req = $db->prepare("UPDATE users SET bio = ? WHERE id = ?");
            $req->execute(array($bio, $_SESSION['id']));
        } else {
            $error = "La bio ne doit pas dépasser 128 caractères.";
        }
    }
}

if (isset($_FILES['avatar-f']) && $_FILES['avatar-f']['error'] === UPLOAD_ERR_OK) {
    if ($_FILES['avatar-f']['size'] <= 2097152) {
        $filename = $_FILES['avatar-f']['name'];
        $file_extension = pathinfo($filename, PATHINFO_EXTENSION);
        $newfilename = "avatar.".$file_extension;
        $tmp_name = $_FILES['avatar-f']['tmp_name'];
        $upload_directory = '../img/user/'.$_SESSION['id'].'/';
        if (!file_exists($upload_directory)) {
            mkdir($upload_directory, 0777, true);
        }
        $path = $upload_directory.$newfilename;
        $req = $db->prepare("SELECT avatar FROM users WHERE id = ?");
        $req->execute(array($_SESSION['id']));
        $oldfilename = $req->fetch();
        if (!empty($oldfilename['avatar'])) {
            unlink($upload_directory.$oldfilename['avatar']);
        }
        move_uploaded_file($tmp_name, $path);
        $req = $db->prepare("UPDATE users SET avatar = ? WHERE id = ?");
        $req->execute(array($newfilename, $_SESSION['id']));
        $_SESSION['avatar'] = $newfilename;
    } else {
        $error = "L'avatar ne doit pas dépasser 2 Mo.";
    }
}

if (isset($_FILES['banner-f']) && $_FILES['banner-f']['error'] === UPLOAD_ERR_OK) {
    if ($_FILES['banner-f']['size'] <= 10485760) {
        $filename = $_FILES['banner-f']['name'];
        $file_extension = pathinfo($filename, PATHINFO_EXTENSION);
        $newfilename = "banner.".$file_extension;
        $tmp_name = $_FILES['banner-f']['tmp_name'];
        $upload_directory = '../img/user/'.$_SESSION['id'].'/';
        if (!file_exists($upload_directory)) {
            mkdir($upload_directory, 0777, true);
        }
        $path = $upload_directory.$newfilename;
        $req = $db->prepare("SELECT banner FROM users WHERE id = ?");
        $req->execute(array($_SESSION['id']));
        $oldfilename = $req->fetch();
        if (!empty($oldfilename['banner'])) {
            unlink($upload_directory.$oldfilename['banner']);
        }
        move_uploaded_file($tmp_name, $path);
        $req = $db->prepare("UPDATE users SET banner = ? WHERE id = ?");
        $req->execute(array($newfilename, $_SESSION['id']));
    } else {
        $error = "La bannière ne doit pas dépasser 10 Mo.";
    }
}

if (isset($error)) {
    header('Content-Type: application/json');
    echo json_encode(array('error' => true,'message' => $error));
}
if (isset($newpseudo) && $newpseudo == true) {
    header('Content-Type: application/json');
    echo json_encode(array('changedpseudo' => true, 'pseudo' => $pseudo));
}

?>