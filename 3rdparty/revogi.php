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
OnBrightnessWhite -> true white chanel


*/
require_once dirname(__FILE__) . '/include/common.php';
class W2_revogi
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
	
	protected $_commandCodes = array(
	'CMD_ON' => '{"sn":"SPW1080000000252","mode":0,"ledid":"80000000","r":255,"g":255,"b":255,"br":199}',
	'CMD_OFF' => '{"sn":"SPW1080000000252","mode":0,"ledid":"80000000","r":255,"g":255,"b":255,"br":255}',
	'CMD_RGB'=>'{"sn":"SPW1080000000252","mode":1,"ledid":"80000000","r":%d,"g":%d,"b":%d,"br":255}',
    //'CMD_BRIGHTNESS' => '{"sn":"SPW1080000000252","mode":1,"r":%d,"g":%d,"b":%d,"br":%d}',
	'CMD_WHITE' => '{"sn":"SPW1080000000252","mode":0,"ledid":"80000000","r":255,"g":255,"b":255,"br":%d}',
	'CMD_DISCO' => '{"sn":"SPW1080000000252","dance":1}',
	'CMD_GET_PROP' => '{"sn":"SPW1080000000252"}'
	);
	public function __construct($host = '192.168.1.110', $wait=0, $repeat=1, $increm=10, $ID = 0, $LocalId="", $nbLeds=0, $colorOrder=0, $port = 8888) {
		$this->_host = $host;
		$this->_port = 8888;
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
	
	function getStatus() {
		$string = $this->_commandCodes['CMD_GET_PROP'];
		return $this->send($string,551);
	}

	public function retStatus() {
		$OutStr = $this->getStatus();

		         // ob_start();
                 // var_dump($OutStr);
                 // $res = ob_get_clean();
                 // log::add($this->_log,'debug','return state:'.$res );
		if ( isset($OutStr) && $OutStr!=NOSOCKET && $OutStr!=NOTCONNECTED && $OutStr!=NORESPONSE && $OutStr!=BADRESPONSE) {		
			if ($OutStr["data"]["br"]!="") {
				/*
				//"power", "bright", "ct", "rgb","hue","sat","color_mode"		
				 log::add($this->_log,'debug','2' );
				if( ($OutStr["data"]["r"]==255 || $OutStr["data"]["r"]==0) &&
                  ($OutStr["data"]["g"]==255 || $OutStr["data"]["g"]==0) &&
                  ($OutStr["data"]["b"]==255 || $OutStr["data"]["b"]==0) &&
                  ($OutStr["data"]["br"]==255 || $OutStr["data"]["br"]==0))
                  
                {		
					
				  $this->_return['On'] = 0;
                } 
                else {
                  $this->_return['On'] = 1;
                }*/
				if ( isset($OutStr["data"]["r"])==true && $OutStr["data"]["r"]!="") {
                  	if ( ($OutStr["data"]["r"] != 0 && $OutStr["data"]["g"] != 0 && $OutStr["data"]["b"]!=0) ||
                      ($OutStr["data"]["r"] != 255 && $OutStr["data"]["g"] != 255 && $OutStr["data"]["b"]!=255) ){
                      $r = $OutStr["data"]["r"];
                      $g = $OutStr["data"]["g"];
                      $b = $OutStr["data"]["b"];
                      $this->_return['Color'] = $this->rgb2hex([$r,$g,$b]);
					  $color="on";
					}
                    else {
                      $r = 0;
                      $g = 0;
                      $b = 0;
					  $color="off";
                      $this->_return['Color'] = $this->rgb2hex([$r,$g,$b]);
                    }
                }
				if( isset($OutStr["data"]["br"])==true && $OutStr["data"]["br"]!="" && $OutStr["data"]["br"]!=0
                   && $OutStr["data"]["br"]<=200) {
					$this->_return['White'] = $OutStr["data"]["br"]*100/200;
					$color="on";
				}
                else {
                    $this->_return['White'] = 0;
					$white="off";
                }
				if ($white=="off" && $color=="off") {
					
					$this->_return['On']=0;
				}
				else {
					$this->_return['On']=1;
				}
			}
			else
				$this->_return['Type'] = 'Not a Revogi bulb';
		}
		else if ( !isset($OutStr) )
			$this->_return = BADRESPONSE;
		else
			$this->_return = $OutStr;
		return $this->_return;	
	}

	protected function send($command,$cmd) {
        log::add($this->_log,'debug',"Command : ".$command);
	//	http://192.168.1.29?cmd=250&json=%7B%22sn%22%3A%22SPW1080000000252%22%2C%22mode%22%3A1%2C%22ledid%22%3A%2280000000%22%2C%22r%22%3A255%2C%22g%22%3A255%2C%22b%22%3A255%2C%22br%22%3A254%7D
		$send = "http://".$this->_host."?cmd=$cmd&json=".urlencode($command);
		//log::add($this->_log,'debug',"URL: ".$send);
		$out = file_get_contents($send);		
		        // ob_start();
                // var_dump($out);
                // $res = ob_get_clean();
                // log::add($this->_log,'debug','return state:'.$res );			
		if ( $out !== FALSE ) {
			log::add($this->_log,'debug','return : '. $out);
			$json_decoded_data = json_decode($out, true);
			$response = $json_decoded_data["response"];
			switch ($response){
				case 551:
					// state return
					if ( ($json_decoded_data != NULL) && ($json_decoded_data != FALSE) ){
						foreach ($json_decoded_data as $key => $value) {
							//log::add($this->_log,'debug',">> : $key | $value : ".$json_decoded_data[$key]);
						}
						if (isset($json_decoded_data["data"])) {
							foreach ($json_decoded_data["data"] as $key => $value) {
								//log::add($this->_log,'debug',">> : $key | $value : ".$json_decoded_data["data"][$key]);
							}
							return $json_decoded_data;
						}
						log::add($this->_log,'debug',"Bad response");
					}
					return BADRESPONSE;
				case 250:
					// ack
					return SUCCESS;
				default :
					return BADRESPONSE;	
			}					
		}
		else
			log::add($this->_log,'debug',"No data returned" );
		return NOTCONNECTED;
	}
	public function BrightnessW1Increase($value=50) {
		$value=$value+$this->_increm;
		if ($value<0) $value=0;
		if ($value>100) $value=100;
		$this-> OnBrightnessWhite($value);
		return $value;
	}
	public function BrightnessW1Decrease($value=50) {
		$value=$value-$this->_increm;
		if ($value<0) $value=0;
		if ($value>100) $value=100;
		$this-> OnBrightnessWhite($value);
		return $value;
	}
	public function OnBrightnessWhite($Intensity=50,$color='#000000') {
		// takes jeedom color
		if ($Intensity<0) $Intensity=0;
		if ($Intensity>100) $Intensity=100;
		$Intensity=$Intensity*2;
		$string = sprintf($this->_commandCodes['CMD_WHITE'],$Intensity);
		return $this->send($string,250);
	}	
	
	public function On() {
		$Id =1;
		$string = $this->_commandCodes['CMD_ON'];
		return $this->send($string,250);
	}

	public function Off() {
		$Id =1;
		$string = $this->_commandCodes['CMD_OFF'];
		return $this->send($string,250);
	}
	public function OnMax() {
		$this->OnBrightnessWhite(100,'#000000');	
	}

	public function OnMin() {
		return $this->OnBrightnessWhite(1,'#000000');		
	}

	public function OnMid() {
		return $this->OnBrightnessWhite(50,'#000000');
	}
	public function OnNight() {
		$this->OnBrightnessWhite(1,'#000000');
	}
	
	public function OnWhite() {
		// max brightness
		$string = sprintf($this->_commandCodes['CMD_WHITE'], 200);
		return $this->send($string,250);

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
	function OnBrightness($Intensity,$Color='#000000',$White=0) {
		//log::add($this->_log,'debug','inyternsi');
		//log::add($this->_log,'debug','Col before intensity:'.$Color);
		$Col=$this->hex2rgb($Color);
		//log::add($this->_log,'debug','Col hex :'.$Col);
		$Col[0]= round($Intensity*$Col[0]/100);
		$Col[1]= round($Intensity*$Col[1]/100);
		$Col[2]= round($Intensity*$Col[2]/100);	
		$hex = $this->rgb2hex($Col);
		//log::add($this->_log,'debug','Col after intensity :'.$hex);
		return $this->OnColor($hex,$Intensity,$White);
	}
	public function OnColor($color='Mint',$Bright,$White1='#7FF7F7F',$White2='#7FF7F7F') {
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
		
		$Id = 1;
		$string = sprintf($this->_commandCodes['CMD_RGB'],$r,$g,$b);
		return $this->send($string,250);
	}
	function OnDisco($prg,$speed) {
		// 1 : Idsco
      	// 2 : Night
      	// 3 : Concentration
        // 4 : PC
		$this->On();
		switch ($prg) {
			case 1 :
				$string = sprintf($this->_commandCodes['DISCO']);
				break;
			}
		return $this->send($string,256);
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
