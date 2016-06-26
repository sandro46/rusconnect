<?php

class admin_payments
{
	/**
	 * Переменная для хранения строки запроса
	 * @var string
	 */
	public $query = '';

	/**
	 * ID магазина у платрона
	 * @var integer
	 */
	private $merchantId = 0;
	
	/**
	 * Кодовая строка для шифрования запросов к платрону.
	 * Задается в панели управления 
	 * @var string
	 */
	private $secret = '';
	
	/**
	 * Минимальная сумма платежа.
	 * Используется только при проверке входных данных в методе simplePay
	 * @var float $minPaySumm
	 */
	private $minPaySumm = 10;
	
	/**
	 * Сумма совершаемого платежа
	 * @var float
	 */
	private $summ = 0;

	/**
	 * Код платежной системы, через которую выполняется платеж
	 * @var string
	 */
	private $paySystem = 'TEST';
	
	/**
	 * Доступные платежные системы. Хранится в настройках
	 * @var array
	 */
	private $avaliablePaySystems = array();
	
	/**
	 * Доступные обработчики платежей. Хранится в настройках
	 * @var array
	 */
	private $avaliableHandlers = array();
	
	/**
	 * Время жизни платежа.
	 * @var integer
	 */
	private $lifetime = 0;
	
	/**
	 * Массив урлов для передачи платрону. вызываются платроном, мользователь не имеет отношения к этим урлам.
	 * 	res - урл обработчика результатов платежа
	 * 	ref - урл отмены платежа
	 * 
	 * @var array
	 */
	public $url = array('user_success_url'=>'', // урл для перенаправления пользователя в случае успешного платежа
						 'user_failure_url'=>'',  // урл для перенаправления пользователя в случае ошибки при совершении платежа
						 'result'=>'/ru/payments/result/res.html', // урл на который платрон отправляет информацию о платеже. Имя файла в конце обязательно, так как по нему строится сигнатура к запросу.
						 'refund'=>'/ru/payments/refund/ref.html', // урл для платрона, при отмене платежа. Имя файла в конце обязательно, так как по нему строится сигнатура к запросу.
						 'pay'=>'init_payment.php', // имя скрипта на стороне платрона, на который делаются запросы по оплате. используется при генерации сигнатуры
						 'platron'=>'https://www.platron.ru/init_payment.php?'); // урл на который посылатся запросы по платежам
	
	/**
	 * Домен на котором запускаются скрипты обработки платежей.
	 * @var string
	 */
	private $listen = 'http://deamons.itbc.pro';
	
	/**
	 * Массив запроса
	 * @var $request array
	 */
	private $request = array();
	
	/**
	 * Массив с данными о платеже
	 * @var array
	 */
	private $payInfo = array();
		
	/**
	 * Конструктор
	 * @param integer $merchantId - id магазина. выдается при подключении
	 * @param string $secret - нечто похожее на пароль апи. задается в личном кабинете платрона
	 * @param string $listen - домен на котором выполняется скрипт обработки платежа
	 */
	public function __construct($merchantId = '', $secret = '', $listen='')
	{
		global $core;
		
		$this->merchantId = ($merchantId)? $merchantId : $this->merchantId;
		$this->secret = ($secret)? $secret : $this->secret;
		$this->listen = ($listen)? $listen : $this->listen;
		$this->lifetime = 3600*48;
		$this->avaliablePaySystems = $core->CONFIG['pay_systems'];
		$this->avaliableHandlers = $core->CONFIG['pay_handlers'];
	}
	
