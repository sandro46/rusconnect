<?php
class ModuleGroups extends module {
	
	public $site_id = 0;

	
	public function init() {
		$this->site_id = (isset($_SESSION['current_group_site']))? intval($_SESSION['current_group_site']) : $this->core->edit_site;
	}
	
	public function getTree($parentId = 0, $type = 0, $sub = 0) {
		if(!$this->site_id) $this->init();
		
		$parentId = intval($parentId);
		$type = intval($type);
		$sub = intval($sub);
		
		if($type == 0) {
			return $this->getGroups();
		}
		
		if($type == 1) {
			return ($parentId)? $this->getModules($parentId) : false;
		}
		
		if($type == 2) {
			return ($parentId && $sub)? $this->getControllers($parentId, $sub) : false;
		}
		
		return false;
	}
	
	public function addModuleInGroup($moduleId, $groupId) {
		if(!$moduleId = intval($moduleId)) return false;
		if(!$groupId = intval($groupId)) return false;
		
		$mcms_sites_modules = array(
			'id_site'=>$this->site_id,
			'id_module'=>$moduleId		
		);
		
		$mcms_group_action = array(
			'id_group'=>$groupId,
			'id_module'=>$moduleId,
			'id_action'=>1	
		);
		
		$this->db->autoupdate()->table('mcms_sites_modules')->data(array($mcms_sites_modules));
		$this->db->execute();
		
		$this->db->autoupdate()->table('mcms_group_action')->data(array($mcms_group_action));
		$this->db->execute();
		
		return true;
	}
	
	public function addControllerInModule($controllerId, $moduleId, $groupId) {
		
		if(!$controllerId = intval($controllerId)) return false;
		if(!$moduleId = intval($moduleId)) return false;
		if(!$groupId = intval($groupId)) return false;
		
		$mcms_group_action = array(
			'id_group'=>$groupId,
			'id_module'=>$moduleId,
			'id_action'=>$controllerId	
		);
		
		$this->db->autoupdate()->table('mcms_group_action')->data(array($mcms_group_action));
		$this->db->execute();
		
		return true;
	}
	
	
	
	public function deleteNode($id) {
		
		
	}
	
	public function copyNodeTo($id, $parentId) {
		
		
	}
	
	public function moveNodeTo($id, $parentId) {
		
		
	}
	
	public function setSiteId($id) {
		if(!$id = intval($id)) return false; 
		$_SESSION['current_group_site'] = $id;
		session_commit();
		return true;
	}
	
	public function getModulesListExcludeGroup($groupId) {
		if(!$groupId = intval($groupId)) return false; 
		
		$list = $this->getModules($groupId, true);
		$listCount = count($list);
		$listStr = implode(',',$list);
		
		$sql = "SELECT m.id_module as id, m.describe as name FROM mcms_modules as m WHERE m.id_module ";
		$sql .= ($listCount)? " NOT IN({$listStr}) " : "";
		$sql .= " ORDER BY m.describe";
		$this->db->query($sql);
		$this->db->get_rows();

		return $this->db->rows;
	}
	
	public function getControllersListExcludeId($moduleId, $groupId) {
		if(!$groupId = intval($groupId)) return false;
		if(!$moduleId = intval($moduleId)) return false;
		
		$list = $this->getControllers($moduleId, $groupId, true);
		$listCount = count($list);
		$listStr = implode(',',$list);
		
		$sql = "SELECT a.id, CONCAT(a.name, ' (', a.id, ')') as name FROM mcms_action as a WHERE a.id ";
		$sql .= ($listCount)? " NOT IN({$listStr}) " : "";
		$sql .= " ORDER BY a.name";
		$this->db->query($sql);
		$this->db->get_rows();

		return $this->db->rows;
	}
	
	public function deleteGroup($id) {
		if(!$id = intval($id)) return false;
		
		$sql = "DELETE FROM mcms_group_sites WHERE id_group = {$id} AND id_site = {$this->site_id}";
		$this->db->query($sql);
		
		return true;
	}
	
	public function deleteModule($moduleId, $groupId) {
		if(!$groupId = intval($groupId)) return false;
		if(!$moduleId = intval($moduleId)) return false;
		
		$sql = "DELETE FROM mcms_group_action WHERE id_module = {$moduleId} AND id_group = {$groupId}";
		$this->db->query($sql);
		
		return true;
	}
	
	public function deleteController($moduleId, $groupId, $controllerId) {
		if(!$groupId = intval($groupId)) return false;
		if(!$moduleId = intval($moduleId)) return false;
		if(!$controllerId = intval($controllerId)) return false;
		
		$sql = "DELETE FROM mcms_group_action WHERE id_module = {$moduleId} AND id_group = {$groupId} AND id_action = {$controllerId}";
		$this->db->query($sql);
		
		return true;
	}
	
