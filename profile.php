<?php
require_once dirname(__FILE__).'/php_tool/alreadyConnected.php';
session_start_secure();

$currentPage = 'Profil';

if (isset($_GET['pseudo'])) {
    $getpseudo = SecurizeString_ForSQL($_GET['pseudo']);
    $req = $db->prepare('SELECT id,pseudo,avatar,banner,bio,isAdmin FROM users WHERE pseudo = ?');
    $req->execute(array($getpseudo));
    if ($req->rowCount() > 0) {
        $userinfo = $req->fetch();
        $req = $db->prepare('SELECT Count(*) AS followers FROM follows WHERE id_user_followed = ?');
        $req->execute(array($userinfo['id']));
        $followers = $req->fetch();
        $req = $db->prepare('SELECT Count(*) AS following FROM follows WHERE id_user_following = ?');
        $req->execute(array($userinfo['id']));
        $following = $req->fetch();
    } else {
        header("Location: profile.php");
    }
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
                    <?php if (isConnected() && $_SESSION['id'] == $userinfo['id']): ?>
                        <a href="#" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#modalProfile">Modifier mon profil</a>
                    <?php else: ?>
                        <form id="formFollow" method="POST">
                            <input type="hidden" name="pseudo" value="<?php echo $userinfo['pseudo'] ?>">
                            <?php
                            if (isConnected()) {
                                $req = $db->prepare('SELECT Count(*) AS follow FROM follows WHERE id_user_following = ? AND id_user_followed = ?');
                                $req->execute(array($_SESSION['id'], $userinfo['id']));
                                $follow = $req->fetch();
                                if($follow['follow'] == 0) {
                                    echo '<button id="btnFollow" type="submit" class="btn btn-primary" onmouseover="changeText()" onmouseout="resetText()">Suivre</button>';
                                } else {
                                    echo '<button id="btnFollow" type="submit" class="btn btn-primary" onmouseover="changeText()" onmouseout="resetText()">Suivi</button>';
                                }
                            } else {
                                echo 'Suivre';
                            }
                            ?>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
            <div class="d-flex justify-content-start align-items-center">
                <a href="#" class="link-secondary link-underline link-underline-opacity-0 link-underline-opacity-75-hover">
                    <h6 class="card-subtitle text-body-secondary"><?php echo '<span id="nbFollowers">'.$followers['followers'].'</span>'; if($followers['followers']>1){echo " abonnés";}else{echo " abonné";}?></h6>
                </a>
                <h6 class="ms-2 mb-1 me-2">-</h6>
                <a href="#" class="link-secondary link-underline link-underline-opacity-0 link-underline-opacity-75-hover">
                    <h6 class="card-subtitle text-body-secondary"><?php echo '<span id="nbFollowing">'.$following['following'].'</span>'; if($following['following']>1){echo " abonnements";}else{echo " abonnement";}?></h6>
                </a>
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
                        <div class="mb-3">
                            <input type="hidden" name="MAX_FILE_SIZE" value="2097152">
                            <label for="avatar-f" class="form-label">Avatar</label>
                            <input type="file" class="form-control" id="avatar-f" name="avatar-f">
                        </div>
                        <div class="mb-3">
                            <input type="hidden" name="MAX_FILE_SIZE" value="10485760">
                            <label for="banner-f" class="form-label">Bannière</label>
                            <input type="file" class="form-control" id="banner-f" name="banner-f">
                        </div>
                        <div class="form-floating mb-3">
                            <textarea id="bio-f" name="bio-f" class="form-control" style="resize: none; height:30vh"><?php echo $userinfo['bio']; ?></textarea>
                            <label for="bio-f" class="form-label">Bio</label>
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
    <!-- Toast - Modifier le profil -->
    <?php
    successToast("Profil modifié avec succès !", "changeProfileToast");
    endif;
    ?>
</main>
<?php
require_once dirname(__FILE__).'/php_tool/template_bot.php';
?>