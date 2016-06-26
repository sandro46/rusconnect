<?php
$controller->id = 24;
$controller->cached = 0;
$controller->init();

$controller->load('users.php', 'system');

	if(intval($_POST['id_user']))
	{		
		$id = intval($_POST['id_user']);
	
		$email = trim(addslashes($_POST['email']));
		$login = trim(addslashes($_POST['login']));
		$memo = trim(addslashes($_POST['memo']));
	
		if($_POST['password']) $password =  md5((md5(trim($_POST['password'])).$core->CONFIG['security']['user']['pass_salt']));
	
		$user['id_user'] = $id;
		$user['name'] = addslashes(trim($_POST['name']));
		$user['email'] = $email;
		$user['login'] = $login;
		$user['memo'] = $memo;
		$user['default_site_id'] = (intval($_POST['default_site_id']))? intval($_POST['default_site_id']) : 2;
	
		if($password) $user['password'] = $password;
	
		$data[] = $user;
				
		//$core->history->add('mcms_user',array('id_user',$id),'edit');
	}
	else
		{
			$id = admin_users::generate_id();
	
			if($_POST['password']) $password =  md5((md5(trim($_POST['password'])).$core->CONFIG['security']['user']['pass_salt']));
	
			$user['id_user'] = $id;
			$user['name'] = addslashes(trim($_POST['name']));
			$user['email'] = trim(addslashes($_POST['email']));
			$user['login'] = trim(addslashes($_POST['login']));;
			$user['password'] = $password;
			$user['memo'] = trim(addslashes($_POST['memo']));
			$user['default_site_id'] = (intval($_POST['default_site_id']))? intval($_POST['default_site_id']) : 2;
			
			$data[] = $user;
		}


		
	$core->db->autoupdate()->table('mcms_user')->data($data)->primary('id_user');
	$core->db->execute();


	unset($email, $login, $password, $memo, $data, $user);


	if(count($_POST['id_group']))
	{
		$core->db->delete('mcms_user_group', $id, 'id_user');
	
		foreach($_POST['id_group'] as $id_group)
		{
			$data[] = array('id_user'=>$id, 'id_group'=>$id_group);
		}
	
		$core->db->autoupdate()->table('mcms_user_group')->data($data);
		$core->db->execute();
		$controller->redirect('/'.$core->CONFIG['lang']['name'].'/users/list/', 'All changes have been saved.');
	}
	else
		{
			$controller->redirect('/'.$core->CONFIG['lang']['name'].'/users/edit/id/'.$id.'/', 'All changes have been saved, but it was not user defined membership in the group. Please edit this user');
		}




?>