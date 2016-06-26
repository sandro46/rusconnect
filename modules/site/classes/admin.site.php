<?php 

class admin_site extends main_module {
	
	public function changeTheme($themeId) {
		$themeId = intval($themeId);
		if(!$themeId) return false;
		
		$this->makeStatic($this->shopId, $themeId, true);
		unset($_SESSION['cache:clientInstance']);
		
		return true;
	}
	
	public function getThemeSettingFormData() {
	    $theme = $this->getThemeInfo();
	    $path = CORE_PATH.str_replace('/templates/', 'static/', $theme['static_path']);
	    $settingFile = 'settings.php';
	     
	    if(file_exists($path.$settingFile)) {
	        $data = include $path.$settingFile;
	        if(!is_array($data)) return false;
	        
	        return $data;
	    }
	    
	    return false;
	}
	
	public function getThemeSetting() {
	    $theme = $this->getThemeInfo();
        $sql = "SELECT data FROM site_settings WHERE client_id = {$this->clientId} AND site_id = {$this->shopId} AND name = 'theme-setting:{$theme['name']}'";
        $this->db->query($sql);
        $data = json_decode($this->db->get_field(), true);
        
        return (empty($data))? array() : $data;
	}
	
	public function getThemeSettingFields() {
	    $data = $this->getThemeSettingFormData();
	    if(!$data) return false;
	    
	    $setting = $this->getThemeSetting();
	        
        foreach($data as $key=>$item) {
            if($key === 'groups') {
                continue;
            }
                
            if(!is_array($item) || !isset($item['fields']) || !is_array($item['fields'])) {
                continue;
            }
                    
            $name = $item['alias'];
                
            if(isset($item['multiple']) && $item['multiple']) {
                if(isset($setting[$name]) && is_array($setting[$name]) && count($setting[$name])) {
                    $data[$key]['data'] = $setting[$name];
                } else {
                    $data[$key]['data'] = false;
                }
                
                continue;
            }
                
            foreach($item['fields'] as $fieldIndex=>$fieldData) {
                $defaultValue = (isset($fieldData['default']))? $fieldData['default'] : '';
                $data[$key]['fields'][$fieldIndex]['value'] = (isset($setting[$name]) && isset($setting[$name][$fieldData['alias']]))? $setting[$name][$fieldData['alias']] : $defaultValue;
            }
        }
	        
        return $data;
	}
	
	public function deleteShop($siteId) {
		$siteId = intval($siteId);
		if(!$this->checkAccessToSite($siteId)) return false;
			
		$tables = array(
				// shop data
				'tp_stores',
				'tp_vendors',
				'tp_product',
				'tp_product_comment',
				'tp_product_crossselling',
				'tp_product_dimensions',
				'tp_product_feature',
				'tp_product_feature_variants',
				'tp_product_group',
				'tp_product_img',
				'tp_product_store',
				'tp_product_to_feature',
				'tp_product_to_feed',
				'tp_product_to_group',
				'tp_product_to_parameters',
				'tp_product_to_ym_feature',
				'tp_shop_to_modules',
				'tp_shop_user_feedback',
				'site_pages',
				'site_menu',
				'tp_order',
				'mcms_rewrite',
				
				// system data
				'mcms_sites_alias',
				'mcms_sites_modules',
				'mcms_themes',
				'mcms_tmpl',
				'site_settings',
				'tp_shop'
		);
		
		$keyVariable = array(
				'client_id'=>$this->clientId,
				'shop_id'=>$siteId,
				'site_id'=>$siteId,
				'id_site'=>$siteId
		);
		
		foreach($tables as $table) {
			$fields = $this->getTableFieldsList($table);
			if(!$fields) continue;
		
			$where = array('1');
		
			foreach($fields as $key) {
				if(isset($keyVariable[$key])) {
					$where[] = "`$key` = {$keyVariable[$key]}";
				}
			}
		
			$sql = 'DELETE  FROM `'.$table.'`  WHERE '.implode($where, ' AND ');
		
			$this->db->query($sql);
			//echo $sql."\n";
				
		}
		
		$sql = "DELETE FROM mcms_sites WHERE id = {$siteId}";
		//echo $sql."\n";
		$this->db->query($sql);
	}
	
	
	public function getSiteInfo($siteId) {
		$siteId = intval($siteId);
		
		$sql = "SELECT * FROM tp_shop WHERE client_id = {$this->clientId} AND shop_id = {$siteId}";
		$this->db->query($sql);
		$this->db->get_rows(1);
		
		$shopInfo = $this->db->rows;
		
		$sql = "SELECT * FROM site_settings WHERE client_id = {$this->clientId} AND site_id = {$siteId}";
		$this->db->query($sql);
		$this->db->get_rows();
		
		$integration = array();
		
		foreach($this->db->rows as $item) {
			$integration[$item['name']] = json_decode($item['data'], true);
		}
		
		$sql = "SELECT * FROM mcms_sites WHERE id = {$siteId}";
		$this->db->query($sql);
		$this->db->get_rows(1);
		
		$domains = $this->db->rows;
		
		$sql = "SELECT * FROM mcms_sites_alias WHERE id_site = {$siteId}";
		$this->db->query($sql);
		$this->db->get_rows();
		$domains['alias'] = $this->db->rows;
		
		$shopInfo['integration'] = $integration;
		$shopInfo['domains'] = $domains;
		
		$sql = "SELECT 
					sm.*, 
					(SELECT stm1.settings FROM tp_shop_to_modules as stm1 WHERE stm1.client_id = {$this->clientId} AND stm1.shop_id = {$siteId} AND stm1.module_id = sm.id ) as settings, 
					(SELECT COUNT(*) FROM tp_shop_to_modules as stm WHERE stm.client_id = {$this->clientId} AND stm.shop_id = {$siteId} AND stm.module_id = sm.id ) as used 
				FROM 
					tp_shop_modules as sm 
				WHERE 1";
		$this->db->query($sql);
		$this->db->get_rows();
		$shopInfo['modules'] = $this->db->rows;
		
		foreach($shopInfo['modules'] as $k=>$item) {
			if(strlen($item['settings'])) {
				$shopInfo['modules'][$k]['settings'] = json_decode($item['settings'], true);
			}
		}
				
		return $shopInfo; 
	}
	
