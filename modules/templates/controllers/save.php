<?php
$controller->id = 24;
$controller->cached = 0;
$controller->init();

$controller->load('templates.php', 'system');

	if(intval($_POST['id_template']))
	{
		$core->history->add('mcms_tmpl',array('id_template',intval($_POST['id_template'])),'edit');
		$message = $core->mess->get(1).' '.addslashes($_POST['name_module']).'/'.addslashes($_POST['name']);
		$core->log->access(1, $message);
	}
	else
		{
			$_POST['id_template'] = $core->MaxId('id_template','mcms_tmpl')+1;
			$message = $core->mess->get(2).' '.addslashes($_POST['name_module']).'/'.addslashes($_POST['name']);
			$core->log->access(1, $message);
		}

$id_template = intval($_POST['id_template']);

admin_templates::save();

$tpl_info = admin_templates::get_tpl_info($id_template);

if($tpl_info)
	{
		$core->tpl->src = $tpl_info['source'];
	
		$core->db->select()->from('mcms_sites')->fields('name')->where('id = '.$tpl_info['id_site']);
		$core->db->execute();
		$core->db->get_rows(1);
	
		$core->tpl->compil($tpl_info['name'], $tpl_info['name_module'], $core->db->rows['name']);

	}



if($_POST['returned'])
{
	$controller->redirect('/'.$core->CONFIG['lang']['name'].'/templates/edit/id/'.$_POST['id_template'].'/theme/'.$tpl_info['theme'].'/', 'All data saved');
}
else
	{
		$controller->redirect('/'.$core->CONFIG['lang']['name'].'/templates/list/filter/name_module/value/'.$tpl_info['name_module'].'/', 'All data saved');
	}


?>