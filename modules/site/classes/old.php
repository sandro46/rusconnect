<?php

class sites extends sv_module
{
	public $themeList = array(3=>'tech', 2=>'creative');
	private $catalog = false;
	private $shop = false;
	
	public function loadReview($id, $site_id){
		$id = intval($id);
		$sql = "SELECT
						cs.*
				FROM
						sv_client_sites as cs
						LEFT JOIN 
									mcms_sites as ms 
						ON 
									ms.id = cs.site_id
				WHERE 
						cs.client_id = {$this->clientId} AND
						cs.custom_id = $site_id 
				LIMIT 1";
		
		
		
		$this->db->query($sql);
		$this->db->get_rows(1);
		
		$a = $this->db->rows;
		$sql = "SELECT  * FROM tp_site_blocks WHERE type_id = 2 AND client_id = {$this->clientId} AND shop_id = ".$a['site_id']." AND block_id = {$id}";
		//echo $sql;
		$this->db->query($sql);
		$this->db->get_rows(1);
		
		$a = $this->db->rows;
		return $a;
	}
	
	public function saveReview($data){
		//print_r($data);
		$id = intval($id);
		$sql = "SELECT
		cs.*
		FROM
		sv_client_sites as cs
		LEFT JOIN
		mcms_sites as ms
		ON
		ms.id = cs.site_id
		WHERE
		cs.client_id = {$this->clientId} AND
		cs.custom_id = {$data['shop_id']}
		LIMIT 1";
		
		
		
		$this->db->query($sql);
		$this->db->get_rows(1);
		
		$a = $this->db->rows;
		
		$arr = array(
				'shop_id'=>$a['site_id'],
				'title'=>$data['title'],
				'text'=>$data['text'],
				'client_id'=>$this->clientId,
				'type_id' => 2
		);
		if(intval($data['block_id']))
			$arr['block_id'] = $data['block_id'];
		else
			$arr['add_date'] = time();
		
		$this->db->autoupdate()->table('tp_site_blocks')->data(array($arr))->primary('block_id', 'shop_id', 'client_id');
		$this->db->execute();
		//$this->db->debug();
		
		
	}
	
	public function loadInfo($id){
		$id = intval($id);
		$sql = "SELECT
		
		cs.*
		
		FROM
		sv_client_sites as cs
		LEFT JOIN mcms_sites as ms ON ms.id = cs.site_id
		WHERE cs.client_id = {$this->clientId} AND
		cs.custom_id = $id
		LIMIT 1
		";
		
		
		
		$this->db->query($sql);
		$this->db->get_rows(1);
		
		$a = $this->db->rows;
		$a['pay_types'] = 		explode(',', $a['pay_types']);
		$a['delivery_types'] = 	explode(',', $a['delivery_types']);
		
		
		
		$sql = "SELECT  'main' id, id id_site, server_port, server_name FROM mcms_sites WHERE id = ".$a['site_id']."
		UNION
				SELECT id, 			id_site,  server_port, server_name FROM  mcms_sites_alias WHERE id_site = ".$a['site_id'];
		
		$this->db->query($sql);
		$this->db->get_rows();
		
		$a['sites'] = $this->db->rows;
		
		$sql = "SELECT  * FROM tp_site_blocks WHERE type_id = 2 AND client_id = {$this->clientId} AND shop_id = ".$a['site_id'];
		//echo $sql;
		$this->db->query($sql);
		$this->db->add_fields_deform(array('add_date'));
		$this->db->add_fields_func(array('dateAgo'));
		$this->db->get_rows();
		
		$a['reviews'] = $this->db->rows;
		//print_r($a);
		
		
		return $a;
		
	}
	
	public function saveInfo($data){

		
		$arr[] = array(
					'custom_id'		=>intval($data['id']),
					'name'			=>$data['name'],
					'phone'			=>$data['phone'],
					'mail'			=>$data['mail'],
					'yandex'		=>$data['yandex'],
					'google'		=>$data['google'],
					'unisender'		=>mysql_real_escape_string($data['unisender']),
					'logo'			=>$data['logo'],
					'icon'			=>$data['icon'],
					'client_id'		=>$this->clientId,
					'pay_types'		=>implode(",", $data['pay_types']),
					'delivery_types'=>implode(",", $data['delivery_types']),
					
				'b_facebook'		=>$data['b_facebook'],
				'b_twitter'		=>$data['b_twitter'],
				'b_vk'		=>$data['b_vk'],
				'b_youtube'		=>$data['b_youtube'],
				'b_skype'		=>$data['b_skype'],
				'b_odno'		=>$data['b_odno'],
				'b_mir'		=>$data['b_mir'],
				'b_goog'		=>$data['b_goog']
			);

		$this->db->autoupdate()->table('sv_client_sites')->data($arr)->primary('custom_id', 'client_id');
		$this->db->execute();
		
		$sql = "SELECT site_id FROM sv_client_sites WHERE custom_id = ".intval($data['id'])." AND client_id = ".$this->clientId;
		$this->db->query($sql);
		$site_id = $this->db->get_field();
		
		$sql = "UPDATE  mcms_sites SET name 	server_name";
		
		//$this->db->debug();
	}
	
