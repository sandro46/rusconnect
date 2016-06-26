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
# @Date: 02.06.2011                                                            #
# ---------------------------------------------------------------------------- #
# M-CMS v5.0                                                                   #
################################################################################



//$core->modules->this['menu'][]= array('controller' => 'list' , 'name'=>'Список', 'action_id'=>1);
//$core->modules->this['menu'][]= array('controller' => 'add' , 'name'=>'Добавление', 'action_id'=>5);
//$core->modules->this['menu'][]= array('controller' => 'show' , 'name'=>'Просмотр', 'action_id'=>7);
$core->modules->this['controllers_path']= '/controllers/';


$core->modules->add_controller('list', 'add', 'show', 'result', 'refund');
$core->modules->add_default_controller('list');


$core->modules->this['tpl']['cach_param']= array('_site' => 1 , '_lang' => 1 , '_module' => 1 , '_action' => 1);
$core->modules->this['tpl']['cach_expire'] = 0;
$core->modules->this['tpl']['cached'] = 0;
$core->modules->this['tpl']['name'] = 'main.html';


// PAY CONFIG //

$core->CONFIG['pay_systems'] = array('TEST');
$core->CONFIG['pay_handlers'] = array('sms'=>array(), 'enotice'=>array());

$core->CONFIG['pay_handlers']['sms']['ok_url'] = 'http://sms.itbc.pro/ru/smsservice/payments/result/ok/';
$core->CONFIG['pay_handlers']['sms']['fail_url'] = 'http://sms.itbc.pro/ru/smsservice/payments/result/fail/';
$core->CONFIG['pay_handlers']['sms']['callback'] = 'smsservice|admin.smsservice.php|admin_smsservice->platronPay';
$core->CONFIG['pay_handlers']['sms']['listen'] = 'http://deamons.itbc.pro';

$core->CONFIG['pay_handlers']['enotice']['ok_url'] = 'http://cp.enotice.org/ru/smsservice/payment_ok.html';
$core->CONFIG['pay_handlers']['enotice']['fail_url'] = 'http://cp.enotice.org/ru/smsservice/payment_fail.html';
$core->CONFIG['pay_handlers']['enotice']['callback'] = 'smsservice|admin.smsservice.php|admin_smsservice->platronPay';
$core->CONFIG['pay_handlers']['enotice']['listen']  = 'http://deamons.itbc.pro';

$core->CONFIG['pay_handlers']['secret'] = 'rizofisypatosebo';
$core->CONFIG['pay_handlers']['merchant_id'] = 651;

?>