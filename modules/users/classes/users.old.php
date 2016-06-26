<?php
class admin_users
{
	public function get_list($page, $limit, $order)
	{
		global $core;

		$start = $page * $limit;
		$order = ($order)? $order: 'id_user';

		$core->db->select()->from('mcms_user')->fields('*')->order($order)->limit($limit, $start);
		$core->db->execute();
		$core->db->get_rows();

		return $core->db->rows;
	}

	public function get_total()
	{
		global $core;

		$sql = "SELECT COUNT(*) FROM mcms_user";
		$core->db->query($sql);

		return $core->db->get_field();
	}

	public function del($id)
	{
		global $core;
		$core->history->add('mcms_user',array('id_user',$id),'del');
//		$core->db->query("UPDATE `mcms_user` SET del = 1 WHERE id_user = {$id}");
		$core->db->delete('mcms_user', $id, 'id_user');
		$core->db->delete('mcms_user_group', $id, 'id_user');
		return true;
	}

	public function generate_id()
	{
		global $core;

		$sql = "SELECT MAX(id_user) FROM mcms_user";
		$core->db->query($sql);

		return $core->db->get_field() + 1;
	}

	public function get_langs()
	{
	global $core;

	$core->db->select()->from('mcms_language')->fields('rewrite', 'id')->order('`order`');
	$core->db->execute();
	$core->db->get_rows();

	return $core->db->rows;
	}

	public function get_langs_usign_uid($uid)
	{
	global $core;

	$sql = "SELECT l.rewrite, l.id, (SELECT u.name FROM mcms_user as u WHERE u.id_user = ".$uid." AND lang_id = l.id) as name, (SELECT u.id FROM mcms_user as u WHERE u.id_user = ".$uid." AND lang_id = l.id) as us_id FROM mcms_language as l";
	$core->db->query($sql);
	$core->db->get_rows();

	return $core->db->rows;
	}

	public function get_groups_list()
	{
		global $core;

		$core->db->select()->from('mcms_group')->fields('id_group', 'name')->where('lang_id = '.$core->CONFIG['lang']['id'])->order('name');
		$core->db->execute();
		$core->db->get_rows();

		return $core->db->rows;
	}

	public function get_groups_list_ussign_uid($uid)
	{
		global $core;

		$sql = "SELECT gr.id_group, gr.name, IF((SELECT COUNT(*) FROM mcms_user_group as ugr WHERE ugr.id_user = ".$uid." AND ugr.id_group = gr.id_group), 1,0) as selected FROM mcms_group as gr WHERE lang_id = ".$core->CONFIG['lang']['id']." ORDER BY gr.name";

		$core->db->query($sql);
		$core->db->get_rows();

		return $core->db->rows;
	}

	public function get_user_info($uid)
	{
		global $core;

		$core->db->select()->from('mcms_user')->fields('login', 'id_user', 'name', 'email', 'memo')->where("id_user = {$uid}");
		$core->db->execute();
		$core->db->get_rows();

		return $core->db->rows[0];
	}

	public function get_user_group_list()
	{
		global $core;

		$sql = "SELECT ugr.id_user, ugr.id_group, (SELECT gr.name FROM mcms_group as gr WHERE gr.id_group = ugr.id_group AND lang_id = ".$core->CONFIG['lang']['id']." ) as name, (SELECT u.name FROM mcms_user as u WHERE u.id_user = ugr.id_user ) as user_name FROM mcms_user_group as ugr";

		$core->db->query($sql);
		$core->db->get_rows();

		$groups = $core->db->rows;

		foreach($groups as $group)
			{
			$ret[$group['id_user']]['user_name'] = $group['user_name'];
			$ret[$group['id_user']]['id_user'] = $group['id_user'];
			$ret[$group['id_user']][] = array('id'=>$group['id_user'], 'name'=>$group['name']);
			}

		return $ret;
	}

	public function get_user_list_by_group($grid)
	{
		global $core;

		$sql = "SELECT u.id_user, u.name FROM mcms_user as u WHERE u.id_user IN( SELECT ugr.id_user FROM mcms_user_group as ugr WHERE ugr.id_group =".$grid.")";
		$core->db->query($sql);
		$core->db->get_rows();

		return $core->db->rows;
	}

	public function get_groups_list_opt($select_grid)
	{
		global $core;

		$sql = "SELECT name, id_group, IF(id_group = ".$select_grid.", 1,0) as selected FROM mcms_group WHERE lang_id = ".$core->CONFIG['lang']['id'];
		$core->db->query($sql);
		$core->db->get_rows();

		return $core->db->rows;
	}

	public function get_all_sites()
	{
		global $core;
		
		$core->db->select()->from('mcms_sites')->fields('*');
		$core->db->execute();
		$core->db->get_rows();
		
		return $core->db->rows;
	}
}
?>