<?php
define("UTORRENT_TORRENT_HASH",0);
define("UTORRENT_TORRENT_STATUS",1);
define("UTORRENT_TORRENT_NAME",2);
define("UTORRENT_TORRENT_SIZE",3);
define("UTORRENT_TORRENT_PROGRESS",4);
define("UTORRENT_TORRENT_DOWNLOADED",5);
define("UTORRENT_TORRENT_UPLOADED",6);
define("UTORRENT_TORRENT_RATIO",7);
define("UTORRENT_TORRENT_UPSPEED",8);
define("UTORRENT_TORRENT_DOWNSPEED",9);
define("UTORRENT_TORRENT_ETA",10);
define("UTORRENT_TORRENT_LABEL",11);
define("UTORRENT_TORRENT_PEERS_CONNECTED",12);
define("UTORRENT_TORRENT_PEERS_SWARM",13);
define("UTORRENT_TORRENT_SEEDS_CONNECTED",14);
define("UTORRENT_TORRENT_SEEDS_SWARM",15);
define("UTORRENT_TORRENT_AVAILABILITY",16);
define("UTORRENT_TORRENT_QUEUE_POSITION",17);
define("UTORRENT_TORRENT_REMAINING",18);
define("UTORRENT_FILEPRIORITY_HIGH",3);
define("UTORRENT_FILEPRIORITY_NORMAL",2);
define("UTORRENT_FILEPRIORITY_LOW",1);
define("UTORRENT_FILEPRIORITY_SKIP",0);
define("UTORRENT_TYPE_INTEGER",0);
define("UTORRENT_TYPE_BOOLEAN",1);
define("UTORRENT_TYPE_STRING",2);
define("UTORRENT_STATUS_STARTED",1);
define("UTORRENT_STATUS_CHECKED",2);
define("UTORRENT_STATUS_START_AFTER_CHECK",4);



class uTorrent {
    //set host to be the IP or hostname of your uTorrent machine
    //set user and pass to be the user and pass you use for the webui
    private $host = '';
    private $user = '';
    private $pass = '';
   	private $port = '7234';
   	private $url = '';
    private $token = '';
    
    public function __construct($host, $user, $pass, $port = false)
    {
    	$this->host = $host;
    	$this->user = $user;
    	$this->pass = $pass;
    	$this->port = $port;  	    	
    }
    
    public function start()
    {
    	$this->getToken();
    	$this->setRequestUrl();
    }
    
    public function resume($token)
    {
    	$this->token = $token;
    	$this->setRequestUrl();
    }
    
