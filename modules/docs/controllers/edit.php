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


$controller->id = 6;
$controller->cached = 0;
$controller->cache_param  = array('_site' => 1 , '_lang' => 1 , '_url' => 1, '_uid'=>1 );
$controller->cache_expire = 0;
$controller->init();
$controller->load('docs.class.php');

$core->title = 'M-cms Control Panel - '.$core->modules->this['describe'];


$id_doc = intval($_GET['id']);

if(!$id_doc) $controller->redirect('/'.$core->CONFIG['lang']['name'].'/pages/list/', 'Bad request!');

$root = new admin_docs();
$item = $root->get_info($id_doc);



if($item['type'] == 6)
{
	if(!$item['multidb'])  $controller->redirect('/'.$core->CONFIG['lang']['name'].'/pages/list/', 'Error. Item not exists!');
		
	foreach($item['fieldsInfo'] as $fieldName=>$fieldInfo)
	{
		
		if($fieldInfo['field_base_type'] == 'text')
		{
			$core->lib->widget('CKeditor');
			$core->widgets->CKeditor()->InstanceName = 'CKeditor-'.$fieldName;
			$core->widgets->CKeditor()->TextareaName = $fieldName;
			$core->widgets->CKeditor()->Value = $item['multidb'][$fieldName];  
			$core->widgets->CKeditor()->Create();
			
			$core->tpl->assign($fieldName, $core->widgets->CKeditor()->itemHtml);
		}
		
	}
	
	
	$item['multidb']['parent'] = $item['parent_id'];
	$item['multidb']['type'] = $item['dataTypeId'];
	$item['multidb']['id_doc'] = $item['id_doc'];
	$item['multidb']['rewrite'] = $item['rewrite'];
	$item['multidb']['rewrite_id'] = $item['rewrite_id'];
	$item['multidb']['id_template'] = $item['template_id'];
	$item['multidb']['typeValue'] = 6;
	$item['multidb']['visible'] = $item['visible'];
	
	//$item['multidb']['order'] = $item['order'];
	
	$controller->tpl = $root->get_type_tpl($item['dataTypeId']);
	
	$core->tpl->assign('typeinfo', $item['multidb']);
	
	//print_r($item['multidb']['rewrite_id']);
	$core->tpl->assign('type_client_template', $root->get_type_tpl($item['dataTypeId'], 'client'));
	$core->tpl->assign('tpl_list_doc', $root->get_templates_list(0,1000,'name', 'name_module', 'pages'));
	
}
else
	{
		$core->tpl->assign('typeinfo', $item);
		$controller->tpl = 'defaultForm.html';
	}


		
		

$core->tpl->assign('langs',$core->get_all_langs());






?>