	public function checkAccount() {
		return $this->core->CONFIG['is_free'];
	}
	
	public function addCatalogApi(admin_catalog $api) {
		$this->catalog = $api;
	}
	
	public function addShopApi(client_shop $api) {
		$this->shop = $api;
	}
	
	private function getSiteStaticFolder($siteId) {
		$sql = "SELECT  server_name FROM mcms_sites WHERE id = {$siteId}";
		$this->db->query($sql);
		
		return $this->db->get_field();
	}
	
	private function getSiteIdByStaticFolder($name) {
		$sql = "SELECT id FROM  mcms_sites WHERE server_name = '{$name}'";
		$this->db->query($sql);
		
		return $this->db->get_field();
	}
	
	public function copyTemplateToAllShop($themeName = 'tech', $shopIdSource = 46) {
		$sourcePath = CORE_PATH.'static/shop/'.$this->getSiteStaticFolder($shopIdSource).'/'.$themeName;
		$sourceFolder = $this->getSiteStaticFolder($shopIdSource);
		
		if(!file_exists($sourcePath)) {
			return -1;
		}
		
		$staticPath = CORE_PATH.'static/shop/';
		
		foreach(scandir($staticPath) as $item) {
			if($item == '.' || $item == '..' || $item == $sourceFolder) continue;
			
			$destPath = $staticPath.$item.'/'.$themeName;
			
			if(file_exists($destPath)) {
				$this->replaceTemplates($themeName,$shopIdSource, $item);
				$this->RecursiveCopy($sourcePath, $destPath, false);
			}
		}
		
		
		
	}
	
	public function replaceTemplates($themeName = 'tech', $shopIdSource = 46, $destSiteName) {
		$destSiteid = $this->getSiteIdByStaticFolder($destSiteName);
		$rootModuleName = $this->getSiteStaticFolder($shopIdSource);
		
		if($destSiteid && $destSiteid != $shopIdSource) {
			$sql = "SELECT * FROM mcms_tmpl WHERE id_site = {$shopIdSource}";
			$this->db->query($sql);
			$this->db->get_rows();
			
			$items = $this->db->rows;
			
			foreach($items as $k=>$item) {
				//if($destSiteid != 45) continue;
				unset($items[$k]['id_template']);
				
				$items[$k]['id_site'] = $destSiteid;
				
				if($item['name_module'] == $rootModuleName) {
					$items[$k]['name_module'] = $destSiteName;
				}
				
				//unset($items[$k]['source']);
				
				$sql = "SELECT id_template FROM mcms_tmpl WHERE id_site = {$destSiteid} AND name_module = '{$items[$k]['name_module']}' AND name = '{$items[$k]['name']}' AND theme = '{$items[$k]['theme']}'";
				$this->db->query($sql);
				$oldId = intval($this->db->get_field());
				//echo $sql."\n\n";
				if($oldId) {
					$items[$k]['id_template'] = $oldId;
					//$v = $items[$k];
					//unset($v['source']);
					//print_r($v);
					$items[$k]['source'] = mysql_real_escape_string($items[$k]['source']);
					$this->db->autoupdate()->table('mcms_tmpl')->data(array($items[$k]))->primary('id_template');
					$this->db->execute();
					//$this->db->debug();
					echo "update module: {$items[$k]['name_module']} name: {$items[$k]['name']}\n";
				} else {
					$this->db->autoupdate()->table('mcms_tmpl')->data(array($items[$k]));
					$this->db->execute();
					echo "insert module: {$items[$k]['name_module']} name: {$items[$k]['name']}\n";
				}
			}

		}
		
		
	}
	
	public function copyDataToTheme($themeName = 'tech', $shopIdSource = 46) {
		$sourcePath = CORE_PATH.'static/shop/'.$this->getSiteStaticFolder($shopIdSource).'/'.$themeName;
		$destPath = CORE_PATH.'static/themes/'.$themeName;
		
		
		if(!file_exists($sourcePath)) {
			return -1;
		}
		
		if(!file_exists($destPath)) {
			return -2;
		}
		
		if(!file_exists($destPath)) {
			return -3;
		}
		
		$this->RecursiveCopy($sourcePath, $destPath, false);
		//echo "[$sourcePath] \n [$destPath]\n\n";
		return true;
	}
	
