<?php
$controller->id = 4; 
$controller->cached = 0; 
$controller->init();

$controller->load('users.php', 'system');

$id = intval($_GET['id']);

if(!$id) $controller->redirect('/'.$core->CONFIG['lang']['name'].'/templates/list/', 'User has not been removed because it is not specified id');
if($id == 1) $controller->redirect('/'.$core->CONFIG['lang']['name'].'/users/list/', '–едактирование этого пользовател€ запрещено!');



admin_users::del($id);
	
$controller->redirect('/'.$core->CONFIG['lang']['name'].'/users/list/', 'ѕользовтель удален из системы');

	




?>