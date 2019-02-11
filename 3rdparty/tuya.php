<?php
/*
infos sur les plugs :
https://github.com/Marcus-L/m4rcus.TuyaCore

Recup des clés:
https://github.com/clach04/python-tuya/wiki



*/
require_once dirname(__FILE__) . '/include/common.php';
class W2_TuyaBase
{
	//Tuya Bulbs commands
	protected $_host;
	protected $_port;
	protected $_wait;
	protected $_repeat;
	protected $_repeatOK;
	protected $_color = "#000000";
	protected $_colorAmb = "#000000";
	protected $_increm;
	protected $_returnOK = false;
	protected $_cap = NULL;
	protected $_delay = 101000; //microseconds
	protected $_return ;
	protected $_log;
	protected $_socket;
	protected 	$_commandCodes = array(
	'CMD_OFF' => '{"t":"%d","devId":"%s","dps":{"1":false,"2":"white"},"uid":""}',
	'CMD_ON'  => '{"t":"%d","devId":"%s","dps":{"1":true,"2":"white"},"uid":""}',
	'CMD_OFF_SW_D' => '{"t":"%d","devId":"%s","dps":{"%d":false,"%d":%d},"uid":""}',
	'CMD_OFF_SW' => '{"t":"%d","devId":"%s","dps":{"%d":false},"uid":""}',
	'CMD_ON_SW_D'  => '{"t":"%d","devId":"%s","dps":{"%d":true,"%d":%d},"uid":""}',
	'CMD_ON_SW'  => '{"t":"%d","devId":"%s","dps":{"%d":true},"uid":""}',
	'CMD_GET_PROP'  => '{"gwId":"%s","devId":"%s"}',
	'CMD_CT'  => '{"t":"%d","devId":"%s","dps":{"2":"white","4": %d},"uid":""}',
	'CMD_BRIGHTNESS'  => '{"t":"%d","devId":"%s","dps":{"2":"white","3": %d},"uid":""}',
	'CMD_BRIGHTNESS_COL'  => '{"t":"%d","devId":"%s","dps":{"2":"colour","3": %d},"uid":""}',
	'CMD_COLOR'  => '{"t":"%d","devId":"%s","dps":{"2":"colour","5": "%s"},"uid":""}',
	);
	protected $_prefix = array(0, 0, 85, 170, 0, 0, 0, 0, 0, 0, 0 );
    protected $_suffixWrite = array(0, 0, 0, 0, 0, 0, 170, 85);
	protected $_suffixRead = array(0, 0, 170, 85 );
	protected $_devId= "0120015260091453a970";
    protected $_LocalKey = "5f5f784cd82d449b";
	protected $_version = "3.1";
	//Used to dynamically call class methods
	public function call(){
		echo __METHOD__;
	}	
	public function __construct($host = '192.168.1.110', $wait=0, $repeat=1, $increm=10, $LocalKey=0, $ID="", $cap=NULL, $nbLeds=0, $colorOrder=0, $port = 6668) {
		$this->_host = $host;
		$this->_port = $port;
		$this->_devId=$ID;
		$this->_LocalKey=$LocalKey;
		$this->_repeatOK = true;
		if ($wait < 0)
			$wait = 0;
		if ($wait > 100)
			$wait = 100;
		$this->_wait = $wait*1000;
		if ($repeat<1)
			$repeat =1;
		if ($repeat>5)
			$repeat =5;
		$this->_repeat = $repeat;
		if ($increm<1)
			$increm =1;
		if ($increm>25)
			$increm =25;
		$this->_devID = $ID;
		$this->_LocalKey = $LocalKey;
		$this->_increm = $increm;
		$this->_cap = $cap;
		$myRet = new wifilightV2c;
		$this->_return = $myRet->_return_WFL;
		$this->_return['Type'] = true;
		$this->_log = $myRet->_log;
		$this->_socket=NULL;
	}
	public function GetColor() {
		return $this->_color;
	}
	public function GetColorAmb() {
		return $this->_colorAmb;
	}
	public function getStatus() {
		$Id = 10;
		$string = sprintf($this->_commandCodes['CMD_GET_PROP'],$this->_devId,$this->_devId);
		$this->_returnOK = true;
		return $this->send($string,$Id,0);
	}
	public function retStatus($OutStr=0) {
		if ($OutStr == 0) $OutStr = $this->getStatus();
		if ( isset($OutStr) && $OutStr!=NOSOCKET && $OutStr!=NOTCONNECTED && $OutStr!=NORESPONSE && $OutStr!=BADRESPONSE) {		
		//log::add($this->_log,'debug',"set");
			if ($OutStr["devId"]!="") {
				//log::add($this->_log,'debug',"Not null");
				// for tuya light
				// 1 : (1 or NULL) ON/Off
				// 2 : colour or white
				// 3 : brightness (white)
				// 4 : color temp (white)
				// 5 : colour 
				if( isset($OutStr["dps"][1])==true ) {		
					if ($OutStr["dps"][1] == 1){
						$this->_return['On'] = 1;
						log::add($this->_log,'debug','ON');
					} 
					else {
						$this->_return['On'] = 0;
						log::add($this->_log,'debug','OFF');
					}
				}	
				if( isset($OutStr["dps"][2])==true && $OutStr["dps"][2]!="") {
						if ($OutStr["dps"][2] == 'white')
							$this->_return['White'] = $OutStr["dps"][2];
						log::add($this->_log,'debug',"mode:".$OutStr["dps"][2]);					
				}
				if( isset($OutStr["dps"][3])==true && $OutStr["dps"][3]!="") {
					if( isset($OutStr["dps"][2])==true && $OutStr["dps"][2]!="") {
						if ($OutStr["dps"][2] == "white") {
							$White = $OutStr["dps"][3]*100/255;
							$this->_return['White'] = $White;
							log::add($this->_log,'debug',"White:".$OutStr["dps"][3]);	
						}
					}
				}
				if( isset($OutStr["dps"][4])==true && $OutStr["dps"][4]!="") {
					$Ct = $OutStr["dps"][4]*100/255;
					$this->_return['Kelvin'] = $Ct;
					log::add($this->_log,'debug',"Temp:".$OutStr["dps"][4]);
				}
				if( isset($OutStr["dps"][5])==true && $OutStr["dps"][5]!="") {
					$Colhex = substr($OutStr["dps"][5],0,6);
					$this->_return['Color'] = $Colhex;
					log::add($this->_log,'debug',"Col:".$Colhex);
				}
			}
			else
				$this->_return['Type'] = 'Not a Tuya light';
		}
		else if ( !isset($OutStr) )
			$this->_return = BADRESPONSE;
		else
			$this->_return = $OutStr;
		return $this->_return;	
	}
	protected function send($command,$Id,$Crypt=1) {
		// $Crypt 
		// 1 : 1 send to code / 2 responses no json
		// 0 : 1 send no code / 1 reponse json decode
		// 2 : 1 send code special / 1 response no json
		$message = $command;
		log::add($this->_log,'debug','Commande : '.$message );
		if ($Crypt == 2) $message= $this->CryptS($message,$Id,false);
		else $message= $this->Crypt($message,$Id,$Crypt);
		// Create a TCP/IP socket.		
		//if ($this->_socket!=NULL) 
			$this->_socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		
		if ($this->_socket === false) {
			log::add($this->_log,'debug',"socket_create() failed: reason: " .socket_strerror(socket_last_error()) );
			return NOSOCKET;
		}
		else {
			//log::add($this->_log,'debug','socket_create() OK.');
			log::add($this->_log,'debug',"try to connect to : $this->_host  $this->_port");
			socket_set_option($this->_socket, SOL_SOCKET, SO_RCVTIMEO, array("sec" => 0, "usec" => 250000));
			socket_set_option($this->_socket, SOL_SOCKET,SO_SNDTIMEO, array("sec" => 0, "usec" => 500000));				
			//socket_set_nonblock($this->_socket);
			// store the current time
			$time = microtime(true);
			$result = true;
			$timeout= 1;
			// loop until a connection is gained or timeout reached
			while (!@socket_connect($this->_socket, $this->_host, $this->_port)) {
				$err = socket_last_error($this->_socket);
				// success!
				if($err === 56) {
					//log::add($this->_log,'debug',"Connect OK");
					$result = true;
					break;
				}
				if ((microtime(true) - $time) >= $timeout) {
					//socket_close($this->_socket);
					log::add($this->_log,'debug',"Time out");
					$result = false;
					break;
				}
				usleep(5000);
			}			
			if ($result === false) {
				log::add($this->_log,'debug',"socket_connect() failed. Reason: ($result) " . socket_strerror(socket_last_error($this->_socket)) );
				socket_close($this->_socket);
				return NOTCONNECTED ;
			}
			else {			
				log::add($this->_log,'debug','Connect OK');			
				for ($iCount=0;$iCount<$this->_repeat;$iCount++) {
					socket_set_option($this->_socket,SOL_SOCKET, SO_SNDTIMEO, array("sec" => 0, "usec" => 250000));
					$ret = socket_write($this->_socket,$message,strlen($message));	
					if ($ret === false ) {
						log::add($this->_log,'debug',"socket_write() failed. Reason: ($ret) " .socket_strerror(socket_last_error()) );							
					}
					else {	
						// 2 responses
						$resp="";					
						if ($Crypt == 1 ) $resp = $this->GetResp(2);
						if ($resp!=BADRESPONSE ) $resp = $this->GetResp($Crypt);
						socket_close($this->_socket);
						return $resp;							
					}
					usleep($this->_wait);
				}
				socket_close($this->_socket);		
				return BADRESPONSE;			
			}
		}
	}
	public function GetResp($JDecode){
		$out2="";
		$out="";
		$time = microtime(true);
		$timeout = 0.1;
		do {
			$host = $this->_host;
			$port = $this->_port;
			socket_recvfrom($this->_socket, $content, 4096,MSG_DONTWAIT, $host, $port);
			$out .= $content;
			$content="";
		} while((ord(substr($out,strlen($out)-1,1))!=85) && ((microtime(true) - $time) <= $timeout));
		$longueur=strlen($out);
		log::add($this->_log,'debug',"Return Out size: ".$longueur);
		if ( isset($out) && $out !== FALSE && ord(substr($out,strlen($out)-1,1))==85) {
			$str ="";
			for($i=0;$i<$longueur;$i++) {	  
			  $caractere=substr($out,$i,1);
			  $out2[$i] = ord($caractere);
			  if ( $out2[$i] >=32 && $out2[$i] <128) $str.=" ".ord($caractere);
			  //log::add($this->_log,'debug',"code ascii [".$i."]=  ".$out2[$i]);
			}
			//log::add($this->_log,'debug',"return : ".$str);
			$result = true;
			for($i=0;$i<11;$i++) {  
				  if ($out2[$i] != $this->_prefix[$i])
						$result = false;
			}
			if ( isset($out) && $out !== FALSE && $out !="" && strlen($out)>23 && $result==true && $JDecode==0) {
				log::add($this->_log,'debug',"return OK :". $JDecode);
				$lengthStr = substr($out,12,4);
				// convert $length to int reverse
				$length = ord($lengthStr[3])+ ord($lengthStr[2])*256 + ord($lengthStr[1])*65536 + ord($lengthStr[0])*65536*256;
				log::add($this->_log,'debug',"length:".$length);
				$result2 = 1;
				$deb=0;
				for($i=16+$length-4;$i<16+$length;$i++) {  
					if ($out2[$i] != $this->_suffixRead[$deb])
							$result2 = 0;
					$deb++;
				}
				if ( (strlen($out) == 16 + $length) && substr($out,16+$length-4,4) && $result2==1 ) {
					// skip bytes 17-20 (unknown?)
					$string = substr ($out,20,$length-12);
					//$string = base64_encode($string);
					//$string = utf8_encode ( $string );
					$length = strlen($string);
					$str = "";
					for($i=0;$i<$length;$i++) {
					  $caractere=substr($string,$i,1);;
					  $str.=$caractere;
					  //log::add($this->_log,'debug',"code ascii [".$i."]= ".ord($caractere)."->".$caractere."<-");
					}
					log::add($this->_log,'debug',"len of decoded returned mess:".strlen($str));
					log::add($this->_log,'debug',"return decoded : ".$str);
					$json_decoded_data = json_decode($string, true);
					if ( ($json_decoded_data != NULL) && ($json_decoded_data != FALSE) ){
						foreach ($json_decoded_data as $key => $value) {
							//log::add($this->_log,'debug',">>> : $key | $value : ".$json_decoded_data[$key]);
						}
						if (isset($json_decoded_data["dps"])) {
							foreach ($json_decoded_data["dps"] as $key => $value) {
								//log::add($this->_log,'debug',">>>>>>>>>>> : $key | $value : ".$json_decoded_data["dps"][$key]);
							}	
							log::add($this->_log,'debug','Read Json OK');
							
							return $json_decoded_data;
						}
						log::add($this->_log,'debug',"Bad response");
					}
					else
						log::add($this->_log,'debug',"Bad JSON");
				}
				else {	
					return BADRESPONSE;
				}								
			}
			else {
				if ($result==true ) {									
					return SUCCESS;
				}
				else
					log::add($this->_log,'debug',"socket_read() failed: reason: " .socket_strerror(socket_last_error()) );	
			}
				
		}
		else {
			log::add($this->_log,'debug',"return empty");	
		}
		return BADRESPONSE;
	}
	public function On() {
		$Id =1;
		$time=time();
		log::add($this->_log,'debug','ON' );
		$this->_returnOK =true;
		$ret = $this->send("",9,2);
		$string = sprintf($this->_commandCodes['CMD_ON'],$time,$this->_devId);
		$ret = $this->send($string,7,1);
		log::add($this->_log,'debug','End ON' );
		return $ret;
	}
	public function Off() {
		$Id =1;
		$time=time();
		log::add($this->_log,'debug','OFF' );
		$this->_returnOK =true;
		$ret = $this->send("",9,2);
		$string = sprintf($this->_commandCodes['CMD_OFF'],$time,$this->_devId);
		$ret = $this->send($string,7,1);
		log::add($this->_log,'debug','End OFF' );
		return $ret;
	}	
	
