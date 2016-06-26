<?php
class ProductList extends widget {


	public $param_id = 0;
	public $type_name = false;
	public $limit = 4;
	
	private $shop = null;

	public function load() {
		$this->shop = $this->core->shop;
	}
	
	public function ProductListAction() {
		if(!isset($this->type_name)) return false;
		if($this->type_name == 'list' && intval($this->param_id)) {
			 $result = $this->shop->getProductsFromList(intval($this->param_id), false, 0, $this->limit);
			 return $result;
		}
		
		if($this->type_name == 'recent_buy') {
		    $result = $this->shop->getProductsRecentBuy(0, $this->limit);
		    return $result;
		}
				
		if($this->type_name == 'products') {
		    $result =  $this->shop->getProducts(intval($this->param_id),0,$this->limit,'name','asc');
		    return $result[0];
		}
	}
}





