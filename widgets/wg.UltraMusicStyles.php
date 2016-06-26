<?php
class UltraMusicStyles extends widgets implements iwidget
{
	
	public function main()
	{
		
	}
	
	public function out()
	{
		
		global $core;
		
		$core->db->select()->from('genres')->fields()->where('parent_id = '.intval($parentId))->order('name');
		$core->db->execute('db_music');
		$core->db->get_rows();

		return $core->db->rows;
	}
}