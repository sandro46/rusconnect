<?php
################################################################################
# This file was created by M-cms core.                                         #
# If you want create a new controller files,                                   #
# look at modules section in admin interface.                                  #
#                                                                              #
# If you want modify this header, look at /modules/modules/class/modules.php   #
# ---------------------------------------------------------------------------- #
# In this controlle you can use all core api through $core variable            #
# also there is other components api:                                          #
#     $controller = Controller object. Look at /classes/controllers.php        #
#     $ajax = Ajax api object. Look as /classes/ajax.php                       #
# In this file you must to specify the action id and set cached flag           #
# and call ini method.                                                         #
# If you can use template in this controole, please specify variable "tpl".    #
# Example:                                                                     #
#     $controller->id = 1; Controller action id = 1. Look at database.         #
#     $controller->cached = 0; Cache system is off                             #
#     $controller->init(); Call controller initiated method                    #
#     $controller->tpl = 'filename'; Template name.                            #
# You can specify the template in any line of controller, but                  #
# if you want to use caching, you must specify the template to call            #
# the method of checking the cache.                                            #
# If you can break controoler logic, to call $core->footer()                   #
# If you need help, look at api documentation.                                 #
# ---------------------------------------------------------------------------- #
# @Core: 5.001                                                                 #
# @Author: Alexey Pshenichniy <inc.mcms@gmail.com> <M-zone Inc.> 2005-2009     #
# @Date: 28.03.2010                                                            #
# ---------------------------------------------------------------------------- #
# M-cms v5.0 (core build - 5.001)                                              #
################################################################################

$controller->id = 1; 
$controller->cached = 0; 
$controller->cache_param  = array('_site' => 1 , '_lang' => 1 , '_url' => 1, '_uid'=>1 );
$controller->cache_expire = 2*60;
$controller->init();
$controller->tpl = 'test.html';
$controller->cached();

$core->title = 'Управление шаблонами';
$controller->load('templates.php');
$root = new ModuleTemplates();
$root->setDefaultTheme();




/*
$core->ajax->register($root,'getList',1);
$core->ajax->register($root,'getSource',1); 
$core->ajax->register($root,'updateSource',1); 
$core->ajax->register($root,'remove',1); 

$core->ajax->listen();
$site_id = (isset($_SESSION['templates_site_id']))? $_SESSION['templates_site_id'] : $core->edit_site;

$core->tpl->assign('current_templates_site_id', $site_id);
$core->tpl->assign('default_theme_edit', $root->defaultTheme);
$core->tpl->assign('thems_list',$root->getAllThems());
$core->tpl->assign('modules_list', $root->getAllModules());	

*/

//print_r($root);


?>
