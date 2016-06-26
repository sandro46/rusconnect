<?php 



class notifications extends main_module {
    private $sender = false;
    private $shop;
    private $templates = false;
    private $tplParserPlugins;
    protected $eventCache = array();
    
    
    public function process($id) {
        $id = intval($id);
        $sql = "SELECT q.* FROM tp_notice_queue as q WHERE q.id = {$id}";
        $this->db->query($sql);
        $this->db->get_rows(1);
        $message = $this->db->rows;
        
        $message['message_data'] = json_decode($message['message_data'],true);
        $message['message_setting'] = json_decode(($message['message_setting']),true);
        
        $shopId = intval($message['shop_id']);
        $clientId = intval($message['client_id']);
        $noticeId = intval($message['notice_id']);
        $actionId = intval($message['message_type']);
        $object = $this->getObjectAliasByNoticeId($noticeId,$shopId,$clientId);
        
        $sql = "SELECT * FROM tp_notice_recipient WHERE notice_id = {$noticeId} AND shop_id = {$shopId} AND client_id = {$clientId}";
        $this->db->query($sql);
        $this->db->get_rows();
        $recipients = $this->recipientDataNormalize($this->db->rows);
        $recipients = $this->filterRecipients($recipients, $message['message_type'], $message['message_data'], $shopId, $clientId);
        
        if(!$this->sender) {
            $this->sender = new sender();
        }
        
        if(!$this->templates) {
            $this->initTplInstance();
        }
        
        $shopInfo = $this->getShopInfo($clientId, $shopId);
        $static =  $this->getThemePath($shopId);
        
        $this->templates->assign('shopInfo', $shopInfo);
        $this->templates->assign('url', $shopInfo['url']);
        $this->templates->assign('static', $shopInfo['url'].$static);
        $this->templates->assign($object, $message['message_data']);
        
        $this->sendNotice($actionId, $message['message_setting'], $message['message_data'], $recipients);
    }

    private function sendNotice($actionId, $message, $data, $recipients) {
        $count = 0;
        
        if($actionId == 1) {
            $this->filterTextForCli($message['text']);
            $this->filterTextForCli($message['title']);

            foreach($recipients as $destination) {
                $this->sender->email(mysql::str($message['sender']), mysql::str($destination['data']), $message['title'], $message['text']);
                $count++;
            }
            
            return $count;
        }
        
        if($actionId == 4 || $actionId == 5) {
            $extended = $this->parseExtendedData($message['extended']);
        }
        
        if($actionId == 4) {
            foreach($recipients as $destination) {
                $this->sender->json($message['url'], $message['method'], $data, $extended);
                $count++;
            }
            
            return $count; 
        }
        
        if($actionId == 5) {
             foreach($recipients as $destination) {
                $this->sender->xml($message['url'], $message['method'], $data, $extended);
                $count++;
            }
            
            return $count; 
        }
    }
    
    private function getThemePath($shopId) {
        $sql = "SELECT static_path FROM mcms_themes WHERE id_site = {$shopId}";
        $this->db->query($sql);
        return $this->db->get_field();
    } 
    
    private function getShopInfo($clientId, $shopId) {
        $sql = "SELECT url, name, email, phone, logo FROM tp_shop WHERE client_id = {$clientId} AND shop_id = {$shopId}";
        $this->db->query($sql);
        $this->db->get_rows(1);
        
        return $this->db->rows;
    }
    
    private function filterRecipients($recipients, $typeId, $data, $shopId, $clientId) {
        foreach($recipients as &$item) {
            if($item['recipient_type'] == 'buyer') {
                $item['data'] = $this->getBuyerDestinationByTypeId($typeId, $data);
            }
            
            if($item['recipient_type'] == 'admin') {
                $item['data'] = $this->getAdminDestinationByTypeId($typeId, $clientId);
            }
            
            if($item['recipient_type'] == 'contact_id') {
                $item['data'] = $this->getDestinationByContactId($typeId, $item['data'], $clientId);
            }
            
            if($item['recipient_type'] == 'user_id') {
                $item['data'] = $this->getDestinationByUserId($typeId, $item['data'], $shopId, $clientId);
            }
        }
        
        return $recipients;
    }
    
