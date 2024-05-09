<?php 
require_once dirname(__FILE__).'/php/alreadyConnected.php';
redirectIfNotConnected();

$currentPage = 'Admin';

require_once dirname(__FILE__).'/php/template_top.php';
?>
<main>
    <h1>Admin</h1>
    <p>Vous êtes connecté en tant qu'administrateur.</p>

</main>
<?php
require_once dirname(__FILE__).'/php/template_bot.php';
?>