	public function copyModule($moduleId, $fromGroupId, $toGroupId) {
		if(!$moduleId = intval($moduleId)) return false;
		if(!$fromGroupId = intval($fromGroupId)) return false;
		if(!$toGroupId = intval($toGroupId)) return false;
		
		$sql = "INSERT INTO mcms_group_action (id_group, id_module, id_action) 
					SELECT {$toGroupId}, {$moduleId}, ma.id_action 
				FROM 
					mcms_group_action as ma 
				WHERE 
					ma.id_group = {$fromGroupId} AND
					ma.id_module = {$moduleId}";
		$this->db->query($sql);
		return true;
	}
	
	public function copyController($controllerId, $fromGroupId, $fromModuleId, $toGroupId, $toModuleId) {
		if(!$controllerId = intval($controllerId)) return false;
		if(!$fromGroupId = intval($fromGroupId)) return false;
		if(!$fromModuleId = intval($fromModuleId)) return false;
		if(!$toGroupId = intval($toGroupId)) return false;
		if(!$toModuleId = intval($toModuleId)) return false;
		
		$mcms_group_action = array(
			'id_group'=>$toGroupId,
			'id_module'=>$toModuleId,
			'id_action'=>$controllerId
		);
		
		$this->db->autoupdate()->table('mcms_group_action')->data(array($mcms_group_action));
		$this->db->execute();
		
		return true;
	}
	
	public function addNewGroup($name) {
		$name = mysql::str(urldecode(decode_unicode_url($name)));
		$mcms_group = array('name'=>$name);
		
		$this->db->autoupdate()->table('mcms_group')->data(array($mcms_group));
		$this->db->execute();
		
		$groupId = $this->db->insert_id;
		
		$mcms_group_sites = array(
			'id_group'=>$groupId,
			'id_site'=>$this->site_id
		);
		
		$this->db->autoupdate()->table('mcms_group_sites')->data(array($mcms_group_sites));
		$this->db->execute();
		
		$mcms_user_group = array(
			'id_user'=>1,
			'id_group'=>$groupId
		);
		
		$this->db->autoupdate()->table('mcms_user_group')->data(array($mcms_user_group));
		$this->db->execute();
		
		return true;
	}
	
	
	
	private function getGroups() {
		$sql = "SELECT g.id, g.name, 0 as type, 1 as is_folder FROM mcms_group as g LEFT JOIN mcms_group_sites as gs ON g.id = gs.id_group WHERE gs.id_site = {$this->site_id} ";
		$this->db->query($sql);
		$this->db->get_rows();
		
		return $this->db->rows;	
	}
	
	private function getModules($groupId, $onlyId=false) {
		$sql = "SELECT DISTINCT m.id_module as id";
		
		if(!$onlyId) {
			$sql .= ", CONCAT(m.describe, ' (', m.id_module, ')') as name, 1 as type, gs.id_group as sub1, 1 as is_folder ";
		}
		
		$sql .= " FROM mcms_modules as m 
					LEFT JOIN mcms_sites_modules as sm ON m.id_module = sm.id_module
					LEFT JOIN mcms_group_action as ga ON m.id_module = ga.id_module
					LEFT JOIN mcms_group_sites as gs ON ga.id_group = gs.id_group
				  WHERE
					sm.id_site = {$this->site_id} AND
					gs.id_group = {$groupId} AND
					gs.id_site = {$this->site_id} ";
		
		$sql .= (!$onlyId)? " ORDER BY m.describe " : '';
		
		$this->db->query($sql);
		
		if($onlyId) {
			$this->db->get_rows(false, false, 'id');
		} else {
			$this->db->get_rows();
		}
		
		return $this->db->rows;	
	}
	
	private function getControllers($moduleId, $groupId, $onlyId=false) {
		$sql = "SELECT ma.id ";
		if(!$onlyId) {
			$sql .= ",  CONCAT(ma.name, ' (', ma.id, ')') as name, 2 as type, ga.id_group as sub1, ga.id_module as sub2, 0 as is_folder ";
		} 
		$sql .= " FROM mcms_action as ma
					LEFT JOIN mcms_group_action as ga ON ma.id = ga.id_action
					LEFT JOIN mcms_group_sites as gs ON ga.id_group = gs.id_group
					LEFT JOIN mcms_sites_modules as sm ON ga.id_module = sm.id_module
				WHERE 
					sm.id_site = {$this->site_id} AND
					sm.id_module = {$moduleId} AND
					gs.id_site = {$this->site_id} AND
					gs.id_group = {$groupId}
				ORDER BY ma.name";
		$this->db->query($sql);
		if($onlyId) {
			$this->db->get_rows(false, false, 'id');
		} else {
			$this->db->get_rows();
		}
		
		return $this->db->rows;	
	}
}
?>