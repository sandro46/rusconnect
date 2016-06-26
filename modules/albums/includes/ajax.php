<?php

// This file was created by M-cms core system for ajax functions

	function get_folder_tracks($item)
	{
		$tracks = array(); 
				
		if(substr($item, -1) != '/' && substr($item, -1) != '\\') $item .= '/';
		
		foreach(scandir($item) as $file)
		{
			if($file != '.' && $file != '..' && is_file($item.$file) && strtolower(substr($file, -4)) == '.mp3')
			{
				$tracks[] = array('parsed'=>'', 'real'=>$file, 'path'=>$item.$file);
			}
		}
		
		return $tracks;
	}
		
	function get_folder_album_cover($item)
	{
		$image = false;
		if(substr($item, -1) != '/' && substr($item, -1) != '\\') $item .= '/';
		
		foreach(scandir($item) as $file)
		{
			if($file != '.' && $file != '..' && is_file($item.$file) && (strtolower(substr($file, -4)) == '.jpg' || strtolower(substr($file, -4)) == '.jpeg' || strtolower(substr($file, -4)) == '.gif' || strtolower(substr($file, -4)) == '.png'))
			{
				$image = $file;
			}
		}
		
		return $image;
	}
	
	function parse_album_name_from_files($item)
	{
		
		$info = explode("-", $item);
		
		$albumInfo = array();
		$albumInfo['artist'] = trim($info[0]);
		$albumInfo['album'] = trim($info[1]);
		$albumInfo['year'] = trim($info[3]);
		$albumInfo['title'] = $albumInfo['artist'].' - '.$albumInfo['album'].' - '.$albumInfo['year'];
		$albumInfo['rewrite'] = translateItem($albumInfo['artist'].' - '.$albumInfo['album']);
		
		$info[2] = trim($info[2]);
		if(substr($info[2], 0, 1) == '[' && substr($info[2], -1) == ']')
		{
			$styles = explode(",",trim(substr($info[2], 1, -1)));
			foreach($styles as $style) $albumInfo['genres'][] = $style;
		}
		
		return $albumInfo;
	}


	function trackNameParser($file)
	{
		// удаляем .mp3
		$file = substr($file, 0, -4);
		// удаляем нижние подчеркивание
		$file = str_replace(array('_', "'", "`", "&"), array(' ', "", "", "and"), $file);
		$file = trim($file);
		
		// Удаляем номер трека в середине
		$file = preg_replace("/^(.+?)-[ ]{0,}[0-9]{1,}[ ]{0,}-[ ]{0,}(.+?)$/s", '$1- $2', $file);
		
		// удаляем первые две цифры (номер трека) или буквы c цифрами (пластинка-дорожка)
		$file = preg_replace("/^[0-9]{1,}(.+?)/", '$1', $file);
		$file = preg_replace("/^[a-zA-Z]{1,2}[0-9]{1,}(.+?)/", '$1', $file);
		$file = trim($file);
		// удаляем тире после номера трека
		if(substr($file, 0, 1) == '-') $file = substr($file, 1);
		// удаляем точку после номера трека
		if(substr($file, 0, 1) == '.') $file = substr($file, 1);
		$file = trim($file);
		// первая буква заглавная
		$file = ucfirst($file);
		
		return $file;
	}

	function uploadComleteFileToAlbum($realFileName, $tmpFileName, $albumId)
	{			
		$albumId = intval($albumId);
		if(!$albumId) return false;
		
		$realFileName = str_replace(array("'", "`", "&", " ", "^", '"'), array("", "", "and", "_", ".", "."), $realFileName);
		
		if(substr(strtolower($realFileName), -4) == '.mp3')
		{
			$file = trackNameParser($realFileName);
		}
				
		$realFileName = translateItem($realFileName);
		
		$root = new album($albumId);
		$root->get();
		global $core;
				
		$path = substr($core->CONFIG['albums']['albumsPath'].$root->rewrite, 0, -5).'/';
		$smallPath = substr($root->rewrite, 0, -5).'/';
				
		if(!file_exists($path))
		{
			mkdir($path, 0775, true);
		}
				
		if(!file_exists($path))
		{
			return 'error -1';
		}
		
		
		rename($core->CONFIG['temp_path'].$tmpFileName, $path.$realFileName);
		$data = array();
		$data[] = array('album_id'=>$albumId, 'name'=>$file, 'filename'=>$smallPath.$realFileName);
		
		$core->db->autoupdate()->table('albums_tracks')->data($data);
		$core->db->execute('db_music');
		
		$TrackId = $core->db->insert_id;
		
		$core->db->select()->from('albums_tracks')->fields('cnt')->where('id = '.$TrackId);
		$core->db->execute('db_music');
		
		$cnt = $core->db->get_field();
		
		return array('id'=>$TrackId, 'file'=>$smallPath.$realFileName, 'name'=>$file, 'cnt'=>$cnt);
	}

	function get_artist_input_context($str)
	{
		global $core;
		
		$core->db->select()->from('m-zone.artists')
						   ->fields()
						   ->where('LOWER(title) LIKE "'.strtolower(addslashes(trim($str))).'%"')
						   ->limit(8);
		$core->db->execute();
		$core->db->get_rows();
		
		return $core->db->rows;
	}

	function get_album_input_context($str)
	{
		global $core;
		
		$core->db->select()->from('m-zone.albums')
						   ->func('DISTINCT(`title`) as title')
						   ->where('LOWER(title) LIKE "'.strtolower(addslashes(trim($str))).'%"')
						   ->limit(8);
		$core->db->execute();
		$core->db->get_rows();
		
		return $core->db->rows;
	}

	function upload_album_cover($realFileName, $tmpFileName)
	{
		global $core;
		
		$core->lib->load('images');
				
		$realFile = $core->CONFIG['temp_path'].'album_image-'.md5(time().microtime()).substr($realFileName, -4);

		if(!@rename($core->CONFIG['temp_path'].$tmpFileName, $realFile))
		{
			unlink($core->CONFIG['temp_path'].$tmpFileName);
			return false;
		}
		
		if(!images::checkMime($realFile))
		{
			unlink($realFile);
			return false;
		}
		
		$core->images = new images($core->CONFIG['albums']['images']['upload_path'], $core->CONFIG['albums']['images']['upload_path'], $core->CONFIG['albums']['images']['local_path'], $core->CONFIG['albums']['images']['local_path'], 2);
		$core->images->fname = $realFile;
				
		if(!$core->images->open())
		{
			unlink($realFile);
			return false;			
		}
		
		$thumbName = $core->images->upload_folder.$core->CONFIG['albums']['images']['thumb']['path'].$core->images->info['new_name'];
		$previewName = $core->images->upload_folder.$core->CONFIG['albums']['images']['preview']['path'].$core->images->info['new_name'];
		
		$core->images->resize($core->CONFIG['albums']['images']['thumb']['width'], $core->CONFIG['albums']['images']['thumb']['height'], $thumbName, true, 'h');
		$core->images->resize($core->CONFIG['albums']['images']['preview']['width'], $core->CONFIG['albums']['images']['preview']['height'], $previewName, true, 'h');
		$core->images->resize($core->CONFIG['albums']['images']['source']['width'], $core->CONFIG['albums']['images']['source']['height'], $realFile, true, 'h');
		
		$core->images->close(false);
		$core->images->open();
		$core->images->updateSize();
		$core->images->saveFromFile(1, $realFileName);
		
		$info = array('file'=>$core->CONFIG['albums']['images']['url'].$core->images->info['local_filename'], 'id'=>$core->images->id);
				
		if(!$info) 
		{
			unlink($realFile);
			return false;
		}
		
		return $info;		
	}

	function get_genres_list($genreId)
	{		
		$root = new admin_albums();
		
		$genres = $root->getGenres(intval($genreId));
		if(!$genres || !is_array($genres)) return 'error';
		if(!count($genres)) return 'no items';
		
		return $genres;
	}

	function get_genres_list_from_element($genreId, $elemetId)
	{		
		$elemetId = intval($elemetId);
		
		$result = get_genres_list($genreId);
		
		
		return array($result, $elemetId);
	}
	
	function translateItem($title)
	{		
		if(!$title) return 'error';

		$title = decode_unicode_url($title);
		
		$root = new admin_albums();
		
		$title = $root->rewriteFormed($title);
		
		return $title;
	}

	function getCoversArchiveWindow($page = 0, $filter = '')
	{
		global $core;
		
		$root = new admin_albums();
		
		$filter = htmlspecialchars($filter);
		$limit = 20;
		$page = intval($page);
		
		$root->getCoversFromArchive($filter, $page, $limit);
		
		$pagenav = ajax_pagenav($root->totalEnrys, $limit, $page, 'updateCoversList');
		
		$core->tpl->assign('images', $root->covers);
		$core->tpl->assign('ajaxPagenav', $pagenav);
		
		
		return $core->tpl->get('coversArchive.html', 'albums');
	}
	
	function getCoverData($page = 0, $filter = '')
	{
		global $core;
		
		$root = new admin_albums();
		
		$filter = htmlspecialchars($filter);
		$limit = 20;
		$page = intval($page);
		
		$root->getCoversFromArchive($filter, $page, $limit);
		
		$pagenav = ajax_pagenav($root->totalEnrys, $limit, $page, 'updateCoversList');
		
		$core->tpl->assign('images', $root->covers);
		$core->tpl->assign('ajaxPagenav', $pagenav);
		
		return $core->tpl->get('coversListOnlyData.html', 'albums');
	}
	
	function doneUploadFile($realFileName, $tmpFileName)
	{
		
		
	}
	
	function addAlbumCommnet($albumId, $text)
	{
		$albumId = intval($albumId) +0;
		if(!$albumId) return 0;
		if(!checkLastComment()) return -1;
		
		$text = mysql_escape_string(htmlspecialchars(addslashes($text)));
		
		global $core;
		$data[] = array('album_id'=>$albumId, 'user_id'=>$core->user->id, 'date'=>time(), 'comment'=>$text);
		$core->db->autoupdate()->table('albums_comments')->data($data);
		$core->db->execute('db_music');
		
		if(!$core->db->insert_id) return 0;
		$comment_id = $core->db->insert_id;
		
		$core->db->select()->from('albums_comments')->fields('$all')->where('id = '.$comment_id);
		$core->db->execute('db_music');
		$core->db->get_rows(1);
		
		if(!count($core->db->rows)) return 0;
		
		$comment['userpic'] = 'img_2a28eb7cda6909088d605c64fad55c7d.jpg';
		$comment['username'] = $core->user->info['login'];
		$comment['time'] = date('H:i', $core->db->rows['date']);
		$comment['date'] = date('d.m:Y', $core->db->rows['date']);
		$comment['text'] = stripslashes($core->db->rows['comment']);
		$comment['userid'] = $core->user->id;
		
		$_SESSION['last_comment'] = time();
		
		return $comment;
	}
	
	function checkLastComment()
	{
		if(!isset($_SESSION['last_comment'])) return true;
		if($_SESSION['last_comment']+20 >= time()) return false;
		
		return true;
	}
	
	function createTorrentForAlbum($id)
	{
		$id = intval($id);
		
		if(!isset($_SESSION['AlbumCreator']) || !isset($_SESSION['AlbumCreator'][$id])) return 'Incorrect album id';
		
		global $core;
		
		$albumInfo = $_SESSION['AlbumCreator'][$id];
		
		$dir = $core->CONFIG['albums']['albumsPath'].$albumInfo['localFolder'].'/';
		$torrentFile = $core->CONFIG['albums']['torrent']['pathForSave'].$albumInfo['rewrite'].'.torrent';
		$anonce = $core->CONFIG['albums']['torrent']['anoncePath'];
		
		$torrent = new Torrent($dir, $anonce);
		$torrent->is_private(true);
		$torrent->comment('It\'s M-CMS Automaticaly torrent creator system by M-zone.');
		
		$runCommand = "\"C:\Program Files\uTorrent\uTorrent.exe\" /DIRECTORY \"{$dir}\" \"{$torrentFile}\"";
		$WshShell = new COM("WScript.Shell");
		$WshShell->Exec($runCommand)->StdOut->ReadAll;
		
		//echo proc_open("uTorrent.exe /DIRECTORY \"{$dir}\" \"{$torrentFile}\"");
	}

	function check_login_for_used($login)
	{
		$login = addslashes(mysql_real_escape_string($login));
		
		global $core;
		
		$sql = "SELECT COUNT(*) as cnt FROM site_users WHERE login = '{$login}'";
		$core->db->query($sql, 'db_music');
		$result = $core->db->get_field();
		
		return $result;
	}
	
	function checkCaptcha($string)
	{
		if(md5(strtolower(trim($string))) == $_SESSION['secureHash']) return true;
		return false;
	}
	
	function createUser($name, $login, $pass, $email, $captcha)
	{
		global $core;
		
		$name = addslashes(mysql_real_escape_string($name));
		$login = addslashes(mysql_real_escape_string($login));
		$pass = addslashes(mysql_real_escape_string($pass));
		$email = addslashes(mysql_real_escape_string($email));
		$invite = md5(microtime().md5($name).chr(mt_rand(127,256)).md5($login).chr(mt_rand(127,256)).md5($pass).chr(mt_rand(127,256)).md5($email).chr(mt_rand(127,256)).time());
		
		if(!checkCaptcha($captcha)) return 'captcha error';
		
		$data[] = array('id_user'=>$login, 'name'=>$name, 'login'=>$login, 'password'=>md5(md5($pass).$core->CONFIG['secure']['user']['pass_salt']), 'default_site_id'=>4, 'email'=>$email, 'memo'=>'from ultra-music site', 'disable'=>0, 'invite_code'=>md5($invite), 'checked'=>0);
		$core->db->autoupdate()->table('site_users')->data($data);
		$core->db->execute('db_music');
				
		if(!$core->db->insert_id) return 'insert error';
		
		$message = "Вы зарегистрировались на сайте ultra-music.org<br><br>Ваш логин для входа: <b>{$login}</b><br>Ваш пароль для входа: <b>{$pass}</b><br><br><br>Вам необходимо активировать Ваш аккаунт.<br>Для этого перейдите по ссылке: <a href='http://ultra-music.org/ru/-utils/usercheck/protection/{$invite}/'>http://ultra-music.org/ru/-utils/usercheck/protection/{$invite}/</a><br><br><br>С Уважением,<br>Администрация сайта <b>ultra-music.org</b>";
		
		$core->lib->load('smtp');
		$mailer = new smtp_sender('smtp.itbc.pro', 'alexey@itbc.pro', 'QzWx123-');
		$mailer->defaultType = 'text/html';
		$mailer->send('noreply@itbc.pro', $email, 'Регистрация на сайте www.ultra-music.org', $message);
		
		return 'ok';
	}
	
?>