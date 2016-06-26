<?php 

class admin_clients extends main_module {	


	public function addClient($data) {				
		if(empty($data['enable'])) $data['enable'] = 0;
		if(empty($data['send_invoice'])) $data['send_invoice'] = 0;
		
		$client = array(
			'label'=>mysql::str($data['label']),
			'company_name'=>mysql::str($data['company']),
			'city'=>mysql::str($data['city']),
			'company_site'=>mysql::str($data['site']),
			'enabled'=>intval($data['enable']) > 0,
			'system_user_id'=>2,
			'reg_date'=>time(),
			'memo'=>mysql::str($data['comment'])
		);
		
		$this->db->autoupdate()->table('a_clients')->data(array($client));
		$this->db->execute();
		
		$client['client_id'] = $this->db->insert_id;
		$login = '1'.str_pad($client['client_id'], 5, '0', STR_PAD_LEFT);
		
		$user = array(
			'client_id'=>$client['client_id'],
			'name_first'=>mysql::str($data['name_first']),
			'name_last'=>mysql::str($data['name_last']),
			'name_second'=>mysql::str($data['name_second']),
			'phone'=>mysql::str($data['phone']),
			'email'=>mysql::str($data['email']),
			'reg_date'=>time(),
			'enabled'=>intval($data['enable']) > 0,
			'password'=>md5(md5($data['password']).$this->core->CONFIG['security']['user']['pass_salt']),
			'login'=>$login
		);
		
		$this->db->autoupdate()->table('a_users')->data(array($user));
		$this->db->execute();
		$user['user_id'] = $this->db->insert_id;
		$user['password'] = $data['password'];
		
		return array('client'=>$client, 'user'=>$user);
	}
	
	public function editClient($id, $data) {
		$clientId = intval($id);
		$userId = intval($data['user_id']);
		
		if(empty($data['enable'])) $data['enable'] = 0;
		
		$client = array(
				'id'=>$clientId,
				'label'=>mysql::str($data['label']),
				'company_name'=>mysql::str($data['company']),
				'city'=>mysql::str($data['city']),
				'company_site'=>mysql::str($data['site']),
				'enabled'=>intval($data['enable']) > 0,
				'system_user_id'=>2,
				'memo'=>mysql::str($data['comment'])
		);
		
		$this->db->autoupdate()->table('a_clients')->data(array($client))->primary('id');
		$this->db->execute();
				
		$user = array(
				'user_id'=>$userId,
				'name_first'=>mysql::str($data['name_first']),
				'name_last'=>mysql::str($data['name_last']),
				'name_second'=>mysql::str($data['name_second']),
				'phone'=>mysql::str($data['phone']),
				'email'=>mysql::str($data['email']),
				'enabled'=>intval($data['enable']) > 0		
		);
		
		$this->db->autoupdate()->table('a_users')->data(array($user))->primary('user_id');
		$this->db->execute();
		
		return true;
	}
	
	public function deleteClient($id) {
		$sql = "DELETE FROM a_clients WHERE id = ".intval($id);
		$this->db->query($sql);
		$sql = "DELETE FROM a_users WHERE id = ".intval($id);
		$this->db->query($sql);
	}
	
	public function getClientList($instance, $start = 0, $limit = 20, $sortBy = 'date', $sortType = 'DESC', $filters = false) {
		$sql = "SELECT 
					SQL_CALC_FOUND_ROWS
					c.id, c.label, c.memo, c.reg_date,
					IFNULL(u.name_first, '') as name_first, 
					IFNULL(u.name_last, '') as name_last, 
					IFNULL(u.login, '') as login, 
				
				
					IF(c.enabled, 'Активный', 'Заблокирован') as enable_name,
					IF(c.enabled, 'success', 'danger') as enable_label,
				
					IF(c.is_free, 'Бесплатный', 'Платный') as free_name,
					IF(c.is_free, '', 'info') as free_label,
					
					(SELECT COUNT(*) FROM a_users as uu WHERE uu.client_id = c.id) as count_users,
					(SELECT COUNT(*) FROM tp_shop as ts WHERE ts.client_id = c.id) as count_shops,
					IF((SELECT COUNT(*) FROM tp_shop as ts WHERE ts.client_id = c.id)>0, 'success', 'important') as shops_count_label,
					IFNULL((SELECT al.date FROM system_auth_log as al WHERE al.client_id = c.id ORDER BY date DESC LIMIT 1),0) as last_auth_date
					
				FROM
					a_clients as c 
						LEFT JOIN a_users as u ON u.user_id = (
							SELECT uuu.user_id FROM a_users as uuu WHERE uuu.client_id = c.id ORDER BY uuu.user_id LIMIT 1
						)
				WHERE
					1=1 ";
		
		$sql .= " ORDER BY $sortBy $sortType LIMIT $start,$limit";
		$this->db->query($sql);
		
		$this->db->add_fields_deform(array('last_auth_date', 'reg_date'));
		$this->db->add_fields_func(array('dateAgo','dateAgo'));
		$this->db->get_rows();
		
		return $this->db->rows;
	}
	
