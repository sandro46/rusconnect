<?php

class admin_catalog
{
	public $site_id = 0;
	private $types = array();

	public function __construct()
	{
		global $core;
		//if($core->site_id == 3) {
			$this->site_id = (isset($_SESSION['current_catalog_site']))? intval($_SESSION['current_catalog_site']) : 2;
		//} else {
		//	$this->site_id = $core->site_id;
		//}
		
		//$core->edit_site = 2;
		$this->types = array('simple'=>1,'html'=>2,'extended'=>3,'folder'=>4);
	}

	public function setSiteId($id) {
		if(!$id = intval($id)) return false; 
		$_SESSION['current_catalog_site'] = $id;
		session_write_close();
		session_commit();
		return true;
	}
	
	public function getCrumbs($id) {
		global $core;
		
		$crumbs = array();
		$first = $id;
		while(true) {
			$sql = "SELECT c.id, c.parent_id, c.title, c.rewrite_id, get_rewrite(c.rewrite_id) as url FROM catalog_entries as c WHERE c.id = $id";
			$core->db->query($sql);
			$core->db->get_rows(1);

			if($core->db->num_rows()>0) {
				if($core->db->rows['id'] == $first) {
					$core->db->rows['islast'] = true;
				}
				$crumbs[] = $core->db->rows;
				
				if($core->db->rows['parent_id'] != 0) {
					$id = $core->db->rows['parent_id'];
					continue;
				}
				break;
			} else {
				break;
			}
		}
		krsort($crumbs);
		
		return $crumbs;
	}

	public function get($id, $kk = false) {
		global $core;
		
		$sql = "SELECT c.*, FROM_UNIXTIME(c.date, '%d.%m.%Y') as date, get_rewrite(c.rewrite_id) as url FROM catalog_entries as c WHERE id = {$id}";
		
		$core->db->query($sql);
		$core->db->add_fields_deform(array('title','description','description2','text'));
		$core->db->add_fields_func(array('stripslashes','stripslashes','stripslashes','stripslashes'));
		$core->db->get_rows(1);

		$arr = $core->db->rows;
		
		if(!isset($arr['img']) || $arr['img'] == '' || $arr['img'] == '0'){
			preg_match("/img.*?src\=\"(.*?)\"/s", $arr['text'],$aa);
			if(isset($aa[1]) && $aa[1] != '')
				$arr['img'] = $aa[1];
		}
		
		$order = 'c.id DESC';
		if($id == 42) $order = ' c.date DESC';
		if($id == 773) $order = ' c.id ASC';
		
		
	
		$sql = "SELECT c.*, FROM_UNIXTIME(c.date, '%d.%m.%Y') as date, get_rewrite(c.rewrite_id) as url FROM catalog_entries as c WHERE parent_id = {$id} ";
		if($kk)
			$sql .= " AND (id <> {$kk} OR parent_id = 773) ";
			
		$sql .= " ORDER BY {$order}";
                
                $core->db->query($sql);
                $core->db->add_fields_deform(array('title','description','description2','text'));
                $core->db->add_fields_func(array('stripslashes','stripslashes','stripslashes','stripslashes'));
                $core->db->get_rows();
 
 		if($core->db->num_rows()>0){
 			$arr['sub_pages'] = $core->db->rows;
 		}

 		return $arr;		
	}
	
	public function deleteNode($id) {
		global $core;
		
		$id = intval($id);
		if(!$id) return false;
		
// 		$core->db->delete('catalog_entries', $id, 'id');
// 		$core->db->delete('catalog_entries', $id, 'parent_id');
		
		$core->db->delete('catalog_entries', array('id'=>$id, 'shop_static_sys'=>0) );
		$core->db->delete('catalog_entries', array('parent_id'=>$id, 'shop_static_sys'=>0));
	}
	
