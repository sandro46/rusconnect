<?php
############################################################################
#          This controller was created automatically core system           #
#                                                                          #
# ------------------------------------------------------------------------ #
# @Creator module version 1.2598 b                                         #
# @Author: Alexey Pshenichniy                                              #
# ------------------------------------------------------------------------ #
# Alpari CMS v.1 Beta   $17.06.2008                                        #
############################################################################


$controller->id = 1;
$controller->cached = 0;
$controller->cache_param  = array('_site' => 1 , '_lang' => 1 , '_url' => 1, '_uid'=>1 );
$controller->cache_expire = 0;
$controller->init();

$core->title = 'M-cms Control Panel - '.$core->modules->this['describe'];

$controller->load('sites_control.php', 'system');


$act = $_GET['act'];

switch($act)
{
	case 'disable':
		$site = new sites_control();
		$site->delete_module(intval($_GET['id']));
		
		$controller->redirect('/'.$core->CONFIG['lang']['name'].'/sites_control/sites_modules/', 'The module has been removed.');
	break;
	
	case 'add':
		
	break;
	
	case 'add_save':
		$site = new sites_control();
		$site->add_module(intval($_GET['id']));
		
		$controller->redirect('/'.$core->CONFIG['lang']['name'].'/modules/list/', 'The module has been added.');
	break;
	
	default:
		$controller->tpl = 'site_modules.html';
		
		$site = new sites_control();
		$site->get_sites_modules_list();
		
		$core->tpl->assign('sites_list_use_module', $site->list);	
	break;
}


$core->lib->load('ajax');
	
$ajax = new ajax();
$ajax->debug_mode = 0;
$ajax->request_type = 'POST';
$ajax->add_func('add_module_in_site','get_modules_list');
$ajax->init();
$ajax->user_request();
$core->tpl->assign('ajax_output', $ajax->output);




?>