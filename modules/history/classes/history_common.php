<?php

class history_common
{
	
	
	public function get_modules_list()
	{
		global $core;
		
		$sql = "SELECT `name` , `describe` FROM `mcms_modules` WHERE `lang_id` = {$core->CONFIG['lang']['id']} AND `name` in (SELECT h.gr_name FROM `mcms_history` as h GROUP BY h.gr_name)";
		$core->db->query($sql);
		$core->db->get_rows();
		
		
		return $core->db->rows;
	}
	
	
	
	
	
}


?>