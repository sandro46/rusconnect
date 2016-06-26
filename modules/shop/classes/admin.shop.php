<?php

class admin_shop extends main_module {	
				
	public function deleteGroup($groupId) {
		
		$sql = "SELECT product_id FROM tp_product WHERE group_id = {$groupId} AND client_id = {$this->clientId} AND shop_id = {$this->shopId} OR product_id IN(SELECT pg.product_id FROM tp_product_to_group as pg WHERE pg.group_id = {$groupId} AND pg.shop_id = {$this->shopId} AND pg.client_id = {$this->clientId})";
		$this->db->query($sql);
		$this->db->get_rows();
		$products = $this->db->rows;
		
		foreach($products as $item) {
			$this->deleteProduct($item['product_id']);
		}
		
		$sql = "DELETE FROM tp_product_group WHERE group_id = {$groupId} AND client_id = {$this->clientId} AND shop_id = {$this->shopId}";
		$this->db->query($sql);
				
		$sql = "SELECT group_id FROM tp_product_group WHERE parrent_id = {$groupId} AND client_id = {$this->clientId} AND shop_id = {$this->shopId}";
		$this->db->query($sql);
		$this->db->get_rows();
		$groups = $this->db->rows;
		
		if(!empty($groups) && is_array($groups) && count($groups) > 0) {
			foreach($groups as $item) {
				$this->deleteGroup($item['group_id']);
			}
		}
	}
	
	public function deleteProduct($productId) {
		$sql = "DELETE FROM tp_product WHERE product_id = {$productId} AND client_id = {$this->clientId} AND shop_id = {$this->shopId}";
		$this->db->query($sql);
		
		$sql = "DELETE FROM tp_product_comment WHERE product_id = {$productId} AND client_id = {$this->clientId} AND shop_id = {$this->shopId}";
		$this->db->query($sql);
		
		$sql = "DELETE FROM tp_product_dimensions WHERE product_id = {$productId} AND client_id = {$this->clientId} AND shop_id = {$this->shopId}";
		$this->db->query($sql);
		
		$sql = "DELETE FROM tp_product_img WHERE product_id = {$productId} AND client_id = {$this->clientId} AND shop_id = {$this->shopId}";
		$this->db->query($sql);
		
		$sql = "DELETE FROM tp_product_sales WHERE product_id = {$productId} AND client_id = {$this->clientId} AND shop_id = {$this->shopId}";
		$this->db->query($sql);
		
		$sql = "DELETE FROM tp_product_store WHERE product_id = {$productId} AND client_id = {$this->clientId} AND shop_id = {$this->shopId}";
		$this->db->query($sql);
		
		$sql = "DELETE FROM tp_product_to_feature WHERE product_id = {$productId} AND client_id = {$this->clientId} AND shop_id = {$this->shopId}";
		$this->db->query($sql);
		
		$sql = "DELETE FROM tp_product_to_feed WHERE product_id = {$productId} AND client_id = {$this->clientId} AND shop_id = {$this->shopId}";
		$this->db->query($sql);
		
		$sql = "DELETE FROM tp_product_to_group WHERE product_id = {$productId} AND client_id = {$this->clientId} AND shop_id = {$this->shopId}";
		$this->db->query($sql);
		
		$sql = "DELETE FROM tp_product_to_ym_feature WHERE product_id = {$productId} AND client_id = {$this->clientId} AND shop_id = {$this->shopId}";
		$this->db->query($sql);	
	}
	
	public function deleteVendor($vendorId) {
		$vendorId = intval($vendorId);
		if(!$this->checkAccessToVendor($vendorId)) return false;
		
		$sql = "DELETE FROM tp_vendors WHERE id = {$vendorId}";
		$this->db->query($sql);
		
		$sql = "UPDATE tp_product SET vendor_id = 0 WHERE vendor_id = {$vendorId} AND shop_id = {$this->shopId} AND client_id = {$this->clientId}";
		$this->db->query($sql);
		
		return true;
	}
	
	public function addCategory($id, $parent_id, $data) {
		$id = intval($id);
		$parent_id = intval($parent_id);
		
		$isNew = ($id)? false : true;
		
		$tp_product_group = array(
			'shop_id'=>$this->shopId,
			'client_id'=>$this->clientId,
			'parrent_id'=>$parent_id,
			'name'=>mysql::str($data['c.name']),
			'meta_description'=>mysql::str($data['c.meta_description']),
			'meta_keyword'=>mysql::str($data['c.meta_keyword']),
			'meta_title'=>mysql::str($data['c.meta_title']),
			'description'=>mysql::str($data['c.description']),
			'url_big'=>(isset($data['image_original'])) ? mysql::str($data['image_original']): '',
			'url_preview'=>(isset($data['image_original'])) ? mysql::str($data['image_preview']): '',
			'ext_char1'=>(isset($data['image_icon'])) ? mysql::str($data['image_icon']): '',
			'product_category'=>intval($data['c.category']),
		    'ext_text2'=>(empty($data['description2']))? '' : mysql::str($data['description2'])
		);
		
		if(!$isNew) {
			$tp_product_group['group_id'] = $id;
			$this->db->autoupdate()->table('tp_product_group')->data(array($tp_product_group))->primary('group_id','client_id','shop_id');
			$this->db->execute();
		} else {
			$this->db->autoupdate()->table('tp_product_group')->data(array($tp_product_group));
			$this->db->execute();
			$id = $this->db->insert_id;
		}
		
		$data['url'] = $data['c.url'];
		
		// рерайт
		if($data['url'] && $isNew) {
			$data['url'] = mysql::str($data['url']);
			if(substr($data['url'], 0,1) != '/') $data['url'] = '/'.$data['url'];
				
			$sql = "SELECT COUNT(*) FROM mcms_rewrite WHERE rewrite = '{$data['url']}' AND id_site = {$this->shopId}";
			$this->db->query($sql);
			if(intval($this->db->get_field())) {
				return -101;
			} else {
				$rewrite = array(
					'group'=>'Total',
					'rewrite'=>$data['url'],
					'real_url'=>"/shop/category/id/{$id}/",
					'id_site'=>$this->shopId
				);
		
				$this->db->autoupdate()->table('mcms_rewrite')->data(array($rewrite));
				$this->db->execute();
				$rewrite_id = $this->db->insert_id;
				$tp_product_group = array(
					'client_id'=>$this->clientId,
					'group_id'=>$id,
					'shop_id'=>$this->shopId,
					'rewrite_id'=>$rewrite_id
				);
		
				$this->db->autoupdate()->table('tp_product_group')->data(array($tp_product_group))->primary('group_id','client_id','shop_id');
				$this->db->execute();
			}
		} else if(!$isNew && !empty($data['url'])) {
			$sql = "SELECT rewrite_id FROM tp_product_group WHERE shop_id={$this->shopId} AND client_id = {$this->clientId} AND group_id = {$id}";
			$this->db->query($sql);
			$rewrite_id = intval($this->db->get_field());
				
			if(substr($data['url'], 0,1) != '/') $data['url'] = '/'.$data['url'];
				
			if(!$rewrite_id) {
				$rewrite = array(
					'group'=>'Total',
					'rewrite'=>$data['url'],
					'real_url'=>"/shop/category/id/{$id}/",
					'id_site'=>$this->shopId
				);
		
				$this->db->autoupdate()->table('mcms_rewrite')->data(array($rewrite));
				$this->db->execute();
				
				$rewrite_id = $this->db->insert_id;
				
				$tp_product_group = array(
					'client_id'=>$this->clientId,
					'group_id'=>$id,
					'shop_id'=>$this->shopId,
					'rewrite_id'=>$rewrite_id
				);
		
				
				$this->db->autoupdate()->table('tp_product_group')->data(array($tp_product_group))->primary('group_id','client_id','shop_id');
				$this->db->execute();
			} else {
				$rewrite = array(
					'id'=>$rewrite_id,
					'group'=>'Total',
					'rewrite'=>$data['url'],
					'real_url'=>"/shop/category/id/{$id}/",
					'id_site'=>$this->shopId
				);
		
				$this->db->autoupdate()->table('mcms_rewrite')->data(array($rewrite))->primary('id');
				$this->db->execute();
			}
		}
		
		
		
		return $id;
	}
	
