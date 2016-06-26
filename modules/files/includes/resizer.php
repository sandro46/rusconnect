<?php 

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
    //$sorceFullPath = CORE_PATH.$fileSource['local'];
    $destFilename = md5(time().microtime()) . '.' . pathinfo($sorceFullPath, PATHINFO_EXTENSION);

    $mimeCheckResult = images::checkMime($source);
    if($mimeCheckResult !== true) {
        //unlink($source);
        return array('error'=>'File is not image', 'from'=>3, 'code'=>$mimeCheckResult,'source'=>$source);
    }

    $core->images = new images(false, false, false, false, 2);
    $core->images->fname = $source;

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

function modernUploaderProcess($file, $options = false) {
    global $core;

    if(!empty($options) && isset($options['alradyuploaded'])) {
        $newFileName = basename($file['name']);
        $dir = CORE_PATH.'vars/files/';
        $full_path = $dir.'images/'.$newFileName;
        $http_path = '/vars/files/'.$newFileName;
    } else {
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
    }

    if(!$options) { 
        $options = array(
            'maxsize'=>(!empty($_POST['maxsize']))? json_decode(urldecode($_POST['maxsize']), true) : false,
            'resize'=>(!empty($_POST['resize']))? json_decode(urldecode($_POST['resize']), true) : false,
        );
    }
    
    if($options['maxsize']) {
        $size = getimagesize($full_path);
        $maxsize = explode('x', $options['maxsize']);
        if($maxsize && count($maxsize) == 2 && $size && count($size) > 2) {
            if($size[0] > $maxsize[0]) return array('error'=>true,'code'=>145, 'size'=>$size[0], 'limit'=>$maxsize[0], 'message'=>'The image is too large. Image width: '.$size[0].' Max. width: '.$maxsize[0]);
            if($size[1] > $maxsize[1]) return array('error'=>true,'code'=>146, 'size'=>$size[1], 'limit'=>$maxsize[1], 'message'=>'The image is too large. Image height: '.$size[1].' Max. height: '.$maxsize[1]);
        }
    }

    $result = array();
    $useResize = false;
    if(!empty($options['resize'])) {
        $resize = $options['resize'];
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
    } else {
        $result[] = array('name'=>$http_path, 'size'=>false, 'filename'=>$file['name']);
    }

    if(file_exists($full_path) && !isset($options['alradyuploaded'])) {
        unlink($full_path);
    }

    return array(
        'success'=>'ok',
        'resize'=>$useResize,
        'images'=>$result
    );
}


?>