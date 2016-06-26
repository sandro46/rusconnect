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
 * Базовый класс. Входит в состав ядра. Реализует методы для работы с базами данных.
 * Сласс относительно большой, много полезных и очень гибких методов.
 *
 * ######################################################################################
 * Он умеет:
 * 		- Работать с несколькими серверами и базами данных через один обьект класса
 * 		- Обрабатывать ошибки
 * 		- Записовать логи запросов (по желанию ил принудительно при возьникновении ошибки в запросе)
 * 		- Выбирать данные из базы после чего автоматически их обрабатывать и выводить чистый массив с обработанными данными пользовалельскими методами
 * 		- Автоматически (Обновлять/Добавлять) Записи в базе данных на основе передаваеммого массива данных
 * 		- блокировать / Разблокировать таблицы иммитируя транзакцию
 * 		- Удобным образом получать данные из запроса при выводе одной строки
 * 		- Автоматически удалять записи по переданному ключу
 *
 * 	также еще много удобных прелестей:
 * 		- При выполнении запросов Insert, Update, Delete, Replase, SET Автоматически записывает в лог данные о запросе
 * 		- При возьникновении ошибки, записывает данные о выполняемом запросе, о времени выполнения, возникшей ошибке, и другие дополнительные данные
 */


/**
 * Интерфейс для всех классов работы с базой
 */
interface mysql_interface
{
	public function query($sql, $db_name= 'db_site');
	public function close($db_name= 'db_site');
	public function get_field($field= 0, $row= 0, $date_filter= 0);
	public function get_rows($one_row= 0);
}


/**
 * Общие методы используемые в классах для работы с базой
 */
class mysql_ext
{
	public $colback_func 		= array();
	public $colback_func_param = 'first';
	public $sql_obj= NULL; // обьект запроса

	public $cacheFoundRows = false;
	
	function __construct()
	{

	}

	/**
	 * Метод добавляет название поля в специальный массив для последующей обработки этого поля.
	 * В качестве параметра может быть либо массив либо строка.
	 * Если массив, то ключи должны быть в строгой числовой последовательности начиная с нуля, где каждому ключу соответствуюет название поля
	 * Для каждого поля ключ служет ссылкой на ключ в массиве функций для обработки этого поля назначеной при помощи add_fields_func
	 *
	 * Каждое переданное поле в массиве, при вызове функции get_rows обрабатывается назначеной функцией. см. описание функции add_fields_func.
	 */
	public function add_fields_deform($param)
	{
		if(is_array($param))
			{
			$this->colback_func['_fields']= $param;
			}
			else
				{
				$this->colback_func['_fields'][0]= $param;
				}
	}
	
	public function replace(array $param) {
		foreach($param as $field=>$function) {
			$this->colback_func['_fields'][] = $field;
			reset($this->colback_func['_fields']);
			end($this->colback_func['_fields']);
			$currentElement = key($this->colback_func['_fields']);
			$functionExtended = explode(',',$function);		
			$this->colback_func[$currentElement]['func'] = $functionExtended[0];
			if(count($functionExtended) == 1) {
				$this->colback_func[$currentElement]['param']= 0;
			} elseif(count($functionExtended) == 2) {
				$this->colback_func[$currentElement]['param']= str_replace('$this', '$_row', $functionExtended[1]);
			} elseif(count($functionExtended) > 2) {
				$params = $functionExtended; unset($params[0]);
				$params = implode(',',$params);
				$this->colback_func[$currentElement]['param']= str_replace('$this', '$_row', $params);
			}
		}
	}
	
	/**
	 * Синоним add_fields_deform
	 *
	 * @param unknown_type $param
	 */
	public function deform($param)
	{
		$this->add_fields_deform($param);
	}

	/**
	 * Метод добавляет название функции для обработки поля. Принимает в качестве параметра либо строку с названием функции (без скобок и параметров) либо массив
	 * Где каждый индекс массива соответствует индексу ссылающимуся на индекс с содержанием поля добавленого при помощи add_fields_deform
	 *
	 * Метод возвращает значение поля обработаное переданой функцией.
	 */
	public function add_fields_func($param)
	{
		if(is_array($param))
			{
			foreach($param as $k => $v)
				{
				$tmp= explode(',', $v);
				$this->colback_func[$this->colback_func['_fields'][$k]]['func']= $tmp[0];

				if(count($tmp)> 2)
					{
					foreach($tmp as $kk => $vv)
						{
						if($kk!= 0)
							{
							$param_str.= $vv. ",";
							}
						}

					$this->colback_func[$this->colback_func['_fields'][$k]]['param']= str_replace('$this', '$_row', substr($param_str, 0, strlen($param_str)- 1));
					}
					elseif(count($tmp)== 1)
						{
						$this->colback_func[$this->colback_func['_fields'][$k]]['param']= 0;
						}
						elseif(count($tmp)== 2)
							{
							$this->colback_func[$this->colback_func['_fields'][$k]]['param']= str_replace('$this', '$_row', $tmp[1]);
							}
					unset($tmp, $param_str);
				}
			}
			else
				{
				$tmp= explode(',', $param);
				$this->colback_func[0]['func']= $tmp[0];
				if(count($tmp)> 2)
					{
					foreach($tmp as $k => $v)
						{
							if($k!= 0)
								{
								$param_str.= $v. ",";
								}
						}

					$this->colback_func[0]['param']= str_replace('$this', '$_row', substr($param_str, 0, strlen($param_str)- 1));
					}
					elseif(count($tmp)== 1)
						{
						$this->colback_func[0]['param']= 0;
						}
						elseif(count($tmp)== 2)
							{
							$this->colback_func[0]['param']= str_replace('$this', '$_row', $tmp[1]);
							}
				}
	}