	public function OnMax() {
		$this->On();
		return $this->OnBrightnessWhite (100);
	}
	public function OnMin() {
		$this->On();
		return $this->OnBrightnessWhite (10);
	}
	public function OnMid() {
		$this->On();
		return $this->OnBrightnessWhite (50);
	}
	
	/*
	public function OnNight() {
		$this->On();
		$Id = 1;	
		$string = sprintf($this->_commandCodes['CMD_BRIGHTNESS'],$Id, 1);
		return $this->send($string,$Id);		
	}	
	*/

	public function OnColor($color='Red',$Bright=0) {
		$this->On();
		switch ($color) {
			case 'Random':	$rgb[0] = (int)mt_rand(0,255);
							$rgb[1] = (int)mt_rand(0,255);
							$rgb[2] = (int)mt_rand(0,255);	
							$color= $this->rgb2hex($rgb);
							break;
			case 'Blue':		$color= '#0000FF' ; break;
			case 'Violet':		$color= '#7F00FF' ; break;
			case 'BabyBlue':	$color= '#00bbff' ; break;
			case 'Aqua':		$color= '#00FFFF' ; break;
			case 'SpringGreen':	$color= '#00FF7F' ; break;
			case 'Mint':		$color= '#00FF43' ; break;
			case 'Green':		$color= '#00FF00' ; break;
			case 'LimeGreen':	$color= '#a1FF00' ; break;
			case 'Yellow':		$color= '#FFFF00' ; break;
			case 'YellowOrange':$color= '#FFD000' ; break;
			case 'Orange':		$color= '#FFA500' ; break;
			case 'Red':			$color= '#FF0000' ; break;
			case 'Pink':		$color= '#FF0061' ; break;
			case 'Fuchsia':		$color= '#FF00FF' ; break;
			case 'Lilac':		$color= '#D000FF' ; break;
			case 'Lavendar':	$color= '#6100FF' ; break;
			default:
			if(substr($color,0,1)!= "#"){
				$color= '#FF0000' ;
			}
		}
		$this->_color= $color;
		$colorhex = str_replace( "#", "",$color);  
		$r = (int)hexdec(substr($color,1,2));
		$g = (int)hexdec(substr($color,3,2));
		$b = (int)hexdec(substr($color,5,2));
		 
		$hsv = $this->RGB2HSV ($r, $g, $b);
		log::add($this->_log,'debug','HSV:'.$hsv['H']." ".$hsv['S']." ".$hsv['V']);
		$hsv['H'] = $hsv['H']*255/360;
		$hsv['S'] = $hsv['S']*255/100;
		$hsv['V'] = $hsv['V']*255/100;
		$hsvhex = $this->hsv2hex($hsv);
		
		$hsvhex = str_replace( "#", "",$hsvhex);	
		$hexvalue = $colorhex ."00".$hsvhex;
		//log::add($this->_log,'debug','hexvalue:'.$hexvalue);
		$time=time();
		$string = sprintf($this->_commandCodes['CMD_COLOR'],$time,$this->_devId,$hexvalue);
		return $this->send($string,7);	
	}
	public function OnBrightness($Intensity,$Color='#000000',$White=0) {
		//log::add($this->_log,'debug','inyternsi');
		//log::add($this->_log,'debug','Col before intensity:'.$Color);
		$Col=$this->hex2rgb($Color);
		//log::add($this->_log,'debug','Col hex :'.$Col);
		$Col[0]= round($Intensity*$Col[0]/100);
		$Col[1]= round($Intensity*$Col[1]/100);
		$Col[2]= round($Intensity*$Col[2]/100);	
		$hex = $this->rgb2hex($Col);
		//log::add($this->_log,'debug','Col after intensity :'.$hex);
		$Bright =0;
		return $this->OnColor($hex,$Bright,$White,0);
	
	}
	public function OnBrightnessWhite($value=0x0e,$Col) {
		$Id =1;
		$value =$value * 255 / 100;
		$time=time();
		//log::add($this->_log,'debug','BRIGHT' );
		$string = sprintf($this->_commandCodes['CMD_BRIGHTNESS'],$time,$this->_devId,$value);
		return $this->send($string,7);
	}

