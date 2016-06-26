<?php
$controller->id = 24;
$controller->cached = 0;
$controller->init();

$core->title = 'M-cms Control Panel - '.$core->modules->this['describe'];

$controller->load('actions.php', 'system');

$id_action = intval($_GET['id']);


if(!$id_action)
	{
	$id_action = admin_actions::get_next_id();
//	admin_actions::delete_all($id_action);
	}
	else
		{
		$core->history->add('mcms_action',array('id_action',$id_action),'edit');
//		$core->db->delete('mcms_action', $id_action, 'id_action');
		}


foreach($_POST['name'] as $lang_id=>$name)
	{
	$data[] = array('id_action'=>$id_action, 'name'=>addslashes($name), 'lang_id'=>intval($lang_id));
	}
$core->db->autoupdate()->table('mcms_action')->data($data)->primary('id_action','lang_id');
$core->db->execute();

$controller->redirect('/'.$core->CONFIG['lang']['name'].'/actions/list/', 'All data saved.');

?>