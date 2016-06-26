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



$controller->id = 1;
$controller->cached = 0;
$controller->cachedAll = true;
$controller->cache_param  = array('_site' => 1 , '_lang' => 1 , '_url' => 1, '_uid'=>1);
$controller->cache_expire = 86400;
$controller->init();
$controller->tpl = 'list.html';
$controller->cached();

$core->title = ($core->fromIndex)? $core->title :  'CMS | '.$core->modules->this['describe'].' | '.$controller->descr.'';

$controller->load('admin.albums.php');
$controller->load('ajax.php');

$root = new admin_albums();

$page = (isset($_GET['page']))? intval($_GET['page']) : 0;
$limit = 10;
$orderby = (isset($_GET['orderby']))? $_GET['orderby']:'date';
$ordertype = (isset($_GET['ordertype']))? $_GET['ordertype']:'desc'; 


if(isset($_GET['genre']))
{
	$genre = $root->getGenres(0, false, htmlspecialchars($_GET['genre']));
	
	if(!$genre || !count($genre))
	{
		$core->error_page('404');
	}
	else
		{
			$curent_genre = $genre[0]['name'];
						
			$core->tpl->assign('curent_page', 'genres');
			$core->tpl->assign('genre', $curent_genre);
			$core->tpl->assign('genre_title', $core->title);
			$keyword = $curent_genre;
			$result = $root->getAll($page, $limit, $orderby, $ordertype, $genre[0]['id']);
					
			$core->tpl->assign('albums', $result[0]);
			$core->tpl->assign('pagenav', pagenav($result[1], $limit, $page, "page", $genre[0]['rewrite_id'], 99999));
		}
}
else
	{	
		$result = $root->getAll($page, $limit, $orderby, $ordertype);
		$core->tpl->assign('albums', $result[0]);
		
		if($_GET['category'] == 'popular')
		{
			$keyword = 'Популярная музыка';
			$core->tpl->assign('curent_page', 'popular');
			$core->tpl->assign('pagenav', pagenav($result[1], $limit, $page, "page", '/music/popular/', 99999));
		}
		elseif($_GET['category'] == 'random')
			{
				$keyword = 'случайный альбом';
				$core->tpl->assign('curent_page', 'random');
				$core->tpl->assign('pagenav', pagenav($result[1], $limit, $page, "page", '/music/random/', 99999));
			}
			else
				{	
					$keyword = 'новинки mp3';
					$core->tpl->assign('curent_page', 'new');
					$core->tpl->assign('pagenav', pagenav($result[1], $limit, $page, "page", '/music/new/', 99999));
				}
	}


$core->title = 'Скачать музыку '.$keyword.', torrent '.$keyword.' скачать, скачать mp3 '.$keyword;
$core->meta_description = "$keyword music, скачать $keyword, download $keyword, $keyword торент трекер, слушать $keyword".$core->CONFIG['albums']['meta_description'];
$core->meta_keywords = "$keyword music, скачать $keyword, download $keyword, $keyword торент трекер, слушать $keyword".$core->CONFIG['albums']['meta_keywords'];
	

			//echo $core->log->sql();

$core->lib->load('ajax');
$ajax = new ajax();
$ajax->debug_mode = 0;
$ajax->request_type = 'POST';
$ajax->add_func('check_login_for_used', 'checkCaptcha', 'createUser');
$ajax->init();
$core->tpl->assign('ajax_output', $ajax->output);
$ajax->user_request();


?>