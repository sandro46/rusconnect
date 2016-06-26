<?php

class crm_client extends sv_module
{
	public function getTask($instance, $page = 0, $limit = 20, $sortBy = 't.start_date', $sortType = 'DESC', $filters) {
		$start = $page*$limit;
			
		
		$sql = "
			SELECT
				SQL_CALC_FOUND_ROWS
				t.task_id as id, t.comment, t.type_id, t.user_id, t.status_id, t.executor_id,
				u1.name as create_user_name,
				u1.name as creator_name,
				u2.name as executor_name,
				tt.name as type_name,
				st.name as status_name,
				t.start_date as timestamp_start,
				t.start_date as todayStyleTime,
				t.end_date as timestamp_end,
				t.create_date,
				FROM_UNIXTIME(t.start_date, '%d.%m.%Y %H:%i') as start_date, 
				FROM_UNIXTIME(t.end_date, '%d.%m.%Y %H:%i') as end_date,
				FROM_UNIXTIME(t.start_date, '%d.%m.%Y') as start_date_for_js, 
				FROM_UNIXTIME(t.start_date, '%H:%i') as start_time_for_js, 
	
				FROM_UNIXTIME(t.start_date, '%d') as start_date_d,
				FROM_UNIXTIME(t.start_date, '%m') as start_date_m,
				FROM_UNIXTIME(t.start_date, '%Y') as start_date_y,
				FROM_UNIXTIME(t.start_date, '%H') as start_date_h,
				FROM_UNIXTIME(t.start_date, '%i') as start_date_i,
				FROM_UNIXTIME(t.end_date, '%d') as end_date_d,
				FROM_UNIXTIME(t.end_date, '%m') as end_date_m,
				FROM_UNIXTIME(t.end_date, '%Y') as end_date_y,
				FROM_UNIXTIME(t.end_date, '%H') as end_date_h,
				FROM_UNIXTIME(t.end_date, '%i') as end_date_i,
				
				IF(t.contact_id >0, 
					(SELECT cc.name FROM crm_contacts as cc WHERE cc.contact_id = t.contact_id AND cc.client_id = {$this->clientId}),
					(SELECT cd.name FROM crm_deals as cd WHERE cd.deal_id = t.deal_id AND cd.client_id = {$this->clientId})
				) as event_name
				
			FROM 
				crm_task as t
					LEFT JOIN crm_users as u1 ON u1.system_user_id = t.user_id
					LEFT JOIN crm_users as u2 ON u2.system_user_id = t.executor_id
					LEFT JOIN crm_task_types as tt ON tt.type_id = t.type_id
					LEFT JOIN crm_task_statuses as st ON st.status_id = t.type_id
					
			WHERE
				tt.client_id = 0 AND
				st.client_id = 0 AND
				
				t.client_id = {$this->clientId} ";
		
		if(isset($filters['dateFrom']) && isset($filters['dateTo']) && self::testInputDate($filters['dateFrom']) && self::testInputDate($filters['dateTo'])) {
			$sql .= " AND t.start_date >= ".self::makeTimestampFromDate($filters['dateFrom'],true);
			$sql .= " AND t.start_date <= ".self::makeTimestampFromDate($filters['dateTo'],false,true);
		}
		
		if(isset($filters['status']) && intval($filters['status'])) {
			$sql .= " AND t.status_id = {$filters['status']}";
		} else {
			$sql .= " AND t.status_id <> 2 AND t.status_id <> 3";
		}
		
		if(isset($filters['executor']) && intval($filters['executor'])) {
			$sql .= " AND t.executor_id = {$filters['executor']}";
		} else {
			if(isset($filters['executor']) && $filters['executor'] === false) {
				
			} else {
				$sql .= " AND t.executor_id = {$this->user->id}";
			}
			
		}
		
		if(isset($filters['creator']) && intval($filters['creator'])) {
			$sql .= " AND t.user_id = {$filters['creator']}";
		}
		
		if(isset($filters['deal_id']) && intval($filters['deal_id'])) {
			$sql .= " AND t.deal_id = ".intval($filters['deal_id']);
		}
		
		$sql .= " ORDER BY $sortBy $sortType LIMIT $start,$limit";

		//echo $sql;
		$this->db->query($sql);
		$this->db->add_fields_deform(array('event_name', 'comment', 'todayStyleTime'));
		$this->db->add_fields_func(array('crm_client::parseNameForSheduler', 'crm_client::parseNameForSheduler', 'crm_client::todayStyleTime'));
		$this->db->get_rows();
		return $this->db->rows;	
	}
	
	public function getCompanies($instance, $page = 0, $limit = 20, $sortBy = 'date', $sortType = 'DESC', $filters = false) {
		$start = $page*$limit;
		$sql = "
			SELECT
				SQL_CALC_FOUND_ROWS
				c.company_id as id, c.user_id, cu.name as creator_name, c.sv_companies_id, c.date, c.source_id, 
				cs.name as source_name, 
				c.name as company_name,  c.address, c.brand, c.phone, c.site, c.mail,
				c.country_id, c.region_id, c.city_id
			FROM 
				crm_companies as c
					LEFT JOIN crm_users as cu ON cu.system_user_id = c.user_id
					LEFT JOIN crm_companies_sources as cs ON cs.id = c.source_id
			WHERE
				 c.client_id = {$this->clientId} AND 
				 cu.client_id = {$this->clientId}";
		
		if(isset($filters['only_id'])) {
			$filters['only_id'] = intval($filters['only_id']);
			$sql .= " AND c.company_id = {$filters['only_id']}";
		}
		
		if(isset($filters['name'])) {
			$filters['name'] = mysql::str($filters['name']);
			$sql .= " AND LOWER(c.name) LIKE LOWER('%{$filters['name']}%') ";
		}
		
		if(isset($filters['brand'])) {
			$filters['brand'] = mysql::str($filters['brand']);
			$sql .= " AND LOWER(c.brand) LIKE LOWER('%{$filters['brand']}%') ";
		}
		
		if(isset($filters['sourceId']) && intval($filters['sourceId'])) {
			$sql .= " AND c.source_id = {$filters['sourceId']}";
		}
		
		$sql .= " ORDER BY $sortBy $sortType LIMIT $start,$limit";
		
		$this->db->query($sql);
		$this->db->add_fields_deform(array('date'));
		$this->db->add_fields_func(array('dateAgo'));
		$this->db->get_rows();
		
		return $this->db->rows;	
	}
	
