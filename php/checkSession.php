<?php
require_once dirname(__FILE__).'/alreadyConnected.php';
session_start_secure();

header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $status = isConnected();
    $isAdmin = $_SESSION['isAdmin'] ?? false;
    $pseudo = $status ? $_SESSION['pseudo'] : null;
    echo json_encode(array('error' => false, 'status' => $status, 'pseudo' => $pseudo, 'isAdmin' => $isAdmin));
} else {
    echo json_encode(array('error' => true, 'message' => 'Invalid request method'));
}

?>