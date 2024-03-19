<?php
require_once dirname(__FILE__).'/php_tool/db.php';
require_once dirname(__FILE__).'/php_tool/mails.php';

if (isset($_POST['form'])) {
    $pseudo = SecurizeString_ForSQL($_POST['pseudo']);
    $email = SecurizeString_ForSQL($_POST['email']);
    $password = SecurizeString_ForSQL($_POST['password']);
    $password2 = SecurizeString_ForSQL($_POST['password2']);
    $name = SecurizeString_ForSQL($_POST['name']);
    $firstname = SecurizeString_ForSQL($_POST['firstname']);
    $birthdate = $_POST['birthdate'];
    if (!empty($pseudo) AND !empty($email) AND !empty($password) AND !empty($password2) AND !empty($name) AND !empty($firstname) AND !empty($birthdate)) {
        if (strlen($pseudo) <= 32) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $req = $db->prepare("SELECT id FROM users WHERE email = ?");
                $req->execute(array($email));
                $emailexist = $req->rowCount();
                if ($emailexist == 0) {
                    if ($password == $password2) {
                        if (strlen($password) >= 12) {
                            $password = password_hash($password, PASSWORD_DEFAULT);
                            $key = "";
                            for($i=1;$i<255;$i++) {
                                $key .= mt_rand(0,9);
                            }
                            $req = $db->prepare("INSERT INTO users(email,password,name,firstname,birth_date,pseudo) VALUES(?,?,?,?,?,?)");
                            $req->execute(array($email, $password, $name, $firstname, $birthdate, $pseudo));
                            $req = $db->prepare("INSERT INTO emailsNonVerifies(email,token,id_user) VALUES (?,?,(SELECT id FROM users WHERE email = ?))");
                            $req->execute(array($email, $key, $email));
                            sendMailConfirm($email, $key);
                            $error = "Votre compte a bien été créé ! <a href=\"login.php\">Me connecter</a>";
                            $status = 1;
                        } else {
                            $error = "Votre mot de passe doit faire au minimum 12 caractères.";
                        }
                    } else {
                        $error = "Vos mots de passe ne correspondent pas.";
                    }
                } else {
                    $error = "Cette adresse email existe déjà.";
                }
            } else {
                $error = "Votre adresse email n'est pas valide.";
            }
        } else {
            $error = "Votre nom d'utilisateur ne doit pas dépasser 32 caractères !";
        }
    } else {
        $error = "Tous les champs doivent être complétés !";
    }
}
?>

<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='utf-8'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>YGreg - Register</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel='stylesheet' type='text/css' href='css/default.css'>
    <link rel='stylesheet' type='text/css' href='css/login.css'>
</head>
<body>
    <section>
        <form method="POST" action="">
            <h1>Inscription à YGreg</h1>
            <div>
                <input type="text" class="form_input" name="pseudo" placeholder="" required/>
                <label>Pseudo</label>
            </div>
            <div>
                <input type="text" class="form_input" name="email" placeholder="" required/>
                <label>Adresse Mail</label>
            </div>
            <div>
                <input type="password" class="form_input" name="password" placeholder="" required/>
                <label>Mot de passe</label>
            </div>
            <div>
                <input type="password" class="form_input" name="password2" placeholder="" required/>
                <label>Confirmer le mot de passe</label>
            </div>
            <div>
                <input type="text" class="form_input" name="name" placeholder="" required/>
                <label>Nom</label>
            </div>
            <div>
                <input type="text" class="form_input" name="firstname" placeholder="" required/>
                <label>Prénom</label>
            </div>
            <div>
                <input type="date" class="form_input" name="birthdate" placeholder="" required/>
                <label>Date de naissance</label>
            </div>
            <input type="submit" class="form_submit" name="form" value="S'inscrire"/>
            <?php 
            if (!isset($error)) {
                $error = "";
            }
            if(isset($status) && $status == 1) { 
                echo '<div class="success-message">'.$error.'</div>'; 
            } else { 
                echo '<div class="error-message">'.$error.'</div>'; 
            }
            ?>
        </form>
    </section>
</body>
</html>