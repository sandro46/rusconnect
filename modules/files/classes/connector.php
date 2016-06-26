<?php

class files_connector
{
	
	public $template = array();
	public $folders = array();
	public $treeHtml = '';
	
	
	
	public function __construct()
	{
		$this->template['item'] = '<span class="Folder" onclick="openFolder(%d)"><span class="Icon"  onclick="openFolder(%d)"></span></span><a href="#"  onclick="openFolder(%d)">%s</a>';
	}
	
	public function getFolders()
	{
		global $core;
				
		$core->db->select()->from('mcms_files_folders_entry')->fields('*')->where('site_id = '.$core->edit_site.' OR site_id = 0')->order('parent_id ASC, folder_id ASC');
		$core->db->execute();
		$core->db->get_rows(false, 'folder_id');
		
		$array = array();
		$idStack = array();
		
		foreach($core->db->rows as $item)
		{
			if($item['parent_id'] == 0)
			{
				$array[$item['folder_id']] = 	$item;
			}
			else 
				{
					if(isset($array[$item['parent_id']]))
					{
						$array[$item['parent_id']]['parent'][$item['folder_id']] = $item;
						$idStack[$item['folder_id']] =& $array[$item['parent_id']]['parent'][$item['folder_id']];
					}
					else 
						{	
							$idStack[$item['parent_id']]['parent'][$item['folder_id']] =& $item;
							$idStack[$item['folder_id']] =& $idStack[$item['parent_id']]['parent'][$item['folder_id']];
						}
				}			
		}
		
		$this->folders = $array;
		
		return $this->folders;		
	}
	
	public function createTree()
	{
		if(!$this->folders || !is_array($this->folders) || !($this->folders)) $this->getFolders();
				
		$this->treeHtml = $this->getTreeItemHtml($this->folders);
		
		return $this->treeHtml;		
	}
	
	public function getImages($folderId, $page=0, $limit=8)
	{
		$folderId = intval($folderId);
		
		if(!$folderId) return false;

		global $core;
		
		$page = intval($page);
		$limit = intval($limit);
		$start = $page * $limit;
		
		$core->db->select()->from('mcms_images')->fields('*')->where('folder_id = '.$folderId)->order('reg_date DESC')->limit($limit, $start);
		$core->db->execute();
		$core->db->get_rows();
		
		$core->tpl->assign('ConnectorImages', $core->db->rows);
		
		$core->db->select()->from('mcms_images')->fields('$count')->where('folder_id = '.$folderId);
		$core->db->execute();
		
		$total = $core->db->get_field();
		
		$pagenav =  ajax_pagenav($total, $limit, $page, 'updateImagesList', $folderId);
		
		$core->tpl->assign('pagenav', $pagenav);
		
		return $core->tpl->get('connector.imagesList.html', 'files');
	}
	
	private function getTreeItemHtml($folderArray)
	{
		$out = "<ul class='treeFolderMenuclosedFolder'>";
				
		if(!isset($folderArray['id']) && count($folderArray))
		{
			foreach($folderArray as $item)
			{
				if($item['parent_id'] == 0) $out = "<ul class='treeFolderMenuopenedFolder' style='padding-left:0px;'>";
				$html .= "<li id='treeFolderMenu-item-".$item['folder_id']."'".((!isset($item['parent']))? ' class="noSub"' : '').">";
				$html .= sprintf($this->template['item'], $item['folder_id'], $item['folder_id'], $item['folder_id'], $item['name']);
				if(isset($item['parent'])) $html .= $this->getTreeItemHtml($item['parent']);
				$html .= "</li>";
			}
		}
		else 
			{			
				if($folderArray['parent_id'] == 0) $out = "<ul class='treeFolderMenuopenedFolder' style='padding-left:0px;'>";		
				$html .= "<li id='treeFolderMenu-item-".$folderArray['folder_id']."'".((!isset($folderArray['parent']))? ' class="noSub"' : '').">";
				$html .= sprintf($this->template['item'], $folderArray['folder_id'], $folderArray['folder_id'], $folderArray['folder_id'], $folderArray['name']);
				if(isset($folderArray['parent'])) $html .= $this->getTreeItemHtml($folderArray['parent']);
				$html .= "</li>";
			}

		$out .= $html."</ul>";
		
		return $out;
	}
	
	
	
	
	
}


?>