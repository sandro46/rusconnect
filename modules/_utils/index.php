<?php

	//echo $core->user->id;
	//if(!$core->user->id && (!$core->controller || ($core->controller != 'login' && $core->controller != 'exit'))) die('Access denied');

	
	if(!$core->controller && $core->CONFIG['utils']['enable'] == 1)
	{
		print_r(print_all_system_utils());
	
		die();
	}
	
	
	if(!in_array($core->controller, $core->CONFIG['utils']['list']))
	{
		$core->user->init();
		if(!$core->user->id) die('Access denied to utilite <b>'.$core->controller.'</b>');
	}
	
	$lib = 'utils.'.$core->controller.'.php';
	
	if(!file_exists($core->CONFIG['utils_dir'].$lib)) die("Lib not exists!");

	include $core->CONFIG['utils_dir'].$lib;

	function print_all_system_utils()
	{
		global $core;
		foreach(scandir($core->CONFIG['utils_dir']) as $fname)
		{
			
			if(substr($fname, 0, 6)=='utils.')
			{
				$f = substr($fname, 6, -4);
				$arr[] = $f;
			}
		}	
		
		return $arr;
	}

	die();
?>