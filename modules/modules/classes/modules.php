<?php

class admin_modules
{

	public function get_list($page, $limit, $order, $order_type)
	{
		global $core;
		
		$order_type = ($order_type == 'desc')? ' DESC':' ASC';
		
		$start = $page * $limit;
		$order = ($order)? addslashes($order): 'menu_order';
		
		$core->db->select()->from('mcms_modules_type mt', 'mcms_modules_types_fields mtf')
						   ->fields('mtf.name', 'mt.module_id')
						   ->where('mt.type_id = id_type AND mtf.lang_id='.$core->CONFIG['lang']['id']);
		
		$core->db->execute();
		$core->db->get_rows();
		
		foreach($core->db->rows as $item) $groups[$item['module_id']]=$item['name'];
		
		$core->db->select()->from('mcms_modules')
						   ->fields()
						   ->lang('$curent')
						   ->limit($limit, $start)
						   ->order($order,$order_type);
						   
		$core->db->execute();
		$core->db->colback_func_param = 0;
		$core->db->add_fields_deform(array('reg_date', 'describe'));
		$core->db->add_fields_func(array('date,"d.m.Y H:i"', 'stripslashes'));	
		$core->db->get_rows();
		
		foreach($core->db->rows as &$row) $row['describe2'] = $groups[$row['id_module']];
		
		return $core->db->rows;
	}
		
	public function get_total()
	{
		global $core;
		
		$core->db->select()->from('mcms_modules')
						   ->fields('$count')
						   ->lang('$curent');
		$core->db->execute();
									  		
		return $core->db->get_field();
	}
	
	public function get_langs_ussign_module_info($id_module)
	{
		global $core;
		
		$sql = "SELECT l.id, l.name, l.rewrite, (SELECT m.describe FROM mcms_modules as m WHERE m.lang_id = l.id AND m.id_module = ".$id_module.") as `describe`, (SELECT m.meta_title FROM mcms_modules as m WHERE m.lang_id = l.id AND m.id_module = ".$id_module.") as meta_title, (SELECT m.meta_description FROM mcms_modules as m WHERE m.lang_id = l.id AND m.id_module = ".$id_module.") as meta_description,  (SELECT m.meta_keywords FROM mcms_modules as m WHERE m.lang_id = l.id AND m.id_module = ".$id_module.") as meta_keywords FROM mcms_language as l";
		$core->db->query($sql);
		$core->db->get_rows();
		
		return $core->db->rows;
	}
	
	public function get_langs()
	{
		global $core;
		
		$core->db->select()->from('mcms_language')->fields('id', 'name', 'rewrite');
		$core->db->execute();
		$core->db->get_rows();
		
		return $core->db->rows;
	}

	public function delete_all($id_module)
	{
		global $core;
		             
		$sql = "DELETE FROM mcms_gr_act_lang WHERE mcms_gr_act_lang.id_gr_act IN(SELECT gra.id FROM mcms_group_action as gra WHERE gra.id_module =".$id_module.")";
		$core->db->query($sql);  
		 
		$core->db->delete('mcms_group_action', $id_module, 'id_module');
		$core->db->delete('mcms_modules', $id_module, 'id_module');
		$core->db->delete('mcms_sites_modules', $id_module, 'id_module');
	}
	
	public function get_info($module_id)
	{
		global $core;
				
		$core->db->select()->from('mcms_modules')->fields('name','location','menu_visible', 'id_module', 'type')->where('id_module = '.$module_id.' AND lang_id = '.$core->CONFIG['lang']['id']);
		$core->db->execute();
		$core->db->get_rows();
		
		$list = $core->db->rows[0];
		
		$core->db->select()->from('mcms_modules_type')->fields('type_id')->where('module_id = '.$module_id)->limit(1);
		$core->db->execute();
		
		$list['moduletypeid'] = $core->db->get_field();
		
		return $list;
	}
	
	#!FIXME: create new installer. this method not used in core version >= 5.xxx
	public function install()
	{
		global $core,$controller;
		$query = "select max(m.id_module) as id from mcms_modules as m";
		$core->db->query($query);
		$core->db->get_rows();

		$file = $core->CONFIG['module_dir'].$_GET['mod'].'/install.sql';
		if(file_exists($file))
		{
			$fp = fopen($file, "rb");
			$contents = fread($fp, filesize($file));
			fclose($fp);

			$contents = explode("###",$contents);

			foreach($contents as $content)
			{
				
				$content = str_replace('<%id>',($core->db->rows[0]['id']+1),$content);
				$core->db->query($content);
			}

			
			

			$controller->redirect('/'.$core->CONFIG['lang']['name'].'/control_modules/list/', 'All changes have been saved, but it was not user defined membership in the group. Please edit this user');
		}
		else
		{
			$controller->redirect('/'.$core->CONFIG['lang']['name'].'/modules/add/', 'All changes have been saved, but it was not user defined membership in the group. Please edit this user');
		}	
	}
	
