<?php
/**
 * The SMTP Class provide access to a smtp server.
 * Multiple message sending is possible, too.
 *  
 *
 * @package: smtp.class.inc
 * @author: Steffen 'j0inty' StollfuÃŸ
 * @created 12.06.2008
 * @copyright: Steffen 'j0inty' StollfuÃŸ
 * @version: 0.7.1-dev
 */
/**
 * Class SMTP_Exception
 *
 * @author j0inty
 * @version 1.1.0-final
 */
class SMTP_Exception extends Exception
{
	/**
	 * SMTP Exception constructor
	 * 
	 * @param integer $intErrorCode
	 * @param string $strErrorMessage
	 */
	function __construct($intErrorCode, $strErrorMessage = null)
	{
		switch($intErrorCode)
		{
			case SMTP::ERR_SOCKETS:
				$strErrorMessage = "Sockets Error: (". socket_last_error() .") -- ". socket_strerror(socket_last_error());
			break;
			
			case SMTP::ERR_NOT_IMPLEMENTED:
				$strErrorMessage = "Not implemented now.";
			break;
		}
		parent::__construct($strErrorMessage, $intErrorCode);
	}
	/**
	 * Store the Exception string to a given file
	 * 
	 * @param string $strLogFile  logfile name with path
	 */
	public function saveToFile($strLogFile)
	{
		if( !$resFp = @fopen($strLogFile,"a+") )
		{
			return false;
		}
		$strMsg = date("Y-m-d H:i:s -- ") . $this;
		if( !@fputs($resFp, $strMsg, strlen($strMsg)) )
		{
			return false;
		}
		@fclose($resFp);
	}
	/**
	 * toString method
	 */
	public function __toString()
	{
		return __CLASS__ ."[". $this->getCode() ."] -- ". $this->getMessage() ." in file ". $this->getFile() ." at line ". $this->getLine().PHP_EOL."Trace: ". $this->getTraceAsString() .PHP_EOL;
	}
}

/**
 * SMTP class
 * 
 * @author j0inty
 */
class SMTP
{
	/**
	 * E-Mail Priotities
	 * 
	 * @var const integer
	 */
	const MESSAGE_PRIO_LOW = 5;
	const MESSAGE_PRIO_MEDIUM = 3;
	const MESSAGE_PRIO_HIGH = 1;
	/**
	 * @var const string XMAILER
	 */
	const XMAILER = "M-CMS.Mailer v.5.20.3541 -- Info: www.m-cms.org Product owner: www.itbc.pro";
	/**
	 * @var const integer No errors occured
	 * @access public
	 */
	const ERR_NONE = 0;
	/**
	 * @var const integer parameter error
	 */
	const ERR_PARAMETER = 1;
	/**
	 * @var const integer logging error
	 */
	const ERR_LOG = 2;
	/**
	 * @var const integer sockets extension error
	 */
	const ERR_SOCKETS = 3;
	/**
	 * @var const integer sockets extension error
	 */
	const ERR_STREAM = 4;
	/**
	 * @var const integer invalid response code came from the server
	 */
	const ERR_INVALID_RESPONSE = 5;
	/**
	 * @var const integer comes when a function isn't implemented yet
	 */
	const ERR_NOT_IMPLEMENTED = 20;
	/**
	 * Authorization method constant
	 */
	const AUTH_PLAIN = 100;
	/**
	 * Regular Expression for a valid email adress
	 * I know that this regex isn't the best
	 * 
	 */
	const REGEX_EMAIL = "((<)|([A-Za-z0-9._-](\+[A-Za-z0-9])*)+@[A-Za-z0-9.-]+\.[A-Za-z]{2,6}|(>))";
	/**
	 * default buffer size for socket reads
	 * 
	 * @var const integer
	 */
	const DEFAULT_BUFFER_SIZE = 4096;
	/**
	 * @var string SMTP Server Hostname
	 * @access private
	 */
	private $strHostname = null;
	/**
	 * @var string $strIPAdress
	 * @access private
	 */
	private $strIPAdress = null;
	/**
	 * @var integer SMTP Server Port
	 * @access private
	 */
	private $intPort = 25;
	/**
	 * @var resource Socket
	 * @access private
	 */
	private $resSocket = false;
	/**
	 * @var boolean $bSocketConnected
	 */
	private $bSocketConnected = false;
	/**
	 * @var array Connection Timeout
	 * @access private
	 */
	private $arrConnectionTimeout = array("sec" => "10", "msec" => "0");
	/**
	 * @var boolean Using sockets extension
	 * @access private
	 */
	private $bUseSockets = true;
	/**
	 * @var boolean  Hide the username at the log file (sha256)
	 * @access private
	 */
	private $bHideUsernameAtLog = true;
	/**
	 * @var string log path to file
	 * @access private
	 */
	private $strLogFile = null;
	/**
	 * @var boolean log filedescriptor opened
	 * @access private
	 */
	private $bLogOpened = false;
	/**
	 * @var resource log file descriptor
	 * @access private 
	 */
	private $resLogFp = false;
	/**
	 * @var string  Use it as prefix for every log line
	 * @access private
	 */
	private $strLogDatetimeFormat = "Y-m-d H:i:s";
	/**
	 * constructor
	 * 
	 * @param string $strLogFile filename where the class will log
	 * @param boolean $bHideUsernameAtLog should the username hide in the logfile
	 * @param boolean $bUseSockets should the class use the sockets extension ?
	 * 
	 * @return SMTP SMTP class object
	 * @throw SMTP_Exception
	 * @access public
	 */
	function __construct( $strLogFile = null, $bHideUsernameAtLog = true, $bUseSockets = true)
	{
		// Check input
		if( !is_bool($bHideUsernameAtLog) )
		{
			throw new SMTP_Exception(self::ERR_PARAMETER,"Invalid hide username at logfile parameter given");
		}
		if( !is_bool($bUseSockets) )
		{
			throw new SMTP_Exception(self::ERR_PARAMETER,"Invalid UseSockets parameter given");
		}
		elseif( $bUseSockets && !extension_loaded("sockets") )
		{
			throw new SMTP_Exception(self::ERR_PARAMETER,"You choose the socket extension support, but this isn't available");
		}
		if( is_string($strLogFile) )
		{
			$this->strLogFile = $strLogFile;
			$this->openLog();
		}
		$this->bHideUsernameAtLog = $bHideUsernameAtLog;
		$this->bUseSockets = $bUseSockets;
	}
	
