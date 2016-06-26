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
 * Класс шаблонизатора. Прототип smarty. Входит в состав ядра.
 */

class templates
 {

	/**
	 * Путь к каталогу где будут хранится откомпилированые шаблоны
	 */
	public $compil_dir = "";

	/**
	 * Префикс для обозначения переменных
	 */
	public $var_start_symbol = '$';

	/**
	 * Тег с которого будут начинатся все переменные и функции шаблонизатора
	 */
	public $tag_open = '{';

	/**
	 * Закрывающий тег для функций и переменных шаблонизатора
	 */
	public $tag_close = '}';

	/**
	 * С каким расширением будут сохранятся компилированые шаблоны
	 */
	public $compil_file_ext = '.php';

	/**
	 * Название таблици в которой хранятся шаблоны
	 */
	public $db_table_tpl = 'mcms_tmpl';

	/**
	 * Тип проверки на актуальность откомпилированой версии шаблона
	 *
	 * 1 - простая првоерка: првоерка на существование какой либо версии откомпилированого шаблона
	 * 2 - сложная првоерка: првоерка на существование откомпилированого шаблона и проверка его версии на основе md5 шаблона в базе и откомпилированой версии
	 *
	 * @var int
	 */
	public $compil_chek_level = 2;

	/**
	 * Массив для хранения переменных которые будут видны из шаблона
	 */
	public $vars = array();

	 /**
	  * Временная переменная для хранения информации получней вызыванием функции ob_start(templates_buffering_callback)
	  * Эта херня нужна для корректного кеширования внутри шаблона при помощи memcache
	  * -> function parse_tpl секция {memcache name=value expire=value}{/memcache}
	  */
	public $tmp_buffer_obj = '';
	
	/**
	 * переменная для првоерки режима отладки парсера
	 *
	 * @var bolean
	 */
	public $debugParser = 0;

	private $cur_update_table = 0;

	// - Служебные переменные парсера и вьювера


	public $src = "";
	public $out = "";
	public $tplname = "";
	public $md5_compil = "";
	public $foreaches = array();
	public $foreaches_this_name = "";
	public $foreaches_this_item = "";
	public $declared_functions = array();
	public $htmlminimizer = false;
	public $useSyntaxSugar = false;


	private $cached_tpls = array();
	private $datatype = array();
	private $trigers = array();
	private $filename = "";


	function __construct() {
		
	}


	/**
	 * Метод добавляет тригер
	 *
	 * $triger_name - название тригера для шаблонизатора
	 * $value1		- первый элемент тригера
	 * $value2 		- второй элемент тригера
	 */
	public function add_triger($triger_name, $values)
	{
		$this->trigers[$triger_name] = $values;
	}

	/**
	 * Метод добавляет данные для публичного доступа из шаблона
	 * первый параметр - название переменной через которую быдет производится доступ из шаблона
	 * второй параметр - сами данные которые передаем переменной
	 */
	public function assign($name, $value)
	{
		$this->vars[$name] = (is_object($value))? get_object_vars($value) : $value;
	}

	/**
	 * Функция выводит шаблон непосредственно в стандартный вывод
	 * Принимает два параметра
	 * первый - $tpl_name - название шаблона
	 * второй - необязательный параметр. если вторйо параметр передан и передан 0 то будет выполнятся проверка версии шаблона и будет выполнена его перекомпиляция если шаблон обновился
	 */
	public function display($tpl_name, $compil_chek = 1)
	{
		global $core;
		$this->src = $this->out = $this->md5_compil = $this->compil = $sourceFromFile = false;
		
		if(strstr($tpl_name, 'file:') !== false) {
			$tpl_name = substr($tpl_name, 5);
			$sourceFromFile = true;
		}
		
		$this->tplname = $tpl_name;
		$this->setFilename($core->module_name, $this->tplname, $core->site_name);
				
		//print_r($this);
		
		if($compil_chek) {
			switch($this->compil_chek_level) {
				case 1:
					if(file_exists($this->compil_dir.$this->filename)){
						$core->log->add_tpl_fetch($core->module_name.'/'.$this->tplname);
					} else {
						$this->compil($tpl_name,false,false,$sourceFromFile);
					}
				break;

				case 2:						
					$this->check_compil(false,$sourceFromFile);

					if($this->compil) {
						$this->compil($tpl_name,false,false,$sourceFromFile);
					} else {
						$core->log->add_tpl_fetch($core->module_name.'/'.$this->tplname);
					}

				break;
				
				case 3:
					if(file_exists($this->compil_dir.$this->filename)) {
						if($this->compil_expire($this->compil_dir.$this->filename)) $this->compil($tpl_name);	
					} else {
						if($this->compil) {
							$this->compil($tpl_name,false,false,$sourceFromFile);
						} else {
									$core->log->add_tpl_fetch($core->module_name.'/'.$this->tplname);
						}
					}	
				break;
					
			}
		}
		
		$this->render();
	}

	/**
	 * Метод возвращает чистый html со всеми вставками (результат выполнения компилированого шаблона)
	 *
	 * В качестве параметра берет название шаблона $tpl_name и вторйо необязательный параметр $chek
	 * Если второй параметр установлен и равен 0 то проверка шаблона будет выполнена и вследствии приведет к компиляции шаблона
	 * По умолчанию проверка не выполняется
	 */
	public function fetch($tpl_name, $chek=1, $mcache=0, $cache_expire = 0, $name_module = '') {
		global $core;
		// класс общественный и используеммый только через один обьект, поэтому обнуляем все общественные переменные :)
		$this->src = $this->out = $this->md5_compil = $this->compil = false;
	
		$name_module = ($name_module)? $name_module : $core->module_name;
	
		// пишем в логер что шаблончик вызвался и вызваля из кэша
		$core->log->add_tpl_fetch($name_module.'/'.$tpl_name);
	
		$sourceFromFile = false;
			
		if(strstr($tpl_name, 'file:') !== false) {
			$tpl_name = substr($tpl_name, 5);
			$sourceFromFile = true;
		}
			
		$this->tplname = $tpl_name;
		$this->setFilename($name_module, $this->tplname, $core->site_name);
		
		if($chek) {
			switch($this->compil_chek_level) {
				case 1:
					if(!file_exists($this->compil_dir.$this->filename)) {
						$this->compil($tpl_name, $name_module,false,$sourceFromFile);
					}
				break;
				case 2:
					$this->check_compil($name_module,$sourceFromFile);
					if($this->compil) {
						$this->compil($tpl_name, $name_module,false,$sourceFromFile);
					}
				break;
				case 3:
					if(file_exists($this->compil_dir.$this->filename)) {
						if($this->compil_expire($this->compil_dir.$this->filename)) $this->compil($tpl_name, $name_module);	
					} else {
						$this->compil($tpl_name, $name_module,false,$sourceFromFile);	
					}	
				break;
			}
		}

		// если передан аргумент указывающий на кэширование - то обробатываем
		if($mcache && is_array($mcache)) {
			// генерируем ключик к кешируеммому шаблону
			$varname_md5 = $this->get_cache_var_name($this->tplname, $mcache);

			// если шаблон ранее запрашивался из кэша то выводим его из временного массива
			if(isset($this->cached_tpls[$varname_md5])) {
				return $this->cached_tpls[$varname_md5];
			}

			// смторим есть ли в кеше данные по сгенерированому ключику
			$tpl_html = $core->memcache->get($varname_md5);

			// если по ключику не пришел шаблон из кэша то рендерим шаблон выводим его и пишем в кэш
			if($tpl_html === false) {
				$tpl_html = $this->exec_tpl($this->filename);
				$core->memcache->set($varname_md5, $tpl_html, 0, $cache_expire);
				return $tpl_html;
			} else {
				// если по ключу был найден шаблон в кэше то выводим его а в лог пишем что читаем из кэша
				$core->log->add_cache_tpl($name_module.'/'.$this->tplname, $varname_md5, $cache_expire);
				return $tpl_html;
			}
		} else {
			// если кэширование не включено - просто рендерим и выводим шаблон
			return $this->exec_tpl($this->filename);
		}
	}

	/**
	 * Альтернативный вызов метода fetch с минимальным набором параметров
	 */
	public function get($tpl_name, $name_module = '', $extended = false) {
		
		if($extended && is_array($extended)) {
			$this->vars = array_merge($this->vars, $extended);
		}
		
		return $this->fetch($tpl_name, 1, 0, 0, $name_module);
	}
	
	/**
	 * Метод компилирует шаблон переданый в качестве параметра $tpl_name
	 * Метод не првоеряет наличие компилированой версии и не провиряет версию шаблона
	 * после компиляции результат записывается в файл
	 */
	public function compil($tpl_name, $name_module = 0, $site_name=0,$sourceFromFile=false) {
		global $core;
		
		$this->tplname = $tpl_name;
		$site_name = ($site_name)? $site_name : $core->site_name;
		$name_module = ($name_module) ? $name_module : $core->module_name;

		$this->setFilename($name_module, $this->tplname, $site_name);

		if(!$this->src)	$this->get_source_tpl($name_module,false,false,$sourceFromFile);

		$this->parse_tpl();
		$this->write_file();
		return true;
	}

	/**
	 * Метод парсит шаблон
	 * В качестве параметра принимает сорц шаблона $this->src и списко асигнованых переменных $this->vars
	 *
	 * -----------------------------------------------------------------------------------------------------------------------------------------------------------------------------
	 * Доступные теги для парсинга:
	 *
	 * {$varname} 										  - выводит ассигнованую переменную "varname"
	 *			  										  - Если нужно вывести элемент массива, то используется конструкция {%varname.index}
	 *													  - вложеность массива неограничена
	 *
	 * {if $varname условие параметр}...{else}...{if}     - эквивалент конструкции if.... else .... в php
	 * 												      - $varname - любая асигнованая переменная
	 *  											      - условие  - стандартные логические операции php (!= / == / === / < / > / ! / <> / =< / >=)
	 *												      - параметр - проверяемая строка либо асигнованая переменная (его наличие необязательно)
	 *												      - конструкция можит быт ьиспользована как в связке с {else} так и без нее
	 *												      - конструкция можит вкладыватся сама в себя на любой уровень и в другие конструкции тегов
	 *
	 * {foreach from=$varname as=keyname} ... {/foreach}  - эквивалент конструкции foreach(from as k=>val)
	 *													  - $varname - любой ассигнованый массив (вложенность массива не имеет значения)
	 *													  - keyname  - сюбая строка которая будет интерпритироватся как индекс маасива
	 *													    через эту переменную будет осуществлятся доступ к текущим значениям пассива
	 *													  - для вставки текущего элемента массива в html используется конструкция {$keyname}
	 *													    если нужно обращатся к текущему элементу как к массиву, то используется конструкция {$keyname.subkeyname}
	 *														вложеность такой конструкции неограничена
	 *													  - индекс текущего элемента массива содержится в переменной {$foreach.[arrayname].key}
	 *														где [arrayname] - название массива которое формируется из название передаваемой переменной при обьявлении конструкции
	 *														!!!arrayname вводится без символа $ !!!!
	 *
	 * {add $varname=param}								  - добавляет внутреннюю переменную в класс для последующего вызова из шаблона
	 *													  - $varname - название переменной
	 *													  - param - значение
	 *
	 * {perm value} ... {/perm}							  - Конструкция проверяет права пользователя на доступ к action, id которого указывается в качестве параметра (value)
	 * 													  - Если проверка прошла успешно то выполняется код который заключен в блок
	 *
	 * {memcache name=value expire=value} ... {/memcache} - Конструкция вызывает кэширование прямо из шаблона части кода. Может кэшировать как обычный html так и куски кода с функциями шаблонизатора
	 *													  - name=value - название блока кэширования (обычная строка без символов обозначающих переменную) например name=cached_block
	 * 													  - expire=value - время жизни кэша (число типа integer бозначающее секунды)
	 * -----------------------------------------------------------------------------------------------------------------------------------------------------------------------------
	 */
	private function parse_tpl() {
		$md5 = md5($this->src);
		
		// парсим шаблон
		$parser = @new template_parser($this);
		$parser->run();
		$this->src = $parser->source;
	
		// добавление md5
		$this->src = '<? $this->md5_compil=\''.$md5.'\'; ?>'."\n".$this->src;
	
		return true;
	}

	/**
	 * Метод чистит значение переменной переданой в шаблон
	 */
	public function _unset($var_name) {
		unset($this->vars[$var_name]);
	}

	
	public function getModulenameByTemplateId($idTemplate)
	{
		global $core;
		
		$core->db->select()->from('mcms_tmpl')->fields('name_module')->where('id_template = '.intval($idTemplate))->limit(1);
		$core->db->execute('db_site');
		
		return $core->db->get_field();
	}

	public function init() {
		if($this->useSyntaxSugar) {
			$this->syntaxModifier();
		}
	}

	############################### Методы для работы с шаблонами ##################################

	/**
	 * Метод создает файл и записывает туда откомпилированый сорц
	 * Если файл был создан ранее, он перезаписывается
	 * В качестве параметра берет название файла $this->filename и сорц $this->src
	 */
	private function write_file()
	{
		global $core;
	
		$core->log->add_tpl_compil($this->filename);
	
		if(!file_exists($this->compil_dir)) {
			$this->compil_dir = substr($this->compil_dir, 0, strlen($this->compil_dir)-1);
			mkdir($this->compil_dir, 0775);
			$this->compil_dir .= '/';
		}
	
		$fp = fopen($this->compil_dir.$this->filename,'w+');
		fwrite($fp, $this->src);
		fclose($fp);
	}

	/**
	 * Метод проверяет версию шаблона в базе и сверяет с версией откомпилированого шаблона
	 * Метод в качестве параметра принимает название откомпилированого файла из $this->filename и хэш сорца полученый методом md5()
	 *
	 * Проверка производится на основе md5 отпечатка исходного кода шаблона
	 * При создании файла с откомпилированым шаблоном, в его первые 60 байт записывается хэш исходника до компиляции.
	 * Этот хэш сверяется с хэшем проверяемого шаблона
	 *
	 * Метод возвращает false если шаблон обновился либо если шаблон не откомпилирован вобще в противном случае возвращает true
	 * Также результат проверки копируется в $this->compil / если шаблон обновился то $this->compil = 1 если нет то $this->compil = 0
	 */
	private function check_compil($name_module = false,$source_from_file=false)
	{
		$this->compil = 1;
		
		if(is_file($this->compil_dir.$this->filename)) {
			$fp = fopen($this->compil_dir.$this->filename, 'r'); // читаем файл шаблона
			$md5 = fgets($fp, 100); // в первых 60 байтах содержится md5 хэш компилированого шаблона. читаем его
			$md5 = str_replace(array('<? $this->md5_compil=', '; ?>', "'"), '',$md5 ); // реплейсим md5
			$md5 = trim($md5);
			$md5 = str_replace('\\', '', $md5);
			
			$this->get_source_tpl($name_module,false,false,$source_from_file);
			$this->compil = ($md5 != md5($this->src))? 1 : 0;
		}
		
		return ($this->compil)? false : true;
	}
	

	/**
	 * Метод обращается к базе и вытаскивает исходный код шаблона
	 * В качестве параметра берет $this->tplname
	 * Результат записывает в переменную $this->src
	 */
	public function get_source_tpl($name_module = '', $not_parse = false, $tpl_name = false,$sourceFromFile=false)
	{
		global $core;
		
		$src = '';
		
		$tpl_name = ($tpl_name)? $tpl_name : $this->tplname;
		$name_module = ($name_module)? $name_module : $core->module_name;
		$sourse_file_path=CORE_PATH."modules/".$name_module."/templates/".$tpl_name;
		if(file_exists($sourse_file_path)) {
			$src=file_get_contents($sourse_file_path);
		} else {
			$sql = "SELECT source FROM `{$this->db_table_tpl}` USE INDEX (main_find_index)  WHERE `id_site` = {$core->site_id} AND `id_lang` = {$core->langId} AND `theme` = '{$core->theme}' AND `name_module` = '{$name_module}' AND `name` = '{$tpl_name}' AND `del` = 0 ";
			$core->db->query($sql, 'db_site');
			$core->db->get_rows(1);
			
			if(!$core->db->rows) {
				$this->src = '';
				$core->log->syslog('Вызванный шаблон не найден в базе. sql: '.$sql);
				return false;
			}
			
			$src=$core->db->rows['source'];
		}
		
		if($not_parse) {
			$src = str_replace('\"', '"', $src);
			$src = str_replace("\'", "'", $src);
			return $src;
		}
		
		$this->src = stripslashes($src);
		$this->src = str_replace('\"', '"', $this->src);
		$this->src = str_replace("\'", "'", $this->src);
	}

	/**
	 * Метод выполняет отображение шаблона
	 * Вызывается из метода display
	 * Название файла шаблона берет из внутренней переменной $this->filename
	 */
	private function render()
	{
		global $core;


		if($this->htmlminimizer)
		{
			echo $this->minmaizer($this->exec_tpl($this->filename));
		}
		else
			{
				include $this->compil_dir.$this->filename;
			}
	}

	/**
	 * Метод прогоняет через парсер переданый исходный код шаблона.
	 */
	public function parseSource($source, $plugins = false) {
	    $parser = @new template_parser($this, $source);
	    
	    if($plugins) {
	        $parser->plugins = $plugins;
	    }
	    
	    
	    $parser->run();
	    return $parser->source;
	} 
	
	/**
	 * Метод прогоняет через парсер переданый исходный код шаблона, записывает его во временный кэш и выполняет.
	 */
	public function runSource($source, $plugins = false, $unlinkAfterCompil = true) {
	    $tpl = $this->parseSource($source, $plugins);
	    if(!strlen($tpl)) return $tpl;
	    $filename = '_tmp_compl_source_'.time().microtime().'.tpl';
	    $path = $this->compil_dir.'/'.$filename;
	    if(!file_exists($this->compil_dir)) {
	        mkdir($this->compil_dir, 0775);
	    }
	    $fp = fopen($path,'w+');
	    fwrite($fp, $tpl);
	    fclose($fp);
	    
	    global $core;
	    ob_start();
	    include $path;
	    $ret = ob_get_clean();
	    //ob_clean();
	    if($this->htmlminimizer) $ret = $this->minmaizer($ret);
	    
	    if($unlinkAfterCompil) {
	        unlink($path);
	    }
	    return $ret;
	}
	
	/**
	 * Метод принимает в качестве параметра название шаблона в базе и возвращает откомпилированый html после обработки php
	 * Используется в методе fetch этого класа
	 */
	private function exec_tpl($filename)
	{
		global $core;
		ob_start();
		include $this->compil_dir.$filename;
		$ret = ob_get_clean();
		//ob_clean();
		if($this->htmlminimizer) $ret = $this->minmaizer($ret);
		return $ret;
	}

	/**
	 * Метод генерирует md5 ключик для работы с кэшем на основании переданых аргументов
	 *
	 * В качестве параметров принимает название шаблона и массив с дополнительными парами 'параметр' => 'значение'
	 *
	 * Среди дополнительных параметров есть и некоторые предоприделенные параметры:
	 *
	 *		[_site]		- название сайт из config.php
	 *		[_date]		- метка времени | если значение установлено и больше 1 то принимается установленное значение в другом случае проставляется текущая метка времени
	 *		[_user_id]	- id пользователя | из $core->user->id
	 *		[_lang]		- название языка из $core->CONFIG['lang']['name']
	 *		[_url]		- текущий урл (преобразованый а не реальный rewrite) с учетом GET запроса
	 *
	 *
	 * Чтобы использовать предустановленые переменные, небходимо передать их в массиве вторым аргументом, где в массиве ключами будут - название параметров а значения равны 1. (кроме параметра _date)
	 *
	 * Например чтобы сгенерировать ключ с учетом url`а, языка и названия сайта нужно передать следующий массив параметров:
	 *
	 *	array('_url'=>1, '_site'=>1, '_lang'=>1)
	 *
	 * Если необходимо добавить дополнительный параметр который не входит в список предустановленных, то массив будет выглядить следующим образом:
	 *
	 *	array('_url'=>1, '_site'=>1, '_lang'=>1, 'customm_param'=>'value');
	 *
	 * Название ключа массива 'customm_param' может быть любым не совпадающим с именем ни одного из предустановленых параметров.
	 */
	public function get_cache_var_name($tpl_name, $params)
	{
		global $core;
	
		$str = '';
	
		reset($params);
		asort($params);
	
		foreach($params as $k=>$v)
			{
			switch($k)
				{
				case '_site':
					$str  .= $core->site_name;
				break;
	
				case '_editor':
					$str  .= $core->edit_site;
				break;
				
				case '_date':
					if($v != 1)
						{
						$str  .= $v;
						}
						else
							{
							$str  .= time();
							}
	
				break;
	
				case '_user_id':
					$str  .= $core->user->id;
				break;
	
				case '_lang':
					$str  .=  $core->CONFIG['lang']['name'];
				break;
	
				case '_url':
					$str  .= $_SERVER['REQUEST_URI'];
				break;
	
				default:
					$str  .= $v;
				break;
				}
			}
		$str .= $tpl_name;
	
		return md5($str);
	}

	/**
	 * Метод работает аналогично методу get_cache_var_name, только принимает в качестве параметра многомерный ассоциативный массив $param['tpl'], $param['cach_param']
	 */
	public function get_cache_var_name_from_arr($param)
	{
		$md5 = $this->get_cache_var_name($param['tpl'], $param['cach_param']);
	
		return $md5;
	}

	/**
	 * Метод проверяет существование кэша в memcache и если шаблон был закэширован, возвращает true и записывает кэшированый html в массив $this->cached_tpls c ключем равным его md5, если шаблон не был кэширован - возвращает false
	 */
	public function get_cache_if_exits($param_array)
	{
		global $core;
		$param_array['md5'] = $this->get_cache_var_name_from_arr($param_array);
		$tpl_html = $core->memcache->get($param_array['md5']);
	
		if($tpl_html === false)
			{
			return false;
			}
			else
				{
				$this->cached_tpls[$tpl_html] = $tpl_html;
				return true;
				}
	}


	/**
	 * Метод выводит список всех тем на основе шаблонов в базе данных
	 *
	 * @return assoc. array
	 */
	public function getThemsList()
	{
		global $core;
		
		$sql = 'SELECT DISTINCT (`theme`) FROM `mcms_tmpl` WHERE 1 ORDER BY `theme`';
		$core->db->query($sql);
		$core->db->get_rows();
		
		$thems = array();
		
		foreach($core->db->rows as $item) $thems[] = $item['theme'];
		
		return $thems;
	}
	
	public function getThemeByTplId($id_template)
	{
		global $core;
		
		$sql = "SELECT DISTINCT(`theme`) FROM mcms_tmpl WHERE id_template = {$id_template}";
		$core->db->query($sql);
		
		return $core->db->get_field();
	}

	
	// FIXME: !!!!!
	private function setFilename($module, $tplname, $site)
	{
		global $core;
		
		$theme = ($core->theme)? $core->theme : 'default';
		$module = ($module)? $module : 'no_module';
		$site = ($site)? $site : ($core->site_name)? $core->site_name : 'no_site';
		$lang = $core->langId;
		$fname = $lang.'.'.$tplname.$this->compil_file_ext;
		
		$pathArray = array($site, $theme, $module);
		
		$path  = implode('/',$pathArray).'/';
		$fullPath = $this->compil_dir.$path;
				
		if(!file_exists($fullPath))
		{
			@mkdir($fullPath, 0775, true) or die('<b style="color:red; font-size:30px;">CORE ERROR!</b> - Dont created folders for the cache templates. Maby bad rules? <b>[ $ sudo chmod -R 0775 /var/www/mysyte.com/vars/tpls_compil/ ]</b>');	
		}
		
		$filename = $path.$fname;
				
		$this->filename = $filename;
		
		return array('path'=>$path, 'fullPath'=>$fullPath, 'fname'=>$fname, 'filename'=>$filename);
	}
	
	private function minmaizer($html)
	{
		$html =  (string)str_replace(array("\r", "\r\n", "\n", "   "), '',  $html);
		$html =	preg_replace("/\<\!\-\-(.+?)\-\-\>/s", "", $html);
		return $html;
	}
	
	####### Синтаксический сахар #######
	private function syntaxModifier() {
		$this->assign('$propagation', 'evt = event || window.event; evt.cancelBubble = true;');
	}
}

