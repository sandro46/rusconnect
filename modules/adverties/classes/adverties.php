<?php


class adverties extends sv_module
{
	public $smsprice = 0.35;
	
	public function getCampaignStatuses() {
		$this->db->select()->from('adverties_status')->fields('id', 'name');
		$this->db->execute();
		$this->db->get_rows();
		
		return $this->db->rows;
	}
	
	public function getCampaignTypes() {
		$this->db->select()->from('adverties_types')->fields('id', 'name');
		$this->db->execute();
		$this->db->get_rows();
		
		return $this->db->rows;
	}
	
	public function getCampaign($instance, $page = 0, $limit = 20, $sortBy = 'date', $sortType = 'DESC', $filters = false) {
		$start = $page*$limit;
		
		
		
		$sql = "
			SELECT
				SQL_CALC_FOUND_ROWS
				a.name, a.campaing_id, a.campaing_id as id,  a.type_id, a.status_id, a.budget, a.budget_spent, a.auditory, a.user_id, a.executor_id, a.create_date, 
				a.start_date, 
				a.end_date,
				
				FROM_UNIXTIME(a.start_date, '%d.%m.%Y') as start_date_formated, 
				FROM_UNIXTIME(a.start_date, '%H:%i') as start_time_formated, 
				FROM_UNIXTIME(a.end_date, '%d.%m.%Y') as end_date_formated, 
				FROM_UNIXTIME(a.end_date, '%H:%i') as end_time_formated, 
				
				ads.name as status_name,
				at.name as type_name,
				cu1.name as executor_name,
				cu2.name as creator_name
							
			FROM adverties as a
				LEFT JOIN crm_users as cu1 ON cu1.id = a.user_id
				LEFT JOIN crm_users as cu2 ON cu2.id = a.executor_id
				LEFT JOIN adverties_types as at ON at.id = a.type_id
				LEFT JOIN adverties_status as ads ON ads.id = a.status_id
				
			WHERE
				a.client_id = {$this->clientId} ";
			
		if(isset($filters['id']) && intval($filters['id'])) {
			$filters['id'] = intval($filters['id']);
			$sql .= " AND a.campaing_id = {$filters['id']}";
		}
		
		if(isset($filters['type_id']) && intval($filters['type_id'])) {
			$filters['type_id'] = intval($filters['type_id']);
			$sql .= " AND a.type_id = {$filters['type_id']}";
		}
		
		if(isset($filters['status_id']) && intval($filters['status_id'])) {
			$filters['status_id'] = intval($filters['status_id']);
			$sql .= " AND a.status_id = {$filters['status_id']} ";
		}
		
		if(isset($filters['executor_id']) && strlen($filters['executor_id'])) {
			$filters['executor_id'] = intval($filters['executor_id']);
			$sql .= " AND a.executor_id = {$filters['executor_id']} ";
		}
			
		$sql .= " ORDER BY $sortBy $sortType LIMIT $start,$limit";
		
		//echo $sql;
		$this->db->query($sql);
		$this->db->add_fields_deform(array('create_date', 'start_date', 'end_date'));
		$this->db->add_fields_func(array('dateAgo', 'crm_client::todayStyleTime', 'crm_client::todayStyleTime'));
		$this->db->get_rows(false, 'id');
		
		return $this->db->rows;	
	}
	
	function startSMScamp($data) {
		
		$adverties = array(
			'client_id'=>$this->clientId,
			'name'=>mysql::str($data['name']),
			'type_id'=>1,
			'status_id'=>5,
			'user_id'=>$this->user->id,
			'executor_id'=>intval($data['respond']),
			'create_date'=>time(),
			'start_date'=>intval($data['start']),
			'end_date'=>intval($data['end']),
			'budget'=>floatval($data['user_budget']),
			'auditory'=>intval($data['auditory']),
			'budget_spent'=>0);
		
		$this->db->autoupdate()->table('adverties')->data(array($adverties));
		$this->db->execute();
		
		$camp_id = $this->db->insert_id;
		$adverties_sms_phones = array();		
		
		foreach($data['contacts'] as $id=>$status) {
			if(intval($status) && intval($id)) {
				$id = intval($id);
				$this->db->select()->from('crm_contacts_phones')->fields('phone')->where("client_id = {$this->clientId} AND contact_id = {$id}");
				$this->db->execute();
				$phone = $this->db->get_field();
				$phone = preg_replace("/[^0-9]/", "", $phone);
				$adverties_sms_phones[] = array(
					'client_id'=>$this->clientId,
					'campaing_id'=>$camp_id,
					'contact_id'=>$id,
					'phone'=>$phone,
					'text'=>mysql::str($data['text']),
					'status'=>9,
					'sender'=>mysql::str($data['sender']),
					'remote_id'=>0,
					'price'=>round($this->countTextParts(mysql::str($data['text']))*$this->smsprice,2),
					'send_date'=>0);
			}			
		}
		
		$this->db->autoupdate()->table('adverties_sms_phones')->data($adverties_sms_phones);
		$this->db->execute();
		
	}
	
	public function countTextParts($text) {
		$counters['rus'] = array(70, 134, 201, 268, 335, 402, 469, 536);
		$counters['eng'] = array(160, 306, 459, 612, 765, 918, 1071, 1224);
		
		$text = stripslashes($text);
		$text = str_replace('\r\n', " ", $text);
		$text = str_replace("\r\n", " ", $text);
		$text = str_replace("\n", " ", $text);
		$text = str_replace('\n', " ", $text);
		$text = str_replace('&amp;', '&', $text);
		$text = stripslashes($text);
		
		$lang = 'eng';
		$symbols = utf8_strlen($text);
		$counter = 0;
		
		//if(!preg_match("/[ЁёЙйа-яА-Я]/", $text)) $lang = 'eng';
		if(preg_match("/[^(\x20-\x7F\n\r)]+/", $text)) $lang = 'rus';
		
		if($symbols > $counters[$lang][7]) {
			$counter = $counters[$lang][7];
			return $counter;
		}
		
		foreach($counters[$lang] as $index=>$volume) {
			if($symbols <= $volume) {
				$counter = $index+1;
				break;
			}
		}
		
		return $counter;
	}
	
	//
	//$eng = array(160, 306, 459, 612, 765, 918, 1071, 1224);
	
}