	public function copyTempatesToTheme($themeName = 'tech', $shopIdSource = 46, $test = false) {
		$sitename = $this->getSiteStaticFolder($shopIdSource);
		$destPath = CORE_PATH.'static/themes/'.$themeName.'/templates/';
		
		if($test) {
			$destPath .= 'test/';
			mkdir($destPath);
		}
		
		if(!$sitename) return -1;
		if(!file_exists($destPath)) {
			mkdir($destPath);
			//return -2;
		}
		
		$sql = "SELECT * FROM mcms_tmpl WHERE id_site = {$shopIdSource} AND id_lang = 1 AND theme = 'default'";
		$this->db->query($sql);
		$this->db->get_rows();
		$meta = array();
		
		foreach($this->db->rows as $item) {
			$modulename = ($item['name_module'] == $sitename)? 'site' : $item['name_module'];
			$moduleurl = ($modulename == 'catalog')? 'pages' : $modulename;
			$version = '2.0.8';
			 
			$_meta = array("url"=>"/{$moduleurl}/{$item['name']}", "module"=>$modulename, "theme"=>"current", "name"=>$item['description'], "vers"=>$version, "lang"=>"ru", "last_update"=>$item['date']);
		
			if(!file_exists($destPath.$moduleurl)) {
				mkdir($destPath.$moduleurl);
			}
			
			$f = fopen($destPath.$moduleurl.'/'.$item['name'], 'w');
			fwrite($f, $item['source']);
			fclose($f);
			
			$meta[] = $_meta;
		}
		
		$metaText = json_encode($meta);
		
		$f = fopen($destPath.'/templates.meta', 'w');
		fwrite($f, $metaText);
		fclose($f);
		
		return true;
	}

	public function createRequestOnDomainRegFromFree($domain) {
		
		// create request for reg
		
	}
	
	public function createRequestOnDomainRegFromPremium($domain) {
		if($this->checkAccount()) return false;
		
		// create request for reg
	}
	
	public function getList($instance, $page = 0, $limit = 20, $sortBy = 'date', $sortType = 'DESC', $filters = false) {
		$start = $page*$limit;
		$sql = "SELECT
					SQL_CALC_FOUND_ROWS
					cs.custom_id as id, cs.name, cs.create_date, IF(cs.active, 'Да', 'Нет') as active,
					ms.server_name as domain,
					IFNULL((SELECT GROUP_CONCAT(msa.server_name) FROM mcms_sites_alias as msa WHERE msa.id_site = cs.site_id), 'Нет алиасов') as aliases
										
				FROM 
					sv_client_sites as cs
						LEFT JOIN mcms_sites as ms ON ms.id = cs.site_id
				WHERE cs.client_id = {$this->clientId}";
	
		
		$sql .= " ORDER BY $sortBy $sortType LIMIT $start,$limit";
		//echo $sql;
		$this->db->query($sql);
		$this->db->add_fields_deform(array('create_date'));
		$this->db->add_fields_func(array('dateAgo'));
		$this->db->get_rows();
		return $this->db->rows;
	}
	
	private function makeShopFiles($data) {		
		$data['system_theme'] = 'default';
		
		$staticpath = '/templates/shop/'.$data['server_name'].'/'.$this->themeList[$data['tpl_id']].'/';
		$staticreal = CORE_PATH.'static/shop/'.$data['server_name'].'/'.$this->themeList[$data['tpl_id']].'/';
		$sourcereal = CORE_PATH.'static/themes/'.$this->themeList[$data['tpl_id']].'/';
		
		//echo "static: $staticpath \n real: $staticreal\n sources: $sourcereal";
		
		
		// создаем структуру папок в директории со статическими файлами
		if(!mkdir($staticreal, 0700, true)) return false;		
		$this->RecursiveCopy($sourcereal, $staticreal, false);
//
//		// читаем метаданные о шаблонах
		$templatesMeta = file_get_contents($staticreal.'templates/templates.meta');
		$templatesMeta = json_decode($templatesMeta, true);
//
		$mcms_templates = array();
//		
//		// формируем запрос на запись в базу шаблонов
		foreach($templatesMeta as $key=>$item) {
			$mcms_templates[] = array(
				'id_site'=>$data['site_id'], 
				'id_lang'=>$this->getLangIdByCode($item['lang']), 
				'theme'=>(($item['theme'] == 'current')? $data['system_theme'] : $item['theme']),
				'name_module'=>(($item['module'] == 'site')? $data['server_name'] : $item['module']),
				'name'=>substr(strrchr($item['url'], '/'), 1),
				'description'=>$item['name'],
				'source'=>mysql_real_escape_string(file_get_contents($staticreal.'templates'.$item['url'])),
				'date'=>time(),
				'del'=>0
			);
//			
//			// удаляем с диска шаблоны
			//unlink($staticreal.'templates'.$item['url']);
		}
//		
//		// записываем в базу шаблоны
		$this->db->autoupdate()->table('mcms_tmpl')->data($mcms_templates);
		$this->db->execute();
		//$this->db->debug();
		
	
		
		$mcms_themes = array(
			'id_site'=>$data['site_id'],
			'name'=>'default',
			'description'=>$data['tpl_name'],
			'static_path'=>$staticpath
		);
		
		$this->db->autoupdate()->table('mcms_themes')->data(array($mcms_themes));
		$this->db->execute();
		
		

		
		
		//print_r($data);
		
		/*
		var_dump($res);
		echo CORE_PATH."static/shop/yandex.ru/theme1/";
		*/
		return false;
		
		
		
		
		
		
		//print_r($data);
		//return var_dump($res);	
	}
	