	/**
	 * Простой вызов создания платежа
	 * Данные берутся из GET запроса
	 * 
	 * необходимые данные:
	 * 	client - id клиента который совершает платеж
	 * 	summ - сумма платежа
	 *  pay - платежная система
	 *  phone - номер телефона клиента
	 *  handler - обработчик (кто возьмет на себя обязательства по передаче товара) - доступные обработчики настраиваются в конфиге этог омодуля
	 *  
	 * Дополнительные данные
	 * 
	 *  email - мыло клиента
	 *  desc - описание платежа (например: оплата услуг по счету №33 от 29.01.2007г.)
	 */
	public function simplePay()
	{
		if(!isset($_GET['client']) || !intval($_GET['client'])) return 'Не указан id клиента';
		if(!isset($_GET['summ']) || floatval($_GET['summ']) < $this->minPaySumm) return 'Недопустимая сумма платежа.';
		if(!isset($_GET['pay']) || !in_array($_GET['pay'], $this->avaliablePaySystems)) return 'Недопустимая платежная система.';
		if(!isset($_GET['phone']) || strlen($_GET['phone'] < 10)) return 'Не указан номер телефона плательщика либо указан некорректный номер.';
		if(!isset($_GET['handler']) || !isset($this->avaliableHandlers[$_GET['handler']])) return 'Недопустимый обработчик платежа.';
		
		$client_id = intval($_GET['client']);
		$pay_system = $_GET['pay'];
		$summ = floatval($_GET['summ']);
		$phone = preg_replace('/[^0-9]/i', '', $_GET['phone']);
		$description = addslashes($_GET['desc']);
		$email = (isset($_GET['email']))? addslashes($_GET['email']) : '';
		$handler = $_GET['handler'];
		
		return $this->create($summ, $pay_system, $client_id, $this->getip(), $phone, $email, $description, $this->avaliableHandlers[$handler]['callback'], $this->avaliableHandlers[$handler]['ok_url'], $this->avaliableHandlers[$handler]['fail_url'], $handler);
	}
	
	/**
	 * Создание платежа
	 * 
	 * @param float $summ - сумма платежа
	 * @param sting $paySystem - платежная система
	 * @param integer $clientId - ид клиента который совершает платеж
	 * @param string $clientIp - ип клиента который совершает платеж 
	 * @param string $clientPhone - телефон клиента
	 * @param string $clientEmail - мыло клиента
	 * @param string $description - описание платежа, комментарий
	 * @param string $okUrl - урл на который будет перенаправлен пользователь в случае успешного платежа
	 * @param string $failUrl - урл на который будет перенаправлен пользователь в случае неудачи
	 * -------------------------------------------------------------------------------------------
	 * 
	 * @return array[url] - урл на который нужно перенаправить клиента
	 * @return array[id] - id платежа в таблице платежей, он же order_id
	 * @return array[platron_id] - id платежа в платроне
	 * 
	 * В случае возникновения ошибки, результат будет строка с ошибкой
	 */
	public function create($summ, $paySystem, $clientId, $clientIp, $clientPhone, $clientEmail, $description, $callback, $okUrl, $failUrl, $handler = 'sms')
	{
		global $core;

		// генерируем "соль"
		$salt = $this->getSalt();
		
		// пишем недостающие опции в паблик
		$this->url['user_success_url'] = $okUrl;
		$this->url['user_failure_url'] = $failUrl;

		// начальный массив для записи в базу
		$pay = array(
				'date'=>time(),
				'client_id'=>$clientId,
				'client_ip'=>$clientIp, 
				'currency'=>'RUR',
				'payment_system'=>$paySystem,
				'lifetime'=>$this->lifetime,
				'description'=>$description,
				'client_phone'=>$clientPhone,
				'client_email'=>$clientEmail,
				'salt'=>$salt,
				'amount'=>$summ,
				'callback'=>$callback,
				'handler'=>$handler);
			
		$core->db->autoupdate()->table('pg_payments')->data(array($pay));
		$core->db->execute();

		// запись не получилась, вернем ошибку
		if(!$core->db->insert_id) return 'Ошибка создания транзакции. Сервер вернул ошибочный результат.';
			
		$pay['id']=$core->db->insert_id;
		
		// собираем массив который уйдет на платрон
		$request = $this->makePayRequest($pay);
		
		// генерируем сигнатуру
		$request['pg_sig'] = PG_Signature::make($this->url['pay'], $request, $this->secret);
		$pay['sig'] = $request['pg_sig'];
			
		// делаем из массива запроса, строку
		$this->makeQuery($request);
			
		$core->db->autoupdate()->table('pg_payments')->data(array($pay))->primary('id');
		$core->db->execute();

		// отправляем запрос, получаем результат
		$responce = $this->query();
			
		// проверяем на ошибку результат
		$error = $this->checkResponceToError($responce);
		
		if($error !== true)
		{
			$core->db->delete('pg_payments', $pay['id'], 'id');
			return $error;
		}
			
		// проверяем сигнатуру результата
		if($this->checkResponceSignature($responce, $this->url['pay']) !== true)
		{
			$core->db->delete('pg_payments', $pay['id'], 'id');
			return 'Bad responce signature.';
		}
			
		$pay['platron_id'] = $responce['pg_payment_id'];
			
		// обновляем запись в базе
		$core->db->autoupdate()->table('pg_payments')->data(array($pay))->primary('id');
		$core->db->execute();
			
		return array('url'=>$responce['pg_redirect_url'], 'id'=>$pay['id'], 'platron_id'=>$responce['pg_payment_id']);
	}
	
