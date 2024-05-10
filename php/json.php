<?php
/**
 * Get the configuration from the private.json file
 */
$config_json = file_get_contents(dirname(__FILE__).'/../private.json');
$json = json_decode($config_json, true);
?>