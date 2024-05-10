<?php
/**
 * Template for the top of the pages
 * 
 * This template is used to display the top of the pages
 */
require_once dirname(__FILE__).'/alreadyConnected.php';
session_start_secure();
require_once dirname(__FILE__).'/toast.php';
?>
<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>YGreg - <?php echo "$currentPage" ?></title>
    <link rel="icon" type="image/png" href="img/logo/YGreg_logo.png" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous" defer></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script src="js/template.js" defer></script>
    <link href="css/main.css" rel="stylesheet">
    <?php if($currentPage == "Accueil"){echo "<script src='js/index.js' defer></script>";} ?>
    <?php if($currentPage == "Profil"){echo "<script src='js/profile.js' defer></script>";} ?>
    <?php if($currentPage == "Paramètres"){echo "<script src='js/settings.js' defer></script>";} ?>
    <?php if($currentPage == "Notifications"){echo "<script src='js/notifications.js' defer></script>";} ?>
</head>
<body class="overflow-hidden">
    <div class="container-fluid">
        <!-- Container principal -->
        <div class="row">
            <!-- Row contenant 2 colonnes (sidebar | navbar+main) -->
            <div class="col-2 col-lg-2 p-0 vh-100">
                <!-- Sidebar -->
                <div class="d-none d-lg-flex flex-column flex-shrink-0 p-3 bg-light h-100">
                    <a href="index.php" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-dark text-decoration-none">
                        <img src="img/logo/YGreg_logo.png" alt="YGreg" width="40" height="40" class="bi me-2">
                        <span class="fs-4">YGreg</span>
                    </a>
                    <hr>
                    <ul class="nav nav-pills flex-column mb-auto">
                        <li class="nav-item">
                            <a href="index.php" class="nav-link <?php if($currentPage === 'Accueil'){echo 'active';}else{echo 'link-dark';} ?>">
                                <img src="img/icon/accueil.png" class="icon bi me-2" width="16" height="16">
                                Accueil
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php if(isConnected()){echo "profile.php?pseudo=".$_SESSION['pseudo'];}else{echo '#';} ?>" class="nav-link <?php if($currentPage === 'Profil'){echo 'active';}else{echo 'link-dark';} ?>" aria-current="page" <?php if(!isConnected()){echo "data-bs-toggle='modal' data-bs-target='#modalLogin'";} ?>>
                                <img src="img/icon/profil.png" class="icon bi me-2" width="16" height="16">
                                Profil
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php if(isConnected()){echo "notifications.php";}else{echo '#';} ?>" class="nav-link <?php if($currentPage === 'Notifications'){echo 'active';}else{echo 'link-dark';} ?>"  <?php if(!isConnected()){echo "data-bs-toggle='modal' data-bs-target='#modalLogin'";} ?>>
                                <img src="img/icon/cloche.png" class="icon bi me-2" width="16" height="16">
                                Notifications
                                <span id="nbNotif" class="badge text-bg-danger"></span>
                            </a>
                        </li>
                    </ul>
                    <hr>
                    <div class="d-flex align-items-center justify-content-between">
                        <?php if(isConnected()): ?>
                        <div class="dropdown">
                            <a href="#" class="d-flex align-items-center link-dark text-decoration-none dropdown-toggle" id="dropdownUser2" data-bs-toggle="dropdown" aria-expanded="false">
                                <img src="<?php if(!empty($_SESSION['avatar'])){echo "img/user/".$_SESSION['id']."/".$_SESSION['avatar'];}else{echo "img/icon/utilisateur.png";} ?>" alt="Icon User" width="40" height="40" class="<?php if(empty($_SESSION['avatar'])){echo 'icon';} ?> rounded-circle me-3 object-fit-cover">
                                <strong class="fs-5"><?php echo $_SESSION['pseudo']; ?></strong>
                            </a>
                            <ul class="dropdown-menu text-small shadow" aria-labelledby="dropdownUser2">
                                <li><a class="dropdown-item" href="settings.php">Paramètres</a></li>
                                <li><a class="dropdown-item" href="mailto:admin@allaine.cc">Support</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a id="logout-button" class="dropdown-item" href="#">Se déconnecter</a></li>       
                            </ul>
                        </div>
                        <?php else: ?>
                        <a href="#" id="login" class="d-flex align-items-center link-dark text-decoration-none" data-bs-toggle='modal' data-bs-target='#modalLogin'>
                            <img src="img/icon/utilisateur.png" alt="Icon User" width="40" height="40" class="icon rounded-circle me-3 object-fit-cover">
                            <strong class="fs-5">Se connecter</strong>
                        </a>
                        <?php endif; ?>
                        <img id="icon-theme" src="img/icon/moon.png" class="icon rounded" onclick="switchTheme()" width="32" height="32">
                    </div>
                </div>
                <!-- Sidebar responsive -->
                <div class="d-lg-none d-flex flex-column flex-shrink-0 bg-light h-100">
                    <a href="index.php" class="d-flex justify-content-center link-dark text-decoration-none p-3" title data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title="Icon-only">
                        <img src="img/logo/YGreg_logo.png" alt="YGreg" width="40" height="40" class="bi me-2">
                    </a>
                    <ul class="nav nav-pills nav-flush flex-column mb-auto text-center">
                        <li class="nav-item">
                            <a href="index.php" class="py-3 border-bottom nav-link <?php if($currentPage === 'Accueil'){echo 'active';}else{echo 'link-dark';} ?>">
                                <img src="img/icon/accueil.png" class="icon bi me-2" width="32" height="32">
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php if(isConnected()){echo "profile.php?pseudo=".$_SESSION['pseudo'];}else{echo '#';} ?>" class="py-3 border-bottom nav-link <?php if($currentPage === 'Profil'){echo 'active';}else{echo 'link-dark';} ?>" aria-current="page" <?php if(!isConnected()){echo "data-bs-toggle='modal' data-bs-target='#modalLogin'";} ?>>
                                <img src="img/icon/profil.png" class="icon bi me-2" width="32" height="32">
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php if(isConnected()){echo "notifications.php";}else{echo '#';} ?>" class="py-3 border-bottom nav-link <?php if($currentPage === 'Notifications'){echo 'active';}else{echo 'link-dark';} ?>"  <?php if(!isConnected()){echo "data-bs-toggle='modal' data-bs-target='#modalLogin'";} ?>>
                                <img src="img/icon/cloche.png" class="icon bi me-2" width="32" height="32">
                                <span id="nbNotif" class="badge text-bg-danger"></span>
                            </a>
                        </li>
                    </ul>
                    
                    <?php if(isConnected()): ?>
                    <div class="dropdown border-top">
                        <a href="#" class="p-3 d-flex justify-content-center align-items-center link-dark text-decoration-none dropdown-toggle" id="dropdownUser2" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="<?php if(!empty($_SESSION['avatar'])){echo "img/user/".$_SESSION['id']."/".$_SESSION['avatar'];}else{echo "img/icon/utilisateur.png";} ?>" alt="Icon User" width="40" height="40" class="rounded-circle object-fit-cover">
                        </a>
                        <ul class="dropdown-menu text-small shadow" aria-labelledby="dropdownUser2">
                            <li><a class="dropdown-item" href="settings.php">Paramètres</a></li>
                            <li><a class="dropdown-item" href="mailto:admin@allaine.cc">Support</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a id="logout-button" class="dropdown-item" href="#">Se déconnecter</a></li>       
                        </ul>
                    </div>
                    <?php else: ?>
                    <a href="#" id="login" class="d-flex align-items-center link-dark text-decoration-none" data-bs-toggle='modal' data-bs-target='#modalLogin'>
                        <img src="img/icon/utilisateur.png" alt="Icon User" width="32" height="32" class="icon rounded-circle object-fit-cover">
                    </a>
                    <?php endif; ?>
                    <div class="d-flex justify-content-center pb-3">
                        <img id="icon-theme" src="img/icon/moon.png" class="icon rounded" onclick="switchTheme()" width="32" height="32">
                    </div>
                </div>
            </div>
            <div class="col-10 col-lg-10 p-0 vh-100">
                <!-- Navbar -->
                <nav class="navbar navbar-expand-lg bg-body-tertiary">
                    <div class="container-fluid d-flex justify-content-space-around">
                        <div class="dropdown">
                            <?php if($currentPage == "Accueil" || $currentPage == "Profil"): ?>
                                <button class="btn btn-light dropdown" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <img src="img/icon/filtre.png" class="icon img-fluid d-block mx-auto" width="30">
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="filterDropdown">
                                    <?php if("Profil" == $currentPage): ?>
                                        <li><a class="dropdown-item" href="#">Gregs</a></li>
                                        <li><a class="dropdown-item" href="#">Réponses</a></li>
                                        <li><a class="dropdown-item" href="#">Likes</a></li>
                                    <?php elseif ("Accueil" == $currentPage): ?>
                                        <li><a class="dropdown-item" href="#">Récents</a></li>
                                        <li><a class="dropdown-item" href="#">Populaires</a></li>
                                        <li><a class="dropdown-item" href="#">Découvertes</a></li>
                                        <li><a class="dropdown-item" href="#">Suivis</a></li>
                                    <?php endif; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                        <div class="search-container position-relative top">
                            <input class="form-control me-2" type="search" id="search-bar" name="searchbar" placeholder="Rechercher" aria-label="Search">
                            <div id="search-results" class="position-absolute w-100 bg-light border rounded border-top-5 border-primary" style="display:none;z-index:2;"></div>
                        </div>
                        <div class="d-flex align-items-center">
                            <?php 
                            if (!isBan()) {
                                if (isConnected()) {
                                    $modal = "modalPost";
                                } else {
                                    $modal = "modalLogin";
                                }
                                echo "<button class='d-none d-md-block btn btn-primary ' type='button' data-bs-toggle='modal' data-bs-target='#".$modal."'>Écrire un Greg</button>";
                                echo "<button class='d-block d-md-none btn btn-primary ' type='button' data-bs-toggle='modal' data-bs-target='#".$modal."'>Greg</button>";

                            } else {
                                $req = $db->prepare("SELECT ban_time FROM users WHERE id = ?");
                                $req->execute(array($_SESSION['id']));
                                $banTime = $req->fetch();
                                if ($banTime['ban_time'] != null) {
                                    $banTime = date_create_from_format('Y-m-d H:i:s', $banTime['ban_time']);
                                    $banTime = $banTime->format('d/m/Y H:i:s');
                                    echo "<p class='text-danger m-0'>Vous êtes banni jusqu'au ".$banTime."</p>";
                                } else {
                                    echo "<p class='text-danger m-0'>Vous êtes banni définitivement.</p>";
                                }
                            }
                            ?>
                        </div>
                    </div>
                </nav>
                <?php if(isConnected() && !isBan()): ?>
                <!-- Modal - Ecrire un tweet -->
                <div class="modal fade" id="modalPost" tabindex="-1" role="dialog" aria-labelledby="modalPostLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <form id="formPostId" class="formPost" method="POST" action="">
                                <div class="modal-header border-bottom-0">
                                    <h5 class="modal-title" id="modalPostLabel">Nouveau message</h5>
                                </div>
                                <div class="modal-body">
                                    <textarea id="textAreaPostId" name="textAreaPostId" class="form-control mb-1" placeholder="Saisir un message" required style="resize: none; height:30vh"></textarea>
                                    <input type="hidden" name="MAX_FILE_SIZE" value="2097152">
                                    <label for="images" class="form-label">Ajouter des images</label>
                                    <input type="file" id="images" name="images" class="form-control"/>
                                </div>
                                <div class="modal-footer border-top-0">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>                                
                                    <input type="submit" class="btn btn-primary" name="postSubmit" value="Envoyer le message"/>
                                </div>
                                <input type="hidden" name="id_parent" value="" />
                            </form>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                <?php if(!isConnected()): ?>
                <!-- Modal - Se connecter -->
                <div class="modal fade" id="modalLogin" tabindex="-1" role="dialog" aria-labelledby="modalLoginLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <form id="formLoginId" class="formLogin" method="POST" action="">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalLoginLabel">Se connecter</h5>
                                </div>
                                <div class="modal-body">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="username" name="user" placeholder="" required autocomplete="email"/>
                                        <label for="username" class="form-label">Adresse Mail</label>
                                    </div>
                                    <div class="form-floating mb-3">
                                        <input type="password" class="form-control" id="password" name="password" placeholder="" required autocomplete="current-password"/>
                                        <label for="password" class="form-label">Mot de passe</label>
                                    </div>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" value="" id="showpassword" onchange="TogglePassword(this.checked)">
                                        <label class="form-check-label" for="showpassword">
                                            Montrer le mot de passe
                                        </label>
                                    </div>
                                    <div id="tfaDiv" class="form-floating mb-3" style="display:none;">
                                        <input type="text" class="form-control" id="tfa_code" name="tfa_code" placeholder="" autocomplete="one-time-code"/>
                                        <label for="tfa_code" class="form-label">Code de double authentification</label>
                                    </div>
                                    <div id="error-message" class="text-danger"></div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-bs-toggle='modal' data-bs-target='#modalRegister'>S'inscrire</button>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>                                
                                    <input type="submit" class="btn btn-primary" name="formLogin" value="Valider"/>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- Modal - S'inscrire -->
                <div class="modal fade" id="modalRegister" tabindex="-1" role="dialog" aria-labelledby="modalRegisterLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <form enctype="multipart/form-data" id="formRegisterId" class="formRegister" method="POST" action="">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalRegisterLabel">S'inscrire</h5>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="mail" class="form-label">Adresse email</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="mail" name="mail1-r">
                                            <span class="input-group-text">@</span>
                                            <input type="text" class="form-control" name="mail2-r">
                                        </div>
                                        <div class="form-text">Utiliser une adresse email valide uniquement. Un mail de vérification vous sera envoyé.</div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Mot de passe</label>
                                        <input type="password" class="form-control" id="password-r" name="password-r" autocomplete="new-password">
                                        <div class="form-text">
                                            Les caractéristiques minimales pour un mot de passe sont :
                                            <ul>
                                                <li>12 caractères</li>
                                                <li>1 majuscule</li>
                                                <li>1 chiffre</li>
                                                <li>1 caractère spécial</li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <label for="password-r-repeat" class="form-label">Répéter le mot de passe</label>
                                        <input type="password" class="form-control" id="password-r-repeat" name="password2-r">
                                    </div>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" value="" id="showpassword-r" onchange="TogglePasswordRegister(this.checked)">
                                        <label class="form-check-label" for="showpassword">
                                            Montrer le mot de passe
                                        </label>
                                    </div>
                                    <hr>
                                    <div class="mb-3">
                                        <label for="pseudo" class="form-label">Pseudo</label>
                                        <input type="text" class="form-control" id="pseudo" name="pseudo-r" autocomplete="username">
                                    </div>
                                    <div class="mb-3">
                                        <input type="hidden" name="MAX_FILE_SIZE" value="2097152">
                                        <label for="avatar" class="form-label">Avatar</label>
                                        <input type="file" class="form-control" id="avatar" name="avatar-r">
                                    </div>
                                    <hr>
                                    <div class="row mb-3">
                                        <div class="col">
                                            <label for="firstname" class="form-label">Prénom</label>
                                            <input type="text" class="form-control" id="firstname" name="firstname-r" autocomplete="given-name">
                                        </div>
                                        <div class="col">
                                            <label for="name" class="form-label">Nom</label>
                                            <input type="text" class="form-control" id="name" name="name-r" autocomplete="family-name">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="birthdate" class="form-label">Date de naissance</label>
                                        <input type="date" class="form-control" id="birthdate" name="birthdate-r" autocomplete="bday">
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label for="inputAddress" class="form-label">Adresse</label>
                                            <input type="text" class="form-control" id="inputAddress" name="address-r" autocomplete="street-address">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="inputCity" class="form-label">Ville</label>
                                            <input type="text" class="form-control" id="inputCity" name="city-r">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="inputZip" class="form-label">Code Postal</label>
                                            <input type="text" class="form-control" id="inputZip" name="zipcode-r" autocomplete="postal-code">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="inputCountry" class="form-label">Pays</label>
                                            <input type="text" class="form-control" id="inputCountry" name="country-r" autocomplete="country-name">
                                        </div>
                                        <div class="col-12 d-flex justify-content-center align-items-center">
                                            <div class="g-recaptcha" data-sitekey="6LeClLIpAAAAAIt1EesWjZ_TEuMne4QRk-TTuBQ2"></div>
                                        </div>
                                    </div>
                                    <div id="error-message-r" class="text-danger text-align-center"></div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-bs-toggle='modal' data-bs-target='#modalLogin'>Se connecter</button>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>                                
                                    <input type="submit" class="btn btn-primary" name="formRegister" value="Valider"/>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                <!-- Response modal -->
                <div class="modal fade" id="modalResponses" tabindex="-1" role="dialog" aria-labelledby="modalReponsesLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title"></h5>
                            </div>
                            <div class="modal-body">
                            <!-- Responses -->
                            </div>
                        </div>
                    </div>
                </div>
                <?php if(isConnected() && isAdmin() && !isBan()): ?>
                <!-- Modal - Admin Template -->
                <div class="modal fade" id="modalAdmin" tabindex="-1" role="dialog" aria-labelledby="modalAdminLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <form id="formAdmin" class="formAdmin" method="POST" action="">
                                <div class="modal-header bg-danger text-white">
                                    <h5 class="modal-title" id="modalAdminLabel"></h5>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label class="form-label" for="notif-message">Contenu de la notification</label>
                                        <textarea class="form-control" id="notif-message" name="notifMessage" rows="3" required></textarea>
                                    </div>
                                    <div id="ban-date-div" class="form-group">

                                    </div>
                                    <input type="hidden" id="admin-post-control-id" name="adminPostControlId" value=""/>
                                    <input type="hidden" id="admin-action-type" name="adminActionType" value=""/>
                                    <div id="error-message-admin" class="text-danger text-align-center"></div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>                                
                                    <input type="submit" class="btn btn-primary" name="formAdmin" value="Valider"/>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                <!-- Toast -->
                <?php 
                if (!isConnected()) {
                    echo '<div class="toast-container position-fixed bottom-0 end-0 m-3">';
                        successToast('Connexion réussie','loginToast');
                        successToast('Inscription réussie. Vérifier votre adresse email.','registerToast');
                        errorToast("Votre avatar ne respecte pas les conditions requises et n'a pas été enregistré.",'avatarToast');
                    echo '</div>';
                } elseif (isConnected() && isAdmin()) {
                    echo '<div class="toast-container position-fixed bottom-0 end-0 m-3">';
                        successToast('La notification a été envoyée.','sendNotifToast');
                    echo '</div>';
                }
                ?>
                <!-- Main -->
