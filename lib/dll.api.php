<?php
################################################################################
# ---------------------------------------------------------------------------- #
# @Author: Alexey Pshenichniy <inc.mcms@gmail.com> <M-zone Inc.> 2005-2009     #
# @Date: 20.05.2008                                                            #
# @license: GNU GENERAL PUBLIC LICENSE Version 2                               #
# ---------------------------------------------------------------------------- #
# M-cms v4.1 (core build - 4.102)                                              #
################################################################################


	function slicetext($size, $text) {
		$size = intval($size);
		$text = stripslashes($text);
		$text = (mb_strlen($text)>= $size-3)? mb_substr($text, 0, $size-3).'...':$text;
		
		return $text;
	}
	
	function replaceNull($one, $two) {
		return ($one == 'null' || $one == 'NULL')? $two : $one;
	}
	
	function jsonError() {
		switch (json_last_error()) {
	      	case JSON_ERROR_NONE:
	            echo 'No errors';
	        break;
	        case JSON_ERROR_DEPTH:
	            echo 'Maximum stack depth exceeded';
	        break;
	        case JSON_ERROR_STATE_MISMATCH:
	            echo 'Underflow or the modes mismatch';
	        break;
	        case JSON_ERROR_CTRL_CHAR:
	            echo 'Unexpected control character found';
	        break;
	        case JSON_ERROR_SYNTAX:
	            echo 'Syntax error, malformed JSON';
	        break;
	        case JSON_ERROR_UTF8:
	            echo 'Malformed UTF-8 characters, possibly incorrectly encoded';
	        break;
	        default:
	            echo 'Unknown error';
	        break;
    	}
	}


	function parce_digits($int) {
		$zerro = false;
		$dig = '';
		if(strstr($int, '.')) {
			$_Ret = explode(".", $int);
			
			if(count($_Ret) == 2) {
				$zerro = trim($_Ret[1]);
				$int = trim($_Ret[0]);
			}
		}
		
		$len= strlen($int);
		for($i= 0; $i< ($len+ 3); $i+= 3) {
			$dig= substr($int, ($len- $i), 3). " ". $dig;
			if(($len- $i)< 3&& ($len- $i!= 0)&& ($len!= $i)) {
				$dig= substr($int, 0, $len- $i). " ". $dig;
				break;
			}
		}
		if($zerro !== false) $dig = trim($dig).",".$zerro;
			
		return trim($dig);
	}
	

	function parse_rub($int) {
		if($int == 0) return '-';
		return parce_digits($int).' р.';
	}
	
	function now($option) {
		global $core;    
		    
		$current = $core->get_now($option);
		
		if($current) return $current;    
		    
		
		$time= TIMESTAMP;
		switch($option)
			{
			case "day":
			case "d":
				$tmp= @date("d", $time);
			break;
			
			case "month":
			case "m":
				$tmp= @date("m", $time);
			break;
			
			case "year":
			case "y":
				$tmp= @date("Y", $time);
			break;
			
			case "hour":
			case "h":
				$tmp= @date("H", $time);
			break;
			
			case "min":
			case "i":
				$tmp= @date("i", $time);
			break;
			
			case "sec":
			case "s":
				$tmp= @date("s", $time);
			break;
			
			case "weekday":
			case "w":
				$tmp= @date("w", $time);
			break;
			
			case "weeknum":
			case "W":
				$tmp= @date("W", $time);
			break;
			
			default:
				$tmp= - 1;
			break;
			}
			
		$core->set_now(array($option=>$tmp));
			
		return $tmp;
	}

	function templates_buffering_callback($buffer) {
		global $core;
	
		unset($core->tpl->tmp_buffer_obj);
	
		$core->tpl->tmp_buffer_obj = $buffer;
	
		return $buffer;
	}

	function dateAgoS($time) { return date("d.m.Y H:i", $time); }
	
	function dateAgoSS($time) { return date("d.m.Y", $time); }
	
	function dateAgo($time) {
		global $core;
		
		$now = time();
		$dayNow = date("d", $now);
		$timeDay = date("d", $time);
		$timeMonth = date("m", $time);
		$timeYear = date("Y", $time);
		$NowMonth = date("m", $now);
		$NowYear = date("Y", $now);
		$secondsAgo = $now-$time; 
		
		$minust['ru'] = array('Минуту','Минуты', 'Минут');
		$minust['en'] = array('Minute','Minutes', 'Minutes');
		
		$hours['ru'] = array('Час','Часа', 'Часов');
		$hours['en'] = array('Hour','Hour', 'Clocks');
		
		$seconds['ru'] = array('Секунду','Секунды', 'Секунд');
		$seconds['en'] = array('Second','Second', 'Seconds');
		
		$month['ru'] = array('Месяц','Месяца', 'Месяцев');
		$month['en'] = array('Month','Month', 'Months');
				
		$agoText['ru'] = 'назад';
		$agoText['en'] = 'ago';
		
		$tomorow['ru'] = 'Вчера в';
		$tomorow['en'] = 'Tomorow in';
		
		//редавтирование было вчера
		if($timeDay == $dayNow-1 && $timeMonth == $NowMonth && $timeYear == $NowYear) {
			return $tomorow[$core->CONFIG['lang']['name']].' '.date("H:i", $time);
		}
		
		
		if($dayNow == $timeDay && $timeMonth == $NowMonth && $timeYear == $NowYear) {
			// редактирование было сегодня 
			
			if($secondsAgo == 60) {
				// минуту назад
				return $minust[$core->CONFIG['lang']['name']][0].' '.$agoText[$core->CONFIG['lang']['name']];
			}
			
			if($secondsAgo > 60) {
				// больше минуты назад
				$text = '';
				
				if($secondsAgo > 3600) {
					// больше часа назад
					
					// целые часы
					$hourAgo = (int)($secondsAgo/3600);
					// целые минуты
					$minustAgo = (int)(($secondsAgo%3600)/60);
					
					$text .= $hourAgo.' ';
					$text .= formatTextInCOunter($hourAgo, $hours[$core->CONFIG['lang']['name']]);
					$text .= ' '.$minustAgo.' ';
					$text .= formatTextInCOunter($minustAgo, $minust[$core->CONFIG['lang']['name']]);
					$text .= ' '.$agoText[$core->CONFIG['lang']['name']];
					
					return $text;
				}
				
				$minustAgo = (int)($secondsAgo / 60);
				$secondsA = (int)($secondsAgo % 60);
				
				$text .= $minustAgo.' ';
				$text .= formatTextInCOunter($minustAgo, $minust[$core->CONFIG['lang']['name']]);
				$text .= ' '.$secondsA.' ';
				$text .= formatTextInCOunter($secondsA, $seconds[$core->CONFIG['lang']['name']]);
				$text .= ' '.$agoText[$core->CONFIG['lang']['name']];
					
				return $text;
			}
			
			if($secondsAgo < 60) {
				// меньше минуты назад
				$text = $secondsAgo.' '.formatTextInCOunter($secondsAgo, $seconds[$core->CONFIG['lang']['name']]).' '.$agoText[$core->CONFIG['lang']['name']];
				
				return $text;
			}	
		}
		
		if($dayNow == $timeDay && $timeMonth < $NowMonth && $timeYear == $NowYear) {
			$MonthCount = intval($NowMonth) - intval($timeMonth);
			$MonthCountText = ($MonthCount == 1)? '': $MonthCount.' ';
			
			$text = $MonthCountText.formatTextInCOunter($MonthCount, $month[$core->CONFIG['lang']['name']]).' назад';
			
			return $text;
		}
		
		return date("d.m.Y H:i", $time);	
	}
	
	function bday($time) {
		return date('d.m.Y', $time);
	}

	function now_date($lang_name, $time, $type = 'string'){
		$days = array('01'=>'01','02'=>'02','03'=>'03','04'=>'04','05'=>'05','06'=>'06','07'=>'07','08'=>'08','09'=>'09',10=>10,11=>11,12=>12,13=>13,14=>14,15=>15,16=>16,17=>17,18=>18,19=>19,20=>20,21=>21,22=>22,23=>23,24=>24,25=>25,26=>26,27=>27,28=>28,29=>29,30=>30,31=>31);

		$mons[1]['ru'] = array ('01'=>'Января', '02'=>'Февраля', '03'=>'Марта', '04'=>"Апреля", '05'=>"Мая", '06'=>"Июня", '07'=>"Июля", '08'=>"Августа", '09'=>"Сентября", 10=>"Октября", 11=>"Ноября", 12=>"Декабря");
		$mons[1]['en'] = array ('01'=>'January','02' =>'February','03'=>'March','04' =>"April",'05 '=>"May", '06'=>"June", '07'=>"July", '08'=>"August", '09'=>"September", 10 =>"October", 11 =>"November", 12 =>"December");
	
		$mons[0]['ru'] = array ('01'=>'Январь', '02'=>'Февраль', '03'=>'Март', '04'=>"Апрель", '05'=>"Май", '06'=>"Июнь", '07'=>"Июль", '08'=>"Август", '09'=>"Сентябрь", 10=>"Октябрь", 11=>"Ноябрь", 12=>"Декабрь");
		$mons[0]['en'] = array ('01'=>'January','02' =>'February','03'=>'March','04' =>"April",'05'=>"May", '06'=>"June", '07'=>"July", '08'=>"August", '09'=>"September", 10 =>"October", 11 =>"November", 12 =>"December");
	
		$weeks[0]['ru'] = array ('01' => 'Понедельник', '02' => 'Вторник',	'03' => 'Среда',		'04' => 'Четверг',		'05' => 'Пятница',	'06' => 'Суббота',		'07' => 'Воскресенье');
		$weeks[0]['en'] = array ('01' => 'Monday ',		'02' => 'Tuesday ', '03' => 'Wednesday ',	'04' => 'Thursday ',	'05' => 'Friday ',	'06' => 'Saturday ',	'07' => 'Sunday');

		$dayName[0]['en'] = array('Yesterday', 'Today');
		$dayName[0]['ru'] = array('Вчера', 'Сегодня','Завтра');
		

		
		
	
	
		for($i=1998; $i!= 2010; $i++){
			$years[$i] = $i;
		}
	
		switch($type){
			case 'string':
				$ret =  $days[@date('d', $time)].' '.$mons[1][$lang_name][@date('m', $time)].' '.$years[@date('Y', $time)];
			break;
	
			case 'array':
				$ret = 	array('mon'=>$mons[1][$lang_name][@date('m', $time)], 'day'=>$days[@date('d', $time)], 'year'=>$years[@date('Y', $time)]);
			break;
	
			case 'select':
				$ret = "<select name=\"date[day]\" id=\"date_day\">\n";
				foreach($days as $k=>$v)
					{
					$ret .= "<option value='".$k."'";
	
					if($v == $days[@date('d', $time)]) $ret .= " selected='selected' ";
	
					$ret .= ">".$v."</option>\n";
					}
				$ret .= "</select>\n";
	
				$ret .= "<select name=\"date[mon]\" id=\"date_mon\">\n";
				foreach($mons[1][$lang_name] as $k=>$v)
					{
					$ret .= "<option value='".$k."'";
	
					if($k == $days[@date('m', $time)]) $ret .= " selected='selected' ";
	
					$ret .= ">".iconv('cp1251','utf-8',$v)."</option>\n";
					}
				$ret .= "</select>\n";
	
				$ret .= "<select name=\"date[year]\" id=\"date_year\">\n";
				foreach($years as $k=>$v)
					{
					$ret .= "<option value='".$k."'";
	
					if($v == $years[@date('Y', $time)]) $ret .= " selected='selected' ";
	
					$ret .= ">".$v."</option>\n";
					}
				$ret .= "</select>\n";
			break;
	
			case 'string_long':
				$ret = $weeks[0][$lang_name][date('0N', $time)].', '.$days[date('d', $time)].' '.$mons[1][$lang_name][date('m', $time)].' '.$years[date('Y', $time)];
			break;
			
			case 'string_long_2':
				//list()
				list($nowDay,$nowMonth,$nowYear,$nowHour,$nowMins) = explode(',', date("d,m,Y,H,i"));
				list($timeDay,$timeMonth,$timeYear,$timeHour,$timeMins) = explode(',', date("d,m,Y,H,i", $time));
				
				if(intval($nowMonth) == intval($timeMonth) && intval($nowYear) == intval($timeYear)) {
					$dayShift = intval($nowDay)-intval($timeDay);
					if($dayShift == 1) {
						$ret = 'Вчера в ';
					} elseif($dayShift == 0) {
						$ret = 'Сегодня в ';
					} elseif($dayShift == -1) {
						$ret = 'Завтра в ';
					} else {
						$ret = "{$timeDay}.{$timeMonth}.$timeYear в ";
					}
				} else {
					$ret = "{$timeDay}.{$timeMonth}.$timeYear в ";
				}
				
				$ret .= "{$timeHour}:{$timeMins}";
			break;
	
			case 'string_all':
				$ret =  $days[date('d', $time)].' '.$mons[1][$lang_name][date('m', $time)].' '.$years[date('Y', $time)].' '.date('H', $time).':'.date('i', $time).':'.date('s', $time);
			break;
	
			case 'string_FY':
				$ret = $mons[0][$lang_name][date('m', $time)].' '.$years[date('Y', $time)];
			break;
	
			case 'string_DF':
				$ret = $days[date('d', $time)].' '.$mons[0][$lang_name][date('m', $time)].': ';
			break;
	
			case 'string_Y':
				$ret = $years[date('Y', $time)];
			break;
			
		}
	
		return $ret;
	}

	function text_process($text){
		$text = stripslashes($text);
		$text = htmlspecialchars_decode($text);
	
		return $text;
	}

	function pagenav($total, $limit, $page, $start_name="start", $extra_arg="", $offset=4, $tpl='default'){	
		$total = intval($total);
		$perpage = intval($limit);
		$current = intval($page*$perpage);
	
		
		
		$templates['default']['container'] = '<div class="pagination">%s</div>';
		$templates['default']['prev'] = '<a href="%s/">&laquo;</a>';
		$templates['default']['cur'] = '<span class="current">%d</span>';
		$templates['default']['item'] = '<a href="%s/">%d</a>';
		$templates['default']['next'] = '<a href="%s/">&raquo;</a>';
		$templates['default']['iter'] = '<span>...</span>';
		
	
		$templates['rc_theme']['container'] = '<ul class="categoryPagination">%s</ul>';
		$templates['rc_theme']['prev'] = '<li><a href="%s/" class="prevpage"> </a></li>';
		$templates['rc_theme']['cur'] = '<li><span>%d</span></li>';
		$templates['rc_theme']['item'] = '<li><a href="%s/">%d</a></li>';
		$templates['rc_theme']['next'] = '<li><a href="%s/" class="nextpage"> </a>';
		$templates['rc_theme']['iter'] = '<li>...</li>';
		
		$tpl = (!isset($templates[$tpl]))? 'default' : $tpl;
		
		
		
		
		$url = '/'.$extra_arg.'/'.trim($start_name).'/';
		$url = str_replace("//","/",$url);
		$offset = ($offset)? $offset:4;
		$ret = '';
	
		if ($total <= $perpage) return $ret;
	
		$total_pages = ceil($total / $perpage);
		
		if($total_pages > 1){
			$prev = ($current - $perpage)/$perpage;
			
			if($prev >= 0) $ret .= sprintf($templates[$tpl]['prev'], $url.$prev);
						
			$counter = 1;
			$current_page = intval(floor(($current + $perpage) / $perpage));
			
			while($counter <= $total_pages){
				if($counter == $current_page){
					$ret .= sprintf($templates[$tpl]['cur'], $counter);
				}elseif(($counter > $current_page-$offset && $counter < $current_page + $offset ) || $counter == 1 || $counter == $total_pages ){
					if($counter == $total_pages && $current_page < $total_pages - $offset) $ret .= $templates[$tpl]['iter'];	
					$ret .= sprintf($templates[$tpl]['item'], $url.(($counter - 1)), $counter);
					if($counter == 1 && $current_page > 1 + $offset) $ret .= $templates[$tpl]['iter'];
				}

				$counter++;
			}	
				
			$next = $current + $perpage;
			if ($total > $next) $ret .= sprintf($templates[$tpl]['next'], $url.($next)/$perpage);
		}
		
		$ret = sprintf($templates[$tpl]['container'], $ret);
		
		return $ret;
	}
	
	function ajax_pagenav($total, $limit, $page, $js_function, $extra_arg=""){
		$total = intval($total);
		$perpage = intval($limit);
		$current = intval($page*$perpage);
	
		global $core;
		
		$templates['default']['container'] = '<div class="pagination">%s</div>';
	//	$templates['default']['prev'] = '<a href="#">&laquo;</a>';
		$templates['default']['prev'] = '<span  onclick="'.$js_function.'(%d, \'%s\')" class="previous paginate_button paginate_button_disabled">Previous</span>';
		$templates['default']['cur'] = '<span class="current">%d</span>';
		$templates['default']['cur'] = '<span class="paginate_active">%d</span>';
		$templates['default']['item'] = '<a href="#" onclick="'.$js_function.'(%d, \'%s\')">%d</a>';
		$templates['default']['item'] = '<span onclick="'.$js_function.'(%d, \'%s\')" class="paginate_button">%d</span>';
	//	$templates['default']['next'] = '<a href="#" onclick="'.$js_function.'(%d, \'%s\')">&raquo;</a>';
		$templates['default']['next'] = '<span onclick="'.$js_function.'(%d, \'%s\')" class="next paginate_button">Next</span>';
		$templates['default']['iter'] = '<span class="paginate_button">...</span>';
		$templates['default']['offset'] = 4;
		
		$templates['wide']['container'] = '<div class="pagination">%s</div>';
		$templates['wide']['prev'] = '<a href="#" onclick="'.$js_function.'(%d, \'%s\')">&laquo;</a>';
		$templates['wide']['cur'] = '<span class="current">%d</span>';
		$templates['wide']['item'] = '<a href="#" onclick="'.$js_function.'(%d, \'%s\')">%d</a>';
		$templates['wide']['next'] = '<a href="#" onclick="'.$js_function.'(%d, \'%s\')">&raquo;</a>';
		$templates['wide']['iter'] = '<span>...</span>';
		$templates['wide']['offset'] = 4;
	
		$templates['constell']['container'] = '<ul class="controls-buttons">%s</ul>';
		$templates['constell']['prev'] = '<li><a href="#" onclick="'.$js_function.'(%d, \'%s\')" title="Предыдущая страница"><img src="/templates/constell/images/icons/fugue/navigation-180.png" width="16" height="16"></a></li>';
		$templates['constell']['cur'] = '<li><a href="#" title="" class="current"><b>%d</b></a></li>';
		$templates['constell']['item'] = '<li><a href="#" onclick="'.$js_function.'(%d, \'%s\')" title=""><b>%d</b></a></li>';
		$templates['constell']['next'] = '<li><a href="#" onclick="'.$js_function.'(%d, \'%s\')" title="Следующая страница"><img src="/templates/constell/images/icons/fugue/navigation.png" width="16" height="16"></a></li>';
		$templates['constell']['iter'] = '<li>...</li>';
		$templates['constell']['offset'] = 4;
		
		$templates['rc_theme']['container'] = '<ul class="categoryPagination">%s</ul>';
		$templates['rc_theme']['prev'] = '<li><a href="javascript:void(0)" onclick="'.$js_function.'(%d, \'%s\')" class="prevpage"> </a></li>';
		$templates['rc_theme']['cur'] = '<li><span>%d</span></li>';
		$templates['rc_theme']['item'] = '<li><a href="javascript:void(0)" onclick="'.$js_function.'(%d, \'%s\')">%d</a></li>';
		$templates['rc_theme']['next'] = '<li><a href="javascript:void(0)" onclick="'.$js_function.'(%d, \'%s\')" class="nextpage"> </a>';
		$templates['rc_theme']['iter'] = '<li>...</li>';
		$templates['rc_theme']['offset'] = 4;
		
		$offset = $templates[$core->theme]['offset'];
		$ret = '';
	
		if ($total <= $perpage) return $ret;
	
		$total_pages = ceil($total / $perpage);
		
		if($total_pages > 1){
			$prev = ($current - $perpage)/$perpage;
			if($prev >= 0) $ret .= sprintf($templates[$core->theme]['prev'], $prev, $extra_arg);
			$counter = 1;
			$current_page = intval(floor(($current + $perpage) / $perpage));
			
			while($counter <= $total_pages){
				if($counter == $current_page){
					$ret .= sprintf($templates[$core->theme]['cur'], $counter);
				} elseif(($counter > $current_page-$offset && $counter < $current_page + $offset ) || $counter == 1 || $counter == $total_pages ){
					if($counter == $total_pages && $current_page < $total_pages - $offset) $ret .= $templates[$core->theme]['iter'];
					$ret .= sprintf($templates[$core->theme]['item'], ($counter - 1), $extra_arg, $counter);
					if($counter == 1 && $current_page > 1 + $offset) $ret .= $templates[$core->theme]['iter'];
				}
				
				$counter++;
			}	
				
			$next = $current + $perpage;
			if ($total > $next) $ret .= sprintf($templates[$core->theme]['next'], $next/$perpage, $extra_arg);
		}
		
		$ret = sprintf($templates[$core->theme]['container'], $ret);
		return $ret;
	}

	function correct_data($type, $data){
		switch($type)
		{
			case 'string':
			case 's':
				return addslashes($data);
			break;
	
			case 'int':
			case 'i':
				return intval($data);
			break;
	
			case 'float':
			case 'f':
				return floatval($data);
			break;
		}
	
	}

	function get_all_langs(){
		global $core;
	
		$cache_var = $core->tpl->get_cache_var_name('dll.api::get_all_langs', array('_site'=>true, '_editor'=>true, '_user_id'=>true, '_lang'=>true));
		
		if(!$langs = $core->memcache->get($cache_var))
    	{
			$sql = $core->db->select()->from('mcms_language')
							   ->fields()
							   ->where('visible_in_menu = 1')
							   ->order('`order`');  
							   
			$core->db->execute();
			
			while($row = mysql_fetch_assoc($core->db->result))
				$langs[$row['id']] = $row;
    		
			if($core->site_id == 4) $core->memcache->add($cache_var, $langs, false, 14*24*60*60);
			if($core->site_id != 4) $core->memcache->add($cache_var, $langs, false, 10*60);
    	}
				
		return $langs;
	}

	function strip_2_slashes($text){
		$text = stripslashes($text);
		$text = stripslashes($text);
	
		return $text;
	}

	function htmlencodeANDstrip($text){
   		return htmlspecialchars(stripslashes($text));
	}

	function delete_html_tags($text){
	    $text = preg_replace("'<[\/\!]*?[^<>]*?>'si", '', $text);  
	
		  
		 $search = array('<font color=green>',
		                 '<b>',
		                 '</b>',
		                 '</font>',
		                 '<span style="color: #d37a26;">',
		                 '</span>',
		                 '<span style="color: #5aa000;">',
		                 ',',' "', '" ');
		 $replacement = array('','','','','','','','', "�", "�");
		     
		 return str_replace($search, $replacement, $text);
	}

	function js_text_replace_escape($text){	
	    
	    return addslashes(delete_html_tags(str_replace(array('"', ';', ','), array('', '&#037e;', '&sbquo;'), $text)));
	     
	}

	function utf8_urldecode($str){
	    $str =  preg_replace_callback('/%u([0-9a-f]{4})/i',create_function('$arr','return chr(hexdec($arr[1]));'),$str);
	
	    return $str;
	}

	function MaxId($value,$table){
		global $core;
		$core->db->query("SELECT MAX(`".$value."`) AS id FROM `".$table."`");

		return $core->db->get_field();
	}

	function get_all_sites(){
		global $core;

		$core->db->select()->from('mcms_sites')->fields('id', 'name',	'server_name', 'server_alias', 'describe');
		$core->db->execute();
		$core->db->get_rows();

		return $core->db->rows;
	}

	function &array_recursive_finder($key, &$form) {
		if (array_key_exists($key, $form)) {
    		$ret =& $form[$key];
    		return $ret;
  		}
  			
  		foreach($form as $k => $v) {
    		if (is_array($v)) {
      			$ret =& array_recursive_finder($key, $form[$k]);
      			if ($ret) {
       	 			return $ret;
      			}
    		}
  		}
 		return FALSE;
	}	
	
	function string2assocArray($string, $separate = ','){	
        $get = array();
        $k = 0;
        
        foreach(explode($separate,$string) as $item){
                if($k){
                    $get[$k] = $item;
                    $k = 0;
                } else {
                        $get[$item] = NULL;
                        $k = $item;
                }   
        }
            
        return $get;
	}

	function space2nbsp($str){		
		return str_replace(' ', '&nbsp;', $str);
	}

	
	function utf8($str, $character = 'cp1251'){
		return iconv($character, 'utf-8', $str);
	}
	
	function encodestring($st){
		$cyr = array(
				'ж',  'ч',  'щ',   'ш',  'ю',  'а', 'б', 'в', 'г', 'д', 'е', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ъ', 'ь', 'я','ы','э',
				'Ж',  'Ч',  'Щ',   'Ш',  'Ю',  'А', 'Б', 'В', 'Г', 'Д', 'Е', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ъ', 'Ь', 'Я','Ы','Э');
		$lat = array(
				'zh', 'ch', 'sht', 'sh', 'yu', 'a', 'b', 'v', 'g', 'd', 'e', 'z', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'y', '', 'ya','i','e',
				'Zh', 'Ch', 'Sht', 'Sh', 'Yu', 'A', 'B', 'V', 'G', 'D', 'E', 'Z', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'c', 'Y', '', 'ya','I','E');
		
		return str_replace($cyr, $lat, $st);
	}
	
	function get_formated_file_size($size){
		if($size < 1024) return $size.' b';
		if($size < 1024*1024) return round($size/1024, 2).' Kb';
		if($size < 1024*1024*1024) return round($size/1024/1024, 2).' Mb';
		if($size < 1024*1024*1024*1024) return round($size/1024/1024/1024, 2).' Gb';
		if($size < 1024*1024*1024*1024*1024) return round($size/1024/1024/1024, 2).' Tb';
	}

	function decode_unicode_url($str){
	  $res = '';
	
	  $i = 0;
	  $max = strlen($str) - 6;
	  while ($i <= $max){
	    $character = $str[$i];
	    if (($character == '%' && $str[$i + 1] == 'u') || ($character == '\\' && $str[$i + 1] == 'u')){
	      $value = hexdec(substr($str, $i + 2, 4));
	      $i += 6;
	
	      if ($value < 0x0080) // 1 byte: 0xxxxxxx
	        $character = chr($value);
	      else if ($value < 0x0800) // 2 bytes: 110xxxxx 10xxxxxx
	        $character =
	            chr((($value & 0x07c0) >> 6) | 0xc0)
	          . chr(($value & 0x3f) | 0x80);
	      else // 3 bytes: 1110xxxx 10xxxxxx 10xxxxxx
	        $character =
	            chr((($value & 0xf000) >> 12) | 0xe0)
	          . chr((($value & 0x0fc0) >> 6) | 0x80)
	          . chr(($value & 0x3f) | 0x80);
	    } else
	      $i++;
	
	    $res .= $character;
	  }
	
	  return $res . substr($str, $i);
	}

	function formatTextInCOunter($count, $one, $two='', $many=''){
		if(is_array($one)){
			$two = $one[1];
			$many = $one[2];
			$one = $one[0];	
		}
		
		if($count == 1) return $one;
		if($count == 0) return $many;
		if($count >= 2 && $count <= 4) return $two;
		if($count >= 5 && $count <= 20) return $many;

		if($count > 20){
			$count += '';
			return formatTextInCOunter(substr($count, -1), $one, $two, $many);	
		}
	}

	function utf8_strlen($str){
	    $count = 0;
	
	    for($i = 0; $i < strlen($str); $i++) {
	        $value = ord($str[$i]);
	        if($value > 127){
	            if($value >= 192 && $value <= 223)
	                $i++;
	            elseif($value >= 224 && $value <= 239)
	                $i = $i + 2;
	            elseif($value >= 240 && $value <= 247)
	                $i = $i + 3;
	            else
	                die('Not a UTF-8 compatible string');
	        }
	      
	        $count++;
	    }
	  
	    return $count;
	}
	
	function lastday($month = '', $year = '', $format='d/m/Y') {
	   if (empty($month)) {
	      $month = date('m');
	   }
	   if (empty($year)) {
	      $year = date('Y');
	   }
	   $result = strtotime("{$year}-{$month}-01");
	   $result = strtotime('-1 second', strtotime('+1 month', $result));
	   return date($format, $result);
	}
	
	function curentdaystamp($from_start = true,$format='d.m.Y',$separate='.', $date = false) {
	   $date = ($date)? $date: date($format);
		
	   $date = explode($separate, date($format));
	   $time = ($from_start)? mktime(0,0,1, $date[1], $date[0], $date[2]) : mktime(23,59,59, $date[1], $date[0], $date[2]);
	   return $time;
	}
	
	function isajaxrequest(){
		if(!empty($_GET["rs"])) $mode = "get";		
		if(!empty($_POST["rs"])) $mode = "post";	
		if(empty($mode)) return false;
		if($mode == "get" && !isset($_GET['rsobj'])) return false; 
		if($mode == "post" && !isset($_POST['rsobj'])) return false; 	

		return true;
	}
	
	function escape_js_val($val){
		$val = stripslashes($val);
		$val = str_replace("\\", "\\\\", $val);
		$val = str_replace("\r", "\\r", $val);
		$val = str_replace("\n", "\\n", $val);
		$val = str_replace("'", "\\'", $val);
		return str_replace('"', '\\"', $val);
	}
	
	function bugalterVolumeParse($vol){
		$vol = floatval($vol);
		
		if(is_float($vol))
		{
			$vol = explode('.',$vol.'');
			$ret = $vol[0].'-';
			$ret .= (strlen($vol[1]) == 1)? $vol[1].'0' : $vol[1];
			
			return $ret;
		}
		
		return $vol.'-'.'00';
	}

	
  	function num2str($inn, $stripkop=false) 
  	{
       $nol = 'ноль';
       $str[100]= array('','сто','двести','триста','четыреста','пятьсот','шестьсот', 'семьсот', 'восемьсот','девятьсот');
       $str[11] = array('','десять','одиннадцать','двенадцать','тринадцать', 'четырнадцать','пятнадцать','шестнадцать','семнадцать', 'восемнадцать','девятнадцать','двадцать');
       $str[10] = array('','десять','двадцать','тридцать','сорок','пятьдесят', 'шестьдесят','семьдесят','восемьдесят','девяносто');
       $sex = array(
           array('','один','два','три','четыре','пять','шесть','семь', 'восемь','девять'),// m
           array('','одна','две','три','четыре','пять','шесть','семь', 'восемь','девять') // f
       );
       $forms = array(
           array('копейка', 'копейки', 'копеек', 1), // 10^-2
           array('рубль', 'рубля', 'рублей',  0), // 10^ 0
           array('тысяча', 'тысячи', 'тысяч', 1), // 10^ 3
           array('миллион', 'миллиона', 'миллионов',  0), // 10^ 6
           array('миллиард', 'миллиарда', 'миллиардов',  0), // 10^ 9
           array('триллион', 'триллиона', 'триллионов',  0), // 10^12
       );
       $out = $tmp = array();
       // Поехали!
      $tmp = explode('.', str_replace(',','.', $inn));
       $rub = number_format($tmp[ 0], 0,'','-');
       if ($rub== 0) $out[] = $nol;
       // нормализация копеек
       $kop = isset($tmp[1]) ? substr(str_pad($tmp[1], 2, '0', STR_PAD_RIGHT), 0,2) : '00';
       $segments = explode('-', $rub);
       $offset = sizeof($segments);
       if ((int)$rub== 0) { // если 0 рублей
           $o[] = $nol;
           $o[] = morph( 0, $forms[1][ 0],$forms[1][1],$forms[1][2]);
      }
       else {
           foreach ($segments as $k=>$lev) {
               $sexi= (int) $forms[$offset][3]; // определяем род
               $ri = (int) $lev; // текущий сегмент
               if ($ri== 0 && $offset>1) {// если сегмент==0 & не последний уровень(там Units)
                   $offset--;
                   continue;
               }
               // нормализация
               $ri = str_pad($ri, 3, '0', STR_PAD_LEFT);
               // получаем циферки для анализа
               $r1 = (int)substr($ri, 0,1); //первая цифра
               $r2 = (int)substr($ri,1,1); //вторая
               $r3 = (int)substr($ri,2,1); //третья
               $r22= (int)$r2.$r3; //вторая и третья
               // разгребаем порядки
               if ($ri>99) $o[] = $str[100][$r1]; // Сотни
               if ($r22>20) {// >20
                   $o[] = $str[10][$r2];
                   $o[] = $sex[ $sexi ][$r3];
               }
               else { // <=20
                   if ($r22>9) $o[] = $str[11][$r22-9]; // 10-20
                   elseif($r22> 0) $o[] = $sex[ $sexi ][$r3]; // 1-9
               }
               // Рубли
               $o[] = morph($ri, $forms[$offset][ 0],$forms[$offset][1],$forms[$offset][2]);
               $offset--;
           }
       }
       // Копейки
       if (!$stripkop) {
           $o[] = $kop;
           $o[] = morph($kop,$forms[ 0][ 0],$forms[ 0][1],$forms[ 0][2]);
       }
       
       return mb_ucfirst(preg_replace("/\s{2,}/",' ',implode(' ',$o)));
   }
    
	function morph($n, $f1, $f2, $f5) 
	{
		$n = abs($n) % 100;
		$n1= $n % 10;
		if ($n>10 && $n<20) return $f5;
		if ($n1>1 && $n1<5) return $f2;
		if ($n1==1) return $f1;
		return $f5;
	}

	if (!function_exists('mb_ucfirst') && function_exists('mb_substr')) {
     function mb_ucfirst($string) {  
          $string = mb_ereg_replace("^[\ ]+","", $string);  
          $string = mb_strtoupper(mb_substr($string, 0, 1, "UTF-8"), "UTF-8").mb_substr($string, 1, mb_strlen($string), "UTF-8" );  
          return $string;  
     }  
	}
	
	function clearNullResult($text) {
	    if(!$text) return '-';
	    return $text;
	}
?>