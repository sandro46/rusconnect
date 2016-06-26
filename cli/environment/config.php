<?php
	################################################################################
	#                             MAIN CONFIG FILE                                 #
	################################################################################

	define('CORE_PATH', dirname(__FILE__).'/');
	define('TIME_START', microtime());
	define('OPTIMIZE', false);
	define('IS_DEV', ((!empty($_COOKIE['isdev']) && $_COOKIE['isdev'])? true : false));
	
	
	/*if($_SERVER['REMOTE_ADDR'] == '193.201.89.179)*/ define('MAINTENANCE', false); /*else define('MAINTENANCE', true);*/

	error_reporting(E_ALL); // E_ERROR
	ini_set('display_errors',1);
	
	date_default_timezone_set('Europe/Moscow');
	@ini_set('mbstring.internal_encoding', 'UTF-8');
	
	
	if(!empty($_SERVER['HTTP_HOST'])) {
	    $_SERVER['SERVER_NAME'] = $_SERVER['HTTP_HOST'];
	}
	

    # Каталоги
    $config['local_path'] = CORE_PATH;
    $config['core_path'] = CORE_PATH.'core/';
    $config['var_path'] = CORE_PATH.'vars/';
    $config['lib_path'] = CORE_PATH.'lib/';
    $config['utils_path'] = CORE_PATH.'lib/';
    $config['module_path'] = CORE_PATH.'modules/';
    $config['widgets_path'] = CORE_PATH.'widgets/';
    $config['files_path'] = CORE_PATH.'vars/files/';
    $config['temp_path'] = CORE_PATH.'vars/temp/';
    $config['cache_path'] = CORE_PATH.'vars/cache/';
    $config['tpl_cache_path'] = CORE_PATH.'vars/tpls_compil/';

    $config['imgPath'] = $config['files_path'].'images/';
    $config['img_local_path'] = '/vars/files/images/';

    define('CORE_CLASS_PATH', $config['core_path']);

    # Шаблонизатор
    $config['templates']['compil_dir']   = $config['tpl_cache_path']; // путь к каталогу для хранения скомпилированых шаблонов
    $config['templates']['bd_src_table'] = 'mcms_tmpl'; // название таблицы с шаблонами
    $config['templates']['file_ext']     = '.inc'; // расширение скомпилированых файлов
    $config['templates']['check_level']  = 2; // сложность проверки актуальности скомпилированых шаблонов (1-проверка только на наличие, 2 - проверка на наличие и на наличие изменений по md5, 3 - принудительная перекомпиляция)
    $config['templates']['debug']['varsWarning'] = 0; // выводить отладочную информацию шаблониатора в коментариях к html
    $config['templates']['syntax_sugar'] = true; // Использовать синтаксический сахар
    $config['templates']['min'] = false;
    
    
    # Memcache
    $config['memchache']['server_ip']      = '127.0.0.1';
    $config['memchache']['server_port']    = '11211';
    $config['memchache']['run']            = 0;

    # Файловое кширование
    $config['cache']['path'] = $config['cache_path']; // путь к файлам кеша
    $config['cache']['enable'] = 0; // для каких сайтов использовать (false - отключено)

    # Логирование
    $config['db_logs']['sql_error'] = 0; // использовать логирование sql запросов в базу данных (будут логироваться запросы выполненые с ошибками)
    $config['db_logs']['php_error'] = 0; // использовать логирование ошибок php в базу
    $config['db_logs']['sql_data'] = 0; // использовать логирование sql запросов в базу данных (будут логироваться запросы на удалине, изменение, вставку)
    $config['db_logs']['access'] = 0; // использовать логирование действий пользователя

    # Основная база данных (дополнителные баззы указываются с другим ключем, например: db_cars)
   $config['db_site']['dbname']      = 'rusconnect';
    $config['db_site']['dbhostname']  = 'localhost';
    $config['db_site']['dbusername']  = 'rusconnect';
    $config['db_site']['dbpass']      = 'xZ2KsBYPV9UE7NZK';

    # Отладочная консоль
    $config['console']['syslog'] = true;
    $config['console']['sites'] = array(1,2); //array(1,2,3); // для каких сайтов включать логирование
    $config['console']['level'] = 6; // уровень логирования (от 1 до 7)
    $config['console']['db']['call_trace'] = 1; // использовать трассировку вызова в консоле отладки в разделе базы данных
    $config['console']['db']['query_time'] = 1; // измерять время выполнения каждого запроса

    # Управляеммые сайты (если есть эта настройка, то приоритетным сопоставлением будет этот массив, затем база данных)
   // $config['sites'][1]['admin.mcms.loc'] = array('admin.m-cms.org');

    # Безопасность
    $config['security']['session']['protect']	= 1; // режим защиты сессии от кражи (для эффективной работы, необходимо установить соль для сессий)
    $config['security']['session']['salt'] = 'MNcdjWJ23jNfMIwkm'; // соль для безопасных сессий
    $config['security']['user']['pass_salt'] = 'g9Fnw3x6b$43-c,m~jifw745'; // соль для паролей пользователей
    $config['security']['ajax_csrf'] = false;
    
    # Язык по умолчанию
    $config['lang']['default']['id'] = 1; // id языка по умолчанию
    $config['lang']['default']['name'] = 'ru'; // сокращенное названия языка по умолчанию
    $config['lang']['multi'] = false; // использовать мультиязычность?

    # Настройка утилит
    $config['utils']['enable'] = 1; // разрешено использовать утилиты
    $config['utils']['list'] = array('login','exit','captcha'); // какие утилиты доступны

    # Настройка интерфейса администратора
    $config['ui']['templates']['codelight'] = 1; // использовать подсветку синтаксиса в редакторе шаблонов
    $config['ui']['adminTheme'] = 'green'; // тема по умолчанию для панели администрирования
    $config['ui']['default_editor'] = 1; // текстовый редактор по умолчанию 1 - CKeditor 2 - mcEditor  0 - off

    # Настройка системной части панели управления
    $config['admin']['moduleName'] = 'AdminPanel'; // название модуля для шаблонов административной части
    $config['admin']['change_site_with_root_login'] = false; // хитрая опция меняет id сайта на сайт админки если пользователь авторизовался на любом сайте с user_id = 1
    
    # Настройка рерайтов
    $config['rewrites']['UseUserRewrite'] = true; // использовать пользовательские реврайты (которые в базе), если стоит false то не использовать
    $config['rewrites']['ExtendedRewrite'] = false; //  использовать расширенные реврайты (которые в файле в виде регулярок), если стоит false то не использовать

    # Производительность
    $config['perfomance']['cache_rules'] = 0; // кэшировать права доступа 1 - в сессии 2 - в memcache 3 - в базе,  false - не кеширвоать
    $config['perfomance']['cache_user_info'] = 0; // кэшировать информацию о пользователе 1 - в сессии, false - не кеширвоать
    $config['perfomance']['cache_modules'] = 0;  // кэшировать информацию о модулях 1 - в сессии, false - не кеширвоать
    $config['perfomance']['cache_sites'] = 1;  // кэшировать информацию о сайтах 1 - в сессии, false - не кеширвоать
    $config['perfomance']['cache_theme'] = 1;  // кэшировать информацию о темах 1 - в сессии, false - не кеширвоать     
    
    # Сессия
    $config['session']['store'] = 'memcache'; // file, memcache, mysql
    $config['session']['lifetime'] = 14400;
    
    
    # Дя каких сайтов запретить доступ без авторизации
    # По умолчанию для сайта с id = 1 установлена обязательная авторизация.
    # Если необходимо использовать в других сайтах обязательную авторизацию то:
    # - можно указать домен полностью
    # - можно указать регулярное выражение для подстановки домена
    # - можно указать id сайта
    $config['auth'] = array(1,2);

    /**
     * Переопределение настроек для различных вариантов оптимизации.
     */

    if(OPTIMIZE === false) {
	// не использовать оптимизацию.
    } elseif(OPTIMIZE == 3) { // максимальный уровень
    	$config['templates']['debug']['varsWarning'] = 0;
    	$config['templates']['check_level']  = 1;
    	$config['db_logs']['sql_error'] = 0;
	    $config['db_logs']['php_error'] = 0;
	    $config['db_logs']['sql_data'] = 0;
	    $config['db_logs']['access'] = 0;
    	$config['console']['syslog'] = false;
	    $config['console']['db']['call_trace'] = false;
	    $config['console']['db']['query_time'] = false;
	    $config['console']['level'] = 2;
    	$config['perfomance']['cache_rules'] = 1;
    	$config['perfomance']['cache_user_info'] = 1;
   	 	$config['perfomance']['cache_modules'] = 1;
    	$config['perfomance']['cache_sites'] = 1;
    	$config['perfomance']['cache_theme'] = 1;
    } elseif(OPTIMIZE == 2) {
    	$config['templates']['debug']['varsWarning'] = 0;
    	$config['templates']['check_level']  = 2;
    	$config['db_logs']['sql_error'] = 0;
	    $config['db_logs']['php_error'] = 0;
	    $config['db_logs']['sql_data'] = 0;
	    $config['db_logs']['access'] = 0;
    	$config['console']['syslog'] = false;
	    $config['console']['db']['call_trace'] = false;
	    $config['console']['db']['query_time'] = false;
	    $config['console']['level'] = 6;
    	$config['perfomance']['cache_rules'] = 1;
    	$config['perfomance']['cache_user_info'] = 1;
   	 	$config['perfomance']['cache_modules'] = 1;
    	$config['perfomance']['cache_sites'] = 1;
    	$config['perfomance']['cache_theme'] = 1;
    } elseif(OPTIMIZE == 1) {
    	$config['templates']['debug']['varsWarning'] = 1;
    	$config['templates']['check_level']  = 2;
    	$config['db_logs']['sql_error'] = 0;
	    $config['db_logs']['php_error'] = 0;
	    $config['db_logs']['sql_data'] = 0;
	    $config['db_logs']['access'] = 0;
    	$config['console']['syslog'] = true;
	    $config['console']['db']['call_trace'] = false;
	    $config['console']['db']['query_time'] = false;
	    $config['console']['level'] = 6;
    	$config['perfomance']['cache_rules'] = 1;
    	$config['perfomance']['cache_user_info'] = 1;
   	 	$config['perfomance']['cache_modules'] = 0;
    	$config['perfomance']['cache_sites'] = 0;
    	$config['perfomance']['cache_theme'] = 0;
    } elseif(OPTIMIZE === 0) {
    	$config['templates']['debug']['varsWarning'] = 1;
    	$config['templates']['check_level']  = 2;
    	$config['db_logs']['sql_error'] = 0;
	    $config['db_logs']['php_error'] = 0;
	    $config['db_logs']['sql_data'] = 0;
	    $config['db_logs']['access'] = 0;
    	$config['console']['syslog'] = true;
	    $config['console']['db']['call_trace'] = true;
	    $config['console']['db']['query_time'] = true;
	    $config['console']['level'] = 6;
    	$config['perfomance']['cache_rules'] = 0;
    	$config['perfomance']['cache_user_info'] = 0;
   	 	$config['perfomance']['cache_modules'] = 0;
    	$config['perfomance']['cache_sites'] = 0;
    	$config['perfomance']['cache_theme'] = 0;
    } 



?>
