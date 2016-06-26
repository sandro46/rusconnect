<?php 


include $core->CONFIG['module_path'].$core->siteObject['custom_preloader'].'/client.shop.php';
$core->shop = new client_shop();


if(!empty($_POST['product']) && intval($_POST['product'])) {
    $info = $core->shop->getProductInfo(intval($_POST['product']));
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

