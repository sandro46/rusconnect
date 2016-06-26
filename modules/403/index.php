<?php
$cid = ($core->controller_object instanceof controller)? $core->controller_object->id : 0;
echo $core->log->sys();
$core->fatal_error('Ошибка доступа','Вам запрещен доступ в этот раздел.</p><p>Для связи с администратором используйте почту <a href="mailto:'.$_SERVER['SERVER_ADMIN'].'">'.$_SERVER['SERVER_ADMIN'].'</a><br><a href="javascript:void(0)" onclick="window.history.go(-1)">Назад</a>   <a href="/">На главную</a></p><hr>Техническая информация: UID:'.$core->user->id.' SID: '.$core->site_id.' MID: '.$core->module_id.' CID: '.$cid.'</hr>')

?>