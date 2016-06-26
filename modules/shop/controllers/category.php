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
    
    
	$groupId = intval($_GET['id']);
	$groupInfo = $core->shop->getGroupInfo($groupId);

	/* temp fix */
	//$groupInfo['description'] = strip_tags($groupInfo['description']);
	//$groupInfo['description'] = mb_substr($groupInfo['description'], 0, mb_strrpos($groupInfo['description'], ' ', 100));
	
	define('use_modifications', true);
	
	if(!$groupInfo) {
		// 404
	} else {
		$core->tpl->assign('category_id', $groupId);
		$core->tpl->assign('category_info', $groupInfo);
		
		$crumbs = $core->shop->getGroupsTreeUp($groupId);
		array_shift($crumbs);
		
		$core->tpl->assign('shop_crumbs', $crumbs);
		/* лимит */
		if(isset($_GET['limit'])) {
		    $_SESSION['pagelimit'] = intval($_GET['limit']);
		} else {
		    if(empty($_SESSION['pagelimit'])) $_SESSION['pagelimit'] = 20;
		}
		
		/* постраничник */
		$page = (!empty($_GET['page']))? intval($_GET['page']) : 0;
		$limit = $_SESSION['pagelimit'];
		$ordersList = array('new','popular','sale');
		$order = (!empty($_GET['order']) && in_array($_GET['order'], $ordersList))? $_GET['order'] : 'popular';
		$orderType = (!empty($_GET['order_type']) && $_GET['order_type'] == 'down')? 'desc' : 'asc';
		$sortArrow = ($orderType == 'desc')? 'down' : 'up';
		
		$basicurl = '/ru/shop/category/id/'.$groupId.'/';
		$pageNavExtra = $basicurl.'order/'.$order.'/order_type/'.$sortArrow.'/';
		$fullurl = ($page > 0)? $pageNavExtra.'page/'.$page.'/' : $pageNavExtra;
		$core->tpl->assign('page_limit', $limit);
		
		if($limit == 0) {
		    $page = 0;
		    $limit = 999999;
		}
				
		if($order == 'popular') {
		    $orderType = 'desc';
		}
		
		$products = $core->shop->getProducts($groupId,($page*$limit),$limit,$order,$orderType);
		
		if(count($products[0]) === 1){
			header('Location: ' . $products[0][0]['url']);
		}
		
		$core->tpl->assign('shop_products', $products[0]);
		$core->tpl->assign('sort_prefix', $basicurl);
		$core->tpl->assign('sort_arrow', $sortArrow);
		$core->tpl->assign('sort_name', $order);
		$core->tpl->assign('current_url', $fullurl);
		$core->tpl->assign('current_page', $page);
		
		
		$core->tpl->assign('pagination', pagenav($products[1], $limit, $page, "page", $pageNavExtra, 4, $core->theme));
		
		$core->title = empty($groupInfo['meta_title']) ? $groupInfo['name'] : $groupInfo['meta_title'];
		$core->meta_description = $groupInfo['meta_description'];
		$core->meta_keywords = $groupInfo['meta_keyword'];

		$controller->tpl = 'category.html';
	}
}

	

?>