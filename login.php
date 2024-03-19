<?php
session_start();
require_once('php_tool/alreadyConnected.php');
require_once('php_tool/db.php');

if (isset($_POST['form'])){
    $email = SecurizeString_ForSQL($_POST['user']);
    $password = SecurizeString_ForSQL($_POST['password']);
    if (!empty($email) AND !empty($password)) {
        $req = $db->prepare("SELECT id,email,password,pseudo,verified,isTfaEnabled,isAdmin FROM users WHERE email = ?");
        $req->execute(array($email));
        $isUserExist = $req->fetch();
        if ($isUserExist) {
            $user = $req->fetch();
            if (password_verify($password, $user['password'])) {
                if ($user['verified']) {
                    if ($user['isTfaEnabled']) {
                        $_SESSION['email'] = $user['email'];
                        if(isset($_GET['redirect'])){
                            header("Location: /2FA/2FA_login?redirect=".$_GET['redirect']."");
                        } else {
                            header("Location: /2FA/2FA_login");
                        }
                    } else {
                        $_SESSION['id'] = $user['id'];
                        $_SESSION['email'] = $user['email'];
                        $_SESSION['pseudo'] = $user['pseudo'];
                        $_SESSION['isAdmin'] = $user['isAdmin'];
                        if(isset($_GET['redirect'])){
                            header("Location: ".$_GET['redirect']."");
                        } else {
                            header("Location: index.php");
                        }
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

<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='utf-8'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>YGreg - Login</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel='stylesheet' type='text/css' href='css/default.css'>
    <link rel='stylesheet' type='text/css' href='css/login.css'>
</head>
<body>
    <section>
        <form method="POST" action="">
            <h1>Connexion à YGreg</h1>
            <div>
                <input type="text" class="form_input" name="user" placeholder="" required/>
                <label>Adresse Mail</label>
            </div>
            <div>
                <input type="password" class="form_input" name="password" placeholder="" required/>
                <label>Mot de passe</label>
            </div>
            <input type="submit" class="form_submit" name="form" value="Se connecter"/>
            <div class=error-message><?php if(isset($error)){echo '<p>'.$error."</p>";} ?></div>
        </form>
    </section>
</body>
</html>