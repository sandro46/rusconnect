<?php
############################################################################
#          This controller was created automatically core system           #
#                                                                          #
# ------------------------------------------------------------------------ #
# @Creator module version 1.2598 b                                         #
# @Author: Alexey Pshenichniy                                              #
# ------------------------------------------------------------------------ #
# Alpari CMS v.1 Beta   $17.06.2008                                        #
############################################################################


$controller->id = 38;
$controller->cached = 0;
$controller->init();


$allowedHandlers = array(
    'resizeImage'=>1, 
    'moveTemplate'=>1,
    'moveTemplateVideo'=>1, 
    'ckeditor'=>1, 
    'squaerresize'=>1, 
    'shop_import'=>1, 
    'shop_import_extended'=>1);


class qqUploadedFileXhr {
    function save($path) {    
    	
        $input = fopen("php://input", "r");
        $target = fopen($path, "w");
        
        while($size = stream_copy_to_stream($input, $target, 1024)) {
        	if(!$size) return false;
        }

        return true;
    }
    function getName() {
        return $_GET['qqfile'];
    }
    function getSize() {
        if (isset($_SERVER["CONTENT_LENGTH"])){
            return (int)$_SERVER["CONTENT_LENGTH"];            
        } else {
            throw new Exception('Getting content length is not supported.');
        }      
    }   
}

class qqUploadedFileForm {  
    function save($path) {
        if(!move_uploaded_file($_FILES['qqfile']['tmp_name'], $path)){
            return false;
        }
        return true;
    }
    function getName() {
        return $_FILES['qqfile']['name'];
    }
    function getSize() {
        return $_FILES['qqfile']['size'];
    }
}

class qqFileUploader {
    private $allowedExtensions = array();
    private $sizeLimit = 209715200;
    private $file;

    function __construct(array $allowedExtensions = array(), $sizeLimit = 209715200){        
        $allowedExtensions = array_map("strtolower", $allowedExtensions);
            
        $this->allowedExtensions = $allowedExtensions;        
        $this->sizeLimit = $sizeLimit;
        
        $this->checkServerSettings();       
        
        if (isset($_GET['qqfile'])) {
            $this->file = new qqUploadedFileXhr();
        } elseif(isset($_POST['qqfile'])) {
        	$_GET['qqfile'] = $_POST['qqfile']; 
        	$this->file = new qqUploadedFileXhr();
        } elseif(isset($_FILES['qqfile'])) {
            $this->file = new qqUploadedFileForm();
        } else {
            $this->file = false;
        }
    }
    
    private function checkServerSettings(){        
        $postSize = $this->toBytes(ini_get('post_max_size'));
        $uploadSize = $this->toBytes(ini_get('upload_max_filesize'));               
    }
    
    private function toBytes($str){
        $val = trim($str);
        $last = strtolower($str[strlen($str)-1]);
        switch($last) {
            case 'g': $val *= 1024;
            case 'm': $val *= 1024;
            case 'k': $val *= 1024;        
        }
        return $val;
    }
    
    /**
     * Returns array('success'=>true) or array('error'=>'error message')
     */
    function handleUpload($uploadDirectory, $replaceOldFile = FALSE){
        if (!is_writable($uploadDirectory)){
            return array('error' => "Server error. Upload directory isn't writable. $uploadDirectory");
        }
        
        if (!$this->file){
            return array('error' => 'No files were uploaded.');
        }
       
        
        $size = $this->file->getSize();
        
        if ($size == 0) {
            return array('error' => 'File is empty');
        }
        
        if ($size > $this->sizeLimit) {
            return array('error' => 'File is too large');
        }
        
        $pathinfo = pathinfo($this->file->getName());
        $filename = $pathinfo['filename'];
        //$filename = md5(uniqid());
        $ext = $pathinfo['extension'];

        if($this->allowedExtensions && !in_array(strtolower($ext), $this->allowedExtensions)){
            $these = implode(', ', $this->allowedExtensions);
            return array('error' => 'File has an invalid extension, it should be one of '. $these . '.');
        }
        
        if(!$replaceOldFile){
            /// don't overwrite previous files that were uploaded
            while (file_exists($uploadDirectory . $filename . '.' . $ext)) {
                $filename .= rand(10, 99);
            }
        }
        
        if ($this->file->save($uploadDirectory . $filename . '.' . $ext)){
            return array('success'=>true, 'filename'=>$filename . '.' . $ext);
        } else {
            return array('error'=> 'Could not save uploaded file.' .
                'The upload was cancelled, or server error encountered');
        }
        
    }    
}

