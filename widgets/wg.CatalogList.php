<?php
class CatalogList extends widgets implements iwidget 
{
	
	public $parent_id = 0;
	
	

	
	public function main()
	{

	}
	
	public function out()
	{
		global $core;
	
		$this->site_id = $core->site_id; 	
		$this->get_site_map();
		
		$this->html = $this->generate($this->data);
		return $this->html;
	}
	

	
	private function getPagesList()
	{
		
		global $core;
		
		$sql = "SELECT d.id_doc, d.title, d.rewrite_id, d.type, d.type_value, r.rewrite FROM mcms_docs as d, mcms_rewrite as r WHERE d.id_site = {$core->site_id} AND d.type = 6 AND d.parent_id = {$this->parent} AND d.visible = 1 AND d.lang_id = {$core->CONFIG['lang']['id']} AND  r.id = d.rewrite_id ORDER BY d.order";  
		
		$core->db->query($sql);
		$core->db->get_rows();
		
		$this->list = $core->db->rows;
		
		foreach($this->list as $key => $item)
		{
			if($item['type'] == 6)
			{
				$data_type_id = $core->multidb->getTypeByItemId($item['type_value']);
				$data_type_name = $core->multidb->getTypeNameById($data_type_id);
				
				$core->multidb->select()->type($data_type_name)->fields('icon')->where('id = '.$item['type_value']);
				$core->multidb->get_rows();
				$this->list[$key] = array_merge($this->list[$key], $core->multidb->rows);
			}
		}
		
	}
		

	
	
	
	
	
	
	
	
	
}

?>