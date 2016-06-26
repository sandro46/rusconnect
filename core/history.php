<?php
################################################################################
# ---------------------------------------------------------------------------- #
# @Author: Alexey Pshenichniy <inc.mcms@gmail.com> <M-zone Inc.> 2005-2009     #
# @Date: 20.05.2008                                                            #
# @license: GNU GENERAL PUBLIC LICENSE Version 2                               #
# ---------------------------------------------------------------------------- #
# M-cms v4.1 (core build - 4.102)                                              #
################################################################################

class history extends module
{

	public $list 		= array();
	public $errors		= array();
	private $pergroup   = 1000;

	private $history 		= array();
	private $source 		= array();
	private $source_rows	= 0;
	private $historydata	= array();




	/**
	 * Метод добавляет запись истории
	 *
	 * @param string $tableName - Название таблицы из которой берутся данные для записи истории
	 * @param array $primary - Индексированый массив с первичными ключами по которым нужно брать записи из таблицы источника 0=>primary_key 1=>primary_value
	 * @param string $method - Дествие при котором вызвана запись истории. Есть всего три действия для которых пишется история 1. edit, 2. del, 3. rollback
	 * @return boolean
	 */
	public function add($tableName, $id, $method, $rollbackId = 0){
		if(!$this->checkMetod($method)) return -1;
		if(!is_array($id) || count($id) < 2) return -2;
		$rollbackId = intval($rollbackId);
		
		if($rollbackId) {
			$group = $this->history[$rollbackId]['gr_name'];
			$site = $this->history[$rollbackId]['site_id'];
		} else {
			$group = (!empty($this->core->module_name))? $this->core->module_name : $tableName;
			$site = $this->core->edit_site;
		}
		
		$this->getSourceData($tableName, $id[0], $id[1]);
		
		$query = array(
			'gr_name' => $group, 
			'primary_value' => (is_array($id[1]))? implode(':', $id[1]) : $id[1], 
			'primary_key' => (is_array($id[0]))? implode(':', $id[0]) : $id[0], 
			'user_id' => $this->user->id, 
			'site_id' => $site, 
			'tablename' => mysql::str($tableName), 
			'date' => time(), 
			'ip_address' => $_SERVER['REMOTE_ADDR'], 
			'action' => $method, 
			'rollback_id' => $rollbackId
		);
		
		$this->db->autoupdate()->table('mcms_history')->data(array($query));
		$this->db->execute();
		$historyId = $this->db->insert_id;
		
		if(!$this->source || empty($this->source)) return false;
		
		$query = array();
		
		foreach($this->source as $rowId=>$row) {
			foreach($row as $name=>$data) {
				$query[] = array(
					'id_history'=>$historyId,
					'id_row'=>$rowId+1,
					'name'=>$name,
					'data'=>mysql::str($data)
				);
			}
		}
		
		$this->db->autoupdate()->table('mcms_history_data')->data($query);
		$this->db->execute();

		return $historyId;
	}

	/**
	 * Метод удаляет запись истории
	 *
	 * @param integer $history_id - ID записи истории, которую следует удалить
	 * @return boolean
	 */
	public function delete($history_id) {
		$sql = "DELETE FROM mcms_history_data WHERE id_history = {$history_id}";
		$this->db->query($sql);
		$sql = "DELETE FROM mcms_history WHERE id = {$history_id}";
		$this->db->query($sql);

		return true;
	}

	/**
	 * Метод возвращает значение ID из основной таблицы соответствующей записи истории
	 *
	 * @param array $ids[] - не индексированный массив ID записей истории
	 * @param string $groupName - название группы
	 * @param array $fieldsData - список колонок которые необходимо выбрать из данных истории, синтаксис 'Поле' => 'Имя возвращаемого значения'
	 * @return array - индексированый, ассоциативный массив списка истории
	 */
	public function getSpecificList(array $primary, $module_name, array $order, $limit = 100, $start = 0) {

		$sql = "SELECT 
					h.id, 
					h.date, 
					h.action, 
					h.ip_address, 
					h.rollback_id, 
					h.primary_key, 
					h.primary_value, 
					IF(h.rollback_id, (
							SELECT h2.date FROM mcms_history AS h2 WHERE h2.id = h.rollback_id
						), 0) as parent_date,
				
					(SELECT u.name FROM mcms_user AS u WHERE u.id_user = h.user_id) AS user_name
				FROM 
					mcms_history AS h 
				WHERE 
					h.gr_name = '".mysql::str($module_name)."' AND 
					(h.site_id = {$this->core->edit_site} OR h.site_id = 0) AND 
					(h.primary_key = '".mysql::str($primary[0])."' AND 
					h.primary_value = '".mysql::str($primary[1])."') 
				ORDER BY 
					h.{$order['by']} {$order['type']} 
				LIMIT {$start}, {$limit}";
		
		$this->db->query($sql);
		$this->db->colback_func_param = 'first';
		$this->db->add_fields_deform(array('date', 'parent_date'));
		$this->db->add_fields_func(array('dateAgo', 'dateAgo'));
		$this->db->get_rows();

		$this->list = $this->db->rows;

		return $this->list;
	}

