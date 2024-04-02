<?php
require_once dirname(__FILE__).'/db.php';

if (isset($_POST['form'])) {
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
                        if(isset($_GET['redirect'])) {
                            header("Location: /login.php?redirect=".$_GET['redirect']."");
                        } else {
                            header("Location: /login.php");
                        }
                    } else {
                        createLoginCookie($user['email'], $user['token']);
                        if(isset($_GET['redirect'])) {
                            header("Location: ".$_GET['redirect']."");
                        } else {
                            header("Location: /index.php");
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
    <link rel="icon" type="image/png" href="img/logo/YGreg_logo.png" />
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel='stylesheet' type='text/css' href='css/default.css'>
    <link rel='stylesheet' type='text/css' href='css/login.css'>
    <script src="js/login.js" defer></script>
</head>
<body>
    <section>
        <form method="POST" action="">
            <h1>Connexion à YGreg</h1>
            <div class="container">
                <input type="text" class="form_input" name="user" placeholder="" required/>
                <label>Adresse Mail</label>
            </div>
            <div class="container">
                <input type="password" class="form_input" id="password" name="password" placeholder="" required/>
                <label>Mot de passe</label>
                <img src="img/icon/password_hide.png" alt="Show/Hide Password" class="show-password" onmouseover="ShowPass_MouseOn(this)" 
				onmouseout="ShowPass_MouseOff(this)" onclick="TogglePassword(this)"/>
            </div>
            <input type="submit" class="form_submit" name="form" value="Se connecter"/>
            <div class=error-message><?php if(isset($error)){echo '<p>'.$error."</p>";} ?></div>
        </form>
    </section>
</body>
</html>