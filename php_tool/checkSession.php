<?php
require_once dirname(__FILE__).'/alreadyConnected.php';
session_start_secure();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $status = isConnected();
    header('Content-Type: application/json');
    echo json_encode(array('error' => false,'status' => $status));
} else {
    header('Content-Type: application/json');
    echo json_encode(array('error' => true,'message' => 'Invalid request method'));
}

?>