	/**
	 * Результат платежа
	 */
	public function result($xmlResponce = true)
	{
		global $core;
		
		// кучка проверок
		if(!$this->request()) return $this->errorResponce('Ошиибка запроса. недостаточно данных', $xmlResponce, basename($this->url['result']));
		
		$this->request['pg_order_id'] = intval($this->request['pg_order_id']);
		$this->request['pg_payment_id'] = intval($this->request['pg_payment_id']);
		$this->payInfo = $this->getPayInfo($this->request['pg_order_id']);
		
		if(!intval($this->request['pg_order_id'])) return  $this->errorResponce('Ошибка запроса. Некорректный order_id', $xmlResponce, basename($this->url['result']));
		if(!intval($this->request['pg_payment_id'])) return  $this->errorResponce('Ошибка запроса. Некорректный pg_payment_id', $xmlResponce, basename($this->url['result']));
		if(!$this->payInfo) return  $this->errorResponce('Ошибка запроса. Платеж не обнаружен.', $xmlResponce, basename($this->url['result']));
		if($this->payInfo['platron_id'] != $this->request['pg_payment_id']) return  $this->errorResponce('Ошибка запроса. pg_payment_id не совпадает с данными в базе.', $xmlResponce, basename($this->url['result']));
		if($this->request['pg_sig'] != PG_Signature::make(basename($this->url['result']), $this->request, $this->secret)) return  $this->errorResponce('Ошибка запроса. Неверная сигнатура.', $xmlResponce, basename($this->url['result']));
		
		// все проверили, все хорошо, начинаем обработку
		
		// пытаемся обработать кaлбэк, но только в случае если платеж еще небыл обработан
		if($this->payInfo['status'] == 3) $this->payInfo['calback_result'] = $this->callback($this->payInfo);
		
		// меняем статус платежа
		$this->payInfo['status'] = ($this->request['pg_result'] == 1)? 1 : 4;
		
		// обновляем платеж
		$core->db->autoupdate()->table('pg_payments')->data(array($this->payInfo))->primary('id');
		$core->db->execute();
		
		// возвращаем платрону результат
		$message = ($this->request['pg_result'] == 1)? 'Товар передан покупателю': 'Исполнение платежа приостановлено';
		$this->responceQuery($message, 'ok', basename($this->url['result']), true);
		
		
		return $message;		
	}

