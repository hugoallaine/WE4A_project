<?php
session_start();
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
    <script src="/WE4A_project/js/template.js" defer></script>
</head>
<body>
    <div class="container-fluid">
        <!-- Container principal -->
        <div class="row">
            <!-- Row contenant 2 colonnes (sidebar | navbar+main) -->
            <div class="col-2 p-0 vh-100">
                <!-- Sidebar -->
                <div class="d-flex flex-column flex-shrink-0 p-3 bg-light h-100">
                    <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-dark text-decoration-none">
                        <img src="img/logo/YGreg_logo.png" alt="YGreg" width="40" height="40"
                            class="bi me-2">
                        <span class="fs-4">YGreg</span>
                    </a>
                    <hr>
                    <ul class="nav nav-pills flex-column mb-auto">
                        <li class="nav-item">
                            <a href="index.php" class="nav-link <?php if($currentPage === 'Accueil'){echo 'active';}else{echo 'link-dark';} ?>">
                                <img src="img/icon/accueil.png" class="bi me-2" width="16" height="16">
                                Accueil
                            </a>
                        </li>
                        <li>
                            <a href="profile.php" class="nav-link <?php if($currentPage === 'Profil'){echo 'active';}else{echo 'link-dark';} ?>" aria-current="page">
                                <img src="img/icon/profil.png" class="bi me-2" width="16" height="16">
                                Profil
                            </a>
                        </li>
                        <li>
                            <a href="messages.php" class="nav-link <?php if($currentPage === 'Messages'){echo 'active';}else{echo 'link-dark';} ?>">
                                <img src="img/icon/messages.png" class="bi me-2" width="16" height="16">
                                Messages
                            </a>
                        </li>
                        <li>
                            <a href="friends.php" class="nav-link <?php if($currentPage === 'Amis'){echo 'active';}else{echo 'link-dark';} ?>">
                                <img src="img/icon/amis.png" class="bi me-2" width="16" height="16">
                                Amis
                            </a>
                        </li>
                    </ul>
                    <hr>
                    <?php if(isset($_SESSION['id'])): ?>
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center link-dark text-decoration-none dropdown-toggle"
                            id="dropdownUser2" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="img/icon/utilisateur.png" alt="" width="32" height="32" class="rounded-circle me-3">
                            <strong>Pseudo</strong>
                        </a>
                        <ul class="dropdown-menu text-small shadow" aria-labelledby="dropdownUser2">
                            <li><a class="dropdown-item" href="#">Paramètres</a></li>
                            <li><a class="dropdown-item" href="#">Support</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="#">Se déconnecter</a></li>
                        </ul>
                    </div>
                    <?php else: ?>
                    <a href="#" class="d-flex align-items-center link-dark text-decoration-none" data-bs-toggle='modal' data-bs-target='#modalLogin'>
                        <img src="img/icon/utilisateur.png" alt="" width="32" height="32" class="rounded-circle me-3">
                        <strong>Se connecter</strong>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-10 p-0">
                <!-- Navbar -->
                <nav class="navbar navbar-expand-lg bg-body-tertiary">
                    <div class="container-fluid d-flex justify-content-space-around">
                        <div class="dropdown">
                        <button class="btn btn-light dropdown" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="img/icon/filtre.png" class="img-fluid d-block mx-auto" width="30">
                        </button>
                            <ul class="dropdown-menu" aria-labelledby="filterDropdown">
                                <li><a class="dropdown-item" href="#">Filtre 1</a></li>
                                <li><a class="dropdown-item" href="#">Filtre 2</a></li>
                                <li><a class="dropdown-item" href="#">Filtre 3</a></li>
                                <!-- Ajoutez autant de filtres que vous le souhaitez -->
                            </ul>
                        </div>
                        <form class="d-flex" role="search">
                            <input class="form-control me-2" type="search" name="searchbar" placeholder="Rechercher" aria-label="Search">
                            <button class="btn btn-outline-success" type="submit"><img src="img/icon/loupe.png"
                                    class="img-fluid d-block mx-auto" width="30"></button>
                        </form>
                        <button class="btn btn-primary " type="button" data-bs-toggle='modal' data-bs-target='#modalPost'>Écrire un Greg</button>
                    </div>
                </nav>
                <!-- Modal - Ecrire un tweet -->
                <div class="modal fade" id="modalPost" tabindex="-1" role="dialog" aria-labelledby="modalPostLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <form id="formPostId" class="formPost" method="POST" action="">
                                <div class="modal-header border-bottom-0">
                                    <h5 class="modal-title" id="modalPostLabel">Nouveau message</h5>
                                </div>
                                <div class="modal-body">
                                    <textarea id="textAreaPostId" name="textAreaPostId" class="form-control" placeholder="Saisir un message" required style="resize: none; height:20vh"></textarea>
                                </div>
                                <div class="modal-footer border-top-0">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>                                
                                    <input type="submit" class="btn btn-primary" name="postSubmit" value="Envoyer le message"/>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
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
                                        <input type="text" class="form-control" id="username" name="user" placeholder="" required/>
                                        <label for="username" class="form-label">Adresse Mail</label>
                                    </div>
                                    <div class="form-floating mb-3">
                                        <input type="password" class="form-control" id="password" name="password" placeholder="" required/>
                                        <label for="password" class="form-label">Mot de passe</label>
                                    </div>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" value="" id="showpassword" onchange="TogglePassword(this.checked)">
                                        <label class="form-check-label" for="showpassword">
                                            Montrer le mot de passe
                                        </label>
                                    </div>
                                    <div id="error_message" class="text-danger"></div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>                                
                                    <input type="submit" class="btn btn-primary" name="formLogin" value="Valider"/>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- Main -->
