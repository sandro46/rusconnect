<?php 

class main_module extends module {
	public $clientId = 0;
	public $userId = 0;
	public $parentId = 0;
	public $shopId = 0;
	
	public $clientInfo = array();
	public $userInfo = array();
	public $shopInfo = array();
	public $userSetting = array();
	public $partnerInfo = array();
	
	protected $eventCache = array();
	protected $global_api = null;
	
	
	public static $instance = array();
	public static $loaded = false;
	public $notice = false;
	
	public function __construct() {
		$this->module();
		$this->init();
	}
	
	public static function login($login, $password) {
		$siteId = core::$instance->site_id;
		$sql = "SELECT 
					u.user_id,
					u.enabled as user_active, 
					u.block_reason as user_block_reason,
					c.id as client_id,
					c.system_user_id,
					c.enabled as client_active, 
					c.block_reason as client_block_reason
				FROM 
					a_users as u 
						LEFT JOIN a_clients as c ON c.id = u.client_id
				
				WHERE 
					u.login = '{$login}' AND
					u.password = '{$password}'";

		
		core::$instance->db->query($sql);
		core::$instance->db->get_rows(1);

		$uin = core::$instance->db->rows;
				
		return (!is_array($uin) || !isset($uin['user_id']))? false : $uin;
	}
	
	public function init() {
		//unset($_SESSION['cache:clientInstance']);

		if(isset($_SESSION['cache:clientInstance'])) {
			$clientInstance = $_SESSION['cache:clientInstance'];
			$this->clientInfo = $clientInstance['clientInfo'];
			$this->clientId = $clientInstance['clientId'];
			$this->userSetting = $clientInstance['userSetting'];
			$this->userInfo = $clientInstance['userInfo'];
			$this->userId = $clientInstance['userId'];
			$this->shopId = $clientInstance['shopId']; 
			$this->shopInfo = $clientInstance['shopInfo']; 
		} else {
			// procedure for initialize client
			$this->userId = $this->user->custom_id;
			$clientInstance = $this->updateClientCache();
		}
		
		if(self::$loaded) return true;
		
		if(!$this->checkUserBlock()) {
			session_unset();
			header('Location: /ru/-utils/login/');
			die();
		}
		
		self::$instance = $clientInstance; 
		
		$this->tpl->assign('userSetting', $this->userSetting);
		$this->tpl->assign('clientInfo', $this->clientInfo);
		$this->tpl->assign('userInfo', $this->userInfo);
		$this->tpl->assign('shopInfo', $this->shopInfo);
			
		global_admin_api::$clientId = $this->clientId;
		global_admin_api::$userId = $this->userId;
		
		$this->ajax->register('global_admin_api', 'saveUserSetting', 0, 'globalApi');
		$this->ajax->listen('globalApi::saveUserSetting');
		$this->ajax->register('global_admin_api', 'changeSiteEditId', 0, 'globalApi');
		$this->ajax->listen('globalApi::changeSiteEditId');
		$this->ajax->register('global_admin_api', 'sessionCheck', 0, 'globalApi');
		$this->ajax->listen('globalApi::sessionCheck');
		
		
		
		if($this->clientId <= 2) {
			// is admin!
		} else {
			if(!self::$loaded && MAINTENANCE ) {
				//self::$loaded = true;
				$this->tpl->assign('dont_show_title', true);
				$this->core->title = 'Технические работы.';
				$this->core->modules->this['tpl']['name'] = false;
				$this->core->content = $this->tpl->get('maintenances.html', 'Cabinet');
				$this->core->footer();
			}
			
			
			if(!$this->shopInfo && !self::$loaded) {
				$_GET['module'] = 'site';
				$_GET['controller'] = 'welcome';
				//self::$loaded = true;
				$this->core->loader->init();
			}
		}

		$this->tpl->assign('mySites', $this->loadSites());
		self::$loaded = true;
		
		if(!class_exists('notifications')) {
		    $this->core->lib->loadModuleFiles('notice', 'notifications.php');
		}
	
		
	   if($this->notice instanceof notifications) {
		    
		} else {
		    $this->notice = new notifications();
		    $this->notice->init();
		}
	}
	
	public function updateClientCache() {
		$this->loadClientInfo();
		$clientInstance = array(
				'clientInfo'=>$this->clientInfo,
				'clientId'=>$this->clientId,
				'userSetting'=>$this->userSetting,
				'userInfo'=>$this->userInfo,
				'userId'=>$this->userId,
				'shopInfo'=>$this->shopInfo,
				'shopId'=>$this->shopId
		);
		
		$_SESSION['cache:clientInstance'] = $clientInstance;
		
		return $clientInstance;
	}
	