	/**
	 * Отмена платежа
	 */
	public function refund()
	{
		global $core;
		
		// кучка проверок
		if(!$this->request()) return $this->errorResponce('Ошиибка запроса. недостаточно данных', $xmlResponce, basename($this->url['refund']));
		
		$this->request['pg_order_id'] = intval($this->request['pg_order_id']);
		$this->request['pg_payment_id'] = intval($this->request['pg_payment_id']);
		$this->payInfo = $this->getPayInfo($this->request['pg_order_id']);
		
		if(!intval($this->request['pg_order_id'])) return  $this->errorResponce('Ошибка запроса. Некорректный order_id', $xmlResponce, basename($this->url['refund']));
		if(!intval($this->request['pg_payment_id'])) return  $this->errorResponce('Ошибка запроса. Некорректный pg_payment_id', $xmlResponce, basename($this->url['refund']));
		if(!$this->payInfo) return  $this->errorResponce('Ошибка запроса. Платеж не обнаружен.', $xmlResponce, basename($this->url['refund']));
		if($this->payInfo['platron_id'] != $this->request['pg_payment_id']) return  $this->errorResponce('Ошибка запроса. pg_payment_id не совпадает с данными в базе.', $xmlResponce, basename($this->url['refund']));
		if($this->request['pg_sig'] != PG_Signature::make(basename($this->url['refund']), $this->request, $this->secret)) return  $this->errorResponce('Ошибка запроса. Неверная сигнатура.', $xmlResponce, basename($this->url['refund']));
		
		// все проверили, все хорошо, начинаем обработку
		
		// меняем статус платежа
		$this->payInfo['status'] = 2;
		
		$message = 'Исполнение платежа приостановлено';
		
		$this->responceQuery($message, 'ok', basename($this->url['refund']), true);
		
		return $message;
	}
	
	
	
	######## Всякая служебная хрень ########
	
	/**
	 * Метод получает данные по id платежа
	 * 
	 * @param integer $orderId
	 */
	private function getPayInfo($orderId)
	{
		global $core;
		
		$core->db->select()->from('pg_payments')->fields('*')->where('id = '.$orderId);
		$core->db->execute();
		$core->db->get_rows(1);
		
		if(!$core->db->rows || !is_array($core->db->rows) || !count($core->db->rows) || !isset($core->db->rows['id'])) return false;
		
		return $core->db->rows;
	}
	
	/**
	 * Метод смотрит GET и определяет, пришол запрос из платрона или нет
	 */
	private function request()
	{
		$this->request = array();
		
		foreach($_GET as $key=>$item)
			if(substr($key,0,3) == 'pg_') $this->request[$key]=$item;
			
		if(count($this->request[$key]>3)) return true;
		
		return false;
	}
	
	/**
	 * Метод генерирует xml с ошибкой для платрона
	 *
	 * @param string $message - сообщение об ошибке
	 * @param boolean $xmlResponceFlag - флаг указывающий на то возвращать результат в виде xml или отправить результат на вывод
	 * @param string $scriptNameForSign - имя файла скрипта для генерации сигнатуры
	 */
	private function errorResponce($message, $xmlResponceFlag, $scriptNameForSign)
	{
		if(!$xmlResponceFlag) return $message;
		
		$this->responceQuery($message, 'error', $scriptNameForSign, true);
		
		return $message;
	}
	
	/**
	 * Метод выполняет функции связаные с передачей товара покупателю.
	 * 
	 * @param array $payObject - бьект платежа.
	 */
	private function callback($payObject)
	{
		global $core;
		
		if(!isset($payObject['callback'])) return 'callback undefined';
		
		$call = explode('|', $payObject['callback']);
		
		if(!$call || count($call) != 3) return 'error callback string';
		if(strlen($call[2]) < 2) return 'error callback function';
		
		$filename = $call[1];
		$modulename = $call[0];
		
		$callType = 'function';
		$method = $call[2];
		$classname = false;
		
		if(strstr($call[2], '::') !== false) $callType = 'static';
		if(strstr($call[2], '->') !== false) $callType = 'dinamic';
		
		if(!file_exists($core->CONFIG['module_dir'].$modulename)) return 'module folder not exists. '.$core->CONFIG['module_dir'].$filename;
		if($callType != 'function' && !file_exists($core->CONFIG['module_dir'].$modulename.'/classes/'.$filename)) return 'filename not exists. '.$core->CONFIG['module_dir'].$modulename.'/classes/'.$filename;
		if($callType == 'function' && !file_exists($core->CONFIG['module_dir'].$modulename.'/includes/'.$filename)) return 'filename not exists. '.$core->CONFIG['module_dir'].$modulename.'/includes/'.$filename;
		
		if($callType == 'function')
			require_once $core->CONFIG['module_dir'].$modulename.'/includes/'.$filename;
		else 
			require_once $core->CONFIG['module_dir'].$modulename.'/classes/'.$filename;
			
		if($callType == 'function')
		{
			if(!function_exists($method)) return 'function not exists. '.$method;
			return call_user_func_array($method, $payObject);
		}
		
		if($callType == 'static')
		{
			$call[2] = explode('::', $call[2]);
			
			if(!class_exists($call[2][0])) return 'class not exists. '.$call[2][0];
						
			return call_user_func_array(array($call[2][0], $call[2][1]), $payObject);
		}
		
		if($callType == 'dinamic')
		{
			$call[2] = explode('->', $call[2]);
			
			if(!class_exists($call[2][0])) return 'class not exists. '.$call[2][0];
			
			$obj = new $call[2][0];
			if(!method_exists($obj, $call[2][1])) return 'method ['.$call[2][1].'] not exists in '.$call[2][0];
			
			return call_user_func_array(array($obj, $call[2][1]), $payObject);
		}
	}
	
