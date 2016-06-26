<?php 

class userCabinet /*extends global_client_api*/ {
    
    private $shop;
    private $db;
    private $tpl;
    private $core;
    
    private $clientId;
    private $shopId;
    private $userId;
    private $contactId;
    private $user;
    
    public function __construct() {
        $this->core = core::$instance;
        $this->db = $this->core->db;
        $this->tpl = $this->core->tpl;
        $this->shop = $this->core->shop;
        
        $this->shopId = $this->shop->shopId;
        $this->clientId = $this->shop->clientId;
        $this->userId = $this->shop->userId;
        $this->contactId = $this->shop->userInfo['crm_contact_id'];
        $this->user = $this->shop->userInfo;
    }
    
    public function getClientInfo() {
        return $this->shop->userInfo;
    }
    
    public function getAddresses() {
        
    }
    
    public function getClientMainAddress() {
        return $this->shop->getClientAdresses(false, true);
    }
    
    public function getPriceCategory() {
        return array(
            'name'=>'1 категория',
            'id'=>1,
            'color'=>'#CCCCCC'
        );
    }
    
    public function getCompanyInfo() {
        
    }
    
    public function getBillingInformation() {
        
    }
    
    public function getAllInfo() {
        return array(
            'user'=>$this->getClientInfo(),
            'address'=>$this->getClientMainAddress(),
            'category'=>$this->getPriceCategory(),
            'billing'=>$this->getBillingInformation(),
            'orders'=>$this->getOrders()
        );
    }
    
    public function getPersonalManager() {
        $managerId = (!empty($this->shop->userInfo) && !empty($this->shop->userInfo['contact']) && !empty($this->shop->userInfo['contact']['responsible_user_id']))? $this->shop->userInfo['contact']['responsible_user_id'] : 0;
        if(!intval($managerId)) return false;
        
        $sql = "SELECT u.user_id as id, u.name_first, u.name_last, u.email FROM a_users as u WHERE u.client_id = {$this->shop->clientId} AND u.user_id = {$managerId}";
        $this->db->query($sql);
        $this->db->get_rows(1);
        
        return $this->db->rows;
    }
    
    public function getOrders() {       
        $sql = "SELECT SQL_CALC_FOUND_ROWS
					o.client_id,
					o.contact_id,
					o.order_id,
					o.shop_id,
					o.status_id,
					o.status_pay_id,
					o.delivery_type_id,
					o.delivery_date,
					o.create_date,
					o.address,
					o.comment,
					o.recipient_info,
					get_order_sum(o.client_id, o.order_id, o.shop_id) sum,
					c.name client_name,
					
					(SELECT cp.phone FROM crm_contacts_phones as cp WHERE cp.client_id = {$this->clientId} AND cp.contact_id = o.contact_id LIMIT 1) as client_phone,
					(SELECT ce.email FROM crm_contacts_email as ce WHERE ce.client_id = {$this->clientId} AND contact_id =  o.contact_id LIMIT 1) as client_email,
					(SELECT GROUP_CONCAT((CONCAT(cod.count, ' x ', cod.product_name)) SEPARATOR ', ') FROM tp_order_items as cod WHERE cod.shop_id = {$this->shopId} AND cod.client_id = {$this->clientId} AND cod.order_id = o.order_id) as order_short_info,
					
					s.name order_status_name,
					s.color color,
					p.name order_pay_name,
					d.name delivery_name		
				FROM
					tp_order as o 
						LEFT JOIN crm_contacts c ON o.contact_id = c.contact_id AND o.client_id = c.client_id
						LEFT JOIN tp_order_status s ON s.id = o.status_id LEFT JOIN tp_order_pay_status p ON p.id = o.status_pay_id
						LEFT JOIN tp_delivery_types d ON d.id = o.delivery_type_id
				WHERE
					o.shop_id = {$this->shopId} AND
					o.client_id = {$this->clientId} AND
                    o.contact_id = {$this->contactId} 
                    
                ORDER BY o.create_date DESC";
    
        
        
        $this->db->query($sql);
        $this->db->filter('create_date', function($field) {
           return date('d.m.Y', $field); 
        });
        $this->db->get_rows();
        $list = $this->db->rows;
        
        
        return $list;
    }
    
