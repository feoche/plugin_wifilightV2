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

RGBW WIFI Bulb Command Definition		
Device Type Code:0x44     Version:0x04	
---------------------------------------
Command title	Command Format	
0x10	 command of syncing time:
Send	【0X10】+【0x14】+【10 digit and single digit of year】+【month】+【day】+【hour】+【minute】+【second】+【week】+【reserved for future use:0x0】+【0xF0 remote,0x0F local】+【check digit】(length of command:12)

Return	【0xF0 remote,0x0F local】+ 【0X10】+ 【0x00】+【check digit】(length of command:4)	
---------------------------------------		
0x11	command of requesting time :
Send	【0X11】+【0X1A】+【0X1B】+【0xF0 remote,0x0F local】+【check digit】(length of command:5)

Return	【0xF0 remote,0x0F local】+【0X11】+【0x14】+【10 digit and single digit of year】+【month】+【day】+【hour】+【minute】+【second】+【week】+【reserved for future use:0x0】+【check digit】(length of command:12)
---------------------------------------
0x21	command of setting timing content:
Send	【0X21】＋                                                                                                     
	【Timer switch for time 1】＋【10 digit and single digit of year for time 1】＋【month for time 1】＋【day for time 1】+【hour for time 1】＋【minute for time 1】＋【second for time 1】+【week for time 1】＋【mode for time 1】＋【32bit Value1~Value4 for time 1】＋【switch for time 1】＋
	【Timer switch for time 2】＋【10 digit and single digit of year for time 2】＋【month for time 2】＋【day for time 2】+【hour for time 2】＋【minute for time 2】＋【second for time 2】+【week for time 2】＋【mode for time 2】＋【32bit Value1~Value4 for time 2】＋【switch for time 2】＋
	【Timer switch for time 3】＋【10 digit and single digit of year for time 3】＋【month for time 3】＋【day for time 3】+【hour for time 3】＋【minute for time 3】＋【second for time 3】+【week for time 3】＋【mode for time 3】＋【32bit Value1~Value4 for time 3】＋【switch for time 3】＋
	【Timer switch for time 4】＋【10 digit and single digit of year for time 4】＋【month for time 4】＋【day for time 4】+【hour for time 4】＋【minute for time 4】＋【second for time 4】+【week for time 4】＋【mode for time 3】＋【32bit Value1~Value4 for time 4】＋【switch for time 4】＋
	【Timer switch for time 5】＋【10 digit and single digit of year for time 5】＋【month for time 5】＋【day for time 5】+【hour for time 5】＋【minute for time 5】＋【second for time 5】+【week for time 5】＋【mode for time 5】＋【32bit Value1~Value4 for time 5】＋【switch for time 5】＋   
	【Timer switch for time 6】＋【10 digit and single digit of year for time 6】＋【month for time 6】＋【day for time 6】+【hour for time 6】＋【minute for time 6】＋【second for time 6】+【week for time 6】＋【mode for time 6】＋【32bit Value1~Value4 for time 6】＋【switch for time 6】＋
	【Reserved bits:0x0】＋【0xF0 remote,0x0F local】＋【check digit】(length of command:88)
	
Return	【0xF0 remote,0x0F local】+ 【0X21】+ 【0x00】+【check digit】(length of command:4)	
	"Note:Send command of timing content
	1.when mode value is 0x61,value 1,2,3 and 4 means value of RGBW separately.In addition,RGB and W cannot lihgt together.In this command,either value of RGB or W must be 0.If both value of RGB and W are not 0,then W will be 0.
	2.when mode value is 0x00,do not mind value 1~value 4.Bulbs'RGBW value will same as the previous.
	3.when mode value is not 0x61,it is dynamic mode value.Value1 means speed,range is from 0x01 to 0x1f.  
	4.Timing available:when value is 0xF0,timing works;when value is 0x0F,timing doesn't work.
	5.Timing switcher:when value is 0xF0,it means  ""turn on"";when value is 0x0F,it means  ""turn off"" 
	6.Week:when setting cycle timing,bit0 is empty,bit 1~bit 7 correspond Monday,Tuesday,Wendnesday~Sunday.For example,bit 7 is Sunday.
---------------------------------------		
0x22	command of requesting time content	
Send	【0X22】+【0X2A】+【0X2B】+【0xF0remote,0x0F local】+【check digit】(length of command:5)

Return	【0xF0 remote，0x0F local】+【0X22】＋
	【Timer switch for time 1】＋【10 digit and single digit of year for time 1】＋【month for time 1】＋【day for time 1】+【hour for time 1】＋【minute for time 1】＋【second for time 1】+【week for time 1】＋【mode for time 1】＋【32bit Value1~Value4 for time 1】＋【switch for time 1】＋
	【Timer switch for time 2】＋【10 digit and single digit of year for time 2】＋【month for time 2】＋【day for time 2】+【hour for time 2】＋【minute for time 2】＋【second for time 2】+【week for time 2】＋【mode for time 2】＋【32bit Value1~Value4 for time 2】＋【switch for time 2】＋
	【Timer switch for time 3】＋【10 digit and single digit of year for time 3】＋【month for time 3】＋【day for time 3】+【hour for time 3】＋【minute for time 3】＋【second for time 3】+【week for time 3】＋【mode for time 3】＋【32bit Value1~Value4 for time 3】＋【switch for time 3】＋
	【Timer switch for time 4】＋【10 digit and single digit of year for time 4】＋【month for time 4】＋【day for time 4】+【hour for time 4】＋【minute for time 4】＋【second for time 4】+【week for time 4】＋【mode for time 3】＋【32bit Value1~Value4 for time 4】＋【switch for time 4】＋
	【Timer switch for time 5】＋【10 digit and single digit of year for time 5】＋【month for time 5】＋【day for time 5】+【hour for time 5】＋【minute for time 5】＋【second for time 5】+【week for time 5】＋【mode for time 5】＋【32bit Value1~Value4 for time 5】＋【switch for time 5】＋   
	【Timer switch for time 6】＋【10 digit and single digit of year for time 6】＋【month for time 6】＋【day for time 6】+【hour for time 6】＋【minute for time 6】＋【second for time 6】+【week for time 6】＋【mode for time 6】＋【32bit Value1~Value4 for time 6】＋【switch for time 6】＋
	【reserved for future use:0x0】＋【check digit】(length of command:88)
---------------------------------------		
0x31	command of setting color and color temperature	
Send	【0X31】+【8bit red data 】+【8bit green data】+【8bit blue data】+【8bit warm white data】+【8bit status sign】+【0xF0 remote,0x0F local】+【check digit】(length of command:8)

Return	Local(0x0F):no return
	Remote(0xF0):【0xF0 remote】+ 【0X31】+【0x00】+【check digit】                                                                Status sign:【0XF0】 means changing RGB,【0X0F】means W	
	Note:phone send commands which control static color.Range of static color value is 00-0xff.When value is 0,PWM is 0%;when value is 0XFF,PWM is 100%;	
---------------------------------------		
0x41	command of setting color,color temperature(in music mode,this value will not be saved)	
Send	【0X41】+【8bit red value】+【8bit green data】+【8bit blue data】+【8bit warm white data】+【8bit status sign】+【0x0F local】+【check digit】(length of command:8)

