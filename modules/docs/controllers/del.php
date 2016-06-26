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


$controller->id = 4; 
$controller->cached = 0; 
$controller->init();


$controller->load('docs.class.php');

$id_doc = intval($_GET['id']);


if(!$id_doc)
	{ 
	$controller->redirect('/'.$core->CONFIG['lang']['name'].'/pages/list/', 'Document have not been removed.<br>Wrong request: Invalid document id.<br>');
	}
	else
		{
		$admin_docs = new admin_docs();
		$admin_docs->delete($id_doc);
		$controller->redirect('/'.$core->CONFIG['lang']['name'].'/pages/list/', 'Document has been removed.');
		}

?>