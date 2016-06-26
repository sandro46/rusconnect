<?php
################################################################################
# ---------------------------------------------------------------------------- #
# @Author: Alexey Pshenichniy <inc.mcms@gmail.com> <M-zone Inc.> 2005-2009     #
# @Date: 24.03.2005 - 13.12.2011                                               #
# @license: GNU GENERAL PUBLIC LICENSE Version 2                               #
# ---------------------------------------------------------------------------- #
# M-cms v5.5 (core build - 12.2.6684)                                          #
################################################################################

class core
{
	// основные объекты
	public $tpl;
	public $db;
	public $lib;
	public $log;
	public $user;
	public $memcache;
	public $widgets;
	public $url_parser;
	public $ajax;
	public $loader;
	public $history;
	public $shop;

	// сайт
	public $CONFIG = array(); // конфиг
	public $site_name = '';  // название сайта
	public $site_id = ''; // id сайта
	public $site_type = 1; // тип сайта. 1 - админка
	public $edit_site = 1; // id редактируеммого сайта (если это админка)
	public $siteObject = array(); // настройки сайта
	public $edit_site_options = array(); // настройки редактируеммого сайта

	// URL
	public $parse_url_off = 0;

	// язык
	public $lang = 'ru';

	// интерфейс
	public $theme = 'default';
	public $themePath = '/';
	public $main_tpl = 'main.html';
	public $contentType = 'text/html';
	public $langId = 0;
	public $page_name = '';

	// данные
	public $module_name = '';
	public $module_id = 0;
	public $controller = '';
	public $controller_object = false;
	public $content = '';
	public $controller_content = '';

	// страница
	public $title = 'Main Page';
	public $meta_title = '';
	public $meta_keywords = '';
	public $meta_description = '';
	public $light_version = false;

	public $debug = false;
	
	// статическая ссылка на ядро
	public static $instance = false;
	


