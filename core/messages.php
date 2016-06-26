<?php
################################################################################
# ---------------------------------------------------------------------------- #
# @Author: Alexey Pshenichniy <inc.mcms@gmail.com> <M-zone Inc.> 2005-2009     #
# @Date: 20.05.2008                                                            #
# @license: GNU GENERAL PUBLIC LICENSE Version 2                               #
# ---------------------------------------------------------------------------- #
# M-cms v4.1 (core build - 4.102)                                              #
################################################################################


/**
 * Базовый класс. Входит в состав ядра. Реализует методы для работы с многоязычными строковыми данными условно именнуеммые "Сообщения"
 */
class messages
{
	/**
	 * Внутренняя переменная. Хранит текст уже вызванных сообщений чтобы не вызывать повторно одинаковые
	 *
	 * @var array
	 */
	private $messages = array();
	
	function __construct()
	{
		//$this->get_all();
	}
	
	/**
	 * Метод вытаскивает все сообщения на текущем языке и записывает их в массив $this->messages
	 */
	private function get_all()
	{
		global $core;
		
		$core->db->select()->from('mcms_messages')->fields('name', 'text', 'message_id')->where('lang_id = '.$core->CONFIG['lang']['id']);
		$core->db->execute();
		
		$res = $core->db->result;
		
		while($row = mysql_fetch_assoc($res))
			{
			$this->messages[$row['message_id']] = $row;
			}
	}
	
	/**
	 * Метод возвращает текст сообщения на текущем языки принимая в качестве параметра его id
	 *
	 * @param int $message_id
	 * @return string
	 */
	function get($message_id)
	{
		
		if(isset($this->messages[$message_id]))
			return $this->messages[$message_id]['text'];		
		

		global $core;
		
		$core->db->select()->from('mcms_messages')->fields('name', 'text', 'message_id')->where('lang_id = '.$core->CONFIG['lang']['id'].' AND message_id = '.$message_id);
		$core->db->execute();
		$core->db->get_rows();
		
		if(!isset($core->db->rows[0]) || !isset($core->db->rows[0]['text'])) return  false; 
		
		$this->messages[$core->db->rows[0]['message_id']] = $core->db->rows[0];
				
		return $core->db->rows[0]['text'];
	}
	
}

?>