Return	No return	
	Status sign:【0XF0】means changing RGB,【0X0F】means changing W	
---------------------------------------
0x51	command of setting command of custom mode	
Send	【0X51】+【First point 32bit color value】+【Second point 32bit color value】+【Third point 32bit color value】+
          【Fourth point 32bit color value】+【Fifth point 32bit color value】+【Sixth point 32bit color value】+
          【Seventh point 32bit color value】+【Eighth point 32bit color value】+【Ninth point 32bit color value】+
          【Tenth point 32bit color value】+【Eleventh point 32bit color value】+【Twelfth point 32bit color value】+
          【Thirteenth point 32bit color value】+【Fourteenth point 32bit color value】+【Fifteenth point 32bit color value】+
          【Sixteenth point 32bit color value】+【8bit speed value】+【8bit mode value】+
          【0XFF】+【0xF0 remote,0x0F local】+【check digit】(length of command:55)"
          
Return	"If command is local(0x0F):no return
	If command is remote (0xF0):【0xF0 remote】+ 【0X51】+【0x00】+【check digit】"	
	Note:In command of custom mode,value of RGB cannot be 0x01,0x02,0x03.Otherwise,RGB is over.	
---------------------------------------		
0x52	command of requesting custom mode's content 	
Send	【0X52】+【0X5A】+【0X5B】+【0xF0 remote,0x0F local】+【check digit】(length of command:5)

Return	"【0xF0 remote,0x0F local】+【【0X52】+【First point 32bit color value】+【Second point 32bit color value】+【Third point 32bit color value】+
          【Fourth point 32bit color value】+【Fifth point 32bit color value】+【Sixth point 32bit color value】+
          【Seventh point 32bit color value】+【Eighth point 32bit color value】+【Ninth point 32bit color value】+
          【Tenth point 32bit color value】+【Eleven point 32bit color value】+【Twelfth point 32bit color value】+
          【Thirteenth point 32bit color value】+【Fourteenth point 32bit color value】+【Fifteenth point 32bit color value】+
          【Sixteenth point 32bit color value】+【8bit speed value】+【8bit changing mode value】+
          【0XFF】+【check digit】(length of command:70)"	
---------------------------------------		
0x61	command of setting builted-in mode	
Send	【0x61】+【8bit mode value】+【8bit speed value】+【0xF0 remote,0x0F local】+【check digit】(length of command:5)	
Return	If command is local(0x0F):no return
	If command is remote (0xF0):【0xF0 remote】+ 【0X61】+【0x00】+【check digit】"	
	Note:mode value refers to definition in the end of file,range of speed value is 0x01--0x1F	
---------------------------------------		
0x71	command of setting key's value(switcher command) command

Send	【0X71】+【8bit value】+【0xF0remote,0x0F local】+【check digit】(length of command:4)	
Return	【0xF0remote,0x0F local】+ 【0X71】+【switcher status value】+【check digit】	
	Note:key value0x23 means "turn on",0x24 means "turn off"	
---------------------------------------		
0x81	command of requesting devices'status	
Send	【0X81】+【0X8A】+【0X8B】+【check digit】(length of comman:4)

Return	【0X81】+【8bit device name】+【8bit turn on/off】+【8bit mode value】+【8bit run/pause】+ 【8bit speed value】+【8bit red value】+【8bit green data】+【8bit blue data】+  【8bit warm white data】+【version NO】+【8bit cool white data】+【8bit status sign】+【check digit】(length of command:14)	
	"Note:when module received command of checking devices's status,module will reply,
	【8bit turn on/off】:0x23 means  turn on;0x24 means  turn off
	【8bit run/pause status】:0x20 means  status in present,0x21 means  pause status,it is unuseful in this item
	【8bit speed value】means speed value of dynamic model,range:0x01-0x1f,0x01 is the fast
	Status sign:【0XF0】means RGB,【0X0F】means W"	
---------------------------------------		
			RGBW value definition		
COLOR		R					0x00--0xFF
		G					0x00--0xFF
		B					0x00--0xFF
		W					0x00--0xFF
		C					0x00--0xFF
SPEED		SPEED					0x01--0x1F (Fastest -Slowest)
KEY		PAUSE					0x20
	 	PLAY					0x21
		SWITCH					0x22
		POWER ON				0x23
		POWER OFF				0x24
CHANGING	gradual					0x3A
		jump					0x3B
		flash					0x3C
MODE	1:7 colors gradual change		0x25
		2:red gradual change			0x26
		3:green gradual change			0x27
		4:glue gradual change			0x28
		5:yellow gradual change			0x29
		6:cyan gradual change			0x2A
		7:purple gradual change			0x2B
		8:white gradual change			0x2C
		9:red and green gradual change		0x2D
		10:red and blue gradual change		0x2E
		11:green and blue gradual change	0x2F
		12:7 colors stroboflash			0x30
		13:red stroboflash			0x31
		14:green stroboflash			0x32
		15:glue stroboflash			0x33
		16:yellow stroboflash			0x34
		17:cyan stroboflash			0x35
		18:purple stroboflash			0x36
		19:white stroboflash			0x37
		20:7 colors jump change			0x38
		custom Mode				0x60
		static Mode				0x61
		music Mode				0x62
		testing Mode			0x63
	
Description check digit:a byte sum which a whole command minus "check digit" 

* Object creation: $light = new Milight('x.y.z.w'); // where x.y.z.w is the wifi bridge IP address on the LAN


* 1-2016 first version
* 9-2016 state feedback added
* 10-2016 repetition of orders added
*
AriluxRGBWW
OnBrightness
OnBrightnessWhite -> true white
OnBrightnessWhite2

MagicUfo + AriluxRGBW
OnBrightness
OnBrightnessWhite -> true white

AriluxRGB
OnBrightness
OnBrightnessWhite -> with 3 colors

AriluxSPIRGB
OnBrightness
OnBrightnessWhite -> with 3 colors

*/
require_once dirname(__FILE__) . '/include/common.php';

class W2_MagicUFOBase
{

	protected $_host;
	protected $_port;
	protected $_wait;
	protected $_repeat;
	protected $_increm;
	protected $_color = array(0,0,0); // rgb color
	protected $_delay = 100; //microseconds
	protected $_commandCodes = array(0,0,0,0,0,0,0,0);
	protected $_EOFCtrl = 0x00;
	protected $_return ;
	protected $_log;

	public function __construct($host = '192.168.1.110', $wait=0, $repeat=1, $increm=10, $ID=0, $LocalId="", $nbLeds=0, $colorOrder=0, $port = 5577) {
		$this->_host = $host;
		$this->_port = $port;
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
		//log::add($this->_log,'debug','Get color hex : '.$hex);
		return $hex;
	}

	public function SetColor($Col) {
		$this->_color=$this->hex2rgb($Col);
		return 0;
	}
	
