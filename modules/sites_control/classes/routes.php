<?php

class routes
{
	
	public $list = array();
	
	public function __construct()
	{
		
		
	}
	
	
	public function get_routes($type)
	{
		global $core;
		
		$core->db->connect($core->CONFIG, 'svn_control');
		
		
		if($type == 'fronts')
		{
			$sql = "SELECT rt.id as route_id, s.id as server_id, r.id as repo_id, s.description AS server_name, s.active AS server_active, s.address AS server_dest, r.name AS repo_name, r.active AS repo_active, src.description AS source_name, src.path AS src_path, rt.active AS route_active, CONCAT( 'svn://', r.name, src.path ) AS url
					FROM repository AS r, sources AS src, servers AS s, routes AS rt
					WHERE (
					rt.id_repo
					IN ( 1, 2, 3 )
					)
					AND (
					s.id = rt.id_server
					AND r.id = rt.id_repo
					AND src.id = rt.id_sources
					)
					ORDER BY s.description";
		
			$core->db->query($sql, 'svn_control');
			$core->db->get_rows();
			
			foreach($core->db->rows as $row)
			{
				$this->list[$row['server_name']]['server_name'] = $row['name'];
				$this->list[$row['server_name']]['items'][] = $row;
				$this->list[$row['server_name']]['server_id'] = $row['server_id'];
				$this->list[$row['server_name']]['active'] = $row['server_active'];
			}	
		}
		else
			{
				$sql = "SELECT rt.id as route_id, s.id as server_id, r.id as repo_id, s.description AS server_name, s.active AS server_active, s.address AS server_dest, r.name AS repo_name, r.active AS repo_active, src.description AS source_name, src.path AS src_path, rt.active AS route_active, CONCAT( 'svn://', r.name, src.path ) AS url
						FROM repository AS r, sources AS src, servers AS s, routes AS rt
						WHERE (
						rt.id_repo
						IN ( 1, 2, 3 )
						)
						AND (
						s.id = rt.id_server
						AND r.id = rt.id_repo
						AND src.id = rt.id_sources
						)
						ORDER BY  src.path";
		
				$core->db->query($sql, 'svn_control');
				$core->db->get_rows();
				
				foreach($core->db->rows as $row)
				{
					$this->list[$row['url']]['url'] = $row['url'];
					$this->list[$row['url']]['items'][] = $row;
				}
			}
		
		
		return $this->list;
	}
	
