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


$controller->id = 24;
$controller->cached = 0;
$controller->init();

$controller->load('docs.class.php');

$type_id = intval($_GET['type']);



if(!$type_id) $controller->redirect('/'.$core->CONFIG['lang']['name'].'/pages/list/', 'Bad request!');

$ret = $_POST['returnedOut'];
unset($_POST['returnedOut']);
	
$id_doc = admin_docs::save($type_id);
		

if($ret)
{
	$controller->redirect('/'.$core->CONFIG['lang']['name'].'/pages/edit/id/'.$id_doc.'/', 'All data has been updated.');
}
else
	{
		$controller->redirect('/'.$core->CONFIG['lang']['name'].'/pages/list/opened/'.$id_doc, 'All data has been saved.');
	}


?>