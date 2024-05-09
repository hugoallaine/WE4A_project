<?php
require_once dirname(__FILE__).'/php/alreadyConnected.php';
require_once dirname(__FILE__).'/php/db.php';
require_once dirname(__FILE__).'/php/postManager.php';
session_start_secure();

$currentPage = 'Accueil';

require_once dirname(__FILE__).'/php/template_top.php';
?>
<main>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-9 col-md-12 p-0 vh-100 overflow-auto" id="posts-container">
                <!-- Posts -->
            </div>
            <div class="col-lg-3 p-0 bg-light">
                
            </div>
        </div>
    </div>
</main>
<?php
require_once dirname(__FILE__).'/php/template_bot.php';
?>