	public function create($data) {
		$data['server_name'] = str_replace(array('http://', 'ftp://', 'https://'), '', $data['domain']);
				
		//print_r($data);
		
		/* $this->client_id = 1;
		$this->shop->shopId = 42;
		$this->shop->clientId = 1;
		//$this->createTestReviews(42);
		$this->addDemodataInPages(42, 3);
		//$this->addDemodataInCatalog(42);
		die(); */
		//$data['tpl_name'] ='Creative'; 
		//$data['tpl_id'] = 2;
		
		
		$mcms_sites = array(
			'name'=>$data['name'],
			'server_name'=>$data['server_name'],
			'server_port'=>80
		);
		
		$this->db->autoupdate()->table('mcms_sites')->data(array($mcms_sites));
		$this->db->execute();
		$data['site_id'] = $site_id = $this->db->insert_id;
		
		if($data['server_name'] != $this->user->info['login'].'.ncity.biz' && $data['server_name'] != $this->user->info['login'].'.ncity.biz/') {
			$mcms_sites_alias = array('id_site'=>$data['site_id'], 'server_name'=>$this->user->info['login'].'.ncity.biz', 'server_port'=>'80');
			$this->db->autoupdate()->table('mcms_sites_alias')->data(array($mcms_sites_alias));
			$this->db->execute();
		}
		
		$this->db->select()->from('sys_skeleton_site_modules')->fields('module_id as id');
		$this->db->execute();
		$this->db->get_rows();
		
		$mcms_sites_modules = array();
		
		foreach($this->db->rows as $item) {
			$mcms_sites_modules[] = array('id_site'=>$data['site_id'], 'id_module'=>$item['id']);	
		}
		
		$this->db->autoupdate()->table('mcms_sites_modules')->data($mcms_sites_modules);
		$this->db->execute();
		
		$this->db->select()->from('sys_skeleton_site_groups')->fields('group_id as id');
		$this->db->execute();
		$this->db->get_rows();
		
		$mcms_group_sites = array();
		
		foreach($this->db->rows as $item) {
			$mcms_group_sites[] = array('id_site'=>$data['site_id'], 'id_group'=>$item['id']);	
		}
		
		$this->db->autoupdate()->table('mcms_group_sites')->data($mcms_group_sites);
		$this->db->execute();
		
		
		$sv_client_sites = array(
			'client_id'=>$this->clientId,
			'site_id'=>$data['site_id'],
			'name'=>$data['name'],
			'create_date'=>time(),
			'active'=>1,
			'phone'=>$data['phone'],
			'mail'=>$data['mail'],
			'logo'=>$data['logo'],
			'icon'=>$data['icon'],
			'tpl_name'=>$data['tpl_name'],
			'tpl_id'=>$data['tpl_id'],
			'merchant_id'=>$data['merchant_id'],
			'merchant_key'=>$data['merchant_key'],
			'sms_order'=>$data['sms_order'],
			'sms_status'=>$data['sms_status'],
			'pay_types'=>'1,2,3',
			'delivery_types'=>'1,2,3'
		);
		
		$this->db->autoupdate()->table('sv_client_sites')->data(array($sv_client_sites));
		$this->db->execute();
	
		/****   START TEST DATA *****/
		$this->shop->shopId = $data['site_id'];
		$this->shopId = $data['site_id'];

		
		$this->createTestData($data);

		shell_exec("/var/www/new.ncity.biz/bin/create_domain {$name}");
	}
	
	private function createTestData($data) {
		$this->addDemodataInPages($data['site_id'], $data['tpl_id']); // статика
		$this->addDemodataInCatalog($data['site_id']); // товары и категории
		$this->createTestReviews($data['site_id']);
		$this->makeShopFiles($data);
	}
	
