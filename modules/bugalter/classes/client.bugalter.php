<?php

class client_bugalter
{
	private $companyId  = 0;
	private $clientId = 0;

	public function __construct(){
		global $core;
		if(!$core->user->custom_id) {
			$result = $this->auth();
			if(!$result[0]) {
				$this->companyId = false;
				$core->tpl->assign('UserError', $result[1]);
			}
		} else {
			$this->companyId = $core->user->custom_id;
		}
	}
	
	
	
	
	//** Trade Place **//
	public function tp_get_all_product_info($product_id = false, $site_id = false){
		global $core;
		
		$product_info = array();
		$product_id = intval($product_id);
		$site_id = intval($site_id);
		if(!$product_id || !$site_id)
			return "Товар не найден";
		
		$core->db->select()->from('tp_product')->fields('*')->where('product_id = '.$product_id.' AND client_id = '.$this->clientId.' AND site_id = '.$site_id);
		$core->db->execute();
		$core->db->get_rows();
		
		if($core->db->num_rows() != 1)
			return "Товар не найден";
		
		$product_info = $core->db->rows;

		
		return $product_info[0];
	}
	
	

	public function tp_add_product_parameter($site_id, $name, $id = false){
		global $core;
		$data = array();

		$site_id = intval($site_id);
		$id = intval($id);
		$name = mysql_real_escape_string((addslashes($name)));
		
		
		$data = array(
						'site_id' => $site_id,
						'client_id' => $this->clientId,
						'name' => $name
		);
		
		if($id) $data['id'] = $id;
			
		
		$core->db->autoupdate()->table('tp_product_parameters')->data(array($data))->primary('id');
		$core->db->execute();
		return $core->db->insert_id;
	}
	
	
	
	
	//** OLD **//
	public function getMyInfo() {
		global $core;
		
		$company = $this->getCompanyInfo($this->companyId);
		
		$core->db->select()->from('sv_clients')->fields('notice_email', 'notice_phone')->where('company_id = '.$this->companyId);
		$core->db->execute();
		$client = $core->db->get_rows(1);
		
		return array_merge($company, $client);
	}
	
	public function auth() {
		global $core;
		
		$sql = "SELECT * FROM sv_clients WHERE user_id = {$core->user->id}";
		$core->db->query($sql);
		$core->db->get_rows(1);
		
		if(!is_array($core->db->rows) || !isset($core->db->rows['id'])) return array(false, 'Пользователя с таким логином/паролем не существует!');
		if(!$core->db->rows['enabled']) return array(false, 'Ваша учетная запись заблокирована.');
		
		$this->companyId = $core->db->rows['company_id'];
		$this->clientId = $core->db->rows['company_id'];
		$_SESSION['custom_uid'] = $this->companyId;;
		
		return array(true, $core->db->rows);
	}
	
	public function getId() {
		return $this->companyId;
	}
		
	public function addPerson($id, $person) {
		global $core;
		
		$id = (intval($id))? intval($id) : false;

		$sv_persons = array(
			'company_id'=>$this->companyId,
			'start_date'=>self::makeTimestampFromDate($person['date_priem'],true),
			'base_value'=>self::numericCorrect($person['oklad']),
			'name'=>self::str($person['name']),
			'surname'=>self::str($person['surname']),
			'lastname'=>self::str($person['lastname']),
			'pasport_series'=>intval($person['pasport_series']),
			'pasport_number'=>intval($person['pasport_number']),
			'pasport_name'=>self::str($person['pasport_name']),
			'pasport_date'=>(self::testInputDate($person['date_priem'])? self::makeTimestampFromDate($person['date_priem'],true) : 0),
			'inn'=>(bugalter_valid::innCheck($person['inn'])? $person['inn']:0),
			'snils'=>(strlen($person['snils'])? $person['snils'] : ''),
			'birthday'=>(self::testInputDate($person['birthday'])? self::makeTimestampFromDate($person['birthday'], true) : ''),
			'post'=>intval($person['post']),
			'departament'=>intval($person['departament']),
			'region'=>intval($person['region']),
			'city'=>intval($person['city']),
			'address'=>self::str($person['address']),
			'work_phone'=>self::str($person['work_phone']),
			'work_phone_add'=>self::str($person['work_phone_add']),
			'mobile_phone'=>self::str($person['mobile_phone']),
			'home_phone'=>self::str($person['home_phone']),
			'email'=>self::str($person['email']),
			'skype'=>self::str($person['skype'])
		);
		
		if($id) {
			if(!$this->checkAccess('sv_persons', $id)) return false;
			$sv_persons['id'] = $id;
		}
		
		
		$core->db->autoupdate()->table('sv_persons')->data(array($sv_persons))->primary('id');
		$core->db->execute();

		if($id) {
			return array(true, 'Данные обновлены');
		}
		
		return array(true,'Новый сотрудник добавлен.');
	}
		
	public function gridToPdf($html, $filename) {
		global $core;
		
		//$css = file_get_contents(CORE_PATH.'static/my.enotice.loc/css/light/jquery.datatables.css');
		//$css .= file_get_contents(CORE_PATH.'static/my.enotice.loc/css/light/theme.css');
		
		$core->tpl->assign('gridContent', decode_unicode_url(stripslashes($html)));
		$core->tpl->assign('gridCSS', $css);
		$html = $core->tpl->get('gridToPdfContainer.html');
	
		$core->lib->load('topdf');
		$pdf = new topdf();
		$file = $pdf->add(decode_unicode_url(stripslashes($html)), 0, $core->site_id, $filename);
		return $file[1];
	}
	
	public function addIncommingPay($id = false, $data) {
		global $core;
		$id = ($id && intval($id))? intval($id) : false;
		
		if(!$id) {
			if(!isset($data['company']) || !intval($data['company'])) return -1;
			if(!isset($data['doc_num']) || !strlen($data['doc_num'])) return -2;
			if(!isset($data['summ']) || !floatval($data['summ'])) return -3;
			if(!isset($data['date']) || !self::testInputDate($data['date'])) return -4;
			
			$sv_payments = array(
				'invoice_id'=>intval($data['invoice']),
				'doc_id'=>self::str($data['doc_num']),
				'system_type'=>1,
				'budget_type'=>0,
				'type'=>1,
				'date'=>self::makeTimestampFromDate($data['date']),
				'source_company_id'=>intval($data['company']),
				'dest_company_id'=>$this->companyId,
				'dest_name_row_one'=>'',
				'dest_name_row_two'=>self::str($data['comment']),
				'local_create_date'=>time(),
				'value'=>floatval($data['summ']),
				'create_company_id'=>$this->companyId
			);
			
			if($sv_payments['invoice_id'] && $this->checkAccess('sv_invoices', $sv_payments['invoice_id'])) {
				$core->db->select()->from('sv_invoices')->fields('id','doc_num')->where("id = {$sv_payments['invoice_id']}");
				$core->db->execute();
				$core->db->get_rows(1);
				
				if(isset($core->db->rows['doc_num'])) {
					$sv_payments['invoice_num'] = $core->db->rows['doc_num'];
					$sv_invoices = array('id'=>$core->db->rows['id'],'status'=>2);
					$core->db->autoupdate()->table('sv_invoices')->primary('id');
					$core->db->execute();
				}
			} else {
				unset($sv_payments['invoice_id']);
			}

			$core->db->autoupdate()->table('sv_payments')->data(array($sv_payments));
			$core->db->execute();
			$id = $core->db->insert_id;
		} else {
			
			
		}
		
		return $id;
		
	}
	
