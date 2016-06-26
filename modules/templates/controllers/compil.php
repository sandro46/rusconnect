<?php
$controller->id = 30; 
$controller->cached = 0; 
$controller->init();


$core->title = 'M-cms Control Panel - '.$core->modules->this['describe'];


$controller->load('templates.php', 'system');

$id = intval($_GET['id']);

$core->log->access(1, $core->mess->get(4).' id - '.$id);

if($id)
	{
		$tpl_info = admin_templates::get_tpl_info($id);
	
		if(!$tpl_info)
		{
			$controller->redirect('/'.$core->CONFIG['lang']['name'].'/templates/list/', 'Template not been compiled because you entered the wrong template id');
		}
		else
			{
				$core->tpl->src = $tpl_info['source'];
		
				$core->db->select()->from('mcms_sites')->fields('name')->where('id = '.$tpl_info['id_site']);	
				$core->db->execute();
				$core->db->get_rows(1);	
		
				$core->tpl->compil($tpl_info['name'], $tpl_info['name_module'], $core->db->rows['name']);	
			}
		$controller->redirect('/'.$core->CONFIG['lang']['name'].'/templates/list/', 'Template has been compiled');
	}
	else
		{
			$controller->redirect('/'.$core->CONFIG['lang']['name'].'/templates/list/', 'Template not been compiled because it is not specified id');
		}
?>