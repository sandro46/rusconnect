<?php 
class requests extends sv_module
{
	
	
	public function getList($instance, $page = 0, $limit = 20, $sortBy = 'date', $sortType = 'DESC', $filters = false) {
		$start = $page*$limit;
		$sql = "SELECT SQL_CALC_FOUND_ROWS r.*, IF(r.type = 1, 'Простая заявка', 'Стандарт+') as type_name, FROM_UNIXTIME(r.date, '%d.%m.%Y') as date FROM site_request as r WHERE 1=1 ";
		
		if(isset($filters['dateFrom']) && isset($filters['dateTo']) && self::testInputDate($filters['dateFrom']) && self::testInputDate($filters['dateTo'])) {
			$sql .= " AND r.date >= ".self::makeTimestampFromDate($filters['dateFrom'],true);
			$sql .= " AND r.date <= ".self::makeTimestampFromDate($filters['dateTo'],false,true);
		}
		
		if(isset($filters['like_name'])) {
			$searche = addslashes(decode_unicode_url($filters['like_name'])); // пришло скорее всего в utf-8, нужно почистить строку
			$searche = preg_replace("/[^а-яА-ЯёЁa-zA-Z0-9\ \-\_\+\=]+/isu",'',$searche); // убираем все спец-вимволы

			$sql .= " AND (LOWER(r.name) LIKE LOWER('%{$searche}%') OR LOWER(r.company_name) LIKE LOWER('%{$searche}%'))";
		}
		
		if(isset($filters['request_type']) && intval($filters['request_type']) > 0) {
			$sql .= " AND r.type = ".intval($filters['request_type']);
		}
		
		$sql .= " ORDER BY $sortBy $sortType LIMIT $start,$limit";
				
		$this->db->query($sql);
		$this->db->get_rows();
		
		return $this->db->rows;	
	}
	
	public function getCityList(){
		$sql = "SELECT * FROM geo_cities WHERE is_active = 1 ORDER BY title";
		$this->db->query($sql);
		$this->db->get_rows();
		return $this->db->rows;
	}
	
	
	public function deleteRequest($id) {
		if(!$id=intval($id)) return false;
		$this->db->delete('site_request', $id, 'id');
		return true;
	}
	
	public function saveCompany($id, $data) {
		global $core;
		$id = ($id && intval($id))? intval($id) : false;
		if(!$id) {
			if(!isset($data['type']) || !intval($data['type'])) return -1;
			if(!isset($data['city']) || !intval($data['city'])) return -1; //FIX_ME
			if(!isset($data['name']) || strlen($data['name']) <2) return -2;
			if(!isset($data['mail']) || strlen($data['mail']) <2) return -3;
			
			if(!isset($data['name_first']) || strlen($data['name_first']) <2) return -4;
			if(!isset($data['name_last']) || strlen($data['name_last']) <2) return -4;
			
			$sv_companies = array(
				'parent_id'=>1,
				'name'=>mysql::str($data['name']),
				'brand'=>mysql::str($data['brand']),
				'type'=>intval($data['type']),
				'city_id'=> intval($data['city']),
				'address'=>mysql::str($data['address']),
				'phone'=>mysql::str($data['phone']),
				'site'=>mysql::str($data['site']),
				'mail'=>mysql::str($data['mail'])
			);
						
			$core->db->autoupdate()->table('sv_companies')->data(array($sv_companies));
			$core->db->execute();

			$id = $core->db->insert_id;
			$result = $this->createPartnerAccount($data, $id);
		} else {
			$sv_companies = array('id'=>$id);
			if(isset($data['type']) && intval($data['type'])) $sv_companies['type'] = intval($data['type']);
			if(isset($data['city']) && intval($data['city'])) $sv_companies['city_id'] = intval($data['city']);
			if(isset($data['name']) && strlen($data['name']) > 2) $sv_companies['name'] = mysql::str($data['name']);
			if(isset($data['address']) && strlen($data['address']) > 2) $sv_companies['address'] = mysql::str($data['address']);
			if(isset($data['phone']) && strlen($data['phone']) > 2) $sv_companies['phone'] = mysql::str($data['phone']);
			if(isset($data['site']) && strlen($data['site']) > 2) $sv_companies['site'] = mysql::str($data['site']);
			if(isset($data['mail']) && strlen($data['mail']) > 2) $sv_companies['mail'] = mysql::str($data['mail']);
			if(isset($data['brand']) && strlen($data['brand']) > 2) $sv_companies['brand'] = mysql::str($data['brand']);
			
			if(count($sv_companies) > 1) {
				$core->db->autoupdate()->table('sv_companies')->data(array($sv_companies))->primary('id');
				$core->db->execute();
			}
		}
		
		$sv_companies_props = array('company_id'=>$id);
			
		if(isset($data['bik']) && bugalter_valid::checkBik($data['bik'])) $sv_companies_props['bank_bik'] = $data['bik'];
		if(isset($data['bill']) && bugalter_valid::bankAccountCheck($data['bill'])) $sv_companies_props['bill_num'] = $data['bill'];
		if(isset($data['inn']) && bugalter_valid::innCheck($data['inn'])) $sv_companies_props['inn'] = $data['inn'];
		if(isset($data['kpp']) && strlen($data['kpp']) > 5 && bugalter_valid::kppCheck($data['kpp'])) $sv_companies_props['kpp'] = $data['kpp']; 
		if(isset($data['ogrn']) && strlen($data['ogrn']) > 5 && bugalter_valid::orgnCheck($data['ogrn'])) $sv_companies_props['ogrn'] = $data['ogrn'];
	
		if(count($sv_companies_props) > 1) {
			$core->db->autoupdate()->table('sv_companies_props')->data(array($sv_companies_props))->primary('company_id');
			$core->db->execute();
		}
		
		if($data['type'] == 8) {
			return $result['id_user'];
		}
		
		return $id;		
	}
	
