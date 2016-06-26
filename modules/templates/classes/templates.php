<?php 

class ModuleTemplates extends module {
	
	public $defaultTheme = '';
	
	public function getList($instance, $start = 0, $limit = 20, $sortBy = 'date', $sortType = 'DESC', $filters = false) {
		$sql = "SELECT 
					SQL_CALC_FOUND_ROWS
					tpl.id_template as id, 
					tpl.id_site, 
					tpl.id_lang, 
					tpl.theme, 
					tpl.name_module, 
					tpl.name, 
					tpl.description, 
					tpl.del, 
					tpl.date,
				
					lang.name as lang,
					u.name as user_name,
					s.name as site_name,
				
					(SELECT count(*) FROM mcms_history as h WHERE h.tablename = 'mcms_tmpl' AND h.site_id = tpl.id_site AND h.primary_key = 'id_template:id_lang' AND h.primary_value = CONCAT(tpl.id_template,':',tpl.id_lang) AND h.action = 'edit') as edit_history_count					
				FROM
					mcms_tmpl as tpl
						JOIN mcms_language as lang ON lang.id = tpl.id_lang
						JOIN mcms_user as u ON u.id_user = tpl.user_edit_id
						JOIN mcms_sites as s ON s.id = tpl.id_site
				
				WHERE
					 1=1 ";
		
		if(isset($filters['name'])) {
			$filters['name'] = mysql::str($filters['name']);
			$sql .= " AND (LOWER(tpl.name) LIKE LOWER('%{$filters['name']}%') OR LOWER(tpl.description) LIKE LOWER('%{$filters['name']}%'))";
		}
		
		if(isset($filters['name_module'])) {
			if(is_array($filters['name_module'])) {
				$filters['name_module'] = array_map(function($item){
					$item = intval($item);
					return ($item > 0)? $item : null;
				}, $filters['name_module']);
					$sql .= " AND tpl.name_module IN(".implode(',',$filters['name_module']).")";
			} else {
				$filters['name_module'] = mysql::str($filters['name_module']);
				$sql .= " AND tpl.name_module = '{$filters['name_module']}'";
			}
		}
 
		if(isset($filters['site_id'])) {
			$filters['site_id'] = intval($filters['site_id']);
			$_SESSION['templates_site_id'] = $filters['site_id'];
			$sql .= " AND tpl.id_site = {$filters['site_id']}";
		} else {
			$site_id = (isset($_SESSION['templates_site_id']))? $_SESSION['templates_site_id'] : $this->core->edit_site;
			$sql .= " AND tpl.id_site = {$site_id}";
		}
		
		if(isset($filters['theme'])) {
			if($filters['theme'] != '*' && $filters['theme']) {
				$filters['theme'] = mysql_real_escape_string($filters['theme']);
				$sql .= " AND tpl.theme = '{$filters['theme']}'";
			} 
		} else {
			$site_id = (isset($_SESSION['templates_site_id']))? $_SESSION['templates_site_id'] : $this->core->edit_site;
			$theme = ($site_id == 1)? 'green' : false;
			if($theme) $sql .= " AND tpl.theme = '{$theme}'";
		}
		
		if(isset($filters['id'])) {
			$filters['id'] = intval($filters['id']);
			$sql .= " AND tpl.id_template = {$filters['id']} ";
		}
		
		if(isset($filters['html'])) {
			$filters['html'] = mysql_real_escape_string($filters['html']);
			$sql .= " AND LOWER(tpl.source) LIKE LOWER('%{$filters['html']}%') ";
		}
		
		if(isset($filters['trash'])) {
			$sql .= " AND tpl.del = 1 ";
		} else {
			$sql .= " AND tpl.del = 0 ";
		}
				
		
		$sql.=" ORDER BY {$sortBy} {$sortType} LIMIT {$start},{$limit}";
		//echo $sql;	
		$this->db->query($sql);
		$this->db->add_fields_deform(array('date'));
		$this->db->add_fields_func(array('dateAgoSS'));
		$this->db->get_rows();
		
		return $this->db->rows;
	}
	
