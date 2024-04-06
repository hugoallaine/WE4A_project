<?php
require_once dirname(__FILE__).'/db.php';

if (isset($_POST['formLogin'])) {
    $email = SecurizeString_ForSQL($_POST['user']);
    $password = SecurizeString_ForSQL($_POST['password']);
    if (!empty($email) AND !empty($password)) {
        $req = $db->prepare("SELECT id,email,password,pseudo,verified,isTfaEnabled,isAdmin FROM users WHERE email = ?");
        $req->execute(array($email));
        $isUserExist = $req->rowCount();
        if ($isUserExist) {
            $user = $req->fetch();
            if (password_verify($password, $user['password'])) {
                if ($user['verified']) {
                    if ($user['isTfaEnabled']) {
                        $_SESSION['email'] = $user['email'];
                        $_SESSION['isTfaEnabled'] = $user['isTfaEnabled'];
                    } else {
                        createLoginCookie($user['email'], $user['token']);
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
?>