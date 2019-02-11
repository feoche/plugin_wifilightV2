<?php
/*
* LimitlessLED Technical Developer Opensource API: http://www.limitlessled.com/dev/
* The MIT License (MIT)
*
* Copyright (c) 2015 Diving-91 (User:diving91 https://www.jeedom.fr/forum/)
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

Object creation: $light = new Milight('x.y.z.w'); // where x.y.z.w is the wifi bridge IP address on the LAN

4 Methods for all bulbs type
-whiteSetGroup($grp)	Select Group for Dual White Lights. 0=All groups, 1 to 4 desired group. To use before lighting light on
-whiteGetGroup() 	Return Group for Dual White Lights. 0=All groups, 1 to 4 desired group
-rgbwSetGroup($grp)	Select Group for RGBW Lights. 0=All groups, 1 to 4 desired group. To use before lighting light on
-rgbwGetGroup()		Return Group for RGBW Lights. 0=All groups, 1 to 4 desired group
11 Methods for dual white bulbs
-whiteOn()		Light On selected group and uses Actual Brightness & Color Temperature
-whiteOnMax()		Light On selected group and set Brightness to Max
-whiteOnMid()		Light On selected group and set Brightness to Mid
-whiteOnMin()		Light On selected group and set Brightness to Min
-whiteOnNight()		Light On selected group and set Night mode
-whiteOnWarm()		Light On selected group and set Color Temperature to Full Warm
-whiteOnLukewarm()	Light On selected group and set Color Temperature to Mid Warm/Cool
-whiteOnCool()		Light On selected group and set Color Temperature to Full Cool
-whiteBrightness($dir)	Modify Brightness - To be used right after whiteOn method (10 steps of brightness possible) -1=One step decrease, 1=One step increase
-whiteColor($dir)	Modify Color Temperature - To be used right after whiteOn method (10 steps of color temperature possible) -1=One step cooler, 1=One step warmer
-whiteOff()		Light Off selected group
14 Methods for rgbw bulbs
-rgbwOn()		Light On selected group and uses Actual Brightness & Color & Mode (ie rgb or w)
-rgbwOnMax()		Light On selected group and set Brightness to Max for Actual Mode (ie rgb or w) - Note that W and RGB have separate brightness memory
-rgbwOnMid()		Light On selected group and set Brightness to Mid for Actual Mode (ie rgb or w) - Note that W and RGB have separate brightness memory
-rgbwOnMin()		Light On selected group and set Brightness to Min for Actual Mode (ie rgb or w) - Note that W and RGB have separate brightness memory
-rgbwOnBrightness($val)	Light On selected group and set Brightness to specified value from 0 to 100 ( translated into 0x02 to 0x1e) - This affects only Actual mode (ie rgb or w)
-rgbwOnNight()		Light On selected group and set Night mode
-rgbwOnWhite()		Light On selected group and set mode to White
-rgbwOnColor($color)	Light On selected group and set mode to specified Color from 0x00 to 0xFF or a predefined value in the below list or a #rrggbb color code
				Random, Violet, Blue, BabyBlue, Aqua, Mint, SpringGreen, Green, LimeGreen, Yellow, YellowOrange, Orange, Red, Pink, Fuchsia, Lilac, Lavendar
				eg: rgbwOnColor(0x33) or rgbwOnColor('Mint') or rgbwOnColor('#c03378')
-rgbwOnDisco($prog)	Light On selected group and set mode to selected Disco program (from 1 to 9) - see below for program description.
			Tuning the desired brightness right after this method is needed - Disco seem to start with Mid brightness as well as Mid DiscoSpeed
				Available disco programs: 1=rainbowSwirl, 2=whiteFade, 3=rgbwFade, 4=rainbowJump, 5=disco, 6=redTwinkle, 7=greenTwinkle, 8=blueTwinkle, 9=allCombined
-rgbwDiscoNext()	Light On selected group and set mode to next Disco program (Round Robin 1 to 9 than 1 again) - see above for program description description.
			Tuning the desired brightness right after this method is needed - Disco seem to start with Mid brightness as well as Mid DiscoSpeed
-rgbwDiscoMin()		Set disco speed to Min - It turns bulb on but does not switch its mode (rgw,w,disco), so this has no effect when not in disco mode
			To be used right after rgbwOnDisco($prog) or rgbwDiscoNext() methods
-rgbwDiscoMid()		Same as above with speed to Mid
-rgbwDiscoMax()		Same as above with speed to Max
-rgbwOff()		Light Off selected group
*/

