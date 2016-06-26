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
# @Core: 281                                                                   #
# @Author: Alexey Pshenichniy <inc.mcms@gmail.com> <M-zone Inc.> 2005-2010     #
# @Date: 29.12.2010                                                            #
# ---------------------------------------------------------------------------- #
# M-CMS v5.0                                                                   #
################################################################################



$controller->id = 1;
$controller->cached = 0;
$controller->cache_param  = array('_site' => 1 , '_lang' => 1 , '_url' => 1, '_uid'=>1);
$controller->cache_expire = 30;
$controller->init();
$controller->tpl = 'tasks.html';
$controller->cached();

$core->title = 'M-cms control panel - '.$core->modules->this['describe'].'';

$core->lib->load('sv_module');
$controller->load('crm_client.php');
$controller->load('client.bugalter.php', 'bugalter');
$api = new crm_client();
$api->init();

$buh = new client_bugalter();

$core->ajax->register($api, 'methods', 1);
$core->ajax->listen();

$core->tpl->assign('myUsers', $api->getClientUsers());
$core->tpl->assign('task_types', $api->getTaskTypes());
$core->tpl->assign('task_statuses', $api->getTaskStatusTypes());
$core->tpl->assign('sources', $api->getSourcesList());
$core->tpl->assign('dealPhases', $api->getDealPhases());
$core->tpl->assign('dealStatuses', $api->getDealStatuses());
$core->tpl->assign('companys', $api->getCompanies('deals', 0, 1000, 'name', 'asc'));
$core->tpl->assign('companiesTypes',$buh->getCompaniesTypes());
$core->tpl->assign('companySources', $api->getCompSourcesList());
$core->tpl->assign('contactSources',$api->getSourcesList());
$core->tpl->assign('ref_phone', $api->getPhoneTypes());
$core->tpl->assign('ref_email', $api->getEmailTypes());
$core->tpl->assign('ref_mesn', $api->getMesengerTypes());
$core->tpl->assign('map_contract_status', $buh->getContractStatuses());




?>