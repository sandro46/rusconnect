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
$controller->cache_expire = 30;
$controller->init();
$controller->tpl = 'list.html';
$controller->cached();

//Default page title for all admin modules
if ($core->site_name == $core->getAdminModule()) {
	$core->title = 'M-cms Control Panel - '.$core->modules->this['describe'];
} else {
	$core->title = $core->modules->this['describe'];
}

$core->tpl->assign('navigation', $root->getNavigationHTML());
$core->tpl->assign('wordslist', $root->getData());
//print_r($data);

// OLD CODE
////Extended module class (files location in: /module_dir/class/className.php)
//include $core->CONFIG['module_dir'].$core->modules->this['uri']['class'].'glossary.class.php';
//
//$url = $core->CONFIG['lang']['name'].'/'.$core->module_name;
//$url1 = $url.'/'.$controller->real_name.'/filtr';
//
//$limit = (intval($_GET['limit']))? intval($_GET['limit']):10;
//$page  = intval($_GET['page']);
//$order = addslashes($_GET['order']);
//$order_type = $_GET['type'];
//
//$class = new Glossary();
//
//$core->tpl->assign('url',$url1);
//$core->tpl->assign('list',$class->getList());
//$fl = iconv('utf-8','windows-1251',$class->getFiltr());
//$core->tpl->assign('filtr',urlencode($fl));
//$core->tpl->assign('filtr_str',$fl);
//$core->tpl->assign('url1',$url);
//
//print "<pre>";
////print_r($core);
////print_r($controller);
//print "</pre>";
// /OLD CODE

?>