	public function getAccessRules($groupId, $userId, $objectId, $action = false) {
		$sql = "SELECT gr.read, gr.write, gr.delete FROM a_access_groups_rules as gr WHERE group_id = {$groupId} AND object_id = {$objectId} AND (client_id = 0 OR client_id = {$this->clientId})";
		$this->db->query($sql);
		$groupRules = $this->db->get_rows(1);
		
		if(empty($groupRules)) {
			$groupRules = array('read'=>false, 'write'=>false, 'delete'=>false);
		}
		
		if(!$userId) {
			return $groupRules;
		}
				
		$sql = "SELECT ur.read, ur.write, ur.delete FROM a_access_user_rules as ur WHERE client_id = {$this->clientId} AND user_id = {$userId} AND object_id = {$objectId}";
		$this->db->query($sql);
		$userRules = $this->db->get_rows(1);
		
		if($action) {
			if(!in_array($action, $userRules) && !in_array($action, $groupRules)) {
				return false;
			} else if(!in_array($action, $userRules)) {
				return $groupRules[$action];
			} else {
				return $userRules[$action];
			}
		}
		
		return (empty($userRules))? $groupRules : $userRules;
	}
	
	public function logAuth() {
	    $this->core->lib->load('analytics');
	    $stat = new analytics();
	    $visitInfo = $stat->getInfo();
	    
	    $query = array(
	        'client_id'=>$this->clientId,
	        'shop_id'=>$this->shopId,
	        'user_id'=>$this->userId,
	        'date'=>time(),
	        'ipaddress'=>$visitInfo['ip'],
	        'os'=>$visitInfo['device']['os']['name'],
	        'browser'=>$visitInfo['device']['client']['name'],
	        'browser_version'=>$visitInfo['device']['client']['version'],
	        'device_type'=>$visitInfo['device']['device']['type'],
	        'os_family'=>$visitInfo['device']['os_family'],
	        'source'=>3
	    );
	    
	    $referer = substr(strstr($_SERVER['HTTP_REFERER'], '://'), 3);
	    $referer = strstr($referer, '/', true);
	    
	    if($referer == 'ncity.biz') {
	        $query['source'] = 1;
	    } elseif($referer == 'my.ncity.biz') {
	        $query['source'] = 2;
	    }
	    
	    $this->db->autoupdate()->table('system_auth_log')->data(array($query));
	    $this->db->execute();
	}
	
	public function notice($event, $data) {
	    $eventId = 0;
	    
        if(!isset($this->eventCache[$event])) {
            $event = explode('.',$event);
            if(count($event) != 2) return false;
            $object = mysql::str($event[0]);
            $event = mysql::str($event[1]);
            $sql = "SELECT e.id FROM tp_notice_event as e LEFT JOIN tp_notice_event_object as eo ON e.object_id = eo.id WHERE eo.object_alias = '{$object}' AND e.event_alias = '{$event}'";
            $this->db->query($sql);
            $eventId = intval($this->db->get_field());
            $this->eventCache[$event] = $eventId;
        } else {
            $eventId = $this->eventCache[$event];
        }
        
        if(!$eventId) return false;
        
        $sql = "SELECT * FROM tp_notice_notifications WHERE client_id = {$this->clientId} AND shop_id = {$this->shopId} AND event_id = {$eventId} AND enabled = 1";
        $this->db->query($sql);
        $notices = $this->db->get_rows();
        
        if(!empty($notices)) {
            $query = array();
            foreach($notices as $item) {
                $query = array(
                    'client_id'=>$this->clientId,
                    'shop_id'=>$this->shopId,
                    'notice_id'=>$item['notice_id'],
                    'message_data'=>mysql::str(json_encode($data)),
                    'message_setting'=>mysql::str(json_encode($item['data'])),
                    'message_type'=>intval($item['action_id']),
                    'date'=>time()
                );
    
                $this->db->autoupdate()->table('tp_notice_queue')->data(array($query));
                $this->db->execute();
                $messageId = $this->db->insert_id;
    
                // send to queue!!
            }
        }
	}
	
	protected function loadSites() {
		$sql = "SELECT shop_id, url FROM tp_shop WHERE client_id = {$this->clientId}";
		$this->db->query($sql);
		$this->db->get_rows();
		
		return $this->db->rows;
	}
	
	protected function checkUserBlock() {
		$sql = "SELECT u.enabled as user_enable, c.enabled as client_enable FROM a_users as u LEFT JOIN a_clients as c ON c.id = u.client_id WHERE u.client_id = {$this->clientId} AND u.user_id = {$this->userId}";
		$this->db->query($sql);
		$this->db->get_rows(1);
		$user = $this->db->rows;

		if(empty($user) || !isset($user['user_enable'])) return false;
		if(!intval($user['user_enable'])) return false;
		if(!intval($user['client_enable'])) return false;
		
		return true;
	}
	
