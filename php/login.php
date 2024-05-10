<?php
require_once dirname(__FILE__).'/alreadyConnected.php';
session_start_secure();
require_once dirname(__FILE__).'/db.php';
require_once dirname(__FILE__).'/vendor/autoload.php';
use RobThree\Auth\TwoFactorAuth;
$tfa = new TwoFactorAuth($issuer = 'YGreg');

/**
 * API to manage the login actions
 * 
 * Response:
 * - error (boolean): true if an error occured
 * - message (string): the error message
 */
if (isset($_POST['user']) && isset($_POST['password'])) {
    $email = SecurizeString_ForSQL($_POST['user']);
    $password = SecurizeString_ForSQL($_POST['password']);
    if (!empty($email) AND !empty($password)) {
        $req = $db->prepare("SELECT id,email,password,token,pseudo,avatar,verified,tfaKey,isAdmin,isBan,ban_time FROM users WHERE email = ?");
        $req->execute(array($email));
        $isUserExist = $req->rowCount();
        if ($isUserExist) {
            $user = $req->fetch();
            if (password_verify($password, $user['password'])) {
                if ($user['verified']) {
                    if (!empty($user['tfaKey']) && !empty($_POST['tfa_code'])) {
                        if ($tfa->verifyCode($user['tfaKey'], $_POST['tfa_code'])) {
                            $_SESSION['id'] = $user['id'];
                            $_SESSION['email'] = $user['email'];
                            $_SESSION['token'] = $user['token'];
                            $_SESSION['pseudo'] = $user['pseudo'];
                            $_SESSION['isBan'] = $user['isBan'];
                            if (empty($user['avatar'])) {
                                $_SESSION['avatar'] = null;
                            } else {
                                $_SESSION['avatar'] = $user['avatar'];
                            }
                            $_SESSION['isAdmin'] = $user['isAdmin'];
                        } else {
                            $error = "Code invalide.";
                        }
                    } else if (!empty($user['tfaKey']) && empty($_POST['tfa_code'])) {
                        header('Content-Type: application/json');
                        echo json_encode(array('tfa' => true));
                    } else {
                        $_SESSION['id'] = $user['id'];
                        $_SESSION['email'] = $user['email'];
                        $_SESSION['token'] = $user['token'];
                        $_SESSION['pseudo'] = $user['pseudo'];
                        $_SESSION['isBan'] = $user['isBan'];
                        if (empty($user['avatar'])) {
                            $_SESSION['avatar'] = null;
                        } else {
                            $_SESSION['avatar'] = $user['avatar'];
                        }
                        $_SESSION['isAdmin'] = $user['isAdmin'];
                    }
                } else {
                    $error = "Votre adresse mail n'a pas été confirmé, consultez votre boite mail.";
                }
            } else {
                $error = "Mot de passe ou adresse mail invalide.";
            }
        } else {
            $error = "Mot de passe ou adresse mail invalide.";
        }
    } else {
        $error = "Tous les champs doivent être complétés.";
    }
}

if (isset($error)) {
    header('Content-Type: application/json');
    echo json_encode(array('error' => true,'message' => $error));
}
?>