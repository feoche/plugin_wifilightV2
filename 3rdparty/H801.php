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
* https://github.com/zoot1612/plugin_mh/blob/master/MH_API.txt


* Object creation: $light = new Milight('x.y.z.w',wait,repeat,port,ID); 
		x.y.z.w is the wifi bridge IP address on the LAN
        wait : pause in ms between two orders
        repeat : number of repetitions of sending order
        port : do not modify
        ID : part of the mac address of the controller
        
* sources
	https://www.domoticz.com/forum/viewtopic.php?f=51&t=7957&sid=9f52fd41ff9965a40199b74d3bed5a85&start=20
	https://w.wol.ph/2016/09/16/controlling-esp8266-h801-wifi-controller-python/
* protocol
Color and white modes
[0Xfb][0xeb][R][G][B][W1][W2][Mac address][00]
R : red color 0->0xFF
G : Green color 0-> 0xFF
B : Blue color 0-> 0xFF
W1: first white channel color 0-> 0xFF
W2: second white channel color 0-> 0xFF
Mac address : 3 bytes in reverse order [Mac6][Mac5][Mac4]

Scene modes :[0Xfb][0xec][Code][0x01][0x00][0x00][0x00][Mac address][00]
  Code :
  0 : StaticState
  1 : FullColor
  2 : MonochromeShade
  3 : MonochromeFade
  4 : MonoChromeLight
  5 : MixedColorshade
  6 : MixedColorFade
  7 : MixedColorLight
  8 : RedFade
  9 : GreenFade
  10 : BlueFade
  11 : MonoChromeTransition
  12 : MixedColorTransition
  13 : SevenColorTransition


* 1-2017 first version

OnBrightness -> color
OnBrightnessWhite -> white with 3 colors

*/
require_once dirname(__FILE__) . '/include/common.php';
class W2_H801
{
	protected $_host;
	protected $_port;
	protected $_wait;
	protected $_repeat;
	protected $_increm;
	protected $_color = array(0,0,0); // rgb color
	protected $_delay = 10000; //microseconds
	protected $_return ;
	protected $_log;
	protected $_commandCodes = array(0xfb,0x00,0,0,0,0,0,0,0,0,0);

	public function __construct($host = '192.168.1.110', $wait=0, $repeat=1, $increm=10, $ID = 0, $LocalId="", $nbLeds=0, $colorOrder=0, $port = 30977) {
		$this->_host = $host;
		$this->_port = 30977;
		$this->_commandCodes[7]= strval(hexdec(substr($ID,4,2)));
		$this->_commandCodes[8]= strval(hexdec(substr($ID,2,2)));
		$this->_commandCodes[9]= strval(hexdec(substr($ID,0,2)));
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
		$this->_log = $myRet->_log;
	}
	
	//Used to dynamically call class methods
	public function call(){
		echo __METHOD__;
	}

	public function GetColor() {
		$hex = $this->rgb2hex($this->_color);
		return $hex;
	}

	public function SetColor($Col) {
		$this->_color=$this->hex2rgb($Col);
		return 0;
	}
	
	public function getStatus() {
		return false;
	}
	public function retStatus() {

		return $this->_return;	
	}

