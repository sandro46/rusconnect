<?php
################################################################################
# ---------------------------------------------------------------------------- #
# @Author: Alexey Pshenichniy <inc.mcms@gmail.com> <M-zone Inc.> 2005-2009     #
# @Date: 20.05.2008                                                            #
# @license: GNU GENERAL PUBLIC LICENSE Version 2                               #
# ---------------------------------------------------------------------------- #
# M-cms v4.1 (core build - 4.102)                                              #
################################################################################


/**
 * Класс для работы с модулями. Базовый клас. Входит в состав ядра.
 */
class modules
{

	/**
	 * Переменная содержит информацию о текущем модуле
	 *
	 * @var unknown_type
	 */
	public $this = array();
	public $controllerLoad = null;
	
	private $info = array();
	private $modulesIndexer = array();
	private $core = false;



	function __construct($core)
	{
		$this->core = $core;
		$this->this['id_module']= 0;
		$this->this['sub_menu']= array();
		$this->this['uri']= array();
		$this->this['describe']= '';
		$this->this['location']= '';
		$this->this['tpl']= '';
	}

	/**
	 * Метод инициализации текущего модуля
	 */
	public function init()
	{
		if(isset($this->this['menu'])) {
			$sub_menu = array();
			foreach($this->this['menu'] as $menu_item) {
			    if($this->core->perm->check($menu_item['action_id'])) {
				    $sub_menu[] = $menu_item;
			    }
			}
		    unset($this->this['menu']);
		    $this->this['menu'] = $sub_menu;
		}
	}

	/**
	 * Метод добавляет контроллер к списко доступных в етом модуле
	 */
	public function add_controller()
	{
	    foreach(func_get_args() as $cntr) {
			$this->this['controllers'][] = $cntr;
		}
	}

	/**
	 * Метод добавляет контроллер вызываеммый по умолчанию (если вызванный контроллер не найден или недоступен или не выбран)
	 *
	 * @param string $controller_name
	 */
	public function add_default_controller($controller_name)
	{
		$this->this['controllers']['default'] = $controller_name;
	}

	/**
	 * Метод возвращает информацию о текущем модуле по id установленным в $core->module_name
	 * Возвращает true в случае успеха и false в случае неуудачи
	 *
	 * Если метод отработал верно, то результат записывается в массив $this->this[id] где id - это id модуля в базе (cделано для обхода повторных запросов)
	 */
	public function get_info()
	{
		$cached = false;
		
		if(isset($this->core->CONFIG['perfomance']) && isset($this->core->CONFIG['perfomance']['cache_modules']) && $this->core->CONFIG['perfomance']['cache_modules']) {
			$cacheVar = md5("moduleId:{$this->core->module_name}");
			$cached = true;
			if(isset($_SESSION[$cacheVar])) {
				if(!is_array($_SESSION[$cacheVar])) return false;
				$this->core->module_id =  $_SESSION[$cacheVar]['id_module'];
				$this->info[$this->core->module_id] = $_SESSION[$cacheVar];
				$this->this = $this->info[$this->core->module_id];
				return true;	
			}
		}
		
		$this->core->db->select()->from('mcms_modules')->fields('*')->where("name = '{$this->core->module_name}'");
		$this->core->db->execute();
		$this->core->db->add_fields_deform(array('location'));
		$this->core->db->add_fields_func(array('modules::fix_location'));	
		$this->core->db->get_rows(1);
		if(!$this->core->db->rows || !isset($this->core->db->rows['id_module'])) return false;
		
		$this->core->module_id = $this->core->db->rows['id_module'];
		$this->info[$this->core->module_id] = $this->core->db->rows;
		$this->info[$this->core->module_id]['uri'] = $this->get_module_uri($this->info[$this->core->module_id]['location']);
		$this->this = $this->info[$this->core->module_id];
						
		if($cached) $_SESSION[$cacheVar] = $this->info[$this->core->module_id];
		
		return true;
	}
	
	public function getModuleIdByName($name)
	{
		if(isset($this->modulesIndexer[$name])) return $this->modulesIndexer[$name];
				
		$this->core->db->select()->from('mcms_modules')->fields('id_module')->where("`name` = '". $name. "'");
		$this->core->db->execute();
		
		$this->modulesIndexer[$name] = $this->core->db->get_field();
		
		return $this->core->db->get_field();		
	}

	/**
	 * Метод генерирует список модулей на основе прав доступа текущего пользователя пользователя ($core->user->id)
	 */
	public function modules_list_use_permission()
	{    	    
    	$cache_var = $this->core->tpl->get_cache_var_name('modules::modules_list_use_permission', array('_site'=>true, '_editor'=>true, '_user_id'=>true, '_lang'=>true));
    
    	if(!$return = $this->core->memcache->get($cache_var))
    	{
		       $sql= "SELECT
						DISTINCT(gra.id_module) as id,
						m.name as name,
						m.type as type,
						m.describe as 'describe',
						m.menu_order as menu_order,
						'1' as is_module,
						IF((SELECT count(*) FROM mcms_history as h WHERE h.gr_name = m.name) > 0, CONCAT('/{$this->core->CONFIG['lang']['name']}/history/list/modulename/',m.name,'/'), NULL) as history_link,
						(SELECT d.id_doc FROM mcms_docs AS d WHERE d.title = m.describe AND d.id_site = 1 LIMIT 1) as help_id
					FROM
						mcms_group_action as gra
						LEFT JOIN mcms_modules as m
						ON m.id_module = gra.id_module
					WHERE
						gra.id_group IN (
							SELECT ugr.id_group
							FROM mcms_user_group AS ugr
							WHERE ugr.id_user=".$this->core->user->id." AND  
								  ugr.id_group IN (SELECT grs.id_group FROM mcms_group_sites as grs WHERE grs.id_site = ".$this->core->edit_site.")
										)
						AND (
							m.menu_visible =1
							AND m.lang_id = ".$this->core->CONFIG['lang']['id']."
							AND m.menu_parent_id = 0
							)
						AND m.id_module IN (SELECT sm.id_module FROM mcms_sites_modules as sm WHERE sm.id_site = ".$this->core->edit_site.")

				 ORDER BY menu_order";
				

    		$this->core->db->query($sql);
    		$this->core->db->get_rows();
    
    		$return = $this->core->db->rows;
    
    	}


		return $return;
	}

	/**
	 * Метод возвращает пути модуля к различным его директориям
	 */
	private function get_module_uri($location)
	{
		$ret['dir']= $location;
		$ret['include']= $ret['dir']. 'includes/';
		$ret['class'] = $ret['dir'].'classes/';
		return $ret;
	}
	
	public static function fix_location($location)
	{
		return (substr($location, 0,1) == '/')? substr($location, 1) : $location;
	}

}

class module {
	
	protected $tpl;
	protected $db;
	protected $lib;
	protected $log;
	protected $user;
	protected $memcache;
	protected $widgets;
	protected $url_parser;
	protected $ajax;
	protected $loader;
	protected $core;
	
	public function module() {
		$this->core = core::$instance;
		$this->tpl = $this->core->tpl;
		$this->db = $this->core->db;
		$this->lib = $this->core->lib;
		$this->log = $this->core->log;
		$this->user = $this->core->user;
		$this->memcache = $this->core->memcache;
		$this->widgets = $this->core->widgets;
		$this->url_parser = $this->core->url_parser;
		$this->ajax = $this->core->ajax;
		$this->loader = $this->core->loader;
	}
	
	
}

?>