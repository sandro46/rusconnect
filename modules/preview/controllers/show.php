<?php
############################################################################
#          This controller was created automatically core system           #
#                                                                          #
# ------------------------------------------------------------------------ #
# @Creator module version 1.2598 b                                         #
# @Author: Alexey Pshenichniy                                              #
# ------------------------------------------------------------------------ #
# Alpari CMS v.1 Beta   $17.06.2008                                        #
############################################################################



$controller->id = 7;
$controller->cached = 0;
$controller->cache_param  = array('_site' => 1 , '_lang' => 1 , '_url' => 1, '_uid'=>1 );
$controller->cache_expire = 30;
$controller->init();
$controller->cached();

if (($hash = addslashes($_GET['hash']))) {
	$core->db->query("SELECT `key`, `value` FROM `mcms_temp_data` WHERE `group` = 'preview' AND `sub_group` = '{$hash}'");
	$core->db->get_rows();
	foreach($core->db->rows as $row) {
		$data[$row['key']] = $row['value'];
	}

	
	$core->title = $data['title'];
	$controller->content = $data['content'];

//	$core->db->query("DELETE FROM `mcms_temp_data` WHERE `group` = 'preview' AND `sub_group` = '{$hash}'");
} else {
	$controller->content = $core->tpl->fetch('404.html',1,1,0,$core->site_name);
	$core->footer();
	die();
}

//Default page title for all admin modules
//$core->title = 'M-cms Control Panel - '.$core->modules->this['describe'];

//Extended module class (files location in: /module_dir/class/className.php)
//$controller->load('className.php');


?>