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
 * ---------------------------------------------------------------------------------------------------------------------------
 * Базовый класс для работы с использованием ajax.
 *
 * 
 * Для отладки приложений использующих етот класс используйте режим отладки. $this->debug_mode = 1; 
 * Метод передачи данных указывается в переменной $this->request_type = ['POST' / 'GET']
 * 
 * ----------------------------------------------------------------------------------------------------------------------------
 * Для инициализации класса необходимо подлючить етот файл в скрипт и создать его обьект.
 * После необходимо указать какие функции могут быть использованы (метод add_func) через запрос
 * Далее необходимо вызвать два метода:
 * 	ajax_init();
 *	user_request();
 *
 * Первый метод генерирует javascript который потребуется для работы с httprequest
 * Второй метод проверяет запущен скрипт через внешний запрос или в обычном режиме
 * В html нужно вставить код содержащийся в переменной output. Код необходимо вставлять после вызова метода ajax_init();
 * 
 * Далее нужно написать javascript обработчик результата работы скрипта, например:
 * 
 * function set_result_data(data)
 * {
 * 	alert(data);
 * }
 * 
 * Чтобы вызвать функцию php из javascript`a необходимо вставить такую строку: 
 * 
 * x_название_функции_php('данные передаваемые в php функцию', 'название js функции которая будет вызыватся при получении данных');
 * 
 * например: x_time(set_result_data);  - будет вызвана функция php time(), затем результат работы функции будет 
 * передан js ункции set_result_data в качестве параметра.
 * ----------------------------------------------------------------------------------------------------------------------------
 */


class ajax
 {

	private $global_functions = array();
	private $remote_uri = "";
	private $f_redirect = ""; 
	private $this_func = "";
	private $this_args = "";
	private $global_obj = array();
	private $instanceRandomName = '';
	
	public $request_type = "POST";
	public $debug_mode = 0;
	public $output = "";
	public $mode = 'async';
	public $compress = false;


	function __construct($instanceName = 'def')
	{
		//$this->instanceRandomName = chr(rand(97, 122)).chr(rand(97, 122)).chr(rand(97, 122)).chr(rand(97, 122));	
		$this->instanceRandomName = $instanceName;
		$this->remote_uri = $_SERVER["REQUEST_URI"];
	}

 	public function init()
 	{
		$this->get_all_javascript();
	}	

 	public function assign_obj($obj_var, $class_name)
 	{
		eval("\$this->global_obj['".$class_name."'] = new \$class_name('".$obj_var."');");
	}
	
 	public function user_request()
 	{
		if(!empty($_GET["rs"])) $mode = "get";		
		if(!empty($_POST["rs"])) $mode = "post";	
		if(empty($mode)) return;

		
	
		
		if ($mode == "get")
		{
			if(!isset($_GET['rsobj'])) return; 
			if($_GET['rsobj'] != $this->instanceRandomName) return;
			
			$this->send_header();
			$this->this_func = $_GET["rs"]; 
			$this->this_args = (!empty($_GET["rsargs"]))? $_GET["rsargs"] : 0;
		}
		else
			{
				if(!isset($_POST['rsobj'])) return; 
				if($_POST['rsobj'] != $this->instanceRandomName) return;
				
				$this->this_func = $_POST["rs"]; 
				$this->this_args = (!empty($_POST["rsargs"]))? $_POST["rsargs"] : 0;
			} 
		
		
			                      	
		if (!in_array($this->this_func, $this->global_functions))
		{
			echo "-:функция $this->this_func не определена";
		}
		else
			{
				if(!function_exists($this->this_func))	
				{
					echo "-:функция $this->this_func не определена. Может забыл подключить файл с методами для ajax?";
					die();
				}
					
				echo "+:";
				$_func = explode("___", $this->this_func);
				
						
				if(count($_func)>1)
				{
					//$result = call_user_func_array(array($_func[0], $_func[1]), $this->this_args);
					
					$params = "";
					
					if($this->this_args)
					{
						foreach($this->this_args as $k=>$param)
						{
							$params .= ($k === 0)? "'".$param."'" : ", '".$param."'";
						}
					}
						
					$params = $this->char_translit($params);
					
					eval("\$result = \$this->global_obj['".$_func[0]."']->".$_func[1]."(".$params.");");
				}
				else
					{						
						# php 5.3.3 FIX
						if(!is_array($this->this_args)) $this->this_args = array();
						$result = call_user_func_array($this->this_func, $this->this_args);
					}
					
				$result = $this->get_js_repr($result);
				$result = trim($result);
			
				echo "var res = ".$result."; res;";
			}
		exit();
	}

 	public function add_func()
	{
		foreach(func_get_args() as $func)
		{
			$this->global_functions[] = $func;
		}		
	}
		
 	public function char_translit($str)
	{
		/**
		 * Декодирование переданых данных после кодирвоания javascript`ом. 
		 * Данные кодировались функцией escape() и дополнительно заменялись все символы '+'
		 * 
		 * ---------------------------------------------------------------------------------
		 * Метод escape() возвращает строку (в формате Unicode) . Все пробелы, пунктуация и любые не-ASCII символы в ней закодированы и выглядят как %xx, где xx эквивалентно шестнадцатиричному числу, обозначающему символ. Например, пробелы будут возвращены как "%20".
		 * Символы, числовые значения которых больше 255 будут представлены в формате %uxxxx.
		 */
			
		$str = str_replace(array('%u0439','%u0446','%u0443','%u043A','%u0435','%u043D','%u0433','%u0448','%u0449','%u0437','%u0445','%u044A','%u0444','%u044B','%u0432','%u0430','%u043F','%u0440','%u043E','%u043B','%u0434','%u0436','%u044D','%u044F','%u0447','%u0441','%u043C','%u0438','%u0442','%u044C','%u0431','%u044E'), array('й','ц','у','к','е','н','г','ш','щ','з','х','ъ','ф','ы','в','а','п','р','о','л','д','ж','э','я','ч','с','м','и','т','ь','б','ю'),$str);
		$str = str_replace(array('%u0419','%u0426','%u0423','%u041A','%u0415','%u041D','%u0413','%u0428','%u0429','%u0417','%u0425','%u042A','%u0424','%u042B','%u0412','%u0410','%u041F','%u0420','%u041E','%u041B','%u0414','%u0416','%u042D','%u042F','%u0427','%u0421','%u041C','%u0418','%u0422','%u042C','%u0411','%u042E'), array('Й','Ц','У','К','Е','Н','Г','Ш','Щ','З','Х','Ъ','Ф','Ы','В','А','П','Р','О','Л','Д','Ж','Э','Я','Ч','С','М','И','Т','Ь','Б','Ю'),$str);
		$str = str_replace('%2b', '+', $str);
		
		return $str;
	}	

	
	
		
	private function send_header()
	{
		@header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");   
		@header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		@header ("Cache-Control: no-cache, must-revalidate");  
		@header ("Pragma: no-cache");  
	}
	
	private function get_main_js()
	{
			$this->request_type = strtoupper($this->request_type);
			if ($this->request_type != "" && $this->request_type != "GET" && $this->request_type != "POST") return "// Неверный метод: $t.. \n\n";
			
			ob_start();
			?>
	        var <?php echo $this->instanceRandomName; ?>_debug_mode = <?php echo $this->debug_mode ? "true" : "false"; ?>;
			var <?php echo $this->instanceRandomName; ?>_request_type = "<?php echo $this->request_type; ?>";
			var <?php echo $this->instanceRandomName; ?>_target_id = "";
			var <?php echo $this->instanceRandomName; ?>_failure_redirect = "<?php echo $this->f_redirect; ?>";
			
			function <?php echo $this->instanceRandomName; ?>_debug(text) 
	        {
			if (<?php echo $this->instanceRandomName; ?>_debug_mode)
					alert(text);
			}
	       
	        function php_error_returned(text)
	        {
	        var prowin = window.open( "about:blank", null, "height=600,width=800,status=no, scrollbars=yes, toolbar=no,menubar=no,location=no");
	
	        
	        
	        prowin.document.write(text);
	        }
			
	 		function <?php echo $this->instanceRandomName; ?>_init_object() 
	        {
	 			<?php echo $this->instanceRandomName; ?>_debug("<?php echo $this->instanceRandomName; ?>_init_object() called..");
	 			
	 			var A;
	 			
	 			var msxmlhttp = new Array(
					'Msxml2.XMLHTTP.5.0',
					'Msxml2.XMLHTTP.4.0',
					'Msxml2.XMLHTTP.3.0',
					'Msxml2.XMLHTTP',
					'Microsoft.XMLHTTP');
				for (var i = 0; i < msxmlhttp.length; i++) {
					try {
                                                if(typeof(ActiveXObject) != 'function') continue;
                                                else
						A = new ActiveXObject(msxmlhttp[i]);
					} catch (e) {
						A = null;
					}
				}
	 			
				if(!A && typeof XMLHttpRequest != "undefined")
					A = new XMLHttpRequest();
				if (!A)
					<?php echo $this->instanceRandomName; ?>_debug("Could not create connection object.");
				return A;
			}
			
			var <?php echo $this->instanceRandomName; ?>_requests = new Array();
			
			function <?php echo $this->instanceRandomName; ?>_cancel() 
	        {
			for (var i = 0; i < <?php echo $this->instanceRandomName; ?>_requests.length; i++) 
				<?php echo $this->instanceRandomName; ?>_requests[i].abort();
			}
			
	        function _spec_str_replace(subject)
			{
			return subject.split('+').join('%2b');
			}
	        
			function <?php echo $this->instanceRandomName; ?>_do_call(func_name, args) 
	        {
				var i, x, n;
				var uri;
				var post_data;
				var target_id;
				
				<?php echo $this->instanceRandomName; ?>_debug("in <?php echo $this->instanceRandomName; ?>_do_call().." + <?php echo $this->instanceRandomName; ?>_request_type + "/" + <?php echo $this->instanceRandomName; ?>_target_id);
				target_id = <?php echo $this->instanceRandomName; ?>_target_id;
				if (typeof(<?php echo $this->instanceRandomName; ?>_request_type) == "undefined" || <?php echo $this->instanceRandomName; ?>_request_type == "") 
					<?php echo $this->instanceRandomName; ?>_request_type = "GET";
				
				uri = "<?php echo $this->remote_uri; ?>";
				
				if (<?php echo $this->instanceRandomName; ?>_request_type == "GET") 
	            {
	           
				if (uri.indexOf("?") == -1) 
					uri += "?rs=" + escape(func_name);
				else
					uri += "&rs=" + escape(func_name);
				uri += "&rst=" + escape(<?php echo $this->instanceRandomName; ?>_target_id);
				uri += "&rsrnd=" + new Date().getTime();
	            uri += "&rsobj=<?php echo $this->instanceRandomName; ?>"; 
				
				
				for (i = 0; i < args.length-1; i++)
	            {
					uri += "&rsargs[]=" + _spec_str_replace(escape(args[i]));
				}
	             
	            uri += "&PHPSESSID=<? echo session_id(); ?>";   
	                
	                
				post_data = null;
				} 
				else if (<?php echo $this->instanceRandomName; ?>_request_type == "POST") 
	            	{
					post_data = "rs=" + escape(func_name);
					post_data += "&rst=" + escape(<?php echo $this->instanceRandomName; ?>_target_id);
					post_data += "&rsrnd=" + new Date().getTime();
					post_data += "&rsobj=<?php echo $this->instanceRandomName; ?>";
					
					for (i = 0; i < args.length-1; i++) 
	                	{
	                    post_data = post_data + "&rsargs[]=" + args[i];
	                    }
					post_data += "&PHPSESSID=<? echo session_id(); ?>"; 
					}
					else 
	            		{
						alert("Неверный тип запроса: " + <?php echo $this->instanceRandomName; ?>_request_type);
						}
				
				<?php echo $this->instanceRandomName; ?> = <?php echo $this->instanceRandomName; ?>_init_object();
				if (<?php echo $this->instanceRandomName; ?> == null) 
	            {
					if (<?php echo $this->instanceRandomName; ?>_failure_redirect != "") 
	                {
						location.href = <?php echo $this->instanceRandomName; ?>_failure_redirect;
						return false;
					} 
	                else 
	                	{
						<?php echo $this->instanceRandomName; ?>_debug("Не оприделен тип броузера:\n" + navigator.userAgent);
						return false;
						}
				} 
	            else 
	            	{
					<?php echo $this->instanceRandomName; ?>.open(<?php echo $this->instanceRandomName; ?>_request_type, uri, <? if($this->mode == 'sync') echo 'false'; if($this->mode == 'async') echo 'true'; ?>);
					<?php echo $this->instanceRandomName; ?>_requests[<?php echo $this->instanceRandomName; ?>_requests.length] = <?php echo $this->instanceRandomName; ?>;
	                
					if (<?php echo $this->instanceRandomName; ?>_request_type == "POST") 
	                {
					<?php echo $this->instanceRandomName; ?>.setRequestHeader("Method", "POST " + uri + " HTTP/1.1");
					<?php echo $this->instanceRandomName; ?>.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
					}
				
					
					
		
					
					
					<? if($this->mode != 'sync'): ?>
					<?php echo $this->instanceRandomName; ?>.onreadystatechange = function()
	                 {
	                 	
						if (this.readyState != 4) 
							return;					
						<?php echo $this->instanceRandomName; ?>_debug("received " + this.responseText);
					
						var status;
						var data;
						var txt = this.responseText.replace(/^\s*|\s*$/g,"");
						status = txt.charAt(0);
						data = txt.substring(2);
						
						function Asc(String)
						{
						
							return String.charCodeAt(0);
						
						}
						
						
						if (status == "") 
	                    {
	
						} 
	                    else if (status == "-") 
							alert("Error: " + data);
						else 
	                    	{
							if (target_id != "") 
								{
								document.getElementById(target_id).innerHTML = eval(data);
								}
								else 
									{
									try {
										var callback;
										var extra_data = false;
										if (typeof args[args.length-1] == "object") 
		                                	{
											callback = args[args.length-1].callback;
											extra_data = args[args.length-1].extra_data;
											} 
		                                    else 
		                                    	{
												callback = args[args.length-1];
												}
										
						
										
										if(typeof(callback) == 'function')
										{
											callback(eval(data), extra_data);
										}
										
										if(typeof(callback) == 'string')
										{
											eval(callback+'(eval(data), '+extra_data+')');
										}
										
										
										} 
		                                catch (e) 
		                                	{
		                                	alert(e);
											php_error_returned(data);
											}
									}
							}
						}
						<? endif; ?>
					}
				
				<?php echo $this->instanceRandomName; ?>_debug(func_name + " uri = " + uri + "/post = " + post_data);
				<?php echo $this->instanceRandomName; ?>.send(post_data);
				
				<?php echo $this->instanceRandomName; ?>_debug(func_name + " waiting..");
				
				
				
				<? if($this->mode == 'sync'): ?>
				data = <?php echo $this->instanceRandomName; ?>.responseText.replace(/^\s*|\s*$/g,"");
				data = data.substring(2);
				data = data.substring(0, data.length-4);
				
				eval(data);
				return res;
				<? endif; ?>
				
				delete <?php echo $this->instanceRandomName; ?>;
				return true;
			}
	      	<?
			
			$ret = ob_get_contents();
			ob_end_clean();
				
			if($this->compress)
			$ret = str_replace(array('  ', '	',"\n", '\n','\r', chr(13)), array('','','','','', ''), $ret);
			
		return $ret;
	}
			
 	private function get_js_repr($value) 
	{
		//$type = gettype($value);
		switch(gettype($value))
			{
			case 'boolean':
				return ($value) ? "Boolean(true)" : "Boolean(false)";
			break;
			
			case 'integer':
				return "parseInt($value)";
			break;
			
			case 'double':
				return "parseFloat($value)";
			break;
			
			case 'array':
			case 'object':
				$s = "{ ";
				if ($type == "object") 
					{
					$value = get_object_vars($value);
					} 
				foreach ($value as $k=>$v) 
					{
					$esc_key = $this->escape_val($k);
					if (is_numeric($k)) 
						$s .= "$k: " . $this->get_js_repr($v) . ", ";
					else
						$s .= "\"$esc_key\": " . $this->get_js_repr($v) . ", ";
					}			
				if (count($value))
					$s = substr($s, 0, -2);
				return $this->char_translit($s) . " }";
			break;
			
			default:
				$esc_val = $this->escape_val($value);
				$s = "'".$esc_val."'";
				return $this->char_translit($s);
			break;			
			}	
	}
			
	private function escape_val($val)
	{
		$val = stripslashes($val);
		$val = str_replace("\\", "\\\\", $val);
		$val = str_replace("\r", "\\r", $val);
		$val = str_replace("\n", "\\n", $val);
		$val = str_replace("'", "\\'", $val);
		return str_replace('"', '\\"', $val);
	}
			
	private function get_all_javascript()
	{
		$this->output = $this->get_main_js();
		
		foreach($this->global_functions as $func)
		{
			$this->output .= $this->get_function_js($func);
		}
			
		return 	$this->output;
	}

	private function get_function_js($func_name) 
	{
		ob_start();	
		?>
		function x_<?php echo $func_name; ?>() {
		                	return <?php echo $this->instanceRandomName; ?>_do_call("<?php echo $func_name; ?>", x_<?php echo $func_name; ?>.arguments);
		                }
	                       
		<?php
		$js = ob_get_contents();
		ob_end_clean();
		
		//$js = str_replace(array('  ', '	',"\n", '\n','\r', chr(13)), array('','','','','', ''), $js);
		
		return $js;
		
	}	
			
		
	public function get_function_set_js()
	{
		foreach($this->global_functions as $func)
		{
			$ret .= $this->get_function_js($func);
		}
		return $ret;
	}

	
	
		
		
		
		
 }
?>