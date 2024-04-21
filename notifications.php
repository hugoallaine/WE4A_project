<?php
require_once dirname(__FILE__).'/php_tool/alreadyConnected.php';
session_start_secure();
redirectIfNotConnected();

$currentPage = 'Notifications';

require_once dirname(__FILE__).'/php_tool/template_top.php';
?>
<main>

</main>
<?php
require_once dirname(__FILE__).'/php_tool/template_bot.php';
?>

