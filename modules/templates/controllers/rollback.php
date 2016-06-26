<?php
$controller->id = 37; 
$controller->cached = 0; 
$controller->init();

$controller->load('templates.php', 'system');

$class = new admin_templates;

if (isset($_GET['preview_id'])) 
	{
		global $core;
		
		$controller->tpl = 'rollback_preview.html';
		$preview_id = intval($_GET['preview_id']);
		$sql = "SELECT * FROM mcms_history_data WHERE id_history = {$preview_id}";
		$core->db->query($sql);
		$core->db->get_rows();
		$history = array();
		
		foreach ($core->db->rows as $row) 
			{
			$history[$row['lang_id']-1][$row['value']] = stripslashes($row['data']); 
			}
			
		$sql = "SELECT * FROM mcms_tmpl WHERE id_template = {$history['0']['id_template']}";
		$core->db->query($sql);
		$core->db->get_rows();
		$current = array();
		
		foreach ($core->db->rows as $row) 
			{
			$current[$row['lang_id']-1] = $row;
			}
			
		$core->tpl->assign('langs',$core->get_all_langs());
		$core->tpl->assign('history',$history);
		$core->tpl->assign('current',$current);
	} 
	else 
		{ 
		if ($class->rollback()) 
			{
			$controller->redirect('/'.$core->CONFIG['lang']['name'].'/templates/history/', 'All data saved');
			} 
			else 
				{
				$controller->redirect('/'.$core->CONFIG['lang']['name'].'/templates/history/', 'Some error occured');
				}
		}

?>
