<?php

################################################################################
# Класс реализует виртуальную базу данных.                                     #
# Этот класс используется для динамического создания различных типов страниц   #
# с разным количеством полей и наследованием свойств с неограниченым числом    #
# итераций наследования.                                                       #
# Для работы системы используется базовый класс базы данных. Для этого была    #
# создана таблица-матрица с разными типами полей. Связь матрицы с реальными    #
# данными выполняется средствами базы данных.                                  #
# Используеммые таблицы:                                                       #
#    multidb_data_array - матрица данных                                       #
#    multidb_types - таблица типов                                             #
#    multidb_fields - таблица названий полей и связи с матрицей                #
#    multidb_types_fields - таблица связи типа и полей матрицы                 #
#                                                                              #
# ---------------------------------------------------------------------------- #
# @Author: Alexey Pshenichniy <inc.mcms@gmail.com> <M-zone Inc.> 2005-2009     #
# @Date: 28.03.2009                                                            #
# @license: GNU GENERAL PUBLIC LICENSE Version 2                               #
# ---------------------------------------------------------------------------- #
# M-cms v5.1 (core build - 5.143)                                              #
################################################################################

class multidb 
{
	
	public $table_data_array = 'multidb_data_array';
	public $table_fields = 'multidb_fields';
	public $table_types = 'multidb_types';
	public $table_types_fields = 'multidb_types_fields';
	public $recursionTypeFoldingMax = 10;
	public $rows = array();
	public $isLoaded = false;
		
	
	private $baseDataTypes = array();
	private $types = array();
	private $fields = array();
	private $dbStructure = array();
	private $indexer = array();
	private $structureLoaded = false;
	
	private $query = null;
	
	
	/**
	 * Конструктор класса
	 *
	 */
	public function __construct()
	{
		$this->baseDataTypes = array('varchar', 'int', 'text', 'blob');
	}
	
	/**
	 * Метод выполняет инициализацию базы данных, загружает структуру
	 */
	public function load()
	{
		if(!$this->isLoaded) $this->loadStructure();
		
		return true;
	}
	
	/**
	 * Метод возвращает список типов данных
	 *
	 * @return unknown
	 */
	public function getTypes()
	{
		if(!$this->isLoaded) $this->loadStructure();
		
		return $this->types;
	}
	
	/**
	 * Метод возвращает список полей указанного типа данных c учетом наследования
	 *
	 * @return unknown
	 */
	public function getFields($typeId)
	{
		if(!$this->isLoaded) $this->loadStructure();
		if(!isset($this->fields[$typeId])) return false;
		
		return $this->fields[$typeId];
	}
	
	/**
	 * Метод возвращает всю структуру виртуальной базы данных
	 *
	 * @return unknown
	 */
	public function getStructure()
	{
		if(!$this->isLoaded) $this->loadStructure();
		
		return $this->dbStructure;
	}
	
	/**
	 * Метод возвращает объект конструктора запросов в базу
	 *
	 * @return unknown
	 */
	public function select()
	{
		unset($this->query);
		
		$this->query = new queryBuilderSelect($this);
		
		return $this->query;	
	}
	
	/**
	 * Метод обрабатывает объект конструктора запросов, генерируя реальный запрос в базу
	 *
	 */
	public function get_rows()
	{
		global $core;
		
		// загружаем структуру виртуальной базы
		if(!$this->isLoaded) $this->loadStructure();
		
		// генерируем sql запрос
		$this->query->getQuery();

		// выполняем запрос к реальной базе
		$core->db->query($this->query->sql);
		// получаем результат запроса
		$core->db->get_rows(1);
		
		$this->rows = $core->db->rows;
		
		return $this->rows;
	}
	
	/**
	 * Метод проверяет на наличие колонки в типе
	 *
	 * @param string $fieldName
	 * @param int $typeId
	 * @return boolean
	 */
	public function fieldExists($fieldName, $typeId)
	{
		if(isset($this->indexer['fields']['name'][$typeId][$fieldName]) && is_array($this->indexer['fields']['name'][$typeId][$fieldName])) return true;
		
		return false;
	}
	
	/**
	 * Метод возвращает id типа по его имени
	 *
	 * @param unknown_type $typeName
	 * @return unknown
	 */
	public function getTypeId($typeName)
	{
		return $this->indexer['types'][$typeName];
	}
	
	/**
	 * Метод возвращает все поля указаного типа
	 *
	 * @param unknown_type $typeId
	 * @return unknown
	 */
	public function getFieldsByType($typeId)
	{
		return $this->dbStructure[$typeId]['fields'];
	}
	
