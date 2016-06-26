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


$controller->id = 1;
$controller->cached = 0;
$controller->init();
$controller->tpl = 'show.html';
$core->title = 'M-cms control panel - '.$core->modules->this['describe'].'';

$id= intval($_GET['id']);

if(!$id) $controller->redirect('/'.$core->CONFIG['lang']['name'].'/feedback/list/', 'Wrong request: Invalid action id.<br>');
	

$controller->load('admin.feedback.php');
$root = new admin_feedback();



$root->getInfo($id);
$core->tpl->assign('order', $root->data);



?>