	public function getEvents($instance, $page = 0, $limit = 20, $sortBy = 'date', $sortType = 'DESC', $filters = false) {
		$start = $page*$limit;
		$sql = "
			SELECT
				SQL_CALC_FOUND_ROWS
				e.event_id as id, e.date, e.user_id, e.event_type_id, e.company_id, e.contact_id, e.deal_id, e.task_id, e.value_old, e.value_new, e.comment,
				u.name as user_name,
				t.name as type_name,
				IF(e.company_id, (SELECT c.name FROM crm_companies as c WHERE c.company_id = e.company_id AND c.client_id = {$this->clientId}), '-') as company,
				IF(e.contact_id, (SELECT c.name FROM crm_contacts as c WHERE c.contact_id = e.contact_id AND c.client_id = {$this->clientId}), '-') as contact,
				IF(e.deal_id, (SELECT d.name FROM crm_deals as d WHERE d.deal_id = e.deal_id AND d.client_id = {$this->clientId}), '-') as deal
			FROM
				crm_events as e
					LEFT JOIN crm_users as u ON u.system_user_id = e.user_id
					LEFT JOIN crm_events_types as t ON t.id = e.event_type_id
			WHERE
				e.client_id = {$this->clientId} ";
		
		if(isset($filters['type_id']) && intval($filters['type_id'])) {
			$sql .= " AND e.event_type_id = {$filters['type_id']}";
		}
		
		if(isset($filters['user_id']) && intval($filters['user_id'])) {
			$sql .= " AND e.user_id = {$filters['user_id']}";
		}
		
		
		$sql .= " ORDER BY $sortBy $sortType LIMIT $start,$limit";
		
		$this->db->query($sql);
		$this->db->add_fields_deform(array('date', 'company', 'contact', 'deal'));
		$this->db->add_fields_func(array('dateAgo', 'replaceNull,"-"', 'replaceNull,"-"', 'replaceNull,"-"'));
		$this->db->get_rows();
		
		return $this->db->rows;	
	}
	
	public function getTaskForGrid($instance, $page = 0, $limit = 20, $sortBy = 'date', $sortType = 'DESC', $filters = false) {
		$start = $page*$limit;
		$sql = "
			SELECT
				SQL_CALC_FOUND_ROWS
				t.task_id as id, t.deal_id, t.contact_id, t.type_id, t.user_id, t.status_id, t.executor_id, t.create_date, t.start_date, t.end_date, t.comment,
				tt.name as type_name,
				u1.name as user_name,
				s.name as status_name,
				u2.name as executor_name,
				FROM_UNIXTIME(t.start_date, '%d.%m.%Y') as start_date_formated,
				FROM_UNIXTIME(t.start_date, '%H:%i') as start_time_formated,
				IF(t.deal_id, 
					(SELECT CONCAT('Сделка - ',d.name) FROM crm_deals as d WHERE d.client_id = {$this->clientId} AND d.deal_id = t.deal_id), 
					(SELECT CONCAT('Контакт - ',c.name) FROM crm_contacts as c WHERE c.client_id = {$this->clientId} AND c.contact_id = t.contact_id)
				) as task_object,
				IF(t.deal_id, 
					(SELECT d.name FROM crm_deals as d WHERE d.client_id = {$this->clientId} AND d.deal_id = t.deal_id), 
					(SELECT c.name FROM crm_contacts as c WHERE c.client_id = {$this->clientId} AND c.contact_id = t.contact_id)
				) as task_object_name,
				IF(t.deal_id, 'Сделка', 'Контакт') as task_type_text
				
			FROM 
				crm_task as t
					LEFT JOIN crm_task_types as tt ON tt.type_id = t.type_id
					LEFT JOIN crm_users as u1 ON u1.system_user_id = t.user_id
					LEFT JOIN crm_users as u2 ON u2.system_user_id = t.executor_id
					LEFT JOIN crm_task_statuses as s ON s.status_id = t.status_id
			WHERE
				t.client_id = {$this->clientId} AND
				tt.client_id = 0 AND

				s.client_id = 0  
				";
		
		if(isset($filters['execId']) && intval($filters['execId'])) {
			$sql .= " AND t.executor_id = {$filters['execId']}";
		}
		
		if(isset($filters['typeId']) && intval($filters['typeId'])) {
			$sql .= " AND t.type_id = {$filters['typeId']}";
		}
		
		if(isset($filters['statusId'])) {
			if($filters['statusId'] != 0) {
				$sql .= " AND t.status_id = {$filters['statusId']}";
			}
		} else {
			$sql .= " AND t.status_id =1";
		}
		
		if(isset($filters['task_id'])) {
			$filters['task_id'] = intval($filters['task_id']);
			$sql .= " AND t.task_id = {$filters['task_id']}";
		}
		
		$sql .= " ORDER BY $sortBy $sortType LIMIT $start,$limit";
		$this->db->query($sql);
		$this->db->add_fields_deform(array('create_date', 'start_date', 'end_date'));
		$this->db->add_fields_func(array('dateAgo', 'crm_client::todayStyleTime', 'crm_client::todayStyleTime'));
		$this->db->get_rows((isset($filters['task_id']))?1:false);
		
		return $this->db->rows;	
	}
	
