<?php
require_once dirname(__FILE__).'/json.php';

$servername = $json['db_host'];
$username = $json['db_user'];
$password = $json['db_password'];
$dbname = $json['db_name'];
try {
    $db = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

function SecurizeString_ForSQL($string){
    $string = trim($string);
    $string = stripcslashes($string);
    $string = addslashes($string);
    $string = htmlspecialchars($string);
    return $string;
}

function RestoreString_FromSQL($string){
    $string = htmlspecialchars_decode($string);
    $string = stripslashes($string);
    return $string;
}

function generateToken($length) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_=~';
    $chars_length = strlen($chars);
    $token = '';
    for ($i = 0; $i < $length; $i++) {
        $token .= $chars[rand(0, $chars_length - 1)];
    }
    return $token;
}

function checkToken($token, $id) {
    global $db;
    $req = $db->prepare("SELECT * FROM users WHERE token = ? AND id = ?");
    $req->execute(array($token, $id));
    $result = $req->rowCount();
    if ($result) {
        return true;
    }
    return false;
}

function createLoginCookie($email, $token) {
    setcookie('email', $email, time() + 24*3600);
    setcookie('token', $token, time() + 24*3600);
}
?>