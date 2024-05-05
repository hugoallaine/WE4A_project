<?php
require_once dirname(__FILE__).'/php_tool/alreadyConnected.php';
require_once dirname(__FILE__).'/php_tool/db.php';
require_once dirname(__FILE__).'/php_tool/postManager.php';
session_start_secure();

$currentPage = 'Accueil';

require_once dirname(__FILE__).'/php_tool/template_top.php';
?>
<main>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-9 col-md-12 p-0 vh-100 overflow-auto" id="posts-container">
                <!-- Posts -->
            </div>
            <!-- <div class="col-lg-4 p-0">
                <h5>Statistiques</h5>
            </div> -->
        </div>
        
    </div>
</main>
<?php
require_once dirname(__FILE__).'/php_tool/template_bot.php';
?>