	/**
	 * Синоним add_fields_func
	 *
	 * @param unknown_type $param
	 */
	public function callback($param)
	{
		$this->add_fields_func($param);
	}
	
	public function callbackUseParram($param = 1)
	{
		$this->colback_func_param = $param;
	}

	
	/**
	 * Метод добавляет / изменяет данные в таблице $table из массива $data.
	 * В качестве условия передается $if_key_then_update
	 * Если в массиве $data будет найден индекс $if_key_then_update и он будет больше 0 то выполнится запрос UPDATE иначе будут выполнятся запросы INSERT
	 *
	 * Метод возвращает многомерный массив
	 *
	 * array['errors'] = (int) число ошибочных запросов
	 * array['updated'] = (int) число успешных запросов
	 * array[inserts_id] = array[index] = (int) id вставленного поля, где index соответствует индексу в передаваеммом массиве $data
	 */
	public function autoupdate()
	{
		$this->sql_obj= new db_query_autoupdate();
		return $this->sql_obj;
	}

	/**
	 * Метод генерирует запрос типа select из класа select
	 */
	public function select()
	{
		$this->sql_obj= new db_query_select();
		return $this->sql_obj;
	}

	/**
	 * Метод выполняет текущий згенерированый обьектный запрос
	 */
	public function execute($db_name= 'db_site')
	{
		if($this->sql_obj)
			{
			$sql = $this->sql_obj->exec();
			if($db_name)
				{
				foreach($sql as $_query)
					{
					$this->query($_query, $db_name);
					$inserts_id[] = $this->insert_id;
					}
				}
				
			return $inserts_id;
			}
			else
				{
				return false;
				}
		unset($this->sql_obj);
	}

	/**
	 * Метод выводит отладку текущего запроса
	 */
	public function debug()
	{
		print_r($this->sql_obj);
		
		
		
	}
	
	/**
	 * Метод возвращает количество полей который вернул бы предыдущий запрос без LIMIT
	 */
	public function found_rows()
	{
		if($this->cacheFoundRows !== false) {
			$cnt = $this->cacheFoundRows;
			$this->cacheFoundRows = false;
			return $cnt;
		}
		
		$sql= "SELECT FOUND_ROWS() AS cnt_rows";
		$this->query($sql, $this->lastQueryDatabase);
		$amount= $this->get_field('cnt_rows');
		return $amount;
	}

	/**
	 * Метод удаляет все записи из таблицы $table где первичный ключ с именем $value_key равен значению $value
	 * По умолчанию название первичного ключа устанавливается как 'id';
	 */
	public function delete($table, $value, $value_key= nill)
	{
		if(!is_array($value))
		{
			$where = '`'. $value_key. '` = "'. $value. '"';
		}
		else
		{
			$where = '';
			foreach($value as $field=>$data)
			{
				$where .= ' `'. $field. '` = "'. $data. '" and';
			}
		}
		$where = (substr($where,-3)=='and')?substr($where,0,-3):$where;



		$sql= 'DELETE FROM `'. $table. '` WHERE '.$where;
		$this->query($sql);
	}

	/**
	 * Метод блокирует таблицу на запись (второй параметр w) / на чтение (второй параметр r)
	 */
	public function lock($table, $mode)
	{
		$sql  = 'LOCK TABLE `'.$table.'` ';
		$sql .= ($mode == 'w')? 'WRITE':'READ';
		$this->write_log = 0;
		$this->query($sql);
		$this->write_log = 1;
	}

	/**
	 * Метод разблокирует таблицу заблокированую запросом LOCK TABLE
	 */
	public function unlock($table)
	{
		$sql  = 'LOCK TABLE `'.$table.'`';
		$this->write_log = 0;
		$this->query($sql);
		$this->write_log = 1;
	}
	
	public function sql_filters(array $filters)
	{
		$sql = array();
		
		foreach($filters as $fieldName=>$fieldValue)
		{

			if($fieldName == '!')
			{
				foreach ($fieldValue as $customWhere)
				{
					$sql[] = $customWhere;
				}
				
				unset($filters[$fieldName]);
			}
			else
			{
				if(substr($fieldValue, 0, 1) == '~')
				{
					$fieldValue = substr($fieldValue, 1);
				}
				
				if(substr($fieldValue, 0, 5) == '$LIKE')
				{
					$fieldValue = 'LIKE "'.substr($fieldValue, 5).'"';
				}
				elseif(substr($fieldValue, 0, 1) == '!')
				{
					$fieldValue = substr($fieldValue, 1);
					$fieldValue = ' != '.$fieldValue.'';
				}
				
				elseif(substr($fieldValue, 0, 1) == '>')
				{
					$fieldValue = substr($fieldValue, 1);
					$fieldValue = '> '.$fieldValue.'';
				}
				
				elseif(substr($fieldValue, 0, 1) == '<')
				{
					$fieldValue = substr($fieldValue, 1);
					$fieldValue = '< '.$fieldValue.'';
				}
				else
				{
					$fieldValue = '= '.$fieldValue.'';
				}
				
				$sql[]= " ".$fieldName." ".$fieldValue." ";
			}
				
			
		}
		
		return implode(" AND ", $sql);		
	}
}


/**
 * Класс для работы с базой через библиотеку mysql
 *
 * Класс наследует методы класса mysql_ext
 */
class mysql extends mysql_ext implements mysql_interface
{

	public $result= NULL;
	public $rows= array();
	public $num_rows= 0;
	public $write_log= 1;
	public $calc_rows = 0;
	public $insert_id = 0;
	public $err_num = 0;
	public $error= 0;
	public $connects= array();
	public $lastQueryDatabase = false;
	
