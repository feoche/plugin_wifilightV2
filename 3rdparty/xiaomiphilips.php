<?php
require_once dirname(__FILE__) . '/include/common.php';
class W2_XiaomiPhilipsBase
{
	//Yeelight Philips Bulbs commands
	protected $_host;
	protected $_port;
	protected $_wait;
	protected $_repeat;
	protected $_repeatOK;
	protected $_color = "#000000";
	protected $_increm;
	protected $_returnOK = true;
	protected $_delay = 101000; //microseconds
	protected $_socket = false;
	protected $_Id = 0;
	protected $_magic ;
	protected $_unknown;
	protected $_device ;
	protected $_ts ;
  	protected $_tsDif ;
	protected $_checksum ;
	protected $_token;
	protected $_return ;
	protected $_log;
	
	protected $_commandCodes = array(
	
	'CMD_TOGGLE' => '{"id":%d,"method":"toggle","params":[]}', //
	'CMD_ON' => '{"id":%d,"method":"set_power","params":["on"]}',
	'CMD_OFF' => '{"id":%d,"method":"set_power","params":["off"]}',
    'CMD_CT' => '{"id":%d,"method":"set_cct","params":[%d]}',

	
	'CMD_BRIGHT_UP' => '{"id":1,"method":"set_adjust","params":["increase", "bright"]}',
	'CMD_BRIGHT_DOWN' => '{"id":1,"method":"set_adjust","params":["decrease", "bright"]}',
	'CMD_TEMP_UP' => '{"id":1,"method":"set_adjust","params":["increase", "cct"]}',
	'CMD_TEMP_DOWN' => '{"id":1,"method":"set_adjust","params":["decrease", "cct"]}',

    'CMD_HSV' => '{"id":%d,"method":"set_hsv","params":[%d, %d]}',
	'CMD_RGB'=>'{"id":%d,"method":"set_rgb","params":[%d]}',
    'CMD_BRIGHTNESS' => '{"id":%d,"method":"set_bright","params":[%d]}',
      
	'CMD_GET_PROP_WHITE' => '{"id":%d,"method":"get_prop","params":["power","bright","cct","snm","dv"]}',
	'CMD_GET_PROP_CEILING' => '{"id":%d,"method":"get_prop","params":["power","bright","cct","snm","dv","bl","ac"]}',
    'CMD_GET_PROP_EYE' => '{"id":%d,"method":"get_prop","params":["power","bright","notifystatus","ambstatus","ambvalue","eyecare","scene_num","bls","dvalue"]}',

    'CMD_MOON' => '{"id":%d,"method":"enable_bl","params":["%s"]}',
	
 
 
	'CMD_ON_BG' => '{"id":%d,"method":"bg_set_power","params":["on"]}',
	'CMD_OFF_BG' => '{"id":%d,"method":"bg_set_power","params":["off"]}',
	'CMD_RGB_BG' => '{"id":%d,"method":"bg_set_rgb","params":[%d]}',
	'CMD_BRIGHTNESS_BG' => '{"id":%d,"method":"bg_set_bright","params":[%d]}',
	
	'CMD_BRIGHT_UP_BG' => '{"id":1,"method":"bg_set_adjust","params":["increase", "bright"]}',
	'CMD_BRIGHT_DOWN_BG' => '{"id":1,"method":"bg_set_adjust","params":["decrease", "bright"]}',
      
    'CMD_ENABLE_AC' => '{"id":%d,"method":"enable_ac","params":[%d]}',
	
    'CMD_ENABLE_EYE_CARE' => '{"id":%d,"method":"set_eyecare","params":["%s"]}',
	'CMD_EYE_CARE_NOT' => '{"id":%d,"method":"set_notifyuser","params":["%s"]}',
	
    'CMD_SCENE' => '{"id":%d,"method":"apply_fixed_scene","params":[%d]}',
    'CMD_SCENE_EYE_CARE' => '{"id":%d,"method":"set_user_scene","params":[%d]}',
    'CMD_BRIGHTNESS_AMB' => '{"id":%d,"method":"set_amb_bright","params":[%d]}',
    'CMD_ENABLE_AMB' => '{"id":%d,"method":"enable_amb","params":["%s"]}',
	'CMD_DELAY_OFF' => '{"id":%d,"method":"delay_off","params":[%d]}',

	);
	//Used to dynamically call class methods
	public function call(){
		echo __METHOD__;
	}	
	public function __construct($host = '192.168.1.110', $wait=0, $repeat=1, $increm=10, $ID = 0, $LocalId="", $nbLeds=0, $colorOrder=0, $port =54321) {
		$this->_host = $host;
		$this->_port = $port;
		$this->_token = $ID;
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
		$this->_increm = $increm;
		$myRet = new wifilightV2c;
		$this->_return = $myRet->_return_WFL;
		$this->_return['Type'] = true;
		$this->_log = $myRet->_log;	
	}
	protected function getStatus($string) {
		
		$this->getHello();
		//usleep(1000000);
		//$this->_tsDif = hexdec($this->_ts) - time();
		$this->_Id ++;		
		$string = sprintf($string,$this->_Id);
		$str = $this->send($string,$this->_Id);
		return $str;	
	}
	public function GetColor() {
		return $this->_color;
	}
	/*public function Create(&$socket,$IPaddr) {
		$socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
		//socket_set_option($socket,SOL_SOCKET,SO_RCVTIMEO,array("sec"=>0,"usec"=>20000));
		return socket_connect($socket,$IPaddr,54321);	
	}
	*/
	public function Decode($buf) {
		//log::add($this->_log,'debug',"buf :".$buf);
		return FALSE;
	}
	public function getHello(){
		log::add($this->_log,'debug','Get hello');
		$Hello = '21310020ffffffffffffffffffffffffffffffffffffffffffffffffffffffff';
		if ($this->_socket === false) {
			$this->_socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
			socket_set_option($this->_socket,SOL_SOCKET,SO_RCVTIMEO,array("sec"=>0,"usec"=>20000));
		}
		if ($this->_socket !== false) {
			
			$message = hex2bin($Hello);
			$Icpt=0;
			log::add($this->_log,'debug','ip:'.$this->_host.' port:'.$this->_port);
			do {
                $ServerTime= time();
				socket_sendto($this->_socket, $message, strlen($message), 0, $this->_host, $this->_port);		
				$Icpt++;
				$Icpt2=0;
				do {
					$buf='';
					$bytes = socket_recvfrom($this->_socket, $buf, 4096,0, $this->_host, $this->_port);
					$Icpt2++;
					$continue = true;
					if (isset($buf[0])) {
						if (dechex(ord($buf[0]))!=21) 
							$continue = true;
						else
							$continue = false;
					}	
				}while ($continue == true && ($Icpt2<5));
				$continue = true;
				if (isset($buf[0])) {
					if (dechex(ord($buf[0]))!=21 )
						$continue = true;
					else
						$continue = false;
				}
			} while ($continue == true && ($Icpt<4));
			//log::add($this->_log,'debug',"byte=".$bytes);
			if ($bytes >0) {
				if (dechex(ord($buf[0]))==21) {
					$msg = bin2hex($buf);
					//log::add($this->_log,'debug','Msg :'.$msg);					
					$this->_magic = substr($msg, 0, 4);
					$this->_length = substr($msg, 4, 4);
					$this->_unknown = substr($msg, 8, 8);
					$this->_device = substr($msg, 16, 8);
					$this->_ts = substr($msg, 24, 8);
					$this->_checksum = substr($msg, 32, 32);
					//$this->_ts - $ServerTime;
					$this->_tsDif = hexdec($this->_ts) - time();
				}
				else {
					log::add($this->_log,'debug','Error - Bad response');
				}
			}
			else {
					log::add($this->_log,'debug','Error - No response');
			}
		}
		else {
			log::add($this->_log,'debug','Error - No socket');
		}
	}

