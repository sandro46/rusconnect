<?php
class ShopMenu extends widget {
	public $menu_id = 0;
	private $cache = array();
	
	public function ShopMenuAction() {
		$this->menu_id = intval($this->menu_id);
		$sql = "SELECT 
		          m.*,
		          IF(m.type_id = 2, (SELECT get_rewrite(pg.rewrite_id) FROM site_pages as pg WHERE pg.site_id = {$this->core->site_id} AND pg.page_id = m.value),  
		              IF(m.type_id = 3, (SELECT get_rewrite(pr.rewrite_id) FROM tp_product as pr WHERE pr.shop_id = {$this->core->site_id} AND pr.product_id = m.value), 
		                  '')
		          ) as url
		          
		        FROM 
		          site_menu as m 
		        WHERE 
		          m.site_id = {$this->core->site_id} AND 
		          m.parent_id = {$this->menu_id} AND 
		          m.hidden <> 1 
		      ORDER BY 
		          m.order";

		$this->core->db->query($sql);
		$this->core->db->filter('url', function($rewrite, $row) {
            if($row['type_id'] == 4) {
                return ($row['custom_link'] == '/')? '/' : 'http://'.$row['custom_link'];
            } else if($row['type_id'] == 2) {
                return (!empty($rewrite))? $rewrite : '/'.$this->core->lang.'/pages/show/id/'.$row['value'].'/';
            } else if($row['type_id'] == 3) {
                return (!empty($rewrite))? $rewrite : '/'.$this->core->lang.'/shop/product/id/'.$row['value'].'/';
            } 
		});
		
		
		$this->core->db->get_rows();
		$list = $this->core->db->rows;
		/*
		if(empty($list)) return array();
		
		foreach($list as $k=>&$item) {
		    
			if($item['type_id'] == 4) {
				
				$item['url'] = ($item['custom_link'] == '/')? '/' : 'http://'.$item['custom_link'];
			} else if($item['type_id'] == 2) {
				$sql = "SELECT title, rewrite_id, get_rewrite(rewrite_id) as rewrite_url, page_id FROM site_pages WHERE site_id = {$this->core->site_id} AND page_id = {$item['value']} ";
				$this->core->db->query($sql);
				$this->core->db->get_rows(1);
				$info = $this->core->db->rows;
				if(empty($info)) {
					unset($list[$k]);
				} else {
					$item['url'] = (intval($info['rewrite_id'])>0)? $info['rewrite_url'] : '/ru/pages/show/id/'.$item['value'].'/';
				}
			} else if($item['type_id'] == 3) {
				$sql = "SELECT rewrite_id, get_rewrite(rewrite_id) as rewrite_url FROM tp_product WHERE shop_id = {$this->core->site_id} AND product_id = {$item['value']} ";
				$this->core->db->query($sql);
				$this->core->db->get_rows(1);
				$info = $this->core->db->rows;
				if(empty($info)) {
					unset($list[$k]);
				} else {
					$item['url'] = (intval($info['rewrite_id'])>0)? $info['rewrite_url'] : '/ru/shop/product/id/'.$item['value'].'/';
				}
			}
		}
		*/
		return $list;
	}
}