	public function addProduct($data, $productId=false) {
		
		$productId = intval($productId);
		$isNew = true;
		
		if($productId) {
			$sql = "SELECT COUNT(*) FROM tp_product WHERE shop_id={$this->shopId} AND client_id = {$this->clientId} AND product_id = {$productId}";
			$this->db->query($sql);
			if(!$this->db->get_field()) return -2;
			$isNew = false;
		}
		
		$tp_product = array(
			'client_id'=>$this->clientId,
			'shop_id'=>$this->shopId,
			'article'=>mysql::str($data['article']),
			'title'=>mysql::str($data['title']),
			'description'=>mysql::str($data['description']),
			'category_id'=>intval($data['category_id']),
			'status_id'=>(intval($data['publiched']) == 1)? 1 : 2,
			'group_id'=>intval($data['group_id']),
			'avaliable_type'=>intval($data['avaliable']['type']),
			'avaliable_pending'=>intval($data['avaliable']['pending']),
			'full_description'=>mysql::str($data['full_description']),
		    'full_description2'=>mysql::str($data['full_description2']),
		    'full_description3'=>mysql::str($data['full_description3']),
			'seo_keywords'=>mysql::str($data['meta_keywords']),
			'seo_description'=>mysql::str($data['meta_description']),
			'price'=>$this->clearDoubleNumbers($data['retail_price']),
			'add_date'=>time(),
			'update_date'=>time(),
			'vendor_id'=>intval($data['vendor_id']),
			'sales'=>0,
			'price_new'=>0.00,
			'sales_summ'=>0.00,
			'sales_procent'=>0,
			'sales_start'=>0,
			'sales_end'=>0
		);
		
		/***/
		
		$tp_product['price_type'] = intval($data['price_type']);
		$tp_product['pack_size'] = floatval($data['pakage']);
		$tp_product['pack_unit'] = intval($data['unit']);
		$tp_product['min_order'] = intval($data['minorder']);
		$tp_product['price2'] = $this->clearDoubleNumbers($data['price2']);
		$tp_product['price3'] = $this->clearDoubleNumbers($data['price3']);
		$tp_product['price4'] = $this->clearDoubleNumbers($data['price4']);
		$tp_product['price5'] = $this->clearDoubleNumbers($data['price5']);
	
		/***/
		// скидки
		if($data['wholesale'] && $isNew) { // скидка на опт
			// product_id, type, start_date, end_date, new_price, procent, quantity
			//$this->addProductSales($productId, 2, time(), time(), 0.00, intval($data['wholesale_procent']), intval($data['wholesale_qt']));
		}
		
		if($data['sales']) { // скидка просто
			$salesType = ($data['sales_data']['timeaction'])? 2 : 1;
			$salesSumm = ($data['sales_data']['summ'])? floatval($data['sales_data']['summ']) : $tp_product['price']*(intval($data['sales_data']['procent'])/100);
			$priceNew =  $tp_product['price'] - $salesSumm;
			
			$tp_product['sales'] = $salesType;
			$tp_product['price_new'] = $priceNew;
			$tp_product['sales_summ'] = $salesSumm;
			$tp_product['sales_procent'] = (intval($data['sales_data']['procent']))? intval($data['sales_data']['procent']) : false;
			if($tp_product['sales_procent'] === false && $priceNew > 0) $tp_product['sales_procent'] = round((100*$salesSumm/$priceNew));
			$tp_product['sales_start'] = ($salesType == 2)? $this->makeDate($data['sales_data']['start']) : 0 ;
			$tp_product['sales_end'] = ($salesType == 2)?  $this->makeDate($data['sales_data']['stop']) : 0 ;
		}
			
		
		if($productId) {
			$tp_product['product_id'] = $productId;
			$this->db->autoupdate()->table('tp_product')->data(array($tp_product))->primary('client_id','shop_id', 'product_id');
			$this->db->execute();
		} else {
			$this->db->autoupdate()->table('tp_product')->data(array($tp_product));
			$this->db->execute();
			$productId = $this->db->insert_id;
			
			if(!$productId) return -100;
		}
		
		if($this->db->error) return $this->db->error;
		
		// рерайт
		if($data['url'] && $isNew) {
			$data['url'] = mysql::str($data['url']);
			if(substr($data['url'], 0,1) != '/') $data['url'] = '/'.$data['url'];
			
			$sql = "SELECT COUNT(*) FROM mcms_rewrite WHERE rewrite = '{$data['url']}' AND id_site = {$this->shopId}";
			$this->db->query($sql);
			if(intval($this->db->get_field())) {
				$data['url'] = '/'.$productId.'-'.substr($data['url'],1);
			} 
				
			$rewrite = array(
				'group'=>'Total',
				'rewrite'=>$data['url'],
				'real_url'=>"/shop/product/id/{$productId}/",
				'id_site'=>$this->shopId
			);
			
			
			$this->db->autoupdate()->table('mcms_rewrite')->data(array($rewrite));
			$this->db->execute();
			$rewrite_id = $this->db->insert_id;
			$tp_product = array(
					'client_id'=>$this->clientId,
					'product_id'=>$productId,
					'shop_id'=>$this->shopId,
					'rewrite_id'=>$rewrite_id
			);
			
			$this->db->autoupdate()->table('tp_product')->data(array($tp_product))->primary('client_id','product_id','shop_id');
			$this->db->execute();
			
		} else if(!$isNew && !empty($data['url'])) {
			$sql = "SELECT rewrite_id FROM tp_product WHERE shop_id={$this->shopId} AND client_id = {$this->clientId} AND product_id = {$productId}";
			$this->db->query($sql);
			$rewrite_id = intval($this->db->get_field());
			
			if(substr($data['url'], 0,1) != '/') $data['url'] = '/'.$data['url'];
			
			if(!$rewrite_id) {
				$rewrite = array(
						'group'=>'Total',
						'rewrite'=>$data['url'],
						'real_url'=>"/shop/product/id/{$productId}/",
						'id_site'=>$this->shopId
				);
				
				$this->db->autoupdate()->table('mcms_rewrite')->data(array($rewrite));
				$this->db->execute();
				$rewrite_id = $this->db->insert_id;
				$tp_product = array(
						'client_id'=>$this->clientId,
						'product_id'=>$productId,
						'shop_id'=>$this->shopId,
						'rewrite_id'=>$rewrite_id
				);
				
				$this->db->autoupdate()->table('tp_product')->data(array($tp_product))->primary('client_id','product_id','shop_id');
				$this->db->execute();
			} else {
				$rewrite = array(
						'id'=>$rewrite_id,
						'group'=>'Total',
						'rewrite'=>$data['url'],
						'real_url'=>"/shop/product/id/{$productId}/",
						'id_site'=>$this->shopId
				);
				
				$this->db->autoupdate()->table('mcms_rewrite')->data(array($rewrite))->primary('id');
				$this->db->execute();
			}
			// получаем текущий rewrite id
			// если он есть, обновляем. 
			// если нет, добавляем
		}
		
		// дополнительные категории
		if(!$isNew) {
			$sql = "DELETE FROM tp_product_to_group WHERE shop_id={$this->shopId} AND client_id = {$this->clientId} AND product_id = {$productId}";
			$this->db->query($sql);
		}
		
		if(is_array($data['extended_groups']) && count($data['extended_groups']) > 0) {
			$tp_product_to_group = array();
			
			foreach($data['extended_groups'] as $item) {
				if(!intval($item)) continue;
				
				$tp_product_to_group[] = array(
					'client_id'=>$this->clientId,
					'product_id'=>$productId,
					'shop_id'=>$this->shopId,
					'group_id'=>intval($item)
				);
			}
			
			if(count($tp_product_to_group) > 0) {
				$this->db->autoupdate()->table('tp_product_to_group')->data($tp_product_to_group);
				$this->db->execute();
			}
		}
		
		// наличие
		if(intval($data['avaliable']['type']) == 1 && intval($data['avaliable']['store_id'])) {
			$tp_product_store = array(
				'client_id'=>$this->clientId,
				'product_id'=>$productId,
				'shop_id'=>$this->shopId,
				'store_id'=>intval($data['avaliable']['store_id']),
				'quantity'=>intval($data['avaliable']['quantity'])
			);
			
			
			$this->db->autoupdate()->table('tp_product_store')->data(array($tp_product_store))->primary('client_id','product_id','shop_id');
			$this->db->execute();
		}
		
		// лента товаров
		$this->addProductFeed($data['feeds'], $productId);
		
		// картинки
		$this->addProductImages($data['images'], $data['remove_helper']['images'], $productId);
				
		
		// габариты
		$tp_product_dimensions = array(
			'client_id'=>$this->clientId,
			'product_id'=>$productId,
			'shop_id'=>$this->shopId,
			'width'=>$this->clearDoubleNumbers($data['size']['width'][0]),
			'height'=>$this->clearDoubleNumbers($data['size']['height'][0]),
			'depth'=>$this->clearDoubleNumbers($data['size']['depth'][0]),
			'weight'=>$this->clearDoubleNumbers($data['size']['weight'][0]),
			'width_unit'=>intval($data['size']['width'][1]),
			'height_unit'=>intval($data['size']['height'][1]),
			'depth_unit'=>intval($data['size']['depth'][1]),
			'weight_unit'=>intval($data['size']['weight'][1])
		);
		
		$this->db->autoupdate()->table('tp_product_dimensions')->data(array($tp_product_dimensions))->primary('client_id','product_id','shop_id');
		$this->db->execute();
		
		// пользовательские характеристики
		if(is_array($data['user_features']) && count($data['user_features']) > 0) {
			$this->processUserFeatures($productId, $data['user_features']);
		} else {
			$this->clearUserFeatures($productId);
		}

		if(empty($tp_product['category_id'])) {
			$tp_product['category_id'] = 0;
		}
		
		// Yandex характеристики
		if(is_array($data['ym_features']) && count($data['ym_features']) > 0 && $tp_product['category_id'] > 0) {
			foreach($data['ym_features'] as $item) {
				$this->addYMFeature($productId, $item);
			}
		} else {
			$this->clearYMFeature($productId);
		}
				
		// Фикс старых Yandex характеристик
		if($tp_product['category_id'] > 0) {
			$sql = "SELECT pyf.id, pyf.feature_id, yf.cat_id FROM tp_product_to_ym_feature as pyf LEFT JOIN ym_features as yf ON yf.id = pyf.feature_id  WHERE pyf.shop_id = {$this->shopId} AND pyf.client_id = {$this->clientId} AND pyf.product_id = {$productId} AND yf.cat_id != {$tp_product['category_id']}";
			$this->db->query($sql);
			$this->db->get_rows();
			if(!empty($this->db->rows)) {
				foreach($this->db->rows as $row) {
					$sql = "DELETE FROM tp_product_to_ym_feature WHERE id = {$row['id']}";
					$this->db->query($sql);
				}
			}	
		} else {
			$sql = "DELETE FROM tp_product_to_ym_feature WHERE shop_id = {$this->shopId} AND client_id = {$this->clientId} AND product_id = {$productId}";
		}

		/*  модификации */		
		if(!empty($data['modifications']) && is_array($data['modifications'])) {
		    
		    foreach($data['modifications'] as $index=>$mod) {
		        $mId = intval($mod['id']);
		        $mod['price1'] = $this->clearDoubleNumbers($mod['price1']); 
		        $mod['price2'] = $this->clearDoubleNumbers($mod['price2']); 
		        $mod['price3'] = $this->clearDoubleNumbers($mod['price3']);
		        $mod['price4'] = $this->clearDoubleNumbers($mod['price4']);
		        $mod['price5'] = $this->clearDoubleNumbers($mod['price5']);
		        $mod['title'] = mysql::str($mod['title']);
		        $mod['article'] = mysql::str($mod['article']);
		        $mod['position'] = ($index + 1) * 10;
		        
		        
		        $this->updateModificationFromProduct($mId, $productId, $mod);
		    }
		}
		
		
		return $productId;
	}
	
	public function addYMFeature($productId, $feature) { 
		$tp_product_to_ym_feature = array(
			'client_id'=>$this->clientId,
			'shop_id'=>$this->shopId,
			'product_id'=>$productId,
			'feature_id'=>intval($feature['id']),
			'variant_id'=>intval($feature['variant_id']),
			'value'=>mysql::str($feature['value'])
		);
		
		$this->db->autoupdate()->table('tp_product_to_ym_feature')->data(array($tp_product_to_ym_feature))->primary('client_id','product_id','shop_id','feature_id');
		$this->db->execute();
		
		return $this->db->insert_id;
	}
	
	public function clearYMFeature($productId) {
		$sql = "DELETE FROM tp_product_to_ym_feature WHERE client_id = {$this->clientId} AND product_id = {$productId} AND shop_id = {$this->shopId}";
		$this->db->query($sql);
		
		return true;
	}
	
	public function clearUserFeatures($productId) {
		$sql = "DELETE FROM tp_product_to_feature WHERE client_id = {$this->clientId} AND product_id = {$productId} AND shop_id = {$this->shopId}";
		$this->db->query($sql);
		
		return true;
	}
	
	public function addProductToOrder($orderId, $productId, $count) {
		
		$orderId = intval($orderId);
		$productId = intval($productId);
		$count = intval($count);
		
		if(!$count) return -1;
		if(!$this->checkAccessToProduct($productId)) return -2;
		if(!$this->checkAccessToOrder($orderId)) return -3;
		
		$product = $this->getProductEditInfo($productId);
		
		$tp_order_items = array(
			'client_id'=>$this->clientId,
			'shop_id'=>$this->shopId,
			'order_id'=>$orderId,
			'product_id'=>$productId,
			'product_name'=>$product['title'],
			'product_cost'=>$product['price'],
			'add_date'=>time(),
			'count'=>$count
		);
		
		$this->db->autoupdate()->table('tp_order_items')->data(array($tp_order_items));
		$this->db->execute();
		
		$this->addOrderEvent($orderId, 3, '');
		$this->notice('order.edit', $this->getOrderInfo($orderId));
		
		return $this->db->insert_id;
	}
	
	public function insertUserFeature($id = false, $name, $type) {
		$query = array(
				'client_id'=>$this->clientId,
				'shop_id'=>$this->shopId,
				'name'=>mysql::str($name),
				'type'=>intval($type)
		);
		
		if($id) {
			$query['id'] = $id;
			$this->db->autoupdate()->table('tp_product_feature')->data(array($query))->primary($id);
		} else {
			$this->db->autoupdate()->table('tp_product_feature')->data(array($query));
		}
		
		
		$this->db->execute();
		
		return ($id)? $id : $this->db->insert_id;
	}
	