	/*
	public function OnBrightnessWhite($value=0x0e,$Col) {
		$this->On();
		$Id = 1;
		if ($value<1) $value=1;
		if ($value>100) $value=100;
		$string = sprintf($this->_commandCodes['CMD_BRIGHTNESS'],$Id, $value);
		return $this->send($string,$Id);
	}
	public function BrightnessIncreaseInt($value=50) {
		$this->On();
		$Id = 1;
		$string = sprintf($this->_commandCodes['CMD_BRIGHT_UP'],$Id);
		$this->send($string,$Id);
		$OutStr = $this->getStatus();
		if( isset($OutStr["result"][1])==true && $OutStr["result"][1]!="") {
				$Br=$OutStr["result"][1];
				return $Br;
		}
		return false;
	}
	public function BrightnessDecreaseInt($value=50) {
		$this->On();
		$Id = 1;
		$string = sprintf($this->_commandCodes['CMD_BRIGHT_DOWN'],$Id);
		$this->send($string,$Id);
		$OutStr = $this->getStatus();
		if( isset($OutStr["result"][1])==true && $OutStr["result"][1]!="") {
				$Br=$OutStr["result"][1];
				return $Br;
		}
		return false;
	} 
    public function BrightnessIncrease($value=50,$Col="#000000") {
		$value=$value+$this->_increm;
		if ($value<0) $value=0;
		if ($value>100) $value=100;
		$this-> OnBrightness($value,$Col);
		return $value;
	}
	public function BrightnessDecrease($value=50,$Col="#000000") {
		$value=$value-$this->_increm;
		if ($value<0) $value=0;
		if ($value>100) $value=100;
		$this-> OnBrightness($value,$Col);
		return $value;
	}
    */

