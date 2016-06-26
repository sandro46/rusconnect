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
# @Date: 09.06.2009                                                            #
# ---------------------------------------------------------------------------- #
# M-cms v4.1 (core build - 4.102)                                              #
################################################################################



$controller->id = 1;
$controller->cached = 0;
$controller->cache_param  = array('_site' => 1 , '_lang' => 1 , '_url' => 1, '_uid'=>1);
$controller->cache_expire = 30;
$controller->init();
$controller->tpl = 'css-form.html';
$controller->cached();
$core->title = 'M-cms control panel - '.$core->modules->this['describe'].'';


$controller->load('templates.php');
$controller->load('ajax_ext.php');
$core->lib->load('ajax');

$ajax = new ajax();
$ajax->debug_mode = 0;
$ajax->request_type = 'POST';
$ajax->add_func('get_addCssHtml', 'createNewCSSfile');
$ajax->init();

$ajax->user_request();



if(isset($_GET['act']) && $_GET['act'] == 'save')
{
	$core->log->access(1, $core->mess->get(12).' '.$_GET['file']);
	
	if($_POST['filename'])
	{
		$res = admin_templates::saveCSS($_POST['filename'], $_POST['TplSourceEditor_lang']);
		
		if($_POST['returned'] == 1)
		{
			$core->lib->dll('redirect');
    		speedRedirect('/'.$core->CONFIG['lang']['name'].'/templates/style/file/'.$_POST['filename'].'/saved/'.$res);
		}
		else 
			{
				$controller->redirect('/'.$core->CONFIG['lang']['name'].'/templates/style/', 'All data saved');
				die();
			}
	}	
}

if(isset($_GET['act']) && $_GET['act'] == 'del' && isset($_GET['file']))
{
	$core->log->access(1, $core->mess->get(11).' '.$_GET['file']);
	admin_templates::deleteCSS($_GET['file']);
	$core->lib->dll('redirect');
    speedRedirect('/'.$core->CONFIG['lang']['name'].'/templates/style/'.$res);
}

if(isset($_GET['saved'])) $core->tpl->assign('curentSavedStatus', $_GET['saved']);

$files = admin_templates::getCSSlist();

$core->tpl->assign('filesList', $files);
$core->tpl->assign('ajax_output', $ajax->output);

$core->log->access(1, 10);

if(isset($_GET['file']) && in_array($_GET['file'], array_keys($files)))
{
	$core->tpl->assign('CSSfilename', $_GET['file']);
	$core->tpl->assign('CSSfileSource', file_get_contents($files[$_GET['file']]['path']));
}





?>