	public function updateSite($info) {
		if(empty($info['site_id'])) return -1;
		$siteId = intval($info['site_id']);
		if(!$this->checkAccessToSite($siteId)) return -2;
		
		// Общие настройки + seo
		$query = array(
			'client_id'=>$this->clientId,
			'shop_id'=>$siteId,
			'name'=>mysql::str($info['name']),
			'phone'=>mysql::str($info['phone']),
			'email'=>mysql::str($info['email']),
			'logo'=>(!empty($info['logo']))? mysql::str($info['logo']) : '',
			'meta_title_prefix'=>mysql::str($info['meta_title_prefix']),
			'meta_title'=>mysql::str($info['meta_title']),
			'meta_description'=>mysql::str($info['meta_description']),
			'meta_keywords'=>mysql::str($info['meta_keywords'])
		);
		
		$this->db->autoupdate()->table('tp_shop')->data(array($query))->primary('shop_id', 'client_id');
		$this->db->execute();
		
		// Интеграция
		$query = array();
		$settingData = array();
		
		foreach($info['integration'] as $name=>$item) {
			$settingData[mysql::str($name)] = $item;
		}
		
		$query[] = array(
				'client_id'=>$this->clientId,
				'site_id'=>$siteId,
				'name'=>'integrations',
				'data'=>json_encode($settingData)
		);
	
		if(count($settingData) > 0) {
			$this->db->autoupdate()->table('site_settings')->data($query)->primary('client_id', 'site_id', 'name');
			$this->db->execute();
		}
		
		// Модули
		foreach($info['modules'] as $moduleId=>$moduleInfo) {
			if(!$moduleInfo['enable']) {
				$moduleId = intval($moduleId);
				$sql = "DELETE FROM tp_shop_to_modules WHERE shop_id = {$siteId} AND client_id = {$this->clientId} AND module_id = {$moduleId}";
				$this->db->query($sql);
			} else {
				unset($moduleInfo['enable']);
				$query = array(
					'shop_id'=>$siteId,
					'client_id'=>$this->clientId,
					'module_id'=>$moduleId,
					'settings'=>json_encode($moduleInfo)
				);
				
				$this->db->autoupdate()->table('tp_shop_to_modules')->data(array($query))->primary('client_id', 'shop_id', 'module_id');
				$this->db->execute();
			}
		}
		
		return true;
	}
	
	public function addMirror($siteId, $domain) {
		$siteId = intval($siteId);
		if(!$this->checkAccessToSite($siteId)) return -1;
		$domain = preg_replace("/^[0-9a-zA-Zа-яА-Я\-\.]/",'', $domain);
		
		$sql = "SELECT COUNT(*) FROM mcms_sites WHERE server_name = '{$domain}'";
		$this->db->query($sql);
		if(intval($this->db->get_field())>0) return -2;
		
		$sql = "SELECT COUNT(*) FROM mcms_sites_alias WHERE server_name = '{$domain}'";
		$this->db->query($sql);
		if(intval($this->db->get_field())>0) return -3;
		
		$this->addNginxSiteAlias($domain);
		$query = array('id_site'=>$siteId, 'server_name'=>$domain, 'server_port'=>80);
		$this->db->autoupdate()->table('mcms_sites_alias')->data(array($query));
		$this->db->execute();
		
		return true;
	}
	
	public function removeMirror($siteId, $domain) {
		$siteId = intval($siteId);
		if(!$this->checkAccessToSite($siteId)) return -1;
		
		$sql = "DELETE FROM mcms_sites WHERE server_name = '{$domain}' AND id = {$siteId}";
		$this->db->query($sql);
		
		$sql = "DELETE FROM mcms_sites_alias WHERE server_name = '{$domain}' AND id_site = {$siteId}";
		$this->db->query($sql);
				
		$this->removeNginxSiteAlias($domain);
	}
	
	public function getSitesList($instance, $start = 0, $limit = 20, $sortBy = 'date', $sortType = 'DESC', $filters = false) {
		$sql = "SELECT SQL_CALC_FOUND_ROWS
					s.shop_id as id,
					s.url,
					s.name,
					s.create_date,
					s.prepaid_to,
					s.status,
					ss.name as status_name,
					s.is_promo,
					s.is_free
				FROM
					tp_shop as s
						LEFT JOIN tp_shop_statuses as ss ON ss.id = s.status
 				WHERE
					s.client_id = {$this->clientId}";
		
		if(isset($filters['id']) && intval($filters['id'])) {
			$sql .= ' s.shop_id = '.intval($filters['id']);
		}
		
		$sql .= " ORDER BY $sortBy $sortType LIMIT $start,$limit";
		$this->db->query($sql);
		
		$this->db->add_fields_deform(array('create_date','prepaid_to'));
		$this->db->add_fields_func(array('datepicker','datepicker'));
		
		if(isset($filters['id']) && intval($filters['id'])) {
			$this->db->get_rows(1);
		} else {
			$this->db->get_rows();
		}
		
		
		return $this->db->rows;
	}
	