	public function getAllModulesList()
	{
		global $core;
		$path = $core->CONFIG['module_dir']; 
		$list = array();

		$location_in = '';
		foreach(scandir($path) as $dir)
		{
			if(substr($dir, 0, 1) != '.' && substr($dir, -4, 1) != '.' && substr($dir, -5, 1) != '.')
			{
				$location_in .= " '/".$dir."/',";
				$dirs[] = $dir;
			}
		}

		$query = "SELECT m.id_module as id,m.location as name,m.describe as 'describe'  FROM mcms_modules as m WHERE m.location in (".substr($location_in,0,-1).") and lang_id='".$core->CONFIG['lang']['id']."'";
		$core->db->query($query);

		$data = array();
		while($row = mysql_fetch_assoc($core->db->result))
		{
			foreach($row as $key=>$value)
			{
				if($key == 'name')
				{
					$nkey = substr(substr($value,0,-1),1);
					$data[$nkey] = ($row['name'] == $value)? $row:array();
				}	
			}
		}
		return array('base' => $data, 'dirs' => $dirs);
	}
	
	public function uninstall()
	{
		global $core;
		if(isset($_GET['mod']) and is_numeric($_GET['mod']))
		{
			$core->db->delete('mcms_modules', $_GET['mod'], 'id_module');
			$core->db->delete('mcms_group_action', $_GET['mod'], 'id_module');
		}
		
	}

	public function get_max_order($idModule)
	{
		global $core;
		
		$sql = "SELECT MAX(m.menu_order) FROM mcms_modules as m WHERE m.id_module IN(SELECT mt.module_id FROM mcms_modules_type as mt WHERE mt.type_id = (SELECT mt2.type_id FROM mcms_modules_type as mt2 WHERE mt2.module_id = {$idModule}))";    
		
		$core->db->query($sql);
		
		$order = $core->db->get_field()+1;
		
		return $order;
		
	}

	public function get_modules_types()
	{
		global $core;
		$core->db->select()->from('mcms_modules_types_fields')
					   	   ->fields('*')
					   	   ->lang('$curent')
					   	   ->order('name');
		$core->db->execute();
		$core->db->get_rows();
						   
		return $core->db->rows;
	}
	
	public function get_modules_sites()
	{
		global $core;
		
		$core->db->select()->from('mcms_sites')
						   ->fields('id', 'describe')
						   ->order('`describe`');
		$core->db->execute();
		$core->db->get_rows();
		
		return $core->db->rows;
	}
		
	public function edit($id_module)
	{
		global $core;
		
		//$core->db->delete('mcms_modules', $id_module, 'id_module');
		
		$name 			= addslashes($_POST['name']);
		$location 		=  addslashes($_POST['location']);
		$menu_visible	= ($_POST['menu_visible'])? 1:0;
		$order 			= $this->get_max_order($id_module);
		
		foreach($_POST['describe'] as $lang_id=>$describe)
		{
			$data[] = array('id_module'=>$id_module, 'name'=>$name, 'describe'=>addslashes($describe), 'location'=>$location, 'reg_date'=>time(), 'lang_id'=>intval($lang_id), 'menu_visible'=>$menu_visible, 'menu_parent_id'=>0, 'meta_title'=>$_POST['meta_title'][$lang_id], 'meta_description'=>$_POST['meta_description'][$lang_id], 'meta_keywords'=>$_POST['meta_keywords'][$lang_id], 'type'=>intval($_POST['belongs']), 'menu_order' => $order);
		}
	
		$core->db->autoupdate()->table('mcms_modules')->data($data)->primary('id_module', 'lang_id');
		$core->db->execute();
		
		$core->db->autoupdate()->table('mcms_modules_type')->data(array(array('module_id'=>$id_module, 'type_id'=>intval($_POST['setInMenu']))))->primary('module_id');
		$core->db->execute();
		
		return true;
	}
	
	public function add()
	{
		global $core;

		$creater = new src_creater();
		$creater->process();
		
		$id_module = $core->MaxId('id_module', 'mcms_modules')+1;
		
		$this->insert_module($id_module);
		$this->add_permission($creater, $id_module);	  
	}