/*
* Some minor modifications by B. Caron - 2015/10/18
* Add major features by B. Caron - 2016/10/27
*	order repeat
*	delay between two orders
*   milight Hue color to rgb added
*/
require_once dirname(__FILE__) . '/include/common.php';
class W2_milightV3 {
	protected $_host;
	protected $_port;
	protected $_wait;
	protected $_repeat;
	protected $_repeatOK;
	protected $_color = array(0,0,0,0);
	protected $_increm;
	protected $_delay = 101000; //microseconds
	protected $_ActiveGroup; // 0 means all, else group 1 to 4
	protected $_return ;
	protected $_log;
	
	
	protected $_commandCodes = array(
	//RGBW Bulbs commands
	'rgbwAllOn' => array(0x42, 0x00), //
	'rgbwAllOff' => array(0x41, 0x00), //
	'rgbwGroup1On' => array(0x45, 0x00), //
	'rgbwGroup2On' => array(0x47, 0x00), //
	'rgbwGroup3On' => array(0x49, 0x00), //
	'rgbwGroup4On' => array(0x4B, 0x00), //
	'rgbwGroup1Off' => array(0x46, 0x00), //
	'rgbwGroup2Off' => array(0x48, 0x00), //
	'rgbwGroup3Off' => array(0x4a, 0x00), //
	'rgbwGroup4Off' => array(0x4c, 0x00), //
	'rgbwAllNightMode' => array(0xc1, 0x00), //
	'rgbwGroup1NightMode' => array(0xc6, 0x00), //
	'rgbwGroup2NightMode' => array(0xc8, 0x00), //
	'rgbwGroup3NightMode' => array(0xca, 0x00), //
	'rgbwGroup4NightMode' => array(0xcc, 0x00), //
	'rgbwBrightnessMax' => array(0x4e, 0x1b), //
	'rgbwBrightnessMin' => array(0x4e, 0x02), //
	'rgbwBrightnessMid' => array(0x4e, 0x0e), //
	'rgbwBrightness' => array(0x4e, 0x0e), // Exact value (from 0x02 to 0x1e) modified by Method rgbwOnBrightness
	'rgbwDiscoMode' => array(0x4d, 0x00), // 20 different modes
	'rgbwDiscoSlower' => array(0x43, 0x00),
	'rgbwDiscoFaster' => array(0x44, 0x00),
	'rgbwAllSetToWhite' => array(0xc2, 0x00), //
	'rgbwGroup1SetToWhite' => array(0xc5, 0x00), //
	'rgbwGroup2SetToWhite' => array(0xc7, 0x00), //
	'rgbwGroup3SetToWhite' => array(0xc9, 0x00), //
	'rgbwGroup4SetToWhite' => array(0xcb, 0x00), //
	'rgbwColor' => array(0x40, 0x00), // Exact value (from 0x00 to 0xff) modified by Method rgbwOnColor
	//);
	//	private $_commandCodes = array(
	//white Bulb commands
	'whiteAllOn' => array(0x35, 0x00), //
	'whiteGroup1On' => array(0x38, 0x00), //
	'whiteGroup2On' => array(0x3d, 0x00), //
	'whiteGroup3On' => array(0x37, 0x00), //
	'whiteGroup4On' => array(0x32, 0x00), //
	'whiteAllOff' => array(0x39, 0x00), //
	'whiteGroup1Off' => array(0x3b, 0x00), //
	'whiteGroup2Off' => array(0x33, 0x00), //
	'whiteGroup3Off' => array(0x3a, 0x00), //
	'whiteGroup4Off' => array(0x36, 0x00), //
	'whiteAllNightMode' => array(0xbb, 0x00), //
	'whiteGroup1NightMode' => array(0xbb, 0x00), //
	'whiteGroup2NightMode' => array(0xb3, 0x00), //
	'whiteGroup3NightMode' => array(0xba, 0x00), //
	'whiteGroup4NightMode' => array(0xb6, 0x00), //
	'whiteBrightnessUp' => array(0x3c, 0x00), //There are ten steps between min and max
	'whiteBrightnessDown' => array(0x34, 0x00), //There are ten steps between min and max
	'whiteAllBrightnessMax' => array(0xb5, 0x00), //
	'whiteGroup1BrightnessMax' => array(0xb8, 0x00), //
	'whiteGroup2BrightnessMax' => array(0xbd, 0x00), //
	'whiteGroup3BrightnessMax' => array(0xb7, 0x00), //
	'whiteGroup4BrightnessMax' => array(0xb2, 0x00), //
	'whiteWarmIncrease' => array(0x3e, 0x00), // There are ten steps between min and max
	'whiteCoolIncrease' => array(0x3f, 0x00) //There are ten steps between min and max
	);
	
	
	public function __construct($host = '192.168.1.110', $wait=0, $repeat=1, $increm=10, $ID=0, $LocalId="", $nbLeds=0, $colorOrder=0, $port = 8899) {
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
		$this->SetGroup();
		$myRet = new wifilightV2c;
		$this->_return = $myRet->_return_WFL;
		$this->_log = $myRet->_log;
	}
	public function GetColor() {
		return $this->_color[$this->_ActiveGroup];
		return 0;
	}
	public function SetColor($Col) {
		$this->_color[$this->_ActiveGroup]=$Col;
		return 0;
	}
	public function SetGroup($group=0) {
		$this->_ActiveGroup = $group;
	}

	public function GetGroup($group=0) {
		return $this->_ActiveGroup;
	}
	public function retStatus() {

		return $this->_return;	
	}	
	//Used to dynamically call class methods
	public function call(){
		echo __METHOD__;
	}
	
