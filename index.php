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
            <div class="col-lg-8 col-md-12 p-0 vh-100 overflow-auto" id="posts-container">
                <!-- Posts -->
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
                    <!-- Responses -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?php
require_once dirname(__FILE__).'/php_tool/template_bot.php';
?>