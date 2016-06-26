<?php
$controller->id = 5; 
$controller->cached = 0; 
$controller->init();
$controller->tpl = 'edit.html';

$core->title = 'M-cms Control Panel - '.$core->modules->this['describe'];

$controller->load('messages.php', 'system');

$id_message = 0;

$core->tpl->assign('id_message', $id_message);
$core->tpl->assign('langs', admin_messages::get_langs());

$core->log->access(1, 13);

?>