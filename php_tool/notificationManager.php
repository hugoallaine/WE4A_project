<?php
require_once dirname(__FILE__).'/alreadyConnected.php';
session_start_secure();
require_once dirname(__FILE__).'/db.php';

class NotificationManager {
    private $db;
    function __construct() {
        global $db;
        $this->db = $db;
    }

    function getNotifications($id_user, $nb_only = false, $is_read = 0, $is_delete = 0) {
        if ($is_read == 2) {
            $req = $this->db->prepare('SELECT * FROM notifications WHERE user_id = ? AND is_delete = 0 ORDER BY created_at DESC');
            $req->execute(array(SecurizeString_ForSQL($id_user)));
        } else {
            $req = $this->db->prepare('SELECT * FROM notifications WHERE user_id = ? AND is_read = ? AND is_delete = ? ORDER BY created_at DESC');
            $req->execute(array(SecurizeString_ForSQL($id_user), SecurizeString_ForSQL($is_read), SecurizeString_ForSQL($is_delete)));
        }
        if ($nb_only) {
            return $req->rowCount();
        }
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    function readNotifications($id_user) {
        $req = $this->db->prepare('UPDATE notifications SET is_read = 1 WHERE user_id = ?');
        $req->execute(array(SecurizeString_ForSQL($id_user)));
    }

    function deleteNotification($id_user, $id_notification) {
        $req = $this->db->prepare('UPDATE notifications SET is_delete = 1 WHERE id = ? AND user_id = ?');
        $req->execute(array(SecurizeString_ForSQL($id_notification), SecurizeString_ForSQL($id_user)));
    }

    function deleteOldNotifications() {
        $req = $this->db->prepare('UPDATE notifications SET is_delete = 1 WHERE is_read = 1 AND created_at < NOW() - INTERVAL 2 WEEK');
        $req->execute();
    }
}

$notificationManager = new NotificationManager();
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_SESSION['id'])) {
        if (isset($_GET['count']) && $_GET['count'] == true) {
            $res = $notificationManager->getNotifications($_SESSION['id'], true);
            header('Content-Type: application/json');
            echo json_encode(array('count' => $res));
        } else {
            $res = $notificationManager->getNotifications($_SESSION['id']);
        }
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['idNotification']) && isset($_SESSION['id'])) {
        $notificationManager->deleteNotification($_SESSION['id'], $_POST['idNotification']);
    } elseif (isset($_SESSION['id'])) {
        $notificationManager->readNotifications($_SESSION['id']);
    }
}
?>