<?php
$controller->id = 6; 
$controller->cached = 0; 
$controller->init();
$controller->tpl = 'add_form.html';

$core->title = 'M-cms Control Panel - '.$core->modules->this['describe'];


$controller->load('users.php');

$id = intval($_GET['id']);


if(!$id) $controller->redirect('/'.$core->CONFIG['lang']['name'].'/users/list/', 'Bad request!');
if($id == 1)
{
	$controller->redirect('/'.$core->CONFIG['lang']['name'].'/users/list/', iconv('utf-8', 'cp1251', 'Вы не можете редактировать данные этого пользователя.!'));
}

$core->tpl->assign('user', admin_users::get_user_info($id));
$core->tpl->assign('u_sites', admin_users::get_all_sites());
$core->tpl->assign('grouplist', admin_users::get_groups_list_ussign_uid($id));


?>