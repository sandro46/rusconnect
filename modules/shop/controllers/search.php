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


$controller->tpl = 'search_result.html';

if(majax::is()) {
    $queryTimeLimit = 2;
    
    if(empty($_POST['query'])) {
        echo json_encode(array('error'=>true, 'message'=>'query is empty'));
        die();
    }
    
    $query = preg_replace("/([^a-zA-Zа-яА-Я\-\_\-\+\=0-9\.\?\s]{1,})/ui", '', trim($_POST['query']));
    
    if(isset($_SESSION['last_ajax_searche_query'])) {
        if($_SESSION['last_ajax_searche_query']['query'] == $query) {
            if($_SESSION['last_ajax_searche_query']['time'] + $queryTimeLimit  >= time()) {
                echo json_encode(array('error'=>true, 'message'=>'often ask the same. wait '. (abs(time() - $_SESSION['last_ajax_searche_query']['time'] - $queryTimeLimit)) . ' s.'));
                die();
            } 
        }
    }
    
    $_SESSION['last_ajax_searche_query'] = array(
        'time' =>time(),
        'query'=>$query
    );
    
    $productId = $core->shop->getProductIdByArticle(mysql::str($query));
    $result = array();
    $exact = false;
    
    if($productId) {
        $info = $core->shop->getProductInfo($productId, true);
        $exact = $info;
        $result[] = $info;
    }
    
    $limit = (!empty($_POST['limit']) && intval($_POST['limit']))? intval($_POST['limit']) : 20;
    
    $query = explode(' ', $query);
    $search = array();
    foreach($query as $item) {
        $search[] = trim($item);
    }
    
    $findResult = $core->shop->findProduct($search, 0, $limit, 'name', 'asc', array('multiple'=>true));
    
    $foundRows = (!empty($findResult) && intval($findResult[0]) > 0)? intval($findResult[0]) : 0;
    $foundRows = (!empty($result))? $foundRows + count($result) : $foundRows;
    
    if(!empty($findResult) && intval($findResult[0]) > 0) {
        $result = array_merge($result, $findResult[1]);
    }
    
    echo json_encode(array(
       'found' =>  $foundRows,
       'data' => $result,
       'exact' => $exact
    ));
    
    die();
}


if(!empty($_SERVER['QUERY_STRING']) && substr($_SERVER['QUERY_STRING'], 0, 6) == 'query=') {
	$_GET['query'] = urldecode(substr($_SERVER['QUERY_STRING'], 6));

}
if(empty($_GET['query'])) {
	///print_r($_SERVER);
    $core->tpl->assign('search_small_query', true);
} else {
    $query = $_GET['query'];
    $query = preg_replace("/([^a-zA-Zа-яА-Я\-\_\-\+\=0-9\.\?]{1,})/ui", '', $query);
    $core->tpl->assign('query', $query);
  
    
    $productId = $core->shop->getProductIdByArticle(mysql::str($query));
    
    if($productId) {
        @header("Location: ".$core->shop->getProductUrlById($productId));
        die();
    }
    
    $page = (!empty($_GET['page']))? intval($_GET['page']) : 0;
    $limit = (!empty($_GET['limit']))? intval($_GET['limit']) : $_SESSION['pagelimit'];;
    $ordersList = array('price','date','name', 'rating');
    $order = (!empty($_GET['order']) && in_array($_GET['order'], $ordersList))? $_GET['order'] : 'date';
    $orderType = (!empty($_GET['order_type']) && $_GET['order_type'] == 'down')? 'desc' : 'asc';
    $sortArrow = ($orderType == 'desc')? 'down' : 'up';
    $basicurl = '/ru/shop/search/query/'.$query.'/';
    $pageNavExtra = $basicurl.'order/'.$order.'/order_type/'.$sortArrow.'/';
    
    
    $products = $core->shop->findProduct(mysql::str(mb_strtolower($query)), ($page*$limit),$limit,$order,$orderType);
    
    $core->tpl->assign('shop_products', $products[1]);
    $core->tpl->assign('sort_prefix', $basicurl);
    $core->tpl->assign('sort_arrow', $sortArrow);
    $core->tpl->assign('sort_name', $order);
    $core->tpl->assign('founded_rows', $products[0]);
    $core->tpl->assign('founded_rows_human', morph($products[0], 'товар','товара','товаров'));
    
    
    $core->tpl->assign('pagination', pagenav($products[0], $limit, $page, "page", $pageNavExtra, 4, $core->theme));
    $core->title =  'Поиск по каталогу сайта';

    //$controller->tpl = 'category.html';

    
}




/*
if(!isset($_GET['id'])) {
	$controller->tpl = 'category_list.html';
} else {
	$groupId = intval($_GET['id']);
	$groupInfo = $core->shop->getGroupInfo($groupId);
	if(!$groupInfo) {
		// 404
	} else {
		$core->tpl->assign('category_id', $groupId);
		$core->tpl->assign('category_info', $groupInfo);
		$core->tpl->assign('shop_crumbs', $core->shop->getGroupsTreeUp($groupId));
		

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
		$core->title =  $groupInfo['name'];
		$core->meta_description = $groupInfo['meta_description'];
		$core->meta_keywords = $groupInfo['meta_keyword'];
		$controller->tpl = 'category.html';
	}
}
*/
	

?>