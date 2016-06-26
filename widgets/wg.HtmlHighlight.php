<?php

class HtmlHighlight extends widgets implements iwidget 
{
	
	public $height = '350px';
	public $path = '/templates/default/javascript/codemirror/';
	public $blockId = '';
	public $objectId = '';
	public $element = '';
	public $counter = 0;
	
	
	public function main()
	{
		global $core;
		
		$html = $core->tpl->get('wg.HtmlHighlight.html', $core->getAdminModule());
		
		$this->run($html);
	}
	
	
	
	public function out()
	{
		$this->generateElement();
		
		
		return get_object_vars($this);
	}
	
	private function generateElement()
	{
		$this->element = '
		<script language="javascript" type="text/javascript">
			var '.$this->objectId.' = new CodeMirror.fromTextArea(\''.$this->blockId.'\', 
				{
					height: "'.$this->height.'",
					parserfile: ["parsexml.js", "parsecss.js", "tokenizejavascript.js", "parsejavascript.js", "parsehtmlmixed.js"],
					stylesheet: ["/templates/default/css/codemirror/xmlcolors.css", "/templates/default/css/codemirror/jscolors.css", "/templates/default/css/codemirror/csscolors.css"],
					path: "/templates/default/javascript/codemirror/"
				});
	
			objectCollection['.$this->counter.'] = new Object();
			objectCollection['.$this->counter.'][\'link\'] =  '.$this->objectId.';
			objectCollection['.$this->counter.'][\'parentId\'] = \''.$this->blockId.'\';
		</script>';
		
		$this->counter++;
	}
	
	
}



?>