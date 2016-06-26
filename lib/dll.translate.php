<?php
	################################################################################
	# ---------------------------------------------------------------------------- #
	# @Author: Alexey Pshenichniy <inc.mcms@gmail.com> <M-zone Inc.> 2005-2009     #
	# @Date: 20.05.2008                                                            #
	# @license: GNU GENERAL PUBLIC LICENSE Version 2                               #
	# ---------------------------------------------------------------------------- #
	# M-cms v4.1 (core build - 4.102)                                              #
	################################################################################
	
	
	function translateRUtoEN($string) {
		$RuLower = array('а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я');
		$RuUpper = array('А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я');
		
		$EnUpper = array('A', 'B', 'V', 'G', 'D', 'E', 'E', 'G', 'Z', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'C', 'CH', 'SH', 'SCH', '', 'I', '', 'E', 'YU', 'YA');
		$EnLower = array('a', 'b', 'v', 'g', 'd', 'e', 'e', 'g', 'z', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'ch', 'sh', 'sch', '', 'i', '', 'e', 'yu', 'ya');
		
		$string = str_replace($RuLower, $EnLower, $string);
		$string = str_replace($RuUpper, $EnUpper, $string);
		
		return $string;		
	}
	
	function translateENtoRU($string) {
		$RuLower = array('а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я');
		$RuUpper = array('А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я');
		
		$EnUpper = array('A', 'B', 'V', 'G', 'D', 'E', 'E', 'G', 'Z', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'C', 'CH', 'SH', 'SCH', '', 'I', '', 'E', 'YU', 'YA');
		$EnLower = array('a', 'b', 'v', 'g', 'd', 'e', 'e', 'g', 'z', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'ch', 'sh', 'sch', '', 'i', '', 'e', 'yu', 'ya');
		
		$string = str_replace($EnLower, $RuLower, $string);
		$string = str_replace($EnUpper, $RuUpper, $string);
		
		return $string;		
	}

	
	function rewriteEscapeSymbolDelete($string) {
		$escpeSearche = array('{','}','[',']','\'','"',';',':','/','\\','|','>','<',',', '№','?','!','@','`','~', '*', '^', '%', '#', '=', '+', '$', '&');
		$escpeReplace = array('', '', '',     '',  '',  '','', '',  '',  '','',  '','',  '', '', '', '', '', '',  '',   '', '',  '',  '',   '', '', '');
		
		$string = str_replace(' ', '_', $string);
		$string = str_replace($escpeSearche, $escpeReplace, $string);
		
		return $string;
	}


?>