<?php

class admin_feedback
{

	public $list = array();
	public $data = array();
	
	
	public function __construct()
	{
		// Empty
	}


	public function getList($page=0, $limit=25)
	{
		global $core;
		
		$start = $page * $limit;
		
		$core->db->select()->from('site_feedback')->fields('$all')->order('date')->limit($limit, $start);
		$core->db->execute();
		$core->db->colback_func_param = 0;
		$core->db->add_fields_deform(array('date', 'name'));
		$core->db->add_fields_func(array('date,"d.m.Y H:i"', 'stripslashes'));	
		
		$core->db->get_rows();
		
		$this->list = $core->db->rows;
		
		return $this->list;
	}
	
	public function get_total()
	{
		global $core;
		
		$core->db->query("SELECT COUNT(*) FROM site_feedback");
		
		return $core->db->get_field();
	}
	
	public function process()
	{
		global $core;

		$name = addslashes($_POST['name']);
		$contact = addslashes($_POST['contact']);
		$order = addslashes(htmlspecialchars($_POST['order']));
		
		$message = '';
		$message = $this->createMessage($name, $contact, $order);
		$this->sendMail($message);	
		
		$data[] = array('name'=>$name, 'date'=>time(), 'contact'=>$contact, 'comment'=>$order);
		$core->db->autoupdate()->table('site_feedback')->data($data);
		$core->db->execute();
		
			
	}
	
	public function sendMail($message)
	{
		$to      = $this->getMailAddress();
		$subject = iconv('utf-8', 'KOI8-R//IGNORE', 'Запрос с сайта apex.dubna.ru -- '.time());

		$headers = 'From: info@apex-dubna.ru' . "\r\n" .
		    'Reply-To: info@apex-dubna.ru' . "\r\n" .
		    'X-Mailer: PHP/' . phpversion();
		
		mail($to, $subject, $message, $headers);
	}
	
	public function createMessage($name, $contact, $order)
	{
		$message = "Запрос с сайта apex.dubna.ru\n\n";
		$message .= "Имя: {$name}\n";
		$message .= "Контактные данные: {$contact}\n\n\n";
		$message .= "Текст:\n\n\n{$order}\n";
		
		return iconv('utf-8', 'KOI8-R//IGNORE', $message);
	}
	
	public function getInfo($itemId)
	{
		global $core;
		
		$core->db->select()->from('site_feedback')->fields('$all')->where("id = {$itemId}");
		$core->db->execute();
		$core->db->colback_func_param = 0;
		$core->db->add_fields_deform(array('date', 'name'));
		$core->db->add_fields_func(array('date,"d.m.Y H:i"', 'stripslashes'));	
		$core->db->get_rows(1);
		
		$this->data = $core->db->rows; 
		
		return $this->data;
	}

	public function getMailAddress()
	{
		global $core;
		
		
		
		return 'info@apex-dubna.ru';
	}
	
	public function delete($itemId)
	{
		global $core;
		
		$core->db->delete('site_feedback', $itemId, 'id');
		
		return true;
	}

}


?>