	public function getclientInfo($id) {
		$sql = "SELECT * FROM a_clients WHERE id = {$id}";
		$this->db->query($sql);
		$this->db->add_fields_deform(array('reg_date', 'company_name'));
		$this->db->add_fields_func(array('dateAgo', 'htmlentities'));
		$this->db->get_rows(1);
		
		$client = $this->db->rows;
		
		$sql = "SELECT * FROM a_users WHERE client_id = {$id} ORDER BY user_id DESC LIMIT 1";
		$this->db->query($sql);
		$this->db->add_fields_deform(array('reg_date'));
		$this->db->add_fields_func(array('dateAgo'));
		$this->db->get_rows(1);
		
		$user = $this->db->rows;
		
		return array('client'=>$client, 'user'=>$user);
	}
	
	public function getPartnerList($instance, $start = 0, $limit = 20, $sortBy = 'date', $sortType = 'DESC', $filters = false) {
		$sql = "SELECT
					SQL_CALC_FOUND_ROWS
					p.*,
					pt.name as partner_type_name,
					(SELECT COUNT(*) FROM a_subscribers as s WHERE s.referal = p.referal) as subscribes
				FROM
					a_partners as p
						LEFT JOIN a_partners_types as pt ON pt.id = p.partner_type
		
				WHERE 1=1 ";
		
		$sql .= " ORDER BY $sortBy $sortType LIMIT $start,$limit";
		$this->db->query($sql);
		
		$this->db->add_fields_deform(array('reg_date'));
		$this->db->add_fields_func(array('dateAgo'));
		$this->db->get_rows();
		
		return $this->db->rows;
	}
	
	public function getPartnerTypes() {
		$sql = "SELECT * FROM a_partners_types WHERE 1";
		$this->db->query($sql);
		$this->db->get_rows();
		
		return $this->db->rows;
	}
	
	public function getPartnerInfo($id) {
		$id = intval($id);
		$sql = "SELECT * FROM a_partners WHERE id = {$id}";
		$this->db->query($sql);
		$this->db->get_rows(1);
		$info = $this->db->rows;
		
		if(!is_array($info) || !count($info)) return false;
		
		$info['mail'] = $this->getEmailInfo($info['email']);
		
		return $info;
	}
	
	public function deletePartner($id) {
		$id = intval($id);
		$info = $this->getPartnerInfo($id);
		
		if(!$info || !is_array($info) || !count($info)) return false;
		
		$sql = "DELETE FROM a_partners WHERE id = {$id}";
		$this->db->query($sql);
		
		$this->db->connect($this->core->CONFIG, 'db_mail');
		
		$sql = "DELETE FROM mailbox WHERE username = '{$info['email']}'";
		$this->db->query($sql);
		
		return true;
	}
	
	public function getEmailInfo($email) {
		$this->db->connect($this->core->CONFIG, 'db_mail');
		
		$sql = "SELECT * FROM mailbox WHERE username = '".mysql::str($email)."'";
		$this->db->query($sql, 'db_mail');
		$this->db->get_rows(1);
		
		if(!is_array($this->db->rows) || !count($this->db->rows)) return false;
		
		return $this->db->rows;
	}
	
	public function addPartner($id, $data) {
		$id = intval($id);
		$this->db->connect($this->core->CONFIG, 'db_mail');
		
		if($id) {
			$info = $this->getPartnerInfo($id);
			if($info && $info['mail']['local_part'] != $data['email']) {
				$sql = "DELETE * FROM mailbox WHERE username = '{$info['mail']['username']}'";
				$this->db->query($sql, 'db_mail');
			}
		}
		
		$mail = array(
				'username'=>mysql::str($data['email']).'@ncity.biz',
				'password'=>mysql::str($data['email_password']),
				'name'=>mysql::str($data['name']),
				'maildir'=>mysql::str($data['email']).'@ncity.biz/',
				'quota'=>0,
				'local_part'=>mysql::str($data['email']),
				'domain'=>'ncity.biz',
				'created'=>date('Y-m-d H:i:s'),
				'modified'=>date('Y-m-d H:i:s'),
				'active'=>1
		);
		
		$this->db->autoupdate()->table('mailbox')->data(array($mail))->primary('username');
		$this->db->execute('db_mail');
		
		$partner = array(
			'name'=>mysql::str($data['name']),
			'phone'=>mysql::str($data['phone']),
			'email'=>mysql::str($data['email']).'@ncity.biz',
			'city'=>mysql::str($data['city']),
			'referal'=>mysql::str($data['referal']),
			'client_id'=>0,
			'partner_type'=>intval($data['partner_type'])	
		);
		
		if($id) {
			$partner['id'] = $id;
		} else {
			$partner['reg_date'] = time();
		}
		
		$this->db->connect($this->core->CONFIG, 'db_site');
		$this->db->autoupdate()->table('a_partners')->data(array($partner))->primary('id');
		$this->db->execute();
		
		
		$partner['mail'] = $mail;
		
		return $partner;
	}



























}

?>