###############################  ПАРСЕР ##################################

/**
 * Очень не хочется туда лезть еще раз.... Работает и работает... Быстро работает...
 * Но есть недостатки с таким парсером - он не отлавливает ошибки синтаксиса шаблонов!
 * Если и занятся реализацией етого недостатка то парсер станет работать в разы медленнее, а ето уже губительно.
 */

class template_parser
{
	public $source = '';


	private $tpl = null;
	private $declare_function = array();
	private $spaces = "[ ]{0,}";
	public $plugins = array();
    


	public function __construct($tpl, $source = false) {
		$this->source = ($source !== false)? $source : $tpl->src;
		$this->tpl = $tpl;

		$this->plugins = array(
		    'parse_gridmenu',
		    'parse_supergrid',
		    'parse_gridaction',
		    'parse_widget',
		    'parse_memcache',
		    'parse_perm',
		    'parse_include',
		    'parse_mc',
		    'parse_add',
		    'parse_del',
		    'parse_iterat',
		    'parse_deiterat',
		    'parse_img',
		    'parse_foreachSmarty',
		    'parse_print',
		    'parse_trigger',
		    'parse_literal',
		    'parse_foreachQuiky',
		    'parse_forQuiky',
		    'parse_simple_if',
		    'parse_vars'
		);
	}