	public function updateuserFeature($productId, $feature, $fid = false) {
	    $fid = intval($fid);
	   
	    if(!isset($feature['value']))  { //empty($feature['value'])
	       if($fid) {
	           $sql = "DELETE FROM tp_product_to_feature WHERE product_id = {$productId} AND shop_id = {$this->shopId} AND client_id = {$this->clientId} AND feature_id = {$fid}";
	           $this->db->query($sql);
	           
	           return false;
	       } else {
	           return false;
	       }
	    }
	    
		if(!$fid) {
			$sql = "SELECT id, type FROM tp_product_feature WHERE client_id = {$this->clientId} AND shop_id = {$this->shopId} AND name = '".mysql::str($feature['name'])."' LIMIT 1";
			$this->db->query($sql);
			$this->db->get_rows(1);
			
			if(empty($this->db->rows)) {
				$featureId = $this->insertUserFeature(false, $feature['name'], $feature['type']);
			} else {
				if($this->db->rows['type'] == intval($feature['type'])) {
					$featureId = intval($this->db->rows['id']);
				} else {
					$featureId = $this->insertUserFeature(intval($this->db->rows['id']), $feature['name'], $feature['type']);
				}
			}
		} else {
			$featureId = $fid;
			$featureData = array(
			    'client_id'=>$this->clientId,
			    'shop_id'=>$this->shopId,
			    'id'=>$featureId,
			    'name'=>mysql::str($feature['name']),
			    'type'=>intval($feature['type'])
			);
			
			if(!empty($feature['order'])) {
			    $featureData['order'] = intval($feature['order']);
			}
			
			$this->db->autoupdate()->table('tp_product_feature')->data(array($featureData))->primary('shop_id','client_id','id');
			$this->db->execute();
		}
		
		$this->appendFeatureToProduct($featureId, $productId, $feature['value'], $feature['unit']);
	}
	
	public function appendFeatureToProduct($featureId, $productId, $value, $unit) {
	    $tp_product_to_feature = array(
	        'client_id'=>$this->clientId,
	        'shop_id'=>$this->shopId,
	        'product_id'=>$productId,
	        'feature_id'=>$featureId,
	        'variant_id'=>0,
	        'value'=>mysql::str($value),
	        'unit_id'=>intval($unit)
	    );

	    $this->db->autoupdate()->table('tp_product_to_feature')->data(array($tp_product_to_feature))->primary('product_id','shop_id','client_id','feature_id');
	    $this->db->execute();
	}
	
	public function processUserFeatures($productId, $feature) {
		$sql = "SELECT * FROM tp_product_to_feature WHERE client_id = {$this->clientId} AND shop_id = {$this->shopId} AND product_id = {$productId}";
		$this->db->query($sql);
		$this->db->get_rows(false, 'feature_id');
		$existFeatures = $this->db->rows;
		
		if(!empty($existFeatures) && !empty($feature)) {
			foreach($feature as $item) {
				if(empty($item['fid']) || empty($existFeatures[$item['fid']])) {
				    if(!isset($item['value'])) continue;
					$fid = $this->insertUserFeature(false,$item['name'], $item['type']);
					$this->appendFeatureToProduct($fid, $productId, $item['value'], $item['unit']);
				} else {
					$this->updateuserFeature($productId, $item, intval($item['fid']));
					unset($existFeatures[$item['fid']]);
				}
			}
			
			if(count($existFeatures) > 0) {
				$forDelete = array();
				foreach($existFeatures as $item) {
					$forDelete[] = $item['feature_id'];
				}
				
				if(count($forDelete) > 0) {
					$sql = "DELETE FROM tp_product_to_feature WHERE client_id = {$this->clientId} AND shop_id = {$this->shopId} AND product_id = {$productId} AND feature_id IN(".implode(',',$forDelete).")";
					$this->db->query($sql);
				}
			}
			
		} else if(empty($feature)) {
			$sql = "DELETE FROM tp_product_to_feature WHERE client_id = {$this->clientId} AND shop_id = {$this->shopId} AND product_id = {$productId}";
			$this->db->query($sql);
		} else if(empty($existFeatures)){
			foreach($feature as $item) {
				if(!isset($item['value'])) continue;
				$fid = $this->insertUserFeature(false,$item['name'], $item['type']);
				$this->appendFeatureToProduct($fid, $productId, $item['value'], $item['unit']);
			}
		}
	}
	
	public function addProductSales($productId, $type, $start_date, $end_date, $new_price, $procent, $quantity) {
		if($type == 1) {
			$tp_product_sales = array(
				'client_id'=>$this->clientId,
				'product_id'=>$productId,
				'shop_id'=>$this->shopId,
				'type_id'=>$type,
				'new_price'=>$new_price,
				'start_date'=>$start_date,
				'end_date'=>$end_date
			);
		} else {
			$tp_product_sales = array(
				'client_id'=>$this->clientId,
				'product_id'=>$productId,
				'shop_id'=>$this->shopId,
				'type_id'=>$type,
				'new_price'=>$new_price,
				'start_date'=>$start_date,
				'end_date'=>$end_date,
				'wholesale_procent'=>$procent,
				'wholesale_quantity'=>$quantity
			);
		}
		
		$this->db->autoupdate()->table('tp_product_sales')->data(array($tp_product_sales));
		$this->db->execute();
		
		return $this->db->insert_id;
	}
	
	public function getProductFormSupport($groupId) {
		$groupId = intval($groupId);
		$info = array();
		$info['stores'] = $this->getStoresList('class');
		$info['category_id'] = 0;
		
		if($groupId) {
			$sql = "SELECT product_category FROM tp_product_group WHERE client_id = {$this->clientId} AND shop_id = {$this->shopId} AND group_id = {$groupId}";
			$this->db->query($sql);
			$info['category_id'] =  $this->db->get_field();
		}
		
		
		return $info;
	}
	
	public function getProductEditInfo($product_id) {
		$product_id = intval($product_id);
		if(!$product_id) return false;
		
		$sql = "SELECT
						get_rewrite(p.rewrite_id) as rewrite,
						p.product_id as id,
						p.*
				FROM
						tp_product p
				WHERE
						p.shop_id = {$this->shopId} AND
						p.client_id = {$this->clientId} AND
						p.product_id = {$product_id} ";
		
		$this->db->query($sql);
		$this->db->add_fields_deform(array('sales_start', 'sales_end', 'add_date', 'sales'));
		$this->db->add_fields_func(array('h_date','h_date','h_date', 'intval'));
		
		$this->db->get_rows(1);
		$info = $this->db->rows;
		
		if(!$info || !is_array($info)) return false;
		
		$info['extended_groups'] = $this->getProductExtendedGroups($product_id);
		$info['images'] = $this->getProductImages($product_id);
		$info['yandex_features'] = $this->getProductYMfeatures($product_id);
		$info['user_features'] = $this->getProductFeatures($product_id);
		$info['feeds'] = $this->getProductFeeds($product_id);
		$info['avaliable'] = $this->getProductAvaliable($product_id);
		$info['dimensions'] = $this->getProductDimensions($product_id);
		$info['stores'] = $this->getStoresList('class');
		$info['modifications'] = $this->getProductModification($product_id);
		
		
		
		return $info;
		
	} 
	
	public function getProductModification($product_id) {
	    $sql = "SELECT p.* FROM tp_product p WHERE p.shop_id = {$this->shopId} AND p.client_id = {$this->clientId} AND p.parent_id = {$product_id} ORDER BY p.position";
	    $this->db->query($sql);
	    $this->db->get_rows();
	    $list = $this->db->rows;
	    
	    return $list;
	}
	
	public function getModificationById($product_id) {
	    $sql = "SELECT p.* FROM tp_product p WHERE p.shop_id = {$this->shopId} AND p.client_id = {$this->clientId} AND p.product_id = {$product_id}";
	    $this->db->query($sql);
	    $this->db->get_rows(1);
	    $info = $this->db->rows;
	    $info['yandex_features'] = $this->getProductYMfeatures($product_id);
	    $info['dimensions'] = $this->getProductDimensions($product_id);
	    
	    return $info;
	}
	
	public function editModification($id, $parentId, $data) {
	    $id = intval($id);
	    $parentId = intval($parentId);
	    
	    $query = array(
	        'client_id'=>$this->clientId,
	        'shop_id'=>$this->shopId,
	        'article'=>mysql::str($data['article']),
	        'title'=>mysql::str($data['name']),
	        'description'=>mysql::str($data['description']),
	        'status_id'=>1,
	        'price_type'=>intval($data['price_type']),
	        'pack_size'=>floatval($data['pakage']),
	        'pack_unit'=>intval($data['unit']),
	        'min_order'=>intval($data['minorder']),
	        'price'=>floatval($data['price']),
	        'price2'=>floatval($data['price2']),
	        'price3'=>floatval($data['price3']),
	        'price4'=>floatval($data['price4']),
	        'price5'=>floatval($data['price5']),
	        'parent_id'=>$parentId
	    );
	    
	    if($id) {
	        $query['product_id'] = $id;
	    } else {
	        $query['status_id'] = 3;
	    }
	    
	    
	    $this->db->autoupdate()->table('tp_product')->data(array($query))->primary('client_id','shop_id','product_id');
	    $this->db->execute();
	    if(!$id) $id = $this->db->insert_id;
	    
	    $query['product_id'] = $id;

	    return $query;
	}
	
	private function updateModificationFromProduct($id, $parentId, $data) {
	    $query = array(
	        'client_id'=>$this->clientId,
	        'shop_id'=>$this->shopId,
	        'product_id'=>$id,
	        'parent_id'=>$parentId,
	        'status_id'=>1,
	        'price'=>$data['price1'],
	        'price2'=>$data['price2'],
	        'price3'=>$data['price3'],
	        'price4'=>$data['price4'],
	        'price5'=>$data['price5'],
	        'article'=>$data['article'],
	        'title'=>$data['title'],
	        'position'=>$data['position'],
	    );
	    
	    $this->db->autoupdate()->table('tp_product')->data(array($query))->primary('client_id','shop_id','product_id');
	    $this->db->execute();
	    
	    return true;
	}
	
	public function removeModification($id) {
	    $id = intval($id);
	    $sql = "DELETE FROM tp_product WHERE shop_id = {$this->shopId} AND client_id = {$this->clientId} AND product_id = {$id}";
	    $this->db->query($sql);
	    
	    return true;
	}
	
	public function getProductDimensions($product_id) {
		$sql = "SELECT 
					d.*,
					(SELECT du.name FROM tp_product_feature_units as du WHERE du.id = d.width_unit) as width_unit_name,
					(SELECT du.name FROM tp_product_feature_units as du WHERE du.id = d.height_unit) as height_unit_name,
					(SELECT du.name FROM tp_product_feature_units as du WHERE du.id = d.depth_unit) as depth_unit_name,
					(SELECT du.name FROM tp_product_feature_units as du WHERE du.id = d.weight_unit) as weight_unit_name
				FROM 
					tp_product_dimensions as d

				WHERE 
					d.shop_id = {$this->shopId} AND
					d.client_id = {$this->clientId} AND
					d.product_id = {$product_id}";
		$this->db->query($sql);
		$this->db->get_rows(1);
		
		if(!$this->db->rows || !isset($this->db->rows['width'])) return false;
		
		return $this->db->rows;
	}

