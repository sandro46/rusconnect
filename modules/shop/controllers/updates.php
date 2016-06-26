<?php 




$controller->id = 1;
$controller->cached = 0;
$controller->cache_param  = array('_site' => 1 , '_lang' => 1 , '_url' => 1, '_uid'=>1);
$controller->cache_expire = 30;
$controller->init();


//$controller->tpl = 'clients_main.html';
$core->title = 'M-cms control panel - '.$core->modules->this['describe'].'';

$controller->load('sync.php');
$controller->load('admin.shop.php');
$api = new admin_shop();
$api->init();


$sync = new sync($api->clientId, $api->shopId, $core->CONFIG['1csql']);
//$sync->updateAll();

print_r($sync->getProductByArticle('270200'));

echo "\n\n".'ok';

/*
$api->updateStatic();
$api->updateTemplates();
//$api->updateModules(array(3=>json_encode(array())));
*/
die();



















?>