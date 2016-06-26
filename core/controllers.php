<?php
################################################################################
# ---------------------------------------------------------------------------- #
# @Author: Alexey Pshenichniy <inc.mcms@gmail.com> <M-zone Inc.> 2005-2009     #
# @Date: 20.05.2008                                                            #
# @license: GNU GENERAL PUBLIC LICENSE Version 2                               #
# ---------------------------------------------------------------------------- #
# M-cms v4.1 (core build - 4.102)                                              #
################################################################################

class controller
{
	public $name = '';
	public $descr = '';
	public $id = 0;
	public $access_list = array();
	public $real_name = '';
	public $show_help = 1;

	public $cache_param = array();
	public $cache_expire = 0;
	public $cached = 0;
	public $tpl = '';
	public $content = '';
	public $cachedAll = true;
	
	public $class_url = '';
	public $include_url = '';
	private $core = false;
	

	public function __construct($core) 
	{
		$this->core = $core;
	}
	
	/**
	 * Метод инициализации контроллера. Вызывается при загрузке контроллера
	 * Так же метод проверяет имеет ли доступ пользователь к етому контроллеру.
	 */
	public function init()
	{
		if(!$this->core->perm->check($this->id)) {	
			$this->core->error_page(403);
		}
		
		//$this->access_list = $core->perm->get_perm_module();
		//$this->name = $this->access_list[$this->id]['name'];
		//$this->descr = $this->get_info($this->id);

		$this->core->tpl->assign('controller', array('name'=>$this->core->controller, 'real_name'=>$this->core->controller));
		$this->class_url = $this->core->CONFIG['module_path'].$this->core->modules->this['uri']['class'];		
		$this->include_url = $this->core->CONFIG['module_path'].$this->core->modules->this['uri']['include'];		
		
		if(is_callable($this->core->modules->controllerLoad)) {
			call_user_func($this->core->modules->controllerLoad, $this, $this->core);
		}
	}
	
	/**
	 * Метод перенаправления страницы
	 *
	 * @param string $url - URL куда следует перенаправить пользователя
	 * @param string $message - Сообщение которое будет выводится пользователю при перенаправлении
	 */
	public function redirect($url, $message, $title = 'Error', $timeout = 2000)
	{
		echo '<script type="javascript">document.location.href="'.$url.'"</script><a href="'.$url.'">Продолжить...</a>';
		die();
	}
	
	/**
	 * Метод првоеряет состояние кеширования результата работы текущего контроллера
	 * Метод следует вызывать только после инициализации и указания параметров кеширования для контроллера и используемого шаблона.
	 * 
	 * Если текущий контроллер был закеширован и кеш не устарел то он передается шаблонизатору и выволнение контроллера прекращается.
	 * Если кеш не был найден то выполнение скрипта продолжается.
	 */
	public function cached()
	{		
		if($this->cached) {	
			$varname_md5 = $this->core->tpl->get_cache_var_name($this->tpl, $this->cache_param);
			$html = $this->core->memcache->get($varname_md5);	
			if($html) {
				$this->content = $html;
				$this->core->log->add_cache_tpl($core->module_name.'/'.$this->tpl, $varname_md5, $this->cache_expire);
				$this->tpl = '';
				$this->core->footer();
					die();
			}
		}
	}
	
	/**
	 * Метод вытаскивает информацию о контроллере id которого передам
	 *
	 * @param integer $id
	 * @return array - controller info
	 */
	public function get_info($id)
	{
		$this->core->db->select()->from('mcms_action')->fields('name')->where('id_action = '.$id.' AND lang_id = '.$core->CONFIG['lang']['id']);
		$this->core->db->execute();
		
		return $this->core->db->get_field();
	}
	
	
	public function load($filename, $type = 'base', $class_url = false)
	{
		if($class_url) {	
			if(is_file($class_url.$filename)) {	
				$this->core->log->syslog("Из контроллера вызвано подключение файла <b>{$filename}</b>");
				require_once $class_url.$filename;
			} else {
				$this->core->log->syslog("Из контроллера вызвано подключение файла <b>{$filename}</b>, но файл не был подключен. Не найден. Путь: <b>{$class_url}</b>");
			}
			
			return true;	
		} 
		
		if($type != 'base') {
			$path = $this->getPossibleModulePath($type);
			if(is_file($path[0].$filename)) {
				$this->core->log->syslog("Из контроллера вызвано подключение файла стороннего модуля. Файл: <b>{$filename}</b> Модуль: <b>{$type}</b>");
				require_once $path[0].$filename;
			} elseif(is_file($path[1].$filename)) {
				$this->core->log->syslog("Из контроллера вызвано подключение файла стороннего модуля. Файл: <b>{$filename}</b> Модуль: <b>{$type}</b>");
				require_once $path[1].$filename;
			} else {
				$this->core->log->syslog("Из контроллера вызвано подключение файла стороннего модуля. Файл не был найден. Файл: <b>{$filename}</b> Модуль: <b>{$type}</b>");
			}
			return true;
		}
		
		
		if(file_exists($this->class_url.$filename)) {
			$this->core->log->syslog("Из контроллера вызвано подключение файла <b>{$filename}</b>");
			require_once $this->class_url.$filename;
		} elseif(file_exists($this->include_url.$filename)) {
			$this->core->log->syslog("Из контроллера вызвано подключение файла <b>{$filename}</b>");
			require_once $this->include_url.$filename;
		} else {
			$this->core->log->syslog("Из контроллера вызвано подключение файла <b>{$filename}</b>, но файл не был подключен. Не найден. Путь: <b>{$class_url}</b>");
		}
		
		return true;
	}
	
	private function getPossibleModulePath($name) {	
		$class_url = $this->core->CONFIG['module_path'].$name.'/classes/';		
		$include_url = $this->core->CONFIG['module_path'].$name.'/includes/';	
		
		return array($class_url, $include_url);
	}
	
	
	
}
?>