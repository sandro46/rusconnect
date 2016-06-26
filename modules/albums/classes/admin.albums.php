<?php

class admin_albums
{

	public $errors = false;
	public $groups = array();
	public $covers = array();
	public $totalEnrys = '';


	public function __construct()
	{

	}

	public function get_years_list()
	{
		$array = array();
		$commonDate = date("Y", time());

		for($y = 1950; $y <= $commonDate; $y ++)
		{
			$array[] = $y;
		}

		return $array;
	}

	public function getGroupsList()
	{
		global $core;

		$core->db->select()->from('groups')->fields('$all');
		$core->db->execute('db_music');

		$this->groups = $core->db->get_rows();

		return $this->groups;
	}

	public function getGenres($parentId = 0, $id = 0, $rewrite = 0)
	{
		global $core;

		$query = $core->db->select()->from('genres')->fields();
		$query->where('parent_id = '.intval($parentId))->order('name');
		
		if($id) $query->where('id = '.intval($id));
		if($rewrite) $query->where('rewrite_id = "/genres/'.addslashes($rewrite).'/"');
		
		$core->db->execute('db_music');
		$core->db->get_rows();

		return $core->db->rows;
	}

	public function getCoversFromArchive($request = false, $page = 0, $limit = 100)
	{
		global $core;

		$start = $page * $limit;

		if($request)
		{
			$request = htmlspecialchars($request);
			$sql = "SELECT artist as artist_small, album as album_small, REPLACE(artist, '{$request}', '<span style=\"color:red; background-color:gainsboro\">{$request}</span>') as `artist`, REPLACE(album, '{$request}', '<span style=\"color:red; background-color:gainsboro\">{$request}</span>') as album, image FROM covers_archive WHERE artist LIKE '%{$request}%' OR album LIKE '%{$request}%' LIMIT {$start},{$limit}";
			$sqlTotal = "SELECT COUNT(*) FROM covers_archive WHERE artist LIKE '%{$request}%' OR album LIKE '%{$request}%'";
		}
		else
			{
				$sql = "SELECT artist, album, artist as artist_small, album as album_small, image FROM covers_archive LIMIT {$start},{$limit}";
				$sqlTotal = "SELECT COUNT(*) FROM covers_archive";
			}
		
		$core->db->query($sql, 'db_music');
		$core->db->get_rows();
		
		$this->covers = $core->db->rows;
				
		$core->db->query($sqlTotal, 'db_music');
		
		$this->totalEnrys = $core->db->get_field();
		
		//return $sql;		
	}

	public static function rewriteFormed($string)
	{
		global $core;
		$core->lib->dll('translate');
		$string = rewriteEscapeSymbolDelete($string);
		$string = translateRUtoEN($string);

		return $string;
	}

	public static function getAlbumsTypes()
	{
		global $core;
		
		///$core->db->select()->from('site_albums_types')->fields('$all');
		///$core->db->execute();
		//$core->db->get_rows();
		
		///return $core->db->rows;
	}
	
	public static function updateArtists()
	{
		$core->db->select()->from('artists_bk')->fields('*');
		$core->db->execute('db_music');
		$core->db->get_rows();
		$result = $core->db->rows;
		
		foreach ($result as $item)
		{
			$item['title'] = trim($item['title']);
			$data[] = array('title'=>$item['title'], 'checked'=>1, 'rewrite_id'=>$core->url_parser->add(admin_albums::rewriteFormed($item['title']).'.html', ''));
		}
		
		$core->db->autoupdate()->table('artists')->data($data);
		$core->db->execute('db_music');
		
		unset($data);
		
		$core->db->select()->from('artists')->fields('*');
		$core->db->execute('db_music');
		$core->db->get_rows();
		$result = $core->db->rows;
		
		foreach ($result as $item)
		{
			$data[] = array('id'=>$item['rewrite_id'], 'real_url'=>'/music/show/artist/'.$item['id'].'/', 'group'=>'Music');
			$cnt++;
		}
		
		
		$sql = "SELECT COUNT(*) FROM mcms_rewrite";
		$core->db->query($sql, 'db_site');
		
		$core->db->autoupdate()->table('mcms_rewrite')->data($data)->primary('id');
		$core->db->execute();
		
		echo $core->log->dumpQueries();
	}
	
