<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once dirname(__FILE__).'/vendor/PHPMailer/src/PHPMailer.php';
require_once dirname(__FILE__).'/vendor/PHPMailer/src/Exception.php';
require_once dirname(__FILE__).'/vendor/PHPMailer/src/SMTP.php';

function sendMail($destinataire, $sujet, $message) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'mail.allaine.cc';
        $mail->SMTPAuth = true;
        $mail->Username = 'ygreg@allaine.cc';
        $mail->Password = 'JzVk&V^x7c&kW6TB2XYHfgxQNP9rh5iz@o%8$E2CvxXw6C*xwfUraetz%pJqs6Qd';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;
        $mail->setFrom('ygreg@allaine.cc', 'YGreg');
        $mail->addAddress($destinataire);
        $mail->addReplyTo('no-reply@allaine.cc','No-Reply');
        $mail->isHTML(true);
        $mail->Subject = $sujet;
        $mail->Body = $message;
        $mail->send();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

function sendMailConfirm($destinataire, $confirmkey) {
    $sujet = "Confirmation de votre compte YGreg";
    $message = '
    <html>
    <head>
        <meta charset="utf-8">
        <title>Confirmation de votre compte YGreg</title>
    </head>
    <body>
        <h1>V&eacute;rification de votre adresse email - YGreg</h1>
        <p>Bonjour, veuillez confirmer votre compte en cliquant sur le lien suivant : <a href="http://ygreg.allaine.cc/mail_confirmation.php?mail='.urlencode($destinataire).'&key='.urlencode($confirmkey).'">Confirmer</a></p>
        <p>Si vous n\'&ecirc;tes pas &agrave; l\'origine de cette action, merci d\'ignorer ce mail.</p>
    </body>
    </html>
    ';
    sendMail($destinataire, $sujet, $message);
}

// A voir plus tard
function sendMailReset($destinataire, $resetkey) {
    $sujet = "Réinitialisation de votre mot de passe YGreg";
    $message = '
    <html>
    <head>
        <meta charset="utf-8">
        <title>R&eacute;initialisation de votre mot de passe YGreg</title>
    </head>
    <body>
        <h1>R&eacute;initialisation de votre mot de passe - YGreg</h1>
        <p>Bonjour, veuillez r&eacute;initialiser votre mot de passe en cliquant sur le lien suivant : <a href="http://ygreg.allaine.cc/reset_password.php?mail='.urlencode($destinataire).'&key='.urlencode($resetkey).'">Réinitialiser</a></p>
        <p>Si vous n\'&ecirc;tes pas &agrave; l\'origine de cette action, merci d\'ignorer ce mail.</p>
    </body>
    </html>
    ';
    sendMail($destinataire, $sujet, $message);
}

function sendMailTfaEnabled($destinataire) {
    $sujet = "Activation de l'authentification à deux facteurs YGreg";
    $message = '
    <html>
    <head>
        <meta charset="utf-8">
        <title>Activation de l\'authentification &agrave; deux facteurs YGreg</title>
    </head>
    <body>
        <h1>Activation de l\'authentification &agrave; deux facteurs - YGreg</h1>
        <p>Bonjour, l\'authentification &agrave; deux facteurs a bien &eacute;t&eacute; activ&eacute;e sur votre compte.</p>
        <p>Si vous n\'&ecirc;tes pas &agrave; l\'origine de cette action, merci de contacter le support.</p>
    </body>
    </html>
    ';
    sendMail($destinataire, $sujet, $message);
}

function sendMailTfaDisabled($destinataire) {
    $sujet = "Désactivation de l'authentification à deux facteurs YGreg";
    $message = '
    <html>
    <head>
        <meta charset="utf-8">
        <title>Désactivation de l\'authentification &agrave; deux facteurs YGreg</title>
    </head>
    <body>
        <h1>D&eacute;sactivation de l\'authentification &agrave; deux facteurs - YGreg</h1>
        <p>Bonjour, l\'authentification &agrave; deux facteurs a bien &eacute;t&eacute; d&eacute;sactiv&eacute;e sur votre compte.</p>
        <p>Si vous n\'&ecirc;tes pas &agrave; l\'origine de cette action, merci de contacter le support.</p>
    </body>
    </html>
    ';
    sendMail($destinataire, $sujet, $message);
}
?>