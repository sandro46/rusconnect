<?php
class SMSMessages extends widgets implements iwidget
{
	public $messages = array();
	
	public function main()
	{
	}
	
	public function out()
	{
		global $core;
		
		$sql = "SELECT
					mes.*
		          FROM sms_clients_messages AS mes,
	                   sms_clients AS cli
		          WHERE mes.client_id = cli.id AND
		                cli.user_id={$core->user->id} AND
		                mes.dont_show = 0 
		                ORDER BY mes.date DESC";
		$core->db->query($sql);
		$core->db->get_rows();	
		$this->messages = $core->db->rows;
				
		foreach($this->messages as $key=>$val)
		{
			if($val['type'] == 'confirm')
			{
				$this->messages[$key]['callback'] = str_replace(array('{$id}', '{$slid}'), array($val['id'], $val['sendlist_id']), $this->messages[$key]['callback']);
			}
		}

		return $this->messages;
	}
	
	
}