	private $config= array();
	private $connect_id= NULL;
	private $sql = '';
	private $curentDb = 'db_site';

	

	function __construct(array $config)
	{
		$this->config = $config;
		$this->write_log = ($this->config['db_logs']['sql_error'])? 1 : 0;
		$this->write_log = ($this->config['db_logs']['sql_data'])? 2 : $this->write_log;
	}

	/**
	 * Метод Устанавливает соединение с sql сервером, возвращает ссылку на соединение и аписывает ее в $this->connect_id
	 */
	public function connect($config= 0, $db_name= 'db_site')
	{
		if($config) $this->config = $config;
		
		$this->connects[$db_name]= NULL;

		if(isset($this->config[$db_name])) {
			$this->connects[$db_name] = @mysql_connect($this->config[$db_name]['dbhostname'], $this->config[$db_name]['dbusername'], $this->config[$db_name]['dbpass']);
			
			if(!$this->connects[$db_name]) {
				core::fatal_error('Ошибка подключения к базе данных.', 'Не удалось подключиться к серверу базы данных. Были использованны следующие настройки: [hostname: <b>'.$this->config[$db_name]['dbhostname'].'</b>] [username: <b>'.$this->config[$db_name]['dbusername'].'</b>]</p><hr> <p>'.mysql_error());
			}
			
			$this->select_db($db_name);
			$this->error = mysql_error($this->connects[$db_name]);
	
			if($this->error == false) {
				$sql= "SET NAMES utf8";
				@mysql_query($sql, $this->connects[$db_name]);
			} else {
				core::fatal_error('Ошибка подключения к базе данных.', 'Подключение было установленно, однако выбрать базу данных <b>`'.$this->config[$db_name]['dbname'].'`</b> не удалось</p><hr><p>ERROR: '.$this->error);
			}

			return $this->connects[$db_name];
		} else {
				core::fatal_error('Ошибка подключения к базе данных.', 'Не найдена конфигурация к указанной базе данных: <b>'.$db_name.'<b>');
		}
	}

	/**
	 * Метод устанавливает рабочую бд
	 *
	 */
	public function select_db($db_name)
	{
		@mysql_select_db($this->config[$db_name]['dbname'], $this->connects[$db_name]);
		$this->curentDb = $db_name;
	}
	
	/**
	 * Метод возвращает результат запроса переданного в качестве параметра $sql
	 * Также дублирует полученый результат в переменную $this->result
	 *
	 * Если установлен второй параметр то метод работает как статический (для связи классов внутри класса core)
	 */
	public function query($sql, $db_name= 'db_site')
	{
		$core = core::$instance;
		$this->result= NULL;

		if(!$this->connects[$db_name]) $this->connect($this->config, $db_name);
		if($this->curentDb != $db_name) $this->select_db($db_name);
		
		$this->sql = $sql;
		$queryTime = 0;
		$trace = '';
		
		if(isset($core->CONFIG['console']['db']) && isset($core->CONFIG['console']['db']['query_time']) && $core->CONFIG['console']['db']['query_time']) {
			$core->log->timeLine();
		}
		
		$this->result= mysql_query($sql, $this->connects[$db_name]);
		if(isset($core->CONFIG['console']['db']) && isset($core->CONFIG['console']['db']['query_time']) && $core->CONFIG['console']['db']['query_time']) {
			$queryTime = $core->log->timeLine();
		}
		
		if(isset($core->CONFIG['console']['db']) && isset($core->CONFIG['console']['db']['call_trace']) && $core->CONFIG['console']['db']['call_trace']) {
			$trace = $core->log->sqlTrace();
		}
		
		$this->error = trim(mysql_error($this->connects[$db_name]));
		$this->err_num = intval(mysql_errno($this->connects[$db_name]));

		
		if($this->write_log != -1)
		{		
			if($this->err_num > 0)
			{
				$core->log->addQuery($sql, mysql_error($this->connects[$db_name]), mysql_errno($this->connects[$db_name]), $queryTime, $trace);
			}
			else
				{
					$core->log->addQuery($sql, '', 0, $queryTime, $trace);
				}
		}

		//if(preg_match('/^INSERT/', $sql))
		if(is_bool($this->result) && $this->result === true)
		{
				$this->insert_id = mysql_insert_id($this->connects[$db_name]);
		}

		$this->lastQueryDatabase = $db_name;
		
		return $this->result;
	}

	/**
	 * Метод метод аналогичен query но первый не выполняет обработку ошибок и не пищет лог
	 */
	public function s_query($sql, $db_name= 'db_site')
	{
	$this->result = mysql_query($sql, $this->connects[$db_name]);
		
	//$this->num_rows = @mysql_num_rows($this->result);
	return $this->result;
	}

	/**
	 * Метод закрывает соединение с базой
	 */
	public function close($db_name= 'db_site')
	{
		if(isset($this->connects[$db_name]))
			{
				mysql_close($this->connects[$db_name]);
			}
		$this->connects[$db_name]= NULL;
	}