	public final function __construct($config = array()) {
		if(self::$instance instanceof core) {
			return self::$instance;
		}

		self::$instance = $this;
		$this->CONFIG = $config;

		// отладочная консоль и режим отладки
		// настраивается в конфиге. Включить можно установкой куки isdev = 1
		if(IS_DEV) {
			define('DEBUG', true);
			$this->debug = true; 

			register_shutdown_function(function(){
				if(!defined('DEBUG') || DEBUG !== true) return; 
				$lastError = error_get_last();
		
				if(is_array($lastError) && !empty($lastError['type']) &&
						$lastError['type'] != E_WARNING &&
						$lastError['type'] != E_NOTICE &&
						$lastError['type'] != E_CORE_WARNING &&
						$lastError['type'] != E_USER_NOTICE) {
					if(class_exists('loger') && $this->log instanceof loger && !$this->log->syslogShowed) {
						@header('Content-Type: text/html; charset=utf-8');
						echo $this->console(false);
					} else {
						echo "<h1>Core panic!</h1> <p>Error: {$lastError['message']}<br>Line: {$lastError['line']}<br>File: {$lastError['file']}</p>";
						$e = new Exception();
						echo "</br><h2>Trace: </h2><p>".$e->getTraceAsString()."</p>";
					}
				}
			});
		}
		
		
		include $this->CONFIG['core_path'].'library.php';

		// загрузчик и основные классы
		$this->lib = new library($this->CONFIG);
		$this->lib->loadWithoutCheck('templates','mysql','loger','debug_class', 'users', 'majax', 'modules' ,'widgets','permissions','history');

		$this->startSession();
		
		// Если запрос пришел от Ajax, мы отключаем все логи, отладчики, обработку шаблонов и прочее. Это значительно облегчает обработку таких запросов
		if(majax::is()) { 
			$this->light_version = true; 
		}

				
		// логер
		$this->log = ($this->light_version)? new debug_class(false,true) : new loger($this->CONFIG);
		$this->log->syslog('Логирование системы включено.');
		$this->log->syslog('Подключены библиотеки: '.implode(', ',$this->lib->getLoaded()));

		// кеширование
		if(isset($this->CONFIG['cache']) && isset($this->CONFIG['cache']['enable']) && $this->CONFIG['cache']['enable']) {
			$this->log->syslog('Включено файловое кеширование. Загружаем библиотеку.');
			$this->lib->load('cache');
			$this->cache = new cache($this->CONFIG, $this);
			$this->log->syslog('Проверяем наличие кеша.');
			$this->cache->init();
			$this->log->syslog('В кеше файлов не найдено.');
		}

		// база данных
		$this->db = new mysql($this->CONFIG);
		$this->log->syslog("Загружен драйвер базы данных. Пробуем подключиться (host: <b>{$this->CONFIG['db_site']['dbhostname']}</b> database: <b>{$this->CONFIG['db_site']['dbname']}</b> login: <b>{$this->CONFIG['db_site']['dbusername']}</b>)");
		$this->db->connect();
		$this->log->syslog('Соединение с базой данных установлено.');
		
		// шаблонизатор
		$this->log->syslog('Запускаем и конфигурируем шаблонизатор.');
		$this->tpl = new templates();
		$this->tpl->compil_dir   	  = $this->CONFIG['templates']['compil_dir'];
		$this->tpl->db_table_tpl 	  = $this->CONFIG['templates']['bd_src_table'];
		$this->tpl->compil_file_ext   = $this->CONFIG['templates']['file_ext'];
		$this->tpl->compil_chek_level = $this->CONFIG['templates']['check_level'];	
		$this->tpl->debugParser		  = $this->CONFIG['templates']['debug']['varsWarning'];
		$this->tpl->htmlminimizer 	  = (isset($this->CONFIG['templates']['min']) && $this->CONFIG['templates']['min'])? true : $this->tpl->htmlminimizer;
		$this->tpl->useSyntaxSugar 	  = (isset($this->CONFIG['templates']['syntax_sugar']) && $this->CONFIG['templates']['syntax_sugar'])? true : $this->tpl->useSyntaxSugar;
		$this->tpl->init();
		$this->tpl->assign('IS_DEV', IS_DEV);
		
		$this->log->syslog("Движок шаблонов запущен. Каталог для кеша: <b>{$this->tpl->compil_dir}</b> Уровень проверки кеша: <b>{$this->tpl->compil_chek_level}</b> Минимайзер: <b>".($this->tpl->htmlminimizer? 'включен':'выключен')."</b>");

		

		$this->lib->dll('api');
		$this->log->syslog('Подключен файл с внешними функциями.');

		// определение сайта
		$this->log->syslog('Определяем какой сайт запустить.');
		$this->get_site_options();
		$this->log->syslog("Определен сайт с именем <b>{$this->site_name}</b> id: <b>{$this->site_id}</b> это панель администратора? <b>".($this->is_admin()? 'да':'нет')."</b>");

		//пользователи
		$this->log->syslog('Подключаем класс для работы с пользователями.');
		$this->user = new users($this);

		// аякс
		$csrfsec = (!empty($this->CONFIG['security']['ajax_csrf']) && $this->CONFIG['security']['ajax_csrf'])? true : false;
		$this->log->syslog('Подключаем класс для работы с ajax. Защита от атаки <b>CSRF</b> для ajax <b>'.(($csrfsec)? '<span style="color:green">Включена</span>' : '<span style="color:red">Отключена</span>').'</b>');
		$this->ajax = new majax($csrfsec);

		// модули
		$this->log->syslog('Подключаем библиотеку управления модулями.');
		$this->modules = new modules($this);

		// виджеты
		$this->widgets = new widgets();
		$this->log->syslog('Подключен движок виджетов.');
		
		// права доступа
		$this->log->syslog('Подключаем библиотеку управления правами доступа.');
		$this->perm = new permissions($this);

		// История изменений
		$this->history = new history();

		$this->log->syslog('Пробуем найти обертку над ядром, класс main_module.');
		if(file_exists(CORE_PATH.'core/main_module.php')) {
			$this->log->syslog('Класс main_module найден. Подключаем.');
			include CORE_PATH.'core/main_module.php';
		} else {
			$this->log->syslog('Класс main_module не найден.');
		}
		
		$this->log->syslog('Ядро загружено.');
	}