	public function getAll($page=0, $limit=10, $orderby = 'date', $ordertype='desc', $genre=0)
	{
		global $core;
		
		$page = intval($page)+0;
		$limit = intval($limit)+0;
		$genre = intval($genre)+0;
		
		$orders = array('date', 'popular', 'random');
		$ordersfields = array(  'date'=>'a.date',
								/*'popular'=>'a.rating',*/
								'popular'=>'counter',
								'random'=>'RAND()');
		
		if(!in_array($orderby, $orders)) $orderby = 'date';
		if($ordertype != 'desc' && $ordertype != 'asc') $ordertype = 'desc'; 
		
		$start = $page * $limit;
		
		
		// TODO: OPTIMISE ME pLS!!!!
		$sql = "SELECT 
					SQL_CALC_FOUND_ROWS
					a.id,
					r.rewrite, 
					a.title,
					(SELECT 
						ROUND(  
								(SELECT COUNT(*) FROM music_db.site_torrent_peers as stp WHERE stp.info_hash = a.torrent_hash AND stp.left = 0)
								* 100  
								/ (SELECT COUNT(tp.id) as cnt FROM music_db.site_torrent_peers as tp WHERE tp.left = 0 GROUP BY tp.info_hash ORDER BY `cnt`  DESC LIMIT 1)
								/ 20
						)
				    ) as rating,
					

					(SELECT COUNT(*) FROM music_db.site_torrent_peers as p WHERE p.info_hash = a.torrent_hash AND p.left = 0) as counter,
					
					
					(SELECT g.name FROM music_db.genres as g WHERE g.id = (SELECT agt.id_genre FROM music_db.albums_genres as agt WHERE agt.id_album = a.id LIMIT 1)) as base_genre, 
					(SELECT g.rewrite_id FROM music_db.genres as g WHERE g.id = (SELECT agt.id_genre FROM music_db.albums_genres as agt WHERE agt.id_album = a.id LIMIT 1)) as base_genre_rewrite, 
					a.size,
					CONCAT('{$core->CONFIG['albums']['images']['url']}', i.filename) as cover, 
					(SELECT COUNT(*) FROM music_db.albums_comments WHERE album_id = a.id) as comments_count,
					a.date, 
					u.login as creatorName 
				FROM 
					music_db.albums as a,";
		$sql .=  ($genre)? ' music_db.albums_genres as ag, ' : ' ';
		$sql .=		"`m-cms.org`.mcms_rewrite as r, 
					`m-cms.org`.mcms_images as i,
					`m-cms.org`.mcms_user as u
				WHERE
					r.id = a.rewrite_id AND
					i.id_image = a.cover_id AND
					u.id_user = a.user_id AND
					a.torrent != '0' ";
					
		$sql .= ($genre)? " AND ag.id_album = a.id AND ag.id_genre = {$genre}" : '';
		$sql .= " ORDER BY {$ordersfields[$orderby]} {$ordertype} LIMIT {$start},{$limit}";
		
		//echo $sql;
		
		$core->db->query($sql);
		$core->db->colback_func_param = 0;
		$core->db->add_fields_deform(array('date', 'title', 'size'));
		$core->db->add_fields_func(array('dateAgo', 'stripslashes', 'get_formated_file_size'));
		$core->db->get_rows(false, 'id');

		$albums = $core->db->rows;
		
		$core->db->query('SELECT FOUND_ROWS() as count');
        $rowsCount = $core->db->get_field();
		
		$albumsIds = (!count($albums))? '0' : implode(",", array_keys($albums));
		
		$sql = "SELECT t.album_id, t.name, t.cnt FROM  albums_tracks as t WHERE t.album_id IN ({$albumsIds})  ORDER BY t.album_id, t.cnt";
		$core->db->query($sql, 'db_music');
		$core->db->colback_func_param = 0;
		$core->db->add_fields_deform(array('cnt', 'title'));
		$core->db->add_fields_func(array('StrPadNull', 'slicetext,"55"'));
		$core->db->get_rows(false, 'album_id');
		
		foreach ($core->db->rows as $albumId=>$file)
		{
			if(count($file)>6)
			{
				$albums[$albumId]['tracks'][0] = $file[0];
				$albums[$albumId]['tracks'][1] = $file[1];
				$albums[$albumId]['tracks'][2] = $file[2];
				$albums[$albumId]['tracks'][3] = $file[3];
				$albums[$albumId]['tracks'][4] = $file[4];
				$albums[$albumId]['tracks'][5] = $file[5];
				$albums[$albumId]['tracks'][6] = array('cnt'=>'', 'name'=>'...');
			}
			else 
				$albums[$albumId]['tracks'] = $file;
		}
		
		return array($albums, $rowsCount);		
	}
	
