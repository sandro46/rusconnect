<?php
$controller->id = 5; 
$controller->cached = 0; 
$controller->init();
$controller->tpl = 'edit.html';

$core->title = 'M-cms Control Panel - '.$core->modules->this['describe'];

$controller->load('groups.php', 'system');

$gr_id = 0;


$core->tpl->assign('sites_access', admin_groups::get_sites_access(0));
$core->tpl->assign('group_id', $gr_id);
$core->tpl->assign('langs', admin_groups::get_langs_use_group_name($gr_id));
$core->tpl->assign('gr_modules', admin_groups::get_module_list($gr_id));	
?>