<?php 
error_reporting(E_ALL);
ini_set('display_errors',1);



include $core->CONFIG['module_path'].$core->siteObject['custom_preloader'].'/client.shop.php';
$core->shop = new client_shop();

$options = array(
    'login'=> $core->CONFIG['1cServices']['price']['login'],
    'password' => $core->CONFIG['1cServices']['price']['password']
);

$client = new SoapClient($core->CONFIG['1cServices']['price']['url'], $options);
$response = $client->{$core->CONFIG['1cServices']['price']['function']}(array('arrCodes'=>array('65050')));

$core->shop->syncArticleAll();

//$core->shop->syncArticleAll();
die();

/*

$core->db->query("SELECT c.*, u.crm_contact_id, u.password, u.email FROM tp_user_cartbox as c LEFT JOIN tp_user as u ON u.user_id = c.user_id  WHERE c.user_id != 1");
$core->db->get_rows();
$list = $core->db->rows;

foreach($list as $item) {
    $sql = "SELECT * FROM tp_user_address WHERE user_id = {$item['user_id']} ORDER BY address_id DESC LIMIT 1";
    $core->db->query($sql);
    $address = $core->db->get_rows();
    $phone = $core->shop->getContactPhone($item['crm_contact_id']);
   
    $query = array(
        'cartbox_id' => $item['cart_id'],
        'contact_id' => $item['crm_contact_id'],
        'delivery_type' => 3,
        'short_address' => makeShortAddress($address),
        'comment' => '',
        'phone' => $phone,
        'pay_type_name' => 'Квитанция'
    );
    
    $core->shop->makeOrderFromCartbox($query);
    
}


function makeShortAddress($data) {
    $return = '';
    $list = array();
    
    if(!empty($data['zip'])) {
        $list[] = $data['zip'];
    }
    
    if(!empty($data['region'])) {
        $list[] = $data['region'];
    }
    
    if(!empty($data['city'])) {
        $list[] = 'г. ' . $data['city'];
    }
    
    if(!empty($data['street'])) {
        $list[] = 'ул. '.$data['street'];
    }
    
    if(!empty($data['house'])) {
        $list[] = 'д. '.$data['house'];
    }
    
    if(!empty($data['building'])) {
        $list[] = 'стр. '.$data['building'];
    }
    
    if(!empty($data['flat'])) {
        $list[] = 'кв./оф. '.$data['flat'];
    }
    
    return implode(', ', $list);
}


die();*/

$op = (isset($_GET['op']))? $_GET['op'] : false;
$article = (isset($_GET['article']))? $_GET['article'] : false;

/*
 * $config['1cServices'] = array(
        'price' => array(
            'url' => 'https://1c.rusconnect.ru/trade/ws/PriceAndQuantityQuery?wsdl',
            'login' => 'ws',
            'password' => 'rusconws',
            'function' => 'QuantityInStockAndPrices'
        )
    );
 */

$options = array(
    'login'=> $core->CONFIG['1cServices']['price']['login'],
    'password' => $core->CONFIG['1cServices']['price']['password']
);

$client = new SoapClient($core->CONFIG['1cServices']['price']['url'], $options);

//QuantityInStockAndPrices
//QuantityInStockAndPricesResponse

$response = $client->{$core->CONFIG['1cServices']['price']['function']}(array('arrCodes'=>array('89992', '899810', '89882')));

if($response instanceof stdClass) {
    if(!empty($response) && !empty($response->return && !empty($response->return->Товар))) {
        print_r($response->return->Товар);
    }
}

die();
//print_r($client->__soapCall('QuantityInStockAndPrices', array('МассивАртикулов'=>array(array('Артикул' => '112445')))));


print_r($client);

die();


if(!$op) {
    echo json_encode(array('error'=>1, 'message'=>'Operation not specified')); die(); 
}


if($op == 'update') {
    if($article) {
        $result = $core->shop->syncArticle($article);
        echo json_encode($result); die();
    } else {
        echo json_encode(array('error'=>10, 'message'=>'Update command is used together with article. For update all, use update_all command')); die();
    }
} elseif($op == 'update_all') {
    $result = $core->shop->syncArticleAll();
    echo json_encode($result); die();
} else {
    echo json_encode(array('error'=>2, 'message'=>'command not specified')); die();
}




?>