<?php

class admin_sales
{


	public function __construct()
	{
		// Empty
	}


	public function get_list()
	{
		global $core;

		// Empty

	}
	
	public function get_events_types()
	{
		global $core;
		
		$core->db->select()->from('sales_clients_events_types')->fields('$all')->order('name');
		$core->db->execute();
		
		return $core->db->get_rows();
	}
	
	public function get_clients_type()
	{
		global $core;
		
		$core->db->select()->from('sales_clients_types')->fields('$all');
		$core->db->execute();
		$core->db->get_rows();
		
		return $core->db->rows;
	}
	
	public function get_regions()
	{
		global $core;
		
		$core->db->select()->from('sales_clients_regions')->fields('$all');
		$core->db->execute();
		$core->db->get_rows();
		
		return $core->db->rows;
	}
	
	public function get_clients_list()
	{
		global $core;
		
		$core->db->select()->from('sales_clients')->fields('$all')->order('name');
		
		$sql = "SELECT 
					c.id, 
					c.name, 
					c.phone, 
					c.email, 
					c.site, 
					c.create_date, 
					c.comment, 
					c.manager_id, 
					c.region_id, 
					c.type_id,
					(SELECT ct.name FROM sales_clients_types as ct WHERE ct.id = c.type_id) as type,
					(SELECT r.name FROM sales_clients_regions as r WHERE r.id = c.region_id) as region,
					(SELECT u.name FROM mcms_user as u WHERE u.id_user = c.manager_id) as manager
					
				FROM sales_clients as c
				
				WHERE 1
				
				ORDER BY c.name";
		
		$core->db->query($sql);
		$core->db->add_fields_deform(array('create_date', 'comment'));
		$core->db->add_fields_func(array('dateAgo', 'stripslashes'));
				
		return $core->db->get_rows();
	}

	public function add_event($startHour, $startMin, $endHour, $endMin, $client_id, $event_type, $comment, $d, $m, $y)
	{
		global $core;
				
		$data[] = array(
				'client_id'=>$client_id,
				'start_date'=>mktime($startHour, $startMin, 0, $m+1, $d, $y),
				'end_date'=>mktime($endHour, $endMin, 0, $m+1, $d, $y),
				'event_id'=>$event_type,
				'state'=>1,
				'manager_id'=>$core->user->id,
				'result'=>9,
				'comment'=>$comment);
		$core->db->autoupdate()->table('sales_clients_events')->data($data);
		$core->db->execute();
		
		return $core->db->insert_id;
	}
	
