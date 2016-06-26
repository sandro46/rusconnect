<?php
################################################################################
# ---------------------------------------------------------------------------- #
# @Author: Alexey Pshenichniy <inc.mcms@gmail.com> <M-zone Inc.> 2005-2009     #
# @Date: 20.05.2008                                                            #
# @license: GNU GENERAL PUBLIC LICENSE Version 2                               #
# ---------------------------------------------------------------------------- #
# M-cms v4.1 (core build - 4.102)                                              #
################################################################################


class loader
{

	static private $preloader_path = '';
	static public $SisPreloader = false;
	
	private $modules_path  = '';
	private $controller_status = 0; // 0 - не найден файл контроллера; 1 - контроллер не выбран и не удалось найти контроллер по умолчанию; 3 - Контроллер есть в списке разрешенных; 4 - Контроллер по умолчанию
	private $config = '';
	private $core = false;
	private $isPreloader = false;
	
	
	public function __construct($core) {	
		$this->core = $core;
		$this->modules_path = $this->core->CONFIG['module_path'];
		if($this->core->module_name == '-utils') {	
			include $this->modules_path.'_utils/index.php';
			return true;				
		}	
	}

	public function init() {
		$this->core->module_name = (isset($_GET['module']))? addslashes($_GET['module']) : false;
		$this->core->controller = (isset($_GET['controller']))? addslashes($_GET['controller']) : false;
	
		if($this->core->module_name) {
			$this->core->log->syslog("Выбран модуль <b>{$this->core->module_name}</b>. ");
			$module_exists = $this->core->modules->get_info();
						
			if($module_exists) {
				
				$this->core->log->syslog("ID модуля: <b>{$this->core->module_id}</b> Путь к модулю: <b>{$this->modules_path}</b>");
				if(!$this->core->perm->check()) {
					$this->core->log->syslog("Запускаем предзагрузчик.");
					$this->preloader();
					$this->display_error(403);
					return false;
				} 
				
				$this->core->log->syslog("Запускаем предзагрузчик.");
				$this->preloader();
				
				$this->load_module();
				$this->core->lib->load('controllers');
				$this->load_controller();
				return true;
			} else {
				
				$this->core->log->syslog("Модуль с таким именем не найден в базе.");
				$this->display_error(404);
			}
		} else {
			$this->core->log->syslog("Модуль не выбран.");
			$this->core->log->syslog("Запускаем предзагрузчик.");
			$this->preloader();
			
			$this->core->log->syslog("Пытаемся найти модуль главной страницы.");
      		if(is_file($this->core->CONFIG['module_path'].'index/index.php')) {
      			
      			$core = $this->core;
      			$module_path = $this->core->CONFIG['module_path'].'index/index.php';
      			$this->core->log->syslog("Запускаем модуль главной страницы. Путь к файлам: <b>{$module_path}</b>");
				include $module_path;
				
				$this->core->log->syslog("Модуль главной страницы подключен.");
				return true;
			}
			else {
				$this->core->log->syslog("Модуль главной страницы не найден. Запускать нечего. Выводим 404.");
			}
			
			return false;
        }

        return true;
	}

	public function preloader() {		
		$module_path = ($this->core->is_admin())? $this->core->getAdminModule(true) : $this->core->site_name;
		$path = $this->core->CONFIG['module_path'].$module_path.'/';
		$preloader = $path.'index.php';
				
		if(is_file($preloader)) {
			self::$preloader_path = $path;
			$core = $this->core;
			$this->core->log->syslog("Подключаем предзагрузчик. Пут к файлу: <b>{$preloader}</b>");
			$this->isPreloader = true;
			
			include($preloader);
			$this->isPreloader = false;
			
			$this->core->log->syslog("Предзагрузчик запущен. Пут к файлу: <b>{$preloader}</b>");
		} else {
			$this->core->log->syslog("Предзагрузчик не найден. Пут для поиска: <b>{$preloader}</b>");
		}
		
		if(isset($this->core->siteObject['custom_preloader'])) {
			$this->core->log->syslog("Для этого сайта указан пользовательский предзагрузчик: <b>{$this->core->siteObject['custom_preloader']}</b>");
			$preloader = $this->core->CONFIG['module_path'].$this->core->siteObject['custom_preloader'].'/index.php';
			if(is_file($preloader)) {
				$this->core->log->syslog("Пользовательский предзагрузчик подключен: <b>{$this->core->siteObject['custom_preloader']}</b>");
				$core = $this->core;
				$this->isPreloader = true;
				include($preloader);
				$this->isPreloader = false;
			} else {
				$this->core->log->syslog("Пользовательский предзагрузчик не найден: <b>{$this->core->siteObject['custom_preloader']}</b>");
			}
		}
	}

