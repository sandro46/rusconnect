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
$controller->tpl = 'list.html';
$controller->cached();


$core->title = 'M-cms Control Panel - '.$core->modules->this['describe'];

$controller->load('sites_control.php');

$core->tpl->assign('sites_list', sites_control::get_list());


?>