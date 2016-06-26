<?php 

class parser extends main_module {
	private $usedLink = array();
	private $pageCache = array();
	private $productsPerPage = 96;
	private $translate = null;
	private $lastRealUrlQuery = '';
	private $resourceMetaInfo = array();
	public $encoding = 'utf-8';
	public $cookie = false;
 	
	
	
	public function makeDate($date, $withoutYear = false) {
		$date = trim($date);
		
		if($withoutYear && strlen($date) < 8) {
			$date .= '/00';
		}
		echo "->$date<-\n";
		if(!preg_match("/([0-9]{1,2})\.([0-9]{1,2})\.([0-9]{2,4})/", $date, $match)) {
			return false;
		}

		return ($withoutYear)? mktime(0,0,0,intval($match[2]),intval($match[1]),1970) : mktime(0,0,0,intval($match[2]),intval($match[1]),intval($match[3]));
	}
		
	public function copyImage($url) {
		$fdest_name = 'fromparser_'.substr(strrchr($url, '/'),1);	
	    
	    if(strpos($fdest_name, '&') !== false) {
            $fdest_name = substr($fdest_name, 0, strpos($fdest_name, '&'));
        }
		  
		$fdest = CORE_PATH.'vars/temp/';
	    
				
		if(!file_exists($fdest.$fdest_name)) {
			$fs = fopen($url, 'r');
			$fd = fopen($fdest.$fdest_name, 'w+');
			
			while($boof = fread($fs, 2048)) {
				
				fwrite($fd, $boof);
			}
				
			fclose($fs);
			fclose($fd);
		} 
				
		return $fdest.$fdest_name;
	}
	
	public function makeImageForProduct($file) {
		$image1 = resizeImage($file, CORE_PATH.'vars/files/', 50,50)['name'];
		$image2 = resizeImage($file, CORE_PATH.'vars/files/', 150,150)['name'];
		$image3 = resizeImage($file, CORE_PATH.'vars/files/', 300,300)['name'];
		$fnameOriginal = md5_file($file).strrchr($file, '.');
		if($image1 === false || $image2 === false || $image3 === false) return false;
		
		$image4 = '/vars/files/images/'.$fnameOriginal;
		
		copy($file, CORE_PATH.'vars/files/images/'.$fnameOriginal);
	
		return array($image1, $image2,$image3,$image4);
	}
	
	public function makeImageForCategory($file, $size = false) {
		$sizeW = !empty($size)? $size[0] : 200;
		$sizeH = !empty($size)? $size[1] : 200;
		
		$fnameOriginal = md5_file($file).strrchr($file, '.');
		$forigin = '/vars/files/images/'.$fnameOriginal;
		copy($file, CORE_PATH.'vars/files/images/'.$fnameOriginal);
		
		$image1 = resizeImage($file, CORE_PATH.'vars/files/', $sizeW, $sizeH)['name'];
		
		return array('/vars/files/images/'.$fnameOriginal, $image1);
	}

	public function getResourceHtml($url, $getMeta = false) {
		if(isset($this->pageCache[md5($url)]) && !$getMeta) return $this->pageCache[md5($url)];
		
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_COOKIEJAR, './cookie.jar');
		curl_setopt($curl, CURLOPT_COOKIEFILE, './cookie.file');
		
		if($this->cookie) {
			curl_setopt($curl, CURLOPT_COOKIE, $this->cookie);
		}
		
		curl_setopt($curl, CURLOPT_URL,$url);
		curl_setopt($curl, CURLOPT_USERAGENT,$_SERVER['HTTP_USER_AGENT']);
		
		$result = curl_exec($curl);
		
