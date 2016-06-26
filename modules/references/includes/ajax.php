<?php

// This file was created by M-cms core system for ajax functions



function getOperations($instance, $page, $limit, $sortBy, $sortType, $filters)
{
	$root = new admin_references();
	$filters = json_decode(stripslashes($filters),true);
	$page = intval($page)+0;
	$limit = intval($limit)+0;
	
	$possibleSorted = array('id', 'group_name', 'calculation_method_name', 'name', 'price', 'cost_increasing_factor', 'id');
	
	if($sortBy && in_array($sortBy, $possibleSorted))
	{
		$sortBy = $sortBy;
		$sortType = ($sortType == 'desc')? 'desc':'asc';
	}
	else
		{
			$sortBy = $possibleSorted[0];
			$sortType = 'desc';
		}
	
	global $core;

	$dataObject = $root->getOperations($instance, $page, $limit, $sortBy, $sortType, $filters);
	$pager = ajax_pagenav($rowsCount = $core->db->found_rows(), $limit, $page, "grid.{$instance}.gopage", "false");
	
	return array('pager'=>$pager, 'data'=>$dataObject, 'rows'=>$rowsCount, 'show_from'=>$page*$limit, 'show_to'=>count($dataObject)+$page*$limit);
}










?>