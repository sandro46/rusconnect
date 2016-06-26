<?php

class UserLog extends widgets implements iwidget 
{
	
	public $limit = 20;
	public $html = '';
	public $list = array();
	
	public function main()
	{
		$lang = $this->core->CONFIG['lang']['id'];
		
		$sql = "SELECT l.date, s.describe as site_name, l.remote_ip, IF(l.message != '', l.message, l.url) as message, (SELECT m.name FROM mcms_modules as m WHERE m.id_module = l.id_module AND m.lang_id = {$lang}) as module_name, u.name as user_name, l.url
				FROM  
					mcms_sites as s, 
					mcms_user as u,
					mcms_logs_access as l
				WHERE 
					u.id_user = l.user_id AND
					s.id = l.id_site AND 
					l.type = 1
				ORDER BY l.date DESC
				LIMIT 0,{$this->limit}";
		
		$this->core->db->query($sql);
		$this->core->db->colback_func_param = 0;
		$this->core->db->add_fields_deform(array('date'));
		$this->core->db->add_fields_func(array('date,"d.m.Y H:i"'));
		$this->core->db->get_rows();		
		$this->list = $this->core->db->rows;
		
		
		$this->core->tpl->assign('UsersLogListWidget', $this->list);
		
		//$this->html = $this->core->tpl->get('wg.UserLogList.html', 'mcms-admin');
		

		
		$this->run($this->list);
	}

	public function out()
	{
		
		return $this->list;
	}
	
	
	
}


?>