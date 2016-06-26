<?php 


require_once CORE_PATH . 'plugins/unisender/unisender_api.php';
require_once CORE_PATH . 'plugins/unisender/request_limiter.php';
 
$listId = (!empty($_POST['list']))? intval($_POST['list']) : false;
$token = (!empty($_POST['token']))? $_POST['token'] : false;
$name = (!empty($_POST['name']))? $_POST['name'] : false;
$email = (!empty($_POST['email']))? $_POST['email'] : false;


if(!$listId) {
    echo json_encode(array('error'=>2, 'message'=>'List id not specified')); die();
}

if(!$token) {
    echo json_encode(array('error'=>3, 'message'=>'Not so easy :)')); die();
}

if(!$name) {
    echo json_encode(array('error'=>10, 'message'=>'Client name not specified')); die();
}

if(!$email || !emailCheck($email)) {
    echo json_encode(array('error'=>11, 'message'=>'Client email not specified or not valid email')); die();
}

$limit = new RequestLimiter();
$limit->rpm = 1;
$limit->conlimit = 1;
$limit->rpd = 1;
 
if(!$limit->checkTocken($token)) {
    echo json_encode(array('error'=>4, 'message'=>'CSRF invalid')); die();
} 

if(!$limit->check_limit()) {
    echo json_encode(array('error'=>5, 'message'=>'not so much and not often')); die();
}
 
$us = new UniSenderApi($core->CONFIG['unisender']['apikey']);
$us->subscribe(array(
    'list_ids'=>$listId,
    'fields[email]'=>$email,
    'fields[Name]'=>$name
));

echo json_encode(array('success'=>true)); die();

function emailCheck($email) {
    $pattern = '/^(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){255,})(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){65,}@)(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22))(?:\\.(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-+[a-z0-9]+)*\\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-+[a-z0-9]+)*)|(?:\\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\\]))$/iD';
    return (preg_match($pattern, $email) == 1)? true : false;
}

?>