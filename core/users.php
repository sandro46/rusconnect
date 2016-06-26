<?php
################################################################################
# ---------------------------------------------------------------------------- #
# @Author: Alexey Pshenichniy <inc.mcms@gmail.com> <M-zone Inc.> 2005-2012     #
# @Date: 20.05.2008                                                            #
# @license: GNU GENERAL PUBLIC LICENSE Version 2                               #
# @lastModified = 1324128382                                                   #
# ---------------------------------------------------------------------------- #
# M-cms v4.1 (core build - 4.102)                                              #
################################################################################


/**
 * Класс реализует работы с пользователями:
 * 		- Авторизацию пользователей по токенам
 * 		- Идентификацию пользователей
 * 		- Запись данных в сессию
 * 		- Проверку на "подленник" сессии (защита от кражи сессии)
 *
 */
class users
{
	/**
	 * ID пользователя
	 *
	 * @var integer
	 */
	public $id = 0;
	
	/**
	 * логин пользователя
	 *
	 * @var string
	 */
	public $login = '';
	
	/**
	 * Пользователь прошел авторизацию?
	 *
	 * @var bool
	 */
	public $auth = false;
	
	/**
	 * Личные данные пользователя
	 *
	 * @var array
	 */
	public $info = array();
	
	/**
	 * Уникальный ключ сессии пользователя (генерируется в етом класе а не броузером!)
	 *
	 * @var string
	 */
	public $secKey = '';
	
	/**
	 * Сообщение об ошибке
	 * @var string
	 */
	public $messages = '';
	
	/**
	 * ID Текущего сайта
	 * @var integet
	 */
	public $site_id = 0;
	
	/**
	 * Группы в которых состоит пользователь
	 * @var array
	 */
	public $groups = array();
	
	/**
	 * Дополнительный id пользователя для использования в других модулях
	 * @var integer
	 */
	public $custom_id = 0;
	
	private $core = false;
	
	/**
	 * Конструктор класса
	 */
	public function __construct($core) {
		$this->core = $core;
	}
	