	public function albumExists($albumId)
	{
		global $core;
		
		$sql = "SELECT COUNT(*) FROM albums WHERE id = {$albumId}";
		$core->db->query($sql, 'db_music');
		
		return $core->db->get_field();
	}
	
	public static function getAlbumsAccess()
	{
		global $core;
		
		$core->db->select()->from('site_albums_group_types')->fields('$all')->order('id_user_group');
		$core->db->execute();
		$core->db->get_rows();
		
		$acces = array();
		
		if(!$core->db->rows) return false;
		
		foreach($core->db->rows as $item)
		{
			$acces[$item['id_user_group']][] = $item;
		}
		
		return $acces;		
	}
	
	public function trackExists($id)
	{
		global $core;
		
		$sql = "SELECT COUNT(*) FROM albums_tracks WHERE id = {$id}";
		$core->db->query($sql, 'db_music');
		
		return $core->db->get_field();
	}



}

class track
{
	public $id = 0;
	public $name = '';
	public $album_id = '';
	public $filename = '';
	public $path = '';
	public $fullpath = '';
	public $album = false;
	
	
	public function __construct($id)
	{
		$this->id = $id;
		if(!$this->get()) return false;
		
		$this->album = new album($this->album_id);
		$this->album->get();
	}
	
	
	private function get()
	{
		global $core;
		
		$core->db->select()->from('albums_tracks')->fields('$all')->where('id = '.$this->id);
		$core->db->execute('db_music');
		$core->db->get_rows(1);
		
		if(!is_array($core->db->rows) || !count($core->db->rows)) return false;
		
		$this->name = $core->db->rows['name'];
		$this->album_id = $core->db->rows['album_id'];
		$this->filename = $core->db->rows['filename'];
		$this->path = $core->CONFIG['albums']['albumsURL'];
		$this->fullpath = $this->path.$this->filename;
		
		return true;
	}
}

class album
{
	public $id = 0;
	public $artist = '';
	public $artistId = 0;
	public $album = '';
	public $title = '';
	public $rewrite = '';
	public $rewriteId = '';
	public $cover = 0;
	public $year = '';
	public $describe = '';
	public $tracks = array();
	public $genres = array();
	public $loadFromTorrent = false;
	public $createTorrent = true;
	public $createArchive = true;
	public $error = false;
	public $tags = array();
	public $archiveFile = false;
	public $torrentFile = false;
	public $artistNotFound = false;
	public $info = false;
	

	private $localFolder = '';
	private $fsFolder = '';
	private $albumType = 0; 
	private $userId = 0;
	private $createDate = 0;
	private $files = array();
	private $albumsTypes = array();
	private $possibleTypes = array();
	private $userGroups = array();
	private $checked = false;
	private $postData = array();
	private $genreListFromDB = false;
	private $statusUploadedFiles =false;
	private $statusUploadedCover = false;
	private $statusCreatedTorrent = false;
	
	private $torrentFilename = '';
	private $ziparchiveFilename = '';
		
	## Public
	
	public function __construct($id = false)
	{
		if($id) $this->id = $id;
		
		$this->postData = $_POST;
		$this->albumsTypes = admin_albums::getAlbumsTypes();
	}
	
	public function get()
	{
		global $core;
		
		$info = $this->getAlbumInfo();
		$core->lib->load('images');
		$cover = new images();
		$cover = $cover->get_image_info($info['cover_id']);
			
		$this->userId = $info['user_id'];
		$this->artistId = $info['artist_id'];
		$this->artist = $info['artist_name'];
		$this->album = $info['name'];
		$this->year = $info['year'];
		$this->info = $info;
		$this->title = $info['title'];
		$this->cover = $cover['filename'];
		$this->rewrite = $core->url_parser->get($info['rewrite_id']);
		$this->info['rewrite'] = $this->rewrite;
		$this->tracks = $this->getTracks();
		$this->genres = $this->getGenres();	
		$this->artistNotFound = ($info['artist_checked'])? 0 : 1;	
		$this->fsFolder = $core->CONFIG['albums']['albumsPath'].substr($this->rewrite, 0, -5);
		$this->localFolder = substr($this->rewrite, 0, -5);
		$this->comments = $this->getAlbumComments();
	}
	