    private function getAdminDestinationByTypeId($typeId, $clientId) {
        $sql = "SELECT user_id, phone, email FROM a_users WHERE client_id = {$clientId} AND is_admin = 1 ORDER BY user_id  LIMIT 1";
        $this->db->query($sql);   
        $this->db->get_rows(1);
        $info = $this->db->rows;
        
        if($typeId == 1) return $info['email'];
        if($typeId == 2) return $info['phone'];
        if($typeId == 3) return $info['user_id'];
    }
    
    private function getDestinationByUserId($typeId, $userId, $shopId, $clientId) {
        if($typeId == 3) return intval($userId);
        if($typeId == 1) {
            $sql = "SELECT email FROM a_users WHERE client_id = {$clientId} AND user_id = {$userId} LIMIT 1";
            $this->db->query($sql);
            return $this->db->get_field();
        }
        
        if($typeId == 2) {
            $sql = "SELECT phone FROM a_users WHERE client_id = {$clientId} AND user_id = {$userId} LIMIT 1";
            $this->db->query($sql);
            return $this->db->get_field();
        }
    }
    
    private function getDestinationByContactId($typeId, $contactId, $clientId) {        
        if($typeId == 1) {
            $sql = "SELECT email FROM crm_contacts_email WHERE client_id = {$clientId} AND contact_id = {$contactId} LIMIT 1";
            $this->db->query($sql);
            return $this->db->get_field();
        } 
        
        if($typeId == 2) {
            $sql = "SELECT phone FROM crm_contacts_phones WHERE client_id = {$clientId} AND contact_id = {$contactId} LIMIT 1";
            $this->db->query($sql);
            return $this->db->get_field();
        }
    }
    
    private function getBuyerDestinationByTypeId($typeId, $data) {
        if($typeId == 1) return $data['contact']['email'];
        if($typeId == 2) return $data['contact']['phone'];
        if($typeId == 3) return $data['contact_id'];
    }
       
    public function test($actionId, $eventId, $destination, $message) {
       if(!$this->sender) $this->sender = new sender();
        
        
        if($actionId == 1) {
            $this->filterText($objectId, $eventId, $message['text']);
            $this->filterText($objectId, $eventId, $message['title']);
            
            return $this->sender->email(mysql::str($message['sender']), mysql::str($destination), $message['title'], $message['text']);
        } 
        
        if($actionId == 4 || $actionId == 5) {
            $data = $this->getTestDataForMessage($objectId, $eventId);
            $extended = $this->parseExtendedData($eventId, $message['extended']);
        }
        
        if($actionId == 4) {
            return $this->sender->json($message['url'], $message['method'], $data, $extended);
        }
        
        if($actionId == 5) {
            return $this->sender->xml($message['url'], $message['method'], $data, $extended);
        }
    }

    public function parseExtendedData($text) {
        $data = explode("\n", $text);
        if(empty($data)) return false;
        $ret = array();
        
        foreach($data as $item) {
            $name = trim(strstr($item, '=', true));
            $value = trim(substr(strstr($item, '='), 1));
            
            if($name) {
                $ret[$name] = $value;
            }
        }
        
        return $ret;
    }
   
    public function filterTextForCli(&$message) {
        $message = $this->templates->runSource($message, $this->tplParserPlugins);
    }
    
    public function filterText($objectId, $eventId, &$message, $data = false) {
        if(!$this->templates) $this->initTplInstance();
        $objectAlias = $this->getObjectAliasByEventId($eventId);
        
        if(!$data) {
            $data = $this->getTestDataForMessage($objectId, $eventId);
            $this->templates->assign($objectAlias, $data);
        }
        
        $this->templates->assign('url', $this->shopInfo['url']);
        $this->templates->assign('static', $this->shopInfo['url'].'/'.$this->core->themePath);
        $this->templates->assign($objectAlias, $data);
        
        $message = $this->templates->runSource($message, $this->tplParserPlugins);
        return $message;
    }

    private function getNotificationsByEventId($eventId) {
        $sql = "SELECT * FROM tp_notice_notifications WHERE client_id = {$this->clientId} AND shop_id = {$this->shopId} AND event_id = {$eventId} AND enabled = 1";
        $this->db->query();
        $this->db->get_rows();
        
        return $this->db->rows;
    }
    