	public function getProductYMfeatures($product_id) {
		$sql = "SELECT 
					* 
				FROM 
					tp_product_to_ym_feature 
				WHERE 
					shop_id = {$this->shopId} AND
					client_id = {$this->clientId} AND
					product_id = {$product_id}";
		
		$this->db->query($sql);
		$this->db->get_rows(false,'id');
		
		return $this->db->rows;
	}
	
	public function getProductFeatures($product_id) {
		$product_id = intval($product_id);
		if(!$product_id) return false;
		
		$sql = "SELECT
					pf.variant_id,
					pf.value,
					pf.unit_id,
					f.name,
					f.type,
					f.id
					
					
				FROM
					tp_product_to_feature as pf
						LEFT JOIN tp_product_feature as f ON f.id = pf.feature_id 
				WHERE
					pf.shop_id = {$this->shopId} AND
					pf.client_id = {$this->clientId} AND
					pf.product_id = {$product_id}";
		
		$this->db->query($sql);
		$this->db->get_rows();
		
		return $this->db->rows;
	}
	
	public function getProductFeeds($product_id, $all = false) {
		$product_id = intval($product_id);
		if(!$product_id) return false;
		
		if($all) {
			$sql = "SELECT 
						f.name, f.id,
						IF((SELECT COUNT(*) FROM tp_product_to_feed as pf WHERE pf.feed_id = f.id AND pf.shop_id = {$this->shopId} AND pf.client_id = {$this->clientId} AND pf.product_id = {$product_id}) > 0, 1,0) as feed_use
					FROM 
						tp_product_feeds as f
					WHERE 
						1";	
		} else {
			$sql = "SELECT
						pf.feed_id,
						pf.product_id,
						f.name as feed_name
					FROM
						tp_product_to_feed as pf
							LEFT JOIN tp_product_feeds as f ON f.id = pf.feed_id
					WHERE
						pf.shop_id = {$this->shopId} AND
						pf.client_id = {$this->clientId} AND
						pf.product_id = {$product_id} ";
		}
		
		$this->db->query($sql);
		$this->db->get_rows();
		
		return $this->db->rows;
	}
	
	public function getFeeds() {
		$sql = "SELECT * FROM tp_product_feeds WHERE 1";
		$this->db->query($sql);
		$this->db->get_rows();
		
		return $this->db->rows;
	}
	
	public function getProductExtendedGroups($product_id) {
		$product_id = intval($product_id);
		if(!$product_id) return false;
		
		$sql = "SELECT
					pg.group_id,
					g.name
				FROM
					tp_product_to_group as pg
						LEFT JOIN tp_product_group as g ON g.group_id = pg.group_id
				WHERE
					pg.shop_id = {$this->shopId} AND
					pg.client_id = {$this->clientId} AND
					pg.product_id = {$product_id} AND
		
					g.shop_id = {$this->shopId} AND
					g.client_id = {$this->clientId}";
		
		$this->db->query($sql);
		$this->db->get_rows();
		
		return $this->db->rows;
	}
	
	public function getProductAvaliable($product_id) {
		$sql = "SELECT 
					avaliable_type as type,
					avaliable_pending as  pending
				FROM 
					tp_product 
				WHERE 
					shop_id = {$this->shopId} AND
					client_id = {$this->clientId} AND
					product_id = {$product_id} ";
		$this->db->query($sql);
		$this->db->get_rows(1);
		
		$avaliable = $this->db->rows;
		
		if(!$avaliable || !is_array($avaliable) || !isset($avaliable['type'])) {
			return false;
		}
		
		#TODO: Добавить возможность выбирать из нескольких складов
		$sql = "SELECT  
					store_id, 
					quantity 
				FROM 
					tp_product_store 
				WHERE
					shop_id = {$this->shopId} AND
					client_id = {$this->clientId} AND
					product_id = {$product_id} ";
		$this->db->query($sql);
		$this->db->get_rows(1);
		$avaliable['store'] = $this->db->rows;
		
		return $avaliable;
	}
	
	public function getProductImages($product_id) {
		$product_id = intval($product_id);
		if(!$product_id) return false;
		
		$sql = "SELECT 
					* 
				FROM 
					tp_product_img 
				WHERE 
					shop_id = {$this->shopId} AND 
					client_id = {$this->clientId} AND 
					product_id = {$product_id} 
					AND url1 <> '' 
				ORDER BY `order` ASC";
		
		$this->db->query($sql);
		$this->db->get_rows();
		$images = $this->db->rows;
		$return = array();
		
		foreach ($images as $item) {
			$return[] = array(
				array('name'=>$item['url1'], 'order'=>$item['order'], 'img_id'=>$item['img_id']),
				array('name'=>$item['url2'], 'order'=>$item['order'], 'img_id'=>$item['img_id']),
				array('name'=>$item['url3'], 'order'=>$item['order'], 'img_id'=>$item['img_id']),
			    array('name'=>$item['url4'], 'order'=>$item['order'], 'img_id'=>$item['img_id']),
			);
		}
		
		
		return $return;
	}
	
	public function reorderCategory($categoryOrderData) {
		if(!is_array($categoryOrderData) || !count($categoryOrderData)) return false;

		foreach($categoryOrderData as $item) {
			$groupId = intval($item['group_id']);
			$order = intval($item['order']);
			
			$sql = "UPDATE tp_product_group SET `order` = {$order} WHERE client_id = {$this->clientId} AND shop_id = {$this->shopId} AND group_id = {$groupId}";
			$this->db->query($sql);
		}
		
		return true;
	}
	
	public function getAdminCatalogList($instance, $start = 0, $limit = 20, $sortBy = 'date', $sortType = 'DESC', $filters = false) {		
		$result = array();
		$rowsCount = 0;
		$groupId = (isset($filters['group_id']))? intval($filters['group_id']) : 0;
		
		$sql = "SELECT 
					SQL_CALC_FOUND_ROWS
					DISTINCT(p.product_id) as id,
					'product' row_type,
					get_rewrite(p.rewrite_id) rewrite,
					p.description,
					p.article,
					p.title title,
					p.title name,
					p.update_date update_date,
					p.price,
					IFNULL(p.article, '#000000') as article,
					(SELECT GROUP_CONCAT(pf.name) FROM tp_product_to_feed as ptf LEFT JOIN tp_product_feeds as pf ON pf.id = ptf.feed_id WHERE  ptf.shop_id = {$this->shopId} AND ptf.client_id = {$this->clientId} AND ptf.product_id = p.product_id) as public_site, 
					IFNULL((SELECT y.title FROM tp_product_group as g LEFT JOIN ym_categories as y ON y.id = g.product_category WHERE g.group_id = p.group_id AND g.shop_id = {$this->shopId} AND g.client_id = {$this->clientId}), '-') as product_type_name,
					(SELECT COUNT(*) FROM tp_product as p_sub WHERE p_sub.shop_id = {$this->shopId} AND p_sub.client_id = {$this->clientId} AND p_sub.parent_id = p.product_id) as mod_count,
					ps.name as status_name,
					IF(p.sales, p.price - p.price_new,'-') as sale,
					IFNULL(
							(
								SELECT 
									img.url1 
								FROM 
									tp_product_img as img 
								WHERE 
									img.product_id = p.product_id AND
									img.client_id = p.client_id AND
									img.shop_id = p.shop_id  AND
									img.url1 <> '' 
								ORDER BY `order` ASC LIMIT 1
							), '/templates/admin/green/images/no_image.png') as img
					
				FROM
					tp_product as p
						LEFT JOIN tp_product_status as ps ON ps.id = p.status_id
						
				WHERE
					p.shop_id = {$this->shopId} AND
					p.client_id = {$this->clientId} ";
					
						
	
		/* add filters */
        $filtered = false;
		
		if(isset($filters['search']) && $filters['search']) {
			$query = mysql::str(mb_strtolower($filters['search']));
			$sql .= " AND (LOWER(p.title) LIKE '%{$query}%' OR LOWER(p.full_description) LIKE '%{$query}%' OR p.article = '{$query}' OR LOWER(p.description) LIKE '%{$query}%' )";
			$filtered = true;
		}

		if(!empty($filters['feed']) && intval($filters['feed'])) {
		    $feedId = intval($filters['feed']);
		    $sql .= " AND EXISTS (SELECT pf2.product_id FROM tp_product_to_feed as pf2 WHERE pf2.shop_id = {$this->shopId} AND pf2.client_id = {$this->clientId} AND pf2.product_id = p.product_id AND pf2.feed_id = {$feedId}) ";
		    $filtered = true;
		}
		
		if(!empty($filters['avaliable']) && intval($filters['avaliable'])) {
		    $avaliable = $filters['avaliable'];
		    $sql .= " AND p.avaliable_type =  {$avaliable}";
		    $filtered = true;
		}
		
		if(!empty($filters['sales']) && intval($filters['sales'])) {
		    $sales = intval($filters['sales']);
		    
		    if($sales == 10) {
		        $sql .= " AND p.sales <> 0";
		    } else {
		        $sql .= " AND p.sales =  {$sales}";
		    }
		   
		    $filtered = true;
		}
		
		if(!empty($filters['vendor']) && intval($filters['vendor'])) {
		    $vendor = intval($filters['vendor']);
		    $sql .= " AND p.vendor_id =  {$vendor}";

		    $filtered = true;
		}
		
		if(!empty($filters['category']) && intval($filters['category'])) {
		    $category = intval($filters['category']);
		    $sql .= " AND p.category_id =  {$category}";
		
		    $filtered = true;
		}
		
		
		if(!$filtered) {
		    $sql .=  "AND (p.group_id = {$groupId} OR p.product_id IN(SELECT pg.product_id FROM tp_product_to_group as pg WHERE pg.group_id = {$groupId} AND pg.shop_id = {$this->shopId} AND pg.client_id = {$this->clientId}))";
		
		    $result = $this->getGroupsList($groupId);
		    // $rowsCount += $this->db->found_rows();
		}
		
		$sql .=  " AND parent_id = 0";
		
		$sql .= " ORDER BY $sortBy $sortType LIMIT $start,$limit";
		
		//echo $sql;
		$this->db->query($sql);
		$this->db->add_fields_deform(array('update_date'));
		$this->db->add_fields_func(array('h_date'));
		$products = $this->db->get_rows();
		$products = (is_array($products))? $products : array();
		$result = array_merge($result, $products);
		$rowsCount += $this->db->found_rows();

		//echo $sql;
		
	
		$this->db->cacheFoundRows = $rowsCount;
		
		return $result;
	}
	
	public function getAdminCatalogCrumbs($groupId = 0, $productId = 0) {
		$groupId = intval($groupId);
		$productId = intval($productId);
		
		if(!$groupId && !$productId) return false;
		
		
		if($productId) {
			$sql = "SELECT group_id as id, title as name FROM tp_product WHERE client_id = {$this->clientId} AND shop_id = {$this->shopId} AND product_id = {$productId}";
			$this->db->query($sql);
			$this->db->get_rows(1);
			return $this->db->rows;
		} else {
			return $this->getGroupsTreeUp($groupId);
		}
	}
	
