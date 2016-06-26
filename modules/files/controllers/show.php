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



$controller->id = 7;
$controller->init();



if($core->site_type == 2) {
	$core->lib->loadModuleFiles('shop', 'admin.shop.php');
	$shop = new admin_shop();
	$shop->init();
	$controller->tpl = 'manager.html';
}


$core->ajax->register($core->tpl, 'get', 0, 'tpl');

?>