	public function OnWarm() {
		return $this->OnKelvin(5);
	}
	public function OnCool() {
		return $this->OnKelvin(100);
	}
	public function OnLukeWarm() {
		return $this->OnKelvin(50);
	}

	/*
	public function KelvinIncrease($value=50) {
        
		// $OutStr = $this->getStatus();
		// if( isset($OutStr["result"][2])==true && $OutStr["result"][2]!="") {
			// $Ct=$OutStr["result"][2];
			// $this->On();
			// $Ct = round($Ct*(6500-1700)/100 +1700);				
			// $this->OnKelvin($Ct);
		// }
		// else
        
		$this->On();
		$Id = 1;	
		$string = sprintf($this->_commandCodes['CMD_TEMP_UP'],$Id);
		$this->send($string,$Id);
		$OutStr = $this->getStatus();
		if( isset($OutStr["result"][2])==true && $OutStr["result"][2]!="") {
			$Ct=$OutStr["result"][2];
			$Ct = round(($Ct-1700) *100/(6500-1700));
			return $Ct;
		}
		return false;
	}
	public function KelvinDecrease($value=50) {
	    	
        // $OutStr = $this->getStatus();
		// if( isset($OutStr["result"][2])==true && $OutStr["result"][2]!="") {
			// $Ct=$OutStr["result"][2];
			// $this->On();
			// $Ct = round($Ct*(6500-1700)/100 +1700);
			// $this->OnKelvin($Ct);
		// }
		// else
		
        $this->On();
		$Id = 1;
		$string = sprintf($this->_commandCodes['CMD_TEMP_DOWN'],$Id);
		$this->send($string,$Id);
		$OutStr = $this->getStatus();
		if( isset($OutStr["result"][2])==true && $OutStr["result"][2]!="") {
			$Ct=$OutStr["result"][2];
			$Ct = round(($Ct-1700) *100/(6500-1700));
			return $Ct;
		}	
		return false;
	}
*/
	public function OnKelvin($value=50) {
		$value=$value*255/100;
		$Id =1;
		$time=time();
		//log::add($this->_log,'debug','CT' );
		$string = sprintf($this->_commandCodes['CMD_CT'],$time,$this->_devId,$value);
		return $this->send($string,7);
	}
	public function hex2rgb($hex) {
	   $hex = str_replace("#", "", $hex);
	   if(strlen($hex) == 3) {
		  $r = hexdec(substr($hex,0,1).substr($hex,0,1));
		  $g = hexdec(substr($hex,1,1).substr($hex,1,1));
		  $b = hexdec(substr($hex,2,1).substr($hex,2,1));
	   } else {
		  $r = hexdec(substr($hex,0,2));
		  $g = hexdec(substr($hex,2,2));
		  $b = hexdec(substr($hex,4,2));
	   }
	   $rgb = array($r, $g, $b);
	   return $rgb; // returns an array with the rgb values
	}	
	public function rgb2hex($rgb) {
	   $hex = "#";
	   $hex .= str_pad(dechex($rgb[0]), 2, "0", STR_PAD_LEFT);
	   $hex .= str_pad(dechex($rgb[1]), 2, "0", STR_PAD_LEFT);
	   $hex .= str_pad(dechex($rgb[2]), 2, "0", STR_PAD_LEFT);
	   return $hex; // returns the hex value including the number sign (#)
	}
	public function hsv2hex($hsv) {
	   $hex = "#";
	   $hex .= str_pad(dechex($hsv["H"]), 2, "0", STR_PAD_LEFT);
	   $hex .= str_pad(dechex($hsv["S"]), 2, "0", STR_PAD_LEFT);
	   $hex .= str_pad(dechex($hsv["V"]), 2, "0", STR_PAD_LEFT);
	   return $hex; // returns the hex value including the number sign (#)
	}
	public function addpadding($string)
	{
		 $blocksize = 16;
		 $len = strlen($string);
		 $pad = $blocksize - ($len % $blocksize);
		 //log::add($this->_log,'debug',"leng :". $len);
		 $string .= str_repeat(chr($pad), $pad);
		 return $string;
	}
	public function DeCrypt($cmd){
					//$cmd=array(0x00,0x00,0x55,0xaa,0x00,0x00,0x00,0x00,0x00,0x00,0x00,0x07,0x00,0x00,0x00,0x9b,0x33,0x2e,0x31,0x38,0x30,0x64,0x33,0x34,0x33,0x33,0x36,0x65,0x64,0x63,0x35,0x31,0x33,0x64,0x37,0x4f,0x2b,0x45,0x71,0x4d,0x51,0x47,0x65,0x52,0x38,0x4c,0x47,0x4a,0x54,0x78,0x36,0x44,0x37,0x50,0x55,0x31,0x53,0x53,0x31,0x38,0x2f,0x56,0x63,0x74,0x66,0x34,0x52,0x36,0x34,0x67,0x66,0x52,0x45,0x67,0x45,0x4a,0x68,0x4a,0x77,0x78,0x71,0x64,0x47,0x65,0x77,0x57,0x6b,0x53,0x4b,0x6e,0x33,0x38,0x34,0x56,0x67,0x55,0x2b,0x69,0x77,0x5a,0x4b,0x62,0x50,0x76,0x30,0x45,0x52,0x41,0x53,0x69,0x6b,0x50,0x56,0x52,0x66,0x75,0x73,0x54,0x4a,0x31,0x50,0x66,0x33,0x35,0x55,0x52,0x33,0x46,0x68,0x61,0x41,0x48,0x38,0x77,0x6e,0x36,0x4b,0x7a,0x65,0x66,0x6b,0x47,0x76,0x6f,0x64,0x49,0x4c,0x61,0x73,0x77,0x62,0x67,0x30,0x42,0x36,0x45,0x63,0x4c,0x79,0x70,0x4e,0x66,0x6a,0xfc,0x8c,0x91,0x1c,0x00,0x00,0xaa,0x55);
					//$cmd = array(0x00, 0x00, 0x55, 0xaa, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x07, 0x00, 0x00, 0x00, 0x9b, 0x33, 0x2e, 0x31, 0x38, 0x66, 0x39, 0x32, 0x31, 0x66, 0x65, 0x31, 0x62, 0x62, 0x30, 0x38, 0x30, 0x37, 0x62, 0x39, 0x31, 0x50, 0x72, 0x52, 0x38, 0x47, 0x63, 0x69, 0x57, 0x61, 0x31, 0x34, 0x47, 0x6c, 0x32, 0x70, 0x39, 0x6b, 0x2b, 0x54, 0x2b, 0x39, 0x74, 0x44, 0x74, 0x6e, 0x50, 0x58, 0x57, 0x37, 0x37, 0x31, 0x75, 0x4f, 0x73, 0x55, 0x75, 0x73, 0x7a, 0x6f, 0x68, 0x41, 0x64, 0x36, 0x6a, 0x43, 0x42, 0x74, 0x4d, 0x74, 0x4e, 0x44, 0x70, 0x55, 0x67, 0x65, 0x64, 0x4e, 0x67, 0x44, 0x69, 0x48, 0x48, 0x54, 0x62, 0x77, 0x74, 0x38, 0x57, 0x6d, 0x72, 0x56, 0x54, 0x62, 0x56, 0x51, 0x72, 0x58, 0x6d, 0x32, 0x72, 0x53, 0x4c, 0x61, 0x45, 0x4e, 0x58, 0x66, 0x6e, 0x2b, 0x31, 0x37, 0x74, 0x47, 0x76, 0x78, 0x4b, 0x78, 0x4a, 0x36, 0x31, 0x42, 0x71, 0x2f, 0x58, 0x6f, 0x42, 0x31, 0x78, 0x58, 0x32, 0x4e, 0x69, 0x72, 0x61, 0x4d, 0x35, 0x52, 0x4c, 0x48, 0x36, 0x7a, 0x52, 0x78, 0x6f, 0x51, 0x46, 0x47, 0xea, 0x7b, 0x30, 0xa9, 0x00, 0x00, 0xaa, 0x55);
					// prefix : 11 caracteres		
              		// 1 : 1 caracteres
                    // size : 4 caracteres
                    // $encode
                    // suffix : 8 caracteres
                    // $encode
                    //    $this->_version : 3 car 17_19
                    //    $md5s : 16 car
                    //    $string to decode
                    // car 11+1+4+3+16=35 à len-8
              		$subcommand= array_slice($cmd,35,-8);
              		$stringCom = vsprintf(str_repeat('%c', count($subcommand)), $subcommand);
					for($i=0;$i<strlen($stringCom);$i++) {
					  $caractere=substr($stringCom,$i,1);
					  if (ord($caractere)>31 && ord($caractere)<128) $str.=$caractere;
					  log::add($this->_log,'debug',"code ascii [".$i."]= ".dechex(ord($caractere)));
					}
					log::add($this->_log,'debug',"Len= ".strlen($stringCom));										
					$stringCom = base64_decode($stringCom);
					log::add($this->_log,'debug',"Len= ".strlen($stringCom));
					$key = $this->_LocalKey;
					//$this->_LocalKey="";
					$stringCom = openssl_decrypt ($stringCom ,'AES-128-ECB' ,$this->_LocalKey ,OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING);
					$this->_LocalKey = $key;
					log::add($this->_log,'debug',"Decoded:");
					$str="";
					for($i=0;$i<strlen($stringCom);$i++) {
					  $caractere=substr($stringCom,$i,1);
					  if (ord($caractere)>31 && ord($caractere)<128) $str.=$caractere;
					  log::add($this->_log,'debug',"code ascii [".$i."]= ".ord($caractere));
					}
					log::add($this->_log,'debug',"rec:".$str);
					return $stringCom;
	}
	public function Crypt($string,$command,$Crypt=0) {
		$string=$this->addpadding($string);
		if ($Crypt==1) {
			if  (function_exists('openssl_encrypt')===true)          {
				$string = openssl_encrypt ( $string ,'AES-128-ECB' ,$this->_LocalKey ,OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING);
			}
			elseif (function_exists('mcrypt_encrypt')===true) 
				$string = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $this->_LocalKey, $string, MCRYPT_MODE_ECB,'');
			else {
				log::add($this->_log,'debug',"Neither mcrypt_encrypt() nor openssl_encrypt() istalled on this server");
				return 0;
			}
			$longueur=strlen($string);
			for($i=0;$i<$longueur;$i++) {
			  $caractere=substr($string,$i,1);
			  //log::add($this->_log,'debug',"code ascii [".$i."]=  ".ord($caractere));
			}
			$string = base64_encode($string);
			$payload= "data=".$string."||lpv=".$this->_version."||";
			$longueur=strlen($payload);
			for($i=0;$i<$longueur;$i++) {
			  $caractere=substr($payload,$i,1);
			  //log::add($this->_log,'debug',"code ascii [".$i."]= ".ord($caractere));
			}
			$md5 = md5($payload.$this->_LocalKey);
			//log::add($this->_log,'debug',"MD5: ". $md5);
			$md5s = substr($md5,8,16);
			$encode = $this->_version.$md5s.$string;
		}
		else
			$encode = $string;
		//log::add($this->_log,'debug',"Encode: ". $encode);
		$length = strlen($encode)+sizeof($this->_suffixWrite);
		$byte0 = floor(($length/(65536*256)));
		$byte1 = floor(($length%(65536*256))/65536);
		$byte2 = floor(($length%65536)/256);
		$byte3 = $length%256;
		$cmd = implode("",array_map("chr", $this->_prefix)).chr($command).chr($byte0).chr($byte1).chr($byte2).chr($byte3).$encode.implode("",array_map("chr", $this->_suffixWrite));
		$longueur = strlen($cmd);
		//log::add($this->_log,'debug',"Cmd: ");
		for($i=0;$i<$longueur;$i++) {
		  $caractere=substr($cmd,$i,1);
		  //log::add($this->_log,'debug',"code ascii [".$i."]= ".ord($caractere));
		}
		return $cmd;
	}
	public function CryptS($string,$command,$Crypt=0) {
		log::add($this->_log,'debug',"CryptS");
		//log::add($this->_log,'debug',"Encode: ". $encode);
		$length = 8;
		$byte0 = floor(($length/(65536*256)));
		$byte1 = floor(($length%(65536*256))/65536);
		$byte2 = floor(($length%65536)/256);
		$byte3 = $length%256;
		$cmd = implode("",array_map("chr", $this->_prefix)).chr($command).chr($byte0).chr($byte1).chr($byte2).chr($byte3).implode("",array_map("chr", $this->_suffixWrite));
		$longueur = strlen($cmd);
		//log::add($this->_log,'debug',"Cmd: ");
		for($i=0;$i<$longueur;$i++) {
		  $caractere=substr($cmd,$i,1);
		  //log::add($this->_log,'debug',"code ascii [".$i."]= ".ord($caractere));
		}
		return $cmd;
	}
	public function RGB2HSV ($R, $G, $B) {  
	// RGB Values:Number 0-255
    // HSV Results:Number 0-100/0-360
	   $HSL = array();

	   $var_R = ($R / 255);
	   $var_G = ($G / 255);
	   $var_B = ($B / 255);

	   $var_Min = min($var_R, $var_G, $var_B);
	   $var_Max = max($var_R, $var_G, $var_B);
	   $del_Max = $var_Max - $var_Min;

	   $V = $var_Max;

	   if ($del_Max == 0)
	   {
		  $H = 0;
		  $S = 0;
	   }
	   else
	   {
		  $S = $del_Max / $var_Max;

		  $del_R = ( ( ( $var_Max - $var_R ) / 6 ) + ( $del_Max / 2 ) ) / $del_Max;
		  $del_G = ( ( ( $var_Max - $var_G ) / 6 ) + ( $del_Max / 2 ) ) / $del_Max;
		  $del_B = ( ( ( $var_Max - $var_B ) / 6 ) + ( $del_Max / 2 ) ) / $del_Max;

		  if      ($var_R == $var_Max) $H = $del_B - $del_G;
		  else if ($var_G == $var_Max) $H = ( 1 / 3 ) + $del_R - $del_B;
		  else if ($var_B == $var_Max) $H = ( 2 / 3 ) + $del_G - $del_R;

		  if ($H<0) $H++;
		  if ($H>1) $H--;
	   }
	   $H=round($H*360);
	   $S=round($S*100);
	   $V=round($V*100);
	   $HSL['H'] = $H;
	   $HSL['S'] = $S;
	   $HSL['V'] = $V;
	   return $HSL;
	}	
}
class W2_Tuya_SW_C extends W2_TuyaBase
{	public function retStatus($OutStr=0) {
		//log::add($this->_log,'debug',"RetStatus Start");
		if ($OutStr == 0) $OutStr = $this->getStatus();
		//log::add($this->_log,'debug',"End status");
		if ( isset($OutStr) && $OutStr!=NOSOCKET && $OutStr!=NOTCONNECTED && $OutStr!=NORESPONSE && $OutStr!=BADRESPONSE) {		
		//log::add($this->_log,'debug',"set");
			if ($OutStr["devId"]!="") {
				log::add($this->_log,'debug',"Not null");
				// for tuya light
				// 1 : (1 or NULL) ON/Off
				// 2 : delay
				// 4 : Current mA
				// 5 : Power	W
				// 6 : Voltage  V 
				if( isset($OutStr["dps"][1])==true ) {		
					if ($OutStr["dps"][1] == 1){
						$this->_return['On'] = 1;
						log::add($this->_log,'debug','ON');
					} 
					else {
						$this->_return['On'] = 0;
						log::add($this->_log,'debug','OFF');
					}
				}	
				if( isset($OutStr["dps"][2])==true ) {		
					$this->_return['Delay'] = $OutStr["dps"][2];
					log::add($this->_log,'debug','Delay:'.$OutStr["dps"][2]);
				}	
				if( isset($OutStr["dps"][4])==true ) {		
					$this->_return['Current'] = $OutStr["dps"][4];
					log::add($this->_log,'debug','Current mA:'.$OutStr["dps"][4]);	
				}
				if( isset($OutStr["dps"][5])==true ) {		
						$this->_return['Power'] = $OutStr["dps"][5]/10;
						log::add($this->_log,'debug','Power W:'.$OutStr["dps"][5]/10);	
				}
				if( isset($OutStr["dps"][6])==true ) {		
						$this->_return['Voltage'] = $OutStr["dps"][6]/10;
						log::add($this->_log,'debug','Voltage V:'.$OutStr["dps"][6]/10);
				}
			}
			else
				$this->_return['Type'] = 'Not a Tuya SW';
		}
		else if ( !isset($OutStr) )
			$this->_return = BADRESPONSE;
		else
			$this->_return = $OutStr;
		return $this->_return;	
	}