function squaerresize($source, $destPath, $size = false){
	global $core;
	$fileSource = moveTemplate($source, $destPath);
		
	$core->lib->load('images');
	$destPathLocal = '/vars/files/images/';
	$destPath = CORE_PATH.substr($destPathLocal, 1);
	
	$sprev1 = (is_array($size) && isset($size[0]))? $size[0] : 220;
	$sprev2 = (is_array($size) && isset($size[1]))? $size[1] : false;
	$sprev3 = (is_array($size) && isset($size[2]))? $size[2] : false;
	
	$fnameBig = 'big_'.$fileSource['filename'];
	$fnamePrev = $sprev1.'_'.$fileSource['filename'];
	$fnamePrev2 = $sprev2.'_'.$fileSource['filename'];
	$fnamePrev2 = $sprev3.'_'.$fileSource['filename'];
	$sorceFullPath = CORE_PATH.$fileSource['local'];
		
	$mimeCheckResult = images::checkMime($sorceFullPath);
	if($mimeCheckResult !== true) {
		//unlink($source);
		return array('error'=>'File is not image' , 'from'=>4,  'code'=>$mimeCheckResult);
	}
	
	$core->images = new images(false, false, false, false, 2);
	$core->images->fname = $sorceFullPath;
	
	if(!$core->images->open()){
		//unlink($sorceFullPath);
		return false;
	}
	
	$bigInfo = $core->images->resizeResample(false,false,$destPath,$fnameBig);
	
		
	$core->images->resizeResample($sprev1,$sprev1,$destPath,$fnamePrev);
	$return = array();
	
	$return[] = array('name'=>$fileSource['local'], 'size'=>$bigInfo['width']);
	$return[] = array('name'=>$destPathLocal.$fnameBig, 'size'=>$sprev1);
	
	
	if($sprev2) {
		$core->images->resizeResample($sprev2,$sprev2,$destPath,$fnamePrev2);
		$return[] = array('name'=>$destPathLocal.$fnamePrev2, 'size'=>$sprev2);
	}
	
	if($sprev3) {
		$core->images->resizeResample($sprev3,$sprev3,$destPath,$fnamePrev3);
		$return[] = array('name'=>$destPathLocal.$fnamePrev3, 'size'=>$sprev3);
	}
	
	
	$return['resizedWidth']=$bigInfo['width'];
	$return['resizedHeight']=$bigInfo['height'];
	
	

	return $return;	
}

function smartresize($source, $destPath, $width, $height) {
	global $core;
	$fileSource = moveTemplate($source, $destPath, true);
	
	$core->lib->load('images');
	$destPathLocal = '/vars/files/images/';
	$destPath = CORE_PATH.substr($destPathLocal, 1);
	$sorceFullPath = CORE_PATH.$fileSource['local'];
	$destFilename = md5(time().microtime()) . '.' . pathinfo($sorceFullPath, PATHINFO_EXTENSION);
	
	$mimeCheckResult = images::checkMime($sorceFullPath);
	if($mimeCheckResult !== true) {
		//unlink($source);
		return array('error'=>'File is not image', 'from'=>3, 'code'=>$mimeCheckResult);
	}
	
	$core->images = new images(false, false, false, false, 2);
	$core->images->fname = $sorceFullPath;
	
	if(!$core->images->open()){
		//unlink($sorceFullPath);
		return false;
	}
	
	if($core->images->info['width'] > $core->images->info['height']) {
		$baseline = 'w';
	} else {
		$baseline = 'h';
	}
	
	$test = $core->images->resize($width, $height, $destPath.$destFilename, true, $baseline, 100, true);
	
	
	//$core->images->resize($width, $height, $destPath.$destFilename, true, $baseline, 100);
	
	//print_r($core->images->errors);
	$core->images->close(false);
	
	
	return array('name'=>$destPathLocal.$destFilename, 'size'=>array($width,$height));
}

function moveTemplateVideo($source, $destPath, $useCopy = false) {
	$destPath = CORE_PATH.'/vars/files/video/';
	if(!is_dir($destPath)) {
		@mkdir($destPath,'0775');
	}
	
	$sourceInfo = pathinfo($source);
	$destFilename = md5_file($source).'.'.$sourceInfo['extension'];
	
	
	
	return array('local'=>'/vars/files/video/'.$destFilename, 'filename'=>$destFilename);
}

