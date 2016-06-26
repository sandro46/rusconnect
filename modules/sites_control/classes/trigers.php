<?php
class trigers
{

	private $tables = array();
	private $fields = array();
	private $triger_names = array();	
	private $sql_colection = array();
	private $define_triggers = array();
	
	
	public $triger_status = array();
	public $bases_list = array();
	public $ignor_tables = array();
	
	public $droped_trigers = array();
	public $added_triggers = array();
	
	public $auto_added      = 1;
	
	public function __construct()
	{
	
		$this->triger_names = array('_after_ins_tr', '_after_upd_tr', '_after_del_tr');
		$this->ignor_tables = array('sessions', 'mcms_logs', 'mcms_history', 'mcms_history_data', 'mcms_history_fields', 'mcms_user', 'mcms_logs_access', 'mcms_logs_error', 'mcms_user_group', 'mcms_group', 'mcms_marketing_groups', 'mcms_marketing_units');
		
		$this->get_bases_list();
		$this->get_tables();
		$this->get_all_triggers();
	}
	
	public function check_all_triggers()
	{
		global $core;
		
		foreach($this->tables as $tablename) 
			{
			if(!in_array($tablename, $this->ignor_tables))
				{
				$this->triger_status[$tablename] = $this->define_triggers[$tablename];
				}
			}
		
		return $this->triger_status[$tablename];
	}
	
	public function replace_all_triger()
	{
		global $core;
		
		if(!count($this->triger_status)) $this->check_all_triggers();
		if(!$this->fields) $this->get_all_fields();
		
		foreach($this->tables as $tablename)
		{
			
			if(!in_array($tablename, $this->ignor_tables))
			{
				if($this->triger_status[$tablename][0])
					{
					$this->drop_trigger($tablename.$this->triger_names[0]);
					$this->droped_trigers[] = $tablename.$this->triger_names[0];
					}
					else
						{
						$this->added_triggers[] = $tablename.$this->triger_names[0];
						}
				
				if($this->triger_status[$tablename][1])
					{
					$this->drop_trigger($tablename.$this->triger_names[1]);	
					$this->droped_trigers[] = $tablename.$this->triger_names[1];
					}
					else
						{
						$this->added_triggers[] = $tablename.$this->triger_names[0];
						}
				
				if($this->triger_status[$tablename][2])
					{
					$this->drop_trigger($tablename.$this->triger_names[2]);	
					$this->droped_trigers[] = $tablename.$this->triger_names[2];
					}
					else
						{
						$this->added_triggers[] = $tablename.$this->triger_names[0];
						}

				if($this->auto_added)
				{
				 	$this->add_trigger($tablename, 1);
				    $this->add_trigger($tablename, 2);
				    $this->add_trigger($tablename, 3);     
				}

			}
		}
	}
	
	
	public function drop_trigger($trigger_name)
	{
		global $core;
		
		$sql = "DROP TRIGGER `".$trigger_name."`";
		$core->db->query($sql);
		
		return true;
	}
	
	
	
	
	private function get_bases_list()
	{
		global $core;
		
		$core->db->select()->from('mcms_sites')->fields('id', 'name',	'server_name', 'server_alias', 'db_name')->where('name != "sas" AND db_name != "sas-ru"');
		$core->db->execute();
		$core->db->get_rows();
		
		foreach($core->db->rows as $site_info)
		{
			$this->bases_list[$site_info['id']] = $site_info['db_name'];
		}
	}
	
	private function add_trigger($tablename, $trigger_type)
	{	
		switch ($trigger_type)
		{
			case 'insert':
			case '1':
				$this->add_insert_trigger($tablename);
			break;
			
			case 'update':
			case '2':
				$this->add_edit_triger($tablename);
			break;
			
			case 'delete':
			case '3':
				$this->add_delete_triger($tablename);
			break;
		}
	}
	
