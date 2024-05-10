<?php
require_once dirname(__FILE__).'/php/alreadyConnected.php';
session_start_secure();

$currentPage = 'Profil';

if (isset($_GET['pseudo'])) {
    $getpseudo = SecurizeString_ForSQL($_GET['pseudo']);
    $req = $db->prepare('SELECT id,pseudo,avatar,banner,bio,isAdmin,isBan,ban_time FROM users WHERE pseudo = ?');
    $req->execute(array($getpseudo));
    if ($req->rowCount() > 0) {
        $userinfo = $req->fetch();
        $req = $db->prepare('SELECT Count(*) AS followers FROM follows WHERE id_user_followed = ?');
        $req->execute(array($userinfo['id']));
        $followers = $req->fetch();
        $req = $db->prepare('SELECT Count(*) AS following FROM follows WHERE id_user_following = ?');
        $req->execute(array($userinfo['id']));
        $following = $req->fetch();
        if (isConnected() && $_SESSION['id'] == $userinfo['id']) {
            $req = $db->prepare('SELECT * FROM user_statistics WHERE id_user = ?');
            $req->execute(array($userinfo['id']));
            $stats = $req->fetch();
        }
    } else {
        header("Location: profile.php");
    }
} elseif (isConnected()) {
    header("Location: profile.php?pseudo=".$_SESSION['pseudo']);
} else {
    header("Location: index.php");
}