	/**
	 * Метод выводит массив строк из базы полученный путем mysql_fetch_array но за вычетом повторных результатов с числовыми ключами
	 * Если массив colback_func не пустой, то добавленые поля обрабатываются см. описание функций add_fields_deform и  add_fields_func
	 * если параметр не передан то будет выполнятся проверка наличия функций обработки и обработка полей
	 *
	 * Возвращает многомерный массив вида array[key][field] = value
	 * Где: key - индекс записи (индексируется начиная с нуля), field - название поля в таблице базы, value - значение поля
	 * Также результат записывается в переменную $this->rows
	 */
	public function get_rows($one_row = 0, $BaseKeySet = '', $onefield = false)
	{
		unset($this->rows);
		$this->rows = array();
		
		if($one_row)
		{
			$row = @mysql_fetch_assoc($this->result);

			if($row)
			{
				foreach($row as $k => $v)
				{
					if(is_array($this->colback_func) && count($this->colback_func)&& count(array_keys($this->colback_func['_fields'], $k))> 0&& $k!== 0)
					{
						if(trim($this->colback_func[$k]['func']) == '__unset__')
						{
							unset($_row[$k]);
							continue;
						}
						
						if($this->colback_func_param== "first")
						{
							$func= ($this->colback_func[$k]['param']) ? trim($this->colback_func[$k]['func']). '('. trim($this->colback_func[$k]['param']). ', \''. addslashes($v). '\');' : trim($this->colback_func[$k]['func']). '(\''.  addslashes($v). '\');';
						}
						else
							{
								$func= ($this->colback_func[$k]['param']) ? trim($this->colback_func[$k]['func']). '(\''. addslashes($v). '\', '. trim($this->colback_func[$k]['param']). ');' : trim($this->colback_func[$k]['func']). '(\''.  addslashes($v). '\');';
							}
						$func = str_replace('$_row', $v, $func);
						
						try 
							{ 
								eval('$_row[$k]  = '. $func. ';'); 
							} 
							catch (Exception $e)
								{
									$_row[$k]  = NULL;	
								}
						
					}
					else
						{
							$_row[$k]= $v;
						}
				}
			}

			if(isset($_row)) $this->rows = $_row;

			return $this->rows;
		}
		else
			{
				if(!$this->result) return false;
				while($row = @mysql_fetch_assoc($this->result))
				{
					foreach($row as $k => $v)
					{
						if(isset($this->colback_func) && is_array($this->colback_func) && count($this->colback_func)&& count(array_keys($this->colback_func['_fields'], $k))> 0&& $k!== 0)
						{
							if(trim($this->colback_func[$k]['func']) == '__unset__')
							{
								unset($_row[$k]);
								continue;
							}
							
							if($this->colback_func_param== "first")
							{
								$func= ($this->colback_func[$k]['param']) ? trim($this->colback_func[$k]['func']). '('. trim($this->colback_func[$k]['param']). ', \''. addslashes($v). '\');' : trim($this->colback_func[$k]['func']). '(\''.  addslashes($v). '\');';
							}
							else
								{
									$func= ($this->colback_func[$k]['param']) ? trim($this->colback_func[$k]['func']). '(\''. addslashes($v). '\', '. trim($this->colback_func[$k]['param']). ');' : trim($this->colback_func[$k]['func']). '(\''.  addslashes($v). '\');';
								}

							$func = str_replace('$_row', $v, $func);
							
							try { 
									eval('$_row[$k]  = '. $func. ';'); 
								} 
								catch (Exception $e)
									{$_row[$k]  = NULL;}

						}
						else
							{
								$_row[$k]= $v;
							}
					}

					if($BaseKeySet && isset($_row[$BaseKeySet])) 
					{
						if(isset($ret[$_row[$BaseKeySet]]))
						{
							if(!isset($ret[$_row[$BaseKeySet]][0]))
							{
								$_tmp_old_row = $ret[$_row[$BaseKeySet]];
								$ret[$_row[$BaseKeySet]] = array();
								$ret[$_row[$BaseKeySet]][0] = $_tmp_old_row;
							}
								
							$ret[$_row[$BaseKeySet]][count($ret[$_row[$BaseKeySet]])] = $_row;
								
							unset($_tmp_old_row);
						}
						else 
							{
								$ret[$_row[$BaseKeySet]]= $_row;
							}
							
					}	
					else
						{
							if($onefield && isset($_row[$onefield]))
							{
								
								$ret[]= $_row[$onefield];
							}
							else 
								{
									$ret[]= $_row;
								}
						}
				}
					
				
				if(!isset($ret) || !is_array($ret))
				{
					$this->rows = array();
				}
				else 
					{
						$this->rows = $ret;
					}
					
				$this->colback_func = false;
				$this->colback_func_param = false;
				unset($ret, $func, $row, $_row);
				return $this->rows;
			}
	}

	/**
	 * Метод возвращает одну либо несколько ячеек
	 * Первый параметр - номер поля (начиная с нуля) или название, второй параметр - номер строки (начиная с нуля). если не указать второй параметр то будет выводится ячейка для первой строки
	 * Третий необязательный параметр - фильтр данных на выводе. Тип строковый, доступные параметры:
	 *			int 	- фильтрует выводимые данные как число (в случае если преобразуеммая строка больше 9 символов, вывод всеравно будет коректен // обход 2147483647)
	 *			bool	- выводит булевские данные в строгом соответствии, для сравнения ===
	 *			slashes - удаляет все слешы добавленные функцией addslashes из строки
	 *			float	- фильтрует выводимые данные по типу float
	 */
	public function get_field($field= 0, $row= 0, $date_filter= 0)
	{
		$this->result= @mysql_result($this->result, $row, $field);
		if($date_filter)
			{
				switch($date_filter)
				{
					case 'int':
						if(strlen($this->result)> 9)
							{
								$ret= 0;
								for($i= 0; $i!= strlen($this->result); $i+ 9)
									{
										$ret.= intval(substr($this->result, $i));
									}
								$this->result= $ret;
							} else
							{
								$this->result= intval($this->result);
							}
						break;
					case 'bool':
						$this->result= ($this->result) ? true : false;
						break;
					case 'slashes':
						$this->result= stripslashes($this->result);
						break;
					case 'float':
						$this->result= floatval($this->result);
						break;
				}
			}
		return $this->result;
	}

	/**
	 * Метод высвобождает всю память занятую выполненым запросом
	 */
	public function free_result()
	{
		if($this->result)
			{
				unset($this->result, $this->rows);
				return mysql_free_result($this->result);
			} else
			{
				return NULL;
			}
	}

