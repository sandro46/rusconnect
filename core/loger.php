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
 * Базовый класс. Входит в состав ядра. Cодержит методы для записи и отображения логов
 */
class loger
{
	public $tpl_fetch= array();
	public $tpl_compil= array();
	public $cached_tpl= array();
	public $queries= array();
	public $access_status = 0;
	public $access_enable = 1;
	public $criticalError = array();
	public $syslogShowed = false;
	
	// флаг указывающий на статус записи лога пользователя за текущую сессию
	public $accesLoged = false;

	private $conf= array();
	private $logstart= array();
	private $module_log= array();
	private $logend= array();
	private $timeline = false;
	private $load_log = array();
	
	

	/**
	 * Конструктор класса, в качестве параметра принимает массив с конфигурационной информацией
	 *
	 * @param array $config
	 */
	function __construct($config)
	{
		$this->conf= $config;
		$this->access_enable = $config['db_logs']['access'];
		$this->startTime();
	}

	/**
	 * Метод возвращает пиковое значение использования оперативной памяти скриптом в мегабайтах
	 *
	 * @return string
	 */
	public function dump_mem()
	{
	
		$bite= @memory_get_peak_usage();
		//if($bite) $bite = 1024;
		$mb= round((($bite/ 1024)/ 1024), 4);
		return $mb. ' mb  ( '. parce_digits($bite). ' b )';
	}

	
	
	/**
	 * Метод добавляет в историю запись о выполненом запросе к базе
	 *
	 * @param string $sql_query - выполненый запрос
	 * @param string $error - ошибка которую вернул сервер после выполнения запроса
	 * @param int $errno - номер ошибки которую вернул сервер после выполнения запроса
	 */
	public function addQuery($sql_query, $error= NULL, $errno= NULL, $time=0,$trace = '')
	{
		if(strlen(trim($error)) > 1) {
			
		} else {
			$error = NULL; $errno = NULL;
		}
		
		$this->queries[]= array('sql' => $sql_query , 'error' => $error , 'errno' => $errno, 'time'=>$time, 'trace'=>$trace);
	}
	
	public function getLastSql()
	{
		$last = end($this->queries);
		return $last['sql'];
	}

	/**
	 * Метод добавляет название выполненого шаблона в историю
	 *
	 * @param string $tpl_name - название шаблона
	 */
	public function add_tpl_fetch($tpl_name)
	{
		$this->tpl_fetch[]['tpl']= $tpl_name;
	}

	/**
	 * Метод добавляет название шаблона в историю откомпилированых шаблонов
	 *
	 * @param string $tpl_name - название шаблона
	 */
	public function add_tpl_compil($tpl_name)
	{
		$this->tpl_compil[]= array('tpl' => $tpl_name);
	}

	/**
	 * Метод добавляет в историю название шаблона который выводился из кэша
	 *
	 * @param string $tpl_name - название шаблона
	 * @param string $md5_cache_var - ключ под которым хранится шаблон 
	 */
	public function add_cache_tpl($tpl_name, $md5_cache_var, $expire)
	{
		$this->cached_tpl[]= array('name' => $tpl_name , 'md5_cached' => $md5_cache_var, 'expire'=>$expire);
	}

	/**
	 * Метод выводит статистику кеширования шаблонов при помощи memcache
	 *
	 * @return string html 
	 */
	public function dump_cached()
	{
	global $core;
		$ret= '';
		foreach($this->cached_tpl as $q)
			{
				$ret.= '<div><b>Шаблон:</b> '. $q['name']. ' | Срок жизни: '.$q['expire'].' | Ключ: '.$q['md5_cached'].' / <a href="/utils/memcache_clearId/key/'.$q['md5_cached'].'/ref/'.$_SERVER['REQUEST_URI'].'/">Замочить</a></div>';
			}
		
		if(isset($core->memcache) && $core->memcache instanceof memcache)
		{
			$_memcahce_server_info = $core->memcache->getExtendedStats();
			$ret.= '<br>';
			$ret.= '<div>[Статистика сервера memcache]</div>';
			$ret.= '<div><br></div>';
			
			foreach($_memcahce_server_info as $srver_addres=>$server)
			{
				$ret.= '<div><b>['.$srver_addres.']</b>  | <a href="/ru/-utils/memcache_flush/ref/'.$_SERVER['REQUEST_URI'].'/">Сбористь все данные</a></div><br>';
				foreach($server as $k=>$v)
				{
					$ret.= '<div style="padding-left:15px;">'.$k.' = '.$v.'</div>';
				}
			}	
		}
		else 
			{
				$ret.= '<br><div>[Сервер memcache не включен!]</div><div><br></div>';
			}
			
		return $this->getHtml('Memcache',$ret, false);
	}
	