    private function getNotification($noticeId) {
        $noticeId = intval($noticeId);
        $sql = "SELECT * FROM tp_notice_notifications WHERE client_id = {$this->clientId} AND shop_id = {$this->shopId} AND notice_id = {$noticeId}";
        $this->db->query($sql);
        $this->db->get_rows(1);
        $notice = $this->db->rows;
        $notice['recipient'] = $this->getNoticeRecipients($noticeId);
        $notice['data'] = json_decode($notice['data'], true);
        return $notice;
    }
    
    public function getNoticeRecipients($noticeId) {
        $sql = "SELECT * FROM tp_notice_recipient WHERE client_id = {$this->clientId} AND shop_id = {$this->shopId} AND notice_id = {$noticeId}";
         
        $this->db->query($sql);
        $this->db->get_rows();
        $list = $this->recipientDataNormalize($this->db->rows);
    
        return $list;
    }
    
    private function getAdminEmail() {
        $sql = "SELECT email FROM a_users WHERE client_id = {$this->clientId}";
        $this->db->query($sql);
        return $this->db->get_field();
    }
    
    private function recipientDataNormalize($data) {
        $out = array();
    
        foreach($data as $item) {
            if($item['data_type'] != 'none') {
                $item['data'] = $item['data_'.$item['data_type']];
            } else {
                $item['data'] = false;
            }
             
            unset($item['data_type']);
            unset($item['data_char']);
            unset($item['data_int']);
            unset($item['client_id']);
            unset($item['shop_id']);
            $out[] = $item;
        }
    
        return $out;
    }
    
    private function getTestDataForMessage($objectId,$actionId) {
        if($objectId == 1) {
            $this->initShopProxy(15,999,false);
            $order = $this->shop->getOrderInfo(2);
            unset($order['client_id']);
            unset($order['shop_id']);
            return $order;
        } else if($objectId == 2) {
            return array(
                'email'=>'info@ncity.biz',
                'user_id'=>121975
            );
        } else if($objectId == 3) {
            if($actionId == 15) {
                return array(
                    'email'=>'info@ncity.biz',
                    'name'=>'Иван Иванов',
                    'phone'=>'+71231231231',
                    'user_comment'=>'Удобное время для звонка: с 18:00 по 22:00'
                );
            } 
            
            return array(
                'email'=>'info@ncity.biz',
                'name'=>'Иван Иванов',
                'phone'=>'+71231231231',
                'login'=>'454546412',
                'source_id'=>7,
                'source_name'=>'Покупатель магазина',
                'user_comment'=>''
            );
        } else if($objectId == 4) {
            
        } 
    }
    
    private function getObjectIdByEvent($eventId) {
        $eventId = intval($eventId);
        
        if(!$eventId) return false;
        
        $sql = "SELECT eo.id FROM tp_notice_event_object as eo LEFT JOIN tp_notice_event as e ON eo.id = e.object_id WHERE e.id = {$eventId}";
        $this->db->query($sql);
   
        return $this->db->get_field();
    }
    
    private function getObjectAliasByNoticeId($noticeId, $shopId, $clientId) {
        $sql = "SELECT eo.object_alias FROM tp_notice_event_object as eo LEFT JOIN tp_notice_event as e ON eo.id = e.object_id LEFT JOIN tp_notice_notifications as nn ON e.id = nn.event_id WHERE nn.notice_id = {$noticeId} AND nn.client_id = {$clientId} AND nn.shop_id = {$shopId}";
        $this->db->query($sql);
        return $this->db->get_field();
    }
    
    private function getObjectAliasByEventId($eventId) {
        $eventId = intval($eventId);
    
        if(!$eventId) return false;
    
        $sql = "SELECT eo.object_alias FROM tp_notice_event_object as eo LEFT JOIN tp_notice_event as e ON eo.id = e.object_id WHERE e.id = {$eventId}";
        $this->db->query($sql);
         
        return $this->db->get_field();
    }
    
    private function initShopProxy($clientId  = false, $shopId  = false, $userId = false) {
        if(!$this->shop) {
            $this->shop = new shopProxy($clientId, $shopId, $userId);
        } else {
            $this->shop->init($clientId  = false, $shopId  = false, $userId = false);
        }
    }
    
