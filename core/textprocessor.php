<?php
################################################################################
# ---------------------------------------------------------------------------- #
# @Author: Alexey Pshenichniy <inc.mcms@gmail.com> <M-zone Inc.> 2005-2009     #
# @Date: 20.05.2008                                                            #
# @license: GNU GENERAL PUBLIC LICENSE Version 2                               #
# ---------------------------------------------------------------------------- #
# M-cms v4.1 (core build - 4.102)                                              #
################################################################################



class textprocessor
{
    
    
    public $src = '';
    private $functions = array();
    

    
    
    public function __construct()
    {
        
        $this->add_process('flash_replace');
        $this->add_process('addslashes');
    
    }
    
    public function add_process($proc_name, $param=0)
    {
        $this->functions[] = array($proc_name, $param);
    }
       
    public function run($text)
    {
        
       $this->src = $text; 
        
       foreach($this->functions as $func)
       {
          eval('$this->src = $this->'.$func[0].'('.$func[1].');');  
       }
        
       return $this->src;
    }
    
    public function html($text, $mod = 'd')
    {
        
        
    }
    
    
    
    private function flash_replace()
    {
        if(preg_match_all("/\<pre id=\"flash_video_replacement\"\>(.+?)\<\/pre\>/s", $this->src, $res))
        {
            foreach($res[1] as $k =>$row)
            {
                $curent_params = explode(';', $row);
                $params = array();
                
                
                foreach($curent_params as $param)
                {
                   $_cur_params  = explode('=', $param);
                   $params[trim($_cur_params[0])] = $_cur_params[1]; 
                }
                   
                
                $str  = '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="'.$params['width'].'" height="'.$params['height'].'">';
                
                if(isset($params['pic']))
                    $str .= '<param name="movie" value="/templates/flash/player.swf?swflink='.$params['swf'].'&amp;piclink='.$params['pic'].'" />'; 
                else 
                    $str .= '<param name="movie" value="/templates/flash/player.swf?swflink='.$params['swf'].'" />'; 
                
                $str .= '<!--[if !IE]>-->';

                if(isset($params['pic']))
                    $str .= '<object type="application/x-shockwave-flash" data="/templates/flash/player.swf?swflink='.$params['swf'].'&amp;piclink='.$params['pic'].'" width="'.$params['width'].'" height="'.$params['height'].'">';
                else 
                    $str .= '<object type="application/x-shockwave-flash" data="/templates/flash/player.swf?swflink='.$params['swf'].'" width="'.$params['width'].'" height="'.$params['height'].'">';
                
                                        
                $str .= '<!--<![endif]-->';
                $str .= '	<div>';
                $str .= '	<p><a href="http://www.adobe.com/go/getflashplayer"><img src="http://static.alpari.ru/img/noflash.png" alt="" style="border:none;" /></a></p>';
                $str .= '	</div>';
                $str .= '<!--[if !IE]>-->';
                $str .= '</object>';
                $str .= '<!--<![endif]-->';
                $str .= '</object>';
                
    
                
                $search = $res[0][$k];
                $replacement = $str;
                
                $this->src = str_replace($search, $replacement, $this->src);
            }
        }
		

		
        
        
    
     
       return $this->src;
    }
    
    private function addslashes()
    {
        $this->src = addslashes($this->src);
        
        return $this->src;
    }
}



?>