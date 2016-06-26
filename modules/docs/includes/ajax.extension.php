<?php
	
	function get_templates_list($start=0, $order='name_module', $filter = '', $filter_value = '')
	{
		global $core;
	
		$limit = 10;
	
		if($filter && $filter_value) $core->tpl->assign('select_filter', array('name'=>$filter, 'value'=>$filter_value));
	
		$templates_list = admin_docs::get_templates_list($start, $limit, $order, $filter, $filter_value);
		$core->tpl->assign('templates_list', $templates_list);
	
		$pagenav = $core->ajax_pagenav(admin_docs::get_total_templates_list($filter, $filter_value), $limit, $start, 'update_templates_list');
	
		$core->tpl->assign('pagenav', $pagenav);
		$core->tpl->assign('tpl_modules', admin_docs::get_module_list());
	
		return $core->tpl->fetch('temp_list.html');
	}
	
	function get_blocks_list()
	{
		global $core;
	
		$dir = $core->CONFIG['path_lib'].'/';
	
		foreach(scandir($dir) as $file_name)
		{
		if(substr($file_name, 0, 5) == 'block')
			{
			$files[] = array('name'=>$file_name);
			}
		}
	
		$core->tpl->assign('info_blocks', $files);
	
		return $core->tpl->fetch('block_list.html', 1, 0, 0, 'docs');
	}
	
	function update_draged_item($id_doc, $new_parent_id, $before_node, $before_tr_down = 0)
	{
		if(!intval($id_doc)) return false;
	
		global $core;
		
		if(intval($before_tr_down) > 0 && $before_tr_down != 'undefined')
		{
		$before_node = intval($before_tr_down);
		}
		
		
		if(intval($before_node))
		{
		$sql0 = "SELECT d1.order as `curent`, (SELECT d2.order FROM mcms_docs as d2 WHERE d2.id_doc = ".intval($before_node)." LIMIT 1) as `before` FROM mcms_docs as d1 WHERE d1.id_doc =".intval($id_doc)." LIMIT 1";
		$core->db->query($sql0);
		$core->db->get_rows(1);
		
		$order = $core->db->rows;
		
		$sql1 = "UPDATE `mcms_docs` SET `parent_id` = ".intval($new_parent_id).", `order` = ".$order['before']." WHERE id_doc = ".intval($id_doc);
		$core->db->query($sql1);
		
		$sql2 = "UPDATE `mcms_docs` SET `order` = ".$order['curent']." WHERE id_doc = ".intval($before_node);
		$core->db->query($sql2);
		
		return $sql1.' ------- '.$sql2.' ---- '.$sql0;
		}
		else
			{
			$sql = "SELECT MAX(d2.order) FROM mcms_docs as d2 WHERE d2.parent_id = ".intval($new_parent_id);	
			$core->db->query($sql);
	
			$order = $core->db->get_field() +1;
			
			$sql = "UPDATE `mcms_docs` SET `parent_id` = ".intval($new_parent_id).", `order` = ".$order." WHERE id_doc = ".intval($id_doc);
			$core->db->query($sql);
			
			return $sql;
			}
		
		return true;
	}
	
	function update_name_item($id_doc, $name)
	{
		if(!intval($id_doc)) return false;
	
		global $core, $ajax;
	
		$sql = "UPDATE `mcms_docs` SET `title` = '".addslashes($name)."' WHERE id_doc = ".intval($id_doc).' AND lang_id = '.$core->CONFIG['lang']['id'];
		$core->db->query($sql);
	
		return true;
	}
	
	function get_modules_list($parent_id)
	{
		global $core;
	
		$core->tpl->assign('list_modules', admin_docs::get_modules_list(1));
		$core->tpl->assign('item_parent_id', $parent_id);
	
		$html = $core->tpl->fetch('modules_list.html', 1, 0, 0, 'docs');
	
		return $html;
	}
	
	function insert_doc_module($id_module, $parent_id)
	{
		global $core;
	
		$id_doc = admin_docs::generate_doc_id();
		$module_info  = admin_docs::get_module_descr($id_module);
	
	
		foreach($core->get_all_langs() as $lang)
			$data[] = array('id_doc'=>$id_doc, 'id_site'=>$core->edit_site, 'del'=>0, 'user_id'=>$core->user->id, 'title'=>$module_info[$lang['id']], 'type' => 2, 'type_value'=>$id_module, 'lang_id'=>$lang['id'], 'parent_id'=>$parent_id, 'text'=>'', 'anonce'=>'', 'more_block'=>'', 'meta_title'=>'', 'meta_desc'=>'', 'meta_keyw'=>'' );
	
		$core->db->autoupdate()->table('mcms_docs')->data($data);
		$core->db->execute();
	
		return true;
	}
	
	function get_window_add_simple_block($id_doc)
	{
		global $core;
		
		$sql = "SELECT d.lang_id, d.title FROM mcms_docs as d WHERE d.id_doc = ".intval($id_doc);
		$core->db->query($sql);
		$core->db->get_rows();
		
		$doc_titles = $core->db->rows;
		
		$langs = $core->get_all_langs();
		
		$core->tpl->assign('langs_simple_link', $langs);
		$core->tpl->assign('doc_id_simple_link', $id_doc);
		
		$html = $core->tpl->fetch('add_simple_link.html', 1, 0, 0, 'docs');
	
		return $html;
	}
	
	function get_window_add_shadow_block($id_doc)
	{
		global $core;
		
		$sql = "SELECT d.lang_id, d.title FROM mcms_docs as d WHERE d.id_doc = ".intval($id_doc);
		$core->db->query($sql);
		$core->db->get_rows();
		
		$doc_titles = $core->db->rows;
		
		$langs = $core->get_all_langs();
		
		foreach($doc_titles as $row)
		{
			if(isset($langs[$row['lang_id']])) $langs[$row['lang_id']]['title_s'] = $row['title'];
		}
	
		$core->tpl->assign('langs_simple_link', $langs);
		$core->tpl->assign('doc_id_simple_link', $id_doc);
		
		$html = $core->tpl->fetch('add_simple_link.html', 1, 0, 0, 'docs');
	
		return $html;
	}
	
	function save_simple_link($titles, $links, $parent_id)
	{
		global $core;
		
		if(!$titles) return false;
		if(!$links) return false;
		if(!intval($parent_id)) return false;
		
		$links = explode(",", $links);
		
		foreach($links as $link_str)
		{
		    $out_links = explode('^', $link_str);
		    
		    if($out_links[1]) $rewrite_id[$out_links[0]] = $core->url_parser->add(addslashes($out_links[1]), addslashes($out_links[1]), 'Cross');
		}
		
	    
		
		$id_doc = $core->MaxId('id_doc', 'mcms_docs')+1;
		$sql = "SELECT MAX(`order`) FROM `mcms_docs` WHERE `parent_id` = ".$id_doc;
		$core->db->query($sql);
		$order = $core->db->get_field()+10;
		
		$titles = explode(",", $titles);	
		
		foreach($titles as $title_str)
		{
		  $title_arr = explode('^', $title_str);
		  if($title_arr[1])
		      {
		      $lang_id = intval($title_arr[0]);
		      $title = addslashes($title_arr[1]);
		      $data[] = array('id_doc'=>$id_doc, 'title'=>$title, 'rewrite_id'=>$rewrite_id[$lang_id], 'parent_id'=>intval($parent_id), 'user_id'=>$core->user->id, 'type'=>4,'id_site'=>$core->edit_site, 'lang_id'=>$lang_id, 'order'=>$order);
		      }    
		}
		
		
		$core->db->autoupdate()->table('mcms_docs')->data($data);
		$core->db->execute();
		
		return true;
	}
	
	function change_view_lang($lang_id)
	{
	    $_SESSION['docs_view_lang'] = intval($lang_id);
	    return true;
	}
	
	function get_types_select_html()
	{
		global $core;
		
		$types = $core->multidb->getTypes();
		
		if(count($types) == 1)
		{
			$typeOne = array_shift($types);
			
			return $typeOne['id'];
		}
		
		$core->tpl->assign('types_list', $types);
		$html = $core->tpl->fetch('types.list.html');
	
		
		return $html;
	}
	
	function upload_image_catalog($realFileName, $tmpFileName)
	{
		global $core;
		
		
		$uploadPath = $core->CONFIG['file_path'].'images/';
		$localPath = '/vars/files/images/';
		
		$core->lib->load('images');
				
		$realFile = $core->CONFIG['temp_path'].'catalog_image-'.md5(time().microtime()).substr($realFileName, -4);

		if(!@rename($core->CONFIG['temp_path'].$tmpFileName, $realFile))
		{
			unlink($core->CONFIG['temp_path'].$tmpFileName);
			return false;
		}
		
		if(!images::checkMime($realFile))
		{
			unlink($realFile);
			return false;
		}
		
		$core->images = new images($uploadPath, $uploadPath, $localPath, $localPath, 2);
		$core->images->fname = $realFile;
				
		if(!$core->images->open())
		{
			unlink($realFile);
			return false;			
		}
				
		$core->images->resize('50', '50', $realFile, true, 'h');
		
		$core->images->close(false);
		$core->images->open();
		$core->images->updateSize();
		$core->images->saveFromFile(1, $realFileName);
		
		$info = array('file'=>$core->images->info['local_filename'], 'id'=>$core->images->id);
				
		if(!$info) 
		{
			unlink($realFile);
			return false;
		}
		
		return $info;		
	}
	
	
	
?>