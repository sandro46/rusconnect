<?php
class PagesMenu extends widgets implements iwidget 
{

	public $parent = 0;
	public $list = array();
	public $tree = false;
	public $level = 1;
	public $MultiFields = array();
	public $clildsFromRoot = false;
	public $rootItem = false;
	public $one_item_id = false;

	private $html = '';
	private $recursionLevel = 0;
	private $MaxRecursionLevel = 11;

	public function main()
	{

	}

	public function out()
	{
		//if($this->one_item_id)
		//{
		//	$this->one_item_id = intval($this->one_item_id);
		//	return $this->getDocItem($this->one_item_id);
		//}

		if($this->rootItem)
		{
			$this->list = $this->getRootDocId();
			return $this->list;
		}

		if($this->tree)
			$this->getPagesListTree();
		else
			$this->getPagesListFromParent();

		return $this->list;
	}

	private function getRootDocId()
	{
		global $core;

		$doc_id = $this->getPermanentLevel($this->parent);

		return $this->getDocItem($doc_id);
	}

	private function getDocItem($docId)
	{
		global $core;

		$sql = "SELECT d.id_doc, d.parent_id, d.title, d.rewrite_id, d.type, d.type_value, IF(d.rewrite_id>0,(SELECT rew.rewrite FROM mcms_rewrite as rew WHERE rew.id = d.rewrite_id),CONCAT('/docs/show/id/',d.id_doc,'/')) as rewrite FROM mcms_docs as d WHERE d.id_site = {$this->core->site_id} AND d.type = 6 AND d.lang_id = {$this->core->CONFIG['lang']['id']} AND d.id_doc = {$docId}";  
		$core->db->query($sql);
		$core->db->get_rows(1, 'id_doc');

		$data = $core->db->rows;

		if(!isset($data['id_doc'])) return false;

		$data_type_id = $this->core->multidb->getTypeByItemId($data['type_value']);
		$data_type_name = $this->core->multidb->getTypeNameById($data_type_id);

		$this->core->multidb->select()->type($data_type_name)->fields('*')->where('id = '.$data['type_value']);
		$this->core->multidb->get_rows();
		$data = array_merge($data, $this->core->multidb->rows);

		return $data;
	}

