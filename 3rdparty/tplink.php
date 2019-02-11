 <?php
require_once dirname(__FILE__) . '/include/common.php';
// LB 100 110 120
// OnBrightness -> white
// LB 130
// OnBrightness -> color
// OnBrightnessWhite -> true white 
class W2_TpLinkHS100 extends W2_TpLinkLBBase
{	
	function getStatus() {
		$string = sprintf($this->_commandCodes['CMD_GET_PROP_SW']);
		return $this->send($string);
	}
	public function retStatus() {
		$OutStr = $this->getStatus();
		if ( isset($OutStr) && $OutStr!=NOSOCKET && $OutStr!=NOTCONNECTED && $OutStr!=NORESPONSE && $OutStr!=BADRESPONSE) {		
			if ($OutStr["err_code"]==0) {
				//"on_off":1,"mode":"normal","hue":180,"saturation":100,"color_temp":0,"brightness":35,"err_code":		
				if( isset($OutStr["relay_state"])) {		
					$this->_return['On'] = $OutStr["relay_state"];
				}					
			}
			else
				$this->_return['Type'] = 'Not a SW100 TP-Link plug';
		}
		else if ( !isset($OutStr) )
			$this->_return = BADRESPONSE;
		else
			$this->_return = $OutStr;
		return $this->_return;	
	}	
	public function On() {
		$string = sprintf($this->_commandCodes['CMD_ON_SW']);
		return $this->send($string,"");
	}
	public function Off() {
		$string = sprintf($this->_commandCodes['CMD_OFF_SW']);
		return $this->send($string,"");
	}
	public function OnLed() {
		$string = sprintf($this->_commandCodes['CMD_ON_LED']);
		return $this->send($string,"");
	}
	public function OffLed() {
		$string = sprintf($this->_commandCodes['CMD_OFF_LED']);
		return $this->send($string,"");
	}
}
class W2_TpLinkHS110 extends W2_TpLinkHS100
{	
	public function ErasePow() {
		$string = sprintf($this->_commandCodes['CMD_ERASE_POW']);
		return $this->send($string,"");
	}
	function getStatus() {
		$string = sprintf($this->_commandCodes['CMD_GET_PROP_SW']);
		return $this->send($string);
	}
	function getPower() {
		$string = sprintf($this->_commandCodes['CMD_GET_POW_SW']);
		return $this->send($string);
	}
	public function retPower() {
		$OutStr = $this->getStatus();
		if ( isset($OutStr) && $OutStr!=NOSOCKET && $OutStr!=NOTCONNECTED && $OutStr!=NORESPONSE && $OutStr!=BADRESPONSE) {		
			if ($OutStr["err_code"]==0) {
				//"on_off":1,"mode":"normal","hue":180,"saturation":100,"color_temp":0,"brightness":35,"err_code":		
				if( isset($OutStr["relay_state"])) {		
					$this->_return['On'] = $OutStr["relay_state"];
				}
					//cunsomption
					$OutStr = $this->getPower();
					if ( isset($OutStr) && $OutStr!=NOSOCKET && $OutStr!=NOTCONNECTED && $OutStr!=NORESPONSE && $OutStr!=BADRESPONSE) {		
						if ($OutStr["err_code"]==0) {
							 // "get_realtime": {"err_code": 0,"voltage": w, current": x, "power": y,"total": z}
						
							if( isset($OutStr["power"])) {		
								$this->_return['Power'] = $OutStr["power"];
							}
							if( isset($OutStr["voltage"])) {		
								$this->_return['Voltage'] = $OutStr["voltage"];
							}
							if( isset($OutStr["current"])) {		
								$this->_return['Current'] = $OutStr["current"];
							}
							if( isset($OutStr["total"])) {		
								$this->_return['Consommation'] = $OutStr["total"];
							}							
						}
						else
							$this->_return['Type'] = 'Not a SW110 TP-Link plug';
					}
					else if ( !isset($OutStr) )
						$this->_return = BADRESPONSE;
					else
						$this->_return = $OutStr;





				
			}
			else
				$this->_return['Type'] = 'Not a SW110 TP-Link plug';
		}
		else if ( !isset($OutStr) )
			$this->_return = BADRESPONSE;
		else
			$this->_return = $OutStr;
		return $this->_return;	
	}
}
class W2_TpLinkLBBase
{