	/**
	 * Метод возвращает данные колонки по ее названию и id типа
	 *
	 * @param unknown_type $fieldName
	 */
	public function getFieldByName($fieldName, $typeId, $fieldParam = 0)
	{
		if($fieldParam) return $this->indexer['fields']['name'][$typeId][$fieldName][$fieldParam];
		
		return $this->indexer['fields']['name'][$typeId][$fieldName];
	}
	
	/**
	 * Метод возвращает информацию о типе данных без загрузки самой базы.
	 *
	 * @param Id Type $idType
	 * @return Assoc array result or false be else on error
	 */
	public function getTypeInfoNonLoad($idType)
	{
		global $core;
		
		$idType = intval($idType);
		
		if(!$idType) return false;
		
		$core->db->select()->from($this->table_types)->fields('*')->where("id = {$idType}");
		$core->db->execute();
		$core->db->get_rows(1);
		
		return $core->db->rows;
	}
	
	/**
	 * Метод получает id типа по одной записе в матрице данных
	 *
	 * @param unknown_type $id
	 * @return unknown
	 */
	public function getTypeByItemId($id)
	{
		global $core;
		
		$core->db->select()->from($this->table_data_array)->fields('id_type')->where("id = {$id}");
		$core->db->execute();
		
		return $core->db->get_field();
	}
	
	/**
	 * Метод получает название типа по его id без загрузки структуры базы
	 * 
	 */
	public function getTypeNameById($id)
	{
		global $core;
		
		$core->db->select()->from($this->table_types)->fields('name')->where("id = {$id}");
		$core->db->execute();
				
		return $core->db->get_field();
	}
	
	/**
	 * Метод добавляет строку в базу данных
	 *
	 * @param assoc  $array
	 * @param unknown_type $id_type
	 * @return unknown
	 */
	public function insertData($array, $id_type)
	{
		global $core;
		
		$this->load();
		
		$fields = $this->getFieldsByType($id_type);
		$data = array();
		
		
		if(isset($fields[0]))
		{
			foreach($fields as $field)
			{
				
				if(isset($array[$field['field_name']]))
				{
					$data[$field['data_array_link']] = $_POST[$field['field_name']];
				}
			}
		}
		else
			{
				if(isset($array[$fields['field_name']]))
				{
					$data[$fields['data_array_link']] = $array[$fields['field_name']];
				}
			}
			
		$data['id_type'] = 	$id_type;
			
		$core->db->autoupdate()->table($this->table_data_array)->data(array($data));
		$core->db->execute();
		//$core->db->debug();
		
		
		return $core->db->last_id();	
	}
	
	/**
	 * Метод удаляет одну запись из виртуальной базы по id
	 *
	 * @param unknown_type $idItem
	 * @return unknown
	 */
	public function delete($idItem)
	{
		global $core;
		
		$core->db->delete($this->table_data_array, intval($idItem), 'id');
		
		return true;		
	}
	
