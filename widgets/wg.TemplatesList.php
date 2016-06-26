<?php


class TemplatesList extends widgets implements iwidget 
{
	public $limit = 10;
	public $filter = '';
	public $keyword = '';	
	
	private $list = array();
	private $html = '';
	
	public function main()
	{
		if($filter && $keyword)
		{
			$this->core->db->select()->from('mcms_tmpl')->fields('name_module', 'id_template', 'name', 'description', 'date')->order('date')->where('lang_id = '.$this->core->CONFIG['lang']['id'].' AND `theme` = "'.$this->core->theme.'" AND `'.addslashes($this->filter).'` = "'.addslashes($this->keyword).'" AND `del`=0 AND `id_site` = '.$this->core->edit_site)->limit($this->limit);
		}
		else
			{
				$this->core->db->select()->from('mcms_tmpl')->fields('name_module', 'id_template', 'name', 'description', 'date')->order('date')->where('lang_id = '.$this->core->CONFIG['lang']['id'].' AND `theme` = "'.$this->core->theme.'" AND `del`=0 AND `id_site` = '.$this->core->edit_site)->limit($this->limit);	
			}
		
		//include CORE_PATH.'modules/templates/classes/templates.php';	
			
		$this->core->db->execute();
		$this->core->db->colback_func_param = 0;
		$this->core->db->add_fields_deform(array('date', 'description'));
		$this->core->db->add_fields_func(array('date,"d.m.Y H:i"', 'slicetext,45'));	
		$this->core->db->get_rows();
		$this->core->tpl->assign('WidgetTemplatesList', $this->core->db->rows);
		//$this->core->tpl->assign('allThems', $this->core->tpl->getThemsList());
		//$this->core->tpl->assign('tpl_modules', admin_templates::get_module_list());
		
		$this->list = $this->core->db->rows;
		//$this->html = $this->core->tpl->get('wg.TemplatesList.html', 'mcms-admin');	
		
		//$this->run($this->html);
	}
	
	public function out()
	{
		return $this->list;
	}
	
	
	
	
}





?>