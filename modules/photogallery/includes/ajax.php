<?php

// This file was created by M-cms core system for ajax functions



	function upload_album_cover($realFileName, $tmpFileName)
	{
		global $core;
				
		$core->lib->load('images');
				
		$realFile = $core->CONFIG['temp_path'].'album_image-'.md5(time().microtime()).substr($realFileName, -4);


		if(!@rename($core->CONFIG['temp_path'].$tmpFileName, $realFile))
		{
			unlink($core->CONFIG['temp_path'].$tmpFileName);
			return '-1';
		}

		if(!images::checkMime($realFile))
		{
			unlink($realFile);
			return '-2';
		}
				
		$core->images = new images($core->CONFIG['albums']['images']['upload_path'], $core->CONFIG['albums']['images']['upload_path'], $core->CONFIG['albums']['images']['local_path'], $core->CONFIG['albums']['images']['local_path'], 2);
		$core->images->fname = $realFile;
		
		if(!$core->images->open())
		{
			unlink($realFile);
			return '-3';			
		}

		$previewName = $core->CONFIG['albums']['images']['upload_path'].$core->images->info['new_name'];
		
		$core->images->resize(800, 600, $realFile, true, 'w', 80);
		$core->images->close(false);
		$core->images->open();
		$core->images->updateSize();
		$core->images->saveFromFile(3, $realFileName);
		$core->images->create_preview(200, 150, true, 'w', 80);
		$info = array('file'=>$core->images->info['preview_path'], 'id'=>$core->images->id, 'w'=>$core->images->info['p_width'], 'h'=>$core->images->info['p_height']);
				
		if(!$info) 
		{
			unlink($realFile);
			return '-4';
		}
				
		return $info;		
	}

	function upload_album_image($realFileName, $tmpFileName, $AlbumId)
	{
		global $core;
		
		
		
		$core->lib->load('images');
				
		$realFile = $core->CONFIG['temp_path'].'album_image-'.md5(time().microtime()).substr($realFileName, -4);

		if(!@rename($core->CONFIG['temp_path'].$tmpFileName, $realFile))
		{
			unlink($core->CONFIG['temp_path'].$tmpFileName);
			return 'error';
		}
		
		if(!intval($AlbumId))
		{
			unlink($core->CONFIG['temp_path'].$tmpFileName);
			return 'error';
		}
		
		if(!images::checkMime($realFile))
		{
			unlink($realFile);
			return 'error';
		}
		
		$core->images = new images($core->CONFIG['albums']['images']['upload_path'], $core->CONFIG['albums']['images']['upload_path'], $core->CONFIG['albums']['images']['local_path'], $core->CONFIG['albums']['images']['local_path'], 2);
		$core->images->fname = $realFile;
				
		if(!$core->images->open())
		{
			unlink($realFile);
			return 'error';			
		}
		
		
		$core->db->select()->from('site_photogallery_albums')->fields('folder_id')->where('id = '.intval($AlbumId));
		$core->db->execute();
		
		$folder_id = $core->db->get_field();
		
		$previewName = $core->CONFIG['albums']['images']['upload_path'].$core->images->info['new_name'];
		
		//$core->images->resize(800, 600, $realFile, true, 'h', 80);
		//$core->images->close(false);
			
		//$core->images->open();
		//$core->images->updateSize();
		$core->images->saveFromFile($folder_id, $realFileName);
		$core->images->create_preview(200, 150, true, 'h', 80);
		
		$data[] = array('image_id'=>$core->images->id, 'album_id'=>intval($AlbumId));
		
		$core->db->autoupdate()->table('site_photogallery_albums_images')->data($data);
		$core->db->execute();
		
		$info = array('file'=>$core->images->info['preview_path'], 'id'=>$core->images->id, 'w'=>$core->images->info['p_width'], 'h'=>$core->images->info['p_height']);

		$core->images->close(false);
		
		unlink($realFile);
		
		if(!$info) return 'error';
		
		return $info;		
	}

	function removeImage($imageId, $albumId)
	{
		global $core;
		
		$imageId = intval($imageId);
		$albumId = intval($albumId);
		
		if(!$imageId || !$albumId) return;
		
		$core->db->select()->from('mcms_images')->fields('filename', 'preview_filename')->where('id_image = '.$imageId);
		$core->db->execute();
		$core->db->get_rows(1);
		
		$image = $core->db->rows;
		//print_r($image);
		@unlink($core->CONFIG['local_path'].$image['filename']);
		@unlink($core->CONFIG['local_path'].$image['preview_filename']);
		
		$core->db->delete('mcms_images', $imageId, 'id_image');
		$core->db->delete('site_photogallery_albums_images', $imageId, 'image_id');
		
		return;		
	}

	function updateImageComment($text, $imageId)
	{
		global $core;
		
		$imageId = intval($imageId);
		$text = addslashes($text);
		
		$sql = "UPDATE mcms_images SET describe2 = '{$text}' WHERE id_image = {$imageId}";
		$core->db->query($sql);
	}


?>