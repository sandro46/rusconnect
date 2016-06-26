<?php
$controller->id = 24; 
$controller->cached = 0; 
$controller->init();

$controller->load('modules.php', 'system');
$controller->load('creater.php', 'system');

$id_module = intval($_GET['id']);

$modules = new admin_modules();


//print_r($_POST);

if(!$id_module) $modules->add();
else $modules->edit($id_module);
	

$controller->redirect('/'.$core->CONFIG['lang']['name'].'/modules/list/', 'All data saved.<br><br>Recommended update lists of access rights to the plug or it will not appear in the left-hand menu.<br>');

?>