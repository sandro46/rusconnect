<?php 


include $core->CONFIG['module_path'].$core->siteObject['custom_preloader'].'/client.shop.php';
$core->shop = new client_shop();

$core->setTheme();
$core->langId = 1;

if(empty($_POST['op'])) {
	$overcost = (!empty($_POST['ov']) && floatval($_POST['ov']) > 0)? floatval($_POST['ov']) : 0;
	$result = $core->shop->getCartSummary($overcost);
	if(!empty($_POST['tpl']) && is_array($_POST['tpl']) && count($_POST['tpl']) == 2) {
	    $core->tpl->assign('cart_summary', $result);
	    $tpl = $core->tpl->get(mysql::str($_POST['tpl'][0]), mysql::str($_POST['tpl'][1]));
	    $result['tpl'] = $tpl;
	}
	
	echo json_encode(array('status'=>'ok', 'error'=>false, 'result'=>$result));
	die();
} 

if($_POST['op'] == 'add') {
	if(empty($_POST['prod']) || empty($_POST['qt'])) {
		echo json_encode(array('status'=>'error', 'message'=>'uknown product id', 'error'=>true));
	} else {
		$overcost = (!empty($_POST['ov']) && floatval($_POST['ov']) > 0)? floatval($_POST['ov']) : 0;
		$result = $core->shop->addToCart($_POST['prod'], $_POST['qt']);
		if($result) {
			$result = $core->shop->getCartSummary($overcost);
			if(!empty($_POST['tpl']) && is_array($_POST['tpl']) && count($_POST['tpl']) == 2) {
			    $core->tpl->assign('cart_summary', $result);
			    $tpl = $core->tpl->get(mysql::str($_POST['tpl'][0]), mysql::str($_POST['tpl'][1]));
			    $result['tpl'] = $tpl;
			}
			
			echo json_encode(array('status'=>'ok', 'error'=>false, 'result'=>$result));
		} else {
			echo json_encode(array('status'=>'error', 'message'=>'server error', 'error'=>true));
		}
	}
} else if($_POST['op'] == 'rm') {
	if(empty($_POST['prod'])) {
		echo json_encode(array('status'=>'error', 'message'=>'uknown product id', 'error'=>true));
	} else {
		$overcost = (!empty($_POST['ov']) && floatval($_POST['ov']) > 0)? floatval($_POST['ov']) : 0;
		$result = $core->shop->removeFromCart($_POST['prod']);
		if($result) {
		    if(!empty($_POST['tpl']) && is_array($_POST['tpl']) && count($_POST['tpl']) == 2) {
		        $core->tpl->assign('cart_summary', $result);
		        $tpl = $core->tpl->get(mysql::str($_POST['tpl'][0]), mysql::str($_POST['tpl'][1]));
		        $result['tpl'] = $tpl;
		    }
		    
			$result = $core->shop->getCartSummary($overcost);
			echo json_encode(array('status'=>'ok', 'error'=>false, 'result'=>$result));
		} else {
			echo json_encode(array('status'=>'error', 'message'=>'server error', 'error'=>true));
		}
	}
} else if($_POST['op'] == 'change') {
	if(empty($_POST['prod']) || empty($_POST['qt'])) {
		echo json_encode(array('status'=>'error', 'message'=>'uknown product id', 'error'=>true));
	} else {
		$overcost = (!empty($_POST['ov']) && floatval($_POST['ov']) > 0)? floatval($_POST['ov']) : 0;
		$result = $core->shop->addToCart($_POST['prod'], false, $_POST['qt']);
		if($result) {
		    if(!empty($_POST['tpl']) && is_array($_POST['tpl']) && count($_POST['tpl']) == 2) {
		        $core->tpl->assign('cart_summary', $result);
		        $tpl = $core->tpl->get(mysql::str($_POST['tpl'][0]), mysql::str($_POST['tpl'][1]));
		        $result['tpl'] = $tpl;
		    }
		    
			$result = $core->shop->getCartSummary($overcost);
			echo json_encode(array('status'=>'ok', 'error'=>false, 'result'=>$result));
		} else {
			echo json_encode(array('status'=>'error', 'message'=>'server error', 'error'=>true));
		}
	}
} else {
	echo json_encode(array('status'=>'error', 'message'=>'uknown operation', 'error'=>true));
}


/*
$result = $core->shop->addComment(intval($_POST['product']), array(
	'name' => $_POST['name'],
	'email' => (empty($_POST['email']))? '' : $_POST['email'],
	'rating' => (empty($_POST['score']))? 0 : intval($_POST['score']),
	'text'=> $_POST['text']
));
*/

die();
?>

