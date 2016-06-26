<?php
################################################################################
# ---------------------------------------------------------------------------- #
# @Author: Alexey Pshenichniy <inc.mcms@gmail.com> <M-zone Inc.> 2005-2009     #
# @Date: 20.05.2008                                                            #
# @license: GNU GENERAL PUBLIC LICENSE Version 2                               #
# ---------------------------------------------------------------------------- #
# M-cms v4.1 (core build - 4.102)                                              #
################################################################################



class debug_class
{
	public $loged = true;
	public $call_functions = array();
	private $reg_functions = array();
	private $full_ignore = false;
	
	function __construct($loged = true, $fullIgnore = false)
	{
		$this->loged = $loged;
		$this->full_ignore = $fullIgnore;
	}
	
	public function __call($name, $arguments)
	{
		if($this->full_ignore) return false;
		if(in_array($name, $this->reg_functions)) {
			if($this->reg_functions[$name]['eval']) eval($this->reg_functions[$name]['eval']);
			$this->call_functions[] = array('registred'=>1, 'name'=>$this->reg_functions[$name]);
			return ($this->reg_functions[$name]['return']) ? $this->reg_functions[$name]['return'] : false;
		} else {
			$this->call_functions[] = array('registred'=>0, 'name'=>$name);
			return false;
		}
	}
	
	public function register_func($name, $return=0, $eval=0)
	{
		if($this->loged) {
			$this->reg_functions[$name]['eval'] = $eval;
			$this->reg_functions[$name]['return'] = $return;
		}
	}
	
}
?>