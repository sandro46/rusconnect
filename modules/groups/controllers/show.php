<?php
$controller->id = 1; 
$controller->cached = 0; 
$controller->cache_param  = array('_site' => 1 , '_lang' => 1 , '_url' => 1, '_uid'=>1 );
$controller->cache_expire = 5*60;
$controller->init();
$controller->tpl = 'show.html';
$controller->cached();

$core->title = 'M-cms Control Panel - '.$core->modules->this['describe'];

$controller->load('groups.php', 'system');

$gr_id = intval($_GET['id']);

if($gr_id)
	{
	$core->tpl->assign('gr_module_list', admin_groups::get_access_list_optimize($gr_id));		
	$core->tpl->assign('group_users', admin_groups::get_user_list_by_group($gr_id));	
	$core->tpl->assign('langs', admin_groups::get_langs_use_group_name($gr_id));
	}	
	else
		{
		$controller->redirect('/'.$core->CONFIG['lang']['name'].'/groups/list/', 'Bad request url.');
		}

?>