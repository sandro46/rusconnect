<?php

class FlashUploader extends widgets implements iwidget
{
	public $file_size_limit = "1000 MB";
	public $file_types = "*.*";
	public $file_types_description = "All Files";
	public $file_upload_limit = 100;
	public $file_queue_limit = 100;
	public $debug = true;
	public $postUploadHoock = false;
	public $title = "Загрузка файлов";
	
	private $html = '';
	
	public function main()
	{
		$this->setSettings();
		
		$this->run($this->html);
	}
	
	
	public function out()
	{		
		$this->setSettings();
		
		return $this->html;
	}
	
	private function setSettings()
	{
		global $core;
		
		$core->tpl->assign('FlashUploaderSetting_file_size_limit', $this->file_size_limit);
		$core->tpl->assign('FlashUploaderSetting_file_types', $this->file_types);
		$core->tpl->assign('FlashUploaderSetting_file_types_description', $this->file_types_description);
		$core->tpl->assign('FlashUploaderSetting_file_upload_limit', $this->file_upload_limit);
		$core->tpl->assign('FlashUploaderSetting_file_queue_limit', $this->file_queue_limit);
		$core->tpl->assign('FlashUploaderSetting_debug', $this->debug);
		$core->tpl->assign('FlashUploaderSetting_title', $this->title);
		$core->tpl->assign('FlashUploaderSetting_session', session_id());
		
		
		$this->html = $core->tpl->get('wg.FlashUploader.html', $core->getAdminModule());
	}
	
}



?>