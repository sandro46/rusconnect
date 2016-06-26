<?php
class SitesMenu extends widgets implements iwidget 
{
	private $list = Array();
	public $html = '';
	
	
	public function main()
	{
		global $core;
		
		$core->db->select()->from('mcms_sites')->fields('*')->order('id');
		$core->db->execute();
		$core->db->get_rows();
		
		$this->list = $core->db->rows;
		
		foreach($core->db->rows as $row)
		{
			//$html .= '<div siteid="2" style="cursor:pointer;" onClick="document.location.href=\\\'/'.$core->CONFIG['lang']['name'].'/-utils/changesite/id/'.$row['id'].'/\\\'">'.$row['server_name'].'</div>';   
			$this->html .= '<div siteid="2" style="cursor:pointer;" onClick="createCookie(\\\'site_id\\\', '.$row['id'].', 999999); document.location.href = \\\'/\\\';">'.$row['describe'].'</div>';   
			//$html .= '<li siteid="2"  onClick="createCookie(\\\'site_id\\\', '.$row['id'].', 999999); document.location.href = \\\'/\\\';"><a href="#" >'.$row['describe'].'</a></li>';
		}
		
		$this->run($this->html);
	}
	
	
	public function out()
	{

		return $this->list;
	}
	
	
}
?>