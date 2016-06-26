<?php

class client_reviews
{
	public function __construct()
	{
		// Empty
	}

	public function get_topic_list()
	{
		global $core;

		$sql = 'SELECT 
						p.id id, 
						p.date date, 
						p.user_id user_id, 
						p.title title, 
						p.img img,
						p.link link,
						IF (LENGTH(p.text)>180, CONCAT(SUBSTRING_INDEX(SUBSTR(p.text,1,180)," ", 110), "..."), p.text) text,
						c.name_first name_first,
						c.name_last name_last 
				FROM
						press_posts p,
						sv_clients c
				WHERE 
						c.user_id = p.user_id
				ORDER BY 
						p.id DESC';
        $core->db->query($sql);
        $core->db->add_fields_deform(array ('date'));
		$core->db->add_fields_func(array ('custom_date'));
		//print_r($core->db->get_rows());
		return $core->db->get_rows();
	}
	
    public function update_post($info)
	{
		global $core;
		//print_r($info);
		//$info = json_decode($info, true);
		//print_r($info);
		 //die('dsd');
		if($core->user->id != 1) return false;
		
		$title = mysql_real_escape_string($info['title']);
		$text = mysql_real_escape_string($info['text']);
		$link = mysql_real_escape_string($info['link']);
		//$id = intval($id);
		
		$item = array();
		
		$item['date'] = time();
		$item['user_id'] = $core->user->id;
		$item['title'] = $title;
		$item['text'] = $text;
		$item['img'] = $info['img'];
		$item['link'] = $link;
		
		//print_r($item);
		
		if($id && $core->user->id == 1){
		    $item['id'] = $id;
            $core->db->autoupdate()->table('press_posts')->data(array($item))->primary('id');
		}else{
		    $core->db->autoupdate()->table('press_posts')->data(array($item));
		}
		
		$core->db->execute();
	}
	
    public function get_entry($id = false, $br = true)
	{
		global $core;
		
		$id = intval($id);

		$core->db->select()->from('press_posts p', 'sv_clients c')->fields(
                                                						'p.id id', 
                                                						'p.date date', 
                                                						'p.user_id user_id', 
                                                						'p.title title', 
                                                						'p.text text', 
																		'p.img img',
																		'p.link link',
                                                						'c.name_first name_first', 
                                                						'c.name_last name_last '
        )->where('p.id = '.$id.' AND c.user_id = p.user_id');
        
        
		$core->db->execute();
		if($br){
		    $core->db->add_fields_deform(array ('date','text'));
		    $core->db->add_fields_func(array ('custom_date','nl2br'));
	    }else{
	        $core->db->add_fields_deform(array ('date'));
		    $core->db->add_fields_func(array ('custom_date'));
	    }
		$topic = $core->db->get_rows(1);
		
		if($core->db->num_rows()>0){
    		$core->db->select()->from('press_comments p', 'sv_clients c')->fields(
                                                    					'p.id id', 
                                                						'p.date date', 
                                                						'p.user_id user_id', 
                                                						'p.text text', 
                                                						'c.name_first name_first', 
                                                						'c.name_last name_last '
    		)->where('p.post_id = '.$id.' AND c.user_id = p.user_id');
    		
    		$core->db->execute();
    		$core->db->add_fields_deform(array ('date', 'text'));
		    $core->db->add_fields_func(array ('custom_date','nl2br'));
    		$topic['comments'] = $core->db->get_rows();

    		return $topic;
		}
		
		return false;
	}
	
    public function create_comment($text = '', $post_id = false, $comment_id = false)
	{
		global $core;
        if($core->user->id == 2) return false;
        
		$post_id = intval($post_id);
		
		if(!$post_id) return false;
		
		$text = mysql_real_escape_string($text);
			
		$item = array();
		
		$item['date'] = time();
		$item['user_id'] = $core->user->id;
		$item['post_id'] = $post_id;
		$item['text'] = $text;
		
		$core->db->autoupdate()->table('press_comments')->data(array($item));
		$core->db->execute();
	}
	
	public function get_comment($id = false){
	    global $core;
	    $core->db->select()->from('press_comments p', 'sv_clients c')->fields(
                                                    					'p.id id', 
                                                						'p.date date', 
	    																'p.post_id post_id', 
                                                						'p.user_id user_id', 
                                                						'p.text text', 
                                                						'c.name_first name_first', 
                                                						'c.name_last name_last '
    		)->where('p.id = '.$id.' AND c.user_id = p.user_id');
    		
    	$core->db->execute();
    	$core->db->add_fields_deform(array ('date'));
	    $core->db->add_fields_func(array ('custom_date'));
	    return $core->db->get_rows(1);
	}

}

function custom_date($time){
    return date('d.m.Y', $time);
}


?>