	public function getAllThems($site_id = 0) {
		$site_id = ($site_id)? $site_id : ((isset($_SESSION['templates_site_id']))? $_SESSION['templates_site_id'] : $this->core->edit_site);
		
		$sql = "SELECT t.name FROM mcms_themes as t WHERE t.id_site = {$site_id}";
		
		
		
		$this->db->query($sql);
		$this->db->get_rows();
		
		return $this->db->rows;
	}
	
	public function getAllModules($site_id = 0) {
		$site_id = ($site_id)? $site_id : ((isset($_SESSION['templates_site_id']))? $_SESSION['templates_site_id'] : $this->core->edit_site);
		$sql = "SELECT tpl.name_module as name  FROM mcms_tmpl as tpl WHERE tpl.id_site = {$site_id} GROUP BY tpl.name_module ORDER BY tpl.name_module";
		$this->db->query($sql);
		$this->db->get_rows();

		return $this->db->rows;
	}
	
	public function getAllSites() {
		/* IF it is client cabinet, select only user sites */
		$sql = "SELECT id, name, server_name FROM mcms_sites";
		
		$this->db->query($sql);
		$this->db->get_rows();
		
		return $this->db->rows;
	}
	
	public function setDefaultTheme($name = false) {
		if(!$name) {
			$name = (isset($_SESSION['default_edit_theme']))? $_SESSION['default_edit_theme'] : (($this->core->edit_site == 1)? 'green' : 'default');
		}
		
		$this->defaultTheme = $name;
		$_SESSION['default_edit_theme'] = $name;
	}
	
	public function getSource($id, $extended = false) {
		$this->db->select()->from('mcms_tmpl')->fields('*')->where("id_template = {$id} and id_lang = {$this->core->langId}");
		$this->db->execute();
		$this->db->get_rows(1);
		$out = $this->db->rows;
		
		$out['source'] = stripslashes($this->db->rows['source']);	
		$out['title'] = 'Редактирование шаблона '.$this->db->rows['name_module'].'/'.$this->db->rows['name'];		
		
		if($extended) {
			$out['all_modules'] = array();
			$out['all_sites'] = $this->getAllSites();
			
			$sql = "SELECT m.name, ms.id_site, (SELECT COUNT(*) FROM mcms_tmpl as t WHERE t.id_site = ms.id_site AND t.name_module = m.name AND t.id_lang = {$this->core->langId}) as cnt_tpl FROM mcms_modules as m LEFT JOIN mcms_sites_modules as ms ON m.id_module = ms.id_module ORDER BY ms.id_site, m.name";
			$this->db->query($sql);
			$this->db->get_rows();
			
			foreach($this->db->rows as $item) {
				if(!isset($out['all_modules'][$item['id_site']])) $out['all_modules'][$item['id_site']] = array();
				$out['all_modules'][$item['id_site']][] = $item;
			}
		}
		
		return $out;
	}
	