	public function getUserNextDomain() {
		$sql = "SELECT COUNT(*) FROM tp_shop WHERE client_id = {$this->clientId}";
		$this->db->query($sql);
		$shopCount = intval($this->db->get_field())+1;
		
		$getRandChars = function($length=6) {
			$charset = 'abcdefghijklmnopqrstuvwxyz';
			$str = '';
			while($length--) {
				$str .= $charset[mt_rand(0, strlen($charset)-1)];
			}
			
			return $str;
		};
		
		$checkSiteExists = function($domain) {
			$sql = "SELECT COUNT(*) FROM mcms_sites WHERE server_name = '{$domain}'";
			$this->db->query($sql);
			if(intval($this->db->get_field())) return true;
			
			$sql = "SELECT COUNT(*) FROM mcms_sites_alias WHERE server_name = '{$domain}'";
			$this->db->query($sql);
			if(intval($this->db->get_field())) return true;
			
			return false;
		};
		
		$maxIterations = 50;
		while($maxIterations--) {
			$domain = $getRandChars().'.ncity.biz';
			if(!$checkSiteExists($domain)) break;
		}
		
		return $domain;
	}
	
	public function getThemeInfo($themeId = 0, $themeName = false) {
		$themeId = intval($themeId);
		
		if($themeId) {
			$sql = "SELECT t.* FROM tp_themes as t WHERE t.id = {$themeId}";
		} elseif($themeName) {
			
		} else {
			$sql = "SELECT t.*, st.static_path FROM tp_themes as t LEFT JOIN mcms_themes as st ON st.id_site = {$this->shopId} WHERE t.name = st.name";
		}
			
		$this->db->query($sql);
		$this->db->add_fields_deform(array('description'));
		$this->db->add_fields_func(array('nl2br'));
		$this->db->get_rows(1);

		return $this->db->rows;
	}
	
	public function getThemesList($instance='simple', $start = 0, $limit = 20, $sortBy = 'id', $sortType = 'DESC', $filters = false) {
		$sql = "SELECT 
					t.*,  
					
					IF(t.enable, 'Активная', 'Отключена') as enable_name,
					IF(t.enable, 'success', 'danger') as enable_label,
				
					IF(t.is_free, 'Бесплатная', 'Платная') as free_name,
					IF(t.is_free, 'success', 'warning') as free_label
				
				
				FROM tp_themes as t WHERE t.enable = 1 ";
		
		
		
		$sql .= " ORDER BY $sortBy $sortType LIMIT $start,$limit";
		
		$this->db->query($sql);
		$this->db->get_rows();
		
		return $this->db->rows;
	}
	
	public function getThemeBlocks() {
		$theme = $this->getThemeInfo();
		$sql = "SELECT
					sb.block_id as id,
					sb.type_id,
					sbt.name as type_name,
					sb.block_name,
					sb.block_title,
					sb.template_name,
					IFNULL((SELECT 
								tpl.source 
						 	FROM 
						 		mcms_tmpl as tpl 
							WHERE 
							 	tpl.id_site = {$this->shopId} AND 
							 	tpl.id_lang = '{$this->core->langId}' AND 
							 	tpl.name_module = '{$this->shopInfo['site_system_name']}' AND 
								tpl.name = sb.template_name AND
								tpl.del = 0		
						), '') as template_html,
					sb.h_position,
					sb.v_position,
					sb.order
				FROM 
					site_blocks as sb
						LEFT JOIN site_blocks_type as sbt ON sbt.id = sb.type_id
				WHERE
					sb.site_id = {$this->shopId} AND 
					sb.theme = '{$theme['name']}' 
				ORDER BY
					sb.h_position,
					sb.order";

		$this->db->query($sql);
		$this->db->get_rows();
		$list = array();
		
		foreach($this->db->rows as $item) {
			if(!isset($list[$item['h_position']])) {
				$list[$item['h_position']] = array();
			}
			
			if(!isset($list[$item['h_position']][$item['v_position']])) {
				$list[$item['h_position']][$item['v_position']] = array();
			}
			
			$list[$item['h_position']][$item['v_position']][] = $item;
		}
		
		return $list;
	}
	
	public function getAllThemeBlocks() {
		$this->db->select()->from('site_blocks_type')->fields('*');
		$this->db->execute();
		$this->db->get_rows();
		
		return $this->db->rows;
	}
	
	public function getThemeGridHtml() {
		$theme = $this->getThemeInfo();
		$path = CORE_PATH."static/theme_sources/{$theme['name']}/";
		
		if(!file_exists($path)) return -1;
		if(!file_exists($path."data/theme.grid.html")) return -2;
		
		return file_get_contents($path."data/theme.grid.html");
	}
	
	public function getPagesListFromParent($parentId, $openeditem = false) {
		$parentId = intval($parentId);
		
		// todo: make order???? mayby...
		$sql = "SELECT 
					s1.page_id as id, 
					s1.parent_id as pid, 
					s1.type, 
					s1.title as name, 
					s1.hidden,
					(SELECT COUNT(*) FROM site_pages as s2 WHERE s2.site_id = {$this->shopId} AND s2.parent_id = s1.page_id) as childs
				FROM 
					site_pages as s1 
				WHERE 
					s1.site_id = {$this->shopId} AND 
					s1.parent_id = {$parentId} 
				ORDER BY 
					s1.order ASC";
		
		$this->db->query($sql);
		$this->db->get_rows();
		$list = array();
		
		foreach($this->db->rows as $item) {
			$item['isLastNode'] = (intval($item['childs'])>0)? true : false;
			$item['children'] = (intval($item['childs'])>0)? array() : false;
			
			$list[] = $item;
		}
		
		
		return $list;
	}
	