	protected function send($code) {
		$command = $this->_commandCodes[$code];
		$command[] = 0x55; //last byte always 0x55, will appended to all commands
		$message = vsprintf(str_repeat('%c', count($command)), $command);
		$mess='';
		for ($iVal=0;$iVal<strlen($message);$iVal++){
			$mess=$mess.dechex($command[$iVal])." ";
		}
		log::add($this->_log,'debug','Commande : '.$mess);
		if($this->_repeatOK == false) $this->_repeat=1;
		if ($socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP)) {
			for ($iCount=0;$iCount<$this->_repeat-1;$iCount++) {
				socket_sendto($socket, $message, strlen($message), 0, $this->_host, $this->_port);
				usleep($this->_wait);
				usleep($this->_delay);
			}
			socket_sendto($socket, $message, strlen($message), 0, $this->_host, $this->_port);			
			socket_close($socket);
			usleep($this->_wait);
			usleep($this->_delay); //wait 100ms before sending next command
		}
		else {
			log::add($this->_log,'debug','No socket');
		}
		log::add($this->_log,'debug','End ');
	}
	protected function rgbToColor($r,$g,$b) {
		$r /= 255;
		$g /= 255;
		$b /= 255;
		$max = max($r,$g,$b);
		$min = min($r,$g,$b);
		$l = ($max+$min)/2;
		$d = $max-$min;

		if($d==0){
			$h=$s=0; // achromatic
		} 
		else {
			$s=$d/(1-abs(2*$l-1));
			switch( $max ){
				case $r: $h=60*fmod((($g - $b)/$d),6); if ($b > $g) {$h += 360;} break;
				case $g: $h=60*(($b-$r)/$d+2); break;
				case $b: $h=60*(($r-$g)/$d+4); break;
			}
		}
		// This portion Copyright (c) 2014 Yashar Rashedi <info@rashedi.com>
		// modified by Bernard Caron
		$color = (256 + 171 - round($h / 360.0 * 256.0-0.0001)) % 256;
		return ($color + 0xfa) & 0xff;
	}
	// convert from Hue (Sat=1. Lum=0.5) to RGB
	public function ColorTorgb($Col){ 
		//log::add($this->_log,'debug',"col to convert :".$Col);
		$Colrgb=array(0,0,0);
		$Hue= (((int)((171.-$Col)*360./256.))+720) % 360;
		$Sat=1.;
		$Lum=0.5;
		$Hue2=$Hue/60;
		$C = (1 - abs(2.*$Lum - 1))*$Sat; 
		$m=$Lum-$C/2;
		$RE=$Hue2-((int)($Hue2/2))*2;
		$X = $C * (1. - abs($RE - 1));
		//log::add($this->_log,'debug',"Hue C X m RE=".$Hue." ".$C." ".$X." ".$m." ".$RE);
		if (0<=$Hue && $Hue<60) {
			$R=$C;
			$G=$X;
			$B=0;
		}
		if (60<=$Hue && $Hue<120) {
			$R=$X;
			$G=$C;
			$B=0;
		}
		if (120<=$Hue && $Hue<180) {
			$R=0;
			$G=$C;
			$B=$X;
		}
		if (180<=$Hue && $Hue<240) {
			$R=0;
			$G=$X;
			$B=$C;
		}
		if (240<=$Hue && $Hue<300) {
			$R=$X;
			$G=0;
			$B=$C;
		}
		if (300<=$Hue && $Hue<360) {
			$R=$C;
			$G=0;
			$B=$X;
		}
		$Colrgb[0]=($R+$m)*255;
		$Colrgb[1]=($G+$m)*255;
		$Colrgb[2]=($B+$m)*255;
	
		//log::add($this->_log,'debug',"col to convert :".$Colrgb[0]." ".$Colrgb[1]." ".$Colrgb[2]);
		//log::add($this->_log,'debug','3');
		$hex=$this->rgb2hex($Colrgb);
		//log::add($this->_log,'debug','Ret :'.$hex);
		return $hex;		
	}
	public function rgbGetColor() {
		$hex = $this->_color;
		//log::add($this->_log,'debug','Get color hex : '.$hex);
		return $hex;
	}
	public function rgb2hex($rgb) {
	   $hex = "#";
	   $hex .= str_pad(dechex($rgb[0]), 2, "0", STR_PAD_LEFT);
	   $hex .= str_pad(dechex($rgb[1]), 2, "0", STR_PAD_LEFT);
	   $hex .= str_pad(dechex($rgb[2]), 2, "0", STR_PAD_LEFT);
	   return $hex; // returns the hex value including the number sign (#)
	}
	public function OnControl($value) {
	}
}
class W2_mylightStripRGB extends W2_milightV3
{
	// Start Methods applicable to RGBW Lights 	
	public function On() {
		switch ($this->_ActiveGroup) {
			case 0: $this->send('rgbwAllOn'); break;
			case 1: $this->send('rgbwGroup1On'); break;
			case 2: $this->send('rgbwGroup2On'); break;
			case 3: $this->send('rgbwGroup3On'); break;
			case 4: $this->send('rgbwGroup4On'); break;
		}
	}

