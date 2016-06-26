<?php

class files
{
	
	public $list = array();
	public $errors = array();
	
	private $upload_folder = '';
	private $site_id = 0; 
	
	
	
	
	public function __construct()
	{
		global $core;
		
		$this->site_id = $core->edit_site; 
	}
	
	public function get_list($folder_id, $filter = '*.*')
	{
		global $core;
		
		$sql_get_folders = "SELECT fld.id_folder AS `id` , fld.name, fld.name AS `alias` , 'folder.gif' AS `mime_type` , 1 AS `visible`, 
							(SELECT COUNT(*) FROM `mcms_files`  WHERE `folder_id` = fld.id_folder AND `lang_id` = ".$core->CONFIG['lang']['id'].") 
							+ 
							(SELECT COUNT(*) FROM `mcms_images` WHERE `folder_id` = fld.id_folder	AND `lang_id` = ".$core->CONFIG['lang']['id'].") 
							
							AS `count`, 0 as `size`, 0 AS width, 0 AS height, 0 AS `filename`,0 as `preview`,  0 as `p_width`,  0 as `p_height`
							FROM `mcms_files_folders` AS `fld` 
							WHERE fld.lang_id =  ".$core->CONFIG['lang']['id']." 
							AND fld.parent_id = ".$folder_id." AND fld.id_site = ".$core->edit_site." ORDER BY fld.id
							";
		
		$sql_get_images = "SELECT img.id_image AS `id` , img.name, img.alias, 'image/jpeg' AS `mime_type` , 1 AS `visible`, 0 as `count`, img.size, img.width, img.height, img.filename, img.filename as `preview`, img.p_width, img.p_height
							FROM `mcms_images` AS `img`
							WHERE img.lang_id = ".$core->CONFIG['lang']['id']."
							AND img.id_site = ".$core->edit_site."
							AND img.folder_id = ".$folder_id." ORDER BY img.alias
							";
		
		$sql_get_files = "SELECT fl.id_file AS `id` , fl.name, fl.alias, fl.mime_type, fl.visible, 0 as `count`, fl.size, 0 AS `width`, 0 AS `height`, 0 AS `filename`, 0 as `preview`,  0 as `p_width`,  0 as `p_height`
							FROM `mcms_files` AS `fl`
							WHERE fl.lang_id = ".$core->CONFIG['lang']['id']."
							AND fl.id_site = ".$core->edit_site."
							AND fl.folder_id = ".$folder_id." ORDER BY fl.alias
							";
		
		
		/*
		switch($filter)
		{
			case '*.*':
				$sql = $sql_get_folders.' UNION '.$sql_get_images.' UNION '.$sql_get_files;
			break;
			
			case '*.jpg':
				$sql = $sql_get_folders.' UNION '.$sql_get_images;
			break;
			
			case '*.file':
				$sql = $sql_get_folders.' UNION '.$sql_get_files;
			break;
			
		}
		*/
		
		
		$core->db->query($sql_get_folders);
		$core->db->get_rows();
		
		$folders = $core->db->rows;
		
		$core->db->query($sql_get_images);
		$core->db->add_fields_deform(array('filename','mime_type', 'size'));
		$core->db->add_fields_func(array('get_file_name_in_url', 'get_file_icon_by_mime', 'get_formated_file_size'));
		$core->db->get_rows();
		
		$images = $core->db->rows;
		
		
		$core->db->query($sql_get_files);
		$core->db->add_fields_deform(array('filename','mime_type', 'size'));
		$core->db->add_fields_func(array('get_file_name_in_url', 'get_file_icon_by_mime', 'get_formated_file_size'));
		$core->db->get_rows();
		
		$files = $core->db->rows;
				
		if(!count($folders)) $folders = array();
		if(!count($images)) $images = array();
		if(!count($files)) $files = array();
		
		$this->list = array_merge($folders, $images, $files);
		
		return $this->list;
	}
	
