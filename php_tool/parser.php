<?php
require_once dirname(__FILE__).'/db.php';

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['content'])) {
        $parsedString = parsePseudoForProfile($_POST['content']);
        header('Content-Type: application/json');
        echo json_encode(array('error' => false, 'content' => $parsedString));
    } else {
        header('Content-Type: application/json');
        echo json_encode(array('error' => true, 'message' => 'Missing content'));
    }
}

?>