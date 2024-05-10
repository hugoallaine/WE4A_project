<?php
/**
 * API to manage the logout action
 */
session_start();
unset($_SESSION);
session_destroy();
?>