	public function Off() {
		switch ($this->_ActiveGroup) {
			case 0: $this->send('rgbwAllOff'); break;
			case 1: $this->send('rgbwGroup1Off'); break;
			case 2: $this->send('rgbwGroup2Off'); break;
			case 3: $this->send('rgbwGroup3Off'); break;
			case 4: $this->send('rgbwGroup4Off'); break;
		}
	}

	public function OnMax() {
	
		 $this->On($this->_ActiveGroup); 
		 $this->send('rgbwBrightnessMax');
		
	}

	public function OnMin() {

		$this->On($this->_ActiveGroup);
		$this->send('rgbwBrightnessMin');
		
	}

	public function OnMid() {

		$this->On($this->_ActiveGroup); 
		$this->send('rgbwBrightnessMid');
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
	public function OnBrightness($value=0x0e) {
		$x = min(round(2+$value*25/100,0),100);
		$this->_commandCodes['rgbwBrightness'][1] = $x;
		$this->On();
		$this->send('rgbwBrightness');
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
	public function OnBrightnessWhite($Intensity=50,$Color='#FFFFFF') {
		OnBrightness($Intensity);
		$Color='#FFFFFF';
		//log::add($this->_log,'debug','Col :'.$Color);
		$Col=$this->hex2rgb($Color);
		return $this->OnColor($hex);
	}
	public function OnNight() {
		switch ($this->_ActiveGroup) {
			case 0: $this->Off(0); $this->send('rgbwAllNightMode'); break;
			case 1: $this->Off(1); $this->send('rgbwGroup1NightMode'); break;
			case 2: $this->Off(2); $this->send('rgbwGroup2NightMode'); break;
			case 3: $this->Off(3); $this->send('rgbwGroup3NightMode'); break;
			case 4: $this->Off(4); $this->send('rgbwGroup4NightMode'); break;
		}
	}	
	public function OnColor($color='Mint',$Bright) {
		$color = (string)$color;
		//log::add($this->_log,'debug','in color : '.$color);
		switch ($color) {
			case 'Random':		$this->_commandCodes['rgbwColor'][1] = (int)mt_rand(0,255);  
								$this->_color[$this->_ActiveGroup]= $this->ColorTorgb($this->_commandCodes['rgbwColor'][1]);
								break;
			case 'Blue':		$this->_commandCodes['rgbwColor'][1] = 0x10; $this->_color[$this->_ActiveGroup]= '#0000FF'  ; break;
			case 'Violet':		$this->_commandCodes['rgbwColor'][1] = 0xeb; $this->_color[$this->_ActiveGroup]= '#7F00FF' ;  break;
			case 'BabyBlue':	$this->_commandCodes['rgbwColor'][1] = 0x20; $this->_color[$this->_ActiveGroup]= '#00bbff' ; break;
			case 'Aqua':		$this->_commandCodes['rgbwColor'][1] = 0x30; $this->_color[$this->_ActiveGroup]= '#00FFFF' ; break;
			case 'SpringGreen':	$this->_commandCodes['rgbwColor'][1] = 0x40; $this->_color[$this->_ActiveGroup]= '#00FF7F' ; break;
			case 'Mint':		$this->_commandCodes['rgbwColor'][1] = 0x4A; $this->_color[$this->_ActiveGroup]= '#00FF43' ; break;
			case 'Green':		$this->_commandCodes['rgbwColor'][1] = 0x55; $this->_color[$this->_ActiveGroup]= '#00FF00' ; break;
			case 'LimeGreen':	$this->_commandCodes['rgbwColor'][1] = 0x70; $this->_color[$this->_ActiveGroup]= '#a1FF00' ; break;
			case 'Yellow':		$this->_commandCodes['rgbwColor'][1] = 0x80; $this->_color[$this->_ActiveGroup]= '#FFFF00' ; break;
			case 'YellowOrange':$this->_commandCodes['rgbwColor'][1] = 0x88; $this->_color[$this->_ActiveGroup]= '#FFD000' ; break;
			case 'Orange':		$this->_commandCodes['rgbwColor'][1] = 0x8F; $this->_color[$this->_ActiveGroup]= '#FFA500' ; break;
			case 'Red':			$this->_commandCodes['rgbwColor'][1] = 0xaa; $this->_color[$this->_ActiveGroup]= '#FF0000' ; break;
			case 'Pink':		$this->_commandCodes['rgbwColor'][1] = 0xbd; $this->_color[$this->_ActiveGroup]= '#FF0061' ; break;
			case 'Fuchsia':		$this->_commandCodes['rgbwColor'][1] = 0xd6; $this->_color[$this->_ActiveGroup]= '#FF00FF' ; break;
			case 'Lilac':		$this->_commandCodes['rgbwColor'][1] = 0xE0; $this->_color[$this->_ActiveGroup]= '#D000FF' ; break;
			case 'Lavendar':	$this->_commandCodes['rgbwColor'][1] = 0xF0; $this->_color[$this->_ActiveGroup]= '#6100FF' ; break;
			case ((int)$color > 0x00) && ((int)$color <= 0xff): 
				$x = (int)$color; 
				$this->_commandCodes['rgbwColor'][1] = $x; 
				$this->_color[$this->_ActiveGroup]=ColorTorgb($x);				
				break;
			case (substr($color,0,1)== "#"):
				$r = (int)hexdec(substr($color,1,2));
				$g = (int)hexdec(substr($color,3,2));
				$b = (int)hexdec(substr($color,5,2));
				$this->_commandCodes['rgbwColor'][1]= $this->rgbToColor($r,$g,$b);
				$this->_color[$this->_ActiveGroup]= $color;
				break;
			default:
				$this->_commandCodes['rgbwColor'][1] = 0x40; 
				$this->_color[$this->_ActiveGroup]= $this->ColorTorgb($this->_commandCodes['rgbwColor'][1]);
				break;
		}
		//log::add($this->_log,'debug','code sent : '.$this->_CodeToSend[15]);
		$this->On($this->_ActiveGroup);
		$this->send('rgbwColor');
			
		//log::add($this->_log,'debug','put color hex : '.$this->_color[$this->_ActiveGroup]);
	}	

	public function OnDisco($prog,$speed=0) {
	//   1=rainbowSwirl, 2=whiteFade, 3=rgbwFade, 4=rainbowJump, 5=disco, 6=redTwinkle, 7=greenTwinkle, 8=blueTwinkle, 9=allCombined
		if ($prog < 1) $prog=1;
		if ($prog > 9) $prog=9;
		//log::add($this->_log,'debug','NDisco prog : '.$prog);
		switch ($this->_ActiveGroup) {
			//rgbwOnColor is used to reset Disco mode to first program
			// modification by B. Caron case 1 to 4
			case 0: $this->OnColor('Mint',$Bright); for ($i=1;$i<=$prog;$i++) {$this->send('rgbwDiscoMode');} break;
			case 1: $this->OnColor('Mint',$Bright); for ($i=1;$i<=$prog;$i++) {$this->send('rgbwDiscoMode');} break;
			case 2: $this->OnColor('Mint',$Bright); for ($i=1;$i<=$prog;$i++) {$this->send('rgbwDiscoMode');}  break;
			case 3: $this->OnColor('Mint',$Bright); for ($i=1;$i<=$prog;$i++) {$this->send('rgbwDiscoMode');} break;
			case 4: $this->OnColor('Mint',$Bright); for ($i=1;$i<=$prog;$i++) {$this->send('rgbwDiscoMode');} break;
		}
	}

	public function OnDiscoNext() {
		$this->On($this->_ActiveGroup); 
		$this->send('rgbwDiscoMode');
	}

	public function DiscoMin() {
		$this->On($this->_ActiveGroup); 
		for ($i = 0; $i < 10; $i++) {
			$this->send('rgbwDiscoSlower');
		}
	}

	public function DiscoMid() {
		$this->DiscoMin();
		for ($i = 0; $i < 5; $i++) {$this->send('rgbwDiscoFaster');}
	}

	public function DiscoMax() {
		$this->DiscoMin();
		for ($i = 0; $i < 10; $i++) {$this->send('rgbwDiscoFaster');}
	}

	public function DiscoSlower() {
		$this->On($this->_ActiveGroup);
		$this->send('rgbwDiscoSlower');
	}

	public function DiscoFaster() {
		$this->On($this->_ActiveGroup);
		$this->send('rgbwDiscoFaster');
	}
	public function DiscoSpeed($speed,$prog) {
		if($speed>100) $speed=100;
		if($speed<1) $speed=1;
		$speed= round($speed/11)+1;
		$speed=11-$speed;
		//log::add($this->_log,'debug','Speed Prog V3 : '.$speed);
		$this->DiscoMax();
		for ($iBcl=0;$iBcl<$speed;$iBcl++) {
			$this->DiscoSlower();
		}
	}
	//End Methods applicable to RGBW Lights
}
class W2_mylightRGBW  extends W2_milightV3
{
	// Start Methods applicable to RGBW Lights 	
	public function On() {
		switch ($this->_ActiveGroup) {
			case 0: $this->send('rgbwAllOn'); break;
			case 1: $this->send('rgbwGroup1On'); break;
			case 2: $this->send('rgbwGroup2On'); break;
			case 3: $this->send('rgbwGroup3On'); break;
			case 4: $this->send('rgbwGroup4On'); break;
		}
	}

	public function Off() {
		switch ($this->_ActiveGroup) {
			case 0: $this->send('rgbwAllOff'); break;
			case 1: $this->send('rgbwGroup1Off'); break;
			case 2: $this->send('rgbwGroup2Off'); break;
			case 3: $this->send('rgbwGroup3Off'); break;
			case 4: $this->send('rgbwGroup4Off'); break;
		}
	}

	public function OnMax() {
	
		 $this->On($this->_ActiveGroup); 
		 $this->send('rgbwBrightnessMax');
		
	}

	public function OnMin() {

		$this->On($this->_ActiveGroup);
		$this->send('rgbwBrightnessMin');
		
	}

	public function OnMid() {

		$this->On($this->_ActiveGroup); 
		$this->send('rgbwBrightnessMid');
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
	public function OnBrightness($value=0x0e) {
		$x = min(round(2+$value*25/100,0),100);
		$this->_commandCodes['rgbwBrightness'][1] = $x;
		$this->On();
		$this->send('rgbwBrightness');
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
	public function OnBrightnessWhite($value=0x0e) {	
		$x = min(round(2+$value*25/100,0),100);
		$this->_commandCodes['rgbwBrightness'][1] = $x;
		$this->On();
		$this->send('rgbwBrightness');
	}

	public function OnNight() {
		switch ($this->_ActiveGroup) {
			case 0: $this->Off(0); $this->send('rgbwAllNightMode'); break;
			case 1: $this->Off(1); $this->send('rgbwGroup1NightMode'); break;
			case 2: $this->Off(2); $this->send('rgbwGroup2NightMode'); break;
			case 3: $this->Off(3); $this->send('rgbwGroup3NightMode'); break;
			case 4: $this->Off(4); $this->send('rgbwGroup4NightMode'); break;
		}
	}	

	public function OnWhite() {
		switch ($this->_ActiveGroup) {
			case 0: $this->On(0); $this->send('rgbwAllSetToWhite'); break;
			case 1: $this->On(1); $this->send('rgbwGroup1SetToWhite'); break;
			case 2: $this->On(2); $this->send('rgbwGroup2SetToWhite'); break;
			case 3: $this->On(3); $this->send('rgbwGroup3SetToWhite'); break;
			case 4: $this->On(4); $this->send('rgbwGroup4SetToWhite'); break;
		}
	}
	
	public function OnColor($color='Mint',$Bright) {
		$color = (string)$color;
		//log::add($this->_log,'debug','in color : '.$color);
		switch ($color) {
			case 'Random':		$this->_commandCodes['rgbwColor'][1] = (int)mt_rand(0,255);  
								$this->_color[$this->_ActiveGroup]= $this->ColorTorgb($this->_commandCodes['rgbwColor'][1]);
								break;
			case 'Blue':		$this->_commandCodes['rgbwColor'][1] = 0x10; $this->_color[$this->_ActiveGroup]= '#0000FF'  ; break;
			case 'Violet':		$this->_commandCodes['rgbwColor'][1] = 0xeb; $this->_color[$this->_ActiveGroup]= '#7F00FF' ;  break;
			case 'BabyBlue':	$this->_commandCodes['rgbwColor'][1] = 0x20; $this->_color[$this->_ActiveGroup]= '#00bbff' ; break;
			case 'Aqua':		$this->_commandCodes['rgbwColor'][1] = 0x30; $this->_color[$this->_ActiveGroup]= '#00FFFF' ; break;
			case 'SpringGreen':	$this->_commandCodes['rgbwColor'][1] = 0x40; $this->_color[$this->_ActiveGroup]= '#00FF7F' ; break;
			case 'Mint':		$this->_commandCodes['rgbwColor'][1] = 0x4A; $this->_color[$this->_ActiveGroup]= '#00FF43' ; break;
			case 'Green':		$this->_commandCodes['rgbwColor'][1] = 0x55; $this->_color[$this->_ActiveGroup]= '#00FF00' ; break;
			case 'LimeGreen':	$this->_commandCodes['rgbwColor'][1] = 0x70; $this->_color[$this->_ActiveGroup]= '#a1FF00' ; break;
			case 'Yellow':		$this->_commandCodes['rgbwColor'][1] = 0x80; $this->_color[$this->_ActiveGroup]= '#FFFF00' ; break;
			case 'YellowOrange':$this->_commandCodes['rgbwColor'][1] = 0x88; $this->_color[$this->_ActiveGroup]= '#FFD000' ; break;
			case 'Orange':		$this->_commandCodes['rgbwColor'][1] = 0x8F; $this->_color[$this->_ActiveGroup]= '#FFA500' ; break;
			case 'Red':			$this->_commandCodes['rgbwColor'][1] = 0xaa; $this->_color[$this->_ActiveGroup]= '#FF0000' ; break;
			case 'Pink':		$this->_commandCodes['rgbwColor'][1] = 0xbd; $this->_color[$this->_ActiveGroup]= '#FF0061' ; break;
			case 'Fuchsia':		$this->_commandCodes['rgbwColor'][1] = 0xd6; $this->_color[$this->_ActiveGroup]= '#FF00FF' ; break;
			case 'Lilac':		$this->_commandCodes['rgbwColor'][1] = 0xE0; $this->_color[$this->_ActiveGroup]= '#D000FF' ; break;
			case 'Lavendar':	$this->_commandCodes['rgbwColor'][1] = 0xF0; $this->_color[$this->_ActiveGroup]= '#6100FF' ; break;
			case ((int)$color > 0x00) && ((int)$color <= 0xff): 
				$x = (int)$color; 
				$this->_commandCodes['rgbwColor'][1] = $x; 
				$this->_color[$this->_ActiveGroup]=ColorTorgb($x);				
				break;
			case (substr($color,0,1)== "#"):
				$r = (int)hexdec(substr($color,1,2));
				$g = (int)hexdec(substr($color,3,2));
				$b = (int)hexdec(substr($color,5,2));
				$this->_commandCodes['rgbwColor'][1]= $this->rgbToColor($r,$g,$b);
				$this->_color[$this->_ActiveGroup]= $color;
				break;
			default:
				$this->_commandCodes['rgbwColor'][1] = 0x40; 
				$this->_color[$this->_ActiveGroup]= $this->ColorTorgb($this->_commandCodes['rgbwColor'][1]);
				break;
		}
		//log::add($this->_log,'debug','code sent : '.$this->_CodeToSend[15]);
		$this->On($this->_ActiveGroup);
		$this->send('rgbwColor');
			
		//log::add($this->_log,'debug','put color hex : '.$this->_color[$this->_ActiveGroup]);
	}	

	public function OnDisco($prog,$speed=0) {
	//   1=rainbowSwirl, 2=whiteFade, 3=rgbwFade, 4=rainbowJump, 5=disco, 6=redTwinkle, 7=greenTwinkle, 8=blueTwinkle, 9=allCombined
		if ($prog < 1) $prog=1;
		if ($prog > 9) $prog=9;
		//log::add($this->_log,'debug','NDisco prog : '.$prog);
		$Bright=50;
		switch ($this->_ActiveGroup) {
			//rgbwOnColor is used to reset Disco mode to first program
			// modification by B. Caron case 1 to 4
			case 0: $this->OnColor('Mint',$Bright); for ($i=1;$i<=$prog;$i++) {$this->send('rgbwDiscoMode');} break;
			case 1: $this->OnColor('Mint',$Bright); for ($i=1;$i<=$prog;$i++) {$this->send('rgbwDiscoMode');} break;
			case 2: $this->OnColor('Mint',$Bright); for ($i=1;$i<=$prog;$i++) {$this->send('rgbwDiscoMode');}  break;
			case 3: $this->OnColor('Mint',$Bright); for ($i=1;$i<=$prog;$i++) {$this->send('rgbwDiscoMode');} break;
			case 4: $this->OnColor('Mint',$Bright); for ($i=1;$i<=$prog;$i++) {$this->send('rgbwDiscoMode');} break;
		}
	}

	public function OnDiscoNext() {
		$this->On($this->_ActiveGroup); 
		$this->send('rgbwDiscoMode');
	}

	public function DiscoMin() {
		$this->On($this->_ActiveGroup); 
		for ($i = 0; $i < 10; $i++) {
			$this->send('rgbwDiscoSlower');
		}
	}

	public function DiscoMid() {
		$this->DiscoMin();
		for ($i = 0; $i < 5; $i++) {$this->send('rgbwDiscoFaster');}
	}

	public function DiscoMax() {
		$this->DiscoMin();
		for ($i = 0; $i < 10; $i++) {$this->send('rgbwDiscoFaster');}
	}

	public function DiscoSlower() {
		$this->On($this->_ActiveGroup);
		$this->send('rgbwDiscoSlower');
	}

	public function DiscoFaster() {
		$this->On($this->_ActiveGroup);
		$this->send('rgbwDiscoFaster');
	}
	public function DiscoSpeed($speed,$prog) {
		if($speed>100) $speed=100;
		if($speed<1) $speed=1;
		$speed= round($speed/11)+1;
		$speed=11-$speed;
		//log::add($this->_log,'debug','Speed Prog V3 : '.$speed);
		$this->DiscoMax();
		for ($iBcl=0;$iBcl<$speed;$iBcl++) {
			$this->DiscoSlower();
		}
	}
	//End Methods applicable to RGBW Lights

}
class W2_mylightWhite  extends W2_milightV3
{
	// Start Methods applicable to Dual White Lights
	public function On() {
		switch ($this->_ActiveGroup) {
			case 0: $this->send('whiteAllOn'); break;
			case 1: $this->send('whiteGroup1On'); break;
			case 2: $this->send('whiteGroup2On'); break;
			case 3: $this->send('whiteGroup3On'); break;
			case 4: $this->send('whiteGroup4On'); break;
		}
	}

	public function Off() {
		switch ($this->_ActiveGroup) {
			case 0: $this->send('whiteAllOff'); break;
			case 1: $this->send('whiteGroup1Off'); break;
			case 2: $this->send('whiteGroup2Off'); break;
			case 3: $this->send('whiteGroup3Off'); break;
			case 4: $this->send('whiteGroup4Off'); break;
		}
	}


	public function OnMax() {
		$this->_repeatOK = false;
		switch ($this->_ActiveGroup) {
			case 0: $this->On(0); $this->send('whiteAllBrightnessMax'); break;
			case 1: $this->On(1); $this->send('whiteGroup1BrightnessMax'); break;
			case 2: $this->On(2); $this->send('whiteGroup2BrightnessMax'); break;
			case 3: $this->On(3); $this->send('whiteGroup3BrightnessMax'); break;
			case 4: $this->On(4); $this->send('whiteGroup4BrightnessMax'); break;
		}
	}
	public function OnMin() {
		$this->_repeatOK = false;
		$this->On($this->_ActiveGroup); 
		for ($i = 0; $i < 10; $i++) {
			$this->BrightnessDir(-1);
		}
	}

	public function OnMid() {
		$this->_repeatOK = false;
		$this->On($this->_ActiveGroup); 
		$this->OnMax($this->_ActiveGroup); 
		for ($i = 0; $i < 5; $i++) {
			$this->BrightnessDir(-1);
		}
	}
	public function BrightnessDir($dir) {
		$this->_repeatOK = false;
		$this->On($this->_ActiveGroup);
		switch ($dir) {
			case -1: $this->send('whiteBrightnessDown'); break;
			case 1: $this->send('whiteBrightnessUp'); break;
		}
	}
	public function BrightnessIncrease($Br) {
		$this->_repeatOK = false;
		$this->On($this->_ActiveGroup);
		$this->send('whiteBrightnessUp');
		$Br=$Br+10;
		if ($Br > 100) $Br=100;
		return $Br;
	}
	public function BrightnessDecrease($Br) {
		$this->_repeatOK = false;
		$this->On($this->_ActiveGroup);
		$this->send('whiteBrightnessDown');
		$Br=$Br-10;
		if ($Br < 0) $Br=0;
		return $Br;
	}
	public function OnNight() {
		switch ($this->_ActiveGroup) {
			case 0: $this->send('whiteAllNightMode'); break;
			case 1: $this->send('whiteGroup1NightMode'); break;
			case 2: $this->send('whiteGroup2NightMode'); break;
			case 3: $this->send('whiteGroup3NightMode'); break;
			case 4: $this->send('whiteGroup4NightMode'); break;
		}
	}

	public function OnBrightness($slider,$Col) {
		$this->_repeatOK = false;
		log($this->_log,'debug','Slider :'.$slider);
		if($slider>100) $slider=100;
		if($slider<1) $slider=1;
		$slider= round($slider/10)+1;;
		if ($slider>5) {
			$this->OnMax(); 
			for ($i = 0; $i <11-$slider; $i++) {
				$this->BrightnessDir(-1);
			}
		}
		else{
			$this->OnMin(); 
			for ($i = 0; $i <$slider-1; $i++) {
				$this->BrightnessDir(1);
			}
		}
	}
	public function OnBrightnessWhite($slider,$Col="#000000") {
		$this->_repeatOK = false;
		log($this->_log,'debug','Slider :'.$slider);
		if($slider>100) $slider=100;
		if($slider<1) $slider=1;
		$slider= round($slider/10)+1;;
		if ($slider>5) {
			$this->OnMax(); 
			for ($i = 0; $i <11-$slider; $i++) {
				$this->BrightnessDir(-1);
			}
		}
		else{
			$this->OnMin(); 
			for ($i = 0; $i <$slider-1; $i++) {
				$this->BrightnessDir(1);
			}
		}
	}
	
	public function OnWarm() {
		$this->_repeatOK = false;
		$this->On($this->_ActiveGroup);
		for ($i = 0; $i < 10; $i++) {
			$this->KelvinDir(-1);
		}
	}

	public function OnCool() {
		$this->_repeatOK = false;
		$this->On($this->_ActiveGroup);
		for ($i = 0; $i < 10; $i++) {
			$this->KelvinDir(1);
		}
	}

	public function OnLukewarm() {
		$this->_repeatOK = false;
		$this->OnWarm();
		$this->On($this->_ActiveGroup);
		for ($i = 0; $i < 5; $i++) {
			$this->KelvinDir(-1);
		}
	}
	public function KelvinIncrease($value=50) {
		$this->_repeatOK = false;
		$this->KelvinDir(1);
		$value=$value+10;
		if ($value > 100) $value=100;
		return $value;
	}
	public function KelvinDecrease($value=50) {
		$this->_repeatOK = false;
		$this->KelvinDir(-1);
		$value=$value-10;
		if ($value < 0) $value=0;
		return $value;
	}

	public function OnKelvin($value=50) {
		$this->_repeatOK = false;
		if($value>100) $value=100;
		if($value<1) $value=1;
		$value= round($value/11)+1;
		$this->On($this->_ActiveGroup);
		if ($value>6) {
			$this->OnCool();
			for ($i = 0; $i < 11 - $value; $i++) {
				$this->send('whiteWarmIncrease');
			}
		}
		else {
			$this->OnWarm();
			for ($i = 0; $i < $value-1; $i++) {
				$this->send('whiteCoolIncrease');
			}
		}
	}

	public function KelvinDir($dir) {
		$this->_repeatOK = false;
		$this->On($this->_ActiveGroup);
		switch ($dir) {
			case -1: $this->send('whiteWarmIncrease'); break;
			case 1: $this->send('whiteCoolIncrease'); break;
		}	
	}
	//End Methods applicable to Dual White Lights 

}

?>
