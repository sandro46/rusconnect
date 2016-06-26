<?php

    class moduleInstallCreate
	{
		private $patch = '';
		private $filesTree =array();
		private $filesList = array();
		private $filesMap = '';
		private $foldersMap = array();
		private $moduleName = '';
		private $moduleVersion = '';
		private $moduleInfo = array();
        private $savePath = '';
		private $scriptsSourcer = '';
		private $sqlSources = '';
		private $tplSources = '';
		private $installFile = '';
				
		
		
		public function __construct($patch, $savePath) 
		{ 
			$this->patch = $patch;
			$this->savePath = $savePath;
		}
		
		public function create($module)
		{		
			$this->clearObjectData();
			
			$this->moduleName = $module;
			$this->getModuleInfo();
			$this->getTplSources();
			$this->codeTplSource();	
			$this->foldersMap[] = '/'.$this->moduleName;
			$this->filesTree = $this->getFilesList($this->patch.$this->moduleName.'/', $this->patch);
			$this->readSources();
			$this->installFileSource = $this->codeSource($this->scriptsSourcer);
			$this->make();
		}
		
		
		
		
		private function getModuleInfo()
		{
			global $core;
			
			$core->db->select()->from('mcms_modules')->fields('$all')->where("location = '/{$this->moduleName}/'");
			$core->db->execute();
			$core->db->get_rows();			
			$this->moduleInfo['mcms_modules'] = $core->db->rows;
			
			$this->moduleVersion = $this->moduleInfo['mcms_modules'][0]['reg_date'];
		}
		
		private function getTplSources()
		{
			global $core;
			
			$core->db->select()->from('mcms_tmpl')->fields('$all')->where("name_module = '{$this->moduleName}' AND del = 0");
			$core->db->execute();
			$core->db->get_rows();
						
			$this->tplSources = $core->db->rows;			
		}
		
		private function codeTplSource()
		{
			$this->tplSources = serialize($this->tplSources);
			$this->tplSources = $this->codeSource($this->tplSources);			
		}
		
		private function readSources()
		{
			$this->scriptsSourcer = '';
						
			foreach($this->filesList as $file)
			{
				$this->filesMap[] = array('file'=>$file['name'], 'start'=>strlen($this->scriptsSourcer), 'stop'=>strlen($file['source']), 'path'=>$file['path']);
				
				$this->scriptsSourcer .= $file['source'];	
			}
		} 
		
		private function codeSource($src)
		{
			$src = base64_encode($src);
			$src = gzcompress($src, 9);
			
			return $src;
		}
		
		private function encodeSource($src)
		{
			$src = gzuncompress($src);
			$src = base64_decode($src);
			
			return $src;
		}
			
		private function getFilesList($folder, $folderPrefix)
		{				
			foreach(scandir($folder) as $val)
			{
				if($val != '.' && $val != '..')
				{
					if(is_dir($folder.$val)) 
					{ 
						$array[$val] = $this->getFilesList($folder.$val.'/', $folderPrefix);
						$this->foldersMap[] = substr($folder.$val, strlen($folderPrefix)-1);
									
					}
					else
						{
							$array[$val] = array('name'=>$val, 'size'=> filesize($folder.$val));
							$this->filesList[] = array('name'=>$val, 'size'=> filesize($folder.$val), 'source'=>$this->getSource($folder.$val), 'path'=>substr($folder, strlen($folderPrefix)-1));
						}
				}
			}
			
			return $array;
		}
		
		private function getSource($file)
		{
			$fp = fopen($file, 'r');
			$src = fread($fp, filesize($file));
			fclose($fp);
			
			return $src;
			
		}
	
		private function clearObjectData()
		{
			$this->filesTree = array();
			$this->filesList = array();
			$this->filesMap = '';
			$this->foldersMap = array();
			$this->moduleName = '';
			$this->moduleVersion = '';
			$this->moduleInfo = array();
			$this->scriptsSourcer = '';
			$this->sqlSources = '';
			$this->tplSources = '';
			$this->installFile = '';
		}
		
		private function make()
		{
			global $core;
			
			$this->foldersMap = $this->codeSource(serialize($this->foldersMap));
			$this->filesMap = $this->codeSource(serialize($this->filesMap));
			$this->moduleInfo = $this->codeSource(serialize($this->moduleInfo));
						
			$installMap = 'Modulename:'.$this->moduleName.';ModuleVersion:'.$this->moduleVersion.'|';
			
			$packMap = array();
			$stop = 512;
			
			$packMap['src'] = array($stop, strlen($this->installFileSource));
			$stop += $packMap['src'][1];
			$packMap['fld'] = array($stop, strlen($this->foldersMap));
			$stop += $packMap['fld'][1];
			$packMap['fls'] = array($stop, strlen($this->filesMap));
			$stop += $packMap['fls'][1];
			$packMap['tpl'] = array($stop, strlen($this->tplSources));
			$stop += $packMap['tpl'][1];
			$packMap['inf'] = array($stop, strlen($this->moduleInfo));
			
			
			$installMap .= serialize($packMap);
			$installMap = str_pad($installMap, 512, ' ');
			
			
			$fp = fopen($this->savePath.$this->moduleName.'.install.mcms', 'w');
			fwrite($fp, $installMap);
			fwrite($fp, $this->installFileSource);
			fwrite($fp, $this->foldersMap);
			fwrite($fp, $this->filesMap);
			fwrite($fp, $this->tplSources);
			fwrite($fp, $this->moduleInfo);

			fclose($fp);
			
			// $this->installFileSource - files sources
			// $this->foldersMap  - folders map for create folders on server for new module
			// $this->filesMap - map files for crate fileas and map for read source files positions		
			// $this->tplSources - source of template from database (mysql lib format from method mysql::get_rows())
			
		}
	
	}
	
	
    class moduleInstall
    {
	
    	private $patch = '';
    	private $filename = '';
    	private $packMap = '';
    	private $mouleInfo = '';
    	private $packageSource = '';
    	private $installFileSource = '';
    	private $foldersMap = '';
    	private $filesMap = '';
    	private $tplSources = '';
    	private $installPatch = '';
    	private $dbModuleInfo = array();
    	private $accessGroups = false;
    	private $accessSites = false;
    	private $showModeType = false;
    	private $errors = array();
    	
    	
    	public function __construct($readPatch, $installPatch)
    	{
    		$this->patch = $readPatch;
    		$this->installPatch = $installPatch;
    	}
    	
    	public function install($filename)
    	{
    		$this->clear();
    		$this->filename = $filename;
    		$this->getPackMap();
    		
    		if(!$this->checkPackMap())
    		{
    			$this->errors[] = 'Не возможно прочитать карту разбивки пакета. Установка невозможна';
    			return false;
    		}
  
    		$this->readPackage();
    		$this->encodeAll();
    		$this->createTpl();
    		
    		$this->makeFilesMap();
    		
    		unset($this->packageSource, $this->installFileSource);
    	
    		$this->createFolders();
    		$this->writeFiles();
    		$this->writeDBinfo();
    	}
    	
    	public function setAccessGroups($groupsArray)
    	{
    		$this->accessGroups = $groupsArray;
    	}
    	
    	public function setAccessSites($sitesArray)
    	{
    		$this->accessSites = $sitesArray;	
    	}
    	
    	public function setShowMode($showModeId)
    	{
    		$this->showModeType = $showModeId;	
    	}

    	
    	
    	private function clear()
    	{
    		$this->filename = '';
    		$this->packMap = '';
    		$this->mouleInfo = '';
    		$this->packageSource = '';
    		$this->installFileSource = '';
    		$this->foldersMap = '';
    		$this->filesMap = '';
    		$this->tplSources = '';
    		$this->errors = array();
    		$this->dbModuleInfo = array();
    	}
    	
    	private function makeFilesMap()
    	{
    		foreach($this->filesMap as &$file)
    		{
    			$file['src'] = substr($this->installFileSource, $file['start'], $file['stop']);			
    		}
    		
    	}
    	
    	private function encodeAll()
    	{
    		$this->installFileSource = $this->encodeSource($this->installFileSource);
    		$this->foldersMap = $this->encodeSource($this->foldersMap);
    		$this->filesMap = $this->encodeSource($this->filesMap);
    		$this->tplSources = $this->encodeSource($this->tplSources);
    		$this->dbModuleInfo = $this->encodeSource($this->dbModuleInfo);
    		
    		$this->filesMap = unserialize($this->filesMap);
    		$this->foldersMap = unserialize($this->foldersMap);
    		$this->tplSources = unserialize($this->tplSources);	
    		$this->dbModuleInfo = unserialize($this->dbModuleInfo);
    	}
    	
    	private function checkPackMap()
    	{
    		if(!isset($this->packMap['src']) || !isset($this->packMap['src'][0]) || !isset($this->packMap['src'][1])) return false;
    		if(!isset($this->packMap['fld']) || !isset($this->packMap['fld'][0]) || !isset($this->packMap['fld'][1])) return false;
    		if(!isset($this->packMap['fls']) || !isset($this->packMap['fls'][0]) || !isset($this->packMap['fls'][1])) return false;
    		
    		return true;
    	}
    	
    	private function getPackMap()
    	{
    		$fp = fopen($this->patch.$this->filename, 'r');
    		$string = fread($fp, 512);
    		fclose($fp);
    		
    		$string = trim($string);
    		
    		$info = substr($string, 0, strpos($string, "|"));
    		$map = substr($string, strpos($string, "|")+1);
    				
    		$this->mouleInfo['Modulename'] = substr($info, 11, strpos($info, ";")-11);
    		$this->mouleInfo['ModuleVersion'] = substr($info, strpos($info, ";")+15);
    		
    		$this->packMap = unserialize($map);
    		
    		unset($map, $string, $info, $fp);
    	}
    	
    	private function readPackage()
    	{
    		$fp = fopen($this->patch.$this->filename, 'r');
    		
    		while (!feof($fp)) 
    		{
    		  $this->packageSource .= fread($fp, 1024);
    		}
    		
    		fclose($fp);
    		
    		$this->installFileSource = substr($this->packageSource, $this->packMap['src'][0], $this->packMap['src'][1]);
    		$this->foldersMap = substr($this->packageSource, $this->packMap['fld'][0], $this->packMap['fld'][1]);
    		$this->filesMap = substr($this->packageSource, $this->packMap['fls'][0], $this->packMap['fls'][1]);
    		$this->tplSources = substr($this->packageSource, $this->packMap['tpl'][0], $this->packMap['tpl'][1]);
    		$this->dbModuleInfo = substr($this->packageSource, $this->packMap['inf'][0], $this->packMap['inf'][1]);
    	}	
    	
    	private function encodeSource($src)
    	{
    		$src = gzuncompress($src);
    		$src = base64_decode($src);
    		
    		return $src;
    	}
    	
    	private function createFolders()
    	{
    		foreach($this->foldersMap as $folder)
    		{
    			@mkdir($this->installPatch.$folder);			
    		}
    	}
    	
    	private function writeFiles()
    	{
    		foreach($this->filesMap as $file)
    		{
    			$fp = fopen($this->installPatch.$file['path'].$file['file'], 'w');
    			fwrite($fp, $file['src']);
    			fclose($fp);			
    		}
    	}
    	

    	private function getMenuOrderNewModule($type_id)
    	{
    		global $core;
    		
    		$sql = "SELECT MAX(m.menu_order) FROM mcms_modules as m WHERE m.id_module IN(SELECT mt.module_id FROM mcms_modules_type as mt WHERE mt.type_id = {$type_id})";    
			$core->db->query($sql);
		
			$order = $core->db->get_field()+1;
		
			return $order;
    	}
    	
    	private function setDefaultModuleInfo($onlyOneOption = false)
    	{
    		if($onlyOneOption)
    		{
    			if($onlyOneOption == 'g') $this->accessGroups = array(1);
    			if($onlyOneOption == 's') $this->accessSites = array(1);
    		}
    		else 
    			{	
    				$this->accessGroups = array(1);
    				$this->accessSites = array(1);
    				$this->showModeType = 3;
    			}
    	}
    	
    	private function createModuleAccessRules($id_module)
    	{
    		global $core;
    		
    		foreach($this->accessGroups as $gr)
    		{
    			$sql = "INSERT INTO `mcms_group_action` (`id_group`, `id_module`, `id_action`) SELECT {$gr}, {$id_module}, id_action FROM `mcms_action` WHERE `lang_id` = {$core->CONFIG['lang']['default']['id']} AND `del` = 0";
    			$core->db->query($sql);
    			
    			$sql = "INSERT INTO `mcms_gr_act_lang` (`id_gr_act`, `lang_id`) SELECT id, {$core->CONFIG['lang']['default']['id']} FROM `mcms_group_action` WHERE `id_group` = {$gr} AND `id_module` = {$id_module}";
    			$core->db->query($sql);
    		}
    		
    		unset($sql, $gr);
    		
    		foreach($this->accessSites as $st)
    		{
    			$sql = "INSERT INTO `mcms_sites_modules` (`id_site`, `id_module`) VALUES({$st}, {$id_module})";
    			$core->db->query($sql);
    		}
    		
    		$sql = "INSERT INTO `mcms_modules_type` (`module_id`, `type_id`) VALUES({$id_module}, {$this->showModeType})";
    		$core->db->query($sql);
    		
    	}
    	
    	private function createTpl()
    	{
    		$tplData = array();
    		
    		if(!$this->tplSources || !count($this->tplSources)) return false;
    		
   			foreach($this->tplSources as $item)
   			{
   				$idTemplate = $item['id_template'];
   				
   				unset($item['id'], $item['id_template']);
   					
   				$item['del'] = 0;
   				$item['date'] = time();
   				
   				$tplData[$idTemplate][] = $item;
   			}
   		
    		
    		unset($item, $idTemplate);
    		
    		$data = array();
    		$tplId = MaxId('id_template', 'mcms_tmpl')+10;
    		
    		foreach($tplData as $tplItem)
    		{
    			foreach($tplItem as $item)
    			{
    				$item['id_template'] = $tplId;
    				$data[] = $item;
       			}
       			
       			$tplId ++;
    		}
    		
    		global $core;
    		
    		$core->db->autoupdate()->table('mcms_tmpl')->data($data)->primary('name_module', 'name', 'id_site', 'lang_id');
    		$core->db->execute();
     	}
    	
    	private function writeDBinfo()
    	{
    		global $core;
    		    		
    		$newModuleId = $core->MaxId('id_module', 'mcms_modules')+10; 
    		
    		$this->dbModuleInfo = $this->dbModuleInfo['mcms_modules'];
    		
    		if(!count($this->dbModuleInfo))
    		{
    			$this->setDefaultModuleInfo();
    			    			
    			$data = array();
    
    			$data[] = array('id_module'=>$newModuleId, 
    							'name'=>$this->mouleInfo['Modulename'], 
    							'describe'=>$this->mouleInfo['Modulename'],
    							'location'=>'/'.$this->mouleInfo['Modulename'].'/',
    							'reg_date'=>time(),
    							'lang_id'=>$core->CONFIG['lang']['default']['id'],
    							'menu_order'=>$this->getMenuOrderNewModule($this->showModeType),
    							'menu_visible'=>($this->showModeType)? 1:0,
    							'menu_parent_id'=>0,
    							'type'=>0,
    							'meta_title'=>$this->mouleInfo['Modulename'],
    							'meta_description'=>$this->mouleInfo['Modulename'],
    							'meta_keywords'=>$this->mouleInfo['Modulename']);
    			
    			$core->db->autoupdate()->table('mcms_modules')->data($data);
    			$core->db->execute();

    			$this->createModuleAccessRules($newModuleId);
    			return true;
    		} 
        		
    		if(!$this->accessGroups) $this->setDefaultModuleInfo('g');
    		if(!$this->accessSites) $this->setDefaultModuleInfo('s');
    		    		
    		foreach($this->dbModuleInfo as $index=>$item)
    		{
    			unset($this->dbModuleInfo[$index]['id']);
    			
    			$this->dbModuleInfo[$index]['id_module'] = $newModuleId;
    			$this->dbModuleInfo[$index]['reg_date'] = time();
    			$this->dbModuleInfo[$index]['menu_order'] = $this->getMenuOrderNewModule($this->showModeType);
    			$this->dbModuleInfo[$index]['menu_visible'] = ($this->showModeType)? 1:0;		
    		}
    		    		
    		$core->db->autoupdate()->table('mcms_modules')->data($this->dbModuleInfo);
    		$core->db->execute();

    		$this->createModuleAccessRules($newModuleId);
    	}
    	
    	
    }	
	
	
	class moduleControll
	{
	    
	    
	    public $list = array();
	    private $packagesPath = '';
	    
	    
	    
	    public function __construct($packDir)
	    {
	       $this->packagesPath = $packDir; 
	    }
	    
	    public function getPackageList()
	    {
	        foreach(scandir($this->packagesPath) as $file)
	        {
	            if($this->checkFile($this->packagesPath.$file)) $this->setModulePack($file);
	        }
	    }
	    
	    public function checkInstall($filename)
	    {
	    	 $res = $this->checkInstalled(substr($filename, 0, -13));
	    	 
	    	 if($res && count($res)) return true;
	    	 
	    	 return false;
	    }

	    private function checkFile($file)
	    {
	        if(!is_file($file)) return false;
	        if(substr($file, -5) == '.mcms') return true;
	        
	        return false;	        
	    }
	    
	    private function setModulePack($filename)
	    {
	        $info = array();
	        
	        $info['filename'] = $filename;
	        $info['size'] = get_formated_file_size(filesize($this->packagesPath.$filename));
	        $info['modulename'] = substr($filename, 0, -13);
	        
	        $installed = $this->checkInstalled($info['modulename']);
	        
	        $info['installed'] = ($installed)? true : false;
	        $info['moduleInfo'] = $installed;
	        $info['packInfo'] = $this->readPackageInfo($filename);
	        
	        $this->list[] = $info;
	    }
	    
	    private function readPackageInfo($filename)
	    {
	        $fp = fopen($this->packagesPath.$filename, 'r');
    		$string = fread($fp, 512);
    		fclose($fp);
    		
    		$string = trim($string);
    		
    		$info = substr($string, 0, strpos($string, "|"));

    		$mouleInfo = array();
    		
    		$mouleInfo['Modulename'] = substr($info, 11, strpos($info, ";")-11);
    		$mouleInfo['ModuleVersion'] = substr($info, strpos($info, ";")+15);
    		
    		return $mouleInfo;
	    }
	    
	    private function checkInstalled($moduleName)
	    {
	        global $core;
	        
	        $core->db->select()->from('mcms_modules')->fields('reg_date', 'id_module')->where("location = '/{$moduleName}/'")->limit(1);
	        $core->db->execute();
	        $core->db->get_rows(1);


	        if(!count($core->db->rows)) return false;
	        
	        return $core->db->rows;
	    }
	    
	}
	

	
	
?>	