	private function add_insert_trigger($tablename)
	{
		global $core;
		
		if(!count($this->fields[$tablename])) return false;
		
		$curent_name = $tablename.$this->triger_names[0];
		
		
		if(in_array('id_site', $this->fields[$tablename]))
		{
			$sql = "CREATE TRIGGER `".$curent_name."` AFTER INSERT ON `".$tablename."` \n FOR EACH ROW \n BEGIN\n";
			
			foreach($this->bases_list as $site_id=>$base_name)
			{
				$sql .= "IF NEW.id_site = ".$site_id." THEN INSERT INTO `".$base_name."`.`".$tablename."` VALUES(";
				foreach($this->fields[$tablename] as $fieldname)
				{
				$sql .= "NEW.`".$fieldname."`,";	
				}
				
				$sql = substr($sql,0,-1);
				$sql .= ");\n";
				$sql .= "END IF; \n";
			}
		
			$sql .= "END; \n";
			$this->sql_colection[] = $sql;
			$core->db->query($sql);
			unset($sql);	
		}
		else
			{
				$sql = "CREATE TRIGGER `".$curent_name."` AFTER INSERT ON `".$tablename."` \n FOR EACH ROW \n BEGIN\n";
				foreach($this->bases_list as $site_id=>$base_name)
				{
					$sql .= "INSERT INTO `".$base_name."`.`".$tablename."` VALUES(";
					foreach($this->fields[$tablename] as $fieldname)
					{
					$sql .= "NEW.`".$fieldname."`,";
					}

					$sql = substr($sql,0,-1);
					$sql .= ");\n";
				}

				$sql .= "\n END; \n";
				
				$this->sql_colection[] = $sql;
				$core->db->query($sql);
				unset($sql);
			}
		
			
	}
	
	private function add_delete_triger($tablename)
	{
		global $core;
		
		if(!count($this->fields[$tablename])) return false;
		
		$curent_name = $tablename.$this->triger_names[2];
		
		if(in_array('id_site', $this->fields[$tablename]))
		{
			
			$sql = "CREATE TRIGGER `".$curent_name."` AFTER DELETE ON `".$tablename."` \n FOR EACH ROW \n BEGIN\n";
			foreach($this->bases_list as $site_id=>$base_name)
				{
				$sql .= "IF OLD.id_site = ".$site_id." THEN DELETE FROM `".$base_name."`.`".$tablename."`";
				$sql .= " WHERE `".$base_name."`.`".$tablename."`.`id` = OLD.`id`;\n";
				$sql .= "END IF; \n";
				}
	
			$sql .= "END; \n";
			$this->sql_colection[] = $sql;
			$core->db->query($sql);
			unset($sql);
		}
		else
			{
				$sql = "CREATE TRIGGER `".$curent_name."` AFTER DELETE ON `".$tablename."` \n FOR EACH ROW \n BEGIN\n";
				foreach($this->bases_list as $site_id=>$base_name)
				{
					$sql .= "DELETE FROM `".$base_name."`.`".$tablename."`";
					$sql .= " WHERE `".$base_name."`.`".$tablename."`.`id` = OLD.`id`;\n";
				}

				$sql .= "\n END; \n";
				$this->sql_colection[] = $sql;
				$core->db->query($sql);
				unset($sql);
			}
		
		
	}
	
