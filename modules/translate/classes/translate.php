<?php 
class translate extends main_module {
	
	public function getTranslatedLinks() {
		$sql = "SELECT t.id, t.url, u.name as username, t.is_new FROM translate as t LEFT JOIN sms_clients_user as u ON u.id = t.user_id WHERE 1=1";
		$this->db->query($sql);
		$this->db->get_rows(false,'url');
		
		return $this->db->rows;
	}
	
	public function deleteDictPhrase($id) {
		$id = intval($id);
		if(!$id) return false;
		
		$sql = "DELETE FROM translate_dict WHERE id = {$id}";
		$this->db->query($sql);
		
		return true;
	}
	
	public function addPhraseToDict($id, $data) {
		$id = intval($id);
		
		$query = array(
			'name_en'=>mysql::str($data['phrase_en']),
			'name_ru'=>mysql::str($data['phrase_ru'])
		);
		
		if(!$id) {
			$query['user_id'] = $this->userInfo['id'];
			$query['create_date'] = time();
		} else {
			$query['id'] = $id;
		}
		
		$this->db->autoupdate()->table('translate_dict')->data(array($query))->primary('id');
		$this->db->execute();
		
		return $this->db->insert_id;
	}
	
	public function getDictionaryList($instance, $start, $limit, $sortBy, $sortType, $filters = false) {
		$sql = "SELECT
					SQL_CALC_FOUND_ROWS
					d.id, d.name_en, d.name_ru, d.create_date,
					u.name as user_name
				FROM
					translate_dict as d
						LEFT JOIN sms_clients_user as u ON u.id = d.user_id
				WHERE 1=1 ";
	
		if(!empty($filters['find'])) {
			$findString = mb_strtolower(mysql::str(trim($filters['find'])), 'UTF-8');
			$sql .= " AND (LOWER(d.name_en) LIKE '%{$findString}%' OR LOWER(d.name_ru) LIKE '%{$findString}%') ";
		}
		
		$sql .= " ORDER BY $sortBy $sortType LIMIT $start,$limit";
		
		$this->db->query($sql);
		$this->db->add_fields_deform(array('create_date'));
		$this->db->add_fields_func(array('translate::dtime'));
		$this->db->get_rows();
	
		return $this->db->rows;
	}
	
	public function getMyTranlateList($instance, $start, $limit, $sortBy, $sortType, $filters = false) {
		$sql = "SELECT
					SQL_CALC_FOUND_ROWS
					t.id, t.chars, t.url, t.title_en, t.last_update, 
					tt.name as type_name, t.is_new
				FROM
					translate as t
						LEFT JOIN translate_types as tt ON tt.id = t.type_id
				WHERE t.user_id = {$this->userInfo['id']} ";
	
		if(!empty($filters['type']) && intval($filters['type'])) {
			$sql .= " AND t.type_id = ".intval($filters['type']);
		}
	
		if(!empty($filters['is_new']) && intval($filters['is_new'])) {
			$sql .= " AND t.is_new = ".intval($filters['is_new']);
		}
	
		$sql .= " ORDER BY $sortBy $sortType LIMIT $start,$limit";
	
		$this->db->query($sql);
		$this->db->add_fields_deform(array('last_update'));
		$this->db->add_fields_func(array('translate::dtime'));
		$this->db->get_rows();
	
		return $this->db->rows;
	}
	
	
	public function addTextToTranslate($info) {
		$query = array(
			'user_id'=>$this->userInfo['id'],
			'chars'=>0,
			'url'=>mysql::str($info['link']),
			'type_id'=>1,
			'title_en'=>mysql::str(trim($info['title'])),
			'text_en'=>mysql::str(trim($info['text'])),
			'is_root'=>(intval($info['isRoot']) > 0)? 1 : 0,
			'approve'=>0,
			'is_new'=>1,
			'last_update'=>time()
		);
		
		if(!empty($info['isRoot']['subtitle'])) {
			$query['subtext_en'] = mysql::str($info['isRoot']['subtitle']);
		}
		
		$this->db->autoupdate()->table('translate')->data(array($query));
		$this->db->execute();
		
		$id = $this->db->insert_id;
		
		return array('url'=>$info['link'], 'id'=>$id, 'localId'=>$info['localId']);
	}
	
