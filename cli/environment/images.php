<?php
################################################################################
# ---------------------------------------------------------------------------- #
# @Author: Alexey Pshenichniy <inc.mcms@gmail.com> <M-zone Inc.> 2005-2009     #
# @Date: 20.05.2008                                                            #
# @license: GNU GENERAL PUBLIC LICENSE Version 2                               #
# ---------------------------------------------------------------------------- #
# M-cms v4.1 (core build - 4.102)                                              #
################################################################################


class images
{

	public $info = array();
	public $id	 = 0;
	public $source = NUll;
	public $logo_positions = array();
	public $errors = array();
	public $image = array();
	public $mime = '';
	public $fname = '';
	public $mimeSource = false;
	public $sizeSource = array();

	public $site_id = 0;
	public $upload_folder = '';
	public $preview_folder = '';
	public $local_upload_folder = '';
	public $local_preview_folder = '';
	
	

	public function __construct($uploadFolder = false, $preview_folder=false, $uploadLocalFolder = false, $previewLocalFolder = false, $site_id=1)
	{
		global $core;
		
		//if(!extension_loaded('GD')) die('not load GD extension');

		$this->site_id = $site_id;
		$this->upload_folder = ($uploadFolder)? $uploadFolder : $core->CONFIG['imgPath'];
		$this->preview_folder = ($preview_folder)? $preview_folder : $core->CONFIG['imgPath'];
		$this->local_preview_folder = ($previewLocalFolder)? $previewLocalFolder : $core->CONFIG['img_local_path'];
		$this->local_upload_folder = ($uploadLocalFolder)? $uploadLocalFolder : $core->CONFIG['img_local_path'];
	}

	public function upload()
	{
		if(count($_FILES))
		{

		if(isset($_FILES['image']))
			{
			$this->set_info($_FILES['image']);

			if(!move_uploaded_file($_FILES['image']['tmp_name'], $this->info['new_path']))
				{
				$this->error_report('Ошибка закачки картинки -> файл был загружен, но небыл обработан.');
				return false;
				}

			list($this->info['width'], $this->info['height']) =  getimagesize($this->info['new_path']);
			}
			else
				{
				$this->error_report('Ошибка закачки картинки -> файл небыл загружен на сервер.');
				}
		}
		else
			{
			$this->error_report('Ошибка закачки картинки -> файл небыл загружен на сервер.');
			return false;
			}

		return true;
	}
		
	public function saveFromFile($folder_id, $describe)
	{
		$this->info['name'] = '';
		$this->info['describe'] = $describe;
		$this->info['p_height'] = 0;
		$this->info['p_width'] = 0;
		$this->info['id_folder'] = $folder_id;

		if(!rename($this->fname, $this->info['new_path']))
		{
			
			return false;
		}

		$this->fname = $this->info['local_filename'];
		$this->save();

		return $this->id;
	}
	
	public function merge($img_path, $position=1, $margin=5, $opacity=100)
	{
		global $core;

		if(!file_exists($this->info['new_path']))
			{
			$this->error_report('Ошибка добавления логотипа -> не загружена базовая картинка.');
			return false;
			}

		if(!file_exists($img_path))
			{
			$this->error_report('Ошибка добавления логотипа -> не загружена картинка логотипа.');
			return false;
			}

		list($src['w'], $src['h']) =  getimagesize($img_path);

		if(!$src['w'] || !$src['h'])
			{
			$this->error_report('Ошибка добавления логотипа -> невозможно оприделить размеры логотипа.');
			return false;
			}


		$src_img = $this->open($img_path);

		if(!$src_img)
			{
			$this->error_report('Ошибка добавления логотипа -> невозможно открыть картинку логотипа.');
			return false;
			}
		@imagecolortransparent($src_img);
		$positions = $this->get_logo_position($src['w'], $src['h'], $position, $margin);

		if(!$this->source) $this->open();
		if(!$this->source)
			{
			$this->error_report('Ошибка добавления логотипа -> невозможно открыть базовый рисунок.');
			@imagedestroy($src_img);
			return false;
			}

		if(!@imagecopymerge($this->source, $src_img, $positions['x'], $positions['y'], 0, 0, $src['w'], $src['h'], $opacity))
			{
			$this->error_report('Ошибка добавления логотипа -> невозможно объеденить логотип и базовую картинку.');
			@imagedestroy($src_img);
			return false;
			}

		@imagedestroy($src_img);
		return true;
	}

