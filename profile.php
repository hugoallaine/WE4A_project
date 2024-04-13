<?php
require_once dirname(__FILE__).'/php_tool/alreadyConnected.php';
$currentPage = 'Profil';

if (isset($_GET['pseudo'])) {
    $getpseudo = SecurizeString_ForSQL($_GET['pseudo']);
    $req = $db->prepare('SELECT id,pseudo,avatar,banner,bio,isAdmin FROM users WHERE pseudo = ?');
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
        <img src="<?php if(isset($userinfo['banner'])){echo "img/user/".$userinfo['id'].'/'.$userinfo['banner'];}else{echo "img/icon/banner.jpg";} ?>" class="card-img-top" alt="Banner" height=300>
        <div class="card-body">
            <div class="d-flex justify-content-between">
                <h5 class="card-title"><img src="img/user/<?php echo $userinfo['id'].'/'.$userinfo['avatar']; ?>" alt="Avatar de <?php echo $userinfo['pseudo']; ?>" class="rounded me-2" width=60 height=60>
                    <?php 
                    echo $userinfo['pseudo'];  
                    if(isset($userinfo['isAdmin']) && $userinfo['isAdmin'] == 1) {
                        echo '<span class="badge bg-danger m-2">Admin</span>';
                    }
                    ?>
                </h5>
                <?php
                if(isset($_SESSION['id']) && $_SESSION['id'] == $userinfo['id']) {
                    echo '<a href="edit_profile.php" class="btn btn-primary">Modifier mon profil</a>';
                }
                ?>
            </div>
            <p class="card-text"><?php echo $userinfo['bio']; ?></p>
        </div>
    </div>
</main>
<?php
require_once dirname(__FILE__).'/php_tool/template_bot.php';
?>