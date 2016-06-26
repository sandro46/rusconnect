<?php
$controller->id = 4; 
$controller->cached = 0; 
$controller->init();

$core->title = 'M-cms Control Panel - '.$core->modules->this['describe'];

$gr_id = intval($_GET['id']);

if(!$gr_id)
	{
	$controller->redirect('/'.$core->CONFIG['lang']['name'].'/groups/list/', 'Group has not been removed because it is not specified id');
	}
	else
		{
		// удаляем все данные по связи таблиц языков с таблицес связи экшинов и модулей
		$sql = "DELETE FROM mcms_gr_act_lang WHERE mcms_gr_act_lang.id_gr_act IN(SELECT gra.id FROM mcms_group_action as gra WHERE gra.id_group =".$gr_id.")";
		$core->db->query($sql);
	
		// удаляем все записи из таблицы связей группы с экшином и модулем
		$core->db->delete('mcms_group_action', $gr_id, 'id_group');
	
		// удаляем все записи в таблице групп
		$core->db->delete('mcms_group', $gr_id, 'id_group');
		
		// удаляем все записи в таблице связей группы с пользователями
		$core->db->delete('mcms_user_group', $gr_id, 'id_group');
		
		$controller->redirect('/'.$core->CONFIG['lang']['name'].'/groups/list/', 'Group has been removed.');
		}
?>