	protected function send($cmd) {
		if ($this->_socket === false) {
			log::add($this->_log,'debug',"socket_create() failed: reason: " .socket_strerror(socket_last_error()) );
			return NOSOCKET;
		}
		else {
			log::add($this->_log,'debug','Commande : '.$cmd );		
			$key = md5(hex2bin($this->_token));
			$iv = md5(hex2bin($key.$this->_token));
			$data = bin2hex(openssl_encrypt($cmd, 'AES-128-CBC', hex2bin($key), OPENSSL_RAW_DATA, hex2bin($iv)));			
			$length = sprintf('%04x', (int)strlen($data)/2 + 32);
			$ts = sprintf('%08x', time() + $this->_tsDif);
			$packet = $this->_magic.$length.$this->_unknown.$this->_device.$ts.$this->_token.$data;		
			$checksum = md5(hex2bin($packet));
			$packet = $this->_magic.$length.$this->_unknown.$this->_device.$ts.$checksum.$data;
			$message = hex2bin($packet);
			log::add($this->_log,'debug',"try to connect to : $this->_host  $this->_port");
			socket_set_option($this->_socket, SOL_SOCKET, SO_RCVTIMEO, array("sec" => 0, "usec" => 250000));
			socket_set_option($this->_socket, SOL_SOCKET,SO_SNDTIMEO, array("sec" => 0, "usec" => 250000));				
			$time = microtime(true);
			$result = true;
			$timeout= 1;			
			while (!@socket_connect($this->_socket, $this->_host, $this->_port)) {
				$err = socket_last_error($this->_socket);
				// success!
				if($err === 56) {
					$result = true;
					break;
				}
				// if timeout reaches then call exit();
				//log::add($this->_log,'debug',"while :".microtime(true));
				if ((microtime(true) - $time) >= $timeout) {
					//socket_close($socket);
					log::add($this->_log,'debug',"Time out");
					$result = false;
					break;
				}
				// sleep for a bit
				usleep(5000);
			}			
			//$result = true;	
			if ($result === false) {
				log::add($this->_log,'debug',"socket_connect() failed. Reason: ($result) " . socket_strerror(socket_last_error($this->_socket)) );
				socket_close($socket);
				return NOTCONNECTED ;
			}
			else {					
				for ($iCount=0;$iCount<$this->_repeat;$iCount++) {
					socket_set_option($this->_socket,SOL_SOCKET, SO_SNDTIMEO, array("sec" => 0, "usec" => 250000));
					$ret = socket_write($this->_socket,$message,strlen($message));	
					if ($ret === false ) {
						log::add($this->_log,'debug',"socket_write() failed. Reason: ($ret) " .socket_strerror(socket_last_error()) );							
					}
					else {
						
						if ($this->_returnOK == false) {
							log::add($this->_log,'debug',"No Return");
							return NOSTATE;
						}
						else {
							socket_set_option($this->_socket, SOL_SOCKET, SO_RCVTIMEO, array("sec" => 0, "usec" => 999000));							
							$buf='';
							$bytes = socket_recvfrom($this->_socket, $buf, 4096,0, $this->_host, $this->_port);
							if ($bytes > 0) {
								//log::add($this->_log,'debug',"byte=".$bytes);
								$buf=bin2hex($buf);
								//log::add($this->_log,'debug','Msg :'.$buf);
								$data_length = strlen($buf) - 64;
								$data = substr($buf, 64, $data_length);
								$out = openssl_decrypt(hex2bin($data), 'AES-128-CBC', hex2bin($key), OPENSSL_RAW_DATA, hex2bin($iv));
								log::add($this->_log,'debug',"return : ".$out);
								$json_decoded_data = json_decode($out, true);
								if ( ($json_decoded_data != NULL) && ($json_decoded_data != FALSE) ){
									if (isset($json_decoded_data["result"])) {
										foreach ($json_decoded_data["result"] as $key => $value) {
											log::add($this->_log,'debug',">> : $key | $value : ".$json_decoded_data["result"][$key]);
										}
										//socket_close($this->_socket);
										return $json_decoded_data;
									}
									log::add($this->_log,'debug',"Bad response");
								}
							}
							else
								log::add($this->_log,'debug',"socket_read() failed: reason: " .socket_strerror(socket_last_error()) );	
						}						
					}
					usleep($this->_wait);
				}
				//socket_close($this->_socket);		
				return BADRESPONSE;				
			}			
		}
	}
	public function OnControl($value) {
		$this->getHello();
		$this->_Id++;
		$value="{".$value."}"."\r\n";
		return $this->send($value);
	}
	public function Toggle() {
		$this->getHello();
		$this->_Id++;
		$string = sprintf($this->_commandCodes['CMD_TOGGLE'],$this->_Id);
		return $this->send($string);
	}
	public function On() {
		$this->getHello();
		$this->_Id++;
		$string = sprintf($this->_commandCodes['CMD_ON'],$this->_Id);
		return  $this->send($string);
	}
	public function Off() {
		$this->getHello();
		$this->_Id++;
		$string = sprintf($this->_commandCodes['CMD_OFF'], $this->_Id);
		return $this->send($string);
	}	
	public function OnMax() {
		$this->getHello();
		$this->_Id++;
		$string = sprintf($this->_commandCodes['CMD_BRIGHTNESS'],$this->_Id, 100);
		return $this->send($string);
	}
	public function OnMin() {
		$this->getHello();
		$this->_Id++;
		$string = sprintf($this->_commandCodes['CMD_BRIGHTNESS'],$this->_Id, 5);
		return $this->send($string);
	}
	public function OnMid() {
		$this->getHello();
		$this->_Id++;
		$string = sprintf($this->_commandCodes['CMD_BRIGHTNESS'],$this->_Id, 50);
		return $this->send($string);
	}