	private function createTestReviews($site_id){
		$data = array();
		$data[] = array('add_date'=>time(), 'client_id'=>$this->clientId,'shop_id'=>$site_id, 'type_id'=>2, 'title'=>'Вера Н', 'text'=>'Отличный магазин.Вежливые менеджеры.Перезвонили сразу после оформления заказа.Я в восторге.Спасибо!');
		$data[] = array('add_date'=>time(), 'client_id'=>$this->clientId,'shop_id'=>$site_id, 'type_id'=>2, 'title'=>'Светлана Яковлева', 'text'=>'Буквально через несколько минут после интернет заказа, поступил звонок на мобильный, диспетчер уточнила дату и время доставки.');
		$data[] = array('add_date'=>time(), 'client_id'=>$this->clientId,'shop_id'=>$site_id, 'type_id'=>2, 'title'=>'Nikitos', 'text'=>'P.S. А ещё был бонус от компании - 3 маленьких симпатичных махровых полотенца, кстати, очень хорошего качества и накопительную дисконтную карточку - пустячок - а приятно!!!');
		
		$this->db->autoupdate()->table('tp_site_blocks')->data($data);
		$this->db->execute();
		//$this->db->debug();
		//print_r($this->db->error);
	}
	
	
	private function addDemodataInCatalog($site_id) {
		$groups = array();
		
		$groups[0] = $this->makeDemoGroup($site_id, 
				'Бытовая техника', 
				'Когда потребуется купить товары для дома, то не придется тащить через весь город пакеты с покупками, беспокоясь о том, чтобы ничего не разбить … Ваши близкие также могут обратиться в наш интернет-магазин и все приобрести по самым низким ценам в городе...', 
				0, 
				'/vars/files/images/d51f8287e66e16ddf9593ecc64d1e80b.jpg'); 
		
		$groups[1] = $this->makeDemoGroup($site_id,
				'Телевизоры и видео',
				'Покупая товар в нашем магазине, Вы можете быть уверены, что мы сделаем все для того, чтобы Вы имели возможность выбрать именно тот телевизор, который идеально впишется в интерьер помещения.',
				$groups[0],
				'/vars/files/images/5faec87cfc914b1672585f05534963aa.jpg');
		
		$groups[2] = $this->makeDemoGroup($site_id,
				'Техника для дома',
				'В нашем интернет-магазине представлены и актуальные новинки техники для дома и сада, которые за короткое время помогут Вам навести безукоризненную чистоту в помещении, помыть машину, полить сад водой и многое другое.',
				$groups[0],
				'/vars/files/images/42ab2c463b38101dc70f356a08642b8c.jpg');
		
		$groups[3] = $this->makeDemoGroup($site_id,
				'Кухня в удовольствие',
				'Наш интернет магазин представляет огромный выбор практичных и оригинальных товаров для кухни. У нас вы можете заказать фирменные товары для кухни по низкой цене со скидкой.',
				$groups[0],
				'/vars/files/images/cd2a47df7a5f467b38ca607a2d96d0dd.jpg');
		
		$groups[4] = $this->makeDemoGroup($site_id,
				'Обувь и одежда',
				'Коллекции женской, мужской и детской одежды, обуви, аксессуаров и др. товаров из европейских каталогов. Информация о доставке и оплате. Таблицы размеров, советы по уходу за вещами.',
				0,
				'/vars/files/images/b820e040a2f175f95fbf8bb84b456b8c.jpg');
		
		$groups[5] = $this->makeDemoGroup($site_id,
				'Женская одежда',
				'Наш интернет магазин предлагает Вам внушительный каталог стильных элементов гардероба, способных сделать любую женщину привлекательной, элегантной и сексуальной.',
				$groups[4],
				'/vars/files/images/a621336714325884db9ff540ed51b394.jpg');
		
		$groups[6] = $this->makeDemoGroup($site_id,
				'Мужская одежда',
				'В нашем интернет-магазине всегда большой выбор изделий мужской одежды и количество представленных коллекций постоянно увеличивается.',
				$groups[4],
				'/vars/files/images/b4ae93286046c0fc4d9f471f2cc72816.jpg');
		
		$groups[7] = $this->makeDemoGroup($site_id,
				'Женская обувь',
				'Женская обувь порадует вас огромным ассортиментом. Обувь от Rendez-vous — это приобретение, которое подчеркнет вкус и респектабельность обладателя',
				$groups[4],
				'/vars/files/images/80f4f273cd2a3d1d6f01ae2b3fd953f4.jpg');
		
		$groups[8] = $this->makeDemoGroup($site_id,
				'Мужская обувь',
				'Мужская обувь в интернет магазине представлена ведущими мировыми именами, которые специализируются именно на этом производстве.',
				$groups[4],
				'/vars/files/images/0c4b551499fb04e2539ea1eb412ff23c.jpg');
		
		$groups[9] = $this->makeDemoGroup($site_id,
				'Тренажеры и фитнес',
				'Уважаемые покупатели! Мы рады предложить Вашему вниманию огромный ассортимент нашего магазина, состоящий из великого множества товаров для спорта и отдыха.',
				0,
				'/vars/files/images/6613363419fe0e4f3580099a63e756bc.jpg');
		
		$groups[10] = $this->makeDemoGroup($site_id,
				'Тренажеры',
				'Широкий ассортимент моделей, к примеру, House Fit, Kampfer, Ab Rocket, AB Couch, Body Solid, представлен в нашем интернет-магазине. Вы без труда выберете тренажер, который подойдет Вам по цене и техническим особенностям.',
				$groups[9],
				'/vars/files/images/84ed955d8c392bca003654c593f4cc67.jpg');
		
		$groups[11] = $this->makeDemoGroup($site_id,
				'Товары для фитнеса',
				'У нас вы можете купить легкие товары для фитнеса по низкой цене. Мы предлагаем Вам как товары для фитнеса так и товары для йоги. ',
				$groups[9],
				'/vars/files/images/420def2428e62976d42a6a6767705625.jpg');
		
		$groups[12] = $this->makeDemoGroup($site_id,
				'Для животных',
				'Купить товары для животных в нашем интернет-магазине – это проявление заботы о любимых питомцах, не выходя из дома. … А также можете уверено оплатить заказ с помощью банковской карты через проверенную и надежную систему Uniteller.',
				0,
				'/vars/files/images/77cf0d68b4f4397b951c386187382f6e.jpg');
		$groups[13] = $this->makeDemoGroup($site_id,
				'Салон красоты',
				'Большой выбор фирменных товаров для красоты и здоровья позволит вас всегда выглядеть неотразимой, а действующие акции сделают покупки в нашем магазине еще выгоднее В каталоге представлены лучшие изделия для поддержания красоты вашего тела.',
				0,
				'/vars/files/images/b0b16367f0cf5da9c980e9c7021ff1dc.jpg');
		$groups[14] = $this->makeDemoGroup($site_id,
				'Товары для здоровья',
				'В нашем интернет - магазине Вы можете купить товары для здоровья, красоты и комфорта. Дайте человеку здоровье и направление в жизни; и он никогда не будет останавливаться, чтобы беспокоиться о том, счастлив он или нет.',
				0,
				'/vars/files/images/0be1f4829181ec10eb988d037a1c087f.jpg');
		
		$lastGroupId = $groups[15];
		
		$groupsEntryClone = array(
			1=>$groups[0],
			3=>$groups[4],
			4=>$groups[10],
			7=>$groups[5],
			8=>$groups[1],
			9=>$groups[2],
			10=>99,
			11=>99,
			12=>$groups[6],
			13=>$groups[7],
			14=>$groups[8],
			15=>$groups[10],
			16=>$groups[11],
			17=>$groups[12],
			18=>$groups[13],
			19=>$groups[14],
			20=>$groups[3]
		);
		
		//product_id, client_id, group_id, title, comment,  url, url_big, url_preview, is_main, sx, sy, sw, sh
		$this->db->query('SELECT * FROM tp_product_img WHERE shop_id = 46 AND client_id != 0 AND product_id != 0');
		$this->db->get_rows();
		$img = $this->db->rows;
		
		foreach($img as $k=>$item) {
			$item['client_id'] = $this->clientId;
			$item['shop_id'] = $site_id;
			$item['add_date'] = time();
			
			$this->db->autoupdate()->table('tp_product_img')->data(array($item));
			$this->db->execute();
			$img[$k]['img_id'] = $this->db->insert_id;
		}
		
		$this->db->query("SELECT * FROM tp_product WHERE shop_id = 46");
		$this->db->get_rows();
		
		$products = $this->db->rows;
		
		foreach($products as $k => $item) {
			$item['client_id'] = $this->clientId;
			$item['shop_id'] = $site_id;
			$item['add_date'] = time();
			$item['group_id'] = (isset($groupsEntryClone[$item['group_id']]))? $groupsEntryClone[$item['group_id']] : $lastGroupId;
			$item['rewrite_id'] = 0;
			$products[$k] = $item;
		}
		
		$this->db->autoupdate()->table('tp_product')->data($products);
		$this->db->execute();		
	}
	
