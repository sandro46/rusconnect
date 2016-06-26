<?php


//$core->db->select()->from('mcms_themes')->fields('description', 'name')->where('id_site = '.$core->site_id);
//$core->db->execute();
//$core->db->get_rows();
//$core->tpl->assign('thems',$core->db->rows);


echo $core->tpl->get('index.html','_auth');


die();