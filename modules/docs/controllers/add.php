<?php
############################################################################
#             This file was created automatically core system              #
#                                                                          #
# ------------------------------------------------------------------------ #
# @Creator module version 1.2598 b                                         #
# @Author: Alexey Pshenichniy                                              #
# ------------------------------------------------------------------------ #
# Alpari CMS v.1 Beta   $17.06.2008                                        #
############################################################################


$controller->id = 5;
$controller->cached = 0;
$controller->cache_param  = array('_site' => 1 , '_lang' => 1 , '_url' => 1, '_uid'=>1 );
$controller->cache_expire = 0;
$controller->init();
$controller->load('docs.class.php');
$controller->load('ajax.extension.php');
$core->title = 'M-cms Control Panel - '.$core->modules->this['describe'];

$core->lib->load('ajax');
$ajax = new ajax();
$ajax->debug_mode = 0;
$ajax->request_type = 'POST';
$ajax->add_func('upload_image_catalog');
$ajax->init();
$core->tpl->assign('ajax_output', $ajax->output);
$ajax->user_request();



$type_id = intval($_GET['type']);
$parent = intval($_GET['parent']);

if(!$type_id) $controller->redirect('/'.$core->CONFIG['lang']['name'].'/pages/list/', 'Bad request!');

$root = new admin_docs();

$controller->tpl = $root->get_type_tpl($type_id);


$core->tpl->assign('langs',$core->get_all_langs());
$core->tpl->assign('typeinfo', array('type'=>$type_id, 'parent'=>$parent));
$core->tpl->assign('tpl_list_doc', $root->get_templates_list(0,1000,'name', 'name_module', 'pages'));
$core->tpl->assign('type_client_template', $root->get_type_tpl($type_id, 'client'));

$fields = $core->multidb->getFields($type_id);

foreach($fields as $item)
{
	if($item['field_base_type'] == 'text')
	{
		$core->lib->widget('CKeditor');
		$core->widgets->CKeditor()->InstanceName = 'CKeditor-'.$item['field_name'];
		$core->widgets->CKeditor()->TextareaName = $item['field_name'];
		$core->widgets->CKeditor()->Value = '';  
		$core->widgets->CKeditor()->Create();
		$core->tpl->assign($item['field_name'], $core->widgets->CKeditor()->itemHtml);	
		//print_r($core->widgets->CKeditor());
	}
}






?>
