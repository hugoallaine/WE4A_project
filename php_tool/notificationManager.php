<?php
require_once dirname(__FILE__).'/alreadyConnected.php';
session_start_secure();
require_once dirname(__FILE__).'/db.php';

class Notification {
    function __construct($id_user, $type, $content) {
        $this->id_user = $id_user;
        $this->type = $type;
        $this->content = $content;
    }
}

class NotificationManager {
    private $db;
    function __construct() {
        global $db;
        $this->db = $db;
    }

    function getNotifications($id_user, $nb_only = false) {
        $req = $this->db->prepare('SELECT * FROM notifications WHERE user_id = ? AND is_read = 0 ORDER BY created_at DESC');
        $req->execute(array($id_user));
        if ($nb_only) {
            return $req->rowCount();
        }
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    function readNotifications($id_user) {
        $req = $this->db->prepare('UPDATE notifications SET is_read = 1 WHERE user_id = ?');
        $req->execute(array($id_user));
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
    if (isset($_SESSION['id'])) {
        $notificationManager->readNotifications($_SESSION['id']);
    }
}
?>