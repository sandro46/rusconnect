<?php

class updates_creater
{
	
	public $name = '0000';
	public $type = 'system';
	public $tempPath = '/';
	public $savePath = '/';
	
	public $isStoped = false;

	
	private $coreVersion = 0;
	private $mcmsVersion = 0;
	private $fileStructure = array('sql', 'tpl');
	private $avaliableTypes = array('system', 'security', 'modules', 'plugins', 'widgets', 'ui');
	private $infoFilename = 'update.info.ini';
	private $infoSource = '';
	private $structureFilename = 'structure';
	private $changeLogFilename = 'changelog';
	private $changeLogText = '';
	private $errors = array();
	private $updatePakageName = 'mcms-update.tgz';
	
	private $files = array();
	private $templates = array();
	private $sqlInstructions = array();
	
	
		
	
	public function __construct($tempFolder, $saveFolder, $updateName, $updateType)
	{
		
		$this->name = $updateName;
		$this->type = $updateType;
		$this->savePath = $saveFolder;
		
		//$this->tempPath = $tempFolder.$this->type.'/'.$this->name.'/';
		$this->tempPath = $tempFolder.$this->name.'/';
		
		$this->updatePakageName = $this->name.'.'.$this->type.'.'.$this->updatePakageName;
		
		if(!in_array($this->type, $this->avaliableTypes))
		{
			$this->errors[]= "Не верный тип обновления. Доступные типы: ".implode(", ", $this->avaliableTypes);
			
			$this->isStoped = true;
			
			return false;
		}
		
		return true;
	}
	
	public function create()
	{
		$this->greateFolderStructure();
		$this->copyFiles();
		
		
		$this->createInfo();
	}
	
	public function addFiles($filesArray)
	{
		foreach($filesArray as $file)
		{
			$fileInfo = pathinfo($file);
			$basePath = str_replace(CORE_PATH, '', $fileInfo['dirname'].'/');
			$filename = $fileInfo['basename'];
			
			$this->files[] = array($basePath, $fileInfo['dirname'].'/'.$filename, $filename);			
		}
		
		return $this->files;
	}

	public function addFolder($folderName)
	{
		if(substr($folderName, -1) != '/') $folderName .= '/';
				
		foreach(scandir($folderName) as $item)
		{
			if($item != '.' && $item != '..')
			{
				if(is_file($folderName.$item))
				{
					$this->addFiles(array($folderName.$item));
				}
				else 
					{
						$this->addFolder($folderName.$item);
					}
			}
		}		
	}
	
	public function addModule()
	{
		
	}
	
	
	
	
	private function greateFolderStructure()
	{
		foreach ($this->fileStructure as $folder)
		{
			@mkdir($this->tempPath.$folder, 0775, true);
		}
		
		return true;
	}
	
	private function createInfo()
	{
		$this->infoSource .= "[info]\r\n";
		$this->infoSource .= "name = {$this->name}\r\n";
		$this->infoSource .= "core = ".CORE_VERSION."\r\n";
		$this->infoSource .= "version = ".VERSION."\r\n";
		$this->infoSource .= "date = ".time()."\r\n";
		$this->infoSource .= "type = {$this->type}\r\n";
		
		
		$fileLink = fopen($this->tempPath.$this->infoFilename, 'w');
		fwrite($fileLink, $this->infoSource);
		fclose($fileLink);
		
		return $this->infoSource;		
	}
	
	private function copyFiles()
	{
		foreach($this->files as $file)
		{
			if(!file_exists($this->tempPath.$file[0])) @mkdir($this->tempPath.$file[0], 0775, true);
			copy($file[1], ($this->tempPath.$file[0].$file[2]));	
	
		}
	}
	
	/*
	
	$folderTempUpdate = CORE_PATH.'vars/temp/updates/'.$updateType.'/'.$updateNum.'/';
	
	$updateStructure = array(
								$folderTempUpdate.'core/',
								$folderTempUpdate.'lib/',
								$folderTempUpdate.'modules/',
								$folderTempUpdate.'plugins/',
								$folderTempUpdate.'widgets/',
								$folderTempUpdate.'static/',
								$folderTempUpdate.'sql/');
								
	foreach($updateStructure as $folder)
	{
		mkdir($folder, 0775, true);
	}
	*/
	
	
	
	
	
	
}



class updates_install
{
	
	
	
}




?>