<?php 


if(empty($_POST['product']) || !intval($_POST['product'])) json_error(1,'Не указан ID продукта');
if(empty($_POST['name']) || strlen($_POST['name']) <2) json_error(2,'Не указано Имя');
if(empty($_POST['text']) || strlen($_POST['text']) <2) json_error(3,'Коментарий пустой');

include $core->CONFIG['module_path'].$core->siteObject['custom_preloader'].'/client.shop.php';
$core->shop = new client_shop();

$result = $core->shop->addComment(intval($_POST['product']), array(
	'name' => $_POST['name'],
	'email' => (empty($_POST['email']))? '' : $_POST['email'],
	'rating' => (empty($_POST['score']))? 0 : intval($_POST['score']),
	'text'=> $_POST['text']
));


echo json_encode(array('status'=>'ok'));


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

