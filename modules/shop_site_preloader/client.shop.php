<?php 

class client_shop extends global_client_api {
	
    public $notice = false; 
    public $auth = false;

    
    public function syncArticle($article) {
        
        
    }
    
    public function syncArticleAll() {
        $config = $this->core->CONFIG['1cServices'];
        $options = array(
            'login'=> $config['price']['login'],
            'password' => $config['price']['password']
        );
        
        $client = new SoapClient($config['price']['url'], $options);
        
        $this->db->query("SELECT product_id, article FROM tp_product WHERE `article` != '' AND `article` IS NOT NULL");
        $this->db->get_rows();
        $list = array_chunk($this->db->rows, 1000);
        
        $guid = array(
            'price' =>  '5634b535-b2f4-11e5-8642-08606e834058',
            'price2' => '5634b536-b2f4-11e5-8642-08606e834058',
            'price3' => '5634b534-b2f4-11e5-8642-08606e834058',
            'price4' => '5634b537-b2f4-11e5-8642-08606e834058',
            'price5' => '5634b532-b2f4-11e5-8642-08606e834058'
        );
        
        $getPrice = function($field, $price) use($guid) {
            foreach($price as $item) {
                if($item->GUID == $guid[$field]) {
                    return $item->Цена;
                }
            }
            
            return 0;
        };
        
        foreach($list as $piece) {
            $query = array();
            foreach($piece as $product) {
                $query[] = $product['article'];
            }
            
            $response = $client->{$config['price']['function']}(array('arrCodes'=>$query));
            
            if($response instanceof stdClass) {
                if(!empty($response) && !empty($response->return && !empty($response->return->Товар))) {
                    
                    foreach($response->return->Товар as $product) {
                        $sql = "UPDATE tp_product SET 
                                    price = " . $getPrice('price', $product->Цены->ЗаписьЦены) . ",
                                    price2 = " . $getPrice('price2', $product->Цены->ЗаписьЦены) . ",
                                    price3 = " . $getPrice('price3', $product->Цены->ЗаписьЦены) . ",
                                    price4 = " . $getPrice('price4', $product->Цены->ЗаписьЦены) . ",
                                    price5 = " . $getPrice('price5', $product->Цены->ЗаписьЦены) . " 
                                        
                                 WHERE 
                                    article = '{$product->Артикул}' ";
                        $this->db->query($sql);
                    }
                }
            } else {
                return false;
            }
            
        }
    
        
        
        
    }
    
    
    public function productHit($productId) {
        $this->db->query("UPDATE tp_product SET popular = popular + 1 WHERE client_id = {$this->clientId} AND shop_id = {$this->shopId} AND product_id = {$productId}");
    }
    
    public function getFeeds() {
        $sql = "SELECT * FROM tp_product_feeds WHERE  show_on_index = 1";
        $this->db->query($sql);
        $this->db->get_rows();
        
        return $this->db->rows;
    }
    
    public function getProductByFeeds() {
        $feeds = $this->getFeeds();
        $products = array();
        
        foreach($feeds as $item) {
            $prods = $this->getProducts(false, 0, 10, 'new', 'asc', array('feeds'=>$item['id']));
            $products[$item['id']] = $prods[0];
        }
        
        return $products;
    } 
    
    public function getViewedProducts($limit) {
        if(empty($_SESSION['lastviewedproduct'])) return false;
        
        $list = array_reverse($_SESSION['lastviewedproduct']);
        $products = array();
        $iteration = 0;
        
        foreach($_SESSION['lastviewedproduct'] as $productId) {
            $iteration++;
            $products[] = $this->getProductInfo($productId, true, false, true);
            if($iteration == $limit) {
                break;
            }
        }
        
        $products = array_reverse($products);
        
        return $products;
    }
    
    public function getCrossSelling($productId, $limit) {
        //$sql = ""
        return false;
    }
    
    public function getCompareFeatures() {
        $ids = array_keys($_SESSION['compare']);
        
        $sql = "SELECT 
                    DISTINCT pf.id, 
                    pf.name 
                FROM 
                    tp_product_feature as pf 
                        LEFT JOIN tp_product_to_feature as ptf ON pf.id = ptf.feature_id 
                WHERE 
                    ptf.shop_id = {$this->shopId} AND
                    ptf.client_id = {$this->clientId} AND
                    ptf.product_id IN(".implode(',',$ids).")";
        
        
        $this->db->query($sql);
        $this->db->get_rows();
        $features = $this->db->rows;
        
        $sql = "SELECT
                    DISTINCT yf.id,
                    yf.title as name
                FROM
                    ym_features as yf
                        LEFT JOIN tp_product_to_ym_feature as ptf ON yf.id = ptf.feature_id
                WHERE
                    ptf.shop_id = {$this->shopId} AND
                    ptf.client_id = {$this->clientId} AND
                    ptf.product_id IN(".implode(',',$ids).")";
        
        
        $this->db->query($sql);
        $this->db->get_rows();
        $ymfeatures = $this->db->rows;
        
        return array('category'=>$ymfeatures, 'user'=>$features);
    }
    
    public function getCompareFeaturesProducts() {
        $ids = array_keys($_SESSION['compare']);
        $features = $this->getCompareFeatures();
        $products = $_SESSION['compare'];
        
        foreach($ids as $productId) {
            $ft = $this->getProductFeaturesForCompare($productId, 'all');
            $pf = $features;
            
           
            
            foreach($pf['category'] as &$item) {
                if(isset($ft['category'][$item['id']])) {
                    $item = array_merge($item, $ft['category'][$item['id']]);
                    $item['used']=true;
                } else {
                    $item['used']=false;
                } 
            }
            
            foreach($pf['user'] as &$item) {
                if(isset($ft['user'][$item['id']])) {
                   
                    $item = array_merge($item, $ft['user'][$item['id']]);
                    $item['used']=true;
                } else {
                    $item['used']=false;
                }
            }
            
            $products[$productId]['features'] = $pf;
        }
        
        return $products;
    }
    
    public function addToCompare($productId) {
        //unset($_SESSION['compare']);
        $productId = intval($productId);
        if(!$productId || !$this->checkAccessToProduct($productId)) return false;
        if(!isset($_SESSION['compare'])) $_SESSION['compare'] = array();        
        if(!isset($_SESSION['compare'][$productId])) {
            $item = $this->getProductInfo($productId, true);
            unset($item['shop_id'], $item['client_id']);
            $_SESSION['compare'][$productId] = $item;
        }
        
        return true;
    }
    
    public function removeFromCompare($productId) {
        $productId = intval($productId);
        if(!$productId) return false;
        if(!isset($_SESSION['compare']) || !isset($_SESSION['compare'][$productId])) return false;
        unset($_SESSION['compare'][$productId]);
        
        return true;
    }
    
    public function getCompareSummary() {
        return (!isset($_SESSION['compare']))? array('length'=>0) : array('length'=>count($_SESSION['compare']),'items'=>$_SESSION['compare']);
    }
    
    public function addWishlist($productId) {
        $productId = intval($productId);
        if(!$productId) return false;
        if(!$this->checkAccessToProduct($productId)) return false;
        
    }
    
	public function removeFromCart($productId) {
	    // if saved cart -> remove from db
		$productId = intval($productId);
		if(!$productId) return false;
		if(!isset($_SESSION['cart']) || !isset($_SESSION['cart'][$productId])) return false;
		unset($_SESSION['cart'][$productId]);
		
		return true;
	}
	
	public function saveCartBox() {
	    $cart = $this->getCartSummary();
	    $orderItems = array();
	    
	    $cartbox = array(
	        'client_id'=>$this->clientId,
	        'shop_id'=>$this->shopId,
	        'user_id'=>$this->userId,
	        'last_view'=>time(),
	        'last_edit'=>time(),
	        'create'=>time()
	    );
	    
	    $this->db->autoupdate()->table('tp_user_cartbox')->data(array($cartbox));
	    $this->db->execute();
	    $cartBoxId = $this->db->insert_id;
	    	    
	    foreach($cart['list'] as $item) {
	        $productName = (!empty($item['parent']))? $item['parent']['title'].' - '.$item['title'] : $item['title'];
	        $boxItem[] = array(
	            'client_id'=>$this->clientId,
	            'shop_id'=>$this->shopId,
	            'cart_id'=>$cartBoxId,
	            'product_id'=>$item['id'],
	            'product_name'=>mysql::str($productName),
	            'price'=>$item['price_f'],
	            'summ'=>$item['summ_float'],
	            'count'=>intval($item['qt'])
	        );
	    }
	    
	    if(count($boxItem)) {
	        $this->db->autoupdate()->table('tp_user_cartbox_item')->data($boxItem);
	        $this->db->execute();
	    }
	    
	    return $cartBoxId;
	}
	
	public function clearCart() {
        // if saved cart -> clear in db
	    
		unset($_SESSION['cart']);
	}
	
	public function getOrderInfo($md5) {
		$sql = "SELECT order_id FROM tp_order WHERE client_id = {$this->clientId} AND shop_id = {$this->shopId} AND MD5(order_id) = '{$md5}'";
		$this->db->query($sql);
		$orderId = $this->db->get_field();

		if(!$orderId) return false;
	
		return $this->getOrder($orderId);
	}
	
	public function checkOrderCode($orderId, $code) {
	    $orderId = intval($orderId);
	    $code = mysql::str($code);
	    
	    $sql = "SELECT COUNT(*) FROM tp_order WHERE client_id = {$this->clientId} AND order_id = {$orderId} AND shop_id = {$this->shopId} AND code = '{$code}'";
	    $this->db->query($sql);
	    
	    return intval($this->db->get_field()) > 0;
	}
	