	/**
	 * Метод загружает всю структуры виртуальной базы данных
	 *
	 * @return assocciative array
	 */
	private function loadStructure()
	{
		global $core;
		
		// получаем все типы данных
		$sql = "SELECT t.id, t.parent_id, t.name, t.description, (SELECT COUNT(*) + (SELECT COUNT(*) FROM multidb_types_fields as tf2 WHERE tf2.id_type = t.parent_id) FROM multidb_types_fields as tf WHERE tf.id_type = t.id) as `count` FROM `{$this->table_types}` as t WHERE 1 ORDER BY t.parent_id, t.id";
		$core->db->query($sql);
		$this->types = $core->db->get_rows(0, 'id');
		
		// получаем все названия колонок всех типов
		$sql = "SELECT f.id_type, ff.id, ff.field_name, ff.field_description, ff.field_base_type, ff.data_array_link, (SELECT COUNT(*) FROM `{$this->table_data_array}` as da WHERE da.id_type = f.id_type) as found_rows FROM `{$this->table_fields}` as ff, `{$this->table_types_fields}` as f, `{$this->table_types}` as t WHERE f.id_type IN(t.id) AND ff.id = f.id_field" ;     
		$core->db->query($sql);
		$this->fields = $core->db->get_rows(0, 'id_type');
		
		$typeFields = array();
		$_tmp_not_root_type = array();
		

		// дальше идет волшебство :)
		foreach($this->types as $id_type=>$type_data)
		{
			$this->indexer['types'][$type_data['name']] = $id_type;
			
			if($type_data['parent_id'] != 0)
			{
				$_tmp_not_root_type[$id_type] = $type_data;
				$_tmp_not_root_type[$id_type]['rows'] = (isset($this->fields[$id_type]['found_rows']))? $this->fields[$id_type]['found_rows'] : $this->fields[$id_type][0]['found_rows']; 
				$_tmp_not_root_type[$id_type]['fields'] = (isset($this->fields[$id_type]['id']))? array($this->fields[$id_type]) : $this->fields[$id_type];
			}
			else 
				{
					$typeFields[$id_type] = $type_data;
					$typeFields[$id_type]['rows'] = (isset($this->fields[$id_type]['found_rows']))? $this->fields[$id_type]['found_rows'] : $this->fields[$id_type][0]['found_rows']; 
					$typeFields[$id_type]['fields'] = (isset($this->fields[$id_type]['id']))? array($this->fields[$id_type]) : $this->fields[$id_type];
				
					$this->indexTreeAdd($typeFields[$id_type]['fields'], $id_type);
				}
		}
		
		unset($id_type, $type_data);
		
		foreach($_tmp_not_root_type as $id_type=>$type_data)
		{
			$typeFields[$id_type] = $type_data;
			
			if(isset($this->fields[$type_data['parent_id']]))
			{
				$typeFields[$id_type]['fields'] = $this->fields[$type_data['parent_id']];
			}
			
			if(isset($this->fields[$id_type]))
			{
				if(isset($this->fields[$type_data['parent_id']]))
				{
					$_tmp_arr_1 = $typeFields[$id_type]['fields'];
					$_tmp_arr_2 = $this->fields[$id_type];
					
					if(isset($_tmp_arr_1['id']))
					{
						$_tmp_arr_1_s =  $_tmp_arr_1;
						unset($_tmp_arr_1);
						$_tmp_arr_1[0] = $_tmp_arr_1_s;
						$_tmp_start_next = 1;
						unset($_tmp_arr_1_s);
					}
					else 
						{
							$_tmp_start_next = count($_tmp_arr_1);
						}
					
					if(isset($_tmp_arr_2['id']))
					{
						$_tmp_arr_2_s = $_tmp_arr_2;
						unset($_tmp_arr_2);
						$_tmp_arr_2[$_tmp_start_next] = $_tmp_arr_2_s;
						unset($_tmp_arr_2_s);
					}
					else 
						{
							foreach($_tmp_arr_2 as $val)
							{
								$_tmp_arr_2_new[$_tmp_start_next] = $val;
								$_tmp_start_next++;
							}
							
							unset($_tmp_arr_2);
							$_tmp_arr_2 = $_tmp_arr_2_new;
							unset($_tmp_arr_2_new, $_tmp_start_next);
						}

					unset($typeFields[$id_type]['fields']);	
					$typeFields[$id_type]['fields'] = array_merge($_tmp_arr_2, $_tmp_arr_1);
					
					$this->indexTreeAdd($typeFields[$id_type]['fields'], $id_type);
					
					unset($_tmp_arr_2, $_tmp_arr_1);
				}
				else 
					{
						$typeFields[$id_type]['fields'] = $this->fields[$id_type];
						$this->indexTreeAdd($typeFields[$id_type]['fields'], $id_type);
					}
			}
			
			$typeFields[$id_type]['rows'] = (isset($this->fields[$id_type]['found_rows']))? $this->fields[$id_type]['found_rows'] : $this->fields[$id_type][0]['found_rows']; 
		}
		unset($_tmp_not_root_type);
		
		
		$this->dbStructure = $typeFields;
		$this->isLoaded = true;
		
					
		return $this->dbStructure;
	}
	
	/**
	 * Метод индексирует структуру базы по колонкам, где за индекс взяты: тип данных, название колонки, Id колонки
	 *
	 * @param unknown_type $arrayFields
	 * @param unknown_type $id_type
	 */
	private function indexTreeAdd($arrayFields, $id_type)
	{
		if(is_array($arrayFields) && count($arrayFields))
		{
			foreach($arrayFields as $field)
			{
				$this->indexer['fields']['name'][$id_type][$field['field_name']] = $field;
				$this->indexer['fields']['id'][$id_type][$field['id']] = $field;
			}
		}
	}
	
}



class queryBuilderSelect 
{
	
	public $typeName = '';
	public $typeId = 0;
	public $fieldsArray = array();
	public $orderExp = '';
	public $limitExp = '';
	public $whereExp = '';
	public $sql = '';
	public $errors = array();
	
	private $multidb = null;
	private $whereStr = '';
	private $orderStr = '';
	
	public function __construct(multidb $obj)
	{
		$this->multidb = $obj;
	}
	
	public function type($type_name)
	{
		$this->typeName = $type_name;
		
		return $this;
	}
	
	public function where($str)
	{
		$this->whereExp = $str;
		
		return $this;
	}
	
