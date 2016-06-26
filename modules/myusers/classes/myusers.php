<?php

class myusers extends sv_module
{
	public function getUserList($instance, $page = 0, $limit = 20, $sortBy = 'date', $sortType = 'DESC', $filters = false){

		$start = $page*$limit;
		
		
		$client_id = $this->clientId;
		$sql = "SELECT
						SQL_CALC_FOUND_ROWS
						u.*
				FROM 
						crm_users u
				WHERE 
						u.client_id = $client_id";
		
		$sql .= " ORDER BY $sortBy $sortType LIMIT $start,$limit";
		$this->db->query($sql);
		$this->db->get_rows();
		
		return $this->db->rows;
		
	}
	
	public function saveUser($id, $data){
		
		$arr = array(
					'client_id' => $client_id,
					'name' => $data['name'],
					'phone' => $data['phone'],
					'post' => $data['post'],
					'departament' => $data['departament'],
					'enable' => $data['enable'],
		
		);
		
		if(isset($data['password']) && $data['password'] != false)
			$arr['password'] = $data['password'];
		
			
		if(isset($id) && intval($id))
			$arr['id'] = $id;
		
		$this->db->autoupdate()->table('crm_users')->data(array($arr))->primary('id','client_id');
		$this->db->execute();
		
		
	}
}


function datepicker($date){
	return ($date)? date('d.m.Y',$date) : '-';
}
function h_date($date){
	return ($date)? date('d-m-Y - H:m',$date) : 'dkdkdkd';
}
?>