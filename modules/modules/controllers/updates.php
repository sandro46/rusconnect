<?php

	$controller->id = 1; 
	$controller->cached = 0; 
	$controller->cache_param  = array('_site' => 1 , '_lang' => 1 , '_url' => 1, '_uid'=>1 );
	$controller->cache_expire = 0;
	$controller->init();
	$controller->tpl = 'list.updates.html';
	$controller->cached();
	$core->title = 'M-cms Control Panel - '.$core->modules->this['describe'];
	

	$core->lib->load('ajax');
	$core->lib->load('archive');
	$core->lib->load('installer');
	$core->lib->load('updates');

	$root = new updates_creater(CORE_PATH.'vars/temp/updates/', CORE_PATH.'vars/temp/updates/', '00001', 'system');
	
	$root->addFiles(array(CORE_PATH.'static/admin/wide/js/custom.js', CORE_PATH.'static/admin/wide/js/hoverIntent.js'));
	
	$root->addFolder(CORE_PATH.'static/admin/default/img');
	$root->addFolder(CORE_PATH.'static/admin/default/login');
	
	
	$root->create();
	
	print_r($root);
	
	
	
	die();

	
    //$test = new gzip_file("test.tgz");
	//$test->set_options(array('basedir' => CORE_PATH, 'overwrite' => 0, 'level' => 9));
	//$test->add_files(array("core", "core/*.php", 'lib', 'lib/*.php', 'modules', 'modules/*.*'));
	//$test->create_archive();
	//if (count($test->errors) > 0)
	//    print ("Errors occurred."); // Process errors here
    
   
?>