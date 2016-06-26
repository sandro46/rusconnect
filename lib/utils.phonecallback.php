<?php 


if(empty($_POST['name']) || strlen($_POST['name'])< 2) json_error(1,'Не указано Имя');
if(empty($_POST['phone']) || strlen($_POST['phone']) < 11) json_error(2,'Не указан номер телефона');

include $core->CONFIG['module_path'].$core->siteObject['custom_preloader'].'/client.shop.php';
$core->shop = new client_shop();

$result = $core->shop->addCallbackContact($_POST['name'], $_POST['phone'], $_POST['comment']);

json_response(array('status'=>'ok'));


function json_response($data) {
    echo json_encode(array('status'=>'ok', 'data'=>$data));
    die();
}

function json_error($code, $message) {
    global $useCap;
    echo json_encode(array(
        'error'=>$code,
        'message'=>$message,
        'uc'=>($useCap)? 1 : 0
    ));
    die();
}


?>

