<?php
class ShopShowStaticPage extends widgets implements iwidget
{
	public $pageId = false;
	public $pageName = "";
	
	public function main()
	{
		$this->pageId = false;
		$this->pageName = "";
		
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
		$names = array('about_purchases', 'delivery_regions', 'paymen_types', 'oferta', 'payments_info', 'about', 'dostavka', 'contacts', 'warranty');
		$this->pageId = intval($this->pageId);
		//echo $this->pageName;
		if(!$this->pageId && !in_array($this->pageName, $names))
			return false;

		$sql = "SELECT 
						c.*, 
						r.rewrite 
				FROM 
						catalog_entries c 
						LEFT JOIN 
									mcms_rewrite r 
						ON 
									r.id = c.rewrite_id 
				WHERE 
						c.site_id = {$shop_id} "; 
		if($this->pageId)
			$sql .= " AND c.id = ".$this->pageId;
		
		if(in_array($this->pageName, $names))
			$sql .= " AND c.static_name = '".$this->pageName."'";
			
		$sql .= " LIMIT 1";
		$this->core->db->query($sql);
		//echo $sql;
		$this->core->db->get_rows(1);
		$static = $this->core->db->rows;

		return $static;
	}
}
