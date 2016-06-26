<?php
	$core->header('utf-8');
	
	session_destroy();
	session_commit();
	session_unset();
	session_write_close();
		
	echo '<script>document.location.href = "/";</script>';
	
?>