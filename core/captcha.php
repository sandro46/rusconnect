<?php
################################################################################
# ---------------------------------------------------------------------------- #
# @Author: Alexey Pshenichniy <inc.mcms@gmail.com> <M-zone Inc.> 2005-2011     #
# @Date: 01.02.2011                                                            #
# @license: GNU GENERAL PUBLIC LICENSE Version 2                               #
# ---------------------------------------------------------------------------- #
# M-cms v5.1 (core build - 5.143)                                              #
################################################################################

class  captcha
{
	
	public $hash = '';
	public $secureCode = '';
	
	
	private $width = 0;
	private $height = 0;
	private $image = false;
	private $fontSize = 0;
	
	/**
	 * Свойство указывающее на минимальное кол-во символов в строке каптчи
	 *
	 * @var integer
	 * @access private
	 */
	public $lenghtSecure = 0;
	
	/**
	 * Набор доступных символов из которых будет составлятся проверочнаая строка
	 *
	 * @var string
	 * @access private
	 */
	public $possibleString = '23456789bcdfghjkmnpqrstvwxyz';
	
	/**
	 * Минимальное кол-во символов в проверочной строке
	 *
	 * @var integer
	 * @access private
	 */
	public $minLenCode = 4;
	
	/**
	 * Цвет шума на картинке
	 *
	 * @var [R (int),G (int),B (int)] array
	 * @access private
	 */
	public $noizeColor = array(100, 120, 180);
	
	/**
	 * Цвет линии
	 *
	 * @var [R (int),G (int),B (int)] array
	 * @access private
	 */
	public $lineColor2 = array(202,168,170); //#caa8aa
	
	/**
	 * Цвет линии
	 *
	 * @var [R (int),G (int),B (int)] array
	 * @access private
	 */
	public $lineColor3 = array(171,183,248);
	
	/**
	 * Цвет шрифта
	 *
	 * @var [R (int),G (int),B (int)] array
	 * @access private
	 */
	public $fontColor  = array(array(112,118,152), array(171,183,248), array(202,168,170), array(100, 120, 180), array(0,255,0), array(0,0,255), array(255,0,0));
	
	/**
	 * Цвет фона
	 *
	 * @var [R (int),G (int),B (int)] array
	 * @access private
	 */
	public $background = array(array(255,255,238), array(255,240,245),array(232,255,226), array(226,237,255));
	
	/**
	 * Файл шрифта
	 *
	 * @var string $filename
	 * @access private
	 */
	public $fontFile = 'monofont.ttf';
	
	/**
	 * Степень паршивости :)
	 * 1 - неразборчиво
	 * 2 - слишком просто
	 *
	 * @var float
	 */
	public $dotVolume = 4;
	
	
	public function __construct($width = 160, $height = 40, $lenghtCode = 5)
	{
		$this->width = $width;
		$this->height = $height;
		$this->lenghtSecure = $lenghtCode;
		
		if($this->lenghtSecure < $this->minLenCode) $this->lenghtSecure = $this->minLenCode;
		
		$this->secureCode = '';
		
		for($i=1; $i <= $lenghtCode; $i++)
			$this->secureCode .= $this->possibleString{mt_rand(0, strlen($this->possibleString)-1)};
 
		$this->hash = md5($this->secureCode); 
		
		$this->fontSize = $this->height * 0.80;
		
		$_SESSION['secureHash']=$this->hash;
		$_SESSION['secureCode']=$this->secureCode;
	}
	
	
	public function generateImage()
	{
		$this->image = ImageCreate($this->width, $this->height);
		
		imagefill($this->image, 0, 0, $this->getColorAllocate($this->background[mt_rand(0,3)]));
		 
		// рисуем линии трех цветов
        for($i=0; $i<round(($this->width*$this->height)/600); $i++) 
        {
        	imageline($this->image, mt_rand(0,$this->width), mt_rand(0,$this->height), mt_rand(0,$this->width), mt_rand(0,$this->height), $this->getColorAllocate($this->lineColor3));
        	imageline($this->image, mt_rand(0,$this->width), mt_rand(0,$this->height), mt_rand(0,$this->width), mt_rand(0,$this->height), $this->getColorAllocate($this->lineColor2));
        }
		
		// создаем текстовое поле
        $textbox = imagettfbbox($this->fontSize, 0, $this->fontFile, $this->secureCode);
        
        $x = ($this->width - $textbox[4])/2;      
      	$y = ($this->height - $textbox[5])/2;
      	
      	//imagettftext($this->image, $this->fontSize, 0, $x, $y, $this->getColorAllocate($this->fontColor[mt_rand(0, 3)]), $this->fontFile , $this->secureCode);      	      		
      	
       	$xx = 1;
      	for($i = 0; $i != strlen($this->secureCode); $i ++)
      	{
      		$yy = $y+(mt_rand(-10,10));
      		$color = $this->getColorAllocate($this->fontColor[mt_rand(0, 6)]);
      		$symbol = $this->secureCode{$i};
      		
			imagettftext($this->image, $this->fontSize, 0, $xx, $yy, $color, $this->fontFile , $symbol);      	      		
      		$xx += $this->fontSize/1.7+mt_rand(-0.2,0.2);
      	}
		
		// ставим точки :)
		for($i=0; $i<round(($this->width*$this->height)/$this->dotVolume); $i++)
         	imagefilledellipse($this->image, mt_rand(0,$this->width), mt_rand(0,$this->height), 1, 1, $this->getColorAllocate($this->noizeColor));

      	return $this;
	}
	
	public function getImage()
	{
		header('Content-Type: image/jpeg');
      	imagejpeg($this->image);     	
	}
		
	private function getColorAllocate(array $collorArray)
	{
		return imagecolorallocate($this->image, $collorArray[0], $collorArray[1], $collorArray[2]);
	}

	function __destruct()
	{
	  if($this->image) imagedestroy($this->image);
	}
	
}


?>