	public function run() {
		// убираем все сторонние вставки php
		$this->source = preg_replace("/\<\?(.+)\?\>/", "",  $this->source);
				
		foreach($this->plugins as $plugin) {
		    if(method_exists($this, $plugin)) {
		        $this->source = @call_user_func_array(array($this, $plugin), array($this->source));
		    }
		} 

		//$this->source = str_replace("  ", '', $this->source);
		//$this->source = str_replace("	", '', $this->source);
		//$this->source = str_replace("\r\n", '', $this->source);
		//$this->source = str_replace("\n", '', $this->source);
		
		return $this->source;
	}

	####### Методы используемые парсером #######№

	private function parse_supergrid($source) {
		global $core;
		
		if(preg_match_all("/".$this->tpl->tag_open."supergrid{$this->spaces}(.+?){$this->tpl->tag_close}(.+?){$this->tpl->tag_open}\/grid{$this->tpl->tag_close}/s" , $source, $matchs))
		{
			$grid = array();
			
			foreach($matchs[1] as $gridNum=>$gridParams)
			{
				$pattern = $matchs[0][$gridNum];
				$replacement = '';
				$settings = array();
				
				
				$params = $this->ext_parse_params($gridParams);
				
				$settingsJson = '{'.trim($matchs[2][$gridNum]).'}';
				
				//$settingsJson = str_replace("'", '"', $settingsJson); // в json должны быть двойные кавычки, блять...
				$settingsJson = preg_replace("/([a-zA-Z0-9_]+?){$this->spaces}:/" , "\"$1\":", $settingsJson); // поправляем написание параметров. можно писать как в javascript а можно жостким json. Возможно будет удобнее писать не задумываясь о кавычках в параметрах.
				$settings = json_decode($settingsJson, true);
				
				//jsonError();echo "\n\n";print_r($settingsJson);print_r($settings);
				if(!is_array($settings) || !count($settings) || !isset($settings['cols']) || !count($settings['cols'])) {
					$replacement = '<!-- Grid widget error: parse error settings list. Use Json syntax! example: {"key":"value", "object":{"a":1,"b":"string"}, "array":[1,3,"str"]} -->';
					continue;
				} 
				
				if(!is_array($params) || !count($params)) {
					$replacement = '<!-- Grid widget error: parse error parameters -->';
					continue;
				} 
				
				if(!isset($params['name'])) {
					$replacement = '<!-- Grid widget error: Grid instance name not set! -->';
					continue;
				}
				
				if(!isset($params['tpl'])) {
					$replacement = '<!-- Grid widget error: Grid template name not set! -->';
					continue;
				}
				
				if(!isset($params['method']) || !isset($params['class'])) {
					$replacement = '<!-- Grid widget error: Not set data class or data method! -->';
					continue;
				}
				// Grid default settings
				if(!isset($settings['view'])) $settings['view'] = 'list';
				if(!isset($settings['limit'])) $settings['limit'] = 10;
				if(!isset($settings['autoload'])) $settings['autoload'] = true;
				
				
				
				$instance = $params['name'];
				
				$settingsJson =  str_replace("'", "\'", decode_unicode_url(json_encode($settings)));
				$paramsJson = str_replace("'", "\'", decode_unicode_url(json_encode($params)));				
				
				$tplInstance = array('params'=>$params,'settings'=>$settings,'instance'=>$params);
				$tplInstance = decode_unicode_url(json_encode($tplInstance));
				$tplInstance = str_replace("'", "\'", $tplInstance);

				$replacement .= "<?php\n";
				$replacement .= "if(!isset(\$this->vars['globalgrid'])) \$this->vars['globalgrid'] = array();\n";
				$replacement .= "\$this->vars['globalgrid']['{$instance}'] = json_decode('{$tplInstance}', true);\n";
				$replacement .= "\$this->vars['instance'] = '{$instance}';\n";
				$replacement .= "\$this->vars['grid'] = json_decode('{$tplInstance}', true);\n";
				$replacement .= "global \$core;\n";
				$replacement .= "\$grid_tpl_site = (\$core->is_admin())? \$core->getAdminModule() : \$core->site_name;\n";
				$replacement .= "echo \$this->get('{$params['tpl']}',\$grid_tpl_site);\n\n";
				$replacement .= "unset(\$this->vars['grid'], \$this->vars['instance']);\n";
				$replacement .= "?>\n\n";
				$replacement .= "<script type='text/javascript'>\n";
				$replacement .= " //try { \n";
				$replacement .= "   grid.{$instance} = new GridInstance('{$instance}'); \n";
				$replacement .= "   grid.{$instance}.params = JSON.parse('{$paramsJson}'); \n";
				$replacement .= "   grid.{$instance}.settings = JSON.parse('{$settingsJson}'); \n";
				$replacement .= "   grid.{$instance}.init(); \n";
				$replacement .= " //} catch(err) { \n";
				$replacement .= " //  alert('Error create grid instance. '+err.message); \n";
				$replacement .= " //} \n";
				$replacement .= "</script>\n";
				
				$source = str_replace($pattern, $replacement, $source);
			}
		}
		
		return $source;
	}
	