	public function OnNight() {
		$this->getHello();
		$this->_Id++;	
		$string = sprintf($this->_commandCodes['CMD_BRIGHTNESS'],$this->_Id, 1);
		return $this->send($string);		
	}	
	public function OnColor($color='Red',$Bright=0) {
		$this->getHello();
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
		$r = (int)hexdec(substr($color,1,2));
		$g = (int)hexdec(substr($color,3,2));
		$b = (int)hexdec(substr($color,5,2));
		$this->_color= $color; 
		$Intcolor= $r*256*256+$g*256+$b;
		$this->_Id++;
		$string = sprintf($this->_commandCodes['CMD_RGB'],$this->_Id, $Intcolor);
		return $this->send($string);	
	}
  	public function OnDisco($prg,$speed=0) {
		// 1 : 
      	// 2 : 
      	// 3 : 
		$this->getHello();
		$this->_returnOK = false;	
		$this->_Id++;
   		$string = sprintf($this->_commandCodes['CMD_SCENE'],$this->_Id,$prg);
		return $this->send($string);
	}
  	public function DiscoSpeed($speed) {
      return -1;
    }
	public function OnHSV($hue=0,$sat=100) {
		if ($hue < 0)
			$hue = 0;
		if ($hue > 360)
			$hue = 360;
		if ($sat < 0)
			$sat = 0;
		if ($sat > 100)
			$sat = 100;
		$this->_Id++;
		$this->getHello();
		$string = sprintf($this->_commandCodes['CMD_HSV'],$this->_Id, $hue,$sat);
		return $this->send($string);	
	}
	public function OnBrightness($value=0x0e,$Col) {
		//$this->getHello();
		$this->getHello();		
		$this->_Id++;
		if ($value<1) $value=1;
		if ($value>100) $value=100;
		$string = sprintf($this->_commandCodes['CMD_BRIGHTNESS'],$this->_Id, $value);
		return $this->send($string);
	}
	public function OnBrightnessWhite($value=0x0e,$Col) {
		$this->getHello();
		$this->_Id++;
		if ($value<1) $value=1;
		if ($value>100) $value=100;
		$string = sprintf($this->_commandCodes['CMD_BRIGHTNESS'],$this->_Id, $value);
		return $this->send($string);
	}
	public function BrightnessIncreaseInt($value=50) {
		$this->getHello();
		$this->_Id++;
		$string = sprintf($this->_commandCodes['CMD_BRIGHT_UP'],$this->_Id);
		$this->send($string);
		$OutStr = $this->getStatus();
		if( isset($OutStr["result"][1])==true && $OutStr["result"][1]!="") {
				$Br=$OutStr["result"][1];
				return $Br;
		}
		return false;
	}
	public function BrightnessDecreaseInt($value=50) {
		$this->getHello();
		$this->_Id++;
		$string = sprintf($this->_commandCodes['CMD_BRIGHT_DOWN'],$this->_Id);
		$this->send($string);
		$OutStr = $this->getStatus();
		if( isset($OutStr["result"][1])==true && $OutStr["result"][1]!="") {
				$Br=$OutStr["result"][1];
				return $Br;
		}
		return false;
	}
    public function BrightnessIncrease($value=50,$Col) {
		$value=$value+$this->_increm;
		if ($value<1) $value=1;
		if ($value>100) $value=100;
		$this-> OnBrightness($value,$Col);
		return $value;
	}
	public function BrightnessDecrease($value=50,$Col) {
		$value=$value-$this->_increm;
		if ($value<1) $value=1;
		if ($value>100) $value=100;
		$this-> OnBrightness($value,$Col);
		return $value;
	}
	public function OnWarm() {
		return $this->OnKelvin(5);
	}
	public function OnCool() {
		return $this->OnKelvin(100);
	}
	public function OnLukeWarm() {
		return $this->OnKelvin(50);
	}
	public function KelvinIncrease($value=50) {
        /*
		$OutStr = $this->getStatus();
		if( isset($OutStr["result"][2])==true && $OutStr["result"][2]!="") {
			$Ct=$OutStr["result"][2];
			$this->On();
			$Ct = round($Ct*(6500-1700)/100 +1700);				
			$this->OnKelvin($Ct);
		}
		else
        */
		$this->getHello();
		$this->_Id++;	
		if ($value<1) $value=1;
		if ($value>100) $value=100;
		$string = sprintf($this->_commandCodes['CMD_TEMP_UP'],$this->_Id);
		$this->send($string);
		$OutStr = $this->getStatus();
		if( isset($OutStr["result"][2])==true && $OutStr["result"][2]!="") {
			$Ct=$OutStr["result"][2];
			return $Ct;
		}
		return false;
	}
	public function KelvinDecrease($value=50) {
	    /*	
        $OutStr = $this->getStatus();
		if( isset($OutStr["result"][2])==true && $OutStr["result"][2]!="") {
			$Ct=$OutStr["result"][2];
			$this->On();
			$Ct = round($Ct*(6500-1700)/100 +1700);
			$this->OnKelvin($Ct);
		}
		else
		*/
		if ($value<1) $value=1;
		if ($value>100) $value=100;
        $this->getHello();
		$this->_Id++;
		$string = sprintf($this->_commandCodes['CMD_TEMP_DOWN'],$this->_Id);
		$this->send($string);
		$OutStr = $this->getStatus();
		if( isset($OutStr["result"][2])==true && $OutStr["result"][2]!="") {
			$Ct=$OutStr["result"][2];
			return $Ct;
		}	
		return false;
	}