	public function get_events($state = 1, $id = false, $page, $limit, $sort_by, $sort_type, $filters = false)
	{
		global $core;
		
		$start = $page * $limit;
		
		$sql = "SELECT
					SQL_CALC_FOUND_ROWS
					ce.id,
					ce.client_id,
					ce.event_id,
					ce.start_date,
					ce.end_date,
					ce.comment,
					ce.done_comment,
					ce.closed_date,
					ce.state,
					ce.manager_id, 	
					ce.result,
					ce.color,
					c.region_id,
					c.name,
					c.phone,
					c.email,
					c.site,
					c.create_date as client_create_date,
					c.comment as client_comment,
					c.type_id as client_type,

					ce.start_date as startY, ce.start_date as startM, ce.start_date as startD, ce.start_date as startH, ce.start_date as startMM,
					ce.end_date as endY, ce.end_date as endM, ce.end_date as endD, ce.end_date as endH, ce.end_date as endMM,
					
					
					(SELECT et.name FROM sales_clients_events_types as et WHERE et.id = ce.event_id) as event_type,
					(SELECT st.name FROM sales_clients_events_states as st WHERE st.id = ce.state) as status,
					(SELECT rs.name FROM sales_clients_results as rs WHERE rs.id = ce.result) as result_name,
					(SELECT ct.name FROM sales_clients_types as ct WHERE ct.id = c.type_id) as type,
					(SELECT r.name FROM sales_clients_regions as r WHERE r.id = c.region_id) as region,
					(SELECT cn.name FROM sales_clients_contacts as cn WHERE cn.client_id = c.id ORDER BY cn.id LIMIT 1) as contact_name				
					
				FROM sales_clients_events as ce, sales_clients as c
				
				WHERE
					c.id = ce.client_id AND
					ce.manager_id = {$core->user->id} ";
		
		if($state !== false)
		{
			if($state == 3)
			{
				$sql .= " AND ce.end_date < ".time()." AND ce.state = 1";
			}
			else 
				{
					$sql .= " AND ce.state = {$state} ";
				}
		}
		
		if(isset($filters['client_id'])) 	{ $filters['ce.client_id'] = $filters['client_id']; unset($filters['client_id']); }	
		if(isset($filters['region_id'])) 	{ $filters['c.region_id'] = $filters['region_id']; unset($filters['region_id']); }	
		if(isset($filters['event_id'])) 	{ $filters['ce.event_id'] = $filters['event_id']; unset($filters['event_id']); }
		if(isset($filters['result'])) 		{ $filters['ce.result'] = $filters['result']; unset($filters['result']); }
		if(isset($filters['client_type'])) 	{ $filters['c.type_id'] = $filters['client_type']; unset($filters['client_type']); }	
		if(isset($filters['start_date']))	{ $filters['!'][] = " ce.start_date >= ".$this->parseCustomDate($filters['start_date'], false, true)." AND ce.start_date <= ".$this->parseCustomDate($filters['start_date'], false, false, true); unset($filters['start_date']); }
		if(isset($filters['name']))			{ $filters['c.name'] = '$LIKE%'.$filters['name'].'%'; unset($filters['name']); }
		if(isset($filters['phone'])) 		{ $filters['c.phone'] = '$LIKE%'.$filters['phone'].'%'; unset($filters['phone']); }
		if(isset($filters['email'])) 		{ $filters['c.email'] = '$LIKE%'.$filters['email'].'%'; unset($filters['email']); }
		
		$sql .= ($id)? " AND ce.id = {$id} ": "";

		if($filters) $filterSql = $core->db->sql_filters($filters);
 		if(strlen($filterSql)>2) $sql.= ' AND'.$filterSql;	
	
		$sql .= " ORDER BY {$sort_by} {$sort_type} LIMIT {$start},{$limit}";
		
		//echo $sql;
		
		$core->db->query($sql);
		$core->db->colback_func_param = 0;
		$core->db->add_fields_deform(array('start_date', 'closed_date', 'name', 'end_date', 'comment', 'client_create_date', 'startM', 'startY', 'startD', 'startH', 'startMM', 'endM', 'endY', 'endD', 'endH', 'endMM'));
		$core->db->add_fields_func(array('date,"d.m.Y H:i"', 'date,"d.m.Y H:i"', 'addslashes' ,'dateAgo', 'stripslashes', 'dateAgo', 'date,"m"', 'date,"Y"', 'date,"d"', 'date,"H"', 'date,"i"', 'date,"m"', 'date,"Y"', 'date,"d"', 'date,"H"', 'date,"i"'));
		$core->db->get_rows(($id)? true : false);
				
		return $core->db->rows;
	}
	

	public function move_event($startTimeStamp, $endTimeStamp, $id)
	{
		global $core;
		
		$sql = "UPDATE sales_clients_events SET start_date = {$startTimeStamp}, end_date = {$endTimeStamp} WHERE id = {$id}";
		$core->db->query($sql);
		
		return true;
	}
	
	public function eventsResult()
	{
		global $core;
		
		$core->db->select()->from('sales_clients_results')->fields('$all');
		$core->db->execute();
		$core->db->get_rows();
		
		return $core->db->rows;
	}
	
	public function get_managers($companyId)
	{
		global $core;
		
		$core->db->select()->from('sales_managers as s', 'mcms_user as u')->fields('u.name', 'u.id_user id')->where('u.id_user = s.user_id AND s.company_id = '.$companyId);
		$core->db->execute();
		$core->db->get_rows();
		
		return $core->db->rows;
	}
	