function moveTemplate($source, $destPath, $useCopy = false){
	$destPath = $destPath;
	
	$sourceInfo = pathinfo($source);
	$destFilename = md5($source.time().microtime()).'.'.$sourceInfo['extension'];
	
	if($useCopy) {
		copy($source, $destPath.$destFilename);
	} else {
		rename($source, $destPath.$destFilename);
	}
	
	return array('local'=>'/vars/files/'.$destFilename, 'filename'=>$destFilename);
}

function resizeImage($source, $destPath=false, $width=0,$height=0,$base='w',$ration=true){
	global $core;
	
	$core->lib->load('images');
	
	$destPath = $destPath.'images/';
	$mimeCheckResult = images::checkMime($source);
	
	if($mimeCheckResult !== true){
		//unlink($source);
		return array('error'=>'File is not image', 'from'=>2, 'code'=>$mimeCheckResult);
	}
	
	$core->images = new images(false, false, false, false, 2);
	$core->images->fname = $source;
				
	if(!$core->images->open()) {
		//unlink($source);
		return false;			
	}
	
	$newfilename = md5(time().microtime()).'.jpg';
	
	$core->images->resize($width, $height, $destPath.$newfilename, $ration, $base, 100, 'image/jpeg');
	$core->images->close(false);
	
	return array('name'=>'/vars/files/images/'.$newfilename, 'size'=>array($width,$height), 'filename'=>$destPath.$newfilename);
}

function modernUploaderProcess($file) {
	global $core;

	$newFileName = 'upload_'.md5(time().microtime()).'.'.pathinfo($file['name'], PATHINFO_EXTENSION);
		
	$dir = CORE_PATH.'vars/files/';
	$full_path = $dir.$newFileName;
	$http_path = '/vars/files/'.$newFileName;
	$error = '';
		
	if(move_uploaded_file($file['tmp_name'], $full_path) ) {
			
	} else {
		$error = 'Что-то пошло не так!';
		$http_path = '';
	}
	
	
	if(isset($_POST['maxsize']) && !empty($_POST['maxsize'])) {
		$maxsize =  json_decode(urldecode($_POST['maxsize']), true);
		$size = getimagesize($full_path);
		$maxsize = explode('x', $maxsize);
		if($maxsize && count($maxsize) == 2 && $size && count($size) > 2) {
			if($size[0] > $maxsize[0]) return array('error'=>true,'code'=>145, 'size'=>$size[0], 'limit'=>$maxsize[0], 'message'=>'The image is too large. Image width: '.$size[0].' Max. width: '.$maxsize[0]);
			if($size[1] > $maxsize[1]) return array('error'=>true,'code'=>146, 'size'=>$size[1], 'limit'=>$maxsize[1], 'message'=>'The image is too large. Image height: '.$size[1].' Max. height: '.$maxsize[1]);
		}
	}
	
	$result = array();
	$useResize = false;
	if(!empty($_POST['resize'])) {
		$resize = urldecode($_POST['resize']);
		$useResize = true;
		if(strlen($resize)) {
			$resize = json_decode($resize, true);
			if(!empty($resize)) {
				$debug  = json_encode($resize);
		
				$sqResize = array();
				$simpleResize = array();
					
				foreach($resize as $sizeParam) {		
					if($sizeParam == 'original') {
						copy($full_path, CORE_PATH.'vars/files/images/'.$newFileName);
						$result[] =  array('name'=>'/vars/files/images/'.$newFileName, 'size'=>false, 'filename'=>$newFileName);
					} else if(strrchr($sizeParam, 'smart') !== false) {
						$tmp = explode(':',$sizeParam);
						if(!isset($tmp[1])) {
							continue;
						}
						$sizeParam = explode('x', $tmp[1]);
						$result[] = smartresize($full_path, $dir, intval($sizeParam[0]), intval($sizeParam[1]));
					} else if(strrchr($sizeParam, 's') !== false) {
						$sqResize[] = intval(substr($sizeParam, 0, -1));
					} else if(strrchr($sizeParam, 'x') !== false) {
						$sizeParam = explode('x', $sizeParam);
						$result[] = resizeImage($full_path, $dir, intval($sizeParam[0]),intval($sizeParam[1]),false,false);
					} else if(strrchr($sizeParam, 'w') !== false || strrchr($sizeParam, 'h') !== false) {
						$result[] = resizeImage($full_path, $dir, intval(substr($sizeParam,0,-1)),intval(substr($sizeParam,0,-1)),substr($sizeParam,-1));
					} else if(strrchr($sizeParam, 'r') !== false) {
						$result[] = resizeImage($full_path, $dir, intval(substr($sizeParam,0,-1)),intval(substr($sizeParam,0,-1)),'w');
					}  else {
						$result[] = resizeImage($full_path, $dir, intval($sizeParam),intval($sizeParam),false,false);
					}
					//echo $sizeParam;
					///vars/files/nodechat_upload_1937976ec3163aa63f250902ef4eb51f.jpg
					// 150w = 150x150 ratio & baseline WIDTH
					// 150h = 150x150 ration & baseline HEIGHT
					// 100r = 99x100 OR 100x85 - ratio, base line is max
					// 100 = 100x100 NO RATIO
					// 150x280 = 150x280 NO RATION
					// 80s = squaer resize. big photo + preview 80x80
				}
					
				if(count($sqResize)) {
					$sqresult = squaerresize($full_path, $dir, $sqResize);
					if(is_array($sqresult)) {
						$result = array_merge($sqresult,$result);
					}
				}
					
			}
		}
	} else {
		$result[] = array('name'=>$http_path, 'size'=>false, 'filename'=>$file['name']);
	}
	
	if(file_exists($full_path)) {
		unlink($full_path);
	}
	
	return array(
		'success'=>'ok', 
		'resize'=>$useResize, 
		'images'=>$result
	);
}

