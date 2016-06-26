<?php
$controller->id = 1; 
$controller->cached = 0; 
$controller->cache_param  = array('_site' => 1 , '_lang' => 1 , '_url' => 1, '_uid'=>1 );
$controller->cache_expire = 5*60;
$controller->init();
$controller->tpl = 'list.html';
$controller->cached();

$core->title = 'M-cms Control Panel - '.$core->modules->this['describe'];


$core->tpl->assign('list_langs', $core->get_all_langs());
?>