<?php
class SiteMap extends widgets implements iwidget 
{
	
	
	public $ignoge = array();
	public $list = array();
	
	private $rec_level = 4;
	private $data = array();
	private $nums = array();
	private $site_id = 0;
	
	public function main()
	{

	}
	
	public function out()
	{
		global $core;
	
		$this->site_id = $core->site_id; 	
		$this->get_site_map();
		
		$this->html = $this->generate($this->data);
		return $this->html;
	}
	

	
	public function get_site_map()
	{
		global $core;
		
		$sql = "SELECT doc.title, doc.id_doc as id, doc.parent_id, IF(doc.rewrite_id>0,(SELECT rew.rewrite FROM mcms_rewrite as rew WHERE rew.id = doc.rewrite_id),CONCAT('/docs/show/id/',doc.id_doc,'/')) as rewrite, (SELECT m.name FROM mcms_modules as m WHERE m.lang_id = 1 AND m.id_module = doc.type_value) as m_link FROM mcms_docs as doc WHERE doc.lang_id = ".$core->CONFIG['lang']['id']." AND doc.id_site = ".$this->site_id." AND doc.title != ''  AND doc.visible = 1 ORDER BY doc.parent_id, doc.order ASC";
		
		$core->db->query($sql);
		
		while($row = mysql_fetch_assoc($core->db->result))
		{
			$this->list[] = $row;
			
			if($row['parent_id'] == 0)
			{
			$this->data[$row['id']] = $row;	
			}
			else
				{	
				$_data = & array_recursive_finder($row['parent_id'], $this->data);
				$_data['childs'][$row['id']] = $row;	
				}			
		}
	}	
	
	private function generate($array)
	{
		if(!is_array($array) || !count($array)) return;
		
		global $core;
		
		$out = "<ul>\n";
		if(isset($array['title']))
		{
			$out .= "<li><a href='/{$core->CONFIG['lang']['name']}{$array['rewrite']}'>{$array['title']}\n";
			if(isset($array['childs'])) $out .= $this->generate($array['childs'])."\n";
		}
		else 
			{
				foreach($array as $item)
				{
					$out .= "<li><a href='/{$core->CONFIG['lang']['name']}{$item['rewrite']}'>{$item['title']}\n";
					if(isset($item['childs'])) $out .= $this->generate($item['childs'])."\n";
					
				}
				
			}
		
			
		
		$out .= "</li>\n";
		$out .= "</ul>\n";
		
		return $out;
	}
		

	
	
	
	
	
	
	
	
	
}

?>