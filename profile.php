<?php
require_once dirname(__FILE__).'/php_tool/alreadyConnected.php';
$currentPage = 'Profil';

if (isset($_GET['pseudo'])) {
    $getpseudo = SecurizeString_ForSQL($_GET['pseudo']);
    $req = $db->prepare('SELECT id,pseudo,avatar,banner,bio,isAdmin FROM users WHERE pseudo = ?');
    $req->execute(array($getpseudo));
    $userinfo = $req->fetch();
    $req = $db->prepare('SELECT Count(*) AS followers FROM follows WHERE id_user_followed = ?');
    $req->execute(array($userinfo['id']));
    $followers = $req->fetch();
    $req = $db->prepare('SELECT Count(*) AS following FROM follows WHERE id_user_following = ?');
    $req->execute(array($userinfo['id']));
    $following = $req->fetch();
} elseif (isConnected()) {
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
                <h5 class="card-title"><img src="<?php if(!empty($userinfo['avatar'])){echo "img/user/".$userinfo['id']."/".$userinfo['avatar'];}else{echo "img/icon/utilisateur.png";} ?>" alt="Avatar de <?php echo $userinfo['pseudo']; ?>" class="rounded me-2" width=60 height=60>
                    <?php 
                    echo $userinfo['pseudo'];  
                    if(isset($userinfo['isAdmin']) && $userinfo['isAdmin'] == 1) {
                        echo '<span class="badge bg-danger m-2">Admin</span>';
                    }
                    ?>
                </h5>
                <div class="d-flex align-items-center">
                    <?php
                    if(isConnected() && $_SESSION['id'] == $userinfo['id']) {
                        echo '<a href="#" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#modalProfile">Modifier mon profil</a>';
                    }
                    ?>
                </div>
            </div>
            <h6 class="card-subtitle mb-2 text-body-secondary"><?php echo $followers['followers']; if($followers['followers']>1){echo " abonnés";}else{echo " abonné";}?> - <?php echo $following['following']; if($following['following']>1){echo " abonnements";}else{echo " abonnement";}?></h6>
            <p class="card-text"><?php echo $userinfo['bio']; ?></p>
        </div>
    </div>
    <?php if(isConnected() && $_SESSION['id'] == $userinfo['id']): ?>
    <!-- Modal - Modifier le profil -->
    <div class="modal fade" id="modalProfile" tabindex="-1" role="dialog" aria-labelledby="modalProfileLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="formProfile" class="formProfile" method="POST" action="">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalProfileLabel">Modifier mon profil</h5>
                    </div>
                    <div class="modal-body">
                        <div class="form-floating mb-3">
                            <input type="text" id="pseudo-f" name="pseudo-f" class="form-control" value="<?php echo $userinfo['pseudo']; ?>" required>
                            <label for="pseudo-f" class="form-label">Pseudo</label>
                        </div>
                        <div class="form-floating mb-3">
                            <textarea id="bio" name="bio" class="form-control" style="resize: none; height:30vh"></textarea>
                            <label for="bio" class="form-label">Bio</label>
                        </div>
                        <div id="error-message" class="text-danger"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>                                
                        <input type="submit" class="btn btn-primary" name="formProfile" value="Valider"/>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>
</main>
<?php
require_once dirname(__FILE__).'/php_tool/template_bot.php';
?>