	public function getVendorsList($instance, $start = 0, $limit = 20, $sortBy = 'o.order_id', $sortType = 'DESC', $filters = false) {
		$sql = "SELECT SQL_CALC_FOUND_ROWS
					v.*,
					(SELECT COUNT(*) FROM tp_product as p WHERE p.shop_id = {$this->shopId} AND p.client_id = {$this->clientId} AND p.vendor_id = v.id) as prod_count
				FROM
					tp_vendors as v
				WHERE
					v.shop_id = {$this->shopId} AND
					v.client_id = {$this->clientId}";
		
		if(isset($filters['id']) && intval($filters['id'])) {
			$sql .= ' AND v.id = '.intval($filters['id']);
		}
		
		$sql .= " ORDER BY $sortBy $sortType LIMIT $start,$limit";
		$this->db->query($sql);
		
		if(isset($filters['id']) && intval($filters['id'])) {
			$this->db->get_rows(1);
		} else {
			$this->db->get_rows();
		}
		

		return $this->db->rows;
	}
	
	public function getOrdersList($instance, $start = 0, $limit = 20, $sortBy = 'o.order_id', $sortType = 'DESC', $filters = false) {
		$sql = "SELECT SQL_CALC_FOUND_ROWS
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
					
					(SELECT cp.phone FROM crm_contacts_phones as cp WHERE cp.client_id = {$this->clientId} AND cp.contact_id = o.contact_id LIMIT 1) as client_phone,
					(SELECT ce.email FROM crm_contacts_email as ce WHERE ce.client_id = {$this->clientId} AND contact_id =  o.contact_id LIMIT 1) as client_email,
					(SELECT GROUP_CONCAT((CONCAT(cod.count, ' x ', cod.product_name)) SEPARATOR ', ') FROM tp_order_items as cod WHERE cod.shop_id = {$this->shopId} AND cod.client_id = {$this->clientId} AND cod.order_id = o.order_id) as order_short_info,
					
					s.name order_status_name,
					s.color color,
					p.name order_pay_name,
					d.name delivery_name		
				FROM
					tp_order as o 
						LEFT JOIN crm_contacts c ON o.contact_id = c.contact_id AND o.client_id = c.client_id
						LEFT JOIN tp_order_status s ON s.id = o.status_id LEFT JOIN tp_order_pay_status p ON p.id = o.status_pay_id
						LEFT JOIN tp_delivery_types d ON d.id = o.delivery_type_id
				WHERE
					o.shop_id = {$this->shopId} AND
					o.client_id = {$this->clientId}";
		
		
		if(isset($filters['search']) && $filters['search']) {
			$query = mysql::str(mb_strtolower($filters['search']));
			$sql .= " AND (LOWER(c.name) LIKE '%{$query}%' OR LOWER(s.name) LIKE '%{$query}%' OR d.name = '{$query}')";
		} else {
			if(isset($filters['status']) && intval($filters['status'])) {
				$sql .= " AND o.status_id = ".intval($filters['status']);
			}
			
			if(isset($filters['delivery']) && intval($filters['delivery'])) {
				$sql .= " AND  o.delivery_type_id = ".intval($filters['delivery']);
			}
		}
		
		if(isset($filters['contact_id']) && intval($filters['contact_id'])) {
		    $sql .= " AND o.contact_id = ".intval($filters['contact_id']);
		}
		
		$sql .= " ORDER BY $sortBy $sortType LIMIT $start,$limit";
		
		
		
		
		
		//echo $sql;
		$this->db->query($sql);
		$this->db->add_fields_deform(array('create_date','delivery_date'));
		$this->db->add_fields_func(array('h_date','h_date'));
		$this->db->get_rows();
		
		
		

		return $this->db->rows;
	}
	
	public static function getOrderShortInfo($orderId) {
		$sid = self::$instance['shopId'];
		$cid = self::$instance['clientId'];
		$sql = "SELECT product_name, `count`,  FROM tp_order_items WHERE shop_id = {$sid} AND client_id = {$cid} AND order_id {$orderId}";
		
		core::$instance->db->query($sql);
		core::$instance->db->get_rows();
		$list = core::$instance->db->rows;
		
		if(!$list || empty($list)) return 'Нет товаров';
		
		$info = '';
		
		foreach($list as $item) {
			$info .= " {$item['count']} x {$item['product_name']},";
		}
		
		return substr($info, 0, -1);
	} 
	
	public function getStoresList($instance, $start = 0, $limit = 20, $sortBy = 'o.order_id', $sortType = 'DESC', $filters = false) {
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS ";
		
		if($instance == 'class') {
			$sql .= "	s.store_id as id, s.name FROM tp_stores as s ";
		} else {
			$sql .= " s.store_id as id, s.name,	s.index, s.address as store_address, s.memo, ss.name as status_name, s.status as status_id, 
						(SELECT SUM(sp1.quantity) FROM tp_product_store as sp1 WHERE sp1.client_id = s.client_id AND sp1.shop_id = s.shop_id AND sp1.store_id = s.store_id) as full_quantity,
						(SELECT COUNT(sp2.quantity) FROM tp_product_store as sp2 WHERE sp2.client_id = s.client_id AND sp2.shop_id = s.shop_id AND sp2.store_id = s.store_id) as uniq_quantity 
					FROM 
						tp_stores as s
							LEFT JOIN tp_stores_status as ss ON ss.id = s.status ";
		}
		
		$sql .= "WHERE 
						s.client_id = {$this->clientId} AND
						s.shop_id = {$this->shopId} ";
		
				
		$this->db->query($sql);
		$this->db->get_rows();	

		return $this->db->rows;
	}
	
	public function getClientsList($instance, $start = 0, $limit = 20, $sortBy = 'o.order_id', $sortType = 'DESC', $filters = false) {	
		$sql = "SELECT
					SQL_CALC_FOUND_ROWS
					c.name, c.surname, c.lastname,  c.contact_id as id, c.source_id, c.date, 
					cs.name as source_name,
					IFNULL(c.comment,'') as comment,
					(SELECT CONCAT(u.name_last, ' ', u.name_first) FROM a_users as u WHERE u.client_id = {$this->clientId} AND u.user_id =  c.responsible_user_id) as resp_user, 
					(SELECT ce.email FROM crm_contacts_email as ce WHERE ce.client_id = c.client_id AND ce.contact_id = c.contact_id LIMIT 1) as email,
					(SELECT cp.phone FROM crm_contacts_phones as cp WHERE cp.client_id = c.client_id AND cp.contact_id = c.contact_id LIMIT 1) as phone,
					(SELECT COUNT(*) FROM tp_order as o WHERE o.client_id = c.client_id AND o.shop_id = {$this->shopId} AND o.contact_id = c.contact_id) as orders,
				
					
					
					(SELECT SUM( oi.product_cost * oi.count ) FROM tp_order_items as oi LEFT JOIN tp_order as o2 ON oi.order_id = o2.order_id WHERE oi.client_id = c.client_id AND oi.shop_id = {$this->shopId} AND o2.client_id = c.client_id AND o2.shop_id = {$this->shopId} AND o2.contact_id = c.contact_id) as orders_sum

				FROM
					crm_contacts as c
						LEFT JOIN crm_contacts_sources as cs ON cs.source_id = c.source_id AND (cs.client_id = {$this->clientId} OR cs.client_id = 0)
				WHERE
					c.client_id = {$this->clientId} ";
		
		if($this->userInfo['group_id'] == 2) {
		    $sql .= " AND (c.responsible_user_id = {$this->userInfo['user_id']} OR c.responsible_user_id = 0)";
		} 

		
		if(!empty($filters['source_id']) && intval($filters['source_id'])) {
			$sid = intval($filters['source_id']);
			$sql .= " AND c.source_id = {$sid}";
		}
		
		if(!empty($filters['special']) && intval($filters['special'])) {
			$sid = intval($filters['special']);
			
			switch ($sid) {
				case 1:
					$sql .= " AND EXISTS (SELECT o.order_id FROM tp_order as o WHERE o.client_id = c.client_id AND o.shop_id = {$this->shopId} AND o.contact_id = c.contact_id LIMIT 1)";
				break;
					
				case 2:
					$sql .= " AND EXISTS (SELECT cf.id FROM crm_contacts_special_flags as cf WHERE cf.contact_id =c.contact_id AND cf.flag_id = 1 LIMIT 1)";
				break;
				
				case 3:
					$sql .= " AND c.source_id = 8";
				break;
				
				case 4:
					$sql .= " AND EXISTS (SELECT cf.id FROM crm_contacts_special_flags as cf WHERE cf.contact_id =c.contact_id AND cf.flag_id = 5 LIMIT 1)";
				break;
				
				case 5:
					$sql .= " AND EXISTS (SELECT o.order_id FROM tp_order as o WHERE o.client_id = c.client_id AND o.shop_id = {$this->shopId} AND (o.status_id = 1 OR o.status_id = 2)  LIMIT 1)";
				break;
			}
		}
		
		if(!empty($filters['search']) && strlen(trim($filters['search']))>0) {
			$query = preg_replace('/^[|~\`\'\"\:\;\/\?\>\<\*\&\^\%\$\#\-]/i', '', trim($filters['search']));
			$query = mb_strtolower(mysql::str($query));
			$sql .= " AND (LOWER(c.name) LIKE '%{$query}%' OR LOWER(c.comment) LIKE '%{$query}%' ";
			
			$sql .= " "; // добавить поиск номера
			$sql .= " "; // добавить поиск мыла
			
			$sql .= ")";			
		} 
		
		
		$sql .= " ORDER BY $sortBy $sortType LIMIT $start,$limit";
		$this->db->query($sql);
		$this->db->add_fields_deform(array('date', 'email', 'phone', 'orders_sum'));
		$this->db->add_fields_func(array('h_date', 'clearNullResult', 'clearNullResult', 'clearNullResult'));
		$this->db->get_rows();
		
		return $this->db->rows;
	}
		
	public function getOrderInfo($orderId) {
		$sql = "SELECT 
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
					o.code,
					o.recipient_info,
					get_order_sum(o.client_id, o.order_id, o.shop_id) sum,
					d.name as delivery_type_name,
					pt.name as pay_type,
					ps.name as pay_status_type,
					ps.label as pay_status_label
				FROM
					tp_order as o
						LEFT JOIN tp_order_status s ON s.id = o.status_id LEFT JOIN tp_order_pay_status p ON p.id = o.status_pay_id
						LEFT JOIN tp_delivery_types d ON d.id = o.delivery_type_id
						LEFT JOIN tp_pay_type pt ON pt.id = o.pay_type_id
						LEFT JOIN tp_order_pay_status ps ON ps.id = o.status_pay_id
				WHERE
					o.shop_id = {$this->shopId} AND
					o.client_id = {$this->clientId} AND
					o.order_id = {$orderId}";

		$this->db->query($sql);
		$this->db->add_fields_deform(array('create_date','delivery_date'));
		$this->db->add_fields_func(array('h_date','h_date'));
		$this->db->get_rows(1);

		if(!$this->db->rows) return false;
		
		$info = $this->db->rows;
		$info['items'] = $this->getOrderItems($orderId);
		$info['contact'] = $this->getContactInfo($info['contact_id']);
		
		return $info;
	}
	
