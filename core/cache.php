<?php
################################################################################
# ---------------------------------------------------------------------------- #
# @Author: Alexey Pshenichniy <inc.mcms@gmail.com> <M-zone Inc.> 2005-2009     #
# @Date: 20.05.2008                                                            #
# @license: GNU GENERAL PUBLIC LICENSE Version 2                               #
# ---------------------------------------------------------------------------- #
# M-cms v4.1 (core build - 4.102)                                              #
################################################################################

class cache
{
	private $uri = '';
	private $server_name = '';
	private $filename = '';
	private $path = '';
	private $cache_exits = 0;
	private $filepath = '';
	private $core;
	
	public $cache_dir = '';
	public $expire = 0;
	public $cached = 0;
	
	function __construct($core)
	{
		$this->core = $core;
		$this->uri = $_SERVER['REQUEST_URI'];
		$this->server_name = $_SERVER['SERVER_NAME'];
		$this->cache_dir = $this->core->CONFIG['cache']['path'];
		
	}
	
	public function init()
	{
		$this->get_path();
		$this->check();

		if($this->cache_exits) {
			$this->render();
		}
	}
	
	public function set($html)
	{	
		if(!$this->path) $this->get_path();
		
		$this->make_dir();	
		$f = fopen($this->filepath, 'w');
				
		fwrite($f, $html);
		fclose($f);
		
		$f = fopen($this->filepath.'.expire', 'w');
				
		fwrite($f, $this->expire);
		fclose($f);
	}
	
	public function check()
	{
		if(is_file($this->filepath)) {
			if(!is_file($this->filepath.'.expire')) {
				$this->cache_exits = 0;
				return false;
			}
			
			$expire = file_get_contents($this->filepath.'.expire');
			
			if($expire == 0) {
				$this->cache_exits = 1;	
				return true;
			}
			
			if(filemtime($this->filepath) + $expire < time()) {
				$this->cache_exits = 0;
				return false;
			}
			
			$this->cache_exits = 1;	
			return true;
		} else {
			$this->cache_exits = 0;	
			return false;
		}
	}
	
	public function get_path()
	{		
		$this->filename = basename($this->uri);
		
		if(strpos($this->filename, '.html') === false) {
			$this->filename = 'index.html';
			$this->path = $this->server_name.$this->uri;
		} else {
			$this->path = $this->server_name.dirname($this->uri).'/';
		}
		$this->filepath = $this->cache_dir.$this->path.$this->filename;
	}

	private function render()
	{
		
		
		$this->core->log->stopTime();
		
		$str = file_get_contents($this->filepath);
		echo $str;
		echo "\n<!-- This page cached //-->\n";
		echo("<!-- Core Time : ".$this->core->log->dumpTime()." | SQL : ".count($this->core->log->queries)."  | Tpl  :  ".count($this->core->log->tpl_fetch)." | Compil Tpl : ".count($this->core->log->tpl_compil)." | Cached Tpl: ".count($this->core->log->cached_tpl)." | MEM: ".$this->core->log->dump_mem()." //-->\n");	
		die();
	}
	
	private function make_dir()
	{	
		if(!file_exists($this->cache_dir)) {	
			mkdir($this->cache_dir, 0777, true);
		}
	
		$dirs_array = explode('/', $this->path);		
		$sub_dir = '';
		foreach($dirs_array as $dir) {
			trim($dir);
			if($dir) {
				$sub_dir .= '/'.$dir;
				if(!file_exists($this->cache_dir.$sub_dir)) {
					mkdir($this->cache_dir.$sub_dir, 0775);
				}
			}	
		}
	}
	
}
?>