<?php 

class notifications_admin extends main_module {
    
    public function getNotification($noticeId) {
        $noticeId = intval($noticeId);
        $sql = "SELECT * FROM tp_notice_notifications WHERE client_id = {$this->clientId} AND shop_id = {$this->shopId} AND notice_id = {$noticeId}";
        $this->db->query($sql);
        $this->db->get_rows(1);
        $notice = $this->db->rows;
        $notice['recipient'] = $this->getNoticeRecipientsForAdmin($noticeId);
        $notice['data'] = json_decode($notice['data'], true);
        return $notice;
    }
    
    
    public function deleteNotice($noticeId) {
        $noticeId = intval($noticeId);
        if(!$noticeId) return false;
        
        $sql = "DELETE FROM tp_notice_notifications WHERE client_id = {$this->clientId} AND shop_id = {$this->shopId} AND notice_id = {$noticeId}";
        $this->db->query($sql);
        
        $sql = "DELETE FROM tp_notice_recipient WHERE client_id = {$this->clientId} AND shop_id = {$this->shopId} AND notice_id = {$noticeId}";
        $this->db->query($sql);
        
        return true;
    }
    
    public function save($noticeId, $info, $message) {
        $noticeId = intval($noticeId);
        $isNew = ($noticeId)? false : true;
        
        $query = array(
            'client_id'=>$this->clientId,
            'shop_id'=>$this->shopId,
            'title'=>mysql::str($info['title']),
            'event_id'=>intval($info['eventId']),
            'action_id'=>intval($info['actionId']),
            'enabled'=>1,
            'data'=>mysql::str(json_encode($message))
        );
        
        if($noticeId) {
            $query['notice_id'] = $noticeId;
            $this->db->autoupdate()->table('tp_notice_notifications')->data(array($query))->primary('client_id', 'shop_id', 'notice_id');
            $this->db->execute();
        } else {
            $query['create_date'] = time();
            $query['create_user_id'] = $this->userId;
            
            $this->db->autoupdate()->table('tp_notice_notifications')->data(array($query));
            $this->db->execute();
            $noticeId = $this->db->insert_id;
        }
        
        if($isNew) {
            foreach($info['recipients'] as $type=>$value) {
                $this->addRecipient($noticeId, $type, $value);
            }
        } else {
            $this->updateRecipients($noticeId, $info['recipients']);
        }
        
    }
        
    public function getNotificationsList($instance, $start = 0, $limit = 20, $sortBy = 'o.order_id', $sortType = 'DESC', $filters = false) {
        $sql = "SELECT SQL_CALC_FOUND_ROWS
                    n.*,
                    e.title as event_name,
                    IF(n.enabled, 'success', 'warning') as enabled_mark,
                    IF(n.enabled, 'Работает', 'Остановлено') as enabled_name,
                    e.event_alias,
                    eo.object_alias as object_alias,
                    eo.group as object_group,
                    a.name as action_name,
                    CONCAT(name_first, ' ', name_last) as creator_name
                FROM
                    tp_notice_notifications as n
                        LEFT JOIN tp_notice_action as a ON a.id = n.action_id
                        LEFT JOIN tp_notice_event as e ON e.id = n.event_id
                        LEFT JOIN tp_notice_event_object as eo ON eo.id = e.object_id
                        LEFT JOIN a_users as u ON u.user_id = n.create_user_id
                WHERE
                    n.client_id = {$this->clientId} AND
                    n.shop_id = {$this->shopId}
                ";
        
        $sql .= " ORDER BY $sortBy $sortType LIMIT $start,$limit";
        
        //echo $sql;
        $this->db->query($sql);
        $this->db->add_fields_deform(array('create_date'));
        $this->db->add_fields_func(array('h_date'));
        $this->db->get_rows();
        
        return $this->db->rows;
    }
    
    public function getNoticeRecipientsForAdmin($noticeId) {
        $sql = "SELECT * FROM tp_notice_recipient WHERE client_id = {$this->clientId} AND shop_id = {$this->shopId} AND notice_id = {$noticeId}";
       
        $this->db->query($sql);
        $this->db->get_rows();
        $list = $this->recipientDataNormalize($this->db->rows);
        
        return $list;
    }
    
    public function getEvents() {
        $sql = "SELECT * FROM tp_notice_event_object";
        $this->db->query($sql);
        $obj = $this->db->get_rows(false,'id');
        
        $sql = "SELECT * FROM tp_notice_event ORDER BY object_id, id";
        $this->db->query($sql);
        $evn = $this->db->get_rows(false,'object_id');
        
        foreach($obj as $objId=>&$objRow) {
            $objRow['events'] = $evn[$objId];
        }
        
        return $obj;
    }
    
    public function getActions() {
        $sql = "SELECT * FROM tp_notice_action";
        $this->db->query($sql);
        return $this->db->get_rows();
    }
    
    private function updateRecipients($noticeId, $recipientsList) {
        
    }
    
    private function addRecipient($noticeId, $type, $data) {
        $type = intval($type);
        $noticeId = intval($noticeId);
        
        if(!$type || !$noticeId) return false;
        
        $typeHelper = array(
            1 => 'buyer',
            2 => 'admin', // email :: user_id
            3 => 'group_id',
            4 => 'group_id',
            5 => 'user_id',
            6 => 'email',
            7 => 'phone'
        );
        
        $dataTypeHelper = array(
            1 => 'none',
            2 => 'none', // char :: int
            3 => 'int',
            4 => 'int',
            5 => 'int',
            6 => 'char',
            7 => 'char'  
        );
        
        $query = array();
        
        $row = array(
            'client_id'=>$this->clientId,
            'shop_id'=>$this->shopId,
            'notice_id'=>$noticeId,
            'recipient_type'=>$typeHelper[$type],
            'recipient_local_type'=>$type,
            'data_type'=>$dataTypeHelper[$type],
        );
            
        if($type == 1 || $type == 2) {
            //if($type == 2) {
            //    $row['data_'.$dataTypeHelper[$type]] = $this->getAdminEmail(); // getAdminEmail :: getAdminUserId
            //}
            
            $query[] = $row;
        } else {
            if(is_array($data) && !empty($data)) {
                foreach($data as $rcp) {
                    $coll = array();
                    $coll['data_'.$dataTypeHelper[$type]] = ($dataTypeHelper[$type] == 'char')? mysql::str($rcp) : intval($rcp);
                    $query[] = array_merge($row, $coll);
                }
            }
        }
       
        if(!empty($query)) {
            $this->db->autoupdate()->table('tp_notice_recipient')->data($query);
            $this->db->execute();
        }
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
}



if(!function_exists('h_date')) {
    function h_date($date){
        return ($date)? date('d-m-Y - H:i',$date) : 'не установлено';
    }
}






?>