<?php

class admin_glossary {
	public $dbTable = 'site_glossary';

	public function __construct() {
	}

	public function getNavigationHTML() {
		global $core;

		switch ($core->CONFIG['lang']['id']) {
			case(1): // RU
				$sql = "SELECT lang_id , SUBSTRING(name, 1, 1) as `index` FROM {$this->dbTable} WHERE name != '' AND (lang_id = 1 OR lang_id = 2) GROUP BY `index` ORDER BY lang_id ASC, name ASC";
			break;
			default: // Other
				$sql = "SELECT lang_id , SUBSTRING(name, 1, 1) as `index` FROM {$this->dbTable} WHERE name != '' AND lang_id = 2 GROUP BY `index` ORDER BY lang_id ASC, name ASC";
			break;
		}

		$core->db->query($sql);
		$core->db->get_rows();
		foreach ($core->db->rows as $row) {
			$data[$row['lang_id']][] = array('index' => $row['index'], 'lang_id' => $row['lang_id']);
		}

		$core->tpl->assign('tableofcontents', $data);
		return $core->tpl->fetch('tableofcontents.html',1,0,0,'glossary');
	}

	public function getData() {
		global $core;

		switch ($core->CONFIG['lang']['id']) {
			case(1): // RU
				if (isset($_GET['letter'])) {
					$letter = urldecode(stripslashes($_GET['letter']));
				} else {
					$letter = 'A';
				}

				$like = " AND name like '{$letter}%'";

				if (preg_match('/[a-z]/i',$letter)) {
					$sql = "SELECT en.item_id , CONCAT(en.name, ' (', (SELECT name FROM {$this->dbTable} WHERE lang_id = 1 AND item_id = en.item_id), ')') as name , (SELECT description FROM {$this->dbTable} WHERE lang_id = 1 AND item_id = en.item_id) as description FROM {$this->dbTable} as en WHERE lang_id = 2 AND en.name != ''{$like} ORDER BY lang_id ASC, name ASC";
				} else {
					$sql = "SELECT ru.item_id , CONCAT(ru.name, ' (', (SELECT name FROM {$this->dbTable} WHERE lang_id = 2 AND item_id = ru.item_id), ')') as name , ru.description FROM {$this->dbTable} as ru WHERE lang_id = 1 AND ru.name != ''{$like} ORDER BY lang_id ASC, name ASC";
				}
			break;
			case(2): // EN
				if (isset($_GET['letter'])) {
					$letter = urldecode(stripslashes($_GET['letter']));
					$like = " AND name like '{$letter}%'";
				} else {
					$like = " AND name like 'A%'";
				}

				$sql = "SELECT item_id , name , description FROM {$this->dbTable} WHERE lang_id = 2 AND name != ''{$like} ORDER BY lang_id ASC, name ASC";
			break;
			default: // Other
				if (isset($_GET['letter'])) {
					$letter = urldecode(stripslashes($_GET['letter']));
					$like = " AND name like '{$letter}%'";
				} else {
					$like = " AND name like 'A%'";
				}

				$sql = "SELECT en.item_id , CONCAT(en.name, ' (', (SELECT name FROM {$this->dbTable} WHERE lang_id = {$core->CONFIG['lang']['id']} AND item_id = en.item_id), ')') as name , (SELECT description FROM {$this->dbTable} WHERE lang_id = {$core->CONFIG['lang']['id']} AND item_id = en.item_id) as description FROM {$this->dbTable} as en WHERE lang_id = 2 AND en.name != ''{$like} ORDER BY lang_id ASC, name ASC";
			break;
		}

		$core->db->query($sql);
		$core->db->get_rows();
		foreach ($core->db->rows as $row) {
			$data[] = array('item_id' => $row['item_id'],  'name' => $row['name'], 'description' => nl2br($row['description']));
		}
		return $data;
	}

	public function editItem($item_id) {
		global $core;

		$core->tpl->assign('item_id', $item_id);

		$sql = "SELECT name, description, lang_id FROM {$this->dbTable} WHERE item_id = {$item_id}";
		$core->db->query($sql);
		$core->db->get_rows();
		foreach($core->db->rows as $row) {
			$data[$row['lang_id']]['word'] = $row['name'];
			$data[$row['lang_id']]['text'] = $row['description'];
		}

		return $data;
	}

	public function save() {
		global $core;

		$item_id = intval($_POST['item_id']);
		if ($item_id == 0) $item_id = $core->MaxId('item_id', $this->dbTable) + 1;

		if (!is_array($_POST['name']) || !is_array($_POST['description'])) {
			return false;
		}

		$names = $_POST['name'];
		$descriptions = $_POST['description'];

		foreach ($names as $lang_id => $name) {
			$data[] = array(
				'item_id' => $item_id,
				'lang_id' => $lang_id,
				'name' => addslashes($name),
				'description' => addslashes($descriptions[$lang_id]),
			);
		}

		$core->db->autoupdate()->table($this->dbTable)->data($data)->primary('item_id','lang_id');
		$core->db->execute();

		return true;
	}

	public function delete($item_id) {
		global $core;

		$sql = "DELETE FROM {$this->dbTable} WHERE item_id = {$item_id}";
		$core->db->query($sql);

		return true;
	}
}

?>