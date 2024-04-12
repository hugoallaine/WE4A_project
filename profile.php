<?php
require_once dirname(__FILE__).'/php_tool/alreadyConnected.php';
$currentPage = 'Profil';

if (isset($_GET['pseudo'])) {
    $getpseudo = SecurizeString_ForSQL($_GET['pseudo']);
    $req = $db->prepare('SELECT pseudo,avatar,banner FROM users WHERE pseudo = ?');
    $req->execute(array($getpseudo));
    $userinfo = $req->fetch();
} elseif (isset($_SESSION['id']) && isset($_SESSION['pseudo'])) {
    header("Location: profile.php?pseudo=".$_SESSION['pseudo']);
} else {
    header("Location: index.php");
}

require_once dirname(__FILE__).'/php_tool/template_top.php';
?>
<main>
    <div class="card">
        <div class="card-header">
            <h1>Profil de <?php echo $userinfo['pseudo']; ?></h1>
        </div>
        <div class="card-body">
            <div class="banner" style="background-image: url('img/user/<?php echo $userinfo['id'].'/'.$userinfo['banner']; ?>');">
                <img src="img/user/<?php echo $userinfo['id'].'/'.$userinfo['avatar']; ?>" alt="Avatar de <?php echo $userinfo['pseudo']; ?>">
            </div>
        </div>
    </div>
</main>
<?php
require_once dirname(__FILE__).'/php_tool/template_bot.php';
?>