	public function fields()
	{
		$this->fieldsArray = func_get_args();
		
		if(!count($this->fieldsArray))
		{
			$this->fieldsArray = 'all';
			
			return $this;
		}
		if($this->fieldsArray[0] == '*')
		{ 
			$this->fieldsArray = 'all';
			
			return $this;
		}
		
		$fields = array();
		
		foreach($this->fieldsArray as $item)
		{
			if(is_array($item))
			{
				$fields = array_merge($fields, $item);
			}
			else
				$fields[] = $item;
		}
		
		$this->fieldsArray = $fields;
		
		return $this;
	}
	
	public function order($str)
	{
		$this->orderExp = $str;
		
		return $this;
	}
	
	public function limit($start, $limit=0)
	{
		$this->limitExp = ($limit==0)? ' LIMIT '.$start : ' LIMIT '.$start.', '.$limit;
		
		return $this;
	}
	
	public function getWhere()
	{
		if(strlen($this->whereExp))
		{
			$where = explode(" ", $this->whereExp);
					
			$_trig = 0;
			$_expr = '';
			
			foreach($where as $cond)
			{
				if($_trig == 0 || $_trig == 2)
				{
					if($this->multidb->fieldExists($cond, $this->typeId))
					{
						$_expr.= ' d.'.$this->multidb->getFieldByName($cond, $this->typeId, 'data_array_link'); 
					}
					else 
						{
							if($cond == 'id')
							{
								$_expr.= " d.id";
							}
							else
								{
									$_expr.= "{$cond}";
								}
						}
				}
				
				if($_trig == 1 || $_trig == 3) $_expr.= " {$cond} ";
				
				if($_trig == 3)
				{
					$_trig = 0;
				}
				else
					{
						$_trig++;
					}
			}
			
			$this->whereStr = $_expr;
			unset($_expr, $_trig, $cond, $where);
			
			return $this->whereStr;
		}
		
		return false;
	}
	
	public function getOrder()
	{
		$order = explode(',', $this->orderExp);
		$_subOrdExpr = '';
			
		foreach($order as $_expr)
		{
			$_subOrd = explode(" ", trim($_expr));
			$_trig = 0;
					
			foreach($_subOrd as $val)
			{
				if($_trig == 0)
				{
					if($this->multidb->fieldExists($val, $this->typeId))
					{
						$_subOrdExpr .= " d.".$this->multidb->getFieldByName($val, $this->typeId, 'data_array_link'); 
					}
					else 
						{
							$this->errors[] = 'Uknown order expression. Field <b>'.$val.'</b> not exists!'; 
							$this->orderStr = '';
							
							return $this->orderStr;
						}
				}
				
				if($_trig == 1)
				{
					$valTm = strtolower($val);
					if($valTm == 'desc' || $valTm == 'asc')
					{
						$_subOrdExpr .= " ".$valTm;
					}
					else 
						{
							$_subOrdExpr .= " ASC";
						}
				}
				
				$_trig = ($_trig == 1)? 0 : 1;
			}
			
			$_subOrdExpr .= ", "; 
			
		}
		
		$_subOrdExpr = trim($_subOrdExpr);
		
		if($_subOrdExpr) $_subOrdExpr = substr($_subOrdExpr, 0, -1);
		
		$this->orderStr = $_subOrdExpr;

		unset($_subOrdExpr, $_trig, $valTm, $val, $_expr, $_subOrd, $order);
		
		return $this->orderStr;
	}

	public function getQuery()
	{
		$this->typeId = $this->multidb->getTypeId($this->typeName);
		
		$fields = array();
		
		if(is_array($this->fieldsArray) && count($this->fieldsArray))
		{
			foreach($this->fieldsArray as $fieldName)
				$fields[] = $this->multidb->getFieldByName($fieldName, $this->typeId);
		}
		
		if($this->fieldsArray == 'all') $fields = $this->multidb->getFieldsByType($this->typeId);
		
		unset($this->fieldsArray);
		$this->fieldsArray = $fields;
		unset($fields);
				
		
		$this->sql = 'SELECT ';
		
		foreach($this->fieldsArray as $field)
		{
			if(!isset($field['data_array_link'])) continue;
			
			$this->sql .= " d.".$field['data_array_link']." as ".$field['field_name'].","; 
		}
		
		$this->getWhere();
		$this->getOrder();
		
		
		$this->sql = substr($this->sql, 0, -1);
		$this->sql .= " FROM ".$this->multidb->table_data_array." as d WHERE d.id_type = ".$this->typeId;
		$this->sql .= ($this->whereStr)? " AND ({$this->whereStr})": '';
		$this->sql .= ($this->orderStr)? ' ORDER BY '.$this->orderStr:'';
		$this->sql .= ($this->limitExp)? $this->limitExp : '';
		

		return $this->sql;
	}


}







?>