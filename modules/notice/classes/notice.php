<?php 

// @TODO: Нужно реализовать все :)

class notice_client extends global_client_api {
    public $messages = array();

    
   
    
    public function addNotice($title, $text, $action) {
        
    }
    
    public function adminMail($title, $text) {
        $email = $this->shopInfo['email'];
      
       // $email = 'alexey@itbc.pro';
        
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
        $headers .= 'Content-transfer-encoding: 8bit' . "\r\n";
        $headers .= 'From: no-reply@ncity.biz' . "\r\n" .'X-Mailer: NodeJS Queue Mailer/2.3.0';

        $this->core->langId = 1;
        $sid = $this->core->site_id;
       
        $this->tpl->assign('message_text', $text);
        $this->core->site_id = 2;
        $message = $this->tpl->get('site_admin_notice.html','mailer');
        $this->core->site_id = $sid;

        mail($email, $title, $message, $headers, '-fno-reply@ncity.biz');
    }
}







?>