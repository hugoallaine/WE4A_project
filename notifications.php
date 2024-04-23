<?php
require_once dirname(__FILE__).'/php_tool/alreadyConnected.php';
session_start_secure();
redirectIfNotConnected();

$currentPage = 'Notifications';

require_once dirname(__FILE__).'/php_tool/template_top.php';

$req = $db->prepare("SELECT * FROM notifications WHERE user_id = ?");
$req->execute([$_SESSION['id']]);
$notifications = $req->fetchAll();

?>
<main>
    <div class="notifications-container col-12 p-0 overflow-auto vh-100 d-flex align-items-center flex-column">
        <h1>Notifications</h1>
        <?php
            foreach ($notifications as $notification) {

                $date = date_create_from_format('Y-m-d H:i:s', $notification['created_at']);
                $formatted_date = $date->format('d/m/Y H:i:s');

                echo "<div class='card col-lg-8 col-md-12'>
                    <div class='card-body'>
                        <h5 class='card-title'>".$formatted_date."</h5>
                        <p class='card-text'>".$notification['content']."</p>
                    </div>
                </div>";
            }
        ?>
    </div>
</main>
<?php
require_once dirname(__FILE__).'/php_tool/template_bot.php';
?>