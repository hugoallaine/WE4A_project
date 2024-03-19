<?php
require_once(dirname(__FILE__)."/2FA_config.php");

if(!isset($_SESSION['email']) || !isset($_SESSION['isTfaEnabled'])){
    header("Location: /index.php");
}

use RobThree\Auth\TwoFactorAuth;

if(isset($_POST['tfa'])){
    $req = $db->prepare("SELECT authKey FROM tfa INNER JOIN users ON tfa.id_user = users.id WHERE users.email = ?");
    $req->execute(array($_SESSION['email']));
    $user = $req->fetch();
    $tfa = new TwoFactorAuth();
    if(!empty($_POST['tfa_code_app']) AND $tfa->verifyCode($user['tfa_code'], $_POST['tfa_code_app'])){
        $_SESSION['id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['admin'] = $user['admin'];
        unset($_SESSION['isTfaEnabled']);
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
    <title>Connexion 2FA - Jeu2Dame</title>
    <link rel="stylesheet" href="../style/border.css">
    <link rel="stylesheet" href="../style/stylebutton1.css">
    <link rel="stylesheet" href="../style/2FAlogin.css">
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