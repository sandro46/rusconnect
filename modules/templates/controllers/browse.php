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



$controller->id = 6; 
$controller->cached = 0; 
$controller->cache_param  = array('_site' => 1 , '_lang' => 1 , '_url' => 1, '_uid'=>1 );
$controller->cache_expire = 0;
$controller->init();
$controller->tpl = 'cached.html';
$controller->cached();

$core->title = 'M-cms Control Panel - '.$core->modules->this['describe'];

$controller->load('templates.php', 'system');
$core->log->access(1, 7);
	
$order  	 = ($_GET['order'])? addslashes($_GET['order']) : 'name';
$order_type  = ($_GET['type'])? addslashes($_GET['type']) : 'desc'; 	

$core->tpl->assign('templates_list',admin_templates::getCached($order, $order_type));

?>