    public function editAddress() {
        
    }
    
    public function editBilling() {
        
    }
    
    public function updatePersonal($data) {
        $filtred = array(
            'name'=>$this->filter($data, 'name'),
            'surname'=>$this->filter($data, 'surname'),
            'lastname'=>$this->filter($data, 'lastname'),
            'email'=>$this->filter($data, 'email'),
            'phone'=>$this->filter($data, 'phone')
        );
        
        if($filtred['email']) {
            $sql = "SELECT COUNT(*) FROM tp_user WHERE client_id={$this->clientId} AND shop_id = {$this->shopId} AND email = '{$filtred['email']}' AND user_id != {$this->userId}";
            $this->db->query($sql);
            
            if(intval($this->db->get_field())) {
                return array(
                  'error'=>21,
                  'message'=>'Указанный email уже используется другим пользователем'  
                );
            }
            
            
            $query = array(
                'client_id'=>$this->clientId,
                'shop_id'=>$this->shopId,
                'user_id'=>$this->userId,
                'email'=>$filtred['email'],
                'login'=>$filtred['email']
            );
            
            $this->db->autoupdate()->table('tp_user')->data(array($query))->primary('client_id', 'shop_id', 'user_id');
            $this->db->execute();
            
            $query = array(
                'client_id'=>$this->clientId,
                'contact_id'=>$this->contactId,
                'email_type'=>1,
                'email'=>$filtred['email']
            );
            
            $this->db->autoupdate()->table('crm_contacts_email')->data(array($query))->primary('client_id', 'contact_id', 'email_type');
            $this->db->execute();
        }
        
        $query = array(
            'client_id'=>$this->clientId,
            'contact_id'=>$this->contactId,
        );
        
        if($filtred['name']) $query['name'] = $filtred['name'];
        if($filtred['surname']) $query['surname'] = $filtred['surname'];
        if($filtred['lastname']) $query['lastname'] = $filtred['lastname'];
        
        $this->db->autoupdate()->table('crm_contacts')->data(array($query))->primary('client_id', 'contact_id');
        $this->db->execute();
        
        if($filtred['phone']) {
            $query = array();
            $query[] = array(
                'client_id'=>$this->clientId,
                'contact_id'=>$this->contactId,
                'phone_type'=>1,
                'phone'=>$filtred['phone']
            );
            $query[] = array(
                'client_id'=>$this->clientId,
                'contact_id'=>$this->contactId,
                'phone_type'=>5,
                'phone'=>$filtred['phone']
            );
            
            $this->db->autoupdate()->table('crm_contacts_phones')->data($query)->primary('client_id', 'contact_id', 'phone_type');
            $this->db->execute();
        }

        return $this->shop->updateUserInfo();
    }
    
    public function updatePassword($passold, $passnew) {
        $passold = md5(md5(trim($passold)));
        $passnew = md5(md5(trim($passnew)));
        
        $sql = "SELECT COUNT(*) FROM tp_user WHERE client_id = {$this->clientId} AND shop_id = {$this->shopId} AND user_id = {$this->userId} AND password = '{$passold}'";
        $this->db->query($sql);
        if(!intval($this->db->get_field())) {
            return array(
                'error'=>1,
                'message'=>'Указан не корректный текущий пароль'
            );
        } else {
            $query = array(
                'client_id'=>$this->clientId,
                'shop_id'=>$this->shopId,
                'user_id'=>$this->userId,
                'password'=>$passnew
            );
            
            $this->db->autoupdate()->table('tp_user')->data(array($query))->primary('client_id', 'shop_id', 'user_id');
            $this->db->execute();
            
            return true;
        }
    }
    
    public function delAddress() {
        
    }
    
    public function changeOrder() {
        
        
    }
    
    private function filter($object, $key, $type = 'string', $notExists = false) {
        if(empty($object[$key])) return $notExists;
        
        switch ($type) {
            case 'string':
                return mysql::str($object[$key]);
            break;
            
            case 'int':
                return intval($object[$key]);
            break;
            
            default:
                return mysql::str($object[$key]);
            break;
        }
    }
}



?>