	/**
	 * Метод генерирует xml ответ для платрона
	 * 
	 * @param string $message - сообщение
	 * @param string $status - статус ответа (ok - успешно error - ошибка)
	 * @param string $scriptName - имя файла скрипта для сигнатуры
	 * @param boolean $execute - флаг вывода xml
	 */
	private function responceQuery($message, $status, $scriptName, $execute = false)
	{
		$salt = $this->getSalt();
		$responce = array('pg_salt'=>$salt, 'pg_status'=>$status, 'pg_description'=>$message);
		if($status == 'error') $responce['pg_error_description'] = $message;
		$sign = PG_Signature::make($scriptName, $responce, $this->secret);
		$responce['pg_sig'] = $sign;
		
		$xml = $this->assocArrayToXML('response', $responce);
		
		if($execute) echo $xml;
		
		return $xml;
	}
	
	/**
	 * Метод генерирует соль для сигнатуры
	 */
	private function getSalt()
	{
		$a = microtime();
		$b = CORE_VERSION.VERSION.PRODUCT.AUTHOR;
		for($i=0; $i != 15; $i++) $c .= chr(rand(161,254));
		return md5($a.$c.$b);	
	}
	
	/**
	 * Метод генерирует строку get запроса по массиву
	 * @param array $request
	 */
	private function makeQuery($request)
	{		
		$this->query = http_build_query($request);
		$this->query = $this->url['platron'].$this->query;
		
		return $this->query;
	}
	
	/**
	 * Метод генерирует обьект для запроса к платрону на совершение платежа
	 * @param array $object
	 */
	private function makePayRequest($object)
	{
		
		$obj['pg_success_url'] = $this->url['user_success_url'];
		$obj['pg_failure_url'] =  $this->url['user_failure_url'];
		$obj['pg_refund_url'] = $this->listen.$this->url['refund'];
		$obj['pg_result_url'] = $this->listen.$this->url['result'];
		$obj['pg_amount'] =  $object['amount'];
		$obj['pg_description'] = $object['description'];
		$obj['pg_lifetime'] =  $object['lifetime'];
		$obj['pg_merchant_id'] =  $this->merchantId;
		$obj['pg_order_id'] =  $object['id'];
		$obj['pg_salt'] =  $object['salt'];
		$obj['pg_user_ip'] = $object['client_ip'];
		$obj['pg_user_phone'] = $object['client_phone'];
		$obj['pg_user_email'] = $object['client_email'];
		$obj['pg_user_cardholder'] = 'none';
		$obj['pg_currency'] = 'RUR';
		$obj['pg_encoding'] = 'utf-8';
		$obj['pg_payment_system'] = $object['payment_system'];
		
		return $obj;
	}