	public function getMenuList($onlyRoot = false) {
		$sql = "SELECT
					SQL_CALC_FOUND_ROWS
					m.menu_id as id, m.type_id, mt.name as type_name, mt.icon as type_icon, m.title, m.custom_link, m.hidden, IF(m.hidden, 'hidden', '') as hidden_name,
					m.order, m.parent_id, m.locked
				FROM
					site_menu as m
						LEFT JOIN site_menu_types as mt ON mt.id = m.type_id	
				WHERE
					m.site_id = {$this->shopId} %s
					
				ORDER BY m.order";

		$this->db->query(sprintf($sql, 'AND m.parent_id = 0'));
		$this->db->get_rows(false, 'id');
		$list = $this->db->rows;
		
		if($onlyRoot) return $list;
		
		$this->db->query(sprintf($sql, 'AND m.parent_id != 0'));
		$this->db->get_rows(false, 'id');
		$sub = $this->db->rows;
		
		foreach($sub as $item) {
			if(isset($list[$item['parent_id']])) {
				if(!isset($list[$item['parent_id']]['sub'])) {
					$list[$item['parent_id']]['sub'] = array();
				}
				
				$list[$item['parent_id']]['sub'][] = $item;
			}
		}
		
		return $list;
	}
	
	public function getMenuItemInfo($menuId) {
		$sql = "SELECT
					m.menu_id as id, m.type_id, m.title, m.custom_link, m.hidden, m.order, m.parent_id, m.locked, m.value,
					mt.name as type_name, 
					mt.icon as type_icon,
					IF(m.hidden, 'hidden', '') as hidden_name
				FROM
					site_menu as m
						LEFT JOIN site_menu_types as mt ON mt.id = m.type_id
				WHERE
					m.site_id = {$this->shopId} AND
					m.menu_id = {$menuId}";
		
		$this->db->query($sql);
		$this->db->get_rows(1);
		$info = $this->db->rows;
	
		return $info;
	}
	
	public function changeMenuOrder($order) {
		if(!is_array($order)) return false;
		
		$orders = array();
		
		foreach($order as $parent) {
			$parentId = intval($parent['id']);
			if(!empty($parent['children'])) {
				foreach($parent['children'] as $order=>$item) {
					$order = $order+1;
					$orders[] = array(
						'menu_id'=>$item['id'],
						'site_id'=>$this->shopId,
						'parent_id'=>$parentId,
						'order'=>$order
					);
				}
			}
		}
		
		if(!empty($orders)) {
			$this->db->autoupdate()->table('site_menu')->data($orders)->primary('site_id', 'menu_id');
			$this->db->execute();
		}
	}
	
	public function changeMenuVisible($menuId) {
		$menuId = intval($menuId);
		$menuInfo = $this->getMenuItemInfo($menuId);
		if(!$menuInfo) return -1;
		$newState = ($menuInfo['hidden'])? '0' : '1';
		$sql = "UPDATE site_menu SET hidden = {$newState} WHERE site_id = {$this->shopId} AND menu_id = {$menuId}";
		$this->db->query($sql);
		
		return intval($newState);
	}
	
	public function getCatalogList() {
		$this->lib->loadModuleFiles('shop', 'admin.shop.php');
		$shop = new admin_shop();
		$shop->init();
		
		return $shop->getGroupTreePlain();
	}
		
	public function editMenu($id, $info) {
		$id = intval($id);
				
		$data = array(
			'site_id'=>$this->shopId,
			'hidden'=> (isset($info['visible']) && intval($info['visible']))? 0 : 1,
			'parent_id'=> intval($info['parent_id']),
			'type_id'=> intval($info['type_id']),
			'title'=> mysql::str($info['name'])
		);
		
		if($info['type_id'] == 4) {
			$data['custom_link'] = mysql::str($info['value']);
			$data['value'] = 0;
		} else {
			$data['custom_link'] = '';
			$data['value'] = intval($info['value']);
		}
		
		if($id) {
			$data['menu_id'] = $id;
		} else {
			$sql = "SELECT MAX(`order`) FROM site_menu WHERE parent_id = {$data['parent_id']} AND site_id = {$this->shopId}";
			$data['order'] = intval($this->db->get_field()) + 10;
		}
		
		$this->db->autoupdate()->table('site_menu')->data(array($data))->primary('site_id', 'menu_id');
		$this->db->execute();
		
		return $id || $this->db->insert_id;
	}
	
	public function getPagesTemplates() {
		$sql = "SELECT id_template as id, name, description FROM mcms_tmpl WHERE name_module = 'pages' AND id_site = {$this->shopId} AND del != 1 AND id_lang = {$this->core->langId}";
		
		$this->db->query($sql);
		$this->db->get_rows();
		
		return $this->db->rows;
	}
	
	public function getPageInfo($id) {
		$id = intval($id);
		if(!$id) return false;
		
		$sql = "SELECT
					p.page_id as id,
					p.parent_id,
					p.rewrite_id,
					p.template_id,
					p.type,
					p.childs_order,
					p.special_date,
					p.title,
					p.description,
					p.text,
					p.meta_description,
					p.meta_keywords,
					p.hidden,
					get_rewrite(p.rewrite_id) as url
				FROM
					site_pages as p
				WHERE
					p.site_id = {$this->shopId} AND
					p.page_id = {$id} ";
		
		$this->db->query($sql);
		$this->db->add_fields_deform(array('special_date'));
		$this->db->add_fields_func(array('datepicker'));
		$this->db->get_rows(1);
		
		//echo $sql;
		
		return $this->db->rows;
	}
	
