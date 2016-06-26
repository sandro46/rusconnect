<?php

function check_login_for_used($login)
{
	$login = addslashes(mysql_real_escape_string($login));
		
	global $core;
		
	$sql = "SELECT COUNT(*) as cnt FROM site_users WHERE login = '{$login}'";
	$core->db->query($sql);
	$result = $core->db->get_field();
		
	return $result;
}
	
function checkCaptcha($string)
{
	if(md5(strtolower(trim($string))) == $_SESSION['secureHash']) return true;
	return false;
}
	
function createUser($name, $login, $pass, $email, $captcha)
{
	global $core;
		
	$name = addslashes(mysql_real_escape_string($name));
	$login = addslashes(mysql_real_escape_string($login));
	$pass = addslashes(mysql_real_escape_string($pass));
	$email = addslashes(mysql_real_escape_string($email));
	$invite = md5(microtime().md5($name).chr(mt_rand(127,256)).md5($login).chr(mt_rand(127,256)).md5($pass).chr(mt_rand(127,256)).md5($email).chr(mt_rand(127,256)).time());
		
	if(!checkCaptcha($captcha)) return 'captcha error';
		
	$data[] = array('id_user'=>$login, 'name'=>$name, 'login'=>$login, 'password'=>md5(md5($pass).$core->CONFIG['security']['user']['pass_salt']), 'default_site_id'=>2, 'email'=>$email, 'memo'=>'from swfgames.ru site', 'disable'=>0, 'invite_code'=>md5($invite), 'checked'=>0);
	$core->db->autoupdate()->table('site_users')->data($data);
	$core->db->execute();
			
	if(!$core->db->insert_id) return 'insert error';
		
	$message = "Вы зарегистрировались на сайте www.swfgames.ru<br><br>Ваш логин для входа: <b>{$login}</b><br>Ваш пароль для входа: <b>{$pass}</b><br><br><br>Вам необходимо активировать Ваш аккаунт.<br>Для этого перейдите по ссылке: <a href='http://swfgames.itbc.pro/ru/-utils/usercheck/protection/{$invite}/'>http://swfgames.itbc.pro/ru/-utils/usercheck/protection/{$invite}/</a><br><br><br>С Уважением,<br>Администрация сайта <b>www.swfgames.ru</b>";
		
	$core->lib->load('smtp');
	$mailer = new smtp_sender('smtp.itbc.pro', 'alexey@itbc.pro', 'QzWx123-');
	$mailer->defaultType = 'text/html';
	$mailer->send('noreply@swfgames.ru', $email, 'Регистрация на сайте swfgames.ru', $message);
		
	return 'ok';
}

$core->lib->load('ajax');
$ajax = new ajax();
$ajax->debug_mode = 0;
$ajax->request_type = 'POST';
$ajax->add_func('check_login_for_used', 'checkCaptcha', 'createUser');
$ajax->init();
$core->tpl->assign('ajax_output', $ajax->output);
$ajax->user_request();


class UserRegistration extends widgets implements iwidget
{
	
	public function main()
	{
		
	}
	
	public function out()
	{
		
	}
	
	
}