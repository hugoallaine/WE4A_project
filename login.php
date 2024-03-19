<?php
session_start();
require_once(dirname(__FILE__).'/php_tool/alreadyConnected.php');

if (isset($_SESSION['email']) && (!isset($_SESSION['id']))) {
    require_once(dirname(__FILE__).'/php_tool/2FA_login.php');
} else {
    require_once(dirname(__FILE__).'/php_tool/main_login.php');
}
?>