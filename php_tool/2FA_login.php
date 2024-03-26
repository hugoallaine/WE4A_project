<?php
require_once dirname(__FILE__)."/2FA_config.php";

if(!isset($_SESSION['email'])){
    header("Location: /login.php");
}

use RobThree\Auth\TwoFactorAuth;

if(isset($_POST['tfa'])){
    $req = $db->prepare("SELECT authKey FROM tfa INNER JOIN users ON tfa.id_user = users.id WHERE users.email = ?");
    $req->execute(array($_SESSION['email']));
    $user = $req->fetch();
    $tfa = new TwoFactorAuth();
    if(!empty($_POST['tfa_code_app']) AND $tfa->verifyCode($user['tfa_code'], $_POST['tfa_code_app'])){
        createLoginCookie($_SESSION['email'], $user['token']);
        unset($_SESSION['email']);
        if(isset($_GET['redirect'])){
            header("Location: ../".$_GET['redirect']."");
        } else {
            header("Location: ../");
        }
    } else {
        $erreur = "Code invalide.";
    }
}

?>
<!DOCTYPE html>
<html lang="fr">        
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Connexion 2FA - Jeu2Dame</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel='stylesheet' type='text/css' href='css/default.css'>
</head>
<body>
    <header>
        <a class="header_titre" href="/">Jeu2Dame</a>
        <nav>
            <button onclick="window.location.href = '../logout';">Annuler la connexion</button>
        </nav>
    </header>
    <section>
        <div id="fenetre_centre">
        <h2>Tentative de connexion en cours...</h2>
        <br/>
        <p>Merci d'entrer votre code de double authentification 2FA pour vous connecter.</p>
        <br/>
        <br/>
        <form method="POST">
            <input type="text" placeholder="Vérification Code" name="tfa_code_app">
            <button class="FAloginbutton" type="submit" name="tfa">Valider</button>
        </form>
        <?php
        if(isset($erreur)){
            echo $erreur;
        }
        ?>
        </div>
    </section>
    <footer>
        <h4>Développé par des gens sympathiques</h4>
    </footer>
</body>
</html>