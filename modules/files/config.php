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
# ---------------------------------------------------------------------------- #
# M-cms v4.1 (core build - 4.102)                                              #
################################################################################



//$core->modules->this['menu'][]= array('controller' => 'show' , 'name'=>40, 'action_id'=>7);
$core->modules->this['controllers_path']= '/controllers/';



$core->modules->add_controller("add", "del", "edit", "save", "show", "upload", 'download', 'connector');
$core->modules->add_default_controller('show');



$core->modules->this['tpl']['cach_param']= array('_site' => 1 , '_lang' => 1 , '_module' => 1 , '_action' => 1);
$core->modules->this['tpl']['cach_expire']= 0;
$core->modules->this['tpl']['cached'] = 0;
$core->modules->this['tpl']['name'] = 'main.html';



//Advanced configuration options
$core->CONFIG['controller']['images_ext'] = 'jpg,jpeg,gif,png,bmp';
$core->CONFIG['controller']['files_ext'] = 'doc,xml,xls,txt,pdf,flv,ico,swf,mp3,rar,zip,gz,tar,htm,html,xhtm,xhtml,psd,pwp';
$core->CONFIG['controller']['max_file_size'] = '1024000';









?>