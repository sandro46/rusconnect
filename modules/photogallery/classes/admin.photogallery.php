<?php

class admin_photogallery
{


	public function __construct()
	{
		
	}


	public function get_albums_list($page=0, $limit=10, $inSite=false)
	{
		global $core;
		
		$start = intval($page) * intval($limit);
		
		$sql = "SELECT 
		
					al.id, 
					al.name, 
					al.user_id,
					al.cover_id,
					al.edit_date,
					al.create_date,
					al.rewrite_id,
					al.show_in_site,
					u.name as user_name, 
					(SELECT i.preview_filename FROM mcms_images as i WHERE i.id_image = al.cover_id) as cover_image, 
					(SELECT r.rewrite FROM mcms_rewrite as r WHERE r.id = al.rewrite_id) as rewrite, 
					(SELECT COUNT(*) FROM `site_photogallery_albums_images` as ai WHERE ai.album_id = al.id) as count_photos
					
				FROM 
					site_photogallery_albums as al,
					mcms_user as u
					
				WHERE 
					u.id_user = al.user_id AND
					al.site_id = {$core->edit_site} ";

		$sql .= ($inSite !== false)? (($inSite == 1)? ' AND al.show_in_site = 1 ': ' AND al.show_in_site = 0') : ' ';
		
		$sql .=	"ORDER BY al.name
				LIMIT {$start}, {$limit}";

		$core->db->query($sql);
		$core->db->colback_func_param = 0;
		$core->db->add_fields_deform(array('edit_date', 'create_date', 'name'));
		$core->db->add_fields_func(array('dateAgo', 'dateAgo', 'stripslashes'));	
		$core->db->get_rows();
		
		return $core->db->rows;		
	}

	public function get_total_albums()
	{
		global $core;
		
		$sql = "SELECT COUNT(*) FROM site_photogallery_albums WHERE site_id = {$core->edit_site}";
		$core->db->query($sql);
		
		return $core->db->get_field();
	}

	public function get_album_info($id)
	{
		global $core;
		
		$sql = "SELECT 
		
					al.id, 
					al.name, 
					al.user_id,
					al.cover_id,
					al.edit_date,
					al.create_date,
					al.rewrite_id,
					al.show_in_site,
					u.name as user_name, 
					(SELECT i.preview_filename FROM mcms_images as i WHERE i.id_image = al.cover_id) as cover_image, 
					(SELECT r.rewrite FROM mcms_rewrite as r WHERE r.id = al.rewrite_id) as rewrite, 
					(SELECT COUNT(*) FROM `site_photogallery_albums_images` as ai WHERE ai.album_id = al.id) as count_photos
					
				FROM 
					site_photogallery_albums as al,
					mcms_user as u
					
				WHERE 
					u.id_user = al.user_id AND 
					al.id = {$id}";
		
		$core->db->query($sql);
		$core->db->colback_func_param = 0;
		$core->db->add_fields_deform(array('edit_date', 'create_date', 'name'));
		$core->db->add_fields_func(array('date,"d.m.Y"', 'date,"d.m.Y"', 'stripslashes'));	
		$core->db->get_rows(1);
		
		return $core->db->rows;					
	}
	
	public function get_album_images($albumId, $page=0, $limit=10)
	{
		global $core;
		
		$start = intval($page) * intval($limit);
	
		
		$sql = "SELECT
					i.id_image,
					i.describe2,
					i.filename,
					i.preview_filename,
					i.width,
					i.height,
					i.p_width,
					i.p_height,
					i.size,
					i.reg_date,
					i.folder_id
				FROM 
					mcms_images as i
				WHERE 
					i.id_image IN (SELECT ai.image_id FROM site_photogallery_albums_images as ai WHERE ai.album_id = {$albumId})
				ORDER BY reg_date DESC
				LIMIT {$start},{$limit}";
				
		$core->db->query($sql);
		$core->db->colback_func_param = 0;
		$core->db->add_fields_deform(array('reg_date', 'describe2', 'size'));
		$core->db->add_fields_func(array('date,"d.m.Y"',  'stripslashes', 'get_formated_file_size'));	
		$core->db->get_rows();

		
		return $core->db->rows;		
	}
	
	public function get_total_images($albumId)
	{
		global $core;
		
		$sql = "SELECT COUNT(*) FROM site_photogallery_albums_images WHERE album_id = {$albumId}";
		$core->db->query($sql);
		
		return $core->db->get_field();
	}
	
	public function saveAlbum()
	{
		global $core;
				
		$isnew = false;
		
		$albumId = intval($_POST['AlbumId']);
		$name = addslashes($_POST['AlbumName']);
		$cover = intval($_POST['AIcoverId']);
		$rewrite_id = intval($_POST['rewrite_id']);
		$showInSite = (isset($_POST['show_in_site']) && intval($_POST['show_in_site']) == 1)? 1 : 0;
		
		if(!$albumId)
		{
			$isnew = true;
			$albumId = MaxId('id_image', 'mcms_images')+1;
			$folder_id = $this->addFolder($name);
		}
		
		if(!$rewrite_id && isset($_POST['AlbumRewrite']) && strlen($_POST['AlbumRewrite'])>=2)
		{
			$rewrite_id = $core->url_parser->add(addslashes($_POST['AlbumRewrite']), "/photogallery/show/album/{$albumId}/limit/9999999/");
		}
		else
			{
				if($rewrite_id && !$_POST['AlbumRewrite'])
				{
					$core->url_parser->del($rewrite_id);
					$rewrite_id = 0;
				}
				elseif(isset($_POST['AlbumRewrite']) && strlen($_POST['AlbumRewrite'])>=2 && $rewrite_id != 0)
					{
						$core->url_parser->edit($rewrite_id, addslashes($_POST['AlbumRewrite']), "/photogallery/show/album/{$albumId}/limit/999999/");
					}
			}
		
		
		
		if($isnew)
		{
			$data[] = array('name'=>$name, 'create_date'=>time(), 'edit_date'=>time(), 'user_id'=>$core->user->id, 'cover_id'=>$cover, 'rewrite_id'=>$rewrite_id, 'folder_id'=>$folder_id, 'site_id'=>$core->edit_site, 'show_in_site'=>$showInSite);
			$core->db->autoupdate()->table('site_photogallery_albums')->data($data);
			$core->db->execute();
		}
		else 
			{
				$data[] = array('id'=>$albumId, 'name'=>$name, 'edit_date'=>time(), 'cover_id'=>$cover, 'rewrite_id'=>$rewrite_id, 'site_id'=>$core->edit_site, 'show_in_site'=>$showInSite);
				$core->db->autoupdate()->table('site_photogallery_albums')->data($data)->primary('id');
				$core->db->execute();
			}
		
		return $albumId;
	}
	
	public function saveImage()
	{
		
	}

	
	
	private function addFolder($name)
	{
		global $core;
		
		$folderId = MaxId('folder_id', 'mcms_files_folders_entry')+1;
		
		$data[] = array('folder_id'=>$folderId, 'parent_id'=>2, 'name'=>$name, 'lang_id'=>$core->CONFIG['lang']['id'], 'site_id'=>$core->edit_site);
		
		$core->db->autoupdate()->table('mcms_files_folders_entry')->data($data);
		$core->db->execute();
		
		return $folderId;
	}
}


?>