	public function saveCompany($id = false, $data, $flag=false) {
		global $core;
		$id = ($id && intval($id))? intval($id) : false;
		
		if(!$id) {
		
			if(!isset($data['type']) || !intval($data['type'])) return -1;
			if(!isset($data['name']) || strlen($data['name']) <2) return -2;
			//if(!isset($data['address']) || strlen($data['address']) <2) return -3;
			if(isset($data['bik']) && strlen($data['bik'] >1) && !bugalter_valid::checkBik($data['bik'])) return -4; 
			if(isset($data['bill']) && strlen($data['bill'] >1) && !bugalter_valid::bankAccountCheck($data['bill'])) return -5;
			if(isset($data['inn']) && strlen($data['inn'] >1) && !bugalter_valid::innCheck($data['inn'])) return -6;
			if(isset($data['kpp']) && strlen($data['kpp'] >1) && strlen($data['kpp']) > 5 && !bugalter_valid::kppCheck($data['kpp'])) return -7;
			if(isset($data['ogrn']) && strlen($data['ogrn'] >1) && strlen($data['ogrn']) > 5 && !bugalter_valid::orgnCheck($data['ogrn'])) return -7;
		
			$sv_companies = array(
				'name'=>self::str($data['name']),
				'brand'=>self::str($data['brand']),
				'type'=>intval($data['type']),
				'address'=>self::str($data['address']),
				'phone'=>self::str($data['phone']),
				'site'=>self::str($data['site']),
				'mail'=>self::str($data['mail']),
				'parent_id'=>$this->companyId
			);
			
			$core->db->autoupdate()->table('sv_companies')->data(array($sv_companies));
			$core->db->execute();
			$id = $core->db->insert_id;
		} else {
			if($id > 0) {
				if(!$this->checkAccess('sv_companies', $id)) return false;
				$sv_companies = array('id'=>$id);
			} else {
				$sv_companies = array('id'=>$this->companyId);
				$sv_clients = array('company_id'=>$this->companyId);
				if(isset($data['notice_email']) && strlen($data['notice_email']) > 2) $sv_clients['notice_email'] = self::str($data['notice_email']);
				if(isset($data['notice_phone']) && strlen($data['notice_phone']) > 2) $sv_clients['notice_phone'] = self::str($data['notice_phone']);
				if(count($sv_clients > 1)) {
					$core->db->autoupdate()->table('sv_clients')->data(array($sv_clients))->primary('company_id');
					$core->db->execute();
				}		
			}
			
			if(isset($data['type']) && intval($data['type'])) $sv_companies['type'] = intval($data['type']);
			
			if($flag && $flag != 'no-update-name') {
				if(isset($data['name']) && strlen($data['name']) > 2) $sv_companies['name'] = self::str($data['name']);
			}
			
			if(isset($data['address']) && strlen($data['address']) > 2) $sv_companies['address'] = self::str($data['address']);
			if(isset($data['phone']) && strlen($data['phone']) > 2) $sv_companies['phone'] = self::str($data['phone']);
			if(isset($data['site']) && strlen($data['site']) > 2) $sv_companies['site'] = self::str($data['site']);
			if(isset($data['mail']) && strlen($data['mail']) > 2) $sv_companies['mail'] = self::str($data['mail']);
			if(isset($data['brand']) && strlen($data['brand']) > 2) $sv_companies['brand'] = self::str($data['brand']);
			
			if(count($sv_companies) > 1) {
				$core->db->autoupdate()->table('sv_companies')->data(array($sv_companies))->primary('id');
				$core->db->execute();
			}
		}
		
		$sv_companies_props = array('company_id'=>$id);
			
		if(isset($data['bik']) && bugalter_valid::checkBik($data['bik'])) $sv_companies_props['bank_bik'] = $data['bik'];
		if(isset($data['bill']) && bugalter_valid::bankAccountCheck($data['bill'])) $sv_companies_props['bill_num'] = $data['bill'];
		if(isset($data['inn']) && bugalter_valid::innCheck($data['inn'])) $sv_companies_props['inn'] = $data['inn'];
		if(isset($data['kpp']) && strlen($data['kpp']) > 5 && bugalter_valid::kppCheck($data['kpp'])) $sv_companies_props['kpp'] = $data['kpp']; 
		if(isset($data['ogrn']) && strlen($data['ogrn']) > 5 && bugalter_valid::orgnCheck($data['ogrn'])) $sv_companies_props['ogrn'] = $data['ogrn'];
	
		if(count($sv_companies_props) > 1) {
			$core->db->autoupdate()->table('sv_companies_props')->data(array($sv_companies_props))->primary('company_id');
			$core->db->execute();
		}
		
		return $id;		
	}
	
	public function saveContract($id = false, $data) {
		global $core;
		
		$id = ($id && intval($id))? intval($id) : false;
				
		if(!$id) {
			if(!isset($data['contragent']) || !intval($data['contragent'])) return false;
			if(!isset($data['category']) || !intval($data['category'])) return false;
			if(!isset($data['status']) || !intval($data['status'])) return false;
			if(!isset($data['date']) || !self::testInputDate($data['date'])) return false;
			if(!isset($data['name']) || !strlen($data['name'])) return false;
			if(!isset($data['doc_num']) || !strlen($data['doc_num'])) return false;
			
			$sv_contracts = array(
				'company_id'=>intval($data['contragent']),
				'category_id'=>intval($data['category']),
				'type_id'=>(($data['type'] == 1)? 1 : 2),
				'status_id'=>intval($data['status']),
				'date'=>self::makeTimestampFromDate($data['date']),
				'doc_num'=>self::str($data['doc_num']),
				'name'=>self::str($data['name']),
				'summ'=>((isset($data['summ']))? intval($data['summ']) : 0),
				'create_company_id'=>$this->companyId
			);
		} else {
			if(!$this->checkAccess('sv_contracts', $id)) return false;
			$sv_contracts = array('id'=>$id);
			
			if(isset($data['contragent']) && intval($data['contragent'])) 	$sv_contracts['company_id'] = intval($data['contragent']);
			if(isset($data['category']) && intval($data['category']))  		$sv_contracts['category_id'] = intval($data['category']);
			if(isset($data['status']) && intval($data['status'])) 			$sv_contracts['status_id'] = intval($data['status']);
			if(isset($data['date']) && self::testInputDate($data['date'])) 	$sv_contracts['date'] = self::makeTimestampFromDate($data['date']);
			if(isset($data['name']) && strlen($data['name'])) 				$sv_contracts['name'] = self::str($data['name']);
			if(isset($data['doc_num']) && strlen($data['doc_num'])) 		$sv_contracts['doc_num'] = self::str($data['doc_num']);
			if(isset($data['type'])) $sv_contracts['type_id'] = 			($data['type'] == 1)? 1 : 2;
			if(isset($data['summ']) && strlen($data['summ'])) 				$sv_contracts['summ'] = self::str($data['summ']);
		}
				
		$core->db->autoupdate()->table('sv_contracts')->data(array($sv_contracts))->primary('id');
		$core->db->execute();
		
		if(!$id) return $core->db->insert_id;
		
		return $id;
	}
	
	public function referenceAdd($refName, $name) {
		global $core;
		$name = self::str(urldecode($name));
		$data = array('name'=>$name, 'company_id'=>$this->companyId);
		
		if($refName == 'contract_category') {
			$core->db->autoupdate()->table('sv_contracts_categories')->data(array($data));
			$core->db->execute();
		}
		
		if($refName == 'contract_status') {
			$core->db->autoupdate()->table('sv_contracts_status')->data(array($data));
			$core->db->execute();
		}
		
		if($refName == 'person_post') {
			$core->db->autoupdate()->table('sv_persons_post')->data(array($data));
			$core->db->execute();
		}
		
		if($refName == 'person_departament') {
			$core->db->autoupdate()->table('sv_persons_departaments')->data(array($data));
			$core->db->execute();
		}
		
		$data['id'] = $core->db->insert_id;
		
		return $data;
	}
	
	public function makePay($destId, $doc_num, $summ, $comment) {
		global $core;
		
		if(!$destId = intval($destId)) return false;
		if(!$this->checkAccess('sv_companies', $destId)) return false;
		
		$summ = self::numericCorrect($summ);
		
		$pay = array('doc_id'=>$doc_num, 'date'=>date('d.m.y'),'pay_form'=>'электронно', 'status'=>'','value'=>num2str($summ),'summ'=>parce_digits($summ));
		
		$source = $this->getMyInfo();
		$dest = $this->getCompanyInfo($destId);
		
		$pay['source_inn'] = $source['inn'];
		$pay['source_kpp'] = $source['kpp'];
		$pay['source_bill_num'] = $source['bill_num'];
		$pay['source_bank_name'] = $source['bank_name'].($source['bank_address']? ' г. '.$source['bank_address'] : '');
		$pay['source_bank_bik'] = $source['bank_bik'];
		$pay['source_bank_kor_num'] = $source['bank_kor_num'];
		
		
		$pay['dest_inn'] = $dest['inn'];
		$pay['dest_kpp'] = $dest['kpp'];
		$pay['dest_bill_num'] = $dest['bill_num'];
		$pay['dest_bank_name'] = $dest['bank_name'].($dest['bank_address']? ' г. '.$dest['bank_address'] : '');
		$pay['dest_bank_bik'] = $dest['bank_bik'];
		$pay['dest_bank_kor_num'] = $dest['bank_kor_num'];
		$pay['dest_name_row_one'] = decode_unicode_url(urldecode($comment));
		$pay['queue'] = 6;
				
		$core->tpl->assign('pay', $pay);
		$core->tpl->assign('source', array('short_name'=>$source['short_name']));
		$core->tpl->assign('dest', array('short_name'=>$dest['short_name']));
		$html = $core->tpl->get('pay.forma.html');
		$core->lib->load('topdf');
		$pdf = new topdf();
		$file = $pdf->add($html, 0, $core->site_id, 'Платежное_поручение_'.$doc_num.'.pdf');
		
		return $file[1];
	}
	