	public function getContacts($instance, $page = 0, $limit = 20, $sortBy = 'date', $sortType = 'DESC', $filters = false) {
		$start = $page*$limit;
		
		$sql = "
			SELECT
				SQL_CALC_FOUND_ROWS
				cc.contact_id as id, cc.user_id, cc.date, FROM_UNIXTIME(cc.date, '%d.%m.%Y') as date_d, cc.company_id, cc.name as contact_name, cc.responsible_user_id, cc.post, cc.departament, cc.source_id, cc.comment,
				get_crm_user_name(cc.user_id) as create_user_name,
				get_crm_user_name(cc.responsible_user_id) as responsible_user_name,
				get_crm_contact_source_name(cc.source_id, {$this->clientId}) as source_name,
				IFNULL((SELECT cmp.name FROM crm_companies as cmp WHERE cmp.company_id = cc.company_id AND cmp.client_id = {$this->clientId}),'Физ. лицо') as company_name,
				(SELECT COUNT(*) FROM crm_contacts_phones as ccp WHERE ccp.contact_id = cc.contact_id) as cnt
				
			FROM crm_contacts as cc		
				
			WHERE
				cc.client_id = {$this->clientId} ";
		
		if(isset($filters['sourceId']) && intval($filters['sourceId'])) {
			$sql .= " AND cc.source_id = {$filters['sourceId']}";
		}
		
		if(isset($filters['id']) && intval($filters['id'])) {
			$sql .= " AND cc.contact_id = {$filters['id']}";
		}
		
		if(isset($filters['company_id']) && intval($filters['company_id'])) {
			$filters['company_id'] = intval($filters['company_id']);
			$sql .= " AND cc.company_id = {$filters['company_id']} ";
		}
		
		if(isset($filters['contact_name']) && strlen($filters['contact_name'])) {
			$filters['contact_name'] = mysql::str($filters['contact_name']);
			$sql .= " AND LOWER(cc.name) LIKE LOWER('%{$filters['contact_name']}%') ";
		}
		
		
		if(isset($filters['company']) && strlen($filters['company'])) {
			$filters['company'] = mysql::str($filters['company']);
			$sql .= " AND cc.company_id IN(SELECT cmp2.company_id FROM crm_companies as cmp2 WHERE cmp2.client_id = {$this->clientId} AND LOWER(cmp2.name) LIKE LOWER('%{$filters['company']}%')) ";
		}
			
		$sql .= " ORDER BY $sortBy $sortType LIMIT $start,$limit";
		
		if($this->core->debug) {
			//echo $sql;
		}
		
		$this->db->query($sql);
		$this->db->add_fields_deform(array('date'));
		$this->db->add_fields_func(array('dateAgo'));
		$this->db->get_rows();
		
		return $this->db->rows;	
	}

	public function getContactsOnlyId() {
		$sql = "SELECT c.contact_id as id, (SELECT COUNT(*) FROM crm_contacts_phones as ccp WHERE ccp.contact_id = c.contact_id) as cnt FROM crm_contacts as c WHERE c.client_id = {$this->clientId}";
		$this->db->query($sql);
		$this->db->get_rows();
		return $this->db->rows;	
	}
	
	public function getDealMap($id) {
		if(!$id = intval($id)) return false;
		$deal = $this->getDeals('map', 0, 1, 'date','desc',array('id'=>$id));
		
		if(!is_array($deal)) return false;
		
		$info = array(
			'deal'=>$deal,
			'contacts'=>$this->getContacts('map', 0, 9999, 'id','desc',array('company_id'=>$deal['company_id'])),
			'task'=>$this->getTask('map',0,9999, 'id','desc',array('deal_id'=>$id, 'executor'=>false)),
			'comments'=>$this->getDealComments($id)
		);
		
		return $info;
	}
	
	public function saveDeal($id, $info) {
		$id = ($id && intval($id))? intval($id) : false;
		
		$info['volume'] = preg_replace("/[^0-9]/",'',$info['volume']);
		
		if($id) {
			$crm_deals = array(
				'deal_id'=>$id,
				'client_id'=>$this->clientId,
				'company_id'=>intval($info['company']),
				'user_id'=>$this->user->id,
				'source_id'=>0,
				'contract_id'=>0,
				'responsible_user_id'=>intval($info['resp']),
				'name'=>mysql::str($info['name']),
				'contract_summ'=>mysql::str($info['volume']),
				'status_id'=>intval($info['status']),
				'phase_id'=>intval($info['phase']),
				'product_name'=>mysql::str($info['product'])
			);
			
			$this->db->autoupdate()->table('crm_deals')->data(array($crm_deals))->primary('deal_id', 'client_id');
			$this->db->execute();
			
			$crm_events = array(
				'client_id'=>$this->clientId,
				'date'=>time(),
				'user_id'=>$this->user->id,
				'event_type_id'=>11,
				'company_id'=>$crm_deals['company_id'],
				'contact_id'=>0,
				'deal_id'=>$id,
				'task_id'=>0
			);
			
			$this->db->autoupdate()->table('crm_events')->data(array($crm_events));
			$this->db->execute();
			
			return $id;
		}
		
		
		$crm_deals = array(
			'client_id'=>$this->clientId,
			'company_id'=>intval($info['company']),
			'user_id'=>$this->user->id,
			'source_id'=>0,
			'contract_id'=>0,
			'responsible_user_id'=>intval($info['resp']),
			'date'=>time(),
			'name'=>mysql::str($info['name']),
			'contract_summ'=>mysql::str($info['volume']),
			'status_id'=>1,
			'phase_id'=>intval($info['phase']),
			'product_name'=>mysql::str($info['product'])
		);
		
		$this->db->autoupdate()->table('crm_deals')->data(array($crm_deals));
		$this->db->execute();
		$newDealId = $this->db->insert_id;
		
		$crm_events = array(
			'client_id'=>$this->clientId,
			'date'=>time(),
			'user_id'=>$this->user->id,
			'event_type_id'=>5,
			'company_id'=>0,
			'contact_id'=>0,
			'deal_id'=>$newDealId,
			'task_id'=>0
		);
			
		$this->db->autoupdate()->table('crm_events')->data(array($crm_events));
		$this->db->execute();
	
		
		
		return $newDealId;
	}
	