	public function On($delay=0) {
		$Id =1;
		$time=time();
		//log::add($this->_log,'debug','ON' );
		$string = sprintf($this->_commandCodes['CMD_ON_SW'],$time,$this->_devId,1);
		return $this->send($string,7);
	}
	public function Off($delay=0) {
		$Id =1;
		$time=time();
		//log::add($this->_log,'debug','OFF' );
		$string = sprintf($this->_commandCodes['CMD_OFF_SW'],$time,$this->_devId,1);
		return $this->send($string,7);
	}	
}
class W2_Tuya_SW extends W2_TuyaBase
{	public function retStatus($OutStr=0) {
		//log::add($this->_log,'debug',"RetStatus Start");
		return $this->_return;
		if ($OutStr == 0) $OutStr = $this->getStatus();
		//log::add($this->_log,'debug',"End status");
		if ( isset($OutStr) && $OutStr!=NOSOCKET && $OutStr!=NOTCONNECTED && $OutStr!=NORESPONSE && $OutStr!=BADRESPONSE) {		
		//log::add($this->_log,'debug',"set");
			if ($OutStr["devId"]!="") {
				//log::add($this->_log,'debug',"Not null");
				// for tuya light
				// 1 : (1 or NULL) ON/Off
				// 2 : delay
				if( isset($OutStr["dps"][1])==true ) {		
					if ($OutStr["dps"][1] == 1){
						$this->_return['On'] = 1;
						log::add($this->_log,'debug','ON');
					} 
					else {
						$this->_return['On'] = 0;
						log::add($this->_log,'debug','OFF');
					}
				}	
				if( isset($OutStr["dps"][2])==true ) {		
					$this->_return['Delay'] = $OutStr["dps"][2];
					log::add($this->_log,'debug','Delay:'.$OutStr["dps"][2]);
				}	
			}
			else
				$this->_return['Type'] = 'Not a Tuya SW';
		}
		else if ( !isset($OutStr) )
			$this->_return = BADRESPONSE;
		else
			$this->_return = $OutStr;
		return $this->_return;	
	}

