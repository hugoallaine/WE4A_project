<?php
require_once dirname(__FILE__).'/php_tool/alreadyConnected.php';
session_start_secure();
redirectIfNotConnected();

require_once dirname(__FILE__).'/php_tool/parser.php';
require_once dirname(__FILE__).'/php_tool/notificationManager.php';
$notifications = $notificationManager->getNotifications($_SESSION['id'], false, 1);

$currentPage = 'Notifications';

require_once dirname(__FILE__).'/php_tool/template_top.php';
?>
<main>
    <div class="notifications-container col-12 p-0 overflow-auto vh-100 d-flex align-items-center flex-column">
        <h1 class="text-primary p-3">Notifications</h1>
        <?php
            foreach ($notifications as $notification) {
                $date = date_create_from_format('Y-m-d H:i:s', $notification['created_at']);
                $formatted_date = $date->format('d/m/Y H:i:s');
                $content = parsePseudoForProfile($notification['content']);

                echo "<div class='card col-lg-8 col-md-12 mb-1'>
                        <div class='card-body d-flex justify-content-between align-items-center'>
                            <div>
                                <h5 class='card-title'>".$formatted_date."</h5>
                                <p class='card-text'>".$content."</p>
                            </div>
                            <div>";
                            if ($notification['type'] == 'follow') {
                                echo "<img src='img/icon/amis.png' alt='follow' class='icon' width=50 height=50>";
                            } else if ($notification['type'] == 'like') {
                                echo "<img src='img/icon/like.png' alt='like' class='icon' width=50 height=50>";
                            } else if ($notification['type'] == 'comment') {
                                echo "<img src='img/icon/response.png' alt='comment' class='icon' width=50 height=50>";
                            } else if ($notification['type'] == 'post') {
                                echo "<img src='img/icon/messages.png' alt='post' class='icon' width=50 height=50>";
                            }
                echo        "</div>
                        </div>
                    </div>";
            }
        ?>
    </div>
</main>
<?php
require_once dirname(__FILE__).'/php_tool/template_bot.php';
?>