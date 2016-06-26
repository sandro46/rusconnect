<?php
$controller->id = 1; 
$controller->cached = 0; 
$controller->cache_param  = array('_site' => 1 , '_lang' => 1 , '_url' => 1, '_uid'=>1 );
$controller->cache_expire = 5*60;
$controller->init();
$controller->tpl = 'list.html';
$controller->cached();

$core->title = 'Редактор прав доступа';

$controller->load('groups.php');

$root = new ModuleGroups();
$root->init();

$core->tpl->assign('current_group_site_id', $root->site_id);

$core->ajax->register($root, 'getTree', 1); 
$core->ajax->register($root, 'getModulesListExcludeGroup', 1);
$core->ajax->register($root, 'getControllersListExcludeId', 1);
$core->ajax->register($root, 'addModuleInGroup', 1);
$core->ajax->register($root, 'addControllerInModule', 1);
$core->ajax->register($root, 'addNewGroup', 1);
$core->ajax->register($root, 'deleteController', 4);
$core->ajax->register($root, 'deleteModule', 4);
$core->ajax->register($root, 'deleteGroup', 4);
$core->ajax->register($root, 'copyController', 1);
$core->ajax->register($root, 'copyModule', 1);
$core->ajax->register($root, 'setSiteId', 1);
$core->ajax->listen();


?>