	public function create_preview($width, $height, $ration = 0, $baseline, $quality=100)
	{
		if(!$this->resize($width, $height,$this->info['preview_fname'],$ration, $baseline, $quality))
		{
			$this->error_report('Ошибка создания превью картинки.');
		}
		else
			{
				if($this->id)
				{
					global $core;

					$p_info = getimagesize($this->info['preview_fname']);
					
					$this->info['p_width'] = $p_info[0];
					$this->info['p_height'] = $p_info[1];
					
					unset($p_info);
					
					$data[] = array('id_image'=>$this->id,
							'p_width'=>$this->info['p_width'],
							'p_height'=>$this->info['p_height'],
							'preview_filename'=>$this->info['preview_path']);
		

					$core->db->autoupdate()->table('mcms_images')->data($data)->primary('id_image');
					$core->db->execute();
				}
			}
		
		return $this->info['preview_path'];
	}

	public function save()
	{
		global $core;

		$img_id = $core->MaxId('id_image', 'mcms_images')+1;

	
			$data[] = array('id_image'=>$img_id,
							'id_site'=>$this->info['id_site'],
							'name'=>addslashes($this->info['name']),
							'describe'=>addslashes(htmlspecialchars($this->info['describe'])),
							'filename'=>$this->info['local_filename'],
							'width'=>$this->info['width'],
							'height'=>$this->info['height'],
							'p_width'=>$this->info['p_width'],
							'p_height'=>$this->info['p_height'],
							'size'=>$this->info['size'],
							'alias'=>$this->info['alias'],
							'uid'=>$core->user->id,
							'reg_date'=>time(),
							'folder_id'=>$this->info['id_folder'],
							'preview_filename'=>$this->info['preview_path']);
		

		$core->db->autoupdate()->table('mcms_images')->data($data);
		$core->db->execute();

		$this->id = $img_id;
	}

	public function open($filename=false)
	{
		global $core;


		if($filename)
		{
			$image_info = @getImageSize($filename);


			switch($image_info['mime'])
 			{
		        case 'image/gif':
					$source = @imageCreateFromGIF($filename);
		        break;

		        case 'image/jpeg':
					$source = @imageCreateFromJPEG($filename);
		        break;

		        case 'image/png':
					$source = @imageCreateFromPNG($filename);
		        break;

		        case 'image/wbmp':
					$source = @imageCreateFromWBMP($filename);
		        break;
			}

			if(!$source) return false;

			
			return $source;
		}
		else
			{
				if(!isset($this->info['mime']) && !$this->fname) return false;
				if(!isset($this->info['mime']) && $this->setInfoFromFilename($this->fname) == false) return false;
				
				$this->mime = $this->info['mime'];

				switch($this->info['mime'])
	 			{
			        case 'image/gif':
						$this->source = @imageCreateFromGIF($this->fname);
			        break;

			        case 'image/jpeg':
						$this->source = @imageCreateFromJPEG($this->fname);
			        break;

			        case 'image/png':
						$this->source = @imageCreateFromPNG($this->fname);
			        break;

			        case 'image/wbmp':
						$this->source = @imageCreateFromWBMP($this->fname);
			        break;
				}
				
				if(!$this->source) return false;
				
				
				return true;
			}


	}
	
	public function writeFile($source, $filename, $quality)
	{
		if($source)
		{
				switch($this->mime)
 				{
			        case 'image/gif':
						imagegif($source, $filename);
			        break;
	
			        case 'image/jpeg':
						imagejpeg($source, $filename, $quality);
			        break;
	
			        case 'image/png':
						imagepng($source, $filename);
			        break;
	
			        case 'image/wbmp':
						imagewbmp($source, $filename);
			        break;
			}

			imagedestroy($source);
		}
	}

	public function close($save = true)
	{
		if($this->source)
		{
			if($save)
			{
				switch($this->mime)
 				{
			        case 'image/gif':
						imagegif($this->source, $this->info['new_path']);
			        break;
	
			        case 'image/jpeg':
						imagejpeg($this->source, $this->info['new_path']);
			        break;
	
			        case 'image/png':
						imagepng($this->source, $this->info['new_path']);
			        break;
	
			        case 'image/wbmp':
						imagewbmp($this->source, $this->info['new_path']);
			        break;
 				}
			}

			imagedestroy($this->source);
		}
	}

	public function print_error_report()
	{
		foreach($this->errors as $error)
			{
			$out .= 'Fatal error: '.$error.'<br>';
			}
		return $out;
	}

	public function get_image_info($image_id)
	{
		if(isset($this->image[$image_id])) return $this->image[$image_id];

		global $core;

		$sql = "SELECT im.id_image, im.folder_id, im.preview_filename, im.name, im.describe, im.filename, im.alias, im.width, im.height, im.p_width, im.p_height, im.size, im.uid, im.reg_date FROM mcms_images as im WHERE im.id_image = ".intval($image_id);

		$core->db->query($sql);
		$core->db->get_rows(1);

		$this->image[$image_id] = $core->db->rows;
		return $this->image[$image_id];
	}

