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
# @Core: 4.205                                                                 #
# @Author: Alexey Pshenichniy <inc.mcms@gmail.com> <M-zone Inc.> 2005-2009     #
# @Date: 01.07.2009                                                            #
# ---------------------------------------------------------------------------- #
# M-cms v4.2 (core build - 4.205)                                              #
################################################################################

$controller->id = 1;
$controller->cached = 0;
$controller->cache_param  = array('_site' => 1 , '_lang' => 1 , '_url' => 1, '_uid'=>1);
$controller->cache_expire = 30;
$controller->init();
$controller->tpl = 'main.html';
$controller->cached();

$core->title = 'Rusconnect Shop - Кабинет пользователя';

$controller->load('UserCabinet.php');
$api = new userCabinet();

$core->ajax->register($core->tpl,'get',1, 'tpl');
$core->ajax->register($api,'getClientInfo',1);
$core->ajax->register($api,'getAddresses',1);
$core->ajax->register($api,'getPriceType',1);
$core->ajax->register($api,'getCompanyInfo',1);
$core->ajax->register($api,'getOrders',1);
$core->ajax->register($api,'getAllInfo',1);

$core->ajax->register($api,'updatePersonal',1);
$core->ajax->register($api,'updatePassword',1);

$core->ajax->listen();

$core->tpl->assign('Manager', $api->getPersonalManager());
$core->tpl->assign('majax',$core->ajax->out());
?>
