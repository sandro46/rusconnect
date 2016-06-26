<?php

class client_shop extends sv_module
{	
	//** Trade Place **//
	public function delete_item($type, $id){
		$shop_id = 1;
		$client_id = $this->clientId;
		$type = intval($type);
		$id = intval($id);
		
		if($type == "product"){
			$sql = "DELETE FROM  tp_product WHERE client_id = $client_id AND product_id = $id AND shop_id = $shop_id";
			$this->db->query($sql);
		}
		if($type == "group"){
			$sql = "DELETE FROM tp_product_group WHERE client_id = $client_id AND group_id = $id AND shop_id = $shop_id";
			$this->db->query($sql);
		}
	}
	public function set_order_status($status_id, $order_id){
		$shop_id = 1;
		$client_id = $this->clientId;
		$status_id = intval($status_id);
		$order_id = intval($order_id);
		
		$arr = array(
						'shop_id' => $shop_id,
						'client_id' => $client_id,
						'order_id' => $order_id,
						'status_id' => $status_id
				);
		
		$this->db->autoupdate()->table('tp_order')->data(array($arr))->primary('shop_id','client_id','order_id');
		$this->db->execute();
		
	}
	
	public function delete_order_item($prod_id, $order_id){
		$client_id = $this->clientId;
		$shop_id = 1;
		$prod_id = intval($prod_id);
		$order_id = intval($order_id);
		
		$sql = "DELETE FROM tp_order_items WHERE order_id = $order_id AND client_id = $client_id AND shop_id = $shop_id AND product_id = $prod_id";
		$this->db->query($sql);
		
		return $this->get_order_info($order_id, $shop_id);
	}
	public function add_prod_to_order($prod_id, $order_id, $count){
		$order_id = intval($order_id);
		$shop_id = 1;
		$client_id = $this->clientId;

		
		$sql = "SELECT client_id, shop_id, product_id, 	title product_name, price product_cost FROM  tp_product WHERE client_id = $client_id AND product_id = $prod_id AND shop_id = $shop_id";
		$this->db->query($sql);
		
		$this->db->get_rows(1);
		$arr = $this->db->rows;
		$arr['add_date'] = time();
		$arr['count'] = intval($count);
		$arr['order_id'] = $order_id;
			

		
		$this->db->autoupdate()->table('tp_order_items')->data(array($arr));
		$this->db->execute();
		
		return $this->get_order_info($order_id, $shop_id);
	}
	public function save_order_data($data,$order_items){


		$client_id = $this->clientId;
		$shop_id = 1;

		$products = array();
		if(is_array($order_items)){
			foreach($order_items as $item){
				$products[] = array(
										'client_id' => $client_id,
										'shop_id' => $shop_id,
										'order_id' => intval($data['order_id']),
										'product_id' => intval($item['prod_id']),
										'product_cost' => floatval($item['cost']),
										'count' => intval($item['count'])						
						);
			}
			$this->db->autoupdate()->table('tp_order_items')->data($products)->primary('shop_id','client_id','order_id','product_id');
			$this->db->execute();
				
				
		}
		
		$data['client_id'] = $client_id;
		$data['shop_id'] = $shop_id;
		$tmpdate = explode(".", $data['delivery_date']);
		$data['delivery_date'] = mktime(12, 0, 0, $tmpdate[1], $tmpdate[0], $tmpdate[2]);
		
		$this->db->autoupdate()->table('tp_order')->data(array($data))->primary('shop_id','client_id','order_id');
		$this->db->execute();
		return $this->get_order_info($data['order_id'], $data['shop_id']);
	}
	public function get_delivery_types(){
		$sql = "SELECT * FROM tp_delivery_types";
		
		$this->db->query($sql);
		$this->db->get_rows();
		
		return $this->db->rows;
	}
	public function get_order_status_list(){
		$sql = "SELECT * FROM  tp_order_status";
	
		$this->db->query($sql);
		$this->db->get_rows();
	
		return $this->db->rows;
	}
	public function add_new_param_variant($data){
		$client_id = $this->clientId;
		$shop_id = 1;
		
		$arr = array(
				'client_id' => $client_id,
				'shop_id' => $shop_id,
				'name' => $data['name'],
				'parameter_id' => intval($data['param'])
		);
		
		$this->db->autoupdate()->table('tp_product_parameters_variants')->data(array($arr));
		$this->db->execute();
		return $this->get_all_product_params();
	}
	public function add_new_param($data){
		$client_id = $this->clientId;
		$shop_id = 1;
		
		$arr = array(
				'client_id' => $client_id,
				'shop_id' => $shop_id,
				'name' => $data['name']
		);
		$this->db->autoupdate()->table('tp_product_parameters')->data(array($arr));
		$this->db->execute();
		return $this->get_all_product_params();
	}
	
