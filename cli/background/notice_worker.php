<?php


//include '../environment/config.php';
define('ENV', (dirname(dirname(__FILE__))).'/environment/');
define('RUN_FROM_CLI', true);
include dirname(dirname(dirname(__FILE__))) .'/config.php';
include ENV.'mysql.php';
include ENV.'debug_class.php';
include ENV.'templates.php';


class core {
    public $CONFIG = array();
    public $db;
    public $notice;
    public $shop;
    public $shopProxy;
    public $log;

    public static $instance;
    
    public function __construct($config) {
        $this->CONFIG = $config;
        $this->log = new debug_class();
        $this->db = new mysql($this->CONFIG);
        $this->db->connect();
        $this->notice = new notifications();
        
        self::$instance = $this;
    }
}



class main_module {
    public $core;
    public $db;
    
    public function init() {
        $this->core = core::$instance;
        $this->db = $this->core->db;
    }
}

class module extends main_module {
    
}




include CORE_PATH.'modules/notice/classes/notifications.php';
include CORE_PATH.'modules/shop/classes/admin.shop.php';


// for cli dev!
//$core = new core($config);
//$core->notice->init();
//$core->notice->process(23);


// for supervisor
$worker= new GearmanWorker();
$worker->addServer();
$worker->addFunction("process:rc:notice", function($job) use ($config) {
    $data = $job->workload();
    $core = new core($config);
    $core->notice->init();
    $core->notice->process($data);
});
while ($worker->work());






