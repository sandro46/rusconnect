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



$controller->id = 7;
$controller->cached = 0;
$controller->cache_param  = array('_site' => 1 , '_lang' => 1 , '_url' => 1, '_uid'=>1);
$controller->cache_expire = 30;
$controller->init();
$controller->tpl = 'show.html';
$controller->cached();


$core->title = 'M-cms control panel - '.$core->modules->this['describe'].'';


$controller->load('admin.bugalter.php');
$root = new admin_bugalter();
//$doc = $root->simplePay(2, 5, 3000.00, 'Оплата услуг СМС по сч.№31 от 13.09.2011г.');
//$doc = $root->simplePay(2, 5, 1500.00, 'Оплата услуг СМС по сч.№32 от 17.09.2011г.');
//download($root->getPDFpaymentFilename(27));
//download($root->getPDFpaymentFilename(28));

$fp = fopen(CORE_PATH.'vars/apilog.'.date('d.m.Y'), 'w_');
fwrite($fp, '['.date('H:i:s').'] '.serialize($_GET)."-".serialize($_POST));
fclose($fp);



// create pdf $root->getPDFpaymentFilename(intval($_GET['payid']));



/*
$payFrom = 2;
$payTo = 1;
$summ = 45633.50;
$doc_num = '116';
$comment = 'Оплата услуг по счету №1212';
$queue = 3;
$status = '01';
$pay_id = $root->makePayment($payFrom, $payTo, $doc_num, $comment, $queue, $status, $summ);
*/

function download($file)
{
	echo '<script type="text/javascript">document.location.href="'.$file.'";</script>';
	die();
}



// Controller logic, source code





$core->lib->load('ajax');
$ajax = new ajax();
$ajax->debug_mode = 1;
$ajax->request_type = 'POST';
$ajax->add_func('');
$ajax->init();
$core->tpl->assign('ajax_output', $ajax->output);
$ajax->user_request();

?>