<?php

// This file was created by M-cms core system for ajax functions


function add_event($startHour, $startMin, $endHour, $endMin, $client_id, $event_type, $comment, $day, $month, $year)
{
	$startHour = intval($startHour);
	$startMin = intval($startMin);
	$endHour = intval($endHour);
	$endMin = intval($endMin);
	$day = intval($day);
	$month = intval($month);
	$year = intval($year);
	$client_id = intval($client_id);
	$event_type = intval($event_type);
	$comment = addslashes(htmlspecialchars($comment));
	
	if(!$client_id) return 'Нужно выбрать клиента';
	if(!$event_type) return 'Нужно выбрать событие';

	$root = new admin_sales();
	$id = $root->add_event($startHour, $startMin, $endHour, $endMin, $client_id, $event_type, $comment, $day, $month, $year);
	
	return $id;
}

function updateTimeEvent($startDate, $startTime, $endDate, $endTime, $id)
{
	$id = intval($id);
	
	if(!$id) return 'Событие куда то пропало :(';	
	
	$startDate = explode('.', $startDate);
	$startTime = explode(':',$startTime);
	$endDate = explode('.', $endDate);
	$endTime = explode(':',$endTime);
	

	$startTimeStamp = mktime($startTime[0], $startTime[1], 0, $startDate[1],$startDate[0],$startDate[2]);
	$endTimeStamp = mktime($endTime[0], $endTime[1], 0, $endDate[1],$endDate[0],$endDate[2]);
	
	$root = new admin_sales();
	$root->move_event($startTimeStamp, $endTimeStamp, $id);
}

function get_event($id)
{
	$id = intval($id);
	$root = new admin_sales();
	
	return $root->get_events(false, $id, 0, 9999, 'start_date', 'desc');
}

function done_event($reason, $comment, $id)
{
	$id = intval($id);
	$root = new admin_sales();
	$root->doneEvent($reason, $comment, $id);
	
	return array('id'=>$id);
}

function getAllEvents($instance, $page, $limit, $sortBy, $sortType, $filters)
{
	global $core;
	
	$root = new admin_sales();
	
	$possibleSorted = array('start_date', 'name', 'region', 'type', 'event_type', 'result_name', 'status', 'comment');
	$possibleSearche = array();
	
	$filters = stdClassToObject(json_decode(stripslashes($filters)));
	$page = intval($page)+0;
	$limit = intval($limit)+0;
	
	$state = 2;
	if(isset($filters['state'])) 
	{
		$state = intval($filters['state']);
		if($state == 99) $state = false;
		
		unset($filters['state']);
	}
	
	if($sortBy && in_array($sortBy, $possibleSorted))
	{
		$sortBy = $sortBy;
		$sortType = ($sortType == 'desc')? 'desc':'asc';
	}
	else
		{
			$sortBy = $possibleSorted[0];
			$sortType = 'desc';
		}	

	global $core;

		
	$dataObject = $root->get_events($state, false, $page, $limit, $sortBy, $sortType, $filters);
	$pager = ajax_pagenav($core->db->found_rows(), $limit, $page, "grid.{$instance}.gopage", "false");
	
	return array('pager'=>$pager, 'data'=>$dataObject);
}

function add_client($jsonObject)
{
	$client = stdClassToObjectRecurs(json_decode(stripslashes($jsonObject)));
	
	// string params
	$client['name'] = addslashes($client['name']);
	$client['phone'] = addslashes($client['phone']);
	$client['email'] = addslashes($client['email']);
	$client['comment'] = addslashes($client['comment']);
	$client['login'] = addslashes($client['login']);
	$client['pass'] = addslashes($client['pass']);
	$client['price'] = addslashes($client['price']);
	
	// integer params
	$client['type'] = intval($client['type']);
	$client['region'] = intval($client['region']);
	$client['manager'] = intval($client['manager']);
	
	if(isset($client['contacts']) && is_array($client['contacts']) && count($client['contacts']) > 0)
	{
		foreach($client['contacts'] as $k=>$v) $client['contacts'][$k] = array('name'=>addslashes($v['name']), 'position'=>addslashes($v['position']), 'phone'=>addslashes($v['phone']));
	}

	// data check
	if(!$client['type'] || !$client['region'] || !$client['manager']) return false;

	$root = new admin_sales();
	$client['id'] = $root->add_client($client);
			
	return ($client['id'])? $client : false;	
}

function saveEvent($eventId, $commentOne, $commentTwo)
{
	$eventId = intval($eventId);
	$commentOne = addslashes($commentOne);
	$commentTwo = addslashes($commentTwo);
	
	
	$root = new admin_sales();
	$root->saveEvent($eventId, $commentOne, $commentTwo);
}

function getRegions()
{
	$root = new admin_sales();
	return $root->get_regions();
}

function getEventsTypes()
{
	$root = new admin_sales();
	return $root->get_events_types();
}

function getClientsTypes()
{
	$root = new admin_sales();
	return $root->get_clients_type();	
}

function getClients()
{
	$root = new admin_sales();
	return $root->get_clients_list();	
}

function getClientInfo($id)
{
	$id = intval($id);
	if(!$id) return false;
	$root = new admin_sales();
	return $root->getClientInfo($id);	
}

?>