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



$controller->id = 6;
$controller->cached = 0;
$controller->cache_param  = array('_site' => 1 , '_lang' => 1 , '_url' => 1, '_uid'=>1);
$controller->cache_expire = 0;
$controller->init();
$controller->tpl = 'edit.html';
$controller->cached();


$core->title = 'CMS | '.$core->modules->this['describe'].' | '.$controller->descr.'';

$id = intval($_GET['id']);

$controller->load('admin.albums.php');
$controller->load('ajax.php');
$core->lib->load('ajax');
$ajax = new ajax();
$ajax->debug_mode = 0;
$ajax->request_type = 'POST';
$ajax->add_func('uploadComleteFileToAlbum');
$ajax->init();
$core->tpl->assign('ajax_output', $ajax->output);
$ajax->user_request();


$admin = new admin_albums();
$root = new album($id);
$root->get();

$is_new = (isset($_GET['new']))? 1 : 0;

$core->tpl->assign('is_new', $is_new);
$core->tpl->assign('album', $root);
$core->tpl->assign('coverUrl', $core->CONFIG['albums']['images']['url']);
$core->tpl->assign('AIyears', $admin->get_years_list());
$core->tpl->assign('AItracksCount', count($root->tracks));
$core->tpl->assign('AIgroups', $admin->getGroupsList());

// Controller logic, source code







?>