	private function parse_grid($source) {
		global $core;
		if(preg_match_all("/".$this->tpl->tag_open."grid{$this->spaces}name{$this->spaces}={$this->spaces}([a-zA-Z_]{1,}){$this->tpl->tag_close}(.+?){$this->tpl->tag_open}\/grid{$this->tpl->tag_close}/s" , $source, $matchs))
		{
			foreach($matchs[2] as $k=>$val)
			{
				$tpl = '';
				$tplphp = '';
				
				$pattern = $matchs[0][$k];
				$gridName = $matchs[1][$k];
				$gridInfo = array();
				
				$matchs[2][$k] = str_replace(array('[cols][]', '{', '}', ':', '[rows]'), array('$gridInfo["'.$gridName.'"]["columns"][]', 'array(', ');', '=>','$gridInfo["'.$gridName.'"]["rows_setting"]'), $val);
				$matchs[2][$k] = preg_replace("/\[limit\]{$this->spaces}={$this->spaces}([0-9]{1,})/s", '$gridInfo["'.$gridName.'"]["limit"] = $1;', $matchs[2][$k]);
				$matchs[2][$k] = preg_replace("/\[curentpage\]{$this->spaces}={$this->spaces}([0-9]{1,})/s", '$gridInfo["'.$gridName.'"]["curentpage"] = $1;', $matchs[2][$k]);
				$matchs[2][$k] = preg_replace("/\[function\]{$this->spaces}={$this->spaces}'(.+?)'/s", '$gridInfo["'.$gridName.'"]["function"] = "$1";', $matchs[2][$k]);
				$matchs[2][$k] = preg_replace("/\[templatefile\]{$this->spaces}={$this->spaces}'(.+?)'/s", '$gridInfo["'.$gridName.'"]["templatefile"] = "$1";', $matchs[2][$k]);
				
				
				eval($matchs[2][$k]);
				
				$gridInfo[$gridName]['columns_count'] = count($gridInfo[$gridName]['columns']);
			
				
				if(isset($gridInfo[$gridName]['rows_setting']) && isset($gridInfo[$gridName]['rows_setting']['click']))
				{
					$gridInfo[$gridName]['rows_setting']['click'] = preg_replace("/%(.+?)%/", '{$1}', $gridInfo[$gridName]['rows_setting']['click']);
				}
				
				$tplphp = "<?php \n";
				$tplphp .= '$this->vars["grid"]["'.$gridName.'"]["limit"] = \''.$gridInfo[$gridName]['limit'].'\';'."\n";
				$tplphp .= '$this->vars["grid"]["'.$gridName.'"]["curentpage"] = \''.$gridInfo[$gridName]['curentpage'].'\';'."\n";
				$tplphp .= '$this->vars["grid"]["'.$gridName.'"]["function"] = \''.$gridInfo[$gridName]['function'].'\';'."\n";
				$tplphp .= '$this->vars["grid"]["'.$gridName.'"]["templatefile"] = \''.$gridInfo[$gridName]['templatefile'].'\';'."\n";
				$tplphp .= '$this->vars["grid"]["'.$gridName.'"]["rows_setting"]["clicable"] = '.($gridInfo[$gridName]['rows_setting']['clicable'] ? '1' : '0').';'."\n";
				$tplphp .= '$this->vars["grid"]["'.$gridName.'"]["rows_setting"]["autoload"] = '.($gridInfo[$gridName]['rows_setting']['autoload'] ? '1' : '0').';'."\n";
				$tplphp .= '$this->vars["grid"]["'.$gridName.'"]["rows_setting"]["click"] = \''.$gridInfo[$gridName]['rows_setting']['click'].'\';'."\n";
				$tplphp .= '$this->vars["grid"]["'.$gridName.'"]["columns_count"] = '.$gridInfo[$gridName]['columns_count'].';'."\n";
				
				
				foreach($gridInfo[$gridName]['columns'] as $SettingKey=>$SettingItem)
				{
					if(!isset($SettingItem['sort']) || !$SettingItem['sort'] || !($SettingItem['sort'] === true || $SettingItem['sort'] === false)) $gridInfo[$gridName]['columns'][$SettingItem['name']]['sort'] = 0; else $gridInfo[$gridName]['columns'][$SettingItem['name']]['sort'] = $SettingItem['sort']+0;
					if(!isset($SettingItem['align']) || !$SettingItem['align'] || !($SettingItem['align'] != 'left' && $SettingItem['align'] != 'right') ) $gridInfo[$gridName]['columns'][$SettingItem['name']]['align'] = 'left'; else $gridInfo[$gridName]['columns'][$SettingItem['name']]['align'] = $SettingItem['align'];
					if(!isset($SettingItem['searche']) || !$SettingItem['searche'] || !($SettingItem['searche'] === true || $SettingItem['searche'] === false)) $gridInfo[$gridName]['columns'][$SettingItem['name']]['searche'] = 0; else $gridInfo[$gridName]['columns'][$SettingItem['name']]['searche'] = $SettingItem['searche']+0;
					if(!isset($SettingItem['sort_type']) || !$SettingItem['sort_type'] || !($SettingItem['sort_type'] == 'asc' || $SettingItem['sort_type'] == 'desc')) $gridInfo[$gridName]['columns'][$SettingItem['name']]['sort_type'] = 'asc'; else $gridInfo[$gridName]['columns'][$SettingItem['name']]['sort_type'] = $SettingItem['sort_type'];
					if(!isset($SettingItem['sorted']) || !$SettingItem['sorted'] || !($SettingItem['sorted'] === true || $SettingItem['sorted'] === false)) $gridInfo[$gridName]['columns'][$SettingItem['name']]['sorted'] = 0; else $gridInfo[$gridName]['columns'][$SettingItem['name']]['sorted'] = $SettingItem['sorted']+0; 
					if(!isset($SettingItem['datepiker']))  $gridInfo[$gridName]['columns'][$SettingItem['name']]['datepiker'] = 0; else $gridInfo[$gridName]['columns'][$SettingItem['name']]['datepiker'] = 1;
					
					$gridInfo[$gridName]['columns'][$SettingItem['name']]['name'] = $SettingItem['name'];
					$gridInfo[$gridName]['columns'][$SettingItem['name']]['title'] = $SettingItem['title'];
					
					if($gridInfo[$gridName]['columns'][$SettingItem['name']]['sorted'] == true)
					{
						$gridInfo[$gridName]['sort_by'] = $SettingItem['name'];
						$gridInfo[$gridName]['sort_type'] = $gridInfo[$gridName]['columns'][$SettingItem['name']]['sort_type'];
						
						$tplphp .= '$this->vars["grid"]["'.$gridName.'"]["sort_by"] = "'.$gridInfo[$gridName]['sort_by'].'";'."\n";
						$tplphp .= '$this->vars["grid"]["'.$gridName.'"]["sort_type"] = "'.$gridInfo[$gridName]['sort_type'].'";'."\n";
					}
					
					
					$tplphp .= '$this->vars["grid"]["'.$gridName.'"]["columns"]["'.$SettingItem['name'].'"]["sort"] = '.$gridInfo[$gridName]['columns'][$SettingItem['name']]['sort'].';'."\n";
					$tplphp .= '$this->vars["grid"]["'.$gridName.'"]["columns"]["'.$SettingItem['name'].'"]["align"] = "'.$gridInfo[$gridName]['columns'][$SettingItem['name']]['align'].'";'."\n";
					$tplphp .= '$this->vars["grid"]["'.$gridName.'"]["columns"]["'.$SettingItem['name'].'"]["searche"] = '.$gridInfo[$gridName]['columns'][$SettingItem['name']]['searche'].';'."\n";
					$tplphp .= '$this->vars["grid"]["'.$gridName.'"]["columns"]["'.$SettingItem['name'].'"]["sort_type"] = "'.$gridInfo[$gridName]['sort_type'].'";'."\n";
					$tplphp .= '$this->vars["grid"]["'.$gridName.'"]["columns"]["'.$SettingItem['name'].'"]["sorted"] = '.$gridInfo[$gridName]['columns'][$SettingItem['name']]['sorted'].';'."\n";
					$tplphp .= '$this->vars["grid"]["'.$gridName.'"]["columns"]["'.$SettingItem['name'].'"]["name"] = "'.$SettingItem['name'].'";'."\n";
					$tplphp .= '$this->vars["grid"]["'.$gridName.'"]["columns"]["'.$SettingItem['name'].'"]["title"] = "'.$SettingItem['title'].'";'."\n";
					$tplphp .= '$this->vars["grid"]["'.$gridName.'"]["columns"]["'.$SettingItem['name'].'"]["datepiker"] = '.$gridInfo[$gridName]['columns'][$SettingItem['name']]['datepiker'].';'."\n";
				
					unset($gridInfo[$gridName]['columns'][$SettingKey], $SettingItem);
				}
				
				//$tplphp .= '$_tmp = $this->get("'.$gridInfo[$gridName]['templatefile'].'", $core->site_name); unset($_tmp);'."\n";
				$tplphp .= " ?>\n";
				
				$this->tpl->vars['grid'][$gridName] = $gridInfo[$gridName];
				
				$tpl = $this->tpl->get_source_tpl($core->site_name, true, $gridInfo[$gridName]['templatefile']);
				$tpl = $this->_parse_grid_template($tpl, $gridInfo, $gridName);
				//print_r($gridInfo);
				//echo $tpl;
				//die();	
				
				$source = $tplphp.str_replace($pattern, $tpl, $source);
				//echo $source;
				//die();
				
				unset($pattern, $replacement, $tpl, $tplphp, $gridName, $gridInfo, $SettingItem, $SettingKey);
			}
		}
		
		return $source;		
	}
	