	public function createPartnerAccountFromExistCompany($info, $companyId) {
		return $this->createPartnerAccount($info, $companyId);
	}
	
	public function createPartnerAccount($info, $companyId) {
		global $core;
		
		$companyId = intval($companyId);
		$data = $info;
		
		if(!$companyId) return false;
		if(!is_array($info)) return false;
		
		$passLen = 7;
		$passString = "Nsn86690jBwb234NMqeqiOIfEgrEkw464568EbhvgcvcsjhbPW7786890974gEGG3UwjHUWdsgfEEGGbcviyv1234325364EG707EG6087435121";
		$login = 'd'.str_pad($companyId,4,'0',STR_PAD_LEFT);
		$pass = '';
			
		for($i=0;$i <= $passLen; $i++) {
			$pass .= $passString[rand(0,strlen($passString))];
		}
		
		$mcms_user = array(
			'name'=>mysql::str($info['name_last']).' '.mysql::str($info['name_first']),
			'login'=>$login,
			'password'=>md5(md5($pass).$core->CONFIG['security']['user']['pass_salt']),
			'default_site_id'=>5,
			'email'=>mysql::str($info['mail']),
			'memo'=>'Пароль: '.$pass,
			'disable'=>0
		);
		
		if($info['type'] == 8) {
			$mcms_user['is_fist_reg'] = 1;
		}
		
		$core->db->autoupdate()->table('mcms_user')->data(array($mcms_user));
		$core->db->execute();
		//$this->db->debug();
		
		$id_user = $core->db->insert_id;
######################################		
		$mcms_user_group[] = array(
			'id_user'=>$id_user,
			'id_group'=>20
		);
		
		$mcms_user_group[] = array(
			'id_user'=>$id_user,
			'id_group'=>18
		);
######################################		
		$core->db->autoupdate()->table('mcms_user_group')->data($mcms_user_group);
		$core->db->execute();
		//$this->db->debug();
		
		$sv_clients = array(
			'user_id'=>$id_user,
			'mail'=>$mcms_user['email'],
			'reg_date'=>time(),
			'company_id'=>$companyId,
			'name_first'=>mysql::str($info['name_first']),
			'name_last'=>mysql::str($info['name_last']),
			'name_second'=>mysql::str($info['name_second']),
			'company_post'=>mysql::str($info['userpost']),
			'enabled'=>1	
		);
		
		if($info['type'] == 8) {
			$sv_clients['is_fist_reg'] = 1; 
		}
		
		$core->db->autoupdate()->table('sv_clients')->data(array($sv_clients));
		$core->db->execute();
		$clientId = $core->db->insert_id;
		
		
		$crm_companies = array(
			'client_id'=>$this->clientId,
			'user_id'=>$this->user->id,
			'date'=>time(),
			'sv_companies_id'=>$companyId,
			'source_id'=>5,
			'name'=>mysql::str($data['name']),
			'brand'=>mysql::str($data['brand']),
			'phone'=>mysql::str($data['phone']),
			'site'=>mysql::str($data['site']),
			'mail'=>mysql::str($data['mail']),
			'address'=>mysql::str($data['address'])
		);
				
		
		$core->db->autoupdate()->table('crm_companies')->data(array($crm_companies));
		$core->db->execute();
		//$this->db->debug();
		$CrmCompanyId = $core->db->insert_id;
		
		$crm_contacts = array(
			'client_id'=>$this->clientId,
			'user_id'=>$this->user->id,
			'date'=>time(),
			'company_id'=>$CrmCompanyId,	
			'name'=>mysql::str($data['name_first']).' '.mysql::str($data['name_last']).' '.mysql::str($data['name_second']),
			'responsible_user_id'=>$this->user->id,
			'post'=>mysql::str($data['userpost']),
			'source_id'=>5
		);
		
		$core->db->autoupdate()->table('crm_contacts')->data(array($crm_contacts));
		$core->db->execute();
		//$this->db->debug();
		$CrmContactId = $core->db->insert_id;
		
		$crm_contacts_email = array(
			'client_id'=>$this->clientId,
			'contact_id'=>$CrmContactId,
			'email_type'=>1,
			'email'=>mysql::str($data['mail'])
		);
		
		$core->db->autoupdate()->table('crm_contacts_email')->data(array($crm_contacts_email));
		$core->db->execute();
		//$this->db->debug();
		
		$crm_contacts_phones = array(
			'client_id'=>$this->clientId,
			'contact_id'=>$CrmContactId,
			'phone_type'=>2,
			'phone'=>mysql::str($data['phone'])
		);
		
		$core->db->autoupdate()->table('crm_contacts_phones')->data(array($crm_contacts_phones));
		$core->db->execute();
		//$this->db->debug();
		
		
		$crm_events = array(
			'client_id'=>$this->clientId,
			'date'=>time(),
			'user_id'=>$this->user->id,
			'event_type_id'=>4,
			'company_id'=>$CrmCompanyId,
			'contact_id'=>0,
			'deal_id'=>0,
			'task_id'=>0
		);
			
		$this->db->autoupdate()->table('crm_events')->data(array($crm_events));
		$this->db->execute();
		//$this->db->debug();
		
		$crm_events = array(
			'client_id'=>$this->clientId,
			'date'=>time(),
			'user_id'=>$this->user->id,
			'event_type_id'=>1,
			'company_id'=>0,
			'contact_id'=>$CrmContactId,
			'deal_id'=>0,
			'task_id'=>0
		);
			
		$this->db->autoupdate()->table('crm_events')->data(array($crm_events));
		$this->db->execute();

#######################################################
		
		$sql = "INSERT INTO `crm_companies_sources` (`client_id`, `name`) VALUES
					({$this->clientId}, 'Холодные звонки'),
					({$this->clientId}, 'Рекламная кампания skidki.ncity.biz'),
					({$this->clientId}, 'СМС Рассылка'),
					({$this->clientId}, 'Email рассылка'),
					({$this->clientId}, 'Заявка на портале');";
		
		
		$this->db->query($sql);
		
		$sql = "INSERT INTO `crm_contacts_sources` (`client_id`, `name`) VALUES
					({$this->clientId}, 'Холодные звонки'),
					({$this->clientId}, 'Рекламная кампания skidki.ncity.biz'),
					({$this->clientId}, 'СМС Рассылка'),
					({$this->clientId}, 'Email рассылка'),
					({$this->clientId}, 'Заявка на портале');";
		
		
		$this->db->query($sql);
		
		
		$crm_users = array(
			'client_id'=>$clientId,
			'name'=> mysql::str($info['name_first']).' '.mysql::str($info['name_last']),
			'email'=> mysql::str($info['mail']),
			'phone'=>'',
			'post'=>mysql::str($info['userpost']),
			'checked'=>1,
			'system_user_id'=>$id_user,
			'enable'=>1		
		);
		
		
					
		$this->db->autoupdate()->table('crm_users')->data(array($crm_users));
		$this->db->execute();
		
		if($info['type'] == 8) {
			$notice = "Создана тестовая учетная запись. Телефон: {$data['phone']}";
		} else {
			$notice = "Учетная запись партнера создана, и подтверждена. Логин: {$login} Пароль: {$pass} Имя: {$mcms_user['name']}";
		}
		
		
 		$this->sendsms('89615233857',$notice,'Ncity');
 		if($info['type'] != 8) {
			$title = 'Регистрация на бизнес-портале Ncity';
			$message = "Вам была создана учетная запись на бизнес-портале <b>Ncity.biz</b><br>Используйте для входа следующие данные:<br>Логин: <b>{$login}</b><br>Пароль: <b>{$pass}</b><br><br>-----<br>С Уважением, команда Ncity.biz<br>8-800-555-88-60<br>intellsochi@gmail.com";
			@$this->sendEmail($title,$message,$mcms_user['email']);
 		}
		return array('login'=>$login,'id_user'=>$id_user);
	}
	
	public function sendsms($phone,$text,$sender){
		if(!class_exists('littlesms')) include CORE_PATH.'modules/index/sms.php';
		
		$gate = new littlesms('ncity', 'g53qavdsbfdh2312', false);
		$gate->url = 'deamons.itbc.pro/api';
		$gate->messageSend($phone,$text, $sender);
	}
	
	public function sendEmail($title,$message,$mail, $from='noreply@ncity.biz') 
	{
		if(!class_exists('smtp_sender')) $this->lib->load('smtp');
		$mailer = new smtp_sender('smtp.itbc.pro', 'alexey@itbc.pro', 'QzWx123-');
		$mailer->defaultType = 'text/html';
		$mailer->send($from, $mail, $title, $message);
	}
	
}




































?>