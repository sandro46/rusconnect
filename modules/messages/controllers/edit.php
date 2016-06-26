<?php
$controller->id = 6; 
$controller->cached = 0; 
$controller->init();
$controller->tpl = 'edit.html';

$core->title = 'M-cms Control Panel - '.$core->modules->this['describe'];

$id_message = intval($_GET['id']);

if(!$id_message)
	{ 
	$controller->redirect('/'.$core->CONFIG['lang']['name'].'/messages/list/', 'Wrong request: Invalid action id.<br>');
	}

$controller->load('messages.php', 'system');

$core->tpl->assign('id_message', $id_message);
$core->tpl->assign('langs', admin_messages::get_langs_ussign_message_info($id_message));
?>