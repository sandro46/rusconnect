<?php
class ProductGroups extends widget {

	public $parent_id = 0;
	public $current_id = 0;
	public $showSubLevel = 0;
	public $limit = 0;
	private $shop = null;

	public function load() {
		$this->shop = $this->core->shop;
	}

	public function ProductGroupsAction() {
		$this->limit = intval($this->limit);
		$this->showSubLevel = intval($this->showSubLevel);
		return $this->shop->getCategories($this->parent_id, $this->current_id, 'default', false, false, $this->limit,  $this->showSubLevel);
	}

}