	public function getStatus() {
		$this->_commandCodes[0] =0x81;
		$this->_commandCodes[1] =0x8A;
		$this->_commandCodes[2] =0x8B;
		$this->_commandCodes[3]= $this->CheksumCalc($this->_commandCodes,3);
		$ret = $this->Send(4,14);
		return $ret; // return $this->Send(4,11);

	}
	public function retStatus() {
		$OutStr = $this->getStatus();
		for($i = 0; $i < strlen($OutStr); $i++){
			$Out[$i] = str_pad(ord($OutStr[$i]), 2, '0', STR_PAD_LEFT);
		}
		if ( isset($OutStr) && $OutStr!=NOSOCKET && $OutStr!=NOTCONNECTED && $OutStr!=NORESPONSE ) {		
			if ($Out[0]==0x81) {
				// col from controller
				$Col[0]=$Out[6];
				$Col[1]=$Out[7];
				$Col[2]=$Out[8];
				$this->_return['Color'] = $this->rgb2hex($Col);	
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
				$speed= round((33-$Out[5])*100/32);
				$this->_return['Speed'] = intval($speed);			
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
				
				$white=round($Out[9]*100/255);
				$this->_return['White'] = intval($white);
			}
			else
				$this->_return['Type'] = 'Not a compatible MagicHome controller';
		}
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
				log::add($this->_log,'debug',"socket_connect() failed" );
				return NOTCONNECTED ;
			}
			else {			
				log::add($this->_log,'debug','try to Send');	
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
							} while ( strlen($out)<$responseLength && $Icpt2<33);
							if ( strlen($out)<$responseLength){
								$out[0]=0;	// incomplete response : ignore datagram
							}
							if ( strlen($out)>$responseLength){
								// sometimes the datagram contains more than one 0x81
								do {
									$out = substr ( $out , 1 );
								} while ( $out[0]!=0x81 &&  strlen($out)>$responseLength);
							}
							$mess="";
							for ($iVal=0;$iVal<strlen($out);$iVal++){
								$mess=$mess.dechex(ord($out[$iVal]))." ";
							}
							log::add($this->_log,'debug','return : '.$mess);	
							if ( strlen($out) == $responseLength &&	$out[0]!=0x81 ) {
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
		$this->_commandCodes[0] =0x71;
		$this->_commandCodes[1] =0x23;
		$this->_commandCodes[2] =0x0F;
		$this->_commandCodes[3]= $this->CheksumCalc($this->_commandCodes,3);
		return $this->Send(4,0);
	}

	public function Off() {
		$this->_commandCodes[0] =0x71;
		$this->_commandCodes[1] =0x24;
		$this->_commandCodes[2] =0x0F;
		$this->_commandCodes[3]= $this->CheksumCalc($this->_commandCodes,3);
		return $this->Send(4,0);
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
		$this->OnBrightnessWhite(5,'#000000');
	}
	
	public function OnWhite() {
		$this->OnBrightnessWhite(100,'#000000');
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
		$Bright =0;
		return $this->OnColor($hex,$Bright,$White,0);
	}
	public function BrightnessW1Increase($value=50,$color='#000000') {
		$value=$value+$this->_increm;
		if ($value<0) $value=0;
		if ($value>100) $value=100;
		$this-> OnBrightnessWhite($value,$color);
		return $value;
	}
	public function BrightnessW1Decrease($value=50,$color='#000000') {
		$value=$value-$this->_increm;
		if ($value<0) $value=0;
		if ($value>100) $value=100;
		$this-> OnBrightnessWhite($value,$color);
		return $value;
	}
	public function OnBrightnessWhite($Intensity=50,$color='#000000') {
		//log::add($this->_log,'debug','bright');
		$Out = $this->getStatus();
		//log::add($this->_log,'debug',"ret from Status :".$Out[0]);
	
		if (isset($Out[0]) &&  isset($Out[6]) &&  isset($Out[7]) &&  isset($Out[8])){
			
			//log::add($this->_log,'debug',"out :".ord($Out[0]));
			
			if (ord($Out[0])==0x81) {
				//log::add($this->_log,'debug',"out0OK :".$Out[9]);
				$this->_commandCodes[1]=ord($Out[6]);
				$this->_commandCodes[2]=ord($Out[7]);
				$this->_commandCodes[3]=ord($Out[8]);
			}
			else {
				$Col=$this->hex2rgb($color);
				$this->_commandCodes[1] = $Col[0];
				$this->_commandCodes[2] = $Col[1];
				$this->_commandCodes[3] = $Col[2];
				log::add($this->_log,'debug','Bad state');
			}
			
		}
		else {		
				$Col=$this->hex2rgb($color);
				$this->_commandCodes[1] = $Col[0];
				$this->_commandCodes[2] = $Col[1];
				$this->_commandCodes[3] = $Col[2];
				log::add($this->_log,'debug','No state');
		}
		$Intensity=$Intensity*255/100;
		if ($Intensity>255) $Intensity=255;
		if ($Intensity<0) $Intensity=0;
		//log::add($this->_log,'debug','ret from Status :');
		$this->_commandCodes[0] = 0x31;
		$this->_commandCodes[4] = $Intensity;
		$this->_commandCodes[5] = $this->_EOFCtrl;
		$this->_commandCodes[6] = 0x0F;
		$this->_commandCodes[7]= $this->CheksumCalc($this->_commandCodes,7);
		return $this->Send(8,0);
	}
	public function OnColor($color='Mint',$Bright=0,$White=0,$White2=0) {
		$Out = $this->getStatus();

		if (isset($Out[0]) &&  isset($Out[9])){
			
			//log::add($this->_log,'debug',"out :".ord($Out[0]));
			
			if (ord($Out[0])==0x81) {
				$this->_commandCodes[4]=ord($Out[9]);				
			}
			else {
				$this->_commandCodes[4] = round($White*255/100);
			}			
		}
		else {		
			log::add($this->_log,'debug','State return NOK');
			$this->_commandCodes[4] = round($White*255/100);
		}

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

		
		$this->_commandCodes[0] = 0x31;
		$this->_commandCodes[5] = $this->_EOFCtrl; //$this->_commandCodes[5] = 0xF0;
		$this->_commandCodes[6] = 0x0F;
		$this->_commandCodes[7]= $this->CheksumCalc($this->_commandCodes,7);		
		$this->_color[0] = $this->_commandCodes[1];
		$this->_color[1] = $this->_commandCodes[2];
		$this->_color[2] = $this->_commandCodes[3];
		return $this->Send(8,0);
		//log::add($this->_log,'debug','Color memorized by MagicOnCOlor = '.$this->_color[0]." - ".$this->_color[1]." - ".$this->_color[2]);
	}