	public function result_map($func, $assoc = true) {
		if(is_callable($func)) {
			$localResult = $this->result;
			
			if($assoc) {
				while($row = mysql_fetch_assoc($localResult)) {
					$func($row);
				}
			} else {
				while($row = mysql_fetch_row($localResult)) {
					$func($row);
				}
			}
			
			unset($localResult);
		}
	}
	
	/**
	 * Метод возвращает количество строк которые вернул предыдущий запрос
	 */
	public function num_rows()
	{
		if($this->result)
			{
				return mysql_num_rows($this->result);
			} else
			{
				return NULL;
			}
	}

	/**
	 * Метод возвращает id последнего вставленого поля методом INSERT
	 */
	public function last_id()
	{
		return mysql_insert_id();
	}

	/**
	 * Метод записывает логи запросов
	 */
	public function write_log($sql)
	{
		global $core;
		
		$sql = str_replace(array("\n" , "\r"), array(' ' , ' '), $sql);
		$text = ($this->error)? '##~ACCESS IN DB.##~SQL : '.$sql : '##~DB ERROR.##~ERROR TEXT : '.$this->error.'##~SQL : '.$sql; 
		$sql= "INSERT INTO `mcms_logs_error` (`gr_name`, `ip`, `text`, `date`, `user_id`) VALUES ('sql', '". $_SERVER['REMOTE_ADDR']. "', '". addslashes($text). "', ". time(). ", ".$core->user->id.")";
		@mysql_query($sql);
	}

	public function get_fields_names($tablename, $db_name= 'db_site')
	{
		$fields = @mysql_list_fields($this->config[$db_name]['dbname'], $tablename, $this->connects[$db_name]);
		$columns = @mysql_num_fields($fields);

		for ($i = 0; $i < $columns; $i++)
			{
			$field_names[] = @mysql_field_name($fields, $i);
			}

		return $field_names;
	}

	public function last_query()
	{
		return $this->sql;
	}

	public function last_query_object()
	{
		return $this->sql_obj;
	}
	
	public function info()
	{
		$info['server']=mysql_get_server_info();
		$info['proto']=mysql_get_proto_info();
		$info['host']=mysql_get_host_info();
		$info['client']=mysql_get_client_info();
		$info['status']=explode('  ', mysql_stat($this->connects['db_site']));

		return $info;
	}
	
	public function get_db_name($db_name= 'db_site') {
		return $this->config[$db_name]['dbname'];
	}
	
	/**
	 * Метод првоеряет, существует ли запись в таблице $table с ключем ($feild_name) равным значению $field_val
	 * Возвращает 1 либо 0
	 */
	static function row_exists($table, $feild_name, $field_val=0)
	{
		if(is_array($feild_name))
			{

				$_where = 0;

				foreach($feild_name as $_exit_field => $_exit_value)
					{
					if($_where)
						{
						$_where .= ' AND `'.$_exit_field.'` = "'.$_exit_value.'"';
						}
						else
							{
							$_where = 'WHERE `'.$_exit_field.'` = "'.$_exit_value.'"';
							}
					}

				$sql = 'SELECT * FROM `'. $table. '` '.$_where;
				
				
			}
			else
				{
					$sql = 'SELECT * FROM `'. $table. '` WHERE `'. $feild_name. '`= "'.$field_val.'"';
				}
				
				
		$res= mysql_query($sql);
		
		return (mysql_num_rows($res))?1:0;

	}
	
	static function str($string) {
		return mysql_real_escape_string($string);
	}

	static function int($integer) {
		return intval($integer);
	}


}


/**
 * Класс для работы с базой через библиотеку mysqlI
 *
 * Класс наследует методы класса mysql_ext
 */
class imysql extends mysql_ext implements mysql_interface
{
	public $result		= null;
	public $rows		= array();
	public $num_rows	= 0;
	public $write_log	= 1;
	public $calc_rows 	= 0;
	public $insert_id 	= 0;
	public $err_num 	= 0;

	private $error		= 0;
	private $config		= array();
	private $connects	= array();
	private $connect_id	= null;



	/**
	 * Метод Устанавливает соединение с sql сервером, возвращает ссылку на соединение и аписывает ее в $this->connect_id
	 */
	public function connect($config = 0, $db_name= 'db_site')
	{


		if(!$this->config) $this->config = $config;

		$this->connects[$db_name]= mysqli_init();

		if(isset($this->config[$db_name]))
			{
			$conection = mysqli_real_connect($this->connects[$db_name], $this->config[$db_name]['dbhostname'], $this->config[$db_name]['dbusername'], $this->config[$db_name]['dbpass'],  $this->config[$db_name]['dbname']) or die('Can not connect to mysql server.<br>');

			$this->error = mysqli_error($this->connects[$db_name]);

			return $this->connects[$db_name];
			}
			else
				{
				die(iconv('cp1251', 'utf-8','Ошибка запроса к базе. Не найдена конфигурация к указанной базе данных: <b>'.$db_name.'<b>'));
				}
	}