	public function save_product_img($data){
		$client_id = $this->clientId;
		$shop_id = 1;
		$url = $data['url'];
		$id = $data['product_id'];
		$filename = substr($data['filename'],0,strrpos($data['filename'], '.'));
		
		$arr = array(
						'title' => $filename,
						'url'=> $url,
						'url_preview' => $url,
						'product_id'=> $id,
						'client_id' => $client_id,
						'shop_id' => $shop_id,
						'add_date' => time()
		);
		
		$this->db->autoupdate()->table('tp_product_img')->data(array($arr));
		$this->db->execute();
		
		$inser_id = $this->db->insert_id;
		$sql = "SELECT 
							img_id, 
							client_id, 	
							shop_id, 	
							title, 	
							comment, 	
							url, 	
							url_preview, 	
							is_main, 	
							add_date 
				FROM 
							tp_product_img 
				WHERE 
							client_id = $client_id AND 
							shop_id = $shop_id AND 
							img_id = $inser_id";
		
		$this->db->query($sql);
		$this->db->get_rows();
		
		return $this->db->rows;
	}
	public function get_all_product_params(){
		$client_id = $this->clientId;
		$shop_id = 1;
		$data = array();
		
		$sql = "SELECT * FROM tp_product_parameters WHERE client_id = $client_id AND shop_id = $shop_id";
		
		$this->db->query($sql);
		$this->db->get_rows();
		
		$param = $this->db->rows;
		
		foreach ($param as $key=>$val){
			$sql = "SELECT * FROM tp_product_parameters_variants 
					WHERE client_id = $client_id AND shop_id = $shop_id AND parameter_id = ".$val['param_id'];
			$this->db->query($sql);
			$this->db->get_rows();
			$data[$val['param_id']] = $val;
			
			foreach ($this->db->rows as $kkey=>$vval )
				$data[$val['param_id']]['variants'][$vval['variant_id']] = $vval;
		}
 		return $data;
	}
	
