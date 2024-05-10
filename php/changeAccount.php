<?php
require_once dirname(__FILE__).'/alreadyConnected.php';
session_start_secure();
require_once dirname(__FILE__).'/db.php';
require_once dirname(__FILE__).'/mails.php';
require_once dirname(__FILE__).'/vendor/autoload.php';
use RobThree\Auth\TwoFactorAuth;
$tfa = new TwoFactorAuth($issuer = 'YGreg');

/**
 * Function to delete a directory and its content
 */
function rrmdir($dir) { 
    if (is_dir($dir)) { 
        $objects = scandir($dir); 
        foreach ($objects as $object) { 
            if ($object != "." && $object != "..") { 
            if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object); 
            } 
        } 
        reset($objects); 
        rmdir($dir); 
    } 
} 

/**
 * API to change account informations
 * 
 * POST parameters:
 * - Change informations:
 *   - firstname (string): the new firstname
 *   - name (string): the new name
 *   - birthdate (string): the new birthdate
 *   - address (string): the new address
 *   - city (string): the new city
 *   - zipcode (string): the new zipcode
 *   - country (string): the new country
 * - Change pseudo:
 *   - pseudo-f (string): the new pseudo
 * - Change bio:
 *   - bio-f (string): the new bio
 * - Change avatar:
 *   - avatar-f (file): the new avatar
 * - Change banner:
 *   - banner-f (file): the new banner
 * - Change password:
 *   - oldPassword (string): the old password
 *   - newPassword (string): the new password
 *   - newPasswordConfirm (string): the new password confirmation
 * - Enable 2FA:
 *   - tfa_code (string): the 2FA code
 *   - password_check_tfa (string): the password to check 2FA
 *   - tfa_secret (string): the 2FA secret
 * - Disable 2FA:
 *   - password_check (string): the password to check
 * - Delete account:
 *   - password_check_delete (string): the password to delete account
 * 
 * Response:
 * - error (boolean): true if an error occured
 * - message (string): the error message
 * - changedpseudo (boolean): true if the pseudo has been changed
 * - pseudo (string): the new pseudo
 */
if (isConnected()) {
    // Change informations
    if (isset($_POST['firstname']) && isset($_POST['name']) && isset($_POST['birthdate']) && isset($_POST['address']) && isset($_POST['city']) && isset($_POST['zipcode']) && isset($_POST['country'])) {
        $firstname = SecurizeString_ForSQL($_POST['firstname']);
        $name = SecurizeString_ForSQL($_POST['name']);
        $birthdate = SecurizeString_ForSQL($_POST['birthdate']);
        $address = SecurizeString_ForSQL($_POST['address']);
        $city = SecurizeString_ForSQL($_POST['city']);
        $zipcode = SecurizeString_ForSQL($_POST['zipcode']);
        $country = SecurizeString_ForSQL($_POST['country']);
        $req = $db->prepare("SELECT u.name,u.firstname,u.birth_date,a.address,a.city,a.zip_code,a.country FROM users u JOIN address a ON u.id = a.id_user WHERE u.id = ?");
        $req->execute(array($_SESSION['id']));
        $oldinfos = $req->fetch();
        $count = 0;
        if ($oldinfos['firstname'] != $firstname && !empty($firstname) && strlen($firstname) <= 32) {
            $req = $db->prepare("UPDATE users SET firstname = ? WHERE id = ?");
            $req->execute(array($firstname, $_SESSION['id']));
            $count += 1;
        }
        if ($oldinfos['name'] != $name && !empty($name) && strlen($name) <= 32) {
            $req = $db->prepare("UPDATE users SET name = ? WHERE id = ?");
            $req->execute(array($name, $_SESSION['id']));
            $count += 1;
        }
        if ($oldinfos['birth_date'] != $birthdate && !empty($birthdate)) {
            $req = $db->prepare("UPDATE users SET birth_date = ? WHERE id = ?");
            $req->execute(array($birthdate, $_SESSION['id']));
            $count += 1;
        }
        if ($oldinfos['address'] != $address && !empty($address)) {
            $req = $db->prepare("UPDATE address SET address = ? WHERE id_user = ?");
            $req->execute(array($address, $_SESSION['id']));
            $count += 1;
        }
        if ($oldinfos['city'] != $city && !empty($city)) {
            $req = $db->prepare("UPDATE address SET city = ? WHERE id_user = ?");
            $req->execute(array($city, $_SESSION['id']));
            $count += 1;
        }
        if ($oldinfos['zip_code'] != $zipcode && !empty($zipcode) && strlen($zipcode) == 5) {
            $req = $db->prepare("UPDATE address SET zip_code = ? WHERE id_user = ?");
            $req->execute(array($zipcode, $_SESSION['id']));
            $count += 1;
        }
        if ($oldinfos['country'] != $country && !empty($country)) {
            $req = $db->prepare("UPDATE address SET country = ? WHERE id_user = ?");
            $req->execute(array($country, $_SESSION['id']));
            $count += 1;
        }
        if ($count > 0) {
            header('Content-Type: application/json');
            echo json_encode(array('error' => false));
        } else {
            header('Content-Type: application/json');
            echo json_encode(array('error' => true,'message' => "Aucune information n'a été modifiée."));
        }
    }

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
            $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif');
            if (in_array($file_extension, $allowed_extensions) === true) {
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
                $error = "L'avatar doit être au format jpg, jpeg, png ou gif.";
            }
        } else {
            $error = "L'avatar ne doit pas dépasser 2 Mo.";
        }
    }

    // Change banner
    if (isset($_FILES['banner-f']) && $_FILES['banner-f']['error'] === UPLOAD_ERR_OK) {
        if ($_FILES['banner-f']['size'] <= 10485760) {
            $filename = $_FILES['banner-f']['name'];
            $file_extension = pathinfo($filename, PATHINFO_EXTENSION);
            $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif');
            if (in_array($file_extension, $allowed_extensions) === true) {
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
                $error = "La bannière doit être au format jpg, jpeg, png ou gif.";
            }
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
                sendMailTfaEnabled($_SESSION['email']);
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
            sendMailTfaDisabled($_SESSION['email']);
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
                // delete directory
                $dir = '../img/user/'.$_SESSION['id'].'/';
                rrmdir($dir);
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