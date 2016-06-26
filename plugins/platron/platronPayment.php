<?php

/**
 * @property-read string $merchant
 * @property-read string $secret_key
 * @property-read string $lifetime
 * @property-read string $testmode
 */
class platronPayment 
{
    private $merchant_id;
	private $order_id;
	private $secret_key;
	private $url = 'https://www.platron.ru/payment.php';
    private $db = '';
    private $shopId;
    private $clientId;
    private $paysystem;
    
	public $lifetime = 60;
	public $testmode = false;
	public $paysystemId;
	
	const STATE_CAPTURED = 2;
	const STATE_CANCELED = 4;
	
	public function __construct($merchantId, $secretKey, $clientId, $shopId, $db) {
	    $this->merchant_id = $merchantId;
	    $this->secret_key = $secretKey;
	    $this->db = $db;
	    $this->clientId = $clientId;
	    $this->shopId = $shopId;
	    
	    if(!class_exists('PG_Signature')) {
	        require_once dirname(__FILE__).'/PG_Signature.php';
	    }
	}
	
    public function payment($order, $paySystemId)
    {	
        $this->paysystemId = intval($paySystemId);
        $this->paysystem = $this->getPaySystemAlias($this->paysystemId);
        
        if($this->testmode) {
            $this->paysystem = 'TESTCARD';
        }
        
        
        if(!$this->paysystem) {
            return array(
                'error'=>101,
                'message'=>'paysystem not valid'  
            );
        }
        
        $form_fields = array(
            'pg_merchant_id'	=> $this->merchant_id,
			'pg_currency'		=> 'RUR',
            'pg_amount'			=> $order['sum'],
            'pg_lifetime'		=> $this->lifetime * 60,
			'pg_testing_mode'	=> $this->testmode,
            'pg_description'	=> 'Заказ в магазине Rusconnect #' . $order['order_id'],
			'pg_result_url'		=> 'http://' . $_SERVER['SERVER_NAME'] .  "/ru/-utils/platron/op/result/id/{$order['order_id']}/",
			'pg_success_url'	=> 'http://' . $_SERVER['SERVER_NAME'] .  "/ru/-utils/platron/op/success/id/{$order['order_id']}/",
			'pg_failure_url'	=> 'http://' . $_SERVER['SERVER_NAME'] .  "/ru/-utils/platron/op/fail/id/{$order['order_id']}/",
            'pg_salt'			=> rand(21,43433), 
        	'pg_user_ip'		=> $_SERVER['REMOTE_ADDR'],
        	'pg_user_phone'		=> str_replace(" ", '', $order['contact']['phone']),
        	'pg_user_email'		=> $order['contact']['email'],
        	'pg_user_contact_email'=>$order['contact']['email'],
        	'pg_user_cardholder'=> 'none',
        	'pg_encoding' 		=> 'utf-8',
        );
		
        $paymentId = $this->savePaymentQuery($order, $this->paysystemId);
        $form_fields['pg_order_id'] = $paymentId;
        
		if(isset($this->paysystem) && $this->paysystem) {
			$form_fields['pg_payment_system'] = $this->paysystem;
			$form_fields['pg_sig'] = PG_Signature::make('init_payment.php', $form_fields, $this->secret_key);
			
			$query = 'https://www.platron.ru/init_payment.php?'.http_build_query($form_fields);
			$result = $this->p_query($query, false);
			
			return $result;
		} else {
			$form_fields['pg_sig'] = PG_Signature::make('payment.php', $form_fields, $this->secret_key);
			
			return array(
                'error'=>102,
			    'message'=>'not used mod without redirect'
			);
		}
    }

    private function savePaymentQuery($data) {
        $query = array(
            'client_id'=>$this->clientId,
            'shop_id'=>$this->shopId,
            'order_id'=>$data['order_id'],
            'pay_system_id'=>$this->paysystemId,
            'status_id'=>1,
            'amount'=>$data['sum'],
            'total'=>0
        );
        
        $this->db->autoupdate()->table('tp_payments')->data(array($query));
        $this->db->execute();
        
        return $this->db->insert_id;
    }
    
