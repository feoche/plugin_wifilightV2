<?php
require_once dirname(__FILE__) . '/include/common.php';
class W2_YeeLightBase
{

	//Yeelight Bulbs commands
	protected $_host;
	protected $_port;
	protected $_wait;
	protected $_repeat;
	protected $_repeatOK;
	protected $_color = "#000000";
	protected $_colorAmb = "#000000";
	protected $_increm;
	protected $_returnOK = true;
	protected $_cap = NULL;
	protected $_delay = 101000; //microseconds
	protected $_return ;
	protected $_log;
	protected $_commandCodes = array(
	'CMD_TOGGLE' => '{"id":%d,"method":"toggle","params":[]}'."\r\n", //
	'CMD_ON' => '{"id":%d,"method":"set_power","params":["on","smooth",500]}'."\r\n",
	'CMD_OFF' => '{"id":%d,"method":"set_power","params":["off","smooth",500]}'."\r\n",
    'CMD_CT' => '{"id":%d,"method":"set_ct_abx","params":[%d, "smooth", 500]}'."\r\n",
	'CMD_NIGHT_W' =>  '{"id":%d,"method":"set_bright","params":[1, "smooth", 500]}'."\r\n",
	'CMD_NIGHT_RGBW' => '{"id":%d,"method":"set_scene", "params": ["color", 16750848, 1]}'."\r\n",
	'CMD_BRIGHT_UP' => '{"id":1,"method":"set_adjust","params":["increase", "bright"]}'."\r\n",
	'CMD_BRIGHT_DOWN' => '{"id":1,"method":"set_adjust","params":["decrease", "bright"]}'."\r\n",
	'CMD_TEMP_UP' => '{"id":1,"method":"set_adjust","params":["increase", "ct"]}'."\r\n",
	'CMD_TEMP_DOWN' => '{"id":1,"method":"set_adjust","params":["decrease", "ct"]}'."\r\n",
	'CMD_WHITE_W' => '{"id":1,"method":"set_bright","params":[100, "smooth", 500]}'."\r\n",
	'CMD_WHITE_RGBW' => '{"id":1,"method":"set_rgb","params":[16777215, "smooth", 500]}'."\r\n",
    'CMD_HSV' => '{"id":%d,"method":"set_hsv","params":[%d, %d, "smooth", 200]}'."\r\n",
	'CMD_RGB'=>'{"id":%d,"method":"set_rgb","params":[%d, "smooth", 500]}'."\r\n",
    'CMD_BRIGHTNESS' => '{"id":%d,"method":"set_bright","params":[%d, "smooth", 200]}'."\r\n",
    'CMD_BRIGHTNESS_SCENE' => '{"id":%d,"method":"set_bright","params":[%d, "smooth", 500]}'."\r\n",
    'CMD_COLOR_SCENE' => '{"id":%d,"method":"set_scene","params":["cf",1,0,"100,1,%d,1"]}'."\r\n",
	'CMD_GET_PROP' => '{"id":%d,"method":"get_prop","params":["power", "bright", "ct", "rgb","hue","sat","color_mode","bg_power", "bg_bright", "bg_ct", "bg_rgb", "bg_hue","bg_sat","bg_lmode"]}'."\r\n",
	'CMD_MOON' => '{"id":%d,"method":"set_scene","params":["nightlight",%d]}'."\r\n",
    'CMD_NIGHTR' => '{ "id":%d, "method":"set_scene", "params":["ct",4000, 40]}'."\r\n",
	'CMD_READING' => '{ "id":%d, "method":"set_scene", "params":["ct",4000, 100]}'."\r\n",
    'CMD_CONC' => '{ "id":%d, "method":"set_scene", "params":["ct",6500, 100]}'."\r\n",
    'CMD_PC' => '{ "id":%d, "method":"set_scene", "params":["ct",2700, 30]}'."\r\n",
	'CMD_CTS' => '{"id":%d, "method":"set_scene", "params":["ct", %d, %d]}'."\r\n",
	
	'CMD_ON_BG' => '{"id":%d,"method":"bg_set_power","params":["on","smooth",500]}'."\r\n",
	'CMD_OFF_BG' => '{"id":%d,"method":"bg_set_power","params":["off","smooth",500]}'."\r\n",
	'CMD_RGB_BG'=>'{"id":%d,"method":"bg_set_rgb","params":[%d, "smooth", 500]}'."\r\n",
	'CMD_BRIGHTNESS_BG' => '{"id":%d,"method":"bg_set_bright","params":[%d, "smooth", 200]}'."\r\n",
	'CMD_BRIGHT_UP_BG' => '{"id":1,"method":"bg_set_adjust","params":["increase", "bright"]}'."\r\n",
	'CMD_BRIGHT_DOWN_BG' => '{"id":1,"method":"bg_set_adjust","params":["decrease", "bright"]}'."\r\n",
	

	);
	//Used to dynamically call class methods
	public function call(){
		echo __METHOD__;
	}	
	public function __construct($host = '192.168.1.110', $wait=0, $repeat=1, $increm=10, $ID=0, $LocalId="", $nbLeds=0, $colorOrder=0, $port = 55443) {
		$this->_host = $host;
		$this->_port = $port;
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
		//$this->_cap = $cap;
		$myRet = new wifilightV2c;
		$this->_return = $myRet->_return_WFL;
		$this->_return['Type'] = true;
		$this->_log = $myRet->_log;	
	}
	public function GetColor() {
		return $this->_color;
	}
	public function GetColorAmb() {
		return $this->_colorAmb;
	}
	public function getStatus() {
		$Id = 1;
		$string = sprintf($this->_commandCodes['CMD_GET_PROP'],$Id);
		return $this->send($string,$Id);
	}
	public function retStatus($OutStr=0) {
		if ($OutStr == 0) $OutStr = $this->getStatus();
		if ( isset($OutStr) && $OutStr!=NOSOCKET && $OutStr!=NOTCONNECTED && $OutStr!=NORESPONSE && $OutStr!=BADRESPONSE) {		
			if ($OutStr["id"]!="") {
				//"power", "bright", "ct", "rgb","hue","sat","color_mode"	
				//"bg_power", "bg_bright", "bg_ct", "bg_rgb", "bg_hue","bg_sat"	, "bg_lmode"
				// 1 means color mode, 2 means color temperature mode, 3 means HSV mode.
				if( isset($OutStr["result"][0])==true && $OutStr["result"][0]!="") {		
					if ($OutStr["result"][0] == "off"){
						$this->_return['On'] = 0;
					} 
					else if ($OutStr["result"][0] =="on"){
						$this->_return['On'] = 1;
					}
				}	
				if( isset($OutStr["result"][1])==true && $OutStr["result"][1]!="") {
					if( isset($OutStr["result"][6])==true && $OutStr["result"][6]!="") {
						if ($OutStr["result"][6] == 2)
							$this->_return['White'] = $OutStr["result"][1];
						if ($OutStr["result"][6] == 1)
							$this->_return['Intensity'] = $OutStr["result"][1];
					}	
				}
				if( isset($OutStr["result"][2])==true && $OutStr["result"][2]!="") {
					$Ct = $OutStr["result"][2];
					if (isset($this->_cap['KelvinMax'])) $Ct = round(($Ct-$this->_cap['KelvinMin']) /($this->_cap['KelvinMax']-$this->_cap['KelvinMin'])*100) ;
					else	$Ct = round(($Ct-2710) /(6500-2710)*100) ;				
					$this->_return['Kelvin'] = $Ct;
				}
				if( isset($OutStr["result"][3])==true && $OutStr["result"][3]!="") {
					$Col[0]=intval(intval($OutStr["result"][3])/256/256);
					$Col[1]=intval((intval($OutStr["result"][3]) - $Col[0]*256*256)/256);
					$Col[2]=intval((intval($OutStr["result"][3]) - $Col[0]*256*256 - $Col[1]*256));
					$this->_return['Color'] = $this->rgb2hex($Col);
				}
				if( isset($OutStr["result"][7])==true && $OutStr["result"][7]!="") {		
					if ($OutStr["result"][7] == "off"){
						$this->_return['AmbOn'] = 0;
					} 
					else if ($OutStr["result"][7] =="on"){
						$this->_return['AmbOn'] = 1;
					}
				}	
				if( isset($OutStr["result"][8])==true && $OutStr["result"][8]!="") {
					if( isset($OutStr["result"][13])==true && $OutStr["result"][13]!="") {
						if ($OutStr["result"][13] == 2)
							$this->_return['AmbWhite'] = $OutStr["result"][8];
						if ($OutStr["result"][13] == 1)
							$this->_return['AmbIntensity'] = $OutStr["result"][8];
					}	
				}
				if( isset($OutStr["result"][9])==true && $OutStr["result"][9]!="") {
					$Ct = $OutStr["result"][9];
					$Ct = round(($Ct-2710) /(6500-2710)*100) ;				
					$this->_return['AmbKelvin'] = $Ct;
				}
				if( isset($OutStr["result"][10])==true && $OutStr["result"][10]!="") {
					$Col[0]=intval(intval($OutStr["result"][10])/256/256);
					$Col[1]=intval((intval($OutStr["result"][10]) - $Col[0]*256*256)/256);
					$Col[2]=intval((intval($OutStr["result"][10]) - $Col[0]*256*256 - $Col[1]*256));
					$this->_return['AmbColor'] = $this->rgb2hex($Col);
				}
			}
			else
				$this->_return['Type'] = 'Not a Jiaoyue650 Yeelight';
		}
		else if ( !isset($OutStr) )
			$this->_return = BADRESPONSE;
		else
			$this->_return = $OutStr;
		return $this->_return;	
	}
	public function Create(&$socket,$IPaddr) {
		$socket = @socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array("sec" => 0, "usec" => 250000));
		socket_set_option($socket, SOL_SOCKET,SO_SNDTIMEO, array("sec" => 0, "usec" => 500000));
		return socket_connect($socket,$IPaddr,55443);	
	}
	public function Decode($buf) {
		$json_decoded_data = json_decode($buf, true);
		if ( ($json_decoded_data != NULL) && ($json_decoded_data !== FALSE) ){
			//log::add($this->_log,'debug',"JSON1");
			foreach ($json_decoded_data as $key => $value) {
				log::add($this->_log,'debug',">>> : $key | $value : ".$json_decoded_data[$key]);
			}
			if (isset($json_decoded_data["params"])) {
				$Conv = array(
				'power' => 0,
				'bright' => 1,
				'ct' => 2,
				'rgb' => 3,
				'hue' => 4,
				'sat' => 5,
				'color_mode' => 6,
				'bg_power' => 7,
				'bg_bright' => 8,
				'bg_ct' => 9,
				'bg_rgb' => 10,
				'bg_hue' => 11,
				'bg_sat' => 12,
				'bg_lmode' => 13
				);
				$out=array();
				$out["id"]="1";
				foreach ($json_decoded_data["params"] as $key => $value) {
					//log::add($this->_log,'debug',">>>>>>>>>>> : $key | ".$json_decoded_data["params"][$key]);
					$out["result"][$Conv[$key]] = $json_decoded_data["params"][$key];
				}
				log::add($this->_log,'debug','Read Json OK');
				$outRet = $this->retStatus($out);
				return $outRet;			
			}
			else log::add($this->_log,'debug',"Bad response");
			return FALSE;
		}
		else log::add($this->_log,'debug',"Bad response");
		return FALSE;
	}
	protected function send($command,$Id) {
		$message = $command;
		log::add($this->_log,'debug','Commande : '.$message );
		// Create a TCP/IP socket.		
		$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		
		if ($socket === false) {
			log::add($this->_log,'debug',"socket_create() failed: reason: " .socket_strerror(socket_last_error()) );
			return NOSOCKET;
		}
		else {
			//log::add($this->_log,'debug','socket_create() OK.');
			log::add($this->_log,'debug',"try to connect to : $this->_host  $this->_port");
			socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array("sec" => 0, "usec" => 250000));
			socket_set_option($socket, SOL_SOCKET,SO_SNDTIMEO, array("sec" => 0, "usec" => 500000));				
			//socket_set_nonblock($socket);
			// store the current time
			$time = microtime(true);
			$result = true;
			$timeout= 1;
			//log::add($this->_log,'debug',"while :".$time);
			// loop until a connection is gained or timeout reached
			while (!@socket_connect($socket, $this->_host, $this->_port)) {
				$err = socket_last_error($socket);
				// success!
				if($err === 56) {
					//log::add($this->_log,'debug',"Connect OK");
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
				log::add($this->_log,'debug',"socket_connect() failed. Reason: ($result) " . socket_strerror(socket_last_error($socket)) );
				socket_close($socket);
				return NOTCONNECTED ;
			}
			else {			
				log::add($this->_log,'debug','Send OK.');			
				for ($iCount=0;$iCount<$this->_repeat;$iCount++) {
					socket_set_option($socket,SOL_SOCKET, SO_SNDTIMEO, array("sec" => 0, "usec" => 250000));
					$ret = socket_write($socket,$message,strlen($message));	
					if ($ret === false ) {
						log::add($this->_log,'debug',"socket_write() failed. Reason: ($ret) " .socket_strerror(socket_last_error()) );							
					}
					else {
						if ($this->_returnOK == false) {
							log::add($this->_log,'debug',"No Return");
							return NOSTATE;
						}
						else {			
							socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array("sec" => 0, "usec" => 999000));							
							$out = socket_read($socket, 250);
							if ( isset($out) && $out !== FALSE && $out !="" ) {
								log::add($this->_log,'debug','return : '. $out);
								$json_decoded_data = json_decode($out, true);
								if ( ($json_decoded_data != NULL) && ($json_decoded_data != FALSE) ){
									foreach ($json_decoded_data as $key => $value) {
										//log::add($this->_log,'debug',">>> : $key | $value : ".$json_decoded_data[$key]);
									}
									if (isset($json_decoded_data["result"])) {
										foreach ($json_decoded_data["result"] as $key => $value) {
											//log::add($this->_log,'debug',">>>>>>>>>>> : $key | $value : ".$json_decoded_data["result"][$key]);
										}
										socket_close($socket);
										log::add($this->_log,'debug','Read Json OK');
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
				socket_close($socket);		
				return BADRESPONSE;			
			}
		}
	}
	public function OnControl($value) {
		$Id = 1;
		$value="{".$value."}"."\r\n";
		$OutStr = $this->send($value,$Id);
		//log::add($this->_log,'debug',"$ret=".$OutStr);
		if ( isset($OutStr) && $OutStr!=NOSOCKET && $OutStr!=NOTCONNECTED && $OutStr!=NORESPONSE && $OutStr!=BADRESPONSE) {
			//log::add($this->_log,'debug',"OutStr=".$OutStr);
			$pos = strpos($value, "bg_set_");
			if ($pos!== false) {
				$OutStr = MODEONBG;
			}
			else
				$OutStr = MODEON;
		}
		return $OutStr;
	}
	public function Toggle() {
		$Id = 1;
		$string = sprintf($this->_commandCodes['CMD_TOGGLE'],$Id);
		return $this->send($string,$Id);
	}
	public function On() {
		$Id = 1;
		$string = sprintf($this->_commandCodes['CMD_ON'],$Id);
		return $this->send($string,$Id);
	}
	public function Off() {
		$Id =1;
		$string = sprintf($this->_commandCodes['CMD_OFF'], $Id);
		return $this->send($string,$Id);
	}	
	public function OnMax() {
		$this->On();
		$Id = 1;
		$string = sprintf($this->_commandCodes['CMD_BRIGHTNESS'],$Id, 100);
		return $this->send($string,$Id);
	}
	public function OnMin() {
		$this->On();
		$Id = 1;
		$string = sprintf($this->_commandCodes['CMD_BRIGHTNESS'],$Id, 5);
		return $this->send($string,$Id);
	}
	public function OnMid() {
		$this->On();
		$Id = 1;
		$string = sprintf($this->_commandCodes['CMD_BRIGHTNESS'],$Id, 50);
		return $this->send($string,$Id);
	}

	public function OnNight() {
		$this->On();
		$Id = 1;	
		$string = sprintf($this->_commandCodes['CMD_BRIGHTNESS'],$Id, 1);
		return $this->send($string,$Id);		
	}	
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
		$r = (int)hexdec(substr($color,1,2));
		$g = (int)hexdec(substr($color,3,2));
		$b = (int)hexdec(substr($color,5,2));
		$this->_color= $color; 
		$Intcolor= $r*256*256+$g*256+$b;		
		$Id = 1;
		$string = sprintf($this->_commandCodes['CMD_RGB'],$Id, $Intcolor);
		return $this->send($string,$Id);	
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
		$Id = 1;
		$string = sprintf($this->_commandCodes['CMD_HSV'],$Id, $hue,$sat);
		return $this->send($string,$Id);	
	}
	public function OnBrightness($value=0x0e,$Col) {
		$this->On();
		$Id = 1;
		if ($value<1) $value=1;
		if ($value>100) $value=100;
		$string = sprintf($this->_commandCodes['CMD_BRIGHTNESS'],$Id, $value);
		return $this->send($string,$Id);
	}
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

	public function OnKelvin($value=50) {
		$this->On();
		$Id = 1;
		$value = round($value*(6500-1700)/100 +1700);
		$string = sprintf($this->_commandCodes['CMD_CT'],$Id, $value);
		return $this->send($string,$Id);
	}
	public function rgb2hex($rgb) {
	   $hex = "#";
	   $hex .= str_pad(dechex($rgb[0]), 2, "0", STR_PAD_LEFT);
	   $hex .= str_pad(dechex($rgb[1]), 2, "0", STR_PAD_LEFT);
	   $hex .= str_pad(dechex($rgb[2]), 2, "0", STR_PAD_LEFT);
	   return $hex; // returns the hex value including the number sign (#)
	}	
}

class W2_YeeLightWhite extends W2_YeeLightBase
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
				$this->_return['Type'] = 'Not a White Yeelight bulb';
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

class W2_YeeLightRGBW extends W2_YeeLightBase
{	/*
	public function retStatus() {
		$OutStr = $this->getStatus();
		if ( isset($OutStr) && $OutStr!=NOSOCKET && $OutStr!=NOTCONNECTED && $OutStr!=NORESPONSE && $OutStr!=BADRESPONSE) {
			if (isset($OutStr["id"]) && $OutStr["id"]!="") {
				//"power", "bright", "ct", "rgb","hue","sat","color_mode"		
				if( isset($OutStr["result"][1])==true && $OutStr["result"][1]!="") {
					if( isset($OutStr["result"][6])==true && $OutStr["result"][6]!="") {
						if ($OutStr["result"][6] == 2)
							$this->_return['White'] = $OutStr["result"][1];
						if ($OutStr["result"][6] == 1)
							$this->_return['Intensity'] = $OutStr["result"][1];
					}
				}
				if( isset($OutStr["result"][0])==true && $OutStr["result"][0]!="") {		
					if ($OutStr["result"][0] =="off"){
						$this->_return['On'] = 0;
					} 
					else if ($OutStr["result"][0] =="on"){
						$this->_return['On'] = 1;
					}
				}		
				if( isset($OutStr["result"][2])==true && $OutStr["result"][2]!="") {
					$Ct = $OutStr["result"][2];
					$Ct = round(($Ct-1700) /(6500-1700)*100) ;				
					$this->_return['Kelvin'] = $Ct;
				}
				if( isset($OutStr["result"][3])==true && $OutStr["result"][3]!="") {
					$Col[0]=intval(intval($OutStr["result"][3])/256/256);
					$Col[1]=intval((intval($OutStr["result"][3]) - $Col[0]*256*256)/256);
					$Col[2]=intval((intval($OutStr["result"][3]) - $Col[0]*256*256 - $Col[1]*256));
					$this->_return['Color'] = $this->rgb2hex($Col);
				}			
			}
			else
				$this->_return['Type'] = 'Not a RGBW Yeelight bulb';
		}
		else if ( !isset($OutStr) )
			$this->_return = BADRESPONSE;
		else
			$this->_return = $OutStr;
		return $this->_return;	
	}	
	*/
	public function OnWhite() {
		$this->On();
		$Id = 1;
		$string = sprintf($this->_commandCodes['CMD_WHITE_RGBW'],$Id);
		return $this->send($string,$Id);	
	}
}
class W2_YeeLightStrip extends W2_YeeLightBase
{	/*
	public function retStatus() {
		$OutStr = $this->getStatus();
		if ( isset($OutStr) && $OutStr!=NOSOCKET && $OutStr!=NOTCONNECTED && $OutStr!=NORESPONSE && $OutStr!=BADRESPONSE) {		
			if ($OutStr["id"]!="") {
				//"power", "bright", "ct", "rgb","hue","sat","color_mode"		
				if( isset($OutStr["result"][1])==true && $OutStr["result"][1]!="") {
					$this->_return['Intensity'] = $OutStr["result"][1];
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
					$Ct = ($Ct-1700) /(6500-1700)*100 ;				
					$this->_return['Kelvin'] = $Ct;
				}
				if( isset($OutStr["result"][3])==true && $OutStr["result"][3]!="") {
					$Col[0]=intval(intval($OutStr["result"][3])/256/256);
					$Col[1]=intval((intval($OutStr["result"][3]) - $Col[0]*256*256)/256);
					$Col[2]=intval((intval($OutStr["result"][3]) - $Col[0]*256*256 - $Col[1]*256));
					$this->_return['Color'] = $this->rgb2hex($Col);
				}
				
			}
			else
				$this->_return['Type'] = 'Not a Strip Yeelight';
		}
		else if ( !isset($OutStr) )
			$this->_return = BADRESPONSE;
		else
			$this->_return = $OutStr;
		return $this->_return;	
	}	
	*/
	public function OnWhite() {
		$this->On();	
		$Id = 1;
		$string = sprintf($this->_commandCodes['CMD_WHITE_RGBW'],$Id);	
		return $this->send($string,$Id);		
	}
}
class W2_YeeLightCeiling extends W2_YeeLightBase
{	/*
	public function retStatus() {
		$OutStr = $this->getStatus();
		if ( isset($OutStr) && $OutStr!=NOSOCKET && $OutStr!=NOTCONNECTED && $OutStr!=NORESPONSE && $OutStr!=BADRESPONSE) {		
			if ($OutStr["id"]!="") {
				//"power", "bright", "ct", "rgb","hue","sat","color_mode"		
				if( isset($OutStr["result"][1])==true && $OutStr["result"][1]!="") {
					if( isset($OutStr["result"][6])==true && $OutStr["result"][6]!="") {
						if ($OutStr["result"][6] == 2)
							$this->_return['White'] = $OutStr["result"][1];
						if ($OutStr["result"][6] == 1)
							$this->_return['Intensity'] = $OutStr["result"][1];
					}
						
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
					$Ct = round(($Ct-2710) /(6500-2710)*100) ;				
					$this->_return['Kelvin'] = $Ct;
				}
				if( isset($OutStr["result"][3])==true && $OutStr["result"][3]!="") {
					$Col[0]=intval(intval($OutStr["result"][3])/256/256);
					$Col[1]=intval((intval($OutStr["result"][3]) - $Col[0]*256*256)/256);
					$Col[2]=intval((intval($OutStr["result"][3]) - $Col[0]*256*256 - $Col[1]*256));
					$this->_return['Color'] = $this->rgb2hex($Col);
				}				
			}
			else
				$this->_return['Type'] = 'Not a Ceiling  Yeelight';
		}
		else if ( !isset($OutStr) )
			$this->_return = BADRESPONSE;
		else
			$this->_return = $OutStr;
		return $this->_return;	
	}
	*/
	public function OnMoon($value) {
		$this->On();
		$Id = 1;
		if ($value<1) $value=1;
		if ($value>100) $value=100;
		$string = sprintf($this->_commandCodes['CMD_MOON'],$Id, $value);
		return $this->send($string,$Id);
	}
  	public function OnKelvin($value=50) {
		$this->On();
		$Id = 1;
		$value = round($value*(6500-2710)/100 +2710);
		$string = sprintf($this->_commandCodes['CMD_CT'],$Id, $value);
		return $this->send($string,$Id);
	}
	public function OnWarm() {
		return $this->OnKelvin(5);
	}
	
}

class W2_YeeLightMijia extends W2_YeeLightBase
{	protected $_returnOK = false;
	/*
	public function retStatus() {
		$OutStr = $this->getStatus();
		if ( isset($OutStr) && $OutStr!=NOSOCKET && $OutStr!=NOTCONNECTED && $OutStr!=NORESPONSE && $OutStr!=BADRESPONSE) {		
			if ($OutStr["id"]!="") {
				//"power", "bright", "ct", "rgb","hue","sat","color_mode"		
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
					$Ct = ($Ct-1700) /(6500-1700)*100 ;				
					$this->_return['Kelvin'] = $Ct;
				}			
			}
			else
				$this->_return['Type'] = 'Not a Xiaomi Mijia Light';
		}
		else if ( !isset($OutStr) )
			$this->_return = BADRESPONSE;
		else
			$this->_return = $OutStr;
		return $this->_return;	
	}
	*/
	function getStatus() {
		$Id = 1;
		$string = sprintf($this->_commandCodes['CMD_GET_PROP'],$Id);
		$this->_returnOK = true;
		$ret = $this->send($string,$Id);
		$this->_returnOK = false;
		return $ret;
	}
	public function OnDisco($prg,$speed) {
		// 1 : Night reading
      	// 2 : Night
      	// 3 : Concentration
        // 4 : PC
		$this->_returnOK = false;	
		$this->On();
		$Id = 1;
      switch ($prg) {
        case 1 :
			$string = sprintf($this->_commandCodes['CMD_NIGHTR'],$Id);
          	break;
        case 2 :
			$string = sprintf($this->_commandCodes['CMD_READING'],$Id);
          	break;
        case 3 :
			$string = sprintf($this->_commandCodes['CMD_CONC'],$Id);
          	break;
        case 4 :
			$string = sprintf($this->_commandCodes['CMD_PC'],$Id);
          	break;
      	}
		return $this->send($string,$Id);
	}
  	public function DiscoSpeed($speed) {
      return -1;
    }
	public function OnKelvin($value=50) {
		$this->_returnOK = false;
		$this->On();
		$OutStr = $this->getStatus();
		$Br=50;
		if( isset($OutStr["result"][1])== true && $OutStr["result"][1]!="") {
				$Br=$OutStr["result"][1];
		}
		$Id = 1;
		$value = round($value*(6500-2700)/100 +2700);
		$string = sprintf($this->_commandCodes['CMD_CTS'],$Id, $value,$Br);
		return $this->send($string,$Id);
	}
	public function OnBrightness($value=0x0e,$Col,$white1=0,$white2=0) {
		$this->_returnOK = false;
		$this->On();
		$OutStr = $this->getStatus();
		$Ct=3000;
		if( isset($OutStr["result"][2])== true && $OutStr["result"][2]!="") {
				$Ct=$OutStr["result"][2];
		}
		$Id = 1;
		if ($value<1) $value=1;
		if ($value>100) $value=100;
		//$Ct = round($Ct*(6500-2700)/100 +2700);
		$string = sprintf($this->_commandCodes['CMD_CTS'],$Id, $Ct,$value);
		return $this->send($string,$Id);
	}
	public function OnBrightnessWhite($value=0x0e,$Col) {
		$this->_returnOK = false;
		$this->On();
		$OutStr = $this->getStatus();
		$Ct=3000;
		if( isset($OutStr["result"][2])== true && $OutStr["result"][2]!="") {
				$Ct=$OutStr["result"][2];
		}
		$Id = 1;
		if ($value<1) $value=1;
		if ($value>100) $value=100;
		//$Ct = round($Ct*(6500-2700)/100 +2700);
		$string = sprintf($this->_commandCodes['CMD_CTS'],$Id, $Ct,$value);
		return $this->send($string,$Id);
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
		$this->_returnOK = false;		
		$this->On();
		$Id = 1;	
		$string = sprintf($this->_commandCodes['CMD_TEMP_UP'],$Id);
		$this->send($string,$Id);
		$OutStr = $this->getStatus();
		if( isset($OutStr["result"][2])==true && $OutStr["result"][2]!="") {
			$Ct=$OutStr["result"][2];
			$Ct = round(($Ct-2700) *100/(6500-2700));
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
		$this->_returnOK = false;
        $this->On();
		$Id = 1;
		$string = sprintf($this->_commandCodes['CMD_TEMP_DOWN'],$Id);
		$this->send($string,$Id);
		$OutStr = $this->getStatus();
		if( isset($OutStr["result"][2])==true && $OutStr["result"][2]!="") {
			$Ct=$OutStr["result"][2];
			$Ct = round(($Ct-2700) *100/(6500-2700));
			return $Ct;
		}	
		return false;
	}
}
class W2_YeeLightJiaoyue650 extends W2_YeeLightBase
{

	public function OnMoon($value) {
		$this->On();
		$Id = 1;
		if ($value<1) $value=1;
		if ($value>100) $value=100;
		$string = sprintf($this->_commandCodes['CMD_MOON'],$Id, $value);
		return $this->send($string,$Id);
	}
  	public function OnKelvin($value=50) {
		$this->On();
		$Id = 1;
		$value = round($value*(6500-2710)/100 +2710);
		$string = sprintf($this->_commandCodes['CMD_CT'],$Id, $value);
		return $this->send($string,$Id);
	}
	public function OnWarm() {
		return $this->OnKelvin(5);
	}
	public function OnAmb() {
		$Id = 1;
		$string = sprintf($this->_commandCodes['CMD_ON_BG'],$Id);
		return $this->send($string,$Id);
	}
	public function OffAmb() {
		$Id =1;
		$string = sprintf($this->_commandCodes['CMD_OFF_BG'], $Id);
		return $this->send($string,$Id);
	}
	public function OnBrightnessAmb($value=0x0e,$Col) {
		$this->OnAmb();
		$Id = 1;
		if ($value<1) $value=1;
		if ($value>100) $value=100;
		$string = sprintf($this->_commandCodes['CMD_BRIGHTNESS_BG'],$Id, $value);
		return $this->send($string,$Id);
	}	
	public function OnColorAmb($color='Red',$Bright=0) {
		$this->OnAmb();
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
		if ($color == 'Random') {
			$color = (int)mt_rand(0,256*256*256-1);
		}
		else {
			$r = (int)hexdec(substr($color,1,2));
			$g = (int)hexdec(substr($color,3,2));
			$b = (int)hexdec(substr($color,5,2));
		}
		$this->_colorAmb= $color; 
		$Intcolor= $r*256*256+$g*256+$b;	
		
		$Id = 1;
		$string = sprintf($this->_commandCodes['CMD_RGB_BG'],$Id, $Intcolor);
		return $this->send($string,$Id);	
	}
     public function BrightnessIncreaseAmb($value=50, $Col="#000000", $white1 =0, $white2 =0) {
		$value=$value+$this->_increm;
		if ($value<0) $value=0;
		if ($value>100) $value=100;
		$this-> OnBrightnessAmb($value,$Col);
		return $value;
	}
	public function BrightnessDecreaseAmb($value=50,$Col="#000000", $white1 =0, $white2 =0) {
		$value=$value-$this->_increm;
		if ($value<0) $value=0;
		if ($value>100) $value=100;
		$this-> OnBrightnessAmb($value,$Col);
		return $value;
	}
	public function BrightnessIncreaseAmbInt($value=50) {
		$this->On();
		$Id = 1;
		$string = sprintf($this->_commandCodes['CMD_BRIGHT_UP_BG'],$Id);
		$this->send($string,$Id);
		$OutStr = $this->getStatus();
		if( isset($OutStr["result"][1])==true && $OutStr["result"][1]!="") {
				$Br=$OutStr["result"][1];
				return $Br;
		}
		return false;
	}
	public function BrightnessDecreaseAmbInt($value=50) {
		$this->On();
		$Id = 1;
		$string = sprintf($this->_commandCodes['CMD_BRIGHT_DOWN_BG'],$Id);
		$this->send($string,$Id);
		$OutStr = $this->getStatus();
		if( isset($OutStr["result"][1])==true && $OutStr["result"][1]!="") {
				$Br=$OutStr["result"][1];
				return $Br;
		}
		return false;
	}
	public function OnDisco($prg,$speed) {
		// 1 : Night reading
      	// 2 : Night
      	// 3 : Concentration
        // 4 : PC
		$this->_returnOK = false;	
		$this->On();
		$Id = 1;
		$string="";
      switch ($prg) {
        case 1 :
			$string = sprintf($this->_commandCodes['CMD_NIGHTR'],$Id);
          	break;
        case 2 :
			$string = sprintf($this->_commandCodes['CMD_READING'],$Id);
          	break;
        case 3 :
			$string = sprintf($this->_commandCodes['CMD_CONC'],$Id);
          	break;
        case 4 :
			$string = sprintf($this->_commandCodes['CMD_PC'],$Id);
          	break;
      	}
		return $this->send($string,$Id);
	}
	public function OnNight() {
		$this->On();
		$Id = 1;	
		$string = sprintf($this->_commandCodes['CMD_MOON'],$Id, 1);
		return $this->send($string,$Id);		
	}
  	public function DiscoSpeed($speed) {
      return -1;
    }
  	
}

?>