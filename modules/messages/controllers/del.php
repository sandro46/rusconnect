<?php
$controller->id = 4; 
$controller->cached = 0; 
$controller->init();

$controller->load('messages.php', 'system');

$id_message = intval($_GET['id']);

$core->log->access(1, 15);

if(!$id_message)
	{ 
	$controller->redirect('/'.$core->CONFIG['lang']['name'].'/messages/list/', 'The action have not been removed.<br>Wrong request: Invalid module id.<br>');
	}
	else
		{
		admin_messages::delete($id_message);
		$controller->redirect('/'.$core->CONFIG['lang']['name'].'/messages/list/', 'The module has been removed. All rights cleared the table.');
		}
?>