	public function getInvoicePdf($invoiceId) {
		global $core;
		
		if(!$invoiceId = intval($invoiceId)) return false;
		if(!$this->checkAccess('sv_invoices', $invoiceId)) return false;

		$files = $this->getInvoiceFiles($invoiceId);
		if($files['pdf']) return $files['pdf'];
		
		$data = $this->getInvoices('invoices',0,1,'date','DESC',array('id'=>$invoiceId));
		$data = $data[0];
		$data['rows'] = $this->getInvoiceDetail($invoiceId);
		$data['rows_result'] = array('summ'=>$data['volume'],'summ_lang'=>num2str($data['summ_float']),'lang_count'=>plural::asString(count($data['rows']),plural::NEUTRAL,array('наименование','наименования','наименований')));
		$data['source_info'] = $this->getCompanyInfo($this->companyId);
		$data['destination_info'] = $this->getCompanyInfo($data['dest_company_id']);
		
		foreach($data['rows'] as $k=>$v) {
			$data['rows'][$k]['summ'] = parce_rub($v['summ']);
			$data['rows'][$k]['price'] = parce_rub($v['price']);
		}
		
		$core->tpl->assign('inv', $data);
		$html = $core->tpl->get('schet.forma.html');
	
		$core->lib->load('topdf');
		$pdf = new topdf();
		$file = $pdf->add($html, 0, $core->site_id, 'Счет на оплату для '.self::clearCompanyName($data['company_name']).' №'.$data['doc_num'].' от '.$data['date'].'.pdf');
		
		//$sql = "UPDATE sv_invoices SET pdf_file_id = ".$file[1]." WHERE id = ".$invoiceId;
		//$core->db->query($sql);
		
		return $file[1];
	}
	
	public function saveInvoice($id, $docNum, $date, $client, $contract, $summ, $rows) {
		global $core;
		
		$id = intval($id);
		if(!is_array($rows) || !count($rows)) return false;
		
		$sv_invoices = array(
			'doc_num'=>$docNum,
			'contract_id'=>intval($contract),
			'date'=>self::makeTimestampFromDate($date),
			'source_company_id'=>$this->companyId,
			'dest_company_id'=>intval($client),
			'volume'=>floatval($summ),
			'create_company_id'=>$this->companyId,
			'status'=>2		
		);
		
		$core->db->autoupdate()->table('sv_invoices')->data(array($sv_invoices));
		$core->db->execute();
		
		$invoiceId = $core->db->insert_id;
		$sv_invoices_items = array();
		
		foreach($rows as $item) {
			$sv_invoices_items[] = array(
				'invoice_id'=>$invoiceId,
				'name'=>mysql_real_escape_string(addslashes($item['name'])),
				'unit'=>mysql_real_escape_string(addslashes($item['unit'])),
				'count'=>intval($item['volume']),
				'price'=>floatval($item['price']),
				'summ'=>floatval($item['summ'])
			);
		}
		
		$core->db->autoupdate()->table('sv_invoices_items')->data($sv_invoices_items);
		$core->db->execute();
		
		return $invoiceId;
	}
	
	public function getActPdf($invoiceId) {
		global $core;
		
		if(!$invoiceId = intval($invoiceId)) return false;
		if(!$this->checkAccess('sv_acts', $invoiceId)) return false;

		//$files = $this->getActFiles($invoiceId);
		//if($files['pdf']) return $files['pdf'];
		
		$data = $this->getActs('invoices',0,1,'date','DESC',array('id'=>$invoiceId));
		$data = $data[0];
		$data['rows'] = $this->getActDetail($invoiceId);
		$data['rows_result'] = array('summ'=>$data['volume'],'summ_lang'=>num2str($data['summ_float']),'lang_count'=>plural::asString(count($data['rows']),plural::NEUTRAL,array('наименование','наименования','наименований')));
		$data['source_info'] = $this->getCompanyInfo($this->companyId);
		$data['destination_info'] = $this->getCompanyInfo($data['dest_company_id']);
		
		
		$core->tpl->assign('inv', $data);
		$html = $core->tpl->get('akt.forma.html', 'bugalter');
	
		
		
		$core->lib->load('topdf');
		$pdf = new topdf();
		$file = $pdf->add($html, 0, $core->site_id, 'Акт для '.self::clearCompanyName($data['company_name']).' №'.$data['doc_num'].' от '.$data['date'].'.pdf');
		
		//$sql = "UPDATE sv_acts SET pdf_file_id = ".$file[1]." WHERE id = ".$invoiceId;
		//$core->db->query($sql);
		
//		/return $data;
		return $file[1];
		
	}
	
	public function saveAct($id, $docNum, $date, $client, $contract, $summ, $rows, $invoiceId=0) {
		global $core;
		
		$id = intval($id);
		if(!is_array($rows) || !count($rows)) return false;
		
		$sv_invoices = array(
			'doc_num'=>$docNum,
			'contract_id'=>intval($contract),
			'date'=>self::makeTimestampFromDate($date),
			'source_company_id'=>$this->companyId,
			'dest_company_id'=>intval($client),
			'volume'=>floatval($summ),
			'create_company_id'=>$this->companyId,
			'invoice_id'=>intval($invoiceId),
			'status'=>2		
		);
		
		$core->db->autoupdate()->table('sv_acts')->data(array($sv_invoices));
		$core->db->execute();
		
		$invoiceId = $core->db->insert_id;
		$sv_invoices_items = array();
		
		foreach($rows as $item) {
			$count = (isset($item['count']))? intval($item['count']) : intval($item['volume']);
			
			$sv_invoices_items[] = array(
				'act_id'=>$invoiceId,
				'name'=>mysql_real_escape_string(addslashes($item['name'])),
				'unit'=>mysql_real_escape_string(addslashes($item['unit'])),
				'count'=>$count,
				'price'=>floatval($item['price']),
				'summ'=>floatval($item['summ'])
			);
		}
		
		$core->db->autoupdate()->table('sv_acts_items')->data($sv_invoices_items);
		$core->db->execute();
		
		return $invoiceId;
	}
	
	public function addActOnInvoice($info) {
		$invoiceId = intval($info['invoice']);
		
		$rows = $this->getInvoiceDetail($invoiceId);
		$fullInfo = $this->getInvoices('act', 0, 1, 'date', 'desc', array('id'=>$invoiceId));
		$fullInfo = $fullInfo[0];
		
		$id = $this->saveAct(false, $info['doc'], $info['date'], $fullInfo['dest_company_id'], $fullInfo['contract_id'], $fullInfo['summ_float'], $rows, $invoiceId);
		
		return $this->getActByInvoice($invoiceId);
	}
	
	public function autocomplete($instance, $request, $extended = false) {
		global $core;
		
		$request = addslashes(decode_unicode_url($request)); // пришло скорее всего в utf-8, нужно почистить строку
		$request = preg_replace("/[^а-яА-ЯёЁa-zA-Z0-9\ \-\_\+\=]+/isu",'',$request); // убираем все спец-вимволы
		
		if($instance == 'contragents' && strlen($request) > 1) {
			return $this->getContragentsReference($request);	
		}
		
		if($instance == 'contracts') {
				return $this->getContractrsReference($extended, $request);
		}
		
		if($instance == 'bik' && strlen($request) >= 1) {
			$request = preg_replace("/[^0-9]/", '',$request);
			return $this->getBankInfoForAutocomplete($request);
		}
		
		if($instance == 'invoices' && strlen($request) >= 1) {
			return $this->getInvoicesForAutocomplete($request,$extended);
		}
		
		if($instance == 'region') {
			return $core->geo->searchRegion($request,$extended);
		}
		
		if($instance == 'city') {
			return $core->geo->searchCity($request,$extended);
		}
		
		return array();
	}
	
	public function getNextInvoiceId() {
		global $core;
		
		$sql = "SELECT doc_num FROM sv_invoices WHERE source_company_id = {$this->companyId} AND create_company_id = {$this->companyId} ORDER BY date DESC LIMIT 1";
		$core->db->query($sql);
		$doc_num = $core->db->get_field();
		
		return intval($doc_num)+1;
	}
	
	public function getNextActId() {
		global $core;
		
		$sql = "SELECT doc_num FROM sv_acts WHERE source_company_id = {$this->companyId} AND create_company_id = {$this->companyId} ORDER BY date DESC LIMIT 1";
		$core->db->query($sql);
		$doc_num = $core->db->get_field();
		
		return intval($doc_num)+1;
	}
	
	public function getPersonName($personId) {
		global $core;
		
		if(!$personId = intval($personId)) return false;
		if(!$this->checkAccess('sv_persons', $personId)) return false;
		
		$core->db->select()->from('sv_persons')->fields('surname','name','lastname')->where('id = '.$personId);
		$core->db->execute();
		$core->db->get_rows(1);
		
		return $core->db->rows;
	}
	
	public function getPersonInfo($personId) {
		global $core;
		
		if(!$personId = intval($personId)) return false;
		if(!$this->checkAccess('sv_persons', $personId)) return false;
		
		$sql = "SELECT 
					p.id, p.name, p.surname, p.lastname, p.base_value, p.region, p.city, p.address, 
					p.pasport_series, p.pasport_number, p.pasport_name,
					p.inn, p.snils,
					p.post, p.departament,
					p.work_phone, p.work_phone_add, p.mobile_phone, p.home_phone, p.email, p.skype,
					
					IF(p.region, (SELECT r.title FROM geo_regions as r WHERE r.region_id = p.region), '') as region_name,
					IF(p.city, (SELECT c.title FROM geo_cities as c WHERE c.city_id = p.city), '') as city_name,
					
					FROM_UNIXTIME(p.start_date, '%d.%m.%Y') as start_date,
					FROM_UNIXTIME(p.birthday, '%d.%m.%Y') as birthday,
					FROM_UNIXTIME(p.pasport_date, '%d.%m.%Y') as pasport_date
					
				FROM 
					sv_persons as p
				WHERE 
					p.id = {$personId} AND p.company_id = {$this->companyId}";
		
		$core->db->query($sql);
		$core->db->get_rows(1);
		
		return $core->db->rows;
	}
	