	public function update()
	{
		
	}
	
	public function postCreate()
	{
		global $core;
		
		$data[0] = array();
		if(isset($_POST['AIyear'])) $data[0]['year'] = intval($_POST['AIyear']);
		if(isset($_POST['AlbumCategory'])) $data[0]['group'] = intval($_POST['AlbumCategory']);
		
		$data[0]['id'] = intval($_POST['AlbumId']);
		if(isset($_POST['AIcover']) && intval($_POST['AIcover']) >0) $data[0]['cover_id'] = intval($_POST['AIcover']);
				
		$core->db->connect(false, 'db_music');
		$core->db->autoupdate()->table('albums')->data($data)->primary('id');
		$core->db->execute('db_music');
		
		$this->id = $data[0]['id'];
		$this->get();
		
		$this->torrentFilename = substr($this->rewrite, 0, -5).'.torrent';
		$this->ziparchiveFilename = substr($this->rewrite, 0, -5).'.zip';
		
		//if(isset($_POST['createRar']) && intval($_POST['createRar'])>0) $this->createRar();
		 $this->createTorrent();
		
		return true;
	}
	
	public function create()
	{
		unset($_SESSION['AlbumCreator']);
		
		if(!$this->checked) $this->checkUploadAlbum();
		if($this->error) return false;
		
		$this->createDate = time();

		$this->userId = $this->postData['AIuser'];
		$this->setId();
		$this->artist = $this->escapeString(trim($this->postData['AIartist']));
		$this->artistId = $this->testSetArtist($this->escapeString($this->postData['AIartist']));
		$this->album = $this->escapeString($this->postData['AIalbum']);
		$this->year = intval($this->postData['AIyear']);
		$this->title = $this->artist.' - '.$this->album.' - '.$this->year;
		$this->cover = intval($this->postData['AIcover']);
		$this->rewrite = $this->checkAndCreateRewrite($this->postData['AIrewriteUrl']);
		$this->albumType = $this->setAlbumType();
		$this->genres = $this->setGenres($this->postData['AIstyle']);
		$this->setStepOne();
		
		//$this->saveSessionForAjax();
	
		return true;
	}

	public function testSetArtist($artist)
	{
		global $core;
		
		$artistSearch = strtolower($artist);
		
		$sql = "SELECT * FROM artists WHERE LOWER(title) = '{$artistSearch}' AND checked =1";
		$core->db->query($sql, 'db_music');
		$core->db->get_rows();
		
		if($core->db->rows && isset($core->db->rows[0]['id']))
		{
			return $core->db->rows[0]['id'];
		}
		else 
			{
				$this->artistNotFound = true;
				$core->db->autoupdate()->table('artists')->data(array(array('title'=>$artist, 'rewrite_id'=>0, 'checked'=>0)));
				$core->db->execute('db_music');
				
				return $core->db->insert_id;
			}
	}
	
	public function setStepOne()
	{
		global $core;
		
		$data = array();
		$data[] = array('id'=>$this->id, 'rewrite_id'=>$this->rewriteId, 'name'=>$this->album, 'title'=>$this->title, 'year'=>$this->year, 'group'=>$this->albumType, 'cover_id'=>$this->cover, 'artist_id'=>$this->artistId);

		$core->db->autoupdate()->table('albums')->data($data)->primary('id');
		$core->db->execute('db_music');
		
		$data = array();
		foreach($this->genres as $item)
		{
			$data[] = array('id_album'=>$this->id, 'id_genre'=>$item);	
		}
		
		$core->db->autoupdate()->table('albums_genres')->data($data);
		$core->db->execute('db_music');
	}
	