	/**
	 * Загрузчик сайта
	 */
	public function start() {
		$this->log->syslog('Запускаем сайт.');
		$this->log->syslog('Проверяем необходимость консоли отладки.');

		// консоль
		if(!$this->debug && isset($this->CONFIG['console']) && isset($this->CONFIG['console']['sites']) && (!is_array($this->CONFIG['console']['sites']) || !in_array($this->site_id, $this->CONFIG['console']['sites']))) {
			if(!$this->light_version) {
				$this->log->__destruct();
				$this->log = new debug_class(false,true);
				$this->log->syslog('Консоль отключена.');
			}
		} else {
			$this->log->syslog('Консоль используется.');
		}

		$this->log->syslog('Проверяем необходимость движка обработки url.');

		// парсер урлов и утилиты
		if($this->parse_url_off != 1) {
			$this->log->syslog('Подключаем файл с рерайтами, если такой имеется.');

			$rewrite = $this->lib->adll('rewrites');
			$rewrite = (isset($rewrite) && is_array($rewrite))? $rewrite : array();

			$this->log->syslog('Подключаем движок обработки url.');
			$this->lib->load('url');
			$this->url_parser = new url_parser($_SERVER['REQUEST_URI'], $rewrite, $this);
			$this->url_parser->init();

			if($this->CONFIG['utils']['enable']) {
				$this->log->syslog('Запускаем парсер url для утилит.');
				$status = $this->url_parser->parseUtilsUrl();
				if($status && $_GET['controller']) {
					$this->log->syslog("Вызвана утилита <b>{$_GET['controller']}</b>. Запускаем ее.");
					$file = $this->CONFIG['utils_path'].'utils.'.$_GET['controller'].'.php';
					if(is_file($file)) {
						$core = $this;
						include $file;
					} else {
						$this->log->syslog("Не найден файл с утилитой <b>{$_GET['action']}</b>. Полный путь: <b>{$file}</b>");
					}
					die();
				}
			}
		}

		// язык
		$this->log->syslog('Получаем данные по выбранному языку.');
		if(isset($_GET['lang'])) {
			$this->lang = $_GET['lang'];
			unset($_GET['lang']);
		}
		$this->get_lang_info();

		$this->log->syslog('Подключаем загрузчик модуля.');
		$this->lib->load('loader');
		$this->loader = new loader($this);

		if($this->is_admin()) {
			$this->log->syslog('Начинаем запуск панели управления.');
			$this->header('utf-8');

			$this->log->syslog('Проверяем авторизацию пользователя.');
			$this->user->init();
			$this->log->syslog("Пользователь авторизован. ID: <b>{$this->user->id}</b> Имя: <b>{$this->user->info['name']}</b> Логин: <b>{$this->user->login}</b>");

			$this->log->syslog('Определяем тему.');
			$this->setTheme();
			$this->log->syslog("Текущая тема: <b>{$this->theme}</b> путь к статике: <b>{$_SESSION['site_theme_path']}</b>.");

			$this->log->syslog('Устанавливаем редактируеммый сайт.');
			$this->setSiteEdit();
			$this->log->syslog("Сейчас редактируется сайт с ID: <b>{$this->edit_site}</b>.");

			$this->log->syslog('Подключаем контроль истории изменений.');
			$this->lib->load('history');
			$this->history = new history();

			$this->main_tpl = 'main.html';
			$this->log->syslog("Устанавливаем главный шаблон <b>{$this->main_tpl}</b>.");

			$this->log->syslog('Запускаем движок модулей.');
			$this->loader->init();
		} else {

			$this->log->syslog('Отправляем заголовок.');
			$this->header('utf-8');

			$this->log->syslog('Проверяем авторизацию пользователя.');
			
			$this->user->init($this->useAuth());

			if($this->user->id) {
				$this->log->syslog("Пользователь авторизован. ID: <b>{$this->user->id}</b> Имя: <b>{$this->user->info['name']}</b> Логин: <b>{$this->user->login}</b>");
			} else {
				$this->log->syslog('Пользователь не авторизован. Присваиваем этому пользователю ID гостя.');
				$this->user->id = 3;
			}

			$this->log->syslog('Определяем тему.');
			$this->setTheme();
			$this->log->syslog("Текущая тема: <b>{$this->theme}</b> путь к статике: <b>{$_SESSION['site_theme_path']}</b>.");

			$this->main_tpl= 'main.html';
			$this->log->syslog("Устанавливаем главный шаблон <b>{$this->main_tpl}</b>.");

			$this->log->syslog('Запускаем движок модулей.');
			
			$this->loader->init();
		}

		$this->log->syslog('Загрузка сайта успешно завершена.');
		$this->ajax->listen();
	}

