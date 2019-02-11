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
* https://github.com/jacklaag/rgb_wifi_python/blob/master/API%20Documentation/Protocol.doc

1. Send order:【0XEF】+【0X01】+【0X77】
	Controller response:
	【0X66】+【8bit device name(0x01)】+【8bit swtich on /off】+【8bit mode value】+【8bit run/pause state】+【8bit speed value】+【8bit red data】+【8bit green data】+【8bit blue data】+【0X00】+【0X99】
	i. 【8bit mode value】
	  1. 0x25 ~ 0x38 is built-in mode,ps refer to “appendix1,built-in mode content”
	  2. 0x39 is custom mode;
	  3. 0x41 is static color mode;
	ii. 【8bit switch on /off】
	  1. 0x23 is switch on 
	  2. 0x24 is switch off 
	iii. 【8bit run/pause state】(built-in mode、custom mode operative)
	  1. 0x20 is run 
	  2. 0x21 is pause
	iv. 【8bit speed value】(built-in mode、custom mode operative)
	Range:0x01--0x1F ,0x01 is the fastest,0x1F is the slowest;


2. Send static color:
	a) Send order:
	【0X56】+【8bit red data】+【8bit green data】+【8bit blue data】+【0XAA】
	b) Controller response:no response
	LW12OnColor($color='Mint') $Color is a 3 bytes RGB array

3. Send built-in mode:
	a) Send order:
	【0xBB】+【8bit mode value】+【8bit speed value】+【0X44】
	【8bit mode value】Pls refer to “appendix1,built-in mode content”
	【8bit speed value】Range:0x01--0x1F ,0x01 is the fastest,0x1F is the slowest.
	b) Controller response:no response
	LW12Prg($prg,$speed)

4. Send command:switch on/off ;run/pause 
	a) Send command:
	【0XCC】+【8bit key】+【0X33】
	【8bit key】
	1. 0x23 is switch on
	2. 0x24 is switch off 
	3. 0x20 is run 
	4. 0x21 is pause 
	b) Controller response:no response
	LW12On() LW12Off() LW12PrgOn() LW12PrgOff()

5. Send custom mode:
	a) Send command:
	【0X99】+【First point 24bit colorimetric value(R=?,G=?,B=?)】+【Second point 24bit colorimetric value】+【Third point 24bit colorimetric value】+【Fourth point 24bit colorimetric value】
	+【Fifth point 24bit colorimetric value】+【Sixth point 24bit colorimetric value】+【Seventh point 24bit colorimetric value】+【Eighth point 24bit colorimetric value】
	+【Ninth point 24bit colorimetric value】+【Tenth point 24bit colorimetric value】+【Eleventh point 24bit colorimetric value】+【Twelveth point 24bit colorimetric value】
	+【Thirteenth point 24bit colorimetric value】+【Fourteenth point 24bit colorimetric value】+【Fifteenth point 24bit colorimetric value】+【Sixteenth point 24bit colorimetric value】
	+【8bit speed value】+【8bit CHANGING mode value】+【0XFF】+【0X66】
	i. 【First point 24bit colorimetric value】
	are three bits which are three value of first RGB.There are total 16colors,then color value have 48 types. 
	ii. 【8bit CHANGING mode value】
	1. 0x3A :gradual
	2. 0x3B :jump;
	3. 0x3C :flash
	iii. Notice:
	R=1,G=2,B=3 is a color terminator,it means this color is the last color. 
	b) Controller response:no response

6. built in mode code:
	1: 7 colors  gradual  change 0x25
	2:red gradual  change 0x26
	3:green  gradual  change 0x27
	4:blue gradual  change 0x28
	5:yellow gradual  change 0x29
	6:cyan gradual  change 0x2A
	7:purple gradual  change 0x2B
	8:white gradual  change 0x2C
	9:red and green gradual  change 0x2D
	10:red and blue gradual  change 0x2E
	11:green and blue gradual  change 0x2F
	12: 7 colors flicker 0x30
	13:red flicker 0x31
	14:green flicker 0x32
	15:blue flicker 0x33
	16:yellow flicker 0x34
	17:cyan flicker 0x35
	18:purple flicker 0x36
	19:white flicker 0x37
	20: jumpy 7 colors 0x38
	
7. colorimetric values
	【8bit red data】 	0x00--0xFF
	【8bit green data】 	0x00--0xFF
	【8bit blue data】 	0x00--0xFF


* Object creation: $light = new Milight('x.y.z.w'); // where x.y.z.w is the wifi bridge IP address on the LAN


* 1-2016 first version
* 9-2016 state feedback added
* 10-2016 repetition of orders added

OnBrightness -> color
OnBrightnessWhite -> white with 3 colors


*/
require_once dirname(__FILE__) . '/include/common.php';
class W2_lagutelw12
{
	protected $_host;
	protected $_port;
	protected $_wait;
	protected $_repeat;
	protected $_color = array(0,0,0);
	protected $_increm;
	protected $_delay = 100; //microseconds
	protected $_return ;
	protected $_log;
	protected $_commandCodes = array(0,0,0,0,0);

