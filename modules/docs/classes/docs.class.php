<?php
class admin_docs
{

	public $info = false;
	public $is_module = array();
	public $tree = array();
	public $id = 0;
	private $iterator = 0;

	public function __construct($id_doc = 0)
	{
		if ($id_doc) 
		{
			$info = $this->get_show_data($id_doc);

			if(!$info) return;

			global $core;

					$sql = "SELECT d.id_doc, title, (SELECT COUNT(*) FROM mcms_docs as d2 WHERE d2.parent_id = d.id_doc) as countChilds FROM  mcms_docs as d WHERE d.parent_id = {$id_doc} ORDER BY `order`";
					$core->db->query($sql);		
					$core->db->get_rows();
					$childNodes = $core->db->rows;

					$info['rootParentId'] = $this->getParentRootId($id_doc);

					if(count($childNodes))
					{
						foreach($childNodes as $k=>$child)
						{

							$_tmp_child = $this->get_show_data($child['id_doc'], 'title');
							$_tmp_child['childsCount'] = $child['countChilds'];	
							$_tmp_child['titleLenght'] = strlen($_tmp_child['title']);
							if($_tmp_child['date']) $_tmp_child['date'] = date('d.m.Y', $_tmp_child['date']);

							//echo $_tmp_child['title'].'<br>';
							$info['childs'][] = $_tmp_child;
						}
					}

				//print_r($info);

			if($info)
				{
				$this->is_module = $info['is_module'];
				$this->tree = $info['parent_id'];
				$this->info = $info;
				$this->id	= $id_doc;

				if($this->info['template_id']) $this->set_tpl_name(intval($this->info['template_id']));

				$this->set_doc_blocks($id_doc);
				}
				else
					{
					$this->info = false;
					}
			}
	}

	
	public function save($id_type)
	{
		global $core;

		$tpl_id = 0;
					
		if(isset($_POST['tpl_id']) && intval($_POST['tpl_id']))
		{
			$tpl_id = intval($_POST['tpl_id']);
			unset($_POST['tpl_id']);
		}

		if(isset($_POST['date']))
		{
			$date = mktime(date('H', time()), date('i', time()), 0, $_POST['date']['mon'], $_POST['date']['day'], $_POST['date']['year']);
			
			unset($_POST['date']);
		}
		else 	
			{
				$date = time();
				//$date = 0;
			}

		$visible = 1;
			
		if(isset($_POST['visible']) && ($_POST['visible'] == false || $_POST['visible'] == 'false' || $_POST['visible'] == '0'))
		{
			$visible = 0;

			unset($_POST['visible']);
		}
			
		$rewrite_id = 0;
		$realIdDoc = intval($_POST['id_doc']);
		if($realIdDoc) $returned = true;
				
		$id = ($realIdDoc)? $realIdDoc : $core->MaxId('id_doc', 'mcms_docs')+1;
					
		if(isset($_POST['rewrite']) || isset($_POST['rewrite_id']))
		{
			if(isset($_POST['rewrite_id']) && intval($_POST['rewrite_id']))
			{
				if(isset($_POST['rewrite']) && strlen($_POST['rewrite'])>2)
				{
					$core->url_parser->edit(intval($_POST['rewrite_id']), addslashes($_POST['rewrite']));
					$rewrite_id = intval($_POST['rewrite_id']);
				} 
				else
					{
						$core->url_parser->del(intval($_POST['rewrite_id']));
					}
			}
			else 	
				{
					if(isset($_POST['rewrite']) && strlen($_POST['rewrite'])>2)
					{
						$rewrite_id = $core->url_parser->add($_POST['rewrite'], '/pages/show/id/'.$id.'/');
					}
				}
		}
		
		
		unset($_POST['id_doc'], $_POST['typeValue'], $_POST['rewrite'], $_POST['rewrite_id']);
		
		if(isset($_POST['id']))
		{
			$core->multidb->delete($_POST['id']);
			unset($_POST['id']);
		}
		
		// TODO: Костыли бля.
		foreach($_POST as $key=>$val)
		{
			if(strlen($val) > 4 && is_string($val))
			{
				$_POST[$key] = addslashes($val);
			}
		}
		
		
		$idMultidb = $core->multidb->insertData($_POST, $id_type);
		
		if(!$idMultidb) die('MultiDB return error code. see /classes/docs.class.php line '.__LINE__);
		
		$core->lib->load('textprocessor');
		$txt = new textprocessor();
	
		$parent_id = (intval($_GET['parent']))? intval($_GET['parent']) : 0;
		$id_site = $core->edit_site;
		
		$sql = "SELECT MAX(`order`) FROM mcms_docs WHERE `id_site` = {$id_site} AND `parent_id`= {$parent_id}";
		$core->db->query($sql);
		
		$order = $core->db->get_field()+10;
		
		if($realIdDoc)
		{
			$data[] = array('id_doc'=>$id, 'visible'=>$visible, 'rewrite_id'=>$rewrite_id, 'template_id'=>$tpl_id, 'id_site'=>$core->edit_site, 'date'=>$date, 'user_id'=>$core->user->id, 'type'=>6, 'title'=>$_POST['title'], 'parent_id'=>$parent_id, 'type_value'=>$idMultidb, 'lang_id'=>$core->CONFIG['lang']['id']);
		}
		else 
			{
				$data[] = array('id_doc'=>$id, 'visible'=>$visible, 'rewrite_id'=>$rewrite_id, 'template_id'=>$tpl_id, 'id_site'=>$core->edit_site, 'date'=>$date,  'user_id'=>$core->user->id, 'type'=>6, 'title'=>$_POST['title'], 'parent_id'=>$parent_id, 'order'=>$order, 'type_value'=>$idMultidb, 'lang_id'=>$core->CONFIG['lang']['id']);
			}

		$core->db->autoupdate()->table('mcms_docs')->data($data)->primary('id_doc', 'lang_id');
		$core->db->execute();
	
		return $id;
	}