	public function OnKelvin($value=50) {
		$this->getHello();
		$this->_Id++;
		$string = sprintf($this->_commandCodes['CMD_CT'],$this->_Id, $value);
		return $this->send($string);
	}
	public function rgb2hex($rgb) {
	   $hex = "#";
	   $hex .= str_pad(dechex($rgb[0]), 2, "0", STR_PAD_LEFT);
	   $hex .= str_pad(dechex($rgb[1]), 2, "0", STR_PAD_LEFT);
	   $hex .= str_pad(dechex($rgb[2]), 2, "0", STR_PAD_LEFT);
	   return $hex; // returns the hex value including the number sign (#)
	}	
}

class W2_XiaomiPhilipsCeiling extends W2_XiaomiPhilipsBase
{ /*
     properties = ['power', 'bright', 'cct', 'snm', 'dv', 'bl', 'ac']
     "set_power", ["on"]
     "set_bright", [level]
     "set_cct", [level]  1-100
     "delay_off", [seconds]
     "apply_fixed_scene", [number]  1-3
     "enable_bl", [1]   smart night light
     "enable_ac", [1]  enable automatic CCT
     
     */
	public function retStatus() {
		$OutStr = $this->getStatus($this->_commandCodes['CMD_GET_PROP_CEILING']);
		if ( isset($OutStr) && $OutStr!=NOSOCKET && $OutStr!=NOTCONNECTED && $OutStr!=NORESPONSE && $OutStr!=BADRESPONSE) {		
			if ($OutStr["id"]!="") {
				//"power", "bright", "ct"		
				if( isset($OutStr["result"][1])==true && $OutStr["result"][1]!="") {
					$this->_return['White'] = $OutStr["result"][1];
				}
				if( isset($OutStr["result"][0])==true && $OutStr["result"][0]!="") {		
					if ($OutStr["result"][0] == "off"){
						$this->_return['On'] = 0;
					} 
					else if ($OutStr["result"][0] =="on"){
						$this->_return['On'] = 1;
					}
				}	
				if( isset($OutStr["result"][2])==true && $OutStr["result"][2]!="") {
					$Ct = $OutStr["result"][2];			
					$this->_return['Kelvin'] = $Ct;
				}				
				if( isset($OutStr["result"][3])==true && $OutStr["result"][3]!="") {
					$Snm = $OutStr["result"][3];			
					$this->_return['DiscoNum'] = $Snm;
				}
				// if( isset($OutStr["result"][4])==true && $OutStr["result"][4]!="") {
					// $this->_return['Timer'] = $OutStr["result"][4];			
				// }	
				if( isset($OutStr["result"][5])==true && $OutStr["result"][5]!="") {;
					if ($OutStr["result"][5] == "off"){
						$this->_return['NightMode'] = 0;
					} 
					else if ($OutStr["result"][5] =="on"){
						$this->_return['NightMode'] = 1;
					}
				}	
				if( isset($OutStr["result"][6])==true && $OutStr["result"][6]!="") {			
					if ($OutStr["result"][6] == "off"){
						$this->_return['CCTAuto'] = 0;
					} 
					else if ($OutStr["result"][6] =="on"){
						$this->_return['CCTAuto'] = 1;
					}	
				}					
			}
			else
				$this->_return['Type'] = 'Not a Ceiling Xiaomi Philips';
		}
		else if ( !isset($OutStr) )
			$this->_return = BADRESPONSE;
		else
			$this->_return = $OutStr;
		return $this->_return;	
	}
	public function OnNight($value = "on") {
      	if ($value == "off") $value = 0;
      	if ($value == "on") $value = 1;
		$this->_Id++;
		$this->getHello();
		$string = sprintf($this->_commandCodes['CMD_MOON'],$this->_Id,$value);
		return $this->send($string);
	}
  	public function EnableAC() {
		$this->getHello();
		$this->_Id++;
		$string = sprintf($this->_commandCodes['CMD_ENABLE_AC'],$this->_Id, 1);
		return $this->send($string);
	}
    public function DisableAC() {
		$this->getHello();
		$this->_Id++;
		$string = sprintf($this->_commandCodes['CMD_ENABLE_AC'],$this->_Id, 0);
		return $this->send($string);
	}	
	public function OnTimerOff($value = 0) {
		$this->getHello();
		$this->_Id++;
		$string = sprintf($this->_commandCodes['CMD_DELAY_OFF'],$this->_Id,$value);
		return $this->send($string);
	}
}
class W2_XiaomiPhilipsWhite extends W2_XiaomiPhilipsBase
{	
  