	public function getDeals($instance, $page = 0, $limit = 20, $sortBy = 'date', $sortType = 'DESC', $filters = false) {
		$start = $page*$limit;
		$sql = "
			SELECT
				SQL_CALC_FOUND_ROWS
				d.deal_id as id, d.company_id, d.user_id, d.source_id, d.contract_id, d.responsible_user_id, d.date,
				d.name, d.contract_summ, d.status_id, d.phase_id, d.product_name,
				(SELECT cmp.name FROM crm_companies as cmp WHERE cmp.company_id = d.company_id AND cmp.client_id = {$this->clientId}) as company_name,
				cu1.name as create_user_name,
				cu2.name as responsible_user_name,
				s.name as status_name,
				p.name as phase_name,
				IF(d.contract_id, 
					(SELECT CONCAT('Договор №',cntr.doc_num,' от ', FROM_UNIXTIME(cntr.date, '%d.%m.%Y')) FROM sv_contracts as cntr WHERE cntr.id = d.contract_id),
					' - '
				) as contract_name,
				IF(d.contract_id, 
					(SELECT cntr.doc_num FROM sv_contracts as cntr WHERE cntr.id = d.contract_id),
					''
				) as contract_doc_num,
				IF(d.contract_id, 
					(SELECT FROM_UNIXTIME(cntr.date, '%d.%m.%Y') FROM sv_contracts as cntr WHERE cntr.id = d.contract_id),
					''
				) as contract_date,
				IF(d.contract_id, 
					(SELECT 	status_id FROM sv_contracts as cntr WHERE cntr.id = d.contract_id),
					''
				) as contract_status_id
			FROM 	
				crm_deals as d
					LEFT JOIN crm_users as cu1 ON cu1.system_user_id = d.user_id
					LEFT JOIN crm_users as cu2 ON cu2.system_user_id = d.responsible_user_id
					LEFT JOIN crm_deals_statuses as s ON s.id = d.status_id
					LEFT JOIN crm_deals_phases as p ON p.id = d.phase_id
				
			WHERE
				d.client_id = {$this->clientId}
				";
		
		if(isset($filters['status_id']) && intval($filters['status_id'])) {
			$sql .= " AND d.status_id = {$filters['status_id']}";
		}
		
		if(isset($filters['phase_id']) && intval($filters['phase_id'])) {
			$sql .= " AND d.phase_id = {$filters['phase_id']}";
		}
		
		if(isset($filters['executor_id']) && intval($filters['executor_id'])) {
			$sql .= " AND d.responsible_user_id = {$filters['executor_id']}";
		}
		
		if(isset($filters['id']) && intval($filters['id'])) {
			$sql .= " AND d.deal_id = {$filters['id']}";
		}
			
		$sql .= " ORDER BY $sortBy $sortType LIMIT $start,$limit";
		
		$this->db->query($sql);
		$this->db->add_fields_deform(array('date', 'contract_summ'));
		$this->db->add_fields_func(array('dateAgo','parce_rub'));
		$this->db->get_rows((isset($filters['id']))? 1 : false);

		return $this->db->rows;
	}
	
	public function saveCompany($id=false, $data) {
		$id = ($id && intval($id))? intval($id) : false;
		
		if(isset($data['add_buh']) && $data['add_buh']) {
			$sv_company_id = 0;
			
			if($id) {
				$this->db->select()->from('crm_companies')->fields('sv_companies_id')->where("company_id = {$id} AND client_id = {$this->clientId}");
				$this->db->execute();	
				$sv_company_id = intval($this->db->get_field())+0;
			}
			
			$buh = new client_bugalter();
			$sv_company_id = $buh->saveCompany($sv_company_id,$data, 'no-update-name');				
		}
			
		if(!isset($data['type']) || !intval($data['type'])) return -1;
		if(!isset($data['name']) || strlen($data['name']) <2) return -2;
		if(isset($data['bik']) && strlen($data['bik'] >1) && !bugalter_valid::checkBik($data['bik'])) return -4; 
		if(isset($data['bill']) && strlen($data['bill'] >1) && !bugalter_valid::bankAccountCheck($data['bill'])) return -5;
		if(isset($data['inn']) && strlen($data['inn'] >1) && !bugalter_valid::innCheck($data['inn'])) return -6;
		if(isset($data['kpp']) && strlen($data['kpp'] >1) && strlen($data['kpp']) > 5 && !bugalter_valid::kppCheck($data['kpp'])) return -7;
		if(isset($data['ogrn']) && strlen($data['ogrn'] >1) && strlen($data['ogrn']) > 5 && !bugalter_valid::orgnCheck($data['ogrn'])) return -7;
			
		$crm_companies = array(
			'client_id'=>$this->clientId,
			'user_id'=>$this->user->id,
			'date'=>time(),
			'sv_companies_id'=>$sv_company_id,
			'source_id'=>intval($data['source']),
			'name'=>mysql::str($data['crm_name']),
			'brand'=>mysql::str($data['brand']),
			'phone'=>mysql::str($data['phone']),
			'site'=>mysql::str($data['site']),
			'mail'=>mysql::str($data['mail']),
			'address'=>mysql::str($data['address'])
		);
		
		if($id) {
			$crm_companies['company_id'] = $id;	
			$this->db->autoupdate()->table('crm_companies')->data(array($crm_companies))->primary('company_id', 'client_id');
			$this->db->execute();
			return $crm_companies['company_id'];
		} else {
			$this->db->autoupdate()->table('crm_companies')->data(array($crm_companies));
			$this->db->execute();
			$crm_id = $this->db->insert_id;
			
			if(isset($data['contacts']) && is_array($data['contacts']) && count($data['contacts']) > 0) {
				$crm_contacts = array();
				foreach($data['contacts'] as $item) {
					$crm_contacts[] = array(
						'client_id'=>$this->clientId,
						'user_id'=>$this->user->id,
						'date'=>time(),
						'company_id'=>$crm_id,	
						'name'=>mysql::str($item['name']),
						'responsible_user_id'=>intval($item['response']),
						'post'=>mysql::str($item['post']),
						'source_id'=>intval($item['source'])
					);
				}
				
				$this->db->autoupdate()->table('crm_contacts')->data($crm_contacts);
				$this->db->execute();
			}
			
			$crm_events = array(
				'client_id'=>$this->clientId,
				'date'=>time(),
				'user_id'=>$this->user->id,
				'event_type_id'=>4,
				'company_id'=>$crm_id,
				'contact_id'=>0,
				'deal_id'=>0,
				'task_id'=>0
			);
			
			$this->db->autoupdate()->table('crm_events')->data(array($crm_events));
			$this->db->execute();
			
			return $crm_id;
		}
	}
	