	public function getOrderHistory($orderId) {
		$sql = "SELECT
					h.*,
					u.name_first,
					u.name_last,
					e.name as event_name,
					e.event_alias
				FROM
					tp_order_history as h
						LEFT JOIN a_users as u ON u.user_id = h.user_id AND u.client_id = {$this->clientId}
						LEFT JOIN tp_order_history_events as e ON e.id = h.event_id
				WHERE
					h.shop_id = {$this->shopId} AND
					h.client_id = {$this->clientId} AND
					h.order_id = {$orderId} 
					
				ORDER BY
					h.date DESC";
		

		$this->db->query($sql);
		$this->db->add_fields_deform(array('date'));
		$this->db->add_fields_func(array('h_date'));
		$this->db->get_rows();
		$list = $this->db->rows;
		
		return $list;
	}
	
	public function getOrderAllStatuses() {
		$sql = "SELECT COUNT(*) FROM tp_order_status_client WHERE shop_id = {$this->shopId} AND client_id = {$this->clientId}";
		$this->db->query($sql);
		
		if(intval($this->db->get_field()) > 0) {
			$sql = "SELECT 
						os.id, os.name 
					FROM 
						tp_order_status as os 
							LEFT JOIN tp_order_status_client as osc ON os.id = osc.status_id
					WHERE
						(os.client_id = 0 OR os.client_id = {$this->clientId}) AND
						osc.client_id  = {$this->clientId}";
		} else {
			$sql = "SELECT id, name FROM tp_order_status WHERE client_id = 0";
		}
		
		$this->db->query($sql);
		$this->db->get_rows();
	
		return $this->db->rows;
	}
	
	public function getDeliveryTypes() {
		$sql = "SELECT id, name FROM tp_delivery_types WHERE client_id = 0";
		
		$this->db->query($sql);
		$this->db->get_rows();
		
		return $this->db->rows;
	}
	
	public function cancelOrder($orderId) {
		$data = array(
			'client_id'=>$this->clientId,
			'shop_id'=>$this->shopId,
			'order_id'=>intval($orderId),
			'status_id'=>3
		);
		
		$this->db->autoupdate()->table('tp_order')->data(array($data))->primary('client_id','shop_id','order_id');
		$this->db->execute();
		
		$this->addOrderEvent($orderId, $this->getOrderEventByStatus($data['status_id']), '');
		$this->notice('order.canceled', $this->getOrderInfo($orderId));
		
		
		return true;
	}
	
	public function cancelProduct($orderId, $productId) {
		$orderId = intval($orderId);
		$productId = intval($productId);
		
		if(!$orderId || !$productId) return;
		
		$sql = "DELETE FROM tp_order_items WHERE shop_id = {$this->shopId} AND client_id = {$this->clientId} AND order_id = {$orderId} AND product_id = {$productId}";
		$this->db->query($sql);
		
		$this->addOrderEvent($orderId, 8, '');
		$this->notice('order.edit', $this->getOrderInfo($orderId));
		
		return true;
	}
	
	public function getOrderItems($orderId) {
		$sql = "SELECT 
					i.order_item_id,
					0 as product_action_procent,
					'нет' as product_action_type_name,
					i.product_name,
					i.product_cost,
					i.count,
					i.product_id,
					i.count * i.product_cost as row_total,
					(SELECT img.url2 FROM tp_product_img as img  WHERE img.product_id = i.product_id AND img.client_id = {$this->clientId} AND img.shop_id = {$this->shopId} ORDER BY `order` LIMIT 1) as img
				FROM
					tp_order_items as i
		
				WHERE
					i.shop_id = {$this->shopId} AND
					i.client_id = {$this->clientId} AND
					i.order_id = {$orderId}";
		$this->db->query($sql);
		$this->db->filter('row_total', function($field) {
		   return round($field,2);
		});
		$this->db->get_rows();
		
		return $this->db->rows;
	}
	
	public function getContactInfo($contactId) {
		$sql = "SELECT 
					c.*,
					s.name as source_name
				
				FROM 
					crm_contacts as c
						LEFT JOIN crm_contacts_sources as s ON s.source_id = c.source_id
				WHERE
					c.client_id = {$this->clientId} AND
					c.contact_id = {$contactId}";
		
		$this->db->query($sql);
		$this->db->get_rows(1);
		$contact = $this->db->rows;
		if(!$contact) return false;
		
		$contact['email'] = $this->getContactEmail($contactId);
		$contact['phone'] = $this->getContactPhone($contactId);
		$contact['address'] = $this->getUserAddress($contactId);
		$contact['company'] = (intval($contact['company_id']) > 0)? $this->getContactCompany($contact['company_id']) : false;

		return $contact;
	}
	
	public function getContactSources() {
		$sql = "SELECT * FROM crm_contacts_sources WHERE client_id = {$this->clientId} OR client_id = 0";
		$this->db->query($sql);
		$this->db->get_rows();
		
		return $this->db->rows;
	}
	
	public function getContactEmail($contactId) {
		$sql = "SELECT email FROM crm_contacts_email WHERE client_id = {$this->clientId} AND contact_id = {$contactId} LIMIT 1";
		$this->db->query($sql);
		return $this->db->get_field();
	}
	
	public function getContactPhone($contactId) {
		$sql = "SELECT phone FROM crm_contacts_phones WHERE client_id = {$this->clientId} AND contact_id = {$contactId} LIMIT 1";
		$this->db->query($sql);
		return $this->db->get_field();
	}
	
	public function getContactAddress($contactId) {
	    // deprecate
	}
	
	public function getContactCompany($companyId) {
	    $sql = "SELECT * FROM crm_companies WHERE client_id = {$this->clientId} AND company_id = {$companyId} LIMIT 1";
	    $this->db->query($sql);
	    $this->db->get_rows(1);

	    return $this->db->rows;
	}
	
	public function getUserAddress($contactId) {
	    $sql = "SELECT 
	              a.*
	            FROM
	               tp_user as u 
	                   LEFT JOIN tp_user_address as a ON a.user_id = u.user_id AND a.client_id = {$this->clientId} AND a.shop_id = {$this->shopId} 
	            WHERE
	               u.client_id = {$this->clientId} AND 
	               u.shop_id = {$this->shopId} AND
	               u.crm_contact_id = {$contactId}
	            ORDER BY 
	               a.is_main DESC 
	            LIMIT 1";
	    
	    $this->db->query($sql);
	    $this->db->get_rows(1);

	    return $this->db->rows;
	}
	
	public function getGroupListForTree($parentId, $openeditem = false) {
	    $parentId = intval($parentId);
	    $sql = "SELECT 
					g.group_id as id, 
					g.parrent_id as pid, 
					g.name, 
					'html' as type,
					g.hidden,
					(SELECT COUNT(*) FROM tp_product_group as g2 WHERE g2.shop_id = {$this->shopId} AND g2.client_id = {$this->clientId} AND g2.parrent_id = g.group_id) as childs
				FROM 
					tp_product_group g
				WHERE 
					g.shop_id = {$this->shopId} AND
					g.client_id = {$this->clientId} AND
					g.parrent_id = {$parentId}
				ORDER BY 
					g.order ";
	    
	    $this->db->query($sql);
	    $this->db->get_rows();
	    $list = array();
	    
	    foreach($this->db->rows as $item) {
	        $item['isLastNode'] = (intval($item['childs'])>0)? false  : true;
	        $item['children'] = (intval($item['childs'])>0)? array() : false;
	        $list[] = $item;
	    }
	    
	    return $list;
	}
	
	public function getGroupsList($parentId = 0) {
		$parentId = intval($parentId);
		
		//get_count_goods_in_group({$this->clientId}, {$this->shopId}, g.group_id) as items_count,
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS
					'folder' as row_type,
					'#000000' as article,
					'нет' as sale, 
					
					
					(SELECT 
							COUNT(DISTINCT pr.product_id) 
						FROM 
							tp_product as pr 
						WHERE 
							pr.shop_id = {$this->shopId} AND 
							pr.client_id = {$this->clientId} AND 
							(pr.group_id = g.group_id OR pr.product_id IN(SELECT pgr.product_id FROM tp_product_to_group as pgr WHERE pgr.shop_id = {$this->shopId} AND pgr.client_id = {$this->clientId} AND pgr.group_id = g.group_id))
					) as items_count,
					
					g.shop_id,
					g.client_id,
					g.group_id,
					g.group_id as id,
					g.parrent_id,
					g.name,
					g.hidden,
					IF(g.hidden,'Скрыт','Опубликован') as public_site,
					IF(g.product_category,(SELECT ymc.title FROM ym_categories as ymc WHERE id = g.product_category) ,'Не указано') as product_type_name,
					get_rewrite(g.rewrite_id) as rewrite,
					g.image img,
					'-' as update_date,
					'-' as price,
					'-' as status_name
				FROM
					tp_product_group g
				WHERE
					g.shop_id = {$this->shopId} AND
					g.client_id = {$this->clientId} AND
					g.parrent_id = {$parentId}
				ORDER BY
					g.order ";
					
		$this->db->query($sql);
		$this->db->get_rows();

		return $this->db->rows;
	}
	
	public function getGroupsTreeUp($lowerGroupId) {
		$iterationLimit = 10;
		$result = array();
		$current = $lowerGroupId;
		
		for($i=0; $i <= $iterationLimit; $i++) {
			$item = $this->getGroupInfoShort($current);
			if(!$item || !count($item)) break;
			$result[] = $item;
			if($item['parrent_id'] == 0) break;
			$current = $item['parrent_id'];
		}
		
		return array_reverse($result);
	}
	
	public function getGroupInfoShort($groupId) {
		$sql = "SELECT
					group_id as id,
					parrent_id,
					name,
					url_preview as image,
					ext_char1,
					get_rewrite(rewrite_id) as url
				FROM
					tp_product_group
				WHERE
					group_id = {$groupId} AND
					shop_id = {$this->shopId} AND
					client_id = {$this->clientId}";
		
		$this->db->query($sql);
		$this->db->get_rows(1);
		
		return $this->db->rows;
	}
	
	public function getGeoRegions() {
		$sql = "SELECT region_id, title_ru as name FROM geo_regions WHERE 1 ORDER BY name";

		$this->db->query($sql);
		$this->db->get_rows();
		
		return $this->db->rows;
	}
	
	public function getGeoCities($regionId=0) {
		$sql = "SELECT city_id, region_id, title_ru as name FROM geo_cities WHERE ";
		if($regionId) {
			$sql .= 'region_id = '.intval($regionId);
		} else {
			$sql .= ' 1=1';
		}
	
		$sql .= ' ORDER BY name';
		
		
		$this->db->query($sql);
		$this->db->get_rows();
	
		return $this->db->rows;
	}
	
	public function getStoreInfo($storeId) {
		$sql = "SELECT
					s.store_id,
					s.name,
					s.index,
					s.address,
					s.status,
					s.memo,
					s.region_id,
					s.city_id
				FROM
					tp_stores as s
				WHERE
					s.store_id = {$storeId} AND
					s.shop_id = {$this->shopId} AND
					s.client_id = {$this->clientId}";
		
		

		$this->db->query($sql);
		$this->db->get_rows(1);
		
		return $this->db->rows;
	}
	
