<?php
/*
*
*
* Copyright (c) 2016 Bernard Caron (User: bcaron https://www.jeedom.fr/forum/)
*
* Permission is hereby granted, free of charge, to any person obtaining a copy
* of this software and associated documentation files (the "Software"), to deal
* in the Software without restriction, including without limitation the rights
* to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
* copies of the Software, and to permit persons to whom the Software is
* furnished to do so, subject to the following conditions:
*
* The above copyright notice and this permission notice shall be included in
* all copies or substantial portions of the Software.
*
* API documentation
* Object creation: $light = new Milight('x.y.z.w',wait,repeat,port); 
		x.y.z.w is the wifi bridge IP address on the LAN
        wait : pause in ms between two orders
        repeat : number of repetitions of sending order
        port : do not modify     
* sources
	http://domoticz.com/forum/viewtopic.php?f=28&t=7097&p=47829#p47829
* protocol
Color  controller (370)
[0x56][R][G][B][0xaa]
R : red color 0->0xFF
G : Green color 0-> 0xFF
B : Blue color 0-> 0xFF

Dual White  controller (320)
[0x56][W1][W2][0xaa]
W1 : White 1 0->0xFF
W2 : White 2 0-> 0xFF

On :
[0xcc] [0x23] [0x33]

Off
[0xcc] [0x24] [0x33]

Status
[0xef] [0x01] [0x77]

response :
[0x66] [0x01] [ON/OFF] [User] [Preset] [Speed] [R/W1] [G/W2][B] [Type] [0x99]
Where :
[ON/OFF]  : 0x23/0x24
[User] 0x41 : user input levles/ any other : preset mode
[Preset] 0X20 : off / 0x21: on ([User] != 0x41)
[Speed] Speed for preset mode ([User] != 0x41)
[R/W1] Red (370)/White 1  (320) 0 -> 255
[G/W2] Green (370)/White 2  (320) 0 -> 255
[B] Blue (370)/ignored  (320) 0 -> 255
[Type] 02 : 370 / 01 : 320

* 1-2017 first version

Wifi320 WW
OnBrightnessWhite
OnBrightnessWhite2

Wifi370 RGB
OnBrightness
OnBrightnessWhite -> with 3 colors

*/
require_once dirname(__FILE__) . '/include/common.php';
class W2_wifi3Base
{

	protected $_host;
	protected $_port;
	protected $_wait;
	protected $_repeat;
	protected $_color = array(0,0,0); // rgb color
	protected $_increm;
	protected $_delay = 100; //microseconds
	protected $_commandCodes = array(0,0,0,0,0);
	protected $_return ;
	protected $_log;