	/**
	 * Метод выполняет откат действия
	 *
	 * @param integer $id_history - id в таблице mcms_history до которого следует откатить изменения
	 * @return boolean
	 */
	public function rollback($id_history) {
		$this->getHistory($id_history);
		if(!$this->history[$id_history] || empty($this->history[$id_history])) return -1;
		if(!$this->checkMetod($this->history[$id_history]['action'])) return -2;
		$this->{'rollback_'.$this->history[$id_history]['action']}($id_history);	
	}

	/**
	 * Метод выполняет откат действия "редактирование"
	 *
	 * @param integer $id_history - id в таблице mcms_history до которого следует откатить изменения
	 * @return boolean
	 */
	private function rollback_edit($id_history) {
		$this->getHistoryData($id_history);
		if(!$this->historydata[$id_history] || !is_array($this->historydata[$id_history]) || empty($this->historydata[$id_history])) return -1;
		
		$this->add($this->history[$id_history]['tablename'], array($this->history[$id_history]['primary_key'], $this->history[$id_history]['primary_value']),'rollback',$id_history);
		
		$query = $this->db->autoupdate()->table($this->history[$id_history]['tablename'])->data($this->historydata[$id_history]);
		
		if(count($this->historydata[$id_history]) > 1) {
			$sql = "DELETE FROM `".$this->history[$id_history]['tablename']."` WHERE ";
			
			$primary_keys = explode(':', $this->history[$id_history]['primary_key']);
			$primary_values = explode(':', $this->history[$id_history]['primary_value']);
			$where = array();
			
			foreach($primary_keys as $index=>$primary_key) {
				$where[] = "{$primary_key} = '".mysql::str($primary_values[$index])."'";
			}
			
			if(!count($where)) return -2;
			$sql .= implode(" AND ",$where);
			
			$this->db->query($sql);
		} else {
			foreach(explode(':',$this->history[$id_history]['primary_key']) as $primary_key) {
				$query->primary($primary_key);
			}
		}
	
		$this->db->execute();
	}

	/**
	 * Метод записывает данные истории из таблицы mcms_history во внутренний массив, в элемент с номером $id_history
	 *
	 * @param integer $id_history - id в таблице mcms_history по которому нужно получить данные
	 * @return array - полученный массив
	 */
	public function getHistory($id_history) {
		if(isset($this->history[$id_history])) return $this->history[$id_history];

		$this->db->select()->from('mcms_history')->fields()->where('id = '.intval($id_history));
		$this->db->execute();
		$this->history[$id_history] = $this->db->get_rows(1);

		return $this->history[$id_history];
	}

	/**
	 * Метод записывает данные истории из таблицы mcms_ghistory_data во внутренний массив, в элемент с номером $id_history
	 *
	 * @param integer $id_history - id в таблице mcms_history по которому нужно получить данные
	 * @return array - полученный массив
	 */
	public function getHistoryData($id_history) {
		if(isset($this->historydata[$id_history])) return $this->historydata[$id_history];
		
		$data = array();
		
		$sql = "SELECT * FROM mcms_history_data WHERE id_history = {$id_history}";
		$this->db->query($sql);
		$this->db->result_map(function($row) use (&$data) {
			$data[$row['id_row']][$row['name']] = $row['data'];
		});
		
		$this->historydata[$id_history] = $data;
		
		return $this->historydata[$id_history];
	}

	/**
	 * Метод записывает данные таблицы $tablename во внутренний массив
	 *
	 * @param string $tablename - имя таблицы в БД
	 * @param string $prymary_key - имя поля по которому осуществляется выборка из таблицы $tablename
	 * @param string $prymary_value - значение поля по которому осуществляется выборка из таблицы $tablename
	 * @return array - полученный массив
	 */
	private function getSourceData($tablename, $prymary_key, $prymary_value) {
		if(!is_array($prymary_key) || !is_array($prymary_value)) return false;
		
				
		$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM `{$tablename}` WHERE ";
		$where = array();
		
		foreach($prymary_key as $key_index=>$field) {
			$where[] = " `{$field}` = '{$prymary_value[$key_index]}' ";
		}
		
		$sql .= implode(" AND ",$where);
		
		$this->db->query($sql);
		$this->db->get_rows();

		$this->source = $this->db->rows;
		$this->source_rows = $this->db->found_rows();

		return $this->source;
	}

