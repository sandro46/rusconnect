<?php
################################################################################
# ---------------------------------------------------------------------------- #
# @Author: Alexey Pshenichniy <inc.mcms@gmail.com> <M-zone Inc.> 2005-2012     #
# @Date: 20.05.2008                                                            #
# @license: GNU GENERAL PUBLIC LICENSE Version 2                               #
# @lastModified = 1324128382                                                   #
# ---------------------------------------------------------------------------- #
# M-cms v4.1 (core build - 4.102)                                              #
################################################################################

	if(isset($_GET['ck']) && $_GET['ck'] == 'sid') {
		session_start();
		session_write_close();
		echo time();
		die();
	}

	$core->header('utf-8');

	// auth before ajax query
	if(isset($_POST['majax']) && intval($_POST['majax'] == 1)) {
		$core->tpl->assign('majax',true);
	}

	if(!isset($_POST['SL']) || !isset($_POST['SP'])) {



		checkAjaxAuth(false);

		if(!empty($_GET['type']) && $_GET['type'] == 'persist') {
		    $core->tpl->assign('change_auth_url', 'admin.'.$_SERVER['SERVER_NAME']);
		    include CORE_PATH.'modules/_auth/index.php';

		    die();
		}

		doneAllAuth();
	}

	$login = mysql_real_escape_string(addslashes(trim($_POST['SL'])));
	$pwd = md5(md5($_POST['SP']).$core->CONFIG['security']['user']['pass_salt']);
	// $pwd = md5(md5(121314).$core->CONFIG['security']['user']['pass_salt']);

	// echo $pwd; die();

	checkLoginType($login);

	//if($_SERVER['REMOTE_ADDR'] != '185.15.116.23') {
		//userBlocked('notfound');
	//}

	if($core->site_type == 2) { // кабинет
		$loginResult = main_module::login($login, $pwd);

		if(!is_array($loginResult)) userBlocked("notfound");

		if(intval($loginResult["user_active"]) != 1) userBlocked("blocked", $loginResult["user_block_reason"]);
		if(intval($loginResult["client_active"]) != 1) userBlocked("blocked", $loginResult["client_block_reason"]);

		$core->user->id = $loginResult['system_user_id'];
		$core->user->custom_id = $loginResult['user_id'];
		$core->user->write_session();

    	//$api = new main_module();
    	//$api->init();
    	//$api->logAuth($login);

		checkAjaxAuth(true);
		doneAllAuth();

	} else if($core->site_type == 1) { // Админка

		$sql = "SELECT DISTINCT(id_user) as id, name, login,  memo, disable FROM mcms_user WHERE LOWER(`login`) = '".strtolower($login)."' AND `password` = '".$pwd."'";
		$core->db->query($sql);
		$uin = $core->db->get_rows(1);


		if(!is_array($uin) || !isset($uin['id'])) userBlocked("notfound"); 			 // пользователь не найден
		if($uin['disable'] == 1) userBlocked("blocked", $uin['memo']);     			 // пользователь заблокирован
		if(!$core->perm->check_site(false, $uin['id'])) userBlocked("accessdenied"); // пользователю запрещен доступ к сайту

		$core->user->id = $uin['id'];
		$core->user->write_session();

		checkAjaxAuth(true);
		doneAllAuth();
	} elseif($core->site_type == 3) {
	    $loginResult = main_module::login($login, $pwd);

	    if(!is_array($loginResult)) userBlocked("notfound");

	    if(intval($loginResult["user_active"]) != 1) userBlocked("blocked", $loginResult["user_block_reason"]);
	    if(intval($loginResult["client_active"]) != 1) userBlocked("blocked", $loginResult["client_block_reason"]);

	    $core->user->id = $loginResult['system_user_id'];
	    $core->user->custom_id = $loginResult['user_id'];
	    $core->user->write_session();

	    //$api = new main_module();
	    //$api->init();
	    //$api->logAuth($login);

	    checkAjaxAuth(true);
	    header('Location: http://rc-admin.itbc.pro/');
		die();
	}



	function checkLoginType($login) {
		global $core;

		if(!empty($core->CONFIG['admin']) && !empty($core->CONFIG['admin']['change_site_with_root_login']) && $core->CONFIG['admin']['change_site_with_root_login']) {
			$sql = "SELECT id_user FROM mcms_user WHERE LOWER(`login`) = '".strtolower($login)."'";
			$core->db->query($sql);
			$uin = $core->db->get_field();
			if($uin == 1) {
				session_unset();
				$core->site_type = 1;
				$core->site_id = 1;
				$_SESSION['core:changeSite'] = 1;
			} else {
				unset($_SESSION['core:changeSite']);
			}
		}
	}

	function checkAjaxAuth($authOk = false) {
		if(!$authOk && isset($_POST['SMX']) && intval($_POST['SMX'] == 1)) {
			die();
		}

		if(isset($_POST['SMX']) && intval($_POST['SMX'] == 1) && $authOk) {
			echo '<html><head></head><body>';
			echo '<script type="text/javascript">';
			echo 'parent.MAJAX.ar();';
			echo '</script></body></html>';
			die();
		}
	}

	function doneAllAuth($authOk = false) {
		header('Location: /');
		die();
	}

	function userBlocked($type, $msg='') {
		$message = "";
		switch($type) {
			case "blocked" :
				$message = 'Ваша учетная запись заблокирована.<br>Причина: <br>'.$msg;
			break;

			case "notfound" :
				$message = 'Пользователя с таким логином и паролем не существует.';
			break;

			case "accessdenied" :
				$message = 'Вам запрещен доступ к этому сайту.';
			break;
		}

		core::$instance->tpl->assign('auth_message', $message);
		core::$instance->error_page(401);
		core::$instance->footer();
		die();
	}
?>