	public function __construct($host = '192.168.1.110', $wait=0, $repeat=1, $increm=10, $ID = 0, $LocalId="", $nbLeds=0, $colorOrder=0,$port = 5577) {
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
			$repeat =4;
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
	
	//Used to dynamically call class methods
	public function call(){
		echo __METHOD__;
	}

	public function GetColor() {
		$hex = $this->rgb2hex($this->_color);
		//log::add($this->_log,'debug','Get color hex : '.$hex);
		return $hex;
	}

	public function SetColor($Col) {
		$this->_color=$this->hex2rgb($Col);
		return 0;
	}
	public function getStatus() {
		$this->_commandCodes[0] =0xef;
		$this->_commandCodes[1] =0x11;
		$this->_commandCodes[2] =0x77;
		return $this->Send(3,11);
	}
	protected function Send($size,$responseLength=0) {
		$message = vsprintf(str_repeat('%c', $size), $this->_commandCodes);
		$mess="";
		for ($iVal=0;$iVal<strlen($message);$iVal++){
			$mess=$mess.dechex($this->_commandCodes)." ";
		}
		log::add($this->_log,'debug','Commande : '.$mess);
		// Create a TCP/IP socket. 	
		$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		if ($socket === false) {
			log::add($this->_log,'debug',"socket_create() failed: reason: " .socket_strerror(socket_last_error()) );
			return NOSOCKET;
		}
		else {
			log::add($this->_log,'debug','socket_create() OK.');

			socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array("sec" => 0, "usec" => 100000));				
			socket_set_option($socket, SOL_SOCKET, SO_SNDTIMEO, array('sec' => 0, 'usec' => 100000));
			// switch to non-blocking
			socket_set_nonblock($socket);
			// store the current time
			$time = microtime(true);
			$result = true;
			log::add($this->_log,'debug',"try to connect to : "." ".$this->_host." ".$this->_port);
			$timeout= 0.5;
			$Ret = socket_connect($socket, $this->_host, $this->_port);
			while (!@socket_connect($socket, $this->_host, $this->_port)) {
				
				$err = socket_last_error($socket);
				// success!
				if($err === 56) {
					log::add($this->_log,'debug',"Connect OK");
					$result = true;
					break;
				}
				if ((microtime(true) - $time) >= $timeout) {
					socket_close($socket);
					log::add($this->_log,'debug',"time out ");
					$result = false;
					break;
				}
				usleep(50000);
			}	
			if ($result === false) {
				log::add($this->_log,'debug',"socket_connect() failed. Reason: ($result) " . socket_strerror(socket_last_error($socket)) );
				socket_close($socket);
				return NOTCONNECTED ;
			}
			else {			
				for ($iCount=0;$iCount<$this->_repeat;$iCount++) {
					$result = socket_write($socket,$message,strlen($message));
					if ($result === FALSE) {
						log::add($this->_log,'debug',"socket_write() failed. Reason: ($result) " . socket_strerror(socket_last_error($socket)) );
					}
					else {
						if ($responseLength > 0 ) {		
							$Icpt2=0;
							$out="";
							do {
								usleep(30000);
								$host=$this->_host;
								$port=$this->_port;
								socket_recvfrom($socket, $buf, 100,MSG_DONTWAIT, $host, $port);	
								$Icpt2++;
								$out=$out.$buf;
								//log::add($this->_log,'debug','Nbre received : '.strlen($out));
							} while ( strlen($out)<$responseLength && $Icpt2<10);
							if ( strlen($out)<$responseLength){
								$out[0]=0;	// incomplete response : ignore datagram
							}
							if ( strlen($out)>$responseLength){
								// sometimes the datagram contains more than one 0x81
								do {
									$out = substr ( $out , 1 );
								} while ( $out[0]!=0x66 &&  strlen($out)>$responseLength);
							}
							$mess="";
							for ($iVal=0;$iVal<strlen($out);$iVal++){
								$mess=$mess.dechex(ord($out[$iVal]))." ";
							}
							log::add($this->_log,'debug','return : '.$mess);	
							if ( strlen($out) == $responseLength &&	$out[0]!=0x66 ) {
								// complete datagram
								socket_close($socket);
								return $out;
								
							}
							// else try again

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
		$this->_commandCodes[1] = 0xcc;
		$this->_commandCodes[2] = 0x23;
		$this->_commandCodes[3] = 0x33;
		return $this->Send(3,0);
	}

	public function Off() {
		$this->_commandCodes[1] = 0xcc;
		$this->_commandCodes[2] = 0x24;
		$this->_commandCodes[3] = 0x33;
		return $this->Send(3,0);
	}
	public function OnMax() {
		$this->OnBrightnessWhite(100,'#000000');	
	}

	public function OnMin() {
		return $this->OnBrightnessWhite(1,'#000000',0);		
	}

	public function OnMid() {
		return $this->OnBrightnessWhite(50,'#000000',0);
	}
	public function OnNight() {
		$this->OnBrightnessWhite(5,'#000000',0);
	}
	
	public function OnWhite() {
		$this->OnBrightnessWhite(100,'#000000',0);
	}
	public function OnMax2() {
		$this->OnBrightnessWhite(100,'#000000',0);	
	}

	public function OnMin2() {
		return $this->OnBrightnessWhite(0,'#000000',1);		
	}

	public function OnMid2() {
		return $this->OnBrightnessWhite(0,'#000000',50);
	}
	public function OnNight2() {
		$this->OnBrightnessWhite(0,'#000000',5);
	}
	
	public function OnWhite2() {
		$this->OnBrightnessWhite(0,'#000000',100);
	}
	public function BrightnessIncrease($Intensity,$Color='#808080',$White1=50,$White2=50) {
		$value=$value+$this->_increm;
		if ($value<0) $value=0;
		if ($value>100) $value=100;
		$this-> OnBrightness($value,$Color,$White1,$White2);
		return $value;
	}
	public function BrightnessDecrease($Intensity,$Color='#808080',$White1=50,$White2=50) {
		$value=$value-$this->_increm;
		if ($value<0) $value=0;
		if ($value>100) $value=100;
		$this-> OnBrightness($value,$Col,$White1,$White2);
		return $value;
	}
	function OnBrightness($Intensity,$Color='#808080',$White1=50,$White2=50) {
		//log::add($this->_log,'debug','inyternsi');
		//log::add($this->_log,'debug','Col before intensity:'.$Color);
		$Col=$this->hex2rgb($Color);
		//log::add($this->_log,'debug','Col hex :'.$Col);
		$this->_commandCodes[1] = 0xeb;
		$Col[0]= round($Intensity*$Col[0]/100);
		$Col[1]= round($Intensity*$Col[1]/100);
		$Col[2]= round($Intensity*$Col[2]/100);	
		$hex = $this->rgb2hex($Col);
		//log::add($this->_log,'debug','Col after intensity :'.$hex);
		return $this->OnColor($hex,$Bright,$White1,$White2);
	}
	public function BrightnessW1Increase($Intensity,$Color='#808080',$White1=50,$White2=50) {
		$value=$value+$this->_increm;
		if ($value<0) $value=0;
		if ($value>100) $value=100;
		$this-> OnBrightnessWhite($value,$Color,$White1,$White2);
		return $value;
	}
	public function BrightnessW1Decrease($Intensity,$Color='#808080',$White1=50,$White2=50) {
		$value=$value-$this->_increm;
		if ($value<0) $value=0;
		if ($value>100) $value=100;
		$this-> OnBrightnessWhite($value,$Color,$White1,$White2);
		return $value;
	}
	public function OnBrightnessWhite($Intensity1=50,$color='#7FF7F7F',$Intensity2=50) {			
		$Intensity2=$Intensity2*255/100;
		if ($Intensity2>255) $Intensity2=255;
		if ($Intensity2<1) $Intensity2=1;
		$Intensity2=$Intensity2*255/100;
		if ($Intensity2>255) $Intensity2=255;
		if ($Intensity2<1) $Intensity2=1;
		//log::add($this->_log,'debug','ret from Status :');
		$col = $this->hex2rgb($color);
		$this->_commandCodes[0] = 0x56;
		$this->_commandCodes[1] = $Intensity1;
		$this->_commandCodes[2] = $Intensity2;
		$this->_commandCodes[3] = 0xaa;
		return $this->Send(4,0);
	}
	public function BrightnessW2Increase($Intensity,$Color='#808080',$White1=50,$White2=50) {
		$value=$value+$this->_increm;
		if ($value<0) $value=0;
		if ($value>100) $value=100;
		$this-> OnBrightnessWhite2($value,$Col,$White1,$White2);
		return $value;
	}
	public function BrightnessW2Decrease($Intensity,$Color='#808080',$White1=50,$White2=50) {
		$value=$value-$this->_increm;
		if ($value<0) $value=0;
		if ($value>100) $value=100;
		$this-> OnBrightnessWhite2($value,$Col,$White1,$White2);
		return $value;
	}
	public function OnBrightnessWhite2($Intensity2=50,$color='#7FF7F7F',$Intensity11=50) {	
		$Intensity2=$Intensity2*255/100;
		if ($Intensity2>255) $Intensity2=255;
		if ($Intensity2<1) $Intensity2=1;
		$Intensity2=$Intensity2*255/100;
		if ($Intensity2>255) $Intensity2=255;
		if ($Intensity2<1) $Intensity2=1;
		//log::add($this->_log,'debug','ret from Status :');
		$col = $this->hex2rgb($color);
		$this->_commandCodes[0] = 0x56;
		$this->_commandCodes[1] = $Intensity1;
		$this->_commandCodes[2] = $Intensity2;
		$this->_commandCodes[3] = 0xaa;
		return $this->Send(4,0);
	}
	public function OnColor($color='Mint',$Bright,$White1='#7FF7F7F',$White2='#7FF7F7F') {
		$this->_commandCodes[0] = 0x56;
		$color = (string)$color;		
		//log::add($this->_log,'debug','Color :'.$color);
		switch ($color) {
			case 'Random':		$this->_commandCodes[1] = (int)mt_rand(0,0xFF); $this->_commandCodes[2] = (int)mt_rand(0,0xFF);$this->_commandCodes[3] = (int)mt_rand(0,0xFF);break;
			case 'Violet':		$this->_commandCodes[1] = 0x9F; $this->_commandCodes[2] = 0x00;$this->_commandCodes[3] = 0xFF;break;
			case 'Blue':		$this->_commandCodes[1] = 0x00; $this->_commandCodes[2] = 0x00;$this->_commandCodes[3] = 0xFF;break;
			case 'BabyBlue':	$this->_commandCodes[1] = 0x89; $this->_commandCodes[2] = 0xCF;$this->_commandCodes[3] = 0xF0;break;
			case 'Aqua':		$this->_commandCodes[1] = 0x00; $this->_commandCodes[2] = 0xFF;$this->_commandCodes[3] = 0xFF;break;
			case 'Mint':		$this->_commandCodes[1] = 0x80; $this->_commandCodes[2] = 0xFF;$this->_commandCodes[3] = 0x80;break;
			case 'SpringGreen':	$this->_commandCodes[1] = 0x00; $this->_commandCodes[2] = 0xFF;$this->_commandCodes[3] = 0x7F;break;
			case 'Green':		$this->_commandCodes[1] = 0x00; $this->_commandCodes[2] = 0xFF;$this->_commandCodes[3] = 0x00;break;
			case 'LimeGreen':	$this->_commandCodes[1] = 0x32; $this->_commandCodes[2] = 0xcd;$this->_commandCodes[3] = 0x32;break;
			case 'Yellow':		$this->_commandCodes[1] = 0xFF; $this->_commandCodes[2] = 0xFF;$this->_commandCodes[3] = 0x00;break;
			case 'YellowOrange':$this->_commandCodes[1] = 0xFF; $this->_commandCodes[2] = 0xCC;$this->_commandCodes[3] = 0x00;break;
			case 'Orange':		$this->_commandCodes[1] = 0xFF; $this->_commandCodes[2] = 0x7F;$this->_commandCodes[3] = 0x00;break;
			case 'Red':			$this->_commandCodes[1] = 0xFF; $this->_commandCodes[2] = 0x00;$this->_commandCodes[3] = 0x00;break;
			case 'Pink':		$this->_commandCodes[1] = 0xFF; $this->_commandCodes[2] = 0xC0;$this->_commandCodes[3] = 0xDB;break;
			case 'Fuchsia':		$this->_commandCodes[1] = 0xFF; $this->_commandCodes[2] = 0x00;$this->_commandCodes[3] = 0xFF;break;
			case 'Lilac':		$this->_commandCodes[1] = 0xC8; $this->_commandCodes[2] = 0xA2;$this->_commandCodes[3] = 0xC8;break;
			case 'Lavendar':	$this->_commandCodes[1] = 0xE6; $this->_commandCodes[2] = 0xE6;$this->_commandCodes[3] = 0xFA;break;			
			case (substr($color,0,1)== "#"):
				$rgb= $this->hex2rgb($color);
				$this->_commandCodes[1]=$rgb[0];
				$this->_commandCodes[2]=$rgb[1];
				$this->_commandCodes[3]=$rgb[2];
				break;
			case ((int)$color >= 0x00) && ((int)$color <= 0xFFFFFF): 
				$x = (int)$color;
				$rgb= $this->hex2rgb($x);
				$this->_commandCodes[1]=$rgb[0];
				$this->_commandCodes[2]=$rgb[1];
				$this->_commandCodes[3]=$rgb[2]; 
				break;
			default:			
				$this->_commandCodes[1] = 0xFF; $this->_commandCodes[2] = 0xFF;$this->_commandCodes[3] = 0xFF;
				break;
				
		}
		$this->_color[0] = $this->_commandCodes[1];
		$this->_color[1] = $this->_commandCodes[2];
		$this->_color[2] = $this->_commandCodes[3];
		$this->_commandCodes[4]=0xaa;
		return $this->Send(5,0);
		//log::add($this->_log,'debug','Color memorized by MagicOnCOlor = '.$this->_color[0]." - ".$this->_color[1]." - ".$this->_color[2]);
	}

	function OnDisco($prg,$speed) {
		
		// 1:static state
		// 2:fullcolor
		// 3:MonoChromeShade
		// 4:MonochromeFade
		// 5:MonochromeLight
		// 6:MixedColorShade
		// 7:MixedColorFade
		// 8:MixedColorLight
		// 9:RedFade
		// 10:GreenFade
		// 11:BlueFade
		// 12:MonochromeTransition
		// 13:MixedColorTransition
		// 14:SevenColorTransition	
		if ($prg<0x1) {
			$prg=0x1;
		}

		if ($prg>=14) {
			$prg=14;
		}
		$this->_commandCodes[1] =0xec;
		$this->_commandCodes[2] =$prg;
		$this->_commandCodes[3] =1;
		$this->_commandCodes[3] = 0x0;
		$this->_commandCodes[4] = 0x0;
		$this->_commandCodes[5] = 0x0;
		$this->_commandCodes[6] = 0x0;
		return $this->Send(11,0);
	}
	function DiscoSpeed($speed,$prog=5) {
		return false;
	}
	public function DiscoMin($prog) {
		return false;
	}

	public function DiscoMid($prog) {
		return false;
	}

	public function DiscoMax($prog) {
		return $false;
	}
	
	public function DiscoSlower() {
		return $false;
	}

	public function DiscoFaster() {
		return $false;	
	}

	public function OnControl($value) {
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
}
class W2_wifi370 extends W2_wifi3Base
{
	public function OnBrightnessWhite($Intensity=50,$Color='#FFFFFF',$White1=0,$White2=0) {
		$Color='#FFFFFF';
		//log::add($this->_log,'debug','Col :'.$Color);
		$Col=$this->hex2rgb($Color);
		//log::add($this->_log,'debug','Col hex :'.$Color);
		$Col[0]= $Intensity*$Col[0]/100;
		$Col[1]= $Intensity*$Col[1]/100;
		$Col[2]= $Intensity*$Col[2]/100;	
		$hex = $this->rgb2hex($Col);
		//log::add($this->_log,'debug','Col :'.$hex);
		return $this->OnColor($hex,$Bright,$White1,$White2);
	}
	public function retStatus() {
		$OutStr = $this->getStatus();
		for($i = 0; $i < strlen($OutStr); $i++){
			$Out[$i] = str_pad(ord($OutStr[$i]), 2, '0', STR_PAD_LEFT);
        }
		if ( isset($OutStr) && $OutStr!=NOSOCKET && $OutStr!=NOTCONNECTED && $OutStr!=NORESPONSE ) {		
			if ($Out[0]==0x66 && $Out[1]==0x01) {
				// col from controller
				$Col[0]=$Out[6];
				$Col[1]=$Out[7];
				$Col[2]=$Out[8];
				$this->_return['Color'] = $this->rgb2hex($Col);
				/*				
				$prog=1;
				if ($Out[3]>0x63)
					$prog=0x63;
				if ($Out[3]>0x38 && $Out[3]<0x60) 
					$prog=0x63;
				if ($Out[3]>=0x60) 
					$prog=$Out[3]-0x27;
				if ($Out[3]>=0x25) 
					$prog=$Out[3]-0x24;
				if ($Out[3]<0x25) {
					$prog=1;
				}
				$this->_return['Prog'] = $prog;
				// now prog from 1 to 24
				log::add($this->_log, 'debug', 'Prog CTRL  : '.$Out[3]);
				log::add($this->_log, 'debug', 'Prog jeedom: '.$prog);

				$speed= round((33-$Out[5])*100/32);
				log::add($this->_log, 'debug', 'Speed CTRL  : '.$Out[5]);
				log::add($this->_log, 'debug', 'Speed jeedom : '.$speed);
				$this->_return['Speed'] = intval($speed);	
				*/				
				if ($Out[2]==0x23){
					$this->_return['On'] = 1;
				} 
				else if ($Out[2]==0x24){
					$this->_return['On'] = 0;
				}
				/*

				if ($Out[4]==0x20){
					$this->_return['Play'] = 0;
				} 
				else if ($Out[4]==0x21){
					$this->_return['Play'] = 1;
				}
				*/
			}
			else
				$this->_return['Type'] = 'Not a Wifi370 Controller';
		}
		else if ( !isset($OutStr) )
			$this->_return = BADRESPONSE;
		else
			$this->_return = $OutStr;
		return $this->_return;	
	}
}
class W2_wifi320 extends W2_wifi3Base
{
	public function retStatus() {
		$OutStr = $this->getStatus();
		for($i = 0; $i < strlen($OutStr); $i++){
			$Out[$i] = str_pad(ord($OutStr[$i]), 2, '0', STR_PAD_LEFT);
		}
		if ( isset($OutStr) && $OutStr!=NOSOCKET && $OutStr!=NOTCONNECTED && $OutStr!=NORESPONSE && $OutStr!=BADRESPONSE) {		
			if ($Out[0]==0x66 && $Out[1]==0x01) {
				// col from controller
				$this->_return['White'] = intval($Out[6]*100/255);
				$this->_return['White2'] = intval($Out[7]*100/255);
				/*
				$prog=1;
				if ($Out[3]>0x63)
					$prog=0x63;
				if ($Out[3]>0x38 && $Out[3]<0x60) 
					$prog=0x63;
				if ($Out[3]>=0x60) 
					$prog=$Out[3]-0x27;
				if ($Out[3]>=0x25) 
					$prog=$Out[3]-0x24;
				if ($Out[3]<0x25) {
					$prog=1;
				}
				$this->_return['Prog'] = $prog;
				// now prog from 1 to 24
				log::add($this->_log, 'debug', 'Prog CTRL  : '.$Out[3]);
				log::add($this->_log, 'debug', 'Prog jeedom: '.$prog);

				$speed= round((33-$Out[5])*100/32);
				log::add($this->_log, 'debug', 'Speed CTRL  : '.$Out[5]);
				log::add($this->_log, 'debug', 'Speed jeedom : '.$speed);
				$this->_return['Speed'] = intval($speed);	
				*/				
				if ($Out[2]==0x23){
					$this->_return['On'] = 1;
				} 
				else if ($Out[2]==0x24){
					$this->_return['On'] = 0;
				}
				/*

				if ($Out[4]==0x20){
					$this->_return['Play'] = 0;
				} 
				else if ($Out[4]==0x21){
					$this->_return['Play'] = 1;
				}
				*/
			}
			else
				$this->_return['Type'] = 'Not a Wifi320 Controller';
		}
		else if ( !isset($OutStr) )
			$this->_return = BADRESPONSE;
		else
			$this->_return = $OutStr;
		return $this->_return;	
	}
}
?>
