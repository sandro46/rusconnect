<?php 


include $core->CONFIG['module_path'].$core->siteObject['custom_preloader'].'/client.shop.php';
$core->shop = new client_shop();

if(empty($_POST['op'])) {
	echo json_encode(array('status'=>'error', 'error'=>true, 'message'=>'no action found'));
	die();
} 

if($_POST['op'] == 'add') {
    
	if(empty($_POST['prod'])) {
		echo json_encode(array('status'=>'error', 'message'=>'uknown product id', 'error'=>true));
	} else {
		$result = $core->shop->addToCompare($_POST['prod']);
		if($result) {
			$result = $core->shop->getCompareSummary();
			echo json_encode(array('status'=>'ok', 'error'=>false, 'result'=>$result));
		} else {
			echo json_encode(array('status'=>'error', 'message'=>'server error', 'error'=>true));
		}
	}
	
} else if($_POST['op'] == 'rm') {
	if(empty($_POST['prod'])) {
		echo json_encode(array('status'=>'error', 'message'=>'uknown product id', 'error'=>true));
	} else {
		$result = $core->shop->removeFromCompare($_POST['prod']);
		if($result) {
			$result = $core->shop->getCompareSummary();
			echo json_encode(array('status'=>'ok', 'error'=>false, 'result'=>$result));
		} else {
			echo json_encode(array('status'=>'error', 'message'=>'server error', 'error'=>true));
		}
	}
} else {
	echo json_encode(array('status'=>'error', 'message'=>'uknown operation', 'error'=>true));
}


die();
?>