	public function getPersonDetail($personId, $year = false) {
		global $core;
		if(!$year) $year = date('Y')-1;
		if(!$this->checkAccess('sv_persons', $personId)) return false;
		
		$sql = "SELECT 
					ps.*, 
					ROUND((ps.base_rate*0.13),2) as ndfl, 
					ROUND((ps.base_rate*0.2)) as pfr_ins, 
					ROUND((ps.base_rate*0.06)) as pfr_fund, 
					ROUND((ps.base_rate*0.031)) as ffoms, 
					ROUND((ps.base_rate*0.02)) as tfoms,
					ROUND((ps.base_rate*0.002),2) as fss_ins,
					ROUND((ps.base_rate*0.029),2) as fss_dis,
					
					IF(ndfl_pay_date, FROM_UNIXTIME(ndfl_pay_date, '%d.%m.%Y'), '-') as ndfl_pay_date,
					IF(ndfl_pay_date, FROM_UNIXTIME(paid_date, '%d.%m.%Y'), '-') as paid_date,
					IF(ndfl_pay_date, FROM_UNIXTIME(pfr_ins_pay_date, '%d.%m.%Y'), '-') as pfr_ins_pay_date,
					IF(ndfl_pay_date, FROM_UNIXTIME(ffoms_pay_date, '%d.%m.%Y'), '-') as ffoms_pay_date,
					IF(ndfl_pay_date, FROM_UNIXTIME(tfoms_pay_date, '%d.%m.%Y'), '-') as tfoms_pay_date,
					IF(ndfl_pay_date, FROM_UNIXTIME(fss_ins_pay_date, '%d.%m.%Y'), '-') as fss_ins_pay_date,
					IF(ndfl_pay_date, FROM_UNIXTIME(fss_dis_pay_date, '%d.%m.%Y'), '-') as fss_dis_pay_date
					
				FROM sv_persons_salary as ps WHERE ps.person_id = {$personId} AND year = $year";
		
		$core->db->query($sql);
		$core->db->get_rows(false, 'month');
		
		return $core->db->rows;
	}
	
	public function getPersonsList($year = false, $onlyList = false) {
		global $core;
	
		if(!$year) $year = date('Y');
		$month = intval(date('m'));
		
		$sql = "SELECT 
					p.id,p.base_value,
					CONCAT(p.surname, ' ', SUBSTRING(p.name,1,1), '. ', SUBSTRING(p.lastname,1,1), '.') as name, 
					FROM_UNIXTIME(p.start_date, '%d.%m.%Y') as start_date, 
					FROM_UNIXTIME(p.start_date, '%Y') as start_year,
					FROM_UNIXTIME(p.start_date, '%m')+0 as start_month
				FROM 
					sv_persons as p 
				WHERE 
					p.company_id = {$this->companyId} ";
				
		$core->db->query($sql);
		$core->db->get_rows();
		
		$persons = $core->db->rows;
		if($onlyList) return $persons;
		
		$langMonth = array (1=>'Январь', 2=>'Февраль', 3=>'Март', 4=>"Апрель", 5=>"Май", 6=>"Июнь", 7=>"Июль", 8=>"Август", 9=>"Сентябрь", 10=>"Октябрь", 11=>"Ноябрь", 12=>"Декабрь");
		$langEarn = 'Начислено';
		$langPaid = 'Уплачено';
		$langCred = 'Задолженость';
		$langNW = 'Не работал';
		$langFired = 'Уволен';
		$classes = array('paid'=>'rowPaid','cred'=>'rowNoPaid', 'nw'=>'bg11', 'fired'=>'bg12');

		foreach($persons as $k=>$item) {
			$sql = 'SELECT p.base_rate, p.paid, p.month+0 FROM sv_persons_salary as p WHERE p.person_id = '.$item['id'].' AND p.year = '.$year;
			$core->db->query($sql);
			$core->db->get_rows(false,'month');
			$paid = $core->db->rows;
			for($i=1; $i <= 12; $i++) {
				if(isset($paid[$i])) { // есть платежи за это тмесяц по этому сотруднику
					if(isset($paid[$i]['paid']) && $paid[$i]['paid'] >0) { // что то платили по сотруднику
						if(isset($paid[$i]['base_rate'])) {
							if($paid[$i]['paid'] < $paid[$i]['base_rate']) { // заплатили меньше чем нужно
								$persons[$k]['month'][$i] = array(
									'paid'=>$paid[$i]['base_rate'] - $paid[$i]['paid'],
									'earn'=>$paid[$i]['base_rate'],
									'class'=>$classes['cred'],
									'clickable'=>true,
									'title'=>$langCred.' за '.$langMonth[$i]
								);
							} else { // заплатили как надо либо больше
								$persons[$k]['month'][$i] = array(
									'paid'=>$paid[$i]['paid'],
									'earn'=>$paid[$i]['base_rate'],
									'class'=>$classes['paid'],
									'clickable'=>true,
									'title'=>$langPaid.' за '.$langMonth[$i]
								);
							}
						} else { // непонятно сколько начислили, так чт осчитаем что оплатили все
							$persons[$k]['month'][$i] = array(
								'paid'=>$paid[$i]['paid'],
								'earn'=>$paid[$i]['base_rate'],
								'class'=>$classes['paid'],
								'clickable'=>true,
								'title'=>$langPaid.' за '.$langMonth[$i]
							);
						}
					} else { // за этот месяц есть запись, но нет информации по оплате, считаем это долгом
						$persons[$k]['month'][$i] = array(
							'paid'=>0,
							'earn'=>$paid[$i]['base_rate'],
							'class'=>$classes['cred'],
							'clickable'=>true,
							'title'=>$langCred.' за '.$langMonth[$i]
						);
					}
				} else { // в базе нет какой либо информации по оплате либо начислениям за этот месяц по этому сотруднику
					
					if($item['start_year'] == $year || (isset($item['end_year']) && $item['end_year'] == $year)) { // сотрудник уволен в этом году либо принят на работу в этом году
						if(intval($item['start_month']) > $i) { // сотрудник еще не начал работать
							$persons[$k]['month'][$i] = array(
								'paid'=>'',
								'earn'=>'',
								'clickable'=>false,
								'class'=>$classes['nw'],
								'title'=>$langNW
							);
						} elseif($item['end_year'] == $year && isset($item['end_month']) && $item['end_month'] && intval($item['end_month']) <= $i) { // сотрудник уже уволен
							$persons[$k]['month'][$i] = array(
								'paid'=>'',
								'earn'=>'',
								'clickable'=>false,
								'class'=>$classes['fired'],
								'title'=>$langFired
							);
						} else { // сотрудник трудоустроен, но бабла ему не начислили, и не заплатили
							if($i > $month) { // возможно мы пытаемся взять период в будущем
								$persons[$k]['month'][$i] = array(
									'paid'=>'',
									'earn'=>'',
									'clickable'=>false,
									'class'=>'',
									'title'=>''
								);
							} else { // нет, сотрудник трудоустроен, но бабла ему не начислили, и не заплатили
								$persons[$k]['month'][$i] = array(
									'paid'=>0,
									'earn'=>0,
									'clickable'=>true,
									'class'=>$classes['cred'],
									'title'=>$langCred.' за '.$langMonth[$i]
								);
							}
						}
					} elseif($item['start_year'] > $year || (isset($item['end_year']) && $item['end_year'] > $year-60 && $item['end_year'] < $year)) { // сотрудник уволен еще в прошлом году, либо трудоу строится только со следующего года
						$persons[$k]['month'][$i] = array(
							'paid'=>'',
							'earn'=>'',
							'clickable'=>false,
							'class'=>(($item['start_year'] > $year)? $classes['nw'] : $classes['fired']),
							'title'=>(($item['start_year'] > $year)? $langNW : $langFired)
						);
					} elseif($i > $month && $year == date('Y')) { // возможно мы пытаемся взять период в будущем
						$persons[$k]['month'][$i] = array(
							'paid'=>'',
							'earn'=>'',
							'class'=>'',
							'clickable'=>false,
							'title'=>''
							
						);
					} else { // все варианты проверили. видимо просто сотруднику не дали бабла. плохо.
						$persons[$k]['month'][$i] = array(
							'paid'=>0,
							'earn'=>0,
							'clickable'=>true,
							'class'=>$classes['cred'],
							'title'=>$langCred.' за '.$langMonth[$i]
						);
					}
				}	
			}
		}
	
		return $persons;		
	}
	
	public function getPaymetTypes() {
		global $core;
		
		$sql = "SELECT id, name FROM sv_payments_sys_types ORDER BY name";
		$core->db->query($sql);
		$core->db->get_rows();
		return $core->db->rows;	
	}
	
