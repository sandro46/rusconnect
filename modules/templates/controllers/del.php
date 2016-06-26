<?php



$controller->id = 4; 
$controller->cached = 0; 
$controller->init();


$controller->load('templates.php', 'system');


if($_GET['groupaction'])
{

	if(!isset($_POST['groupActions'])) 	$controller->redirect('/'.$core->CONFIG['lang']['name'].'/templates/list/', 'Bad request');
	
	$core->log->access(1, 6);
	
	foreach($_POST['groupActions'] as $val)
	{
		$core->history->add('mcms_tmpl',array('id_template',intval($val)),'del');
			
		admin_templates::del(intval($val));
	}
	
	
	$controller->redirect('/'.$core->CONFIG['lang']['name'].'/templates/list/', 'All template has been removed');
}
else 
	{
		$id = intval($_GET['id']);

		if($_GET['history_id']) 
		{
			$history_id = intval($_GET['history_id']);
			if ($core->history->delete($history_id)) 
			{
				$controller->redirect('/'.$core->CONFIG['lang']['name'].'/templates/history/', 'History entry has been deleted');
			} 
			else 
				{
					$controller->redirect('/'.$core->CONFIG['lang']['name'].'/templates/history/', 'History entry has not been deleted');
				}
		}
		if($id)
		{
			$langs_data_array = $core->get_all_langs();
			$core->history->add('mcms_tmpl',array('id_template',$id),'del');
			$core->log->access(1, $core->mess->get(5).' id = '.$id);
			
		
			admin_templates::del($id);
		
			$controller->redirect('/'.$core->CONFIG['lang']['name'].'/templates/list/', 'Template has been removed');
		}
		else
			{
				$controller->redirect('/'.$core->CONFIG['lang']['name'].'/templates/list/', 'Template has not been removed because it is not specified id');
			}
	}

	




?>