  /*
   properties = ['power', 'bright', 'cct', 'snm', 'dv']
  "set_power", ["on"]
  "set_bright", [level] 1-100
  "set_cct", [level]  1-100
  "apply_fixed_scene", [number]  1-3
  "delay_off", [seconds]  (s)
  */
	public function retStatus() {	
		$OutStr = $this->getStatus($this->_commandCodes['CMD_GET_PROP_WHITE']);
		if ( isset($OutStr) && $OutStr!=NOSOCKET && $OutStr!=NOTCONNECTED && $OutStr!=NORESPONSE && $OutStr!=BADRESPONSE) {	
			if ($OutStr["id"]!="") {				

				if( isset($OutStr['result'][0])==true && $OutStr['result'][0]!="") {		
					if ($OutStr['result'][0] =="off"){
						$this->_return['On']= 0;
					} 
					else if ($OutStr['result'][0] =="on"){
						$this->_return['On'] = 1;
					}
				}
				if( isset($OutStr['result'][1])==true && $OutStr['result'][1]!="") {
					$this->_return['White'] = $OutStr['result'][1];
				}
              	if( isset($OutStr["result"][2])==true && $OutStr["result"][2]!="") {
					$Ct = $OutStr["result"][2];			
					$this->_return['Kelvin'] = $Ct;
				}				
				if( isset($OutStr["result"][3])==true && $OutStr["result"][3]!="") {
					$Snm = $OutStr["result"][3];			
					$this->_return['DiscoNum'] = $Snm;
				}
				// if( isset($OutStr["result"][4])==true && $OutStr["result"][4]!="") {
					// $this->_return['Timer'] = $OutStr["result"][4];			
				// }					
			}
			else
				$this->_return['Type'] = 'Not a White Xiaomi-Philips bulb';
		}
		else if ( !isset($OutStr) )
			$this->_return = BADRESPONSE;
		else
			$this->_return = $OutStr;
		return $this->_return;	
	}	
	public function OnTimerOff($value = 0) {
		$this->getHello();
		$this->_Id++;
		$string = sprintf($this->_commandCodes['CMD_DELAY_OFF'],$this->_Id,$value);
		return $this->send($string);
	}
}