function shop_import($file) {
    $mimeFormats = array(
        'application/vnd.ms-excel'=>'xls',
        'application/msexcel'=>'xls',
        'application/x-msexcel'=>'xls',
        'application/x-excel'=>'xls',
        'application/x-ms-excel'=>'xls',
        'application/x-dos_ms_excel'=>'xls',
        'application/xls'=>'xls',
        'application/x-xls'=>'xls',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'=>'xlsx',
        'application/csv'=>'csv',
        'text/csv'=>'csv',
        'text/xml'=>'xml',
        'application/x-zip-compressed'=>'zip',
        'application/x-zip'=>'zip'
        
        //'application/vnd.oasis.opendocument.spreadsheet'=>'ods'
    );
    
    include CORE_PATH.'modules/shop/classes/admin.import.php';
    $import = new admin_import();
    $import->init();
    
    $filename = md5(time().microtime()).'.'.pathinfo($file['name'], PATHINFO_EXTENSION);
    $path = CORE_PATH.'vars/import/' . $import->getUploadPath();
    $fullpath = $path.$filename;
    $error = array();
    $info = array();
    $extended = array();
    $filetype = (!empty($mimeFormats[$file['type']]))? $mimeFormats[$file['type']] : false;
    
    if(!file_exists($path)) {
        mkdir($path, 0755, true);
    }
    
    if(!file_exists($path)) {
        $error[] = 'Не удалось создать каталог для импорта ';
    }
    
    if(!move_uploaded_file($file['tmp_name'], $fullpath) ) {
        $error[] = 'Не удалось записать файл';
    }
    
    if(!$filetype) {
        $error[] = 'Формат файла не поддерживается ('.strip_tags($file['type']).')';
    }
    
    if($filetype == 'zip') {
        $unzippath = $path.'unzip.'.substr(md5(time()), 0, 10).'/';
        
        if(file_exists($unzippath)) {
            $unzippath = substr($unzippath, -1) . '.' .substr(md5(time()), 0, 10).'/';
        }
        
        @mkdir($unzippath, 0755, true);
        shell_exec('cd '.$unzippath.' && unzip '.$fullpath);
        $fileinfo = $import->checkUnzipedFolder($unzippath);
        
        if(!$fileinfo) {
            $error[] = 'В загруженном архиве не найдены файлы поддерживаемые системой.';
        } else {
            $title = array();                
            if($fileinfo['1c']) {
                if($fileinfo['offers']) {
                    $document = new commerceml($fileinfo['offers']);
                    if(!$document->loaded) {
                        $fileinfo['offers'] = false;
                    } else {
                        $title[] = '1c offers';
                    }
                }
                
                if($fileinfo['import']) {
                    $document = new commerceml($fileinfo['import']);
                    if(!$document->loaded) {
                        $fileinfo['import'] = false;
                    } else {
                        $title[] = '1c import';
                    }
                }
                
                if(!$fileinfo['import'] && !$fileinfo['offers']) {
                    $error[] = 'Найденные в архиве файлы формата xml не поддерживаются системой.';
                }
            }
            
            if($fileinfo['excel']) {
                $title[] = 'excel file';
            }
            
            if($fileinfo['csv']) {
                $title[] = 'csv file';
            }
            
            if($fileinfo['images']) {
                $title[] = 'images';
            }
            
            if(!empty($error)) {
                @unlink($fullpath);
                
                return array(
                    'error'=>true,
                    'message'=>implode(' | ', $error),
                    'file'=>$filename
                );
            }
            
            $info['type'] = 'rar';
            $info['name'] = pathinfo($fullpath, PATHINFO_BASENAME);
            $info['size'] = filesize($fullpath);
            $info['title'] = $file['name'];
            $info['md5'] = md5_file($fullpath);
            $info['format'] = 'archive';
            $info['id'] = $import->addArchive($info, $fileinfo);
            $info['archive_data'] = array(
              'title'=>'Archive file with: '.implode(', ', $title),
              'type'=>($fileinfo['1c'])? 'CommerceML' : (($fileinfo['excel'])? 'xls' : 'csv')
            );
            
            @unlink($fullpath);
                        
            return array(
                'success'=>'ok',
                'data'=>$info
            );
            
        }
    } elseif($filetype == 'xml') {
        $document = new commerceml($fullpath);
        if(!$document->loaded) {
            $errtmp = $document->getLastError();
            $error[] = 'Формат файла не поддерживается. #' . $errtmp['code']. ' - '.$errtmp['message'];
        } else {
            $extended['type'] = $document->type;
            $extended['version'] = $document->version;
            $filetype = 'CommerceML';
        }
    } 
    
    if(!empty($error)) {
        if(file_exists($fullpath)) {
            @unlink($fullpath);
        }
        
        return array(
            'error'=>true,
            'message'=>implode(' | ', $error),
            'file'=>$filename
        );
    }
    
    $info['type'] = $filetype;
    $info['name'] = $filename;
    $info['size'] = filesize($fullpath);
    $info['title'] = $file['name'];
    $info['id'] = $import->addFile($info);
    $info['format'] = $extended;
    $info['md5'] = md5_file($fullpath);

    return array(
        'success'=>'ok',
        'data'=>$info
    );
}