	/**
	 * Метод завершает выполнение кода, и формирует страницу
	 */
	public function footer($die = 0, $debug = false){
		if($die == 1) {
			$this->tpl->assign('content', $this->content);
			$this->module_name = $this->site_name;
			$this->tpl->display($this->main_tpl);
			die();
		}

		//if($this->log->access_enable == 1 && !$this->log->accesLoged && $this->log->access_status != 1) $this->log->access();

		$controller = ($this->controller_object instanceof controller)? $this->controller_object : false;

		// проверяем был ли установлен шаблон в контроллере
		if($controller && $controller->tpl){
			//если шаблон был установлен, проверяем включено ли кеширование работы контроллера
			if($controller->cached) {
				//если кэширвоание контроллера включено, то пытаемся получить данные из кэша методом шаблонизатора  FETCH
				$this->controller_content =  $this->tpl->fetch($controller->tpl, 1, $controller->cache_param, $controller->cache_expire);
			} else {
				//если кеширвоание выключено, то обрабатываем шаблон и получаем его html
				$this->controller_content =  $this->tpl->fetch($controller->tpl, 1);
			}
			//Добавляем к полученому контенту контент который был добавлен грубым способом из контроллера
			$this->controller_content .= $controller->content;
		} else {
			// если в контроллере не был установлен шаблон то в стек контента контроллера залимваем контент который был установлен в самом контроллере напрямую
			$this->controller_content = ($controller)? $controller->content : $this->controller_content;
		}

		// передаем контент контроллера в шаблонизатор
		$this->tpl->assign('controller_content', $this->controller_content);

		//если для модуля был установлен шаблон то обробатываем его
		if(is_array($this->modules->this['tpl']) && $this->modules->this['tpl']['name']) {
			if($this->modules->this['tpl']['cached']) {
				// если кеширвоание работы модуля включено, то получаем контент шаблона из кэша (если контент устарел то записываем свежий в кэш и получаем его) методом шаблонизатора FETCH
				$this->content = $this->tpl->fetch($this->modules->this['tpl']['name'], 1, $this->modules->this['tpl']['cach_param'], $this->modules->this['tpl']['cach_expire']);
			} else {
				//если кеширвоание отключено то просто получаем html сгенерированого шаблона
				$this->content = $this->tpl->fetch($this->modules->this['tpl']['name'], 1);
			}
			
			if(!$this->content && $this->controller_content) {
			    $this->content = $this->controller_content;
			}
		} else {
			$this->content .= $this->controller_content;
		}
		$this->tpl->assign('content', $this->content);
		$this->tpl->assign('title', $this->title);
		$this->tpl->assign('majax',$this->ajax->out());
		$this->tpl->assign('criticalError', @$this->log->criticalError);
		$this->make_meta();	
		$this->console();

		//if($this->CONFIG['cache']['enable'] && $this->main_tpl && $this->cache->cached) {
		//	$html = $this->tpl->fetch($this->main_tpl,1,0,0,/*$this->site_name*/'Frontend');
		//	$this->cache->set($html);
		//	echo $html;
		//} else {
			//echo $this->site_name; die();
			$this->module_name = ($this->is_admin())? $this->getAdminModule() : $this->site_name;
			$this->tpl->display($this->main_tpl);
		//}

		session_write_close();
		
		if($debug || (defined('DEBUG') && DEBUG === true)) {

			?><html><head><meta http-equiv="content-type" content="text/html; charset=utf-8" /></head><body><?php 
			echo $this->log->sql();
			echo $this->log->sys();
			?></body></html><?php 
		} else {
			die();
		}

	}

