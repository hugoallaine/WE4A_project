<?php
include 'php_tool/submit_post.php';
require_once dirname(__FILE__).'/php_tool/db.php';
?>

<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>YGreg - Home</title>
    <link rel="icon" type="image/png" href="img/logo/YGreg_logo.png"/>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>

    <div class="container">
        

        <div class="row">
            
            <div class="col-md-2 col-xs-0"></div>
                
            <div class="feed-container col-md-7 col-10 bg-primary-subtle">

                


                <?php

                $req = $db->prepare("SELECT * FROM `posts` INNER JOIN users ON posts.id_user = users.id LIMIT 3");
                $req->execute();
                $posts = $req->fetchAll();

                foreach ($posts as $post) {
                    echo "
                    <div class='feed'>
                        <h2>".$post['pseudo']."</h2>
                        <a href='#'><img class='feed-avatar w-50' src='/WE4A_project/img/icon/debug.png' alt='Avatar'></a>
                        <p>".$post['content']."</p>
                    </div>
                    ";
                }

                ?>
            </div>
            <div class="col-md-1 col-2 bg-secondary-subtle p-0">
                <nav class="nav flex-column">
                    
                
                    <?php
                    /* (isset($_SESSION['id'])) */
                    if (true) {
                        echo "
                        <a class='nav-link icon' href='#'><img class='w-100' src='/WE4A_project/img/icon/debug.png' alt='Accueil'></a>
                        <a class='nav-link icon' href='#'><img class='w-100' src='/WE4A_project/img/icon/debug.png' alt='Profile'></a>
                        <a class='nav-link icon buttonWritePost' href='#' data-bs-toggle='modal' data-bs-target='#modalPost'><img class='w-100' src='/WE4A_project/img/icon/debug.png' alt='Ecrire un post'></a>   
                        <a class='nav-link icon' href='#'><img class='w-100' src='/WE4A_project/img/icon/debug.png' alt='Déconnexion'></a>
                        ";
                    } else {
                        echo "
                        <a class='nav-link icon' href='#'><img src='/WE4A_project/img/icon/debug.png' alt='Accueil'></a>
                        <a class='nav-link icon' href='#'><img src='/WE4A_project/img/icon/debug.png' alt='Inscription'></a>
                        ";
                    }
                    ?>
    
                </nav>
            </div>
            <div class="modal fade" id="modalPost" tabindex="-1" role="dialog" aria-labelledby="modalPostLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <form id="formPostId" class="formPost" method="POST" action="">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalPostLabel">Nouveau message</h5>
                            </div>
                            <div class="modal-body">
                                <textarea id="textAreaPostId" name="textAreaPostId" class="form-control" placeholder="Saisir un message" required></textarea>
                                <div class="error-message"><?php if(isset($error)){echo '<p>'.$error."</p>";} ?></div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>                                <input type="submit" class="btn btn-primary" name="postSubmit" value="Envoyer le message"/>
                            </div>
                        </form>
                    </div>
                </div>
            </div>


            <div class="col-md-2 col-xs-0"></div>

        </div>

    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="/WE4A_project/js/home.js"></script>
</body>