	public function getGroupInfo($groupId) {
		$sql = "SELECT
					group_id,
					parrent_id,
					name,
					url_big as image_original,
					url_preview as image_preview,
					description,
					meta_keyword,
					meta_title,
					meta_description,
					hidden,
					rewrite_id,
					product_category,
					get_rewrite(rewrite_id) as url,
					ext_text2,
					ext_char1
				FROM
					tp_product_group
				WHERE
					group_id = {$groupId} AND
					shop_id = {$this->shopId} AND
					client_id = {$this->clientId}";
		
		$this->db->query($sql);
		$this->db->get_rows(1);
				
		return $this->db->rows;
	}
	
	public function buildTree($parentId, $data, $primaryKey = 'id', $parentKey = 'parent_id') {
	    $tree = array();
	    foreach($data as $k=>$item) {
	        if($item[$parentKey] == $parentId) {
	            $tree[$item[$primaryKey]] = $item;
	            unset($data[$k]);
	            $tree[$item[$primaryKey]]['childs'] = $this->buildTree($item[$primaryKey], $data, $primaryKey, $parentKey);
	        }
	    }
	    
	    if(empty($tree)) return null;
	    
	    return $tree;
	}
	
	public function getGroupTree() {
		$sql = "SELECT
					group_id as id,
					parrent_id,
					parrent_id as parent_id,
					name,
					url_preview as image,
					get_rewrite(rewrite_id) as url
				FROM
					tp_product_group
				WHERE
					shop_id = {$this->shopId} AND
					client_id = {$this->clientId}
				ORDER BY
					parrent_id, group_id";
	
		
		
		$this->db->query($sql);
		$this->db->get_rows();
		
		
		/*
		$tree = array();
		$index = array();
		
		foreach ($this->db->rows as $item) {
			if($item['parrent_id'] == 0) {
				$tree[$item['id']] = $item;
				$tree[$item['id']]['childs'] = array();
				$index[$item['id']] = &$tree[$item['id']];
			} else {
				if(!isset($index[$item['parrent_id']])) {
					// error
				} else {
					if(!isset($index[$item['parrent_id']]['childs'])) {
						$index[$item['parrent_id']]['childs'] = array();
					}
					$index[$item['parrent_id']]['childs'][$item['id']] = $item;
					$index[$item['id']] = &$index[$item['parrent_id']]['childs'][$item['id']];
				}
			}
		}*/
		
		$tree = $this->buildTree(0, $this->db->rows);
		
		return $tree;
	}
	
	public function getGroupTreePlain($data = false, $level = 1) {	
		if(!$data) {
			$data = $this->getGroupTree();
		}

		$result = array();
		
		foreach($data as $item) {
			$item['level'] = $level;
			$result[] = $item;
			
			if(isset($item['childs']) && count($item['childs']) >0) {
				$result = array_merge($result, $this->getGroupTreePlain($item['childs'], $level+1));
			}
		}
		
		return $result;
	}
	
	public function getProductTreeView($parentId = 0) {
		$parentId = intval($parentId);
		$sql = "SELECT
					g.group_id as id,
					g.name,
					g.parrent_id as pid,
					1 as childs,
					1 as is_group,
					0 as price
				FROM 
					tp_product_group as g
				WHERE
					g.shop_id = {$this->shopId} AND
					g.client_id = {$this->clientId} AND
					g.parrent_id = {$parentId}				
					
				UNION
					SELECT
						p.product_id as id,
						p.title as name,
						p.group_id as pid,
						0 as childs,
						0 as is_group,
						p.price
					FROM 
						tp_product as p
					WHERE
						p.shop_id = {$this->shopId} AND
						p.client_id = {$this->clientId} AND
						p.group_id = {$parentId} AND 
				        p.parent_id = 0";
		$this->db->query($sql);
		$this->db->get_rows();
		
		return $this->db->rows;
	}
	
	public function getProductCategories() {
		$sql = "SELECT
					id,
					title as name
				FROM
					ym_categories
				WHERE
					custom = 1 AND
					shop_id = {$this->shopId}
				ORDER BY
					title";
	
		$this->db->query($sql);
		$this->db->get_rows();
	
		return $this->db->rows;
	}
	
	public function getYandexProductCategories() {
		$sql = "SELECT
					id,
					title as name
				FROM
					ym_categories
				WHERE
					is_last = 1 AND
					filtered = 1 AND
					custom = 0
				ORDER BY
					title";
	
		$this->db->query($sql);
		$this->db->get_rows();

		return $this->db->rows;
	}

	public function getProductCategoriesAll() {
		$result = array();
		
		$data = $this->getProductCategories();
		//print_r($data);
		
		if(count($data) > 0) {
			$result[] = array('level'=>1, 'id'=>-1, 'name'=>'Мои категории продуктов');
			
			foreach($data as $item) {
				$item['level'] = 2;
				$result[] = $item;
			}
		}
		

		$data = $this->getYandexProductCategories();
		
		
		$result[] = array('level'=>1, 'id'=>-1, 'name'=>'Категории Yandex.Market');

		foreach($data as $item) {
			$item['level'] = 2;
			$result[] = $item;
		}
		
		return $result;
	}

	public function getVendorsListShort() {
		$sql = "SELECT
					id,
					name
				FROM
					tp_vendors
				WHERE
					shop_id = {$this->shopId} AND
					client_id = {$this->clientId}
				ORDER BY
					name";
		
		$this->db->query($sql);
		$this->db->get_rows();
		
		return $this->db->rows;
	}

	public function getFeatureUnits() {
		$sql = "SELECT id, parent_id, name FROM tp_product_feature_units ORDER BY parent_id, id";
		$this->db->query($sql);
		$this->db->get_rows();

		$list = array();
		foreach($this->db->rows as $item) {
			if($item['parent_id'] == 0) {
				$list[$item['id']] = $item;
				$list[$item['id']]['list'] = array();
			} else {
				$list[$item['parent_id']]['list'][] = $item;
			}
		}
		
		return $list;
	}

	public function getYandexFeatureSet($categoryId, $productId=false) {
		$categoryId = intval($categoryId);
		$productId = intval($productId);
		if(!$categoryId) return false;
		
		
		$sql = "SELECT * FROM ym_features WHERE cat_id = {$categoryId}";
		$this->db->query($sql);
		$this->db->get_rows(false,'id');
		$features = $this->db->rows;
		
		if(!$features || !is_array($features) || !count($features)) return false;
		
		foreach($features as $k=>$item) {
			if($item['type'] == 3 || $item['type'] == 2) {
				$sql = "SELECT * FROM ym_features_variant WHERE feature_id = {$item['id']}";
				$this->db->query($sql);
				$this->db->get_rows();
				$features[$k]['list_variant'] = $this->db->rows;
			}
		}
		
		if($productId) {
			$sql = "SELECT category_id FROM tp_product WHERE shop_id={$this->shopId} AND client_id = {$this->clientId} AND product_id = {$productId}";
			$this->db->query($sql);
			if($this->db->get_field() == $categoryId) {
				$sql = "SELECT * FROM tp_product_to_ym_feature WHERE shop_id={$this->shopId} AND client_id = {$this->clientId} AND product_id = {$productId}";
				$this->db->query($sql);
				$this->db->get_rows();
					
				foreach($this->db->rows as $item) {
					$features[$item['feature_id']]['value'] = $item['value'];
					$features[$item['feature_id']]['variant_id'] = $item['variant_id'];
				}
			}
		}
		
		return $features;
	}
	
	public function saveVendor($id, $data) {
		$id = intval($id);
		
		if(!isset($data['image_preview'])) $data['image_preview'] = '';
		if(!isset($data['image_original'])) $data['image_original'] = '';
		if(!isset($data['text_image1'])) $data['text_image1'] = '';
		if(!isset($data['text_image2'])) $data['image_original'] = '';
		if(!isset($data['sub_description'])) $data['sub_description'] = '';
		if(!isset($data['description'])) $data['description'] = '';
		
		if(empty($data['name'])) return 'Обязательно укажите название производителя.';	
		
		$data = array(
			'name'=>mysql::str($data['name']),
		    'description'=>mysql::str($data['description']),
		    'sub_description'=>mysql::str($data['sub_description']),
			'image_preview'=>mysql::str($data['image_preview']),
			'image_original'=>mysql::str($data['image_original']),
		    'text_image1'=>mysql::str($data['text_image1']),
		    'text_image2'=>mysql::str($data['text_image2']),
			'client_id'=>$this->clientId,
			'shop_id'=>$this->shopId
		);
		
		if(!$id) {
			$sql = "SELECT COUNT(*) FROM tp_vendors WHERE name = '".mysql::str($data['name'])."' AND client_id = {$this->clientId} AND shop_id = {$this->shopId}";
			$this->db->query($sql);
			if(intval($this->db->get_field())) {
				return 'Производитель с таким именем уже есть в системе';
			}
		} else {
			$sql = "SELECT COUNT(*) FROM tp_vendors WHERE id = {$id} AND client_id = {$this->clientId} AND shop_id = {$this->shopId}";
			if(!intval($this->db->get_field())) {
				return 'Вам запрещено редактировать эту запись.';
			}
			
			$data['id'] = $id;
		}
		
		$this->db->autoupdate()->table('tp_vendors')->data(array($data))->primary('id');
		$this->db->execute();
		
		return ($id)? $id : $this->db->insert_id;
	}
	
	public function getOrderStatus($orderId) {
		$sql = "SELECT status_id FROM tp_order WHERE client_id = {$this->clientId} AND order_id = {$orderId} AND shop_id = {$this->shopId}";
		$this->db->query($sql);
		
		return $this->db->get_field();
	}
	
	public function addOrderEvent($orderId, $event, $comment= '') {
		$data = array(
			'client_id'=>$this->clientId,
			'shop_id'=>$this->shopId,
			'order_id'=>$orderId,
			'event_id'=>$event,
			'date'=>time(),
			'comment'=>mysql::str($comment),
			'user_id'=>$this->userId
		);
		
		$this->db->autoupdate()->table('tp_order_history')->data(array($data));
		$this->db->execute();
    
		$alias = $this->getOrderEventAlias($data['event_id']);
		$this->eventEmit('order', $alias);
		
		return $alias;
	} 
	
	public function getOrderEventAlias($eventId) {
		$sql = "SELECT event_alias FROM tp_order_history_events WHERE id = {$eventId}";
		$this->db->query($sql);
		
		return $this->db->get_field();
	} 
	
	public function getOrderEventByStatus($statusId) {
		$sql = "SELECT id FROM tp_order_history_events WHERE client_id = 0 AND on_status_id = {$statusId}";
		$this->db->query($sql);
		$eventId = intval($this->db->get_field());
		
		return ($eventId)? $eventId : 2;
	}
	
	public function getStatusNameById($statusId) {
		$sql = "SELECT name FROM tp_order_status WHERE (client_id = 0 OR client_id = {$this->clientId}) AND id = {$statusId}";
		$this->db->query($sql);
		
		return $this->db->get_field();
	}
	
