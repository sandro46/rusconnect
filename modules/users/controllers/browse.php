<?php
$controller->id = 33; 
$controller->cached = 1; 
$controller->cache_param  = array('_site' => 1 , '_lang' => 1 , '_url' => 1, '_uid'=>1 );
$controller->cache_expire = 5*60;
$controller->init();

$core->title = 'M-cms Control Panel - '.$core->modules->this['describe'];

$grid = intval($_GET['group']);

if($grid)
	{
	$controller->tpl = 'access_list_group.html';
	}
	else
		{
		$controller->tpl = 'access_list.html';
		}

$controller->cached();

$controller->load('users.php', 'system');


if($grid)
	{
	$core->tpl->assign('list', admin_users::get_user_list_by_group($grid));
	$core->tpl->assign('group_list', admin_users::get_groups_list_opt($grid));
	}
	else 
		{
		$core->tpl->assign('list', admin_users::get_user_group_list());
		$core->tpl->assign('group_list', admin_users::get_groups_list_opt(0));
		}
?>