    private function initTplInstance() {
        $this->templates = new templates();
        
        $config = $this->core->CONFIG;
        
        $this->templates->compil_dir   	      = $config['templates']['compil_dir'];
        $this->templates->db_table_tpl 	      = $config['templates']['bd_src_table'];
        $this->templates->compil_file_ext     = $config['templates']['file_ext'];
        $this->templates->compil_chek_level   = $config['templates']['check_level'];
        $this->templates->debugParser		  = $config['templates']['debug']['varsWarning'];
        $this->templates->htmlminimizer 	  = (isset($config['templates']['min']) && $config['templates']['min'])? true : false;
        $this->templates->useSyntaxSugar 	  = (isset($config['templates']['syntax_sugar']) && $config['templates']['syntax_sugar'])? true : false;
        $this->templates->init();
        
        $this->tplParserPlugins = array(
            'parse_widget',
            'parse_add',
            'parse_del',
            'parse_iterat',
            'parse_deiterat',
            'parse_foreachSmarty',
            'parse_print',
            'parse_trigger',
            'parse_foreachQuiky',
            'parse_forQuiky',
            'parse_simple_if',
            'parse_vars'
        );
    }
}



class sender {
    
    public function email($from, $to, $title, $message) {
                
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
        $headers .= 'Content-transfer-encoding: 8bit' . "\r\n";
        $headers .= 'From: '.$from. "\r\n" .'X-Mailer: NodeJS Queue Mailer/2.3.0';
        
        mail($to, $title, $message, $headers, '-f'.$from);
        
        return array('status'=>'ok');
    }
    
    public function sms() {
    
    }
    
    public function local() {
    
    }
    
    
    public function json($url, $method, $message, $extended) {
        if(substr($url, 0, 7) != 'http://' && substr($url, 0, 7) != 'https://') return array('status'=>'error', 'error_code'=>982,'message'=>'Destination url incorrect');
        $method = (strtolower($method) == 'put')? 'put' : 'post';
        
        if(is_array($extended) && !empty($extended)) {
            $message = array_merge($message, $extended);
        }
        
        return $this->sendCurlRequest($url, $message, $method, 'application/json');
    }
    
    public function xml($url, $method, $message, $extended) {
        if(substr($url, 0, 7) != 'http://' && substr($url, 0, 7) != 'https://') return array('status'=>'error', 'error_code'=>982,'message'=>'Destination url incorrect');
        $method = (strtolower($method) == 'put')? 'put' : 'post';
    } 
    
    
    private function sendCurlRequest($url, $data, $method, $type = "application/json") {

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_MAXREDIRS, 4);
        curl_setopt($curl, CURLOPT_URL,$url);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Ncity CloudShop Platform. NodeJs NotifySender (v2.05.12 rev.22546) [pid: 998754] [instance: 8]');

        if($method == 'post') {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: '.$type.'; charset=utf-8'));
        } else {
            curl_setopt($curl, CURLOPT_PUT);
            
            //CURLOPT_INFILE
            // CURLOPT_INFILESIZE. 
        }
        
        
        $result = curl_exec($curl);
        $realUrl = curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);
        
        return array('result'=>$result, 'url'=>$realUrl);
    }
}


class shopProxy {
    private $clientId;
    private $shopId;
    private $userId;
    private $shop;
    
    private $startShopId;
    private $startClientId;
    private $startUserId;
    
    public function __construct($clientId = false, $shopId = false, $userId = false) {
        if(!$this->shop) {
            if(!class_exists('admin_shop')) { 
                core::$instance->lib->loadModuleFiles('shop', 'admin.shop.php');
            }
            
            $this->shop = new admin_shop();
            $this->shop->init();
            
            $this->startShopId = $this->shop->shopId;
            $this->startClientId = $this->shop->clientId;
            $this->startUserId = $this->shop->userId;
        }

        if($clientId) $this->shop->clientId = $clientId;
        if($shopId) $this->shop->shopId = $shopId;
        if($userId) $this->shop->userId = $userId;
    }
    
    public function init($clientId = false, $shopId = false, $userId = false) {
        if($clientId) $this->shop->clientId = $clientId; else $this->shop->clientId = $this->startClientId;
        if($shopId) $this->shop->shopId = $shopId; else $this->shop->shopId = $this->startShopId;
        if($userId) $this->shop->userId = $userId; else $this->shop->userId = $this->startClientId;
    }
    
    public function restore() {
        $this->shop->clientId = $this->startClientId;
        $this->shop->shopId = $this->startShopId;
        $this->shop->userId = $this->startClientId;
    }
    
    public function __call($method, $arguments) {
        return call_user_func_array(array($this->shop, $method), $arguments);
    }
}
?>