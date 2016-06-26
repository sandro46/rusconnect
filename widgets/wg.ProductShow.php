<?php
class ProductShow extends widgets implements iwidget
{
	
	public $name = 0;
	public $limit = 0;
	public $order = 0;
	public $page = 0;
	public $letters = 0;

	
	public function main()
	{
	}
	public function __destruct(){
	}
	public function out()
	{
		global $controller, $core;
		//print_r(debug_backtrace());die();
// 		print_r(module::$core);
		//$core = $core->shop;
		$shop_id = $core->shop->site['site_id'];
		$client_id = $core->shop->site['client_id'];

		//print_r($core->shop->site);
		
		$start = $this->limit * $this->page;
		$end = $start + $this->limit;
		
		if($this->name == "reviews"){
			$sql = "SELECT  * FROM tp_site_blocks WHERE type_id = 2 AND client_id = {$client_id} AND shop_id = {$shop_id}";
			if($this->order && $this->order == 'rnd')
				$sql .= " ORDER BY RAND()";
			
			if($this->limit)
				$sql .= " LIMIT ".$this->limit;
			//echo $sql;
			$this->core->db->query($sql);
			$this->core->db->get_rows();

			return $this->core->db->rows;
		}
		
		if($this->name == "slider" || $this->name == "recommend" || $this->name == "new" || $this->name == "product_day"){
			$sql = "SELECT 
							get_rewrite(p.rewrite_id) as rewrite,
							p.client_id, 	
							p.recommend, 	
							p.slider,	
							p.new, 
							p.product_day,	
							p.product_day_date,
							p.product_id, 	
							p.shop_id, 	
							p.article, 	
							p.title, 
							p.description, 
							p.type_id,
							p.status_id,
							p.group_id,
							p.public_site, 
							p.full_description,
							p.seo_url, 	
							p.seo_title, 	
							p.seo_keywords, 	
							p.seo_description, 	
							p.price,
							p.sale,
							IF(p.sale > 0, ROUND((p.price - p.sale),2), 0) economy,
							p.add_date, 	
							p.update_date,
							p.product_id id,
							IFNULL((SELECT 
									img.url
							 FROM 
							 		tp_product_img img 
							 WHERE 
							 		img.product_id = p.product_id AND
							 		img.client_id = p.client_id AND
							 		img.shop_id = p.shop_id AND
							 		img.url <> '' LIMIT 1), '/templates/admin/green/images/no_image.png') img
				         	
					FROM 
							tp_product p
					WHERE
							p.shop_id = {$shop_id} AND
							p.client_id = {$client_id} ";
			
			if($this->name == "slider")
				$sql .= " AND p.slider = 1 ";
			
			if($this->name == "recommend")
				$sql .= " AND p.recommend = 1 ";
			
			if($this->name == "new")		
				$sql .= " AND p.new = 1 ";
			
			if($this->name == "product_day")
				$sql .= " AND p.product_day = 1 AND p.product_day_date > ".time()." ";
			
			if($this->order && $this->order == 'rnd')
				$sql .= " ORDER BY RAND() ";
			
			if($this->limit)
				$sql .=" LIMIT ".$this->limit;
			//echo $sql;
	
	
			$this->core->db->query($sql);
			//$core->db->colback_func_param = 0;
			$this->core->db->add_fields_deform(array('product_day_date'));
			$this->core->db->add_fields_func(array('parce_for_date'));
			$this->core->db->get_rows();
			foreach($this->core->db->rows as $item){
				
			}
			
			return $this->core->db->rows;
		}
	}
}
function parce_for_date($timestamp){
	$m = date('m', $timestamp)-1;
	return date('Y, '.$m.', d, 23, 59, 59', $timestamp);
	
}
