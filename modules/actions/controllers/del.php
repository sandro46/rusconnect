<?php
$controller->id = 4; 
$controller->cached = 0; 
$controller->init();


$controller->load('actions.php', 'system');

$id_action = intval($_GET['id']);

if(!$id_action)
	{ 
	$controller->redirect('/'.$core->CONFIG['lang']['name'].'/actions/list/', 'The action have not been removed.<br>Wrong request: Invalid module id.<br>');
	}
	else
		{
		admin_actions::delete_all($id_action);
		$controller->redirect('/'.$core->CONFIG['lang']['name'].'/actions/list/', 'The module has been removed. All rights cleared the table.');
		}
?>