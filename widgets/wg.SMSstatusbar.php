<?php
class SMSstatusbar extends widgets implements iwidget
{
	
	public $sendlists = 0;
	public $sendSumm = 0;
	public $lastDate = 0;
	public $tarif = 0;
	

	public function main()
	{
	}
	
	
	public function out()
	{
		global $core;
		$sql = "SELECT 
					  cli.id,
					  (SELECT COUNT(*) FROM sms_sendlists as sl WHERE sl.client_id = cli.id AND sl.status NOT IN(2,6)) as sendlists,
					  (SELECT SUM(price_summ) FROM sms_sendlists as sl WHERE sl.client_id = cli.id AND sl.status NOT IN(2,6)) as slprice,
			          (SELECT st.price FROM sms_tarifs as st, sms_tarifs_clients as stc WHERE stc.client_id = cli.id AND st.id = stc.tarif_id ) as tarif
			          FROM 
		                   sms_clients AS cli
			          WHERE 
			                cli.user_id={$core->user->id}";
		$core->db->query($sql);
		$core->db->get_rows(1);
		
		$this->sendlists = $core->db->rows['sendlists'];
		$this->sendSumm = (!$this->sendlists)? '0.00':parce_digits($core->db->rows['slprice']);
		$this->tarif = $core->db->rows['tarif'];
		
		return array('sendlists'=>$this->sendlists, 'sendSumm'=>$this->sendSumm, 'tarif'=>$this->tarif);
	}
	
}
