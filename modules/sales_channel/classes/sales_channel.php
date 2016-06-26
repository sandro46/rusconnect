<?php 


class admin_sales_channel extends main_module {
	
	
	public function getChannelList($instance, $start = 0, $limit = 20, $sortBy = 'o.order_id', $sortType = 'DESC', $filters = false) {
		$sql = "SELECT
					SQL_CALC_FOUND_ROWS
					c.*, 
					ct.name as channel_type,
					(SELECT COUNT(*) FROM sc_channel_data as cd WHERE cd.channel_id = c.id AND cd.client_id = {$this->clientId} AND cd.shop_id = {$this->shopId}) as products,
					(SELECT COUNT(*) FROM sc_channel_shop as cs WHERE cs.channel_id = c.id AND cs.client_id = {$this->clientId} AND cs.shop_id = {$this->shopId}) as installed
				FROM
					sc_channel as c
						LEFT JOIN sc_channel_type as ct ON ct.id = c.type_id 
				WHERE
					c.active = 1 ";
		$onerow = false;
						
		if(!empty($filters['id']) && intval($filters['id'])) {
			$onerow = true;
			$sql .= " AND c.id = ".intval($filters['id']);
		}
		
		$sql .= " ORDER BY $sortBy $sortType LIMIT $start,$limit";
		$this->db->query($sql);
		$this->db->filter('installed', function($field, $row, &$RowLink) {
			$field = (intval($field) > 0)? true : false;
			$RowLink['installed_label'] = ($field)? 'success' : 'danger';
			$RowLink['installed_name'] = ($field)? 'Подключено' : 'Не подключено';
			return ($field)? 1 : '';
		});
		$this->db->get_rows($onerow);
		
		return $this->db->rows;
	}
	
	public function getMyChannelList($instance, $start = 0, $limit = 20, $sortBy = 'o.order_id', $sortType = 'DESC', $filters = false) {
		if(!isset($filters['id']) || !intval($filters['id'])) return false;
		$cnahhelId = intval($filters['id']);
		
		$sql = "SELECT
					SQL_CALC_FOUND_ROWS
					cd.*,
					ct.name as type_name
				FROM 
					sc_channel_data as cd
						LEFT JOIN sc_channel_data_type as ct ON ct.id = cd.type_id
				WHERE
					client_id = {$this->clientId} AND
					shop_id = {$this->shopId} AND
					channel_id = {$cnahhelId} ";
		
		$sql .= " ORDER BY $sortBy $sortType LIMIT $start,$limit";
		
		$this->db->query($sql);
		
		$this->db->filter('data', function($field) {
			return json_decode($field, true);
		});
		
		$this->db->filter('create_date', function($field) {
			return date('d.m.Y H:i', $field);
		});
		
		$this->db->filter('modify_date', function($field) {
			return date('d.m.Y H:i', $field);
		});
		
		$this->db->filter('publiched', function($field, $row, &$rowEntry) {
			$field = intval($field);
			$rowEntry['publiched_name'] = ($field)? 'Да' : 'Нет';
			$rowEntry['publiched_labble'] = ($field)? 'success' : 'default';			
			return $field;
		});
		
		$this->db->get_rows();
		$list = $this->db->rows;
		
		$getProductInfo = function($id, $self) {
			$sql = "SELECT get_rewrite(p.rewrite_id) rewrite, '' as url, p.product_id as id, CONCAT('(#',  p.article, ') ',  p.title) as title FROM tp_product as p WHERE p.shop_id = {$self->shopId} AND p.client_id = {$self->clientId} AND p.product_id = {$id}";
			$self->db->query($sql);
			$self->db->filter('url', $self->filters['product_url']);
			$self->db->get_rows(1);
			
			return $self->db->rows;
		};
		
		$getPageInfo = function($id, $self) {
			$sql = "SELECT get_rewrite(p.rewrite_id) rewrite, '' as url, p.page_id as id, p.title FROM site_pages as p WHERE p.site_id = {$self->shopId} AND p.page_id = {$id}";
			$self->db->query($sql);
			$self->db->filter('url', $self->filters['page_url']);
			$self->db->get_rows(1);
				
			return $self->db->rows;
		};
		
		foreach($list as $k=>$item) {
			if($item['type_id'] == 1) {
				$info = $getProductInfo($item['product_id'], $this);
			} else if($item['type_id'] == 2) {
				$info = $getPageInfo($item['page_id'], $this);
			} else {
				$info = false;
			}
			
			$list[$k]['entry_info'] = $info;
		}
		
		return $list;
	}
	
	public function getChannelInfo($id) {
		if(!$id = intval($id)) return false;
		$info = $this->getChannelList('local', 0, 1, null, null, array('id'=>$id));
		return $info;
	}
	
	public function getChannelSetting($id) {
		$sql = "SELECT * FROM sc_channel_setting WHERE channel_id = {$id}";
		$this->db->query($sql);
		$this->db->get_rows(1);
		$info = $this->db->rows;
		
		return $info;
	}
	
	public function addChannel($id) {
		if(!$id = intval($id)) return false;
		
		$sql = "SELECT id FROM sc_channel_shop WHERE client_id = {$this->clientId} AND shop_id = {$this->shopId} AND channel_id = {$id}";
		$this->db->query($sql);
		if(intval($this->db->get_field())) return true;
		
		$data = array(
			'client_id'=>$this->clientId,
			'shop_id'=>$this->shopId,
			'channel_id'=>$id
		);
		
		$this->db->autoupdate()->table('sc_channel_shop')->data(array($data));
		$this->db->execute();
		
		return true;
	}
	
	public function removeChannel($id) {
		if(!$id = intval($id)) return false;
		
		$sql = "DELETE FROM sc_channel_shop WHERE  client_id = {$this->clientId} AND shop_id = {$this->shopId} AND channel_id = {$id}";
		$this->db->query($sql);
		
		return true;
	}
	
	
	
}


?>