	public function OnDisco($prg,$speed) {
		
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
		// custom Mode				0x60
		// static Mode				0x61
		// music Mode				0x62
		// testing Mode				0x63
		$Out = $this->getStatus();
		if ($prg<0x1) {
			$prg=0x1;
		}
		$prg=$prg+0x24;
		if ($prg>=0x38) {
			$prg=$prg+0x27;
		}
		if ($prg>0x63)
			$prg=0x63;
		
		if (isset($Out[0]) &&  isset($Out[5])){
			if (ord($Out[0])==0x81) {
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

		$this->_commandCodes[0] =0x61;
		$this->_commandCodes[1] =$prg;
		$this->_commandCodes[2] =$speed;
		$this->_commandCodes[3] =0x0F;	
		$this->_commandCodes[4]= $this->CheksumCalc($this->_commandCodes,4);
		return $this->Send(5,0);
	}
	public function DiscoSpeed($speed,$prog=5) {	
		if ($prog<0x1) {
			$prog=0x1;
		}
		$prog=$prog+0x24;
		if ($prog>=0x3d && $prog<0x41) {
			$prog=$prog+23;
		}
		if ($prog>0x63)
			$prog=0x63;
		$Out = $this->getStatus();
		if (isset($Out[0]) &&  isset($Out[3])){
			if (ord($Out[0])==0x81) {
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
		$this->_commandCodes[0] =0x61;
		$this->_commandCodes[1] =$prg;
		$this->_commandCodes[2] =$speed;
		$this->_commandCodes[3] =0x0F;	
		$this->_commandCodes[4]= $this->CheksumCalc($this->_commandCodes,4);
		return $this->Send(5,0);
	}
	public function DiscoMin($prog) {
		return $this->DiscoSpeed(1,$prog);
	}

	public function DiscoMid($prog) {
		return $this->DiscoSpeed(50,$prog);
	}

	public function DiscoMax($prog) {
		return $this->DiscoSpeed(100,$prog);
	}
	
	public function DiscoSlower() {

	}

	public function DiscoFaster() {
	
	}

	
	public function Pause() {
		$this->_commandCodes[0] =0x71;
		$this->_commandCodes[1] =0x20;
		$this->_commandCodes[2] =0x0F;
		$this->_commandCodes[3]= $this->CheksumCalc($this->_commandCodes,3);
		return $this->Send(4,0);
	}
	public function Play() {
		$this->_commandCodes[0] =0x71;
		$this->_commandCodes[1] =0x21;
		$this->_commandCodes[2] =0x0F;
		$this->_commandCodes[3]= $this->CheksumCalc($this->_commandCodes,3);
		return $this->Send(4,0);
	}
	public function SwitchBt() {
		$this->_commandCodes[0] =0x71;
		$this->_commandCodes[1] =0x22;
		$this->_commandCodes[2] =0x0F;
		$this->_commandCodes[3]= $this->CheksumCalc($this->_commandCodes,3);
		return $this->Send(4,0);
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

	public function CheksumCalc($tab,$iNbVal)  {
        $iVal=0;
		for ($iBcl=0;$iBcl<$iNbVal;$iBcl++){
			$iVal=$iVal+$tab[$iBcl];
		}
		return($iVal & 0xFF);
    }
	

}
class W2_MagicUFO extends W2_MagicUFOBase {
	public function retStatus() {
		$OutStr = $this->getStatus();
		for($i = 0; $i < strlen($OutStr); $i++){
			$Out[$i] = str_pad(ord($OutStr[$i]), 2, '0', STR_PAD_LEFT);
		}
		if ( isset($OutStr) && $OutStr!=NOSOCKET && $OutStr!=NOTCONNECTED && $OutStr!=NORESPONSE && $OutStr!=BADRESPONSE) {		
			if ($Out[0]==0x81) {
				// col from controller
				$Col[0]=$Out[6];
				$Col[1]=$Out[7];
				$Col[2]=$Out[8];
				$this->_return['Color'] = $this->rgb2hex($Col);	
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
				$speed= round((33-$Out[5])*100/32);
				$this->_return['Speed'] = intval($speed);			
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
				
				$white=round($Out[9]*100/255);
				$this->_return['White'] = intval($white);
			}
			else
				$this->_return['Type'] = 'Not a compatible MagicHome controller';
		}
		else if ( !isset($OutStr) )
			$this->_return = BADRESPONSE;
		else
			$this->_return = $OutStr;
		return $this->_return;	
	}
}
class W2_AriluxRGBW extends W2_MagicUFOBase {
	public function retStatus() {
		$OutStr = $this->getStatus();
		for($i = 0; $i < strlen($OutStr); $i++){
			$Out[$i] = str_pad(ord($OutStr[$i]), 2, '0', STR_PAD_LEFT);
		}
		if ( isset($OutStr) && $OutStr!=NOSOCKET && $OutStr!=NOTCONNECTED && $OutStr!=NORESPONSE && $OutStr!=BADRESPONSE) {		
			if ($Out[0]==0x81) {
				// col from controller
				$Col[0]=$Out[6];
				$Col[1]=$Out[7];
				$Col[2]=$Out[8];
				$this->_return['Color'] = $this->rgb2hex($Col);	
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
				$speed= round((33-$Out[5])*100/32);
				$this->_return['Speed'] = intval($speed);			
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
				$white=round($Out[9]*100/255);
				$this->_return['White'] = intval($white);
			}
			else
				$this->_return['Type'] = 'Not an Arilux C03';
		}
		else if ( !isset($OutStr) )
			$this->_return = BADRESPONSE;
		else
			$this->_return = $OutStr;
		return $this->_return;	
	}
	
}

class W2_AriluxRGB extends W2_MagicUFOBase {
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
	public function retStatus() {
		$OutStr = $this->getStatus();
		for($i = 0; $i < strlen($OutStr); $i++){
			$Out[$i] = str_pad(ord($OutStr[$i]), 2, '0', STR_PAD_LEFT);
		}
		if ( isset($OutStr) && $OutStr!=NOSOCKET && $OutStr!=NOTCONNECTED && $OutStr!=NORESPONSE && $OutStr!=BADRESPONSE) {		
			if ($Out[0]==0x81) {
				// col from controller
				$Col[0]=$Out[6];
				$Col[1]=$Out[7];
				$Col[2]=$Out[8];
				$this->_return['Color'] = $this->rgb2hex($Col);	
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
				$speed= round((33-$Out[5])*100/32);
				$this->_return['Speed'] = intval($speed);			
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
				$this->_return['Type'] = 'Not an Arilux C01';
		}
		else if ( !isset($OutStr) )
			$this->_return = BADRESPONSE;
		else
			$this->_return = $OutStr;
		return $this->_return;	
	}
}

class W2_AriluxRGBWW extends W2_MagicUFOBase {
	protected $_commandCodes = array(0,0,0,0,0,0,0,0,0);
	public function retStatus() {
		$OutStr = $this->getStatus();
		for($i = 0; $i < strlen($OutStr); $i++){
			$Out[$i] = str_pad(ord($OutStr[$i]), 2, '0', STR_PAD_LEFT);
		}
		if ( isset($OutStr) && $OutStr!=NOSOCKET && $OutStr!=NOTCONNECTED && $OutStr!=NORESPONSE && $OutStr!=BADRESPONSE) {		
			if ($Out[0]==0x81) {
				// col from controller
				$Col[0]=$Out[6];
				$Col[1]=$Out[7];
				$Col[2]=$Out[8];
				$this->_return['Color'] = $this->rgb2hex($Col);	
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
				$speed= round((33-$Out[5])*100/32);
				$this->_return['Speed'] = intval($speed);			
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
				
				$white=round($Out[9]*100/255);
				$this->_return['White'] = intval($white);
				
				$white=round($Out[11]*100/255);
				$this->_return['White2'] = intval($white);
			}
			else
				$this->_return['Type'] = 'Not an Arilux C06 or Sunix';
		}
		else if ( !isset($OutStr) )
			$this->_return = BADRESPONSE;
		else
			$this->_return = $OutStr;
		return $this->_return;	
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
		$this->OnBrightnessWhite(5,'#000000');
	}
	
	public function OnWhite() {
		$this->OnBrightnessWhite(100,'#000000');
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
	public function OnWhite2() {
		$this->OnBrightnessWhite2(100,'#000000',0);
	}
	public function BrightnessW2Increase($value=50,$color='#000000',$Intensity=0) {
		return $this-> BrightnessW2IncreaseP($value,$color,$Intensity);
	}
	public function BrightnessW2IncreaseP($value=50,$color='#000000',$Intensity=0) {
		$value=$value+$this->_increm;
		if ($value<0) $value=0;
		if ($value>100) $value=100;
		$this-> OnBrightnessWhite2($value,$color,$Intensity);
		return $value;
	}
	public function BrightnessW2Decrease($value=50,$color='#000000',$Intensity=0) {
		return $this-> BrightnessW2DecreaseP($value,$color,$Intensity);
	}
	public function BrightnessW2DecreaseP($value=50,$color='#000000',$Intensity=0) {
		$value=$value-$this->_increm;
		if ($value<0) $value=0;
		if ($value>100) $value=100;
		$this-> OnBrightnessWhite2($value,$color,$Intensity);
		return $value;
	}
	public function OnBrightnessWhite2($Intensity2=50,$color='#000000',$Intensity=0) {
		//log::add($this->_log,'debug','bright');
		$this->_EOFCtrl = 0;
		$this->BrightnessWhite2($Intensity2,$color,$Intensity);
	}
	public function BrightnessWhite2($Intensity2=50,$color='#000000',$Intensity=0) {
		// white2 intensity, absolute color (ie with brightness), white2 intensity
		//log::add($this->_log,'debug','bright');
		$Out = $this->getStatus();
		//log::add($this->_log,'debug',"ret from Status :".$Out[0]);
	
		if (isset($Out[0]) &&  isset($Out[6]) &&  isset($Out[7]) &&  isset($Out[8]) &&  isset($Out[11])){
			
			//log::add($this->_log,'debug',"out :".ord($Out[0]));
			
			if (ord($Out[0])==0x81) {
				//log::add($this->_log,'debug',"out0OK :".$Out[9]);
				$this->_commandCodes[1]=ord($Out[6]);
				$this->_commandCodes[2]=ord($Out[7]);
				$this->_commandCodes[3]=ord($Out[8]);
				$this->_commandCodes[4]=ord($Out[9]);
			}
			else {
				$Col=$this->hex2rgb($color);
				$this->_commandCodes[1] = $Col[0];
				$this->_commandCodes[2] = $Col[1];
				$this->_commandCodes[3] = $Col[2];
				$Intensity=$Intensity*255/100;
				if ($Intensity>255) $Intensity=255;
				if ($Intensity<0) $Intensity=0;
				$this->_commandCodes[4]=$Intensity;
				log::add($this->_log,'debug','Bad state');
			}
			
		}
		else {		
				$Col=$this->hex2rgb($color);
				$this->_commandCodes[1] = $Col[0];
				$this->_commandCodes[2] = $Col[1];
				$this->_commandCodes[3] = $Col[2];
				$Intensity=$Intensity*255/100;
				if ($Intensity>255) $Intensity=255;
				if ($Intensity<0) $Intensity=0;
				$this->_commandCodes[4]=$Intensity;
				log::add($this->_log,'debug','No state');
		}
		$Intensity2=$Intensity2*255/100;
		if ($Intensity2>255) $Intensity2=255;
		if ($Intensity2<0) $Intensity2=0;
		//log::add($this->_log,'debug','ret from Status :');
		$this->_commandCodes[0] = 0x31;
		$this->_commandCodes[5] = $Intensity2;
		$this->_commandCodes[6] = $this->_EOFCtrl;
		$this->_commandCodes[7] = 0x0F;
		$this->_commandCodes[8]= $this->CheksumCalc($this->_commandCodes,8);
		return $this->Send(9,0);
	}
	public function BrightnessW1Increase($value=50,$color='#000000',$Intensity=0) {
		return $this-> BrightnessW1IncreaseP($value,$color,$Intensity);
	}
	public function BrightnessW1IncreaseP($value=50,$color='#000000',$Intensity=0) {
		$value=$value+$this->_increm;
		if ($value<0) $value=0;
		if ($value>100) $value=100;
		$this-> OnBrightnessWhite($value,$color,$Intensity);
		return $value;
	}
	public function BrightnessW1Decrease($value=50,$color='#000000',$Intensity=0) {
		return $this-> BrightnessW1DecreaseP($value,$color,$Intensity);
	}
	public function BrightnessW1DecreaseP($value=50,$color='#000000',$Intensity=0) {
		$value=$value-$this->_increm;
		if ($value<0) $value=0;
		if ($value>100) $value=100;
		$this-> OnBrightnessWhite($value,$color,$Intensity);
		return $value;
	}
	public function OnBrightnessWhite($Intensity2=50,$color='#000000',$Intensity=0) {
		//log::add($this->_log,'debug','bright');
		$this->_EOFCtrl = 0;
		$this->BrightnessWhite($Intensity2,$color,$Intensity);
	}
	public function BrightnessWhite($Intensity2=50,$color='#000000',$Intensity=0) {
		//log::add($this->_log,'debug','bright');
		$Out = $this->getStatus();
		//log::add($this->_log,'debug',"ret from Status :".$Out[0]);
	
		if (isset($Out[0]) &&  isset($Out[6]) &&  isset($Out[7]) &&  isset($Out[8]) &&  isset($Out[11])){
			
			//log::add($this->_log,'debug',"out :".ord($Out[0]));
			
			if (ord($Out[0])==0x81) {
				//log::add($this->_log,'debug',"out0OK :".$Out[9]);
				$this->_commandCodes[1]=ord($Out[6]);
				$this->_commandCodes[2]=ord($Out[7]);
				$this->_commandCodes[3]=ord($Out[8]);
				$this->_commandCodes[5]=ord($Out[11]);
				//log::add($this->_log,'debug','Good state');
			}
			else {
				$Col=$this->hex2rgb($color);
				$this->_commandCodes[1] = $Col[0];
				$this->_commandCodes[2] = $Col[1];
				$this->_commandCodes[3] = $Col[2];
				$Intensity2=$Intensity2*255/100;
				if ($Intensity2>255) $Intensity2=255;
				if ($Intensity2<0) $Intensity2=0;
				$this->_commandCodes[5]=$Intensity2;
				log::add($this->_log,'debug','Bad state');
			}
			
		}
		else {		
				$Col=$this->hex2rgb($color);
				$this->_commandCodes[1] = $Col[0];
				$this->_commandCodes[2] = $Col[1];
				$this->_commandCodes[3] = $Col[2];
				$Intensity2=$Intensity2*255/100;
				if ($Intensity2>255) $Intensity2=255;
				if ($Intensity2<0) $Intensity2=0;
				$this->_commandCodes[5]=$Intensity2;
				log::add($this->_log,'debug','No state');
		}
		$Intensity=$Intensity*255/100;
		if ($Intensity>255) $Intensity=255;
		if ($Intensity<0) $Intensity=0;
		$this->_commandCodes[0] = 0x31;
		$this->_commandCodes[4] = $Intensity;
		$this->_commandCodes[6] = $this->_EOFCtrl;
		$this->_commandCodes[7] = 0x0F;
		$this->_commandCodes[8]= $this->CheksumCalc($this->_commandCodes,8);
		return $this->Send(9,0);
	}
	public function OnColor($color='Mint',$Bright=0,$White1=0,$White2=0) {
		$this->_EOFCtrl = 0;
		$this->Color($color,$Bright,$White1,$White2);
	}	
	public function Color($color='Mint',$Bright=0,$White1=0,$White2=0) {
		$Out = $this->getStatus();

		if (isset($Out[0]) &&  isset($Out[9])&&  isset($Out[11])){		
			//log::add($this->_log,'debug',"out :".ord($Out[0]));
			
			if (ord($Out[0])==0x81) {
				$this->_commandCodes[4]=ord($Out[9]);
				$this->_commandCodes[5]=ord($Out[11]);				
			}
			else {
				$this->_commandCodes[4] = round($White1*255/100);
				$this->_commandCodes[5] = round($White2*255/100);
			}
			
		}
		else {		
			log::add($this->_log,'debug','State return NOK');
			$this->_commandCodes[4] = round($White1*255/100);
		}

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
		$this->_commandCodes[0] = 0x31;
		$this->_commandCodes[6] = $this->_EOFCtrl;
		$this->_commandCodes[7] = 0x0F;
		$this->_commandCodes[8]= $this->CheksumCalc($this->_commandCodes,8);		
		$this->_color[0] = $this->_commandCodes[1];
		$this->_color[1] = $this->_commandCodes[2];
		$this->_color[2] = $this->_commandCodes[3];
		return $this->Send(9,0);
		//log::add($this->_log,'debug','Color memorized by MagicOnCOlor = '.$this->_color[0]." - ".$this->_color[1]." - ".$this->_color[2]);
	}
}
class W2_MagicUFOBulbV4 extends W2_MagicUFOBase {
	public function OnBrightnessWhite($Intensity=50,$color='#000000') {
		$Intensity=$Intensity*255/100;
		if ($Intensity>255) $Intensity=255;
		if ($Intensity<0) $Intensity=0;
		$this->_commandCodes[0] = 0x31;
		$this->_commandCodes[1] = 0;
		$this->_commandCodes[2] = 0;
		$this->_commandCodes[3] = 0;
		$this->_commandCodes[4] = $Intensity;
		$this->_commandCodes[6] = $this->_EOFCtrl;
		$this->_commandCodes[7] = 0x0F;
		$this->_commandCodes[8]= $this->CheksumCalc($this->_commandCodes,8);
		return $this->Send(9,0);
	}
	public function OnColor($color='Mint',$Bright=0,$White1=0,$White2=0) {
		parent::OnColor($color='Mint',$Bright,0,0);
	}
}
class W2_Sunix extends W2_AriluxRGBWW {
	public function OnBrightnessWhite2($Intensity2=50,$color='#000000',$Intensity=0) {
		//log::add($this->_log,'debug','bright');
		$this->_EOFCtrl = 0x0F;
		$this->BrightnessWhite2($Intensity2,$color,$Intensity);
	}
	public function OnBrightnessWhite($Intensity2=50,$color='#000000',$Intensity=0) {
		//log::add($this->_log,'debug','bright');
		$this->_EOFCtrl = 0x0F;
		$this->BrightnessWhite($Intensity2,$color,$Intensity);
	}
	public function OnColor($color='Mint',$Bright=0,$White1=0,$White2=0) {
		$this->_EOFCtrl = 0xF0;
		$this->Color($color,$Bright,$White1,$White2);
	}
	public function OnWhite() {
		$this->_EOFCtrl = 0x0F;
		$this->BrightnessWhite(100,'#000000',0);
	}
	public function OnWhite2() {
		$this->_EOFCtrl = 0x0F;
		$this->BrightnessWhite2(100,'#000000',0);
	}
	public function BrightnessW1Increase($value=50,$color='#000000',$Intensity=0) {
		$this->_EOFCtrl = 0x0F;
		return $this-> BrightnessW1IncreaseP($value,$color,$Intensity);
	}
	public function BrightnessW1Decrease($value=50,$color='#000000',$Intensity=0) {
		$this->_EOFCtrl = 0x0F;
		return $this-> BrightnessW1DecreaseP($value,$color,$Intensity);
	}
	public function BrightnessW2Increase($value=50,$color='#000000',$Intensity=0) {
		$this->_EOFCtrl = 0x0F;
		return $this-> BrightnessW2IncreaseP($value,$color,$Intensity);
	}
	public function BrightnessW2Decrease($value=50,$color='#000000',$Intensity=0) {
		$this->_EOFCtrl = 0x0F;
		return $this-> BrightnessW2DecreaseP($value,$color,$Intensity);
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
		$this->OnBrightnessWhite(5,'#000000');
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
	public function OnBrightness($Intensity,$Color='#000000',$White=0) {
		//log::add($this->_log,'debug','intensity');
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
}
class W2_AriluxSPI_RGB extends W2_AriluxRGB {
/*	---------------------------------------		
0x81	command of requesting devices'status	
Send	【0X81】+【0X8A】+【0X8B】+【check digit】(length of comman:4)

Return	【0X81】+【8bit device name】+【8bit turn on/off】+【8bit mode value】+【8bit run/pause】+ 【8bit speed value】+【8bit red value】+【8bit green data】+【8bit blue data】+  【8bit warm white data】+【version NO】+【8bit cool white data】+【8bit status sign】+【check digit】(length of command:14)	
	"Note:when module received command of checking devices's status,module will reply,
	
	
	
	Status sign:【0XF0】means RGB,【0X0F】means W"	
	
	【0X81】+【8bit device name】+【8bit turn on/off】+【8bit mode value High】+【8bit mode value Low】+ 【8bit speed value】+【8bit red value】+【8bit green data】+【8bit blue data】+  【8bit warm white data】+【version NO】+【8bit cool white data】+【8bit status sign】+【check digit】
	【8bit device name】 ?
	【8bit turn on/off】:0x23 means  turn on;0x24 means  turn off
	【8bit mode value H】 【8bit mode value L】means speed value  1 -> 300 + 0x63 0x61 = RGB
	【8bit speed value 】 means speed value  1 -> 100 + 0
	【8bit speed value】+【8bit red value】+【8bit green data】+【8bit blue data】 RGB 0-255
	【8bit warm white data】 ?
	【version NO】 ?
	【8bit cool white data】 ?
	【8bit status sign】 ?
*/
	protected $_nbLeds;
	protected $_colorOrder;
	protected $_type;
	public function retStatus() {
		//log::add($this->_log,'debug','SPI');
		$OutStr = $this->getStatus();
		for($i = 0; $i < strlen($OutStr); $i++){
			$Out[$i] = str_pad(ord($OutStr[$i]), 2, '0', STR_PAD_LEFT);
		}
		if ( isset($OutStr) && $OutStr!=NOSOCKET && $OutStr!=NOTCONNECTED && $OutStr!=NORESPONSE && $OutStr!=BADRESPONSE) {		
			if ($Out[0]==0x81) {
				// col from controller
				$Col[0]=$Out[6];
				$Col[1]=$Out[7];
				$Col[2]=$Out[8];
				$this->_return['Color'] = $this->rgb2hex($Col);	
						
				if ($Out[2]==0x23){
					$this->_return['On'] = 1;
				} 
				else if ($Out[2]==0x24){
					$this->_return['On'] = 0;
				}
				if (($Out[4]==0x61) && ($Out[3]==0)){
					$this->_return['Play'] = 0;
					$this->_return['Prog'] = 0;
				} 
				else {
					$this->_return['Play'] = 1;
					$prog = $Out[3]*256 + $Out[4]-0x63;
					if ($prog>300) $prog=300;
					if ($prog<1) $prog =1;
					$this->_return['Prog'] = $prog;	
				}
				$speed= round(($Out[5]));
				if ($speed>100) $speed=100;
				if ($speed<1) $speed =1;
				$this->_return['Speed'] = intval($speed);
			}
			else
				$this->_return['Type'] = 'Not an SPI RGB Contrôleur';
		}
		else if ( !isset($OutStr) )
			$this->_return = BADRESPONSE;
		else
			$this->_return = $OutStr;
		return $this->_return;	
	}
	public function OnDisco($prg,$speed) {
		
		// 1:7 colors gradual change		0x25
		// to 300
		$Out = $this->getStatus();
		if ($prg>300) {
			$prg=300;
		}
		if ($prg<1) {
			$prg=1;
		}
		$prg=$prg+0x63;
		
		if (isset($Out[0]) &&  isset($Out[5])){
			if (ord($Out[0])==0x81) {
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
		$prgL = $prg % 256;
		$prgH = intval($prg/256);
		$this->_commandCodes[0] =0x61;
		$this->_commandCodes[1] =$prgH;
		$this->_commandCodes[2] =$prgL;
		$this->_commandCodes[3] =$speed;
		$this->_commandCodes[4] =0x0F;	
		$this->_commandCodes[5]= $this->CheksumCalc($this->_commandCodes,5);
		return $this->Send(6,0);
	}
	public function __construct($host = '192.168.1.110', $wait=0, $repeat=1, $increm=10, $ID=0, $LocalId="", $nbLeds=0, $colorOrder=0, $port = 5577) {
		$this->_host = $host;
		$this->_port = $port;
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
		$this->_nbLeds = $nbLeds;
		$this->_colorOrder = $colorOrder;
		$myRet = new wifilightV2c;
		$this->_return = $myRet->_return_WFL;
		$this->_return['Type'] = true;
		$this->_log = $myRet->_log;
	}
	public function DiscoSpeed($speed,$prog=5) {	
		if ($prog<0x1) {
			$prog=0x1;
		}
		$prog=$prog+0x63;
		$Out = $this->getStatus();
		//log::add($this->_log,'debug','ret state');
		if (isset($Out[0]) &&  isset($Out[3]) && isset($Out[4])){
			if (ord($Out[0])==0x81) {
				$prgH = ord($Out[3]);
				$prgL = ord($Out[4]);
			}
			else {
				$prgL = $prog % 256;
				$prgH = intval($prog/256);
				log::add($this->_log,'debug','Bad state');
			}	
		}
		else {		
			log::add($this->_log,'debug','No state');
			$prgL = $prog % 256;
			$prgH = intval($prog/256);
		}		
		if ($speed<0x1)
			$speed=0x1;
		if ($speed>100)
			$speed=100;
		$speed=round($speed);	

		$this->_commandCodes[0] = 0x61;
		$this->_commandCodes[1] = $prgH;
		$this->_commandCodes[2] = $prgL;
		$this->_commandCodes[3] = $speed;
		$this->_commandCodes[4] = 0x0F;
		$this->_commandCodes[5] = $this->CheksumCalc($this->_commandCodes,5);
		return $this->Send(6,0);
	}
  	public function startConfig () {
      	// 10 14 12 09 05 17 1b 1a 03 00 0f 90
        // to send to the controller
		$this->_commandCodes[0] =0x10;
		$this->_commandCodes[1] =0x14;
		$this->_commandCodes[2] =0x12;
		$this->_commandCodes[3] =0x09;
		$this->_commandCodes[4] =0x05;
		$this->_commandCodes[5] =0x17;
		$this->_commandCodes[6] =0x1b;
		$this->_commandCodes[7] =0x1a;
		$this->_commandCodes[8] =0x03;
		$this->_commandCodes[9] =0x00;
		$this->_commandCodes[10] =0x0f;
		$this->_commandCodes[11]= $this->CheksumCalc($this->_commandCodes,11);
		$this->Send(12,0);
    }
}
class W2_AriluxSPI_RGB1 extends W2_AriluxSPI_RGB {
	public function __construct($host = '192.168.1.110', $wait=0, $repeat=1, $increm=10, $ID=0, $LocalId="", $nbLeds=0, $colorOrder=0, $port = 5577) {
		$this->_host = $host;
		$this->_port = $port;
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
		$this->_nbLeds = $nbLeds;
		$this->_colorOrder = $colorOrder;
		$myRet = new wifilightV2c;
		$this->_return = $myRet->_return_WFL;
		$this->_return['Type'] = true;
		$this->_log = $myRet->_log;
	}
	public function config() {
      	$this->startConfig();
		$nbLedsL = $this->_nbLeds % 256;
		$nbLedsH = intval($this->_nbLeds/256);
		$colorOrder = $this->_colorOrder;
		$type=1;
		$this->_commandCodes[0] =0x62;
		$this->_commandCodes[1] =$nbLedsH;
		$this->_commandCodes[2] =$nbLedsL;
		$this->_commandCodes[3] =$type;
		$this->_commandCodes[10] =$colorOrder;
		if ($type == 1) {
		//28 0a 0a 28 01 e0
			$this->_commandCodes[4] =0x28;
			$this->_commandCodes[5] =0x0A;
			$this->_commandCodes[6] =0x0A;
			$this->_commandCodes[7] =0x28;
			$this->_commandCodes[8] =0x01;
			$this->_commandCodes[9] =0xE0;
		}		
		$this->_commandCodes[11] =0xF0;
		$this->_commandCodes[12]= $this->CheksumCalc($this->_commandCodes,12);
		return $this->Send(13,0);
	}
}
class W2_AriluxSPI_RGB2 extends W2_AriluxSPI_RGB {
	public function __construct($host = '192.168.1.110', $wait=0, $repeat=1, $increm=10, $ID=0, $LocalId="", $nbLeds=0, $colorOrder=0, $port = 5577) {
		$this->_host = $host;
		$this->_port = $port;
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
		$this->_nbLeds = $nbLeds;
		$this->_colorOrder = $colorOrder;
		$myRet = new wifilightV2c;
		$this->_return = $myRet->_return_WFL;
		$this->_return['Type'] = true;
		$this->_log = $myRet->_log;
	}
	public function config() {
      	$this->startConfig();
		$nbLedsL = $this->_nbLeds % 256;
		$nbLedsH = intval($this->_nbLeds/256);
		$colorOrder = $this->_colorOrder;
		$type=2;
		$this->_commandCodes[0] =0x62;
		$this->_commandCodes[1] =$nbLedsH;
		$this->_commandCodes[2] =$nbLedsL;
		$this->_commandCodes[3] =$type;
		$this->_commandCodes[10] =$colorOrder;
		if ($type == 2) {
		//12 06 00 12 06 40
			$this->_commandCodes[4] =0x12;
			$this->_commandCodes[5] =0x06;
			$this->_commandCodes[6] =0x00;
			$this->_commandCodes[7] =0x12;
			$this->_commandCodes[8] =0x06;
			$this->_commandCodes[9] =0x40;
		}
		$this->_commandCodes[11] =0xF0;
		$this->_commandCodes[12]= $this->CheksumCalc($this->_commandCodes,12);
		return $this->Send(13,0);
	}
}
class W2_AriluxSPI_RGB3 extends W2_AriluxSPI_RGB {
	public function __construct($host = '192.168.1.110', $wait=0, $repeat=1, $increm=10, $ID=0, $LocalId="", $nbLeds=0, $colorOrder=0, $port = 5577) {
		$this->_host = $host;
		$this->_port = $port;
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
		$this->_nbLeds = $nbLeds;
		$this->_colorOrder = $colorOrder;
		$myRet = new wifilightV2c;
		$this->_return = $myRet->_return_WFL;
		$this->_return['Type'] = true;
		$this->_log = $myRet->_log;
	}
	public function config() {
		$this->startConfig();
		
		$nbLedsL = $this->_nbLeds % 256;
		$nbLedsH = intval($this->_nbLeds/256);
		$colorOrder = $this->_colorOrder;
		$type=3;
		$this->_commandCodes[0] =0x62;
		$this->_commandCodes[1] =$nbLedsH;
		$this->_commandCodes[2] =$nbLedsL;
		$this->_commandCodes[3] =$type;
		$this->_commandCodes[10] =$colorOrder;
		if ($type == 3){
		
			$this->_commandCodes[4] =0x28;
			$this->_commandCodes[5] =0x0A;
			$this->_commandCodes[6] =0x0A;
			$this->_commandCodes[7] =0x28;
			$this->_commandCodes[8] =0x03;
			$this->_commandCodes[9] =0xE8;
		}		
		$this->_commandCodes[11] =0xF0;
		$this->_commandCodes[12]= $this->CheksumCalc($this->_commandCodes,12);
		return $this->Send(13,0);

	}
}
class W2_AriluxSPI_RGB4 extends W2_AriluxSPI_RGB {
	public function __construct($host = '192.168.1.110', $wait=0, $repeat=1, $increm=10, $ID=0, $LocalId="", $nbLeds=0, $colorOrder=0, $port = 5577) {
		$this->_host = $host;
		$this->_port = $port;
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
		$this->_nbLeds = $nbLeds;
		$this->_colorOrder = $colorOrder;
		$myRet = new wifilightV2c;
		$this->_return = $myRet->_return_WFL;
		$this->_return['Type'] = true;
		$this->_log = $myRet->_log;
	}
	public function config() {
        $this->startConfig();
      
		$nbLedsL = $this->_nbLeds % 256;
		$nbLedsH = intval($this->_nbLeds/256);
		$colorOrder = $this->_colorOrder;
		$type=4;
      
		$this->_commandCodes[0] =0x62;
		$this->_commandCodes[1] =$nbLedsH;
		$this->_commandCodes[2] =$nbLedsL;
		$this->_commandCodes[3] =$type;
		$this->_commandCodes[10] =$colorOrder;
		if ($type == 4){
		//0e 0c 06 12 03 E8
			$this->_commandCodes[4] =0x0e;
			$this->_commandCodes[5] =0x0c;
			$this->_commandCodes[6] =0x06;
			$this->_commandCodes[7] =0x12;
			$this->_commandCodes[8] =0x03;
			$this->_commandCodes[9] =0xE8;
		}
		$this->_commandCodes[11] =0xF0;
		$this->_commandCodes[12]= $this->CheksumCalc($this->_commandCodes,12);
		return $this->Send(13,0);

	}
}
class W2_AriluxSPI_RGB5 extends W2_AriluxSPI_RGB {
	public function __construct($host = '192.168.1.110', $wait=0, $repeat=1, $increm=10, $ID=0, $LocalId="", $nbLeds=0, $colorOrder=0, $port = 5577) {
		$this->_host = $host;
		$this->_port = $port;
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
		$this->_nbLeds = $nbLeds;
		$this->_colorOrder = $colorOrder;
		$myRet = new wifilightV2c;
		$this->_return = $myRet->_return_WFL;
		$this->_return['Type'] = true;
		$this->_log = $myRet->_log;
	}
	public function config() {
      	$this->startConfig();
		$nbLedsL = $this->_nbLeds % 256;
		$nbLedsH = intval($this->_nbLeds/256);
		$colorOrder = $this->_colorOrder;
		$type=5;
		$this->_commandCodes[0] =0x62;
		$this->_commandCodes[1] =$nbLedsH;
		$this->_commandCodes[2] =$nbLedsL;
		$this->_commandCodes[3] =$type;
		$this->_commandCodes[10] =$colorOrder;
		if ($type == 5) {
		//0c 0c 06 84 06 40
			$this->_commandCodes[4] =0x0c;
			$this->_commandCodes[5] =0x0c;
			$this->_commandCodes[6] =0x06;
			$this->_commandCodes[7] =0x84;
			$this->_commandCodes[8] =0x06;
			$this->_commandCodes[9] =0x40;
		}		
		$this->_commandCodes[11] =0xF0;
		$this->_commandCodes[12]= $this->CheksumCalc($this->_commandCodes,12);
		return $this->Send(13,0);
	}
}
class W2_AriluxSPI_RGB6 extends W2_AriluxSPI_RGB {
	public function __construct($host = '192.168.1.110', $wait=0, $repeat=1, $increm=10, $ID=0, $LocalId="", $nbLeds=0, $colorOrder=0, $port = 5577) {
		$this->_host = $host;
		$this->_port = $port;
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
		$this->_nbLeds = $nbLeds;
		$this->_colorOrder = $colorOrder;
		$myRet = new wifilightV2c;
		$this->_return = $myRet->_return_WFL;
		$this->_return['Type'] = true;
		$this->_log = $myRet->_log;
	}
	public function config() {
      	$this->startConfig();
		$nbLedsL = $this->_nbLeds % 256;
		$nbLedsH = intval($this->_nbLeds/256);
		$colorOrder = $this->_colorOrder;
		$type=6;
		$this->_commandCodes[0] =0x62;
		$this->_commandCodes[1] =$nbLedsH;
		$this->_commandCodes[2] =$nbLedsL;
		$this->_commandCodes[3] =$type;
		$this->_commandCodes[10] =$colorOrder;
		if ($type == 6) {
		//0c 0c 06 84 06 40
			$this->_commandCodes[4] =0x0c;
			$this->_commandCodes[5] =0x0c;
			$this->_commandCodes[6] =0x06;
			$this->_commandCodes[7] =0x84;
			$this->_commandCodes[8] =0x06;
			$this->_commandCodes[9] =0x40;
		}		
		$this->_commandCodes[11] =0xF0;
		$this->_commandCodes[12]= $this->CheksumCalc($this->_commandCodes,12);
		return $this->Send(13,0);
	}
}
?>