	/**
	 * Метод отправляет запрос на платрон через curl
	 */
	private function query()
	{		
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $this->query);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);	
		curl_setopt($curl, CURLOPT_USERAGENT, 'ITBC.PRO - SMS Service'); 

		$result = curl_exec($curl);
		$resultXml =  @simplexml_load_string($result);
		
		if(!is_object($resultXml)) return false;
		
		$_tmp_object = $this->simpleXMLToArray($resultXml);
		
		if(!isset($_tmp_object['children'])) return false;
		
		foreach($_tmp_object['children'] as $key=>$value)
		{
			$resultObject[$key] = $value['value'];
		}
		
		return $resultObject; 
	}
	/**
	 * Ну хуй знает. просто првоерка на ошибки
	 * @param array $responce - обьект ответа
	 */
	private function checkResponceToError($responce)
	{
		if(!isset($responce['pg_status'])) return 'Payment processing server return bad result.';
		if($responce['pg_status'] == 'error') return $responce['pg_error_description'];
		
		return true;
	}
	
	/**
	 * Проверка сигнатуры ответа
	 * @param array $responce - обьект ответа
	 * @param string $url - имя файла скрипта (для генерации сигнатуры)
	 */
	private function checkResponceSignature($responce, $url)
	{
		$sig = PG_Signature::make($url, $responce, $this->secret);
		if($sig != $responce['pg_sig']) return false;
		
		return true;
	}
	
	/**
	 * Метод вытаскивает реальный ip клиента
	 */
	private function getip()
	{
		if (isset($_SERVER))
		{
	    	if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && validip($_SERVER['HTTP_X_FORWARDED_FOR']))
	    	{
	       		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	     	}
	     	elseif(isset($_SERVER['HTTP_CLIENT_IP']) && validip($_SERVER['HTTP_CLIENT_IP']))
	     		{
	       			$ip = $_SERVER['HTTP_CLIENT_IP'];
	     		}
	     		else
	     			{
	       				$ip = $_SERVER['REMOTE_ADDR'];
	     			}
	   }
	   else
	   {
	   		if(getenv('HTTP_X_FORWARDED_FOR') && validip(getenv('HTTP_X_FORWARDED_FOR')))
	   		{
	       		$ip = getenv('HTTP_X_FORWARDED_FOR');
	     	}
	     	elseif(getenv('HTTP_CLIENT_IP') && validip(getenv('HTTP_CLIENT_IP')))
	     		{
	       			$ip = getenv('HTTP_CLIENT_IP');
	     		}
	     		else
	     			{
	       				$ip = getenv('REMOTE_ADDR');
	     			}
	   }

	   return $ip;
	 }

	/**
	 * Метод возвращает ассоциативный массив из XML
	 * @param string $xml
	 */
	private function simpleXMLToArray($xml) 
	{
		$arXML=array();
		$arXML['name']=trim($xml->getName());
		$arXML['value']=trim((string)$xml);
		$t=array();
		
		foreach($xml->attributes() as $name => $value) $t[$name]=trim($value);
		
		$arXML['attr']=$t;
		$t=array();
		
		foreach($xml->children() as $name => $xmlchild)
		{
			if(isset($t[$name]))
			{
				if(!isset($t[$name][0]))
				{
					$_s = $t[$name];
					unset($t[$name]);
					$t[$name][] = $_s;
					unset($_s);
				}
	
				$t[$name][]=$this->simpleXMLToArray($xmlchild);
			}
			else
				{
					$t[$name]=$this->simpleXMLToArray($xmlchild);
				}
		}
		
		$arXML['children']=$t;
		return($arXML);
	}
	
	/**
	 * Метод возвращает xml из ассоциативного массива
	 * @param string $root_element_name - корневой этемент xml
	 * @param array $ar - исходный массив 
	 */
	private function assocArrayToXML($root_element_name,$ar)
	{	    
		$xml = new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"utf-8\"?><{$root_element_name}></{$root_element_name}>");
	    $f = create_function('$f,$c,$a','
	            foreach($a as $k=>$v) {
	                if(is_array($v)) {
	                    $ch=$c->addChild($k);
	                    $f($f,$ch,$v);
	                } else {
	                    $c->addChild($k,$v);
	                }
	            }');
	    $f($f,$xml,$ar);
	    return $xml->asXML();
	}
}
?>