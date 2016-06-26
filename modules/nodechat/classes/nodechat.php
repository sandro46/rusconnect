<?php 
class nodechat extends main_module {
	
	public function getUsers() {
	    if($this->userInfo['is_admin'] == 1) {
	        $sql = "SELECT 
                       u.user_id,
	                   u.name_first,
	                   u.name_last,
	                   u.phone,
	                   u.email,
	                   u.avatar_small as img,
	                   (SELECT COUNT(*) FROM chat_messages as m WHERE ((m.from_uid = u.user_id AND m.to_uid = {$this->userInfo['user_id']}) OR (m.to_uid = u.user_id AND m.from_uid = {$this->userInfo['user_id']})) AND m.readed = 0) as count_not_readed,
	                   '' as exists_not_readed
	               FROM 
	                   a_users as u
	               WHERE 
	                   u.client_id = {$this->clientId} AND
	                   u.user_id != {$this->userInfo['user_id']} ";
	        
	        
	    } else {
	        $sql = "SELECT 
	                   c.contact_id,
	                   c.name as name_first,
	                   c.surname as name_last,
	                   cp.phone,
	                   ce.email,
	                   (SELECT COUNT(*) FROM chat_messages as m WHERE m.contact_id = c.contact_id AND m.readed = 0 AND m.type = 2 AND m.user_id = {$this->userInfo['user_id']}) as count_not_readed,
	                   '' as exists_not_readed
	                 FROM
	                   crm_contacts as c
	                       LEFT JOIN crm_contacts_phones as cp ON cp.client_id = {$this->clientId} AND cp.contact_id = c.contact_id 
	                       LEFT JOIN crm_contacts_email as ce ON ce.client_id = {$this->clientId} AND ce.contact_id = c.contact_id AND email_type = 1
	                 WHERE
	                   c.client_id = {$this->clientId} AND
	                   c.user_id != 0 ";
	        
	        if($this->userInfo['group_id'] == 2) {
	            $sql .= " AND c.responsible_user_id = {$this->userInfo['user_id']} ";
	        }
	    }

	    $this->db->query($sql);
		$this->db->filter('exists_not_readed', function($field, $row){
		    return (intval($row['count_not_readed']) > 0)? true : false;
		});
		
		$this->db->get_rows();
		
		return $this->db->rows;
	}
	
	public function getMessages() {
	    if($this->userInfo['is_admin'] == 1) {
	        $sql = "SELECT
	                   m.id,
	                   m.timestamp,
	                   m.text,
	                   m.readed,
	                   m.from_uid, 
	                   m.to_uid,
	                   CONCAT(u1.name_last, ' ', u1.name_first) as name_from,
	                   CONCAT(u2.name_last, ' ', u2.name_first) as name_to
	               FROM 
	                   chat_messages as m
	                          LEFT JOIN a_users as u1 ON u1.client_id = {$this->clientId} AND u1.user_id = m.from_uid
	                          LEFT JOIN a_users as u2 ON u2.client_id = {$this->clientId} AND u2.user_id = m.to_uid
	               WHERE
	                   m.from_uid = {$this->userInfo['user_id']} OR
	                   m.to_uid = {$this->userInfo['user_id']}";
	    } else {
	        $sql = "SELECT 
	                   m.id,
	                   m.timestamp,
	                   m.text,
	                   m.readed,
	                   m.user_id,
	                   m.contact_id,
	                   IF(m.type = 1, m.user_id, m.contact_id) as from_uid,
	                   IF(m.type = 2, m.user_id, m.contact_id) as to_uid,     
                       IF(m.type = 1,
                           (SELECT CONCAT(u1.name_last, ' ', u1.name_first) FROM a_users as u1 WHERE u1.client_id = {$this->clientId} AND u1.user_id = m.user_id),
	                       (SELECT CONCAT(c1.surname, ' ', c1.name) FROM crm_contacts as c1 WHERE c1.client_id = {$this->clientId} AND c1.contact_id = m.contact_id)
	                   ) as name_from,
	                   
	                   IF(m.type = 1,
	                       
	                       (SELECT CONCAT(c2.surname, ' ', c2.name) FROM crm_contacts as c2 WHERE c2.client_id = {$this->clientId} AND c2.contact_id = m.contact_id),
	                       (SELECT CONCAT(u2.name_last, ' ', u2.name_first) FROM a_users as u2 WHERE u2.client_id = {$this->clientId} AND u2.user_id = m.user_id)
	                   ) as name_to
	                FROM
                        chat_messages as m
                        
                    WHERE
                       1=1 ";
    	    if($this->userInfo['group_id'] == 2) {
    	       $sql .= " AND m.user_id = {$this->userInfo['user_id']} ";
    	    }
	    }
	    
	    //echo $sql;
	    
		$this->db->query($sql);
		$this->db->add_fields_deform(array('timestamp'));
		$this->db->add_fields_func(array('nodechat::dtime'));
		$this->db->get_rows();
		$messages = array();
		
		foreach($this->db->rows as $item) {
			$userChatRoom = ($item['from_uid'] == $this->userInfo['user_id'])? $item['to_uid'] : $item['from_uid'];
			if(!isset($messages[$userChatRoom])) $messages[$userChatRoom] = array();
			$item['type_message'] = ($item['to_uid'] == $this->userInfo['user_id'])? 'in' : 'out';
			$messages[$userChatRoom][] = $item;	
		}
		
	    //print_r($messages);
		
		return $messages;
 	}
	
	public static function dtime($timestamp) {
		return date('d.m.Y H:i:s', $timestamp);
	}
	
	public static function files_decode($json) {
		$json = stripslashes($json);
		return json_decode($json, true);
	}
	
	
}
?>