	/**
	 * Метод отправляет хидр. Должен вызыватся после вызова метода load_core
	 *
	 * @param unknown_type $charset
	 */
	public function header($charset = 0) {
		// если передана кодировка, отправляем хидр
		if($charset && isset($_GET['controller']) && $_GET['controller'] != 'download' && !majax::is()) {
		    @header('Content-Type: '.$this->contentType.'; charset='.$charset);	
		} 
	}

	/**
	 * Метод делает выбор редактируемого сайта
	 */
	public function setSiteEdit() {
		$this->edit_site = (isset($_SESSION['site_id']) && intval($_SESSION['site_id']))? intval($_SESSION['site_id']) : ((isset($_COOKIE['site_id']) && intval($_COOKIE['site_id']))? intval($_COOKIE['site_id']): (($this->user->id && $this->user->site_id)? $this->user->site_id : 1));
		$this->edit_site_options = $this->getEditSiteName();
		$this->tpl->assign('edit_site',$this->edit_site_options);
	}

	/**
	 * Метод получает информацию о текущем редактируемом сайте.
	 */
	public function getEditSiteName() {
		if(!$this->is_admin()) return $this->siteObject;

		$cached = false;
		if(isset($this->CONFIG['perfomance']) && isset($this->CONFIG['perfomance']['cache_sites']) && $this->CONFIG['perfomance']['cache_sites']) {
			$cached = true;
			$cacheVar =  md5("siteId:{$this->edit_site}");
			if(isset($_SESSION[$cacheVar]) && is_array($_SESSION[$cacheVar])) {
				$this->tpl->assign('current_edit_site', $_SESSION[$cacheVar]);
				return $_SESSION[$cacheVar];
			}
		}

		$this->db->select()->from('mcms_sites')->fields('id', 'name describe', 'server_name','type', 'custom_preloader')->where("id = {$this->edit_site}");
		$this->db->execute();
		$this->db->get_rows(1);

		if($cached) {
			$_SESSION[$cacheVar] = $this->db->rows;
		}

		$this->tpl->assign('current_edit_site', $this->db->rows);

		return $this->db->rows;
	}

	/**
	 * Узменяет или устанавливает текущий модуль
	 * @param string $module_name
	 */
	public function setModule($module_name = false) {
		return $this->module_name = ($module_name)? $module_name : $this->getAdminModule();
	}

	/**
	 * Выводит имя главного модуля для интерфейса администратора
	 * Используется при поиске шаблонов
	 */
	public function getAdminModule($for_loader = false){
		if($for_loader) return '_admin';
		return (isset($this->CONFIG['admin']['moduleName']))? $this->CONFIG['admin']['moduleName'] : 'AdminPanel';
	}

	/**
	 * Запущенный сайт, это панель управления?
	 */
	public function is_admin() {
		return ($this->site_id == 1)? true : false;
	}

