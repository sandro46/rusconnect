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


$controller->id = 7;
$controller->cached = 0;
$controller->init();

$controller->tpl = 'show.html';


$controller->load('client.pages.php');
$api= new client_pages();
$page = $api->getInfo($_GET['id']);
$core->title = /*$api->shopInfo['name'].' - '.*/$page['title'];
$core->tpl->assign('pageInfo', $page);
$core->meta_description = $page['meta_description'];
$core->meta_keywords = $page['meta_keywords'];


/*

$id_doc = intval($_GET['id']);

	if(!$id_doc)
	{
		$controller->content = $core->tpl->fetch('404.html',1,1,0,$core->site_name);
		$core->footer();
		die();
	}
	
	$document = new admin_docs($id_doc);
	
	if(!$document->info)
	{
		$controller->content = $core->tpl->fetch('404.html',1,1,0,$core->site_name);
		$core->test_docs_catch();
		$core->footer();
				
		die();
	}


	if($document->info['blocks'])
	{
		foreach($document->info['blocks'] as $block_file)
		{
			$patch_block = str_replace(array('block.', '.php'), array('',''),$block_file);

			$core->lib->widget_load($patch_block['file']);
		}
	}


	$core->title = $core->delete_html_tags($document->info['title']);
	
	
	
	$core->tpl->assign('title_length', strlen($core->title));
	$core->tpl->assign('document', $document->info);
	$core->tpl->assign('content_lenght', $document->info['content_lenght']);
	$core->tpl->assign('content_lenght', strlen($document->info['content']));
	$core->tpl->assign('rootPageId', $document->info['rootParentId']);
	
	//print_r($document);
	$controller->tpl = ($document->info['tpl_name'])? $document->info['tpl_name'] : 'show.html';
	
	
	$core->meta_description = $document->info['meta_desc'];
	$core->meta_keywords = $document->info['meta_keyw'];
	
	if($document->info['meta_title']) $core->title = $document->info['meta_title'];
	
*/
?>