	protected function Send($size,$responseLength=0) {
		$message = vsprintf(str_repeat('%c', $size), $this->_commandCodes);
		$mess="";
		for ($iVal=0;$iVal<strlen($message);$iVal++){
			$mess=$mess.dechex($this->_commandCodes[$iVal])." ";
		}
		log::add($this->_log,'debug','Commande : '.$mess);
		$socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
		if ($socket === false) {
			log::add($this->_log,'debug',"socket_create() failed: reason: " .socket_strerror(socket_last_error()) );
			return NOSOCKET;
		}
		else {
			log::add($this->_log,'debug','socket_create() OK.');			
			socket_set_option($socket, SOL_SOCKET, SO_SNDTIMEO, array('sec' => 0, 'usec' => 500000));
			// switch to non-blocking
			socket_set_nonblock($socket);
			// store the current time
			$time = microtime(true);
			$result = true;
			log::add($this->_log,'debug',"try to connect to : "." ".$this->_host." ".$this->_port);
			$timeout= 0.5;
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
				usleep(10000);
			}	
			if ($result === false) {
				log::add($this->_log,'debug',"socket_connect() failed. Reason: ($result) " . socket_strerror(socket_last_error($socket)) );
				socket_close($socket);
				return NOTCONNECTED ;
			}
			else {			
				log::add($this->_log,'debug','try to Send');	
				socket_set_option($socket, SOL_SOCKET, SO_SNDTIMEO, array('sec' => 0, 'usec' => 500000));
				for ($iCount=0;$iCount<$this->_repeat;$iCount++) {
					$result = socket_write($socket,$message,strlen($message));
					if ($result === FALSE) {
						log::add($this->_log,'debug',"socket_write() failed. Reason: ($result) " . socket_strerror(socket_last_error($socket)) );
					}			
                  	usleep($this->_delay);
					usleep($this->_wait);	
				}		
				socket_close($socket);
				log::add($this->_log, 'debug', "return : ".$result);
              	if ($result === FALSE ) return BADRESPONSE;
              	return SUCCESS;
			}
		}
	}			
	
	public function On() {
		$this->_commandCodes[1] = 0xeb;
		$this->_commandCodes[2] = 0x7f;
		$this->_commandCodes[3] = 0x7f;
		$this->_commandCodes[4] = 0x7f;
		$this->_commandCodes[5] = 0x7f;
		$this->_commandCodes[6] = 0x7f;
		return $this->Send(11,0);
	}

	public function Off() {
		$this->_commandCodes[1] = 0xeb;
		$this->_commandCodes[2] = 0x0;
		$this->_commandCodes[3] = 0x0;
		$this->_commandCodes[4] = 0x0;
		$this->_commandCodes[5] = 0x0;
		$this->_commandCodes[6] = 0x0;
		return $this->Send(11,0);
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
	public function BrightnessIncrease($value=50) {
		$value=$value+$this->_increm;
		if ($value<0) $value=0;
		if ($value>100) $value=100;
		$this-> OnBrightness($value);
		return $value;
	}
	public function BrightnessDecrease($value=50) {
		$value=$value-$this->_increm;
		if ($value<0) $value=0;
		if ($value>100) $value=100;
		$this-> OnBrightness($value);
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
		return $this->OnColor($hex,$Bright,$White1,$White2);
	}
	public function BrightnessW1Increase($value=50,$color='#7FF7F7F',$Intensity1=50) {
		$value=$value+$this->_increm;
		if ($value<0) $value=0;
		if ($value>100) $value=100;
		$this-> OnBrightnessWhite($value,$color,$Intensity1);
		return $value;
	}
	public function BrightnessW1Decrease($value=50,$color='#7FF7F7F',$Intensity1=50) {
		$value=$value-$this->_increm;
		if ($value<0) $value=0;
		if ($value>100) $value=100;
		$this-> OnBrightnessWhite($value,$color,$Intensity1);
		return $value;
	}
	public function OnBrightnessWhite($Intensity1=50,$color='#7FF7F7F',$Intensity2=50) {			
		$Intensity2=intval($Intensity2*255/100);
		if ($Intensity2>255) $Intensity2=255;
		if ($Intensity2<1) $Intensity2=1;
		$Intensity1=intval($Intensity1*255/100);
		if ($Intensity1>255) $Intensity1=255;
		if ($Intensity1<1) $Intensity1=1;
		$col = $this->hex2rgb($color);
		$this->_commandCodes[1] = 0xeb;
		$this->_commandCodes[2] = $col[0];
		$this->_commandCodes[3] = $col[1];
		$this->_commandCodes[4] = $col[2];
		$this->_commandCodes[5] = $Intensity1;
		$this->_commandCodes[6] = $Intensity2;
		return $this->Send(11,0);
	}
	public function BrightnessW2Increase($value=50,$color='#7FF7F7F',$Intensity1=50) {
		$value=$value+$this->_increm;
		if ($value<0) $value=0;
		if ($value>100) $value=100;
		$this-> OnBrightnessWhite2($value,$color,$Intensity1);
		return $value;
	}
	public function BrightnessW2Decrease($value=50,$color='#7FF7F7F',$Intensity1=50) {
		$value=$value-$this->_increm;
		if ($value<0) $value=0;
		if ($value>100) $value=100;
		$this-> OnBrightnessWhite2($value,$color,$Intensity1);
		return $value;
	}
	public function OnBrightnessWhite2($Intensity2=50,$color='#7FF7F7F',$Intensity1=50) {	
		$Intensity2=intval($Intensity2*255/100);
		if ($Intensity2>255) $Intensity2=255;
		if ($Intensity2<1) $Intensity2=1;
		$Intensity1=intval($Intensity1*255/100);
		if ($Intensity1>255) $Intensity1=255;
		if ($Intensity1<1) $Intensity1=1;
		$col = $this->hex2rgb($color);
		$this->_commandCodes[1] = 0xeb;
		$this->_commandCodes[2] = $col[0];
		$this->_commandCodes[3] = $col[1];
		$this->_commandCodes[4] = $col[2];
		$this->_commandCodes[5] = $Intensity1;
		$this->_commandCodes[6] = $Intensity2;
		return $this->Send(11,0);
	}

	public function OnColor($color='Mint',$Bright,$White1='#7FF7F7F',$White2='#7FF7F7F') {
		//log::add($this->_log,'debug','Color :'.$color,' White1 :'.$White1,' White2 :'.$White2);
		$this->_commandCodes[1] = 0xeb;
		$this->_commandCodes[5] = round($White1*255/100);
		$this->_commandCodes[6] = round($White2*255/100);
		$color = (string)$color;		

		switch ($color) {
			case 'Random':		$this->_commandCodes[2] = (int)mt_rand(0,0xFF); $this->_commandCodes[3] = (int)mt_rand(0,0xFF);$this->_commandCodes[4] = (int)mt_rand(0,0xFF);break;
			case 'Violet':		$this->_commandCodes[2] = 0x9F; $this->_commandCodes[3] = 0x00;$this->_commandCodes[4] = 0xFF;break;
			case 'Blue':		$this->_commandCodes[2] = 0x00; $this->_commandCodes[3] = 0x00;$this->_commandCodes[4] = 0xFF;break;
			case 'BabyBlue':	$this->_commandCodes[2] = 0x89; $this->_commandCodes[3] = 0xCF;$this->_commandCodes[4] = 0xF0;break;
			case 'Aqua':		$this->_commandCodes[2] = 0x00; $this->_commandCodes[3] = 0xFF;$this->_commandCodes[4] = 0xFF;break;
			case 'Mint':		$this->_commandCodes[2] = 0x80; $this->_commandCodes[3] = 0xFF;$this->_commandCodes[4] = 0x80;break;
			case 'SpringGreen':	$this->_commandCodes[2] = 0x00; $this->_commandCodes[3] = 0xFF;$this->_commandCodes[4] = 0x7F;break;
			case 'Green':		$this->_commandCodes[2] = 0x00; $this->_commandCodes[3] = 0xFF;$this->_commandCodes[4] = 0x00;break;
			case 'LimeGreen':	$this->_commandCodes[2] = 0x32; $this->_commandCodes[3] = 0xcd;$this->_commandCodes[4] = 0x32;break;
			case 'Yellow':		$this->_commandCodes[2] = 0xFF; $this->_commandCodes[3] = 0xFF;$this->_commandCodes[4] = 0x00;break;
			case 'YellowOrange':$this->_commandCodes[2] = 0xFF; $this->_commandCodes[3] = 0xCC;$this->_commandCodes[4] = 0x00;break;
			case 'Orange':		$this->_commandCodes[2] = 0xFF; $this->_commandCodes[3] = 0x7F;$this->_commandCodes[4] = 0x00;break;
			case 'Red':			$this->_commandCodes[2] = 0xFF; $this->_commandCodes[3] = 0x00;$this->_commandCodes[4] = 0x00;break;
			case 'Pink':		$this->_commandCodes[2] = 0xFF; $this->_commandCodes[3] = 0xC0;$this->_commandCodes[4] = 0xDB;break;
			case 'Fuchsia':		$this->_commandCodes[2] = 0xFF; $this->_commandCodes[3] = 0x00;$this->_commandCodes[4] = 0xFF;break;
			case 'Lilac':		$this->_commandCodes[2] = 0xC8; $this->_commandCodes[3] = 0xA2;$this->_commandCodes[4] = 0xC8;break;
			case 'Lavendar':	$this->_commandCodes[2] = 0xE6; $this->_commandCodes[3] = 0xE6;$this->_commandCodes[4] = 0xFA;break;			
			case (substr($color,0,1)== "#"):
				$rgb= $this->hex2rgb($color);
				$this->_commandCodes[2]=$rgb[0];
				$this->_commandCodes[3]=$rgb[1];
				$this->_commandCodes[4]=$rgb[2];
				break;
			case ((int)$color >= 0x00) && ((int)$color <= 0xFFFFFF): 
				$x = (int)$color;
				$rgb= $this->hex2rgb($x);
				$this->_commandCodes[2]=$rgb[0];
				$this->_commandCodes[3]=$rgb[1];
				$this->_commandCodes[4]=$rgb[2]; 
				break;
			default:			
				$this->_commandCodes[2] = 0xFF; $this->_commandCodes[3] = 0xFF;$this->_commandCodes[4] = 0xFF;
				break;
				
		}
		$this->_color[0] = $this->_commandCodes[2];
		$this->_color[1] = $this->_commandCodes[3];
		$this->_color[2] = $this->_commandCodes[4];
		$ret =  $this->Send(11,0);
		return $ret;
	}

	function OnDisco($prg,$speed=1) {		
		if ($prg<0x1) {
			$prg=0x1;
		}
		if ($prg>=14) {
			$prg=14;
		}
		$speed=round($speed*0x30)/100;
		if ($speed<0x1) {
			$speed=0x1;
		}
		if ($speed>=0x30) {
			$speed=0x30;
		}
      	$prg = $prg - 1;
		$this->_commandCodes[1] = 0xec;
		$this->_commandCodes[2] = $prg;
		$this->_commandCodes[3] = $speed;
		$this->_commandCodes[4] = 0x0;
		$this->_commandCodes[5] = 0x0;
		$this->_commandCodes[6] = 0x0;
		return $this->Send(11,0);

	}

	function DiscoSpeed($speed,$prg=5) {		
		if ($prg<0x1) {
			$prg=0x1;
		}

		if ($prg>=14) {
			$prg=14;
		}
		$speed=round($speed*0x30)/100;
		if ($speed<0x1) {
			$speed=0x1;
		}
		if ($speed>=0x30) {
			$speed=0x30;
		}
      	$prg = $prg - 1;
		$this->_commandCodes[1] = 0xec;
		$this->_commandCodes[2] = $prg;
		$this->_commandCodes[3] = $speed;
		$this->_commandCodes[4] = 0x0;
		$this->_commandCodes[5] = 0x0;
		$this->_commandCodes[6] = 0x0;
		return $this->Send(11,0);
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
?>
