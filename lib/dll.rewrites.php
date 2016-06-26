<?php
	################################################################################
	# ---------------------------------------------------------------------------- #
	# @Author: Alexey Pshenichniy <inc.mcms@gmail.com> <M-zone Inc.> 2005-2009     #
	# @Date: 20.05.2008                                                            #
	# @license: GNU GENERAL PUBLIC LICENSE Version 2                               #
	# ---------------------------------------------------------------------------- #
	# M-cms v4.1 (core build - 4.102)                                              #
	################################################################################


	# System rewrites
    //$rewrite['all'][] = array("/\/utils\/action\/([A-Za-z_\-]{1,})\/(.+?)$/s", "~/action/\\1/\\2");
    //$rewrite['all'][] = array("/\/utils\/action\/([A-Za-z_\-]{1,})\/$/s", "~/action/\\1");
    
    # Site rewrite
    //$rewrite[2][] = array("/([A-Za-z]{2,3})\/pages\/([0-9]{0,})\.html$/s", "\\1/pages/show/id/\\2/");

	$rewrite['all'][] = array("/\/id([0-9]{0,})$/s", "/ru/catalog/show/id/\\1/type/cupon/");

	return $rewrite;
?>