	private function _parse_grid_template($source, $gridInfo, $gridName)
	{
		$pattern[] = '{$thisGridname}';
		$replacement[]= $gridName;	
		$pattern[] = '{content}';
		$replacement[]= '<script type="text/template" id="grid-'.$gridName.'-tbody-row-template">';	
		$pattern[] = '{/content}';
		$replacement[]= '</script>';	
						
		$source = str_replace($pattern, $replacement, $source);
		
		return $source;
	}
	
	private function parse_gridaction($source) {
		if(preg_match_all("/".$this->tpl->tag_open."gridaction{$this->spaces}name{$this->spaces}={$this->spaces}([a-zA-Z_]{1,}){$this->tpl->tag_close}(.+?){$this->tpl->tag_open}\/gridaction{$this->tpl->tag_close}/s" , $source, $matchs)) {
			foreach($matchs[1] as $gridNum=>$gridName) {
				$pattern = $matchs[0][$gridNum];
				$replacement = '';
				
				$settings = '{'.trim($matchs[2][$gridNum]).'}';
				$settings = preg_replace("/([a-zA-Z0-9_]+?){$this->spaces}:/" , "\"$1\":", $settings); 
				$settings = json_decode($settings, true);
				
				if(is_array($settings)) {
					$replacement .= "<span style=\"display:none\" id=\"grid-{$gridName}-groupAction-automenu\"></span>";
					$replacement .= "<script type=\"text/javascript\">\n";
					$replacement .= " try {\n";
					$replacement .= "	var grid_{$gridName}_groupaction_data = JSON.parse('".json_encode($settings)."');\n";
					$replacement .= " }\n";
					$replacement .= " catch(e) {\n";
					$replacement .= " 	console.log('error grid group action settings! -> '+ e.message);\n";
					$replacement .= " }\n";
					$replacement .= "</script>\n";
				}
			
				$source = str_replace($pattern,$replacement,$source);
			}
		}
		
		return $source;
	}
	
	private function parse_mc($source) {
		if(preg_match_all("/{$this->tpl->tag_open}mc {$this->spaces}([a-zA-Z\_\-]{2,})\/([a-zA-Z\_\-\ ]{1,}){$this->tpl->tag_close}/s", $source, $matchs)) {
			foreach($matchs[0] as $k=>$parent) {
				$parents[] = $parent;
				$replacements[] = '<?php if(isset($this->vars[\'module\']) && isset($this->vars[\'module\'][\'name\']) && $this->vars[\'module\'][\'name\'] == \''.trim($matchs[1][$k]).'\' && $this->vars[\'controller\'][\'name\'] == \''.trim($matchs[2][$k]).'\'): ?>';
			}
	
			$source = str_replace($parents,$replacements,$source);
		}
		
		if(preg_match_all("/{$this->tpl->tag_open}mc {$this->spaces}([a-zA-Z\_\-]{2,}){$this->tpl->tag_close}/s", $source, $matchs)) {
			foreach($matchs[0] as $k=>$parent) {
				$parents[] = $parent;
				$replacements[] = '<?php if(isset($this->vars[\'module\']) && isset($this->vars[\'module\'][\'name\']) && $this->vars[\'module\'][\'name\'] == \''.trim($matchs[1][$k]).'\'): ?>';
			}
		
			$source = str_replace($parents,$replacements,$source);
		}
	
		$source = str_replace("{$this->tpl->tag_open}/mc{$this->tpl->tag_close}",'<?php endif; ?>',$source);
	
		return $source;
	}
	
	
	private function parse_gridmenu($source) {
		if(preg_match_all("/".$this->tpl->tag_open."gridmenu{$this->spaces}(.+?){$this->spaces}{$this->tpl->tag_close}(.+?){$this->tpl->tag_open}\/gridmenu{$this->tpl->tag_close}/s" , $source, $matchs)) {
			foreach($matchs[1] as $gridMenuNum=>$gridMenuParams) {
				
				$params = $this->ext_parse_params($gridMenuParams);
				if(!is_array($params) || !isset($params['name'])) continue;
				
				$pattern = $matchs[0][$gridMenuNum];
				$gridName = trim($params['name']);
				
				$replacement = "	<script type=\"text/template\" id=\"grid-{$gridName}-settings-MenuTemplate\">\n";
				$replacement .= trim($matchs[2][$gridMenuNum]);
				$replacement .= "	</script>\n";
				
				if(isset($params) /*&& !empty($params['settings'])*/) {
					/*$params['settings'] = $this->ext_generate_var($params['settings']);*/
					$nameTmpVar = 'grid_'.$gridName.'_settings_menu_user_data';
					$settingTemplatesVar = "\$this->vars['userSetting']['grid_{$gridName}_hidecols']";
					
					$replacement .= "<script type=\"text/javascript\">\n";
					$replacement .= " try {\n";
					$replacement .= "	var {$nameTmpVar} = JSON.parse('<?php if(isset({$settingTemplatesVar})) echo json_encode({$settingTemplatesVar}); else echo '[]'; ?>');\n";
					$replacement .= " }\n";
					$replacement .= " catch(e) {\n";
					$replacement .= " 	console.log('error user settings! -> '+ e.message);\n";
					$replacement .= " }\n";
					$replacement .= "</script>\n";
				}
				
				$source = str_replace($pattern,$replacement,$source);
				
				$matchs[3][$gridMenuNum] = $params;
			}
		}
		

		return $source;
	}
	