	public function getCompanyInfo($id) {
		if(!$id = intval($id)) return false;
		
		$info = $this->getCompanies('companies',0,1,'c.company_id','asc',array('only_id'=>$id));
		if(!is_array($info) || !isset($info[0])) return false;
		$info = $info[0];
		
		if(isset($info['sv_companies_id']) && intval($info['sv_companies_id'])) {
			$buh = new client_bugalter();
			$info['bugalter'] = $buh->getCompanyInfo($info['sv_companies_id']);
		}
		
		$info['contacts'] = $this->getContacts('contacts', 0, 10, 'cc.date', 'DESC', array('company_id'=>$id));
		
		return $info;
	}
	
	public function deleteCompany($id) {
		if(!$id = intval($id)) return false;
		$sql = "DELETE FROM crm_companies WHERE company_id = {$id} AND client_id = {$this->clientId}";
		$this->db->query($sql);
		$sql = "DELETE FROM crm_contacts WHERE company_id = {$id} AND client_id = {$this->clientId}";
		$this->db->query($sql);
		$sql = "DELETE FROM crm_deals WHERE company_id = {$id} AND client_id = {$this->clientId}";
		$this->db->query($sql);

		return true;
	}
	
	public function updateDealMap($dealId, $info) {
		if(!$dealId = intval($dealId)) return false;
		if(!is_array($info) || !isset($info['task']) || !isset($info['comments'])) return false;
		
		$dealInfo = $this->getDeals('map',0,1,'date','desc',array('id'=>$dealId));
		
		$sql = "DELETE FROM crm_task WHERE deal_id = {$dealId} AND client_id = {$this->clientId}";
		$this->db->query($sql);
		$sql = "DELETE FROM crm_deals_comments WHERE deal_id = {$dealId} AND client_id = {$this->clientId}";
		$this->db->query($sql);
		
		if(is_array($info['comments']) && count($info['comments'])) {
			$data = array();
			foreach($info['comments'] as $item) {
				$timestamp = (isset($item['create_time']))? intval($item['create_time']) : time();
				$data[] = array(
					'client_id'=>$this->clientId,
					'deal_id'=>$dealId,
					'user_id'=>$this->user->id,
					'create_date'=>$timestamp,
					'comment'=>mysql::str($item['text'])
				);
			}
			
			$this->db->autoupdate()->table('crm_deals_comments')->data($data);
			$this->db->execute();
			
			$crm_events = array(
				'client_id'=>$this->clientId,
				'date'=>time(),
				'user_id'=>$this->user->id,
				'event_type_id'=>9,
				'company_id'=>$dealInfo['company_id'],
				'contact_id'=>0,
				'deal_id'=>$dealId,
				'task_id'=>0
			);
			
			$this->db->autoupdate()->table('crm_events')->data(array($crm_events));
			$this->db->execute();
		}
		
		if(is_array($info['task']) && count($info['task'])) {
			$data = array();
			foreach($info['task'] as $item) {
				$timestamp = (isset($item['create_time']))? intval($item['create_time']) : time();
				$data[] = array(
					'client_id'=>$this->clientId,
					'deal_id'=>$dealId,
					'user_id'=>$this->user->id,
					'create_date'=>$timestamp,
					'contact_id'=>0,
					'type_id'=>intval($item['type']),
					'status_id'=>($item['status'])? $item['status'] : 1,
					'executor_id'=>intval($item['response']),
					'create_date'=>$timestamp,
					'start_date'=>$this->makeStamp($item['date'], $item['time']),
					'end_date'=>$this->makeStamp($item['date'], $item['time']),
					'comment'=>mysql::str($item['text'])
				);
			}
			
			//print_r($data);
			$this->db->autoupdate()->table('crm_task')->data($data);
			$this->db->execute();
			
			$crm_events = array(
				'client_id'=>$this->clientId,
				'date'=>time(),
				'user_id'=>$this->user->id,
				'event_type_id'=>10,
				'company_id'=>$dealInfo['company_id'],
				'contact_id'=>0,
				'deal_id'=>$dealId,
				'task_id'=>0
			);
			
			$this->db->autoupdate()->table('crm_events')->data(array($crm_events));
			$this->db->execute();
		}
		
		return true;
	}
	
	public function moveTask($id, $start, $end) {
		if(!$id = intval($id)) return false;
		
		$start = mktime(intval($start['h']), intval($start['i']), 0, intval($start['m']), intval($start['d']), intval($start['y']));
		$end =   mktime(intval($end['h']), intval($end['i']), 0, intval($end['m']), intval($end['d']), intval($end['y']));
		
		$crm_task = array(
			'task_id'=>$id,
			'client_id'=>$this->clientId,
			'start_date'=>$start
		);
		
		if($end) {
			$crm_task['end_date'] = $end;
		}
		
		$this->db->autoupdate()->table('crm_task')->data(array($crm_task))->primary('client_id', 'task_id');
		$this->db->execute();
		
		return true;
	}
	
