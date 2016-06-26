<?php
################################################################################
# ---------------------------------------------------------------------------- #
# @Author: Alexey Pshenichniy <inc.mcms@gmail.com> <M-zone Inc.> 2005-2012     #
# @Date: 05.06.2008                                                            #
# @license: GNU GENERAL PUBLIC LICENSE Version 2                               #
# @lastModified = 1324287321                                                   #
# ---------------------------------------------------------------------------- #
# M-cms v5.9                                                                   #
################################################################################


class majax {
	private $objects = array();
	private $objectsAlias = array();
	private $objectMethods = array();
	private $objectMethodsAccess = array();
	private $objectVars = array();
	private $request = array();
	private $lastToken = '';
	private $useCSRF = false;
	private $handlers = array(
	    'request'=>array(), 
	    'response'=>array()
	);
	
	public function __construct($useCSRF = false) {
	    $this->useCSRF = $useCSRF;
	    if($this->useCSRF) {
	       if(isset($_SESSION['majax_csrf_token'])) {
	           $this->lastToken = $_SESSION['majax_csrf_token'];
    	    } else {
    	        $this->makeToken();
    	    }
	    }
	}
	
	
	
	/**
	 * Метод регистрирует объект для удаленного вызова.
	 * использование:
	 *  	$core->ajax->register($object, 'methods', 1); // регистрирует все методы объекта $object, и связывает права на вызов всех методов с текущим модулем, и action_id = 1
	 *		$core->ajax->register($object, 'clientInfo'); // регистрирует метод clientInfo для объекта $object, при этом права не будут проверяться
	 *		$core->ajax->register($object, 'clientInfo', 4); // регистрирует метод clientInfo для объекта $object, и связывает права на вызов всех методов с текущим модулем, и action_id = 1
	 *		$core->ajax->register($object, 'objectVariable'); // регистрирует objectVariable для объекта $object как переменную (в том случае если это член класса), в случае если это метод класса то регистрация происходит как метода, без проверки прав. Для регистрации переменных права не устанавливаются.
	 *		$core->ajax->register($object, 'vars'); // регистрирует все публичные члены класса для объекта $object
	 *		$core->ajax->register($object, 'methods'); // регистрирует все методы класса для объекта $object, при этом права проверяться не будут
	 *		$core->ajax->register($object); // регистрирует весь объект, а именно все публичные методы, все публичные члены, и права не проверяются
	 *		$core->ajax->register($object,'*'); // регистрирует весь объект, а именно все публичные методы, все публичные члены, и права не проверяются
	 *		$core->ajax->register($object,'*', 21); // регистрирует весь объект, а именно все публичные методы, все публичные члены, и связывает все методы с контроллером 21 текущего метода 
	 *
	 * @param object $object 
	 * @param mixed [$whatRegister]
	 * @param integer [$accessActionId]
	 */
	public function register($object,$whatRegister='*',$accessActionId=0, $alias = false) {
		$isStatic = false;
		
		if(is_string($object) && class_exists($object)) {
			if($whatRegister == '*') return false;
			if(!method_exists($object, $whatRegister)) return false;
			$isStatic = true;
			$className = $object;
			$classVars = get_class_vars($className);
			$this->objects[$className] = $className;
			if($alias) $this->objectsAlias[$className] = $alias;
		} else {
			$className = get_class($object);
			$classVars = array_keys(get_class_vars($className));
			$this->objects[$className] = $object;
			if($alias) $this->objectsAlias[$className] = $alias;
		}
		
		if($whatRegister == '*') { // весь объект
			$this->objectMethods[$className] = get_class_methods($className);
			$this->objectVars[$className] = $classVars;
			
			if($object instanceof sv_module)
				foreach ($this->objectMethods[$className] as $key=>$val) 
					if(in_array($val, array('getClientInfo', 'init', 'module'))) 
						unset($this->objectMethods[$className][$key]);

			if($accessActionId) $this->objectMethodsAccess[$className] = $accessActionId;
		} elseif($whatRegister == 'vars' && !$isStatic) { // все свойства
			$this->objectVars[$className] = $classVars;
		} elseif($whatRegister == 'methods' && !$isStatic) { // все методы
			$this->objectMethods[$className] = get_class_methods($className);
			if($accessActionId) $this->objectMethodsAccess[$className] = $accessActionId;
		} else {
			if(in_array($whatRegister, $classVars) && !$isStatic) { // одно свойство
				if(!in_array($whatRegister, $this->objectVars[$className])) {
					$this->objectVars[$className][] = $whatRegister;
				}
			}						
			if(in_array($whatRegister, get_class_methods($className))) { // один метод
				if(!isset($this->objectMethods[$className]) || !is_array($this->objectMethods[$className]) || !in_array($whatRegister,$this->objectMethods[$className])) {
					$this->objectMethods[$className][] = $whatRegister;	
				}
				if($accessActionId) {
					$this->objectMethodsAccess[$className][$whatRegister] = $accessActionId;
				}
			}
		}
	}
	
