<?php
$controller->id = 1;
$controller->cached = 0;
$controller->cache_param  = array('_site' => 1 , '_lang' => 1 , '_url' => 1, '_uid'=>1);
$controller->cache_expire = 30;
$controller->init();
$controller->tpl = 'list.html';
$controller->cached();


$core->title = 'M-cms control panel - '.$core->modules->this['describe'].'';


$controller->load('admin.photogallery.php');
$root = new admin_photogallery();


// Controller logic, source code

echo $core->tpl->get('run.widget.html', 'photogallery');
die();



?>