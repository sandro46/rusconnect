<?php
class Image extends widgets implements iwidget 
{
	
	public $parent = 0;
	public $list = array();
	public $info = array();
	public $tree = false;
	public $level = 1;
	public $MultiFields = array();
	public $clildsFromRoot = false;
	public $id = 0;
	
	private $html = '';
	
	private $recursionLevel = 0;
	private $MaxRecursionLevel = 11;
	
	
	public function main()
	{

	}
	
	public function out()
	{
		
		
		if($this->id)
		{
			$this->getImageInfo();
			return $this->info;
		}
		else
			{
				return $this->list;
			}
	}
	
	
	private function getImagesList()
	{
		return false;
	}
	
	private function getImageInfo()
	{		
		global $core;
		$core->lib->load('images');
		$img = new images(false, false, false, false, $this->site_id);
		$this->info = $img->get_image_info($this->id);
				
		return $this->info;
	}
	
	
}



?>