	public function display_error($type = 404) {
		self::page($type);
	}

	private function load_module() {
		$module_path = $this->modules_path.$this->core->modules->this['location'];
		$config = $module_path.'config.php';
				
		if(!is_file($config)) {
			$this->core->log->syslog("Не найден конфигурационный файл модуля. Путь: <b>{$config}</b>");
			$this->core->display_error(404);
		}
		
		$core = $this->core;
		
		include $config;
		$this->core->log->syslog("Подключен файл конфигурации модуля <b>{$this->core->module_name}</b>");
		$this->core->modules->init();
		$this->core->tpl->assign('module', $this->core->modules->this);
		$this->core->log->syslog("Модуль <b>{$this->core->module_name}</b> загружен.");
		
		return true;
	}
	
	private function load_controller() {
		$filepath = $this->modules_path.$this->core->modules->this['location'].$this->core->modules->this['controllers_path'];
		$filename = ($this->core->controller) ? $filepath.$this->core->controller.'.php' : $filepath.$this->core->modules->this['controllers']['default'].'.php';
		$name = ($this->core->controller)? $this->core->controller : $this->core->modules->this['controllers']['default'];
		
		$this->core->log->syslog("Загружаем контроллер <b>{$name}</b>");
		$this->core->log->syslog("Путь для поиска контроллера <b>{$filepath}</b>");

		if(!in_array($name, $this->core->modules->this['controllers'])) {
			$this->core->log->syslog("Контроллера <b>{$name}</b> нет в списке доступных в настройках модуля.");
			$this->core->display_error(404);
		}
		
		if(!is_file($filename)) {
			$this->core->log->syslog("Контроллер <b>{$name}</b> есть в списке доступных, однако файл на диске отсутствует.");
			$this->core->display_error(404);
		}
		
		$controller = $this->core->controller_object = new controller($this->core);
		$controller->real_name = $this->core->controller;
		$core = $this->core;
		
		include $filename;
		$this->core->log->syslog("Контроллер подключен и запущен");
		
		return true;
	}
	
	public static function load($filename, $modulename = false) {
		$core = core::$instance;
		if($modulename == false) {
			$classPath = self::$preloader_path;
		} else {
			$classPath = ($modulename)? $core->CONFIG['module_path'].$modulename.'/' : self::$preloader_path;
		}		
		
		if(file_exists($classPath.'classes/'.$filename)) {
			require_once $classPath.'classes/'.$filename;
		} else if(file_exists($classPath.$filename)) {
		    require_once $classPath.$filename;
		}
	}
	
	public static function page($type) {
		$mpath = core::$instance->CONFIG['module_path'];
		
		switch($type) {			
			case 404:
			case 500:
			case 503:
			case 403:
				$module = $mpath.$type;
			break;
			case 401:
				$module = $mpath.'_auth';
			break;
			
			default:
				return false;
			break;
		}

		if(file_exists($module.'/index.php')) {
			$core = core::$instance;
			include $module.'/index.php';
			core::$instance->footer();
		} else {
			core::$instance->log->syslog('Модуль '.$type.' не найден');
			return false;
		}
	}
}


?>
