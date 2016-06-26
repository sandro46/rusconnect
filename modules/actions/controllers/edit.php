<?php
$controller->id = 6; 
$controller->cached = 0; 
$controller->init();
$controller->tpl = 'edit.html';

$core->title = 'M-cms Control Panel - '.$core->modules->this['describe'];

$id_action = intval($_GET['id']);

if(!$id_action)
	{ 
	$controller->redirect('/'.$core->CONFIG['lang']['name'].'/actions/list/', 'Wrong request: Invalid action id.<br>');
	}


$controller->load('actions.php', 'system');

$core->tpl->assign('id_action', $id_action);
$core->tpl->assign('langs', admin_actions::get_langs_ussign_action_info($id_action));
?>