	public function getOrder($orderId) {
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
					o.code,
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
						LEFT JOIN crm_contacts c ON o.contact_id = c.contact_id AND o.client_id = c.client_id
						LEFT JOIN tp_order_status s ON s.id = o.status_id LEFT JOIN tp_order_pay_status p ON p.id = o.status_pay_id
						LEFT JOIN tp_delivery_types d ON d.id = o.delivery_type_id
				WHERE
					o.shop_id = {$this->shopId} AND
					o.client_id = {$this->clientId} AND
					o.order_id = {$orderId}";
		
		$this->db->query($sql);
		$this->db->filter('create_date', $this->filters['datetime']);
		$this->db->filter('delivery_date', $this->filters['datetime']);
		$this->db->get_rows(1);
	
		if(!$this->db->rows) return false;
	
		$info = $this->db->rows;
		$info['items'] = $this->getOrderItems($orderId);
		$info['contact'] = $this->getContactInfo($info['contact_id']);
	
		return $info;
	}
	
	public function getOrderItems($orderId) {
		$sql = "SELECT
					i.order_item_id,
					0 as product_action_procent,
					'нет' as product_action_type_name,
					i.product_name,
					i.product_cost,
					i.count,
					i.product_id as id,
					i.count * i.product_cost as row_total,
					(SELECT img.url2 FROM tp_product_img as img  WHERE img.product_id = i.product_id AND img.client_id = {$this->clientId} AND img.shop_id = {$this->shopId} ORDER BY `order` LIMIT 1) as img
				FROM
					tp_order_items as i
			
				WHERE
					i.shop_id = {$this->shopId} AND
					i.client_id = {$this->clientId} AND
					i.order_id = {$orderId}";
		$this->db->query($sql);
		$this->db->get_rows();
	
		return $this->db->rows;
	}
	
	public function getUserContactId($userId) {
		
	   return $this->userInfo['crm_contact_id'];
	}
	
	public function getContactInfo($contactId) {
		$sql = "SELECT
				c.contact_id,
				c.date,
				c.name,
				c.surname,
			    c.lastname,
				c.source_id,
				c.comment,
				c.company_id,
				c.responsible_user_id,
				s.name as source_name,
				cm.name as company_name
	
			FROM
				crm_contacts as c
					LEFT JOIN crm_contacts_sources as s ON s.source_id = c.source_id
					LEFT JOIN crm_companies as cm ON cm.company_id = c.company_id AND cm.client_id = {$this->clientId}
			WHERE
				c.client_id = {$this->clientId} AND
				c.contact_id = {$contactId}";
		
		$this->db->query($sql);
		$this->db->get_rows(1);
		$contact = $this->db->rows;
		if(!$contact) return false;

		$contact['email'] = $this->getContactEmail($contactId);
		$contact['phone'] = $this->getContactPhone($contactId);
		$contact['address'] = $this->getContactAddress($contactId);

		return $contact;
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
		$sql = "SELECT * FROM crm_contacts_address WHERE client_id = {$this->clientId} AND contact_id = {$contactId} LIMIT 1";
		$this->db->query($sql);
		return $this->db->get_rows(1);
	}	
	
	public function addCallbackContact($name, $phone, $comment) {
		$query = array(
            'client_id'=>$this->clientId,
		    'user_id'=>0,
		    'date'=>time(),
		    'company_id'=>0,
		    'name'=>mysql::str($name),
		    'responsible_user_id'=>0,
		    'source_id'=>9,
		    'comment'=>mysql::str($comment)
		);
		
		$this->db->autoupdate()->table('crm_contacts')->data(array($query));
		$this->db->execute();
		$contactId = $this->db->insert_id;
		
		$query = array(
            'client_id'=>$this->clientId,
		    'contact_id'=>$contactId,
		    'phone_type'=>1,
		    'phone'=>mysql::str($phone)
		);
		
		$this->db->autoupdate()->table('crm_contacts_phones')->data(array($query));
		$this->db->execute();
		
		$message = array(
		    'name'=>mysql::str($name),
		    'phone'=>mysql::str($phone),
		    'email'=>'',
		    'user_comment'=>mysql::str($comment)
		);
		
		$this->notice('user.callback', $message);
		
		return $contactId;
	}
	
	public function addToCart($productId, $quantity=false,$newQuantity=false) {
	    // save cart in db!!!
	    // save cart id in cookie
	    
		$productId = intval($productId);
		
		if(!$productId || !$this->checkAccessToProduct($productId)) return false;
		
		if($quantity < 0 || ($quantity === false && $newQuantity !== false)) {
			if(!isset($_SESSION['cart']) || !isset($_SESSION['cart'][$productId])) return false;
			
			if($newQuantity !== false) {
				$_SESSION['cart'][$productId]['qt'] = intval($newQuantity);
			} else {
				$_SESSION['cart'][$productId]['qt'] += $quantity;
			}
		} else {
			$quantity = intval($quantity);
			if(!isset($_SESSION['cart'])) {
				$_SESSION['cart'] = array();
			}
			
			if(!isset($_SESSION['cart'][$productId])) {
				$item = $this->getProductInfo($productId, true, true);
				
				$item['price'] = $item['price_f'];
				$item['price2'] = $item['price2_f'];
				$item['price3'] = $item['price3_f'];
				$item['price4'] = $item['price4_f'];
				$item['price5'] = $item['price5_f'];
				
				
				unset($item['shop_id'], $item['client_id']);
				$item['qt'] = $quantity;
				$_SESSION['cart'][$productId] = $item;
			} else {
				$_SESSION['cart'][$productId]['qt'] += $quantity;
			}
		}
		return true;
	}
		
	public function getCartSummary($overcost = false) {
		if(!isset($_SESSION['cart']) || !count($_SESSION['cart'])) {
			return array('sum'=>0,'count'=>0,'text'=>'В корзине нет товаров.');
		} else {
			$summary = array('sum'=>0,'summ_float'=>0, 'count'=>0, 'list'=>$_SESSION['cart']);
			
			foreach($summary['list'] as $k=>$item) {
				$summary['list'][$k]['price_full'] =  parse_rub($item['price_f']*$item['qt']);
				$summary['sum'] += $item['price_f']*$item['qt'];
				$summary['count'] += $item['qt'];
				
			}
			
			$summary['summ_float'] = $summary['sum'];
			$summary['sum'] = parse_rub($summary['sum']);
			$summary['text'] = 'В корзине '.$summary['count'].' '.morph($summary['count'], 'товар','товара','товаров').' на сумму '.$summary['sum'];
			$summary['summ_float_w_overcost'] = $summary['summ_float'] + $overcost;
			$summary['sum_w_overcost'] = parse_rub($summary['summ_float'] + $overcost);
			
			return $summary;
		}
	}
	
	public function getGroupInfo($groupId) {
		$groupId = intval($groupId);
		
		if(!$groupId) return false;
		
		$sql = "SELECT
					g.*,
					g.group_id as id,
					get_rewrite(g.rewrite_id) as rewrite,
					'' as url,
					(select ext_char1 from tp_product_group where group_id = g.parrent_id) as parent_icon
				FROM
					tp_product_group as g
				WHERE
					g.shop_id = {$this->shopId} AND
					g.client_id = {$this->clientId} AND 
					g.group_id = {$groupId}";

		$this->db->query($sql);
		$this->db->filter('url', $this->filters['category_url']);
		$this->db->get_rows(1);
		$info = $this->db->rows;
		
		$info['sub'] = $this->getCategories($info['group_id'], false);
		return $info;
	}
	
	public function getProductsFromList($listId, $groupid=false, $page=0, $limit=10) {
		$page = intval($page);
		$limit = intval($limit);
		
		
		
		if($listId < 10) {
			$sql = $this->getProductsBasicQuery(false, array("LEFT JOIN tp_product_to_feed AS ptf ON ptf.product_id = p.product_id"));
			$sql .= " AND ptf.shop_id = {$this->shopId} AND ptf.client_id = {$this->clientId} AND ptf.feed_id = ".intval($listId);
		} else {
			$sql = $this->getProductsBasicQuery(false);
		}
	
		if($groupid) {
			$sql .= " AND (p.group_id = {$groupid} OR p.product_id IN(SELECT pg.product_id FROM tp_product_to_group as pg WHERE pg.shop_id = {$this->shopId} AND pg.client_id = {$this->clientId} AND pg.group_id = {$groupid}))";
		}
		
		if($listId == 100) {
			$sql .= " ORDER BY RAND()";
		}
		
		if($listId == 200) {
			$sql .= " AND p.sales = 1 ";
		}
		
		$sql .= " LIMIT {$page},{$limit}";
		
		$this->db->query($sql);
		$this->db->filter('url', $this->filters['product_url']);
		$this->db->filter('price', $this->filters['price']);
		$this->db->filter('modification_first_price', $this->filters['price']);
		$this->db->filter('old_price', $this->filters['price']);
		$this->db->filter('sales_summ', $this->filters['price']);
		$this->db->get_rows();

		
		
		return $this->db->rows;
	} 
	
	public function getProductsRecentBuy($page=0, $limit=10) {
	    $page = intval($page);
	    $limit = intval($limit);
	    
	    $subquery = array('(SELECT COUNT(*) FROM tp_order_items as toi WHERE toi.shop_id = p.shop_id AND toi.client_id = p.client_id AND toi.product_id = p.product_id) as cnt_buy');
	    
	    $sql = $this->getProductsBasicQuery($subquery, false);
	    $sql .= " ORDER BY cnt_buy DESC LIMIT {$page},{$limit}";
	
	    $this->db->query($sql);
	    $this->db->filter('url', $this->filters['product_url']);
	    $this->db->filter('price', $this->filters['price']);
	    $this->db->filter('old_price', $this->filters['price']);
	    $this->db->filter('sales_summ', $this->filters['price']);
	    $this->db->get_rows();
	
	    return $this->db->rows;
	}
	