	public function __construct($host = '192.168.1.110', $wait=0, $repeat=1, $increm=10, $ID=0, $LocalId="", $nbLeds=0, $colorOrder=0, $port = 5577) {
		$this->_host = $host;
		$this->_port = $port;
		if ($wait < 0)
			$wait = 0;
		if ($wait > 100)
			$wait = 100;
			
		$this->_wait = $wait*1000;
		if ($repeat < 1)
			$repeat = 1;
		if ($repeat > 5)
			$repeat = 5;
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
	function getStatus() {
		$this->_commandCodes[0] =0xef;
		$this->_commandCodes[1] =0x01;
		$this->_commandCodes[2] =0x77;
		return $this->Send(3,11);
	}

	public function retStatus() {
		$OutStr = $this->getStatus();
		for($i = 0; $i < strlen($OutStr); $i++){
			$Out[$i] = str_pad(ord($OutStr[$i]), 2, '0', STR_PAD_LEFT);
		}
		if ( isset($OutStr) && $OutStr!=NOSOCKET && $OutStr!=NOTCONNECTED && $OutStr!=NORESPONSE && $OutStr!=BADRESPONSE) {
			if ($Out[0]==0x66) {
				$Col[0]=$Out[6];
				$Col[1]=$Out[7];
				$Col[2]=$Out[8];
				$this->_return['Color'] = $this->rgb2hex($Col);				
				$prog=1;
				$prog=$Out[3]-0x24;
				if ($Out[3]>20)
					$prog=20;
				if ($Out[3]<1) {
					$prog=1;
				}
				// now prog from 1 to 20
				$this->_return['Prog'] = $prog;
				$speed= round((33-$Out[5])*100/32);
				$this->_return['Speed'] = $speed;							
				if ($Out[2]==0x23){
					$this->_return['On'] = 1;
				} 
				else if ($Out[2]==0x24){
					$this->_return['On'] = 0;
				}

				if ($Out[4]==0x20){
					$this->_return['Play'] = 0;
				} 
				else if ($Out[4]==0x21){
					$this->_return['Play'] = 1;
				}
			}
			else
				$this->_return['Type'] = 'Not a LW12 controller';			
		}
		else if ( !isset($OutStr) )
			$this->_return = BADRESPONSE;
		else
			$this->_return = $OutStr;
		return $this->_return;	
	}

	protected function Send($size,$responseLength=0) {
		$command = $this->_commandCodes;
		$message = vsprintf(str_repeat('%c', $size), $command);
		$mess="";
		for ($iVal=0;$iVal<strlen($message);$iVal++){
			$mess=$mess.dechex($command[$iVal])." ";
		}
		log::add($this->_log,'debug','Commande : '.$mess);
		// Create a TCP/IP socket. 	
		$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		if ($socket === false) {
			log::add($this->_log,'debug',"socket_create() failed: reason: " .socket_strerror(socket_last_error()) );
			return NOSOCKET;
		}
		else {
			socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array("sec" => 0, "usec" => 50000));				
			socket_set_option($socket, SOL_SOCKET, SO_SNDTIMEO, array('sec' => 0, 'usec' => 50000));
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
		$this->_commandCodes[0] =0xCC; 
		$this->_commandCodes[1] =0x23;
		$this->_commandCodes[2] =0x33;
		return $this->Send(3,0);
	}
	public function Off() {
		$this->_commandCodes[0] =0xCC;
		$this->_commandCodes[1] =0x24;
		$this->_commandCodes[2] =0x33;
		return $this->Send(3,0);
	}
	public function OnMax() {
		return $this->OnBrightnessWhite(100,'#000000');	
	}

	public function OnMin() {
		return $this->OnBrightnessWhite(1,'#000000');		
	}

	public function OnMid() {
		return $this->OnBrightnessWhite(50,'#000000');
	}
	public function OnNight() {
		return $this->OnBrightnessWhite(5,'#000000');
	}
	
	public function OnWhite() {
		return $this->OnBrightnessWhite(100,'#000000');
	}
	public function BrightnessIncrease($value=50,$color='#000000') {
		$value=$value+$this->_increm;
		if ($value<0) $value=0;
		if ($value>100) $value=100;
		$this-> OnBrightness($value,$color);
		return $value;
	}
	public function BrightnessDecrease($value=50,$color='#000000') {
		$value=$value-$this->_increm;
		if ($value<0) $value=0;
		if ($value>100) $value=100;
		$this-> OnBrightness($value,$color);
		return $value;
	}
	function OnBrightness($Intensity=50,$Color='#808080',$White=50) {
		//log::add($this->_log,'debug','Col :'.$Color);
		$Col=$this->hex2rgb($Color);
		//log::add($this->_log,'debug','Col hex :'.$Color);
		$Col[0]= $Intensity*$Col[0]/100;
		$Col[1]= $Intensity*$Col[1]/100;
		$Col[2]= $Intensity*$Col[2]/100;	
		$hex = $this->rgb2hex($Col);
		//log::add($this->_log,'debug','Col :'.$hex);
		return $this->OnColor($hex,$Bright);
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
		$Color='#FFFFFF';
		//log::add($this->_log,'debug','Col :'.$Color);
		$Col=$this->hex2rgb($Color);
		//log::add($this->_log,'debug','Col hex :'.$Color);
		$Col[0]= $Intensity*$Col[0]/100;
		$Col[1]= $Intensity*$Col[1]/100;
		$Col[2]= $Intensity*$Col[2]/100;	
		$hex = $this->rgb2hex($Col);
		//log::add($this->_log,'debug','Col :'.$hex);
		return $this->OnColor($hex);
	}
			
	public function OnColor($color='Mint',$Bright=0,$White='#7F7F7F') {
		$color = (string)$color;			
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
				$this->_commandCodes[1] = 0xFF; $this->_commandCodes[2] = 0xFF;$this->_commandCodes[3] = 0xFF;break;
		}
		$this->_color[0] = $this->_commandCodes[1];
		$this->_color[1] = $this->_commandCodes[2];
		$this->_color[2] = $this->_commandCodes[3];
		//log::add($this->_log,'debug','Color memorized by MagicOnCOlor = '.$this->_color[0]," - ",$this->_color[1]," - ",$this->_color[2]);
		$this->_commandCodes[0] = 0x56;
		$this->_commandCodes[4] = 0xAA;
		return $this->Send(5,0);
	}
	
