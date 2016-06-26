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



$controller->id = 5;
$controller->cached = 0;
$controller->cache_param  = array('_site' => 1 , '_lang' => 1 , '_url' => 1, '_uid'=>1 );
$controller->cache_expire = 0;
$controller->init();
$controller->tpl = 'add.html';
$controller->cached();

//Default page title for all admin modules
$core->title = 'M-cms Control Panel - '.$core->modules->this['describe'];


//Extended module class (files location in: /module_dir/class/className.php)

$controller->load('connector.php');
$controller->load('ajax.extension.php');

	if($_GET['type']=='ckeditor' && $_GET['name'] == 'image_browser')
	{		
		$query = $_GET['query'];
		
		$core->lib->load('ajax');
		$ajax = new ajax();
		$ajax->debug_mode = 0;
		$ajax->request_type = 'POST';
		$ajax->add_func('getImagesList', 'getTreeList');
		$ajax->init();
		$core->tpl->assign('ajax_output', $ajax->output);
		$ajax->user_request();	
		
		$root = new files_connector();
		$root->createTree();
		
		$core->tpl->assign('treeHtml', $root->treeHtml);
		$core->tpl->assign('imagesList', 'empty');
		$core->tpl->assign('CKfunctionId', $query['CKEditorFuncNum']);
		
		
		$core->content = $core->tpl->get('connector.imagesBrowser.html', 'files');
		$core->main_tpl = 'main.null.html';
		$core->footer(true);
	
		
		//'<a href="#" onclick="window.opener.CKEDITOR.tools.callFunction('.$query['CKEditorFuncNum'].', \'/link/for/image.jpg\', function(){ this.getDialog().getContentElement(\'Link\', \'txtUrl\').setValue(\'teeeeest\')}); window.close();">Test</a>';

	}
	
	if($_GET['type']=='documents' && $_GET['name'] == 'image_browser')
	{		
		$callback = $_GET['callback'];
		
		$core->lib->load('ajax');
		$ajax = new ajax();
		$ajax->debug_mode = 0;
		$ajax->request_type = 'POST';
		$ajax->add_func('getImagesList', 'getTreeList');
		$ajax->init();
		$core->tpl->assign('ajax_output', $ajax->output);
		$ajax->user_request();	
		
		$root = new files_connector();
		$root->createTree();
		
		$core->tpl->assign('treeHtml', $root->treeHtml);
		$core->tpl->assign('imagesList', 'empty');
		$core->tpl->assign('callback', $callback);
		
		
		$core->content = $core->tpl->get('connector.imagesBrowser.html', 'files');
		$core->main_tpl = 'main.null.html';
		$core->footer(true);
	
		
		//'<a href="#" onclick="window.opener.CKEDITOR.tools.callFunction('.$query['CKEditorFuncNum'].', \'/link/for/image.jpg\', function(){ this.getDialog().getContentElement(\'Link\', \'txtUrl\').setValue(\'teeeeest\')}); window.close();">Test</a>';

	}

//die('Куда полез?');






?>