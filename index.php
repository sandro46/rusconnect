<?php
include	'./config.php';
include CORE_CLASS_PATH.'core.php';
$core = new core($config);
$core->start();
$core->footer();
