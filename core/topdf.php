<?php
class topdf
{	
	private $path = '';
	private $lib_path = '';
	private $lib = null;
	
	public function __construct()
	{
		global $core;
		
		$this->lib_path = CORE_PATH.'plugins/mpdf/';	
		$this->path = $core->CONFIG['files_path']; 
	
		require_once($this->lib_path.'mpdf.php');
    	$this->lib = new mPDF('utf-8', 'A4', '8', '', 10, 10, 7, 7, 10, 10);
	}
	
	public function add($html, $folder, $site, $name)
	{
		$randomname = md5('mcms-pdf.'.time().microtime()).'.pdf';
		
		$this->lib->charset_in = 'utf-8';
		$css = '';
		if(preg_match_all("/\<style type=.+?\>(.+?)\<\/style\>/si", $html,$res))
		{
			foreach($res[1] as $item) $css .= $item;
			$html = preg_replace("/\<style type=.+?\>.+?\<\/style\>/si", '', $html);	
		} 
		
		$this->lib->WriteHTML($css, 1);
		$this->lib->list_indent_first_level = 0; 
    	$this->lib->WriteHTML($html);
    	$this->lib->Output($this->path.$randomname);
    	
    	global $core;
    	
    	$file = array('filename'=>'/vars/files/'.$randomname, 'id_site'=>$site, 'folder_id'=>$folder, 'alias'=>$name, 'size'=>filesize($this->path.$randomname), 'mime_type'=>'application/pdf');
    	$core->db->autoupdate()->table('mcms_files')->data(array($file));
    	$core->db->execute();
    	
    	$file_id = $core->db->insert_id;
    	
    	return array('/vars/files/'.$randomname, $file_id);
	}
	
	public function get()
	{
		
	}
	
}