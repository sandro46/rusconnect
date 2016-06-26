<?php 
include $core->CONFIG['module_path'].$core->siteObject['custom_preloader'].'/client.shop.php';
$core->shop = new client_shop();
$core->setTheme();
$core->langId = 1;

/* ======= */

$cats = $core->shop->getCategories(31, 0, 'default', false, false, false, true);
$products_count = $core->shop->getProductsCount();

$cats_no_childs = [];

foreach($cats as $cat_id=>$cat){
	
	$cats[$cat_id]['products_count'] = $products_count[$cat_id]['products_count'];
	
	if(empty($cat['childs'])){
		$cats_no_childs[] = $cat_id;
		$cats[$cat_id]['childs'] = [];
	}
}

$prods = $core->shop->getProductsNameAndRewrite($cats_no_childs);


foreach($prods as $prod){
	$cats[$prod['group_id']]['childs'][] = array('name'=>$prod['title'], 'url'=>$prod['rewrite']);
}

/* ======= */

$core->tpl->assign('categories', $cats);

$tpl = $core->tpl->get('catalog_ajax', 'shop');

$info = array(
	'tpl'=>$tpl
);

if(empty($info)) {
	echo json_encode(array('status'=>'error', 'message'=>'uknown operation', 'error'=>true));
} else {
	echo json_encode(array('status'=>'ok', 'data'=>$info, 'error'=>false));
}

die();
?>