	/**
	 * @deprecated
	 * Метод формирует вывод отладочной консоли исходя из настроек
	 */
	public function console($check=true)  {
		$console_log = '';

		if(in_array($this->site_id, $this->CONFIG['console']['sites']) || !$check) {
			if($this->CONFIG['console']['level'] >= 1 || !$check) $console_log .= $this->log->dump_string_log();
			if($this->CONFIG['console']['level'] >= 2 || !$check) $console_log .= $this->log->dumpQueries();
			if($this->CONFIG['console']['level'] >= 3 || !$check) $console_log .= $this->log->dump_tpls();
			if($this->CONFIG['console']['level'] >= 4 || !$check) $console_log .= $this->log->sys();
			if($this->CONFIG['console']['level'] >= 5 || !$check) $console_log .= $this->log->dump_cached();
			if($this->CONFIG['console']['level'] >= 6 || !$check) $console_log .= $this->log->dumpIncludes();
			//if($this->CONFIG['console']['level'] >= 7 || !$check) $console_log .= $this->log->trace();
		}

		if(!$check) return $console_log;
		$this->tpl->assign('debug', $console_log);

		return $console_log;
	}

	/**
	 * Метод устанавливает параметры мета-тегов и заголовок страницы
	 */
	private function make_meta() {
		if($this->meta_title) {
			$this->tpl->assign('meta_title', $this->meta_title);
		} else {
			if(isset($this->modules->this['meta_title'])) $this->tpl->assign('meta_title', $this->modules->this['meta_title']);
		}
	
		if($this->meta_description) {
			$this->tpl->assign('meta_description', $this->meta_description);
		} else {
			if(isset($this->modules->this['meta_description'])) $this->tpl->assign('meta_description', $this->modules->this['meta_description']);
		}
	
		if($this->meta_keywords) {
			$this->tpl->assign('meta_keywords', $this->meta_keywords);
		} else {
			if(isset($this->modules->this['meta_keywords'])) $this->tpl->assign('meta_keywords', $this->modules->this['meta_keywords']);
		}	
	}

	/**
	 * Метод устанавливает параметры выбранного языка
	 *
	 * @category core
	 * @access private
	 */
	private function get_lang_info() {
		$def_lang = (isset($this->CONFIG['lang']) && isset($this->CONFIG['lang']['default']))? $this->CONFIG['lang']['default']['id'] : 1;
		
		if(isset($this->CONFIG['lang']) && isset($this->CONFIG['lang']['multi']) && $this->CONFIG['lang']['multi'] == false)  {
			$this->CONFIG['lang'] = $this->CONFIG['lang']['default'];
			$this->CONFIG['lang']['multi'] = false;
			$this->tpl->assign('lang', $this->CONFIG['lang']);
			$this->tpl->assign('CurentLangId', $this->CONFIG['lang']['id']);
			$this->lang = $this->CONFIG['lang']['name'];
			$this->log->syslog("Поддержка мультиязычности отключена. Установлен язык по умолчанию <b>{$this->lang}</b>.");
		} else {
			$this->log->syslog("Поддержка мультиязычности включена. Проверяем поддержку выбранного языка.");
			$sql = "SELECT id, name, descr, charset FROM `mcms_language` WHERE `rewrite` = (IF((SELECT COUNT(*) FROM `mcms_language` WHERE `rewrite`='".addslashes($this->lang)."')>0,'".addslashes($this->lang)."','ru'))";
			$this->db->query($sql);
			$this->db->get_rows(1);
			$this->CONFIG['lang'] = $this->db->rows;
			$this->tpl->assign('lang', $this->CONFIG['lang']);
			$this->tpl->assign('CurentLangId', $this->CONFIG['lang']['id']);
			$this->log->syslog("Установлен язык <b>{$this->lang}</b>.");
		}
			
		$this->langId = $this->CONFIG['lang']['id'];
	}
	