    private function updatePayment($data) {
        
        $query = array(
            'id'=> $data['pg_order_id'],
            'client_id'=>$this->clientId,
            'shop_id'=>$this->shopId,
            'status_id'=>$data['state'],
            'net_amount'=>$data['pg_net_amount'],
            'platron_id'=>$data['pg_payment_id']
        );
    
        $this->db->autoupdate()->table('tp_payments')->data(array($query))->primary('id');
        $this->db->execute();
    
        return true;
    }
    
    private function getPaySystemAlias($id) {
        if(!$id) return false;
        $sql = "SELECT alias FROM pay_system WHERE id = {$id}";
        $this->db->query($sql);
        $alias = $this->db->get_field();
        
        return (empty($alias))? false : $alias;
    }
    


    public function callbackHandler($request, $type)
    {	
		$thisScriptName = PG_Signature::getOurScriptName();
		if($thisScriptName == 'index.php') {
		    $thisScriptName = '';
		}
		if (empty($request['pg_sig']) || !PG_Signature::check($request['pg_sig'], $thisScriptName, $request, $this->secret_key) )
			throw new Exception('Invalid sign (1).');

		$arrResp = array();
		
		if($type == 'check'){
			
			$bCheckResult = 1;
			if(!$bCheckResult) $error_desc = "Товар не доступен";
			
			$arrResp['pg_salt']              = $request['pg_salt']; // в ответе необходимо указывать тот же pg_salt, что и в запросе
			$arrResp['pg_status']            = $bCheckResult ? 'ok' : 'error';
			$arrResp['pg_error_description'] = $bCheckResult ?  ""  : $error_desc;
			$arrResp['pg_sig']				 = PG_Signature::make($thisScriptName, $arrResp, $this->secret_key);
			
			$objResponse = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><response/>');
			$objResponse->addChild('pg_salt', $arrResp['pg_salt']);
			$objResponse->addChild('pg_status', $arrResp['pg_status']);
			$objResponse->addChild('pg_error_description', $arrResp['pg_error_description']);
			$objResponse->addChild('pg_sig', $arrResp['pg_sig']);
			
			print $objResponse->asXML();
		}
		elseif($type == 'result'){
			$response = $request;
			
			if ($request['pg_result'] == 1) {
				$response['state'] = self::STATE_CAPTURED;
			}
			else {
				$response['state'] = self::STATE_CANCELED;
			}
			
			$this->updatePayment($response);
			$objResponse = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><response/>');
			$objResponse->addChild('pg_salt', $request['pg_salt']); // в ответе необходимо указывать тот же pg_salt, что и в запросе
			$objResponse->addChild('pg_status', 'ok'); // !!! Здесь нет возможности проверить ни существования заказа, ни его статус. Так что ответ на оповещение может быть только ОК!
			$objResponse->addChild('pg_description', "Оплата принята");
			$objResponse->addChild('pg_sig', PG_Signature::makeXML($thisScriptName, $objResponse, $this->secret_key));

			header('Content-type: text/xml');
			print $objResponse->asXML();
		}
		else
			throw new Exception('Invalid request type.');
		
		return $response;
    }
    
    private function p_query($query, $debug = false) {
    	$curl = curl_init();
    	curl_setopt($curl, CURLOPT_URL, $query);
    	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    	
       	if($debug) {
    		$url = parse_url($this->query);
    		$url = "{$url['scheme']}://{$url['host']}{$url['path']}";
    		$_request = array();
    		echo "REQUEST_URL = [$url]\n";
    		parse_str(substr($this->query, strlen($url)+1), $_request);
    		print_r($_request);
    	}
    	
    	$result = curl_exec($curl);
    	$resultXml =  @simplexml_load_string($result);
    	
    	if(!is_object($resultXml)) return false;
    	
    	$_tmp_object = $this->simpleXMLToArray($resultXml);
    	
    	if(!isset($_tmp_object['children'])) return false;
    	
    	foreach($_tmp_object['children'] as $key=>$value) {
    		$resultObject[$key] = $value['value'];
    	}
    	
    
    	if($this->debug) {
    		print_r($resultObject);
    	}
    	
    	return $resultObject;
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
