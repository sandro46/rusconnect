<?php 


class mAnalyticTracker {
    
    
    public function init() {
        
    }
    
    public function getCounterCode() {
        return file_get_contents(core::$instance->CONFIG['module_path'].'analytics/includes/counter.tpl');        
    }
}





?>