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
        <div class="feed">
            <h2>Nom d'utilisateur</h2>
            <a href="#"><img class="feed-avatar" src="/WE4A_project/img/icon/debug.png" alt="Avatar"></a>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
        </div>
        <div class="feed">
            <h2>Nom d'utilisateur</h2>
            <a href="#"><img class="feed-avatar" src="/WE4A_project/img/icon/debug.png" alt="Avatar"></a>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
        </div>
        <div class="feed">
            <h2>Nom d'utilisateur</h2>
            <a href="#"><img class="feed-avatar" src="/WE4A_project/img/icon/debug.png" alt="Avatar"></a>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
        </div>
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
        <form class ="formPost" method="POST" action="php_tool/submit_post.php">
            <textarea class="textAreaPost" placeholder="Saisir un message" required></textarea>
            <input type="submit" class="postSubmit" name="postSubmit" value="Envoyer le message"/>
            <div class=error-message><?php if(isset($error)){echo '<p>'.$error."</p>";} ?></div>
        </form>
    </div>
    <script src="/WE4A_project/js/home.js"></script>
</body>