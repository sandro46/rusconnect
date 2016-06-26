<?php
class sites_control
{
	
	public $list = array();
	
	
	
	function __construct()
	{
		
	}
	
	
	public function get_list()
	{
		global $core;
		
		$core->db->select()->from('mcms_sites')
						   ->fields('*');
		$core->db->execute();
		$core->db->get_rows();
		
		foreach($core->db->rows as $row)
		{
			$out[$row['id']] = $row;
		}
		
		$core->db->select()->from('mcms_sites_alias')
						   ->fields('*');
		$core->db->execute();
		$core->db->get_rows();
		
		foreach($core->db->rows as $row)
		{
			$out[$row['id_site']]['alias'][] = $row;
		}
	
		return $out;		
	}
	
	public function get_sites_modules_list()
	{
		global $core;
		
		$sql = "SELECT sm.id, sm.id_site, sm.id_module, m.name as module_name, m.describe as module_describe, s.name as site_name, s.describe as site_describe FROM mcms_sites as s, mcms_sites_modules as sm, mcms_modules as m WHERE m.id_module = sm.id_module AND m.lang_id = ".$core->CONFIG['lang']['id'].' AND s.id = sm.id_site ORDER BY s.id';
		
		$core->db->query($sql);
		$core->db->get_rows();
		
		foreach($core->db->rows as $row)
		{
			$curent_id = $row['id_site'];
			
			$sites[$curent_id]['describe'] = $row['site_describe'];
			$sites[$curent_id]['name'] = $row['site_name'];
			$sites[$curent_id]['id'] = $row['id_site'];
					
			unset($row['site_describe'], $row['site_name'], $row['id_site']);
			
			$sites[$curent_id]['modules'][] = $row;
		}
		
		$this->list = $sites;
		
		return $this->list;		
	}
	
	public function delete_module($id_module_site)
	{
		global $core;

		$core->db->delete('mcms_sites_modules', $id_module_site, 'id');
		
		return true;
	}
	
	public function add_module($id_site, $id_module)
	{
		global $core;
		
		$data[] = array('id_site'=>intval($id_site), 'id_module'=>intval($id_module));

		$core->db->autoupdate()->table('mcms_sites_modules')->data($data);
		$core->db->execute();
		
		return true;
	}

	public function get_modules_list($id_site)
	{
		global $core;

		
		$sql = "SELECT m.id_module, m.name, m.describe, m.location, m.reg_date, m.menu_visible FROM mcms_modules as m WHERE m.id_module NOT IN (SELECT sm.id_module FROM mcms_sites_modules as sm WHERE sm.id_site = ".intval($id_site).") AND m.lang_id = ".$core->CONFIG['lang']['id']." ORDER BY m.describe";
		
		$core->db->query($sql);
		
		//$core->db->select()->from('mcms_modules')->fields('id_module', 'name', 'describe', 'location', 'reg_date', 'menu_visible')->where('lang_id = '.$core->CONFIG['lang']['id']);
		//$core->db->execute();

		$core->db->colback_func_param = 0;

		$core->db->add_fields_deform(array('reg_date', 'describe'));
		$core->db->add_fields_func(array('date,"d.m.Y H:i"', 'stripslashes'));

		$core->db->get_rows();

		return $core->db->rows;
	}
	
}


################ ajax function ##################


function add_module_in_site($id_site, $id_module)
	{
	global $core, $site;
	
	$site->add_module($id_site, $id_module);
	
	return true;
	}

function get_modules_list($id_site)
{
	global $core, $site;

	$core->tpl->assign('list_modules', $site->get_modules_list($id_site));
	$core->tpl->assign('item_parent_id', $id_site);

	$html = $core->tpl->fetch('modules_list.html', 1, 0, 0, 'sites_control');

	return $html;
}



?>