	public function resizeResample($resultWidth=false, $resultHeight=false, $destinationPath, $destination_pic_local) {
		if(!$this->source) $this->open();
		if(!$this->source) return false;
		
		$src_w = $this->info['width'];
		$src_h = $this->info['height'];
		$src_x = 0;
		$src_y = 0;
		
		if($resultWidth == false && $resultHeight == false) {
			$dst_w = $this->info['width'];
			$dst_h = $this->info['height'];
			
			if($this->info['width'] > $this->info['height']) {
				$dst_y = ceil(($this->info['width']-$this->info['height'])/2);
				$dst_x = 0;
				$resultWidth = $resultHeight = $this->info['width'];
			} else {
				$dst_y = 0;
				$dst_x = ceil(($this->info['height']-$this->info['width'])/2);
				$resultWidth = $resultHeight = $this->info['height'];
			}
		} else {
				
			$x_ratio = $resultWidth / $this->info['width'];
			$y_ratio = $resultHeight / $this->info['height'];
			
			if($this->info['width'] > $this->info['height']) {
				$dst_h = ceil($x_ratio * $this->info['height']);
				$dst_w = $resultWidth;
				$dst_x = 0;
				$dst_y = ceil(($resultHeight-$dst_h)/2);
			} else {
				$dst_w = ceil($y_ratio * $this->info['width']);
				$dst_h = $resultHeight;
				$dst_y = 0;
				$dst_x = ceil(($resultWidth-$dst_w)/2);
			}
		}

		$tmp=imagecreatetruecolor($resultWidth, $resultHeight);
		imagefill($tmp, 0, 0, imagecolorallocate($tmp, 255, 255, 255));
		imagealphablending($tmp, true);
		
		if(!$tmp) {
			$this->error_report('Ошибка создания превью картинки. -> imagecreatetruecolor');
			return false;
		}
		
		
		$copyResult = imagecopyresized($tmp, $this->source,  $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
		
		if(!$copyResult) {
			$this->error_report('Ошибка создания превью картинки. -> imagecopyresized');
			return false;
		}
		
		$saveResult = imagejpeg($tmp, $destinationPath.$destination_pic_local, 100);
		
		if(!$saveResult) {
			$this->error_report('Ошибка создания превью картинки. -> imagejpeg');
			return false;
		}
		
		@imagedestroy($tmp);
		
		return array('width'=>$resultWidth,'height'=>$resultHeight, 'local'=>$destination_pic_local);
	}
		
	public function resize($new_width=100, $new_height=100, $destination_pic, $ration=0, $baseline, $quality=100, $activeAdjust = false)
	{
		if(!$this->source) $this->open();
		if(!$this->source) return false;

		if(!$this->source) $this->open();
		if(!$this->source) return false;
		
		if($ration) {
			$x_ratio = $new_width / $this->info['width'];
			$y_ratio = $new_height / $this->info['height'];
		
			if(($this->info['width'] <= $new_width) && ($this->info['height'] <= $new_height)) {
				$tn_width = $this->info['width'];
				$tn_height = $this->info['height'];
			} else {
				if($baseline == 'w') {
					$tn_height = ceil($x_ratio * $this->info['height']);
					$tn_width = $new_width;
				} elseif($baseline == 'h') {
					$tn_width = ceil($y_ratio * $this->info['width']);
					$tn_height = $new_height;
				}
				
				if($activeAdjust && ($baseline == 'w' || $baseline = 'h')) {
					if($tn_width > $new_width) {
						$tn_width = $new_width;
						$tn_height = ceil($x_ratio * $this->info['height']);
					} else if($tn_height > $new_height){
						$tn_height = $new_height;
						$tn_width = ceil($y_ratio * $this->info['width']);
					}
				}
			}
		
		}
		else {
			$tn_width = $new_width;
			$tn_height = $new_height;
		}

		//echo "->>>>> $new_width, $new_height, $destination_pic, $ration, $baseline, $quality\n";
		//$tmp=imagecreatetruecolor($tn_width,$tn_height);
		
		//
		$tmp=imagecreatetruecolor($tn_width,$tn_height);
	
		// прозрачность
		if($this->mime == 'image/png') {
			imagesavealpha($tmp, true);
			imagefill($tmp, 0, 0, imagecolorallocatealpha($tmp, 0, 0, 0, 127));
		}
		
		
		if(!$tmp)
		{
			$this->error_report('Ошибка создания превью картинки. -> imagecreatetruecolor');
			return false;
		}

		$res = imagecopyresampled($tmp,$this->source,0,0,0,0,$tn_width, $tn_height,$this->info['width'],$this->info['height']);
		if(!$res)
		{
			@imagedestroy($tmp);
			$this->error_report('Ошибка копирования картинки -> imagecopyresampled');
			return false;
		}
	
		$this->writeFile($tmp,$destination_pic,$quality);
		//$res = imagejpeg($tmp,$destination_pic,$quality);
		//if(!$res)
		//{
		//	@imagedestroy($tmp);
		//	$this->error_report('Ошибка сохранения картинки -> imagejpeg');
		//	return false;
		//}
		@imagedestroy($tmp);

		$this->info['p_width'] = $tn_width;
		$this->info['p_height'] = $tn_height;

		return true;
	}
	
	public function updateSize()
	{
		clearstatcache();
		$image_info = @getimagesize($this->fname);
		
		$this->info['width'] = $image_info[0];
		$this->info['height'] = $image_info[1];
		$this->info['size'] = filesize($this->fname);
	}
	
	
	
	private function set_info($file)
	{
		$uploaded_file_name = (md5(time().microtime()));

		$extension = substr($file['name'], strrpos($file['name'], '.')+1);

		$this->info['id_site'] 		  = $this->site_id;
		$this->info['new_name'] 	  = 'img_'.$uploaded_file_name.'.'.$extension;
		$this->info['new_path'] 	  = $this->upload_folder.'img_'.$uploaded_file_name.'.'.$extension;
		$this->info['mime'] 		  = $file['type'];
		$this->info['size'] 		  = $file['size'];
		$this->info['preview'] 		  = 'p_img_'.$uploaded_file_name.'.'.$extension;
		$this->info['preview_fname']  = $this->preview_folder.'p_img_'.$uploaded_file_name.'.'.$extension;
		$this->info['preview_path']   = $this->local_preview_folder.'p_img_'.$uploaded_file_name.'.'.$extension;
		$this->info['local_filename'] =	$this->local_upload_folder.'img_'.$uploaded_file_name.'.'.$extension;
		$this->info['alias'] 		  = $file['name'];
		
		return $this->info;
	}

	private function get_logo_position($logo_width, $logo_height, $position = 1, $margin = 5)
	{

		if(is_array($margin))
			{
			$margin_left 	= (isset($margin['left']))? intval($margin['left']) : 0;
			$margin_right 	= (isset($margin['right']))? intval($margin['right']) : 0;
			$margin_top 	= (isset($margin['top']))? intval($margin['top']) : 0;
			$margin_bottom 	= (isset($margin['bottom']))? intval($margin['bottom']) : 0;
			}
			else
				{
				$margin_left 	= $margin;
				$margin_right 	= $margin;
				$margin_top 	= $margin;
				$margin_bottom 	= $margin;
				}

		$center_x = round($this->info['width'] / 2);
		$center_y = round($this->info['height'] / 2);

		$logo_c_x = round($logo_width / 2);
		$logo_c_y = round($logo_height / 2);

		switch($position)
		{
			//слева в верху
			case 1:
				$x_position = $margin_left;
				$y_position = $margin_top;
			break;

			//справа в верху
			case 2:
				$x_position = $this->info['width'] - $margin_right - $logo_width;
				$y_position = $margin_top;
			break;

			//справа внизу
			case 3:
				$x_position = $this->info['width'] - $margin_right - $logo_width;
				$y_position = $this->info['height'] - $margin_bottom  - $logo_height;
			break;

			//слева внизу
			case 4:
				$x_position = $margin_left;
				$y_position = $this->info['height'] - $margin_bottom  - $logo_height;
			break;

			//сверху по середине
			case 5:
				$x_position = $center_x - $logo_c_x;
				$y_position = $margin_top;
			break;

			//снизу по середине
			case 6:
				$x_position = $center_x - $logo_c_x;
				$y_position = $this->info['height'] - $margin_bottom - $logo_height;
			break;

			//в центре
			case 7:
				$x_position = $center_x - $logo_c_x;
				$y_position = $center_y - $logo_c_y;
			break;
		}


		return array('x'=>$x_position, 'y'=>$y_position);
	}

	private function error_report($msg)
	{
		$this->errors[] =$msg;
	}

	private function setInfoFromFilename($filename)
	{
		unset($this->info);
		
		$image_info = @getimagesize($filename);
			
		if(!$image_info || !is_array($image_info) || !isset($image_info['mime'])) return false;
			
		$file_info['name'] = basename($filename);
		$file_info['size'] = filesize($filename);
		$file_info['type'] = $image_info['mime'];
		
		$this->info['width'] = $image_info[0];
		$this->info['height'] = $image_info[1];
		
		$this->set_info($file_info);
				
		return true;
	}

	public static function checkMime($file)
	{
		if(!file_exists($file)) return false;

		$mime = array('image/gif', 'image/jpeg', 'image/png', 'image/wbmp');
		$image = getimagesize($file);
		
		if($image === false) return false;
		if(!in_array($image['mime'], $mime)) return false;
		
		return true;
	}

	public function __destruct()
	{
		if($this->source)
		@imagedestroy($this->source);
	}
	
}


?>