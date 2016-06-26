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
# @Date: 27.06.2009                                                            #
# ---------------------------------------------------------------------------- #
# M-cms v4.1 (core build - 4.102)                                              #
################################################################################



$controller->id = 3;
$controller->cached = 0;
$controller->cache_param  = array('_site' => 1 , '_lang' => 1 , '_url' => 1, '_uid'=>1);
$controller->cache_expire = 30;
$controller->init();
$controller->tpl = 'show.html';
$controller->cached();
$core->title = 'M-cms control panel - '.$core->modules->this['describe'].'';


$information['inc'] = get_included_files();
$information['dll'] = get_loaded_extensions();
$information['php'] = phpversion();
$information['mysql'] = $core->db->info();
$information['core'] = CORE_VERSION;
$information['prod'] = PRODUCT;
$information['aut'] = AUTHOR;
$information['cache'] = $core->CONFIG['cache']['enable'];
$information['mem'] = $core->CONFIG['memchache']['runed'];
$information['memStat'] = $core->memcache->getExtendedStats();
$information['sec'] = $core->CONFIG['security']['session']['protect'];
$information['lang'] = $core->CONFIG['lang']['default']['name'];
$information['util'] = $core->CONFIG['utils']['enable'];
$information['deb'] = $core->CONFIG['debuger']['enable'];
$information['log_sqle'] = $core->CONFIG['logs']['sql_error'];
$information['log_sqld'] = $core->CONFIG['logs']['sql_data'];
$information['log_acc'] = $core->CONFIG['logs']['access']; 
$information['tpl'] = $core->CONFIG['templates'];

$information['disk']['total']['byte'] = (substr(CORE_PATH, 1, 2) == ':')? disk_total_space(substr(CORE_PATH, 0, 2)) : disk_total_space(CORE_PATH);
$information['disk']['free']['byte'] = (substr(CORE_PATH, 1, 2) == ':')? disk_free_space(substr(CORE_PATH, 0, 2)) : disk_free_space(CORE_PATH);
$information['disk']['used']['byte'] = $information['disk']['total']['byte'] - $information['disk']['free']['byte'];
$information['disk']['total']['string'] = get_formated_file_size($information['disk']['total']['byte']);
$information['disk']['free']['string'] = get_formated_file_size($information['disk']['free']['byte']);
$information['disk']['used']['string'] = get_formated_file_size($information['disk']['used']['byte']);
$information['disk']['used']['procent'] = round((100/$information['disk']['total']['byte']) * $information['disk']['used']['byte']);


$core->tpl->assign('information', $information);




?>