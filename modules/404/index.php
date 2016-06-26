<?php


//$core->title = $core->mess->get(188);
//$core->lib->widget_load('site_map');
$core->tpl->assign('dont_show_title', true);
$core->title = 'ERROR 404.';
$core->modules->this['tpl']['name'] = false;
$core->content = $core->tpl->get('404.html', $core->site_name);
$core->footer();

?>
