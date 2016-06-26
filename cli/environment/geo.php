<?php
class geo {
	
	private $city_table = 'geo_cities';
	private $country_table = 'geo_countries';
	private $region_table = 'geo_regions';
	private $limit = 6;
	
	public function __construct($city_table = false, $region_table = false, $country_table = false) {
		if($city_table) $this->city_table = $city_table;
		if($region_table) $this->region_table = $region_table;
		if($country_table) $this->country_table = $country_table;
	}
	
	public function searchCountry($query, $limit = 0) {
		$limit = (!$limit = intval($limit))? $this->limit : $limit;   
		$query = $this->clearSearcheString($query);
		
		global $core;
		
		$sql = "SELECT country_id as id, title as value FROM geo_countries WHERE LOWER(title) LIKE LOWER('{$query}%') LIMIT {$limit}";
		$core->db->query($sql);
		$core->db->get_rows();
		return $core->db->rows;
	}
	
	public function searchRegion($query, $country = 0, $limit = 0) {
		$limit = (!$limit = intval($limit))? $this->limit : $limit;   
		$query = $this->clearSearcheString($query);
		$country = (!$country = intval($country))? $country : false;
		
		global $core;
		
		$sql = "SELECT r.region_id as id, r.country_id,  r.title as value, c.title as country FROM geo_regions as r LEFT JOIN geo_countries as c ON c.country_id = r.country_id WHERE LOWER(r.title) LIKE LOWER('{$query}%') ";  
		if($country) $sql .= " AND r.country_id = {$country} ";
		$sql .= " LIMIT {$limit}";

		$core->db->query($sql);
		$core->db->get_rows();
		return $core->db->rows;
	}
	
	public function searchCity($query, $region = 0, $country = 0, $limit = 0) {
		$limit = (!$limit = intval($limit))? $this->limit : $limit;   
		$query = $this->clearSearcheString($query);
		$country = (!$country = intval($country))? $country : false;
		$region = (!$region = intval($region))? $region : false;
		
		global $core;
		
		$sql = "SELECT 
					c.city_id as id, 
					c.region_id, 
					c.country_id, 
					c.title as value, 
					co.title as country, 
					r.title as region 
				FROM geo_cities as c 
					LEFT JOIN geo_countries as co ON co.country_id = c.country_id 
					LEFT JOIN geo_regions as r ON r.region_id = c.region_id 
				WHERE LOWER(c.title) LIKE LOWER('{$query}%') ";
		  
		if($country) $sql .= " AND c.country_id = {$country} ";
		if($region) $sql .= " AND c.region_id = {$region} ";
		$sql .= " LIMIT {$limit}";
		
		$core->db->query($sql);
		$core->db->get_rows();
		return $core->db->rows;
	}
	
	
	private function clearSearcheString($request) {
		$request = addslashes(decode_unicode_url($request)); // пришло скорее всего в utf-8, нужно почистить строку
		$request = preg_replace("/[^а-яА-ЯёЁa-zA-Z0-9\ \-\_\+\=]+/isu",'',$request); // убираем все спец-вимволы
		
		return $request;
	}
}