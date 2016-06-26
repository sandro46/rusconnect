<?php 

class admin_users extends main_module {

	public function getUsersList($instance, $start = 0, $limit = 20, $sortBy = 'date', $sortType = 'DESC', $filters = false) {
		$sql = "SELECT
					SQL_CALC_FOUND_ROWS
					u.*,
					u.user_id as id,
					IFNULL(u.block_reason, '') as block_reason,
					IFNULL(u.avatar_small, '/vars/files/images/photo.jpg') as avatar_small,
					ug.name as group_name,
					IF(u.user_id = {$this->userId}, 1, 0) as is_locked,
					(SELECT al.date FROM system_auth_log as al WHERE al.client_id = {$this->clientId} AND al.user_id = u.user_id  ORDER BY al.date DESC LIMIT 1) as last_auth,
					
					IF(enabled, 'Активен','Заблокирован') as enable_name,
					IF(enabled, 'success','important') as enable_label
					
					
				FROM 
					a_users as u 
						LEFT JOIN a_access_groups as ug ON ug.group_id = u.group_id
				WHERE 
					u.client_id = {$this->clientId}	";
		
		$sql .= " ORDER BY $sortBy $sortType LIMIT $start,$limit";
		
		//echo $sql;
		$this->db->query($sql);
		$this->db->add_fields_deform(array('reg_date', 'last_auth'));
		$this->db->add_fields_func(array('h_date', 'h_date'));
		$users = $this->db->get_rows();
		
		return $users;
	}
	
	public function addUser($userId, $data) {
	    $userId = intval($userId);
	    $roleId = intval($data['role_id']);
	    $password = false;
	    $newRole = false;
	    $login = false;
	    
	    if(!$roleId) {
	        if(empty($data['role_name'])) return false;
	        
	        $query = array(
                'client_id'=>$this->clientId,
	            'name'=>mysql::str($data['role_name'])
	        );
	        
	        $this->db->autoupdate()->table('a_access_groups')->data(array($query));
	        $this->db->execute();
	        
	        $roleId = $this->db->insert_id;
	        $newRole = true;
	    }

	    
	    if(!empty($data['passwd'])) {
	        $password = md5(md5($data['passwd']).$this->core->CONFIG['security']['user']['pass_salt']);
	    }
	    
	    $info = array(
            'client_id'=>$this->clientId,
	        'name_first'=>mysql::str($data['name_first']),
	        'name_last'=>mysql::str($data['name_last']),
	        'name_second'=>mysql::str($data['name_second']),
	        'phone'=>mysql::str($data['phone']),
	        'email'=>mysql::str($data['email']),
	        'group_id'=>$roleId
	    );
	    
	    if(!empty($data['avatar'])) {
	        $info['avatar_big'] = mysql::str($data['avatar']['big']);
	        $info['avatar_small'] = mysql::str($data['avatar']['small']);
	    }
	    
	    if($userId) {
	        $info['user_id'] = $userId;
	
	        $this->db->autoupdate()->table('a_users')->data(array($info))->primary('client_id', 'user_id');
	        $this->db->execute();
	    } else {
	        $info['password'] = $password;
	        $info['reg_date'] = time();
	        $info['is_admin'] = false;
	        
	        $this->db->autoupdate()->table('a_users')->data(array($info));
	        $this->db->execute();
	        $userId = $this->db->insert_id;
	        
	        $sql = "SELECT COUNT(*) FROM a_users WHERE client_id = {$this->clientId}";
	        $this->db->query($sql);
	        $cnt = $this->db->get_field();
	        
	        $login = '1'.str_pad($this->clientId, 5, '0', STR_PAD_LEFT).'.'.$cnt;
	        
	        $query = array(
	            'client_id'=>$this->clientId,
	            'user_id'=>$userId,
	            'login'=>$login
	        );
	        
	        $this->db->autoupdate()->table('a_users')->data(array($query))->primary('client_id', 'user_id');
	        $this->db->execute();
	    }
	    
	    $rules = array();
	    $groupRules = array();
	    
	    foreach($data['rules'] as $object=>$item) {
	        $rules[] = array(
                'client_id'=>$this->clientId,
                'user_id'=>$userId,
                'object_id'=>intval($object),
                'read'=> ($item['read'])? 1 : 0,
                'write'=> ($item['write'])? 1 : 0,
                'delete'=> ($item['delete'])? 1 : 0,
	        );
	        
	        if($newRole) {
	            $groupRules[] = array(
                    'group_id'=>$roleId,
	                'object_id'=>intval($object),
	                'client_id'=>$this->clientId,
	                'read'=> ($item['read'])? 1 : 0,
	                'write'=> ($item['write'])? 1 : 0,
	                'delete'=> ($item['delete'])? 1 : 0
	            );
	        }
	    }
	    
	    $this->db->autoupdate()->table('a_access_user_rules')->data($rules)->primary('client_id', 'user_id', 'object_id');
	    $this->db->execute();
	    
	    if($newRole) {
	        $this->db->autoupdate()->table('a_access_groups_rules')->data($groupRules);
	        $this->db->execute();
	    }
	    
	    if($login) {
	        return array(
	            'user_name'=>mysql::str($data['name_first']). ' '. mysql::str($data['name_last']),
	            'login'=> $login,
	            'password'=> $data['passwd'],
	            'email'=> $data['email']
	        );
	    }
	    
	    return true;
	}
	