	public function save_product_form($data){
		$shop_id = 1;
		$client_id = $this->clientId;
		if(!is_array($data)) return false;
 
// 		print_r($data);
// 		die();
		$arr = array(
				'client_id' =>  $client_id,
				'public_site' => intval($data['public_site']),
				'shop_id' => $shop_id,
				'article' => mysql_real_escape_string($data['article']),
				'title' => mysql_real_escape_string($data['title']),
				'description' => mysql_real_escape_string($data['description']),
				'group_id' => intval($data['group_id']),
				'full_description' => mysql_real_escape_string($data['full_description']),
				'seo_url' => mysql_real_escape_string($data['seo_url']),
				'seo_title' => mysql_real_escape_string($data['seo_title']),
				'seo_keywords' => mysql_real_escape_string($data['seo_keywords']),
				'seo_description' => mysql_real_escape_string($data['seo_description']),
				'price' => floatval($data['price']),
				'measure_id' => intval($data['measure_id']),
				'update_date' => time()
		);
		
		if(intval($data['id'])){
			$arr['product_id'] = intval($data['id']);
			
			$this->db->autoupdate()->table('tp_product')->data(array($arr))->primary('product_id','client_id','shop_id');
			$this->db->execute();
		}else{
			$arr['add_date'] = $arr['update_date'];
			$this->db->autoupdate()->table('tp_product')->data(array($arr));
			$this->db->execute();
			$arr['product_id'] = $this->db->insert_id;
		}
		
		$arr1 = array();
		foreach($data['img'] as $img){
			$arr1[] = array(
					'product_id' => $arr['product_id'],
					'client_id' =>  $client_id,
					'shop_id' => $shop_id,
					'title' => $img['title'],
					'img_id'=>$img['img_id']
			);
		}
		$this->db->autoupdate()->table('tp_product_img')->data($arr1)->primary('client_id','shop_id','img_id');
		$this->db->execute();
		
		
		

		$sql = "DELETE FROM 
							tp_product_to_parameters 
				WHERE
							product_id = ".$arr['product_id']." AND
							client_id = $client_id AND
							shop_id = $shop_id";
		
		$this->db->query($sql);
		
		$arr2 = array();
// 		print_r($data['params']);
// 		//die();

		foreach($data['params'] as $param){
			if(is_array($param))
				foreach($param as $variant)
					if(is_array($variant))
						$arr2[] = array(
							'product_id' => $arr['product_id'],
							'client_id' =>  $client_id,
							'shop_id' => $shop_id,
							'variant_id' => $variant['variant_id'],
							'parameter_id' => $variant['parameter_id']
						);
			
		}


		$this->db->autoupdate()->table('tp_product_to_parameters')->data($arr2);
		$this->db->execute();
		

		return true;
		
	}
	
	
	public function move_group($items=false, $parrent_id=false){
		//print_r($items);
		$shop_id = 1;
		$client_id = $this->clientId;
		
		$folders = implode(", ", array_keys($items['folder']));
		$products = implode(", ", array_keys($items['product']));
		
		$sql = "UPDATE tp_product_group SET parrent_id = $parrent_id WHERE group_id IN($folders) AND shop_id = $shop_id AND	client_id = $client_id ";
		$this->db->query($sql);
		
		$sql = "UPDATE tp_product SET group_id = $parrent_id WHERE product_id IN($products) AND shop_id = $shop_id AND	client_id = $client_id ";
		$this->db->query($sql);
		return $parrent_id;
	}
	
	public function delete_selected($items=false){
		//print_r($items);
		$shop_id = 1;
		$client_id = $this->clientId;
	
		$folders = implode(", ", array_keys($items['folder']));
		$products = implode(", ", array_keys($items['product']));
	
		$sql = "DELETE FROM tp_product_group WHERE group_id IN($folders) AND shop_id = $shop_id AND	client_id = $client_id ";
		$this->db->query($sql);
	
		$sql = "DELETE FROM  tp_product WHERE product_id IN($products) AND shop_id = $shop_id AND	client_id = $client_id ";
		$this->db->query($sql);
		return true;
	}
	
