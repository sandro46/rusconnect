<?php
$controller->id = 32; 
$controller->cached = 0; 
$controller->init();


$file_name = $_GET['name'];

if(isset($_GET['all']) && $_GET['all'] == 'y')
	{
		$core->log->access(1, 8);
		foreach(scandir($core->tpl->compil_dir) as $file_name)
		{
			if(substr($file_name, strlen($file_name)-(strlen($core->tpl->compil_file_ext))) == $core->tpl->compil_file_ext)
			{
				unlink($core->tpl->compil_dir.'/'.$file_name);		
			}
		}		
		$controller->redirect('/'.$core->CONFIG['lang']['name'].'/templates/browse/', 'All files were removed');	
	}
	else
		{
			$core->log->access(1, $core->mess->get(9).' '.$file_name);
			if(file_exists($core->tpl->compil_dir.'/'.$file_name))
			{
				unlink($core->tpl->compil_dir.'/'.$file_name);
				$controller->redirect('/'.$core->CONFIG['lang']['name'].'/templates/browse/', 'Template has been removed');
			}
			else
				{
					$controller->redirect('/'.$core->CONFIG['lang']['name'].'/templates/browse/', 'Template has not been removed becouse bad filename');
				}	
		}

?>