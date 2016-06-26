<?php
class UserFeedback extends widget {
	public $limit = 20;

	public function UserFeedbackAction() {
		$this->limit = intval($this->limit);
		if(!$this->limit) $this->limit = 3;
		
		$sql = "SELECT * FROM tp_shop_user_feedback WHERE shop_id = {$this->core->site_id} ";
		
		$sql .= " LIMIT {$this->limit}";
		
		$this->core->db->query($sql);
		$this->core->db->get_rows();
		$list = $this->core->db->rows;
		
	
		
		if(empty($list)) return array();
		
		return $list;
	}
}
