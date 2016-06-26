<?php 

$loginWOcapchaCount = 99; 
$useCap = false;

if(!empty($_POST['op']) && $_POST['op'] == 'logout') {
    $isajax = (!empty($_POST['from']) && $_POST['from'] == 'ajax')? true : false;
    unset($_SESSION['shopUserId']);
    session_commit();
    session_write_close();
    
    if($isajax) {
        echo json_encode(array('status'=>'ok'));
        die();
    } else {
        header('Location: '. $_SERVER['REQUEST_URI']);
        die();
    }
}

if(empty($_POST['email'])) json_error(1,'Не указан email');
if(empty($_POST['password'])) json_error(2,'Не указан пароль');
if(!empty($_SESSION['login_capcha']) && empty($_POST['capcha'])) json_error(3,'Не указана капча');
if(empty($_SESSION['login_capcha'])) {
    if(empty($_SESSION['login_try_count'])) $_SESSION['login_try_count'] = 0;
    $_SESSION['login_try_count'] ++;
    if($_SESSION['login_try_count'] >= $loginWOcapchaCount) {
        $_SESSION['login_capcha'] = true;
        $useCap = true;
    }
} 

if(!empty($_SESSION['login_capcha']) && !empty($_SESSION['secureCode']) && trim($_POST['capcha']) != $_SESSION['secureCode']) {
    json_error(4,'Не верные символы с картинки');
}

include $core->CONFIG['module_path'].$core->siteObject['custom_preloader'].'/client.shop.php';
$core->shop = new client_shop();

$login = mysql::str(trim($_POST['email']));
$pass = md5(trim($_POST['password']));

$result = $core->shop->authUser($login, $pass);

json_response($result);


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