	public function get_tree_data($lang_id = 0)
	{
		global $core;
		
		if(!$lang_id) $lang_id = $core->CONFIG['lang']['id'];
				
		//echo $core;
		
		$sql = "SELECT doc.title, (SELECT rew.rewrite FROM mcms_rewrite as rew WHERE rew.id = doc.rewrite_id) as rewrite, doc.parent_id, (SELECT u.name FROM mcms_user as u WHERE u.id_user = doc.user_id) as creator,  doc.type, doc.type_value, doc.id_doc, doc.visible  FROM mcms_docs as doc WHERE doc.lang_id = ".$lang_id.' AND doc.id_site = '.$core->edit_site.'  ORDER BY doc.order';
		$core->db->query($sql);
		$core->db->add_fields_deform(array('title'));
		$core->db->add_fields_func(array('js_text_replace_escape'));
		$core->db->get_rows();
		
		
		return $core->db->rows;
	}

	public function get_templates_list($start, $limit, $order, $filter = '', $filter_value = '')
	{
		global $core;


		$order = ($order)? addslashes($order): 'name_module';

		if($filter && $filter_value && ($filter_value != 'all'))
		{
		$core->db->select()->from('mcms_tmpl')
				           ->fields('name_module', 'id_template', 'name', 'description', 'date')
						   ->order($order)
						   ->where('`'.addslashes($filter).'` = "'.addslashes($filter_value).'" AND id_site = '.$core->edit_site.' AND name != "main.html" AND `del` != 1')
						   ->lang('$curent')
						   ->limit($limit, $start);
		}
		else
			{
			$core->db->select()->from('mcms_tmpl')
							   ->fields('name_module', 'id_template', 'name', 'description', 'date')
							   ->order($order)
							   ->where('id_site = '.$core->edit_site.'  AND name != "main.html" AND `del` != 1')
							   ->lang('$curent')
							   ->limit($limit, $start);
			}

		$core->db->execute();
		$core->db->colback_func_param = 0;
		$core->db->add_fields_deform(array('date', 'description'));
		$core->db->add_fields_func(array('date,"d.m.Y H:i"', 'stripslashes'));
		$core->db->get_rows();

		return $core->db->rows;
	}

	public function get_total_templates_list($filter = '', $filter_value = '')
	{
		global $core;

		if($filter && $filter_value && ($filter_value != 'all'))
		{
		$sql = "SELECT COUNT(*) FROM `mcms_tmpl` WHERE `lang_id` = ".$core->CONFIG['lang']['id']." AND `".addslashes($filter)."` = '".addslashes($filter_value)."' AND id_site = ".$core->edit_site;
		}
		else
			{
			$sql = "SELECT COUNT(*) FROM `mcms_tmpl` WHERE `lang_id` = ".$core->CONFIG['lang']['id'].' AND id_site = '.$core->edit_site;
			}

		$core->db->query($sql);

		return $core->db->get_field();
	}

