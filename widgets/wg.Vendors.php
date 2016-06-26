<?php
class Vendors extends widget {
	public $limit = 20;
	public $onlyImage = false;
	public $random = false;
	
	public function VendorsAction() {
		$this->limit = intval($this->limit);
		$this->onlyImage = ($this->onlyImage)? true : false;
		$this->random = ($this->random)? true : false;
		
		$sql = "SELECT * FROM tp_vendors WHERE  shop_id = {$this->core->site_id} ";
		
		if($this->onlyImage) {
			$sql .= " AND image_preview IS NOT NULL AND image_preview != '' ";
		}
		
		if($this->random) {
		    $sql .= " ORDER BY RAND() ";
		}
		
		
		$sql .= " LIMIT {$this->limit}";
		
		$this->core->db->query($sql);
		$this->core->db->get_rows();
		$list = $this->core->db->rows;
		
		if(empty($list)) return array();
		
		return $list;
	}
}