	private function getDemoRewrite($title1, $title2='') {
		if(!$title2)
		 return '/'.strtolower(preg_replace("[^А-яA-z0-9\_\-]", '', str_replace(' ', '_', encodestring($title1)))).'/';
		
		return '/'.strtolower(preg_replace("[^А-яA-z0-9\_\-]", '', str_replace(' ', '_', encodestring($title1)))).'/'.strtolower(preg_replace("[^А-яA-z0-9\_\-]", '', str_replace(' ', '_', encodestring($title2)))).'/';
	}
	
	private function makeDemoGroup($shopId, $title, $description='', $parentId=0, $image='') {
		$info = array('meta_title'=>'','meta_keyword'=>'','meta_description'=>'','rewrite_action'=>'new','rewrite'=>'','image'=>'', 'parrent_id'=>$parentId);
		
		if(!$parentId) {
			$info['rewrite'] = $this->getDemoRewrite($title);
		} else {
			$sql = "SELECT get_rewrite(rewrite_id) as rewrite, name FROM  tp_product_group WHERE shop_id = {$shopId} AND client_id = {$this->clientId} AND group_id = {$parentId}";
			$this->db->query($sql);
			$this->db->get_rows(1);
			$rewrite = $this->db->rows['rewrite'];
			if($rewrite) {
				$info['rewrite'] = $rewrite.substr($this->getDemoRewrite($title), 1);
			} else {
				$info['rewrite'] = $this->getDemoRewrite($this->db->rows['name'], $title);
			}
		}

		$info['name'] = $title;
		$info['description'] = $description;
		$info['parrent_id'] = $parentId;
		$info['image'] = $image;
		
		return $this->shop->save_group_info_new(0,$info);
	}
	
