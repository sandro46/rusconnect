<?php
class ShopMainInfo extends widgets implements iwidget
{
	
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
		$sql = "SELECT * FROM sv_client_sites WHERE client_id = {$client_id} AND site_id = {$shop_id} LIMIT 1";
		$this->core->db->query($sql);
		$this->core->db->get_rows(1);
		$data = $this->core->db->rows;
	
		return $data;
	}
}