	public function get_module_list()
	{
		global $core;

		$sql = "SELECT DISTINCT(name_module) FROM mcms_tmpl WHERE id_site = ".$core->edit_site;
		$core->db->query($sql);
		$core->db->get_rows();

		return $core->db->rows;
	}

	public function get_info($id_doc)
	{
		global $core;

		$sql = " SELECT doc.id_doc, doc.type, doc.type_value, doc.title,  doc.template_id, doc.parent_id, doc.date, doc.visible, doc.meta_title, doc.meta_desc, doc.meta_keyw, (SELECT tpl.name FROM mcms_tmpl as tpl WHERE tpl.id_template = doc.template_id AND tpl.del = 0 AND lang_id = ".$core->CONFIG['lang']['id']."  LIMIT 1 ) as `template`,  (SELECT rew.rewrite FROM mcms_rewrite as rew WHERE rew.id = doc.rewrite_id) as rewrite, doc.rewrite_id FROM mcms_docs as `doc` WHERE id_doc = ".$id_doc."  AND lang_id = ".$core->CONFIG['lang']['id'];
		$core->db->query($sql);
		$core->db->get_rows(1);

		$doc_info = $core->db->rows;

		$core->db->select()->from('mcms_docs_block')
						   ->fields('file')
						   ->where('id_doc = '.$id_doc);

		$core->db->execute();
		$core->db->get_rows();

		$doc_info['blocks'] = $core->db->rows;
		
		if($doc_info['type'] == 6 && $doc_info['type_value'])
		{
			$data_type_id = $core->multidb->getTypeByItemId($doc_info['type_value']);
			$data_type_name = $core->multidb->getTypeNameById($data_type_id);
			
			$core->multidb->select()->type($data_type_name)->fields('*')->where('id = '.$doc_info['type_value']);
			$core->multidb->get_rows();
			
			foreach($core->multidb->rows as $fieldName=>$fieldData)
			{
				$doc_info['fieldsInfo'][$fieldName] = $core->multidb->getFieldByName($fieldName,$data_type_id);	
			}
			
			$doc_info['multidb'] = $core->multidb->rows;			
			$doc_info['dataType'] = $data_type_name;
			$doc_info['dataTypeId'] = $data_type_id;
		}
		

		return $doc_info;
	}

	public function get_langs_use_doc_info($id_doc)
	{
		global $core;

		$sql = "SELECT l.name, l.rewrite, l.id, (SELECT doc.title FROM mcms_docs as doc WHERE doc.id_doc = ".$id_doc." AND doc.lang_id = l.id) as document_title, (SELECT doc.text FROM mcms_docs as doc WHERE doc.id_doc = ".$id_doc." AND doc.lang_id = l.id) as `text`, (SELECT doc.anonce FROM mcms_docs as doc WHERE doc.id_doc = ".$id_doc." AND doc.lang_id = l.id) as anonce, (SELECT doc.more_block FROM mcms_docs as doc WHERE doc.id_doc = ".$id_doc." AND doc.lang_id = l.id) as more_block, (SELECT doc.meta_title FROM site_docs as doc WHERE doc.id_doc = ".$id_doc." AND doc.lang_id = l.id) as meta_title, (SELECT doc.meta_desc FROM site_docs as doc WHERE doc.id_doc = ".$id_doc." AND doc.lang_id = l.id) as meta_description, (SELECT doc.meta_keyw FROM site_docs as doc WHERE doc.id_doc = ".$id_doc." AND doc.lang_id = l.id) as meta_keywords FROM mcms_language as l";

		$core->db->query($sql);
    
		//$core->db->colback_func_param = 0;

		$core->db->add_fields_deform(array('text',  'document_title', 'anonce', 'more_block'));
		$core->db->add_fields_func(array('strip_2_slashes', 'htmlencodeANDstrip', 'strip_2_slashes', 'strip_2_slashes'));

		$core->db->get_rows();

		return $core->db->rows;
	}

