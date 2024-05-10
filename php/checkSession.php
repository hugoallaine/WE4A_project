<?php
require_once dirname(__FILE__).'/alreadyConnected.php';
session_start_secure();

/**
 * API to check the session status
 * 
 *  Response:
 * - error (boolean): true if an error occured
 * - message (string): the error message
 * - status (boolean): true if the user is connected
 * - pseudo (string): the pseudo of the user
 * - isAdmin (boolean): true if the user is an admin
 */
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