	public function On($delay=0) {
		$Id =1;
		$time=time();
		//log::add($this->_log,'debug','ON' );
		$string = sprintf($this->_commandCodes['CMD_ON_SW_D'],$time,$this->_devId,1,2,$delay);
		return $this->send($string,7);
	}
	public function Off($delay=0) {
		$Id =1;
		$time=time();
		//log::add($this->_log,'debug','OFF' );
		$string = sprintf($this->_commandCodes['CMD_OFF_SW_D'],$time,$this->_devId,1,2,$delay);
		return $this->send($string,7);
	}	
}
class W2_Tuya_SW_5 extends W2_TuyaBase
{	protected $_ActiveGroup; // 1 to 5 : 4SW +USB
	public function SetGroup($group=1) {
		$this->_ActiveGroup = $group;
	}
	public function GetGroup($group=0) {
		return $this->_ActiveGroup;
	}
	public function retStatus($OutStr=0) {
		return $this->_return;
		//log::add($this->_log,'debug',"RetStatus Start");
		if ($OutStr == 0) $OutStr = $this->getStatus();
		//log::add($this->_log,'debug',"End status");
		if ( isset($OutStr) && $OutStr!=NOSOCKET && $OutStr!=NOTCONNECTED && $OutStr!=NORESPONSE && $OutStr!=BADRESPONSE) {		
		//log::add($this->_log,'debug',"set");
			if ($OutStr["devId"]!="") {
				log::add($this->_log,'debug',"Not null");
				// for tuya light
				// $group : (1 or NULL) ON/Off
				$group = $this->_ActiveGroup;
				if( isset($OutStr["dps"][$group])==true ) {		
					if ($OutStr["dps"][$group] == 1){
						$this->_return['On'] = 1;
						log::add($this->_log,'debug','ON');
					} 
					else {
						$this->_return['On'] = 0;
						log::add($this->_log,'debug','OFF');
					}
				}	

			}
			else
				$this->_return['Type'] = 'Not a Tuya SW';
		}
		else if ( !isset($OutStr) )
			$this->_return = BADRESPONSE;
		else
			$this->_return = $OutStr;
		return $this->_return;	
	}