	public function getFilterData() {
		$sql = "SELECT id, name FROM sms_clients_user WHERE client_id = 2";
		$this->db->query($sql);
		$this->db->get_rows();
		
		$users = $this->db->rows;
		
		$sql = "SELECT * FROM translate_types WHERE 1";
		$this->db->query($sql);
		$this->db->get_rows();
		
		$types = $this->db->rows;
		
		return array('users'=>$users, 'types'=>$types);
	}
	
	public function getUserStat($id, $start=false, $end=false) {
		$id = intval($id);
		
		if(!$id) {
			$id = $this->userInfo['id'];
		}
		
		if(!$start || !$end) {
			$start = new DateTime();
			$start->modify("-6 days")->setTime(0,0,0);
			$start = $start->getTimestamp();
			
			$end = new DateTime();
			$end->setTime(23,59,59);
			$end = $end->getTimestamp();
		} else {
			$start = DateTime::createFromFormat('d/m/Y', $start);
			$start->setTime(0,0,0);
			$start = $start->getTimestamp();
				
			$end = DateTime::createFromFormat('d/m/Y', $end);
			$end->setTime(23,59,59);
			$end = $end->getTimestamp();
		}
		
		$uinfo = $this->getUserInfo($id);
		$full = array();
		
		$sql = "SELECT COUNT(*) FROM translate WHERE user_id = {$id} AND last_update >= {$start} AND  last_update <= {$end} AND is_new <> 1";
		$this->db->query($sql);
		$full['translates'] = intval($this->db->get_field());
		
		$sql = "SELECT SUM(chars) FROM translate_stat WHERE user_id = {$id} AND date >= {$start} AND date <= {$end}";
		$this->db->query($sql);
		$full['symbols'] = intval($this->db->get_field())+0;
		$full['price'] = translate::specialRound($full['symbols']) * $uinfo['cost'];

		if(!$full['symbols'])  $full['symbols'] = '0';
		if(!$full['translates'])  $full['translates'] = '0';
		if(!$full['price'])  $full['price'] = '0.00';

		
		return array(
				'info'=>$uinfo,
				'stat'=>$full,
				'start'=>translate::dtime($start),
				'end'=>translate::dtime($end)
		);
	}
	
	public function getTranlateList($instance, $start, $limit, $sortBy, $sortType, $filters = false) {
		$sql = "SELECT 
					SQL_CALC_FOUND_ROWS 
					t.id, t.chars, t.url, t.title, t.text, t.subtext, t.last_update, t.approve,
					tt.name as type_name,
					u.name as user_name 
				FROM
					translate as t 
						LEFT JOIN translate_types as tt ON tt.id = t.type_id
						LEFT JOIN sms_clients_user as u ON u.id = t.user_id
				WHERE t.is_new <> 1 
				";
		
		if(!empty($filters['type']) && intval($filters['type'])) {
			$sql .= " AND t.type_id = ".intval($filters['type']);
		}
		
		if(!empty($filters['user']) && intval($filters['user'])) {
			$sql .= " AND t.user_id = ".intval($filters['user']);
		}
		
		$sql .= " ORDER BY $sortBy $sortType LIMIT $start,$limit";
		
		$this->db->query($sql);
		$this->db->add_fields_deform(array('last_update'));
		$this->db->add_fields_func(array('translate::dtime'));
		$this->db->get_rows();
		
		return $this->db->rows;
	}
	
