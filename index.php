<?php
require_once dirname(__FILE__).'/php_tool/alreadyConnected.php';
session_start_secure();
require_once dirname(__FILE__).'/php_tool/submit_post.php';
require_once dirname(__FILE__).'/php_tool/db.php';

$currentPage = 'Accueil';

require_once dirname(__FILE__).'/php_tool/template_top.php';
?>
<main>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-8 col-md-12 p-0 vh-100 overflow-auto">
                <?php
                if (isset($_SESSION['id'])) {
                    $req = $db->prepare("
                    SELECT posts.*, users.pseudo, users.avatar, 
                    likes.id as like_id
                    FROM posts
                    INNER JOIN users ON posts.id_user = users.id
                    LEFT JOIN likes ON posts.id = likes.id_post AND likes.id_user = ?
                    ORDER BY created_at DESC LIMIT 15
                    ");
                    $req->execute([$_SESSION['id']]);
                } else {
                    $req = $db->query("
                    SELECT posts.*, users.pseudo, users.avatar, 
                    NULL as like_id
                    FROM posts
                    INNER JOIN users ON posts.id_user = users.id
                    ORDER BY created_at DESC LIMIT 15
                    ");
                }
                $posts = $req->fetchAll();

                foreach ($posts as $post) {
                    $date = date_create_from_format('Y-m-d H:i:s', $post['created_at']);
                    $formatted_date = $date->format('d/m/Y');
                    $like_image = !is_null($post['like_id']) ? "/WE4A_project/img/icon/liked.png" : "/WE4A_project/img/icon/like.png";
                    if (!empty($post['avatar'])) {
                        $avatar = "/WE4A_project/img/user/".$post['id_user'].'/'.$post['avatar'];
                    } else {
                        $avatar = "/WE4A_project/img/icon/utilisateur.png";
                    }

                    $reqPic = $db->prepare("SELECT * FROM pictures WHERE id_post = ?");
                    $reqPic->execute([$post['id']]);
                    $picture = $reqPic->fetch();

                    if($picture) {
                        $picture = "/WE4A_project/img/user/".$post['id_user'].'/posts/'.$post['id'].'/'.$picture['path'];
                    }

                    echo "
                        <div class='card rounded-0' data-post-id='" . $post['id'] . "'>
                            <div class='card-body'>
                                <div class='row'>
                                    <div class='col-md-2 col-3 text-center'>
                                        <a class='link-secondary link-underline link-underline-opacity-0' href='/WE4A_project/profile.php?pseudo=" . $post['pseudo'] . "'>
                                        <img src='$avatar' width='32' height='32' alt='Avatar' class='rounded-circle mr-2' style='object-fit: cover;'>
                                        <h5 class='card-title m-0'>" . $post['pseudo'] . "</h5>
                                        </a>
                                        <p class='card-subtitle text-muted'>" . $formatted_date . "</p>
                                    </div>
                                    <div class='col p-0'>
                                        <p>" . RestoreString_FromSQL($post['content']) . "</p>
                                        " . ($picture ? "<a href='$picture'><img src='$picture' class='rounded' width='400' height='320' style='object-fit: cover;'></a>" : "") . "
                                    </div>
                                    <div class='col-1'>
                                        <div class='row'>
                                            <div class='col-12 p-0'>
                                                <button class='btn like-button' " . (isset($_SESSION['id']) ? "data-post-id='" . $post['id'] . "'" : "data-bs-toggle='modal' data-bs-target='#modalLogin'") . ">
                                                    <img src='".$like_image."' alt='like button' class='img-fluid' >
                                                </button>
                                            </div>
                                            <div class='col-12 p-0'>
                                            <button class='btn' type='button' data-bs-toggle='modal' data-bs-target='#" . (isset($_SESSION['id']) ? 'modalPost' : 'modalLogin') . "' data-tweet-id='" . $post['id'] . "'>
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
            <div class="col-lg-4 p-0">
                <h5>Statistiques</h5>
            </div>
        </div>
        <!-- Response modal -->
        <div class="modal fade" id="modalResponses" tabindex="-1" role="dialog" aria-labelledby="modalReponsesLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                   
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?php
require_once dirname(__FILE__).'/php_tool/template_bot.php';
?>