	public function editPage($id, $data) {
		$id = intval($id);
		$types = array('html','text','tpl');
		if(!isset($data['type']) || !in_array($data['type'], $types)) return false;
		$isCreate = false;
		
		$info = array(
			'site_id'=>$this->shopId,
			'template_id'=>intval($data['template_id']), 
			'type'=>$data['type'],
			'title'=>mysql::str($data['title']),
			'description'=>mysql::str($data['description']),
			'text'=>mysql::str($data['text']),
			'meta_description'=>mysql::str($data['meta_description']),
			'meta_keywords'=>mysql::str($data['meta_keywords']),
			'hidden'=>(intval($data['hidden']))? 1 : 0,
			'special_date'=>self::makeDate($data['special_date']),
			'childs_order'=>intval($data['childs_order']),
		    'image'=>(!empty($data['image']))? 	mysql::str($data['image']) : '',
		    'last_update_date'=>time()
		);
		
		if($id) {
			$info['page_id'] = $id;
		} else {
			$info['create_date'] = time();
			$info['parent_id']=intval($data['parent_id']);
			$isCreate = true;
		}
		
		$this->db->autoupdate()->table('site_pages')->data(array($info))->primary('site_id','page_id');
		$this->db->execute();
		$id = ($id)? $id : $this->db->insert_id;
		
		
		$rewrite = (strlen($data['rewrite'])>1)? $data['rewrite'] : false;
		
		if($rewrite && substr($rewrite, 0, 1) != '/') $rewrite = '/'.$rewrite;
		$rewriteId = false;
		
		if($isCreate && $rewrite) {
			$rewriteId = $this->core->url_parser->add($rewrite, '/pages/show/id/'.$id.'/', 'Total', $this->shopId);
		} else if($rewrite) {
			$sql = "SELECT rewrite_id FROM site_pages WHERE page_id = {$id} AND site_id = {$this->shopId}";
			$this->db->query($sql);
			$old_rewrite_id = intval($this->db->get_field());
			if(!$old_rewrite_id) {
				$rewriteId = $this->core->url_parser->add($rewrite, '/pages/show/id/'.$id.'/', 'Total', $this->shopId);
			} else {
				$this->core->url_parser->edit($old_rewrite_id, $rewrite, '/pages/show/id/'.$id.'/', 'Total');
			}
		}

		
		if($rewriteId) {
			$query = array(
				'site_id'=>$this->shopId,
				'page_id'=>$id,
				'rewrite_id'=>$rewriteId
			);
			
			$this->db->autoupdate()->table('site_pages')->data(array($query))->primary('site_id','page_id');
			$this->db->execute();
		}
		
		
		return $id;
	}
	
	public function deletePage($id) {
		$id = intval($id);
		
		$sql = "DELETE FROM site_pages WHERE page_id = {$id} AND site_id = {$this->shopId}";
		$this->db->query($sql);
		
		$sql = "SELECT page_id FROM site_pages WHERE parent_id = {$id} AND site_id = {$this->shopId}";
		$this->db->query($sql);
		$this->db->get_rows();
		
		if(is_array($this->db->rows) && !empty($this->db->rows)) {
			foreach($this->db->rows as $item) {
				$this->deletePage($item['page_id']);
			}
		}
		
		return true;
	}
			
	public function changePageParent($pageId, $parentId) {
	    $pageId = intval($pageId);
	    $parentId = intval($parentId);
	    
	    if(!$pageId) return false;
	    
	    $sql = "SELECT MAX(`order`) FROM site_pages WHERE site_id = {$this->shopId} AND parent_id = {$parentId}";
	    $this->db->query($sql);
	    $order = $this->db->get_field()+10;
	    
	    $sql = "UPDATE site_pages SET parent_id = {$parentId}, `order` = {$order}  WHERE page_id = {$pageId} AND site_id = {$this->shopId}";
	    $this->db->query($sql);
	}
	
	public function changePageOrder($pageId, $contextId, $moveType) {
	    $pageId = intval($pageId);
	    $contextId = intval($contextId);
	    
	    if(!$pageId || !$contextId) return false;
	
	    $sql = "SELECT parent_id FROM site_pages WHERE site_id = {$this->shopId} AND page_id = {$pageId}";
	    $this->db->query($sql);
	    $pageParent = intval($this->db->get_field());
	    
	    $sql = "SELECT parent_id FROM site_pages WHERE site_id = {$this->shopId} AND page_id = {$contextId}";
	    $this->db->query($sql);
	    $contextParent = intval($this->db->get_field());
	    
	    if($pageParent != $contextParent) {
	        $this->changePageParent($pageId, $contextParent);
	    }
	    
	    $sql = "SELECT 
	               p.page_id, 
	               p.site_id, 
	               p.`order` 
	            FROM 
	               site_pages as p 
	            WHERE 
	               p.site_id = {$this->shopId} AND 
	               p.parent_id = (SELECT p2.parent_id FROM site_pages as p2 WHERE p2.page_id = {$pageId} AND p2.site_id = {$this->shopId}) 
	            ORDER BY 
	               p.order ASC";
	    
	    $this->db->query($sql);
	    $this->db->get_rows();
	    $list = $this->db->rows;
	    
	    $contextPosition = false;
	    
	    foreach($list as $k=>$item) {
	        if($item['page_id'] == $pageId) {
	            unset($list[$k]);
	            break;
	        }
	    }
	    
	    foreach($list as $k=>$item) {
	        if($item['page_id'] == $contextId) {
	            $contextPosition = $k;
	            break;
	        }
	    }

	    $current = array(
	        'page_id' =>$pageId,
	        'site_id' =>$this->shopId,
	        'order' =>0
	    );
	    
	    $contextPosition = ($moveType == 'prev')? $contextPosition : $contextPosition+1;
	    $listAfter = array_merge(array_slice($list, 0 , $contextPosition), array($current), array_slice($list, $contextPosition));
        
	    $order = 0;
	    
	    foreach($listAfter as &$item) {
	        $order ++;
	        $item['order'] = $order;
	    }
	    
	    $this->db->autoupdate()->table('site_pages')->data($listAfter)->primary('page_id', 'site_id');
	    $this->db->execute();
	    
	    return $listAfter;
	}
	
