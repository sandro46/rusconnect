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
$controller->cached();

$core->title = 'M-cms control panel - '.$core->modules->this['describe'].'';


$controller->load('client.bugalter.php');
$api = new client_bugalter();

$core->lib->load('geo');
$core->geo = new geo();

$core->ajax->register($core->geo);
$core->ajax->register($api);
$core->ajax->listen();


if(isset($_GET['id']) && intval($_GET['id'])) {
	$controller->tpl = 'persons-show.html';
	
	$currentYear = date('Y');
	$currentMonth =  intval(date('m'));
	
	$personId = intval($_GET['id']);
	$year = (isset($_GET['year']))? intval($_GET['year']) : $currentYear;
	
	$core->tpl->assign('personsList',$api->getPersonsList($year, true));
	$core->tpl->assign('personId',$personId);
	$core->tpl->assign('personName',$api->getPersonName($personId));
	$core->tpl->assign('personInfo', $api->getPersonDetail($personId, $year));
	
	if($currentYear == $year) {
		$core->tpl->assign('currentMonth',$currentMonth);
	}
	
	$core->tpl->assign('currentYear', $year);
} else {
	$controller->tpl = 'persons-all.html';
	$core->tpl->assign('persons', $api->getPersonsList());
	$core->tpl->assign('personsDepartaments', $api->getPersonsDepartaments());
	$core->tpl->assign('personsPost', $api->getPersonsPost());
}




?>