<?php
class PhotoGallery extends widgets implements iwidget 
{
	
	public $albumId = 0;
	public $photoId = 0;
	public $list = array();
	
	private $html = '';
	
	
	public function main()
	{

	}
	
	public function out()
	{
		if($this->albumId) return $this->getAlbumPhotos();
		if($this->photoId) return $this->getPhotoInfo();
		
		return $this->getAlbums();
	}
	
	private function getAlbumPhotos()
	{
		global $core;
			
		$start = 0;
		$limit = 99999;
		
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
					i.id_image IN (SELECT ai.image_id FROM site_photogallery_albums_images as ai WHERE ai.album_id = (SELECT a.id FROM site_photogallery_albums as a WHERE a.id = {$this->albumId} AND a.site_id = {$core->site_id}) )
				ORDER BY reg_date DESC
				LIMIT {$start},{$limit}";
				
		$core->db->query($sql);
		$core->db->colback_func_param = 0;
		$core->db->add_fields_deform(array('reg_date', 'describe', 'size'));
		$core->db->add_fields_func(array('date,"d.m.Y"',  'stripslashes', 'get_formated_file_size'));	
		$core->db->get_rows();

		return $core->db->rows;	
	}
	
	private function getPhotoInfo()
	{
		global $core;
		
		$sql = "SELECT * FROM mcms_images WHERE id_image = {$this->photoId} AND id_site = {$core->site_id}";
		$core->db->query($sql);
		$core->db->colback_func_param = 0;
		$core->db->add_fields_deform(array('reg_date', 'describe2', 'size'));
		$core->db->add_fields_func(array('date,"d.m.Y"',  'stripslashes', 'get_formated_file_size'));	
		$core->db->get_rows(1);
		
		return $core->db->rows;	
	}
	
	private function getAlbums()
	{
		global $core;
		
		$start = 0;
		$limit = 99999;
		
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
					al.site_id = {$core->site_id} AND 
					al.show_in_site = 1
					
				ORDER BY al.name
				LIMIT {$start}, {$limit}";

		$core->db->query($sql);
		$core->db->colback_func_param = 0;
		$core->db->add_fields_deform(array('edit_date', 'create_date', 'name'));
		$core->db->add_fields_func(array('dateAgo', 'dateAgo', 'stripslashes'));	
		$core->db->get_rows();
		
		return $core->db->rows;	
	}
	
}