	private function getPagesListTree()
	{
		$this->list = array();

		if($this->clildsFromRoot)
		{
			$permanentLevel = 0;
			$this->recursionLevel = 0;
			$permanentLevel = $this->getPermanentLevel($this->parent);
			$this->parent = $permanentLevel;
			$levelORwhere = ($this->level>1)? "OR d.parent_id IN (SELECT ss.id_doc FROM mcms_docs as ss WHERE ss.id_site = {$this->core->site_id} AND ss.type = 6 AND ss.visible = 1 AND ss.lang_id = {$this->core->CONFIG['lang']['id']} AND ss.parent_id = {$permanentLevel})" : '';
			$sql = "SELECT d.id_doc, d.parent_id, d.title, d.rewrite_id, d.type, d.type_value, IF(d.rewrite_id>0,(SELECT rew.rewrite FROM mcms_rewrite as rew WHERE rew.id = d.rewrite_id),CONCAT('/docs/show/id/',d.id_doc,'/')) as rewrite FROM mcms_docs as d WHERE d.id_site = {$this->core->site_id} AND d.type = 6 AND d.visible = 1 AND d.lang_id = {$this->core->CONFIG['lang']['id']} AND (d.parent_id = {$permanentLevel} {$levelORwhere})  ORDER BY d.parent_id, d.order";  
		}
		else
		{
				$levelORwhere = ($this->level>1)? "OR d.parent_id IN (SELECT ss.id_doc FROM mcms_docs as ss WHERE ss.id_site = {$this->core->site_id} AND ss.type = 6 AND ss.visible = 1 AND ss.lang_id = {$this->core->CONFIG['lang']['id']} AND ss.parent_id = {$this->parent})" : '';
				$sql = "SELECT d.id_doc, d.parent_id, d.title, d.rewrite_id, d.type, d.type_value, IF(d.rewrite_id>0,(SELECT rew.rewrite FROM mcms_rewrite as rew WHERE rew.id = d.rewrite_id),CONCAT('/docs/show/id/',d.id_doc,'/')) as rewrite FROM mcms_docs as d WHERE d.id_site = {$this->core->site_id} AND d.type = 6 AND d.visible = 1 AND d.lang_id = {$this->core->CONFIG['lang']['id']} AND (d.parent_id = {$this->parent} {$levelORwhere})  ORDER BY d.parent_id, d.order";  
		}

		$this->core->db->query($sql);
		$result = $this->core->db->result;

		while($row = mysql_fetch_assoc($result))
		{
			if($row['type'] == 6)
			{
				$data_type_id = $this->core->multidb->getTypeByItemId($row['type_value']);
				$data_type_name = $this->core->multidb->getTypeNameById($data_type_id);

				$this->core->multidb->select()->type($data_type_name)->fields('*')->where('id = '.$row['type_value']);
				$this->core->multidb->get_rows();
				$row = array_merge($row, $this->core->multidb->rows);
			}
			
		
			if($row['parent_id'] == $this->parent)
			{
				$this->list[$row['id_doc']] = $row;	
				
				//if($this->MultiFields && count($this->MultiFields))
				//{
				//	$data_type_id = $this->core->multidb->getTypeByItemId($row['type_value']);
				//	$data_type_name = $this->core->multidb->getTypeNameById($data_type_id);
				//
				//	$this->core->multidb->select()->type($data_type_name)->fields($this->MultiFields)->where('id = '.$row['type_value']);
				//	$this->core->multidb->get_rows();
				//	$this->list[$row['id_doc']] = array_merge($this->list[$row['id_doc']], $this->core->multidb->rows);
				//}
			}
			else
				{	
					$_data = &array_recursive_finder($row['parent_id'], $this->list);
					$_data['childs'][$row['id_doc']] = $row;	
				}			
		}
	}

	private function getPagesListFromParent()
	{
		$sql = "SELECT d.id_doc, d.title, d.rewrite_id, d.type, d.type_value, IF(d.rewrite_id>0,(SELECT rew.rewrite FROM mcms_rewrite as rew WHERE rew.id = d.rewrite_id),CONCAT('/docs/show/id/',d.id_doc,'/')) as rewrite FROM mcms_docs as d WHERE d.id_site = {$this->core->site_id} AND d.type = 6 AND d.parent_id = {$this->parent} AND d.visible = 1 AND d.lang_id = {$this->core->CONFIG['lang']['id']} ORDER BY d.order";  
		
		$this->core->db->query($sql);
		$this->core->db->get_rows(false, 'id_doc');

		$this->list = $this->core->db->rows;

		foreach($this->list as $key => $item)
		{
			if($item['type'] == 6)
			{
				$data_type_id = $this->core->multidb->getTypeByItemId($item['type_value']);
				$data_type_name = $this->core->multidb->getTypeNameById($data_type_id);
				
				$this->core->multidb->select()->type($data_type_name)->fields('*')->where('id = '.$item['type_value']);
				$this->core->multidb->get_rows();
				$this->list[$key] = array_merge($this->list[$key], $this->core->multidb->rows);
			}
		}
		
	}
	
	// TODO: синькакод!!!!
	private function getPermanentLevel($curentId)
	{
		global $core;
		
		$core->db->select()->from('mcms_docs')->fields()->where('id_doc = '.$curentId)->limit('1');
		$core->db->execute();
		$core->db->get_rows(1);
		
		$rows = $core->db->rows;
		
		if($rows['parent_id'] == 0) return $rows['id_doc'];
		
		if($this->recursionLevel >= $this->MaxRecursionLevel) return 0;
		$this->recursionLevel ++;
		return $this->getPermanentLevel($rows['parent_id']);
	}
	
}



?>
