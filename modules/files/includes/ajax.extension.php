<?php
	

	function get_file_name_in_url($url)
	{
		return substr($url, strrpos($url, '/')+1);	
	}
	
	function get_file_icon_by_mime($mime_type)
	{
		switch($mime_type)
		{
			case 'application/pdf':
				return 'pdf.gif';
			break;
			
			case 'application/rtf':
			case 'application/msword':
				return 'doc.gif';
			break;
			
			case 'application/x-gtar':
			case 'application/x-gzip':
			case 'application/x-bzip2':
			case 'application/x-tar':
			case 'application/zip':
			case 'application/rar':
				return 'rar.gif';
			break;
				
			case 'audio/x-wav':			
				return 'wav.gif';
			break;
			
			case 'application/x-excel':
				return 'excel.gif';
			break;
			
			case 'text/css':
				return 'css.gif';
			break;
			
			case 'text/ini':
			case 'application/octet-stream':
				return 'ini.gif';
			break;
			
			case 'application/x-shockwave-flash':
				return 'swf.gif';
			break;
			
			case 'image/gif':
			case 'image/jpeg':
			case 'image/png':
				return 'jpg.gif';
			break;
			
			case 'application/x-msdownload':
				return 'prop.png';
			break;
			
			case 'text/html':
			case 'text/plain':
			case 'text/richtext':
				return 'txt.gif';
			break;
			
			case 'video/x-msvideo':
				return 'wav.gif';
			break;
			
			case 'folder':
				return 'folder.gif';
			break;
			
			default:
				return 'file.gif';
			break;
		}
	}
	
	
	
	function files_list_chenge_dir($dir_id)
	{
		global $core;
		
		$prev_folder_id = get_folder_id_parent_id($dir_id);
		
		$files = new files();
		$files->get_list($dir_id);
		
		$navigation = get_folder_navigation($dir_id);
		
		$core->tpl->assign('files_navigation_bar', $navigation);
		$core->tpl->assign('files_list', $files->list);
		$core->tpl->assign('this_folder', array('id'=>$dir_id, 'prev_id'=>$prev_folder_id['parent_id']));
		
		$html = $core->tpl->fetch('show.html',1,0,0,'files');
		
		preg_match_all("/\<div id='file_manager'\>(.*?)\<\/div\>\<\!--end file manager--\>/s",$html,$matchs);
		
		return $matchs[1][0];
	}
	
	function get_folder_navigation($id_folder)
	{
		global $core;
		
		$out = '';
		
		$folder_info = get_folder_id_parent_id($id_folder);
		
		if($folder_info['name']) $out = ' '.$folder_info['name'].' / ';
		
		
		if($folder_info['parent_id'] >0)
		{
		$prev_id = 	$folder_info['parent_id'];
		for($i = 0; $i< 10; $i++)
			{
			$folder_info = get_folder_id_parent_id($prev_id);
			$out = ' <a href="#" onClick="filesList_cd('.$folder_info['id_folder'].')"> '.$folder_info['name'].'</a> / '.$out;
			$prev_id = $folder_info['parent_id'];
			if($folder_info['parent_id'] == 0) break;
			}	
		}
	
		$out = '<a href="#" onClick="filesList_cd(0)">root</a> /'.$out;
		
		return $out;
	}
	
	function get_folder_id_parent_id($folder_id)
	{
		global $core;
		
		$core->db->select()->from('mcms_files_folders')->fields('parent_id', 'id_folder', 'name')->where('id_folder = '.$folder_id.' AND lang_id='.$core->CONFIG['lang']['id']);
		$core->db->execute();
		$core->db->get_rows(1);
		
		return $core->db->rows;
	}
	
	function get_window_folder_add($folder_id)
	{
		global $core;
		
		$core->tpl->assign('langs', $core->get_all_langs());
		$core->tpl->assign('this_folder_id', $folder_id);
		$html = $core->tpl->fetch('folder_add.html',1,0,0,'files');
		
		return $html;
	}
	
	function get_window_show_image_add($image_id)
	{
		global $core;
		
		$sql = "SELECT img.name, img.describe, img.filename, img.alias, img.width, img.height, img.size, (SELECT u.name FROM mcms_user as u WHERE u.id_user = img.uid AND u.lang_id = ".$core->CONFIG['lang']['id'].") as uname, img.reg_date FROM mcms_images as img WHERE img.id_image = ".intval($image_id).' AND img.lang_id = '.$core->CONFIG['lang']['id'];
		
		$core->db->query($sql);
		$core->db->colback_func_param = 'first';
		$core->db->add_fields_deform(array('name','describe', 'size', 'reg_date'));
		$core->db->add_fields_func(array('stripslashes', 'stripslashes', 'get_formated_file_size', 'now_date,"'.$core->CONFIG['lang']['name'].'"'));
		
		$core->db->get_rows(1);
		
		$core->tpl->assign('image_info',$core->db->rows);
		
		$html = $core->tpl->fetch('show_image.html',1,0,0,'files');
		
		return $html;
	}
	
	function add_folder_save($name_array, $folder_id)
	{
		global $core;
		
		$names = explode(',',$name_array);
		
		$triger = 1;
		$triger2 = '';
		
		$langs_names = array();
		
		$id_new_folder = $core->MaxId('id_folder', 'mcms_files_folders')+1;
		
		foreach($names as $name)
		{
			if($triger == 1)
			{
			$triger2 = $name;
			$triger = 2;
			}
			else
				{
				$triger = 1;
				$langs_names[$triger2]=$name;
				$triger2 = '';
				}
		}
		
		foreach($langs_names as $lang_id=>$name)
			$data[] = array('parent_id'=>$folder_id, 'name'=>$name, 'lang_id'=>$lang_id, 'id_folder'=>$id_new_folder, 'id_site'=>$core->edit_site);
		
		$core->db->autoupdate()->table('mcms_files_folders')->data($data);
		$core->db->execute();
		
		return $id_new_folder;
	}
	
	function delete_file($file_id)
	{
		global $files;

		$files->delete($file_id, 'file');
		
		return 0;
	}

	function delete_folder($folder_id)
	{
		global $files;
		
		$folder_info = get_folder_id_parent_id($folder_id);
		
		$files->delete($folder_id, 'folder');
		
		return $folder_info['parent_id'];
	}
	
	function delete_image($img_id)
	{
		global $files;
		
		$files->delete($img_id, 'image');
		
		return 0;
	}

	function get_image_info($img_id)
	{
		global $files;
		
		$files->get_image_info($img_id, 1);
		
		
		return 1;
	}
	
	function get_file_info($file_info)
	{
		global $files;
		
		
		return 0;
	}
	
	function get_folder_info($folder_info)
	{
		global $files;
		
		
		return 0;
	}

	function getImagesList($folderId, $page = 0)
	{
		if(!intval($folderId)) return false;
		
		$folderId = intval($folderId);
		$page = intval($page);
		
		$root = new files_connector();
		
		return $root->getImages($folderId, $page);
	}
	
	function getTreeList($folderId)
	{
		global $core;
		
		$folderId = intval($folderId);
		$array = array();
		
		$core->db->select()->from('mcms_files_folders_entry')->fields()->order('parent_id ASC, folder_id ASC')->lang();
		$core->db->execute();
		$core->db->get_rows(false, 'folder_id');
		
		$folders = $core->db->rows;
				
		$core->db->select()->from('mcms_files_folders_entry')->fields('parent_id')->where('folder_id = '.$folderId);
		$core->db->execute();
		
		$parent = $core->db->get_field();
		
		
		if($folderId != 0 && $parent != 0)
		{
			$curentId = $folderId;
			
			while(true)
			{
				if($curentId == 0)	
				{
					break;
				}
				
				if($curentId != $folderId)
				{
					$array[] = $curentId;
				}
								
				$curentId = $folders[$curentId]['parent_id'];
			}
			
		}
		
		$array = array_reverse($array);
		
		
		return $array;
	}
?>