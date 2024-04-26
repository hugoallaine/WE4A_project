<?php
require_once dirname(__FILE__).'/db.php';

function parsePseudoForProfile($string) {
    global $db;
    $pattern = '/@(\w+)/';
    
    $parsedString = preg_replace_callback($pattern, function($matches,$db) {
        $username = $matches[1];
        $req = $db->prepare("SELECT * FROM users WHERE pseudo = ?");
        $req->execute(array($username));
        $exist = $req->rowCount();
        if ($exist == 1) {
            return '<a href="profile.php?pseudo='.$username.'" class="link-primary link-underline link-underline-opacity-0 link-underline-opacity-75-hover">'.$username.'</a>';
        }
        return "@".$username;
    }, $string);
    
    return $parsedString;
}

?>