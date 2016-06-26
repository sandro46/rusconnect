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



$controller->id = 7;
$controller->cached = 0;
$controller->cachedAll = true;
$controller->cache_param  = array('_site' => 1 , '_lang' => 1 , '_url' => 1, '_uid'=>1);
$controller->cache_expire = 172800;
$controller->init();

$core->title = 'CMS | '.$core->modules->this['describe'].' | '.$controller->descr.'';

$controller->load('admin.albums.php');
$controller->load('ajax.php');
$root = new admin_albums();


if(isset($_GET['track_id']) && intval($_GET['track_id']))
{
	if(!$root->trackExists(intval($_GET['track_id']))) $core->error_page('404');
	
	$controller->tpl = 'track.html';
	$controller->cached();
	
	$root = new track(intval($_GET['track_id']));
	
	$core->tpl->assign('album_info', $root->album->info);
	$core->tpl->assign('album_genres', $root->album->genres);
	$core->tpl->assign('album_tracks', $root->album->tracks);
	$core->tpl->assign('album_cover', $root->album->cover);
	$core->tpl->assign('track_name', $root->name);
	$core->tpl->assign('track_fullpath', $root->fullpath);
	
	$core->title = 'Скачать '.$album->name.' torrent '.$root->name.' скачать скачать mp3 '.$root->name;
	$core->meta_description = "$root->name скачать $root->name download $root->name, $root->name торент трекер слушать $root->name".$core->CONFIG['albums']['meta_description'];
	$core->meta_keywords = "$root->name скачать $root->name download $root->name, $root->name торент трекер слушать $root->name".$core->CONFIG['albums']['meta_keywords'];	
	

	//print_r($root);
	
}
else
	{
		if(!isset($_GET['id']) || !intval($_GET['id']) || !$root->albumExists(intval($_GET['id'])))
		{
			$core->error_page('404');
		}
		
		$controller->tpl = 'show.html';
		$controller->cached();
		
		$albumId = intval($_GET['id'])+0;
		$album = new album($albumId);
		$album->get();
		
		$core->tpl->assign('album_info', $album->info);
		$core->tpl->assign('album_genres', $album->genres);
		$core->tpl->assign('album_tracks', $album->tracks);
		$core->tpl->assign('album_cover', $album->cover);
		$core->tpl->assign('album_comments', $album->comments);
		$core->tpl->assign('torrents_path',$core->CONFIG['albums']['torrent']['torrentsURL']);
		
		$core->title = 'Скачать музыку '.$album->title.', torrent '.$album->title.' скачать, скачать mp3 '.$album->title;
		$core->meta_description = "$album->title music, скачать $album->title, download $album->title, $album->title торент трекер, слушать $album->title".$core->CONFIG['albums']['meta_description'];
		$core->meta_keywords = "$album->title music, скачать $album->title, download $album->title, $album->title торент трекер, слушать $album->title".$core->CONFIG['albums']['meta_keywords'];	
	}

		$core->lib->load('ajax');
		$ajax = new ajax();
		$ajax->add_func('addAlbumCommnet', 'check_login_for_used','checkCaptcha', 'createUser');
		$ajax->compress = true;
		$ajax->init();
		$core->tpl->assign('ajax_output', $ajax->output);
		$ajax->user_request();



?>