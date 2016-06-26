<?php 


error_reporting(0); 

include_once CORE_PATH.'plugins/elfinder/php/elFinderConnector.class.php';
include_once CORE_PATH.'plugins/elfinder/php/elFinder.class.php';
include_once CORE_PATH.'plugins/elfinder/php/elFinderVolumeDriver.class.php';
include_once CORE_PATH.'plugins/elfinder/php/elFinderVolumeLocalFileSystem.class.php';


function access($attr, $path, $data, $volume) {
	return strpos(basename($path), '.') === 0    
	? !($attr == 'read' || $attr == 'write')   
	:  null;
}

if($core->site_type == 2) {
	$core->lib->loadModuleFiles('shop', 'admin.shop.php');
	$shop = new admin_shop();
	$shop->init();
	
	
	
	$core->db->select()->from('mcms_themes')->fields('static_path')->where('id_site = '.$shop->shopId)->limit(1);
} else {
	$core->db->select()->from('mcms_themes')->fields('static_path')->where('id_site = '.$core->edit_site)->limit(1);
}


$core->db->execute();
$path = $core->db->get_field();

if($path == '/' || $path == '/templates/') {
	$tmp_path = 'tmp_user_dir_'.$core->user->id;
	$localPath = '/vars/temp/'.$tmp_path;
	$fullPath = CORE_PATH.'vars/temp/'.$tmp_path;
	
	if(!file_exists($fullPath)) {
		@mkdir($fullPath, '0755', true);
	}	
} else {
	$localPath = $path;
	$fullPath = CORE_PATH.preg_replace("/^(\/templates\/)/", "static/", $path);
}

$opts = array(
		'roots' => array(
				array(
						'driver'        => 'LocalFileSystem',
						'path'          => $fullPath, 
						'URL'           => $localPath,
						'accessControl' => 'access' 
				)
		)
);

$connector = new elFinderConnector(new elFinder($opts));
$connector->run();

die();

?>