function shop_import_extended($file) {
    include CORE_PATH.'modules/shop/classes/admin.import.php';
    $import = new admin_import();
    $import->init();
    
    $filename = md5(time().microtime()).'.'.pathinfo($file['name'], PATHINFO_EXTENSION);
    $path = CORE_PATH.'vars/import/' . $import->getUploadPath();
    $fullpath = $path.$filename;
    
    if(!file_exists($path)) {
        mkdir($path, 0755, true);
    }
    
    if(!file_exists($path)) {
        return array(
            'error'=>true,
            'message'=>'Не удалось создать каталог для импорта '
        );
    }
    
    if(!move_uploaded_file($file['tmp_name'], $fullpath) ) {
        return array(
            'error'=>true,
            'message'=>'Не удалось записать файл'
        );
    }
    
    if($file['type'] != 'text/xml') {
        @unlink($fullpath);
        return array(
            'error'=>true,
            'message'=>'Формат файла не поддерживается ('.strip_tags($file['type']).')'
        );
    }
    
    $document = new commerceml($fullpath);
    if(!$document->loaded) {
        $errtmp = $document->getLastError();
        return array(
            'error'=>true,
            'message'=>'Формат файла не поддерживается. #' . $errtmp['code']. ' - '.$errtmp['message']
        );
    } else {
        $extended['type'] = $document->type;
        $extended['version'] = $document->version;
        $filetype = 'CommerceML';
    }
    
    $info['type'] = $filetype;
    $info['name'] = $filename;
    $info['size'] = filesize($fullpath);
    $info['title'] = $file['name'];
    $info['format'] = $extended;
    $info['md5'] = md5_file($fullpath);
    
    
    return array(
        'success'=>'ok',
        'data'=>$info
    );
}