	public function getProductUrlById($productId) {
	    $sql = "SELECT product_id as id, get_rewrite(rewrite_id) as rewrite, '' as url FROM tp_product WHERE client_id = {$this->clientId} AND shop_id = $this->shopId AND product_id = {$productId}";
	    $this->db->query($sql);
	    $this->db->filter('url', $this->filters['product_url']);
	    $this->db->get_rows(1);

	    return $this->db->rows['url'];
	}
	
	public function getProductIdByArticle($article) {
	    $sql = "SELECT product_id, parent_id FROM tp_product WHERE client_id = {$this->clientId} AND shop_id = $this->shopId AND article = '{$article}'";
	    $this->db->query($sql);
	    $this->db->get_rows(1);
	    $product = $this->db->rows;
	    
	    if(!empty($product)) {
	        return (intval($product['parent_id']) > 0)? $product['parent_id'] : $product['product_id'];
	    }
	    
	    return false;
	}
	
	public function findProduct($query,  $page, $limit, $sortBy='price', $sortType='asc', $filters = array()) {
		$sql = $this->getProductsBasicQuery();
		if(!empty($query)) {
		    if(isset($filters) && isset($filters['multiple']) && $filters['multiple'] && is_array($query)) {
		        $sql .= " AND ( ";
		        $sub = array();
		        foreach($query as $item) {
		            $sub[] = "\n (
        		        LOWER(p.title) LIKE '%{$item}%' OR
        		        p.article = '{$item}' 
                    ) "; 
		        }
		        
		        $sub = implode(' AND ', $sub);
		        $sql .= $sub . ' )';
		    } else {
		        $sql .= " AND (
    		        LOWER(p.title) LIKE '%{$query}%' OR
    		        p.article = '{$query}' 
                )";
		    }
		}
		
		if($filters) {
			if(!empty($filters['min_price'])) {
				$sql .= " AND price >= {$filters['min_price']}";
			}
			
			if(!empty($filters['max_parice'])) {
				$sql .= " AND price <= {$filters['max_parice']}";
			}
			
			if(!empty($filters['category'])) {
				$groups = implode(',',$filters['category']);
				$sql .= " AND (p.group_id IN({$groups}) OR p.product_id IN(SELECT pg.product_id FROM tp_product_to_group as pg WHERE pg.shop_id = {$this->shopId} AND pg.client_id = {$this->clientId} AND pg.group_id IN({$groups}))) ";
			}
			
			if(!empty($filters['features'])) {
				$sub = array();
				foreach($filters['features'] as $f) {
					$f[0] = intval($f[0]);
					$f[1] = mysql::str($f[1]);
					$sub[] = "ptf.feature_id = {$f[0]} AND ptf.value = '{$f[1]}'"; 
				}
				
				$sql .= " AND p.product_id IN(SELECT ptf.product_id FROM tp_product_to_feature as ptf WHERE ptf.shop_id = {$this->shopId} AND ptf.client_id = {$this->clientId} AND (";
				$sql .= '('.implode(') OR (', $sub).')))';
			}
			
		}
		
		$orders = array('price'=>'p.price', 'date'=>'p.update_date', 'rating'=>'id','name'=>'p.title', 'product_id'=>'p.product_id');
		$sortBy = $orders[$sortBy];
		$sql .=  " AND p.parent_id = 0 ORDER BY {$sortBy} {$sortType} LIMIT {$page},{$limit}";
				
		$this->db->query($sql);
		
	
		$this->db->filter('url', $this->filters['product_url']);
	    $this->db->filter('price', $this->filters['price_new']);
		$this->db->filter('price2', $this->filters['price_new']);
		$this->db->filter('price3', $this->filters['price_new']);
		$this->db->filter('price4', $this->filters['price_new']);
		$this->db->filter('price5', $this->filters['price_new']);
		$this->db->filter('pack_size', function($field){
		    return (substr($field, -3) == '.00')? intval($field) : $field;
		});
		$this->db->filter('pack_unit', function($field, $row, &$link){
		    $field = intval($field);
		    $unitname = '';
		    if($field == 61) $unitname = 'шт.';
		    if($field == 57) $unitname = 'кг';
		    if($field == 7) $unitname = 'м';
		     
		    $link['pack_unit_name'] = $unitname;
		     
		    return $field;
		});
	    
		$products = $this->db->get_rows();
		$foundRows = $this->db->found_rows();
		
		foreach($products as $i=>$item) {
		    if(intval($item['modification_first'])) {
		        $products[$i]['mod'] = $this->getInfoByMod(intval($item['modification_first']));
		    }
		}
		
		/*
		$this->db->filter('url', $this->filters['product_url']);
		$this->db->filter('old_price', $this->filters['price']);
		$this->db->filter('sales_summ', $this->filters['price']);
		$this->db->filter('price', $this->filters['price_new']);
		$this->db->filter('price2', $this->filters['price_new']);
		$this->db->filter('price3', $this->filters['price_new']);
		$this->db->filter('price4', $this->filters['price_new']);
		$this->db->filter('price5', $this->filters['price_new']);
		$this->db->filter('pack_unit', function($field, $row, &$link){
		    $field = intval($field);
		    $unitname = '';
		    if($field == 61) $unitname = 'шт.';
		    if($field == 57) $unitname = 'кг';
		    if($field == 7) $unitname = 'м';
		     
		    $link['pack_unit_name'] = $unitname;
		     
		    return $field;
		});
		
		$products = $this->db->get_rows();
		$foundRows = $this->db->found_rows();

		
	    foreach($products as $i=>$item) {
	        if(intval($item['parent_id']) > 0) {
	           $sql = "SELECT 
	                       get_rewrite(p.rewrite_id) rewrite, 
	                       '' as url, 
	                       p.title, 
	                       p.description,
	                       (SELECT
								img.url3
							FROM
								tp_product_img as img
							WHERE
								img.product_id = p.product_id AND
								img.client_id = {$this->clientId} AND
								img.shop_id = {$this->shopId}  AND
								img.url1 <> ''
							ORDER BY `order` ASC LIMIT 1) AS img
	                   FROM 
	                       tp_product as p 
	                   WHERE 
	                       p.shop_id = {$this->shopId} AND 
            	           p.client_id = {$this->clientId} AND 
            	           p.product_id = {$item['parent_id']}";   
	           $this->db->query($sql);
	           $this->db->filter('url', $this->filters['product_url']);
	           $this->db->get_rows(1);
	           $mod = $this->db->rows;
	           $item['title'] .= ' '.$mod['title'];
	           $item['article'] = $mod['article'];
	           $item['url'] = $mod['url'];
	           $item['img'] = $mod['img'];
	           $products[$i] = $item;
	        }
	        
			$products[$i]['features'] = $this->getProductFeatures($item['id']);
		}
		*/
		return array($foundRows, $products);
	}
	
	public function makeOrder($user) {		
		$contact = array(
			'client_id'=>$this->clientId,
			'user_id'=>0,
			'date'=>time(),
			'company_id'=>0,
			'name'=>mysql::str($user['full_name']),
			'responsible_user_id'=>0,
			'post'=>'',
			'departament'=>'',
			'source_id'=>7				
		);
		
		$this->db->autoupdate()->table('crm_contacts')->data(array($contact));
		$this->db->execute();
		$contactId = $this->db->insert_id;
		
		$query = array(
			'client_id'=>$this->clientId,
			'contact_id'=>$contactId,
			'email'=>mysql::str($user['email']),
			'email_type'=>1
		);
		
		$this->db->autoupdate()->table('crm_contacts_email')->data(array($query));
		$this->db->execute();
		
		$query = array(
				'client_id'=>$this->clientId,
				'contact_id'=>$contactId,
				'phone'=>mysql::str($user['phone']),
				'phone_type'=>1
		);
		
		$this->db->autoupdate()->table('crm_contacts_phones')->data(array($query));
		$this->db->execute();
		
		$query = array(
				'client_id'=>$this->clientId,
				'contact_id'=>$contactId,
				'country'=>'Россия',
				'address1'=>mysql::str($user['contact_address1']),
				'address2'=>mysql::str($user['contact_address2'])
		);
		
		$this->db->autoupdate()->table('crm_contacts_address')->data(array($query));
		$this->db->execute();
		
		$code = rand(1,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(1,9);
		
		
		$query = array(
			'client_id'=>$this->clientId,
			'shop_id'=>$this->shopId,
			'contact_id'=>$contactId,
			'status_id'=>1,
			'status_pay_id'=>1,
			'delivery_type_id'=>intval($user['ship_type']),
			'delivery_date'=>time()+60*60*48,
			'delivery_time'=>'',
			'create_date'=>time(),
			'address'=>mysql::str($user['address']),
			'comment'=>mysql::str($user['comment']),
			'recipient_phone'=>mysql::str($user['phone']),
		    'code'=>$code
		);
		
		$this->db->autoupdate()->table('tp_order')->data(array($query));
		$this->db->execute();
		$orderId = $this->db->insert_id;
		
		$cart = $this->getCartSummary();
		$orderItems = array();
		
		foreach($cart['list'] as $item) {
			$productName = (!empty($item['vendor']))? $item['vendor'].' '.$item['name'] : $item['name'];
			$orderItems[] = array(
				'client_id'=>$this->clientId,
				'shop_id'=>$this->shopId,
				'order_id'=>$orderId,
				'product_id'=>$item['id'],
				'product_name'=>mysql::str($productName),
				'product_cost'=>$item['price_float'],
				'add_date'=>time(),
				'count'=>intval($item['qt'])
			);
		}
		
		if(count($orderItems)) {
			$this->db->autoupdate()->table('tp_order_items')->data($orderItems);
			$this->db->execute();
		}
		
		$orderInfo = $this->getOrder($orderId);
		$this->notice('order.new', $orderInfo);
		
		return $orderInfo;
	}
	
	public function makeOrderFromCartbox($order) {
	    $contact = $this->getContactInfo($order['contact_id']);
        $cartbox = $this->getCartBox($order['cartbox_id']);
        $code = rand(1,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(1,9);
        
        $query = array(
            'client_id'=>$this->clientId,
            'shop_id'=>$this->shopId,
            'contact_id'=>$order['contact_id'],
            'status_id'=>1,
            'status_pay_id'=>1,
            'delivery_type_id'=>intval($order['delivery_type']),
            'delivery_date'=>time()+60*60*48,
            'delivery_time'=>'',
            'create_date'=>time(),
            'address'=>mysql::str($order['short_address']),
            'comment'=>mysql::str($order['comment']),
            'recipient_phone'=>mysql::str($order['phone']),
            'code'=>$code
        );
        
        $this->db->autoupdate()->table('tp_order')->data(array($query));
        $this->db->execute();
        $orderId = $this->db->insert_id;
        
        foreach($cartbox['list'] as $item) {
            $orderItems[] = array(
                'client_id'=>$this->clientId,
                'shop_id'=>$this->shopId,
                'order_id'=>$orderId,
                'product_id'=>$item['product_id'],
                'product_name'=>mysql::str($item['title']),
                'product_cost'=>$item['price_float'],
                'add_date'=>time(),
                'count'=>intval($item['count'])
            );
        }
        
        if(count($orderItems)) {
            $this->db->autoupdate()->table('tp_order_items')->data($orderItems);
            $this->db->execute();
        }
        
        $orderInfo = $this->getOrder($orderId);
        $this->notice('order.new', $orderInfo);
        $this->addOrderEvent($orderId, 1, 'Указан способ оплаты &laquo;'.$order['pay_type_name'].'&raquo;');
        
        
        return $orderId;
	}
	
	private function getProductsBasicQuery($fields = false, $join = false) {
		$sql = "SELECT SQL_CALC_FOUND_ROWS
					get_rewrite(p.rewrite_id) rewrite,
					'' as url,
					p.product_id id,
					p.description,
					p.article,
					p.title title,
					p.title name,
					p.update_date update_date,
					p.popular,
					p.parent_id,
					
					p.pack_size,
					p.pack_unit,
					p.min_order,
					p.price_type,

					IF(p.sales = 1, p.price_new, p.price) as price,
					p.price as old_price,
					p.sales,
					p.sales_summ,
					p.sales_procent,
					
					p.price2,
					p.price3,
					p.price4,
					p.price5,
	
					(SELECT m.product_id FROM tp_product as m WHERE m.parent_id = p.product_id AND m.shop_id = {$this->shopId} AND m.client_id = {$this->clientId} ORDER BY price = 0, price LIMIT 1) as modification_first,
					(SELECT ROUND(m.price / m.pack_size, 2) FROM tp_product as m WHERE m.parent_id = p.product_id AND m.shop_id = {$this->shopId} AND m.client_id = {$this->clientId} ORDER BY price = 0, price LIMIT 1) as modification_first_price,
					
					IF(p.vendor_id, (SELECT v.name FROM tp_vendors as v WHERE v.client_id = {$this->clientId} AND v.shop_id = {$this->shopId} AND v.id = p.vendor_id), '') as vendor,
					IFNULL(p.article, '#000000') as article,
					IFNULL(
							(SELECT
								img.url3
							FROM
								tp_product_img as img
							WHERE
								img.product_id = p.product_id AND
								img.client_id = {$this->clientId} AND
								img.shop_id = {$this->shopId}  AND
								img.url1 <> ''
							ORDER BY `order` ASC LIMIT 1
					), '/templates/admin/green/images/no_image.png') as img,
					IFNULL(
							(SELECT
								img.url4
							FROM
								tp_product_img as img
							WHERE
								img.product_id = p.product_id AND
								img.client_id = {$this->clientId} AND
								img.shop_id = {$this->shopId}  AND
								img.url1 <> ''
							ORDER BY `order` ASC LIMIT 1
					), '/templates/admin/green/images/no_image.png') as img_original ";
		
		if($fields && is_array($fields)) {
			$sql .= ", ".implode(", ", $fields)." ";
		}
				
		$sql .= "FROM
					tp_product as p
						LEFT JOIN tp_product_status as ps ON ps.id = p.status_id ";
					
		
		if($join && is_array($join)) {
			$sql .= " ".implode(" ", $join)." ";
		}
		
		$sql .= "WHERE
					p.shop_id = {$this->shopId} AND
					p.client_id = {$this->clientId}";
		
		return $sql;
	}
	
	public function getInfoByMod($productId) {
	    $sql = "SELECT
	                p.pack_size,
					p.pack_unit,
					p.min_order,
					p.price_type,
					IF(p.sales = 1, p.price_new, p.price) as price,
					p.price as old_price,
					p.sales,
					p.sales_summ,
					p.sales_procent,
					p.price2,
					p.price3,
					p.price4,
					p.price5
	           FROM 
	               tp_product as p 
	           WHERE
	               p.shop_id = {$this->shopId} AND
				   p.client_id = {$this->clientId} AND
				   p.product_id = {$productId}";
	    $this->db->query($sql);
        $this->db->filter('price', $this->filters['price_new']);
		$this->db->filter('price2', $this->filters['price_new']);
		$this->db->filter('price3', $this->filters['price_new']);
		$this->db->filter('price4', $this->filters['price_new']);
		$this->db->filter('price5', $this->filters['price_new']);
		$this->db->filter('pack_size', function($field){
		    return (substr($field, -3) == '.00')? intval($field) : $field;
		});	
		$this->db->filter('pack_unit', function($field, $row, &$link){
		    $field = intval($field);
		    $unitname = '';
		    if($field == 61) $unitname = 'шт.';
		    if($field == 57) $unitname = 'кг';
		    if($field == 7) $unitname = 'м';
		     
		    $link['pack_unit_name'] = $unitname;
		     
		    return $field;
		});
	    
	    $this->db->get_rows(true);
	    
	    return $this->db->rows;
	}
	
	public function getProducts($groupId, $page, $limit, $sortBy='new', $sortType='asc', $filters = false) {
		$orders = array(
		    'price'=>'p.price', 
		    'date'=>'p.update_date', 
		    'rating'=>'id',
		    'name'=>'p.title', 
		    'product_id'=>'p.product_id',
		    'new'=>'p.update_date DESC',
		    'popular'=>'p.popular',
		    'sale'=>'p.sales DESC'
		);

		if($sortBy == 'new' || $sortBy == 'sale') {
		    $sortType = '';
		}
		
		
		$sortBy = $orders[$sortBy];
		
		$sql = $this->getProductsBasicQuery();
		
		if(!$groupId) {
		    if(!empty($filters)) {
		        if(!empty($filters['vendor'])) {
		            $sql .= " AND p.vendor_id = ".intval($filters['vendor'])." ";
		        }
		        
		        if(!empty($filters['feeds']) && intval($filters['feeds'])) {
		            $feedId = intval($filters['feeds']);
		            $sql .= " AND p.product_id IN(SELECT ptf.product_id FROM tp_product_to_feed as ptf WHERE ptf.client_id = {$this->clientId} AND ptf.shop_id = {$this->shopId} AND ptf.feed_id = {$feedId})";
		        }
		    } else {
		        $sql .= " AND 0 ";
		    }
		} else {
		    $sql .= " AND (p.group_id = {$groupId} OR p.product_id IN(SELECT pg.product_id FROM tp_product_to_group as pg WHERE pg.shop_id = {$this->shopId} AND pg.client_id = {$this->clientId} AND pg.group_id = {$groupId}) OR p.group_id IN (SELECT g3.group_id FROM tp_product_group as g3 WHERE g3.parrent_id = {$groupId} AND g3.client_id = {$this->clientId} AND g3.shop_id = {$this->shopId})) ";
		}
		
		//$sql .= " AND p.status_id = 1 ";
		
		$sql .= " ORDER BY {$sortBy} {$sortType} LIMIT {$page},{$limit}";
		
		$this->db->query($sql);	
		$this->db->filter('url', $this->filters['product_url']);
	    $this->db->filter('price', $this->filters['price_new']);
		$this->db->filter('price2', $this->filters['price_new']);
		$this->db->filter('price3', $this->filters['price_new']);
		$this->db->filter('price4', $this->filters['price_new']);
		$this->db->filter('price5', $this->filters['price_new']);
		$this->db->filter('pack_size', function($field){
		    return (substr($field, -3) == '.00')? intval($field) : $field;
		});
		$this->db->filter('pack_unit', function($field, $row, &$link){
		    $field = intval($field);
		    $unitname = '';
		    if($field == 61) $unitname = 'шт.';
		    if($field == 57) $unitname = 'кг';
		    if($field == 7) $unitname = 'м';
		     
		    $link['pack_unit_name'] = $unitname;
		     
		    return $field;
		});
	    
		$products = $this->db->get_rows();
		$foundRows = $this->db->found_rows();
		
		foreach($products as $i=>$item) {
		    if(intval($item['modification_first'])) {
		        $products[$i]['mod'] = $this->getInfoByMod(intval($item['modification_first']));
		    }
		}
		
		return array($products, $foundRows);
	}

	public function getProductInfo($productId, $short = false, $getParent = false, $getMod = false) {

		$sql = "SELECT
					get_rewrite(p.rewrite_id) as rewrite,
					'' as url,
					p.product_id as id,	
					p.title as name,
					p.parent_id,
					
					(SELECT m.product_id FROM tp_product as m WHERE m.parent_id = p.product_id AND m.shop_id = {$this->shopId} AND m.client_id = {$this->clientId} ORDER BY price LIMIT 1) as modification_first,
					IF(p.vendor_id, (SELECT v.name FROM tp_vendors as v WHERE v.client_id = {$this->clientId} AND v.shop_id = {$this->shopId} AND v.id = p.vendor_id), '') as vendor,
					IFNULL(
							(SELECT
								img.url2
							FROM
								tp_product_img as img
							WHERE
								img.product_id = p.product_id AND
								img.client_id = p.client_id AND
								img.shop_id = p.shop_id  AND
								img.url1 <> ''
							ORDER BY `order` ASC LIMIT 1
					), '/templates/admin/green/images/no_image.png') as img,
					p.*,
					
					IF(p.sales = 1, p.price_new, p.price) as price,
					IF(p.sales = 1, p.price_new, p.price) as price_float,
					p.price as old_price,
					p.price2,
					p.price3,
					p.price4,
					p.price5,
					p.pack_size,
					p.pack_unit,
					p.min_order,
					p.price_type
					
				FROM
					tp_product p
				WHERE
					p.shop_id = {$this->shopId} AND
					p.client_id = {$this->clientId} AND
					p.product_id = {$productId} ";
		
		$this->db->query($sql);
		$this->db->filter('url', $this->filters['product_url']);		
		$this->db->filter('price', $this->filters['price_new']);
		$this->db->filter('price2', $this->filters['price_new']);
		$this->db->filter('price3', $this->filters['price_new']);
		$this->db->filter('price4', $this->filters['price_new']);
		$this->db->filter('price5', $this->filters['price_new']);
		$this->db->filter('pack_unit', function($field, $row, &$link){
		    $field = intval($field);
		    $unitname = '';
		    if($field == 61) $unitname = 'шт.';
		    if($field == 57) $unitname = 'кг';
		    if($field == 7) $unitname = 'м';
		     
		    $link['pack_unit_name'] = $unitname;
		     
		    return $field;
		});
		     
	    $this->db->filter('pack_size', function($field){
	        $ps = $field.'';
	        if(substr($ps, -3) == '.00') {
	            return round($ps);
	        }
	
	        return $field;
	    });
		
		
		$this->db->get_rows(1);
		$info = $this->db->rows;
		
		if(!$info || !is_array($info)) return false;
		
		if($getParent) {
		    if(intval($info['parent_id']) > 0) {
		        $info['parent'] = $this->getProductInfo($info['parent_id'], true);
		    } else {
		        $info['parent'] = false;
		    }
		}
		
		if($getMod) {
		    if(intval($info['modification_first']) > 0) {
		        $info['mod'] = $this->getInfoByMod(intval($info['modification_first']));
		    } else {
		        $info['mod'] = false;
		    }
		}
		
		if($short) return $info;
		
		$info['images'] = $this->getProductImages($productId);
		$info['avaliable'] = $this->getProductAvaliable($productId);
		$info['dimensions'] = $this->getProductDimensions($productId);
		$info['stores'] = $this->getStoresList('class');
		$info['features'] = $this->getProductFeatures($productId);
		$info['comments'] = $this->getProductComments($productId);
		$info['comments_count'] = count($info['comments']);
		
		$info['modifications'] = $this->getModifications($productId);
		
		// $info['accompany'] = $this->getProductAccompany($productId);
		// $info['category_cross'] = $this->getGroupCrossList($info['group_id'], $productId);
		
		// getSimilarProducts($productId)  - похожие товары
		// getCrossSellingProducts($productId)  - с этим товаром покупают
		// getModifications($productId)
		// getRecomendeProducts
	
		
		
		
		return $info;
	}
	
	public function getModifications($productId) {
	    $sql = $this->getProductsBasicQuery();
	    $sql .= " AND parent_id = {$productId} ORDER BY position";
		
		//echo $sql;
		
		$this->db->query($sql);		
		$this->db->filter('url', $this->filters['product_url']);
	    $this->db->filter('price', $this->filters['price_new']);
	    $this->db->filter('price2', $this->filters['price_new']);
	    $this->db->filter('price3', $this->filters['price_new']);
	    $this->db->filter('price4', $this->filters['price_new']);
	    $this->db->filter('price5', $this->filters['price_new']);
	    $this->db->filter('pack_unit', function($field, $row, &$link){
	        $field = intval($field);
	        $unitname = '';
	        if($field == 61) $unitname = 'шт.';
	        if($field == 57) $unitname = 'кг';
	        if($field == 7) $unitname = 'м';
	        
	        $link['pack_unit_name'] = $unitname;
	        
	        return $field;
	    });
	    
        $this->db->filter('pack_size', function($field){
            $ps = $field.'';
            if(substr($ps, -3) == '.00') {
                return round($ps);
            }
            
            return $field;
        });
	    
		
		$products = $this->db->get_rows();
		
		return (!empty($products))? $products : false;
	}
	
	public function getGroupCrossList($groupId, $productId) {
		$sql = "SELECT parrent_id FROM tp_product_group WHERE group_id = {$groupId} AND shop_id = {$this->shopId} AND client_id = {$this->clientId}"; 
		$this->db->query($sql);
		$pid = intval($this->db->get_field());
		
		if($pid == 2) {
			$sql = "SELECT
						g.group_id,
						g.name,
						g.image
					FROM
						tp_product_group_cross as gc
							LEFT JOIN tp_product_group as g ON g.group_id = gc.cross_group_id
					WHERE
						gc.group_id = {$groupId} AND
						gc.shop_id = {$this->shopId} AND
						gc.client_id = {$this->clientId} AND
						g.shop_id = {$this->shopId} AND
						g.client_id = {$this->clientId}";
		} else {
			$sql = "SELECT
						g.group_id,
						g.name,
						g.image
					FROM
						tp_product_group_cross as gc
							LEFT JOIN tp_product_group as g ON g.group_id = gc.cross_group_id
					WHERE
						gc.group_id IN((SELECT pg.group_id FROM tp_product_to_group as pg WHERE pg.shop_id = {$this->shopId} AND pg.client_id = {$this->clientId} AND pg.product_id = {$productId} AND pg.group_id !=  {$groupId} )) AND
						gc.shop_id = {$this->shopId} AND
						gc.client_id = {$this->clientId} AND
						g.shop_id = {$this->shopId} AND
						g.client_id = {$this->clientId}";
		}
		
		
		$this->db->query($sql);
		$this->db->get_rows();
		$list = $this->db->rows;
		
		$list[count($list)-1]['is_last'] = true;
		
		return $list;
	}
	
	public function getStoresList($instance, $start = 0, $limit = 20, $sortBy = 'o.order_id', $sortType = 'DESC', $filters = false) {
	
		$sql = "SELECT SQL_CALC_FOUND_ROWS ";
	
		if($instance == 'class') {
			$sql .= " s.store_id as id, s.name FROM tp_stores as s ";
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
					product_id = {$product_id} AND 
					url1 <> ''
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
	
	public function getProductDimensions($product_id) {
		$sql = "SELECT
					*
				FROM
					tp_product_dimensions
				WHERE
					shop_id = {$this->shopId} AND
					client_id = {$this->clientId} AND
					product_id = {$product_id}";
		$this->db->query($sql);
		$this->db->get_rows(1);
	
		if(!$this->db->rows || !isset($this->db->rows['width'])) return false;
	
		return $this->db->rows;
	}
	
	public function getProductFeaturesForCompare($productId) {
        $sql = "SELECT
                   ptf.feature_id, ptf.variant_id, ptf.value,
                   IFNULL((SELECT u.name FROM tp_product_feature_units as u WHERE u.id = ptf.unit_id), '') as unit,
                   IFNULL((SELECT fv.value FROM tp_product_feature_variants as fv WHERE fv.id = ptf.variant_id), '') as variant,
                   pf.name,
                   pf.type
               FROM
                   tp_product_to_feature as ptf
                       LEFT JOIN tp_product_feature as pf ON pf.id = ptf.feature_id AND pf.client_id = {$this->clientId} AND pf.shop_id = {$this->shopId}
               WHERE
                   ptf.shop_id = {$this->shopId} AND
                   ptf.client_id = {$this->clientId} AND
                   ptf.product_id = {$productId} ";
         
        $this->db->query($sql);
        $this->db->get_rows(false, 'feature_id');
        $list = (is_array($this->db->rows))? $this->db->rows : array();
    
        $sql = "SELECT
            	    pyf.feature_id, pyf.variant_id,
            	    get_ym_feature_value(yf.type,pyf.value) as value,
            	    IFNULL((SELECT fv.value FROM ym_features_variant as fv WHERE fv.id = pyf.variant_id), '') as variant,
            	    yf.title as name,
            	    yf.type,
            	    yf.unit
        	    FROM
        	       tp_product_to_ym_feature as pyf
        	           LEFT JOIN ym_features as yf ON yf.id = pyf.feature_id
        	    WHERE
            	    pyf.shop_id = {$this->shopId} AND
            	    pyf.client_id = {$this->clientId} AND
            	    pyf.product_id = {$productId} ";

       $this->db->query($sql);
       $this->db->get_rows(false, 'feature_id');
       $yfeatures = (is_array($this->db->rows))? $this->db->rows : array();

       return array('category'=>$yfeatures, 'user'=>$list);
    }
	
	public function getProductFeatures($productId,$type = false) {
	    if(!$type || $type == 'user' || $type == 'all') {
	        $sql = "SELECT
	                   ptf.feature_id, ptf.variant_id, ptf.value,
	                   IFNULL((SELECT u.name FROM tp_product_feature_units as u WHERE u.id = ptf.unit_id), '') as unit,
	                   IFNULL((SELECT fv.value FROM tp_product_feature_variants as fv WHERE fv.id = ptf.variant_id), '') as variant,
	                   pf.name,
	                   pf.type
	               FROM
	                   tp_product_to_feature as ptf
	                       LEFT JOIN tp_product_feature as pf ON pf.id = ptf.feature_id AND pf.client_id = {$this->clientId} AND pf.shop_id = {$this->shopId}
	               WHERE
	                   ptf.shop_id = {$this->shopId} AND
	                   ptf.client_id = {$this->clientId} AND
	                   ptf.product_id = {$productId} ";
	        
	        $this->db->query($sql);
	        $this->db->get_rows();
	        $list = (is_array($this->db->rows))? $this->db->rows : array();
	    }
		
		if($type == 'user') return $list;
		
		if(!$type || $type == 'category' || $type == 'all') {
    		$sql = "SELECT
    					pyf.feature_id, pyf.variant_id,
    					 get_ym_feature_value(yf.type,pyf.value) as value,
    					IFNULL((SELECT fv.value FROM ym_features_variant as fv WHERE fv.id = pyf.variant_id), '') as variant,
    					yf.title as name,  
    					yf.type,
    					yf.unit
    				FROM
    					tp_product_to_ym_feature as pyf
    						LEFT JOIN ym_features as yf ON yf.id = pyf.feature_id
    	
    				
    				WHERE
    					pyf.shop_id = {$this->shopId} AND
    					pyf.client_id = {$this->clientId} AND
    					pyf.product_id = {$productId} ";
    		
    		$this->db->query($sql);
    		$this->db->get_rows();
    		$yfeatures = (is_array($this->db->rows))? $this->db->rows : array();
		}
		
		if($type == 'category') return $yfeatures;
		if($type == 'all') return array('category'=>$yfeatures, 'user'=>$list);
		
		$list = array_merge($list, $yfeatures);
		
		return $list;
	}
	
	public function getProductAccompany($productId) {
		$sql = $this->getProductsBasicQuery(false, array("LEFT JOIN tp_product_crossselling as pc ON pc.product_id = {$productId}"));
		$sql .= " AND pc.client_id = {$this->clientId} AND pc.shop_id = {$this->shopId} AND p.product_id = pc.product_id_sell";
		
		//echo $sql;
		$this->db->query($sql);
		$this->db->filter('url', $this->filters['product_url']);
		$this->db->filter('price', $this->filters['price']);
		$this->db->filter('old_price', $this->filters['price']);
		$this->db->filter('sales_summ', $this->filters['price']);
		$this->db->get_rows();
		//echo $sql;
		return $this->db->rows;
	}
	
	public function getProductComments($productId, $start=0, $limit=10) {
		$productId = intval($productId);
		$start = intval($start);
		$limit = intval($limit);
		
		$sql = "SELECT
					SQL_CALC_FOUND_ROWS
					pc.id, pc.date, pc.rating, pc.name, pc.email, pc.comment
				FROM
					tp_product_comment as pc
				WHERE
					pc.client_id = {$this->clientId} AND
					pc.shop_id = {$this->shopId} AND
					pc.product_id = {$productId} 
				LIMIT {$start},{$limit} ";

		$this->db->query($sql);
		$this->db->add_fields_deform(array('comment'));
		$this->db->add_fields_func(array('nl2br'));
		$this->db->get_rows();

		return $this->db->rows;
	}
	
	public function addComment($productId, $data) {
		$productId = intval($productId);
		if(!$productId || !$this->checkAccessToProduct($productId)) return false;
		
		$query = array(
			'client_id'=> $this->clientId,
			'shop_id'=> $this->shopId,
			'product_id'=> $productId,
			'name'=> htmlspecialchars(mysql::str($data['name'])),
			'email'=> htmlspecialchars(mysql::str($data['email'])),
			'date'=>time(),
			'rating'=> (intval($data['rating']) > 5)? 5 : intval($data['rating']),
			'comment'=> htmlspecialchars(mysql::str($data['text']))
		);
		
		$this->db->autoupdate()->table('tp_product_comment')->data(array($query));
		$this->db->execute();
		
		return $this->db->insert_id;
	}
	
	public function getCategories($parent_id, $current_id = 0, $order='default',$tree=false,$showHidden=false, $limit = false, $submenuLevel = false) {
		$parent_id = intval($parent_id);
		$orderPossible = array('order'=>'g.order','name'=>'g.name', 'id'=>'g.id', 'tree'=>'g.parrent_id, g.id', 'default'=>'g.order');
		$orderBy = (isset($orderPossible[$order]))? $orderPossible[$order] : $orderPossible['default'];
		$current_id = intval($current_id);
		$submenuLevel = intval($submenuLevel);

		if(!empty($this->localCache['category']) && !empty($this->localCache['category'][$parent_id.':'.$orderBy.':'.$showHidden])) {
		    $list = $this->localCache['category'][$parent_id.':'.$orderBy.':'.$showHidden];
		} else {
		    $sql = "SELECT
            		    g.group_id as id,
            		    g.parrent_id,
            		    g.product_category,
            		    g.name,
            		    g.description,
            		    g.url_big,
            		    g.url_preview,
						g.ext_char1 as icon,
            		    g.rewrite_id,
            		    '' as url,
            		    get_rewrite(g.rewrite_id) as rewrite,
            		    (SELECT COUNT(*) FROM tp_product_group as g2 WHERE g2.shop_id = {$this->shopId} AND g2.client_id = {$this->clientId} AND g2.parrent_id = g.group_id) as childs
    		      FROM
    		           tp_product_group as g
    		      WHERE
    		           g.shop_id = {$this->shopId} AND
    		           g.client_id = {$this->clientId} AND
    		           g.parrent_id = {$parent_id}";
    		    
		    if(!$showHidden) {
		      $sql .= " AND hidden != 1";
		    }
    		    
    		$sql .= " ORDER BY {$orderBy}";
    		    
			if(is_array($limit)){
				$sql .= ' LIMIT ' . intval($limit[0]) . ', ' . intval($limit[1]);
			} else {
				if(intval($limit)) {
					$sql .= ' LIMIT ' . intval($limit);
				}
			}

    		$this->db->query($sql);
    		$this->db->filter('url', $this->filters['category_url']);
    		$this->db->get_rows(false, 'id');
    		$list = $this->db->rows;
    		$this->localCache['category'][$parent_id.':'.$orderBy.':'.$showHidden] = $list;
		}
		

		// get opened tree
	
		if($current_id) {
		    if(isset($list[$current_id])) {			
				$list[$current_id]['sub'] = $this->getCategories($current_id,false,$order, $tree, $showHidden, $submenuLevel);
				if($submenuLevel) {
    				foreach($list as &$item) {
    				    //if($item['id'] == $current_id) continue;
    				    if(!empty($item['childs']) && $item['childs']){
    				        $item['childs'] = $this->getCategories($item['id'],false,$order);
    				    }
    				}
				}
			} else {
				// get tree up
			    if($submenuLevel) {
			        
			        foreach($list as &$em) {
			            if(!empty($em['childs']) && $em['childs']){
			                $em['childs'] = $this->getCategories($em['id'],false,$order);
			            }
			        }
			    }
			    
				$uplist = $this->getGroupsTreeUp($current_id);
				$sublink = false;
				
				foreach($uplist as $item) {
				    
					if(!$sublink && isset($list[$item['id']])) {
						if(!isset($list[$item['id']]['sub'])) {
							$list[$item['id']]['sub'] = array();
						}

						if($submenuLevel) {
						    $list[$item['id']]['childs'] = $this->getCategories($item['id'],false,$order);
						}

						$list[$item['id']]['sub'] = $this->getCategories($item['id'],false,$order, $tree, $showHidden, $submenuLevel);
						$sublink = &$list[$item['id']]['sub'];
					} else {
						if(isset($sublink[$item['id']])) {
							if(!isset($sublink[$item['id']]['sub'])) {
								$sublink[$item['id']]['sub'] = array();
							}
							
														
							$sublink[$item['id']]['sub'] = $this->getCategories($item['id'],false,$order, $tree, $showHidden, $submenuLevel);
							$sublink = &$sublink[$item['id']]['sub'];
						}
					}
				}
			}
		} else {

		    
		    if($submenuLevel) {
		        foreach($list as &$item) {
		            if(!empty($item['childs']) && $item['childs']) {
		                $item['childs'] = $this->getCategories($item['id'],false,$order);         
		            }
		        }
		    }
		} 


		
		
		return $list;
	}
	
	public function getMenuTree($currentId, $order='default') {
	    $orderPossible = array('order'=>'g.order','name'=>'g.name', 'id'=>'g.id', 'tree'=>'g.parrent_id, g.id', 'default'=>'g.order');
	    $orderBy = (isset($orderPossible[$order]))? $orderPossible[$order] : $orderPossible['default'];
	    $currentId = intval($currentId);
	    
	    $sql = "SELECT
	               g.group_id as id,
            	   g.parrent_id,
            	   g.product_category,
            	   g.name,
            	   g.description,
            	   g.url_big,
            	   g.url_preview,
            	   g.rewrite_id,
            	   '' as url,
            	   get_rewrite(g.rewrite_id) as rewrite,
            	   (SELECT EXISTS FROM tp_product_group as g2 WHERE g2.shop_id = {$this->shopId} AND g2.client_id = {$this->clientId} AND g2.parrent_id = g.group_id) as childs
	           FROM
	               tp_product_group as g
	           WHERE
	               g.shop_id = {$this->shopId} AND
	               g.client_id = {$this->clientId} 
	               ";
	    
	    $sql .= " ORDER BY g.parrent_id ASC, {$orderBy}";
	    
	    $this->db->query($sql);
	    $this->db->filter('url', $this->filters['category_url']);
	    $this->db->get_rows(false, 'id');
	    $list = $this->db->rows;
	    
	    return $list;
	}
	
	public function getGroupsTreeUp($lowerGroupId, $topGroupId=0) {
		$iterationLimit = 10;
		$result = array();
		$current = $lowerGroupId;
	
		for($i=0; $i <= $iterationLimit; $i++) {
			$item = $this->getGroupInfoShort($current);
			if(!$item || !count($item)) break;
			$item['is_last'] = false;
			$result[] = $item;
			if($item['parrent_id'] == $topGroupId) break;
			$current = $item['parrent_id'];
		}
	
		$result = array_reverse($result);
		$result[count($result)-1]['is_last'] = true;
		
		return $result;
	}
	
	public function getGroupInfoShort($groupId) {
		$sql = "SELECT
					group_id as id,
					parrent_id,
					name,
					url_preview as image,
					get_rewrite(rewrite_id) as rewrite,
					get_rewrite(rewrite_id) as url,
					ext_char1
				FROM
					tp_product_group
				WHERE
					group_id = {$groupId} AND
					shop_id = {$this->shopId} AND
					client_id = {$this->clientId}";
		
		$this->db->query($sql);
		$this->db->filter('url', $this->filters['category_url']);
		$this->db->get_rows(1);
		
		return $this->db->rows;
	}
	
	public function getMinPrice($categoryId = false) {
		$sql = "SELECT MIN(price) as price FROM tp_product WHERE shop_id = {$this->shopId} AND client_id = {$this->clientId} AND price > 0 AND sales = 0";
				
		$this->db->query($sql);
		return $this->db->get_field();
	}
	
	public function getMaxPrice($categoryId = false) {
		$sql = "SELECT MAX(price) as aprice FROM tp_product WHERE shop_id = {$this->shopId} AND client_id = {$this->clientId} AND sales = 0";
		$this->db->query($sql);
	
		return $this->db->get_field();
	}
	
	public function getFeaturesForFilter($features = false) {
		$sql = "SELECT id, name FROM tp_product_feature WHERE shop_id = {$this->shopId} AND client_id = {$this->clientId}";
		
		$id = array();
		if($features) {
			foreach($features as $item) {
				$item = intval($item);
				if(!$item) continue;
				$id[] = $item;
			}
			
			if(count($id) > 0) {
				$sql .= ' AND id IN('.implode(',',$id).')';
			}
		}
		
		$this->db->query($sql);
		$this->db->get_rows();
		$features = $this->db->rows;
		
		$sql = "SELECT DISTINCT(value), feature_id FROM tp_product_to_feature WHERE shop_id = {$this->shopId} AND client_id = {$this->clientId}";
		if(count($id) > 0) {
			$sql .= ' AND feature_id IN('.implode(',',$id).')';
		}
		
		$this->db->query($sql);
		$this->db->get_rows(false, 'feature_id');
		$fvariants = $this->db->rows;
		
		foreach($features as &$item) {
			$item['variant'] = $fvariants[$item['id']];
		}
		
		return $features;
	}
	
	public function getVendorInfo($vendorId) {
	    $vendorId = intval($vendorId);
	    
	    $sql = "SELECT * FROM tp_vendors WHERE shop_id = {$this->shopId} AND client_id = {$this->clientId} AND id = {$vendorId}";
	    $this->db->query($sql);
	    $this->db->get_rows(1);
	    
	    return $this->db->rows;
	}
	
	
	private function checkAccessToProduct($productId) {
		$productId = intval($productId);
		if(!$productId) return false;
	
		$sql = "SELECT COUNT(*) FROM tp_product WHERE product_id = {$productId} AND shop_id = {$this->shopId} LIMIT 1";
		$this->db->query($sql);
	
		return (intval($this->db->get_field())> 0)? true : false;
	}
	
	public static function makePrice($price) {
		//return parse_rub($price);
		return intval($price).' руб';
	}

	public function authUser($login, $pass, $byId = false) {
	    $sql = "SELECT * FROM tp_user WHERE client_id = {$this->clientId} AND shop_id = {$this->shopId} ";
	    $login = mysql::str($login);
	    $pass = mysql::str($pass);
	    
	    if(!$byId) {
	        $sql .= " AND login = '{$login}'  AND password = '{$pass}' ";
	    } else {
	        $byId = intval($byId);
	        $sql .= " AND user_id = {$byId} ";
	    }
	    
	    $this->db->query($sql);
	    $this->db->get_rows(1);
	    
	    if(empty($this->db->rows)) return false;
	    
	    $user = $this->db->rows;
	    unset($user['password']);
	    
	    
	    $this->updateUserInfo($user);
	    
	    return true;
	}
	
	public function updateUserInfo($user = false) {
	    if(!$user) {
	        if(!$this->userId) return false;
	        $sql = "SELECT * FROM tp_user WHERE client_id = {$this->clientId} AND shop_id = {$this->shopId} AND user_id = {$this->userId}";
	        $this->db->query($sql);
	        $this->db->get_rows(1);
	        $user = $this->db->rows;
	        
	        if(empty($user)) return false;
	        unset($user['password']);
	    }

	    $user['contact'] = $this->getContactInfo($user['crm_contact_id']);
	    $this->userInfo = $user;
	    $this->userId = $this->userInfo['user_id'];
	    $this->auth = true;
	    $_SESSION['shopUserId'] = $this->userId;
	    $_SESSION['shopUserInfo'] = $this->userInfo;
	    $this->tpl->assign('userInfo', $user);
	    
	    return true;
	}
	
	public function checkAuthUser() {
	    if(!isset($_SESSION['shopUserId'])) return false;
	    $this->authUser('', false, $_SESSION['shopUserId']);
	}
	
	public function addUser($type, $info) {
	    // main user
	    $tp_user = array(
	        'client_id'=>$this->clientId,
	        'shop_id'=>$this->shopId,
	        'login'=>mysql::str($info['email']),
	        'email'=>mysql::str($info['email']),
	        'reg_date'=>time(),
	        'token'=>md5(time().microtime()),
	        'approved'=>1,
	        'password'=>md5(md5($info['password'])),
	        'crm_contact_id'=>0
	    );
	    
	    $this->db->autoupdate()->table('tp_user')->data(array($tp_user));
	    $this->db->execute();
	    $userId = $this->db->insert_id;
	    $companyId = 0;
	    $responseUserId = 0; 
	    
	    // select next manager
	    $sql = "SELECT user_id FROM a_users WHERE group_id = 2";
	    $this->db->query($sql);
	    $this->db->get_rows();
	    $managers = $this->db->rows;
	    if(!empty($managers) && is_array($managers)) {
	        $responseUserId = $managers[array_rand($managers)]['user_id'];
	    }
	    
	    // crm company
	    if($type == 2) {
	        $crm_companies = array(
	            'client_id'=>$this->clientId,
	            'user_id'=>$userId,
	            'date'=>time(),
	            'source_id'=>1,
	            'name'=>mysql::str($info['company']),
	            'phone'=>mysql::str($info['phone']),
	            'mail'=>mysql::str($info['email']),
	            'address'=>mysql::str($info['company_info']['address']),
	            'country_id'=>7,
	            'inn'=>mysql::str($info['company_info']['inn']),
	            'kpp'=>mysql::str($info['company_info']['kpp']),
	            'ogrn'=>mysql::str($info['company_info']['ogrn']),
	            'bank'=>mysql::str($info['company_info']['bank']),
	            'bill'=>mysql::str($info['company_info']['bill']),
	            'bik'=>mysql::str($info['company_info']['bik']),
	            'kor'=>mysql::str($info['company_info']['kor'])
	        );
	        
	        $this->db->autoupdate()->table('crm_companies')->data(array($crm_companies));
	        $this->db->execute();
	        $companyId = $this->db->insert_id;
	        
	    }
	    
	    // crm contact
	    $crm_contacts = array(
	        'client_id'=>$this->clientId,
	        'user_id'=>$userId,
	        'date'=>time(),
	        'company_id'=>$companyId,
	        'name'=>mysql::str($info['name']),
	        'surname'=>mysql::str($info['surname']),
	        'lastname'=>mysql::str($info['lastname']),
	        'responsible_user_id'=>$responseUserId,
	        'source_id'=>7
	    );
	    
	    $this->db->autoupdate()->table('crm_contacts')->data(array($crm_contacts));
	    $this->db->execute();
	    $contactId = $this->db->insert_id;
	    
	    $tp_user = array(
	        'client_id'=>$this->clientId,
	        'shop_id'=>$this->shopId,
	        'user_id'=>$userId,
	        'crm_contact_id'=>$contactId
	    );
	    
	    $this->db->autoupdate()->table('tp_user')->data(array($tp_user))->primary('client_id', 'shop_id', 'user_id');
	    $this->db->execute();

	    // crm contact email
	    $crm_contacts_email = array(
	        'client_id'=>$this->clientId,
	        'contact_id'=>$contactId,
	        'email_type'=>1,
	        'email'=>mysql::str($info['email'])
	    );
	    
	    $this->db->autoupdate()->table('crm_contacts_email')->data(array($crm_contacts_email));
	    $this->db->execute();
	   
	    // crm contact phone
	    $crm_contacts_phones = array(
	        'client_id'=>$this->clientId,
	        'contact_id'=>$contactId,
	        'phone_type'=>phoneType($info['phone']),
	        'phone'=>mysql::str($info['phone'])
	    );
	     
	    $this->db->autoupdate()->table('crm_contacts_phones')->data(array($crm_contacts_phones));
	    $this->db->execute();
	     
	    // crm contact flag
	    $crm_contacts_special_flags = array(
	        'client_id'=>$this->clientId,
	        'contact_id'=>$contactId,
	        'flag_id'=>($info['news'])? 1 : 0
	    );
	    
	    $this->db->autoupdate()->table('crm_contacts_special_flags')->data(array($crm_contacts_special_flags));
	    $this->db->execute();
	    
	    return array(
	        'userId'=>$userId,
	        'contactId'=>$contactId
	    );
	}
	
	public function addUserAddress($id, $data) {
	    $id = intval($id);
	    
	    if($id) {
	        $sql = "SELECT COUNT(*) FROM tp_user_address WHERE client_id = {$this->clientId} AND shop_id = {$this->shopId} AND user_id = {$this->userId} AND address_id = {$id} ";
	        $this->db->query($sql);
	        if(!intval($this->db->get_field())) $id = false;
	    }
	    
	    $isMain = false;
	    
	    if(!$id) {
	        $sql = "SELECT COUNT(*) FROM tp_user_address  WHERE client_id = {$this->clientId} AND shop_id = {$this->shopId} AND user_id = {$this->userId}";
	        $this->db->query($sql);
	        if(!intval($this->db->get_field())) $isMain = true;
	    }
	    
	    $query = array(
	        'client_id'=>$this->clientId,
	        'shop_id'=>$this->shopId,
	        'user_id'=>$this->userId,
	        'zip'=>preg_replace('/[^0-9]/', '', $data['zip']),
	        'region'=>mysql::str($data['region']),
	        'city'=>mysql::str($data['city']),
	        'street'=>mysql::str($data['street']),
	        'house'=>mysql::str($data['house']),
	        'building'=>mysql::str($data['building']),
	        'flat'=>mysql::str($data['flat']),
	        'address_id'=>$id
	    );
	    
	    if($isMain) {
	        $query['is_main'] = 1;
	    }
	    
	    $this->db->autoupdate()->table('tp_user_address')->data(array($query))->primary('client_id', 'shop_id', 'user_id', 'address_id');
	    $this->db->execute();
	    
	    $id = ($id)? $id : $this->db->insert_id;
	    
	    return $id;
	}
	
	public function getClientAdresses($id = false, $mainAddress = false) {
	    $id = intval($id);
	    
	    $sql = "SELECT * FROM tp_user_address WHERE client_id = {$this->clientId} AND shop_id = {$this->shopId} AND user_id = {$this->userId} ";
	    if($id) {
	        $sql .= " AND address_id = {$id} ";
	    } 
	    
	    $sql .= " ORDER BY is_main DESC";
	    
	    if($mainAddress) {
	        $sql .= " LIMIT 1";
	    }    
	    
	    $this->db->query($sql);
	    $this->db->get_rows(($id > 0 || $mainAddress));
	    	    
	    return $this->db->rows;
	}
	
	public function getCartBox($cartId) {
	    $cartId = intval($cartId);
	    $sql = "SELECT * FROM tp_user_cartbox WHERE client_id = {$this->clientId} AND shop_id = {$this->shopId} AND cart_id = {$cartId}";
	    $this->db->query($sql);
	    $this->db->get_rows(1);

	    if(empty($this->db->rows)) return false;
	    
	    $cartbox = $this->db->rows;
	    $sql = "SELECT 
	               cb.product_name as title, 
	               cb.price, 
	               cb.price as price_float,
	               cb.count, 
	               cb.summ,
	               IF(
	                   (SELECT p.parent_id != 0 FROM tp_product as p WHERE p.product_id = cb.product_id AND p.client_id = {$this->clientId} AND p.shop_id = {$this->shopId}),
	                   (SELECT p2.rewrite_id FROM tp_product as p LEFT JOIN tp_product as p2 ON p2.product_id = p.parent_id AND p2.client_id = {$this->clientId} AND p2.shop_id = {$this->shopId} WHERE p.product_id = cb.product_id AND p.client_id = {$this->clientId} AND p.shop_id = {$this->shopId}),
	                   (SELECT p.rewrite_id  FROM tp_product as p WHERE p.product_id = cb.product_id AND p.client_id = {$this->clientId} AND p.shop_id = {$this->shopId})
	               ) as rewrite_id,
	               
	               IF(
	                   (SELECT p.parent_id != 0 FROM tp_product as p WHERE p.product_id = cb.product_id AND p.client_id = {$this->clientId} AND p.shop_id = {$this->shopId}),
	                   (SELECT p.parent_id FROM tp_product as p WHERE p.product_id = cb.product_id AND p.client_id = {$this->clientId} AND p.shop_id = {$this->shopId}),
	                   (cb.product_id)
	               ) as product_id,
	               
	               '' as url
	           FROM 
	               tp_user_cartbox_item as cb
	                   LEFT JOIN tp_product as p ON p.product_id = cb.product_id AND p.client_id = {$this->clientId} AND p.shop_id = {$this->shopId}
	           WHERE 
	               cb.client_id = {$this->clientId} AND 
	               cb.shop_id = {$this->shopId} AND 
	               cb.cart_id = {$cartId}";

	    $this->db->query($sql);
	    $this->db->filter('url', $this->filters['product_url']);
	    $this->db->filter('price', $this->filters['price']);
	    $this->db->get_rows();
	 
	    if(empty($this->db->rows)) return false;
	    
	    $products = array();
	    $cartbox['sum'] = 0;
	    $cartbox['qt'] = 0;
	    
	    foreach($this->db->rows as $item) {
	        $cartbox['sum'] += $item['summ'];
	        $cartbox['qt'] += $item['count'];
	        $products[] = $item;
	    }
	    
	    $cartbox['list'] = $products;
	    $cartbox['count'] = count($products);
	    $cartbox['summ_float'] = $cartbox['sum'];
	    $cartbox['sum'] = ($cartbox['sum']).' руб.';
	    
	    return $cartbox;
	}
	
	private function addOrderEvent($orderId, $event, $comment= '') {
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
	}
	
	public function getUserAddress($addressId, $short = false) {
	    $addressId = intval($addressId);
	    $sql = "SELECT * FROM tp_user_address WHERE client_id = {$this->clientId} AND shop_id = {$this->shopId} AND user_id = {$this->userId} AND address_id = {$addressId}";
	    $this->db->query($sql);
	    $this->db->get_rows(1);
	    $data = $this->db->rows;
	    
	    if(empty($data)) return false;
	    
	    if($short) {
	        $address = array();
	        if(!empty($data['zip'])) $address[] = $data['zip'];
	        if(!empty($data['region'])) $address[] = $data['region'];
	        if(!empty($data['city'])) $address[] = 'г.'.$data['city'];
	        if(!empty($data['street'])) $address[] = $data['street'];
	        $tmp = 'д. '.$data['house'];
	        $tmp .= (!empty($data['building']))? '/'.$data['building']:'';
	        $address[] = $tmp;
	        if(!empty($data['flat'])) $address[] = 'кв. '.$data['flat'];
	        return implode(', ', $address);
	    }
	    
	    return $data;
	}
	
	public function getProductsNameAndRewrite($group) {
		
		if(is_array($group)){
			$group = implode(',',$group);
		} else {
			$group = intval($group);
		}

		$sql = "(SELECT
					p.title,
					p.group_id,
					get_rewrite(p.rewrite_id) rewrite
				FROM tp_product p
				WHERE
					p.group_id in ({$group})
					and p.shop_id = {$this->shopId}
					and p.client_id = {$this->clientId}
				ORDER BY p.group_id)
				
				UNION
				
				(SELECT
					p.title,
					pg.group_id,
					get_rewrite(p.rewrite_id) rewrite
				FROM tp_product p
                	JOIN tp_product_to_group pg on p.product_id = pg.product_id
				WHERE
					pg.group_id in ({$group})
					and p.shop_id = {$this->shopId}
					and p.client_id = {$this->clientId}
				ORDER BY pg.group_id)
				";

		$this->db->query($sql);
		
		$this->db->get_rows();
		//$this->db->get_rows(false, 'group_id');
		
		$prods = $this->db->rows; 
		
		return $prods;
	}

		public function getProductsCount() {

		$sql = "SELECT
					max(products_count) as products_count,
					group_id
				FROM

				(
					(SELECT
						count(title) as products_count,
						parrent_id as group_id
					FROM

					(
						(SELECT
							p.title,
							p.group_id,
							pgr.parrent_id
						FROM tp_product p
							JOIN tp_product_group pgr on pgr.group_id = p.group_id
						WHERE
							p.group_id not in (0, 31, 32)
							and p.shop_id = {$this->shopId}
							and p.client_id = {$this->clientId})
						
						UNION
						
						(SELECT
							p.title,
							pg.group_id,
							pgr.parrent_id
						FROM tp_product p
							JOIN tp_product_to_group pg on p.product_id = pg.product_id
							JOIN tp_product_group pgr on pgr.group_id = pg.group_id
						WHERE
							pg.group_id not in (0, 31, 32)
							and p.shop_id = {$this->shopId}
							and p.client_id = {$this->clientId})
						
					) as prods

					where parrent_id != 31 and parrent_id != 32

					GROUP BY parrent_id
					ORDER BY parrent_id
					)

					UNION

					(SELECT
						count(title) as products_count,
						group_id
					FROM

					(
						(SELECT
							p.title,
							p.group_id,
							pgr.parrent_id
						FROM tp_product p
							JOIN tp_product_group pgr on pgr.group_id = p.group_id
						WHERE
							p.group_id not in (0)
							and pgr.parrent_id = 31
							and p.shop_id = {$this->shopId}
							and p.client_id = {$this->clientId})
						
						UNION
						
						(SELECT
							p.title,
							pg.group_id,
							pgr.parrent_id
						FROM tp_product p
							JOIN tp_product_to_group pg on p.product_id = pg.product_id
							JOIN tp_product_group pgr on pgr.group_id = pg.group_id
						WHERE
							pg.group_id not in (0)
							and pgr.parrent_id = 31
							and p.shop_id = {$this->shopId}
							and p.client_id = {$this->clientId})
							
					) as prods2

					WHERE parrent_id = 31

					GROUP BY group_id
					ORDER BY group_id
					
					)
						
				) as prods3

				GROUP BY group_id
				ORDER BY group_id";

		$this->db->query($sql);
		$this->db->get_rows(false, 'group_id');
		$num = $this->db->rows; 
		
		return $num;
	}

}

function phoneType($phone) {
    if((substr($phone,0,2) == '79' || substr($phone,0,2) == '89') && strlen($phone) == 11) return 1; // мобильный
    if(strlen($phone) == 10 && substr($phone, 0,1) == 9) return 1; // мобильный
    if(strlen($phone) == 10 && substr($phone, 0,1) == 4) return 3; // домашний
        
    return 5;
}

function h_date($date){
	return ($date)? date('d-m-Y - H:i',$date) : 'не установлено';
}





?>