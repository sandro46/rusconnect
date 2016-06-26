<?php

include $core->CONFIG['module_path'].$core->siteObject['custom_preloader'].'/client.shop.php';

if(isset($_GET['op'])) {
    if($_GET['op'] == 'address') {
        unset($_SESSION['currentPreOrderData']);

        if(!isset($_POST['type']) || !intval($_POST['type']) || intval($_POST['type']) > 2) json_error(101,'Не выбран тип адреса');
        if($_POST['type'] == 2) {
            if(!isset($_POST['zip']) || strlen($_POST['zip']) != 6) json_error(102,'Не верно указан индекс');
            if(!isset($_POST['city']) || strlen($_POST['city']) < 2) json_error(103,'Не верно указан город');
        }


        if(!isset($_POST['street']) || strlen($_POST['street']) < 2) json_error(104,'Не верно указана улица');
        if(!isset($_POST['house'])) json_error(105,'Необходимо указать номер дома');
        if(!isset($_POST['phone']) || !phoneCheck($_POST['phone'])) json_error(106,'Не верно указан номер телефона');

        $type = intval($_POST['type']);
        $info = array(
            'index'=>$_POST['zip'],
            'region'=>$_POST['region'],
            'city'=>$_POST['city'],
            'street'=>$_POST['street'],
            'house'=>$_POST['house'],
            'building'=>$_POST['building'],
            'flat'=>$_POST['flat'],
            'zip'=>(!empty($_POST['zip']))? $_POST['zip'] : ''
        );

        $addressId = (isset($_POST['id']) && intval($_POST['id']))? intval($_POST['id']) : false;
        $core->shop = new client_shop();
        $core->shop->checkAuthUser();
        $result = $core->shop->addUserAddress($addressId, $info);

        if($result) {
            $cartBoxId = $core->shop->saveCartBox();
            $preOrder = array(
                'cartbox_id'=>$cartBoxId,
                'address_id'=>$result,
                'address_type'=>$type,
                'order_phone'=>$_POST['phone'],
                'order_phone2'=>$_POST['phone2'],
                'order_comment'=>$_POST['comment']
            );
            $_SESSION['currentPreOrderData'] = $preOrder;
            $core->shop->clearCart();
            json_response($preOrder);
        } else {
            json_error(108,'Внутренняя ошибка сервера. Адрес не добавлен.');
        }
    } else if($_GET['op'] == 'getaddress') {
        if(!isset($_POST['id']) || !intval($_POST['id'])) json_error(107,'Не выбран id адреса');

        $core->shop = new client_shop();
        $core->shop->checkAuthUser();
        $result = $core->shop->getClientAdresses(intval($_POST['id']));
        json_response($result);
    }



    json_error(109,'Метод не распознан.');
}


if(!isset($_POST['type']) || !intval($_POST['type']) || intval($_POST['type']) > 2) json_error(1,'Не выбран тип регистрации');
if(!isset($_POST['email']) || !emailCheck($_POST['email'])) json_error(2,'Не верно указан email');
if(!isset($_POST['phone']) || !phoneCheck($_POST['phone'])) json_error(3,'Не верно указан номер телефона');
if(!isset($_POST['name']) || strlen($_POST['name']) < 2) json_error(4,'Не верно указано Имя');
// if(!isset($_POST['surname']) || strlen($_POST['surname']) < 2) json_error(5,'Не верно указана Фамилия123');
if(!isset($_POST['password']) || strlen($_POST['password']) < 6) json_error(6,'Пароль должен состоять минимум из 6 ти символов');

$type = intval($_POST['type']);
if($type == 2 && (!isset($_POST['comapny']) || strlen($_POST['comapny']) < 3))  json_error(7,'Не верно указано название компании');

$info = array(
    'name'=>$_POST['name'],
    'surname'=>$_POST['surname'],
    'email'=>$_POST['email'],
    'phone'=>preg_replace('/[^0-9\s]/', '', $_POST['phone']),
    'password'=>$_POST['password'],
    'company'=>($type == 2)? $_POST['comapny'] : '',
    'address'=>($type == 2 && !empty($_POST['address']))? $_POST['address'] : '',
    'news'=>1
);

if($type == 2) {
    $company = array(
        'inn'=>(!empty($_POST['company_info']['inn']))? $_POST['company_info']['inn'] : '',
        'kpp'=>(!empty($_POST['company_info']['kpp']))? $_POST['company_info']['kpp'] : '',
        'ogrn'=>(!empty($_POST['company_info']['ogrn']))? $_POST['company_info']['ogrn'] : '',
        'bank'=>(!empty($_POST['company_info']['bank']))? $_POST['company_info']['bank'] : '',
        'bik'=>(!empty($_POST['company_info']['bik']))? $_POST['company_info']['bik'] : '',
        'kor'=>(!empty($_POST['company_info']['kor']))? $_POST['company_info']['kor'] : '',
        'bill'=>(!empty($_POST['company_info']['bill']))? $_POST['company_info']['bill'] : '',
        'address'=>(!empty($_POST['company_info']['address']))? $_POST['company_info']['address'] : '',
    );

    $info['company_info'] = $company;
}

$core->shop = new client_shop();

$login = mysql::str($info['email']);
$sql = "SELECT COUNT(*) FROM tp_user WHERE client_id = {$core->shop->clientId} AND shop_id = {$core->shop->shopId} AND login = '{$login}'";
$core->db->query($sql);
if(intval($core->db->get_field()) > 0) {
    json_error(10,'Пользователь с таким email уже зарегистрирован');
}

$result = $core->shop->addUser($type, $info);
if(is_array($result) && !empty($result['userId'])) {
    $core->shop->authUser('', false, $result['userId']);
}

json_response($result);

function phoneCheck($phone) {
    $phone = preg_replace('/[^0-9]/', '', $phone);
    if(strlen($phone) < 10) return false;

    return true;
}

function emailCheck($email) {
    $pattern = '/^(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){255,})(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){65,}@)(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22))(?:\\.(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-+[a-z0-9]+)*\\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-+[a-z0-9]+)*)|(?:\\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\\]))$/iD';
    return (preg_match($pattern, $email) == 1)? true : false;
}

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
