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



$controller->id = 36;
$controller->cached = 0;
$controller->cache_param  = array('_site' => 1 , '_lang' => 1 , '_url' => 1, '_uid'=>1 );
$controller->cache_expire = 30;
$controller->init();
$controller->tpl = 'list.html';
$controller->cached();

//Default page title for all admin modules
$core->title = 'M-cms Control Panel - '.$core->modules->this['describe'];

$module_name = addslashes($_GET['modulename']);

if ($module_name == '') $module_name = 'templates';


$controller->load('history_common.php', 'system');

$history = new history_common();


$core->tpl->assign('list-modules', $history->get_modules_list());
$core->tpl->assign('module-name', $module_name);

$selected_module = "modulename/{$module_name}";


(isset($_GET['order'])) ? $order['by'] = addslashes($_GET['order']) : $order['by'] = 'date';
(isset($_GET['type'])) ? $order['type'] = addslashes($_GET['type']) : $order['type'] = 'DESC';

if (isset($_GET['key']) && isset($_GET['value'])) 
	{
	$list = $core->history->getSpecificList(array(addslashes($_GET['key']), intval($_GET['value'])), $module_name, $order);
	$selected_module .= '/key/'.addslashes($_GET['key']).'/value/'.intval($_GET['value']);
	$core->tpl->assign('specific_pair', TRUE);
	} 
	else 
		{
		$list = $core->history->getList($module_name,$order);
		}
		
$core->tpl->assign('selected_module', $selected_module);
$core->tpl->assign('array', $list);

?>