	/**
	 * Метод устанавливает тему
	 */
	private function setTheme() {
		
		$cached = (isset($this->CONFIG['perfomance']['cache_theme']) && $this->CONFIG['perfomance']['cache_theme'])? true : false;

		if($cached && isset($_SESSION['site_theme'])) {
			$this->theme = $_SESSION['site_theme'];
			$this->themePath = $_SESSION['site_theme_path'];
			$this->tpl->assign('theme', $this->theme);
			$this->tpl->assign('static', $_SESSION['site_theme_path']);				
			
			return $this->theme;
		} else {
			$this->log->syslog("В сессии не указана тема. Пробуем выбрать из базы тему.");
			
			$this->db->select()->from('mcms_themes')->fields('id', 'name', 'static_path')->where('id_site = '.$this->site_id)->limit(1);
			$this->db->execute();
			$this->db->get_rows(1);
			
			if(!is_array($this->db->rows) || !count($this->db->rows)) {
				$this->log->syslog("В базе не нашлось ни одной темы для этого сайта. Попробуем запуститься с темой default и без static_path");
				$_SESSION['site_theme'] = $this->theme = 'default';
				$_SESSION['site_theme_id'] = 0;
				$_SESSION['site_theme_path'] = '/';
			} else {
				$this->log->syslog("В базе нашлась тема. Установим ее.");
				$_SESSION['site_theme'] = $this->theme = $this->db->rows['name'];
				$_SESSION['site_theme_id'] = $this->db->rows['id'];
				$_SESSION['site_theme_path'] = $this->db->rows['static_path'];
			}
			
			
			$this->themePath = $_SESSION['site_theme_path'];
			$this->tpl->assign('theme', $this->theme);
			$this->tpl->assign('static', $_SESSION['site_theme_path']);		
		}
	}
	
	/**
	 * @deprecated
	 * Метод запускает механизм сессий
	 */
	private function startSession() {
		if(!empty($this->CONFIG['session']) && !empty($this->CONFIG['session']['store'])) {
			if($this->CONFIG['session']['store'] == 'memcache') {
				if(!$this->memcache) {
					$this->memcache = new Memcache;
					$this->memcache->connect($this->CONFIG['memchache']['server_ip'], $this->CONFIG['memchache']['server_port']);
				}
				
				if(!class_exists('sessions_memcache')) {
					$this->lib->loadWithoutCheck('sessions');
				}

				$sessionHandler = new sessions_memcache($this->memcache);
				session_set_save_handler(array($sessionHandler, "open"),
		            array($sessionHandler, "close"),
		            array($sessionHandler, "read"),
		            array($sessionHandler, "write"),
		            array($sessionHandler, "destroy"),
		            array($sessionHandler, "gc"));
			}

		}

		if(isset($_REQUEST['PHPSESSID'])) {
			session_id($_REQUEST['PHPSESSID']);
		}
		
		session_start();
	}
	
