<?php
include 'php_tool/submit_post.php';
require_once dirname(__FILE__).'/php_tool/db.php';
?>

<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='utf-8'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>YGreg - Home</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel='stylesheet' type='text/css' href='css/default.css'>
    <link rel='stylesheet' type='text/css' href='css/home.css'>
</head>
<body>
    <div class="feed-container">
        <?php

        $req = $db->prepare("SELECT * FROM `posts` INNER JOIN users ON posts.id_user = users.id LIMIT 10");
        $req->execute();
        $posts = $req->fetchAll();

        foreach ($posts as $post) {
            echo "
            <div class='feed'>
                <h2>".$post['pseudo']."</h2>
                <a href='#'><img class='feed-avatar' src='/WE4A_project/img/icon/debug.png' alt='Avatar'></a>
                <p>".$post['content']."</p>
            </div>
            ";
        }

        ?>
    </div>
    <nav class="navbar">
    <ul>
            <?php
            /* (isset($_SESSION['id'])) */
            if (true) {
                echo "
                <li><a href='#'><img class='icon' src='/WE4A_project/img/icon/debug.png' alt='Accueil'></a></li>
                <li><a href='#'><img class='icon' src='/WE4A_project/img/icon/debug.png' alt='Profil'></a></li>
                <li><a href='#'><img class='icon buttonWritePost' src='/WE4A_project/img/icon/debug.png' alt='Écrire un message'></a></li>
                <li><a href='#'><img class='icon' src='/WE4A_project/img/icon/debug.png' alt='Paramètres'></a></li>
                ";
            } else {
                echo "
                <li><a href='#'><img class='icon' src='/WE4A_project/img/icon/debug.png' alt='Accueil'></a></li>
                <li><a href='#'><img class='icon' src='/WE4A_project/img/icon/debug.png' alt='Connexion'></a></li>
                ";
            }
            ?>
        </ul>
    </nav>
    <div id="modalPost" class="modalPost" style="display: none;">
        <form id="formPostId" class ="formPost" method="POST" action="">
            <textarea id="textAreaPostId" name="textAreaPostId" class="textAreaPost" placeholder="Saisir un message" required></textarea>
            <input type="submit" class="postSubmit" name="postSubmit" value="Envoyer le message"/>
            <div class=error-message><?php if(isset($error)){echo '<p>'.$error."</p>";} ?></div>
        </form>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="/WE4A_project/js/home.js"></script>
</body>