	public function On($delay=0) {
		$Id =1;
		$time=time();
		$group = $this->_ActiveGroup;
		//log::add($this->_log,'debug','ON' );
		$string = sprintf($this->_commandCodes['CMD_ON_SW'],$time,$this->_devId,$group);
		return $this->send($string,7);
	}
	public function Off($delay=0) {
		$Id =1;
		$time=time();
		$group = $this->_ActiveGroup;
		//log::add($this->_log,'debug','OFF' );
		$string = sprintf($this->_commandCodes['CMD_OFF_SW'],$time,$this->_devId,$group);
		return $this->send($string,7);
	}	
}

class W2_Tuya_SW_2 extends  W2_Tuya_SW_5
{// 1SW +USB Or 2SW pas de conso

	public function On($delay=0) {
		$Id =1;
		$time=time();
		$group = $this->_ActiveGroup;
		//log::add($this->_log,'debug','ON' );
		$string = sprintf($this->_commandCodes['CMD_ON_SW'],$time,$this->_devId,$group);
		return $this->send($string,7);
	}
	public function Off($delay=0) {
		$Id =1;
		$time=time();
		$group = $this->_ActiveGroup;
		//log::add($this->_log,'debug','OFF' );
		$string = sprintf($this->_commandCodes['CMD_OFF_SW'],$time,$this->_devId,$group);
		return $this->send($string,7);
	}	
}