	/**
	 * destructor
	 * 
	 * @access public
	 * @throw SMTP_Exception
	 */
	function __destruct()
	{
		$this->disconnect();
		$this->closeLog();
	}
	/**
	 * connect to the smtp server
	 * 
	 * @param string $strHostname  IP or hostname of the smtp server
	 * @param integer $intPort  Port where the smtp server is listen to
	 * @param array $arrConnectionTimeout
	 * @param boolean $bIPv6
	 * 
	 * @access public
	 * @throw SMTP_Exception
	 */
	public function connect( $strHostname, $intPort = 25, $arrConnectionTimeout = null, $bIPv6 = false)
	{
		if( !is_string($strHostname) )
		{
			throw new SMTP_Exception(self::ERR_PARAMETER,"Invalid hostname string given");
		}
		if( !is_int($intPort) && $intPort > 0 && $intPort < 65535 )
		{
			throw new SMTP_Exception(self::ERR_PARAMETER,"Invalid port given");
		}
		if( !is_bool($bIPv6) )
		{
			throw new SMTP_Exception(self::ERR_PARAMETER,"Invalid ipv6 given");
		}
		
		if( $this->bUseSockets )
		{
			if( !$this->resSocket = @socket_create( (($bIPv6) ? AF_INET6 : AF_INET), SOL_SOCKET, SOL_TCP ) )
			{
				throw new SMTP_Exception(self::ERR_SOCKETS);
			}
			
			$this->log((($bIPv6) ? "AF_INET6" : "AF_INET") ."-TCP-Socket created.");
			
			if(!is_null($arrConnectionTimeout) )
			{
				$this->setSocketTimeout($arrConnectionTimeout);
			}
			
			if( !@socket_connect($this->resSocket,$strHostname,$intPort)
				|| !@socket_getpeername($this->resSocket,$this->strIPAdress) )
			{
				throw new SMTP_Exception(self::ERR_SOCKETS);
			}
		}
		else
		{
			$dTimeout = (double) implode(".",$arrConnectionTimeout);
			if( !@fsockopen("tcp://". $strHostname .":". $intPort, &$intErrno, &$strError, $dTimeout) )
			{
				throw new SMTP_Exception(self::ERR_STREAM,"fopen: [". $intErrno ."] -- ". $strError);
			}
			if(!is_null($arrConnectionTimeout) ) $this->setSocketTimeout($arrConnectionTimeout);
			$this->strIPAdress = @gethostbyname($strHostname);
		}
		$this->strHostname = $strHostname;
		$this->intPort = $intPort;
		$this->bSocketConnected = true;
		$this->log("socket: Connected to ". $this->strIPAdress .":". $intPort ." [". $strHostname ."]");
		// Welcome message from the server
		$this->parseResponse("220");
		// EHLO Stuff
		$this->sendCommand("EHLO ". $strHostname,250);
		
	}
	/**
	 * disconnect from the smtp server
	 * 
	 * @access public
	 * @throw SMTP_Exception
	 */
	public function disconnect()
	{
		if( $this->bSocketConnected )
		{
			$this->sendCommand("QUIT", 221);
			if( $this->bUseSockets )
			{
				if( @socket_close($this->resSocket) === false)
				{
					throw new SMTP_Exception(self::ERR_SOCKETS);
				}
			}
			else
			{
				if( !@fclose($this->resSocket) )
				{
					throw new SMTP_Exception(self::ERR_STREAM,"fclose: Faild to close the socket" );
				}
			}
			$this->bSocketConnected = false;
			$this->log("socket: Disconnected from ". $this->strIPAdress .":". $this->intPort ." [". $this->strHostname ."]");
		}
		
	}
	/**
	 * login into the server
	 * 
	 * !!! Cauition !!!
	 * This is only need for smtp server with authorization, else you don't need to call this function
	 * 
	 * @param string $strUsername Username of your account on the smtp server
	 * @param string $strPassword The password for your account
	 * 
	 * @throw SMTP_Exception
	 * @access public
	 */
	public function login( $strUsername = "", $strPassword = "" , $intAuthMethod = self::AUTH_PLAIN )
	{
		if( empty($strUsername) || empty($strPassword) )
		{
			throw new SMTP_Exception(self::ERR_PARAMETER,"Invalid username or password given. If is no login needed don't use this function here.");
		}
		if( $intAuthMethod == self::AUTH_PLAIN )
		{
			$this->sendCommand("AUTH LOGIN", 334);
			$this->sendCommand(base64_encode($strUsername),334,"Username: ". $strUsername);
			$this->sendCommand(base64_encode($strPassword),235,"Password: ". sha1($strPassword));
		}
		else
		{
			throw new Exception(self::ERR_NOT_IMPLEMENTED);
		}
	}
	/**
	 * Send an email
	 * 
	 * @param string $strFrom  From/Sender Adress
	 * @param 
	 */
	public function sendMessage($strFrom, $mixedTo, $strSubject, $strMessage, $mixedOptionalHeader = null, $mixedCC = null, $mixedBCC = null, $intPriority = self::MESSAGE_PRIO_MEDIUM)
	{
		$arrMailHeader = array();
		if( !is_string($strFrom) || !preg_match(self::REGEX_EMAIL,$strFrom) )
		{
			throw new SMTP_Exception(self::ERR_PARAMETER, "Invalid \"from\" parameter given or invalid adress [". $strFrom ."]");
		}
		if( !is_array($mixedTo) )
		{
			if( !is_string($mixedTo) ) 
				throw new SMTP_Exception(self::ERR_PARAMETER, "Invalid \"to\" parameter given");
		}
		if( !is_string($strSubject) )
		{
			throw new SMTP_Exception(self::ERR_PARAMETER, "Invalid subject parameter given");
		}
		if( !is_string($strMessage) )
		{
			throw new SMTP_Exception(self::ERR_PARAMETER, "Invalid message parameter given");
		}
		if( !is_null($mixedOptionalHeader) && !is_array($mixedOptionalHeader) )
		{
			if( !is_string($mixedOptionalHeader) )
				throw new SMTP_Exception(self::ERR_PARAMETER, "Invalid optional header parameter given");
		}
		if( !is_null($mixedCC) && !is_array($mixedCC) )
		{
			if(!is_string($mixedCC))
				throw new SMTP_Exception(self::ERR_PARAMETER, "Invalid \"cc\" parameter given");
		}
		if( !is_null($mixedBCC) && !is_array($mixedBCC) )
		{
			if( !is_string($mixedBCC) )
				throw new SMTP_Exception(self::ERR_PARAMETER, "Invalid \"bcc\" parameter given");
		}
		if( !is_int($intPriority) || ($intPriority < 1 || $intPriority > 5) )
		{
			throw new SMTP_Exception(self::ERR_PARAMETER, "Invalid priority parameter given. Allowed is a value between [1-5]");
		}
		// Prepare the Header
		
		
		// From
		$this->sendCommand("MAIL FROM: <". $strFrom .">",250);
		$arrMailHeader["from"] = $strFrom;
		
		// TO
		if( !is_array($mixedTo) )
		{
			$this->rcptTo($mixedTo);
			$arrMailHeader["to"] = "<". $mixedTo .">";
		}
		else
		{
			$bFirst = true;
			$arrMailHeader["to"] = "";
			foreach( $mixedTo AS $email )
			{
				$this->rcptTo($email);
				if( $bFirst )
				{
					$arrMailHeader["to"] .= "<". $email .">";
					$bFirst = false;
				}
				else
				{
					$arrMailHeader["to"] .= ", <". $email .">";
				}
			}
		}
		
		// CC
		if( !is_null($mixedCC) )
		{
			if( !is_array($mixedCC) )
			{
				$this->rcptTo($mixedCC);
				$arrMailHeader["cc"] = "<". $mixedCC .">";
			}
			else
			{
				$bFirst = true;
				$arrMailHeader["cc"] = "";
				foreach( $mixedCC AS $email )
				{
					$this->rcptTo($email);
					if( $bFirst )
					{
						$arrMailHeader["cc"] .= "<". $email .">";
						$bFirst = false;
					}
					else
					{
						$arrMailHeader["cc"] .= ", <". $email .">";
					}
				}
			}
		}
		
		// BCC
		if( !is_null($mixedBCC) )
		{
			if( !is_array($mixedBCC) )
			{
				$this->rcptTo($mixedBCC);
				$arrMailHeader["bcc"] = "<". $mixedBCC .">";
			}
			else
			{
				$bFirst = true;
				$arrMailHeader["bcc"] = "";
				foreach( $mixedBCC AS $email )
				{
					$this->rcptTo($email);
					if( $bFirst )
					{
						$arrMailHeader["bcc"] .= "<". $email .">";
						$bFirst = false;
					}
					else
					{
						$arrMailHeader["bcc"] .= ", <". $email .">";
					}
				}
			}
		}
		
		/*
		 *  Send the message
		 */
		$this->sendCommand("DATA", 354);
		// Header first
		while( ($key = key($arrMailHeader)) != null )
		{
			$this->send(ucfirst($key) .": ". $arrMailHeader[$key]);
			next($arrMailHeader);
		}
		$this->send("Subject: ". $strSubject);
		$this->send("Date: ". date("r"));
		$this->send("X-Mailer: ". self::XMAILER);
		
		// default priority for a mail is 3 so we don't need to add a line for that
		if( $intPriority != 3 )
		{
			$this->send("X-Priority: ". $intPriority);
			if( $intPriority == 1 || $intPriority == 2 )
			{
				$this->send("X-MSMail-Priority: High");
			}
			else
			{
				$this->send("X-MSMail-Priority: Low");
			}
		}
		// Optional header
		if( !is_null($mixedOptionalHeader) )
		{
			if( !is_array($mixedOptionalHeader) )
			{
				$this->send($mixedOptionalHeader);
			}
			else
			{
				foreach( $mixedOptionalHeader as &$strHeader )
				{
					$this->send($strHeader);
				}
			}
		}
		// Close the Header part
		$this->send("");
		// Message body
		$this->send($strMessage);
		// Close the message
		$this->sendCommand(".",250);
	}
	/**
	 * Set the socket send and recv timeouts
	 * 
	 * @param array $arrTimeout
	 * 
	 * @throw SMTP_Exception
	 * @return void
	 * @access private
	 */
	private function setSocketTimeout( $arrTimeout )
	{
		if( !is_array($arrTimeout) || !is_int($arrTimeout["sec"]) || !is_int($arrTimeout["usec"]) )
		{
			throw new SMTP_Exception(self::ERR_PARAMETER,"Invalid connection timeout given");
		}
		if( $this->bUseSockets )
		{
			if( !@socket_set_option($this->resSocket,SOL_SOCKET,SO_RCVTIMEO,$arrTimeout)
				|| !@socket_set_option($this->resSocket,SOL_SOCKET,SO_SNDTIMEO,$arrTimeout) )
			{
				throw new SMTP_Exception(self::ERR_SOCKETS);
			}
		}
		else
		{
			if( !@stream_set_timeout($this->resSocket,$arrTimeout["sec"],$arrTimeout["usec"]) )
			{
				throw new SMTP_Exception(self::ERR_STREAM,"stream_set_timeout: Failed to set stream connection timeout");
			}
		}
		$this->log("socket timeout: ". implode(".",$arrTimeout) ." seconds");
	}
	/**
	 * Send a string to the server
	 * 
	 * @param string $str
	 * @param integer $intFlags  socket_send() flags (only need for socket_extension)
	 * 
	 * @throw SMTP_Exception
	 * @access private
	 */
	private function send( $str, $intFlags = 0 )
	{
		$str = $str ."\r\n";
		if( $this->bUseSockets )
		{
			if( !@socket_send($this->resSocket,$str,strlen($str),$intFlags) )
			{
				throw new SMTP_Exception(self::ERR_SOCKETS);
			}
		}
		else
		{
			if( !@fwrite($this->resSocket,$str,strlen($str)) )
			{
				throw new SMTP_Exception(self::ERR_STREAM,"fwrite: Failed to write to socket");
			}
		}
	}
	/**
	 * Recieve a string from the server
	 * 
	 * @param integer $intBufferSize
	 * 
	 * @throw SMTP_Exception
	 * @access private
	 */
	private function recvString( $intBufferSize = self::DEFAULT_BUFFER_SIZE )
	{
		$strBuffer = "";
		if( $this->bUseSockets )
		{
			if( ($strBuffer = @socket_read($this->resSocket, $intBufferSize, PHP_NORMAL_READ)) === false )
			{
				throw new SMTP_Exception(self::ERR_SOCKETS);
			}
			/*
			 * Workaround: PHP_NORMAL_READ stops at "\r" but the network string is terminated by "\r\n",
			 * 			   so we need to call socket_read again for this 1 char "\n"
			 */
			if( ($strBuffer2 = @socket_read($this->resSocket, 1, PHP_NORMAL_READ)) === false )
			{
				throw new SMTP_Exception(self::ERR_SOCKETS);
			}
			$strBuffer .= $strBuffer2;
		}
		else
		{
			if( !$strBuffer = @fgets($this->resSocket,$intBufferSize) )
			{
				throw new SMTP_Exception(self::ERR_STREAM,"fgets: Couldn't read string from socket");
			}
		}
		return $strBuffer;
	}
	/**
	 * Recieve a string from the socket and check was the need response code given.
	 * Else not it will throw an exception with the message from the server
	 * 
	 * @param integer $intNeededCode needed Responsecode from the server
	 * @param integer $intBufferSize  @see recvString()
	 * 
	 * @throw SMTP_Exception
	 * @access private
	 */
	private function parseResponse( $intNeededCode, $intBufferSize = self::DEFAULT_BUFFER_SIZE )
	{
		while(true)
		{
			$strBuffer = $this->recvString($intBufferSize);
			$this->log($strBuffer);
			if(preg_match("/^[0-9]{3}( )/", $strBuffer))
			{
				break;
			}
		}
		if( !preg_match("/^(". $intNeededCode .")/",$strBuffer) )
		{
			throw new SMTP_Exception(self::ERR_INVALID_RESPONSE,$strBuffer);
		}
	}
	/**
	 * Send the command to the server and check for the needed response code
	 * 
	 * @param string $cmd  command for the server
	 * @param integer $neededCode @see parseResponse()
	 * @param string $strLog  String that should log, else we use the command string
	 * @param integer $intFlags  @see send()
	 * 
	 * @throw SMTP_Exception
	 * @access private
	 */
	private function sendCommand( $strCommand, $intNeededCode, $strLog = null, $intFlags = 0 )
	{
		( !is_null($strLog) ) ? $this->log($strLog) : $this->log($strCommand);
		$this->send($strCommand,$intFlags);
		$this->parseResponse($intNeededCode);
	}
	/**
	 * reciept to
	 * 
	 * @param string $email  E-Mail-Adress
	 */
	private function rcptTo($email)
	{
		//if( !is_null($email) )
		//{
		//	if( !preg_match(self::REGEX_EMAIL,$email) )
		//	{
		//		throw new SMTP_Exception(self::ERR_PARAMETER,"Invalid email address given [". $email ."]");
		//	}
			$this->sendCommand("RCPT TO: ". $email, 250);
		//}
	}
	/**
	 * open the log filedescriptor
	 * 
	 * @throw SMTP_Exception
	 * @access private
	 */
	private function openLog()
	{
		// Note: Constructor checks is it a string or not so we don't need to check for null again
		// Think about, test it.
		if( !$this->bLogOpened /*&& !is_null($this->strLogFile)*/ )
		{
			if( !$this->resLogFp = @fopen($this->strLogFile,"a+") )
			{
				throw new SMTP_Exception(self::ERR_LOG,"fopen: Couldn't open log file: ". $this->strLogFile);
			}
			$this->bLogOpened = true;
		}
	}
	/**
	 * close the log filedescriptor
	 * 
	 * @access private
	 */
	private function closeLog()
	{
		if( $this->bLogOpened )
		{
			@fclose($this->resLogFp);
			$this->bLogOpened = false;
		}
	}
	/**
	 * open the log filedescriptor
	 * 
	 * @param string $str  String to log
	 * 
	 * @throw SMTP_Exception
	 * @access private
	 */
	private function log( $str )
	{
		if( $this->bLogOpened )
		{
			$str = date($this->strLogDatetimeFormat). ": ". trim($str) .PHP_EOL;
			if( !@fwrite($this->resLogFp, $str, strlen($str)) )
			{
				throw new SMTP_Exception(self::ERR_LOG,"fwrite: Failed to wrote to logfile. (". trim($str) .")");
			}
		}
	}
}

