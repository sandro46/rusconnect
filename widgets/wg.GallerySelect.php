<?php

function WGgallerySelect_GetFolders()
{
	global $core;
	
	$core->tpl->assign('PhotogalleryAlbums', get_PhotogalleryAlbums());
	
	$html = $core->tpl->get('wg.PhotogalleryFolders.html', $core->getAdminModule());	
	
	$res = preg_match_all("/\<div id\=\"foldersDiv\"\>(.*)\<\/div\>/s", $html, $match);
	
	return $match[1][0];
}

function WGgallerySelect_GetImages($albumId, $page = 0)
{
	global $core;
	
	$albumId = intval($albumId);
	
	$limit = 12;
	
	if(!$albumId) return 'error';
	
	$start = intval($page) * intval($limit);
	//$start = 0;		
	
		$sql = "SELECT
					i.id_image,
					i.describe,
					i.filename,
					i.preview_filename,
					i.width,
					i.height,
					i.p_width,
					i.p_height,
					i.size,
					i.reg_date
				FROM 
					mcms_images as i
				WHERE 
					i.id_image IN (SELECT ai.image_id FROM site_photogallery_albums_images as ai WHERE ai.album_id = {$albumId})
				ORDER BY reg_date DESC
				LIMIT {$start},{$limit}";
				
		$core->db->query($sql);
		$core->db->colback_func_param = 0;
		$core->db->add_fields_deform(array('reg_date', 'describe', 'size'));
		$core->db->add_fields_func(array('date,"d.m.Y"',  'stripslashes', 'get_formated_file_size'));	
		$core->db->get_rows();

		$images = $core->db->rows;
		
		$sql = "SELECT COUNT(*) FROM site_photogallery_albums_images WHERE album_id = {$albumId}";
		$core->db->query($sql);
		$total = $core->db->get_field();
		
		$pagenav =  ajax_pagenav($total, $limit, $page, 'updateImages', $albumId);
		
		//echo $sql;
		//die();
		
		$core->tpl->assign('PhotogalleryAjaxPageNav',$pagenav);
		$core->tpl->assign('PhotogalleryImages', $core->db->rows);
		
		return $core->tpl->get('wg.PhotogalleryImages.html', $core->getAdminModule());	
}

function get_PhotogalleryWindow()
{
	global $core;
	
	$core->tpl->assign('PhotogalleryAlbums', get_PhotogalleryAlbums());		
	$html = $core->tpl->get('wg.PhotogalleryFolders.html', $core->getAdminModule());
	
	return $html;	
}

function get_PhotogalleryAlbums()
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
				u.name as user_name, 
				(SELECT i.preview_filename FROM mcms_images as i WHERE i.id_image = al.cover_id) as cover_image, 
				(SELECT r.rewrite FROM mcms_rewrite as r WHERE r.id = al.rewrite_id) as rewrite, 
				(SELECT COUNT(*) FROM `site_photogallery_albums_images` as ai WHERE ai.album_id = al.id) as count_photos	
			FROM 
				site_photogallery_albums as al,
				mcms_user as u
				
			WHERE 
				u.id_user = al.user_id
				
			ORDER BY al.name";

	$core->db->query($sql);
	$core->db->colback_func_param = 0;
	$core->db->add_fields_deform(array('edit_date', 'create_date', 'name'));
	$core->db->add_fields_func(array('date,"d.m.Y"', 'date,"d.m.Y"', 'stripslashes'));	
	$core->db->get_rows();
	
	return $core->db->rows;
}


$core->lib->load('ajax');
$ajax = new ajax('gallerySelect');
$ajax->debug_mode = 0;
$ajax->request_type = 'POST';
$ajax->add_func('WGgallerySelect_GetImages', 'WGgallerySelect_GetFolders', 'get_PhotogalleryWindow');
$ajax->init();
$core->tpl->assign('gallerySelect_ajax_output', $ajax->output);
$ajax->user_request();

class GallerySelect extends widgets implements iwidget 
{
	
	public $html = '';
	public $selectHandler = 'nullhandler';
	
	
	public function main()
	{
		return $this->html;	
	}
	
	public function out()
	{		
		$this->core->tpl->assign('GallerySelectGalleryHandler',$this->selectHandler);
		
		return $this->core->tpl->get('wg.Photogallery.html', $this->core->getAdminModule());	
	}
	
	public function get_folders_html()
	{
		//wg.Photogallery.html	
		
		$this->core->tpl->assign('PhotogalleryAlbums', $this->get_albums());		
		$this->html = $this->core->tpl->get('wg.PhotogalleryFolders.html', $this->core->getAdminModule());
		
		return $this->html;		
	}
	
	private function get_albums()
	{
		$sql = "SELECT 
					al.id, 
					al.name, 
					al.user_id,
					al.cover_id,
					al.edit_date,
					al.create_date,
					al.rewrite_id,
					u.name as user_name, 
					(SELECT i.preview_filename FROM mcms_images as i WHERE i.id_image = al.cover_id) as cover_image, 
					(SELECT r.rewrite FROM mcms_rewrite as r WHERE r.id = al.rewrite_id) as rewrite, 
					(SELECT COUNT(*) FROM `site_photogallery_albums_images` as ai WHERE ai.album_id = al.id) as count_photos	
				FROM 
					site_photogallery_albums as al,
					mcms_user as u
					
				WHERE 
					u.id_user = al.user_id
					
				ORDER BY al.name";

		$this->core->db->query($sql);
		$this->core->db->colback_func_param = 0;
		$this->core->db->add_fields_deform(array('edit_date', 'create_date', 'name'));
		$this->core->db->add_fields_func(array('date,"d.m.Y"', 'date,"d.m.Y"', 'stripslashes'));	
		$this->core->db->get_rows();
		
		return $this->core->db->rows;	
	}
	
}

?>