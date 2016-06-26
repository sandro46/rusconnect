<?php
$controller->id = 4; 
$controller->cached = 0; 
$controller->init();

$controller->load('modules.php', 'system');

$id_module = intval($_GET['id']);

if(!$id_module)
	{ 
	$controller->redirect('/'.$core->CONFIG['lang']['name'].'/modules/list/', 'The modules have not been removed.<br>Wrong request: Invalid module id.<br>');
	}
	else
		{
		admin_modules::delete_all($id_module);
		$controller->redirect('/'.$core->CONFIG['lang']['name'].'/modules/list/', 'The module has been removed. All rights cleared the table.');
		}


?>