	public function image_upload()
	{
		global $core;
		
		$core->lib->load('images');

		$image = new images($core->edit_site);
		$image->upload();

		if(count($image->errors) > 0) die($image->print_error_report());


		if($_POST['create_logo'] == 1 && $_FILES['logo_in_image'])
			{			
			@move_uploaded_file($_FILES['logo_in_image']['tmp_name'], $core->CONFIG['vars']['temp'].$_FILES['logo_in_image']['name']);	
			
			$opacity = (intval($_POST['opacity']))? intval($_POST['opacity']) : 100;
			
			$margin['top'] 		= intval($_POST['margin_top']);
			$margin['bottom'] 	= intval($_POST['margin_bottom']);
			$margin['left'] 	= intval($_POST['margin_left']);
			$margin['right'] 	= intval($_POST['margin_right']);
			
			
			$image->merge($core->CONFIG['vars']['temp'].$_FILES['logo_in_image']['name'], $_POST['logo_position'], $margin, $opacity);		
			
			if(count($image->errors) > 0)
				{
				die($image->print_error_report());
				}
			
			unlink($core->CONFIG['vars']['temp'].$_FILES['logo_in_image']['name']);
			}

		if($_POST['create_preview'])
			{
			$_POST['save_proportion'] = (intval($_POST['save_proportion']))? 1 : 0;	
			
			$base_line = ($_POST['image_prev_select_point'] == 'w')? 'w':'h';
				
			$image->create_preview($_POST['image_width'], $_POST['image_heidht'], $_POST['save_proportion'], $base_line);
			}	
			
		if(count($image->errors) > 0) die($image->print_error_report());
		
		$image->info['name'] = $_POST['name'];
		$image->info['describe'] = $_POST['describe'];
		$image->info['alias'] = (strlen($_POST['filename']))? $_POST['filename'] : $_FILES['image']['name'];
		$image->info['id_folder'] = intval($_GET['id_folder']);
		
		$image->save();
		$image->close();
			
		return true;
	}
	
	public function files_upload()
	{
		global $core;
		
		$this->site_id = $core->edit_site;
		$this->get_upload_folder();
		
		$file_path = $core->CONFIG['vars']['files'].$this->upload_folder.'/';
		$save_path = '/'.str_replace($core->CONFIG['local_path'], '', $file_path);
		
		//print_r($_FILES['files']);
		
		foreach($_FILES['files']['name'] as $k=>$file)
			{
			if($file)
				{
				$new_filename = $this->get_file_name($file);
					
				if(!move_uploaded_file($_FILES['files']['tmp_name'][$k], $file_path.$new_filename))
					{
					$this->error_report('������ ������� �������� -> ���� ��� ��������, �� ����� ���������.');	
					return false;
					}
					
				$files[] = array('name'=>$file, 'mime_type'=>$_FILES['files']['type'][$k], 'size'=>$_FILES['files']['size'][$k], 'new_path'=>'/'.$this->upload_folder.'/'.$new_filename);
				}
			}
		$this->save($files);
	}

	public function delete($entry_id, $type = 'file')
	{
		switch($type)
		{
			case 'image':
				return $this->delete_image($entry_id);				
			break;
			
			case 'file':
				return $this->delete_file($entry_id);
			break;
			
			case 'folder':
				return $this->delete_folder($entry_id);
			break;
			
			default:
				return false;
			break;
		}
	}
	
	public function get_file_info($file_id)
	{
		global $core;
		
		$core->db->select()->from('mcms_files')->fields('filename', 'id_site', 'folder_id', 'alias', 'filename', 'size', 'mime_type')->where('id='.$file_id.' AND id_site = '.$core->site_id);
		$core->db->execute();
		
		$core->db->get_rows(1);
		
		return $core->db->rows;		
	}

	public function get_image_info($file_id, $lang_id = 0)
	{
		global $core;
		
		if($lang_id == 0)
		{
		$core->db->select()->from('mcms_images')->fields('id_image', 'filename', 'p_width', 'p_height', 'width', 'height', 'describe', 'name', 'alias')->where('id_image='.$file_id.' AND lang_id = '.$core->CONFIG['lang']['id']);
		$core->db->execute();
		$core->db->get_rows(1);
		
		return $core->db->rows;	
		}
		else 
			{
			$core->db->select()->from('mcms_images')->fields('id_image', 'filename', 'p_width', 'p_height', 'width', 'height', 'describe', 'name', 'alias')->where('id_image='.$file_id);
			$core->db->execute();
			$core->db->get_rows();
			
			return $core->db->rows;		
			}
	}
	
