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

$product_id = intval($_GET['id']);
$controller->cached();

if(!isset($_GET['id']) || !intval($_GET['id'])) {
	//404
} else {
	$productId = intval($_GET['id']);
	$productInfo = $core->shop->getProductInfo($productId);
	//print_r($productInfo);
	
	if(!$productInfo) {
		$core->title = 'Товар не найден!';
		
	} else {
		$controller->tpl = 'product.html';
		$core->shop->productHit($productId);
		$crumbs = $core->shop->getGroupsTreeUp($productInfo['group_id']);
		
		array_shift($crumbs);
		end($crumbs);
		
		$parentCategoryUrl = $crumbs[key($crumbs)]['url'];
		$category = $crumbs[key($crumbs)];
		
		$crumbs[key($crumbs)]['is_last'] = false;
		$crumbs[] = array(
		    'name'=>$productInfo['title'],
		    'url'=>$productInfo['url'],
		    'is_last'=>true
		);
		
		$core->tpl->assign('shop_crumbs', $crumbs);
		$core->tpl->assign('product', $productInfo);
		$core->tpl->assign('category_id', $category['id']);
		$core->tpl->assign('category_info', $category);
		$core->tpl->assign('parent_category_info', $crumbs[0]);
		
		if(!empty($productInfo['vendor_id'])) {
		    $core->tpl->assign('vendorInfo', $core->shop->getVendorInfo($productInfo['vendor_id']));
		}

		$core->tpl->assign('parent_category_url', $parentCategoryUrl);
		$core->tpl->assign('lastViewed', $core->shop->getViewedProducts(20));
		$core->tpl->assign('similarProducts', $core->shop->getProducts($category['id'],0,20)[0]);
		$core->tpl->assign('crossSelling', $core->shop->getCrossSelling($productId, 20));
		
		
		$core->title = $productInfo['title'] . ', купить в Русконект оптом и в розницу';
		$core->meta_description = $productInfo['title'].' оптом и в розницу со склада. Весь товар в наличии. Индивидуальные цены для крупных оптовых покупателей.';
		$core->meta_keywords = 'Купить ' . $productInfo['title'] . ', ' . $productInfo['title'] .' цена, '. $productInfo['title'] . ' оптом';	
		

		
		if(empty($_SESSION['lastviewedproduct'])) $_SESSION['lastviewedproduct'] = array();
		if(!in_array($productId, $_SESSION['lastviewedproduct'])) {
		    $_SESSION['lastviewedproduct'][] = $productId;
		}
	}
}




?>