	public function deleteItemMenu($id) {
		$id = intval($id);
		
		$sql = "DELETE FROM site_menu WHERE menu_id = {$id} AND site_id = {$this->shopId}";
		$this->db->query($sql);
		
		return true;
	}

	public function getSiteDomain($siteId) {
		$sql = "SELECT server_name FROM mcms_sites WHERE id = {$siteId}";
		$this->db->query($sql);
		
		return $this->db->get_field();
	}
	
	public function checkAccessToSite($siteId) {
		$siteId = intval($siteId);
		$sql = "SELECT shop_id FROM tp_shop WHERE client_id = {$this->clientId} AND shop_id = {$siteId}";
		$this->db->query($sql);
		
		return (intval($this->db->get_field())>0)? true : false;
	}
	
	/* создание сайта */
	
	public function createShop($info) {
		
		//$info['theme_id'] = 2;
		
		$tmp = $this->addSite($info);

		$siteId = $tmp['siteId'];
		$domain = $tmp['domain'];
		$info['domain'] = $domain;
		
		$this->addShop($siteId, $info);
		$this->addSettings($siteId, $info['settings']);
		$this->makeDomains($siteId);
		$this->makeData($siteId);
		$this->makeStatic($siteId, $info['theme_id']);
		$this->addDemoData($siteId, $info['theme_id']);

		$this->updateClientCache();
		
		return true;
	}
	
	private function addSite($info) {
		$info['domain_type'] = intval($info['domain_type']);
				
		$localDomain = $this->getUserNextDomain();
		$domain = ($info['domain_type'] != 2)? $info['domain'] : $localDomain;
		$alias = ($info['domain_type'] == 2)? false : $domain;
		
		
		
		$data = array(
			'name'=>$domain,
			'type'=>3,
			'server_name'=>$localDomain,
			'server_port'=>80,
			'custom_preloader'=>'shop_site_preloader'
		);
		
		$this->db->autoupdate()->table('mcms_sites')->data(array($data));
		$this->db->execute();
		$siteId = $this->db->insert_id;
		
		$aliasDomain = array();
		
		if($alias) {
			$aliasDomain[] = array(
				'id_site'=>$siteId,
				'server_name'=>$alias,
				'server_port'=>80
			);
		}
		
		if($info['domain_type'] != 2) {
			$aliasDomain[] = array(
				'id_site'=>$siteId,
				'server_name'=>'www.'.$info['domain'],
				'server_port'=>80
			);
		}
		
		if(count($aliasDomain)) {
			$this->db->autoupdate()->table('mcms_sites_alias')->data($aliasDomain);
			$this->db->execute();
		}
		
		return array('siteId'=>$siteId, 'domain'=>$domain);
	}
	
	private function addShop($siteId, $info) {
		$tp_shop = array(
			'client_id'=>$this->clientId,
			'shop_id'=>$siteId,
			'name'=>mysql::str($info['name']),
			'url'=>'http://'.$info['domain'],
			'email'=>mysql::str($info['email']),
			'phone'=>mysql::str($info['phone']),
			'logo'=>mysql::str($info['logo']),
			'create_date'=>time(),
			'status'=>1,
			'is_free'=>1,
			'is_promo'=>0
		);
		
		$this->db->autoupdate()->table('tp_shop')->data(array($tp_shop));
		$this->db->execute();
		
		return true;
	}
	
	private function addSettings($siteId, $settings) {
		$data = array();
		$settingData = array();
		
		foreach($settings as $name=>$item) {
			$settingData[mysql::str($name)] = $item;
		}
		
		$data[] = array(
			'client_id'=>$this->clientId,
			'site_id'=>$siteId,
			'name'=>'integrations',
			'data'=>json_encode($settingData)
		);
		
			
		if(count($settingData) > 0) {
			$this->db->autoupdate()->table('site_settings')->data($data);
			$this->db->execute();
		}
		
		return true;
	}
	
	private function makeDomains($siteId) {
		$sql = "SELECT server_name FROM mcms_sites WHERE id = {$siteId} UNION SELECT server_name FROM mcms_sites_alias WHERE id_site = {$siteId}";
		$this->db->query($sql);
		$this->db->get_rows();
		
		foreach($this->db->rows as $item) {
			$this->addNginxSiteAlias($item['server_name']);
		}
		
		
		return true;
	}
	
	private function addNginxSiteAlias($domain) {
		$f = fopen(CORE_PATH.'bin/ncity-shopdomains', 'a');
		fwrite($f, "\nserver_name {$domain};");
		fclose($f);
		@shell_exec('sudo /etc/init.d/nginx reload');
	}
	
	private function removeNginxSiteAlias($domain) {
		$f = fopen(CORE_PATH.'bin/ncity-shopdomains-ondelete', 'a');
		fwrite($f, "\n{$domain};");
		fclose($f);
	}
	
