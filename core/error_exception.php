<?php
################################################################################
# ---------------------------------------------------------------------------- #
# @Author: Alexey Pshenichniy <inc.mcms@gmail.com> <M-zone Inc.> 2005-2009     #
# @Date: 20.05.2008                                                            #
# @license: GNU GENERAL PUBLIC LICENSE Version 2                               #
# ---------------------------------------------------------------------------- #
# M-cms v4.1 (core build - 4.102)                                              #
################################################################################


class error_exception 
    {

    private $messages = array();
    private $reporting = 6;
    private $reportings_tpl = array();
    

    public function __construct ($message = '', $code = 0, $severity = 0, $filename = '', $lineno = 0)
    {
            global $core;
            
            $this->messages[1]    = 'Fatal error'; //Fatal error
            $this->messages[2]    = 'Warning'; //Warning
            $this->messages[4]    = 'Parser error'; //Parser error
            $this->messages[8]    = 'Notice'; //Notice
            $this->messages[256]  = 'User error'; //User error
            $this->messages[512]  = 'User warning'; //User warning
            $this->messages[1024] = 'User notice'; //User notice
            $this->messages[2048] = 'Strict error'; //Strict error

            $this->reportings_tpl[1] = array(1,2,4,256,512,2048);
            $this->reportings_tpl[2] = array(1,4,256);
            $this->reportings_tpl[6] = array(1,4,2,256,512);   
            $this->reportings_tpl[3] = array(1,4,8);  
            $this->reportings_tpl[4] = array(1,2,4,8);
            $this->reportings_tpl[5] = array(1,2,4,8,256,512,1024,2048);
    }
        
    public function init($message, $code, $severity, $filename, $lineno) 
    {   
        global $core;
        
        if($this->display_error($severity))
        {
            $id_error = $core->log->access(3, $message, $code, $filename, $lineno);
            
            $text = '<div style="border:solid #0033FF 1px; padding:6px; position:relative; margin-top:15px; margin-left:10px; width:800px; background-color:#EDEBE9">';
            $text .= "<b>".$this->messages[$severity]."</b>";
            $text .= ": ".$message."\n";
            $text .= "<br>\nError ticket: <b>".$id_error."</b>\n";
            $text .= "</div>\n";
            echo $text;
            
            if($severity == 1 || $severity == 4 || $severity == 256)
            {
                echo iconv('cp1251', 'utf-8', "\n<br><span style='color:red'>   ");
                die();
            }
        }  
    }
    
    private function display_error($error_severity)
    {
        if(in_array($error_severity, $this->reportings_tpl[$this->reporting])) return true;
        
        return false;
    }
        
}

?>