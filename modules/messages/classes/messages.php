<?php
class admin_messages
{

public function get_list($page, $limit, $order, $order_type, $filter = 0)
	{
		global $core;

		$filter = strtolower($filter);

		//echo $filter;



		$order_type = ($order_type == 'desc')? ' DESC':' ASC';

		$start = $page * $limit;
		$order = ($order)? addslashes($order): 'message_id';

		$filter = ($filter)? addslashes($filter):0;

		
		$sql = "SELECT mr.message_id, mr.name, (SELECT m.name FROM mcms_messages as m WHERE m.message_id = mr.message_id AND m.lang_id = 2 LIMIT 1) as name_en FROM mcms_messages as mr WHERE mr.lang_id = 1 ORDER BY mr.name";
		$core->db->query($sql);
		/*
		if($filter)
		{
		$core->db->select()->from('mcms_messages')
						   	   ->fields('message_id', 'name')
						   	   ->where('lang_id = '.$core->CONFIG['lang']['id'].' AND LOWER(`name`) LIKE "%'.$filter.'%" AND `del` = 0')
						   	   ->limit($limit, $start)
						   	   ->order($order,$order_type);
		}
		else
			{
			$core->db->select()->from('mcms_messages')
						   ->fields('message_id', 'name')
						   ->where('lang_id = '.$core->CONFIG['lang']['id']." AND `del` = 0")
						   ->limit($limit, $start)
						   ->order($order,$order_type);
			}

		*/

		//$core->db->execute();
		$core->db->get_rows();

		return $core->db->rows;
	}

public function get_total($filter = 0)
	{
		global $core;

		if($filter)
			$sql = "SELECT COUNT(*) FROM `mcms_messages` WHERE `lang_id` = ".$core->CONFIG['lang']['id']. ' AND LOWER(`name`) LIKE "%'.$filter.'%" AND `del` = 0';
		else
			$sql = "SELECT COUNT(*) FROM `mcms_messages` WHERE `lang_id` = ".$core->CONFIG['lang']['id']." AND `del` = 0";

		$core->db->query($sql);

		return $core->db->get_field();
	}

public function get_langs()
	{
	global $core;

	$core->db->select()->from('mcms_language')->fields('id', 'name', 'rewrite');
	$core->db->execute();

	$core->db->get_rows();

	return $core->db->rows;
	}

public function get_langs_ussign_message_info($message_id)
	{
	global $core;

	$sql = "SELECT l.name, l.id, l.rewrite, (SELECT mes.name FROM mcms_messages as mes WHERE mes.message_id = ".$message_id." AND mes.lang_id = l.id) as name_message, (SELECT mes.text FROM mcms_messages as mes WHERE mes.message_id = ".$message_id." AND mes.lang_id = l.id) as text_message FROM mcms_language as l";
	$core->db->query($sql);
	$core->db->get_rows();

	return $core->db->rows;
	}

public function get_next_id()
	{
	global $core;

	$sql = 'SELECT MAX(message_id) FROM mcms_messages';

	$core->db->query($sql);

	return intval($core->db->get_field())+1;
	}

public function delete($message_id)
	{
	global $core;

	// удаляем запись в таблице с модулями
	$core->history->add('mcms_messages',array('message_id',$message_id),'del');
	$core->db->query("UPDATE `mcms_messages` SET `del` = 1 WHERE `message_id` = {$message_id}");
	}
}
?>