	public function checkUploadAlbum()
	{		
		global $core;
		
		if(!isset($this->postData['AIartist'])) return $this->error('Неверное значение поля Исполнитель');
		if(!isset($this->postData['AIalbum'])) return $this->error('Неверное значение поля Альбом');
		if(!isset($this->postData['AIstyle']) || !count($this->postData['AIstyle'])) return $this->error('Не выбран стиль.');
		if(!isset($this->postData['AIcover'])) $this->postData['AIcover'] = 0; else $this->postData['AIcover'] = intval($this->postData['AIcover']);
		if(isset($this->postData['AItype']) && $this->postData['AItype'] != 0) $this->albumType = intval($array['AItype']);
		if(!isset($this->postData['AIuser']) || intval($this->postData['AIuser']) == 0) $this->postData['AIuser'] = $core->user->id; else $this->postData['AIuser'] = intval($this->postData['AIuser']);
		if(!isset($this->postData['AIstyleSub'])) $this->postData['AIstyleSub'] = array();
		
		$this->postData['AIstyle'] = array_merge($this->postData['AIstyle'], $this->postData['AIstyleSub']);
		
		$this->checked = true;
	}
	
	## Private
		
	private function checkStatus()
	{
		$this->statusUploadedCover = ($this->cover)? true : false;
		$this->statusUploadedFiles = ($this->archiveFile)? true : false;
		$this->statusCreatedTorrent = ($this->torrentFile)? true : false;
		
		$status  = ($this->statusUploadedCover)? '1':'0';
		$status .= ($this->statusUploadedFiles)? '1':'0';
		$status .= ($this->statusCreatedTorrent)? '1':'0';		 
		
		return $status;
	}
	
	private function checkAndCreateRewrite($rewrite)
	{
		global $core;
		
		if($rewrite && strlen($rewrite)>5)
		{
			if(substr($rewrite, -5) != '.html')
			{
				$rewrite = $rewrite.'.html';				
			}
		}
		else
			{	
				$rewrite = admin_albums::rewriteFormed($this->artist).'_-_'.admin_albums::rewriteFormed($this->album).'.html';
			}	

		// Если реврайт есть в базе, то мы пытаемся добавить в него порядковый номр реврайта, начиная с двойки.
		// Очеь медленная часть кода (если реврайт находится в базе). надо будет оптимизировать. Или вообще избавится от автореврайта.
		if($this->checkRewriteFromDb($rewrite))
		{
			$count = 2;
			$startRewrite = substr($rewrite, 0, -5);
			
			while($this->checkRewriteFromDb($rewrite))
			{
				$rewrite = $startRewrite.'('.$count.')'.'.html';
				$count++;
			}
			
			unset($count, $startRewrite);
		}
			
		$this->rewriteId = $core->url_parser->add($rewrite, '/albums/show/id/'.$this->id, 'Music');
		
		return $rewrite;
	}
	
	private function checkRewriteFromDb($rewrite)
	{
		global $core;
		
		if($core->url_parser->get_id($rewrite, 'Music')) return true;
		
		return false;
	}
	
	private function escapeString($string)
	{
		$string = htmlspecialchars($string);
		
		return $string;
	}
	
	private function error($text)
	{
		$this->error = $text;
		
		return false;		
	}
	
	private function setId()
	{
		global $core;
	
		$data = array();
		$data[] = array('user_id'=>$core->user->id, 'date'=>$this->createDate, 'group'=>6);

		$core->db->autoupdate()->table('albums')->data($data);
		$core->db->execute('db_music');
		
		$this->id = $core->db->insert_id;
	}
	
	private function getAccessSettings()
	{
		global $core;
		
		$sql = "SELECT DISTINCT(sag.id_albums_type) as id_type, sat.name 
				FROM site_albums_types as sat, site_albums_group_types as sag, mcms_user_group as ug
				WHERE sat.id = sag.id_albums_type AND ug.id_user = 1 AND sag.id_user_group IN(ug.id_group)";
		
		$core->db->query($sql);
		$core->db->get_rows(false, 'id_type');
		
		$this->possibleTypes = $core->db->rows;
			
		
		$core->db->select()->from('mcms_user_group')->fields('$all')->where('id_user = '.$core->user->id);
		$core->db->execute();
		$core->db->get_rows(false, 'id_group');
		
		$this->userGroups = $core->db->rows;		
	}
	
	private function setAlbumType()
	{
		$this->getAccessSettings();
		$status = $this->checkStatus();
			
		$type = $this->autosetAlbumType($status);
				
		// FIXME:Hardcode! Только пользователи состоящие в группе 3 или 6 могут изменять тип записи
		if((isset($this->userGroups[6]) || isset($this->userGroups[3])) && $this->albumType != 6 && $this->albumType != 0)
		{
			if($type == 2 || $type == 1)
			{
				// :)
				$this->albumType = $this->albumType;	
			}
			else
				{
					$this->albumType = 	$type;	
				}		
		}
		else 
			{
	
				$this->albumType = 	$type;
			}
		
		return $this->albumType;
	}
		