	public function timeLine($start = false)
	{
		if($start) {
			$start = explode(' ', $start);
			$stop = explode(' ', microtime());
			$out = (floatval($stop[1]) + floatval($stop[0])) - (floatval($start[1]) + floatval($start[0]));
			$out = round($out,5);
			return $out;
		}
		
		
		$out = 0;
		if(!is_array($this->timeline)) {
			$this->timeline = explode(' ', microtime());
		} else {
			$start = $this->timeline;
			$stop = explode(' ', microtime());
			$out = (floatval($stop[1]) + floatval($stop[0])) - (floatval($start[1]) + floatval($start[0]));
			//$out = round($out*100,4);
			$out = round($out,5);
			//$out = $out*10;
			$this->timeline = false;
		}
		
		return $out;
	}
	
	public function dumpIncludes()
	{
		$out = '';
		$files = get_included_files();
		foreach($files as $file) {
			$out .= $file."\n";
		}

		
		return $this->getHtml('Files ('.count($files).')',$out);
	}
	
	/**
	 * Метод создает отчет по выполненым запросам в рамках выполнения скрипта
	 *
	 * @return string - HTML отчета
	 */
	public function dumpQueries()
	{
		$ret= '';
		$allTime = 0;
		foreach($this->queries as $q)
		{
			$sub = "<div>Time: <span style='color:red'>{$q['time']} s</span><br>{$q['trace']}</div>";
			$allTime += $q['time'];
			if(isset($q['error']))
			{
						$ret.= '<div style="color:#ff0000; margin-bottom:4px; border-bottom:solid gray 1px;">'. htmlentities($q['sql']). '<br /><b>Error number:</b> '. $q['errno']. '<br /><b>Error message:</b> '. $q['error']. '<br>'.$sub.'</div>';
			} else
				{
						$ret.= '<div style="margin-bottom:4px; border-bottom:solid gray 1px;">'. htmlentities(str_replace("\\n", '', $q['sql'])).$sub.'</div>';
				}
		}

		$ret.= '<div>Всего: <span style="color:#ff0000;">'. count($this->queries). '</span> SQL Запросов Общее время: <span style="color:#ff0000;">'.$allTime. ' s</span></div>';
		
		return $this->getHtml('SQL',$ret, true, true);
	}

	public function trace()
	{
		return $this->getHtml('Trace',htmlentities(print_r(debug_backtrace(),true)));
	}
	
	/**
	 * Метод формирует отчет о времени генерации страницы
	 *
	 * @return float - время выполнения скрипта в мс
	 */
	public function dumpTime()
	{
		if(!isset($this->logstart)){
				return 0;
		}
		
		$this->logend = explode(' ', microtime());

		
		return (round((((float)$this->logend[1]+ (float)$this->logend[0]) -((float)$this->logstart[1]+ (float)$this->logstart[0])), 4));
	}

	/**
	 * Метод добавляет в лог информацию по модулям
	 *
	 * @param string $param - Название записи
	 * @param string $value - Текст записи
	 */
	public function add_module_log($param, $value, $type = "core", $file='',$message_type='notice')
	{
		if($type == 'php')
		{
			$this->criticalError[] = array('type'=>$type, 'message'=>$value, 'line'=>$param, 'file'=>$file);
			
			return;
		}
		
		$this->module_log['value'][]= $value;
		$this->module_log['param'][]= $param;
		
		if($message_type == 'error')
		{
			$this->criticalError[] = array('type'=>$type, 'message'=>$value);
		}
	}
	