	public function group_public_site($items, $type){
		$shop_id = 1;
		$client_id = $this->clientId;
		
		$folders = implode(", ", array_keys($items['folder']));
		$products = implode(", ", array_keys($items['product']));
		$type = intval($type);
		
		$sql = "UPDATE tp_product SET public_site = $type WHERE group_id IN($folders) AND shop_id = $shop_id AND client_id = $client_id ";
		$this->db->query($sql);
		
		$sql = "UPDATE tp_product SET public_site = $type WHERE product_id IN($products) AND shop_id = $shop_id AND	client_id = $client_id ";
		$this->db->query($sql);
		return true;
	}
	
// 	public function move_group($items=false, $parrent_id=false){
// 		//print_r($items);
// 		$shop_id = 1;
// 		$client_id = $this->clientId;
	
// 		$folders = implode(", ", intval(array_keys($items['folder'])));
// 		$products = implode(", ", intval(array_keys($items['product'])));
	
// 		$sql = "UPDATE tp_product_group SET parrent_id = $parrent_id WHERE group_id IN($folders) AND shop_id = $shop_id AND	client_id = $client_id ";
// 		$this->db->query($sql);
	
// 		$sql = "UPDATE tp_product SET group_id = $parrent_id WHERE product_id IN($products) AND shop_id = $shop_id AND	client_id = $client_id ";
// 		$this->db->query($sql);
// 		return $parrent_id;
// 	}
	
	
	public function get_group_tree(){
		$shop_id = 1;
		$client_id = $this->clientId;
		$sql = "SELECT * FROM tp_product_group WHERE shop_id = $shop_id AND client_id = $client_id ORDER BY parrent_id ASC, group_id ASC";
		$this->db->query($sql);
		$this->db->get_rows();
		return $this->db->rows;
	}
	public function add_new_group($data=false){
		$shop_id = 1;
		$client_id = $this->clientId;
		$data = array(
				'shop_id' => $shop_id,
				'client_id' => $client_id,
				'name' => $data['title'],
				'parrent_id' => intval($data['parrent_id'])
		);
		$this->db->autoupdate()->table('tp_product_group')->data(array($data));
		$this->db->execute();
	}
	public function edit_title($data=false){
		
		//if($type != 'folder' || $type != 'product' || !intval($id)) return;

		$shop_id = 1;
		$client_id = $this->clientId;
		if($data['type'] == 'folder'){
			$data = array(
					'group_id' => intval($data['id']),
					'shop_id' => $shop_id,
					'client_id' => $client_id,
					'name' => $data['title']
			);
			$this->db->autoupdate()->table('tp_product_group')->data(array($data))->primary('group_id','client_id','shop_id');
			$this->db->execute();
		}
		
		
		if($data['type'] == 'product'){
			$data = array(
					'product_id' => intval($data['id']),
					'shop_id' => $shop_id,
					'client_id' => $client_id,
					'title' => $data['title']
			);
				
			$this->db->autoupdate()->table('tp_product')->data(array($data))->primary('product_id','client_id','shop_id');
			$this->db->execute();
		}
		
	}
	public function get_product_list($instance, $page = 0, $limit = 20, $sortBy = 'date', $sortType = 'DESC', $filters = false){

		$start = $page*$limit;
		//print_r($this->clientId);
		$data_out = array();
		if(!isset($filters['group_id'])){
			$filters['group_id'] = 0;
		}
		$site_id = 1;
		$client_id = $this->clientId;
		$cur_group_id = intval($filters['group_id']);
		
		
		//$cur_group_id = 12;
// // 		get crumbs
// // 		SELECT t1.id
// // 		FROM
// // 		test1 t1 JOIN test1 t2
// // 		ON t1.parent = t2.id
// // 		WHERE
// // 		t2.parent = :P
		
// 			$sql = "SELECT
// 							g.name,
// 							g.id
// 					FROM
// 							tp_product_group g
						
										
// 					WHERE
// 								g.shop_id = $site_id AND
// 								g.client_id = $client_id AND
// 								g.group_id = $cur_group_id";
			
// 			$this->db->query($sql);
// 			$this->db->get_rows();
// 			echo $sql;
// 		print_r($this->db->rows);
// 		die();
		
		if($cur_group_id > 0){
			$data_out[0]['row_type'] = 'return';
			$sql = "SELECT 
							parrent_id,
							name
					FROM 
							tp_product_group 
					WHERE 
							shop_id = $site_id AND
							client_id = $client_id AND
							group_id = $cur_group_id";
			$this->db->query($sql);
			$this->db->get_rows(1);
			
			$return_arr = $this->db->rows;
			
			$data_out[0]['group_id'] = $return_arr['parrent_id'];
			$data_out[0]['name'] = $return_arr['name'];
			$data_out[0]['cur_group_id'] = $cur_group_id;
			
		}
		
		
		//–љ–∞—Е–µ—А –љ–∞–і–∞
// 		$crumbs = "";
// 		$ii = $cur_group_id;
// 		$exit = 0;
// 		while (($ii != 0) && $exit<10){
// 			$exit ++;
// 			$sql = "SELECT 
// 							name,
// 							group_id, 	
// 							parrent_id,
// 					FROM 
// 							tp_product_group
// 					WHERE
// 							shop_id = $site_id AND
// 							client_id = $client_id AND
// 							group_id = $ii";
// 			$this->db->query($sql);
// 			$this->db->get_rows(1);
// 			$mm = $this->db->rows;
// 			$ii = $mm['parrent_id'];
// 			$crumbs = $mm['name']."/".$crumbs;
// 		}

// 		$crumbs = "/home/".$crumbs;
		
		//echo $crumbs;

		$sql = "SELECT
						SQL_CALC_FOUND_ROWS
						'folder' row_type,
						(SELECT 
								count(p.product_id) 
						 FROM
						 		tp_product p
						 WHERE
						 		p.client_id = $client_id AND
						 		p.shop_id = $site_id AND
						 		p.group_id = g.group_id) items_count,
						g.shop_id,
						g.client_id,
						g.group_id,
						g.parrent_id,
						g.name,
						g.hidden
				FROM
						tp_product_group g
				WHERE
						g.shop_id = $site_id AND
						g.client_id = $client_id AND
						g.parrent_id = $cur_group_id
				ORDER BY 
						g.name";
		
		$this->db->query($sql);
		$this->db->get_rows();
		
		foreach ($this->db->rows as $val){
			$data_out[] = $val;
		}
		
		$count = $this->db->found_rows();
		
		
		$sql = "SELECT
						SQL_CALC_FOUND_ROWS
						'product' row_type,
						p.product_id id,
						IFNULL((SELECT 
								img.url_preview 
						 FROM 
						 		tp_product_img img 
						 WHERE 
						 		img.product_id = p.product_id AND
						 		img.client_id = p.client_id AND
						 		img.shop_id = p.shop_id  LIMIT 1), '/templates/admin/green/images/no_image.png') img,
						p.title title,
						p.update_date update_date,
						p.price,
						IF(p.public_site,'<span style=\"color:#494\"> Опубликован</span>','<span style=\"color:#944\">Скрыт</span>') public_site,
						s.name status_name,
						t.name product_type_name
				FROM
						tp_product as p
						LEFT JOIN
									 tp_product_status s
						ON
									s.id = p.status_id
						LEFT JOIN
									tp_product_type t
						ON
									t.id = p.type_id
						WHERE
						p.shop_id = $site_id AND
						p.client_id = $client_id AND
						p.group_id = $cur_group_id";
		$sql .= " ORDER BY $sortBy $sortType LIMIT $start,$limit";
		$this->db->query($sql);
		$this->db->add_fields_deform(array('update_date'));
		$this->db->add_fields_func(array('h_date'));
		$this->db->get_rows();
		
		foreach ($this->db->rows as $val){
			$data_out[] = $val;
		}
		$count = $count + $this->db->found_rows();
		
		//print_r($data_out);
		
		
		
		return array(array('magiklongx'=>$count), $data_out);
	}
	public function get_order_info($order_id, $shop_id){		
		$client_id = $this->clientId;

		$data = array();
		$shop_id = intval($shop_id);
		$order_id = intval($order_id);
		//–Ј–Ї–∞–Ї–∞–Ј –Є–љ—Д–Њ
		$sql = "SELECT 
					o.client_id,
					o.order_id,
					o.shop_id,
					o.contact_id,
					o.status_id,
					o.status_pay_id,
					o.delivery_type_id,
					o.delivery_date,
					o.delivery_time,
					o.create_date,
					o.address,
					o.comment comment,
					o.recipient_info,
					o.recipient_phone
				FROM
					tp_order o
				WHERE
					o.client_id = $client_id AND
					o.shop_id = $shop_id AND
					o.order_id = $order_id";
		$this->db->query($sql);
		$this->db->add_fields_deform(array('delivery_date'));
		$this->db->add_fields_func(array('datepicker'));
		$this->db->get_rows(1);
		$data['order'] = $this->db->rows;
		
		//–Ш–љ—Д–∞ –Њ –њ–Њ–Ї—Г–њ–∞—В–µ–ї–µ
		$contact_id = $data['order']['contact_id'];
	
		$sql = "SELECT 
						c.client_id,
						c.contact_id,
						c.user_id,
						c.date,
						c.company_id,
						c.name buyer_name,
						c.responsible_user_id,
						c.post,
						c.departament,
						c.source_id,
						c.comment
						
				FROM 
						crm_contacts c
				WHERE
						c.client_id = $client_id AND
						c.contact_id = $contact_id";
		
		$this->db->query($sql);
		$this->db->get_rows(1);
		$data['contact'] = $this->db->rows;
		
		$sql = "SELECT
						e.client_id,
						e.contact_id,
						e.email_type,
						e.email
						
				FROM
						crm_contacts_email e
				WHERE
						e.client_id = $client_id AND
						e.contact_id = $contact_id";
		
		$this->db->query($sql);
		$this->db->get_rows();
		
		
		$data['contact']['email'] = $this->db->rows;
		
		$sql = "SELECT
						p.client_id,
						p.contact_id,
						p.phone_type,
						p.phone
		
				FROM
						crm_contacts_phones p
				WHERE
						p.client_id = $client_id AND
						p.contact_id = $contact_id";
		
		$this->db->query($sql);
		$this->db->get_rows();
		$data['contact']['phone'] = $this->db->rows;
		
		//—Б–њ–Є—Б–Њ–Ї –њ–Њ–Ј–Є—Ж–Є–є –≤ –Ј–∞–Ї–∞–Ј–µ
		$sql = "SELECT 
						i.client_id,
						i.order_item_id ,
						i.shop_id,
						i.product_id,
						i.product_name product_name,
						i.product_cost,
						i.add_date,
						i.count,
						p.article
				FROM
						tp_order_items i
						LEFT JOIN 
								tp_product p 
						ON 
								p.product_id = i.product_id AND
								p.shop_id = i.shop_id AND
								p.client_id = i.client_id
				WHERE
						i.client_id = $client_id AND
						i.order_id = $order_id AND
						i.shop_id = $shop_id
			";
		$this->db->query($sql);
		$this->db->get_rows();
		
		$data['items'] = $this->db->rows;
		
		return $data;
	}
	public function get_order_list($instance, $page = 0, $limit = 20, $sortBy = 'date', $sortType = 'DESC', $filters = false){

		$start = $page*$limit;
		
		$site_id = 1;
		$client_id = $this->clientId;
		$sql = "SELECT
						SQL_CALC_FOUND_ROWS
						o.client_id, 
						o.contact_id,
						o.order_id, 
						o.shop_id, 
						o.status_id, 
						o.status_pay_id, 
						o.delivery_type_id,
						o.delivery_date,
						o.create_date,
						o.address,
						o.comment,
						o.recipient_info,
						get_order_sum(o.client_id, o.order_id, o.shop_id) sum,
						c.name client_name,
						s.name order_status_name,
						s.color color,
						p.name order_pay_name,
						d.name delivery_name
						
				FROM 
						tp_order as o
						LEFT JOIN 
									crm_contacts c 
						ON 
									o.contact_id = c.contact_id AND
									o.client_id = c.client_id
						LEFT JOIN 
									tp_order_status s
						ON
									s.id = o.status_id
						LEFT JOIN 
									tp_order_pay_status p
						ON
									p.id = o.status_pay_id
						LEFT JOIN 
									tp_delivery_types d
						ON
									d.id = o.delivery_type_id
						
				WHERE 
						o.shop_id = $site_id AND
						o.client_id = $client_id";
		
		$sql .= " ORDER BY $sortBy $sortType LIMIT $start,$limit";
		//echo $sql;
		$this->db->query($sql);
		$this->db->add_fields_deform(array('create_date','delivery_date'));
		$this->db->add_fields_func(array('h_date','h_date'));
		$this->db->get_rows();
		//echo $sql;
		
		return $this->db->rows;
		
	}
	
