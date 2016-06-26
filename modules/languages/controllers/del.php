<?php
$controller->id = 4;
$controller->cached = 0;
$controller->init();

$id = intval($_GET['id']);

if(!$id)
	{
	$controller->redirect('/'.$core->CONFIG['lang']['name'].'/'.$core->module_name.'/list/', 'The language have not been removed.<br>Wrong request: Invalid module id.<br>');
	}
	else
		{
		$core->history->add('mcms_language',array('id',$id),'del');
		$core->db->query("UPDATE `mcms_language` SET del = 1 WHERE id = {$id}");
		$controller->redirect('/'.$core->CONFIG['lang']['name'].'/'.$core->module_name.'/list/', 'The language has been removed. All rights cleared the table.');
		}
?>