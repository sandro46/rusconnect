<?php

	//unset($_SESSION['controllers_indexer'], $_SESSION['controllers']);
	//print_r($_SESSION);

	

	function delete_controller($ctrl)
	{
		$ids = $_SESSION['controllers_indexer'][$ctrl];
		
		unset($_SESSION['controllers'][$ids], $_SESSION['controllers_indexer'][$ctrl]); 
	}

	function get_addControllerHtml()
	{
		global $core;
		
		$core->db->select()->from('mcms_action')
						   ->fields('*')
						   ->lang('$curent')
						   ->order('name');
		$core->db->execute();
		$core->db->get_rows();
		$core->tpl->assign('actions_list', $core->db->rows);
		
		$html = $core->tpl->fetch('controller_add.html');
		
		return $html;
	}
	
	function addNewController($str, $menu)
	{		
		$arr = string2assocArray($str);
		$arr['menu'] = $menu;
				
		$_SESSION['controllers'][] = $arr;
		$_SESSION['controllers_indexer'][$arr['name']] = count($_SESSION['controllers'])-1;
		
		return array($arr['name'], $_SESSION['controllers_indexer'][$arr['name']]);
	}
	
	function get_controllerPreviewData($controllerName)
	{
		global $core;
		
		$creater = new src_creater();
		
		$cid = false;
		$cid = (isset($_SESSION['controllers_indexer'][$controllerName]))?$_SESSION['controllers_indexer'][$controllerName] : false;
		
		if($cid === false) return 'Error!';
		
		$src = $creater->get_controller_src($controllerName);
		
		$core->tpl->assign('this_preview_controller_info', $src);
		
		$html = '<pre>'.htmlspecialchars($src).'</pre>';
		
		return $html;		
	}
	
	function get_moduleInstallHtml($packName)
	{
		global $core;
		
		$sites = admin_modules::get_modules_sites();
		$types = admin_modules::get_modules_types();
		
		$core->db->select()->from('mcms_group')->fields('id_group', 'name')->lang();
		$core->db->execute();
		$core->db->get_rows();
		
		$groups = $core->db->rows;
		
		$core->tpl->assign('mod_sites', $sites);
		$core->tpl->assign('mod_types', $types);
		$core->tpl->assign('mod_groups', $groups);
		$core->tpl->assign('packageName', $packName);
		
		$html = $core->tpl->get('install.package.window.html');
		
		return $html;
	}

	function runInstall($packName, $groups, $sites, $type)
	{
		global $core;
			
		$groups = correctJavascriptArrayAfterPush(explode(",", $groups));
		$sites = correctJavascriptArrayAfterPush(explode(",", $sites));
		
		$installer = new moduleInstall($core->CONFIG['module_dir'], $core->CONFIG['module_dir']);
		
		if($sites) $installer->setAccessSites($sites);
		if($groups) $installer->setAccessGroups($groups);
		
		$installer->setShowMode(intval($type));
		$installer->install($packName);
		
		return;
	}

	function correctJavascriptArrayAfterPush($mixedInput)
	{
		if(!is_array($mixedInput)) $mixedInput = (!$mixedInput)? false : array($mixedInput);
		
		if($mixedInput == false) return false;
		
		foreach($mixedInput as $k=>$val)
		{
			if(intval($val) == 0)
			{
				unset($mixedInput[$k]);
			}
			else 
				{
					$mixedInput[$k] = intval($val);	
				}		
		}
		
		return $mixedInput;
	}
	
	function deletePackage($packName)
	{
		global $core;
		unlink($core->CONFIG['module_dir'].$packName);
		
		return;
	}
	
	function checkInstalled($packname)
	{
		$installer = new moduleControll('false');
	
		$packname = addslashes($packname);
		
		return array($installer->checkInstall($packname), $packname);
	}
	
	function copyInstallPackFromTemp($packageName, $tmpPackName)
	{
		global $core;
		
		@copy($core->CONFIG['temp_path'].$tmpPackName, $core->CONFIG['module_dir'].$packageName);
		@unlink($core->CONFIG['temp_path'].$tmpPackName);	
	
		return $packageName;
	}
	
	function createInstallPackage($packName)
	{
		global $core;
			
		if(!file_exists($core->CONFIG['module_dir'].$packName) || !is_dir($core->CONFIG['module_dir'].$packName)) return false;
		
		$root = new moduleInstallCreate($core->CONFIG['module_dir'], $core->CONFIG['module_dir']);
		$root->create($packName);
	
		return true;
	}
	
	function createAllInstallPackage()
	{
		global $core;
		$root = new moduleInstallCreate($core->CONFIG['module_dir'], $core->CONFIG['module_dir']);
		
		foreach(scandir($core->CONFIG['module_dir']) as $dir)
		{
			if($dir != '.' && $dir != '..' && $dir != '403' && $dir != '404' && $dir != '_utils' && $dir != 'index')
			{
				if(is_dir($core->CONFIG['module_dir'].$dir)) $root->create($dir);	
			}
		}
	}
	
	
	
?>