	public function syslog($message, $type='notice', $object='core') {
		if($this->conf['console']['syslog']) {
			$this->load_log[] = array('time'=>$this->timeLine(TIME_START),'message'=>$message,'type'=>$type,'object'=>$object);
		}
	}
		
	public function sys() {
		global $core;
		$ret = '';
		foreach($this->load_log as $item) {
			$time = explode('.',$item['time'].'');
			$time = $time[0].'.'.str_pad($time[1],5, '0', STR_PAD_RIGHT);
			$ret.= "<div><b>[{$time}]</b> - {$item['message']}</div>";
		}
		
		/*
		$html  = '<div style="padding:2px 0px 2px 0px; margin-bottom:6px; background-color:#F8FAFF; " role="info">';
		$html .=  '<div align="left" style="font-size:12px; padding:2px; color:gray; background-color:#ACCDDC">info</div>';
		$html .=  '<pre style="border:solid gray 1px; padding:5px; margin:0px; font-size:10px;">';
		$html .=     '<div><b>PHP version:</b> '.phpversion().'</div>';
		$html .=     '<div><b>MySQL vrsion:</b> '.$core->db->info().'</div>';
		$html .=     '<div><b>Memmory: </b>'.$this->dump_mem().'</div>';
		$html .=     '<div><b>Time: </b>'.$this->dumpTime().'</div>';
		$html .=  '</pre>';
		$html .= '</div>';
		$html .= $this->getHtml('syslog',$ret, false);
		
		
		return $html;
		*/
		
		$this->syslogShowed = true;
		
		return $this->getHtml('syslog',$ret, false);
	}
	
	
	/**
	 * Метод генерирует отчет по модулям 
	 *
	 * @return string - HTML отчета
	 */
	public function dump_module_log()
	{
		$ret= '';
		for($ii= 0; $ii!= count($this->module_log['value']); $ii++)
		{
				$ret.= (substr($this->module_log['param'][$ii], 0,1) == '!')? '<span style="color:red">'.$this->module_log['param'][$ii].'</span>':$this->module_log['param'][$ii];
				$ret.= ' :: '. $this->module_log['value'][$ii]."\n";
		}
		
		return $this->getHtml('Core',$ret, false);
	}

	/**
	 * Метод формирует отчет по шаблонам
	 *
	 * @return string - HTML отчета
	 */
	public function dump_tpls()
	{
		$ret= '';

		foreach($this->tpl_fetch as $q) $ret.= 'Execute template: '. htmlentities($q['tpl']). "\n";
		foreach($this->tpl_compil as $q) $ret.= 'Compile template: '. htmlentities($q['tpl']). "\n";
		
		return $this->getHtml('Templates',$ret, false);
	}

	/**
	 * Метод выодит отладочную информацию собраную классом-дебагером
	 *
	 */
	public function dump_class_debuger(debug_class $object)
	{
		$ret = '';
		
		foreach($object->call_functions as $function) $ret.= '<div>Выполнение метода: <b>'. $function['name'] . '</b></div>';	
		
		return $this->getHtml('Fake Classes',$ret);
	}
	
	/**
	 * Метод генерирует html для лога строки
	 *
	 * @return unknown
	 */
	public function dump_string_log()
	{
		return "<DIV class=copy_text style='FONT-SIZE: 8pt;  TEXT-ALIGN: center'>Time : ".$this->dumpTime()." | SQL : ".count($this->queries)."  | Tpl  :  ".count($this->tpl_fetch)." | Compil Tpl : ".count($this->tpl_compil)." | Cached Tpl: ".count($this->cached_tpl)." | MEM: ".$this->dump_mem()."</DIV>";	
	}
	
	/**
	 * Метод запускает счет времени выполнения скрипта
	 */
	public function startTime()
	{
		$this->logstart= explode(' ', microtime());
	}