	/**
	 * Метод возвращает результат запроса переданного в качестве параметра $sql
	 * Также дублирует полученый результат в переменную $this->result
	 *
	 * Если установлен второй параметр то метод работает как статический (для связи классов внутри класса core)
	 */
	public function query($sql, $db_name= 'db_site', $mode = 'single')
	{
		global $core;

		$this->result = NULL;

		if(!$this->connects[$db_name]) $this->connect(0, $db_name);

		switch($mode)
			{
			case 'single':
				$this->result= mysqli_query($this->connects[$db_name], $sql);
			break;

			case 'multi';
				$this->result= mysqli_multi_query($this->connects[$db_name], $sql);
			break;

			default:
				$core->trigger_error(iconv('cp1251', 'utf-8', 'Выбранный режим запроса к базе не поддерживается: <b>'.$mode.'<b>'));
				$this->result= mysqli_query($this->connects[$db_name], $sql);
			break;
			}


		$this->error = trim(@mysqli_error($this->connects[$db_name]));
		$this->err_num = intval(@mysqli_errno($this->connects[$db_name]));

		if($this->err_num <1  && $this->result) $this->num_rows= @mysqli_num_rows($this->result);

		if($this->err_num > 0)
			{
			$core->log->addQuery($sql, mysqli_error($this->connects[$db_name]), mysqli_errno($this->connects[$db_name]));
			}
			else
				{
				$core->log->addQuery($sql);
				}

	$this->connect_id = $this->connects[$db_name];

	return $this->result;
	}

	/**
	 * Метод метод аналогичен query но первый не выполняет обработку ошибок и не пищет лог
	 */
	public function s_query($sql, $db_name= 'db_site')
	{
		$this->result= mysqli_query($sql, $this->connects[$db_name]);
		$this->num_rows = @mysqli_num_rows($this->result);
		return $this->result;
	}

	/**
	 * Метод закрывает соединение с базой
	 */
	public function close($db_name= 'db_site')
	{
		if(isset($this->connects[$db_name]))
			{
				mysqli_close($this->connects[$db_name]);
			}
		$this->connects[$db_name]= NULL;
	}

	/**
	 * Метод выводит массив строк из базы полученный путем mysql_fetch_array но за вычетом повторных результатов с числовыми ключами
	 * Если массив colback_func не пустой, то добавленые поля обрабатываются см. описание функций add_fields_deform и  add_fields_func
	 * если параметр не передан то будет выполнятся проверка наличия функций обработки и обработка полей
	 *
	 * Возвращает многомерный массив вида array[key][field] = value
	 * Где: key - индекс записи (индексируется начиная с нуля), field - название поля в таблице базы, value - значение поля
	 * Также результат записывается в переменную $this->rows
	 */
	public function get_rows($one_row = 0)
	{
		unset($this->rows);

		if($one_row)
			{
			$row = @mysqli_fetch_assoc($this->result);

			foreach($row as $k => $v)
				{
				if(count($this->colback_func)&& count(array_keys($this->colback_func['_fields'], $k))> 0&& $k!== 0)
					{
					if($this->colback_func_param== "first")
						{
						$func= ($this->colback_func[$k]['param']) ? trim($this->colback_func[$k]['func']). '('. trim($this->colback_func[$k]['param']). ', \''. addslashes($v). '\');' : trim($this->colback_func[$k]['func']). '(\''.  addslashes($v). '\');';
						}
						else
							{
							$func= ($this->colback_func[$k]['param']) ? trim($this->colback_func[$k]['func']). '(\''. addslashes($v). '\', '. trim($this->colback_func[$k]['param']). ');' : trim($this->colback_func[$k]['func']). '(\''.  addslashes($v). '\');';
							}

					eval('$_row[$k]  = '. $func. ';');
					}
					else
						{
						$_row[$k]= $v;
						}
				}

			$this->rows = $_row;

			return $this->rows;
			}
			else
				{
				while($row= @mysqli_fetch_assoc($this->result))
					{
					foreach($row as $k => $v)
						{
						if(count($this->colback_func)&& count(array_keys($this->colback_func['_fields'], $k))> 0&& $k!== 0)
							{
							if($this->colback_func_param== "first")
								{
								$func= ($this->colback_func[$k]['param']) ? trim($this->colback_func[$k]['func']). '('. trim($this->colback_func[$k]['param']). ', \''. addslashes($v). '\');' : trim($this->colback_func[$k]['func']). '(\''.  addslashes($v). '\');';
								}
								else
									{
									$func= ($this->colback_func[$k]['param']) ? trim($this->colback_func[$k]['func']). '(\''. addslashes($v). '\', '. trim($this->colback_func[$k]['param']). ');' : trim($this->colback_func[$k]['func']). '(\''.  addslashes($v). '\');';
									}

							eval('$_row[$k]  = '. $func. ';');
							}
							else
								{
								$_row[$k]= $v;
								}
						}

					$ret[]= $_row;
					}
				while (mysqli_next_result($this->connect_id));

				$this->rows = $ret;
				$this->colback_func = $this->colback_func_param = false;
				unset($ret, $func, $row, $_row);

				return $this->rows;
				}
	}

	/**
	 * Метод возвращает одну либо несколько ячеек
	 * Первый параметр - номер поля (начиная с нуля) или название, второй параметр - номер строки (начиная с нуля). если не указать второй параметр то будет выводится ячейка для первой строки
	 * Третий необязательный параметр - фильтр данных на выводе. Тип строковый, доступные параметры:
	 *			int 	- фильтрует выводимые данные как число (в случае если преобразуеммая строка больше 9 символов, вывод всеравно будет коректен // обход 2147483647)
	 *			bool	- выводит булевские данные в строгом соответствии, для сравнения ===
	 *			slashes - удаляет все слешы добавленные функцией addslashes из строки
	 *			float	- фильтрует выводимые данные по типу float
	 */
	public function get_field($field= 0, $row= 0, $date_filter= 0)
	{
		$this->result= @mysqli_result($this->result, $row, $field);
		if($date_filter)
			{
			switch($date_filter)
				{
				case 'int':
					if(strlen($this->result)> 9)
						{
						$ret= 0;
							for($i= 0; $i!= strlen($this->result); $i+ 9)
								{
								$ret.= intval(substr($this->result, $i));
								}
							$this->result= $ret;
						}
						else
							{
							$this->result= intval($this->result);
							}
				break;

				case 'bool':
					$this->result= ($this->result) ? true : false;
				break;

				case 'slashes':
					$this->result= stripslashes($this->result);
				break;

				case 'float':
					$this->result= floatval($this->result);
				break;
				}
			}
		return $this->result;
	}

