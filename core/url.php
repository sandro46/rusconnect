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
 * Класс реализует перепись урлов для движка.
 * В данном класе реализовано три уровня для модификации урла:
 * 	1. Базовые правила хранящиеся в базе данных
 *  2. Пользовательские правила хранящиеся в конфигурационном файле сайта config.php
 *  3. Системные правила необходимые для правильной работы ядра системы
 * 
 * Два первых уровня модификации действуют на изменение урла, тоесть модифицируется только сам урл.
 * Третий уровень преобразуя урл в GET запрос исходя из требований системы.
 * 
 * ps. На реализацию класса подтолкнуло в первую очередь интеграции переписи урлов с базой данных (хранение правил переписи в базе) и гибкость возможностей
 */
class url_parser
{
	
	private $uri = '';
	private $rewrites = array();
	private $user_rewrite = array();
	private $core;

	
	function __construct($uri, $rewrites, core $core)
	{
		$this->uri = $uri;
		$this->user_rewrite = $rewrites;
		$this->core = $core;
	}

	/**
	 * Метод выполняет инициализацию класа.
	 * Метод выполняет пошаговый разбор урла. Порядок необходим именно такой: 
	 * 
	 * 	URL ->	Базовые правила -> Пользовательские правила -> Системные правила
	 */
	public function init()
	{
		unset($_GET);
		
		if(!isset($this->core->CONFIG['rewrites']) || !isset($this->core->CONFIG['rewrites']['UseUserRewrite']) || $this->core->CONFIG['rewrites']['UseUserRewrite'] == false) {
			$this->core->log->syslog('Использование рерайтов в базе данных отключено.');		
		} else {
			$this->core->log->syslog('Проверяем рерайты в базе данных.');
			$this->base_rewrite();
		}

		if(!isset($this->core->CONFIG['rewrites']) || !isset($this->core->CONFIG['rewrites']['ExtendedRewrite']) || $this->core->CONFIG['rewrites']['ExtendedRewrite'] == false) {
			$this->core->log->syslog('Использование рерайтов из файла отключено.');		
		} else {
			$this->core->log->syslog('Проверяем рерайты из файла.');
			$this->user_rewrite();
		}

		
		$this->core->log->syslog('Запускаем системные рерайты.');
		$this->system_rewrite();
		
//  		echo $this->core->log->sys();
//  		print_r($_GET);
		
	}
	
	/**
	 * Метод добавляет правило для базового уровня.
	 *
	 * @param string $rewrite  -> Урл который будет интерпретирован как тот который нужно переписать
	 * @param string $real_url -> Урл на который необходимо переписать
	 * @param string $group    -> Группа реврайтов (для расширения) 
	 * @return integer -> Возвращает id вставленого реврайта
	 */
	public function add($rewrite, $real_url, $group = 'Total', $siteId = false)
	{
		global $core;
		
		$siteId = ($siteId)? $siteId : $this->core->edit_site;
		
		$data[] =  array('group'=>addslashes($group), 'rewrite'=>addslashes($rewrite), 'real_url'=>addslashes($real_url), 'id_site' => $siteId);
		
		$this->core->db->autoupdate()->table('mcms_rewrite')->data($data);
		$this->core->db->execute();
		
		return $this->core->db->insert_id;
	}
		
	/**
	 * Метод возвращает id реврайта из базы
	 *
	 * @param string $rewrite
	 * @param string $group
	 * @return integer
	 */
	public function get_id($rewrite, $group = 'Total')
	{
		global $core;
		
		$this->core->db->select()->from('mcms_rewrite')->fields('id')->where('rewrite = \''.addslashes($rewrite).'\' AND `group` = \''.addslashes($group).'\' AND id_site = '.$this->core->edit_site);
		$this->core->db->execute();
		
		return intval($this->core->db->get_field());
	}
	