    public function is_online() 
    {
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$this->url."&action="); 
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_USERPWD,$this->user.":".$this->pass);
        $ret = curl_exec($ch);
        
        if($ret !== false) return true;

        return false;
    }    
    
    public function getTorrents() 
    {
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, $this->url."&list=1");
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_USERPWD,$this->user.":".$this->pass);
        $ret = curl_exec($ch);
        
        //echo curl_getinfo($ch,CURLINFO_HTTP_CODE);
        $obj = json_decode($ret,true);
        $torrents = $obj['torrents'];
        curl_close($ch);
        
        return $torrents;
    }

   
    public function getLabels()
    {
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, $this->url."&list=1");
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_USERPWD,$this->user.":".$this->pass);
        $ret = curl_exec($ch);
        $obj = json_decode($ret,true);
        $labels = $obj['label'];
        curl_close($ch);
        return $labels;
    }
    
    
    //if you set $estring then when the function is done and returns false, estring will be a string telling you what error happened.
    //$filename could probably be a temporary filenames ($_FILES['somefile']['tmp_name']) but I haven't checked
    //$filename can be a URL
    public function addTorrent($filename,&$estring = false) 
    { 
    
        if(substr($filename,0,7) == "http://") 
        {
            $ch = curl_init();
            curl_setopt($ch,CURLOPT_URL,$this->url."&action=add-url&s=".urlencode($filename));
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
            curl_setopt($ch,CURLOPT_USERPWD,$this->user.":".$this->pass);
            curl_exec($ch);
        } 
        else if(file_exists($filename)) 
        	{
	            $form_fields = array();
	            //$form_fields['add_button'] = "Add File \n";
	            $form_fields['torrent_file'] = "@".realpath($filename);
	            $ch = curl_init();
	            curl_setopt($ch,CURLOPT_URL,$this->url."&action=add-file");
	            curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	            //curl_setopt($ch,CURLOPT_HEADER,true);
	            curl_setopt($ch,CURLOPT_USERPWD,$this->user.":".$this->pass);
	            curl_setopt($ch,CURLOPT_POSTFIELDS,$form_fields);
	            curl_setopt($ch,CURLOPT_VERBOSE,true);
	            $ret = curl_exec($ch);
	            //echo $ret;
	            //echo curl_error($ch);
	            
	            curl_close($ch);
	            $obj = json_decode($ret,true);
	            if(isset($obj['error'])) {
	                if($estring !== false) {
	                    $estring = $obj['error'];
	                }
	                return false;
	            } else {
	                return true;
	            }
        	} 
        	else 
        		{
            		$estring = "File doesn't exist!";
           			return false;
        		}
    }
    
    
    public function removeTorrent($hash,$data=false) {
        $hashes = "";
        if(is_array($hash)) {
            foreach($hash as $value) {
                $hashes.="&hash=".$value;
            }
        } else {
            $hashes = "&hash=".$hash;
        }
        if($data) {
            $action='removedata';
        } else {
            $action='remove';
        }
            
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,"http://".$this->host."/gui/?token={$this->token}&action=".$action.$hashes);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_USERPWD,$this->user.":".$this->pass);
        curl_exec($ch);
    }
    public function startTorrent($hash) {
        $hashes = "";
        if(is_array($hash)) {
            foreach($hash as $value) {
                $hashes.="&hash=".$value;
            }
        } else {
            $hashes = "&hash=".$hash;
        }
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,"http://".$this->host."/gui/?token={$this->token}&action=start".$hashes);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_USERPWD,$this->user.":".$this->pass);
        curl_exec($ch);
    }
    public function pauseTorrent($hash) {
        $hashes = "";
        if(is_array($hash)) {
            foreach($hash as $value) {
                $hashes.="&hash=".$value;
            }
        } else {
            $hashes = "&hash=".$hash;
        }
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,"http://".$this->host."/gui/?token={$this->token}&action=pause".$hashes);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_USERPWD,$this->user.":".$this->pass);
        curl_exec($ch);
    }
    public function stopTorrent($hash) {
        $hashes = "";
        if(is_array($hash)) {
            foreach($hash as $value) {
                $hashes.="&hash=".$value;
            }
        } else {
            $hashes = "&hash=".$hash;
        }
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,"http://".$this->host."/gui/?token={$this->token}&action=stop".$hashes);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_USERPWD,$this->user.":".$this->pass);
        curl_exec($ch);
    }
    public function forcestartTorrent($hash) {
        $hashes = "";
        if(is_array($hash)) {
            foreach($hash as $value) {
                $hashes.="&hash=".$value;
            }
        } else {
            $hashes = "&hash=".$hash;
        }
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,"http://".$this->host."/gui/?token={$this->token}&action=forcestart".$hashes);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_USERPWD,$this->user.":".$this->pass);
        curl_exec($ch);
    }
    public function recheckTorrent($hash) {
        $hashes = "";
        if(is_array($hash)) {
            foreach($hash as $value) {
                $hashes.="&hash=".$value;
            }
        } else {
            $hashes = "&hash=".$hash;
        }
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,"http://".$this->host."/gui/?token={$this->token}&action=recheck".$hashes);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_USERPWD,$this->user.":".$this->pass);
        curl_exec($ch);
    }
    //This could get the files of multiple torrents, but unfortunately json_decode overwrites the files array each time, instead of
    // doing something nice like having files[i] where i is the number of how many hashes you used. 
    //If someone writes a better JSON decoding function which does that I'd be happy to use that instead.
    public function getFiles($hash) {
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,"http://".$this->host."/gui/?token={$this->token}&action=getfiles&hash=".$hash);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_USERPWD,$this->user.":".$this->pass);
        $ret = curl_exec($ch);
        $obj = json_decode($ret,true);
        return $obj;
    }
    
    //This could be get the properties of multiple torrents, but unfortunately json_decode overwrites the props array each time, instead of
    // doing something nice like having props[i] where i is the number of how many hashes you used. 
    //If someone writes a better JSON decoding function which does that I'd be happy to use that instead.
    public function getProperties($hash) {
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,"http://".$this->host."/gui/?token={$this->token}&action=getprops&hash=".$hash);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_USERPWD,$this->user.":".$this->pass);
        $ret = curl_exec($ch);
        $obj = json_decode($ret,true);
        return $obj;
    }
    
    /**
    * properties (in format *    propertyname        type and explanation)
    *    trackers        string (what trackers to use seperated by \r\n\r\n)
    *    ulrate        int (maximum upload rate of torrent)
    *    dlrate        int (maximum download rate of torrent)
    *    superseed        bool (whether or not to superseed)
    *    dht            bool (whether or not to use DHT)
    *    pex            bool (whether or not to use peer exchange)
    *    seed_override    bool (whether or not global seeding settings are overridden)
    *    seed_ratio        int (what ratio to seed to before stopping)
    *    seed_time        int (what time to seed to before stopping (seconds))
    *    ulslots        int (number of upload slots)
    */
    //this might work using multiple hashes but for now I haven't set to be able to
    //it equally might work with multiple properties and values, again I haven't tried so haven't coded it to do so
    public function setProperty($hash,$property,$value) {
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,"http://".$this->host."/gui/?token={$this->token}&action=setprops&hash=".$hash."&s=".$property."&v=".$value);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_USERPWD,$this->user.":".$this->pass);
        curl_exec($ch);
    }
    //$file is a number between 0 and the number of files in the torrent minus one. ( I think. might be 1-based)
    //Files are in the order that getFiles returns them in
    //Priority is one of the UTORRENT_FILEPRIORITYs I defined earlier
    //If $files is an array it will set the priority on each
    public function setPriority($hash,$files,$priority) {
        $filenums = "";
        if(is_array($files)) {
            foreach($files as $value) {
                $filenums.="&f=".$value;
            }
        } else {
            $filenums = "&f=".$file;
        }
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,"http://".$this->host."/gui/?token={$this->token}&action=setprio&hash=$hash&p=".$priority.$filenums);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_USERPWD,$this->user.":".$this->pass);
        curl_exec($ch);
    }
    
    public function getSettings() {
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,"http://".$this->host."/gui/?token={$this->token}&action=getsettings");
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_USERPWD,$this->user.":".$this->pass);
        $ret = curl_exec($ch);
        $obj = json_decode($ret,true);
        return $obj;
    }
    public function setSetting($setting,$value) {
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,"http://".$this->host."/gui/?token={$this->token}&action=setsetting&s=".$setting."&v=".$value);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_USERPWD,$this->user.":".$this->pass);
        curl_exec($ch);
    }

    private function setRequestUrl()
    {
    	if(!$this->token) $this->getToken();
    	
    	$this->url = ($this->port)? "http://{$this->host}:{$this->port}/gui/?token={$this->token}" : "http://{$this->host}/gui/?token={$this->token}";
   
    	return $this->url;
    }
    
    private function getToken()
    {
    	$ch = curl_init();

    	curl_setopt($ch,CURLOPT_URL,"http://".$this->host."/gui/token.html?t=".time()); 
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_USERPWD,$this->user.":".$this->pass);

        $ret = curl_exec($ch);
        
        preg_match("/\<div(.+)\>(.+)\<\/div\>/", $ret, $matches);
        
        $this->token = $matches[2];	
    }

}


?>