	/**
	 * Метод высвобождает всю память занятую выполненым запросом
	 */
	public function free_result()
	{
		if($this->result)
			{
			unset($this->result, $this->rows);
			return mysqli_free_result($this->result);
			}
			else
				{
				return NULL;
				}
	}

	/**
	 * Метод возвращает количество строк которые вернул предыдущий запрос
	 */
	public function num_rows()
	{
		if($this->result)
			{
				return mysqli_num_rows($this->result);
			} else
			{
				return NULL;
			}
	}

	/**
	 * Метод возвращает id последнего вставленого поля методом INSERT
	 */
	public function last_id()
	{
		return mysqli_insert_id();
	}

	/**
	 * Метод записывает логи запросов
	 */
	public function write_log($sql)
	{
		$text= str_replace(array("\n" , "\r"), array(' ' , ' '), $sql);
		if($this->error)
			$text.= ' | ERROR: '. $this->error;
		$sql= "INSERT INTO `mcms_logs_error` (`gr_name`, `ip`, `text`, `date`) VALUES ('sql', '". $_SERVER['REMOTE_ADDR']. "', '". addslashes($text). "', ". time(). ")";
		@mysqli_query($sql, $this->connects['db_site']);

	}

}



/**
 * Класс реализует запрос к базе типа SELECT
 */
class db_query_select {

	private $fields= '';
	private $table= '';
	private $where= '';
	private $order= '';
	private $group= '';
	private $limit= '';
	public $sql= array();

	function ___construct(){
	    
	}

	/**
	 * Метод приминяет к запросу таблицу из какой выбирать данные. 
	 * Метод расчитан как на одну таблицу так и на несколько.
	 * Для использования нескольких таблиц, необходимо каждое значение вписывать как параметр и указывать табличные ссылки.
	 * 
	 * Для применения табличных ссылок, можно после названия таблицы через пробел вписать название ссылки (все в одной строке в одном параметре)
	 *
	 * @return db_query_select Object
	 */
	function from() {
		$items = func_get_args();
	
		if(!count($items)) return $this;
		
		foreach($items as $table) {			
			$table = trim($table);
			if(strpos($table, ' ')) {
    			$table_link = trim(str_ireplace('as ', '', substr($table, strpos($table, ' '))));
    			$_table	= trim(substr($table, 0, strpos($table, ' ')));
    			if(strpos($table, ' ')) $_table = trim(substr($_table, strpos($_table, ' ')));       			
    			$_table = "`{$_table}`";
    			if($table_link) $_table .= " AS `{$table_link}`";
    		} else {
				$_table = "`{$table}`";
			}
 			
			$_table = str_ireplace('.', '`.`', $_table);
			$this->table = ($this->table)? $this->table.', '.$_table : $_table;
		    unset($_table, $table_link);
		}	

		return $this;
	}

	/**
	 * Метод назначает какие колонки выбрать.
	 * Возможно использовать с несколькими колонками. Для этого необходимо их вписывать по одной как параметры.
	 * 
	 * Если необходимо использовать табличные ссылки, нужно после названия колонки через пробел вписать название ссылки.
	 * Если необходимо выбрать все колонки, из нескольких таблиц, то таблицам 
	 * назначаются ссылки (см. метод from) и в качестве параметров передается строка такого вида: link.*
	 * Если необходимо выбрать все колонки только из одной таблицы то метод вызывается без параметров, 
	 * либо в качестве единственного параметра вписывается $all
	 *   
	 * Если вызвать метод но не передать параметр, то будет вставлено в запрос "WHERE 1"
	 * 
	 * @return db_query_select Object
	 */
	function fields() {
		$fields= func_get_args();
		
		if(!count($fields) || $fields[0] == '$all' || $fields[0] == '*') {
			$this->fields = '*';	
			return $this;
		}
		
		foreach($fields as $field) {
			$field_link = '';
		
			if(is_array($field)) {
			   foreach($field as $_field) { 
			       if(strpos($_field, ' ')) {
    					$field_link = trim(str_ireplace('as ', '', substr($_field, strpos($_field, ' '))));
    					$_field	= trim(substr($_field, 0, strpos($_field, ' ')));
    					if(strpos($field, ' ')) $_field = trim(substr($_field, strpos($_field, ' ')+1));
    				}
    				
    				if(strpos($_field, '.') === false) $_field = "`{$_field}`";
    				if($field_link) $_field .= " AS `{$field_link}`";
    				$this->fields .= $_field. ", ";
			   }   
			} else {
		    	if(strpos($field,'$count')!== false) {
					$_cnt_from = (strpos($field, ' '))? trim(substr($field, 0, strpos($field, ' '))) : '*';
					
					$this->fields .= 'COUNT('.$_cnt_from.') as `count`, ';
					continue;
		    	}
		    	
		    	if(strpos($field, ' ')) {
					$field_link = trim(str_ireplace('as ', '', substr($field, strpos($field, ' '))));
					$field		= trim(substr($field, 0, strpos($field, ' ')));
					$field 		= (strpos($field, ' '))? trim(substr($field, strpos($field, ' ')+1)) : $field;
				}
				
				if(strpos($field, '.') === false) $field = "`{$field}`";
				if($field_link) $field .= " AS `{$field_link}`";
				
				$this->fields .= $field. ", ";
		    }
		}
			
		$this->fields= substr($this->fields, 0, strlen($this->fields)- 2);
		return $this;
	}