	public function delete($id_doc)
	{
		global $core;

		$core->db->select()->from('mcms_docs')->fields('id_doc')->where('parent_id = '.intval($id_doc));
		$core->db->execute();

		$result = $core->db->result;

		if ($core->db->num_rows() > 0) {
			$this->iterator++;
		}
		while (($row = mysql_fetch_assoc($result)) && ($this->iterator < 10)) {
			self::delete($row['id_doc']);
		}

		// Rewrite cleanup
		$core->db->select()->from('mcms_docs')->fields('rewrite_id')->where('id_doc = '.intval($id_doc));
		$core->db->execute();
		$core->url_parser->del($core->db->get_field());

		// multidb cleanup
		$core->db->select()->from('mcms_docs')->fields('type', 'type_value')->where('id_doc = '.intval($id_doc));
		$core->db->execute();
		$core->db->get_rows(1);
		if($core->db->rows['type'] == 6) $core->db->delete('multidb_data_array', $core->db->rows['type_value'], 'id');
		
		// data cleanup		
		$core->db->delete('mcms_docs', intval($id_doc), 'id_doc');

		return true;
	}

	public function set_rewrite($id_doc)
	{
	global $core;
	
	if($_POST['rewrite'])
		{
		if($_POST['rewrite_id'])
			{
			if($_POST['rewrite_update'] == 1)
				{
				$rewrite_id = intval($_POST['rewrite_id']);
				$core->url_parser->edit($rewrite_id, addslashes($_POST['rewrite']));
				}
				else
					{
					$rewrite_id = intval($_POST['rewrite_id']);
					}
			}
			else
				{
				$rewrite_id = $core->url_parser->add($_POST['rewrite'], '/docs/show/id/'.$id_doc.'/');
				}

		return $rewrite_id;
		}
		else
			{
			return 0;
			}
	}

	public function generate_doc_id()
	{
		global $core;

		$core->db->query('SELECT MAX(id_doc) FROM mcms_docs');

		return intval($core->db->get_field())+1;
	}

	public function get_modules_list($type = 0)
	{
		global $core;

		$core->db->select()->from('mcms_modules')->fields('id_module', 'name', 'describe', 'location', 'reg_date', 'menu_visible')->where('lang_id = '.$core->CONFIG['lang']['id'].' AND `type` = '.$type);
		$core->db->execute();
		$core->db->colback_func_param = 0;
		$core->db->add_fields_deform(array('reg_date', 'describe'));
		$core->db->add_fields_func(array('date,"d.m.Y H:i"', 'stripslashes'));
		$core->db->get_rows();

		return $core->db->rows;
	}

	public function get_module_descr($id_module)
	{

		global $core;

		$sql = "SELECT l.id,  (SELECT m.describe FROM mcms_modules as m WHERE m.id_module = ".$id_module." AND m.lang_id = l.id) as `describe` FROM mcms_language as l";
		$core->db->query($sql);

		while($row = mysql_fetch_assoc($core->db->result))
		{
			$mod_info[$row['id']] = $row['describe'];
		}

		return $mod_info;
	}

