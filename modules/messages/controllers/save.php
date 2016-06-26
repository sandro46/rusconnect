<?php
$controller->id = 24;
$controller->cached = 0;
$controller->init();

$controller->load('messages.php', 'system');

$id_message = intval($_GET['id']);


if(!$id_message)
	{
		$id_message = admin_messages::get_next_id();
		$core->log->access(1, 16);
	}
	else 
		{
			$core->log->access(1, $core->mess->get(17).' '.$id_message);
		}

//admin_messages::delete($id_message);

foreach($_POST['name'] as $lang_id=>$name)
	{
	$data[] = array('message_id'=>$id_message, 'name'=>addslashes($name), 'lang_id'=>intval($lang_id), 'text'=>addslashes($_POST['text'][$lang_id]));
	}

$core->history->add('mcms_messages',array('message_id',$id_message),'edit');
$core->db->autoupdate()->table('mcms_messages')->data($data)->primary('message_id','lang_id');
$core->db->execute();

$controller->redirect('/'.$core->CONFIG['lang']['name'].'/messages/add/', 'All data saved.');
?>