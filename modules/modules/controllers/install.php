<?php
$controller->id = 34; 
$controller->cached = 0; 
$controller->cache_param  = array('_site' => 1 , '_lang' => 1 , '_url' => 1, '_uid'=>1 );
$controller->cache_expire = 5*60;
$controller->init();
$controller->cached();

$controller->load('modules.php', 'system');

$class = new admin_modules();

$class->install();

$controller->redirect('/'.$core->CONFIG['lang']['name'].'/modules/list_modules/', 'All changes have been saved.');
?>