	public function doneEvent($reason, $comment, $id)
	{
		global $core;
		
		$sql = "UPDATE sales_clients_events SET state = 2, result = {$reason}, 	done_comment = '{$comment}', closed_date = ".time()." WHERE id = {$id}";
		$core->db->query($sql);
		
		return true;
	}
	
	public function add_client($client)
	{
		global $core;
		
		$sales_clients[] = array('name'=>$client['name'],
								 'region_id'=>$client['region'],
								 'manager_id'=>$client['manager'],
								 'type_id'=>$client['type'],
								 'phone'=>$client['phone'],
								 'email'=>$client['email'],
								 'comment'=>$client['comment'],
								 'create_date'=>time(),
								 'price'=>$client['price']);
		
		$core->db->autoupdate()->table('sales_clients')->data($sales_clients);
		$core->db->execute();
		
		if(!$core->db->insert_id) return false;
		
		$client['id'] = $core->db->insert_id;
		
		if(strlen($client['login'])>1)
		{
			$sales_clients_demo[] = array('client_id'=>$client['id'],
									  'login'=>$client['login'],
									  'password'=>$client['pass'],
									  'date'=>time());
		
			$core->db->autoupdate()->table('sales_clients_demo')->data($sales_clients_demo);
			$core->db->execute();
		}
		
		//print_r($client);
		
		if(isset($client['contacts']) && is_array($client['contacts']) && count($client['contacts'])>0)
		{
			$sales_clients_contacts = array();

			foreach($client['contacts'] as $item)
			{
				$sales_clients_contacts[] = array('name'=>$item['name'], 'phone'=>$item['phone'], 'position'=>$item['position'], 'client_id'=>$client['id']);	
			}
			
			$core->db->autoupdate()->table('sales_clients_contacts')->data($sales_clients_contacts);
			$core->db->execute();
			//$core->db->debug();	
		}
		
		return $client['id'];
	}
	
	public function saveEvent($id, $comment, $comment2)
	{
		global $core;
		
		$data[] = array('id'=>$id, 'comment'=>$comment, 'done_comment'=>$comment2);
		
		$core->db->autoupdate()->table('sales_clients_events')->data($data)->primary('id');
		$core->db->execute();
	}
	
	public function getClientInfo($id)
	{
		global $core;
		
		$id = intval($id);
		if(!$id) return false;
		
		$sql = "SELECT 
						sc.id, 
						sc.name, 
						sc.region_id, 
						sc.phone, 
						sc.email, 
						sc.site, 
						sc.create_date, 
						sc.comment, 
						sc.manager_id, 
						sc.type_id, 
						sc.price,
						
						(SELECT r.name FROM sales_clients_regions as r WHERE r.id = sc.region_id) as region,
						(SELECT ct.name FROM sales_clients_types as ct WHERE ct.id = sc.type_id) as client_type,
						(SELECT u.name FROM mcms_user as u WHERE u.id_user = sc.manager_id) as manager
						
				FROM sales_clients as sc
				WHERE id = {$id}";
						
		
		$core->db->query($sql);
		$core->db->get_rows(1);
		

		
		if(!$core->db->rows || !count($core->db->rows) || !isset($core->db->rows['name'])) return false;
		
		$client = $core->db->rows;
		
		$core->db->select()->from('sales_clients_contacts')->fields('$all')->where("client_id = {$id}");
		$core->db->execute();
		$core->db->get_rows();
	
		if($core->db->rows && count($core->db->rows) && isset($core->db->rows[0]))
		{
			$client['contacts'] = $core->db->rows;
		}
		
		$core->db->select()->from('sales_clients_demo')->fields('$all')->where("client_id = {$id}");
		$core->db->execute();
		$core->db->get_rows(1);
	
		if($core->db->rows && count($core->db->rows) && isset($core->db->rows['login']))
		{
			$client['demo_login'] = $core->db->rows['login'];
			$client['demo_pass'] = $core->db->rows['password'];
		}
		else
			{
				$client['demo_login'] = '';
				$client['demo_pass'] = '';
			}
		
		
		return $client;
	}
	
}


?>