	private function makeData($siteId) {
		$mcms_sites_modules = array();
		$mcms_sites_modules[] = array('id_site'=>$siteId, 'id_module'=>17);
		$mcms_sites_modules[] = array('id_site'=>$siteId, 'id_module'=>18);
		$mcms_sites_modules[] = array('id_site'=>$siteId, 'id_module'=>27);
		$mcms_sites_modules[] = array('id_site'=>$siteId, 'id_module'=>42);

		$this->db->autoupdate()->table('mcms_sites_modules')->data($mcms_sites_modules);
		$this->db->execute();
		
		$mcms_group_sites = array();
		$mcms_group_sites[] = array('id_group'=>3,'id_site'=>$siteId);
		
		$this->db->autoupdate()->table('mcms_group_sites')->data($mcms_group_sites);
		$this->db->execute();
	}
	
	private function makeStatic($siteId, $themeId, $isUpdateTheme = false) {
		
		
		$theme = $this->getThemeInfo($themeId);
		$domain = $this->getSiteDomain($siteId);
		$staticPath = '/templates/shop/'.$domain.'/'.$theme['name'].'/';
		$sourcePath = CORE_PATH.'static/theme_sources/'.$theme['name'].'/';
		$destPath =  CORE_PATH.'static/shop/'.$domain.'/'.$theme['name'].'/';
		
		$query = array(
			'id_site'=>$siteId,
			'name'=>$theme['name'],
			'description'=>$theme['title'],
			'static_path'=>$staticPath
		);
		
		if($isUpdateTheme) {
			$this->db->autoupdate()->table('mcms_themes')->data(array($query))->primary('id_site');
			$this->db->execute();
		} else {
			$this->db->autoupdate()->table('mcms_themes')->data(array($query));
			$this->db->execute();
		}
		
		
		$this->copyThemeFiles($sourcePath, $destPath);
		$this->copyThemeTpl($siteId, $theme, $isUpdateTheme);
	}

	private function copyThemeTpl($siteId, $theme, $isUpdateTheme = false) {
		$sql = "SELECT {$siteId} as id_site, tpl.id_lang, tpl.user_edit_id, tpl.theme, tpl.name_module, tpl.name, tpl.description, tpl.source, tpl.date, tpl.del FROM mcms_tmpl as tpl WHERE tpl.id_site = {$theme['demo_shop_id']} AND tpl.del = 0 AND tpl.id_lang = 1";
		$this->db->query($sql);
		$this->db->add_fields_deform(array('source'));
		$this->db->add_fields_func(array('mysql::str'));
		$this->db->get_rows();
		if($isUpdateTheme) {
			$this->db->autoupdate()->table('mcms_tmpl')->data($this->db->rows)->primary('id_site','theme', 'name_module', 'name');
		} else {
			$this->db->autoupdate()->table('mcms_tmpl')->data($this->db->rows);
		}
		
		$this->db->execute();
	}
	
	private function copyThemeFiles($source, $target) {
		if(!file_exists($target)) @mkdir($target, 0750, true);
		$d = dir($source);
		
		while (false !== ($entry = $d->read())) {
			if ($entry == '.' || $entry == '..') continue;
			$this->fullCopy("$source/$entry", "$target/$entry");
		}
		$d->close();
	}
	
	private function fullCopy($source, $target) {
		if (is_dir($source))  {
			@mkdir($target,0750);
			$d = dir($source);
			while (false !== ($entry = $d->read())) {
				if ($entry == '.' || $entry == '..') continue;
				$this->fullCopy("$source/$entry", "$target/$entry");
			}
			$d->close();
		}
		else copy($source, $target);
	}
	
	private function addDemoData($siteId, $themeId) {
		$theme = $this->getThemeInfo($themeId);
		$shopId = $theme['demo_shop_id'];
		
		$tables = array('tp_stores', 
						'tp_vendors', 
						'tp_product', 
						'tp_product_comment', 
						'tp_product_crossselling', 
						'tp_product_dimensions', 
						'tp_product_feature',
						'tp_product_feature_variants',
						'tp_product_group',
						'tp_product_img',
						'tp_product_store',
						'tp_product_to_feature',
						'tp_product_to_feed',
						'tp_product_to_group',
						'tp_product_to_parameters',
						'tp_product_to_ym_feature',
						'tp_shop_to_modules',
						'tp_shop_user_feedback',
						'site_pages',
						'site_menu',
						'mcms_rewrite'
		);
		
		$keyVariable = array(
			'client_id'=>array(15, $this->clientId),
			'shop_id'=>array($shopId, $siteId),
			'site_id'=>array($shopId, $siteId),
			'id_site'=>array($shopId, $siteId)
		);
		
		foreach($tables as $table) {
			$fields = $this->getTableFieldsList($table);
			if(!$fields) continue;

			$query = array('t.*');
			$where = array('1');
				
			foreach($fields as $key) {
				if(isset($keyVariable[$key])) {
					$where[] = "t.$key = {$keyVariable[$key][0]}";
					$query[] = "{$keyVariable[$key][1]} as $key";
				}
				
				if($key == 'id') {
					$query[] = "0 as id";
				}
			}

			$sql = 'SELECT '.implode($query,', ').' FROM '.$table.' as t WHERE '.implode($where, ' AND ');

			$this->db->query($sql);
			
			if($table == 'tp_shop_to_modules') {
				
			} else {
				$this->db->add_fields_deform($fields);
				$this->db->add_fields_func(array_pad(array(), count($fields), 'mysql::str'));
			}
			
			$this->db->get_rows();
			
			if($this->db->rows) {
				$this->db->autoupdate()->table($table)->data($this->db->rows);
				$this->db->execute();
			}
			
			
		}		
		
		//echo $this->core->log->sql();
	}
	
