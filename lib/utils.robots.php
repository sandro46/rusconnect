<?php 

//include $core->CONFIG['module_path'].$core->siteObject['custom_preloader'].'/client.shop.php';
//$core->shop = new client_shop();

header('Content-Type: text/plain');

?>User-agent: *
Disallow: /admin
Disallow: /login
Disallow: /ru/shop/
Disallow: /ru/shop/login
Disallow: /ru/shop/place_an_order
Disallow: /ru/shop/category/
Disallow: /sites
Sitemap: http://<?=$_SERVER['SERVER_NAME'];?>/sitemap.xml
