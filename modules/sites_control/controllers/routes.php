<?php
############################################################################
#          This controller was created automatically core system           #
#                                                                          #
# ------------------------------------------------------------------------ #
# @Creator module version 1.2598 b                                         #
# @Author: Alexey Pshenichniy                                              #
# ------------------------------------------------------------------------ #
# Alpari CMS v.1 Beta   $17.06.2008                                        #
############################################################################


$controller->id = 1;
$controller->cached = 0;
$controller->cache_param  = array('_site' => 1 , '_lang' => 1 , '_url' => 1, '_uid'=>1 );
$controller->cache_expire = 0;
$controller->init();
$controller->tpl = 'routes.html';
$controller->cached();

$core->title = 'M-cms Control Panel - '.$core->modules->this['describe'];

$controller->load('routes.php', 'system');
$core->lib->load('ajax');

$routes_type = ($_GET['type'] == 'fronts')? 'fronts':'svn';
	
$ajax = new ajax();
$ajax->debug_mode = 0;
$ajax->request_type = 'POST';
$ajax->add_func('get_window_add_site_in_route', 'set_active_route', 'set_active_repo', 'set_active_server', 'set_deactive_route', 'set_deactive_repo', 'set_deactive_server');
$ajax->init();
$ajax->user_request();

$core->tpl->assign('ajax_output', $ajax->output);

$router = new routes();
$router->get_routes($routes_type);

$core->tpl->assign('routes_list', $router->list);
$core->tpl->assign('routes_type', $routes_type);
?>