	/**
	 * Метод выполянет процедуры "разпознавания пользователя" прошедшого авторизацию и проверяет на уникальность сессии.
	 */
	public function init($dieOnNoLogin=true) {		
		//echo json_encode($_SESSION);die();
		if(isset($_SESSION['uid']) && isset($_SESSION['session_security_key']) && intval($_SESSION['uid']) > 0) {
			$this->check_session();
			$this->id = $_SESSION['uid'];
			$this->get_info();
			
			if($this->id != $_SESSION['uid']) {
				unset($_SESSION['uid'], $_SESSION['session_security_key']);
				$this->init();
				return true;
			}
			
			if(isset($_SESSION['site_theme'])) $this->core->theme = $_SESSION['site_theme'];
			if(isset($_SESSION['custom_uid']) && intval($_SESSION['custom_uid'])) {
				$this->custom_id = intval($_SESSION['custom_uid']);
			}
			
			$this->core->tpl->assign('SystemUser', array('id'=>$this->id, 'login'=>$this->login, 'name'=>$this->info['name']));
			$this->core->tpl->assign('user_info', $this->info);
		} else {	
			if(!isset($this->core->CONFIG['lang']['multi']) || $this->core->CONFIG['lang']['multi'] == true) {
				$this->core->tpl->assign('language_menu', $this->core->get_all_langs());
			}

			if(isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] == 'majax:true') {
				$this->core->tpl->assign('majax',true);
			}
			
			$this->core->tpl->assign('error_message',$this->messages);
			
			if($dieOnNoLogin) {
				if(majax::is()) {
					echo '{"status":"error","message":"Session expired","code":1002}';
					die();
				}
				
				$this->core->error_page(401);
				die();
			}
		}
	}

	/**
	 * Метод получает информацию о пользователе
	 *
	 * @param integer $id
	 */
	public function get_info($id = 0) {
		$id = ($id != 0)? $id : $this->id;
		
		if(isset($this->core->CONFIG['perfomance']) && isset($this->core->CONFIG['perfomance']['cache_user_info']) && $this->core->CONFIG['perfomance']['cache_user_info'] != false) {
			$cached = false;
			$useCahce = true;
			$cacheVar = md5("userId:{$id}");
			if(isset($_SESSION[$cacheVar])) {
				$this->info = $_SESSION[$cacheVar];
				$cached = true;
			}			
		} else {
			$cached = false;
			$useCahce = false;
		}
		
		if(!$cached) {
			$this->core->db->select()->from('mcms_user')->fields('*')->where('id_user = "'.intval($id).'"');
			$this->core->db->execute();
			$this->core->db->get_rows(1);
			
			if(!isset($this->core->db->rows['id_user'])) {
				$this->info = $this->id = $this->site_id = $this->login = false;
				return false;
			} else {
				$this->info = $this->core->db->rows;
				unset($this->info['memo']);
				unset($this->info['password']);
			}	
		}
		
		$this->login = $this->info['login'];
		$this->site_id = $this->info['default_site_id'];
		$this->id = $this->info['id_user'];
		
		if($useCahce == true && $cached == false) {
			$_SESSION[$cacheVar] = $this->info;
		}
	}

	/**
	 * Метод записывает авторизационные данные в переменную сессии
	 */
	public function write_session() {
		$_SESSION['uid'] = $this->id;
		if($this->custom_id > 0) $_SESSION['custom_uid'] = $this->custom_id;
		$_SESSION['session_security_key'] = $this->get_security_key();
	}
	
	/** 
	 * Метод выбирает текущую тему пользователя
	 * @param string $theme
	 */
	public function select_theme($theme=false) {	
		
		$theme = ($theme)? mysql_real_escape_string(trim($theme)) : false;
		
		if($theme) {
			$this->core->db->select()->from('mcms_themes')->fields('id','name','static_path')->where("id_site = {$this->core->site_id} AND name = '{$theme}'");
		} else {
			$this->core->db->select()->from('mcms_themes')->fields('id','name','static_path')->where("id_site = {$this->core->site_id}")->limit(1);
		}
		
		$this->core->db->execute();
		$this->core->db->get_rows(1);

		if(!$this->core->db->num_rows() || !is_array($this->core->db->rows) || !isset($this->core->db->rows['name'])) {
			$this->core->theme = 'default';
			$path = '/templates/';
			$id = 0;
		} else {
			$this->core->theme = $this->core->db->rows['name'];
			$id = $this->core->db->rows['id'];
			$path = $this->core->db->rows['static_path'];
		}
		
		$_SESSION['site_theme'] = $this->core->theme;
		$_SESSION['site_theme_id'] = $id;
		$_SESSION['site_theme_path'] = $path;
		
		return $this->core->theme;		
	}
		
	public function set_site_edit($id) {
		if(!$id = intval($id)) return false;
		if($this->core->is_admin()) {
			$_SESSION['site_id'] = intval($id);
			$this->core->db->select()->from('mcms_sites')->fields('name')->where('id = '.$id);
			$this->core->db->execute();
			session_commit();
			return $this->core->db->get_field();
		}
		
		return false;
	}
	
	/**
	 * Метод проверяет уникальность сессии пользователя
	 */
	private function check_session() {
		$this->get_security_key();
		
		if($this->secKey != $_SESSION['session_security_key'])
		{
			session_destroy();
			echo 'Unauthorized';
			die();
		}
	}
	
	/**
	 * Метод генерирует уникальный id пользователя используя ip адресс с которого он пришел а также ip адресс прокси сервера (если такой имеет место быть)
	 */
	private function get_security_key() {
		$this->secKey = (!empty($_SERVER['HTTP_USER_AGENT']))? $_SERVER['HTTP_USER_AGENT'] : '';
		$this->secKey .= (!empty($_SERVER['HTTP_VIA']))? $_SERVER['HTTP_VIA'] : '';
		$this->secKey .= (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))? $_SERVER['HTTP_X_FORWARDED_FOR'] : '';		
		$this->secKey = md5($this->secKey);
		
		return $this->secKey;
	}
	
	/**
	 * Метод получает все группы в которых состоит пользователь
	 */
	private function get_groups_for_user($userId) {
		if(!$userId) return false;
		
		$sql = "SELECT g.id_group, g.name FROM mcms_group as g, mcms_user_group as ug WHERE ug.id_user = {$userId} AND g.lang_id =1 AND g.id_group IN(ug.id_group)";
		$this->core->db->query($sql);
		$this->core->db->get_rows();
		
		return $this->core->db->rows;		
	}

	/**
	 * Метод определяет реальный ip пользователя
	 */
	private function getip() {
		if (isset($_SERVER)) {
	    	if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $this->validip($_SERVER['HTTP_X_FORWARDED_FOR'])) {
	       		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	     	} elseif(isset($_SERVER['HTTP_CLIENT_IP']) && $this->validip($_SERVER['HTTP_CLIENT_IP'])) {
	       		$ip = $_SERVER['HTTP_CLIENT_IP'];
	     	} else {
	       		$ip = $_SERVER['REMOTE_ADDR'];
	     	}
	   } else {
	   		if(getenv('HTTP_X_FORWARDED_FOR') && $this->validip(getenv('HTTP_X_FORWARDED_FOR'))) {
	       		$ip = getenv('HTTP_X_FORWARDED_FOR');
	     	} elseif(getenv('HTTP_CLIENT_IP') && $this->validip(getenv('HTTP_CLIENT_IP'))) {
	       		$ip = getenv('HTTP_CLIENT_IP');
	     	} else {
	       		$ip = getenv('REMOTE_ADDR');
	     	}
	   }

	   return $ip;
	}
	
	/**
	 * Метод проверяет реальность ip адреса и что он не принадлежит приватной сети
	 * @param string $ip
	 */
	private function validip($ip) {
		if(!empty($ip) && $ip == long2ip(ip2long($ip))) {
			$reserved_ips = array (
				array('0.0.0.0','2.255.255.255'),
				array('10.0.0.0','10.255.255.255'),
				array('127.0.0.0','127.255.255.255'),
				array('169.254.0.0','169.254.255.255'),
				array('172.16.0.0','172.31.255.255'),
				array('192.0.2.0','192.0.2.255'),
				array('192.168.0.0','192.168.255.255'),
				array('255.255.255.0','255.255.255.255')
			);
	
			foreach($reserved_ips as $r) {
				$min = ip2long($r[0]);
				$max = ip2long($r[1]);
				if((ip2long($ip) >= $min) && (ip2long($ip) <= $max)) return false;
			}
		
			return true;
		}
		
		return false;
	}
	
}
?>