	public function getPayments($instance, $page = 0, $limit = 20, $sortBy = 'date', $sortType = 'DESC', $filters = false) {
		global $core;
		$start = $page*$limit;
		
		if($sortBy == 'date') {
			$sortBy = 'p.date';
		} elseif($sortBy == 'outgoing_pay' || $sortBy == 'incomming_pay') {
			$sortBy == 'p.value';
		}
		
		$sql = "
			SELECT
				SQL_CALC_FOUND_ROWS
				p.*, FROM_UNIXTIME(p.date, '%d.%m.%Y') as date, 
				IF(p.source_company_id = {$this->companyId}, p.value, 0) as outgoing_pay,
				IF(p.dest_company_id = {$this->companyId}, p.value, 0) as incomming_pay,
				
				IF(p.budget_type = 0, 
					(SELECT st.name FROM sv_payments_sys_types as st WHERE st.id = p.system_type),
					(SELECT CONCAT((SELECT st.name FROM sv_payments_sys_types as st WHERE st.id = p.system_type), ' ', bt.name) FROM sv_payments_budget_types as bt WHERE bt.id = p.budget_type)
				) as type_name,
				
				CONCAT(p.dest_name_row_one, ' ',p.dest_name_row_two) as comment, 
				
				IF(p.source_company_id = {$this->companyId},
					get_company_name_short(p.dest_company_id),
					get_company_name_short(p.source_company_id)
				) as company_name,
				
				IF(p.invoice_id,
					(SELECT inv.doc_num FROM sv_invoices as inv WHERE inv.id = p.invoice_id),
					''
				) as invoice_doc_num
				
				FROM sv_payments as p
				
				WHERE p.create_company_id = {$this->companyId} ";
		
		if(isset($filters['contragentId']) && intval($filters['contragentId'])) {
			$comp = intval($filters['contragentId']);
			$sql .= " AND (p.dest_company_id = {$comp} OR p.source_company_id = {$comp})";
		}
				
		if(isset($filters['type']) && intval($filters['type'])) {
			$type = intval($filters['type']);
			if($type == 100) { // все доходы
				$sql .= " AND p.dest_company_id = {$this->companyId} AND p.source_company_id <> {$this->companyId}";
			} elseif($type == 200) { // все расходы
				$sql .= " AND p.source_company_id = {$this->companyId} AND p.dest_company_id <> {$this->companyId}";
			} else {
				$sql .= " AND p.system_type = {$type}";
			}
		}
				
		if(isset($filters['dateFrom']) && isset($filters['dateTo']) && self::testInputDate($filters['dateFrom']) && self::testInputDate($filters['dateTo'])) {
			$sql .= " AND p.date >= ".self::makeTimestampFromDate($filters['dateFrom'],true);
			$sql .= " AND p.date <= ".self::makeTimestampFromDate($filters['dateTo'],false,true);
		}
		
		
		$sql .= " ORDER BY $sortBy $sortType LIMIT $start,$limit";
		
		$core->db->query($sql);
		$core->db->add_fields_deform(array('incomming_pay_summ', 'outgoing_pay__summ', 'invoice_sum'));
		$core->db->add_fields_func(array('parce_rub','parce_rub','parce_rub'));
		$core->db->get_rows();
		return $core->db->rows;	
	}

	public function getCompanies($instance, $page = 0, $limit = 20, $sortBy = 'date', $sortType = 'DESC', $filters = false) {
		global $core;
		$start = $page*$limit;
		$sql = "SELECT
					SQL_CALC_FOUND_ROWS
					c.id, c.brand, c.phone, c.mail,
					get_company_name_short(c.id) as company_name,
					ct.short_name,
					
					(SELECT ROUND(SUM(i.volume),2) FROM sv_invoices as i WHERE i.source_company_id = {$this->companyId} AND i.dest_company_id = c.id) as invoice_sum,
					(SELECT ROUND(SUM(p.value),2) FROM sv_payments as p WHERE p.source_company_id  = c.id) as incomming_pay_summ,
					(SELECT ROUND(SUM(p.value),2) FROM sv_payments as p WHERE p.dest_company_id  = c.id) as outgoing_pay__summ
					
					
				FROM sv_companies as c
					LEFT JOIN sv_companies_types as ct ON ct.id = c.type
					
				
				WHERE c.id != {$this->companyId} AND c.parent_id = {$this->companyId} ";
		
		if(isset($filters['like_name'])) {
			$searche = addslashes(decode_unicode_url($filters['like_name'])); // пришло скорее всего в utf-8, нужно почистить строку
			$searche = preg_replace("/[^а-яА-ЯёЁa-zA-Z0-9\ \-\_\+\=]+/isu",'',$searche); // убираем все спец-вимволы

			$sql .= " AND LOWER(REPLACE(CONCAT(ct.short_name, ' ', c.name), '\"','')) LIKE LOWER('%{$searche}%')";
		}
		
		$sql .= " ORDER BY $sortBy $sortType LIMIT $start,$limit";
		
		$core->db->query($sql);
		$core->db->add_fields_deform(array('incomming_pay_summ', 'outgoing_pay__summ', 'invoice_sum'));
		$core->db->add_fields_func(array('parce_rub','parce_rub','parce_rub'));
		$core->db->get_rows();
		
		return $core->db->rows;	
	}
	
	public function getContracts($instance, $page = 0, $limit = 20, $sortBy = 'date', $sortType = 'DESC', $filters = false) {
		global $core;
		$start = $page*$limit;
		
		if($sortBy == 'date') $sortBy = 'c.date';
		
		$sql = "
			SELECT
				SQL_CALC_FOUND_ROWS
				c.id, c.company_id, c.category_id, c.type_id, c.status_id, c.doc_num, c.name, FROM_UNIXTIME(c.date, '%d.%m.%Y') as date, c.summ as summ_float, c.summ,
				get_company_name_short(c.company_id) as company_name,
				
				cat.name as category_name,
				cat.color as category_color,
				t.name as type_name, 
				s.name as status_name
				
			FROM sv_contracts as c

				LEFT JOIN sv_contracts_categories as cat ON cat.id = c.category_id
				LEFT JOIN sv_contracts_status as s ON s.id = c.status_id
				LEFT JOIN sv_contracts_types as t ON t.id = c.type_id

			WHERE c.create_company_id = {$this->companyId} ";
		
		if(isset($filters['contragentId']) && intval($filters['contragentId'])) {
			$sql .= " AND c.company_id = ".intval($filters['contragentId']);
		}
				
		if(isset($filters['status']) && intval($filters['status'])) {
			$sql .= " AND c.status_id = ".intval($filters['status']);
		}
		
		if(isset($filters['contractCategory']) && intval($filters['contractCategory'])) {
			$sql .= " AND c.category_id = ".intval($filters['contractCategory']);
		}
		
		if(isset($filters['dateFrom']) && isset($filters['dateTo']) && self::testInputDate($filters['dateFrom']) && self::testInputDate($filters['dateTo'])) {
			$sql .= " AND c.date >= ".self::makeTimestampFromDate($filters['dateFrom'],true);
			$sql .= " AND c.date <= ".self::makeTimestampFromDate($filters['dateTo'],false,true);
		}
		
		
		
		$sql .= " ORDER BY $sortBy $sortType LIMIT $start,$limit";
		
		$core->db->query($sql);
		$core->db->add_fields_deform(array('summ'));
		$core->db->add_fields_func(array('parce_rub'));
		$core->db->get_rows();
		
		return $core->db->rows;	
	}
	
	public function updateInvoiceStatus($id, $statusId) {
		global $core;
		
		if(!$id = intval($id)) return false;
		if(!$statusId = intval($statusId)) return false;
		if(!$this->checkAccess('sv_invoices', $id)) return false;
		
		$sql = "UPDATE sv_invoices SET `status` = {$statusId} WHERE id = {$id}";
		$core->db->query($sql);
		
		return true;
	}

	public function getInvoiceDetail($id) {
		global $core;
	
		if(!$id = intval($id)) return false;
		if(!$this->checkAccess('sv_invoices', $id)) return false;
		
		$core->db->select()->from('sv_invoices_items')->fields('*')->where('invoice_id = '.$id);
		$core->db->execute();
		$core->db->add_fields_deform(array('name'));
		$core->db->add_fields_func(array('client_bugalter::quoteReplace'));
		$core->db->get_rows();
		
		return $core->db->rows;
	}
	
	public function getInvoiceFiles($id) {
		global $core;
		
		$core->db->select()->from('sv_invoices')->fields('pdf_file_id', 'excel_file_id')->where("id = {$id} AND create_company_id = {$this->companyId}");
		$core->db->execute();
		
		$core->db->get_rows(1);
		
		if(!is_array($core->db->rows) || !isset($core->db->rows['pdf_file_id'])) return false;
		
		return array('pdf'=>$core->db->rows['pdf_file_id'], 'excel'=>$core->db->rows['excel_file_id']);
	}
	
