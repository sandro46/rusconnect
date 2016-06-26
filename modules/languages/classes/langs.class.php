<?php
class admin_system_langs
{
	
	public function get_list()
	{
		global $core;
		
		$core->db->select()->from('mcms_language')->fields('id','name', 'descr', 'charset', 'encoding', 'visible_in_menu', 'rewrite')->order('id');
		$core->db->execute();
		
		$core->db->get_rows();
		
		return $core->db->rows;
	}
	
	public function get_info($id)
	{
		global $core;
		
		$core->db->select()->from('mcms_language')->fields('id', 'name', 'descr', 'charset', 'encoding', 'visible_in_menu', 'rewrite')->where('id = '.$id);
		$core->db->execute();
		$core->db->get_rows();
		
		return $core->db->rows[0];
	}
	
}
?>