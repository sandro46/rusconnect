<?php 


class updater extends main_module {
    
    
    
    public $themesIndex = array();

    public function updateNews() {
    	
    }
    
    public function updateNotice() {
        $sql = "SELECT * FROM tp_notice_notifications WHERE shop_id = 999 AND client_id = 15 ";
        $this->db->query($sql);
        $this->db->get_rows();
        $notifications = $this->db->rows;

        
        $sql = "SELECT * FROM tp_notice_recipient WHERE shop_id = 999 AND client_id = 15";
        $this->db->query($sql);
        $this->db->get_rows();
        $recipients = $this->db->rows;
        
        $sites = $this->getAllSites(array(999));
        
        $sql = "DELETE  FROM tp_notice_recipient WHERE  shop_id != 999";
        $this->db->query($sql);
        
        $sql = "DELETE  FROM tp_notice_notifications WHERE  shop_id != 999";
        $this->db->query($sql);
        
        
        foreach($sites as $site) {

            $query = array();
            foreach($notifications as $item) {
                $item['client_id'] = $site['client_id'];
                $item['shop_id'] = $site['shop_id'];
                $item['data'] = mysql::str(json_encode(json_decode($item['data'],true)));
                
                $query[] = $item;
            }
           
            
            $this->db->autoupdate()->table('tp_notice_notifications')->data($query);
            $this->db->execute();
        
            $query = array();
            foreach($recipients as $item) {
                $item['client_id'] = $site['client_id'];
                $item['shop_id'] = $site['shop_id'];
                unset($item['id']);
                $query[] = $item;
            }
            
         
            $this->db->autoupdate()->table('tp_notice_recipient')->data($query);
            $this->db->execute();
            
       }
       
    }
    
    public function updateStatic() {
       $list = $this->getStaticDestinationCategory();
        
       
       foreach($list as $item) {
          $this->removeFiles($item['path'], true);
          $this->copyThemeFiles($item['theme_source'], $item['path']);
       }
    }
    
    public function updateModules($modules) {
        $query = array();
        $sql = "SELECT s.id, sh.client_id FROM mcms_sites as s LEFT JOIN tp_shop as sh ON sh.shop_id = s.id WHERE sh.client_id IS NOT NULL AND sh.client_id != 15";
        $this->db->query($sql);
        $this->db->get_rows();
        $sites = $this->db->rows;

        foreach($sites as $shop) {
            foreach($modules as $mid=>$mdata) {
                $query[] = array(
                    'client_id'=>$shop['client_id'],
                    'shop_id'=>$shop['id'],
                    'module_id'=>$mid,
                    'settings'=>$mdata
                );
            }
        }
        
        $this->db->autoupdate()->table('tp_shop_to_modules')->data($query)->primary('client_id', 'shop_id', 'module_id');
        $this->db->execute();
    }
    
    public function updateTemplates() {
        $list = $this->getStaticDestinationCategory();
        
        foreach($list as $item) {
            $shopId = $this->getShopIdByUrl($item['site']);
            if(!$shopId) continue;
            $sql = "SELECT * FROM mcms_tmpl WHERE id_site = {$item['example_shop_id']} AND id_lang = 1 AND theme = '{$item['theme']}' AND del=0";
        
            $this->db->query($sql);
            $this->db->get_rows();
            $templates = $this->db->rows;
            
            if(empty($this->db->rows)) {
                echo "Theme sources not found: theme = {$item['theme']} demoId = {$item['example_shop_id']} site name: {$item['site']} siteId = {$shopId}\n";
            }
            
            foreach($templates as &$tpl) {
                unset($tpl['id_template']);
                unset($tpl['user_edit_id']);
                unset($tpl['date']);
                $tpl['id_site'] = $shopId;
                $tpl['source'] = mysql::str($tpl['source']);
            }
            
            $this->db->autoupdate()->table('mcms_tmpl')->data($templates)->primary('id_site', 'id_lang', 'theme', 'name_module', 'name');
            $this->db->execute();
        }
    }
    