if(isset($_GET['source'])) {
	if(!isset($_GET['application']) || !isset($_FILES)) {
		$controller->redirect('/'.$core->CONFIG['lang']['name'].'/files/', 'Куда полез?');	
	}
	
	if($_GET['application'] == 'jup') {
		$allowedExtensions = array();
		$sizeLimit = 200 * 1024 * 1024;
		
		$uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
		$result = $uploader->handleUpload($core->CONFIG['temp_path']);
		
		
		
		if(isset($result['filename']) && isset($_GET['filehandler'])) {
			if(!function_exists($_GET['filehandler']) || !isset($allowedHandlers[$_GET['filehandler']])){
				$result['handle_error'] = 'Handler not alowed';
			}
			else {
				$result = call_user_func_array($_GET['filehandler'], array($core->CONFIG['temp_path'].$result['filename'], CORE_PATH.'/vars/files/'));
				$result['success']=true;
			}
		}
		
		if(isset($result['filename']) && isset($_GET['postupload'])) {
			if(!isset($allowedHandlers[$_GET['postupload']])) {
				$result['handle_error'] = 'Handler not alowed';
			} else {
				$result = call_user_func_array($_GET['postupload'], array(CORE_PATH.'vars/files/'.$result['filename'], CORE_PATH.'vars/files/',220,220,'h'));
			}
		}
				
		echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
		die();
	} elseif($_GET['application'] == 'ckeditor' || $_GET['application'] == 'simple_upload') {
		$fromck = ($_GET['application'] == 'ckeditor')? true : false;	
		
		if($fromck) {
			$callback = $_GET['CKEditorFuncNum'];
			$file = current($_FILES);
			$newFileName = 'upload_'.md5(time().microtime()).'.'.pathinfo($file['name'], PATHINFO_EXTENSION);
			
			$dir = CORE_PATH.'vars/files/';
			$full_path = $dir.$newFileName;
			$http_path = '/vars/files/'.$newFileName;
			$error = '';
			
			if(move_uploaded_file($file['tmp_name'], $full_path) ) {
					
			} else {
				$error = 'Что-то пошло не так!';
				$http_path = '';
			}
			
			echo "<script type=\"text/javascript\">window.parent.CKEDITOR.tools.callFunction(".$callback.",\"".$http_path."\", \"".$error."\" );</script>";
			die();
		} else {
			$nginxId = (isset($_GET['X-Progress-ID']))? $_GET['X-Progress-ID'] : '';
			$result = array();
			
			if(isset($_GET['process'])) {
			    $proc = $_GET['process'];
			    if(!empty($allowedHandlers[$proc]) && $allowedHandlers[$proc]) {
			        $file = current($_FILES);
			        $result = call_user_func_array($proc, array($file));
			        if(isset($_POST['callback'])) {
			            sendJsonResult($result);
			        } else {
			            echo '[done]';
			            die();
			        }
			    }
			}
			
			if(is_array($_FILES['file']['tmp_name'])) {
				foreach($_FILES['file']['tmp_name'] as $k=>$item) {
					$file = array(
						'name'=>$_FILES['file']['name'][$k],
						'type'=>$_FILES['file']['type'][$k],
						'error'=>$_FILES['file']['error'][$k],
						'size'=>$_FILES['file']['size'][$k],
						'tmp_name'=>$item
					);
					
					$result[] = modernUploaderProcess($file);
				}
				if(isset($_POST['callback'])) {
					$result = array(
						'multiple' => true,
						'nginx_id' => $nginxId,
						'data' => $result
					);
					sendJsonResult($result);
				}
				
			} else {
				$file = current($_FILES);
				$result =  modernUploaderProcess($file);
				if(isset($_POST['callback'])) {
					if(!empty($result['error'])) {
						sendJsonResult($result);
					} else {
						sendJsonResult($result['images']);
					}
				}
			}
		}
		
		die();
	}
	
	if(!isset($_FILES) || !isset($_FILES['Filedata']) || !$_FILES['Filedata']) {
		echo 'error';
		die();
	}
	
	$newFilename = 'uploadedFile_'.md5(time().microtime()).'.tmp';
	
	if(!@move_uploaded_file($_FILES['Filedata']['tmp_name'], $core->CONFIG['temp_path'].$newFilename)) {
		echo 'error';
		die();
	}
	
	echo $newFilename;
	die();
} else {
	if($_GET['type']=='image') {
		$core->lib->widget_load('ajax_image_uploader');
		$controller->redirect('/'.$core->CONFIG['lang']['name'].'/files/', '');
	} elseif($_GET['type']=='file') {
		$core->lib->widget_load('ajax_file_uploader');
		$controller->redirect('/'.$core->CONFIG['lang']['name'].'/files/', '');
	} else {
		$controller->redirect('/'.$core->CONFIG['lang']['name'].'/files/', '');
	}
	
		
}

function sendJsonResult($result) {
    echo "<script type=\"text/javascript\">window.parent.{$_POST['callback']}('{$_POST['session_id']}', JSON.parse('".json_encode($result, JSON_UNESCAPED_UNICODE)."'));</script>";
    die();
}
?>