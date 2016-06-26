<?php 

class admin_wpblog extends main_module {


	public function getPostList($start = 0, $limit = 5, $filters = false) {
		//$this->db->connec
		$sql = "SELECT
					SQL_CALC_FOUND_ROWS
					p.*,
					p.post_content as post_teaser,
					u.display_name as user_name,
					(SELECT p2.guid FROM wp_posts as p2 WHERE p2.post_parent = p.ID AND p2.post_type = 'attachment' ORDER BY p2.ID LIMIT 1) as illustration,
					DATE_FORMAT(p.post_date, '%d') as create_day,
					DATE_FORMAT(p.post_date, '%c') as create_month,
					UNIX_TIMESTAMP(p.post_date) as timestmp,
					UNIX_TIMESTAMP(p.post_date) as finedatewtime
				FROM
					wp_posts as p
						LEFT JOIN wp_users as u ON u.ID = p.post_author
				WHERE
					p.post_status = 'publish' AND
					p.post_type = 'post' ";
		
		
		$oneEntry = false;
		
		if($filters && is_array($filters)) {
			if(!empty($filters['post'])) {
				$oneEntry = true;
				$sql .= " AND p.ID = ".intval($filters['post']);
			}
			
			if(!empty($filters['topic'])) {
				$oneEntry = false;
				$topicId = intval($filters['topic']);
				$sql .= " AND p.ID IN(SELECT tr.object_id FROM wp_term_relationships as tr LEFT JOIN wp_term_taxonomy as tt ON tr.term_taxonomy_id = tt.term_taxonomy_id WHERE tt.term_id = {$topicId}) ";
			}
		}
		
		$sql .= " ORDER BY p.post_date DESC LIMIT $start,$limit";
		
	
		
		$this->db->query($sql,'db_blog');
		$this->db->add_fields_deform(array('post_content',  'post_teaser', 'create_month', 'timestmp', 'finedatewtime'));
		$this->db->add_fields_func(array('admin_wpblog::clearFirstIllustration',  'admin_wpblog::wp_trim_words', 'admin_wpblog::getMonthNameOfNum',  'admin_wpblog::getFineDate',  'admin_wpblog::getFineDateTime'));
		$this->db->get_rows($oneEntry);
		
		return $this->db->rows;
	}
	
	public function getTopicList() {
		$sql = "SELECT
					t.term_id as id,
					t.name
				FROM
					wp_terms as t
						LEFT JOIN wp_term_taxonomy as tt ON t.term_id = tt.term_id
				WHERE
					tt.taxonomy = 'category'
				ORDER BY
					t.name ";
		
		//echo $sql;
		
		$this->db->query($sql,'db_blog');
		$this->db->get_rows();
		
		return $this->db->rows;
	}
	
	public function getPostCategory($postId) {
		$postId = intval($postId);
		$sql = "SELECT t.term_id as id, t.name FROM wp_terms as t LEFT JOIN wp_term_taxonomy as tt ON t.term_id = tt.term_id LEFT JOIN wp_term_relationships as tr ON tt.term_taxonomy_id = tr.term_taxonomy_id WHERE tt.taxonomy = 'category' AND tr.object_id = {$postId}";
		$this->db->query($sql,'db_blog');
		$this->db->get_rows();
		
		return $this->db->rows;
	}
	
	public function getPost($id) {
		$data = $this->getPostList(0, 1, array('post'=>$id));
		$data['categories'] = $this->getPostCategory($id);
		
		return $data;
	}
	
	
	
	public static function getMonthNameOfNum($num) {
		$month = array('Янв.','Фев.','Мар.','Апр.','Мая.','Июн.','Июл.','Авг.','Сен.','Окт.','Ноя.','Дек.');
		return $month[intval($num)-1];
	}
	
	public static function getFineDate($data) {
		$month = array('Января','Февраля','Марта','Апреля','Мая','Июня','Июля','Августа','Сентября','Октября','Ноября','Декабря');
		
		return date('d', $data) .' '. $month[intval(date("m", $data))-1]. ' '. date("Y", $data);
	}
	
	public static function getFineDateTime($data) {
		$month = array('Января','Февраля','Марта','Апреля','Мая','Июня','Июля','Августа','Сентября','Октября','Ноября','Декабря');
	
		return date('d', $data) .' '. $month[intval(date("m", $data))-1]. ' '. date("Y", $data).' в '.date('H:i');
	}
	
	public static function clearFirstIllustration($text) {
		$text = stripslashes(nl2br(trim($text)));
		
		if(preg_match("/^\<a.+?\>\<img.+?\/\>\<\/a\>/", $text, $match)) {
			$text = str_replace($match, '', $text);
		}
		
		return $text;
	}
	
	public static function wp_trim_words( $text, $num_words = 55, $more = null ) {
		$original_text = $text;
		$text = strip_tags( $text );
		
		/*
		if ( 'characters' == _x( 'words', 'word count: words or characters?' ) && preg_match( '/^utf\-?8$/i', get_option( 'blog_charset' ) ) ) {
			$text = trim( preg_replace( "/[\n\r\t ]+/", ' ', $text ), ' ' );
			preg_match_all( '/./u', $text, $words_array );
			$words_array = array_slice( $words_array[0], 0, $num_words + 1 );
			$sep = '';
		} else {*/
		
			$words_array = preg_split( "/[\n\r\t ]+/", $text, $num_words + 1, PREG_SPLIT_NO_EMPTY );
			$sep = ' ';
		//}
		
		if ( count( $words_array ) > $num_words ) {
			array_pop( $words_array );
			$text = implode( $sep, $words_array );
			$text = $text . $more;
		} else {
			$text = implode( $sep, $words_array );
		}

		return $text;
	}
	




























}

if(!function_exists('h_date')) {
	function h_date($date){
		return ($date)? date('d.m.Y в H:i',$date) : 'не установлено';
	}
}



?>