	function OnDisco($prg,$speed) {
		
		// 1:7 colors gradual change		0x25
		// 2:red gradual change			0x26
		// 3:green gradual change			0x27
		// 4:glue gradual change			0x28
		// 5:yellow gradual change			0x29
		// 6:cyan gradual change			0x2A
		// 7:purple gradual change			0x2B
		// 8:white gradual change			0x2C
		// 9:red and green gradual change		0x2D
		// 10:red and blue gradual change		0x2E
		// 11:green and blue gradual change	0x2F
		// 12:7 colors stroboflash			0x30
		// 13:red stroboflash			0x31
		// 14:green stroboflash			0x32
		// 15:glue stroboflash			0x33
		// 16:yellow stroboflash			0x34
		// 17:cyan stroboflash			0x35
		// 18:purple stroboflash			0x36
		// 19:white stroboflash			0x37
		// 20:7 colors jump change			0x38
		
		$Out = $this->getStatus();
		if ($prg<0x1) {
			$prg=0x1;
		}
		$prg=$prg+0x24;

		if ($prg>0x38)
			$prg=0x38;
		
		if (isset($Out[0]) &&  isset($Out[5])){
			if (ord($Out[0])==0x66) {
				$speed=ord($Out[5]);
			}
			else {
				$speed=round($speed/100*31);
				log::add($this->_log,'debug','Bad state');
			}	
		}
		else {		
			log::add($this->_log,'debug','No state');
			$speed=round($speed/100*31);
		}

		$this->_commandCodes[0] =0xBB;
		$this->_commandCodes[1] =$prg;
		$this->_commandCodes[2] =$speed;
		$this->_commandCodes[3] =0x44;	
		return $this->Send(4,0);
	}
	function DiscoSpeed($speed,$prog=5) {	
		if ($prog<0x1) {
			$prog=0x1;
		}
		$prog=$prog+0x24;
		if ($prog>0x38)
			$prog=0x38;
		$Out = $this->getStatus();
		if (isset($Out[0]) &&  isset($Out[3])){
			if (ord($Out[0])==0x66) {
				$prg=ord($Out[3]);
			}
			else {
				$prg=$prog;
				log::add($this->_log,'debug','Bad state');
			}	
		}
		else {		
			log::add($this->_log,'debug','No state');
			$prg=$prog;
		}	
		if ($speed<0x1)
			$speed=0x1;
		if ($speed>100)
			$speed=100;
		$speed=round($speed*31/100);	
		$speed = 0x20 - $speed;
		$this->_commandCodes[0] =0xBB;
		$this->_commandCodes[1] =$prg;
		$this->_commandCodes[2] =$speed;
		$this->_commandCodes[3] =0x44;	
		return $this->Send(4,0);
	}
	function LW12Prg($prg,$speed) {
		$prg = 0x24 + $prg;
		$speed = 32-$speed;
		$this->_commandCodes[0] =0xBB;
		$this->_commandCodes[1] =$prg;
		$this->_commandCodes[2] =$speed;
		$this->_commandCodes[3] =0x44;
		return $this->Send(4,0);
	}
	public function DiscoMin($prog) {
		return $this->DiscoSpeed(31,$prog);
	}

	public function DiscoMid($prog) {
		return $this->DiscoSpeed(15,$prog);
	}

	public function DiscoMax($prog) {
		return $this->DiscoSpeed(1,$prog);
	}
	function Play() {
		$this->_commandCodes[0] =0xCC;
		$this->_commandCodes[1] =0x21;
		$this->_commandCodes[2] =0x33;
		return $this->Send(3,0);
	}

	function Pause() {
		$this->_commandCodes[0] =0xCC;
		$this->_commandCodes[1] =0x20;
		$this->_commandCodes[2] =0x33;
		return $this->Send(3,0);
	}
	public function SwitchBt() {
	}
	public function OnControl($value) {
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

}
?>