	public function updateOrder($orderId, $info) {
		$orderId = intval($orderId);
		$data = array(
			'order_id'=>intval($orderId),
			'status_id'=>intval($info['status_id']),
			'shop_id'=>$this->shopId,
			'client_id'=>$this->clientId	
		);
		
		if(!$data['order_id'] || !$this->checkAccessToOrder($data['order_id'])) return -1;
		
		if(intval($data['status_id']) != $this->getOrderStatus($data['order_id'])) {
			$eventId = $this->getOrderEventByStatus($data['status_id']);
			$comment = ($eventId == 2)? $this->getStatusNameById($data['status_id']) : '';
			$eventAlias = $this->addOrderEvent($orderId, $eventId, $comment);
			$this->notice("order.{$eventAlias}", $this->getOrderInfo($orderId));
		}
		
		$this->db->autoupdate()->table('tp_order')->data(array($data))->primary('order_id','shop_id', 'client_id');
		$this->db->execute();

		$sql = "SELECT contact_id FROM tp_order WHERE client_id = {$this->clientId} AND order_id = {$orderId} AND shop_id = {$this->shopId}";
		$this->db->query($sql);
		$contactId = $this->db->get_field();
		 
		if(!$contactId) return -2;
		
		$contactOldInfo = $this->getContactInfo($contactId);
		$contactChanged = false;
		
		// contact
		$contact = array(
			'contact_id'=>$contactId,
			'client_id'=>$this->clientId,
			'source_id'=>intval($info['contact']['source_id']),
			'name'=>mysql::str($info['contact']['fio'])
		);
		
		$this->db->autoupdate()->table('crm_contacts')->data(array($contact))->primary('contact_id', 'client_id');
		$this->db->execute();
		
		if($contactOldInfo['name'] != $contact['name']) {
			$contactChanged = true;
		}
		
		$email = array(
			'contact_id'=>$contactId,
			'client_id'=>$this->clientId,
			'email'=>mysql::str($info['contact']['email'])
		);
		
		$this->db->autoupdate()->table('crm_contacts_email')->data(array($email))->primary('contact_id', 'client_id');
		$this->db->execute();
		
		if($contactOldInfo['email'] != $email['email']) {
			$contactChanged = true;
		}
		
		$phone = array(
			'contact_id'=>$contactId,
			'client_id'=>$this->clientId,
			'phone'=>mysql::str($info['contact']['phone'])
		);
		
		$this->db->autoupdate()->table('crm_contacts_phones')->data(array($phone))->primary('contact_id', 'client_id');
		$this->db->execute();
		
		if($contactOldInfo['phone'] != $phone['phone']) {
			$contactChanged = true;
		}
		
		// address
		
		$address = array(
			'contact_id'=>$contactId,
			'client_id'=>$this->clientId,
			'zip'=>mysql::str($info['address']['zip']),
			'address1'=>mysql::str($info['address']['address1']),
			'address2'=>mysql::str($info['address']['address2']),
		);
		
		$this->db->autoupdate()->table('crm_contacts_address')->data(array($address))->primary('contact_id', 'client_id');
		$this->db->execute();
		
		if($contactChanged) {
			$this->addOrderEvent($data['order_id'], 9, '');
			$this->notice('order.user_change_contact_info', $this->getOrderInfo($orderId));
		}
		
		if($contactOldInfo['address']['zip'] != $address['zip'] || $contactOldInfo['address']['address1'] != $address['address1'] || $contactOldInfo['address']['address2'] != $address['address2']) {
			$this->addOrderEvent($data['order_id'], 3, '');
			$this->notice('order.user_change_address', $this->getOrderInfo($orderId));
		}
		
		return true;
	}
	
	public function makeDate($date, $withoutYear = false) {
		$date = trim($date);
	
		if($withoutYear && strlen($date) < 8) {
			$date .= '/0000';
		}
	
		if(!preg_match("/([0-9]{1,2}).+?([0-9]{1,2}).+?([0-9]{4})/", $date, $match)) {
			return false;
		}
	
		return ($withoutYear)? mktime(0,0,0,intval($match[2]),intval($match[1]),1970) : mktime(0,0,0,intval($match[2]),intval($match[1]),intval($match[3]));
	}
	
	public function eventEmit($object, $event) {
		
	}
	
	public function addCustomGroupFeatures($data) {		
		$query = array(
			'shop_id'=>$this->shopId,
			'custom'=>1,
			'is_last'=>1,
			'filtered'=>1,
			'title'=>mysql::str($data['name']),
			'ycid'=>0,
			'ypid'=>0,
			'yid'=>0,
			'parent_id'=>0
		);
		
		$this->db->autoupdate()->table('ym_categories')->data(array($query));
		$this->db->execute();
		$catId = $this->db->insert_id;
		
	
		
		if(!empty($data['features']) && is_array($data['features']) && count($data['features'])>0) {
			
			
			foreach($data['features'] as $ft) {
				$ym_features = array(
					'cat_id'=>$catId,
					'type'=>intval($ft['type']),
					'name'=>encodestring(preg_replace('/[^а-яА-Яa-zA-Z]/', '', $ft['name'])),
					'title'=>mysql::str($ft['name']),
					'unit'=>(intval($ft['type']) == 4)? $this->getFeatureUnitNameById($ft['unit']) : 0
				);
				
				$this->db->autoupdate()->table('ym_features')->data(array($ym_features));
				$this->db->execute();
				$fid = $this->db->insert_id;
				
				if($ym_features['type'] == 2 && !empty($ft['variant'])) {
					$ym_features_variant = array();
					
					foreach($ft['variant'] as $fv) {
						$ym_features_variant[] = array(
							'feature_id'=>$fid,
							'value'=>mysql::str($fv),
							'yfvid'=>0
						);
					}
					
					$this->db->autoupdate()->table('ym_features_variant')->data($ym_features_variant);
					$this->db->execute();
				}
			}
		} 
		
		return $catId;
	}
	
	public function getFeatureUnitNameById($id) {
		$id = intval($id);
		$sql = "SELECT name FROM tp_product_feature_units WHERE id = {$id}";
		$this->db->query($sql);
		return $this->db->get_field();
	}
	
	public function removeOrder($orderId) {
		$orderId = intval($orderId);
		if(!$this->checkAccessToOrder($orderId)) return false;
		
		$sql = "DELETE FROM tp_order WHERE order_id = {$orderId} AND client_id = {$this->clientId} AND shop_id = {$this->shopId}";
		$this->db->query($sql);
		
		$sql = "DELETE FROM tp_order_items WHERE order_id = {$orderId} AND client_id = {$this->clientId} AND shop_id = {$this->shopId}";
		$this->db->query($sql);
		
		$sql = "DELETE FROM tp_order_history WHERE order_id = {$orderId} AND client_id = {$this->clientId} AND shop_id = {$this->shopId}";
		$this->db->query($sql);
		
		return true;
	}
	
	// private methods	
	private function checkAccessToOrder($orderId) {
		$orderId = intval($orderId);
		if(!$orderId) return false;
	
		$sql = "SELECT COUNT(*) FROM tp_order WHERE order_id = {$orderId} AND shop_id = {$this->shopId} AND client_id = {$this->clientId}";
		$this->db->query($sql);
	
		return (intval($this->db->get_field())> 0)? true : false;
	}
	
	private function checkAccessToProduct($productId) {
		$productId = intval($productId);
		if(!$productId) return false;
		
		$sql = "SELECT COUNT(*) FROM tp_product WHERE product_id = {$productId} AND shop_id = {$this->shopId} AND client_id = {$this->clientId}";
		$this->db->query($sql);
		
		return (intval($this->db->get_field())> 0)? true : false;
	}
	
	private function checkAccessToVendor($vendorId) {
		$vendorId = intval($vendorId);
		if(!$vendorId) return false;
	
		$sql = "SELECT COUNT(*) FROM tp_vendors WHERE shop_id = {$this->shopId} AND client_id = {$this->clientId} AND id = {$vendorId}";
		$this->db->query($sql);
	
		return (intval($this->db->get_field())> 0)? true : false;
	}
	
	private function checkAccessToGroup($groupId) {
		$groupId = intval($groupId);
		if(!$groupId) return false;
		
		$sql = "SELECT COUNT(*) FROM tp_product_group WHERE shop_id = {$this->shopId} AND client_id = {$this->clientId} AND group_id = {$groupId}";
		$this->db->query($sql);
		
		return (intval($this->db->get_field())> 0)? true : false;
	}
	
	private function addProductFeed($feeds, $productId) {
		$sql = "DELETE FROM tp_product_to_feed WHERE shop_id={$this->shopId} AND client_id = {$this->clientId} AND product_id = {$productId}";
		$this->db->query($sql);
		$tp_product_to_feed = array();
		
		if(empty($feeds)) return;
		foreach($feeds as $feedId) {
			if(!intval($feedId)) continue;
			$tp_product_to_feed[] = array(
					'client_id'=>$this->clientId,
					'product_id'=>$productId,
					'shop_id'=>$this->shopId,
					'feed_id'=>intval($feedId)
			);
		}
	
		if(count($tp_product_to_feed)) {
			$this->db->autoupdate()->table('tp_product_to_feed')->data($tp_product_to_feed);
			$this->db->execute();
		}
	}
	
	private function addProductImages($images, $removeHelper, $productId) {
		if(count($removeHelper) > 0) {
			$img = array();
			foreach($removeHelper as $img_id) {
				$img_id = intval($img_id);
				if($img_id) {
					$img[] = $img_id;
				}
			}
	
			if(count($img)) {
				$img = implode(',',$img);
				$sql = "DELETE FROM
				tp_product_img
				WHERE
				shop_id={$this->shopId} AND
				client_id = {$this->clientId} AND
				product_id = {$productId} AND
				img_id IN({$img})";
				$this->db->query($sql);
			}
	
			}
	
			if(is_array($images) && count($images) > 0) {
			$tp_product_img = array();
	
                foreach($images as $item) {
    				if(!is_array($item) || count($item) < 3) continue;
    				if(!isset($item[3])) $item[3] = array('name'=>'');
    				
    				$tp_product_img[] = array(
        				'client_id'=>$this->clientId,
        				'product_id'=>$productId,
        				'shop_id'=>$this->shopId,
        				'title'=>'product image',
        				'order'=>intval($item[0]['order']),
    					'url1'=>$this->checkImageUrl($item[0]['name']),
    					'url2'=>$this->checkImageUrl($item[1]['name']),
    					'url3'=>$this->checkImageUrl($item[2]['name']),
    				    'url4'=>$this->checkImageUrl($item[3]['name']),
    					'img_id'=> (isset($item[0]['img_id']) && intval($item[0]['img_id']))? intval($item[0]['img_id']) : 0
    				);
				}
	
			if(count($tp_product_img)) {
						$this->db->autoupdate()->table('tp_product_img')->data($tp_product_img)->primary('img_id', 'client_id','product_id','shop_id');
						$this->db->execute();
			}
				}
	
		return true;
	}
	
	private function clearDoubleNumbers($price) {
		$price = preg_replace("/[^0-9\.\,]/", '', $price);
		$price = str_replace(',', '.', $price);
	
		return $price;
	}
	
	private function checkImageUrl($url) {
		$realPrefix = '/vars/files/images/';
		return (substr($url, 0, strlen($realPrefix)) == $realPrefix)? $url : '';
	}

}


if(!function_exists('datepicker')) {
	function datepicker($date){
		return ($date)? date('d.m.Y',$date) : '-';
	}
}


if(!function_exists('h_date')) {
    function h_date($date){
        return ($date)? date('d-m-Y - H:i',$date) : 'не установлено';
    }
}
?>