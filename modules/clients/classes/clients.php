<?php 

class clients extends sv_module {
	
	public function getList($instance, $page = 0, $limit = 20, $sortBy = 'date', $sortType = 'DESC', $filters = false) {
		$start = $page*$limit;
		$sql = "SELECT 
					SQL_CALC_FOUND_ROWS 
					c.id, c.user_id, c.mail, c.reg_date, c.company_id, CONCAT(c.name_first, ' ', c.name_last) as contact_name, IF(c.enabled, 'ДА', 'НЕТ') as enable,
					u.login,
					get_company_name_short(c.company_id) as company_name
					
				FROM sv_clients as c
					LEFT JOIN mcms_user as u ON u.id_user = c.user_id 
				WHERE 1=1 ";
		
		if(isset($filters['dateFrom']) && isset($filters['dateTo']) && self::testInputDate($filters['dateFrom']) && self::testInputDate($filters['dateTo'])) {
			$sql .= " AND c.reg_date >= ".self::makeTimestampFromDate($filters['dateFrom'],true);
			$sql .= " AND c.reg_date <= ".self::makeTimestampFromDate($filters['dateTo'],false,true);
		}
		
		if(isset($filters['specialFilterRequest']) && intval($filters['specialFilterRequest']) > 0) {
			$filterType = intval($filters['specialFilterRequest']);
			if($filterType == 1) {
				$sql .= " AND c.is_test_account <> 1";
			} elseif($filterType == 2) {
				$sql .= " AND c.is_test_account = 1";
			}
		}
		
		if(isset($filters['specialFilterActive']) && intval($filters['specialFilterActive']) > 0) {
			$filterType = intval($filters['specialFilterActive']);
			
			if($filterType == 1) {
				$sql .= " AND c.is_free <> 1 AND c.is_expired <> 1 AND EXISTS(SELECT ai.client_id FROM sv_account_invoices as ai WHERE ai.client_id = c.id)  ";
			} elseif($filterType == 2) {
				$sql .= " AND c.reg_date < ".(time()-86400);
			} elseif($filterType == 3) {
				$sql .= " AND NOT EXISTS(SELECT ll.client_id FROM sys_login_log as ll WHERE ll.client_id = c.id)";
			}
		}
		
		$sql .= " ORDER BY $sortBy $sortType LIMIT $start,$limit";
				//echo $sql;
		$this->db->query($sql);
		$this->db->add_fields_deform(array('reg_date'));
		$this->db->add_fields_func(array('dateAgo'));
		$this->db->get_rows();
		
		return $this->db->rows;	
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
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}
	
?>
	