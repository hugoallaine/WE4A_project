<?php
require_once dirname(__FILE__).'/db.php';

/**
 * Parse the pseudo in a string to add a link to the profile
 * @param string $string
 * @return string
 */
function parsePseudoForProfile($string) {
    global $db;
    $pattern = '/@(\w+)/';
    $parsedString = preg_replace_callback($pattern, function($matches) use ($db) {
        $username = $matches[1];
        $req = $db->prepare('SELECT * FROM users WHERE pseudo = ?');
        $req->execute([$username]);
        $exist = $req->rowCount();
        if ($exist == 1) {
            return '<a href="profile.php?pseudo='.$username.'" class="link-primary link-underline link-underline-opacity-0 link-underline-opacity-75-hover"><strong>'.$username.'</strong></a>';
        }
        return "@".$username;
    }, $string);
    return $parsedString;
}

?>