	private function add_edit_triger($tablename)
	{
		global $core;
		
		if(!count($this->fields[$tablename])) return false;
		
		$curent_name = $tablename.$this->triger_names[1];
		
		if(in_array('id_site', $this->fields[$tablename]))
		{
			
			$sql = "CREATE TRIGGER `".$curent_name."` AFTER UPDATE ON `".$tablename."` \n FOR EACH ROW \n BEGIN\n";
			foreach($this->bases_list as $site_id=>$base_name)
				{
				$sql .= "IF NEW.id_site = ".$site_id." THEN UPDATE `".$base_name."`.`".$tablename."` SET ";
				foreach($this->fields[$tablename] as $fieldname)
					{
					$sql .= "`".$base_name."`.`".$tablename."`.`".$fieldname."` = NEW.`".$fieldname."`, ";
					}
	
				$sql = substr($sql,0,-2);
				$sql .= " WHERE `".$base_name."`.`".$tablename."`.`id` = OLD.`id`;\n";
				$sql .= "END IF; \n";
				}
	
			$sql .= "END; \n";
			
			$this->sql_colection[] = $sql;
			$core->db->query($sql);
			unset($sql);
			
		
		}
		else
			{
				$sql = "CREATE TRIGGER `".$curent_name."` AFTER UPDATE ON `".$tablename."` \n FOR EACH ROW \n BEGIN\n";
				foreach($this->bases_list as $site_id=>$base_name)
				{
					$sql .= "UPDATE `".$base_name."`.`".$tablename."` SET ";
					foreach($this->fields[$tablename] as $fieldname)
					{
					$sql .= "`".$base_name."`.`".$tablename."`.`".$fieldname."` = NEW.`".$fieldname."`, ";
					}

					$sql = substr($sql,0,-2);
					$sql .= " WHERE `".$base_name."`.`".$tablename."`.`id` = OLD.`id`;\n";
				}

				$sql .= "\n END; \n";
				$this->sql_colection[] = $sql;
				$core->db->query($sql);
				unset($sql);
			}
		
	}
		

	
	
	private function check_trigers($tablename)
	{
		global $core;
		
		$sql = "SELECT * FROM `information_schema`.`triggers` WHERE `TRIGGER_NAME` = '".$tablename.$this->triger_names[0]."'";
		
		$core->db->query($sql);
		$insert_triger_flag = $core->db->num_rows();
		
		$sql = "SELECT * FROM `information_schema`.`triggers` WHERE `TRIGGER_NAME` = '".$tablename.$this->triger_names[1]."'";
		
		$core->db->query($sql);
		$update_triger_flag = $core->db->num_rows();
		
		$sql = "SELECT * FROM `information_schema`.`triggers` WHERE `TRIGGER_NAME` = '".$tablename.$this->triger_names[2]."'";
		
		$core->db->query($sql);
		$delete_triger_flag = $core->db->num_rows();
		
		return array($insert_triger_flag, $update_triger_flag, $delete_triger_flag);
	}
	
	private function get_table_fields($tablename)
	{
		global $core;
		
		$sql = "SHOW FIELDS FROM `".$tablename."`";
		$core->db->query($sql);
		
		while ($row = mysql_fetch_row($core->db->result))
		{
			$this->fields[$tablename][] = $row[0];
		}
		
		return $this->fields[$tablename];
	}
	
	private function get_tables()
	{
		global $core;
		
		$core->db->query('SHOW TABLES');
		
		while($row = mysql_fetch_row($core->db->result))
		{
			$this->tables[] = $row[0];			
		}
		
		return $this->tables;
	}
	
	private function get_all_fields()
	{
		foreach($this->tables as $tablename) 
			{
			if(!in_array($tablename, $this->ignor_tables))
				{
				$this->get_table_fields($tablename);
				}
			}
	}

	private function get_all_triggers()
	{
		
		global $core;
		
		$sql = "SELECT tr.EVENT_OBJECT_TABLE, tr.EVENT_MANIPULATION  FROM `information_schema`.`TRIGGERS` as tr ORDER BY tr.EVENT_OBJECT_TABLE, EVENT_MANIPULATION";
		$core->db->query($sql);
		$core->db->get_rows();
		
		if(!count($core->db->rows)) return false;
		
		foreach($core->db->rows as $row)
		{
			$this->define_triggers[$row['EVENT_OBJECT_TABLE']]['actions'][] = $row['EVENT_MANIPULATION'];
			
			switch ($row['EVENT_MANIPULATION'])
			{
				case 'INSERT':
					$this->define_triggers[$row['EVENT_OBJECT_TABLE']][0] = 1;
				break;
				
				case 'UPDATE':
					$this->define_triggers[$row['EVENT_OBJECT_TABLE']][1] = 1;
				break;
				
				case 'DELETE':
					$this->define_triggers[$row['EVENT_OBJECT_TABLE']][2] = 1;
				break;
			}
			
		}
	
	}
	
}
?>