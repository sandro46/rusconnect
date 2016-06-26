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

$controller->id = 5;
$controller->cached = 0;
$controller->cache_param  = array('_site' => 1 , '_lang' => 1 , '_url' => 1, '_uid'=>1);
$controller->cache_expire = 0;
$controller->init();
$controller->tpl = 'add.html';
$controller->cached();

$core->title = 'CMS | '.$core->modules->this['describe'].' | '.$controller->descr.'';

$core->lib->dll('translate');
$controller->load('admin.albums.php');
$controller->load('ajax.php');
$controller->load('utorrent.php');

$root = new admin_albums();

$core->tpl->assign('AIyear', date("Y", time()));
$core->tpl->assign('AIyears', $root->get_years_list());
$core->tpl->assign('AIgroups', $root->getGroupsList());
$core->tpl->assign('AIgroup', 4);
$core->tpl->assign('AIstyles', $root->getGenres());
$core->tpl->assign('AIstyle', 0);

$core->lib->load('ajax');
$ajax = new ajax();
$ajax->debug_mode = 0;
$ajax->request_type = 'POST';
$ajax->add_func('upload_album_cover', 'get_genres_list', 'translateItem', 'getCoversArchiveWindow', 'getCoverData', 'get_genres_list_from_element');
$ajax->init();
$core->tpl->assign('ajax_output', $ajax->output);
$ajax->user_request();




//$torrent = new uTorrent($core->CONFIG['albums']['torrent']['webgate']['host'], $core->CONFIG['albums']['torrent']['webgate']['user'], $core->CONFIG['albums']['torrent']['webgate']['password']);
//print_r($torrent->getTorrents());
//die();
//print_r($torrent->getTorrents());
//die();

?>