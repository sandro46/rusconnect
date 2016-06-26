<?php
################################################################################
# This file was created by M-cms core.                                         #
#                                                                              #
# This module configuration file.                                              #
# There you can add entry in the admin menu, specify which of the existing     #
# can use controllers. You can also specify the parameters of caching          #
# and specify template will be used.                                           #
# If in process call module does not find this file, it will be called 404.    #
#                                                                              #
# If you want to use any controller, you'll need to specify                    #
# in $controller->add_controller();                                            #
# Also, you can specify the default controller. This controller will be        #
# called if the URL is not specified controller or if specified controller     #
# was not found or not connected.                                              #
#                                                                              #
# ---------------------------------------------------------------------------- #
# @Author: Alexey Pshenichniy <inc.mcms@gmail.com> <M-zone Inc.> 2005-2009     #
# @Date: 20.05.2008                                                            #
# ---------------------------------------------------------------------------- #
# M-cms v4.1 (core build - 4.102)                                              #
################################################################################


$core->modules->this['menu'][]= array('controller' => 'list' , 'name'=>57, 'action_id'=>1);
$core->modules->this['menu'][]= array('controller' => 'routes' , 'name'=>67, 'action_id'=>1);
$core->modules->this['menu'][]= array('controller' => 'trigers' , 'name'=>74, 'action_id'=>1);
$core->modules->this['menu'][]= array('controller' => 'test_replycation' , 'name'=>101, 'action_id'=>1);
$core->modules->this['menu'][]= array('controller' => 'sites_modules' , 'name'=>102, 'action_id'=>1);

$core->modules->add_controller("add", "del", "edit", "list", "save", "routes", 'trigers', 'test_replycation', 'sites_modules');
$core->modules->add_default_controller('list');


$core->modules->this['controllers_path']= '/controllers/';

$core->modules->this['tpl']['cach_param']= array('_site' => 1 , '_lang' => 1 , '_module' => 1 , '_action' => 1);
$core->modules->this['tpl']['cach_expire']= 0;
$core->modules->this['tpl']['cached'] = 0;
$core->modules->this['tpl']['name'] = 'main.html';


$core->CONFIG['svn_control']['dbname']= 'denisco';
$core->CONFIG['svn_control']['dbhostname']= '10.25.2.72';
$core->CONFIG['svn_control']['dbusername']= 'svn_control'; 
$core->CONFIG['svn_control']['dbpass']= 'fj44VtbqyvH24wm3';




?>