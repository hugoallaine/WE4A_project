<?php

require_once dirname(__FILE__).'/php_tool/alreadyConnected.php';
require_once dirname(__FILE__).'/php_tool/submit_post.php';
require_once dirname(__FILE__).'/php_tool/db.php';

$currentPage = 'Accueil';

require_once dirname(__FILE__).'/php_tool/template_top.php';
?>
<main>
    
    <div class="feed-container col-lg-8 col-md-12 p-0 overflow-auto vh-100">
        <?php
        $req = $db->prepare("
        SELECT posts.*, users.pseudo, 
        likes.id as like_id
        FROM posts
        INNER JOIN users ON posts.id_user = users.id
        LEFT JOIN likes ON posts.id = likes.id_post AND likes.id_user = ?
        LIMIT 10
        ");
        $req->execute([$_SESSION['id']]);
        $posts = $req->fetchAll();

        foreach ($posts as $post) {
            $date = date_create_from_format('Y-m-d H:i:s', $post['created_at']);
            $formatted_date = $date->format('d/m/Y');
            $like_image = !is_null($post['like_id']) ? "/WE4A_project/img/icon/liked.png" : "/WE4A_project/img/icon/like.png";

            echo "
                <div class='card rounded-0 animated-post'>
                    <div class='card-body'>
                        <div class='row'>
                            <div class='col-md-2 col-3 text-center'>
                                <img src='/WE4A_project/img/icon/utilisateur.png' width='32' height='32' alt='Avatar' class='mr-2'>
                                <h5 class='card-title m-0'>" . $post['pseudo'] . "</h5>
                                <p class='card-subtitle text-muted'>" . $formatted_date . "</p>
                            </div>
                            <div class='col p-0'>
                                <p>" . $post['content'] . "</p>
                            </div>
                            <div class='col-1'>
                                <div class='row'>
                                    <div class='col-12 p-0'>
                                        <button class='btn like-button' " . (isset($_SESSION['id']) ? "data-post-id='" . $post['id'] . "'" : "data-bs-toggle='modal' data-bs-target='#modalLogin'") . ">
                                            <img src='".$like_image."' alt='like button' class='img-fluid' >
                                        </button>
                                    </div>
                                    <div class='col-12 p-0'>
                                        <button class='btn'>
                                            <img src='/WE4A_project/img/icon/response.png' alt='response button' class='img-fluid'>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            ";
        }
        ?>
    </div>
</main>
<?php
require_once dirname(__FILE__).'/php_tool/template_bot.php';
?>