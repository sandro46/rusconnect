<?php
################################################################################
# This file was created by M-cms core.                                         #
# If you want create a new controller files,                                   #
# look at modules section in admin interface.                                  #
#                                                                              #
# If you want modify this header, look at /modules/modules/class/modules.php   #
# ---------------------------------------------------------------------------- #
# In this controlle you can use all core api through $core variable            #
# also there is other components api:                                          #
#     $controller = Controller object. Look at /classes/controllers.php        #
#     $ajax = Ajax api object. Look as /classes/ajax.php                       #
# In this file you must to specify the action id and set cached flag           #
# and call ini method.                                                         #
# If you can use template in this controole, please specify variable "tpl".    #
# Example:                                                                     #
#     $controller->id = 1; Controller action id = 1. Look at database.         #
#     $controller->cached = 0; Cache system is off                             #
#     $controller->init(); Call controller initiated method                    #
#     $controller->tpl = 'filename'; Template name.                            #
# You can specify the template in any line of controller, but                  #
# if you want to use caching, you must specify the template to call            #
# the method of checking the cache.                                            #
# If you can break controoler logic, to call $core->footer()                   #
# If you need help, look at api documentation.                                 #
# ---------------------------------------------------------------------------- #
# @Core: 4.102                                                                 #
# @Author: Alexey Pshenichniy <inc.mcms@gmail.com> <M-zone Inc.> 2005-2009     #
# @Date: 20.01.2009                                                            #
# ---------------------------------------------------------------------------- #
# M-cms v4.1 (core build - 4.102)                                              #
################################################################################



$controller->id = 4;
$controller->cached = 0;
$controller->cache_param  = array('_site' => 1 , '_lang' => 1 , '_url' => 1, '_uid'=>1);
$controller->cache_expire = 0;
$controller->init();
$controller->tpl = 'null';
$controller->cached();


$core->title = 'CMS | '.$core->modules->this['describe'].' | '.$controller->descr.'';


$core->lib->load('torrent');
$core->lib->load('archive');
$controller->load('admin.albums.php');
$controller->load('ajax.php');
$root = new admin_albums();

//$dir = "/var/www/m-cms.loc/vars/mn/";
$dir = "/var/www/m-cms.org/www-data/vars/mu/";
$albums = array();



foreach(scandir($dir) as $item)
{
	if($item != '.' && $item != '..' && is_dir($dir.$item) && intval(substr($item, -4)) > 1000)
	{
		
		$albums[] = array('name'=> $item, 'info'=>parse_album_name_from_files($item), 'path'=>$dir.$item, 'mp3'=>get_folder_tracks($dir.$item), 'cover'=>get_folder_album_cover($dir.$item));
	}
}



foreach($albums as $album)
{	

	$_tmp_fname = md5(time().microtime().$album['cover']);
	
	//echo $album['path'].'/'.$album['cover']."\n<br>";
	//echo $core->CONFIG['temp_path'].$_tmp_fname."\n<br>";
		
	copy($album['path'].'/'.$album['cover'], $core->CONFIG['temp_path'].$_tmp_fname);
	
	$img = upload_album_cover($album['cover'], $_tmp_fname);
			
	$_POST['AIuser'] = $core->user->id;
	$_POST['AIartist'] = $album['info']['artist'];
	$_POST['AIalbum'] = $album['info']['album'];
	$_POST['AIyear'] = $album['info']['year'];
	$_POST['AIcover'] = $img['id'];
	
	$_POST['AIrewriteUrl'] = $album['info']['rewrite'].'.html';
	$_POST['AIstyle'] = $album['info']['genres'];

	
	$al = new album();
	
	
	$al->create();
	
	
	foreach($album['mp3'] as $track)
	{
		$tmp_name = md5(time().microtime()).$track['real'];
		
		copy($track['path'], $core->CONFIG['temp_path'].$tmp_name);
		uploadComleteFileToAlbum($track['real'], $tmp_name, $al->id);
	}	
	
	
	unset($_POST);
	
	$_POST['AlbumCategory'] = 3;
	$_POST['AlbumId'] = $al->id;
	$_POST['createTorrent'] = 3;
	
	$sl = new album();
	$sl->postCreate();
	echo $al->id."<br>\n";
}


/*
$new = array(68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89);

foreach($new as $id)
{	
	$_POST['AlbumCategory'] = 3;
	$_POST['AlbumId'] = $id;
	$_POST['createTorrent'] = 3;
	
	
	$al = new album();
	$al->create();

	
}*/



//postCreate()


//print_r($albums);




// Controller logic, source code





$core->lib->load('ajax');
$ajax = new ajax();
$ajax->debug_mode = 0;
$ajax->request_type = 'POST';
$ajax->add_func('');
$ajax->init();
$core->tpl->assign('ajax_output', $ajax->output);
$ajax->user_request();

?>