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
$controller->tpl = 'trigers.html';
$controller->cached();

$core->title = 'M-cms Control Panel - '.$core->modules->this['describe'];

$controller->load('trigers.php', 'system');

$trig = new trigers();


switch($_GET['act'])
{
	case 'del':
	    $trig->auto_added = 1;
		$trig->drop_trigger(addslashes($_GET['trigger']));
		$controller->redirect('/'.$core->CONFIG['lang']['name'].'/sites_control/trigers/', 'Тригер был удален');
	break;
	
	case 'replace':
		$trig->replace_all_triger();
		$controller->redirect('/'.$core->CONFIG['lang']['name'].'/sites_control/trigers/', 'Все тригеры были перезаписаны');
	break;
	
	case 'del_all':
	    $trig->auto_added = 0;
	    $trig->replace_all_triger();
		$controller->redirect('/'.$core->CONFIG['lang']['name'].'/sites_control/trigers/', 'Все тригеры были перезаписаны');
	break;

	default:
		$trig->check_all_triggers();
		$core->tpl->assign('ignor_tables', $trig->ignor_tables);
		$core->tpl->assign('bases_list', $trig->bases_list);
		$core->tpl->assign('triger_status', $trig->triger_status);
	break;
}

?>