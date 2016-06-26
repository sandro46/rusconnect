<?php
################################################################################
# ---------------------------------------------------------------------------- #
# @Author: Alexey Pshenichniy <inc.mcms@gmail.com> <M-zone Inc.> 2005-2009     #
# @Date: 20.05.2008                                                            #
# @license: GNU GENERAL PUBLIC LICENSE Version 2                               #
# ---------------------------------------------------------------------------- #
# M-cms v4.1 (core build - 4.102)                                              #
################################################################################

class library
{

	private $config = array();
	private $loaded_libs = array();
	private $core = false;
	
	public function __construct($config)
	{
		$this->config = $config;
	}

	/**
	 * Метод загружает класс
	 *
	 * @var class_name = название класса
	 * @return bool
	 */
	public function load()
	{
		$_args = func_get_args();
		
		foreach($_args as $class_name)
		{
			if(in_array($class_name, $this->loaded_libs)) continue;
			if(class_exists($class_name)) continue; 
			if(!is_file($this->config['core_path']. $class_name. '.php')) continue;
			include $this->config['core_path'].$class_name.'.php';
			$this->loaded_libs[] = $class_name;
		}
		
	}
	
	public function loadWithoutCheck()
	{		
		foreach(func_get_args() as $class_name) {
			$this->loaded_libs[] = $class_name;
			include $this->config['core_path'].$class_name.'.php';
		}
	}
	
	public function loadModuleFiles($moduleName, $filename) {
		$path = core::$instance->CONFIG['module_path'];
		if(!file_exists($path.$moduleName)) {
			$moduleName = mysql::str($moduleName);
			$sql = "SELECT location FROM mcms_modules WHERE name = {$moduleName}";
			core::$instance->db->query($sql);
			$location = core::$instance->db->get_field();
			if(!$location) return false;
			if(!file_exists($path.$moduleName.$location.$filename)) return false;
			include $path.$moduleName.$location.$filename;
		} else {
			if(file_exists($path.$moduleName.'/classes/'.$filename)) {
				include $path.$moduleName.'/classes/'.$filename;
				return true;
			}
			if(file_exists($path.$moduleName.'/includes/'.$filename)) {
				include $path.$moduleName.'/includes/'.$filename;
				return true;
			}

			return false;
		}
	}
	
	public function getLoaded() 
	{
		return $this->loaded_libs;
	}
		
	public function widget($wname)
	{	
		global $core;
		$_tmp_wg = null;
		$_tmp_fn = $this->config['widgets_path'].'wg.'.$wname.'.php';
		
		//$core = $this->core;
		
		if(class_exists($wname) || $core->widgets->getWidget($wname)) return true;
		
		if(!file_exists($this->config['widgets_path'].'wg.'.$wname.'.php')) {
			$core->log->add_module_log('!WIDGETS', 'Не удалось подключить виджет ['.$wname.']. Не найден файл. Директория: ['.$this->config['widgets_path'].']');
			return false;
		}
		
		include $this->config['widgets_path'].'wg.'.$wname.'.php';
		
		if(!class_exists($wname)) {
			$core->log->add_module_log('!WIDGETS', 'Не удалось подключить виджет ['.$wname.']. Не найден исполняемый класс.');
			
			return false;
		}
		
		$core->log->add_module_log('WIDGETS', 'Подключен виджет ['.$wname.']. ');
		
		return true;
	}
		
	public function dll() {
		
		$_args = func_get_args();
		
		foreach($_args as $dll_name) {
			if(isset($this->loaded_libs[$dll_name]) && $this->loaded_libs[$dll_name] == 1) break;
			if(!file_exists($this->config['lib_path'].'dll.'.$dll_name.'.php')) break;

			$core = $this->core;
			$result = include $this->config['lib_path'].'dll.'.$dll_name.'.php';
			$this->loaded_libs[$dll_name] = 1;
		}
		
	}
	
	public function adll($dll_name) {
		if(isset($this->loaded_libs[$dll_name]) && $this->loaded_libs[$dll_name] == 1) break;
		if(!file_exists($this->config['lib_path'].'dll.'.$dll_name.'.php')) break;
		$core = $this->core;
		$result = include $this->config['lib_path'].'dll.'.$dll_name.'.php';
		$this->loaded_libs[$dll_name] = 1;
		return $result;
	}
	
}
?>