class W2_Tuya_SW_2C extends  W2_Tuya_SW_2
{	// 1SW+USB
	public function retStatus($OutStr=0) {
		//log::add($this->_log,'debug',"RetStatus Start");
		if ($OutStr == 0) $OutStr = $this->getStatus();
		//log::add($this->_log,'debug',"End status");
		if ( isset($OutStr) && $OutStr!=NOSOCKET && $OutStr!=NOTCONNECTED && $OutStr!=NORESPONSE && $OutStr!=BADRESPONSE) {		
		//log::add($this->_log,'debug',"set");
			if ($OutStr["devId"]!="") {
				log::add($this->_log,'debug',"Not null");
				// for tuya light
				// 1 : (1 or NULL) ON/Off
				// 2 : (1 or NULL) ON/Off
				// 7 : current mA
				// 8 : power 
				// 9 : voltage 
				$group = $this->_ActiveGroup;
				if( isset($OutStr["dps"][$group])==true ) {		
					if ($OutStr["dps"][$group] == 1){
						$this->_return['On'] = 1;
						log::add($this->_log,'debug','ON');
					} 
					else {
						$this->_return['On'] = 0;
						log::add($this->_log,'debug','OFF');
					}
				}	
				if( isset($OutStr["dps"][7])==true ) {		
					$this->_return['Current'] = $OutStr["dps"][7];
					log::add($this->_log,'debug','Current:'.$OutStr["dps"][7]);	
				}
				if( isset($OutStr["dps"][8])==true ) {		
						$this->_return['Power'] = $OutStr["dps"][8]/10;
						log::add($this->_log,'debug','Power:'.$OutStr["dps"][8]/10);	
				}
				if( isset($OutStr["dps"][9])==true ) {		
						$this->_return['Voltage'] = $OutStr["dps"][9]/10;
						log::add($this->_log,'debug','Voltage:'.$OutStr["dps"][9]/10);
				}
			}
			else
				$this->_return['Type'] = 'Not a Tuya SW';
		}
		else if ( !isset($OutStr) )
			$this->_return = BADRESPONSE;
		else
			$this->_return = $OutStr;
		return $this->_return;	
	}	
}


class W2_Tuya_SW_3 extends  W2_Tuya_SW_5
{	// 1SW +USB Or 2SW

	public function On($delay=0) {
		$Id =1;
		$time=time();
		$group = $this->_ActiveGroup;
		//log::add($this->_log,'debug','ON' );
		$string = sprintf($this->_commandCodes['CMD_ON_SW'],$time,$this->_devId,$group);
		return $this->send($string,7);
	}
	public function Off($delay=0) {
		$Id =1;
		$time=time();
		$group = $this->_ActiveGroup;
		//log::add($this->_log,'debug','OFF' );
		$string = sprintf($this->_commandCodes['CMD_OFF_SW'],$time,$this->_devId,$group);
		return $this->send($string,7);
	}	
}
class W2_Tuya_SW_4 extends  W2_Tuya_SW_5
{	// 3SW +USB Or 4SW

	public function On($delay=0) {
		$Id =1;
		$time=time();
		$group = $this->_ActiveGroup;
		//log::add($this->_log,'debug','ON' );
		$string = sprintf($this->_commandCodes['CMD_ON_SW'],$time,$this->_devId,$group);
		return $this->send($string,7);
	}
	public function Off($delay=0) {
		$Id =1;
		$time=time();
		$group = $this->_ActiveGroup;
		//log::add($this->_log,'debug','OFF' );
		$string = sprintf($this->_commandCodes['CMD_OFF_SW'],$time,$this->_devId,$group);
		return $this->send($string,7);
	}	
}
/*
class W2_TuyaWhite extends W2_TuyaBase
{
	public function retStatus($OutStr = 0) {	
		if ($OutStr == 0) $OutStr = $this->getStatus();
		if ( isset($OutStr) && $OutStr!=NOSOCKET && $OutStr!=NOTCONNECTED && $OutStr!=NORESPONSE && $OutStr!=BADRESPONSE) {	
			if ($OutStr["id"]!="") {				
				if( isset($OutStr['result'][1])==true && $OutStr['result'][1]!="") {
					$this->_return['White'] = $OutStr['result'][1];
				}
				if( isset($OutStr['result'][0])==true && $OutStr['result'][0]!="") {		
					if ($OutStr['result'][0] =="off"){
						$this->_return['On']= 0;
					} 
					else if ($OutStr['result'][0] =="on"){
						$this->_return['On'] = 1;
					}
				}			
			}
			else
				$this->_return['Type'] = 'Not a White Tuya bulb';
		}
		else if ( !isset($OutStr) )
			$this->_return = BADRESPONSE;
		else
			$this->_return = $OutStr;
		return $this->_return;	
	}	
	public function OnWhite() {
		$this->On();
		$Id = 1;
		$string = sprintf($this->_commandCodes['CMD_WHITE_W'],$Id);
		return $this->send($string,$Id);	
	}
}

class W2_TuyaRGBW extends W2_TuyaBase
{
	public function OnWhite() {
		$this->On();
		$Id = 1;
		$string = sprintf($this->_commandCodes['CMD_WHITE_RGBW'],$Id);
		return $this->send($string,$Id);	
	}
}
class W2_TuyaStrip extends W2_TuyaBase
{	
	public function OnWhite() {
		$this->On();	
		$Id = 1;
		$string = sprintf($this->_commandCodes['CMD_WHITE_RGBW'],$Id);	
		return $this->send($string,$Id);		
	}
}
*/
?>