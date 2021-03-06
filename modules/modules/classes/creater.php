<?php

// � роверка � роверка

class src_creater
{
	public $controller = array();
	public $config = array();
	public $module = array();
	public $data = array();
	public $indexer = array();
	public $location = '';
	public $path = '';
	
	public function __construct()
	{
		global $core;
		
		$this->data = $_SESSION['controllers'];
		$this->indexer = $_SESSION['controllers_indexer'];
		$this->location = str_replace('/', '', $_POST['location']);
		$this->path = $core->CONFIG['module_dir'];
	}
	
	public function process()
	{		
		$this->create_file_structure();
		$this->create_config_file();
		
		if(count($this->data)) $this->create_controllers_tpl();
		if(count($this->data)) $this->create_controllers();
		if($_POST['createTemplate']) $this->create_main_tpl();
		if($_POST['createDefaultClass']) $this->create_default_class();
		if($_POST['createAjaxFile']) $this->createAjaxFile();
			
		$this->clear_session_data();
	}
	
	public function get_controller_src($controller_name)
	{
		$cid 	= $this->indexer[$controller_name];
		$info 	= $this->data[$cid];
		
		$src 	 = $this->get_controller_header();
		$src 	.= $this->get_controller_body($info);
		
		return $src;
	}
	
	

	
	private function create_config_file()
	{
		global $core;
		
		$src = $this->get_config_header();
		$src .=  $this->get_config_body();
		
		$fp = fopen($this->get_module_dir().'config.php', 'w');
		fwrite($fp, $src);
		fclose($fp);
	}
		
	private function get_config_header()
	{
		$text = '<?php';
		$text .= "\n";
		$text .= "################################################################################\n";
		$text .= "# This file was created by M-cms core.                                         #\n";
		$text .= "#                                                                              #\n";
		$text .= "# This module configuration file.                                              #\n";
		$text .= "# There you can add entry in the admin menu, specify which of the existing     #\n";
		$text .= "# can use controllers. You can also specify the parameters of caching          #\n";
		$text .= "# and specify template will be used.                                           #\n";
		$text .= "# If in process call module does not find this file, it will be called 404.    #\n";
		$text .= "#                                                                              #\n";
		$text .= "# If you want to use any controller, you'll need to specify                    #\n";
		$text .= "# in \$controller->add_controller();                                            #\n";
		$text .= "# Also, you must specify the controller on default call.                       #\n";
		$text .= "# This controller will be called if the URL is not specified controller        #\n";
		$text .= "# or if specified controller was not found or not connected.                   #\n";
		$text .= "#                                                                              #\n";
		$text .= "# ---------------------------------------------------------------------------- #\n";
		
		$msg_line	= "# ---------------------------------------------------------------------------- ";
		$msg_core   = "# @Core: ".CORE_VERSION;
		$msg_author = "# @Author: ".AUTHOR;
		$msg_date   = "# @Date: ".DATE_STAMP;
		$msg_prod   = "# ".PRODUCT;
		
		$text .= $msg_author.str_repeat(" ", strlen($msg_line)-strlen($msg_author))."#\n";
		$text .= $msg_date.str_repeat(" ", strlen($msg_line)-strlen($msg_date))."#\n";
		$text .= $msg_line."#\n";
		$text .= $msg_prod.str_repeat(" ", strlen($msg_line)-strlen($msg_prod))."#\n";
		
		$text .= "################################################################################\n\n\n\n";
		
		return $text;
	}
	
	private function get_config_body()
	{
		$text = '';
		$controllers = '';
		
		if(count($this->data))
		{
			foreach($this->data as $info)
			{
				if($info['menu'] && strlen($info['menu'])>2)
				{
					$text .= "\$core->modules->this['menu'][]= array('controller' => '".str_replace('.php', '', $info['name'])."' , 'name'=>'".$info['menu']."', 'action_id'=>".$info['action'].");\n";
					$controllers .= ", '".str_replace('.php', '', $info['name'])."'";
				}
			}
		}
		
		
		$controllers = substr($controllers, 2);
		
		$text .= "\$core->modules->this['controllers_path']= '/controllers/';\n\n\n";
		$text .= "\$core->modules->add_controller(".$controllers.");\n";
		$text .= "\$core->modules->add_default_controller('list');\n\n\n";
		$text .= "\$core->modules->this['tpl']['cach_param']= array('_site' => 1 , '_lang' => 1 , '_module' => 1 , '_action' => 1);\n";
		$text .= "\$core->modules->this['tpl']['cach_expire'] = 0;\n";
		$text .= "\$core->modules->this['tpl']['cached'] = 0;\n";
		$text .= "\$core->modules->this['tpl']['name'] = 'main.html';\n";
		$text .= "\n\n\n\n?>";

		return $text;
	}
		