	public function tp_get_all_product_info($product_id = false){

		$shop_id = 1;
		$product_info = array();
		$product_id = intval($product_id);
		$client_id = $this->clientId;
		if(!$product_id || !$shop_id)
			return "Товар не найден";
		
		$sql = "SELECT
						p.title,
						p.price,
						p.description,
						p.full_description,
						p.group_id,
						IFNULL((SELECT 
								g.name 
						 FROM 
						 		tp_product_group g 
						 WHERE 
						 		g.shop_id = p.shop_id AND 
						 		g.client_id = p.client_id AND
						 		g.group_id = p.group_id), 'Корневая группа') group_name,
						p.seo_url,
						p.seo_title,
						p.seo_keywords,
						p.seo_description,
						p.add_date,
						p.update_date,
						p.article,
						p.public_site,
						p.measure_id,
						p.product_id
				FROM 
						tp_product p
				WHERE
						p.shop_id = $shop_id AND
						p.client_id = $client_id AND
						p.product_id = $product_id";
		
		$this->db->query($sql);
		$this->db->add_fields_deform(array('add_date','update_date'));
		$this->db->add_fields_func(array('h_date','h_date'));
		$this->db->get_rows();
		
 		if($this->db->num_rows() != 1)
			return "Товар не найден";
 		
		$product_info = $this->db->rows;
		$product_info = $product_info[0];
		
		//Картинки
		$sql = "SELECT * FROM tp_product_img WHERE  shop_id = $shop_id AND	client_id = $client_id AND product_id = $product_id";
		$this->db->query($sql);
		$this->db->get_rows();
		
		$product_info['img'] = $this->db->rows;
		
		//параметры товара
		$product_info['params_all'] = $this->get_all_product_params();
		$sql = "SELECT 
						p.*,
						v.name name
				FROM
						tp_product_to_parameters p
						LEFT JOIN 
							 tp_product_parameters_variants v
						ON
							v.parameter_id = p.parameter_id AND
							v.variant_id = p.variant_id  AND
							v.shop_id = $shop_id AND 
							v.client_id = $client_id
				WHERE  
						p.shop_id = $shop_id AND 
						p.client_id = $client_id AND 
						p.product_id = $product_id";
		
		$this->db->query($sql);
		$this->db->get_rows();

		
		
		$tmp = $this->db->rows;
		$par = array();
		foreach($tmp as $item){
			$par[$item['parameter_id']][$item['variant_id']] = $item;
			
		}
		$product_info['params'] = $par;
		
		
		return $product_info;
	}
	
	

	public function tp_add_product_parameter($site_id, $name, $id = false){

		$data = array();

		$site_id = intval($site_id);
		$id = intval($id);
		$name = mysql_real_escape_string((addslashes($name)));
		
		
		$data = array(
						'site_id' => $site_id,
						'client_id' => $this->clientId,
						'name' => $name
		);
		
		if($id) $data['id'] = $id;
			
		
		$this->db->autoupdate()->table('tp_product_parameters')->data(array($data))->primary('id');
		$this->db->execute();
		return $this->db->insert_id;
	}
}
function datepicker($date){
	return ($date)? date('d.m.Y',$date) : '-';
}
function h_date($date){
	return ($date)? date('d-m-Y в H:m',$date) : '-';
}
?>