	/**
	 * Метод останавливает счет времени исполнения скрипта
	 *
	 */
	public function stopTime()
	{
		$this->logend= explode(' ', microtime());
	}

	/**
	 * Синоним метода access. Для совместимости.
	 *
	 * @param unknown_type $type
	 * @param unknown_type $message
	 * @param unknown_type $code
	 * @param unknown_type $filename
	 * @param unknown_type $lineno
	 * @return unknown
	 */
	public function write($type = 1, $message = '', $code = 0, $filename = '', $lineno = 0)
	{
	    return $this->access($type = 1, $message = '', $code = 0, $filename = '', $lineno = 0);
	}
	
	public function sqlTrace()
	{
		$trace = debug_backtrace();
		if(isset($trace[2]['class']) && $trace[2]['class'] == 'mysql_ext') {
			if(isset($trace[3]['class'])) {
				$class = $trace[3]['class'].'->'.$trace[3]['function'];
			} elseif($trace[3]['args']) {
				$class = 'args: '.implode(',',$trace[3]['args']);
			} elseif(isset($trace[3]['function'])) {
				$class = 'from function: '.$trace[3]['function'];
			} else {
				$class = '{main}';
			}
			$file = $trace[2]['file'].' line '.$trace[2]['line'];
		} else {
			if(isset($trace[2]['class'])) {
				$class = $trace[2]['class'].'->'.$trace[2]['function'];
			} elseif(isset($trace[2]['function'])) {
				$class = 'from function: '.$trace[2]['function'];
			} else {
				$class = '{main}';
			}
			
			$file = $trace[1]['file'].' line '.$trace[1]['line'];
		}
	
		$text = 'called from <strong>'.$class.'</strong> in <strong>'.$file.'</strong>';
			
		return $text;
	}
	
	
	/**
	 * Метод логирования всего!
	 * 
	 * Type 1 - Лог доступа
	 * Type 2 - Лог ошибки ядра
	 * Type 3 - Лог ошибки PHP
	 *
	 * @param unknown_type $type
	 * @return unknown
	 */
    public function access($type = 1, $message = '', $code = 0, $filename = '', $lineno = 0)
    {
        global $core;
                
        $db_write_log = $core->db->write_log;
        //$core->db->write_log = -1;
        
        if($type == 1)
        {       

        	if(!$core->CONFIG['logs']['access']) return false;
        	
	        $this->acces_status = 1;
	        $this->accesLoged = true; 
	
	        $data[0] = array('user_id'=>$core->user->id,
	                        'date'=>time(),
	                        'id_site'=>$core->edit_site,
	                        'remote_ip'=>$_SERVER['REMOTE_ADDR'],
	                        'id_module'=>$core->modules->this['id_module'],
	        				'module_name'=>$core->module_name,
	                        'url'=>(isset($core->url_parser))? $core->url_parser->get_uri():'',
	                        'post'=>serialize($_POST),
	                        'get'=>serialize($_GET),
	                        'type'=>$type);
	        
	        if($message)
	        {
	        	$data[0]['message'] = (intval($message)>0)?$core->mess->get(intval($message)) : addslashes($message);
	        }        
        }
        
        if($type == 3)
        {
        	if(!$core->CONFIG['logs']['php_error']) return false;
        	
            $data[] = array('user_id'=>$core->user->id,
                        'date'=>time(),
                        'id_site'=>$core->edit_site,
                        'remote_ip'=>$_SERVER['REMOTE_ADDR'],
                        'id_module'=>$core->modules->this->id_module,
        				'module_name'=>$core->modules->this->name,
                        'url'=>$core->url_parser->get_uri(),
                        'post'=>serialize($_POST),
                        'get'=>serialize($_GET),
                        'type'=>$type, 
                        'message'=>$message,
                        'php_filename'=>$filename,
                        'php_line'=>$lineno,
                        'php_code'=>$code);
        }
        
        
        if($type == 2)
        {
        	if(!$core->CONFIG['logs']['sql_error']) return false;
        	
            $data[] = array('user_id'=>$core->user->id,
                        'date'=>time(),
                        'id_site'=>$core->edit_site,
                        'remote_ip'=>$_SERVER['REMOTE_ADDR'],
                        'id_module'=>$core->modules->this->id_module,
        				'module_name'=>$core->modules->this->name,
                        'url'=>$core->url_parser->get_uri(),
                        'post'=>serialize($_POST),
                        'get'=>serialize($_GET),
                        'type'=>$type, 
                        'message'=>$message);
        }
         
        $core->db->autoupdate()->table('mcms_logs_access')->data($data);
        $core->db->execute();
        
        return $core->db->insert_id;
    }