	private function get_controller_title($str)
	{
		$title = str_replace('$module', '\'.$core->modules->this[\'describe\'].\'', $str);
		$title = str_replace('$controller', '\'.$controller->descr.\'', $title);
		
		return $title;
	}
	
	private function get_controller_header()
	{
		$text = '<?php';
		$text .= "\n";
		$text .= "################################################################################\n";
		$text .= "# This file was created by M-cms core.                                         #\n";
		$text .= "# If you want create a new controller files,                                   #\n";
		$text .= "# look at modules section in admin interface.                                  #\n"; 
		$text .= "#                                                                              #\n"; 
		$text .= "# If you want modify this header, look at /modules/modules/class/modules.php   #\n";
		$text .= "# ---------------------------------------------------------------------------- #\n";
		$text .= "# In this controlle you can use all core api through \$core variable            #\n";
		$text .= "# also there is other components api:                                          #\n";
		$text .= "#     \$controller = Controller object. Look at /classes/controllers.php        #\n";
		$text .= "#     \$ajax = Ajax api object. Look as /classes/ajax.php                       #\n";
		$text .= "# In this file you must to specify the action id and set cached flag           #\n"; 
		$text .= "# and call ini method.                                                         #\n";
		$text .= "# If you can use template in this controole, please specify variable \"tpl\".    #\n";
		$text .= "# Example:                                                                     #\n";
		$text .= "#     \$controller->id = 1; Controller action id = 1. Look at database.         #\n";
		$text .= "#     \$controller->cached = 0; Cache system is off                             #\n";
		$text .= "#     \$controller->init(); Call controller initiated method                    #\n";
		$text .= "#     \$controller->tpl = 'filename'; Template name.                            #\n";
		$text .= "# You can specify the template in any line of controller, but                  #\n";
		$text .= "# if you want to use caching, you must specify the template to call            #\n";
		$text .= "# the method of checking the cache.                                            #\n";
		$text .= "# If you can break controoler logic, to call \$core->footer()                   #\n";
		$text .= "# If you need help, look at api documentation.                                 #\n";		                                                       
		$text .= "# ---------------------------------------------------------------------------- #\n";

		$msg_line	= "# ---------------------------------------------------------------------------- ";
		$msg_core   = "# @Core: ".CORE_VERSION;
		$msg_author = "# @Author: ".AUTHOR;
		$msg_date   = "# @Date: ".DATE_STAMP;
		$msg_prod   = "# ".PRODUCT;
		
		$text .= $msg_core.str_repeat(" ", strlen($msg_line)-strlen($msg_core))."#\n";
		$text .= $msg_author.str_repeat(" ", strlen($msg_line)-strlen($msg_author))."#\n";
		$text .= $msg_date.str_repeat(" ", strlen($msg_line)-strlen($msg_date))."#\n";
		$text .= $msg_line."#\n";
		$text .= $msg_prod.str_repeat(" ", strlen($msg_line)-strlen($msg_prod))."#\n";
		
		$text .= "################################################################################\n\n\n\n";
		
		return $text;
	}
	
	private function get_controller_body($data)
	{
		$text  = '';
		
		$text .= "\$controller->id = ".intval($data['action']).";\n";  
		$text .= "\$controller->cached = ".intval($data['cached']).";\n";
		$text .= "\$controller->cache_param  = array('_site' => 1 , '_lang' => 1 , '_url' => 1, '_uid'=>1);\n";
		$text .= "\$controller->cache_expire = ".intval($data['cachelifetime']).";\n";
		$text .= "\$controller->init();\n";
		$text .= "\$controller->tpl = '".addslashes($data['tpl'])."';\n";
		$text .= "\$controller->cached();\n\n\n";
		$text .= "\$core->title = '".$this->get_controller_title($data['title'])."';\n\n\n";
		
		if(isset($_POST) && isset($_POST['createDefaultClass']) && $_POST['createDefaultClass'] == 1)
		{
			$text .= "\$controller->load('admin.".$_POST['name'].".php');\n";
			$text .= "\$root = new admin_".$_POST['name']."();\n\n\n";
		}
		else 	
			{
				$text .= "//\$controller->load('className.php');\n\n\n\n";
			}
		
		
		$text .= "// Controller logic, source code\n\n\n\n\n\n";
		
		if($data['includeajax'])
		{
			$text .= "\$core->lib->load('ajax');\n";
			$text .= "\$ajax = new ajax();\n";
			$text .= ($data['ajaxdebug'])? '$ajax->debug_mode = 1;' : '$ajax->debug_mode = 0;';
			$text .= "\n";
			$text .= "\$ajax->request_type = 'POST';\n";
			$text .= "\$ajax->add_func('');\n";
			$text .= "\$ajax->init();\n";
			$text .= "\$core->tpl->assign('ajax_output', \$ajax->output);\n";
			$text .= "\$ajax->user_request();\n\n";
		}
	
		
		$text .= "?>";
		
		return $text;
	}
	
