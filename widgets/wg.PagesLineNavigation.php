<?php
class PagesLineNavigation extends widgets implements iwidget 
{
	
	public $parent = 0;
	public $curentId = 0;
	public $list = array();
	
	private $html = '';
	
	
	
	public function main()
	{
		//$this->run($this->list);
	}
	
	public function out()
	{
				
		return $this->getPagesMenu();
	}
	
	private function getPagesMenu()
	{
		
		global $core;
		
		
		$sql = "SELECT doc.title, doc.id_doc as id, doc.parent_id, IF(doc.rewrite_id>0,(SELECT rew.rewrite FROM mcms_rewrite as rew WHERE rew.id = doc.rewrite_id),CONCAT('/docs/show/id/',doc.id_doc,'/')) as rewrite, (SELECT m.name FROM mcms_modules as m WHERE m.lang_id = 1 AND m.id_module = doc.type_value) as m_link FROM mcms_docs as doc WHERE doc.lang_id = ".$this->core->CONFIG['lang']['id']." AND doc.id_site = ".$this->core->site_id." AND doc.title != ''  AND doc.visible = 1 ORDER BY doc.parent_id, doc.order ASC";
		
		$this->core->db->query($sql);
		$this->core->db->get_rows(false, 'id');
		
		$list = $this->core->db->rows;
		$html = '';
		
		if($this->curentId != 0 && $this->curentId != $this->parent)
		{
			$curentId = $this->curentId;
			
			
			
			while(true)
			{
				if($curentId == $this->parent)	
				{
					break;
				}
				
				if($curentId == $this->curentId)
				{
					$html = $list[$curentId]['title'];
				}
				else 
					{
						$html = '<a href="/'.$this->core->CONFIG['lang']['name'].$list[$curentId]['rewrite'].'">'.$list[$curentId]['title'].'</a> <img src="/templates/images/s_right.gif"> '.$html;
					}
					
				
				
				$curentId = $list[$curentId]['parent_id'];
			}
			
			
		}
		
		return $html;	
	}
	
	
}



?>