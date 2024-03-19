<?php
function SecurizeString_ForSQL($string){
    $string = trim($string);
    $string = stripcslashes($string);
    $string = addslashes($string);
    $string = htmlspecialchars($string);
    return $string;
}

try {
    $db = new PDO('mysql:host=localhost;dbname=ygreg', 'php', 'chuApPg9*rGqcxWqXH8#!q%Rc7M4Re5e#EHokYx8L');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Echec de connexion : " . $e->getMessage();
}
?>