	/**
	 * Метод возвращает название реврайта по его id
	 *
	 * @param unknown_type $id
	 * @return unknown
	 */
	public function get($id)
	{
		if(!$id) return false;
		
		global $core;
		
		$this->core->db->select()->from('mcms_rewrite')->fields('rewrite')->where('id = '.$id);
		$this->core->db->execute();
		
		return $this->core->db->get_field();		
	}
	
	/**
	 * Обновляет uri в таблице реврайтов
	 *
	 * @param integer $rewrite_id
	 * @param string $real_url
	 * @param string $rewrite
	 * @param string $group
	 * @return integer
	 */
	public function edit($rewrite_id, $rewrite, $real_url='', $group = 'Total')
	{
		global $core;
		
		if($real_url) {
			$this->core->db->autoupdate()->table('mcms_rewrite')->data(array(array('id' => intval($rewrite_id), 'rewrite' => addslashes($rewrite), 'group' => addslashes($group), 'real_url' => addslashes($real_url))))->primary('id');
		} else {
			$this->core->db->autoupdate()->table('mcms_rewrite')->data(array(array('id' => intval($rewrite_id), 'rewrite' => addslashes($rewrite), 'group' => addslashes($group))))->primary('id');
		}
		
		$this->core->db->execute();
		
		return $rewrite_id;
	}
	
	/**
	 * Метод удаляет реврайт по переданому id
	 *
	 * @param integer $id
	 */
	public function del($id)
	{
		global $core;
		
		$this->core->db->delete('mcms_rewrite', intval($id), 'id');
	}

	/**
	 * Метод возвращает текущий урл не пропарсеный
	 *
	 * @return unknown
	 */
	public function get_uri()
	{
	    return $this->uri;
	}
	
	/**
	 * Метод парсит урл на наличие ссылки на утилиту
	 *
	 * @return unknown
	 */
	public function parseUtilsUrl()
	{
		if(substr($this->uri, 0, 1) == '-' || strstr($this->uri, '/-utils/') !== false) {
			$this->uri = substr($this->uri,2);
			$this->uri = str_replace('-utils','utils',$this->uri);
			return true;
		}
	
		return false;
	}
	
	/**
	 * Метод для парсинга урла из других точек входа
	 */
	static public function parse($url)
	{
		self::uri_parse_get_request($url);
	
		return true;
	}
	
	/**
	 * Метод производит поиск в базе по введеному урлу
	 * Если в базе нашелся реврайт то урл модифицируется
	 */
	private function base_rewrite()
	{
		
		$_lang = preg_replace("/^\/([A-Za-z]{2,3})\/(.+?)$/s", '\\1', $this->uri);
		$_uri = preg_replace("/^\/([A-Za-z]{2,3})\/(.+?)$/s", '/\\2', $this->uri);
		$_before_uri = $this->uri;
				
		$this->core->db->select()->from('mcms_rewrite')->fields('id','real_url', 'redirect')->where('(`rewrite` = \''.addslashes($_uri).'\' OR `rewrite` = \''.substr(addslashes($_uri), 1).'\')  AND `id_site` = '.$this->core->site_id);
		$this->core->db->execute();
		$this->core->db->get_rows();

		if(strlen($_lang) > 3) $_lang = false;
		
		
		
		if(count($this->core->db->rows)) {
			if(!$_lang || $_lang == '/') $_lang = (isset($this->core->CONFIG['lang']['name']))? $this->core->CONFIG['lang']['name'].'/': $this->core->CONFIG['lang']['default']['name'].'/' ;
			$rewrites[$this->core->db->rows[0]['id']] = $this->core->db->rows[0];
			if(substr($this->core->db->rows[0]['real_url'], 0, 1) == '/') $this->core->db->rows[0]['real_url'] = substr($this->core->db->rows[0]['real_url'], 1);
			$this->uri = '/'.$_lang.$this->core->db->rows[0]['real_url'];
			$this->core->log->syslog("Найдено совпадение в таблице преобразования URL из базы. id: <b>{$this->core->db->rows[0]['id']}</b> url до преобразования: <b>{$_before_uri}</b> url после: <b>{$this->uri}</b>");
		    
			// 301 redirect
		    if(!empty($rewrites[$this->core->db->rows[0]['id']]['redirect']) && intval($rewrites[$this->core->db->rows[0]['id']]['redirect'])) {
		        $url = $rewrites[$this->core->db->rows[0]['id']]['real_url'];
		        header("HTTP/1.1 301 Moved Permanently");
                header("Location: {$url}");
                exit(); 
		    }
		    
		} 
	}
	