	private function getTableFieldsList($table) {
		$sql = "SELECT * FROM {$table} WHERE 1 LIMIT 1";
		$this->db->query($sql);
		$this->db->get_rows(1);
		
		if(!$this->db->rows || !is_array($this->db->rows) || !count($this->db->rows)) return false;
		
		return array_keys($this->db->rows);
	}
	
	/* static functions */
	
	public static function makeDate($date, $withoutYear = false) {
		$date = trim($date);
	
		if($withoutYear && strlen($date) < 8) {
			$date .= '/0000';
		}
	
		if(!preg_match("/([0-9]{1,2}).+?([0-9]{1,2}).+?([0-9]{4})/", $date, $match)) {
			return false;
		}
	
		return ($withoutYear)? mktime(0,0,0,intval($match[2]),intval($match[1]),1970) : mktime(0,0,0,intval($match[2]),intval($match[1]),intval($match[3]));
	}
	
	public static function makeTime($time) {
		$time = trim($time);
		if(!preg_match("/([0-9]{1,2})[\ ]{0,}[\.\ \:\-][\ ]{0,}([0-9]{1,2})[\ ]{0,}[\.\ \:\-][\ ]{0,}([0-9]{1,2})/", $time, $match)) {
			if(!preg_match("/([0-9]{1,2})[\ ]{0,}[\.\ \:\-][\ ]{0,}([0-9]{1,2})/", $time, $match)) {
				return false;
			} else {
				return mktime(intval($match[1]), intval($match[2]), 0, 1,1,1970);
			}
		} else {
			return mktime(intval($match[1]), intval($match[2]), intval($match[3]), 1,1,1970);
		}
	}
	
	public static function makeDateTime($date, $time = false) {
		if($time) return self::makeDate($date)+self::makeTime($time);
	
		if(!preg_match("/([0-9]{1,2}).+?([0-9]{1,2}).+?([0-9]{4})[\ ]{0,}[\ \-\:]{0,1}[\ ]{0,}(.+?)$/", $date, $dtime)) {
			return false;
		}
	
		return self::makeDate($dtime[1].'.'.$dtime[2].'.'.$dtime[3])+self::makeTime($dtime[4]);
	}
	
	public static function dateAgo($time) {
		$now = time();
		$dayNow = date("d", $now);
		$timeDay = date("d", $time);
		$timeMonth = date("m", $time);
		$timeYear = date("Y", $time);
		$NowMonth = date("m", $now);
		$NowYear = date("Y", $now);
		$secondsAgo = $now-$time;
	
		$minust = array('Минуту','Минуты', 'Минут');
		$hours = array('Час','Часа', 'Часов');
		$seconds = array('Секунду','Секунды', 'Секунд');
		$month = array('Месяц','Месяца', 'Месяцев');
		$agoText = 'назад';
		$tomorow = 'Вчера в';
	
		//редавтирование было вчера
		if($timeDay == $dayNow-1 && $timeMonth == $NowMonth && $timeYear == $NowYear) {
			return $tomorow.' '.date("H:i", $time);
		}
	
		if($dayNow == $timeDay && $timeMonth == $NowMonth && $timeYear == $NowYear) {
			// редактирование было сегодня
			if($secondsAgo == 60) {
				// минуту назад
				return $minust[0].' '.$agoText;
			}
	
			if($secondsAgo > 60) {
				// больше минуты назад
				$text = '';
	
				if($secondsAgo > 3600) {
					// больше часа назад
	
					// целые часы
					$hourAgo = (int)($secondsAgo/3600);
					// целые минуты
					$minustAgo = (int)(($secondsAgo%3600)/60);
	
					$text .= $hourAgo.' ';
					$text .= formatTextInCOunter($hourAgo, $hours);
					$text .= ' '.$minustAgo.' ';
					$text .= formatTextInCOunter($minustAgo, $minust);
					$text .= ' '.$agoText;
	
					return $text;
				}
	
				$minustAgo = (int)($secondsAgo / 60);
				$secondsA = (int)($secondsAgo % 60);
	
				$text .= $minustAgo.' ';
				$text .= formatTextInCOunter($minustAgo, $minust);
				$text .= ' '.$secondsA.' ';
				$text .= formatTextInCOunter($secondsA, $seconds);
				$text .= ' '.$agoText;
					
				return $text;
			}
	
			if($secondsAgo < 60) {
				// меньше минуты назад
				$text = $secondsAgo.' '.formatTextInCOunter($secondsAgo, $seconds).' '.$agoText;
	
				return $text;
			}
		}
	
		if($dayNow == $timeDay && $timeMonth < $NowMonth && $timeYear == $NowYear) {
			$MonthCount = intval($NowMonth) - intval($timeMonth);
			$MonthCountText = ($MonthCount == 1)? '': $MonthCount.' ';
	
			$text = $MonthCountText.formatTextInCOunter($MonthCount, $month).' назад';
	
			return $text;
		}
	
		return date("d.m.Y H:i", $time);
	}
	
	public static function date($timestamp) {
		return date('d.m.Y', $timestamp);
	}
	
	public static function dtime($timestamp) {
		return date('d.m.Y H:s:i', $timestamp);
	}
	
	public static function dtimeShort($timestamp) {
		return date('d.m.Y H:s', $timestamp);
	}
	
	public static function time($timestamp) {
		return date('H:s:i', $timestamp);
	}
	
	public static function bday($timestamp) {
		return (!$timestamp)? '-' : self::date($timestamp);
	}

}


if(!function_exists('datepicker')) {
	function datepicker($date){
		return ($date)? date('d.m.Y',$date) : '-';
	}
}


?>