    private function getHtml($name, $html, $owerflow = true, $noPre = false)
    {
    	$out  = '';
		$out .= '<div style="padding:2px 0px 2px 0px; margin-bottom:6px; background-color:#F8FAFF; ">';
		$out .= '<div align="left" style="font-size:12px; padding:2px; color:gray; background-color:#ACCDDC">'.$name.'</div>';
		if($noPre) {
			$out .= '<div style="';
			if($owerflow) $out .= 'height:300px; overflow:auto; '; 
			$out .= 'border:solid gray 1px; padding:5px; margin:0px; font-size:10px;">';
			$out .= $html;
			$out .= '</div>';
		} else {
			$out .= '<pre style="';
			if($owerflow) $out .= 'height:300px; overflow:auto;'; 
			$out .= 'border:solid gray 1px; padding:5px; margin:0px; font-size:10px;">';
			$out .= $html;
			$out .= '</pre>';
		}

		$out .= '</div>';
		
		return $out;
    }
    
    ##### синонимы #####
    
    public function tpl()
    {
    	return $this->dump_tpls();
    }
    
    public function sql()
    {
    	return $this->dumpQueries();
    }
    
    private function _LogTrace()
	{
		$out = '';
   		$it = '';
   		$lfn = LFP;
   		$itw = '  ';
	    $Ts = array_reverse(debug_backtrace());
	    foreach($Ts as $T)
	       { 
	        if($T['function'] != 'include' && $T['function'] != 'require' && $T['function'] != 'include_once' && $T['function'] != 'require_once')
	        {
	            $ft = $it . '<'. basename($T['file']) . '> on line ' . $T['line']; 
	            if($T['function'] != '_LogTrace')
	            {
	                if(isset($T['class']))
	                    $ft .= ' in method ' . $T['class'] . $T['type'];
	                else
	                    $ft .= ' in function ';
	                $ft .= $Trace['function'] . '(';
	            }
	            else
	                $ft .= '(';
	            if(isset($T['args'][0]))
	            {
	                if($T['function'] != '_LogTrace')
	                {
	                    $ct = '';
	                    foreach($T['args'] as $A)
	                    {
	                        $ft .= $ct . $this->_LogVar($A, '', $it, $itw, 0);
	                        $ct = $it . $itw . ',';
	                    }
	                }
	                else
	                    $ft .= $this->_LogVar($T['args'][0], '', $it, $itw, 0);
	            }
	            $ft .= $it . ")\r";
	            $out .= $ft;
	            $it .= $itw;
	        }           
	    }
    
	    return $out;
	}