	/**
	 * Метод запускает "наблюдателя" за вызовом удаленны методов
	 * Вызов этого метода необходимо делать в контроллере, до какого либо вывода в браузер, иначе могут быть ошибки
	 */
	public function listen($onlyThis = false) {
		// Определяем по заголовку, пришел нам ajax запрос или нет
		if(isset($_SERVER['HTTP_FROM']) && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['X-HTTP_X_REQUESTED_WITH'] = 'MAJAX') {
			/**
			 * Вообще в спецификации HTTP поле FROM зарезервировано для отправки почтового ящика клиента... воспользуемся этим.
			 * http://tools.ietf.org/html/rfc1945#page-42    - HTTP/1.0
			 * http://tools.ietf.org/html/rfc2068#page-118   - HTTP/1.1
			 */
			$signature = explode(':', $_SERVER['HTTP_FROM']);
			if(count($signature) < 4 || $signature[0] != 'MAJAX') return false;
			$this->checkToken();
			$className = trim($signature[1]); // основой для определения названия класса и метода служит именно сигнатура в поле HTTP_FROM, а в теле запроса это дублируется для справочных целей - так отлаживать проще.
			$methodName = trim($signature[2]);
			$pathFrom = parse_url($signature[3]);
			$pathFragment = (isset($pathFrom['fragment']))? $pathFrom['fragment'] : false;
			$pathFrom = $pathFrom['path'];
			
			$this->getRequest();
			// совпадают ли урл на который идет запрос, и урл с которым был проинициализирован ajax
			if($pathFrom != $_SERVER['REQUEST_URI']) $this->error('Security error. Procedure call is allowed only from source page.', 1);
			// прослушивается только один метод, если вызван не он, выходим
			if($onlyThis != false && $onlyThis != implode('::', array($className,$methodName))) return false;
			// зарегистрирован ли указаный объект
			if(!isset($this->objects[$className])) $this->error('Security error. Object not allowed.', 2);			
			// зарегистрирован ли указанный метод объекта
			if(!in_array($methodName, $this->objectMethods[$className])) $this->error('Security error. Method not allowed.'.print_r(get_class_methods($this->objects[$className]), true), 3);
			// достаточно ли прав на вызов этого метода
			if(isset($this->objectMethodsAccess[$className])) $this->checkAccess($className,$methodName);

			// хук или MagicMethod перед вызовом ajax метда
			if(method_exists($this->objects[$className], '__beforeAjax')) {
				$this->objects[$className]->__beforeAjax($methodName,$this->getArgs());
			}

			if($this->getExtendedCallStack()) {
				if(isset($this->objects['templates'])) {
					$stack = $this->getExtendedCallStack();
					
					foreach($stack as $item) {
						$variable = key($item);
						$item = current($item);
						if(isset($this->objects[$item[0]])) {
							if(method_exists($this->objects[$item[0]], $item[1])) {
								if(!isset($this->objectMethodsAccess[$className]) || $this->checkAccess($item[0], $item[1], false)) {
									$result = (isset($item[2]))?
													call_user_func_array(array($this->objects[$item[0]],$item[1]),$item[2]) :
													call_user_func(array($this->objects[$item[0]],$item[1]));
									$this->objects['templates']->assign($variable, $result);
									//echo "{$variable} < call {$item[0]}::{$item[1]}\n";
								} else {
									//echo "{$variable} < error call! no access rule! {$item[0]}::{$item[1]}\n";
								}
							} elseif(property_exists($this->objects[$item[0]], $item[1])) {
								$this->objects['templates']->assign($variable, $this->objects[$item[0]]->{$item[1]});
								//echo "{$variable} < get {$item[0]}::{$item[1]}\n";
							} else {
								//echo "{$variable} ! not set. property adn method not exists: {$item[0]}::{$item[1]}\n";
							}
						}
					}
				}
			}
			
			if(is_array($this->getArgs())) {
				$result = call_user_func_array(array($this->objects[$className],$methodName),$this->getArgs());
			} else {
				$result = call_user_func(array($this->objects[$className],$methodName));
			}

			// хук или MagicMethod ПОСЛЕ вызова ajax метода
			if(method_exists($this->objects[$className], '__afterAjax')) {
				$this->objects[$className]->__afterAjax($methodName,$this->getArgs(),$result);
			}
						
			// Отправка результата
			$this->response($result);
		}
	}
	
	/**
	 * Метод возвращает javascript для вставки в страницу
	 */
	public function out() {
		$out = "<script type=\"text/javascript\">\n";
		$out .= "MAJAX.token = '{$this->lastToken}';\n";
		$out .= $this->makeJs();
		$out .= "</script>\n";
		return $out;
	}
	
