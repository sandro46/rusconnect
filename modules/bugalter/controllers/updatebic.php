<?php

ini_set('error_reporting', E_ALL);
ini_set('display_errors',1);


class updateProcess
{
	
	public $file = '';
	public $baseFile = '';
	public $tmp = '';
	public $url = '';
	public $baseRawData = '';
	public $database = array();
	public $table = 'sv_banks';
	public $sql = '';
	public $fields = array();
	public $updateList = array();
	
	
	public function __construct($url, $tmpPath)
	{
		$urlObj = pathinfo($url);
			
		$this->tmp = $tmpPath;
		$this->url = $url;
		$this->file = $urlObj['basename'];
		$this->baseFile = $urlObj['filename'].'.DBF';
		$this->fields  =  array('NNP'=>'city',
								'ADR'=>'address',
								'NAMEP'=>'name',
								'NEWNUM'=>'bik',
								'OKPO'=>'okpo',
								'KSNP'=>'kor_num',
								'TELEF'=>'telefon');
	}
	
	public function insert()
	{
		$this->baseFile = 'BNKSEEK.DBF';
		//$this->baseFile = 'BNKDEL.DBF';
		$this->getBaseData();
	
		$data = array();
		foreach($this->baseRawData as $item)
		{
			$_data = null;
			foreach($this->fields as $old=>$new)
				if(isset($item[$old])) $_data[$new] = $item[$old];
				
			$data[] = $_data;
		}
		
		global $core;
		
		$core->db->autoupdate()->table($this->table)->data($data);
		$core->db->execute();
	}
	
	public function update()
	{
		$this->getLastUpdates();
		
		foreach($this->updateList as $item)
		{
			$tmp = new updateProcess($item['url'], $this->tmp);
			$tmp->updateOne();
			$this->sql .= $tmp->sql;
		}
		
		
	}
	
	public function save()
	{
		global $core;
		//foreach(explode(";\n", $this->sql) as $sql) if(strlen($sql)>2) $core->db->query($sql); 
	}
	
	public function updateOne()
	{
		$this->getFile();
		$this->extract();
		$this->getBaseData();
		$this->getUpdateData();
		$this->getUpdateSql();
	}
	
	private function getUpdateSql()
	{
		foreach($this->database as $num=>$item)
		{
			$_sql = "UPDATE {$this->table} SET ";
			foreach($item as $field=>$value) $_sql .= " `".$field."` = '{$value}',";
			$_sql = substr($_sql, 0, -1).' WHERE `bik` = \''.$num.'\';';
			$this->sql .= $_sql."\n";
		}	
	}
		
	private function getUpdateData()
	{
		$this->database = array();
	
		foreach($this->baseRawData as $item)
		{
			$item['NUM_'] = trim($item['NUM_']);
			$item['FIELD_'] = trim($item['FIELD_']);
			$item['NEW_'] = addslashes(trim(iconv('cp866', 'utf-8',$item['NEW_'])));
		
			if(isset($item['NUM_']) && strlen($item['NUM_']) && isset($item['FIELD_']) && strlen($item['FIELD_']))
			{
				if(isset($this->fields[$item['FIELD_']]))
				$this->database[$item['NUM_']][$this->fields[$item['FIELD_']]] = $item['NEW_'];
			}
		}		
	}
	
	public function getBaseData()
	{
		$db = dbase_open($this->tmp.$this->baseFile, 0) or die('Database error. Cant open base file');
		$this->baseRawData = array();
		if ($db) {
		  $record_numbers = dbase_numrecords($db);
		  for ($i = 1; $i <= $record_numbers; $i++) {
		      $this->baseRawData[$i] = dbase_get_record_with_names($db, $i);
		  }
		}
	}
	
	private function getFile()
	{
		$s = file_get_contents($this->url);
		$fp = fopen($this->tmp.$this->file, 'w+');
		fwrite($fp, $s);
		fclose($fp);
	}
	
	private function extract()
	{
		if(!file_exists($this->tmp.$this->file)) die('Extract error. Source file not found');
		system('arj e '.$this->tmp.$this->file.' '.$this->tmp." > /dev/null");
		if(!file_exists($this->tmp.$this->baseFile)) die('Extract error. Destination file not found');
	}
	
	private function getLastUpdates()
	{
		$res = file_get_contents($this->url);
		preg_match_all("/\<tr  bgcolor=(.+?)>(.+?)\<\/tr\>/si", $res, $match);		
		$cnt = 0;
		if(isset($match[2]) && count($match[2])>1)
		{
			foreach($match[2] as $item)
			{
				preg_match("/\<td\>\<a href=\"..\/bic\/(.+?)\"\>/si", $item, $result);
				if(!isset($result[1])) continue;
				if(substr($result[1], 0, 5) == 'SPBIC') break;
				$this->updateList[$cnt] = array('file'=>$result[1], 'url'=>'http://www.cbr.bankir.ru/bic/'.$result[1]);
				unset($result);
				preg_match("/([0-9]{2}\/[0-9]{2}\/[0-9]{4})/si", $item, $result);
				if(!isset($result[1])) continue;
				$this->updateList[$cnt]['date_source'] = $result[1];
				$this->updateList[$cnt]['timestamp']=$this->gettimestampfromdateslashes($result[1]);
				$cnt++;
			}
		}
		
		reset($this->updateList);
		krsort($this->updateList);
	}
	
	private function gettimestampfromdateslashes($date)
	{
		$date = explode('/', $date);
		return mktime(0,0,1,$date[1],$date[0],$date[2]);	
	}

}



/**
 * Обновление всей базы с последней актуальной
 */
$update = new updateProcess('http://www.cbr.ru/mcirabis/PluginInterface/GetBicCatalog.aspx', CORE_PATH.'bik/');
$update->baseFile = 'BNKSEEK.DBF';
$update->getBaseData();
print_r($update);
die();

//$update = new updateProcess('http://www.cbr.bankir.ru/bik_update.shtml', '/var/www/m-cms.loc/vars/temp/');
//$update->update();
//$update->save();
//print_r($core->log->sql());
//die();




/**
 * Загрузка новой версии базы с нуля
 */

//$update = new updateProcess($main['url'], '/var/www/m-cms.loc/vars/temp/');
//$update->insert();