	public function getUserInfo($userId) {
	    $userId = intval($userId);
	    
	    if(!$userId) return false;
	    if(!$this->checkAccessToUser($userId)) return false;
	    
	    $profile = $this->getProfile($userId);
	    $profile['access'] = $this->getAccessObjects($profile['group_id'], $userId);
	    
	    return $profile;
	}
		
	public function getAccessObjects($groupId = 0, $userId = 0) {
		$sql = "SELECT ao.name, ao.id FROM a_access_objects as ao ";
		$this->db->query($sql);
		$this->db->get_rows();
		$list = $this->db->rows;
		
		//if(!$userId) $userId = $this->userId;
		
		foreach($list as $k=>$item) {
			$list[$k] = array_merge($item, $this->getAccessRules($groupId, $userId, $item['id']));
		}
				
		return $list;
	}
	
	public function getAccessGroups() {
		$sql = "SELECT * FROM a_access_groups WHERE client_id = {$this->clientId} OR client_id = 0";
		$this->db->query($sql);
		$this->db->get_rows();
		
		return $this->db->rows;
	}
	
	public function deleteUser($userId) {
		if(!$userId = intval($userId)) return -1;
		if($this->userId == $userId) return -2;
		if(!$this->checkAccessToUser($userId)) return -3;
		
		$sql = "DELETE FROM a_users WHERE client_id = {$this->clientId} AND user_id = {$userId}";
		$this->db->query($sql);
		
		$sql = "DELETE FROM system_user_settings WHERE client_id = {$this->clientId} AND user_id = {$userId}";
		$this->db->query($sql);
		
		$sql = "DELETE FROM a_access_user_rules WHERE client_id = {$this->clientId} AND user_id = {$userId}";
		$this->db->query($sql);
		
		return true;
	}

	public function getProfile($userId = false) {
		$userId = (intval($userId))? intval($userId) : $this->userId;
		
		$sql = "SELECT 
					u.*,
					ug.name as group_name,
					(SELECT al.date FROM system_auth_log as al WHERE al.client_id = {$this->clientId} AND al.user_id = {$userId}  ORDER BY al.date DESC LIMIT 1) as last_auth
				FROM 
					a_users as u 
						LEFT JOIN a_access_groups as ug ON ug.group_id = u.group_id
				WHERE 
					u.client_id = {$this->clientId} AND 
					u.user_id = {$userId}";
		
		
		$this->db->query($sql);
		
		$this->db->add_fields_deform(array('reg_date', 'last_auth'));
		$this->db->add_fields_func(array('h_date','h_date'));
		$this->db->get_rows(1);
		$profile = $this->db->rows;
				
		return $profile;
	}

	public function updateUserAvatar($userId, $imgBig, $imgSmall) {
		$userId = (intval($userId))? intval($userId) : $this->userId;
		if(!$this->checkAccessToUser($userId)) return false;
		
		$query = array(
			'client_id'=>$this->clientId,
			'user_id'=>$userId,
			'avatar_big'=>mysql::str($imgBig),
			'avatar_small'=>mysql::str($imgSmall)
		);
		
		$this->db->autoupdate()->table('a_users')->data(array($query))->primary('client_id', 'user_id');
		$this->db->execute();
		
		return true;
	}
	
	public function updateUserPassword($userId, $pass) {
		$userId = (intval($userId))? intval($userId) : $this->userId;
		if(!$this->checkAccessToUser($userId)) return false;
		
		$pass = md5(md5($pass).$this->core->CONFIG['security']['user']['pass_salt']);
		
		$query = array(
				'client_id'=>$this->clientId,
				'user_id'=>$userId,
				'password'=>$pass
		);
		
		$this->db->autoupdate()->table('a_users')->data(array($query))->primary('client_id', 'user_id');
		$this->db->execute();
		
		return true;
	}
	
	public function blockUser($userId, $data) {
		if(!$userId = intval($userId)) return -1;
		if($this->userId == $userId) return -2;
		if(!$this->checkAccessToUser($userId)) return -3;
		$reason = (!empty($data['reason']))? $data['reason'] : '';
		
		$query = array(
			'client_id'=>$this->clientId,
			'user_id'=>$userId,
			'enabled'=>0,
			'block_reason'=>mysql::str($reason)
		);
		
		$this->db->autoupdate()->table('a_users')->data(array($query))->primary('client_id', 'user_id');
		$this->db->execute();
		
		return true;
	}
	
	public function unblockUser($userId) {
		if(!$userId = intval($userId)) return -1;
		if($this->userId == $userId) return -2;
		if(!$this->checkAccessToUser($userId)) return -3;
		
		$query = array(
				'client_id'=>$this->clientId,
				'user_id'=>$userId,
				'enabled'=>1,
				'block_reason'=>''
		);
		
		$this->db->autoupdate()->table('a_users')->data(array($query))->primary('client_id', 'user_id');
		$this->db->execute();
		
		return true;
	}
	
	private function checkAccessToUser($userId) {
		$sql = "SELECT count(*) FROM a_users WHERE client_id = {$this->clientId} AND user_id = {$userId}";
		$this->db->query($sql);
		
		return (intval($this->db->get_field()) > 0)? true : false; 
	}































}

if(!function_exists('h_date')) {
	function h_date($date){
		return ($date)? date('d.m.Y в H:i',$date) : 'не установлено';
	}
}



?>