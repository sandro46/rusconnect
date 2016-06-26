<?php
$controller->id = 1; 
$controller->cached = 0; 
$controller->cache_param  = array('_site' => 1 , '_lang' => 1 , '_url' => 1, '_uid'=>1 );
$controller->cache_expire = 5*60;
$controller->init();
$controller->tpl = 'list.html';
$controller->cached();

$core->title = 'M-cms Control Panel - '.$core->modules->this['describe'];

$controller->load('actions.php', 'system');


$limit =  (intval($_GET['limit']))? intval($_GET['limit']):20;
$page  =  intval($_GET['page']);
$order = addslashes($_GET['order']);
$order_type = $_GET['type'];

$pagenav_extra  = $core->CONFIG['lang']['name'].'/'.$core->module_name.'/'.$core->controller.'/limit/'.$limit.'/';

$core->tpl->assign('list_actions_page', $page);
$core->tpl->assign('list_actions_limit', $limit);
$core->tpl->assign('list_actions', admin_actions::get_list($page, $limit, $order, $order_type));
$core->tpl->assign('pagenav', pagenav(admin_actions::get_total(), $limit, $page, "page", $pagenav_extra));




?>