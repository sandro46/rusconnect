<?php

	$core->lib->load('captcha');
	session_start();
	$cap = new captcha(200,70,6);
	$cap->fontFile = $core->CONFIG['var_path'].'monofont.ttf';	
	$cap->generateImage();
	session_write_close();
	$cap->getImage();
	
?>