    public function getShopIdByUrl($url) {
        $sql = "SELECT id FROM mcms_sites WHERE server_name = '{$url}'";
        $this->db->query($sql);
        $siteId = intval($this->db->get_field());
        
        if($siteId) return $siteId;
        
        $sql = "SELECT id_site FROM mcms_sites_alias WHERE server_name = '{$url}'";
        $this->db->query($sql);
        $siteId = intval($this->db->get_field());
        
        if($siteId) return $siteId;
        
        return false;
    }
    
    public function getThemes() {
        $sql = "SELECT * FROM tp_themes";
        $this->db->query($sql);
        $this->db->get_rows();
        $this->themesIndex = array();
        
        foreach($this->db->rows as $item) {
            $this->themesIndex[$item['name']] = array(
              'name'=>$item['name'],
              'example_shop_id'=>$item['demo_shop_id'],
              'source'=>CORE_PATH.'static/theme_sources/'.$item['name'].'/'
            );
        }
    }
    
    public function getStaticDestinationCategory() {
        $dir = array();
        
        if(empty($this->themesIndex)) {
            $this->getThemes();
        }
        
        foreach(scandir(CORE_PATH.'static/shop/') as $item) {
            if($item == '.' || $item == '..' || $item == 'demo.ncity.biz' || preg_match("/^demo[0-9]{1,}\..+?$/s", $item)) continue;
            foreach(scandir(CORE_PATH.'static/shop/'.$item.'/') as $theme) {
                if($theme == '.' || $theme == '..') continue;
                if(!isset($this->themesIndex[$theme])) continue;
                $dir[] = array(
                    'site'=>$item,
                    'theme'=>$theme,
                    'path'=>CORE_PATH.'static/shop/'.$item.'/'.$theme.'/',
                    'theme_source'=>$this->themesIndex[$theme]['source'],
                    'example_shop_id'=>$this->themesIndex[$theme]['example_shop_id']
                );
            }
        }
        
        return $dir;
    }
    
    private function getAllSites($excludeSite = array(), $excludeClient = array()) {
        $sql = "SELECT s.id as shop_id, sh.client_id FROM mcms_sites as s LEFT JOIN tp_shop as sh ON sh.shop_id = s.id WHERE sh.client_id IS NOT NULL ";
        
        if(!empty($excludeClient)) {
            $sql.= " AND sh.client_id NOT IN(".implode($excludeClient).")";
        }
        
        if(!empty($excludeSite)) {
            $sql.= " AND s.id NOT IN(".implode($excludeSite).")";
        }
        
        $this->db->query($sql);
        $this->db->get_rows();
        
        return $this->db->rows;
    }
    
    private function copyThemeFiles($source, $target) {
        if(!file_exists($target)) @mkdir($target, 0750, true);
        $d = dir($source);
    
        while (false !== ($entry = $d->read())) {
            if ($entry == '.' || $entry == '..') continue;
            $this->fullCopy("$source/$entry", "$target/$entry");
        }
        $d->close();
    }
    
    private function fullCopy($source, $target) {
        if (is_dir($source))  {
            @mkdir($target,0750);
            $d = dir($source);
            while (false !== ($entry = $d->read())) {
                if ($entry == '.' || $entry == '..') continue;
                $this->fullCopy("$source/$entry", "$target/$entry");
            }
            $d->close();
        }
        else copy($source, $target);
    }
    
    private function removeFiles($path, $inside = false, $first = true) {
        if(substr($path,0,-1) != '/') $path .= '/';
        if(is_dir($path)){
            foreach(scandir($path) as $item) {
                if($item == '.' || $item == '..') continue;
                $this->removeFiles($path.$item, $inside, false);
                if(is_dir($path.$item)) {
                    rmdir($path.$item);
                } else {
                    unlink($path.$item);
                }
            }
        } else {
            @unlink($path);
        }
    }
    
    
    
    
    
}




?>