	private function create_controllers()
	{	
		if(count($this->data) >0)
		{
			foreach($this->data as $ctrl)
			{
				$src = $this->get_controller_src($ctrl['name']);
				
				$fp = fopen($this->get_module_dir().'controllers/'.$ctrl['name'], 'w');
				fwrite($fp, $src);
				fclose($fp);
			}
		}
	}
	
	private function create_controllers_tpl()
	{
		global $core;
		
		$id_template = $core->MaxId('id_template', 'mcms_tmpl');
		
		foreach($this->data as $ctrl)
		{
			if($ctrl['autocreatetpl'])
			{
				$id_template ++;
			
				$src = '<!-- This template for '.$ctrl['name'].' -->';
				$dsc = 'Template for controller '.$ctrl['name'].'. (c) M-cms core system';
			
				foreach($core->get_all_langs() as $lang) $data[] = array('id_template'=>$id_template, 'id_site'=>1, 'name_module'=>$_POST['name'], 'name'=>$ctrl['tpl'], 'description'=>$dsc, 'source'=>$src, 'lang_id'=>$lang['id'], 'date'=>time(), 'del'=>0);
			}
		}
		
		$core->db->autoupdate()->table('mcms_tmpl')->data($data);
		$core->db->execute();
	}

	private function create_main_tpl()
	{
		global $core;
		
		$id_template = $core->MaxId('id_template', 'mcms_tmpl')+1;
			
		$tpl_html = "{\$ModuleAdminMenuOut}\n<br><br>\n{\$controller_content}\n";
		
		foreach($core->get_all_langs() as $lang) $data[] = array('id_template'=>$id_template, 'id_site'=>1, 'name_module'=>$_POST['name'], 'name'=>'main.html', 'description'=>'Main module template. (c) M-cms core system', 'source'=>$tpl_html, 'lang_id'=>$lang['id'], 'date'=>time(), 'del'=>0);
		
		$core->db->autoupdate()->table('mcms_tmpl')->data($data);
		$core->db->execute();
	}
	
	private function create_default_class()
	{		
		$src = $this->get_empty_class_src('admin_'.$_POST['name']);
		$fp = fopen($this->get_module_dir().'classes/admin.'.$_POST['name'].'.php', 'w');
		fwrite($fp, $src);
		fclose($fp);
		
		$src = $this->get_empty_class_src('client_'.$_POST['name']);
		$fp = fopen($this->get_module_dir().'classes/client.'.$_POST['name'].'.php', 'w');
		fwrite($fp, $src);
		fclose($fp);
	}
	
	private function get_empty_class_src($name)
	{
		$src = '';
		
		$src .= "<?php\n\n";
		$src .= "class ".$name."\n";
		$src .= "{\n\n\n";
		$src .= "	public function __construct()\n";
		$src .= "	{\n";
		$src .= "		// Empty\n";
		$src .= "	}\n";
		$src .= "\n\n";
		$src .= "	public function get_list()\n";
		$src .= "	{\n";
		$src .= "		global \$core;\n\n";
		$src .= "		// Empty\n\n";
		$src .= "	}\n";
		$src .= "\n\n\n";		
		$src .= "}\n\n\n?>";
		
		return $src;
	}
		
	private function createAjaxFile()
	{
		$src  = "<?php\n\n";
		$src .= "// This file was created by M-cms core system for ajax functions";
		$src .= "\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n?>";
		
		$fp = fopen($this->get_module_dir().'includes/ajax.php', 'w');
		fwrite($fp, $src);
		fclose($fp);
	}
	
	private function clear_session_data()
	{
		unset($_SESSION['controllers']);
		unset($_SESSION['controllers_indexer']);
	}
	
	private function get_module_dir()
	{
		return $this->path.$this->location.'/';
	}
	
	private function create_file_structure()
	{
		global $core;
		
		if(file_exists($this->get_module_dir())) die('Module folder has ben exists.');
		mkdir($this->get_module_dir(), 0777) or die('Can not created module directory.');
		mkdir($this->get_module_dir().'classes/', 0777) or die('Can not created classes directory.');
		mkdir($this->get_module_dir().'includes/', 0777) or die('Can not created includes directory.');
		mkdir($this->get_module_dir().'controllers/', 0777) or die('Can not created controllers directory.');
	}

}


?>