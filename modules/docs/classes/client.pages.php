<?php 

class client_pages extends global_client_api {
	
	
	
	
	public function getInfo($id) {
		$id = intval($id);
		if(!$id) return false;
		
		$sql = "SELECT
					p.*
				FROM 
					site_pages as p
				WHERE 
					p.site_id = {$this->core->site_id} AND
					p.page_id = {$id} ";
		$this->db->query($sql);
		$this->db->get_rows(1);	
		
		return $this->db->rows;
	}
	
	
	
	
	
	
	
	
}



?>