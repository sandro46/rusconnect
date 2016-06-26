<?php

class admin_references
{


	public function __construct()
	{
		// Empty
	}


	public function getList($instance, $page = 0, $limit = 20, $sortBy = 'date', $sortType = 'DESC', $filters = false)
	{
		global $core;
		
		$start = $page * $limit;
		$siteId = ($core->is_admin())? $core->edit_site : $core->site_id;
		
		$sql = "SELECT
					SQL_CALC_FOUND_ROWS
					r.id, r.description,
					ROUND((SELECT COUNT(*) / (SELECT COUNT(*) FROM mcms_references_fields as rf WHERE rf.reference_id = r.id) FROM mcms_references_data as rd WHERE rd.reference_id = r.id)) as entry_count,
					(SELECT COUNT(*) FROM mcms_references_fields as rf WHERE rf.reference_id = r.id) as field_count
					
				FROM mcms_references as r
				WHERE
					r.site_id = {$siteId}";
		
		$sql.=" ORDER BY {$sortBy} {$sortType} LIMIT {$start},{$limit}";
				
		$core->db->query($sql);
		$core->db->get_rows();
	
		return $core->db->rows;
	}
	
	
	public function getOperations($instance, $page, $limit, $sortBy, $sortType, $filters)
	{
		global $core;
		
		$start = $page * $limit;
		$sql = "SELECT
					SQL_CALC_FOUND_ROWS
					op.id, op.operation_type_id, op.calculation_method_name, op.name, op.price, op.cost_increasing_factor, 
					ot.name as group_name
				FROM 
					operations as op,
					operations_types as ot
				WHERE 
					ot.id = op.operation_type_id";

		$find = false;
		
		if(isset($filters['type']))
		{
			$filters['op.operation_type_id'] =  intval($filters['id']);
			unset($filters['type']);
		}
		
		if(isset($filters['parent_id']))
		{
			$filters['op.parent_id'] =  intval($filters['parent_id']);
			unset($filters['parent_id']);
		}
		else 	
			{
				$filters['op.parent_id'] =  '0';
			}
		
		if(isset($filters['find']))
		{
			$filters['find'] = mysql_real_escape_string(htmlspecialchars(addslashes($filters['find'])));
			$find = ' (op.name LIKE "%'.$filters['find'].'%" OR op.price LIKE "%'.$filters['find'].'%" OR op.cost_increasing_factor LIKE "%'.$filters['find'].'%" OR ot.name LIKE "%'.$filters['find'].'%" OR op.id LIKE "%'.$filters['find'].'%")';
			unset($filters['find']);
		}
		
        $filterSql = $core->db->sql_filters($filters);
 		if(strlen($filterSql)>2) $sql.= ' AND'.$filterSql;	
 		if($find) $sql.= ' AND'.$find;	
 			
		$sql.=" ORDER BY {$sort_by} {$sort_type} LIMIT {$start},{$limit}";
				
		$core->db->query($sql);
		$core->db->add_fields_deform(array('price', 'cost_increasing_factor'));
		$core->db->add_fields_func(array('parce_digits', 'parce_digits'));
		$core->db->get_rows();
		
		$list = $core->db->rows;
						
		$core->db->query('SELECT FOUND_ROWS() as count');
		$rows_count = $core->db->get_field();
					
		return array($rows_count,$list);
	}



}


?>