	private function parse_memcache($source) {
		if(preg_match_all("/".$this->tpl->tag_open."memcache{$this->spaces}name{$this->spaces}={$this->spaces}([a-zA-Z_]{1,}){$this->spaces}expire{$this->spaces}={$this->spaces}([0-9]{1,})".$this->tpl->tag_close."/s" , $source, $matchs))
			{
			foreach($matchs[0] as $k=>$parent)
				{
				$parents[] = $parent;
				$replace  = '<?php if($__tmp_cached = $core->memcache->get(\'templater_cache_'.$matchs[1][$k].'_'.md5($_SERVER['REQUEST_URI']).'\')):';
				$replace .= 'echo $__tmp_cached."<!-- from memcache -->";';
				$replace .= 'unset($__tmp_cached);';
				$replace .= '$core->log->add_cache_tpl("Cached from template source in -> '.$this->tpl->tplname.'", "templater_cache_'.$matchs[1][$k].'_'.md5($_SERVER['REQUEST_URI']).'", "'.$matchs[2][$k].'");';
				$replace .= 'else:';
				$replace .= 'ob_start("templates_buffering_callback"); ?>';
	
				$replacements[] = $replace;
	
				$parents[] = '{/memcache}';
	
				$replace = '<?php ob_end_flush();';
				$replace .= '$core->memcache->set(\'templater_cache_'.$matchs[1][$k].'_'.md5($_SERVER['REQUEST_URI']).'\', $this->tmp_buffer_obj, '.$matchs[2][$k].');';
				$replace .= 'endif; ?>';
	
				$replacements[] = $replace;
				}
	
			$source = str_replace($parents,$replacements,$source);
			}
	
		return $source;
	}

	private function parse_include($source)
	{
		if(preg_match_all("/".$this->tpl->tag_open."include{$this->spaces}(.+?)".$this->tpl->tag_close."/s" , $source, $matchs))
		{
			foreach($matchs[0] as $k=>$parent)
			{
				$tplname = trim($matchs[1][$k]);
				if(substr($tplname, 0, 1) == $this->tpl->var_start_symbol) {
					$tplname = $this->ext_generate_var($tplname);
				} else {
					$tplname = '"'.$tplname.'"';
				}
				
				$parents[] = $parent;
				$replacements[] = '<?php echo $this->get('.$tplname.'); ?>';
			}
			
			$source = str_replace($parents,$replacements,$source);
		}
		
		return $source;
	}
	
	private function parse_print($source)
	{
		if(preg_match_all("/".$this->tpl->tag_open."print{$this->spaces}(.+?)".$this->tpl->tag_close."/s" , $source, $matchs))
		{
			foreach($matchs[0] as $k=>$parent)
			{
				$parents[] = $parent;
				$replacements[] = '<?php print_r('.$this->ext_generate_var($matchs[1][$k]).') ?>';
			}
			
			$source = str_replace($parents,$replacements,$source);
		}
		
		return $source;
	}
	
	private function parse_trigger($source)
	{
		
		if(preg_match_all("/".$this->tpl->tag_open."trigger{$this->spaces}to{$this->spaces}={$this->spaces}(.+?){$this->spaces}from{$this->spaces}={$this->spaces}\[(.+?)\]{$this->spaces}".$this->tpl->tag_close."/s" , $source, $matchs))
		{
			foreach ($matchs[0] as $key=>$patern)
			{
				if(!$matchs[2][$key] || !$matchs[1][$key]) break;
				
				$triggerName = substr($matchs[1][$key], 1);
				$replacement = "<?php ";
				$vars = explode(",", $matchs[2][$key]);
				foreach ($vars as $k=>$var)
				{
					$var = trim($var);
					$var = (substr($var, 0, 1)==$this->tpl->var_start_symbol)? $this->ext_generate_var($var) : $var;
					
					$replacement .= '$this->vars["-triggers"]["'.$triggerName.'"]["vars"]['.$k.'] = '.$var."; \n";	
				}
				$replacement .= '$this->vars["-triggers"]["'.$triggerName.'"]["curent"] = 0; ?>';
				
				$source = str_replace($patern,$replacement,$source);				
			}
			
			unset($triggerName, $replacement, $vars, $replacement, $patern, $key, $matchs, $k, $var);
		}
		
		if(preg_match_all("/".$this->tpl->tag_open.'trigger'.$this->spaces.'\\'.$this->tpl->var_start_symbol."(.+?)".$this->tpl->tag_close."/s",$source, $matchs))
		{
			foreach ($matchs[0] as $key=>$patern)
			{
				$triggerName = $matchs[1][$key];
				
				$replacement = "<?php if(isset(\$this->vars['-triggers']['{$triggerName}']['vars'][\$this->vars['-triggers']['{$triggerName}']['curent']])) echo \$this->vars['-triggers']['{$triggerName}']['vars'][\$this->vars['-triggers']['{$triggerName}']['curent']]; \n";
				$replacement .=  " if(count(\$this->vars['-triggers']['{$triggerName}']['vars']) == (\$this->vars['-triggers']['{$triggerName}']['curent'])+1) \$this->vars['-triggers']['{$triggerName}']['curent'] = 0; else \$this->vars['-triggers']['{$triggerName}']['curent']++; ?>\n";
								
				$source = str_replace($patern,$replacement,$source);				
			}
		}
		
		return $source;
	}
	
	private function parse_perm($source)
	{
		if(preg_match_all("/".$this->tpl->tag_open."perm{$this->spaces}([0-9]{1,})".$this->tpl->tag_close."/s" , $source, $matchs))
		{
			foreach($matchs[0] as $k=>$parent)
			{
				$parents[] = $parent;
				$replacements[] = '<?php if($core->perm->check('.intval(trim($matchs[1][$k])).')): ?>';
			}
	
			$parents[] = '{/perm}';
			$replacements[] = '<?php endif; ?>';
			$parents[] = '{perm-else}';
			$replacements[] = '<?php else: ?>';
	
			$source = str_replace($parents,$replacements,$source);
		}
		
		if(preg_match_all("/".$this->tpl->tag_open."perm{$this->spaces}([0-9A-Za-z_]{1,}:[0-9]{1,})".$this->tpl->tag_close."/s" , $source, $matchs))
		{
			foreach($matchs[0] as $k=>$parent)
			{
				$permission = explode(':', trim($matchs[1][$k]));
				
				$parents[] = $parent;
				$replacements[] = '<?php if($core->perm->check('.intval($permission[1]).', $core->modules->getModuleIdByName('.addslashes($permission[0]).'))): ?>';
			}
	
			$parents[] = '{/perm}';
			$replacements[] = '<?php endif; ?>';
	
			$source = str_replace($parents,$replacements,$source);
		}
	
		return $source;
	}

	private function parse_add($source)
	{
		if(preg_match_all("/".$this->tpl->tag_open."add{$this->spaces}(.+?){$this->spaces}={$this->spaces}(.+?)".$this->tpl->tag_close."/s" , $source, $matchs)) {
			$paterns = array();
			$replacements = array();
			
			foreach($matchs[1] as $num => $varName) {
				$varCode = $this->ext_generate_var($varName);
				$varValue = $matchs[2][$num];
				$parsedValue = $this->ext_parse_value($varValue, true);
				
				$paterns[] 		= $matchs[0][$num];
				$replacements[] = '<?php '.$varCode.' = '.$parsedValue.'; ?>';
			}
				
				
			$source = str_replace($paterns,$replacements,$source);
		}
		
		
		return $source;
	}

	private function parse_iterat($source)
	{
		if(preg_match_all("/".$this->tpl->tag_open."iterate{$this->spaces}(.+?)".$this->tpl->tag_close."/s" , $source, $matchs))
		{
			foreach($matchs[1] as $_k => $_v)
				{
					if(substr(trim($_v),0,1)==$this->tpl->var_start_symbol)
					{
	
						$var_iterate = $this->ext_generate_var(trim($_v));
		
						$parents[] 		= $matchs[0][$_k];
						$replacements[] = '<?php '.$var_iterate.'++; ?>';
					}
				}
	
			$source = str_replace($parents,$replacements,$source);
		}

		return $source;
	}

	private function parse_deiterat($source)
	{
		if(preg_match_all("/".$this->tpl->tag_open."deiterate{$this->spaces}(.+?)".$this->tpl->tag_close."/s" , $source, $matchs))
		{
			foreach($matchs[1] as $_k => $_v)
				{
					if(substr(trim($_v),0,1)==$this->tpl->var_start_symbol)
					{
	
					$var_iterate = $this->ext_generate_var(trim($_v));
	
					$parents[] 		= $matchs[0][$_k];
					$replacements[] = '<?php '.$var_iterate.'--; ?>';
					}
				}
	
			$source = str_replace($parents,$replacements,$source);
		}
		return $source;
	}

