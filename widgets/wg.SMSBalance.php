<?php
class SMSBalance extends widgets implements iwidget
{
	
	public $rub = 0;
	public $sms = 0;
	public $tarif = 0;
	public $usePromised = 0;
	
	public function main()
	{
	}
	
	public function out($sendlist_id = false, $userId = false)
	{
		global $core, $controller;

		if(!$sendlist_id)
		{
			$userId = ($userId)? $userId : $core->user->id;
			$sql = "SELECT
			          pay.balance AS rub,
			          pay.balance AS rub_float,
			          pay.sms_count AS sms,
			          pay.sms_count AS sms_float,
			          (SELECT st.price FROM sms_tarifs as st, sms_tarifs_clients as stc WHERE stc.client_id = pay.client_id AND st.id = stc.tarif_id ) as tarif
			          FROM sms_payments AS pay,
		                   sms_clients AS cli
			          WHERE pay.client_id = cli.id AND
			                cli.user_id={$userId}
			                ORDER BY pay.date DESC LIMIT 1";
		}
		else
			{
				$sql = "
					SELECT
		          			pay.balance AS rub,
					          pay.balance AS rub_float,
					          pay.sms_count AS sms,
					          pay.sms_count AS sms_float,
					          (SELECT st.price FROM sms_tarifs as st, sms_tarifs_clients as stc WHERE stc.client_id = ss.client_id AND st.id = stc.tarif_id ) as tarif
				          FROM sms_payments AS pay,
			                   sms_sendlists AS ss
				          WHERE pay.client_id = ss.client_id AND
		                		ss.id = {$sendlist_id}
			                ORDER BY pay.date DESC LIMIT 1";
			}

		$core->db->query($sql);
		$core->db->colback_func_param = 0;
		$core->db->add_fields_deform(array('rub', 'sms'));
		$core->db->add_fields_func(array('parce_digits', 'parce_digits'));
		$core->db->get_rows(1);

		$balance = $core->db->rows;

		if(!$balance['rub']) $balance['rub'] = '0';
		if(!$balance['sms']) $balance['sms'] = '0';
		if(!$balance['sms_float']) $balance['sms_float'] = 0.00;
		if(!$balance['rub_float']) $balance['rub_float'] = 0.00;

		$balance['PROM'] = $this->usePromised;
		if($this->usePromised)
		{
			$sql = "SELECT
						SUM(pay.incomming_payment) as prom
					FROM
						sms_payments AS pay,
	                   	sms_clients AS cli
	                WHERE
	                	pay.client_id = cli.id AND
			            cli.user_id={$core->user->id} AND
			            pay.promised_payment = 1";
			$core->db->query($sql);
			$core->db->get_rows(1);

			if($core->db->rows && isset($core->db->rows['prom']) && $core->db->rows['prom'] != 0)
			{
				$balance['PROM_SUM'] = parce_digits($core->db->rows['prom']);
				$newBall = $balance['rub_float'] - $core->db->rows['prom'];
				$balance['rub'] = parce_digits($newBall);
				/*if($newBall<0)*/ $balance['PROM_INC'] = 1;
			}
		}

		if($core->user->id == 10)
		{
			global $controller;
			$controller->load('smsgate.php');
			$gate = new smsgate($core->CONFIG['littlesmsgate']['login'], $core->CONFIG['littlesmsgate']['apikey']);
			$this->rub = $gate->get_balance();
			$this->sms = $gate->get_sms_count();
			$balance['sms'] = parce_digits($this->sms);
			$balance['rub'] = parce_digits($this->rub);

		}
		else
		   {
			$this->rub = $balance['rub_float'];
                	$this->sms = $balance['sms_float'];
		   }

		$this->tarif = $balance['tarif'];


		return $balance;
	}
}
