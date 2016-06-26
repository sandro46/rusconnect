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
$controller->cached = 1;
$controller->cache_param  = array('_site' => 1 , '_lang' => 1 , '_url' => 1, '_uid'=>1 );
$controller->cache_expire = 5*60;
$controller->init();
$controller->tpl = 'document_list.html';
$controller->cached();

$core->title = 'M-cms Control Panel - '.$core->modules->this['describe'];

$controller->load('docs.class.php');
$controller->load('ajax.extension.php');
$core->lib->load('ajax');

$ajax = new ajax();

$ajax->debug_mode = 0;
$ajax->request_type = 'POST';
$ajax->add_func('get_types_select_html');
$ajax->add_func('change_view_lang','update_draged_item', 'update_name_item', 'get_modules_list', 'insert_doc_module','get_window_add_simple_block', 'save_simple_link');
$ajax->init();
$core->tpl->assign('ajax_output', $ajax->output);
$ajax->user_request();


$view_lang = 0;

if(isset($_SESSION['docs_view_lang'])) $view_lang = $_SESSION['docs_view_lang'];

$tree_data = admin_docs::get_tree_data($view_lang);


$core->tpl->assign('tree_data_items', $tree_data);
$core->tpl->assign('openedId', intval($_GET['opened']));

$_SESSION['docs_view_lang'] = 1;


?>