	//TpLink Bulbs commands
	private $_host;
	private $_port;
	private $_wait;
	private $_repeat;
	private $_repeatOK;
	private $_color = "#000000";
	protected $_returnOK = true;
	private $_increm;
	private $_delay = 101000; //microseconds
	protected $_return;
	protected $_log;
	protected $_commandCodes = array( 
	'CMD_ON' => '{"smartlife.iot.smartbulb.lightingservice":{"transition_light_state":{"on_off":1,"transition_period":150}}}',
	'CMD_OFF' => '{"smartlife.iot.smartbulb.lightingservice":{"transition_light_state":{"on_off":0,"transition_period":150}}}',
    'CMD_CT' => '{"smartlife.iot.smartbulb.lightingservice":{"transition_light_state":{"color_temp":%d,"on_off":1,"transition_period":150}}}',
	'CMD_WHITE' => '{"smartlife.iot.smartbulb.lightingservice":{"transition_light_state":{"ignore_default":1, "color_temp":4500,"transition_period":150}}}',
    'CMD_HSV' => '{"smartlife.iot.smartbulb.lightingservice":{"transition_light_state":{"ignore_default":1,"color_temp":0,"hue":%d,"saturation":%d,"brightness":%d,"on_off":1,"transition_period":150}}}',
    'CMD_BRIGHTNESS' => '{"smartlife.iot.smartbulb.lightingservice":{"transition_light_state":{"brightness":%d,"on_off":1,"transition_period":150}}}',
	'CMD_CIRCADIAN' => '{"smartlife.iot.smartbulb.lightingservice":{"transition_light_state":{"mode":"circadian","transition_period":0}}}',
	'CMD_GET_PROP' => '{"smartlife.iot.smartbulb.lightingservice":{"get_light_state":{}}}',
	'CMD_ON_SW' => '{"system":{"set_relay_state":{"state":1}}}',
	'CMD_OFF_SW' => '{"system":{"set_relay_state":{"state":0}}}',
	'CMD_GET_PROP_SW' => '{"system":{"get_sysinfo":{}}}',
	'CMD_GET_POW_SW' => '{"emeter":{"get_realtime":{}}}',
	'CMD_ON_LED' => '{"system":{"set_led_off":{"state":1}}}',
	'CMD_OFF_LED' => '{"system":{"set_led_off":{"state":0}}}',
	'CMD_ERASE_POW' => '{"emeter":{"erase_emeter_stat":null}}'
	);
	//Used to dynamically call class methods
	public function call(){
		echo __METHOD__;
	}
	public function encrypt ($buffer,$start="", $key = 0xAB) {
		$bufferOut=$start;
		for ($i = 0; $i < strlen($buffer); $i++) {
		  $c = ord($buffer[$i])^ $key;
		  $bufferOut=$bufferOut.chr($c);
		  $key=$c;
		}
		return $bufferOut;
	}
	public function decrypt ($buffer,$start="", $key = 0xAB) {
		$bufferOut=$start;
		for ($i = 0; $i < strlen($buffer); $i++) {
		  $c = ord($buffer[$i])^ $key;
		  $bufferOut=$bufferOut.chr($c);
		  $key=ord($buffer[$i]);
		}
		return $bufferOut;
	}	
	public function __construct($host = '192.168.1.110', $wait=0, $repeat=1, $increm=10, $ID=0, $LocalId="", $nbLeds=0, $colorOrder=0,$port = 9999) {
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
		$myRet = new wifilightV2c;
		$this->_return = $myRet->_return_WFL;
		$this->_return['Type'] = true;
		$this->_log = $myRet->_log;
	}
	public function GetColor() {
		return $this->_color;
	}
	function getStatus() {
		$string = sprintf($this->_commandCodes['CMD_GET_PROP']);
		return $this->send($string);
	}

