<?php
$controller->id = 1; 
$controller->cached = 0; 
$controller->cache_param  = array('_site' => 1 , '_lang' => 1 , '_url' => 1, '_uid'=>1 );
$controller->cache_expire = 5*60;
$controller->init();
$controller->tpl = 'list.html';
$controller->cached();

$core->title = 'M-cms Control Panel - '.$core->modules->this['describe'];

$controller->load('messages.php', 'system');

$limit =  (intval($_GET['limit']))? intval($_GET['limit']):90000;
$page  =  intval($_GET['page']);
$order = addslashes($_GET['order']);
$order_type = $_GET['type'];
$filter = ($_GET['filter'])?urldecode($_GET['filter']) : 0;



//$pagenav_extra  = $core->CONFIG['lang']['name'].'/'.$core->module_name.'/'.$core->controller.'/limit/'.$limit.'/filter/'.$filter.'/';

$core->tpl->assign('list_messages', admin_messages::get_list($page, $limit, $order, $order_type, strtolower($filter)));
//$core->tpl->assign('pagenav', pagenav(admin_messages::get_total(strtolower($filter)), $limit, $page, "page", $pagenav_extra));
//$core->tpl->assign('pages_limit', $limit);

//if($filter) $core->tpl->assign('filter_messages', $filter);
$core->log->access(1, 14);

?>