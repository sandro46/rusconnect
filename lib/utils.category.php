<?php 


include $core->CONFIG['module_path'].$core->siteObject['custom_preloader'].'/client.shop.php';
$core->shop = new client_shop();
$core->setTheme();
$core->langId = 1;

if(!empty($_POST['category']) && intval($_POST['category'])) {
    define('use_modifications', true);
    $groupId = intval($_POST['category']);
    $page = (!empty($_POST['page']))? intval($_POST['page']) : 0;
    $limit = (!empty($_POST['limit']) && intval($_POST['limit']) > 0)? intval($_POST['limit']) : 18;
    
    
    $products = $core->shop->getProducts($groupId,($page*$limit),$limit);
    $groupInfo = $core->shop->getGroupInfo($groupId);
    $cb = (!empty($_POST['cb']))? $_POST['cb'] : 'console.log';
    $extra = $groupId;
    
    $core->tpl->assign('shop_products', $products[0]);
    $core->tpl->assign('category_info', $groupInfo);
    $core->tpl->assign('pagesCount', ceil($products[1]/$limit));
    $core->tpl->assign('pageItemNum', $page+1);
    
    if(!empty($_POST['onlyrows']) && intval($_POST['onlyrows'])) {
        $tpl = $core->tpl->get('CatalogPopupContentRows.html', 'shop');
    } else {
        $tpl = $core->tpl->get('CatalogPopupContent.html', 'shop');
    }

    $info = array(
        'tpl'=>$tpl,
        'prod'=>$products,
        'cat'=>$groupInfo,
        'cb'=>$cb,
        'page'=>$page,
        'pages'=>ceil($products[1]/$limit),
        'limit'=>$limit
    );
    
    if(empty($info)) {
        echo json_encode(array('status'=>'error', 'message'=>'uknown operation', 'error'=>true));
    } else {
        echo json_encode(array('status'=>'ok', 'data'=>$info, 'error'=>false));
    }
   
} else {
	echo json_encode(array('status'=>'error', 'message'=>'uknown operation', 'error'=>true));
}



die();
?>

