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
# Also, you must specify the controller on default call.                       #
# This controller will be called if the URL is not specified controller        #
# or if specified controller was not found or not connected.                   #
#                                                                              #
# ---------------------------------------------------------------------------- #
# @Author: Alexey Pshenichniy <inc.mcms@gmail.com> <M-zone Inc.> 2005-2010     #
# @Date: 29.12.2010                                                            #
# ---------------------------------------------------------------------------- #
# M-CMS v5.0                                                                   #
################################################################################



$core->modules->this['menu'][]= array('controller' => 'invoices' , 'name'=>'Счета', 'action_id'=>1);
$core->modules->this['menu'][]= array('controller' => 'acts' , 'name'=>'Акты', 'action_id'=>1);
$core->modules->this['menu'][]= array('controller' => 'companies' , 'name'=>'Контрагенты', 'action_id'=>1);
$core->modules->this['menu'][]= array('controller' => 'contracts' , 'name'=>'Договоры', 'action_id'=>1);
$core->modules->this['menu'][]= array('controller' => 'bank' , 'name'=>'Банк', 'action_id'=>1);
$core->modules->this['menu'][]= array('controller' => 'persons' , 'name'=>'Сотрудники', 'action_id'=>1);

$core->modules->this['controllers_path']= '/controllers/';


//$core->modules->add_controller('list', 'add', 'show', 'edit', 'del', 'settings', 'updatebic');
$core->modules->add_controller('productlist','wizard','order','order_form','settings');
$core->modules->add_default_controller('productlist');


$core->modules->this['tpl']['cach_param']= array('_site' => 1 , '_lang' => 1 , '_module' => 1 , '_action' => 1);
$core->modules->this['tpl']['cach_expire'] = 0;
$core->modules->this['tpl']['cached'] = 0;
$core->modules->this['tpl']['name'] = 'main.html';

$core->ajax->register($core->tpl, 'get', 0, 'tpl');

?>