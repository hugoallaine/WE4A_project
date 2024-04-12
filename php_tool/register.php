<?php
require_once dirname(__FILE__).'/db.php';
require_once dirname(__FILE__).'/mails.php';
require_once dirname(__FILE__).'/vendor/autoload.php';
require_once dirname(__FILE__).'/json.php';

function getIp() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

if (isset($_POST['mail1-r']) && isset($_POST['mail2-r']) && isset($_POST['password-r']) && isset($_POST['password2-r']) && isset($_POST['pseudo-r']) && isset($_POST['name-r']) && isset($_POST['firstname-r']) && isset($_POST['birthdate-r']) && isset($_POST['address-r']) && isset($_POST['city-r']) && isset($_POST['zipcode-r']) && isset($_POST['country-r'])) {
    $recaptcha = new \ReCaptcha\ReCaptcha($json['reCaptcha_secret']);
    $gRecaptchaResponse = $_POST['g-recaptcha-response'];
    $resp = $recaptcha->setExpectedHostname('localhost')->verify($gRecaptchaResponse, getIp());
    if ($resp->isSuccess()) {
        $email = SecurizeString_ForSQL($_POST['mail1-r'])."@".SecurizeString_ForSQL($_POST['mail2-r']);
        $password = SecurizeString_ForSQL($_POST['password-r']);
        $password2 = SecurizeString_ForSQL($_POST['password2-r']);
        $pseudo = SecurizeString_ForSQL($_POST['pseudo-r']);
        $avatar = "utilisateur.png";
        $name = SecurizeString_ForSQL($_POST['name-r']);
        $firstname = SecurizeString_ForSQL($_POST['firstname-r']);
        $birthdate = $_POST['birthdate-r'];
        $address = SecurizeString_ForSQL($_POST['address-r']);
        $city = SecurizeString_ForSQL($_POST['city-r']);
        $zipcode = SecurizeString_ForSQL($_POST['zipcode-r']);
        $country = SecurizeString_ForSQL($_POST['country-r']);
        if (!empty($email) && !empty($password) && !empty($password2) && !empty($pseudo) && !empty($name) && !empty($firstname) && !empty($birthdate) && !empty($address) && !empty($city) && !empty($zipcode) && !empty($country)) {
            if (strlen($pseudo) <= 32) {
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $req = $db->prepare("SELECT id FROM users WHERE email = ?");
                    $req->execute(array($email));
                    $emailexist = $req->rowCount();
                    if ($emailexist == 0) {
                        if ($password == $password2) {
                            if (strlen($password) >= 12 && preg_match('/[A-Z]/', $password) && preg_match('/[a-z]/', $password) && preg_match('/[0-9]/', $password) && preg_match('/[^a-zA-Z0-9]/', $password)) {
                                if (strlen($zipcode) == 5) {
                                    $password = password_hash($password, PASSWORD_DEFAULT);
                                    $key = generateToken(255);
                                    $token = generateToken(255);
                                    $req = $db->prepare("INSERT INTO users(email,password,token,name,firstname,birth_date,pseudo,avatar) VALUES (?,?,?,?,?,?,?,?)");
                                    $req->execute(array($email, $password, $token, $name, $firstname, $birthdate, $pseudo, $avatar));
                                    if (isset($_FILES['avatar-r']) && $_FILES['avatar-r']['error'] === UPLOAD_ERR_OK) {
                                        if ($_FILES['avatar-r']['size'] <= 2097152) {
                                            $req = $db->prepare("SELECT id FROM users WHERE email = ?");
                                            $req->execute(array($email));
                                            $line = $req->fetch();
                                            $filename = $_FILES['avatar-r']['name'];
                                            $file_extension = pathinfo($filename, PATHINFO_EXTENSION);
                                            $newfilename = "avatar.".$file_extension;
                                            $tmp_name = $_FILES['avatar-r']['tmp_name'];
                                            $upload_directory = '../img/user/'.$line['id'].'/';
                                            echo $upload_directory;
                                            if (!file_exists($upload_directory)) {
                                                mkdir($upload_directory, 0777, true);
                                            }
                                            $path = $upload_directory.$newfilename;
                                            move_uploaded_file($tmp_name, $path);
                                            $avatar = $newfilename;
                                            $req = $db->prepare("UPDATE users SET avatar = ? WHERE email = ?");
                                            $req->execute(array($avatar, $email));
                                        }
                                    }
                                    $req = $db->prepare("INSERT INTO address(id_user,address,city,zip_code,country) VALUES((SELECT id FROM users WHERE email = ?),?,?,?,?)");
                                    $req->execute(array($email, $address, $city, $zipcode, $country));
                                    $req = $db->prepare("INSERT INTO emailsNonVerifies(email,token,id_user) VALUES (?,?,(SELECT id FROM users WHERE email = ?))");
                                    $req->execute(array($email, $key, $email));
                                    sendMailConfirm($email, $key);
                                } else {
                                    $error = "Votre code postal doit contenir 5 chiffres.";
                                }
                            } else {
                                $error = "Votre mot de passe ne satisfait pas les conditions minimums.";
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
    } else {
        $error = "Merci de remplir le captcha.";
    }
}

if (isset($error)) {
    header('Content-Type: application/json');
    echo json_encode(array('error' => true,'message' => $error));
}
?>