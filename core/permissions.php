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
 * Класс реализует методы для работы с правами доступа. Класс входит в состав ядра
 */
class permissions
{

	private $list= array();
	private $core = false;
	private $module_id = 0;
	private $site_id = 0;

	function __construct($core)
	{
		$this->core = $core;
	}

	
	public function check($action_id= 0, $module_id= 0, $site_id = 0)
	{
		if(!$action_id && !$module_id && (!$site_id || $site_id == $this->site_id)) {
			$this->get_permissions_list();
			return count($this->list) > 0;
		}
		
		if($action_id && !$module_id) {
			return in_array($action_id, $this->list);		
		}
		
		if(!$action_id && $module_id) {
			if($this->module_id == $module_id) {
				return count($this->list) > 0;
			} else {
				return $this->check_module($module_id);
			}
		}
		
		if($action_id && $module_id) {
			if($module_id == $this->module_id) {
				return in_array($action_id, $this->list);
			}
			
			return $this->check_action($action_id, $module_id);
		}
		
		if($site_id) {
			return 	$this->check_site($site_id);
		}
		
		
		return false;
	}
	
	public function check_site($id_site = 0, $userId = 0) 
	{
		$id_site = ($id_site && intval($id_site))? intval($id_site) : (($this->core->is_admin())? $this->core->edit_site : $this->core->site_id);
		$userId = ($userId)? $userId : $this->core->user->id;
		
		$sql = "SELECT 1 FROM mcms_group_sites as gs 
						LEFT JOIN mcms_user_group as ug ON gs.id_group = ug.id_group 
				WHERE 
					ug.id_user = {$userId} AND 
					gs.id_site = {$id_site} 
				LIMIT 1";
		
		$this->core->db->query($sql);
		return $this->core->db->num_rows() == 1;
	}
	
	private function check_module($id_module = 0)
	{
		$id_module = ($id_module && intval($id_module))? intval($id_module) : $this->core->module_id;
		$id_site = ($this->core->is_admin())? $this->core->edit_site : $this->core->site_id;
		
		if(!$id_module) return false;
		
		$sql = "SELECT 1 FROM mcms_sites_modules as sm 
						LEFT JOIN mcms_group_action as ga ON sm.id_module = ga.id_module
						LEFT JOIN mcms_group_sites as gs ON ga.id_group = gs.id_group
						LEFT JOIN mcms_user_group as ug ON gs.id_group = ug.id_group		
				WHERE 
					ug.id_user = {$this->core->user->id} AND 
					gs.id_site = {$id_site} AND
					sm.id_site = {$id_site} AND
					sm.id_module = {$id_module}
				LIMIT 1";
		
		$this->core->db->query($sql);
		return $this->core->db->num_rows() == 1;
	}
	
	private function check_action($id_action = 0, $id_module = 0)
	{
		$id_action = ($id_action && intval($id_action))? intval($id_action) : false;
		$id_module = ($id_module && intval($id_module))? intval($id_module) : $this->core->module_id;
		$id_site = ($this->core->is_admin())? $this->core->edit_site : $this->core->site_id;
		
		if(!$id_module) return false;
		if(!$id_action) return false;
		
		$sql = "SELECT 1 FROM mcms_sites_modules as sm 
						LEFT JOIN mcms_group_action as ga ON sm.id_module = ga.id_module
						LEFT JOIN mcms_group_sites as gs ON ga.id_group = gs.id_group
						LEFT JOIN mcms_user_group as ug ON gs.id_group = ug.id_group		
				WHERE 
					ug.id_user = {$this->core->user->id} AND 
					gs.id_site = {$id_site} AND
					ga.id_action = {$id_action} AND
					sm.id_site = {$id_site} AND
					sm.id_module = {$id_module} AND
					
				LIMIT 1";
		
		$this->core->db->query($sql);
		return $this->core->db->num_rows() == 1;
	}
	
	private function get_permissions_list($id_module = 0)
	{
		$this->module_id = ($id_module && intval($id_module))? intval($id_module) : $this->core->module_id;
		$this->site_id = ($this->core->is_admin())? $this->core->edit_site : $this->core->site_id;
				
		if(!$this->module_id) return false;
		
		$cached = false;
		if(isset($this->core->CONFIG['perfomance']) && isset($this->core->CONFIG['perfomance']) && $this->core->CONFIG['perfomance']['cache_rules']) {
			$cached = true;
			$cacheVar = "permission;userId:{$this->core->user->id};sitreId:{$this->site_id};moduleId:{$this->module_id}";
			if(isset($_SESSION[$cacheVar]) && is_array($_SESSION[$cacheVar])) {
				return $this->list = $_SESSION[$cacheVar];
			}
		}
		
		$sql = "SELECT id_action FROM mcms_group_action as ga
						LEFT JOIN mcms_sites_modules as sm ON ga.id_module = sm.id_module 
						LEFT JOIN mcms_group_sites as gs ON ga.id_group = gs.id_group
						LEFT JOIN mcms_user_group as ug ON gs.id_group = ug.id_group		
				WHERE 
					ug.id_user = {$this->core->user->id} AND 
					gs.id_site = {$this->site_id} AND
					sm.id_site = {$this->site_id} AND
					sm.id_module = {$this->module_id}";
		
		$this->core->db->query($sql);
		$this->core->db->get_rows(false,false, 'id_action');
		
		if($cached) {
			$_SESSION[$cacheVar] = $this->core->db->rows;
		}
		
		return $this->list = $this->core->db->rows;
	}
}
?>