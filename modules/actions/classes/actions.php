<?php
class admin_actions
{

public function get_list($page, $limit, $order, $order_type)
	{
		global $core;

		$order_type = ($order_type == 'desc')? ' DESC':' ASC';

		$start = $page * $limit;
		$order = ($order)? addslashes($order): 'id_action';

		$core->db->select()->from('mcms_action')->fields('id_action', 'name')->where('lang_id = '.$core->CONFIG['lang']['id'].' AND del = 0')->limit($limit, $start)->order($order,$order_type);
		$core->db->execute();

		$core->db->get_rows();

		return $core->db->rows;
	}

public function get_total()
	{
		global $core;

		$sql = "SELECT COUNT(*) FROM `mcms_action` WHERE `lang_id` = ".$core->CONFIG['lang']['id'].' AND del = 0';
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

public function get_langs_ussign_action_info($id_action)
	{
	global $core;

	$sql = "SELECT l.name, l.id, l.rewrite, (SELECT act.name FROM mcms_action as act WHERE act.id_action = ".$id_action." AND act.lang_id = l.id) as name_action FROM mcms_language as l";
	$core->db->query($sql);
	$core->db->get_rows();

	return $core->db->rows;
	}

public function get_next_id()
	{
	global $core;

	$sql = 'SELECT MAX(id_action) FROM mcms_action';

	$core->db->query($sql);

	return intval($core->db->get_field())+1;
	}

public function delete_all($id_action)
	{
	global $core;

	// удаляем все данные по связи таблиц языков с таблицес связи экшинов и модулей
	$sql = "DELETE FROM mcms_gr_act_lang WHERE mcms_gr_act_lang.id_gr_act IN(SELECT gra.id FROM mcms_group_action as gra WHERE gra.id_action =".$id_action.")";
	$core->db->query($sql);

	// удаляем все связи групп акшинов и модулей
	$core->db->delete('mcms_group_action', $id_action, 'id_action');

	// удаляем запись в таблице с модулями
	$core->history->add('mcms_action',array('id_action',$id_action),'del');
	$core->db->query("UPDATE `mcms_action` SET del = 1 WHERE id_action = {$id_action}");
	}
}
?>