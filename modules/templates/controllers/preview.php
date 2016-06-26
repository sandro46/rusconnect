<?php
$controller->id = 22; 
$controller->cached = 0; 
$controller->init();

$controller->load('templates.php', 'system');

$id_template = intval($_GET['id_template']);

if(!$id_template)
	$controller->redirect('/templates/list/', 'Ты кто?');

$templates = new admin_templates();

$template_info = $templates->get_tpl_info($id_template);

if(!$template_info)
	$controller->redirect('/templates/list/', 'Такого шаблона не существует.<br>Может <b>пока</b> не существует?');
	

//$core->module_name = $template_info['name_module'];	

$content = $core->tpl->fetch($template_info['name'], 1, 0,0, $template_info['name_module']);
	
$core->tpl->assign('content', $content);	
$core->tpl->assign('langs', $core->get_all_langs());

$core->module_name = $core->site_name;
$core->tpl->display('main.html');
die();
	






?>