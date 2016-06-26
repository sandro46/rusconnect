<?php
class LeftAdminMenu extends widgets implements iwidget 
{
	private $list = Array();
	
	
	public function main()
	{
		global $core;
		
		$cache_var = $core->tpl->get_cache_var_name('modules::modules_list_use_permission', array('_site'=>true, '_editor'=>true, '_user_id'=>true, '_lang'=>true));

		if(!$return = $core->memcache->get($cache_var))
		{
		
		    $sql= "SELECT
						DISTINCT(gra.id_module) as id,
						m.name as name,
						mt.type_id as type,
						m.describe as 'describe',
						m.menu_order as menu_order,
						'1' as is_module,
						mtf.name as section_name
					FROM
						mcms_group_action as gra
						LEFT JOIN mcms_modules as m
						ON m.id_module = gra.id_module,
						mcms_modules_type as mt,
						mcms_modules_types_fields as mtf
					WHERE
						gra.id_group IN (
							SELECT ugr.id_group
							FROM mcms_user_group AS ugr
							WHERE ugr.id_user=".$core->user->id." AND  
								  ugr.id_group IN (SELECT grs.id_group FROM mcms_group_sites as grs WHERE grs.id_site = ".$core->edit_site.")
										)
						AND (
							m.menu_visible =1
							AND m.lang_id = ".$core->CONFIG['lang']['id']."
							AND m.menu_parent_id = 0
							)
						AND m.id_module IN (SELECT sm.id_module FROM mcms_sites_modules as sm WHERE sm.id_site = ".$core->edit_site.")
						AND mt.module_id = m.id_module
						AND mtf.id_type = mt.type_id
						AND mtf.lang_id = ".$core->CONFIG['lang']['id']."
				 ORDER BY type, menu_order";
				

    		$core->db->query($sql);
    		$core->db->get_rows();
    
    		$this->list = $core->db->rows;
    		
    		$core->memcache->add($cache_var, $this->list, false, 10*60);
		}
		else
		    {
		        $this->list = $return;
		        $core->log->add_module_log('WIDGETS', 'Левое меню выводится из кэша.');
		    }
		
		// FIXME
		
		$_tmp_out = array();
		
		foreach ($this->list as $ls)
		{
			$_tmp_out[$ls['type']][] = $ls;
		}
				
		$this->run($_tmp_out);
	}
	
	
	public function out()
	{
		
	}
	
	
}
?>