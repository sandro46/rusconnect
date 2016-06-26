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

$controller->id = 10;
$controller->cached = 0;
$controller->cache_param  = array('_site' => 1 , '_lang' => 1 , '_url' => 1, '_uid'=>1);
$controller->cache_expire = 30;
$controller->init();

$controller->cached();
$controller->tpl = 'cart_complete.html';



if(!empty($_POST['order_name'])) {    
	$_POST['name_explode'] = explode(' ', $_POST['order_name']);
	$user = array(
			'full_name'=>$_POST['order_name'],
			'first_name'=>$_POST['name_explode'][0],
			'last_name'=>(!empty($_POST['name_explode'][1]))? $_POST['name_explode'][1] : '',
			'email'=>$_POST['order_email'],
			'phone'=>$_POST['order_phone'],
			'address'=>'',
			'comment'=>$_POST['order_comment'],
			'pay_type'=>intval($_POST['pay_type']),
			'ship_type'=>intval($_POST['ship_type']),
	);
	
	$address = "{$_POST['order_city']}, {$_POST['order_street']}, д. {$_POST['order_house']}";
	$address2 = '';
	if(!empty($_POST['order_building'])) $address2 .= ' стр.'.$_POST['order_building'];
	if(!empty($_POST['order_porch'])) $address2 .= ' подъезд '.$_POST['order_porch'];
	if(!empty($_POST['order_floor'])) $address2 .= ' этаж '.$_POST['order_floor'];
	if(!empty($_POST['order_flat'])) $address2 .= ' кв. '.$_POST['order_flat'];
	if(!empty($_POST['order_intercom'])) $address2 .= ' (домофон: '.$_POST['order_intercom'].')';
	
	$user['address'] = $address.$address2;
	$user['contact_address1'] = $_POST['order_city'];
	$user['contact_address2'] = "{$_POST['order_street']}, д. {$_POST['order_house']}".$address2;
	

	$orderInfo = $core->shop->makeOrder($user);
	$_SESSION['order_access_code_'.$orderInfo['order_id']] = $orderInfo['code'];	
	$core->shop->clearCart();
	@header("Location: /ru/shop/complete_order/id/{$orderInfo['order_id']}/");
	die();
} else if(!empty($_GET['id']) && !empty($_POST['code'])) {
    $orderId = intval($_GET['id']);
    $code = mysql::str($_POST['code']);
    
    if($core->shop->checkOrderCode($orderId, $code)) {
        $_SESSION['order_access_code_'.$orderId] = $code;
    }
    
    @header("Location: /ru/shop/complete_order/id/{$orderId}/");
} else if(!empty($_GET['id'])) {
    
    $orderId = intval($_GET['id']);
    $orderInfo = $core->shop->getOrder($orderId);
    $core->tpl->assign('orderId', $orderId);
    
    
    if(!isset($_SESSION['order_access_code_'.$orderId])) {
        $core->tpl->assign('orderInfo', false);
        $core->tpl->assign('codeCheckError', true);
    } else {
        $core->tpl->assign('orderInfo', $orderInfo);
    }
}

$core->title =  $core->shop->shopInfo['name'].' - Статус заказа';





?>