<?php
//
$controller->id = 36; 

// ���������� ��������� ������ ����� �����������?
$controller->cached = 0; 

// ������������� �����������. ����� ����� ����������� ��������, � ��� ���������� �������� ���� ������� ������������ � ����� ����������� � �������� �� ����������� ������ �����������
$controller->init();

// ������ �����������
$controller->tpl = 'history.html';

$core->title = 'M-cms Control Panel - '.$core->modules->this['describe'];

$controller->load('templates.php', 'system');

$class = new admin_templates;

if (isset($_GET['id'])) 
	{
		$controller->tpl = 'history_by_id.html';
		
		$id_template = $core->history->getIdFromHistory(intval($_GET['id']), 'id_template');
		
		$sql = "SELECT id_history FROM `mcms_history_data` WHERE value = 'id_template' AND data = {$id_template} AND lang_id = {$core->CONFIG['lang']['id']}";
		$core->db->query($sql);
		$core->db->get_rows();
		foreach ($core->db->rows as $row) 
			{
			$ids[] = $row['id_history'];
			}
		$ids = array_reverse($ids);
		$list = $core->history->getSpecificList($ids,'templates',array('by' => 'date', 'type' => 'desc'));
		$core->tpl->assign('array', $list);
	} 
	else 
		{
			$controller->tpl = 'history.html';
		
		
			(isset($_GET['order'])) ? $order['by'] = addslashes($_GET['order']) : $order['by'] = 'date';
			(isset($_GET['type'])) ? $order['type'] = addslashes($_GET['type']) : $order['type'] = 'DESC';
		
			$list = $core->history->getList('templates',array('name' => 'tpl_name', 'name_module' => 'tpl_module_name'),$order);
		
			$core->tpl->assign('array',$list);
		}	

		
		
		
?>