<?php
$servername = "localhost";
$username = "php";
$password = "chuApPg9*rGqcxWqXH8#!q%Rc7M4Re5e#EHokYx8L";
$dbname = "ygreg";
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

function generateToken($length) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_+=~';
    $chars_length = strlen($chars);
    $token = '';
    for ($i = 0; $i < $length; $i++) {
        $token .= $chars[rand(0, $chars_length - 1)];
    }
    return $token;
}

function checkToken($token, $email) {
    global $db;
    $req = $db->prepare("SELECT * FROM users WHERE token = ? AND email = ?");
    $req->execute(array($token));
    $result = $req->fetch();
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