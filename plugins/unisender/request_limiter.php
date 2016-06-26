<?php 

class RequestLimiter {
    
    public $rpm = 100; // request p/min
    public $rpd = 0; // request p/day if 0 -> not check
    public $conlimit = 100; // after thic connections count, check rpm
    
    
    private $ipsecfile = 'vars/temp/ipsec';
    private $tokenlength = 40;
    
    public function __construct() {
       
    }
    
    public function getToken() {
        $token = '';
        while(strlen($token) != $this->tokenlength) {
            $token .= chr(rand(65,90));
        }
        
        $_SESSION['token'] = $token;
        
        return $token;
    }
    
    public function setCSRFtoken($callback) {
        $token = $this->getToken();
        if(is_callable($callback)) {
            $callback($token);
        }
        
        return $token;
    }
    
    public function checkTocken($token) {
        if(empty($_SESSION['token'])) return false;
        if($_SESSION['token'] != $token) return false;
        
        return true;
    }
    
    public function check_limit() {
        if (empty($_SERVER['REMOTE_ADDR'])) { 
            return true;
        }
        
        $now = time();
        $this->ipsecfile = CORE_PATH . $this->ipsecfile . '.' . $_SERVER['REMOTE_ADDR'] . '.log';
        
        if (!file_exists($this->ipsecfile)) { // If first request or new request after 1 hour / 24 hour ban, new file with <timestamp>|<counter>
            if ($handle = fopen( $this->ipsecfile, 'w+')) {
                if (fwrite($handle, $now.'|0')) { chmod( $this->ipsecfile, 0700); } // Chmod to prevent access via web
                fclose($handle);
            }
        }  else if(($content = file_get_contents($this->ipsecfile)) !== false) {
            $content = explode('|',$content); // Create paraset [0] -> timestamp  [1] -> counter
            $diff = (int)$now-(int)$content[0]; // Time difference in seconds from first request to now
           
            if ($content[1] == 'ban') { // If [1] = ban we check if it was less than 24 hours and die if so
                if ($diff>86400) { 
                    unlink($this->ipsecfile);  // 24 hours in seconds.. if more delete ip file
                } else {
                    header("HTTP/1.1 503 Service Unavailable");
                    exit("Your IP is banned for 24 hours, because of too many requests.");
                }
            } else {
                $current = ((int)$content[1])+1; // Counter + 1
                if($this->rpd && $this->rpd <= $current) {
                    if ($handle = fopen($this->ipsecfile, 'w+')) {
                        fwrite($handle, $content[0].'|ban');
                        fclose($handle);
                    }
                    return false;
                }
                if ($current > $this->conlimit) { // We check rpm (request per minute) after 200 request to get a good ~value
                    $rpm = ($current/($diff/60));
                    if ($rpm > $this->rpm) { // If there was more than 10 rpm -> ban (if you have a request all 5 secs. you will be banned after ~17 minutes)
                        if ($handle = fopen($this->ipsecfile, 'w+')) {
                            fwrite($handle, $content[0].'|ban');
                            fclose($handle);
                        }
                        return false;
                    }
                }
                if ($handle = fopen($this->ipsecfile, 'w+')) { // else write counter
                    fwrite($handle, $content[0].'|'.$current .'');
                    fclose($handle);
                }
            }
        }
        
        return true;
    } 
}
?>