	private function parse_img($source)
	{	
		if(preg_match_all("/".$this->tpl->tag_open."img{$this->spaces}id{$this->spaces}={$this->spaces}(.+?){$this->spaces}(descr|name|alias|size|user|date|src|prev)".$this->tpl->tag_close."/s", $source, $matchs))
			{	
			foreach($matchs[1] as $k=>$v)
				{
				if(substr(trim($v),0,1)==$this->tpl->var_start_symbol)
					{
					$id_image = $this->ext_generate_var(trim($v));
					}
					else 
						{
						$id_image = trim($v);
						}
	
						
						
					switch ($matchs[2][$k])
					{
						case 'descr':
							
						break;
							
						case 'name':
							
						break;
							
						case 'alias':
								
						break;
							
						case 'size':
								
						break;
						
						case 'user':
								
						break;
						
						case 'date':
								
						break;
						
						case 'src':
								
						break;
						
						case 'prev':
								
						break;		
					}	
				}
			}
			
			return $source;
	}
	
	private function parse_foreachSmarty($source)
	{
		if(!preg_match_all("/".$this->tpl->tag_open."foreach{$this->spaces}from{$this->spaces}={$this->spaces}\\".$this->tpl->var_start_symbol."(.*?){$this->spaces}as{$this->spaces}={$this->spaces}(.+?)".$this->tpl->tag_close."/s", $source, $matchs)) return $source;   
			
		foreach($matchs[1] as $kkk=>$vvv)
		{
			$this->tpl->foreaches_this_name_var = str_replace(".", "_", $vvv);
			$this->tpl->foreaches_this_name = $vvv;
			$this->tpl->foreaches_this_item  = $matchs[2][$kkk];
		
			$this->tpl->foreaches[$this->tpl->foreaches_this_name]['count']   = @count($this->tpl->vars[$this->tpl->foreaches_this_name]);
	
			$from = $this->ext_generate_var('$'.$this->tpl->foreaches_this_name);
	
			$parents[] 		= $matchs[0][$kkk];
			$replacements[] = '<?php if(count('.$from.')) : 
							   		 $this->vars[\'foreach\'][\''.$this->tpl->foreaches_this_name.'\'][\'count\'] = count('.$from.');
							   		 $this->vars[\'foreach\'][\'last\'][\'count\'] = count('.$from.');
							   		 foreach('.$from.' as $this->vars[\'foreach\'][\''.$this->tpl->foreaches_this_name_var.'\'][\'key\'] => $this->vars[\''.$this->tpl->foreaches_this_item.'\']):
							            $this->vars[\'foreach\'][\'key\'] = $this->vars[\'foreach\'][\''.$this->tpl->foreaches_this_name_var.'\'][\'key\']?>';
		}
	
		$parents[] = '{/foreach}';
		
		$_tmpReplacement = '<?php endforeach; ?>';
		
		if($this->tpl->debugParser)
		{
			$_tmpReplacement .= '<?php else:  ?>';
			$_tmpReplacement .= '<!-- empty array : '.$from.' //-->';
		}
		
		$_tmpReplacement.= '<?php endif; ?>';
		
		$replacements[] = $_tmpReplacement;
		
		unset($_tmpReplacement, $from, $vvv, $matchs, $kkk);

	
		$source = str_replace($parents, $replacements, $source);
		$source = str_replace(array("{foreach_break}","{foreach_continue}"), array('<?php break; ?>', '<?php continue; ?>'), $source);
		
		return $source;
	}
	
	private function parse_literal($source)
	{
		$source = str_replace(array('{/literal}', '{literal}'), array('',''),$source);
		
		return $source;
	}
	
	private function parse_foreachQuiky($source)
	{
		preg_match_all("/".$this->tpl->tag_open."foreach{$this->spaces}value{$this->spaces}={$this->spaces}\"(.+?)\"{$this->spaces}from{$this->spaces}={$this->spaces}\\".$this->tpl->var_start_symbol."(.*?)".$this->tpl->tag_close."/s", $source, $matchs);
	
	
		foreach($matchs[2] as $kkk=>$vvv)
		{
			$this->tpl->foreaches_this_name = $vvv;
			$this->tpl->foreaches_this_item  = $matchs[1][$kkk];
	
			$this->tpl->foreaches[$this->tpl->foreaches_this_name]['count']   = count($this->tpl->vars[$this->tpl->foreaches_this_name]);
	
			$from = $this->ext_generate_var('$'.$this->tpl->foreaches_this_name);
	
	
			$parents[] 		= $matchs[0][$kkk];
			$replacements[] = '<?php if(count('.$from.')) : ?>
							   <?php $this->vars[\'foreach\'][\''.$this->tpl->foreaches_this_name.'\'][\'count\'] = count('.$from.') ?>
							   <?php foreach('.$from.' as $this->vars[\'foreach\'][\''.$this->tpl->foreaches_this_name.'\'][\'key\'] => $this->vars[\''.$this->tpl->foreaches_this_item.'\']): ?>
							   <?php $this->vars[\'foreach\'][\'key\'] = $this->vars[\'foreach\'][\''.$this->tpl->foreaches_this_name.'\'][\'key\']?>';
		}
		
	
		$parents[] = '{/foreach}';
		
		$_tmpReplacement = '<?php endforeach; ?>';
		
		if($this->tpl->debugParser)
		{
			$_tmpReplacement .= '<?php else:  ?>';
			//$_tmpReplacement .= '<!-- empty array : '.$from.' //-->';
		}
		
		$_tmpReplacement.= '<?php endif; ?>';
		
		$replacements[] = $_tmpReplacement;
		
		unset($_tmpReplacement, $from, $vvv, $matchs, $kkk);

	
		$source = str_replace($parents, $replacements, $source);
	
		return $source;
	}

	private function parse_forQuiky($source)
	{
		if(preg_match_all("/{$this->tpl->tag_open}for {$this->spaces}([loop|step|start|value]{1,}(.+)){$this->spaces}{$this->tpl->tag_close}/",$source, $matchs))
		{
			$headerPattern = $matchs[0];
			$forSettings = array();
			$forParams = array();
			
			foreach($matchs[1] as $k=>$forstring)
			{
				$forSettings[$k] = explode(' ',$forstring);
				foreach($forSettings[$k] as $_k=>$_t)
				{
				  	$forSettings[$k][$_k]=explode('=', $_t);
					$forSettings[$k][$_k][1] = (substr($forSettings[$k][$_k][1], 0,1) == '$')? $this->ext_generate_var($forSettings[$k][$_k][1]):$forSettings[$k][$_k][1];
					$forParams[$k][$forSettings[$k][$_k][0]] = $forSettings[$k][$_k];
				}
			}
			
			if(isset($forParams))
			{
				$pattern = array();
				$replacement = array();
				
				foreach($forParams as $forNum=>$forParam)
				{
					if(!isset($forParam['value']) || !isset($forParam['value'][1])) continue;
					if(!isset($forParam['loop']) || !isset($forParam['loop'][1])) continue;
					
					$value = $forParam['value'][1];
					$loop = $forParam['loop'][1];
					
					if(!isset($forParam['step']) || !isset($forParam['step'][1])) $step = 1; else $step = $forParam['step'][1];
					if(!isset($forParam['start']) || !isset($forParam['start'][1])) $start = 0; else $start = $forParam['start'][1];
										
					$pattern[$forNum] = $headerPattern[$forNum];
					$replacement[$forNum] = '<?php for('.$value.'='.$start.'; '.$value.'!='.$loop.'; '.$value.'+='.$step.'):?>';
				}
				
				$pattern[] = "{$this->tpl->tag_open}/for{$this->tpl->tag_close}";
				$replacement[] = "<?php endfor; ?>";
				
				$source = str_replace($pattern,$replacement,$source);
			}
		}
	
		return $source;
	}
	
	private function parse_simple_if($source)
	{
		if(preg_match_all("/".$this->tpl->tag_open."if{$this->spaces}(.+?){$this->spaces}(.+?)".$this->tpl->tag_close."/s", $source, $matchs))
			{
	
			foreach($matchs[2] as $k=>$v)
				{
					
						$st = explode(' ',trim($v));
			
						if(substr(@$st[2], 0, 1) == $this->tpl->var_start_symbol)
							$st[2] = $this->ext_generate_var(@$st[2]);
			
						if($matchs[1][$k] == $this->tpl->var_start_symbol)
							$st[0] = $st[0] = $this->ext_generate_var($st[0]);
			
						$source = str_replace(@$matchs[0][$k], '<?php if (isset('.@$st[0].') && '.@$st[0].' '.@$st[1].' '.@$st[2].'): ?>',$source);
						$source = str_replace($this->tpl->tag_open.'else'.$this->tpl->tag_close, '<?php else: ?>',$source);
						$source = str_replace($this->tpl->tag_open.'/if'.$this->tpl->tag_close, '<?php endif; ?>',$source);
				
				}
			}
	
		return $source;
	}
		
	private function parse_vars($source) {
		if(preg_match_all("/".$this->tpl->tag_open.'\\'.$this->tpl->var_start_symbol."(.*?)".$this->tpl->tag_close."/s",$source, $matchs)) {
			foreach($matchs[1] as $k=>$v) {	
				$paterns[] = $matchs[0][$k];
				
				if(strpos($v, '|') !== false) {
					$varHandler = explode('|', trim($v));
					$v = $this->ext_generate_var($this->tpl->var_start_symbol.trim($varHandler[0]));
					
					if(count($varHandler) > 1) {
						$func = trim($varHandler[1]);
						$arguments = array();
						
						if(preg_match("/\((.+?)\)$/", $func, $arguments)) {
							$arguments = $arguments[1];
							$arguments = explode(',', $arguments);
							
							
							foreach($arguments as &$item) {
								$item = trim($item);
								if($item == 'this') { 
									$item = $v;
								} else if(substr($item, 0, 1) == $this->tpl->var_start_symbol) {
									$item = $this->ext_generate_var($item);
								}
							}
							
							$func = substr($func, 0, strpos($func,'('));
						} else {
							$arguments[] = $v;
						}

						if(strpos($func, '::')) {
							if(is_callable(explode('::',$func), true, $callable_name)) {
								$replacements[] = '<?php if(isset('.$v.')) echo '.$callable_name.'('.implode(',',$arguments).'); ?>';
							} else {
								$replacements[] = '<?php if(isset('.$v.')) echo '.$v.'; ?>';
							}
						} else {
							if(function_exists($func)) {
								$replacements[] = '<?php if(isset('.$v.')) echo '.$func.'('.implode(',',$arguments).'); ?>';
							} else {
								$replacements[] = '<?php if(isset('.$v.')) echo '.$v.'; ?>';
							}
						}
					}					
				} else {
					$v = $this->ext_generate_var($this->tpl->var_start_symbol.trim($v));
					$replacements[] = '<?php if(isset('.$v.')) echo '.$v.'; ?>';
				}
			}

			$source = str_replace($paterns,$replacements,$source);
		}
		
		return $source;
	}

	private function parse_del($source)
	{
		if(preg_match_all("/".$this->tpl->tag_open."del{$this->spaces}(.+?)".$this->tpl->tag_close."/s" , $source, $matchs))
		{
		foreach($matchs[0] as $k=>$parent)
			{
			$parents[] = $parent;
			$replacements[] = '<?php unset('.$this->ext_generate_var($matchs[1][$k]).'); ?>';
			}

		$source = str_replace($parents,$replacements,$source);
		}

		return $source;
	}
	
	private function parse_widget($source)
	{
		if(preg_match_all("/{$this->tpl->tag_open}widget{$this->spaces}(.*?){$this->tpl->tag_close}(.*?){$this->tpl->tag_open}\/widget{$this->tpl->tag_close}/s", $source, $matchs))
		{
			$paternts = array();
			$replacements = array();
			
			foreach($matchs[1] as $blokPosition=>$nameWidget)
			{
				$paternts[] = $matchs[0][$blokPosition];
				
				if(isset($matchs[2][$blokPosition]) && strlen($matchs[2][$blokPosition])>1)
				{
					$out = $this->ext_parseWidgetSetting($matchs[2][$blokPosition], $nameWidget);					
					$replacements[] = '<?php if($core->lib->widget("'.$nameWidget.'")): ?>'."\n".$out."\n".'<?php $core->widgets->'.$nameWidget.'()->appendSettings(); $core->widgets->'.$nameWidget.'()->main(); endif; ?>';
				}
				else 
					{
						$replacements[] = '<?php if($core->lib->widget("'.$nameWidget.'")): ?>'."\n".'<?php $core->widgets->'.$nameWidget.'()->appendSettings(); $core->widgets->'.$nameWidget.'()->main(); endif; ?>';
					}
			}
			
			$source = str_replace($paternts, $replacements, $source);
		}
		
		return $source;
	}
		
	private function ext_parseWidgetSetting($str, $nameWidget)
	{
		
		$set = explode("\n", $str);
		$out = '';
		
		
		foreach($set as $settingString)
		{
			if(!$settingString || $settingString == "\n" ||$settingString == "\r" ) continue;
			$keyvalue = explode("=", $settingString);

			if(count($keyvalue) < 2) continue;
			
			$keyvalue[0] = trim($keyvalue[0]); 
			$keyvalue[1] = $this->ext_parseWidgerSettingVar(trim($keyvalue[1])); 
		
			if((substr($keyvalue[1], 0, 1) == '[') && (substr($keyvalue[1], -1) == ']'))
			{
				$keyvalue[1] = substr($keyvalue[1], 1);
				$keyvalue[1] = substr($keyvalue[1], 0, -1);
				
				foreach(explode(",", $keyvalue[1]) as $item)
				{
					$arrayString[] = '"'.trim($item).'"'; 
				}
				
				$out .= '<?php $core->widgets->'.$nameWidget.'()->setting("'.$keyvalue[0].'", array('.implode(", ", $arrayString).')); ?>'."\n"; 
			}
			else 
				{
					$out .= '<?php $core->widgets->'.$nameWidget.'()->setting("'.$keyvalue[0].'", "'.$keyvalue[1].'"); ?>'."\n"; 
				}
		}
		
		return $out;
	}
	
	private function ext_parseWidgerSettingVar($str)
	{
		if(preg_match_all("/".$this->tpl->tag_open."(.*?)".$this->tpl->tag_close."/s",$str, $matchs))
		{
			foreach($matchs[1] as $k=>$v)
			{
			if(substr(trim($v),0,1) == $this->tpl->var_start_symbol)
				{
				$parents[] = $matchs[0][$k];
		
				$template_var_link = $this->ext_generate_var($v);
		
				$replacements[] = '".'.$template_var_link.'."';
				}
			}
		
			$str = str_replace($parents,$replacements,$str);
		}
		
		return $str;
	}
	
	private function ext_generate_var($var_str)
	{

		if($var_str == $this->tpl->var_start_symbol) {
			return '$this->vars';
		}
		
		if(substr($var_str, 0, 1) != $this->tpl->var_start_symbol)
			$var_str = $this->tpl->var_start_symbol.$var_str;
	
		$tmp = '';
	
		$v = explode('.', trim($var_str));
	
		$_start_k = substr(trim($v[0]),1);
	
		if(is_array($v))
			{
			unset($v[0]);
	
			foreach($v as $_v)
				{
				if(substr(trim($_v), 0, 1) == $this->tpl->var_start_symbol)
					{
					$tmp .=  '[$this->vars[\''.substr(trim($_v), 1).'\']]';
					}
					else
						{
						$tmp .=  '[\''.trim($_v).'\']';
						}
				}
	
			$out = '$this->vars[\''.$_start_k.'\']'.$tmp;
			}
	
	
		return $out;
	}

	private function ext_parse_params($paramString)
	{
		$return = array();
		if(preg_match_all("/([a-zA-Z_\.\-]{1,}){$this->spaces}={$this->spaces}([\$a-zA-Z_\.\-]{1,})/s", $paramString, $params))
		{
			foreach($params[1] as $paramNum=>$paramName)
			{
				$paramName = trim($paramName);
				$paramValue = trim($params[2][$paramNum]);
				
				if(substr($paramName, 0, 1) == $this->tpl->var_start_symbol) {
					$paramValue = $paramValue;
				} else {
					if(substr($paramName, 0, 1) == '"' || substr($paramName, 0, 1) == "'") {
						if(substr($paramName, -1) == '"' || substr($paramName, -1) == "'") {
							$paramName = substr($paramName, 1, -1);
						}
					}
					
					if(substr($paramValue, 0, 1) == '"' || substr($paramValue, 0, 1) == "'") {
						if(substr($paramValue, -1) == '"' || substr($paramValue, -1) == "'") {
							$paramValue = substr($paramValue, 1, -1);
						}
					}
				}
				
				
				$return[$paramName] = $paramValue;
			}
		}
		
		return $return;
	}

	private function ext_parse_value($value) {
		$value = trim($value);
		$valType = $this->ext_get_value_type($value);
		
		if($valType == 'variable') return $this->ext_generate_var($value);
		if($valType == 'int') return $value;
		if($valType == 'string') {
			if(substr($value, 0, 1) != '"' && substr($value, 0, 1) != "'") {
				$value = "'".addslashes($value)."'";
			}
			
			return $value;
		}
		
		if($valType == 'array') {
			if(strlen($value)==2) return array();
			$value = 'json_decode("'.addslashes($value).'", true)';
			return $value;
		}
	}

	private function ext_get_value_type($value) {
		if(preg_match("/^[0-9\.]{1,}$/", $value)) return 'int';
		if(preg_match("/^\[.+?\]$/", $value)) return 'array';
		if(substr($value, 0,1) == $this->tpl->var_start_symbol) return 'variable';
		
		return 'string';
	}
}


?>