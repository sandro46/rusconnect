<?php 


include $core->CONFIG['module_path'].$core->siteObject['custom_preloader'].'/client.shop.php';
include CORE_PATH . 'plugins/platron/platronPayment.php';

$core->shop = new client_shop();

$platron = new platronPayment(7344, 'gysogoruhakofone', $core->shop->clientId, $core->shop->shopId, $core->db);
$platron->testmode = true;


$op = (!empty($_GET['op']))? $_GET['op'] : false;
$paysystem = (!empty($_GET['ps']))? intval($_GET['ps']) : false;
$orderId = (!empty($_GET['id']))? intval($_GET['id']) : false;

if($op == 'pay') {
    if(!$paysystem) {
        echo json_encode(array('error'=>2, 'message'=>'Invalid pay system')); die();
    }
    
    if(!$orderId) {
        echo json_encode(array('error'=>3, 'message'=>'Invalid ordertId')); die();
    }
    
    $order = $core->shop->getOrder($orderId);
    
    if(empty($order)) {
        echo json_encode(array('error'=>4, 'message'=>'Order not found')); die();
    }
    
    $prepare = $platron->payment($order, $paysystem);
    
    if(isset($prepare['error'])) {
        echo "<b>Error platron payment prepare.</b> " . $prepare['message'];
        die();
    } else {
        if(isset($prepare['pg_status']) && $prepare['pg_status'] == 'ok') {
            header("Location: " . $prepare['pg_redirect_url']);
            die();
        } else {
            echo "<b>Error platron payment commit.</b> " . json_encode($prepare);
            die();
        }
    }
} elseif($op == 'result') {
    $request = array();
    parse_str($_SERVER['QUERY_STRING'], $request);
    $transaction = $platron->callbackHandler($request, 'result');
    die();
} elseif($op == 'success') {
    $core->setTheme();
    $core->langId = 1;
    $core->content = $core->tpl->get('pay_success.html', 'shop');
    $core->tpl->replaceFindTplSiteName = 'Frontend';
    $core->footer();
} elseif($op == 'fail') {
    
} else {
    echo json_encode(array('error'=>1, 'message'=>'Invalid command')); die();
}


?>