	public function copyNodeTo($id, $parentId) {
		global $core;
		$id = intval($id);
		$parentId = intval($parentId);
		
		if(!$id) return false;
		
		$core->db->select()->from('catalog_entries')->fields('*')->where('id = '.$id.' AND shop_static_sys = 0');
		$core->db->execute();
		$core->db->get_rows(1);
		
		if(!is_array($core->db->rows) || !isset($core->db->rows['id'])) return false;
		
		$catalog_entries = $core->db->rows;
		unset($catalog_entries['id']);
		
		$catalog_entries['rewrite_id'] = 0;
		$catalog_entries['parent_id'] = $parentId;
		
		$core->db->autoupdate()->table('catalog_entries')->data(array($catalog_entries));
		$core->db->execute();
		
		return $core->db->insert_id;
	}
	
	public function moveNodeTo($id, $parentId) {
		global $core;
		$id = intval($id);
		$parentId = intval($parentId);
		if(!$id) return false;
		
		$catalog_entries = array('id'=>$id, 'parent_id'=>$parentId, 'shop_static_sys'=>0);
		$core->db->autoupdate()->table('catalog_entries')->data(array($catalog_entries))->primary('id','shop_static_sys');
		$core->db->execute();
	}
	
	public function save($id, $parent_id, $type, $data) {
		global $core;
//  		print_r($data);
//  		return;
		$id = intval($id);
		$parent_id = intval($parent_id);
		$type = (isset($this->types[$type]))? $this->types[$type]: $this->types[1];
		$siteId = (isset($data['site_id'])? $data['site_id'] : $this->site_id);
		
		$catalog_entries = array(
			'parent_id'=>$parent_id,
			'type_id'=>$type,
			'title'=>self::str($data['title']),
			'img'=>$data['img'],
			'description'=>self::str($data['description']),
			'description2'=>self::str($data['description2']),
			'text'=>self::str($data['text']),
			'date'=>((self::testInputDate($data['date']))? self::makeTimestampFromDate($data['date']) : time()),
			'site_id'=>(isset($data['site_id'])? $data['site_id'] : $this->site_id),
			'visible'=>(intval($data['visible'])? 1 : 0)
			//'shop_static_sys' => (intval($data['shop_static_sys']) ? 1 : 0),
			//'static_name'=>(isset($data['static_name'])? $data['static_name'] : '')
		);
		
		if(isset($data['shop_static_sys'])) {
			$catalog_entries['shop_static_sys'] = intval($data['shop_static_sys']);
		}
		
		if(isset($data['static_name'])) {
			$catalog_entries['static_name'] = $data['static_name'];
		}
		
		if(isset($data['order'])) {
			$catalog_entries['order'] = intval($data['order']);
		}
		
		$_tmp_site = $core->edit_site;
		$core->edit_site = $this->site_id;
		
		if($id) {
			$catalog_entries['id'] = $id;
			
			
			if(isset($data['rewrite_id']) && $data['rewrite_id'] && $data['rewrite_old'] != $data['url']) {
				if(strlen($data['url']) < 2) {
					$core->url_parser->del(intval($data['rewrite_id']));
					$data['rewrite_id'] = 0;
				} else {
					$core->url_parser->edit(intval($data['rewrite_id']), $data['url']);
				}
			}
			
			if(!$data['rewrite_id'] && strlen($data['url']) >=2) {
				$catalog_entries['rewrite_id'] = $core->url_parser->add($data['url'], ('/catalog/show/id/'.$id.'/'), 'Total', $siteId);
			}
		} 
		
		$core->db->autoupdate()->table('catalog_entries')->data(array($catalog_entries))->primary('id');
		$core->db->execute();
		
		if(!$id && $core->db->insert_id && strlen($data['url']) >=2) {
			$id = $core->db->insert_id;
			$url = $data['url'];
			if(substr($url, 0, 1) != '/') $url = '/'.$url;
			
			if(isset($data['simple_link']) && $data['simple_link'] != '') {
				$catalog_entries = array(
						'id'=>$id,
						'rewrite_id'=>$core->url_parser->add($data['url'], $data['simple_link'], 'Total', $siteId)
				);
			} else {
				$catalog_entries = array(
						'id'=>$id,
						'rewrite_id'=>$core->url_parser->add($data['url'], '/catalog/show/id/'.$id.'/', 'Total', $siteId)
				);
			}
			
			
			$core->db->autoupdate()->table('catalog_entries')->data(array($catalog_entries))->primary('id');
			$core->db->execute();
		}
		
		$core->edit_site = $_tmp_site;
		
		return array(($id?$id:$core->db->insert_id),$parent_id);
	}
	
