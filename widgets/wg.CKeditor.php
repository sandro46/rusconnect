<?php
class CKeditor extends widgets implements iwidget 
{

	public $InstanceName ;
	public $TextareaName;
	public $BasePath ;
	public $RelativePath = '';
	public $Width ;
	public $Height ;
	public $ToolbarSet ;
	public $Value ;
	public $Config ;
	public $itemHtml = '';
	public $initialized = false;
	public $returnOutput = false;
	public $textareaAttributes = array( "rows" => 8, "cols" => 60 );

	private $events = array();
	private $globalEvents = array();
	private $textAreaAtrHtml = '';

	private $version = '3.2';
	private $timestamp = 'A1QD';

	public function main()
	{

		$this->InstanceName	= $instanceName ;
		$this->BasePath		= '/ckeditor/' ;
		$this->Width		= '100%' ;
		$this->Height		= '450' ;
		$this->ToolbarSet	= 'Default' ;
		$this->Value		= '' ;
		$this->RelativePath = '/plugins'.$this->BasePath;
		$this->FullPath 	= CORE_PATH.'plugins'.$this->BasePath;
		$this->timestamp	= time();

		$this->Config		= array() ;
		$this->events		= array();
	}

	public function out()
	{

	}

	public function Create()
	{
		$this->itemHtml = $this->CreateHtml();

		$this->run($this->itemHtml);
	}

	public function RetHTML()
	{
		$this->itemHtml = $this->CreateHtml();
		$this->run($this->itemHtml);
		return $this->itemHtml;
	}

	public function CreateHtml()
	{
		global $core;
		$HtmlValue = htmlspecialchars($this->Value) ;
		$Html = '' ;

		$this->setTextAreaSetting();

		$Html = "<textarea name='{$this->TextareaName}' id='{$this->InstanceName}' {$this->textAreaAtrHtml}>{$HtmlValue}</textarea>\n";

		//if(true) return "<textarea style='width:800px; height:600px' name='{$this->TextareaName}' id='{$this->InstanceName}' {$this->textAreaAtrHtml}>{$HtmlValue}</textarea>\n";

		if(!$this->initialized) $Html .= $this->init();

		$_config 	= $this->configSettings();
		$js 		= $this->returnGlobalEvents();

		if (!empty($_config))
			$js .= "CKEDITOR.replace('".$this->InstanceName."', ".$this->jsEncode($_config).");";
		else
			$js .= "CKEDITOR.replace('".$this->InstanceName."');";

		$Html .= $this->script($js);

		return $Html ;
	}

	
	
	
	private function jsEncode($val)
	{
		if (is_null($val)) return 'null';
		if ($val === false)	return 'false';
		if ($val === true) return 'true';
		
		if (is_scalar($val))
		{
			if (is_float($val))
			{
				// Always use "." for floats.
				$val = str_replace(",", ".", strval($val));
			}

			// Use @@ to not use quotes when outputting string value
			if (strpos($val, '@@') === 0) 
			{
				return substr($val, 2);
			}
			else {
				// All scalars are converted to strings to avoid indeterminism.
				// PHP's "1" and 1 are equal for all PHP operators, but
				// JS's "1" and 1 are not. So if we pass "1" or 1 from the PHP backend,
				// we should get the same result in the JS frontend (string).
				// Character replacements for JSON.
				static $jsonReplaces = array(array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"'),
				array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'));

				$val = str_replace($jsonReplaces[0], $jsonReplaces[1], $val);

				return '"' . $val . '"';
			}
		}
		$isList = true;
		for ($i = 0, reset($val); $i < count($val); $i++, next($val))
		{
			if (key($val) !== $i)
			{
				$isList = false;
				break;
			}
		}
		$result = array();
		if ($isList)
		{
			foreach ($val as $v) $result[] = $this->jsEncode($v);
			return '[ ' . join(', ', $result) . ' ]';
		}
		else
		{
			foreach ($val as $k => $v) $result[] = $this->jsEncode($k).': '.$this->jsEncode($v);
			return '{ ' . join(', ', $result) . ' }';
		}
	}
	
	private function returnGlobalEvents()
	{
		$out = "";
		$returnedEvents = array();

		if (!empty($this->globalEvents))
		{
			foreach ($this->globalEvents as $eventName => $handlers) 
			{
				foreach ($handlers as $handler => $code) 
				{
					if (!isset($returnedEvents[$eventName])) 
					{
						$returnedEvents[$eventName] = array();
					}
					
					if (!in_array($code, $returnedEvents[$eventName])) 
					{
						$out .= ($code ? "\n" : "") . "CKEDITOR.on('". $eventName ."', $code);";
						$returnedEvents[$eventName][] = $code;
					}
				}
			}
		}

		return $out;
	}
	
	private function configSettings()
	{
		$_config = $this->Config;
		$_events = $this->events;


		if (!empty($_events)) 
		{
			foreach($_events as $eventName => $handlers) {
				if (empty($handlers)) 
				{
					continue;
				}
				else if (count($handlers) == 1) {
					$_config['on'][$eventName] = '@@'.$handlers[0];
				}
				else {
					$_config['on'][$eventName] = '@@function (ev){';
					foreach ($handlers as $handler => $code) {
						$_config['on'][$eventName] .= '('.$code.')(ev);';
					}
					$_config['on'][$eventName] .= '}';
				}
			}
		}

		return $_config;
	}
	
	private function init()
	{
		$out = "";
		$extraCode = "";
		
		$args = '?t=' . $this->timestamp;
		$out .= $this->script("window.CKEDITOR_BASEPATH='{$this->RelativePath}';");
		$out .= "<script type=\"text/javascript\" src=\"{$this->RelativePath}ckeditor.js{$args}\"></script>\n";
		$extraCode .= ($extraCode ? "\n" : "") . "CKEDITOR.timestamp = '". $this->timestamp ."';";
		
		$out .= $this->script($extraCode);
		
		$this->initialized = true;
		
		return $out;
	}
	
	private function setTextAreaSetting()
	{
		foreach ($this->textareaAttributes as $key => $val) 
		{
			$this->textAreaAtrHtml .= " " . $key . '="' . str_replace('"', '&quot;', $val) . '"';
		}
	}
	
	private function script($js)
	{
		$out = "<script type=\"text/javascript\">";
		$out .= "//<![CDATA[\n";
		$out .= $js;
		$out .= "\n//]]>";
		$out .= "</script>\n";

		return $out;
	}
		
}

?>
