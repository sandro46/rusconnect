<?php
$controller->id = 6; 
$controller->cached = 0; 
$controller->init();
$controller->tpl = 'edit.html';

$core->title = 'M-cms Control Panel - '.$core->modules->this['describe'];

$controller->load('langs.class.php', 'system');

$id = intval($_GET['id']);

if($id)
	{
	$core->tpl->assign('lang_info', admin_system_langs::get_info($id)); 
	}
	else
		{
		$controller->redirect('/'.$core->CONFIG['lang']['name'].'/system_langs/list/', 'Wrong request: Invalid module id.<br>');
		}
?>