	public function getSourcesList() {
		$this->db->select()->from('crm_contacts_sources')->fields('source_id id', 'name')->where("client_id = {$this->clientId}");
		$this->db->execute();
		$this->db->get_rows();
		
		//if($_SERVER['REMOTE_ADDR'] == '37.230.182.169') {
		//	$this->db->debug();
			
		//}
		
		return $this->db->rows;
	}
	
	public function getClientUsers() {
		$this->db->select()->from('crm_users')->fields('*')->where("(client_id = {$this->clientId} OR system_user_id = {$this->user->id}) AND enable = 1");
		$this->db->execute();
		$this->db->get_rows();
	
		/* fix reg error. delete me
		if($_SERVER['REMOTE_ADDR'] == '37.230.182.169') {
			$this->db->debug();
			
			$sql = "SELECT id FROM sv_clients WHERE 1";
			$this->db->query($sql);
			$this->db->get_rows();
			$comp = $this->db->rows;
			
			$data = array();
			
			foreach($comp as $item) {
				$data[] = array('client_id'=>$item['id'], 'name'=>'Холодные звонки');
				$data[] = array('client_id'=>$item['id'], 'name'=>'Рекламная кампания skidki.ncity.biz');
				$data[] = array('client_id'=>$item['id'], 'name'=>'СМС Рассылка');
				$data[] = array('client_id'=>$item['id'], 'name'=>'Email рассылка');
				$data[] = array('client_id'=>$item['id'], 'name'=>'Заявка на портале');			
			}
			
			$this->db->autoupdate()->table('crm_contacts_sources')->data($data);
			$this->db->execute();
			$this->db->debug();
			
			$this->db->autoupdate()->table('crm_companies_sources')->data($data);
			$this->db->execute();
			$this->db->debug();
		
			die();
		}
		*/
		
		
		return $this->db->rows;
	}
	
	public function getPhoneTypes() {
		$this->db->select()->from('crm_contacts_phones_types')->fields('*');
		$this->db->execute();
		$this->db->get_rows();
		
		return $this->db->rows;
	}
	
	public function getMesengerTypes() {
		$this->db->select()->from('crm_contacts_messengers_types')->fields('*');
		$this->db->execute();
		$this->db->get_rows();
		
		return $this->db->rows;
	}
	
	public function getEmailTypes() {
		$this->db->select()->from('crm_contacts_email_types')->fields('*');
		$this->db->execute();
		$this->db->get_rows();
		
		return $this->db->rows;
	}
	
	public function getEventsTypes() {
		$this->db->select()->from('crm_events_types')->fields('*');
		$this->db->execute();
		$this->db->get_rows();
		
		return $this->db->rows;
	}
	
	public function getTaskTypes() {
		$this->db->select()->from('crm_task_types')->fields('type_id id', 'name')->where('client_id = 0');
		$this->db->execute();
		$this->db->get_rows();
		
		return $this->db->rows;
	}
	
	public function getTaskStatusTypes() {
		$this->db->select()->from('crm_task_statuses')->fields('status_id id', 'name')->where('client_id = 0');
		$this->db->execute();
		$this->db->get_rows();
		
		return $this->db->rows;
	}
	
	public function getDealPhases() {
		$this->db->select()->from('crm_deals_phases')->fields('id', 'name');
		$this->db->execute();
		$this->db->get_rows();
		
		return $this->db->rows;
	}
	
	public function getDealStatuses() {
		$this->db->select()->from('crm_deals_statuses')->fields('id', 'name');
		$this->db->execute();
		$this->db->get_rows();
		
		return $this->db->rows;
	}
	
	public function getContactInfo($id) {
		if(!$id = intval($id)) return false;
		$info = $this->getContacts('contacts', 0, 1, 'date', 'desc',array('id'=>$id));
		if(!is_array($info) || count($info) != 1 || !isset($info[0]) || !isset($info[0]['id'])) return false;
		$info = $info[0];
		
		$info['phone'] = $this->getContactPhone($id);
		$info['email'] = $this->getContactEmail($id);
		$info['comments'] = $this->getContactComments($id);
		$info['task'] = $this->getContactTasks($id);
		
		return $info;
	}
	
	public function getContactPhone($id) {
		if(!$id = intval($id)) return false;
		$sql = "SELECT cp.phone, cpt.name FROM crm_contacts_phones as cp LEFT JOIN crm_contacts_phones_types as cpt ON cpt.id = cp.phone_type WHERE cp.client_id = {$this->clientId} AND cp.contact_id = {$id}";
		$this->db->query($sql);
		$this->db->get_rows();
		
		return $this->db->rows;
	}
	
	public function getContactEmail($id) {
		if(!$id = intval($id)) return false;
		$sql = "SELECT ce.email, cet.name FROM crm_contacts_email as ce LEFT JOIN on cet.id = ce.email_type WHERE ce.client_id = {$this->clientId} AND ce.contact_id = {$id}";
		$this->db->query($sql);
		$this->db->get_rows();
		
		return $this->db->rows;
	}
	
	public function getContactComments($id) {
		if(!$id = intval($id)) return false;
		$sql = "SELECT cc.comment_id as id, cc.user_id, cu.name as user_name, cc.text FROM crm_contacts_comments as cc LEFT JOIN crm_users as cu ON cu.id = cc.user_id WHERE cc.client_id = {$this->clientId} AND cc.contact_id = {$id}";
		$this->db->query($sql);
		$this->db->get_rows();
		
		return $this->db->rows;
	}
	
