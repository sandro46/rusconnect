<?php
############################################################################
#          This controller was created automatically core system           #
#                                                                          #
# ------------------------------------------------------------------------ #
# @Creator module version 1.2598 b                                         #
# @Author: Alexey Pshenichniy                                              #
# ------------------------------------------------------------------------ #
# Alpari CMS v.1 Beta   $17.06.2008                                        #
############################################################################



$controller->id = 7;
$controller->cached = 0;
$controller->cache_param  = array('_site' => 1 , '_lang' => 1 , '_url' => 1, '_uid'=>1 );
$controller->cache_expire = 0;
$controller->init();
$controller->tpl = '';
$controller->cached();


//Default page title for all admin modules
$core->title = 'M-cms Control Panel - '.$core->modules->this['describe'];




if(!intval($_GET['file_id'])) {
	
	$controller->redirect('/'.$core->CONFIG['lang']['name'].'/files/', '���� �� ������.');
}


if($_GET['from'] == 'current_session') {
	if(!isset($_SESSION[':saveFile']) || !isset($_SESSION[':saveFile'][intval($_GET['file_id'])])) {
		$controller->redirect('/'.$core->CONFIG['lang']['name'].'/files/', 'File not found!');
	} else {
		$finfo = $_SESSION[':saveFile'][intval($_GET['file_id'])];

		if(isset($_GET['debug'])) {
			print_r($finfo);
			die();
		}
		
		switch($finfo['type']) {
			case 'TXT':
				header('Content-Type: text/html; charset=utf-8');
				header('Content-Disposition: attachment; filename='.$finfo['name'].'.'.$finfo['header']['extension']);
				header('Content-Type: '.$finfo['header']['mime']);
				
				$outstream = fopen("php://output", 'w');
				$titleSended = false;
				$core->db->query($finfo['sql']);
			
				while($row = mysql_fetch_assoc($core->db->result)) {
					if(!$titleSended) {
						$title = array();
						foreach($row as $_name=>$_data) {
							if(isset($finfo['title'][$_name])) {
								$title[] = iconv("utf-8", "cp1251",$finfo['title'][$_name]);
							}
						}
						
						fwrite($outstream, implode("\t",$title)."\r\n");
						$titleSended = true;
					}
					
					$row = array_map(function($item){						
						return iconv("utf-8", "cp1251",$item);
					},array_intersect_key($row, $finfo['title']));
					
					fwrite($outstream, implode("\t",$row)."\r\n");
				}
				
				fclose($outstream);
			break;
			
			case 'CSV':
				header('Content-Type: text/html; charset=windows-1251');
				header('Content-Disposition: attachment; filename='.$finfo['name'].'.'.$finfo['header']['extension']);
				header('Content-Type: '.$finfo['header']['mime']);
				
				$outstream = fopen("php://output", 'w');
				$titleSended = false;
				$core->db->query($finfo['sql']);
			
				while($row = mysql_fetch_assoc($core->db->result)) {
					if(!$titleSended) {
						$title = array();
						foreach($row as $_name=>$_data) {
							if(isset($finfo['title'][$_name])) {
								$title[] = iconv("utf-8", "cp1251",$finfo['title'][$_name]);
							}
						}
						
						fputcsv($outstream, $title);
						$titleSended = true;
					}
					
					$row = array_map(function($item){						
						return iconv("utf-8", "cp1251",$item);
					},array_intersect_key($row, $finfo['title']));
					
					fputcsv($outstream, $row);
				}
				
				fclose($outstream);
			break;	

			case 'EXCEL':
				header('Content-Type: text/html; charset=windows-1251');
				header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
				header('Cache-Control: no-store, no-cache, must-revalidate');
				header('Cache-Control: post-check=0, pre-check=0', FALSE);
				header('Pragma: no-cache');
				header('Content-transfer-encoding: binary');
				header('Content-Disposition: attachment; filename='.$finfo['name'].'.'.$finfo['header']['extension']);
				header('Content-Type: application/x-unknown');
				
				$outstream = fopen("php://output", 'w');
				$titleSended = false;
				$core->db->query($finfo['sql']);

				fwrite($outstream,  '<html><head><meta http-equiv="content-type" content="text/html; charset=windows-1251"></head><body><table border="0" bordercolor="#DDDDDD" cellpadding="5" cellspacing="0"><thead><tr>');
				fwrite($outstream,  '</tr></thead><tbody>');
					
				while($row = mysql_fetch_assoc($core->db->result)) {
					if(!$titleSended) {
						foreach($row as $_name=>$_data) {
							if(isset($finfo['title'][$_name])) {
								fwrite($outstream,  '<th bgcolor="#DDDDDD"><font color="#666666" face="Arial"><b>'.iconv("utf-8", "windows-1251",$finfo['title'][$_name]).'</b></font></th>');
							}
						}
						
						fwrite($outstream,  '</tr></thead><tbody>');
						$titleSended = true;
					}
						
					$row = array_map(function($item){
						return iconv("utf-8", "windows-1251",$item);
					},array_intersect_key($row, $finfo['title']));

					fwrite($outstream,  '<tr><td border="1" bordercolor="#DDDDDD" bgcolor="#F9F9F9"><font color="#868686" face="Arial">');
					fwrite($outstream,  implode('</font></td><td border="1" bordercolor="#DDDDDD" bgcolor="#F9F9F9"><font color="#868686" face="Arial">', $row));
					fwrite($outstream,  '</font></td></tr>');
				}
			
				fwrite($outstream,  '</tbody></table></body></html>');
				fclose($outstream);
			break;
		
		}
		
		
		
		
		die();
	}
}

$controller->load('files.php');

$files = new files();
$file = $files->get_file_info(intval($_GET['file_id']));

if(!count($file)) {
$controller->redirect('/'.$core->CONFIG['lang']['name'].'/files/', '�������� ���� �� ����������.');	
}



header('Content-type: '.$file['mime_type']);
header('Content-length: '.$file['size']);
header('Content-Disposition: attachment; filename="'.$file['alias'].'"');


readfile(CORE_PATH.$file['filename']);

die();
?>