	/**
	 * Условие WHERE в чистом виде
	 * Если вызвать метод но не передать параметр, то будет вставлено в запрос "WHERE 1"
	 * 
	 * @param string SQL WHERE
	 * @return db_query_select Object
	 */
	function where($str = 0){
		if(!$str) {
			$this->where = '1';
			return $this;
		}
		
		$this->where = ($this->where)? $this->where.' AND '.$str : $str;
		return $this;
	}
	
	function func() {
	    $func = func_get_args();
	    if(!count($func)) return $this;
	    if($this->fields) $this->fields .= ", ";
	    
	    foreach($func as $f) {
	        if(is_array($f)) {
	            foreach($f as $_f) {
                    if(strpos($_f, ' ')) {
                        $field_link = trim(str_ireplace('as ', '', substr($_f, strpos($_f, ' '))));
                        $_f	= trim(substr($_f, 0, strpos($_f, ' ')));
                        if(strpos($_f, ' ')) $_f = trim(substr($_f, strpos($_f, ' ')+1));
                    }

    				if($field_link) $_f .= " AS `{$field_link}`";
    				$this->fields .= $_f.", ";
	            }   	     
	        } else {
                if(strpos($f, ' ')) {
        		  $field_link = trim(str_ireplace('as ', '', substr($f, strpos($f, ' '))));
        		  $f = trim(substr($f, 0, strpos($f, ' ')));	
        		  if(strpos($f, ' ')) $f = trim(substr($f, strpos($f, ' ')+1));
        		}
        		
    			if($field_link) $f .= " AS `{$field_link}`";
    			$this->fields .= $f.", "; 
	            
	        }    
	    }
	    
	    $this->fields= substr($this->fields, 0, strlen($this->fields)- 2);
	    return $this;
	}
	
	function group($group) {
		$this->group= $group;
		return $this;
	}

	function order($order, $order_type=0) {
		if($order_type) {
		  $this->order= "`".$order."` ".$order_type;
		} else {
			$this->order = $order;
		}

		return $this;
	}

	function limit($limit, $start= 0) {
		$this->limit= $start. ','. $limit;
		return $this;
	}

	function lang($lang_id='') {
		if($lang_id == '$curent' || $lang_id == '') {
			global $core;
			$this->where = ($this->where)? $this->where.' AND `lang_id` ='.$core->CONFIG['lang']['id']: '`lang_id` ='.$core->CONFIG['lang']['id'];
		} elseif (intval($lang_id)) {					
			$this->where = ($this->where)? $this->where.' AND `lang_id` ='.$lang_id: '`lang_id` ='.$lang_id;
		}		

		return $this;
	}
	
	function exec() {
		$_sql = 'SELECT '. $this->fields. ' FROM '. $this->table;

		if($this->where) $_sql .= ' WHERE '. $this->where;
		if($this->group) $_sql .= ' GROUP BY '. $this->group;
		if($this->order) $_sql .= ' ORDER BY '. $this->order;
		if($this->limit) $_sql .= ' LIMIT '. $this->limit;

		$this->sql[] = $_sql;
		
		return $this->sql;
	}
}


/**
 * Класс реализует запрос к базе типа INSERT и UPDATE
 */
class db_query_autoupdate
{

	private $table= '';
	private $data= array();
	private $primary= array();
	private $dbname = 'db_site';
	public $sql= array();

	function ___construct(){
	    
	}

	function table($str) {
		$this->table= $str;
		return $this;
	}

	function data($data) {
		$this->data= $data;
		return $this;
	}

	function primary() {
		$fields = func_get_args();

		foreach($fields as $field) $this->primary[$field] = $field;

		return $this;
	}
	
	function db($dbname) {
		$this->dbname = $dbname;
		
		return $this;
	}

	function exec() {
		foreach($this->data as $arr_fields) {
			$update = 0;
			$exec_chek = 0;

			if(count($this->primary) > 0) {
				foreach($this->primary as $primary) {	
					if(array_key_exists($primary, $arr_fields)) {
						$_exit_check[$primary] = $arr_fields[$primary];
						$exec_chek = 1;
					} else {
						$update = 0;
						$exec_chek = 0;
						break;
					}
				}

				if($exec_chek)	$update = mysql::row_exists($this->table, $_exit_check);
			} else {
				$update = 0;
			}

			if($update) {
				$where = '';
				$sql= "UPDATE `". $this->table. "` SET ";
				foreach($arr_fields as $field_name => $field_value) {
					if(array_key_exists($field_name, $this->primary)) {
						if($where) {
							$where .= ' AND `'. $field_name.'` = \''.$field_value.'\'';
						} else {
							$where = ' WHERE `'. $field_name.'` = \''.$field_value.'\'';
						}
					}
							
					$sql.= '`'. $field_name. '` = '."'".$field_value."'";
					$sql.= ', ';		
				}
				
				$sql= substr($sql, 0, strlen($sql)- 2);
				$sql.= $where. "; \n";
			} else {
				$sql= "INSERT INTO `". $this->table. "` ";
						
				$fields = '';
				$inserts = '';
						
				foreach($arr_fields as $field_name => $field_value) {
					$fields .= '`'. $field_name. '`, ';
					$inserts.= (is_string($field_value)) ? "'".$field_value."'" : $field_value+ 0;
					$inserts.= ', ';
				}
				
				$fields= substr($fields, 0, strlen($fields)- 2);
				$inserts= substr($inserts, 0, strlen($inserts)- 2);
				$sql.= "($fields) VALUES ($inserts);\n";
				unset($fields, $inserts);
			}
			
			$this->sql[] = $sql;
			unset($sql);
		}
		
		return $this->sql;
	}
}
?>