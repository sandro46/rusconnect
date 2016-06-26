<?php
$controller->id = 5; 
$controller->cached = 0; 
$controller->cache_param  = array('_site' => 1 , '_lang' => 1 , '_url' => 1, '_uid'=>1 );
$controller->cache_expire = 0;
$controller->init();
$controller->cached();


$core->lib->load('installer');
	

//$root = new moduleInstall($core->CONFIG['module_dir'], $core->CONFIG['temp_path']);
//$root->install('forum.install.mcms');

/*
	
	$packPatch = '/var/www/10.89.24.18/vars/temp/';
	$installPach = '/var/www/10.89.24.18/vars/files';

	//$root = new moduleInstall($packPatch, $installPach);
	
	foreach(scandir($packPatch) as $file)
	{
		if($file != '.' && $file != '..' && substr($file, -5) == '.mcms' && is_file($packPatch.$file))
		{
			//$root->install($file);
			echo substr($file, 0, -13).'<br>';
		}
	}
	



/*	

	$root = new moduleInstallCreate('C:/WEB/cms.loc/modules/', 'C:/WEB/cms.loc/vars/temp/');

	foreach(scandir('C:/WEB/cms.loc/modules/') as $dir)
	{
		if($dir != '.' && $dir != '..' && $dir != '403' && $dir != '404' && $dir != '_utils' && $dir != 'index')
		{
			$root->create($dir);
		}
	}
	

*/







?>