	/**
	 * Метод получает id текущего сайта
	 */
	private function get_site_options() {
		$domain = $_SERVER['SERVER_NAME'];

		$this->site_id = 0;
		$this->site_name = '';
		$this->site_type = 0;

		$cached = (isset($this->CONFIG['perfomance']['cache_sites']) && $this->CONFIG['perfomance']['cache_sites'])? false : false;
		
		if($cached && isset($_SESSION['cache:siteInfo'])) {
			$this->site_name = $_SESSION['cache:siteInfo'][0];
			$this->site_id = intval($_SESSION['cache:siteInfo'][1]);
			$this->siteObject = $_SESSION['cache:siteInfo'][2];
			$this->site_type = $this->siteObject['type'];
			$this->tpl->assign('curent_site', $this->siteObject);
			$this->log->syslog("Информация о сайте была закеширована. Берем информацию из кеша.");
			return true;
		}

	
		if(!empty($_SESSION['core:changeSite']) && $_SESSION['core:changeSite']) {
			$sql = "SELECT s.id, s.name, s.name as `describe`, s.server_name, s.type, s.custom_preloader FROM mcms_sites as s WHERE s.id = 1";
		} else {
			$sql = "SELECT
						s.id, s.name, s.name as `describe`, s.server_name, s.type, s.custom_preloader
					FROM
						mcms_sites as s
					WHERE
						(s.server_name = '{$_SERVER['SERVER_NAME']}' AND s.server_port = '{$_SERVER['SERVER_PORT']}') OR
					EXISTS(
						SELECT 
							sa.id 
						FROM 
							mcms_sites_alias as sa 
						WHERE 
							sa.id_site = s.id AND 
							sa.server_name = '{$_SERVER['SERVER_NAME']}' AND 
							sa.server_port = '{$_SERVER['SERVER_PORT']}'
					)";
		}

		$this->db->query($sql);
		$this->db->get_rows(1);

		if(!$this->site_id && isset($this->db->rows['id']) && $this->db->rows['id'] > 5) {
			$this->site_name = $this->db->rows['server_name'];
		} else {
			$this->site_name = (isset($this->db->rows['name']))? $this->db->rows['name'] :false;
		}
		
		
		$this->site_id = (isset($this->db->rows['id']))? $this->db->rows['id']: false;
		$this->tpl->assign('curent_site', $this->db->rows);
		
		if(!$this->site_id) {
			self::fatal_error('CORE Error!', 'За данным доменом не закреплен ни один сайт.</p><p>Для связи с администратором используйте почту <a href="mailto:'.$_SERVER['SERVER_ADMIN'].'">'.$_SERVER['SERVER_ADMIN'].'</a></p></hr><p><b>domain</b>: '.$_SERVER['SERVER_NAME']);
		}
		
		$this->site_type = $this->db->rows['type'];
		$this->siteObject = $this->db->rows;
		
		if($cached) {
			$_SESSION['cache:siteInfo'] = array();
			$_SESSION['cache:siteInfo'][] = $this->site_name;
			$_SESSION['cache:siteInfo'][] = $this->site_id;
			$_SESSION['cache:siteInfo'][] = $this->siteObject;
		}

	}
	
	private function useAuth() {
		if(!isset($this->CONFIG['auth'])) return false;
		
		if(is_string($this->CONFIG['auth'])) {
			return preg_match("/{$this->CONFIG['auth']}/", $_SERVER['SERVER_NAME']);
		}
		
		if(is_int($this->CONFIG['auth'])) {
			return $this->CONFIG['auth'] == $this->site_id;
		}
		
		if(is_array($this->CONFIG['auth'])) {
			foreach($this->CONFIG['auth'] as $item) {
				if(is_string($item) && preg_match("/{$item}/", $_SERVER['SERVER_NAME'])) return true;
				if(is_int($item) && $item == $this->site_id) return true;
			}
			
			return false;
		}
	}
	
	public function __call($func_name, $params) {
		if($func_name == 'error_page') {
			if(!class_exists('loader') || !($this->loader instanceof loader)) {
				$this->lib->load('loader');
			}

			loader::page($params[0]);
			return true;
		}
	}
	
	public static function debug($useHtml = true) {
		echo '<html><head><meta http-equiv="content-type" content="text/html; charset=utf-8" /></head><body>';
		echo self::$instance->console(false);
		echo '</body></html>';
		die();
	}
	
	public static function fatal_error($title, $message) {
		@header('Content-Type: text/html; charset=utf-8');
		echo '<html><head><meta http-equiv="content-type" content="text/html; charset=utf-8" /><h1>'.$title.'</h1><p>'.$message.'</p>';
		
		if(IS_DEV) {
			echo self::$instance->console(false);
		}
		
		echo '</body></html>';
		die();
	}
}



?>