	public function update_unison_configs()
	{
		global $core;
		
		$core->db->connect($core->CONFIG, 'svn_control');
		
		$files_src = array();
		$sql = "SELECT repo.name as repo_name, (SELECT serv.address FROM servers as serv WHERE serv.id = rt.id_server) as server_address, CONCAT( 'root = ', set.value, '/', repo.name ) AS item1, CONCAT( 'root = ssh://webadmin@', (
		
					SELECT sr.address
					FROM servers AS sr
					WHERE sr.id = rt.id_server
					), (
					
					SELECT sr.remotepath
					FROM servers AS sr
					WHERE sr.id = rt.id_server
					), IF( repo.typerepo =0, CONCAT( '/', repo.destination ) , '' ) ) AS item2, CONCAT( 'force = ', set.value, '/', repo.name ) AS item3, (
					
					SELECT src.path
					FROM sources AS src
					WHERE id_repo = repo.id
					AND src.id = rt.id_sources
					) AS add_path
					FROM `settings` AS `set` , `routes` AS `rt` , `repository` AS `repo`
					WHERE set.param = 'mainpath' AND repo.id = rt.id_repo AND (SELECT rp.typerepo  FROM repository as rp WHERE rp.id = rt.id_repo) = 0";
		
		$core->db->query($sql, 'svn_control');
		$core->db->get_rows();
		
		
		foreach($core->db->rows as $item)
		{
			
			if($item['add_path'] != 'NULL' && $item['add_path'] != '')
				{	
				$item['add_path'] = str_replace('/', '',$item['add_path']);	
				$out = '';
				$out .= "# Unison preferences file\n";
				$out .= $item['item1']."\n";
				$out .= $item['item2']."\n";
				$out .= $item['item3']."\n";
				$out .= "log = true\n";
				$out .= "auto = true\n";
				$out .= "batch = true\n";	
				$out .= "ignore = Path ".$item['add_path']."/classes\n";
				$out .= "ignore = Path ".$item['add_path']."/lib\n";
				$out .= "ignore = Path ".$item['add_path']."/modules\n";
				$out .= "ignore = Path ".$item['add_path']."/modules_system\n";
				$out .= "ignore = Path *var/*\n";
				$out .= "ignore = Name {.svn}";
				$this->create_config_file($out, $item['repo_name'], $item['server_address']);
				}
		}
		
		
		$sql = "SELECT DISTINCT(rt.id), repo.name as repo_name, (SELECT serv.address FROM servers as serv WHERE serv.id = rt.id_server) as server_address, CONCAT( 'root = ', set.value, '/', repo.name, (
		
						SELECT src.path
						FROM sources AS src
						WHERE src.id = rt.id_sources
						) ) AS item1, CONCAT( 'root = ssh://webadmin@', (
						
						SELECT sr.address
						FROM servers AS sr
						WHERE sr.id = rt.id_server
						), (
						
						SELECT sr.remotepath
						FROM servers AS sr
						WHERE sr.id = rt.id_server
						), IF( repo.typerepo =0, CONCAT( '/', repo.destination ) , '' ) , (
						
						SELECT src.path
						FROM sources AS src
						WHERE src.id = rt.id_sources
						) ) AS item2, CONCAT( 'force = ', set.value, '/', repo.name, (
						
						SELECT src.path
						FROM sources AS src
						WHERE src.id = rt.id_sources
						) ) AS item3, (
						
						SELECT src.path
						FROM sources AS src
						WHERE id_repo = repo.id
						AND src.id = rt.id_sources
						) AS add_path
						FROM `settings` AS `set` , `routes` AS `rt` , `repository` AS `repo`
						WHERE set.param = 'mainpath' AND repo.id = rt.id_repo AND (SELECT rp.typerepo  FROM repository as rp WHERE rp.id = rt.id_repo) = 1";	
		
		
		$core->db->query($sql, 'svn_control');
		$core->db->get_rows();
		
		
		foreach($core->db->rows as $item)
		{
			$out = '';
			$out .= "# Unison preferences file\n";
			$out .= $item['item1']."\n";
			$out .= $item['item2']."\n";
			$out .= $item['item3']."\n";
			$out .= "log = true\n";
			$out .= "auto = true\n";
			$out .= "batch = true\n";	
			$out .= "ignore = Name {.svn}";
			$this->create_config_file($out, $item['repo_name'], $item['server_address']);
		}
		
		
	}
	
	private function create_config_file($src, $repo_name, $server_addres)
	{
		global $core;
		
		$filepath = $core->CONFIG['var_path'].'/unison/';
	
		$filename = $repo_name.'_'.$server_addres.'_'.md5($src).'.prf';
	
		
		if(file_exists($filepath.$filename))
        {
        $file_content = file_get_contents($filepath.$filename);
        if(md5($src)== md5($file_content)) return;
        }
		
		$f = fopen($filepath.$filename, 'w') or die('not open file - '.$filepath.$filename);
		fwrite($f, $src);
		fclose($f);
		
		//echo $filename;
	}
	
}

function set_active_route($id_route, $type)
{
	global $core;
	
	$core->db->connect($core->CONFIG, 'svn_control');
	
	$sql = "UPDATE 	`denisco`.`routes` SET `active` = 1 WHERE id = ".$id_route;
	
	$core->db->query($sql, 'svn_control');
	
	return $type;
}

function set_active_repo($id_repo, $type)
{
	global $core;
	
	$core->db->connect($core->CONFIG, 'svn_control');
	
	$sql = "UPDATE repository SET `active` = 1 WHERE id = ".$id_repo;
	
	$core->db->query($sql, 'svn_control');
	
	return $type;
}

function set_active_server($id_repo, $type)
{
	global $core;
	
	$core->db->connect($core->CONFIG, 'svn_control');
	
	$sql = "UPDATE servers SET `active` = 1 WHERE id = ".$id_repo;
	
	$core->db->query($sql, 'svn_control');
	
	return $type;
}

function set_deactive_route($id_route, $type)
{
	global $core;
	
	$core->db->connect($core->CONFIG, 'svn_control');
	
	$sql = "UPDATE routes SET `active` = 0 WHERE id = ".$id_route;
	
	$core->db->query($sql, 'svn_control');
	
	return $type;
}

function set_deactive_repo($id_repo, $type)
{
	global $core;
	
	$core->db->connect($core->CONFIG, 'svn_control');
	
	$sql = "UPDATE repository SET `active` = 0 WHERE id = ".$id_repo;
	
	$core->db->query($sql, 'svn_control');
	
	return $type;
}

function set_deactive_server($id_repo, $type)
{
	global $core;
	
	$core->db->connect($core->CONFIG, 'svn_control');
	
	$sql = "UPDATE servers SET `active` = 0 WHERE id = ".$id_repo;
	
	$core->db->query($sql, 'svn_control');
	
	return $type;
}

function get_window_add_site_in_route($id_repo, $source_path)
{
	global $core;
	
	
	
	
}

?>