	public function get_show_data($id_doc, $onlyField = 0)
	{
		global $core;

//		$core->db->select()->from('mcms_docs')->fields('type','parent_id', 'type_value', 'user_id', 'rewrite_id', 'template_id', 'order', 'date', 'meta_title', 'meta_desc', 'meta_keyw')->where("lang_id = {$core->CONFIG['lang']['id']} AND id_doc = {$id_doc}");
//		$core->db->execute();
		$sql = "SELECT d.*, IF(d.rewrite_id>0,(SELECT rew.rewrite FROM mcms_rewrite as rew WHERE rew.id = d.rewrite_id),CONCAT('/docs/show/id/',d.id_doc,'/')) as rewrite  FROM mcms_docs as d WHERE lang_id = {$core->CONFIG['lang']['id']} AND id_doc = {$id_doc}";
		$core->db->query($sql);
		$core->db->get_rows(1);
		
		if(!$core->db->rows || !is_array($core->db->rows) || !count($core->db->rows)) return false;
		
		$doc_type = $core->db->rows;
		
	
		
		if($doc_type['type'] == 6)
		{
			$id = intval($doc_type['type_value']);
			
			$id_type = $core->multidb->getTypeByItemId($id);
			$type_name = $core->multidb->getTypeNameById($id_type);
						
			if($onlyField) 
			{
				if($id_type == 11)
				{
					$core->multidb->select()->type($type_name)->fields('id', $onlyField, 'name')->where('id = '.$id);
				}
				else
					{
						$core->multidb->select()->type($type_name)->fields('id', $onlyField)->where('id = '.$id);
					}
			}
			else 
			{
				$core->multidb->select()->type($type_name)->fields('*')->where('id = '.$id);
			}
			$core->multidb->get_rows();
			
			$info = $core->multidb->rows;
			
			$info['template_id'] = $doc_type['template_id'];
			$info['rewrite_id'] = $doc_type['rewrite_id'];
			$info['id'] = $id_doc;			
			$info['id_doc'] = $id_doc;
			$info['date'] = $doc_type['date'];
			$info['meta_title'] = $doc_type['meta_title'];
			$info['meta_desc'] = $doc_type['meta_desc'];
			$info['meta_keyw'] = $doc_type['meta_keyw'];
			$info['parent_id'] = $doc_type['parent_id'];			
			$info['rewrite'] = $doc_type['rewrite'];

			//$info['order'] = $doc_type['order'];
			
			// FIXME: WARNING! IT`S HARDCODE BLOCK!!!!!!!!!!!
			
			if($id_type == 10 || $id_type == 12)
			{
				$sql = "SELECT LENGTH(d.text1) FROM multidb_data_array as d WHERE d.id = {$id}";
				$core->db->query($sql);
				$info['showLink'] = ($core->db->get_field()>1)? 1 : 0;
			}
			else
				{
					$info['showLink'] = ($info['name'])? 1 :0;
				}
						
			
			$info['type_id'] = $id_type;
			
			return $info;
		}
		
		
		
		if ($doc_type['type'] == 3) {
			$id_doc = intval($doc_type['type_value']);
		}

		if($onlyField)
		{
			$core->db->select()->from('mcms_docs')
						   ->fields('title')
						   ->where('lang_id = '.$core->CONFIG['lang']['id'].' AND id_doc = '.$id_doc);
		}
		else 
			{
				$core->db->select()->from('mcms_docs')
						   ->fields('title', 'text', 'anonce', 'more_block', 'meta_title', 'meta_desc', 'meta_keyw', 'parent_id', 'template_id')
						   ->where('lang_id = '.$core->CONFIG['lang']['id'].' AND id_doc = '.$id_doc);
				
			}
		
		$core->db->execute();
		
		
		if(!$core->db->num_rows())
			return false;

		$core->db->add_fields_deform(array('title'));
		$core->db->add_fields_func(array('stripslashes'));
		$core->db->get_rows(1);

		$core->db->rows['id'] = $id_doc;
		$core->db->rows['type'] = $doc_type['type'];
		
		return $core->db->rows;
	}

	private function set_tpl_name($id_tpl)
	{
		global $core;

		$core->db->select()->from('mcms_tmpl')->fields('name', 'name_module')->where('lang_id = '.$core->CONFIG['lang']['id'].' AND id_template = '.$id_tpl);
		$core->db->execute();
		$core->db->get_rows(1);

		
		
		$this->info['tpl_name'] = $core->db->rows['name'];
		$this->info['tpl_module_name'] = $core->db->rows['name_module'];
		
		return true;
	}

	private function set_doc_blocks($id_doc)
	{
		global $core;

		$core->db->select()->from('mcms_docs_block')->fields('file')->where('id_doc = '.$id_doc);
		$core->db->execute();
		$core->db->get_rows();

		$this->info['blocks'] = $core->db->rows;

		return true;
	}
	
	public function get_type_tpl($id, $type = 'admin')
	{
		global $core;
		
		$typeInfo = $core->multidb->getTypeInfoNonLoad($id);
		
		if($type == 'client') return $typeInfo['client_template_id'];
		
		$this->set_tpl_name($typeInfo['admin_template_id']);
		
		
		return $this->info['tpl_name'];
	}

	private function getParentRootId($childId)
	{
		global $core;
		
		$sql = "SELECT parent_id FROM mcms_docs WHERE id_doc = {$childId}";
		$core->db->query($sql);
				
		$parent_id = $core->db->get_field();
				
		if($parent_id != 0) return $this->getParentRootId($parent_id);
		
		return $childId;		
	}

}


?>
