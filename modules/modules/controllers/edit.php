<?php
$controller->id = 6; 
$controller->cached = 0; 
$controller->init();
$controller->tpl = 'edit.html';

$core->title = 'M-cms Control Panel - '.$core->modules->this['describe'];

$controller->load('modules.php', 'system');

$id_module = intval($_GET['id']);

if(!$id_module)
	{ 
	$controller->redirect('/'.$core->CONFIG['lang']['name'].'/modules/list/', 'Wrong request: Invalid module id.<br>');
	}

$core->tpl->assign('id_module', $id_module);
$core->tpl->assign('moduele_info', admin_modules::get_info($id_module));
$core->tpl->assign('langs', admin_modules::get_langs_ussign_module_info($id_module));
$core->tpl->assign('modules_types', admin_modules::get_modules_types());
$core->tpl->assign('modules_sites', admin_modules::get_modules_sites());
?>