	public function getList($parent_id=0, $filter=false) {
		global $core;
		
		$siteId = ($core->site_id == 3)? $this->site_id : $core->site_id;
		if(!is_array($filter)) $filter = array();
		
		$sql = "SELECT 
					c.id, 
					(SELECT a.title FROM catalog_entries a WHERE a.id = {$parent_id}) parrent_title,
					(SELECT a.description FROM catalog_entries a WHERE a.id = {$parent_id}) parrent_title,
					c.title, 
					c.text,
					c.description,
					FROM_UNIXTIME(c.date, '%d.%m.%Y') as date,
					IF(c.rewrite_id > 0, get_rewrite(c.rewrite_id), CONCAT('/ru/catalog/show/id/',c.id)) as url 
				FROM 
					catalog_entries as c 
				WHERE 
					c.site_id = {$siteId} AND 
					c.parent_id = {$parent_id} " ;
		
		if(isset($filter['visible'])) {
			$sql .= " AND c.visible = ".$filter['visible'];
		}
		
	    if(isset($filter['rnd'])) {
			$sql .= " ORDER BY RAND() ";
		}
		
	    if(isset($filter['limit'])) {
			$sql .= " LIMIT  ".$filter['limit'];
		}	

	    
		//if($parent_id == 296)print_r($sql);
		$core->db->query($sql);
		$core->db->add_fields_deform(array('title'));
		$core->db->add_fields_func(array('stripslashes'));
		$core->db->get_rows();
		//print_r($core->db->rows);
		return $core->db->rows;
	}
	
	public function get_rnd_review(){
		$a = $this->getList(296, array('limit'=>1, 'rnd'=>true));
		$a= $a[0];
		//print_r($a);
		preg_match_all('!<img.*?src="(.*?)"!', $a['text'], $matches);
		$image_src = $matches['1'][0];
		
		$b = array(
				'title' => "<span>".$a['title']."</span><br>".$a['description'],
				'text' => strip_tags($a['text']),
				'img' => $image_src,
				'url' => $a['url']
		);
		return $b;
	}
	
	public function getTree($parent_id=0) {
		global $core;
		
		$siteId = $this->site_id;
		
		$sql = "SELECT
					c.title, c.id, c.parent_id, c.type_id, c.visible, c.site_id, 
					FROM_UNIXTIME(c.date, '%d.%m.%Y') as date,
					get_rewrite(c.rewrite_id) as url,
					t.name as type_name,
					IF(EXISTS (SELECT cc.id FROM catalog_entries as cc WHERE cc.parent_id = c.id), 1, 0) as is_folder
				FROM 
					catalog_entries as c
				LEFT JOIN catalog_types as t ON t.id = c.type_id
				
				WHERE 
					c.parent_id = {$parent_id} AND
					c.site_id = {$siteId}";
		
		$core->db->query($sql);
		$core->db->get_rows();
		
		//echo $sql;
		//return  $sql;
		
		return $core->db->rows;
	}
	
	private static function testInputDate($str) {
		$str = addslashes($str);
		if(preg_match('/^[0-9]{2,2}\.[0-9]{2,2}\.[0-9]{4,4}$/',$str)) {
			return true;
		}
		return false;
	}
	
	private static function makeTimestampFromDate($str,$fromstart=false,$toend=false) {
		$str = explode('.',$str);
		if($fromstart) {
			return mktime(0,0,1,$str[1],$str[0],$str[2]);
		} elseif($toend) {
			return mktime(23,59,59,$str[1],$str[0],$str[2]);
		} else {
			return mktime(0,0,0,$str[1],$str[0],$str[2]);
		}
	}
		
	private static function str($str) {
		return mysql_real_escape_string(decode_unicode_url($str));
	}
}


?>
