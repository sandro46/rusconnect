<?php
$controller->id = 24; 
$controller->cached = 0; 
$controller->init();

$core->title = 'M-cms Control Panel - '.$core->modules->this['describe'];

$controller->load('groups.php', 'system');

$gr_id = intval($_GET['id']);

if(!$gr_id)
	{
	$sql = "SELECT MAX(id_group) FROM mcms_group";
	$core->db->query($sql);
	
	$gr_id = $core->db->get_field()+1;
	}

	if(!$gr_id)
	{
	$controller->redirect('/'.$core->CONFIG['lang']['name'].'/groups/add/', 'Db error. Not correct group id genere');
	}
	else
		{
		// удаляем все данные по связи таблиц языков с таблицес связи экшинов и модулей
		$sql = "DELETE FROM mcms_gr_act_lang WHERE mcms_gr_act_lang.id_gr_act IN(SELECT gra.id FROM mcms_group_action as gra WHERE gra.id_group =".$gr_id.")";
		$core->db->query($sql);
		
		// удаляем все данные по связи таблиц языков с таблицес связи экшинов и модулей
		$sql = "DELETE FROM mcms_gr_act_lang WHERE mcms_gr_act_lang.id_gr_act IN(SELECT gra.id FROM mcms_group_action as gra WHERE gra.id_group =".$gr_id.")";
		$core->db->query($sql);
		
		// удаляем все записи из таблицы связей группы с экшином и модулем
		$core->db->delete('mcms_group_action', $gr_id, 'id_group');
		
		// удаляем все записи в таблице групп
		$core->db->delete('mcms_group', $gr_id, 'id_group');
		
		// формируем данные по группе (название группы и язык)
		foreach($_POST['name'] as $lang_id=>$name)
			$data[] = array('id_group'=>$gr_id, 'name'=>addslashes($name), 'lang_id'=>intval($lang_id));
			
		// добавляем запись в базу
		$core->db->autoupdate()->table('mcms_group')->data($data);
		$core->db->execute();
		
		
		unset($data);
	
		if($_POST['actions'])
			{
			foreach($_POST['actions'] as $id_module=>$actions)
				{
				$id_module = intval($id_module);

	
				foreach($actions['actions'] as $action)
					{
					$sql = "INSERT INTO `mcms_group_action` (`id_group`, `id_module`, `id_action`) VALUES (".$gr_id.", ".$id_module.", ".intval($action).")";
					$core->db->query($sql);
				
					$id_gr_act = $core->db->insert_id;
				
					if($_POST['langs'][$id_module][intval($action)] && $id_gr_act)
						{
						foreach($_POST['langs'][$id_module][intval($action)]['lang_id'] as $lang_id)	
							{
							$sql = "INSERT INTO `mcms_gr_act_lang` (`id_gr_act`, `lang_id`) VALUES (".$id_gr_act.", ".intval($lang_id).")";
							$core->db->query($sql);
							}
						}				
					}
				}
			}
		// чистим связи групп с сайтом	
		//$core->db->delete('mcms_group_sites', $gr_id, 'id_group');
			
		//unset($data);
		/*
		foreach($_POST['sites_access'] as $id_site)
		{
		    $data[] = array('id_group'=>$gr_id, 'id_site'=>$id_site);
		}
		
		$core->db->autoupdate()->table('mcms_group_sites')->data($data);
		$core->db->execute();
		*/	
		$controller->redirect('/'.$core->CONFIG['lang']['name'].'/groups/list/', 'All data saved');
		}
	
?>