<?php
$controller->id = 5; 
$controller->cached = 1; 
$controller->cache_param  = array('_site' => 1 , '_lang' => 1 , '_url' => 1, '_uid'=>1 );
$controller->cache_expire = 0;
$controller->init();
$controller->tpl = 'add_form.html';
$controller->cached();

$core->title = 'M-cms Control Panel - '.$core->modules->this['describe'];

$controller->load('users.php', 'system');

$core->tpl->assign('langs', admin_users::get_langs());
$core->tpl->assign('u_sites', admin_users::get_all_sites());
$core->tpl->assign('grouplist', admin_users::get_groups_list());

?>