class W2_XiaomiPhilipsCare extends W2_XiaomiPhilipsBase
{
    /*
    properties = ['power', 'bright', 'notifystatus', 'ambstatus',
                 'ambvalue', 'eyecare', 'scene_num', 'bls','dvalue', ]
  Eyecare status : 
   ['power': 'off', 'bright': 5, 'notifystatus': 'off',// indicateur fatigue
    'ambstatus': 'off',  // ambiance
    'ambvalue': 41, // bright ambiance
    'eyecare': 'on',
    'scene_num': 3,  
    'bls': 'on', //smart night light on/off 
    'dvalue': 0] //count down after off in min
// controls
"set_power", ["off"]
"set_bright", [level] 1-100
"set_notifyuser", ["on"] //indicateur fatigue
"enable_amb", ["on"]
"set_amb_bright", [level]
"set_eyecare", ["off"]
"set_user_scene", [number] //1-3
"enable_bl", ["on"]
"delay_off", [minutes] >=0

*/
  
	public function retStatus() {
		$OutStr = $this->getStatus($this->_commandCodes['CMD_GET_PROP_EYE']);

		if ( isset($OutStr) && $OutStr!=NOSOCKET && $OutStr!=NOTCONNECTED && $OutStr!=NORESPONSE && $OutStr!=BADRESPONSE) {		
			if ($OutStr["id"]!="") {
				//'power' 'bright' 'notifystatus' 'ambstatus' 'ambvalue' 'eyecare' 'scene_num' 'bls'//smart night light on/off 'dvalue'
				if( isset($OutStr["result"][0])==true && $OutStr["result"][0]!="") {		
					if ($OutStr["result"][0] == "off"){
						$this->_return['On'] = 0;
					} 
					else if ($OutStr["result"][0] =="on"){
						$this->_return['On'] = 1;
					}
				}
				if( isset($OutStr["result"][1])==true && $OutStr["result"][1]!="") {
					$this->_return['White'] = $OutStr["result"][1];
				}
				if( isset($OutStr["result"][2])==true && $OutStr["result"][2]!="") {
					if ($OutStr["result"][2] == "off"){
						$this->_return['EyeNotify'] = 0;
					} 
					else if ($OutStr["result"][2] =="on"){
						$this->_return['EyeNotify'] = 1;
					}
				}
				if( isset($OutStr["result"][3])==true && $OutStr["result"][3]!="") {		
					if ($OutStr["result"][3] == "off"){
						$this->_return['AmbOn'] = 0;
					} 
					else if ($OutStr["result"][3] =="on"){
						$this->_return['AmbOn'] = 1;
					}
				}
				if( isset($OutStr["result"][4])==true && $OutStr["result"][4]!="") {		
					$this->_return['AmbIntensity'] = $OutStr["result"][4];
				}
				if( isset($OutStr["result"][5])==true && $OutStr["result"][5]!="") {
					if ($OutStr["result"][5] == "off"){
						$this->_return['Eye'] = 0;
					} 
					else if ($OutStr["result"][5] =="on"){
						$this->_return['Eye'] = 1;
					}				
				}							
				if( isset($OutStr["result"][6])==true && $OutStr["result"][6]!="") {
					$Snm = $OutStr["result"][6];			
					$this->_return['DiscoNum'] = $Snm;
				}	
				if( isset($OutStr["result"][7])==true && $OutStr["result"][7]!="") {
					if ($OutStr["result"][7] == "off"){
						$this->_return['NightMode'] = 0;
					} 
					else if ($OutStr["result"][7] =="on"){
						$this->_return['NightMode'] = 1;
					}				
				}
				// if( isset($OutStr["result"][8])==true && $OutStr["result"][8]!="") {
					// $this->_return['Timer'] = $OutStr["result"][8];			
				// }
			}
			else
				$this->_return['Type'] = 'Not a Xiaomi Philips Eye care';
		}
		else if ( !isset($OutStr) )
			$this->_return = BADRESPONSE;
		else
			$this->_return = $OutStr;
		return $this->_return;	
	}
    public function OnDisco($prg,$speed=0) {
		// 1 : 
      	// 2 : 
      	// 3 : 
		$this->getHello();
		$this->_returnOK = false;	
		$this->_Id++;
   		$string = sprintf($this->_commandCodes['CMD_SCENE_EYE_CARE'],$this->_Id,$prg);
		return $this->send($string);
	}
  	public function OnNight($value = "on") {
		$this->getHello();		
		$this->_Id++;
		$string = sprintf($this->_commandCodes['CMD_MOON'],$this->_Id,'"'.$value.'"');
		return $this->send($string);
	}
    public function EyeCare($value = "on") {
		$this->getHello();
		$this->_Id++;
		$string = sprintf($this->_commandCodes['CMD_ENABLE_EYE_CARE'],$this->_Id,$value);
		return $this->send($string);
	}
    public function EyeCareNot($value = "on") {
		$this->getHello();
		$this->_Id++;
		$string = sprintf($this->_commandCodes['CMD_EYE_CARE_NOT'],$this->_Id,$value);
		return $this->send($string);
	}    
	public function OnTimerOff($value = 0) {
		$this->getHello();
		$this->_Id++;
		$string = sprintf($this->_commandCodes['CMD_DELAY_OFF'],$this->_Id,$value);
		return $this->send($string);
	}	
    public function AmbianceCare($value = "on") {
		//log::add($this->_log,'debug',"value=".$value);
		$this->getHello();
		$this->_Id++;
		$string = sprintf($this->_commandCodes['CMD_ENABLE_AMB'],$this->_Id,$value);
		//log::add($this->_log,'debug',"string=".$string);
		return $this->send($string);
	}
	public function OnAmb() {
		//log::add($this->_log,'debug',"value=".$value);
		$this->getHello();
		$this->_Id++;
		$string = sprintf($this->_commandCodes['CMD_ENABLE_AMB'],$this->_Id,"on");
		//log::add($this->_log,'debug',"string=".$string);
		return $this->send($string);
	}
	public function OffAmb() {
		//log::add($this->_log,'debug',"value=".$value);
		$this->getHello();
		$this->_Id++;
		$string = sprintf($this->_commandCodes['CMD_ENABLE_AMB'],$this->_Id,"off");
		//log::add($this->_log,'debug',"string=".$string);
		return $this->send($string);
	}
  	public function BrightnessIncreaseAmb($value=50,$Col, $white1 =0, $white2 =0) {
		$value=$value+$this->_increm;
		if ($value<0) $value=0;
		if ($value>100) $value=100;
		$this-> OnBrightnessAmb($value,$Col);
		return $value;
	}
	public function BrightnessDecreaseAmb($value=50,$Col, $white1 =0, $white2 =0) {
		$value=$value-$this->_increm;
		if ($value<0) $value=0;
		if ($value>100) $value=100;
		$this-> OnBrightnessAmb($value,$Col);
		return $value;
	}
  	public function OnBrightnessAmb($value=0x0e,$Col ="", $white1 =0, $white2 =0) {
		$this->getHello();		
		$this->_Id++;
		if ($value<1) $value=1;
		if ($value>100) $value=100;
		$string = sprintf($this->_commandCodes['CMD_BRIGHTNESS_AMB'],$this->_Id, $value);
		return $this->send($string);
	}
}

?>