	protected function send($command,$Id="") {
		log::add($this->_log,'debug','Commande : '.$command);
		$message = $this->encrypt($command);
		// Create a TCP/IP socket.		
		$socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
		//$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

		if ($socket === false) {
			log::add($this->_log,'debug',"socket_create() failed: reason: " .socket_strerror(socket_last_error()) );
			return NOSOCKET;
		}
		else {
			log::add($this->_log,'debug',"try to connect to : $this->_host  $this->_port");
			socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array("sec" => 0, "usec" => 250000));
			socket_set_option($socket, SOL_SOCKET,SO_SNDTIMEO, array("sec" => 0, "usec" => 250000));				
			//socket_set_nonblock($socket);
			// store the current time
			$time = microtime(true);
			$result = true;
			$timeout= 1;
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
				for ($iCount=0;$iCount<$this->_repeat;$iCount++) {
				socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array("sec" => 0, "usec" => 250000));
				socket_set_option($socket, SOL_SOCKET,SO_SNDTIMEO, array("sec" => 0, "usec" => 250000));
					$ret = socket_write($socket,$message,strlen($message));	
					if ($ret === false ) {
						log::add($this->_log,'debug',"socket_write() failed. Reason: ($ret) " .socket_strerror(socket_last_error()) );							
					}
					else {
						if ($this->_returnOK == false) {
							return NOSTATE;
						}
						else {							
							socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array("sec" => 0, "usec" => 300000));							
							$out = socket_read($socket, 500);
							if ( isset($out) && $out !== FALSE && $out !="" ) {
								$out2 = $this->decrypt ($out);			 
								$json_decoded_data = json_decode($out2, true);
								if ( ($json_decoded_data != NULL) && ($json_decoded_data != FALSE)){
									if (isset($json_decoded_data["smartlife.iot.smartbulb.lightingservice"]["get_light_state"])) {
										foreach ($json_decoded_data["smartlife.iot.smartbulb.lightingservice"]["get_light_state"] as $key2 => $value2) {
											if (is_array ($value2)) 
												$value2 = implode($value2);					
											//log::add($this->_log,'debug',">> : $key2 | ".$value2);
										}
										socket_close($socket);
										return $json_decoded_data["smartlife.iot.smartbulb.lightingservice"]["get_light_state"];
									}
									if (isset($json_decoded_data["smartlife.iot.smartbulb.lightingservice"]["transition_light_state"])) {
										foreach ($json_decoded_data["smartlife.iot.smartbulb.lightingservice"]["transition_light_state"] as $key2 => $value2) {
											if (is_array ($value2)) 
												$value2 = implode($value2);					
											//log::add($this->_log,'debug',">> : $key2 | ".$value2);
										}
										socket_close($socket);
										return $json_decoded_data["smartlife.iot.smartbulb.lightingservice"]["transition_light_state"];
									}
									if (isset($json_decoded_data["system"]["get_sysinfo"])) {
										foreach ($json_decoded_data["system"]["get_sysinfo"] as $key2 => $value2) {
											if (is_array ($value2)) 
												$value2 = implode($value2);					
											//log::add($this->_log,'debug',">> : $key2 | ".$value2);
										}
										socket_close($socket);
										return $json_decoded_data["system"]["get_sysinfo"];
									}
									if (isset($json_decoded_data["emeter"]["get_realtime"])) {
										foreach ($json_decoded_data["emeter"]["get_realtime"] as $key2 => $value2) {
											if (is_array ($value2)) 
												$value2 = implode($value2);					
											//log::add($this->_log,'debug',">> : $key2 | ".$value2);
										}
										socket_close($socket);
										return $json_decoded_data["emeter"]["get_realtime"];
									}
								}
								log::add($this->_log,'debug',"json fails" );
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
	public function On() {
		$string = sprintf($this->_commandCodes['CMD_ON']);
		return $this->send($string,"");
	}
	public function Off() {
		$string = sprintf($this->_commandCodes['CMD_OFF']);
		return $this->send($string,"");
	}	
	public function OnMax() {
		$string = sprintf($this->_commandCodes['CMD_BRIGHTNESS'],100);
		return $this->send($string);
	}
	public function OnMin() {

		$string = sprintf($this->_commandCodes['CMD_BRIGHTNESS'],5);
		return $this->send($string);
	}
	public function OnMid() {
		$string = sprintf($this->_commandCodes['CMD_BRIGHTNESS'],50);
		return $this->send($string);
	}

	public function OnNight() {	
		$string = sprintf($this->_commandCodes['CMD_BRIGHTNESS'],1);
		return $this->send($string);		
	}
	public function OnColor($color='Red',$Bright=100) {
		$this->On();
		switch ($color) {
			case 'Random':		 
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
          	$r = (int)mt_rand(0,255);
			$g = (int)mt_rand(0,255);
			$b = (int)mt_rand(0,255);
		}
		else {
            $r = (int)hexdec(substr($color,1,2));
            $g = (int)hexdec(substr($color,3,2));
            $b = (int)hexdec(substr($color,5,2));
        }
		$Bright=($Bright+6)/106;
      	$r=(int)$r*($Bright/100);
		$g=(int)$g*($Bright/100);
        $b=(int)$b*($Bright/100);
		$this->_color= $color; 
		$hsv = $this->RGB2HSV ($r, $g, $b);
		$string = sprintf($this->_commandCodes['CMD_HSV'],$hsv['H'],$hsv['S'],$hsv['V']);
		return $this->send($string);
      
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
		return $this->send($string);	
	}
	public function BrightnessIncrease($value=50,$Col) {
		$value=$value+$this->_increm;
		if ($value<0) $value=0;
		if ($value>100) $value=100;
		$this-> OnBrightness($value,$Col);
		return $value;
	}
	public function BrightnessDecrease($value=50,$Col) {
		$value=$value-$this->_increm;
		if ($value<0) $value=0;
		if ($value>100) $value=100;
		$this-> OnBrightness($value,$Col);
		return $value;
	}
	public function OnBrightness($value=0x0e,$Col) {
		$this->On();
		$status= $this->retStatus();
		if( isset($status["Kelvin"]) ) {
			$Ct = $status["Kelvin"];
			if ($value<0) $value=0;
			if ($value>100) $value=100;
			if ($Ct==-1) { //color mode -> send only brightness
				$string = sprintf($this->_commandCodes['CMD_BRIGHTNESS'],$value);
				$ret = $this->send($string);
			} 
			else { // in white mode have to go to color mode: use color from state
				$Col=$status['Color'];
				$ColCtrl = $this->hex2rgb($Col);							
				$max=max($ColCtrl);
				if ($max>=1) {
					 // saturates one of the colors as other controller does
					 
					$intensity=255/$max * $value/100; //(0->1)
					$ColCtrl[0]=round($ColCtrl[0]*$intensity);
					$ColCtrl[1]=round($ColCtrl[1]*$intensity);
					$ColCtrl[2]=round($ColCtrl[2]*$intensity);
					$ret = $this->OnColor($ColCtrl);
				}
				else {
					$ColCtrl[0]=$Col;
					$ColCtrl[1]=$Col;
					$ColCtrl[2]=$Col;
					$ret = $this->OnColor($ColCtrl,$value);					
				}
			}
		}
		return $ret;	
	}
	public function BrightnessW1Increase($value=50,$Col) {
		$value=$value+$this->_increm;
		if ($value<0) $value=0;
		if ($value>100) $value=100;
		$this-> OnBrightnessWhite($value,$Col);
		return $value;
	}
	public function BrightnessW1Decrease($value=50,$Col) {
		$value=$value-$this->_increm;
		if ($value<0) $value=0;
		if ($value>100) $value=100;
		$this-> OnBrightnessWhite($value,$Col);
		return $value;
	}
	public function OnBrightnessWhite($value=0x0e,$Col) {
		$this->On();
		$status= $this->retStatus();
		if( isset($status["Kelvin"]) ) {
			$Ct = $status["Kelvin"];
			if ($value<1) $value=1;
			if ($value>100) $value=100;
			if ($Ct==-1) { // in color mode goto white send first CT to go to white 
				$this->OnLukeWarm(); // default temp as it is not memorized
				$string = sprintf($this->_commandCodes['CMD_BRIGHTNESS'],$value);
				$ret = $this->send($string);
			}
			else {
				$string = sprintf($this->_commandCodes['CMD_BRIGHTNESS'],$value);
				$ret = $this->send($string);
			}
		}
		return $ret;
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
	public function OnKelvin($value=50) {
		$this->On();
		$Id = 1;
		$value = round($value*(8000-2500)/100 +2500);
		$string = sprintf($this->_commandCodes['CMD_CT'],$value);
		return $this->send($string);
	}
	public function rgb2hex($rgb) {
	   $hex = "#";
	   $hex .= str_pad(dechex($rgb[0]), 2, "0", STR_PAD_LEFT);
	   $hex .= str_pad(dechex($rgb[1]), 2, "0", STR_PAD_LEFT);
	   $hex .= str_pad(dechex($rgb[2]), 2, "0", STR_PAD_LEFT);
	   return $hex; // returns the hex value including the number sign (#)
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
	public function HSV2RGB(array $hsv) {
		list($H,$S,$V) = $hsv;
		$H=$H/360;
		$S=$S/100;
		$V=$V/100;
		//1
		$H *= 6;
		//2
		$I = floor($H);
		$F = $H - $I;
		//3
		$M = $V * (1 - $S);
		$N = $V * (1 - $S * $F);
		$K = $V * (1 - $S * (1 - $F));
		//4
		switch ($I) {
			case 0:
				list($R,$G,$B) = array($V,$K,$M);
				break;
			case 1:
				list($R,$G,$B) = array($N,$V,$M);
				break;
			case 2:
				list($R,$G,$B) = array($M,$V,$K);
				break;
			case 3:
				list($R,$G,$B) = array($M,$N,$V);
				break;
			case 4:
				list($R,$G,$B) = array($K,$M,$V);
				break;
			case 5:
			case 6: //for when $H=1 is given
				list($R,$G,$B) = array($V,$M,$N);
				break;
		}
		$R=$R*255;
		$G=$G*255;
		$B=$B*255;
		return array($R, $G, $B);
	}
}
// white no kelvin
class W2_TpLinkLB100 extends W2_TpLinkLBBase
{
	public function retStatus() {
		if ( isset($OutStr) && $OutStr!=NOSOCKET && $OutStr!=NOTCONNECTED && $OutStr!=NORESPONSE && $OutStr!=BADRESPONSE) {		
			if ($OutStr["err_code"]==0) {
				//"on_off":1,"mode":"normal","hue":180,"saturation":100,"color_temp":0,"brightness":35,"err_code":		
				if( isset($OutStr["on_off"])==true) {		
					$this->_return['On'] = $OutStr["on_off"];
				}	
				if( isset($OutStr["brightness"]) ) {
					$this->_return['White']= $OutStr["brightness"];
				} 				
			}
			else
				$this->_return['Type'] = 'Not a LB100 TP-Link bulb';
		}
		else if ( !isset($OutStr) )
			$this->_return = BADRESPONSE;
		else
			$this->_return = $OutStr;
		return $this->_return;	
	}	
}
// white no kelvin
class W2_TpLinkLB110 extends W2_TpLinkLBBase
{
	public function retStatus() {
		$OutStr = $this->getStatus();
		//"on_off":1,"mode":"normal","hue":180,"saturation":100,"color_temp":0,"brightness":35,"err_code":0}
      	if ( isset($OutStr) && $OutStr!=NOSOCKET && $OutStr!=NOTCONNECTED && $OutStr!=NORESPONSE && $OutStr!=BADRESPONSE) {		
			if ($OutStr["err_code"]==0) {
				//"on_off":1,"mode":"normal","hue":180,"saturation":100,"color_temp":0,"brightness":35,"err_code":		
				if( isset($OutStr["on_off"])==true) {		
					$this->_return['On'] = $OutStr["on_off"];
				}	
				if( isset($OutStr["brightness"]) ) {
					$this->_return['White']= $OutStr["brightness"];
				} 				
			}
			else
				$this->_return['Type'] = 'Not a LB110 TP-Link bulb';
		}
		else if ( !isset($OutStr) )
			$this->_return = BADRESPONSE;
		else
			$this->_return = $OutStr;
		return $this->_return;	
	}	
}
// white + kelvin
class W2_TpLinkLB120 extends W2_TpLinkLBBase
{
	public function retStatus() {
		$OutStr = $this->getStatus();
		if ( isset($OutStr) && $OutStr!=NOSOCKET && $OutStr!=NOTCONNECTED && $OutStr!=NORESPONSE && $OutStr!=BADRESPONSE) {		
			if ($OutStr["err_code"]==0) {
				//"on_off":1,"mode":"normal","hue":180,"saturation":100,"color_temp":0,"brightness":35,"err_code":		
				if( isset($OutStr["on_off"])) {		
					$this->_return['On'] = $OutStr["on_off"];
				}	
				if( isset($OutStr["color_temp"])) {
					$Ct = $OutStr["color_temp"];
					$this->_return['White']= $OutStr["brightness"];
					$Ct = round(($Ct-2500) /(8000-2500)*100) ;				
					$this->_return['Kelvin'] = $Ct;
				} 				
			}
			else
				$this->_return['Type'] = 'Not a LB120 TP-Link bulb';
		}
		else if ( !isset($OutStr) )
			$this->_return = BADRESPONSE;
		else
			$this->_return = $OutStr;
		return $this->_return;	
	}	
}
// color + white + kelvin
class W2_TpLinkLB130 extends W2_TpLinkLBBase
{
	public function retStatus() {
		$OutStr = $this->getStatus();
		if ( isset($OutStr) && $OutStr!=NOSOCKET && $OutStr!=NOTCONNECTED && $OutStr!=NORESPONSE && $OutStr!=BADRESPONSE) {	
			if ($OutStr["err_code"]==0) {
				//"on_off":1,"mode":"normal","hue":180,"saturation":100,"color_temp":0,"brightness":35,"err_code":		
				if( isset($OutStr["on_off"]) && $OutStr["on_off"]!="") {		
					$this->_return['On'] = $OutStr["on_off"];
				}	
				if( isset($OutStr["color_temp"]) ) {
					$Ct = $OutStr["color_temp"];
					if ($Ct==0) { //color mode
						if( isset($OutStr["saturation"]) ) {
							$this->_return['white'] =0;
							$Hue=$OutStr["hue"];
							$this->_return['Intensity']=$OutStr["brightness"];
							$Sat=$OutStr["saturation"];
							$Col= $this->HSV2RGB(array ($Hue,$Sat,100)); // full color
							$this->_return['Color'] = $this->rgb2hex($Col);
						}
					} 
					else {
						$this->_return['White']= $OutStr["brightness"];
						$Ct = round(($Ct-2500) /(8000-2500)*100) ;				
						$this->_return['Kelvin'] = $Ct;
						$this->_return['Color'] = '#000000';
						if( isset($OutStr["saturation"]) ) {
							$Hue=$OutStr["hue"];
							$this->_return['Intensity']=0;
							$Sat=$OutStr["saturation"];
							$Col= $this->HSV2RGB(array ($Hue,$Sat,100));  //full color sent
							$this->_return['Color'] = $this->rgb2hex($Col);
						}
					}
				} 				
			}
			else
				$this->_return['Type'] = 'Not a LB130 TP-Link bulb';
		}
		else if ( !isset($OutStr) )
			$this->_return = BADRESPONSE;
		else
			$this->_return = $OutStr;

		return $this->_return;	
	}	
	public function OnWhite() {	
		$string = sprintf($this->_commandCodes['CMD_WHITE']);	
		return $this->send($string);		
	}
	function OnDisco($prg) {
		$string = sprintf($this->_commandCodes['CMD_CIRCADIAN']);	
		return $this->send($string);			
	}
}
?>