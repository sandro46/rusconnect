<?php
class cupons extends sv_module
{
	
	public $isRootCompany = 0;
	
	public function postInit() {
		$this->isRootCompany = ($this->companyId == 1)? 1 : 0;
	}
	
	public function getCityList(){
		$sql = "SELECT * FROM geo_cities WHERE is_active = 1 ORDER BY title";
		$this->db->query($sql);
		$this->db->get_rows();
		return $this->db->rows;
	}
	
	public function saveCupon($id, $data) {

		$id = intval($id);
		if(!isset($data['region_id'])) $data['region_id'] = 1;
		$cid =  ($this->companyId == 1)? intval($data['company_id']) : $this->companyId;
		$site_cupons = array(
			'company_id'=>$cid,
			'category_id'=>intval($data['category']),
			//'region_id'=>intval($data['region_id']),
			'title'=>self::str($data['title']),
			'description'=>self::str($data['description']),
			'text'=>self::str($data['text']),
			'summ'=>floatval($data['summ']),
			'procent'=>floatval($data['discount']),
			'cover'=>$data['cover']
		);
		
		if($id) {
			$site_cupons['id'] = $id;
		} else {
			$site_cupons['date'] = time();
			$site_cupons['count'] = 0;
		}
		
		$this->db->autoupdate()->table('site_cupons')->data(array($site_cupons))->primary('id');
		$this->db->execute();
		
		if(!$id && $this->db->insert_id) {
			$id = $this->db->insert_id;
			$uniq = str_pad($id, 10, '0', STR_PAD_LEFT);
			$site_cupons = array('id'=>$id, 'uniq_id'=>$id);
		}
		
		$this->db->autoupdate()->table('site_cupons')->data(array($site_cupons))->primary('id');
		$this->db->execute();
		
		
		$sql = "DELETE FROM site_cupons_region WHERE cupon_id = {$id}";
		$this->db->query($sql);
		
		
		if(!$data['region_id']){
			$data['region_id'][0] = array('cupon_id' => $id, 'region_id' => 0);
		}else{
			foreach($data['region_id'] as $key=>$val){
				if(intval($val))
					$data['region_id'][$key] = array('cupon_id' => $id, 'region_id' => intval($val)); 
			}
		}
		
		$this->db->autoupdate()->table('site_cupons_region')->data($data['region_id']);
		$this->db->execute();
		
		return $id;
	}
	

	public function referenceAdd($refName, $name) {
		$name = self::str($name);
		$data = array('name'=>urldecode($name));
			
		if($refName == 'cupon_categories') {
			$this->db->autoupdate()->table('site_cupon_categories')->data(array($data));
			$this->db->execute();
		}
		
		$data['id'] = $this->db->insert_id;
		
		return $data;
	}
	
	public function deleteCupon($id) {	
		$id = intval($id);
		if(!$id) return false;
		
		if($this->companyId == 1) {
			$this->db->delete('site_cupons', $id, 'id');
		} else {
			$sql = "DELETE FROM site_cupons WHERE id = {$id} AND company_id = {$this->companyId}";
			$this->db->query($sql);
		}
		
		$sql = "DELETE FROM site_cupons_region WHERE cupon_id = {$id}";
		$this->db->query($sql);
		
		
		return true;
	}
	
	public function getList($instance, $page = 0, $limit = 20, $sortBy = 'date', $sortType = 'DESC', $filters = false) {
		$start = $page*$limit;
		//echo "!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!11";
		$sql = "SELECT SQL_CALC_FOUND_ROWS
					c.id, c.title, c.description, c.company_id, 
					(SELECT GROUP_CONCAT( r.region_id ) FROM site_cupons_region r WHERE r.cupon_id = c.id GROUP BY r.cupon_id) region_id, 
					c.category_id, c.uniq_id, c.text, c.date, c.summ, c.procent, 
					(SELECT COUNT(cs.id) FROM site_cupon_sended as cs WHERE cs.cupon_id = c.id) as count, c.cover,
					get_company_name_short(c.company_id) as company_name,
					
					cat.name as category_name
				FROM 
					site_cupons as c
				LEFT JOIN site_cupon_categories as cat ON cat.id = c.category_id
				WHERE 1 ";
		
		if($this->companyId == 1) {
			if(isset($filters['contragentId'])) {
				$sql .= ' AND c.company_id = '.intval($filters['contragentId']);
			}
		} else {
			$sql .= " AND c.company_id = {$this->companyId}";
		}
		
		if(isset($filters['сategory'])) {
			$sql .= ' AND c.category_id = '.intval($filters['сategory']);
		}
		
		$sql .= " ORDER BY $sortBy $sortType LIMIT $start,$limit";
		
		$this->db->query($sql);
		$this->db->add_fields_deform(array('title','description','text'));
		$this->db->add_fields_func(array('stripslashes','stripslashes','stripslashes'));
		$this->db->get_rows();
//print_r($this->db->rows);
		return $this->db->rows;	
	}
	
	public function getCuponCategories() {
		$sql = "SELECT id, name FROM site_cupon_categories ORDER BY id";
		$this->db->query($sql);
		$this->db->get_rows();
		return $this->db->rows;	
	}
	
	public function sendsms($phone,$text,$sender){
		if(!class_exists('littlesms')) include CORE_PATH.'modules/index/sms.php';
		
		$gate = new littlesms('ncity', 'g53qavdsbfdh2312', false);
		$gate->url = 'deamons.itbc.pro/api';
		$gate->messageSend($phone,$text, $sender);
	}
	
	public function sendEmail($title,$message,$mail, $from='noreply@ncity.biz') 
	{
		if(!class_exists('smtp_sender')) $this->lib->load('smtp');
		$mailer = new smtp_sender('smtp.itbc.pro', 'alexey@itbc.pro', 'QzWx123-');
		$mailer->defaultType = 'text/html';
		$mailer->send($from, $mail, $title, $message);
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
	
}