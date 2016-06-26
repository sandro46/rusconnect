<?php
class ModuleAdminMenu extends widgets implements iwidget 
{
		
	public function main()
	{
		global $core;
		
		$core->tpl->assign('submenu', $core->modules->this['menu']);
		$html = $core->tpl->get('wg.ModuleAdminMenu.html', $core->getAdminModule());
		
		
		$this->run($html);
	}
	
	public function out()
	{
		
		
	}
	
	
}
?>