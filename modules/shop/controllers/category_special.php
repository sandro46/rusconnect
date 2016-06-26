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



if(!isset($_GET['id'])) {
	$controller->tpl = 'category_list.html';
} else {
	//$groupId = intval($_GET['id']);
	//$groupInfo = $core->shop->getGroupInfo($groupId);
	if(!$groupInfo) {
		// 404
	} else {
		//$core->tpl->assign('category_id', $groupId);
		//$core->tpl->assign('category_info', $groupInfo);
		//$core->tpl->assign('shop_crumbs', $core->shop->getGroupsTreeUp($groupId));
		
		/* постраничник */
		$page = (!empty($_GET['page']))? intval($_GET['page']) : 0;
		$limit = (!empty($_GET['limit']))? intval($_GET['limit']) : 10;
		$ordersList = array('price','date','name', 'rating');
		$order = (!empty($_GET['order']) && in_array($_GET['order'], $ordersList))? $_GET['order'] : 'date';
		$orderType = (!empty($_GET['order_type']) && $_GET['order_type'] == 'down')? 'desc' : 'asc';
		$sortArrow = ($orderType == 'desc')? 'down' : 'up';
		
		$basicurl = '/ru/shop/category/id/'.$groupId.'/';
		$pageNavExtra = $basicurl.'order/'.$order.'/order_type/'.$sortArrow.'/';
		
		$products = $core->shop->getProducts($groupId,($page*$limit),$limit,$order,$orderType);
		
		$core->tpl->assign('shop_products', $products[0]);
		$core->tpl->assign('sort_prefix', $basicurl);
		$core->tpl->assign('sort_arrow', $sortArrow);
		$core->tpl->assign('sort_name', $order);
		
		$core->tpl->assign('pagination', pagenav($products[1], $limit, $page, "page", $pageNavExtra, 4, $core->theme));
		$core->title =  /*$core->shop->shopInfo['name'].' - '.*/$groupInfo['name'];
		$core->meta_description = $groupInfo['meta_description'];
		$core->meta_keywords = $groupInfo['meta_keyword'];
		$controller->tpl = 'category.html';
	}
}

	

?>