require_once dirname(__FILE__).'/php/template_top.php';
?>
<main>
    <div class="container-fluid ">
        <div class="row">
            <div class="col p-0 vh-100 overflow-auto" id="profile-view">
                <div class="card">
                    <div style='width: auto; height: 300px;'>
                        <img src="<?php if(isset($userinfo['banner'])){echo "img/user/".$userinfo['id'].'/'.$userinfo['banner'];}else{echo "img/icon/banner.jpg";} ?>" class="card-img-top object-fit-cover" alt="Banner" style='height:100%; width:100%;'>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <h1 class="card-title d-flex align-items-center fs-4">
                                <div class='rounded me-2' style='width: 60px; height: 60px;'>
                                    <img src="<?php if(!empty($userinfo['avatar'])){echo "img/user/".$userinfo['id']."/".$userinfo['avatar'];}else{echo "img/icon/utilisateur.png";} ?>" alt="Avatar de <?php echo $userinfo['pseudo']; ?>" class="rounded me-2 object-fit-cover" style='height:100%; width:100%;'>
                                </div>
                                <?php 
                                echo '<span data-user-id="'.$userinfo['id'].'" id="pseudo">'.$userinfo['pseudo'].'</span>';
                                if (isset($userinfo['isAdmin']) && $userinfo['isAdmin'] == 1) {
                                    echo '<span class="badge bg-danger m-2">Admin</span>';
                                } 
                                if (isset($userinfo['isBan']) && $userinfo['isBan'] == 1) {
                                    if (isset($userinfo['ban_time']) && $userinfo['ban_time'] != null) {
                                        echo '<span class="badge bg-danger m-2">Banni jusqu\'au '.date('d/m/Y', strtotime($userinfo['ban_time'])).'</span>';
                                    } else {
                                        echo '<span class="badge bg-danger m-2">Banni définitivement</span>';
                                    }
                                }
                                ?>
                            </h1>
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
                                            echo '<button id="btnFollow" type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalLogin">Suivre</button>';
                                        }
                                        ?>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="d-flex justify-content-start align-items-center" data-bs-toggle="modal" data-bs-target="#modalFollows">
                            <a href="#" class="link-secondary link-underline link-underline-opacity-0 link-underline-opacity-75-hover">
                                <h6 class="card-subtitle text-body-secondary"><?php echo '<span id="nbFollowers">'.$followers['followers'].'</span>'; if($followers['followers']>1){echo " abonnés";}else{echo " abonné";}?></h6>
                            </a>
                            <h6 class="ms-2 mb-1 me-2">-</h6>
                            <a href="#" class="link-secondary link-underline link-underline-opacity-0 link-underline-opacity-75-hover">
                                <h6 class="card-subtitle text-body-secondary"><?php echo '<span id="nbFollowing">'.$following['following'].'</span>'; if($following['following']>1){echo " abonnements";}else{echo " abonnement";}?></h6>
                            </a>
                        </div>
                        <?php if (!empty($userinfo['bio'])): ?>
                            <hr>
                            <p class="card-text"><?php echo $userinfo['bio']; ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="" id="posts-container">
                </div>
            </div>

            <?php if(isConnected() && $_SESSION['id'] == $userinfo['id'] && isset($stats)): ?>
            <div class="col-3 p-3 bg-light">
                <h4 class="text-center">Statistiques</h4>
                <hr>
                <div class="d-flex flex-column justify-content-between">
                    <div class="d-flex justify-content-around">
                        <div class="d-flex flex-column align-items-center">
                            <h5>Abonnés</h5>
                            <h6><?php echo $stats['followers_count']; ?></h6>
                        </div>
                        <div class="d-flex flex-column align-items-center">
                            <h5>Abonnements</h5>
                            <h6><?php echo $stats['following_count']; ?></h6>
                        </div>
                    </div>
                    <hr>
                    <div>
                        <div class="d-flex flex-column align-items-center">
                            <h5>Gregs originaux publiés</h5>
                            <h6><?php echo ($stats['total_posts']-$stats['responses']); ?></h6>
                        </div>
                        <div class="d-flex flex-column align-items-center">
                            <h5>Réponses publiées</h5>
                            <h6><?php echo $stats['responses']; ?></h6>
                        </div>
                        <div class="d-flex flex-column align-items-center">
                            <h5>Total de publications</h5>
                            <h6><?php echo $stats['total_posts']; ?></h6>
                        </div>
                        <div class="d-flex flex-column align-items-center">
                            <h5>Moyenne de publication / semaine</h5>
                            <h6><?php echo $stats['avg_posts_per_week']; ?></h6>
                        </div>
                        <div class="d-flex flex-column align-items-center">
                            <h5>Moyenne de publication / mois</h5>
                            <h6><?php echo $stats['avg_posts_per_month']; ?></h6>
                        </div>
                    </div>
                    <hr>
                    <div>
                        <div class="d-flex flex-column align-items-center">
                            <h5>Nombre de likes donnés</h5>
                            <h6><?php echo $stats['likes_given_count']; ?></h6>
                        </div>
                        <div class="d-flex flex-column align-items-center">
                            <h5>Nombre de likes reçus</h5>
                            <h6><?php echo $stats['likes_received_count']; ?></h6>
                        </div>
                        <div class="d-flex flex-column align-items-center">
                            <h5>Moyenne des likes donnés / semaine</h5>
                            <h6><?php echo $stats['avg_likes_given_per_week']; ?></h6>
                        </div>
                        <div class="d-flex flex-column align-items-center">
                            <h5>Moyenne des likes donnés / mois</h5>
                            <h6><?php echo $stats['avg_likes_given_per_month']; ?></h6>
                        </div>
                        <div class="d-flex flex-column align-items-center">
                            <h5>Moyenne des likes reçus / semaine</h5>
                            <h6><?php echo $stats['avg_likes_received_per_week']; ?></h6>
                        </div>
                        <div class="d-flex flex-column align-items-center">
                            <h5>Moyenne des likes reçus / mois</h5>
                            <h6><?php echo $stats['avg_likes_received_per_month']; ?></h6>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
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
    <!-- Modal - Followers -->
    <div class="modal fade" id="modalFollows" tabindex="-1" role="dialog" aria-labelledby="modalFollowsLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalFollowsLabel">Gérer les follows</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col overflow-auto">
                            <h5 class="text-center p-2">Abonnés</h5>
                            <hr>
                            <div id="followers-list" class="list-group">
                                <?php
                                $req = $db->prepare('SELECT id_user_following FROM follows WHERE id_user_followed = ?');
                                $req->execute(array($userinfo['id']));
                                while ($follower = $req->fetch()) {
                                    $req2 = $db->prepare('SELECT id,pseudo,avatar FROM users WHERE id = ?');
                                    $req2->execute(array($follower['id_user_following']));
                                    $follower = $req2->fetch();
                                    echo '<div class="d-flex justify-content-center p-1"><a href="profile.php?pseudo='.$follower['pseudo'].'" class="link-primary link-underline link-underline-opacity-0"><img src="img/user/'.$follower['id'].'/'.$follower['avatar'].'" alt="Icon User" width="32" height="32" class="rounded-circle me-3 object-fit-cover"/>'.$follower['pseudo'].'</a></div>';
                                }
                                ?>
                            </div>
                        </div>
                        <div class="vr"></div>
                        <div class="col overflow-auto">
                            <h5 class="text-center p-2">Abonnements</h5>
                            <hr>
                            <div id="following-list" class="list-group">
                                <?php
                                $req = $db->prepare('SELECT id_user_followed FROM follows WHERE id_user_following = ?');
                                $req->execute(array($userinfo['id']));
                                while ($following = $req->fetch()) {
                                    $req2 = $db->prepare('SELECT id,pseudo,avatar FROM users WHERE id = ?');
                                    $req2->execute(array($following['id_user_followed']));
                                    $following = $req2->fetch();
                                    echo '<div class="d-flex justify-content-between p-3"><a href="profile.php?pseudo='.$following['pseudo'].'" class="link-primary link-underline link-underline-opacity-0"><img src="img/user/'.$following['id'].'/'.$following['avatar'].'" alt="Icon User" width="32" height="32" class="rounded-circle me-3 object-fit-cover"/>'.$following['pseudo'].'</a>';
                                    echo '<button class="btnUnfollow btn btn-secondary" user-id="'.$following['id'].'" type="button">Se désabonner</button></div>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Toast - Modifier le profil -->
    <?php
    echo '<div class="toast-container position-fixed bottom-0 end-0 m-3">';
        successToast("Profil modifié avec succès !", "changeProfileToast");
    echo '</div>';
    endif;
    ?>
</main>
<?php
require_once dirname(__FILE__).'/php/template_bot.php';
?>