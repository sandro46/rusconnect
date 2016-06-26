<?php
################################################################################
# This file was created by M-cms core.                                         #
#                                                                              #
# This module configuration file.                                              #
# There you can add entry in the admin menu, specify which of the existing     #
# can use controllers. You can also specify the parameters of caching          #
# and specify template will be used.                                           #
# If in process call module does not find this file, it will be called 404.    #
#                                                                              #
# If you want to use any controller, you'll need to specify                    #
# in $controller->add_controller();                                            #
# Also, you can specify the default controller. This controller will be        #
# called if the URL is not specified controller or if specified controller     #
# was not found or not connected.                                              #
#                                                                              #
# ---------------------------------------------------------------------------- #
# @Author: Alexey Pshenichniy <inc.mcms@gmail.com> <M-zone Inc.> 2005-2009     #
# @Date: 20.05.2008                                                            #
# @license: GNU GENERAL PUBLIC LICENSE Version 2                               #
# ---------------------------------------------------------------------------- #
# M-cms v4.1 (core build - 4.102)                                              #
################################################################################



$core->modules->this['controllers_path']= '/controllers/';

$core->modules->add_controller('post','topic','index');
$core->modules->add_default_controller('index');


$core->modules->this['tpl']['cached'] = 0;
$core->modules->this['tpl']['name'] = 'main.html';

$core->CONFIG['db_blog']['dbname']      = 'blog-ncity-biz';
$core->CONFIG['db_blog']['dbhostname']  = 'localhost';
$core->CONFIG['db_blog']['dbusername']  = 'blog_ncity_biz';
$core->CONFIG['db_blog']['dbpass']      = 'KufWF2SfdT4n78XZ';



$core->ajax->register($core->tpl, 'get', 0, 'tpl');
?>