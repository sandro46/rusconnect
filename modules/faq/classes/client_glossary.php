<?php

class client_glossary {
	public $dbTable = 'site_glossary';

	public function __construct() {
		global $core;

		$core->navigation->init();
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
					$letter = stripslashes(urldecode($_GET['letter']));
					// Little hack to support win1251 codepage
					if (strlen($_GET['letter']) == 3)
						$letter = iconv('cp1251','utf-8',$letter);
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
}

?>