	/**
	 * Метод выполняет модификацию урлов на пользовательском уровне
	 */
	private function user_rewrite()
	{
		if(!count($this->user_rewrite)) {
			
			return false;
		}
			
		if(isset($this->user_rewrite['all']) && is_array($this->user_rewrite['all'])) {
			foreach($this->user_rewrite['all'] as $rewrite) {
				$_before_uri = $this->uri;
				$this->uri = preg_replace($rewrite[0], $rewrite[1],$this->uri);
				if($this->uri != $_before_uri) {
					$this->core->log->syslog("Найдено совпадение в таблице преобразования URL из файла. url до преобразования: <b>{$_before_uri}</b> url после: <b>{$this->uri}</b>");
				}
			}	
		}
		
		
		if(isset($this->user_rewrite[$this->core->site_id]) && is_array($this->user_rewrite[$this->core->site_id])) {
			foreach($this->user_rewrite[$this->core->site_id] as $rewrite) {	
				$this->uri = preg_replace($rewrite[0], $rewrite[1],$this->uri);
				$this->core->log->syslog("Найдено совпадение в таблице преобразования URL из файла. url до преобразования: <b>{$_before_uri}</b> url после: <b>{$this->uri}</b>");
			}
		}
		
		
		
		
		//			$this->core->log->syslog("Найдено совпадение в таблице преобразования URL. id: <b>{$this->core->db->rows[0]}</b> url до преобразования: <b>{$_before_uri}</b> url после: <b>{$this->uri}</b>");
		
	}
	
