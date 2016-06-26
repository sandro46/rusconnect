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
# @Core: 5.001                                                                 #
# @Author: Alexey Pshenichniy <inc.mcms@gmail.com> <M-zone Inc.> 2005-2009     #
# @Date: 28.03.2010                                                            #
# ---------------------------------------------------------------------------- #
# M-cms v5.0 (core build - 5.001)                                              #
################################################################################



$controller->id = 7;
$controller->cached = 0;
$controller->cache_param  = array('_site' => 1 , '_lang' => 1 , '_url' => 1, '_uid'=>1);
$controller->cache_expire = 30;
$controller->init();
$controller->tpl = 'show.html';
$controller->cached();


$core->title = 'M-cms control panel - '.$core->modules->this['describe'].'';


$controller->load('admin.photogallery.php');
$controller->load('ajax.php');
$root = new admin_photogallery();

if(!isset($_GET['album']) || !intval($_GET['album'])) $controller->redirect('/'.$core->CONFIG['lang']['name'].'/photogallery/list/', 'Bad request.<br>');
	

// Controller logic, source code
$albumId		= intval($_GET['album']);
$page 			= (intval($_GET['page']));
$limit			= (intval($_GET['limit']))? intval($_GET['limit']):12;
$ImagesList 	= $root->get_album_images($albumId, $page, $limit);
$AlbumInfo		= $root->get_album_info($albumId);
$total_item 	= $root->get_total_images($albumId);
$pagenav_extra  = $core->CONFIG['lang']['name'].'/'.$core->module_name.'/'.$controller->real_name.'/album/'.$albumId.'/limit/'.$limit.'/';


$core->tpl->assign('albumInfo', $AlbumInfo);
$core->tpl->assign('albumImages', $ImagesList);
$core->tpl->assign('pagenav', pagenav($total_item, $limit, $page, "page", $pagenav_extra));

$core->lib->load('ajax');
$ajax = new ajax();
$ajax->debug_mode = 0;
$ajax->mode = 'sync';
$ajax->request_type = 'POST';
$ajax->add_func('upload_album_image', 'removeImage', 'updateImageComment');
$ajax->init();
$core->tpl->assign('ajax_output', $ajax->output);
$ajax->user_request();

?>