	public function getInvoices($instance, $page = 0, $limit = 20, $sortBy = 'date', $sortType = 'DESC', $filters = false) {
		global $core;
		$start = $page*$limit;
		
		if($sortBy == 'date') $sortBy = 'i.date';
		if($sortBy == 'contract_date') $sortBy = 'c.date';
		
		$sql = "SELECT 
					SQL_CALC_FOUND_ROWS
					i.id, i.source_company_id, i.dest_company_id, i.volume, i.volume as summ_float, i.doc_num, i.contract_id, FROM_UNIXTIME(i.date, '%d.%m.%Y') as date, i.status as status_id,
					ist.name as status_name,
					IF(i.source_company_id = {$this->companyId}, get_company_name_short(i.dest_company_id), get_company_name_short(i.source_company_id)) company_name,
					c.doc_num as contract_num, c.name as contract_name, FROM_UNIXTIME(c.date, '%d.%m.%Y') as contract_date, c.category_id as contract_category_id,
					(SELECT cc.name FROM sv_contracts_categories as cc WHERE cc.id = c.category_id) as contract_category,
					(SELECT COUNT(*) FROM sv_invoices_items as ii WHERE ii.invoice_id = i.id) as rows_count,
					IF(i.source_company_id = {$this->companyId}, 'Исходящий', 'Входящий') as invoice_type
				FROM 
					sv_invoices as i
				LEFT JOIN sv_contracts as c ON c.id = i.contract_id
				LEFT JOIN sv_invoices_statuses as ist ON ist.id = i.status
				
				WHERE 
					i.create_company_id = {$this->companyId} ";
				
		if(isset($filters['contragentId']) && intval($filters['contragentId'])) {
			$comp = intval($filters['contragentId']);
			$sql .= " AND (i.source_company_id = {$comp} OR i.dest_company_id = {$comp})";
		} else {
			$sql .= " AND (i.source_company_id = {$this->companyId} OR i.dest_company_id = {$this->companyId})";
		}
		
		if(isset($filters['status']) && intval($filters['status'])) {
			$sql .= " AND i.status = ".intval($filters['status']);
		}
		
		if(isset($filters['contractCategory']) && intval($filters['contractCategory'])) {
			$sql .= " AND c.category_id = ".intval($filters['contractCategory']);
		}
		
		if(isset($filters['id']) && intval($filters['id'])) {
			$sql .= " AND i.id = ".intval($filters['id']);
		}
		
		
		if(isset($filters['dateFrom']) && isset($filters['dateTo']) && self::testInputDate($filters['dateFrom']) && self::testInputDate($filters['dateTo'])) {
			$sql .= " AND i.date >= ".self::makeTimestampFromDate($filters['dateFrom'],true);
			$sql .= " AND i.date <= ".self::makeTimestampFromDate($filters['dateTo'],false,true);
		}
		
		$sql .= " ORDER BY $sortBy $sortType LIMIT $start,$limit";
		//echo $sql;
		//return array($sql);
		
		$core->db->query($sql);
		$core->db->add_fields_deform(array('volume'));
		$core->db->add_fields_func(array('parce_rub'));
		$core->db->get_rows();
		
		return $core->db->rows;		
	}
	
	public function updateActStatus($id, $statusId) {
		global $core;
		
		if(!$id = intval($id)) return false;
		if(!$statusId = intval($statusId)) return false;
		if(!$this->checkAccess('sv_acts', $id)) return false;
		
		$sql = "UPDATE sv_acts SET `status` = {$statusId} WHERE id = {$id}";
		$core->db->query($sql);
		
		return true;
	}
	
	public function getActDetail($id) {
		global $core;
	
		if(!$id = intval($id)) return false;
		if(!$this->checkAccess('sv_acts', $id)) return false;
		
		$core->db->select()->from('sv_acts_items')->fields('*')->where('act_id = '.$id);
		$core->db->execute();
		$core->db->get_rows();
		
		return $core->db->rows;
	}
	
	public function getActFiles($id) {
		global $core;
		
		$core->db->select()->from('sv_invoices')->fields('pdf_file_id', 'excel_file_id')->where("id = {$id} AND create_company_id = {$this->companyId}");
		$core->db->execute();
		$core->db->get_rows(1);
		
		if(!is_array($core->db->rows) || !isset($core->db->rows['pdf_file_id'])) return false;
		
		return array('pdf'=>$core->db->rows['pdf_file_id'], 'excel'=>$core->db->rows['excel_file_id']);
	}
	
	public function getActByInvoice($invoiceId) {
		global $core;
		if(!$invoiceId = intval($invoiceId)) return false;
		if(!$this->checkAccess('sv_invoices', $invoiceId)) return false;
		
		$sql = "SELECT ac.doc_num, ac.id, FROM_UNIXTIME(ac.date, '%d.%m.%Y') as date, ac.pdf_file_id, ac.status, acs.name as status_name FROM sv_acts as ac LEFT JOIN sv_acts_statuses as acs ON acs.id = ac.status WHERE ac.invoice_id = {$invoiceId}";
		$core->db->query($sql);
		$core->db->get_rows(1);
		
		if(!$core->db->rows || !$core->db->rows['id']) return false;
		
		$info = array(
			'id'=>$core->db->rows['id'],
			'name'=>'№'.$core->db->rows['doc_num'].' от '.$core->db->rows['date'],
			'status_name'=>$core->db->rows['status_name'],
			'status_id'=>$core->db->rows['status']
		);
		
		return $info;
	}
	
	public function getActs($instance, $page = 0, $limit = 20, $sortBy = 'date', $sortType = 'DESC', $filters = false) {
		global $core;
		$start = $page*$limit;
		
		if($sortBy == 'date') $sortBy = 'i.date';
		if($sortBy == 'contract_date') $sortBy = 'c.date';
		
		$sql = "SELECT 
					SQL_CALC_FOUND_ROWS
					i.id, i.source_company_id, i.dest_company_id, i.volume, i.volume as summ_float, i.doc_num, i.contract_id, FROM_UNIXTIME(i.date, '%d.%m.%Y') as date, i.status as status_id,
					ist.name as status_name,
					IF(i.source_company_id = {$this->companyId}, get_company_name_short(i.dest_company_id), get_company_name_short(i.source_company_id)) company_name,
					c.doc_num as contract_num, c.name as contract_name, FROM_UNIXTIME(c.date, '%d.%m.%Y') as contract_date, c.category_id as contract_category_id,
					(SELECT cc.name FROM sv_contracts_categories as cc WHERE cc.id = c.category_id) as contract_category,
					(SELECT COUNT(*) FROM sv_invoices_items as ii WHERE ii.invoice_id = i.id) as rows_count,
					IF(i.source_company_id = {$this->companyId}, 'Исходящий', 'Входящий') as invoice_type
				FROM 
					sv_acts as i
				LEFT JOIN sv_contracts as c ON c.id = i.contract_id
				LEFT JOIN sv_acts_statuses as ist ON ist.id = i.status
				
				WHERE 
					i.create_company_id = {$this->companyId} ";
		
		if(isset($filters['contragentId']) && intval($filters['contragentId'])) {
			$comp = intval($filters['contragentId']);
			$sql .= " AND (i.source_company_id = {$comp} OR i.dest_company_id = {$comp})";
		} else {
			$sql .= " AND (i.source_company_id = {$this->companyId} OR i.dest_company_id = {$this->companyId})";
		}
		
		if(isset($filters['status']) && intval($filters['status'])) {
			$sql .= " AND i.status = ".intval($filters['status']);
		}
		
		if(isset($filters['contractCategory']) && intval($filters['contractCategory'])) {
			$sql .= " AND c.category_id = ".intval($filters['contractCategory']);
		}
		
		if(isset($filters['id']) && intval($filters['id'])) {
			$sql .= " AND i.id = ".intval($filters['id']);
		}
		
		
		if(isset($filters['dateFrom']) && isset($filters['dateTo']) && self::testInputDate($filters['dateFrom']) && self::testInputDate($filters['dateTo'])) {
			$sql .= " AND i.date >= ".self::makeTimestampFromDate($filters['dateFrom'],true);
			$sql .= " AND i.date <= ".self::makeTimestampFromDate($filters['dateTo'],false,true);
		}
		
		$sql .= " ORDER BY $sortBy $sortType LIMIT $start,$limit";
		
		$core->db->query($sql);
		$core->db->add_fields_deform(array('volume'));
		$core->db->add_fields_func(array('parce_rub'));
		$core->db->get_rows();
		
		return $core->db->rows;		
	}

	public function getContragentsReference($searche = false) {
		global $core;
		
		if($searche) {
			$sql = "SELECT c.id, REPLACE(CONCAT(ct.short_name, ' ', c.name), '\"','') as value, brand as sub FROM sv_companies as c LEFT JOIN sv_companies_types as ct ON ct.id = c.type WHERE c.parent_id 	= {$this->companyId} AND LOWER(REPLACE(CONCAT(ct.short_name, ' ', c.name), '\"','')) LIKE LOWER('%{$searche}%') ORDER BY c.name LIMIT 10";
		} else {
			$sql = "SELECT c.id, c.name, CONCAT(ct.short_name, ' ', c.name) as name1 FROM sv_companies as c LEFT JOIN sv_companies_types as ct ON ct.id = c.type WHERE c.parent_id 	= {$this->companyId} ORDER BY c.name ";
		}

		$core->db->query($sql);
		$core->db->add_fields_deform(array('name'));
		$core->db->add_fields_func(array('client_bugalter::clearCompanyName'));
		$core->db->get_rows();
		return $core->db->rows;	
	}
	