	private function addDemodataInPages($site_id, $tpl_id) {
		
		if($tpl_id == 3) {
		
			$sourcereal = CORE_PATH.'static/themes/'.$this->themeList[$tpl_id].'/docs/';
			
			$page = array(
					'date'=>date('d.m.Y'),
					'description'=>'         ',
					'text'=>'           ',
					'title'=>'Главное меню',
					'url'=>'/mainmenu/',
					'visible'=>0,
					'simple_link'=>'',
					'shop_static_sys' => 1,
					'static_name' => 'mainmenu',
					'site_id'=>$site_id
			);
			
			$result = $this->catalog->save(false, 0, 'simple', $page);
			$parent_id = $result[0];
			
			
			
			$page = array(
					'date'=>date('d.m.Y'),
					'description'=>'Гарантии возврата товаров и безопасности платежей',
					'text'=> file_get_contents($sourcereal.'waranty.html'),
					'title'=>'Возврат и гарантии',
					'url'=>'/warranty.html',
					'visible'=>1,
					'simple_link'=>'',
					'shop_static_sys' => 1,
					'static_name' => 'warranty',
					'site_id'=>$site_id,
					'order'=>30
			);
			$result = $this->catalog->save(false, $parent_id, 'simple', $page);
			
			
			
			$page = array(
					'date'=>date('d.m.Y'),
					'description'=>'Контактные данные для связи',
					'text'=> file_get_contents($sourcereal.'contacts.html'),
					'title'=>'Наши контакты',
					'url'=>'/contacts.html',
					'visible'=>1,
					'simple_link'=>'',
					'shop_static_sys' => 1,
					'static_name' => 'contacts',
					'site_id'=>$site_id,
					'order'=>40
			);
			$result = $this->catalog->save(false, $parent_id, 'simple', $page);
			
			
			$page = array(
					'date'=>date('d.m.Y'),
					'description'=>'Срочная Экспресс доставка грузов, товаров, документов и корреспонденции',
					'text'=> file_get_contents($sourcereal.'dostavka.html'),
					'title'=>'Доставка и оплата',
					'url'=>'/dostavka.html',
					'visible'=>1,
					'simple_link'=>'',
					'shop_static_sys' => 1,
					'static_name' => 'dostavka',
					'site_id'=>$site_id,
					'order'=>20
			);
			$result = $this->catalog->save(false, $parent_id, 'simple', $page);
			
			
			$page = array(
					'date'=>date('d.m.Y'),
					'description'=>'О компании',
					'text'=> file_get_contents($sourcereal.'about.html'),
					'title'=>'О компании',
					'url'=>'/about.html',
					'visible'=>1,
					'simple_link'=>'',
					'shop_static_sys' => 1,
					'static_name' => 'about',
					'site_id'=>$site_id,
					'order'=>10
			);
			$result = $this->catalog->save(false, $parent_id, 'simple', $page);
			
			$page = array(
					'date'=>date('d.m.Y'),
					'description'=>'         ',
					'text'=>'           ',
					'title'=>'Информационные страницы для шаблонов',
					'url'=>'/tmpl_info/',
					'visible'=>0,
					'simple_link'=>'',
					'shop_static_sys' => 1,
					'static_name' => 'tpl_htm',
					'site_id'=>$site_id
			);
			
			$result = $this->catalog->save(false, 0, 'simple', $page);
			$parent_id = $result[0];
			
			
			
			$page = array(
					'date'=>date('d.m.Y'),
					'description'=>'Блок о приеме платежей (главная страница)',
					'text'=> file_get_contents($sourcereal.'payments_info.html'),
					'title'=>'Блок о приеме платежей (главная страница)',
					'url'=>'/payments_info.html',
					'visible'=>1,
					'simple_link'=>'',
					'shop_static_sys' => 1,
					'static_name' => 'payments_info',
					'site_id'=>$site_id
			);
			$result = $this->catalog->save(false, $parent_id, 'simple', $page);
			
			
			$page = array(
					'date'=>date('d.m.Y'),
					'description'=>'Условия оферты',
					'text'=> file_get_contents($sourcereal.'oferta.html'),
					'title'=>'Условия оферты',
					'url'=>'/oferta.html',
					'visible'=>1,
					'simple_link'=>'',
					'shop_static_sys' => 1,
					'static_name' => 'oferta',
					'site_id'=>$site_id
			);
			$result = $this->catalog->save(false, $parent_id, 'simple', $page);
			
			
			$page = array(
					'date'=>date('d.m.Y'),
					'description'=>'Способы оплаты',
					'text'=> file_get_contents($sourcereal.'paymen_types.html'),
					'title'=>'Способы оплаты',
					'url'=>'/paymen_types.html',
					'visible'=>1,
					'simple_link'=>'',
					'shop_static_sys' => 1,
					'static_name' => 'paymen_types',
					'site_id'=>$site_id
			);
			$result = $this->catalog->save(false, $parent_id, 'simple', $page);
			
			
			$page = array(
					'date'=>date('d.m.Y'),
					'description'=>'Регионы доставки',
					'text'=> file_get_contents($sourcereal.'delivery_regions.html'),
					'title'=>'Регионы доставки',
					'url'=>'/delivery_regions.html',
					'visible'=>1,
					'simple_link'=>'',
					'shop_static_sys' => 1,
					'static_name' => 'delivery_regions',
					'site_id'=>$site_id
			);
			$result = $this->catalog->save(false, $parent_id, 'simple', $page);
			
			$page = array(
					'date'=>date('d.m.Y'),
					'description'=>'Совсем не знаком(-а) с покупками в интернет-магазинах? Не беда! Не стоит платить больше и пользоваться услугами посредников. Для каждого магазина на нашем сайте создана подробная инструкция о том, как правильно оформить заказ. Она сделает этот процесс простым и быстрым.',
					'text'=> file_get_contents($sourcereal.'about_purchases.html'),
					'title'=>'О покупках',
					'url'=>'/about_purchases.html',
					'visible'=>1,
					'simple_link'=>'',
					'shop_static_sys' => 1,
					'static_name' => 'about_purchases',
					'site_id'=>$site_id
			);
			
			//print_r($page);
			$result = $this->catalog->save(false, $parent_id, 'simple', $page);
		}
		
		if($tpl_id == 2) {
			
			$page = array(
					'date'=>date('d.m.Y'),
					'description'=>'         ',
					'text'=>'          ',
					'title'=>'Каталог',
					'url'=>'/catalog/',
					'visible'=>1,
					'simple_link'=>'/ru/shop/catalog/'
			);
			$result = $this->catalog->save(false, $parent_id, 'simple', $page);
			
			$page = array(
					'date'=>date('d.m.Y'),
					'description'=>'Страница контактов',
					'text'=>$lorem,
					'title'=>'Контакты',
					'url'=>'/contacts.html',
					'visible'=>1
			);
			$result = $this->catalog->save(false, $parent_id, 'simple', $page);
			
			$page = array(
					'date'=>date('d.m.Y'),
					'description'=>'Страница с описанием способов оплаты',
					'text'=>$lorem,
					'title'=>'Оплата',
					'url'=>'/pay.html',
					'visible'=>1
			);
			$result = $this->catalog->save(false, $parent_id, 'simple', $page);
			
			$page = array(
					'date'=>date('d.m.Y'),
					'description'=>'Страница с описанием доставки',
					'text'=>$lorem,
					'title'=>'Доставка',
					'url'=>'/deliver.html',
					'visible'=>1
			);
			$result = $this->catalog->save(false, $parent_id, 'simple', $page);
			
			$page = array(
					'date'=>date('d.m.Y'),
					'description'=>'Короткая информация о магазине',
					'text'=>$lorem,
					'title'=>'Информация о магазине',
					'url'=>'/about/',
					'visible'=>1
			);
			$result = $this->catalog->save(false, $parent_id, 'simple', $page);
			$parent_id = $result[0];
			
			$page = array(
					'date'=>date('d.m.Y'),
					'description'=>'Салоны продаж',
					'text'=>$lorem,
					'title'=>'Салоны продаж',
					'url'=>'/about/office.html',
					'visible'=>1
			);
			$result = $this->catalog->save(false, $parent_id, 'simple', $page);
			
			$page = array(
					'date'=>date('d.m.Y'),
					'description'=>'Юридическая информация о магазине',
					'text'=>$lorem,
					'title'=>'Юридическая информация',
					'url'=>'/about/legal.html',
					'visible'=>1
			);
			$result = $this->catalog->save(false, $parent_id, 'simple', $page);
			
			$page = array(
					'date'=>date('d.m.Y'),
					'description'=>'Это простая тестовая страница. На нее можно указать ссылку в любом тексте или описании товара.',
					'text'=>$lorem,
					'title'=>'Простая страница',
					'url'=>'/test_simple_page.html',
					'visible'=>0
			);
			$result = $this->catalog->save(false, 0, 'simple', $page);
		}
	}
	
	private function getLangIdByCode($langCode) {
		return 1;
	}
	
	private function RecursiveCopy($source, $dest, $diffDir = false) {
   		$sourceHandle = opendir($source);
   		
    	if($diffDir != false) {
    		mkdir($dest . '/' . $diffDir);
    		//echo "mkdir: [$dest$diffDir]\n";
    	}     	
   
   		while($res = readdir($sourceHandle)) {
        	if($res == '.' || $res == '..') continue;
       
        	if(is_dir($source . '/' . $res)){
            	$this->RecursiveCopy($source . '/' . $res, $dest, $diffDir . '/' . $res);
        	} else {
            	copy($source . '/' . $res, $dest . '/' . $diffDir . '/' . $res); 
            	//echo  "source: [$source/$res] dest: [$dest$diffDir/$res]\n";
        	}
    	}
	}
}
