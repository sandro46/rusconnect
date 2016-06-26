<?php
################################################################################
# ---------------------------------------------------------------------------- #
# @Author: Alexey Pshenichniy <inc.mcms@gmail.com> <M-zone Inc.> 2005-2009     #
# @Date: 20.05.2008                                                            #
# @license: GNU GENERAL PUBLIC LICENSE Version 2                               #
# ---------------------------------------------------------------------------- #
# M-cms v4.1 (core build - 4.102)                                              #
################################################################################



class widget extends module {
	public $name = 'root';
		
	public function appendSettings() {
		//$this->tpl->assign($this->name.'Out', $this->out());
	}
	
	public function setting($property, $value) {
		if($property && strlen($property)>0) {
			if(property_exists($this, $property)) {
				$this->$property = $value;
			}
		}
	}

	public function main() {
		
		if(method_exists($this, $this->name.'Action')) {
			$method = $this->name.'Action';
			$variable = $this->name.'Out';
			$result = $this->{$method}();
			$this->tpl->assign($variable,$result);
		}
	}
}

class widgets extends module {
	private $widgetsCollections = array();
	
	private function load($name) {
		if(!$this->lib->widget($name)) {
			$this->widgetsCollections[$name] = false;
		} else {
			$this->widgetsCollections[$name] = new $name();
			$this->widgetsCollections[$name]->name = $name;
			if(method_exists($this->widgetsCollections[$name], 'load')) {
				$this->widgetsCollections[$name]->load();
			}
		}
	}

	public function getWidget($name) {
		return (isset($this->widgetsCollections[$name]))? $this->widgetsCollections[$name] : false;
	} 
	
	public function __call($name, $args = 0) {
		if(isset($this->widgetsCollections[$name])) {
			return $this->widgetsCollections[$name];
		} else {	
			$this->load($name);
			return $this->widgetsCollections[$name];
		}
	}
	
}

?>