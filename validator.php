<?php
require_once dirname(__FILE__).'/php_tool/db.php';

/**
 * Vérification de l'adresse email utilisateur pour la validation du compte
 */
if (isset($_GET['email']) && isset($_GET['key'])) {
    $email = SecurizeString_ForSQL($_GET['email']);
    $key = SecurizeString_ForSQL($_GET['key']);
    if (!empty($email) && !empty($key)) {
        $req = $db->prepare("SELECT * FROM emailsnonverifies WHERE email = ? AND token = ?");
        $req->execute(array($email, $key));
        $userexist = $req->rowCount();
        if ($userexist) {
            $user = $req->fetch();
            $req = $db->prepare("SELECT * FROM users WHERE email = ?");
            $req->execute(array($email));
            $user = $req->fetch();
            if ($user['verified'] == 0) {
                $req = $db->prepare("UPDATE users SET verified = 1 WHERE email = ?");
                $req->execute(array($email));
                $req = $db->prepare("DELETE FROM emailsnonverifies WHERE email = ?");
                $req->execute(array($email));
                header('Location: index.php');
            } else {
                header('Location: index.php');
            }
        } else {
            echo "Echec de vérification.";
        }
    } else {
        echo "Paramètres manquants.";
    }
}

?>