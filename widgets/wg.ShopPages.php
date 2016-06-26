<?php
class ShopPages extends widget {


	public $parent_id = 0;
	public $current_id = 0;
	public $limit = 4;
	
	private $shop = null;

	public function load() {
		$this->shop = $this->core->shop;
	}
	
	public function ShopPagesAction() {
	    $this->parent_id = intval($this->parent_id);
	    $this->current_id = intval($this->current_id);
	    
	    $sql = "SELECT
	               p.*,
	               p.page_id as id, 
	               get_rewrite(p.rewrite_id) as rewrite,
	               '' as url,
	               p.create_date as date
	            FROM
	               site_pages as p
	            WHERE
                   p.site_id = {$this->core->site_id} ";

	    if(!$this->current_id) {
	        $sql .= "AND p.parent_id = {$this->parent_id}";
	    } else {
	        $sql .= "AND p.parent_id = (SELECT p2.parent_id FROM site_pages as p2 WHERE p2.site_id = {$this->core->site_id} AND p2.page_id = {$this->current_id})";
	    }

	    $this->db->query($sql);
	    $this->db->filter('url', $this->filters['page_url']);
	    $this->db->filter('date', $this->filters['date']);
	    $this->db->get_rows();
	    
	    return $this->db->rows;
	}
}