		$this->lastRealUrlQuery = curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);
		$this->resourceMetaInfo = false;
		
		if($getMeta) {
			$regRes = preg_match("/\<head\>(.+?)\<\/head\>/s", $result, $header);
			if(!$regRes || empty($header) || empty($header[1])) {
				echo 'HTML ERROR!';
				echo $result;
				die();
			}
			$header = $header[1];
			$meta = array();
			
			if(preg_match_all("/\<meta name=\"(.+?)\" content=\"(.+?)\" \/>/", $result, $metaRaw)){
				foreach($metaRaw[1] as $index=>$name) {
					$meta[$name] = $metaRaw[2][$index];
				}
			}
			
			preg_match("/\<title\>(.+?)\<\/title\>/s", $result, $title);

			$this->resourceMetaInfo['title'] = trim($title[1]);
			$this->resourceMetaInfo['meta'] = $meta;
		}
		
		preg_match("/\<body(.+?)\>(.+?)\<\/body\>/si", $result, $result);
		if(!$result || !count($result)) {
			echo "Error page data! {$url}";
			debug_print_backtrace();
			die();
		}

		$html = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $result[2]);
		$html = preg_replace('/<!--(.*)-->/Uis', '', $html);
		
		
		$html = '<!DOCTYPE html><html><head><meta http-equiv="Content-Language" content="ru" /><meta http-equiv="Content-Type" content="text/html; charset='.$this->encoding.'" /></head><body>'.$html.'</body></html>';
		
		$this->pageCache[md5($url)] = $html;
		
		return $html;
	}
	
	public function getHtmlFromNode($node, $inner = false) {	
		$newdoc = new DOMDocument();
		$newdoc->loadHTML('<meta http-equiv="content-type" content="text/html; charset=utf-8">');
		
		if($inner) {
			$children = $node->childNodes;
			foreach ($children as $child) {
				$newdoc->appendChild($newdoc->importNode($child,true));
			}
		} else {
			$newdoc->appendChild($newdoc->importNode($node,true));
		}
		
		$html = $newdoc->saveHTML();
		$html = trim(substr($html, strpos($html, '</html>')+7));

		return $html;
	}
	
	public function getInnerHTML($Node) {
		$Body = $Node->ownerDocument->documentElement->firstChild->firstChild;
		$Document = new DOMDocument();
		$Document->appendChild($Document->importNode($Body,true));
		return $Document->saveHTML();
	}	
	
	public function clearBadData() {
		$tables = array(
					'tp_product_group', 
					'tp_product', 
					'tp_product_to_feature', 
					'tp_product_feature', 
					'tp_product_img', 
					'tp_vendors', 
					'tp_product_to_feed',
					'tp_product_to_group',
					'tp_product_to_parameters',
					'tp_product_to_ym_feature',
					'tp_product_feature_variants',
					'tp_product_dimensions'
		);
	
		foreach($tables as $item) {
			echo "DELETE FROM {$item} WHERE client_id = {$this->clientId} AND shop_id = {$this->shopId}";
			//$this->db->query($sql);
		}
	
		//$sql = "DELETE FROM mcms_rewrite WHERE id_site = {$this->shopId}";
		//$this->db->query($sql);
	}
	
	public function clearPrice($price) {
	    $price = str_replace(",",'.', preg_replace("([^0-9\.\,])", "", $price));
	    if(substr($price, 0,1) == '.') $price = substr($price, 1);
	    if(substr($price, -1,1) == '.') $price = substr($price, 0, -1);
	    return $price;
	}
	
	public function clearString($text) {
	    $text = trim($text);
	    $text = trim($text, chr(194));
	    $text = trim($text, chr(160));
	    
	    return $text;
	}
}







/////// For image downloader /////////

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
		unlink($source);
		return array('error'=>'File is not image','code'=>$mimeCheckResult);
	}

	$core->images = new images(false, false, false, false, 2);
	$core->images->fname = $sorceFullPath;

	if(!$core->images->open()){
		unlink($sorceFullPath);
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
	$fileSource = $source;

	$core->lib->load('images');
	$destPathLocal = '/vars/files/images/';
	$destPath = CORE_PATH.substr($destPathLocal, 1);
	$sorceFullPath = CORE_PATH.'/vars/files/images/'.$fileSource;
	$destFilename = md5(time().microtime()) . '.' . pathinfo($sorceFullPath, PATHINFO_EXTENSION);

	$mimeCheckResult = images::checkMime($sorceFullPath);
	if($mimeCheckResult !== true) {
		unlink($source);
		return array('error'=>'File is not image','code'=>$mimeCheckResult);
	}

	$core->images = new images(false, false, false, false, 2);
	$core->images->fname = $sorceFullPath;

	if(!$core->images->open()){
		unlink($sorceFullPath);
		return false;
	}

	if($core->images->info['width'] > $core->images->info['height']) {
		$baseline = 'w';
	} else {
		$baseline = 'h';
	}

	$test = $core->images->resize($width, $height, $destPath.$destFilename, true, $baseline, 100, true);


	//$core->images->resize($width, $height, $destPath.$destFilename, true, $baseline, 100);

	print_r($core->images->errors);
	$core->images->close();


	return array('name'=>$destPathLocal.$destFilename, 'size'=>array($width,$height));
}

function resizeImage($source, $destPath=false, $width=0,$height=0,$base='w',$ration=true){
	global $core;

	if(!file_exists($source)) return false;
	
	$core->lib->load('images');

	$destPath = $destPath.'images/';
	$mimeCheckResult = images::checkMime($source);
	if($mimeCheckResult !== true)
	{
	    echo 'File is not image: '.$source.' mime: '.$mimeCheckResult;
		@unlink($source);
		return false;
		//return array('error'=>'File is not image','code'=>$mimeCheckResult);
	}

	$core->images = new images(false, false, false, false, 2);
	$core->images->fname = $source;

	if(!$core->images->open())
	{
		unlink($source);
		return false;
	}

	$newfilename = md5(time().microtime()).'.jpg';

	$core->images->resize($width, $height, $destPath.$newfilename, $ration, $base, 100, 'image/jpeg');
	$core->images->close();

	return array('name'=>'/vars/files/images/'.$newfilename, 'size'=>array($width,$height), 'filename'=>$destPath.$newfilename);
}



?>