	public function get_folder_info($folder_id)
	{
		global $core;
		
		$core->db->select()->from('mcms_files_folders')->fields('id_folder', 'parent_id', 'name')->where('id_folder = '.$folder_id.' AND lang_id = '.$core->CONFIG['lang']['name']);
		$core->db->execute();
		$core->db->get_rows(1);
		
		return $core->db->rows;
	}
	
	
	
	
	private function delete_file($file_id)
	{
		global $core;
		
		$file_id = intval($file_id);
		
		echo 'sdfsdfsd';
		
		$file_info = $this->get_file_info($file_id);
		
		@unlink($core->CONFIG['vars']['files'].$file_info['filename']);
		$core->db->delete('mcms_files', $file_id, 'id_file');
		
		return true;
	}
	
	private function delete_folder($folder_id)
	{		
		return $this->delete_folder_recursion($folder_id);
	}
	
	private function delete_image($file_id)
	{
		global $core;

		$image_info = $this->get_image_info($file_id);
		
		@unlink($image_info['filename']);
		
		if($image_info['p_width'] != 0 && $image_info['p_height'])
			@unlink(str_replace('/img_', '/p_img_', $image_info['filename']));

		$core->db->delete('mcms_images', $file_id, 'id_image');
			
		return true;
	}
		
	private function delete_folder_recursion($folder_id)
	{
		global $core;
		
		$folder_items = $this->select_folder_items($folder_id);
		
		if(count($folder_items['files']) > 0)
		{
			foreach($folder_items['files'] as $file)
			{
				$this->delete_file($file['id_file']);
			}
		}
		
		
		if(count($folder_items['images']) > 0)
		{
			foreach($folder_items['images'] as $image)
			{
				$this->delete_image($image['id_image']);
			}	
		}
		
		
		if(count($folder_items['folders']) > 0)
		{
			foreach($folder_items['folders'] as $folder)
			{
				$this->delete_folder_recursion($folder['id_folder']);
			}
		}
		
		
			$core->db->delete('mcms_files_folders', $folder_id, 'id_folder');
			
		return true;
	}
	
	private function save($files)
	{
		global $core;
		
		$file_id = $core->MaxId('id_file', 'mcms_files')+1;		
		
		
		
		if(!strlen($_POST['filename'])) $_POST['filename'] = $files[0]['name'];
		
		foreach($_POST['name'] as $lang_id=>$name)
		{
			$data[] = array('id_file'=>$file_id, 'id_site'=>$this->site_id, 'name'=>addslashes($name), 'describe'=>addslashes($_POST['describe'][$lang_id]), 'filename'=>$files[0]['new_path'], 'alias'=>addslashes($_POST['filename']), 'mime_type'=>$files[0]['mime_type'], 'visible'=>$_POST['visible'], 'folder_id'=>intval($_GET['id_folder']), 'reg_date'=>time(), 'size'=>$files[0]['size'], 'uid'=>$core->user->id, 'lang_id'=>intval($lang_id));
		}
		
		$core->db->autoupdate()->table('mcms_files')->data($data);
		$core->db->execute();
		
		return $file_id;
	}
	
	private function error_report($msg)
	{
		$this->errors[] = iconv('cp1251', 'utf-8', $msg);
	}

	private function get_file_name($real_filename)
	{
		$extension = substr($real_filename, strrpos($real_filename, '.')+1);
		$uploaded_file_name = (md5(time().microtime())).'.'.$extension;
		
		return $uploaded_file_name;
	}
	
	private function get_upload_folder()
	{
		global $core;
		
		$core->db->select()->from('mcms_sites')->fields('server_alias')->where('id = '.$this->site_id);
		$core->db->execute();
		
		$this->upload_folder = $core->db->get_field();
		
		return true;
	}

	private function select_folder_items($folder_id)
	{
		global $core;
		
		$sql = "SELECT DISTINCT(id_folder) FROM mcms_files_folders WHERE parent_id = ".$folder_id;
		$core->db->query($sql);
		$core->db->get_rows();
		
		$out['folders'] = $core->db->rows;
		
		$sql = "SELECT DISTINCT(id_file) FROM mcms_files WHERE folder_id = ".$folder_id;
		$core->db->query($sql);
		$core->db->get_rows();
		
		$out['files'] = $core->db->rows;
		
		$sql = "SELECT DISTINCT(id_image) FROM mcms_images WHERE folder_id = ".$folder_id;
		$core->db->query($sql);
		$core->db->get_rows();
		
		$out['images'] = $core->db->rows;
		
		return $out;
	}

}

	
	
?>