	private function _LogVar(&$Var, $vn, $pit, $itw, $nlvl, $m = '')
	{
	    if($nlvl>=16) return;
	    if($nlvl==0){$tv=serialize($Var);$tv=unserialize($tv);}
	    else $tv=&$Var;
	    $it=$pit.$itw;
	    for($i=0; $i<$nlvl;$i++) $it.='.'.$itw;
	    $o='';$nl="\n";
	    if(is_array($tv))
	    {
	        if(strlen($vn)>0) $o.=$it.$m.'<array> $'.$vn.' = (';
	        else $o.="\r".$it.$m.'<array> = (';
	        $o.= $nl;$AK=array_keys($tv);
	        foreach($AK as $AN) {$AV=&$tv[$AN];$o.=LogVar($AV,$AN,$pit,$itw,$nlvl+1);}
	        $o.=$it.')'.$nl;
	    }
	    else if(is_string($tv))
	    {
	        if(strlen($vn)>0)$o.=$it.$m.'<string> $'.$vn.' = ';
	        else $o.=' '.$m.'<string> = ';
	        if($tv===null) $o.='NULL';
	        else $o.='"'.$tv.'"';
	        $o.=$nl;
	    }
	    else if(is_bool($tv))
	    {
	        if(strlen($vn) > 0) $o.=$it.$m.'<boolean> $'.$vn.' = ';
	        else $o.=' '.$m.'<boolean> = ';
	        if($tv===true) $o.='TRUE';
	        else $o.='FALSE';
	        $o.=$nl;
	    }
	    else if(is_object($tv))
	    {
	        if(strlen($vn)>0)
	        {
	            $o.=$pit.$itw;
	            for($i=0;$i<$nlvl;$i++) $o.='.'.$itw;
	            $o.=$m.'<'.get_class($tv).'::$'.$vn.'> = {'.$nl;
	        }
	        else $o.=' '.$m.'<'.get_class($tv).'::> = {'.$nl;
	        $R=new ReflectionClass($tv);
	        $o.=$it.'.'.$itw.'Class methods {'.$nl;
	        $CM=$R->getMethods();
	        foreach($CM as $MN => $MV)
	        {
	            $o.=$it.'.'.$itw.'.'.$itw.implode(' ',Reflection::getModifierNames($MV->getModifiers())).' '.$MV->getName().'(';
	            $MP=$MV->getParameters(); $ct='';
	            foreach($MP as $MPN => $MPV)
	            {
	                $o.=$ct; $o.=$MPV->isOptional()?'[':'';
	                if($MPV->isArray()) $o.='<array> ';
	                else if($MPV->getClass()!==null) $o.='<'.$MPV->getClass()->getName().'::> ';
	                $o.=$MPV->isPassedByReference()?'&':''; $o.='$'.$MPV->getName();
	                if($MPV->isDefaultValueAvailable())
	                 {
	                    if($MPV->getDefaultValue()===null) $o.=' = NULL';
	                    else if($MPV->getDefaultValue()===true) $o.=' = TRUE';
	                    else if($MPV->getDefaultValue()===false) $o.=' = FALSE';   
	                    else $o.=' = '.$MPV->getDefaultValue();   
	                }
	                $o.=$MPV->isOptional()?']':''; $ct=', ';
	            }
	            $o.=')'.$nl;
	        }
	        $o.=$it.'.'.$itw.'}'.$nl; $o.=$it.'.'.$itw.'Class properties {'.$nl;
	        $CV=$R->getProperties();
	        foreach($CV as $CN => $CV)
	        {
	            $M=implode(' ',Reflection::getModifierNames($CV->getModifiers())).' ';
	            $CV->setAccessible(true);
	            $o.=LogVar($CV->getValue($tv),$CV->getName(),$pit,$itw,$nlvl+2,$M);
	        }
	        $o.=$it.'.'.$itw.'}'.$nl; $o.=$it.'.'.$itw.'Object variables {'.$nl;
	         $OVs=get_object_vars($tv);   
	        foreach($OVs as $ON => $OV) $o.=LogVar($OV,$ON,$pit,$itw,$nlvl+2);
	        $o.=$it.'.'.$itw.'}'.$nl; $o.=$pit.$itw;
	        for($i=0;$i<$nlvl;$i++)    $o.='.'.$itw;
	        $o.='}'.$nl;
	    }
	    else
	    {
	        if(strlen($vn)>0) $o.=$it.$m.'<'.gettype($tv).'> $'.$vn.' = '.$tv;
	        else $o.=' '.$m.'<'.gettype($tv).'> = '.$tv;
	        $o.=$nl;
	    }         
	    return $o;   
	}

	public function __destruct(){
		global $core;
		unset($core->log);
		unset($this);
	}
    
}
?>