	/**
	 * Метод осуществляет проверка на правильность указания метода хранения истории
	 *
	 * @param string $metodName - название метода
	 * @return boolean
	 */
	private function checkMetod($metodName)
	{

		switch($metodName)
		{
			case 'del':
			case 'edit':
			case 'rollback':
				return true;
			break;

			default:
				return false;
			break;
		}
	}

	/**
	 * @deprecate
	 * Метод осуществляет очистку истории по определеной группе
	 *
	 * @param string $tableName - название группы, по которой нужно провести чистку
	 * @return boolean
	 */
	private function cleanupHistory($tableName) {
		global $core;
//		print '<pre>';

		$sql = "SELECT count(*) FROM `mcms_history` WHERE `tablename` = '{$tableName}'";
		$core->db->query($sql);
		$count_all = $core->db->get_field();

		if ($count_all > $this->pergroup) {
			$count_delete = $count_all - $this->pergroup;
			$sql = "SELECT `id` FROM `mcms_history` WHERE `tablename` = '{$tableName}' ORDER BY `id` ASC LIMIT {$count_delete}";
			$core->db->query($sql);
			$core->db->get_rows();
			foreach ($core->db->rows as $row) {
				$sql = "DELETE FROM `mcms_history_data` WHERE `tablename` = '{$tableName}' AND `id_history` = {$row['id']}";
				$core->db->query($sql);
			}
			$sql = "DELETE FROM `mcms_history` WHERE `tablename` = '{$tableName}' ORDER BY `id` ASC LIMIT {$count_delete}";
			$core->db->query($sql);
		}

		$sql = "SELECT primary_value , count(primary_value) as count FROM `mcms_history` WHERE tablename = '{$tableName}' GROUP BY primary_value ORDER BY `id` ASC";
		$core->db->query($sql);
		$core->db->get_rows();
		$count_groups = $core->db->rows;

		if (count($count_groups) > ($this->pergroup / 4)) {
			$count_delete = round(count($count_groups) - ($this->pergroup / 4));
			for ($i = 0; $i < $count_delete; $i++) {
				$sql = "SELECT `id` FROM `mcms_history` WHERE `tablename` = '{$tableName}' AND `primary_value` = {$count_groups[$i]['primary_value']}";
				$core->db->query($sql);
				$core->db->get_rows();
				foreach ($core->db->rows as $row) {
					$sql = "DELETE FROM `mcms_history_data` WHERE `tablename` = '{$tableName}' AND `id_history` = {$row['id']}";
					$core->db->query($sql);
				}
				$sql = "DELETE FROM `mcms_history` WHERE `tablename` = '{$tableName}' AND `primary_value` = {$count_groups[$i]['primary_value']}";
				$core->db->query($sql);
			}
		}

//		пробегаемся по всем записям
//		смотрим если количество записей группы больше чем среднее между 1000 и кол-вом группы - чистим до ceil(1000 / кол-во групп)

		$sql = "SELECT primary_value , count(primary_value) as count FROM `mcms_history` WHERE tablename = '{$tableName}' GROUP BY primary_value ORDER BY `id` ASC";
		$core->db->query($sql);
		$core->db->get_rows();
		$count_groups = $core->db->rows;

		foreach ($count_groups as $row) {
			if ($row['count'] > ($this->pergroup / count($count_groups))) {
				$count_delete = round($row['count'] - ($this->pergroup / count($count_groups)));
				$sql = "SELECT `id` FROM `mcms_history` WHERE `tablename` = '{$tableName}' AND `primary_value` = {$row['primary_value']} ORDER BY `id` ASC LIMIT {$count_delete}";
				$core->db->query($sql);
				$core->db->get_rows();
				foreach ($core->db->rows as $arow) {
					$sql = "DELETE FROM `mcms_history_data` WHERE `tablename` = '{$tableName}' AND `id_history` = {$arow['id']}";
					$core->db->query($sql);
				}
				$sql = "DELETE FROM `mcms_history` WHERE `tablename` = '{$tableName}' AND `primary_value` = {$row['primary_value']} ORDER BY `date` ASC LIMIT {$count_delete}";
				$core->db->query($sql);
			}
		}

		return false;
	}


}




?>