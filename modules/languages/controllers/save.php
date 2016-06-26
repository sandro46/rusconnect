<?php
$controller->id = 24;
$controller->cached = 0;
$controller->init();

$id = intval($_GET['id']);

if($id)
	{
	$data[] = array('id'=>$id, 'name'=>addslashes($_POST['name']), 'descr'=>$_POST['descr'], 'charset'=>addslashes($_POST['charset']), 'encoding'=>addslashes($_POST['encoding']), 'visible_in_menu'=>intval($_POST['visible_in_menu']), 'rewrite'=>addslashes($_POST['name']));
	$core->history->add('mcms_language',array('id',$id),'edit');
	}
	else
		{
		$data[] = array('name'=>addslashes($_POST['name']), 'descr'=>$_POST['descr'], 'charset'=>addslashes($_POST['charset']), 'encoding'=>addslashes($_POST['encoding']), 'visible_in_menu'=>intval($_POST['visible_in_menu']), 'rewrite'=>addslashes($_POST['name']));
		}

$core->db->autoupdate()->table('mcms_language')->data($data)->primary('id');
$core->db->execute();

$controller->redirect('/'.$core->CONFIG['lang']['name'].'/users/list/', 'All changes have been saved.');

?>