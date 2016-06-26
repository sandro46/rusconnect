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
$controller->tpl = 'show.html';
$controller->cached();

//Default page title for all admin modules
$core->title = 'M-cms Control Panel - '.$core->modules->this['describe'];


//Extended module class (files location in: /module_dir/class/className.php)
//$controller->load('className.php');

if (intval($_GET['id'])) {
	global $core;
	$preview_id = intval($_GET['id']);

	$entry = $core->history->getHistory($preview_id);
	$sql = "SELECT * FROM mcms_history_data WHERE id_history = {$preview_id}";
	$core->db->query($sql);
	$core->db->get_rows();
	$history = array();

	$select = '';
	foreach ($core->db->rows as $row)
		{
		$history[$row['lang_id']][$row['value']] = htmlspecialchars(stripslashes($row['data']));
		if ($row['lang_id'] == 1 || !($row['lang_id']))
			{
			$row['value'] = stripslashes($row['value']);
			if ($row['lang_id']) { $flag = '`lang_id`'; } else { $flag = 'NULL as lang_id'; }
			$select .= '`'.$row['value'].'` , (SELECT DATA_TYPE FROM `information_schema`.`COLUMNS` WHERE TABLE_SCHEMA = \''.$core->CONFIG['db_site']['dbname'].'\' AND TABLE_NAME = \''.$entry['tablename'].'\' AND COLUMN_NAME = \''.$row['value'].'\')as '.$row['value'].'_column_type, ';
			}
		}




	$sql = "SELECT {$select} {$flag} FROM {$entry['tablename']} WHERE {$entry['primary_key']} = {$entry['primary_value']}";
	$core->db->query($sql);
	$core->db->get_rows();
	$current = array();

	foreach ($core->db->rows as $row)
		{
		$lang_id = $row['lang_id']; unset($row['lang_id']);
		foreach ($row as $k=>&$col)
			{
			if(substr($k, -12) == '_column_type')
				{
				$col_types[substr($k, 0, -12)]	= $col;
				unset($col, $row[$k]);
				}
				else
					{
					$col = htmlspecialchars($col);
					}

			}
		$current[$lang_id] = $row;
		}

    $core->tpl->assign('multilang', ($flag == '`lang_id`'));
	$core->tpl->assign('fields_types', $col_types);
	$core->tpl->assign('langs',$core->get_all_langs());
	$core->tpl->assign('history',$history);
	$core->tpl->assign('current',$current);

}





?>