	public function getUsersList($instance, $start, $limit, $sortBy, $sortType, $filters = false) {
		$sql = "SELECT SQL_CALC_FOUND_ROWS u.id, u.login, u.name, u.phone, u.email, u.cost, (SELECT COUNT(*) FROM translate as t WHERE t.user_id = u.id AND t.is_new <> 1) as cnt FROM sms_clients_user as u WHERE u.client_id = 2";
		$sql .= " ORDER BY $sortBy $sortType LIMIT $start,$limit";
		
		$this->db->query($sql);
		$this->db->get_rows();
		

		return $this->db->rows;
	}
	
	public function deleteUser($id) {
		$id = intval($id);
		if(!$id) return false;
		
		$sql = "DELETE FROM sms_clients_user WHERE id = {$id}";
		$this->db->query($sql);
		
		return true;
	}
	
	public function getUserInfo($id) {
		$id = intval($id);
		if(!$id) return false;
		
		$sql = "SELECT * FROM sms_clients_user WHERE id = {$id}";
		$this->db->query($sql);
		$this->db->get_rows(1);
		
		return $this->db->rows;
	}
	
	public function saveUser($id, $data) {
		$id = intval($id);
		
		$query = array(
			'client_id'=>2,
			'site_id'=>2,
			'system_user_id'=>2,
			'login'=>mysql::str($data['login']),
			'password'=>$this->makePass($data['pass']),
			'name'=>mysql::str($data['name']),
			'phone'=>mysql::str($data['phone']),
			'email'=>mysql::str($data['email']),
			'cost'=>mysql::str($data['cost']),
			'active'=>1
		);
		
		if(!$id) {
			$sql = "SELECT * FROM sms_clients_user WHERE login = '{$query['login']}'";
			$this->db->query($sql);
			$this->db->get_rows();
			
			if(is_array($this->db->rows) && count($this->db->rows) > 0 && isset($this->db->rows['id'])) return 'Логин уже занят.';
		} else {
			if(!isset($data['pass']) || !strlen($data['pass'])) {
				unset($query['bitkinao']);
			}
			unset($query['login']);
			$query['id'] = $id;
		}
		
		$this->db->autoupdate()->table('sms_clients_user')->data(array($query))->primary('id');
		$this->db->execute();
		
		return true;
	}
	
	public function getTranslateInfo($id) {
		$id = intval($id);
		if(!$id) return false;
		
		$sql = "SELECT * FROM translate WHERE id = {$id}";
		$this->db->query($sql);
		$this->db->get_rows(1);
		
		return $this->db->rows;
	}
	
	public function saveStranslate($id, $data) {
		$id = intval($id);
		if(!$id) return false;
		
		$sql = "SELECT * FROM translate WHERE id = {$id} AND user_id = {$this->userInfo['id']}";
		$this->db->query($sql);
		$this->db->get_rows(1);
		
		if(!is_array($this->db->rows) || !count($this->db->rows) || !isset($this->db->rows['id'])) return false;
		
		$lastChar = intval($this->db->rows['chars']);
		$charText = str_replace(array("\n", "\r", " ", "	"), '', implode('', $data));
		
		$query = array(
			'id'=>$id,
			'chars'=>mb_strlen($charText, 'UTF-8'),
			'title'=>mysql::str($data['title']),
			'text'=>mysql::str($data['text']),
			'is_new'=>'0',
			'last_update'=>time()
		);
		
		if(!empty($data['subtext'])) {
			$query['subtext'] = mysql::str($data['subtext']);
		}
		
		$this->db->autoupdate()->table('translate')->data(array($query))->primary('id');
		$this->db->execute();
		
		$query = array(
			'user_id'=>	$this->userInfo['id'],
			'translate_id'=>$id,
			'chars'=>(mb_strlen($charText, 'UTF-8') - $lastChar),
			'date'=>time()
		);
		
		$this->db->autoupdate()->table('translate_stat')->data(array($query))->primary('id');
		$this->db->execute();
		
		return $charText;
	}
	
