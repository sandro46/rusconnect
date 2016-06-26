<?php 


if(empty($_POST['email']) && empty($_REQUEST['code'])) json_error(1, 'Не указан email');

include $core->CONFIG['module_path'].$core->siteObject['custom_preloader'].'/client.shop.php';
$core->shop = new client_shop();

if(empty($_REQUEST['code'])) {

	$login = mysql::str(trim($_POST['email']));

	$sql = "SELECT COUNT(*) FROM tp_user WHERE shop_id = {$core->shop->shopId} AND login = '{$login}'";

	$core->db->query($sql);

	if($core->db->get_field() === '1'){
		
		$password = genpass(40);
		
		$_SESSION['pass_reset_code'] = $password;
		$_SESSION['pass_reset_email'] = $login;

		//json_response('http://' . $_SERVER['SERVER_NAME'] . '/ru/-utils/shopuserresetpass/?code=' . $password);
		
		$message = 'Вы запросили сброс пароля на сайте <a href="http://'.$_SERVER['SERVER_NAME'].'/">http://'.$_SERVER['SERVER_NAME'].'/</a>.<br>
					Чтобы сгенерировать новый пароль перейдите по ссылке <a href="http://' . $_SERVER['SERVER_NAME'] . '/ru/-utils/shopuserresetpass/?code=' . $password . '">http://' . $_SERVER['SERVER_NAME'] . '/ru/-utils/shopuserresetpass/?code=' . $password . '</a>.<br>
					Если вы не запрашивали сброс пароля, проигнорируйте или удалите это письмо.';
		
		if(my_send_mail($login, 'rusconnect@rusconnect.ru', "Восстановление пароля {$_SERVER['SERVER_NAME']}", $message))
			json_response("Ссылка для сброса пароля выслана на {$login}");
		else
			json_error(7, "Не удалось отправить письмо на {$login}. Восстановление пароля невозможно.");
		
		
	} else {
		json_error(6,'Пользователь с такими E-mail не найден.');
	}

	json_response($result);
	
} else {
	
	if($_REQUEST['code'] == $_SESSION['pass_reset_code']){
		
		$login = $_SESSION['pass_reset_email'];
		$password = genpass(6);
		$md5 = md5(md5($password));

		$sql = "UPDATE tp_user SET password = '{$md5}' WHERE shop_id = {$core->shop->shopId} AND login = '{$login}'";
		
		
		if($core->db->query($sql)){

			$_SESSION['pass_reset_code'] = null;
			$_SESSION['pass_reset_email'] = null;
			
			$message = 'Новый пароль для сайта <a href="http://'.$_SERVER['SERVER_NAME'].'/">http://'.$_SERVER['SERVER_NAME'].'/</a> сгенерирован.<br><br>
						Данные для входа:<br>'."
						Логин: {$login}<br>
						Пароль: {$password}".'<br>
						Страница логина: <a href="http://'.$_SERVER['SERVER_NAME'].'/login/">http://'.$_SERVER['SERVER_NAME'].'/login/</a>
						';
			
			if(my_send_mail($login, 'rusconnect@rusconnect.ru', "Новый пароль для сайта {$_SERVER['SERVER_NAME']} сгенерирован.", $message))
				echo "Новый пароль сгенерирован и выслан на {$login}";
			else
				echo "Новый пароль: {$password}";
				
		} else {
			echo 'Произошла ошибка, попробуйте восстановить пароль еще раз.';
		}

	} else {
		echo 'Ссылка более недействительна или введена неверно.';
	}
}




function json_response($data) {
    echo json_encode(array('status'=>'ok', 'data'=>$data));
    die();
}

function json_error($code, $message) {
    echo json_encode(array(
        'error'=>$code,
        'message'=>$message
    ));
    die();
}

function genpass($lenpass){
	
	$chars="qazxswedcvfrtgbnhyujmkiolp1234567890QAZXSWEDCVFRTGBNHYUJMKIOLP";
	$lenchars = strlen($chars);
		
	for($i=0;$i<$lenpass;$i++)
		$password .= $chars[mt_rand(0,$lenchars)];
	
	return $password;
}

function my_send_mail($mail_to, $mail_from, $subject, $message, $path = null, $filename = null){

	if($mail_to){

		if(!$mail_from)	$mail_from	= $mail_to;
		if($path)		$file		= file_get_contents($path);
		if(!$filename)	$filename	= basename($path);

		$EOL		= "\r\n"; // некоторые почтовые сервера требуют \n
		$boundary   = "--".md5(uniqid(time()));
		$headers    = "MIME-Version: 1.0;$EOL";
		$headers   .= "Content-Type: multipart/mixed; boundary=\"$boundary\"$EOL";
		$headers   .= "From: $mail_from";

		$multipart  = "--$boundary$EOL";
		$multipart .= "Content-Type: text/html; charset=utf-8$EOL";
		$multipart .= "Content-Transfer-Encoding: base64$EOL";
		$multipart .= $EOL;
		$multipart .= chunk_split(base64_encode($message));
		$multipart .=  "$EOL--$boundary$EOL";

		if($file){
			$multipart .= "Content-Type: application/octet-stream; name=\"$filename\"$EOL";
			$multipart .= "Content-Transfer-Encoding: base64$EOL";
			$multipart .= "Content-Disposition: attachment; filename=\"$filename\"$EOL";
			$multipart .= $EOL;
			$multipart .= chunk_split(base64_encode($file));
			$multipart .= "$EOL--$boundary--$EOL";
		}

		if(mail($mail_to, $subject, $multipart, $headers)) return true;
		
	}
}

?>

