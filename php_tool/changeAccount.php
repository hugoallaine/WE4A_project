<?php
require_once dirname(__FILE__).'/alreadyConnected.php';
session_start_secure();
require_once dirname(__FILE__).'/db.php';
require_once dirname(__FILE__).'/vendor/autoload.php';
use RobThree\Auth\TwoFactorAuth;
$tfa = new TwoFactorAuth($issuer = 'YGreg');

if (isConnected()) {
    // Change pseudo
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

    // Change bio
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

    // Change avatar
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

    // Change banner
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

    // Change password
    if (isset($_POST['oldPassword']) && isset($_POST['newPassword']) && isset($_POST['newPasswordConfirm'])) {
        $oldPassword = SecurizeString_ForSQL($_POST['oldPassword']);
        $newPassword = SecurizeString_ForSQL($_POST['newPassword']);
        $newPasswordConfirm = SecurizeString_ForSQL($_POST['newPasswordConfirm']);
        $req = $db->prepare('SELECT password FROM users WHERE id = ?');
        $req->execute(array($_SESSION['id']));
        $user = $req->fetch();
        if (password_verify($oldPassword, $user['password'])) {
            if ($newPassword == $newPasswordConfirm) {
                if (strlen($newPassword) >= 12 && preg_match('/[A-Z]/', $newPassword) && preg_match('/[a-z]/', $newPassword) && preg_match('/[0-9]/', $newPassword) && preg_match('/[^a-zA-Z0-9]/', $newPassword)) {
                    $req = $db->prepare('UPDATE users SET password = ? WHERE id = ?');
                    $req->execute(array(password_hash($newPassword, PASSWORD_DEFAULT), $_SESSION['id']));
                } else {
                    $error = 'Le mot de passe doit contenir au moins 12 caractères';
                }
            } else {
                $error = 'Les mots de passe ne correspondent pas';
            }
        } else {
            $error = 'Mot de passe incorrect';
        }
    }

    // Enable 2FA
    if (isset($_POST['tfa_code']) && isset($_POST['password_check_tfa']) && isset($_POST['tfa_secret'])) {
        $tfa_code = SecurizeString_ForSQL($_POST['tfa_code']);
        $tfa_secret = SecurizeString_ForSQL($_POST['tfa_secret']);
        $password_check_tfa = SecurizeString_ForSQL($_POST['password_check_tfa']);
        $req = $db->prepare('SELECT password FROM users WHERE id = ?');
        $req->execute(array($_SESSION['id']));
        $user = $req->fetch();
        if (password_verify($password_check_tfa, $user['password'])) {
            if ($tfa->verifyCode($tfa_secret, $tfa_code)) {
                $req = $db->prepare('UPDATE users SET tfaKey = ? WHERE id = ?');
                $req->execute(array($tfa_secret, $_SESSION['id']));
            } else {
                $error = 'Code invalide';
            }
        } else {
            $error = 'Mot de passe incorrect';
        }
    }

    // Disable 2FA
    if (isset($_POST['password_check'])) {
        $password_check = SecurizeString_ForSQL($_POST['password_check']);
        $req = $db->prepare('SELECT password FROM users WHERE id = ?');
        $req->execute(array($_SESSION['id']));
        $user = $req->fetch();
        if (password_verify($password_check, $user['password'])) {
            $req = $db->prepare('UPDATE users SET tfaKey = NULL WHERE id = ?');
            $req->execute(array($_SESSION['id']));
        } else {
            $error = 'Mot de passe incorrect';
        }
    }

    // Delete account
    if (isset($_POST['password_check_delete'])) {
        $password_check_delete = SecurizeString_ForSQL($_POST['password_check_delete']);
        $req = $db->prepare('SELECT password FROM users WHERE id = ?');
        $req->execute(array($_SESSION['id']));
        $user = $req->fetch();
        if (password_verify($password_check_delete, $user['password'])) {
            if (checkToken($_SESSION['token'], $_SESSION['id'])) {
                $req = $db->prepare('DELETE FROM likes WHERE id_user = ?');
                $req->execute(array($_SESSION['id']));
                $req = $db->prepare('DELETE pictures FROM pictures JOIN posts ON pictures.id_post = posts.id WHERE posts.id_user = ?');
                $req->execute(array($_SESSION['id']));
                $req = $db->prepare('DELETE FROM posts WHERE id_user = ?');
                $req->execute(array($_SESSION['id']));
                $req = $db->prepare('DELETE FROM follows WHERE id_user_following = ? OR id_user_followed = ?');
                $req->execute(array($_SESSION['id'], $_SESSION['id']));
                $req = $db->prepare('DELETE FROM address WHERE id_user = ?');
                $req->execute(array($_SESSION['id']));
                $req = $db->prepare('DELETE FROM emailsnonverifies WHERE id = ?');
                $req->execute(array($_SESSION['id']));
                $req = $db->prepare('DELETE FROM notifications WHERE user_id = ?');
                $req->execute(array($_SESSION['id']));
                $req = $db->prepare('DELETE FROM users WHERE id = ?');
                $req->execute(array($_SESSION['id']));
                $_SESSION = array();
                session_destroy();
            } else {
                $error = 'Session invalide';
            }
        } else {
            $error = 'Mot de passe incorrect';
        }
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