	public function getDealComments($id) {
		if(!$id = intval($id)) return false;
		
		$sql = "SELECT c.user_id, cu.name as user_name, c.create_date, c.comment FROM crm_deals_comments as c LEFT JOIN crm_users as cu ON cu.id = c.user_id WHERE c.client_id = {$this->clientId} AND c.deal_id = {$id}";
		$this->db->query($sql);
		$this->db->get_rows();
		
		return $this->db->rows;
	}
	
	public function getContactTasks($id) {
		if(!$id = intval($id)) return false;
		$sql = "SELECT 
					ct.task_id as id, ct.type_id, ct.user_id, cu1.name as creator_name, cu2.name as executor_name, ct.status_id, ct.executor_id,  
					FROM_UNIXTIME(ct.create_date, '%d.%m.%Y %H:%i') as create_date,   
					FROM_UNIXTIME(ct.start_date, '%d.%m.%Y %H:%i') as start_date, 
					FROM_UNIXTIME(ct.start_date, '%d.%m.%Y') as start_date_d,
					FROM_UNIXTIME(ct.start_date, '%H:%i') as start_date_h,  
					ct.comment 
				FROM crm_task as ct
					LEFT JOIN crm_users as cu1 ON cu1.system_user_id = ct.user_id
					LEFT JOIN crm_users as cu2 ON cu2.system_user_id = ct.executor_id
				WHERE
					ct.client_id = {$this->clientId} AND
					ct.contact_id = {$id} ORDER BY ct.start_date ASC";
		
		$this->db->query($sql);
		$this->db->get_rows();
		
		return $this->db->rows;
	}
	
	public function saveTask($id, $info) {
		$id = intval($id);
		
		if(!isset($info['deal_id'])) $info['deal_id'] = 0;
		if(!isset($info['contact_id'])) $info['contact_id'] = 0;
		
		$crm_task = array(
			'client_id'=>$this->clientId,
			'deal_id'=>intval($info['deal_id']),
			'contact_id'=>intval($info['contact_id']),
			'type_id'=>intval($info['type_id']),
			'status_id'=>intval($info['status']),
			'executor_id'=>intval($info['executor']),
			'start_date'=>$this->makeStamp($info['date'], $info['time']),
			'end_date'=>$this->makeStamp($info['date'], $info['time']),
			'comment'=>mysql::str($info['comment'])
		);
		
		if($id) {
			$crm_task['task_id'] = $id;
			$this->db->autoupdate()->table('crm_task')->data(array($crm_task))->primary('client_id','task_id');
		} else {
			$crm_task['user_id'] = $this->user->id;
			$crm_task['create_date'] = time();
			$this->db->autoupdate()->table('crm_task')->data(array($crm_task));
		}
		
		
		$this->db->execute();
		
		
		if($id) {
			
			$crm_events = array(
				'client_id'=>$this->clientId,
				'date'=>time(),
				'user_id'=>$this->user->id,
				'event_type_id'=>12,
				'company_id'=>0,
				'contact_id'=>intval($info['contact_id']),
				'deal_id'=>intval($info['deal_id']),
				'task_id'=>0
			);
			
			$this->db->autoupdate()->table('crm_events')->data(array($crm_events));
			$this->db->execute();
			
		} else {
			
			$crm_events = array(
				'client_id'=>$this->clientId,
				'date'=>time(),
				'user_id'=>$this->user->id,
				'event_type_id'=>3,
				'company_id'=>0,
				'contact_id'=>intval($info['contact_id']),
				'deal_id'=>intval($info['deal_id']),
				'task_id'=>0
			);
			
			$this->db->autoupdate()->table('crm_events')->data(array($crm_events));
			$this->db->execute();
		}
		
		return true;
	}
	
