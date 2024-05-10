<?php
require_once dirname(__FILE__).'/php/alreadyConnected.php';
session_start_secure();
redirectIfNotConnected();

require_once dirname(__FILE__).'/php/parser.php';
require_once dirname(__FILE__).'/php/notificationManager.php';
$notificationManager->deleteOldNotifications();
$notifications = $notificationManager->getNotifications($_SESSION['id'], false, 2);

$currentPage = 'Notifications';

require_once dirname(__FILE__).'/php/template_top.php';
?>
<main>
    <div class="notifications-container col-12 p-0 overflow-auto vh-100 d-flex align-items-center flex-column">
        <h1 class="text-primary p-3">Notifications</h1>
        <?php
        if (!empty($notifications)) {
            foreach ($notifications as $notification) {
                $id = $notification['id'];
                $date = date_create_from_format('Y-m-d H:i:s', $notification['created_at']);
                $formatted_date = $date->format('d/m/Y H:i:s');
                $content = parsePseudoForProfile(RestoreString_FromSQL($notification['content']));
                if ($notification['type'] != 'follow') {
                    $req = $db->prepare('SELECT content FROM posts WHERE id = ?');
                    $req->execute(array($notification['id_post']));
                    $class = 'post';
                    $idpost = $notification['id_post'];
                    $post = "<p class='card-text'>".RestoreString_FromSQL($req->fetch()['content'])."</p>";
                } else {
                    $class = '';
                    $post = "";
                }
                if ($notification['is_read'] == 0) {
                    $new = "<span class='badge text-bg-danger ms-2'>Nouveau</span>";
                } else {
                    $new = "";
                }

                echo "<div class='card col-lg-8 col-md-12 mb-1' data-notification-id='".$id."'>";
                echo    "<div class='card-body d-flex justify-content-between align-items-center row'>
                            <div class='d-flex align-items-center me-0 col-1'>";
                            if ($notification['type'] == 'follow') {
                                echo "<img src='img/icon/amis.png' alt='follow' class='icon' width=50 height=50>";
                            } else if ($notification['type'] == 'like') {
                                echo "<img src='img/icon/like.png' alt='like' class='icon' width=50 height=50>";
                            } else if ($notification['type'] == 'comment') {
                                echo "<img src='img/icon/response.png' alt='comment' class='icon' width=50 height=50>";
                            } else if ($notification['type'] == 'new_post_follower') {
                                echo "<img src='img/icon/messages.png' alt='post' class='icon' width=50 height=50>";
                            } else if ($notification['type'] == 'warning') {
                                echo "<img src='img/icon/warning.png' alt='warning' class='icon' width=50 height=50>";
                            } else if ($notification['type'] == 'shock') {
                                echo "<img src='img/icon/hide.png' alt='shock' class='icon' width=50 height=50>";
                            } else if ($notification['type'] == 'delete') {
                                echo "<img src='img/icon/delete.png' alt='delete' class='icon' width=50 height=50>";
                            } else if ($notification['type'] == 'ban') {
                                echo "<img src='img/icon/ban.png' alt='ban' class='icon' width=50 height=50>";
                            } 
            echo            "</div>
                            <div class='".$class." col' data-post-id='".$idpost."' style='cursor: pointer;'>
                                <h5 class='card-title'>".$content."</h5>
                                ".$post."
                                <p class='card-text'>".$formatted_date.$new."</p>
                            </div>
                            <div class='col-2'>
                                <a href='#' class='delete-btn btn btn-danger'>Supprimer</a>
                            </div>
                        </div>
                    </div>";
            }
        } else {
            echo "<div class='d-flex justify-content-center align-items-center h-75'>
                    <h2 class='text-center'>Aucune notification</h2>
                </div>";
        }
        ?>
    </div>
    <!-- Modal delete notification -->
    <div class="modal fade" id="deleteNotificationModal" tabindex="-1" aria-labelledby="deleteNotificationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteNotificationModalLabel">Supprimer la notification</h5>
                </div>
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir supprimer cette notification ?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-danger" id="deleteNotificationBtn">Supprimer</button>
                </div>
            </div>
        </div>
    </div>


</main>
<?php
require_once dirname(__FILE__).'/php/template_bot.php';
?>