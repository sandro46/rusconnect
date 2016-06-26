<?php
$controller->id = 1; 
$controller->cached = 0; 
$controller->cache_param  = array('_site' => 1 , '_lang' => 1 , '_url' => 1, '_uid'=>1 );
$controller->cache_expire = 5*60;
$controller->init();
$controller->tpl = 'list.html';
$controller->cached();

$core->title = 'M-cms Control Panel - '.$core->modules->this['describe'];

$controller->load('modules.php', 'system');
$controller->load('ajax_ext.php', 'system');
$core->lib->load('installer');
$core->lib->load('ajax');

$limit =  (intval($_GET['limit']))? intval($_GET['limit']):10;
$page  =  intval($_GET['page']);
$order = addslashes($_GET['order']);
$order_type = $_GET['type'];

$pagenav_extra  = $core->CONFIG['lang']['name'].'/'.$core->module_name.'/list/limit/'.$limit.'/';


$ajax = new ajax();
$ajax->debug_mode = 0;
$ajax->request_type = 'POST';
$ajax->add_func('createInstallPackage', 'createAllInstallPackage');
$ajax->init();
$ajax->user_request();

$core->tpl->assign('ajax_output', $ajax->output);
$core->tpl->assign('list_modules', admin_modules::get_list($page, $limit, $order, $order_type));
$core->tpl->assign('pagenav', pagenav(admin_modules::get_total(), $limit, $page, "page", $pagenav_extra));
?>