	public function deleteTranslate($id) {
		$id = intval($id);
		if(!$id) return false;

		if($this->clientId != 1) {
			$sql = "DELETE FROM translate WHERE id = {$id} AND user_id = {$this->userInfo['id']}";
			$this->db->query($sql);
			
			$sql = "DELETE FROM translate_stat WHERE translate_id = {$id} AND user_id = {$this->userInfo['id']}";
			$this->db->query($sql);
		} else {
			$sql = "DELETE FROM translate WHERE id = {$id}";
			$this->db->query($sql);
			
			$sql = "DELETE FROM translate_stat WHERE translate_id = {$id}";
			$this->db->query($sql);
		}
		
		
		
		return true;
	}
	
	private function makePass($pass) {
		return md5(md5($pass).$this->core->CONFIG['security']['user']['pass_salt']);
	}
	
	private function getResourceHtml($url) {
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_COOKIEJAR, './cookie.jar');
		curl_setopt($curl, CURLOPT_COOKIEFILE, './cookie.file');
		curl_setopt($curl, CURLOPT_URL,$url);
		curl_setopt($curl, CURLOPT_USERAGENT,$_SERVER['HTTP_USER_AGENT']);
		$result = curl_exec($curl);
	
		preg_match("/\<body(.+?)\>(.+?)\<\/body\>/si", $result, $result);
		$html = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $result[2]);
		$html = preg_replace('/<!--(.*)-->/Uis', '', $html);
		$html = '<html><head></head><body>'.$html.'</body></html>';
	
		return $html;
	}
	
	public function getCategoryList() {
		$startUrl = 'http://www.maisonsdumonde.com/UK/en/';
		$result = $this->getResourceHtml($startUrl);
		$result = preg_replace("/(\<img.+?\>)/si", '',$result);
		$result = preg_replace("/(\<span.+?\>.+?\<\/span\>)/si", '',$result);
	
		$src = new DOMDocument();
		@$src->loadHTML($result);
		$MDM = $src->getElementById('nav');
		$des = new DOMDocument('html');
		$nav = $des->importNode($MDM, true);
		$des->appendChild($nav);
		$html = $des->saveHTML();
		
		return $html;
	}
	
	public function getCategoryText($link, $isRoot = false) {
		$url = 'http://www.maisonsdumonde.com'.$link;
	
		if($isRoot) {
			$return = array('title'=>'', 'text'=>'');
			
			$html = $this->getResourceHtml($url);
			$src = new DOMDocument();
			@$src->loadHTML($html);
			$descr = $src->getElementById('landingContentLarge');

			if(!is_object($descr)) return $return;
			
			$descr = $descr->getElementsByTagName('div');

			if(!is_object($descr)) return $return;
			
			$descr = $descr->item(0)->getElementsByTagName('div');
				
			if(!is_object($descr)) return $return;
			
			$return = array(
					'title'=>$descr->item(0)->getElementsByTagName('h1'),
					'text'=>$descr->item(0)->getElementsByTagName('p')
			);
				
			$return['title'] = trim($return['title']->item(0)->nodeValue);
			$return['text'] = trim($return['text']->item(0)->nodeValue);
	
			return $return;
		} else {
			$html = $this->getResourceHtml($url);
			$html = str_replace(array("’", "–"), array("&acute;", "&#8211;"), $html);
			
			$src = new DOMDocument();
			@$src->loadHTML($html);
			$descr = $src->getElementById('top_category');
			$descr = $descr->getElementsByTagName('p');

			if(!is_object($descr) || !$descr->length) return '';
			
			return $descr->item(0)->nodeValue;
		}
	}
	
	public static function dtime($timestamp) {
		return date('d.m.Y H:s:i', $timestamp);
	}
	
	public static function specialRound($symbols) {
		if(!$symbols) return 0;	
		$base = floor(($symbols/1800));
		if(fmod($symbols,1800) > 250) {
			$base += 1;
		}
		
		return $base;
	}
	
	public static function clearText($text) {
		$text = str_replace(array("\n", "\r"),'', trim($text));
		//$text = atr_replace(array("â", ))
	}
	
}
?>