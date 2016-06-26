<?php
	$controller->id = 1; 
	$controller->cached = 0; 
	$controller->cache_param  = array('_site' => 1 , '_lang' => 1 , '_url' => 1, '_uid'=>1 );
	$controller->cache_expire = 0;
	$controller->init();
	$controller->tpl = 'list.packages.html';
	$controller->cached();
	$core->title = 'M-cms Control Panel - '.$core->modules->this['describe'];
	

	$core->lib->load('ajax');
	$core->lib->load('archive');
	$core->lib->load('installer');
	$controller->load('ajax_ext.php', 'system');
	$controller->load('modules.php', 'system');
	

	
    $ctrl = new  moduleControll($core->CONFIG['module_dir']);
    $ctrl->getPackageList();
   
    

    $core->tpl->assign('list_modules', $ctrl->list);

	
	$ajax = new ajax();
	$ajax->debug_mode = 0;
	$ajax->request_type = 'POST';
	$ajax->add_func('get_moduleInstallHtml', 'runInstall', 'deletePackage', 'copyInstallPackFromTemp', 'checkInstalled');
	$ajax->init();
	$ajax->user_request();
	
	$core->tpl->assign('ajax_output', $ajax->output);
    
    
?>