	public function updateSource($data) {
		if(!is_array($data) || count($data) < 7) return 'Не все поля заполнены. (1)';
		if(!isset($data['id']) || !isset($data['site']) || !isset($data['lang']) || !isset($data['theme'])) return 'Не все поля заполнены. (2)';
		if(!isset($data['module']) ||!isset($data['name']) ||!isset($data['description']) || !isset($data['source'])) return 'Не все поля заполнены. (3)';
			
		$site_id = (isset($_SESSION['templates_site_id']))? $_SESSION['templates_site_id'] : $this->core->edit_site;
		
		
		if(intval($data['id']) < 0) {
			$mcms_tmpl = array(
				'id_site'=>$site_id,
				'id_lang'=>$this->core->langId,
				'theme'=>mysql::str($data['theme']),
				'name_module'=>mysql::str($data['module']),
				'name'=>mysql::str($data['name']),
				'description'=>mysql::str($data['description']),
				'source'=>mysql::str($data['source']),
				'date'=>time(),
				'del'=>0
			);
			
			if(!strlen($mcms_tmpl['theme']) || !strlen($mcms_tmpl['name_module']) || !strlen($mcms_tmpl['name']) || !strlen($mcms_tmpl['description']) || !strlen($mcms_tmpl['source'])) return 'Не все поля заполнены. (4)';
			$this->db->autoupdate()->table('mcms_tmpl')->data(array($mcms_tmpl));
			$this->db->execute();
		} else {
			$mcms_tmpl = array(
				'id_template'=>intval($data['id']),
				'id_site'=>$site_id,
				'id_lang'=>intval($data['lang']),
				'theme'=>mysql::str($data['theme']),
				'name_module'=>mysql::str($data['module']),
				'name'=>mysql::str($data['name']),
				'description'=>mysql::str($data['description']),
				'source'=>mysql::str($data['source']),
				'date'=>time()
			);
			
			if(!$mcms_tmpl['id_template'] || !$mcms_tmpl['id_site'] || !$mcms_tmpl['id_lang']) return 'Не все поля заполнены. (5)';
			$this->db->autoupdate()->table('mcms_tmpl')->data(array($mcms_tmpl))->primary('id_template','id_lang');
			$this->db->execute();
			$this->core->history->add('mcms_tmpl',array(array('id_template', 'id_lang'),array(intval($data['id']), 1)),'edit');
		}
		
		
		
		if(intval($data['id']) < 0) {
			$mcms_tmpl['id_template'] = $this->db->insert_id;
			unset($mcms_tmpl['source']);
			
			return $mcms_tmpl;
		}
		
		return true;
	}
	
	public function remove($id_template, $id_lang) {
		if(!$id_template = intval($id_template)) return false;
		//if(!$id_lang = intval($id_lang)) return false;
		
		$site_id = (isset($_SESSION['templates_site_id']))? $_SESSION['templates_site_id'] : $this->core->edit_site;
		
		$sql = "UPDATE mcms_tmpl SET del=1 WHERE id_template = {$id_template} AND id_site = {$site_id}";
		$this->db->query($sql);
		
		return true;
	}

	// new version
	public function save($id, $data) {
		if(empty($data['source'])) return -1;
		if(empty($data['name'])) return -2;
		if(empty($data['theme'])) return -3;
		if(empty($data['name_module'])) return -4;
		
		$id = intval($id);
		
		if($id) {
			$query = array(
				'id_template'=>$id,
				'theme'=>mysql::str($data['theme']),
				'name_module'=>mysql::str($data['name_module']),
				'name'=>mysql::str($data['name']),
				'description'=>mysql::str($data['description']),
				'source'=>mysql::str($data['source']),
				'id_lang'=>$this->core->langId
			);
			
			$this->db->autoupdate()->table('mcms_tmpl')->data(array($query))->primary('id_template','id_lang');
			$this->db->execute();
			
			if($this->core->history instanceof history) {
				$this->core->history->add('mcms_tmpl',array(array('id_template', 'id_lang'),array($id, 1)),'edit');
			}
			return true;
		} else {
			$query = array(
					'id_site'=>$this->core->edit_site,
					'theme'=>mysql::str($data['theme']),
					'name_module'=>mysql::str($data['name_module']),
					'name'=>mysql::str($data['name']),
					'description'=>mysql::str($data['description']),
					'source'=>mysql::str($data['source']),
					'date'=>time(),
					'del'=>0,
					'id_lang'=>$this->core->langId
			);
				
			$this->db->autoupdate()->table('mcms_tmpl')->data(array($query));
			$this->db->execute();
			return $this->db->insert_id;
		}
	}
	
	public function getHistory($id, $page = 0) {
		return $this->core->history->getSpecificList(array('id_template:id_lang', "{$id}:{$this->core->langId}"),'templates',array('by' => 'date', 'type' => 'DESC'), 10, $page * 10);
	}

	public function rollback($id) {
		if(!$id || !intval($id)) return false;
		$this->core->history->rollback(intval($id));
	}
}





?>