	private function autosetAlbumType($status)
	{
		if($status == '000') return 6;
		if($status != '111' && $status != '011') return 3;
		
		if($status == '111' && isset($this->userGroups[5]) && !isset($this->userGroups[6])&& !isset($this->userGroups[4])&& !isset($this->userGroups[3])) return 1;
		if($status == '011' && isset($this->userGroups[5]) && !isset($this->userGroups[6])&& !isset($this->userGroups[4])&& !isset($this->userGroups[3])) return 1;
		
		if($status == '111' && (isset($this->userGroups[6]) || !isset($this->userGroups[3]))) return 2;
		if($status == '011' && (isset($this->userGroups[6]) || !isset($this->userGroups[3]))) return 2;
				
		if($status == '111' && isset($this->userGroups[4]) && !isset($this->userGroups[6])&& !isset($this->userGroups[5])&& !isset($this->userGroups[3])) return 2;
		if($status == '011' && isset($this->userGroups[4]) && !isset($this->userGroups[6])&& !isset($this->userGroups[5])&& !isset($this->userGroups[3])) return 1;
		
		if($status == '111' && isset($this->userGroups[5]) && !isset($this->userGroups[6])&& !isset($this->userGroups[4])&& !isset($this->userGroups[3])) return 1;
		if($status == '011' && isset($this->userGroups[5]) && !isset($this->userGroups[6])&& !isset($this->userGroups[4])&& !isset($this->userGroups[3])) return 1;	
	}
	
	private function setGenres($styles)
	{
		$genres = array();
		
		foreach($styles as $item)
		{
			if(intval($item))
			{
				$genreInfo = $this->getGenreById(intval($item));
				
				if($genreInfo)
				{
					$genres[] = intval($item);
					$this->addTag($genreInfo['name']);
				}
			}
		}
		
		return $genres;
	}
	
	private function addTag($tag)
	{
		$this->tags[] = $tag;
	}
	
	private function getGenreById($id)
	{
		if(!$this->genreListFromDB) $this->getAllGenres();
		
		if(!isset($this->genreListFromDB[$id])) return false;
		
		return $this->genreListFromDB[$id];		
	}
	
	private function getAllGenres()
	{
		global $core;
		
		$core->db->select()->from('genres')->fields('$all')->order('parent_id');
		$core->db->execute('db_music');
		$core->db->get_rows(false, 'id');
		
		$this->genreListFromDB = $core->db->rows;		
	}
	
	private function getAlbumComments()
	{
		global $core;
		
		$sql = "SELECT 
					ac.user_id, 
					u.login, 
					ac.date as cdate, 
					ac.date as ctime,
					'img_2a28eb7cda6909088d605c64fad55c7d.jpg' as userpic, 
					ac.comment
				FROM 
					music_db.albums_comments as ac,
					`m-cms.org`.mcms_user as u
				WHERE 
					ac.album_id = {$this->id} AND	
					u.id_user = ac.user_id
				ORDER BY ac.date";
		$core->db->query($sql);
		$core->db->colback_func_param = 0;
		$core->db->add_fields_deform(array('cdate', 'comment', 'ctime'));
		$core->db->add_fields_func(array('date,"d.m.Y"', 'stripslashes', 'date,"H:i"'));
		$core->db->get_rows();
				
		return $core->db->rows;
	}
	
	private function saveSessionForAjax()
	{
		$vars = get_object_vars($this);
		unset($vars['genreListFromDB'],$vars['postData'],$vars['userGroups'],$vars['possibleTypes'],$vars['albumsTypes'],$vars['genres'],$vars['tags'],$vars['statusUploadedFiles'],$vars['statusUploadedCover'],$vars['statusCreatedTorrent']);

		$_SESSION['AlbumCreator'][$this->id] = $vars;
		
		unset($vars);
	}

