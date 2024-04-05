<?php
require_once dirname(__FILE__) . '/php_tool/alreadyConnected.php';
//redirectIfNotConnected();
require_once dirname(__FILE__).'/php_tool/submit_post.php';
require_once dirname(__FILE__).'/php_tool/db.php';

$currentPage = 'Accueil';

require_once dirname(__FILE__) . '/php_tool/template_top.php';
?>
<main>
    <div class="feed-container col-md-7 col-10 p-0">
        <?php
        $req = $db->prepare("SELECT * FROM `posts` INNER JOIN users ON posts.id_user = users.id LIMIT 10");
        $req->execute();
        $posts = $req->fetchAll();
        foreach ($posts as $post) {
            echo "
                <div class='card rounded-0 animated-post'>
                    <div class='card-body'>
                        <div class='d-flex align-items-center'>
                            <img src='/WE4A_project/img/icon/debug.png' width='32' height='32' alt='Avatar' class='mr-2'>
                            <h5 class='card-title m-0'>" . $post['pseudo'] . "</h5>
                        </div>
                        <p>" . $post['content'] . "</p>
                    </div>
                </div>
                ";
        }
        ?>
    </div>
</main>
<?php
require_once dirname(__FILE__) . '/php_tool/template_bot.php';
?>