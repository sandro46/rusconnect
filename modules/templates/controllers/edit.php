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



$controller->id = 6; 
$controller->cached = 0; 
$controller->cache_param  = array('_site' => 1 , '_lang' => 1 , '_url' => 1, '_uid'=>1 );
$controller->cache_expire = 0;
$controller->init();
$controller->tpl = 'form.html';
$controller->cached();

$core->title = $core->modules->this['describe'].' - '.$controller->descr;

$controller->load('templates.php');
$controller->load('ajax_ext.php');
$core->lib->load('ajax');

$ajax = new ajax();
$ajax->debug_mode = 0;
$ajax->request_type = 'POST';
$ajax->add_func('rollbackTemplate');
$ajax->init();

$ajax->user_request();

$core->tpl->assign('ajax_output', $ajax->output);


$id = intval($_GET['id']);
$themeName = (isset($_GET['theme']))? addslashes($_GET['theme']) : $core->theme;

$curentModuleName = $core->tpl->getModulenameByTemplateId($id);

$message = $core->mess->get(18).' id = '.$id;
$core->log->access(1, $message);

$core->tpl->assign('curentItemId', $id);
$core->tpl->assign('langs', $core->get_all_langs());
$core->tpl->assign('allThems', $core->tpl->getThemsList());
$core->tpl->assign('template', admin_templates::get_edit_data($id, $themeName));
$core->tpl->assign('curentTplModule', $curentModuleName);
$core->tpl->assign('speedDialMenu', admin_templates::getSpeedTemplateMenuData($curentModuleName, $core->tpl->getThemeByTplId($id)));
$core->tpl->assign('history', admin_templates::listHistory($id));
$core->tpl->assign('showHighlight', $core->CONFIG['ui']['templates']['codelight']);




?>