	private function getAlbumInfo()
	{
		global $core;
		
		$sql = "SELECT 
						al.*, 
						ar.title as artist_name, 
						ar.checked as artist_checked, 
						(SELECT COUNT(*) FROM albums_tracks WHERE album_id = {$this->id}) as tracks_count,
						(SELECT COUNT(*) FROM `m-cms.org`.site_torrent_peers as p WHERE p.info_hash = al.torrent_hash AND p.left = 0) as counter,
						us.name as uname,
						us.login,
						us.email
						
				FROM 
						music_db.albums as al, 
						music_db.artists as ar,
						`m-cms.org`.mcms_user as us
						 
				WHERE 
						al.id = {$this->id} AND 
						ar.id = al.artist_id AND 
						us.id_user = al.user_id";
		
		$core->db->query($sql, 'db_music');
		$core->db->colback_func_param = 0;
		$core->db->add_fields_deform(array('date', 'title', 'size'));
		$core->db->add_fields_func(array('dateAgo', 'stripslashes', 'get_formated_file_size'));
		$core->db->get_rows(1);
				
		return $core->db->rows;
	}
	
	private function getTracks()
	{
		global $core;
		
		$core->db->select()->from('albums_tracks')->fields()->where('album_id = '.$this->id);
		$core->db->execute('db_music');
		$core->db->add_fields_deform(array('filename'));
		$core->db->add_fields_func(array('makeAlbumTrackUrl'));
		$core->db->get_rows(false, 'id');
				
		return $core->db->rows;		
	}

	private function getGenres()
	{
		global $core;
		
		$sql = "SELECT ag.*, g.name FROM albums_genres as ag, genres as g WHERE ag.id_album = {$this->id} AND g.id = ag.id_genre";
		$core->db->query($sql, 'db_music');
		$core->db->get_rows(false, 'id_genre');
		
		return $core->db->rows;	
	}

	private function createRar()
	{
		global $core;
		
		$test = new zip_file($core->CONFIG['albums']['zipPath'].$this->ziparchiveFilename);
		$test->set_options(array('overwrite' => 1, 'level' => 1, 'basedir' => $this->fsFolder));
		$test->add_files("*.*");
		$test->create_archive();
	}
	
	private function createTorrent()
	{
		global $core;
		
		$torrent = new Torrent($this->fsFolder, $core->CONFIG['albums']['torrent']['anoncePath']);
		$torrent->is_private(true);
		$torrent->comment('It\'s ultra-music torrent server.');
		$torrent->name($this->localFolder);
		

		
		if (!$this->error = $torrent->error())
		{
		
			$torrent->save($core->CONFIG['albums']['torrent']['pathForSave'].$this->torrentFilename);
			
			$sql = "UPDATE albums SET torrent = '{$this->torrentFilename}', torrent_hash = '{$torrent->hash_info()}', size = {$torrent->size()} WHERE id = {$this->id}";
			
//			$data[] = array('id'=>$this->id, 'torrent'=>$this->torrentFilename, 'torrent_hash'=>$torrent->hash_info(), 'size'=>$torrent->size());
//			$core->db->autoupdate()->table('albums')->data($data)->primary('id');
//			$core->db->execute('db_music');
			$core->db->query($sql, 'db_music');
			
			$this->startTorrent();
			
			return true;
		}
	}
	
	public function startTorrent()
	{
		global $core;
		
		$escapedFilename = str_replace(array(')', '('), array('\)','\('), $this->torrentFilename);		 
		
		shell_exec('sudo /usr/bin/qbittorrent-nox '.$core->CONFIG['albums']['torrent']['pathForSave'].$escapedFilename);
		shell_exec('sudo /usr/bin/qbittorrent-nox '.$core->CONFIG['albums']['torrent']['pathForSave'].$escapedFilename);


		$tuCurl = curl_init();
		curl_setopt($tuCurl, CURLOPT_URL, "http://bt1.sharedmp3.ru/addtorrent.php?pass=QbRu77aJ&file={$this->torrentFilename}");
		curl_setopt($tuCurl, CURLOPT_VERBOSE, 0);
		curl_setopt($tuCurl, CURLOPT_HEADER, 0);
		curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, 1);

		$tuData = curl_exec($tuCurl);
	} 
	
}

function StrPadNull($str)
{
	if(strlen($str) >1) return $str;
	
	return '0'.$str;
}

function makeAlbumTrackUrl($track)
{
	global $core;
	
	
	return $core->CONFIG['albums']['albumsURL'].$track;
	
	
	
	
	return base64_encode($core->CONFIG['albums']['albumsURL'].$track);
}

?>