	public function saveContact($id, $data) {
	
		$crm_contacts = array(
			'client_id'=>$this->clientId,
			'user_id'=>$this->user->id,
			'date'=>time(),
			'name'=>mysql::str($data['name']),
			'responsible_user_id'=>intval($data['response']),
			'post'=>mysql::str($data['post']),
			'departament'=>'',
			'source_id'=>intval($data['source']),
			'comment'=>''
		);
		
		
		$company = $this->getCompaniesReference($data['company']);
		if(is_array($company) && isset($company[0]) && isset($company[0]['id'])) {
			$crm_contacts['company_id'] = $company[0]['id'];
			$isNewCompany = false;
		} else {
			$crm_companies = array(
				'client_id'=>$this->clientId,
				'user_id'=>$this->user->id,
				'date'=>time(),
				'sv_companies_id'=>0,
				'source_id'=>$crm_contacts['source_id'],
				'name'=>mysql::str($data['company']),
				'brand'=>'',
				'address'=>'',
				'country_id'=>'',
				'region_id'=>'',
				'city_id'=>''
			);
			
			$this->db->autoupdate()->table('crm_companies')->data(array($crm_companies));
			$this->db->execute();
			$crm_contacts['company_id'] = $this->db->insert_id;
			$isNewCompany = true;
		}
		
		$this->db->autoupdate()->table('crm_contacts')->data(array($crm_contacts));
		$this->db->execute();
		
		$contactId = $this->db->insert_id;
		
		$crm_events = array(
			'client_id'=>$this->clientId,
			'date'=>time(),
			'user_id'=>$this->user->id,
			'event_type_id'=>1,
			'company_id'=>$crm_contacts['company_id'],
			'contact_id'=>$contactId,
			'deal_id'=>0,
			'task_id'=>0
		);
		
		$this->db->autoupdate()->table('crm_events')->data(array($crm_events));
		$this->db->execute();
	
		if($isNewCompany) {
			$crm_events = array(
				'client_id'=>$this->clientId,
				'date'=>time(),
				'user_id'=>$this->user->id,
				'event_type_id'=>4,
				'company_id'=>$crm_contacts['company_id'],
				'contact_id'=>0,
				'deal_id'=>0,
				'task_id'=>0
			);
			
			$this->db->autoupdate()->table('crm_events')->data(array($crm_events));
			$this->db->execute();
		}

		if(is_array($data['phone']) && count($data['phone']) >0) {
			$crm_contacts_phones = array();
			foreach($data['phone'] as $phone) {
				$crm_contacts_phones[] = array('client_id'=>$this->clientId, 'contact_id'=>$contactId, 'phone_type'=>intval($phone['type']), 'phone'=>mysql::str($phone['name']));
			}
			
			$this->db->autoupdate()->table('crm_contacts_phones')->data($crm_contacts_phones);
			$this->db->execute();
		}
		
		if(is_array($data['email']) && count($data['email']) >0) {
			$crm_contacts_email = array();
			foreach($data['email'] as $email) {
				$crm_contacts_email[] = array('client_id'=>$this->clientId, 'contact_id'=>$contactId, 'email_type'=>intval($email['type']), 'email'=>mysql::str($email['name']));
			}
			
			$this->db->autoupdate()->table('crm_contacts_email')->data($crm_contacts_email);
			$this->db->execute();
		}
		
		if(is_array($data['comments']) && count($data['comments'])) {
			$crm_contacts_comments = array();
			foreach($data['comments'] as $comment) {
				$crm_contacts_comments[] = array('client_id'=>$this->clientId, 'contact_id'=>$contactId, 'user_id'=>$this->user->id, 'text'=>nl2br(mysql::str($comment)));	
			}
			
			$this->db->autoupdate()->table('crm_contacts_comments')->data($crm_contacts_comments);
			$this->db->execute();
		}
		
		if(is_array($data['task']) && count($data['task'])) {
			$crm_task = array();
			foreach($data['task'] as $task) {
				$crm_task[] = array(
					'client_id'=>$this->clientId,
					'deal_id'=>0,
					'type_id'=>intval($task['type']),
					'user_id'=>$this->user->id,
					'status_id'=>1,
					'contact_id'=>$contactId,
					'executor_id'=>intval($task['response']),
					'create_date'=>time(),
					'start_date'=>$this->makeStamp($task['date'], $task['time']),
					'end_date'=>$this->makeStamp($task['date'], $task['time'], 15),
					'comment'=>mysql::str($task['text'])
				);
			}

			$this->db->autoupdate()->table('crm_task')->data($crm_task);
			$this->db->execute();
		}
		
		return $data;
	}
	
	public function getCRMuserInfo() {
		return $this->clientInfo;	
	}
	
	public function autocomplete($instance, $request, $extended = false) {		
		$request = addslashes(decode_unicode_url($request)); // пришло скорее всего в utf-8, нужно почистить строку
		$request = preg_replace("/[^а-яА-ЯёЁa-zA-Z0-9\ \-\_\+\=]+/isu",'',$request); // убираем все спец-вимволы
		
		if($instance == 'companies' && strlen($request) > 1) {
			return $this->getCompaniesReference($request);	
		}
	}
	
	public function find($request) {
		$request = addslashes(decode_unicode_url($request)); // пришло скорее всего в utf-8, нужно почистить строку
		$request = preg_replace("/[^а-яА-ЯёЁa-zA-Z0-9\ \-\_\+\=]+/isu",'',$request); // убираем все спец-вимволы
		
		return array($request, $this->getCompaniesReference($request));	
	}
	
	public function getCompaniesReference($search) {
		$sql = " SELECT c.company_id as id, c.name as value FROM crm_companies as c WHERE c.client_id = {$this->clientId} AND LOWER(c.name) LIKE LOWER('%{$search}%') ORDER BY c.name LIMIT 10";
		$this->db->query($sql);
		$this->db->get_rows();

		return $this->db->rows;
	}
	
	public function getCompSourcesList() {
		$this->db->select()->from('crm_companies_sources')->fields('id', 'name')->where("client_id = {$this->clientId}");
		$this->db->execute();
		$this->db->get_rows();
		
		return $this->db->rows;
	}
	
	public function deleteContact($id) {
		if(!$id = intval($id)) return false;
		$sql = "DELETE FROM crm_contacts WHERE client_id = {$this->clientId} AND contact_id = {$id}";
		$this->db->query($sql);
		$sql = "DELETE FROM crm_contacts_comments WHERE client_id = {$this->clientId} AND contact_id = {$id}";
		$this->db->query($sql);
		$sql = "DELETE FROM crm_contacts_email WHERE client_id = {$this->clientId} AND contact_id = {$id}";
		$this->db->query($sql);
		$sql = "DELETE FROM crm_contacts_phones WHERE client_id = {$this->clientId} AND contact_id = {$id}";
		$this->db->query($sql);
		
		return true;	
	}
	
	private function makeStamp($date, $time, $addMin = 0) {
		$date = explode('.',$date);
		$time = explode(':',$time);
		if($addMin) {
			$time[1] += $addMin;
		}
		
		return mktime($time[0], $time[1], 0, $date[1], $date[0], $date[2]);
	}
	
	public static function testInputDate($str) {
		$str = addslashes($str);
		if(preg_match('/^[0-9]{2,2}\.[0-9]{2,2}\.[0-9]{4,4}$/',$str)) {
			return true;
		}
		return false;
	}
	
	public static function makeTimestampFromDate($str,$fromstart=false,$toend=false) {
		$str = explode('.',$str);
		if($fromstart) {
			return mktime(0,0,1,$str[1],$str[0],$str[2]);
		} elseif($toend) {
			return mktime(23,59,59,$str[1],$str[0],$str[2]);
		} else {
			return mktime(0,0,0,$str[1],$str[0],$str[2]);
		}
	}
	
	public static function parseNameForSheduler($name) {
		$name = addslashes($name);
		$name = str_replace(array("\n","\l","\r","\t"), array(''), $name);
		return $name;
	}
	
	public static function todayStyleTime($time) {
		return now_date('ru', $time,'string_long_2');
	}
}
?>