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



$controller->id = 24;
$controller->cached = 0;
$controller->cache_param  = array('_site' => 1 , '_lang' => 1 , '_url' => 1, '_uid'=>1 );
$controller->cache_expire = 30;
$controller->init();
$controller->tpl = '';
$controller->cached();

//Default page title for all admin modules
$core->title = 'M-cms Control Panel - '.$core->modules->this['describe'];

if ($root->save()) {
	$controller->redirect('/'.$core->CONFIG['lang']['name'].'/'.$core->module_name.'/list/', 'All data saved.');
} else {
	$controller->redirect('/'.$core->CONFIG['lang']['name'].'/'.$core->module_name.'/list/', 'Some error occured.');
}

?>