	/**
	 * ####################################################################################
	 * Метод модифицирует урл на системном уровне
	 *
	 * Результат модификаций преобразуется в GET запрос к индексной странице
	 * -----------------------------------------------------------------------
	 * Пример урлов которые будут переписаны:
	 * 
	 * 		/about.html -> 	$_GET[lang]= DEFAULT_LANG 
	 * 						$_GET[module]= about
	 * 						$_GET[controller]=default
	 * 
	 * ----------------------------------------------------------
	 * 					
	 * 		/ru/  		->  $_GET[lang] =  ru
	 *
	 * ----------------------------------------------------------
	 *  
	 * 		/ru/acount.html -> $_GET[lang]=ru
	 * 						   $_GET[module]=acount 
	 * 						   $_GET[controller]=default
	 * 
	 * -----------------------------------------------------------
	 * 
	 * 		/ru/templates/ -> $_GET[lang] = ru 
	 * 						  $_GET[module] = templates 
	 * 						  $_GET[controller]=default
	 * 
	 * -----------------------------------------------------------
	 * 
	 * 		/ru/templates/list.html -> 	$_GET[lang] = ru 
	 * 									$_GET[module] = templates  
	 *	 								$_GET[controller]=list
	 * 
	 * -----------------------------------------------------------
	 * 
	 * 		/ru/templates/list/ -> 	$_GET[lang] = ru 
	 * 								$_GET[module] = templates  
	 *	 							$_GET[controller]=listt
	 * 
	 * -----------------------------------------------------------
	 * 
	 * 		/ru/templates/list/page/2/sort/name/ -> $_GET[lang] = ru 
	 * 												$_GET[module] = templates  
	 *	 											$_GET[controller]=listt
	 *
	 * 												$_GET[page]=2
	 * 												$_GET[sort]=name
	 * 
	 * ------------------------------------------------------------------------------------
	 * 
	 * 		/ru/templates/list/page/2/sort/name/index.html -> 	$_GET[lang] = ru 
	 * 															$_GET[module] = templates  
	 *	 														$_GET[controller]=listt
	 * 															$_GET[page]=2
	 * 															$_GET[sort]=name
	 * 		
	 * #######################################################################################		
	 */
	private function system_rewrite()
	{
		if(preg_match("/^\/([A-Za-z]{1,})\.html$/s", $this->uri, $res)) {
			$_GET['lang']= 'ru';
			$_GET['module']= $res[1];
			$_GET['controller']= 'default';
			return true;
		}
		
		if(preg_match("/^\/([A-Za-z]{2,3})\/$/s", $this->uri, $res)) {
			$_GET['lang']= $res[1];
			return true;
		}
			
		if(preg_match("/^\/([A-Za-z]{2,3})\/([A-Za-z_\-]{1,})\.html$/s", $this->uri, $res)) {
			
			$_GET['lang']= $res[1];
			$_GET['module']= $res[2];
			return true;
		}
		
		if(preg_match("/^\/([A-Za-z]{2,3})\/([A-Za-z_\-]{1,})\/$/s", $this->uri, $res)) {
			$_GET['lang']= $res[1];
			$_GET['module']= $res[2];
			return true;
		}
		
		if(preg_match("/^\/([A-Za-z]{2,3})\/([A-Za-z_\-]{1,})\/([A-Za-z_\-]{1,})\.html$/s", $this->uri, $res)) {
			$_GET['lang']= $res[1];
			$_GET['module']= $res[2];
			$_GET['controller']= $res[3];
			return true;
		}
		
		if(preg_match("/^\/([A-Za-z]{2,3})\/([A-Za-z_\-]{1,})\/([A-Za-z_\-]{1,})\/$/s", $this->uri, $res)) {
			$_GET['lang']= $res[1];
			$_GET['module']= $res[2];
			$_GET['controller']= $res[3];
			return true;
		}
		
		if(preg_match("/^\/([A-Za-z]{2,3})\/([A-Za-z_\-]{1,})\/([A-Za-z_\-]{1,})\/(.+?)$/s", $this->uri, $res)) {
			$_GET['lang']= $res[1];
			$_GET['module']= $res[2];
			$_GET['controller']= $res[3];
			$this->uri_parse_get_request($res[4]);
			return true;
		}
		
		if(preg_match("/^\/([A-Za-z]{2,3})\/([A-Za-z_\-]{1,})\/([A-Za-z_\-]{1,})\/(.+?)\/index\.html$/s", $this->uri, $res)) {
			$_GET['lang']= $res[1];
			$_GET['module']= $res[2];
			$_GET['controller']= $res[3];
			$this->uri_parse_get_request($res[4]);
			return true;
		}
		
		if($this->uri != '/index.php'){
			header("HTTP/1.0 404 Not Found");
			die('404 Not Found');
		}
		
	}

	/**
	 * Метод парсит урл который не был разобран регулярными выражениями
	 *
	 * @param string $request
	 */
	static private function uri_parse_get_request($request)
	{
		$arr = array();
		$trigger = 0;
		$stock = '';
	
		foreach(explode('/', $request) as $v) {
			if($v != ''&& substr($v, 0, 1) != '?') {
				if($trigger == 0) {
					$stock = $v;
					$arr[$stock]= NULL;
				} else {
					$arr[$stock]= $v;
					$stock= '';
				}
			} elseif(substr($v, 0, 1) == '?') {
				parse_str(substr($v, 1),$out);
				if($trigger == 0) {
					$arr = array_merge($arr, $out);	
				} else {
					$arr[$stock] = $out;
				}
			}
			
			$trigger = ($trigger) ? 0 : 1;
		}
			
		if(!isset($_GET) || !is_array($_GET)) $_GET = array();
		$_GET = array_merge($_GET, $arr);
	}
	
}

?>
