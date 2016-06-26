<?php
$controller->id = 5; 
$controller->cached = 0; 
$controller->init();
$controller->tpl = 'edit.html';

$core->title = 'M-cms Control Panel - '.$core->modules->this['describe'];

$controller->load('modules.php', 'system');
$controller->load('creater.php', 'system');
$controller->load('ajax_ext.php', 'system');

$id_module = 0;

$core->lib->load('ajax');

$ajax = new ajax();

$ajax->debug_mode = 0;
$ajax->request_type = 'POST';
$ajax->add_func('get_addControllerHtml', 'addNewController', 'get_controllerPreviewData', 'delete_controller');
$ajax->init();

$ajax->user_request();

// FIXME
$core->tpl->assign('cl_list', $_SESSION['controllers']);

$core->tpl->assign('ajax_output', $ajax->output);
$core->tpl->assign('modules_types', admin_modules::get_modules_types());
$core->tpl->assign('modules_sites', admin_modules::get_modules_sites());
$core->tpl->assign('id_module', $id_module);
$core->tpl->assign('langs', admin_modules::get_langs());




?>