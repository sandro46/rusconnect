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
 * Класс для работы с сессиями через пользовательский интерфейс. Базовый класс. Выбор метода работы с сесией происходит в файле engine.php
 *
 * ---------------------------------------------------------------------------------------
 * Тут реализовано два класса. Первый для работы с mysql второй с memcache
 * Опыты показали что класс который работает с mysql быстрее класса для работы с memcache
 * Но недостаток эксперимента в том что он проводился без нагрузки на сервер!
 * Возможно у Вас будут другие результаты производительности....  N join... :)
 */

class sessions_mysql {

	private static $db = null;
	private static $lifetime = null;
	private static $config = null; 
	
	function __construct($dbconfig) {
		self::$lifetime = ini_get('session.gc_maxlifetime');
		self::$config = $dbconfig;
	}
	
	/**
	 * Открытие сессиии
	 *
	 * @return bool
	 */
	public static function open() {
		
		
		self::$db = new mysql();
		self::$db->write_log= 0;
		self::$db->connect($core->CONFIG);
		return true;
	}

	/**
	 * Закрытие сессии
	 *
	 * @return bool
	 */
	public static function close() {
		return true;
	}

	/**
	 * Чтение данных сессии
	 *
	 * @param string $id
	 * @return bool
	 */
	public static function read($id) {
		$sql= "SELECT `sess_data` FROM sessions WHERE sess_id='". addslashes($id). "' limit 1";
		
		self::$db->s_query($sql);
		self::$db->get_rows(1);
		
		if(self::$db->rows) return self::$db->rows['sess_data'];

	    return false;
	}

	/**
	 * Запись данных сессии
	 *
	 * @param string $id
	 * @param string $data
	 * @return bool
	 */
	public static function write($id, $data) {
		
		$sql= "SELECT * FROM `sessions` WHERE `sess_id`='". $id. "'";
		self::$db->s_query($sql);
		self::$db->get_rows(1);
		
		if(self::$db->rows) {
			$sql= "UPDATE `sessions` SET `sess_timestamp`=". time(). ", `sess_data`='". $data. "' WHERE `sess_id` = '". $id. "'";
		} else {
			$sql= "INSERT INTO `sessions` (`sess_id`, `sess_data`, `sess_timestamp`) VALUE ('". addslashes($id). "', '". addslashes($data). "', ". time(). ")";
		}
		
		self::$db->s_query($sql);
		
		return true;
	}

	/**
	 * Удаление сессии
	 *
	 * @param string $id
	 * @return bool
	 */
	public static function destroy($id) {
		
		$sql= "DELETE FROM `sessions` WHERE sess_id='". addslashes($id). "'";
		self::$db->s_query($sql);
		
		return true;
	}

	/**
	 * Истечение срока жизни сессии
	 *
	 * @param integer $time_left
	 * @return bool
	 */
	public static function gc($time_left) {
		global $core;
		$sql= "DELETE FROM `sessions` WHERE `sess_timestamp` < ". time()- $time_left;
		self::$db->s_query($sql);
		return true;
	}
}


class sessions_memcache {

	private $memcache = NULL;
	private $temp_sess_data = array();
	private $lifetime = 1440;

	
	
	/**
	 * Конструктор класса
	 *
	 * @param Memcache $memcache
	 */
	function __construct(Memcache $memcache) {
		global $core;
		
		$this->memcache = $memcache;
		$this->lifetime = (!empty($core->CONFIG['session']) && !empty($core->CONFIG['session']['lifetime']))? $core->CONFIG['session']['lifetime'] : ini_get('session.gc_maxlifetime');		
	}

	/**
	 * Открытие сессиии
	 *
	 * @return bool
	 */
	public function open() {
	    
		return true;
	}
	
	/**
	 * Закрытие сессии
	 *
	 * @return bool
	 */
	public function close() {
		return true;
	}

	/**
	 * Чтение данных сессии
	 *
	 * @param string $id
	 * @return bool
	 */
	public function read($id) {
		if(isset($temp_sess_data[$id])) {
			return $temp_sess_data[$id];
		} else {
			return $this->get_sess_data($id);
		}
	}

	/**
	 * Запись данных сессии
	 *
	 * @param string $id
	 * @param string $data
	 * @return bool
	 */
	public function write($id, $data) {
		$dataStore = json_encode(sessions_memcache::unserialize($data));
		
		$this->memcache->set($id, $dataStore, 0, $this->lifetime);
		$this->temp_sess_data[$id] = $data;
		
		return true;
	}

	/**
	 * Удаление сессии
	 *
	 * @param string $id
	 * @return bool
	 */
	public function destroy($id) {
		$this->memcache->delete($id); 
		return true;
	}

	/**
	 * Истечение срока жизни сессии
	 *
	 * @param integer $time_left
	 * @return bool
	 */
	public function gc($time_left) {
		return true;
	}

	private function get_sess_data($id) {
		$data = json_decode($this->memcache->get($id),true);
		
		if(is_array($data)) {
			// Fucking session unserialize... :)
			$sessionBackup = $_SESSION;
			$_SESSION = $data;
			$temp_sess_data[$id] = session_encode();
			$_SESSION = $sessionBackup;
			
			return $temp_sess_data[$id];
		} else {
			return '';
		}
	}
	
	/**
	 * Fucking session unserialize...
	 */
	public static function unserialize($session_data) {
		$return_data = array();
		$offset = 0;
		while ($offset < strlen($session_data)) {
			if (!strstr(substr($session_data, $offset), "|")) {
				throw new Exception("invalid data, remaining: " . substr($session_data, $offset));
			}
			$pos = strpos($session_data, "|", $offset);
			$num = $pos - $offset;
			$varname = substr($session_data, $offset, $num);
			$offset += $num + 1;
			$data = unserialize(substr($session_data, $offset));
			$return_data[$varname] = $data;
			$offset += strlen(serialize($data));
		}
		return $return_data;
	}	
}

?>