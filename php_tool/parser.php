<?php

function parsePseudoForProfile($string) {
    $pattern = '/@(\w+)/';
    
    $parsedString = preg_replace_callback($pattern, function($matches) {
        $username = $matches[1];
        return '<a href="profile.php?pseudo='.$username.'" class="link-primary link-underline link-underline-opacity-0 link-underline-opacity-75-hover">'.$username.'</a>';
    }, $string);
    
    return $parsedString;
}

?>