	public function getNoInstallModules()
	{
		global $core;
		
		//$core->CONFIG['modules_template_dir'];		
		
	}
	
	private function insert_module($id_module)
	{
		global $core;
		
	  	$name 			= addslashes($_POST['name']);
	  	$order 			= $this->get_max_order($id_module);
		$location 		= '/'.addslashes(str_replace("/", "", $_POST['location'])).'/';
		$menu_visible	= ($_POST['menu_visible'])? 1:0;
		
		$this->delete_all($id_module);
		
		foreach($_POST['describe'] as $lang_id=>$describe)
			{
			$data[] = array('id_module'=>$id_module, 'name'=>$name, 'describe'=>addslashes($describe), 'location'=>$location, 'reg_date'=>time(), 'lang_id'=>intval($lang_id), 'menu_visible'=>$menu_visible, 'menu_parent_id'=>0, 'meta_title'=>$_POST['meta_title'][$lang_id], 'meta_description'=>$_POST['meta_description'][$lang_id], 'meta_keywords'=>$_POST['meta_keywords'][$lang_id], 'menu_order' => $order);
			}
	
		$core->db->autoupdate()->table('mcms_modules')->data($data);
		$core->db->execute();
		
		$data = array();
		
		$data[] = array('module_id'=>$id_module, 'type_id'=>$_POST['setInMenu']);
		
		$core->db->autoupdate()->table('mcms_modules_type')->data($data);
		$core->db->execute();
	}
				
	private function add_permission($creater, $id_module)
	{
		global $core;
		
		if(count($creater->data) > 0)
			{
				foreach($creater->data as $item)
				{
				$data[] = array('id_group'=>1, 'id_module'=>$id_module, 'id_action'=>$item['action']);		
				}
				
				$core->db->autoupdate()->table('mcms_group_action')->data($data);
				$ids = $core->db->execute();
				
				$data = array();
				$lang = $core->get_all_langs();
				
				foreach($ids as $item)
				{
					foreach($lang as $lng) $data[] = array('id_gr_act'=>$item, 'lang_id'=>$lng['id']);	
				}
				
				$core->db->autoupdate()->table('mcms_gr_act_lang')->data($data);
				$core->db->execute();
			}
			else
				{
					$data[] = array('id_group'=>1, 'id_module'=>$id_module, 'id_action'=>1);	
					$core->db->autoupdate()->table('mcms_group_action')->data($data);
					$ids = $core->db->execute();
					
					$data = array();
					$lang = $core->get_all_langs();
					
					foreach($lang as $lng) $data[] = array('id_gr_act'=>$ids[0], 'lang_id'=>$lng['id']);
				}
		
		$data = array();

		foreach($_POST['mod_sites'] as $mods)
		{
			$data[] = array('id_site'=>$mods, 'id_module'=>$id_module);
		}
		
		$core->db->autoupdate()->table('mcms_sites_modules')->data($data);
		$core->db->execute();		
	}
	
	private function generate_install_file($id_module)
	{
		global $core;
		
		$sql = "SELECT HEX(`name`) as `name`, HEX(`describe`) as `describe`, `location`, `reg_date`, `lang_id`, `menu_order`, `menu_visible`, `menu_parent_id`, `type`, HEX(`meta_title`) as `meta_title`, HEX(`meta_description`) as `meta_description`, HEX(`meta_keywords`) as `meta_keywords` FROM `mcms_modules` WHERE `id_module` = $id_module";
		$core->db->query($sql);
		$core->db->get_rows();
		
		$str = 'INSERT INTO `mcms_modules` (`id_module`, `name`, `describe`, `location`, `reg_date`, `lang_id`, `menu_order`, `menu_visible`, `menu_parent_id`, `type, meta_title`, `meta_description`, `meta_keywords`) VALUES ';
		$str .= "\n";
		
		foreach($core->db->rows as $row)
			{	
				$str .= '(<%id>, 0x'.$row['name'].', 0x'.$row['describe'].', \''.$row['location'].'\', '.$row['reg_date'].', '.$row['lang_id'].', '.$row['menu_order'].', '.$row['menu_visible'].', '.$row['menu_parent_id'].', '.$row['type'].', 0x'.$row['meta_title'].', 0x'.$row['meta_description'].', 0x'.$row['meta_keywords'].'),';
				$str .= "\n";
			}
			
		$str = substr($str, 0, -1);
		
		return $str;
	}

}


?>