	private function makeToken() {
	    $this->lastToken = md5(substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyz"), 0, 10));
	    $_SESSION['majax_csrf_token'] = $this->lastToken;
	    return $this->lastToken;
	}
	
	private function checkToken() {
	    if(!$this->useCSRF) return true;	    
	    if(empty($_SERVER['HTTP_X_MAJAX_TOKEN'])) $this->error('Ajax token not found', '1874');
	    if($this->lastToken != $_SERVER['HTTP_X_MAJAX_TOKEN']) $this->error('Ajax token expire', '1875');
	}
	
	/**
	 * Метод генерирует javascript для вставки в страницу
	 */
	private function makeJs() {
		$out = '';
		
		foreach($this->objects as $className=>$object) {
			$instanceName = (isset($this->objectsAlias[$className]))? $this->objectsAlias[$className] : $className;
			$out .= "var {$instanceName} = new Object();\n";
	
			if(isset($this->objectVars[$className])) {
				foreach($this->objectVars[$className] as $varName) {
					$out .= "{$instanceName}.{$varName} = ".$this->generateVarString($this->objects[$className]->$varName).";\n";
				}
			}
			if(isset($this->objectMethods[$className])) {
				foreach($this->objectMethods[$className] as $methodName) {
					if(substr($methodName, 0, 2) == '__') continue; // is magic method
					$out .= "{$instanceName}.{$methodName} = function() { MAJAX.CALL('{$className}','{$methodName}',arguments) };\n";
				}
			}
		}
		
		return $out;
	}
		
	/**
	 * Метод генерирует javascript вывод для объявления зарегистрированных членов класса
	 * @param mixed $var
	 */
	private function generateVarString($var) {
		if(is_array($var)) {
			return $var;
		}
		if(is_bool($var)) {
			return ($var)? 'true':'false';
		}
		if(is_numeric($var)) {
			return $var;
		}
		if(is_string($var)) {
			return "'".addslashes($var)."'";
		}
		
		return 'NULL';
	}
		
	/**
	 * Метод проверяет права на вызов $methodName класса $className
	 * @param string $className
	 * @param string $methodName
	 */
	private function checkAccess($className,$methodName, $dieIfError = true) {
		global $core;
		
		$accessRule = $this->objectMethodsAccess[$className];
		if(is_int($accessRule)) { // одно правло доступа для всех методов этого класса
			if(!$core->perm->check(intval($accessRule))) {
				if($dieIfError) $this->error('Security error. Access denied to this method of class.', 4);
				return false;
			}
			return true;
		}
		
		if(is_array($accessRule)) {
			if(isset($accessRule[$methodName])) {
				if(!is_int($accessRule[$methodName])) {
					if($dieIfError) $this->error('Security error. Access denied to this method of class.', 5);
					return false;
				}
				if(!$core->perm->check(intval($accessRule[$methodName]))) {
					if($dieIfError) $this->error('Security error. Access denied to this method of class.',6);
					return false;
				}
				return true;
			}
			return true;
		}
		
		return true;
	}
	
	/**
	 * Метод возвращает аргументы вызова метода
	 */
	private function getArgs() {
		if(!isset($this->request['a'])) return false;
		
		return $this->request['a'];
	}
	
	/**
	 * Метод возвращает набор дополнительных вызовов
	 */
	private function getExtendedCallStack() {
		if(!isset($this->request['e'])) return false;
	
		return $this->request['e'];
	}
	
	/**
	 * Метод разбирает JSON запрос
	 */
	private function getRequest() {
		$handle = fopen('php://input','r');
		$data = fgets($handle);
		fclose($handle);
		$this->request = json_decode($data,true);
		
		return $this->request;
	}
	
	/**
	 * Метод выводит резултат работы скрипта в формате JSON
	 * @param $data
	 */
	private function response($data) {
		header('Content-Type: application/json; charset=utf-8');
		$response = array('status'=>'ok', 'token'=>$this->lastToken, 'data'=>$data);
		if(isset($this->request['fg'])) {
			$response['fg'] = true;
			global $core;
			$response['found_rows'] = intval($core->db->found_rows());
		}
		
				
		
		echo json_encode($response);
		die();
	}
	
	/**
	 * Метод возвращает ошибку клиенту
	 * @param string $message
	 */
	private function error($message, $code='0') {
		echo '{"status":"error","message":"'.$message.'","code":"'.$code.'"}';
		die();
	}
	
	
	/**
	 * Статический метод проверки на наличие нужного нам заголовка в запросе
	 */
	public static function is() {
		if(!isset($_SERVER['HTTP_FROM'])) return false;
		if(substr($_SERVER['HTTP_FROM'], 0, 5) != 'MAJAX') return false;
		$sign = explode(':', $_SERVER['HTTP_FROM']);
		if(count($sign) < 4 || $sign[0] != 'MAJAX') return false;
		//if($sign[3] != $_SERVER['REQUEST_URI']) return false;
		
		return true;
	}
}
?>