	protected function loadClientInfo($byClientId = false) {
		if(!$byClientId) {
			$sql = "SELECT
						u.user_id,
						u.enabled as user_active,
						u.block_reason as user_block_reason,
						u.name_first,
						u.name_last,
						u.name_second,
						u.contact_id,
						u.reg_date,
						u.email,
						c.system_user_id,
						c.id as client_id
					FROM
						a_users as u
							LEFT JOIN a_clients as c ON c.id = u.client_id
					WHERE
						u.user_id = {$this->userId}";

			$this->db->query($sql);
			$this->db->get_rows(1);
			$this->clientId = $this->db->rows['client_id'];
			$this->userInfo = $this->db->rows;
		} else {
			$this->userInfo = array();
			$this->clientId = $byClientId;
		}
		
		$sql = "SELECT * FROM a_clients WHERE id = {$this->clientId}";
		$this->db->query($sql);
		$this->db->get_rows(1);
		$this->clientInfo = $this->db->rows;
		
		$sql = "SELECT name, data FROM system_user_settings WHERE client_id = {$this->clientId}";
		$this->db->query($sql);
		$this->db->get_rows();
		$this->userSetting = array();
		
		foreach($this->db->rows as $item) {
			$this->userSetting[$item['name']] = json_decode($item['data'], true);
		}
				
		if($this->core->site_id == 2) {		
			if(isset($_SESSION['templates_site_id']) && !empty($_SESSION['templates_site_id'])) {
				$siteId = intval($_SESSION['templates_site_id']);
				$sql = "SELECT s.*, ms.name as site_system_name, ms.server_name as site_mirror FROM tp_shop as s LEFT JOIN mcms_sites as ms ON ms.id = s.shop_id WHERE s.client_id = {$this->clientId} AND s.shop_id = {$siteId}";
			} else {
				$sql = "SELECT s.*, ms.name as site_system_name, ms.server_name as site_mirror FROM tp_shop as s LEFT JOIN mcms_sites as ms ON ms.id = s.shop_id WHERE s.client_id = {$this->clientId}";
			}
		} else {
			$siteId = $this->core->site_id;
			$sql = "SELECT s.*, ms.name as site_system_name, ms.server_name as site_mirror FROM tp_shop as s LEFT JOIN mcms_sites as ms ON ms.id = s.shop_id WHERE s.client_id = {$this->clientId} AND s.shop_id = {$siteId}";
		}
		
		$this->db->query($sql);
		$this->db->get_rows(1);
		if(!empty($this->db->rows)) {
			$this->shopInfo = $this->db->rows;
			$this->shopId = $this->shopInfo['shop_id'];
			$this->core->edit_site = $this->shopId;
			$_SESSION['templates_site_id'] = $this->shopId;
			$_SESSION['global_edit_site_id'] = $this->shopId;
			
			$sql = "SELECT 
						sm.*, 
						(SELECT stm1.settings FROM tp_shop_to_modules as stm1 WHERE stm1.client_id = {$this->clientId} AND stm1.shop_id = {$this->shopId} AND stm1.module_id = sm.id ) as settings, 
						(SELECT COUNT(*) FROM tp_shop_to_modules as stm WHERE stm.client_id = {$this->clientId} AND stm.shop_id = {$this->shopId} AND stm.module_id = sm.id ) as used 
					FROM 
						tp_shop_modules as sm 
					WHERE 1";
			$this->db->query($sql);
			$this->db->get_rows(false, 'id');
			$this->shopInfo['modules'] = $this->db->rows;
			
			foreach($this->shopInfo['modules'] as $k=>$item) {
				if(strlen($item['settings'])) {
					$this->shopInfo['modules'][$k]['settings'] = json_decode($item['settings'], true);
				}
			}
		} else {
			$this->shopId = 0;
			$this->shopInfo = '';
		}
	}	
}

class global_module {
	public static $instance = false;
}

class global_client_api extends main_module {
	
	public function __construct() {
		$this->module();
		$this->init();
	}
	
	public function init() {
		$sql = "SELECT ts.*, ms.server_name FROM tp_shop as ts LEFT JOIN mcms_sites as ms ON ms.id = ts.shop_id WHERE ts.shop_id = {$this->core->site_id}";
		$this->db->query($sql);
		$this->db->get_rows(1);

		$this->site = $this->db->rows;
		$this->shopId = $this->core->site_id;
		$this->tpl->assign('settings', $this->site);
		$this->clientId = $this->site['client_id'];
		$this->loadClientInfo($this->clientId);
		$this->tpl->assign('shopInfo', $this->shopInfo);
				
	}
	
	
	
}

class global_admin_api  {
	public static $clientId = 0;
	public static $userId = 0;
	
	public static function saveUserSetting($name, $data) {
			$query = array(
					'name'=>mysql::str($name),
					'data'=>json_encode($data),
					'client_id'=>self::$clientId,
					'user_id'=>self::$userId);
		
			core::$instance->db->autoupdate()->table('system_user_settings')->data(array($query))->primary('user_id','client_id','name');
			core::$instance->db->execute();
		
			@$_SESSION['cache:clientInstance']['userSetting'][$name] = $data;
	}
	
	public static function changeSiteEditId($siteId) {
		$siteId = intval($siteId);
		if(!$siteId) return false;
		
		$sql = "SELECT COUNT(*) FROM tp_shop WHERE client_id = ". self::$clientId ." AND shop_id = {$siteId}";
		core::$instance->db->query($sql);
		if(!intval(core::$instance->db->get_field())) return false;
		
		$_SESSION['templates_site_id'] = $siteId;
		$_SESSION['global_edit_site_id'] = $siteId;
		unset($_SESSION['cache:clientInstance']);
		session_commit();
		
		return true;
	}
	
	public static function sessionCheck() {
	    return session_id();
	}
}

?>