	public function getContractrsReference($contragentId = 0, $searche = false) {
		global $core;
		
		$sql = "SELECT id, doc_num as value, name as sub FROM sv_contracts WHERE create_company_id = {$this->companyId} ";
		if($contragentId) $sql .= ' AND company_id = '.$contragentId;
		if($searche) $sql .= " AND LOWER(doc_num) LIKE LOWER('%{$searche}%')";
		
		$sql .= ' ORDER BY doc_num';
		
		if($searche) $sql .= ' LIMIT 6';

		$core->db->query($sql);
		$core->db->get_rows();
		return $core->db->rows;	
	}
	
	public function getContractCategories() {
		global $core;
		
		$sql = "SELECT id, name FROM sv_contracts_categories WHERE company_id = 0 OR company_id = {$this->companyId} ORDER BY name";
		$core->db->query($sql);
		$core->db->get_rows();
		return $core->db->rows;	
	}
	
	public function getContractStatuses() {
		global $core;
		
		$sql = "SELECT id, name FROM sv_contracts_status WHERE company_id = 0 OR company_id = {$this->companyId} ORDER BY name";
		$core->db->query($sql);
		$core->db->get_rows();
		return $core->db->rows;	
	}
	
	public function getPersonsPost() {
		global $core;
			
		$sql = "SELECT id, name FROM sv_persons_post WHERE company_id = 0 OR company_id = {$this->companyId} ORDER BY name";
		$core->db->query($sql);
		$core->db->get_rows();
		return $core->db->rows;	
	}
	
	public function getPersonsDepartaments() {
		global $core;
			
		$sql = "SELECT id, name FROM sv_persons_departaments WHERE company_id = 0 OR company_id = {$this->companyId} ORDER BY name";
		$core->db->query($sql);
		$core->db->get_rows();
		return $core->db->rows;	
	}
		
	public function getinvoiceStatuses() {
		global $core;
		
		$sql = "SELECT id, name FROM sv_invoices_statuses WHERE company_id = 0 OR company_id = {$this->companyId} ORDER BY name";
		$core->db->query($sql);
		$core->db->get_rows();
		return $core->db->rows;	
	}
	
	public function getCompaniesTypes() {
		global $core;
		
		$sql = "SELECT id, short_name as name FROM sv_companies_types ORDER BY id";
		$core->db->query($sql);
		$core->db->get_rows();
		return $core->db->rows;	
	}
	
	public function getInvoicesForAutocomplete($searche, $contragent=false) {
		global $core;
		
		if($contragent && intval($contragent)) {
			$sql = "SELECT id, CONCAT('Счет № ',doc_num,' от ', FROM_UNIXTIME(date, '%d.%m.%Y'), ' на ',volume,' руб.') as value, volume, volume as volume2, CONCAT('для ',get_company_name_short(dest_company_id)) as sub FROM sv_invoices WHERE create_company_id = {$this->companyId} AND dest_company_id = ".intval($contragent);			
		} else {
			$sql = "SELECT id, CONCAT('Счет № ',doc_num,' от ', FROM_UNIXTIME(date, '%d.%m.%Y'), ' на ',volume,' руб.') as value, volume, volume as volume2, CONCAT('для ',get_company_name_short(dest_company_id)) as sub FROM sv_invoices WHERE create_company_id = {$this->companyId} AND LOWER(doc_num) LIKE LOWER('%{$searche}%')";
		}
		
		$core->db->query($sql);
		$core->db->get_rows();
		return $core->db->rows;
	}
	
	public function getBankInfoForAutocomplete($bik) {
		global $core;
		
		$sql = "SELECT bik as id, name as value, city as sub, kor_num as kor, address, telefon FROM sv_banks WHERE bik LIKE '{$bik}%' LIMIT 10";
		$core->db->query($sql);
		$core->db->get_rows();
			
		return $core->db->rows;
	}

	public function getCompanyInfo($id)
	{
		if(isset($this->companyCache[$id])) return $this->companyCache[$id];
		if($id != $this->companyId && !$this->checkAccess('sv_companies', $id)) return false;
		
		global $core;
		
		$sql = "SELECT 
					c.id,
					c.name,
					c.brand,
					c.type,
					get_company_name_short(c.id) as short_name,
					c.address, 
					c.phone,
					c.site,
					c.mail,
					c.director_name,
					c.director_position,
					c.nds
				FROM 
					sv_companies as c,
					sv_companies_types as ct

				WHERE 
					c.id = {$id} AND
					ct.id = c.type";
		$core->db->query($sql);
		$core->db->get_rows(1);
		
		if(!$core->db->rows || !isset($core->db->rows['name'])) return false;
		
		$company = $core->db->rows;
		
		$props = $this->getCompanyProps($id);
		if($props) $company = array_merge($company, $props);
		
		$this->companyCache[$id] = $company;

		return $company;
	}
	
	public function getCompanyProps($id)
	{
		if(isset($this->propertyCache[$id])) return $this->propertyCache[$id];
		
		global $core;
		
		if(!$id = intval($id)) return false;
		
		$sql = "SELECT
					cp.inn,
					IF(cp.kpp = 0, '', cp.kpp) as kpp,
					cp.ogrn,
					cp.legal_date,
					cp.bill_num,
					
					IF(cp.bank_kor_num,
						cp.bank_kor_num,
						(SELECT bd.kor_num FROM sv_banks as bd WHERE bd.bik = cp.bank_bik)
					) as bank_kor_num,
					
					IF(cp.bank_address,
						cp.bank_address,
						(SELECT bd.city FROM sv_banks as bd WHERE bd.bik = cp.bank_bik)
					) as bank_address,
					
					IF(cp.bank_name,
						cp.bank_name,
						(SELECT bd.name FROM sv_banks as bd WHERE bd.bik = cp.bank_bik)
					) as bank_name,
					
					cp.bank_bik
					
				FROM
					sv_companies_props as cp

				WHERE
					cp.company_id = {$id}";
		
		$core->db->query($sql);
		$core->db->get_rows(1);
		//echo $sql;
		if(!$core->db->rows || (!isset($core->db->rows['bank_bik']))) return false;
		
		$this->propertyCache[$id] = $core->db->rows;
		
		return $core->db->rows;
	}
	
	public function deltePayment($id) {
		global $core;
		if(!$id = intval($id)) return false;
		if(!$this->checkAccess('sv_payments', $id)) return false;
		
		$core->db->delete('sv_payments',intval($id), 'id');
		return true;
	}
	
	public function deleteCompany($id) {
		global $core;
		if(!$id = intval($id)) return -1;
		if(!$this->checkAccess('sv_companies', $id)) return -2;
		
		$core->db->delete('sv_companies',intval($id), 'id');
		$core->db->delete('sv_companies_props',intval($id), 'company_id');
		
		return true;
	}
	
	public function deleteContract($id) {
		global $core;
		if(!$id = intval($id)) return false;
		if(!$this->checkAccess('sv_contracts', $id)) return false;
		
		$sql = "DELETE FROM sv_contracts WHERE id = {$id} AND create_company_id = {$this->companyId}";
		$core->db->query($sql);
		
		return true;
	}
	
	public function deleteInvoice($id) {
		global $core;
		if(!$id = intval($id)) return false;
		
		$sql = "DELETE FROM sv_invoices WHERE id = {$id} AND create_company_id = {$this->companyId}";
		$core->db->query($sql);
		
		return true;
	}

	public function deleteAct($id) {
		global $core;
		if(!$id = intval($id)) return false;
		
		$sql = "DELETE FROM sv_acts WHERE id = {$id} AND create_company_id = {$this->companyId}";
		$core->db->query($sql);
		
		return true;
	}
	
	private function checkAccess($dateStructure, $primaryKey) {
		global $core;
		
		switch ($dateStructure) {
			case 'sv_companies':
				$sql = "SELECT id FROM sv_companies WHERE id = {$primaryKey} AND parent_id = {$this->companyId}";
				$core->db->query($sql);
				return $core->db->get_field() == $primaryKey;
			break;
			
			case 'sv_payments':
			case 'sv_contracts':
			case 'sv_invoices':
			case 'sv_acts':
				$sql = "SELECT id FROM {$dateStructure} WHERE id = {$primaryKey} AND create_company_id = {$this->companyId}";
				$core->db->query($sql);
				return $core->db->get_field() == $primaryKey;
			break;
			
			case 'sv_persons':
				$sql = "SELECT id FROM {$dateStructure} WHERE id = {$primaryKey} AND company_id = {$this->companyId}";
				$core->db->query($sql);
				return $core->db->get_field() == $primaryKey;
			break;
			
		}
		
		return false;
	}
	
	public static function testInputDate($str) {
		$str = addslashes($str);
		if(preg_match('/^[0-9]{2,2}\.[0-9]{2,2}\.[0-9]{4,4}$/',$str)) {
			return true;
		}
		return false;
	}
	
