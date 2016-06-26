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
$controller->tpl = 'form.html';
$controller->cached();

$core->title = $core->modules->this['describe'].' - '.$controller->descr;


$controller->load('templates.php', 'system');
$core->tpl->assign('allThems', $core->tpl->getThemsList());
$core->tpl->assign('langs', admin_templates::get_langs());
$core->tpl->assign('showHighlight', $core->CONFIG['ui']['templates']['codelight']);


?>