class smtp_sender 
{
	public $logfile = '';
	public $errorFile = '';
	public $smtpHost = '';
	public $smtpPass = '';
	public $smtpPort = 25;
	public $smtpUser = '';
	public $timeout = 2;
	public $defaultType = 'text/plain';
	public $defaultEncoding = 'utf-8';	
	
	
	public function __construct($host, $login, $pass, $port = false)
	{
		global $core;
		
		$this->logfile = $core->CONFIG['temp_path'].'mail/'.date("d.m.Y H:i").'.log';
		$this->errorFile = $core->CONFIG['temp_path'].'mail/'.date("d.m.Y H:i").'.error.log';
		$this->smtpHost = $host;
		$this->smtpPass = $pass;
		$this->smtpUser = $login;
		$this->smtpPort = ($port)? $port : $this->smtpPort;
		
		$this->checkLogDir($core->CONFIG['temp_path'].'mail/');
	}
	
	public function send($from, $to, $subject, $message, $cc = null, $bcc = null, $replyTo = '')
	{
		try
		{
			$header = $this->getHeaders($replyTo, $from);
			$timeout = array("sec" => $this->timeout, "usec" => $this->timeout);
			
			$smtp = new SMTP($this->logfile);
			$smtp->connect($this->smtpHost, $this->smtpPort, $timeout, false);
			$smtp->login($this->smtpUser,$this->smtpPass);
			$smtp->sendMessage($from, $to, $subject, $message, $header, $cc, $bcc, SMTP::MESSAGE_PRIO_HIGH);
			$smtp->disconnect();
			
			return true;
		} 
		catch( SMTP_Exception $e )
			{
    			$e->saveToFile($this->errorFile);
    			return false;
			} 
	}
	
	private function getHeaders($replyto, $from)
	{
		$reply = ($replyto)? $replyto : $from;
		$header = array( "Content-Type: {$this->defaultType}; charset=\"{$this->defaultEncoding}\"", "Reply-To: ". $reply);
		return $header;
	}
	
	private function checkLogDir($folder)
	{
		if(!is_dir($folder))
		{
			if(!@mkdir($folder, 0775, true)) return false;
		}	
		
		return true;
	}
	
		/*
		
		$from = "hans_meier@web.de";
		$to = array("max_muster_mann@web.de","sonja_muster_frau@web.de");
		$cc = "klaus@muster_domain.de";
		$bcc = null;
		
		$subject = "Hello World";
		$message = "Ã¤Ã¶Ã¼Ã„Ã–ÃœÃŸ\r\nHello World,\r\nNice to meet you ;)\r\nregards\r\nj0inty";
		*/
}

?>