	public static function makeTimestampFromDate($str,$fromstart=false,$toend=false) {
		$str = explode('.',$str);
		if($fromstart) {
			return mktime(0,0,1,$str[1],$str[0],$str[2]);
		} elseif($toend) {
			return mktime(23,59,59,$str[1],$str[0],$str[2]);
		} else {
			return mktime(0,0,0,$str[1],$str[0],$str[2]);
		}
	}
	
	public static function clearCompanyName($str) {
		return str_replace(array('\"','"'), array('',''), $str);	
	}
	
	public static function str($str) {
		return mysql_real_escape_string(decode_unicode_url($str));
	}
	
	public static function quoteReplace($str) {
		$str = stripslashes($str);
		$str =  preg_replace("/\"(.+?)\"/","&laquo;\\1&raquo;",$str);
		$str =  str_replace(array("\&laquo;", "\&raquo;"), array('&laquo;','&raquo;'), $str);
		return $str;
	}
	
	public static function checkBik($bik) {
		$bik = preg_replace("/[^0-9]/",'',$bik);
		if(strlen($bik) != 9) return false;
		
		return $bik;
	}

	public static function numericCorrect($num) {
		$num = preg_replace("/[^0-9\.\,]/",'',$num);
		$num = str_replace(',','.',$num);
		
		if(strpos($num,'.') !== false) {
			$num = explode('.', $num);
			$num = round(intval($num[0])+floatval('0.'.$num[1]),2);
		} else {
			$num = intval($num);
		}
		
		return $num;
	}

}


/**
 * Класс для валидации различных данных
 * Например ИНН компании можно проверить по специальному алгоритму
 */
class bugalter_valid {
	
	/**
	 * Проверка ИНН 
	 * @param $inn string or integer (10 or 12 digits)
	 */
	public static function innCheck($inn) {
	    if (preg_match('/\D/', $inn)) return false;
	    
	    $inn = (string) $inn;
	    $len = strlen($inn);
	    
	    if ($len === 10) {
	        return $inn[9] === (string) (((
	            2*$inn[0] + 4*$inn[1] + 10*$inn[2] + 
	            3*$inn[3] + 5*$inn[4] +  9*$inn[5] + 
	            4*$inn[6] + 6*$inn[7] +  8*$inn[8]
	        ) % 11) % 10);
	    } elseif ( $len === 12 ) {
	        $num10 = (string) (((
	             7*$inn[0] + 2*$inn[1] + 4*$inn[2] +
	            10*$inn[3] + 3*$inn[4] + 5*$inn[5] + 
	             9*$inn[6] + 4*$inn[7] + 6*$inn[8] +
	             8*$inn[9]
	        ) % 11) % 10);
	        
	        $num11 = (string) (((
	            3*$inn[0] +  7*$inn[1] + 2*$inn[2] +
	            4*$inn[3] + 10*$inn[4] + 3*$inn[5] +
	            5*$inn[6] +  9*$inn[7] + 4*$inn[8] +
	            6*$inn[9] +  8*$inn[10]
	        ) % 11) % 10);
	        
	        return $inn[11] === $num11 && $inn[10] === $num10;
	    }
	    
	    return false;
	}
	
	/**
	 * Проверка КПП
	 * Проверяется толко на тип вводимых данных и длину
	 * @param string $kpp
	 */
	public static function kppCheck($kpp) {
		 $kpp = preg_replace('/[^0-9]/i', '', $kpp);
		 if(strlen($kpp) == 9) return true;
		 return false;
	}
	
	/**
	 * Проверка номера расчетного счета
	 * Проверяется толко на тип вводимых данных и длину
	 * @param string $billNum
	 */
	public static function bankAccountCheck($billNum) {
		$billNum = preg_replace('/[^0-9]/i', '', $billNum);
		if(strlen($billNum) == 20) return true;
		return false;
	}
	
	/**
	 * Проверка БИК банка
	 * Проверяется корректность данных и выполняется поиск в базе по этому БИК
	 * @param string $bik
	 */
	public static function checkBik($bik){
		$bik = preg_replace('/[^0-9]/i', '', $bik);
		if(strlen($bik) != 9) return false;
		
		global $core;
		
		$core->db->select()->from('sv_banks')->fields('*')->where('bik = "'.$bik.'"');
		$core->db->execute();
		$core->db->get_rows(1);
		
		if(!$core->db->rows || !isset($core->db->rows['name'])) return false;
		
		return true;
	}
	
	/**
	 * Проверка ОГРН и ОГРНИП 
	 * @param $inn string or integer (13 or 15 digits)
	 */
	public static function orgnCheck($ogrn) {
		$ogrn = preg_replace('/[^0-9]/i', '', $ogrn);
		if(strlen($ogrn) !== 13  && strlen($ogrn) !== 15) return false;
		if(strlen($ogrn) == 13) {
			$checksumm = floor(substr($ogrn,12));
			$expr = floor(substr($ogrn,0,12)) - (floor(substr($ogrn,0, 12)/11)*11);
			
			if($checksumm == $expr) return true;		
		}
		else {	
			$checksumm = floor(substr($ogrn,14));
			$expr = floor(substr($ogrn,0,14)) - (floor(substr($ogrn,0, 14)/13)*13);
			if($checksumm == $expr) return true;	
		}
		
		return false;
	}
}


class Plural {
 
     const MALE = 1;
     const FEMALE = 2;
     const NEUTRAL = 3;
    
     protected static $_digits = array(
         self::MALE => array('ноль', 'один', 'два', 'три', 'четыре','пять', 'шесть', 'семь', 'восемь', 'девять'),
         self::FEMALE => array('ноль', 'одна', 'две', 'три', 'четыре','пять', 'шесть', 'семь', 'восемь', 'девять'),
         self::NEUTRAL => array('ноль', 'одно', 'два', 'три', 'четыре','пять', 'шесть', 'семь', 'восемь', 'девять')
         );
    
     protected static $_ths = array(
         0 => array('','',''),
         1=> array('тысяча', 'тысячи', 'тысяч'),   
         2 => array('миллион', 'миллиона', 'миллионов'),
         3 => array('миллиард','миллиарда','миллиардов'),
         4 => array('триллион','триллиона','триллионов'),
         5 => array('квадриллион','квадриллиона','квадриллионов')
         );
    
     protected static $_ths_g = array(self::NEUTRAL, self::FEMALE, self::MALE, self::MALE, self::MALE, self::MALE); // hack 4 thsds
    
     protected static $_teens = array(
         0=>'десять',
         1=>'одиннадцать',
         2=>'двенадцать',
         3=>'тринадцать',
         4=>'четырнадцать',
         5=>'пятнадцать',
         6=>'шестнадцать',
         7=>'семнадцать',
         8=>'восемнадцать',
         9=>'девятнадцать'
         );
 
     protected static $_tens = array(
         2=>'двадцать',
         3=>'тридцать',
         4=>'сорок',
         5=>'пятьдесят',
         6=>'шестьдесят',
         7=>'семьдесят',
         8=>'восемьдесят',
         9=>'девяносто'
         );
    
     protected static $_hundreds = array(
         1=>'сто',
         2=>'двести',
         3=>'триста',
         4=>'четыреста',
         5=>'пятьсот',
         6=>'шестьсот',
         7=>'семьсот',
         8=>'восемьсот',
         9=>'девятьсот'
         );
    
     protected function _ending($value, array $endings = array()) {
         $result = '';
         if ($value < 2) $result = $endings[0];
         elseif ($value < 5) $result = $endings[1];
         else $result = $endings[2];
        
         return $result;   
     }
    
     protected function _triade($value, $mode = self::MALE, array $endings = array()) {
         $result = '';
         if ($value == 0) { return $result; }
         $triade = str_split(str_pad($value,3,'0',STR_PAD_LEFT));
         if ($triade[0]!=0) { $result.= (self::$_hundreds[$triade[0]].' '); }
         if ($triade[1]==1) { $result.= (self::$_teens[$triade[2]].' '); }
         elseif(($triade[1]!=0)) { $result.= (self::$_tens[$triade[1]].' '); }
         if (($triade[2]!=0)&&($triade[1]!=1)) { $result.= (self::$_digits[$mode][$triade[2]].' '); }
         if ($value!=0) { $ends = ($triade[1]==1?'1':'').$triade[2]; $result.= self::_ending($ends,$endings).' '; }
         return $result;
     }
    
     public function asString($value, $mode = self::MALE, array $endings = array()) {
         if (empty($endings)) { $endings = array('','',''); }
         $result = '';
         $steps = ceil(strlen($value)/3);
         $sv = str_pad($value, $steps*3, '0', STR_PAD_LEFT);
         for ($i=0; $i<$steps; $i++) {
             $triade = substr($sv, $i*3, 3);
             $iter = $steps - $i;
             $ends = ($iter!=1)?(self::$_ths[$iter-1]):($endings);
             $gender = ($iter!=1)?(self::$_ths_g[$iter-1]):($mode);
             $result.= self::_triade($triade,$gender, $ends);
         }
         return $result;
     }
    
 }

?>