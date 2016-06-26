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

$step = (empty($_GET['step']) || !intval($_GET['step']) || $_GET['step'] < 1)? 1 : intval($_GET['step']);


if($step == 1 && !empty($_GET['op'])) {
    if($_GET['op'] == 'register') $controller->tpl = "order.register.html";
    if($_GET['op'] == 'forgot') $controller->tpl = "order.forgot.html";
} else if($step > 4) {
    $core->tpl->assign('errorMessage','Что то пошло не так. Вероятно это злые происки махинаторов :)');
    $controller->tpl = 'order.error.html';
} else {
    $controller->tpl = "order.step{$step}.html";
}


$core->title =  "{$core->shop->shopInfo['name']} - Оформление заказа - Шаг {$step}";

if($step > 1 && !$core->shop->auth) {
    $core->tpl->assign('errorMessage','Для продолжения, необходимо авторизоваться либо зарегистрироваться!');
    $controller->tpl = 'order.error.html';
} else if(empty($_SESSION['cart']) && empty($_SESSION['currentPreOrderData'])) {
    $core->tpl->assign('errorMessage','Ваша корзина пуста.');
    $controller->tpl = 'order.error.html';
} else {
    if($step == 2) {
        $addresses = $core->shop->getClientAdresses();
        $mainAddress = false;
    
        if(!empty($addresses)) {
            $mainAddress = $addresses[0];
            if(count($addresses) == 1) $addresses = false;
        }
    
        $core->tpl->assign('mainAddress', $mainAddress);
        $core->tpl->assign('allAddresses', $addresses);
    }
    
    if($step == 3) {
        $orderInfo = array(
            'delivery_type'=>$_SESSION['currentPreOrderData']['address_type'],
            'phone'=>$_SESSION['currentPreOrderData']['order_phone'],
            'phone2'=>$_SESSION['currentPreOrderData']['order_phone2'],
            'commect'=>$_SESSION['currentPreOrderData']['order_comment']
        );
        
        
        $sql = "SELECT * FROM pay_category WHERE 1 ORDER BY `order`";
        $core->db->query($sql);
        $core->db->get_rows();
        $pay_category = $core->db->rows;
        
        $sql = "SELECT * FROM pay_system WHERE 1 ORDER BY group_id";
        $core->db->query($sql);
        $core->db->get_rows();
        $pay_systems = $core->db->rows;
        
        foreach($pay_category as &$item) {
            $item['list'] = array();
            
            foreach($pay_systems as $ps) {
                if($ps['group_id'] == $item['id']) {
                    $item['list'][] = $ps;
                }
            }
        }
        
        $core->tpl->assign('pay_systems', $pay_category);
        $core->tpl->assign('orderInfo', $orderInfo);
        $core->tpl->assign('cartbox',$core->shop->getCartBox($_SESSION['currentPreOrderData']['cartbox_id']));
    }
    
    
    
    if($step == 4) {
        if(!intval($_GET['type'])) {
            $core->tpl->assign('errorMessage','Вы не выбрали способ оплаты. Вернитесь на шаг назад.');
            $controller->tpl = 'order.error.html';
        } else {
            $payType = intval($_GET['type']);
            $payTypeAll = array(
                1=>'Выставить счет для Юр. лица',
                2=>'Квитанция для оплаты через Сбербанк',
            );
            
 
                
               // $core->tpl->assign('errorMessage','Вы выбрали не существующий способ оплаты. Попробуйте выбрать другой способ.');
               // $controller->tpl = 'order.error.html';
                          
            $order = array(
                'pay_type'=>$payType,
                'pay_type_name'=>$payTypeAll[$payType],
                'delivery_type'=>$_SESSION['currentPreOrderData']['address_type'],
                'phone'=>$_SESSION['currentPreOrderData']['order_phone'],
                'phone2'=>$_SESSION['currentPreOrderData']['order_phone2'],
                'commect'=>$_SESSION['currentPreOrderData']['order_comment'],
                'short_address'=>$core->shop->getUserAddress($_SESSION['currentPreOrderData']['address_id'], true),
                'contact_id'=>$core->shop->getUserContactId($core->shop->userId),
                'cartbox_id'=>$_SESSION['currentPreOrderData']['cartbox_id']
            );
            
            // create order
          
            $order['order_id'] = $core->shop->makeOrderFromCartbox($order);
            
            $contact = $core->shop->getContactInfo($order['contact_id']);
            $cartbox = $core->shop->getCartBox($order['cartbox']);
            
            $core->tpl->assign('contact', $contact);
            $core->tpl->assign('order', $order);
            $core->tpl->assign('cartbox',$cartbox);
            
            
            if($order['pay_type'] == 2) {
                // ввод Фио плательщика
                $order['nextstep_name'] = 'Распечатать квитанцию';
                include CORE_PATH.'plugins/invoice_fl/pay.php';
            } elseif($order['pay_type'] == 1) {
                // ввод реквизитов
                include CORE_PATH.'plugins/invoice_ul/pay.php';
                $order['nextstep_name'] = 'Выставить счет';
            } else {
                
                $order['nextstep_name'] = 'Перейти к оплате';
                $order['nextstep_action'] = '/ru/-utils/platron/op/pay/id/' . $order['order_id'] . '/ps/' . $payType . '/';
            }
            
            
            $core->tpl->assign('contact', $contact);
            $core->tpl->assign('order', $order);
            $core->tpl->assign('cartbox',$cartbox);
            
            unset($_SESSION['currentPreOrderData']);
           
        }
        
    }
}




//print_r($core->shop->userInfo);

$core->tpl->assign('orderStep', $step);
$core->tpl->assign('show_sidebar', false);
$core->tpl->assign('show_content_container', false);
$core->tpl->assign('cartInfo', $core->shop->getCartSummary());
$core->tpl->assign('backurl', (!empty($_SERVER['HTTP_REFERER']))? $_SERVER['HTTP_REFERER'] : '/');

?>