<?php
/*
* LimitlessLED Technical Developer Opensource API: http://www.limitlessled.com/dev/
* The MIT License (MIT)
*
* Copyright (c) 2016 bcaro (User:bcaro https://www.jeedom.fr/forum/)
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

Object creation: $light = new Milight('x.y.z.w',1,2,2562); // where x.y.z.w is the wifi bridge IP address on the LAN, 1 is the number of repetition of the control,
															2, is the delay in ms between 2 repetitions, 2562 is the wifi Bridge Port

6 Methods for all bulbs type
-whiteSetGroup($grp)	Select Group for Dual White Lights. 0=All groups, 1 to 4 desired group. To use before lighting light on
-whiteGetGroup() 		Return Group for Dual White Lights. 0=All groups, 1 to 4 desired group
-rgbwSetGroup($grp)		Select Group for RGBW Lights. 0=All groups, 1 to 4 desired group. To use before lighting light on
-rgbwGetGroup()			Return Group for RGBW Lights. 0=All groups, 1 to 4 desired group
-rgbwwSetGroup($grp)	Select Group for RGBWW Lights. 0=All groups, 1 to 4 desired group. To use before lighting light on
-rgbwwGetGroup()		Return Group for RGBWW Lights. 0=All groups, 1 to 4 desired group


8 Methods for V6 Bridge
-rgbwwBridgeOn()				Light On selected group and uses Actual Brightness & Color & Mode (ie rgb or w)
-rgbwwBridgeOff()				Light Off selected group
-rgbwwBridgeOnBrightness($val)	Light On selected group and set Brightness to specified value from 0 to 100 ( translated into 0x02 to 0x1e) - This affects only Actual mode (ie rgb or w)-rgbwwBridgeOnNight()			Light On selected group and set Night mode
-rgbwwBridgeOnWhite()			Light On selected group and set mode to White
-rgbwwBridgeOnColor($color)		Light On selected group and set mode to specified Color from 0x00 to 0xFF or a predefined value in the below list or a #rrggbb color code
								Random, Violet, Blue, BabyBlue, Aqua, Mint, SpringGreen, Green, LimeGreen, Yellow, YellowOrange, Orange, Red, Pink, Fuchsia, Lilac, Lavendar
								eg: rgbwwBridgeOnColor(0x33) or rgbwwBridgeOnColor('Mint') or rgbwwBridgeOnColor('#c03378')
-rgbwwBridgeOnDisco($prog)		Light On selected group and set mode to selected Disco program (from 1 to 9) - see below for program description.
								Tuning the desired brightness right after this method is needed - Disco seem to start with Mid brightness as well as Mid DiscoSpeed
								Available disco programs: 
- rgbwwBridgeDiscoSlower() 		Slower disco mode
- rgbwwDiscofaxter() 			Faster disco mode


11 Methods for dual white bulbs
-whiteOn()				Light On selected group and uses Actual Brightness & Color Temperature
-whiteOnMax()			Light On selected group and set Brightness to Max
-whiteOnMid()			Light On selected group and set Brightness to Mid
-whiteOnMin()			Light On selected group and set Brightness to Min
-whiteOnNight()			Light On selected group and set Night mode
-whiteOnWarm()			Light On selected group and set Color Temperature to Full Warm
-whiteOnLukewarm()		Light On selected group and set Color Temperature to Mid Warm/Cool
-whiteOnCool()			Light On selected group and set Color Temperature to Full Cool
-whiteBrightness($dir)	Modify Brightness - To be used right after whiteOn method (10 steps of brightness possible) -1=One step decrease, 1=One step increase
-whiteColor($dir)		Modify Color Temperature - To be used right after whiteOn method (10 steps of color temperature possible) -1=One step cooler, 1=One step warmer
-whiteOff()				Light Off selected group


18 Methods for rgbww bulbs
-rgbwwOn()					Light On selected group and uses Actual Brightness & Color & Mode (ie rgb or w)
-rgbwwOff()					Light Off selected group
-rgbwwOnMax()				Light On selected group and set Brightness to Max for Actual Mode (ie rgb or w) - Note that W and RGB have separate brightness memory
-rgbwwOnMid()				Light On selected group and set Brightness to Mid for Actual Mode (ie rgb or w) - Note that W and RGB have separate brightness memory
-rgbwwOnMin()				Light On selected group and set Brightness to Min for Actual Mode (ie rgb or w) - Note that W and RGB have separate brightness memory
-rgbwwOnBrightness($val)	Light On selected group and set Brightness to specified value from 0 to 100 ( translated into 0x02 to 0x1e) - This affects only Actual mode (ie rgb or w)
-rgbwwOnKelvin ($val)		set Color Temperature to specified value from 0 (cool) to 100 (warm)
-rgbwwOnSaturation ($val)	set Saturation to specified value from 0 (no) to 100 (full)
-rgbwwOnNight()				Light On selected group and set Night mode
-rgbwwOnWhite()				Light On selected group and set mode to White
-rgbwwOnColor($color)		Light On selected group and set mode to specified Color from 0x00 to 0xFF or a predefined value in the below list or a #rrggbb color code
							Random, Violet, Blue, BabyBlue, Aqua, Mint, SpringGreen, Green, LimeGreen, Yellow, YellowOrange, Orange, Red, Pink, Fuchsia, Lilac, Lavendar
							eg: rgbwOnColor(0x33) or rgbwOnColor('Mint') or rgbwOnColor('#c03378')
-rgbwwOnDisco($prog)		Light On selected group and set mode to selected Disco program (from 1 to 9) - see below for program description.
							Tuning the desired brightness right after this method is needed - Disco seem to start with Mid brightness as well as Mid DiscoSpeed
							Available disco programs: 1=rainbowSwirl, 2=whiteFade, 3=rgbwFade, 4=rainbowJump, 5=disco, 6=redTwinkle, 7=greenTwinkle, 8=blueTwinkle, 9=allCombined
-rgbwwDiscoNext()			Light On selected group and set mode to next Disco program (Round Robin 1 to 9 than 1 again) - see above for program description description.
							Tuning the desired brightness right after this method is needed - Disco seem to start with Mid brightness as well as Mid DiscoSpeed
-rgbwwDiscoMin()			Set disco speed to Min - It turns bulb on but does not switch its mode (rgw,w,disco), so this has no effect when not in disco mode
							To be used right after rgbwOnDisco($prog) or rgbwDiscoNext() methods
-rgbwwDiscoMid()			Same as above with speed to Mid
-rgbwwDiscoMax()			Same as above with speed to Max
-rgbwwDiscoSlower() 		Slower disco mode
-rgwwDiscoFaster() 			Faster disco mode


12 Methods for rgbw bulbs
-rgbwOn()				Light On selected group and uses Actual Brightness & Color & Mode (ie rgb or w)
-rgbwOff()				Light Off selected group
-rgbwOnMax()			Light On selected group and set Brightness to Max for Actual Mode (ie rgb or w) - Note that W and RGB have separate brightness memory
-rgbwOnMid()			Light On selected group and set Brightness to Mid for Actual Mode (ie rgb or w) - Note that W and RGB have separate brightness memory
-rgbwOnMin()			Light On selected group and set Brightness to Min for Actual Mode (ie rgb or w) - Note that W and RGB have separate brightness memory
-rgbwOnBrightness($val)	Light On selected group and set Brightness to specified value from 0 to 100 ( translated into 0x02 to 0x1e) - This affects only Actual mode (ie rgb or w)
-rgbwOnNight()			Light On selected group and set Night mode
-rgbwOnWhite()			Light On selected group and set mode to White
-rgbwOnColor($color)	Light On selected group and set mode to specified Color from 0x00 to 0xFF or a predefined value in the below list or a #rrggbb color code
						Random, Violet, Blue, BabyBlue, Aqua, Mint, SpringGreen, Green, LimeGreen, Yellow, YellowOrange, Orange, Red, Pink, Fuchsia, Lilac, Lavendar
						eg: rgbwOnColor(0x33) or rgbwOnColor('Mint') or rgbwOnColor('#c03378')
-rgbwOnDisco($prog)		Light On selected group and set mode to selected Disco program (from 1 to 9) - see below for program description.
						Tuning the desired brightness right after this method is needed - Disco seem to start with Mid brightness as well as Mid DiscoSpeed
						Available disco programs: 1=rainbowSwirl, 2=whiteFade, 3=rgbwFade, 4=rainbowJump, 5=disco, 6=redTwinkle, 7=greenTwinkle, 8=blueTwinkle, 9=allCombined
-rgbwDiscoSlower() 		Slower disco mode
-rgbwDiscoFaster() 		Faster disco mode

*/

/*
* First version(C) by B. Caron - 2016/12/01
*	full milight V6 bridge control
*	
*
*/
//include './include/test.php';
require_once dirname(__FILE__) . '/include/common.php';
class W2_milightV6
{
	protected $_host;
	protected $_port;
	protected $_wait;
	protected $_repeat;
	protected $_repeatOK;
	protected $_ID1;
	protected $_ID2;
	protected $_socket = NULL;
	protected $_increm;
	protected $_color = array(0,0,0,0,0,0,0,0,0);
	protected $_Seq = array(0,0);
	protected $_delay = 101000; //microseconds
	protected $_ActiveGroup; // 0 means all, else group 1 to 4 1 to 8 or 1 to 9999
	
	protected $_return ;
	protected $_log;
	protected $_CodeToSend= array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0xE6, 0x80, 0x00, 0x31, 0x00, 0x00, 0x08, 0x04, 0x01, 0x00, 0x00, 0x00, 0x00, 0x00, 0xFF); //
	protected $_commandCodes = array(
	// common commands
	'rgbwwGetWBID' 			=> array(0x20, 0x00, 0x00, 0x00, 0x16, 0x02, 0x62, 0x3A, 0xD5, 0xED, 0xA3, 0x01, 0xAE, 0x08, 0x2D, 0x46, 0x61, 0x41, 0xA7, 0xF6, 0xDC, 0xAF, 0XD3, 0xE6, 0x00, 0X00, 0x1E), //
	'rgbwwReset'			=> array(0x30, 0x00, 0x00, 0x00, 0x03, 0xFF, 0xFF, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00), //
	'rgbwwReq' 				=> array(0x10, 0x00, 0x00, 0x00, 0x0A, 0x02, 0xD3, 0xE6, 0x11, 0xAC, 0xCF, 0x23, 0xF5, 0x7B, 0xBA, 0x00, 0x00, 0x00), //
	
	
	// RGB bulb commands strip One channel
	'rgbAllOnb1' 			   => array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0xE6, 0x80, 0x00, 0x31, 0x00, 0x00, 0x05, 0x02, 0x09, 0x00, 0x00, 0x00, 0x01, 0x00, 0xFF), //
	'rgbAllOffb1' 			   => array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0xE6, 0x80, 0x00, 0x31, 0x00, 0x00, 0x05, 0x02, 0x0a, 0x00, 0x00, 0x00, 0x01, 0x00, 0xFF), //	
	'rgbDiscoModeDecreaseb1'   => array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0xE6, 0x80, 0x00, 0x31, 0x00, 0x00, 0x05, 0x02, 0x06, 0x00, 0x00, 0x00, 0xFF, 0x00, 0xFF), //
	'rgbDiscoModeIncreaseb1'   => array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0xE6, 0x80, 0x00, 0x31, 0x00, 0x00, 0x05, 0x02, 0x05, 0x00, 0x00, 0x00, 0xFF, 0x00, 0xFF), //	
	'rgbDiscoSlowerb1' 	       => array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0xE6, 0x80, 0x00, 0x31, 0x00, 0x00, 0x05, 0x02, 0x04, 0x00, 0x00, 0x00, 0xFF, 0x00, 0xFF), //
	'rgbDiscoFasterb1' 	       => array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0xE6, 0x80, 0x00, 0x31, 0x00, 0x00, 0x05, 0x02, 0x03, 0x00, 0x00, 0x00, 0xFF, 0x00, 0xFF), //
	'rgbBrightnessDecb1' 	   => array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0xE6, 0x80, 0x00, 0x31, 0x00, 0x00, 0x05, 0x02, 0x02, 0x00, 0x00, 0x00, 0x01, 0x00, 0xFF), //
	'rgbBrightnessIncb1' 	   => array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0xE6, 0x80, 0x00, 0x31, 0x00, 0x00, 0x05, 0x02, 0x01, 0x00, 0x00, 0x00, 0x01, 0x00, 0xFF), //
	'rgbColorb1'  			   => array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0xE6, 0x80, 0x00, 0x31, 0x00, 0x00, 0x05, 0x01, 0x00, 0xFF, 0xFF, 0xFF, 0x03, 0x00, 0xFF),

	
	
	
	// RGBW bulb commands
	'rgbwAllOn' 			=> array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0xE6, 0x80, 0x00, 0x31, 0x00, 0x00, 0x07, 0x03, 0x01, 0x00, 0x00, 0x00, 0x00, 0x00, 0xFF), //
	'rgbwAllOff' 			=> array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0xE6, 0x80, 0x00, 0x31, 0x00, 0x00, 0x07, 0x03, 0x02, 0x00, 0x00, 0x00, 0x00, 0x00, 0xFF), //	
	'rgbwAllNightMode' 		=> array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0xE6, 0x80, 0x00, 0x31, 0x00, 0x00, 0x07, 0x03, 0x06, 0x00, 0x00, 0x00, 0x00, 0x00, 0xFF), //
	'rgbwAllWhiteOn' 		=> array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0xE6, 0x80, 0x00, 0x31, 0x00, 0x00, 0x07, 0x03, 0x05, 0x00, 0x00, 0x00, 0x00, 0x00, 0xFF), //
	'rgbwBrightness' 		=> array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0xE6, 0x80, 0x00, 0x31, 0x00, 0x00, 0x07, 0x02, 0xFF, 0x00, 0x00, 0x00, 0x01, 0x00, 0xFF), //
	'rgbwDiscoMode' 		=> array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0xE6, 0x80, 0x00, 0x31, 0x00, 0x00, 0x07, 0x04, 0xFF, 0x00, 0x00, 0x00, 0xFF, 0x00, 0xFF), //
	'rgbwDiscoSlower' 		=> array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0xE6, 0x80, 0x00, 0x31, 0x00, 0x00, 0x07, 0x03, 0x04, 0x00, 0x00, 0x00, 0xFF, 0x00, 0xFF), //
	'rgbwDiscoFaster' 		=> array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0xE6, 0x80, 0x00, 0x31, 0x00, 0x00, 0x07, 0x03, 0x03, 0x00, 0x00, 0x00, 0xFF, 0x00, 0xFF), //
	'rgbwAllSetToWhite' 	=> array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0xE6, 0x80, 0x00, 0x31, 0x00, 0x00, 0x07, 0x03, 0x5, 0x00, 0x00, 0x00, 0x00, 0x00, 0xFF), //
	'rgbwColor'  			=> array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0xE6, 0x80, 0x00, 0x31, 0x00, 0x00, 0x07, 0x01, 0xFF, 0xFF, 0xFF, 0xFF, 0x03, 0x00, 0xFF),
	
	// V6 bridge commands
	'rgbwwBridgeOff'  =>        array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0xE6, 0x80, 0x00, 0x31, 0x00, 0x00, 0x00, 0x03, 0x04, 0x00, 0x00, 0x00, 0x01, 0x00, 0xFF), //
	'rgbwwBridgeOn'  =>         array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0xE6, 0x80, 0x00, 0x31, 0x00, 0x00, 0x00, 0x03, 0x03, 0x00, 0x00, 0x00, 0x01, 0x00, 0xFF), //
	'rgbwwBridgeDiscoMode' =>   array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0xE6, 0x80, 0x00, 0x31, 0x00, 0x00, 0x00, 0x04, 0xFF, 0x00, 0x00, 0x00, 0x01, 0x00, 0xFF), //
	'rgbwwBridgeDiscoSlower' => array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0xE6, 0x80, 0x00, 0x31, 0x00, 0x00, 0x00, 0x03, 0x01, 0x00, 0x00, 0x00, 0x01, 0x00, 0xFF), //
	'rgbwwBridgeDiscoFaster' => array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0xE6, 0x80, 0x00, 0x31, 0x00, 0x00, 0x00, 0x03, 0x02, 0x00, 0x00, 0x00, 0x01, 0x00, 0xFF), //
	'rgbwwBridgeColor'  =>      array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0xE6, 0x80, 0x00, 0x31, 0x00, 0x00, 0x00, 0x01, 0xFF, 0xFF, 0xFF, 0xFF, 0x01, 0x00, 0xFF),
	'rgbwwBridgeSetToWhite' =>  array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0xE6, 0x80, 0x00, 0x31, 0x00, 0x00, 0x00, 0x03, 0x05, 0x00, 0x00, 0x00, 0x01, 0x00, 0xFF), //
	'rgbwwBridgeBrightness' =>  array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0xE6, 0x80, 0x00, 0x31, 0x00, 0x00, 0x00, 0x02, 0xFF, 0x00, 0x00, 0x00, 0x01, 0x00, 0xFF), //
	
	//RGBWW Bulbs commands
	'rgbwwAllOn' 				=> array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0xE6, 0x80, 0x00, 0x31, 0x00, 0x00, 0x08, 0x04, 0x01, 0x00, 0x00, 0x00, 0x00, 0x00, 0xFF), //
	'rgbwwAllOff'				=> array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0xE6, 0x80, 0x00, 0x31, 0x00, 0x00, 0x08, 0x04, 0x02, 0x00, 0x00, 0x00, 0x00, 0x00, 0xFF), //	
	'rgbwwAllNightMode' 		=> array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0xE6, 0x80, 0x00, 0x31, 0x00, 0x00, 0x08, 0x04, 0x05, 0x00, 0x00, 0x00, 0x00, 0x00, 0xFF), //
	'rgbwwAllWhiteOn' 			=> array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0xE6, 0x80, 0x00, 0x31, 0x00, 0x00, 0x08, 0x05, 0x64, 0x00, 0x00, 0x00, 0x00, 0x00, 0xFF), //
	'rgbwwBrightness' 			=> array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0xE6, 0x80, 0x00, 0x31, 0x00, 0x00, 0x08, 0x03, 0xFF, 0x00, 0x00, 0x00, 0x01, 0x00, 0xFF), //
	'rgbwwKelvin' 				=> array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0xE6, 0x80, 0x00, 0x31, 0x00, 0x00, 0x08, 0x05, 0xFF, 0x00, 0x00, 0x00, 0x04, 0x00, 0xFF), //
	'rgbwwSaturation' 			=> array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0xE6, 0x80, 0x00, 0x31, 0x00, 0x00, 0x08, 0x02, 0xFF, 0x00, 0x00, 0x00, 0x04, 0x00, 0xFF), //
	'rgbwwDiscoMode' 			=> array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0xE6, 0x80, 0x00, 0x31, 0x00, 0x00, 0x08, 0x06, 0xFF, 0x00, 0x00, 0x00, 0xFF, 0x00, 0xFF), //
	'rgbwwDiscoModeDecrease'	=> array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0xE6, 0x80, 0x00, 0x31, 0x00, 0x00, 0x08, 0x04, 0x04, 0x00, 0x00, 0x00, 0xFF, 0x00, 0xFF), //
	'rgbwwDiscoModeIncrease' 	=> array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0xE6, 0x80, 0x00, 0x31, 0x00, 0x00, 0x08, 0x04, 0x03, 0x00, 0x00, 0x00, 0xFF, 0x00, 0xFF), //
	'rgbwwDiscoSlower' 			=> array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0xE6, 0x80, 0x00, 0x31, 0x00, 0x00, 0x08, 0x04, 0x04, 0x00, 0x00, 0x00, 0xFF, 0x00, 0xFF), //
	'rgbwwDiscoFaster' 			=> array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0xE6, 0x80, 0x00, 0x31, 0x00, 0x00, 0x08, 0x04, 0x03, 0x00, 0x00, 0x00, 0xFF, 0x00, 0xFF), //
	'rgbwwAllSetToWhite' 		=> array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0xE6, 0x80, 0x00, 0x31, 0x00, 0x00, 0x08, 0x05, 0x64, 0x00, 0x00, 0x00, 0x00, 0x00, 0xFF), //
	'rgbwwColor'  				=> array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0xE6, 0x80, 0x00, 0x31, 0x00, 0x00, 0x08, 0x01, 0xFF, 0xFF, 0xFF, 0xFF, 0x03, 0x00, 0xFF),
	
	//RGBWW 8 buttons Bulbs commands
	'rgbwwAllOn8b'             => array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0x04, 0x80, 0x00, 0x31, 0x00, 0x00, 0x0a, 0x06, 0x01, 0x00, 0x00, 0x00, 0x00, 0x00, 0x43), //
	'rgbwwAllOff8b'            => array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0x04, 0x80, 0x00, 0x31, 0x00, 0x00, 0x0a, 0x06, 0x02, 0x00, 0x00, 0x00, 0x00, 0x00, 0x43), //	
	'rgbwwAllNightMode8b'      => array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0x04, 0x80, 0x00, 0x31, 0x00, 0x00, 0x0a, 0x06, 0x64, 0x00, 0x00, 0x00, 0x00, 0x00, 0x43), //
	'rgbwwAllWhiteOn8b'        => array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0x04, 0x80, 0x00, 0x31, 0x00, 0x00, 0x0a, 0x06, 0x05, 0x00, 0x00, 0x00, 0x00, 0x00, 0x43), //
	'rgbwwBrightness8b'        => array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0x04, 0x80, 0x00, 0x31, 0x00, 0x00, 0x0a, 0x04, 0x01, 0x00, 0x00, 0x00, 0x00, 0x00, 0x43), //
	'rgbwwKelvin8b'            => array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0x04, 0x80, 0x00, 0x31, 0x00, 0x00, 0x0a, 0x02, 0x01, 0x00, 0x00, 0x00, 0x00, 0x00, 0x43), //
	'rgbwwSaturation8b'        => array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0x04, 0x80, 0x00, 0x31, 0x00, 0x00, 0x0a, 0x03, 0x01, 0x00, 0x00, 0x00, 0x00, 0x00, 0x43), //
	'rgbwwDiscoMode8b'         => array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0x04, 0x80, 0x00, 0x31, 0x00, 0x00, 0x0a, 0x05, 0x01, 0x00, 0x00, 0x00, 0x00, 0x00, 0x43), //
	'rgbwwDiscoModeDecrease8b' => array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0x04, 0x80, 0x00, 0x31, 0x00, 0x00, 0x0a, 0x04, 0x04, 0x00, 0x00, 0x00, 0xFF, 0x00, 0xFF), //
	'rgbwwDiscoModeIncrease8b' => array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0x04, 0x80, 0x00, 0x31, 0x00, 0x00, 0x0a, 0x04, 0x03, 0x00, 0x00, 0x00, 0xFF, 0x00, 0xFF), //
	'rgbwwDiscoSlower8b'       => array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0x04, 0x80, 0x00, 0x31, 0x00, 0x00, 0x0a, 0x06, 0x04, 0x00, 0x00, 0x00, 0xFF, 0x00, 0xFF), //
	'rgbwwDiscoFaster8b'       => array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0x04, 0x80, 0x00, 0x31, 0x00, 0x00, 0x0a, 0x06, 0x03, 0x00, 0x00, 0x00, 0x00, 0x00, 0x43), //
	'rgbwwAllSetToWhite8b'     => array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0x04, 0x80, 0x00, 0x31, 0x00, 0x00, 0x0a, 0x06, 0x05, 0x00, 0x00, 0x00, 0x00, 0x00, 0x43), //
	'rgbwwColor8b'             => array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0x04, 0x80, 0x00, 0x31, 0x00, 0x00, 0x0a, 0x01, 0x05, 0x00, 0x00, 0x00, 0x00, 0x00, 0x43),

	// Dual white bulb commands
	'whiteAllOn' 			=> array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0xE6, 0x80, 0x00, 0x31, 0x00, 0x00, 0x01, 0x01, 0x07, 0x00, 0x00, 0x00, 0x00, 0x00, 0xFF), //
	'whiteAllOff' 			=> array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0xE6, 0x80, 0x00, 0x31, 0x00, 0x00, 0x01, 0x01, 0x08, 0x00, 0x00, 0x00, 0x00, 0x00, 0xFF), //	
	'whiteAllNightMode' 	=> array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0xE6, 0x80, 0x00, 0x31, 0x00, 0x00, 0x01, 0x01, 0x06, 0x00, 0x00, 0x00, 0x00, 0x00, 0xFF), //	
	'whiteBrightnessInc' 	=> array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0xE6, 0x80, 0x00, 0x31, 0x00, 0x00, 0x01, 0x01, 0x01, 0x00, 0x00, 0x00, 0x01, 0x00, 0xFF), //
	'whiteBrightnessDec' 	=> array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0xE6, 0x80, 0x00, 0x31, 0x00, 0x00, 0x01, 0x01, 0x02, 0x00, 0x00, 0x00, 0x01, 0x00, 0xFF), //
	'whiteKelvinInc' 		=> array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0xE6, 0x80, 0x00, 0x31, 0x00, 0x00, 0x01, 0x01, 0x03, 0x00, 0x00, 0x00, 0x04, 0x00, 0xFF), //
	'whiteKelvinDec' 		=> array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0xE6, 0x80, 0x00, 0x31, 0x00, 0x00, 0x01, 0x01, 0x04, 0x00, 0x00, 0x00, 0x04, 0x00, 0xFF), //
	
	//RGBWW tracking commands
	'rgbwwAllOnTrk'             => array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0x04, 0x80, 0x00, 0x31, 0x00, 0x00, 0x09, 0x01, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x43), //
	'rgbwwAllOffTrk'            => array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0x04, 0x80, 0x00, 0x31, 0x00, 0x00, 0x09, 0x01, 0x01, 0x00, 0x00, 0x00, 0x00, 0x00, 0x43), //	
	//'rgbwwAllNightModeTrk'      => array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0x04, 0x80, 0x00, 0x31, 0x00, 0x00, 0x09, 0x06, 0x64, 0x00, 0x00, 0x00, 0x00, 0x00, 0x43), //
	'rgbwwAllWhiteOnTrk'        => array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0x04, 0x80, 0x00, 0x31, 0x00, 0x00, 0x09, 0x81, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x43), //
	'rgbwwBrightnessTrk'        => array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0x04, 0x80, 0x00, 0x31, 0x00, 0x00, 0x09, 0x04, 0x15, 0x00, 0x00, 0x00, 0x00, 0x00, 0x43), //
	'rgbwwKelvinTrk'            => array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0x04, 0x80, 0x00, 0x31, 0x00, 0x00, 0x09, 0x03, 0x01, 0x00, 0x00, 0x00, 0x00, 0x00, 0x43), //
	'rgbwwSaturationTrk'        => array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0x04, 0x80, 0x00, 0x31, 0x00, 0x00, 0x09, 0x04, 0x01, 0x00, 0x00, 0x00, 0x00, 0x00, 0x43), //
	'rgbwwDiscoModeTrk'         => array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0x04, 0x80, 0x00, 0x31, 0x00, 0x00, 0x09, 0x06, 0x01, 0x00, 0x00, 0x00, 0x00, 0x00, 0x43), //
	//'rgbwwDiscoModeDecreaseTrk' => array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0x04, 0x80, 0x00, 0x31, 0x00, 0x00, 0x09, 0x04, 0x04, 0x00, 0x00, 0x00, 0xFF, 0x00, 0xFF), //
	//'rgbwwDiscoModeIncreaseTrk' => array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0x04, 0x80, 0x00, 0x31, 0x00, 0x00, 0x09, 0x04, 0x03, 0x00, 0x00, 0x00, 0xFF, 0x00, 0xFF), //
	//'rgbwwDiscoSlowerTrk'       => array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0x04, 0x80, 0x00, 0x31, 0x00, 0x00, 0x09, 0x06, 0x04, 0x00, 0x00, 0x00, 0xFF, 0x00, 0xFF), //
	//'rgbwwDiscoFasterTrk'       => array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0x04, 0x80, 0x00, 0x31, 0x00, 0x00, 0x09, 0x06, 0x03, 0x00, 0x00, 0x00, 0x00, 0x00, 0x43), //
	//'rgbwwAllSetToWhiteTrk'     => array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0x04, 0x80, 0x00, 0x31, 0x00, 0x00, 0x09, 0x06, 0x05, 0x00, 0x00, 0x00, 0x00, 0x00, 0x43), //
	'rgbwwColorTrk'             => array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0x04, 0x80, 0x00, 0x31, 0x00, 0x00, 0x09, 0x02, 0x05, 0x00, 0x00, 0x00, 0x00, 0x00, 0x43),
	'rgbwwUpOnTrk'             	=> array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0x04, 0x80, 0x00, 0x31, 0x00, 0x00, 0x09, 0x05, 0x01, 0x00, 0x00, 0x00, 0x00, 0x00, 0x43),
	'rgbwwUpOffTrk'            	=> array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0x04, 0x80, 0x00, 0x31, 0x00, 0x00, 0x09, 0x05, 0x05, 0x00, 0x00, 0x00, 0x00, 0x00, 0x43),
	'rgbwwDwOnTrk'             	=> array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0x04, 0x80, 0x00, 0x31, 0x00, 0x00, 0x09, 0x05, 0x02, 0x00, 0x00, 0x00, 0x00, 0x00, 0x43),
	'rgbwwDwOffTrk'            	=> array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0x04, 0x80, 0x00, 0x31, 0x00, 0x00, 0x09, 0x05, 0x06, 0x00, 0x00, 0x00, 0x00, 0x00, 0x43),
	'rgbwwLeOnTrk'             	=> array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0x04, 0x80, 0x00, 0x31, 0x00, 0x00, 0x09, 0x05, 0x03, 0x00, 0x00, 0x00, 0x00, 0x00, 0x43),
	'rgbwwLeOffTrk'            	=> array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0x04, 0x80, 0x00, 0x31, 0x00, 0x00, 0x09, 0x05, 0x07, 0x00, 0x00, 0x00, 0x00, 0x00, 0x43),
	'rgbwwRiOnTrk'             	=> array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0x04, 0x80, 0x00, 0x31, 0x00, 0x00, 0x09, 0x05, 0x04, 0x00, 0x00, 0x00, 0x00, 0x00, 0x43),
	'rgbwwRiOffTrk'            	=> array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0x04, 0x80, 0x00, 0x31, 0x00, 0x00, 0x09, 0x05, 0x08, 0x00, 0x00, 0x00, 0x00, 0x00, 0x43),	
	'rgbwwSoTrk'            	=> array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0x04, 0x80, 0x00, 0x31, 0x00, 0x00, 0x09, 0x05, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x43),
	'rgbwwSelTrk'            	=> array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0x04, 0x80, 0x00, 0x31, 0x00, 0x00, 0x09, 0x07, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x43),
	'rgbwwSavTrk'            	=> array(0x80, 0x00, 0x00, 0x00, 0x11, 0xFF, 0xFF, 0x04, 0x80, 0x00, 0x31, 0x00, 0x00, 0x09, 0x17, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x43),
	);

	public function __construct($host = '192.168.1.110', $wait=0, $repeat=1, $increm=10, $ID=0, $LocalId="", $nbLeds=0, $colorOrder=0, $port = 5987) {
		
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
		if ($repeat>6)
			$repeat =6;
		$this->_repeat = $repeat;
		if ($increm<1)
			$increm =1;
		if ($increm>25)
			$increm =25;
		$this->_increm = $increm;
		$this->SetGroup();
		$this->Seq[1]=0;
		
		$myRet = new wifilightV2c;
		$this->_return = $myRet->_return_WFL;
		$this->_log = $myRet->_log;			 
	}
	//Used to dynamically call class methods
	public function call(){
		echo __METHOD__;
	}


	public function GetColor() {
		return $this->_color[$this->_ActiveGroup];
		return 0;
	}
	public function SetColor($Col) {
		$this->_color[$this->_ActiveGroup]=$Col;
		return 0;
	}	
	public function SetGroup($group=1) {
		$this->_ActiveGroup = $group;
	}

	public function GetGroup($group=0) {
		return $this->_ActiveGroup;
	}
	public function retStatus() {
		return $this->_return;	
	}	
	public function setId($Id) {
		$this->_ID1 = $Id[1];
		$this->_ID2 = $Id[2];
	}
	public function getId(){
		$this->_socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
		if ($this->_socket != FALSE) {
			socket_set_option($this->_socket,SOL_SOCKET,SO_RCVTIMEO,array("sec"=>0,"usec"=>20000));
			$command = $this->_commandCodes['rgbwwGetWBID'];
			$message = vsprintf(str_repeat('%c', count($command)), $command);
			//log::add($this->_log,'debug','Start - ID');
			$Icpt=0;
			do {
				socket_sendto($this->_socket, $message, strlen($message), 0, $this->_host, $this->_port);		
				$Icpt++;
				$Icpt2=0;
				do {
					//log::add($this->_log,'debug','receive ID');
					usleep(20000);
					socket_recvfrom($this->_socket, $buf, 30,MSG_DONTWAIT, $this->_host, $this->_port);
					$Icpt2++;
					//log::add($this->_log,'debug',"buf0=".dechex(ord($buf[0])));
					for ($iBcl=0;$iBcl<2;$iBcl++){
						//log::add($this->_log,'debug',"mess=".dechex(ord($buf[$iBcl])));
					} 
				}while ((ord($buf[0])!=0x28) && ($Icpt2<50));
			} while ((ord($buf[0])!=0x28) && ($Icpt<4));
			if (ord($buf[0])==0x28) {
				$this->_ID1 = ord($buf[19]);
				$this->_ID2 = ord($buf[20]);
				log::add($this->_log,'debug','ID1/2 : '.$this->_ID1.' '.$this->_ID2);
			}
			$Ret=array(ord($buf[0]),ord($buf[19]),ord($buf[20]));
			}
		else {
			log::add($this->_log,'debug','Error - No socket');
		}
		return $Ret;
	}
	protected function send() {
		if($this->_socket==null) $this->_socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
		if ($this->_socket != FALSE) {
				socket_set_option($this->_socket,SOL_SOCKET,SO_RCVTIMEO,array("sec"=>0,"usec"=>20000));
				$this->Seq[1]++;
				$this->Seq[1]=$this->Seq[1]& 0xFF;
				$this->_CodeToSend[5]=$this->_ID1;
				$this->_CodeToSend[6]=$this->_ID2;
				$this->_CodeToSend[8]=$this->Seq[1];
		
				$iVal=0;
				for ($iBcl=10;$iBcl<21;$iBcl++){
					$iVal=$iVal+$this->_CodeToSend[$iBcl];
				}
				$ival=$iVal & 0xFF;
				$this->_CodeToSend[21]=$ival;

				$message = vsprintf(str_repeat('%c', count($this->_CodeToSend)), $this->_CodeToSend);
				$mess="";
				for ($iBcl=0;$iBcl<22;$iBcl++){
					$mess=$mess." ".dechex(ord($message[$iBcl]));
				}
				log::add($this->_log,'debug',"Commande : ".$mess);
				$Icpt=0;
				do {
					socket_sendto($this->_socket, $message, strlen($message), 0, $this->_host, $this->_port);	
					$Icpt++;
					$Icpt2=0;
					do {
						usleep(10000);
						socket_recvfrom($this->_socket, $buf, 30,MSG_DONTWAIT, $this->_host, $this->_port);	
						$Icpt2++;
					} while (ord($buf[0])!=0x88 && $Icpt2<50);
				} while ((ord($buf[0])!=0x88) && ($Icpt<4));
				if (ord($buf[0])==0x88 ) {
					log::add($this->_log,'debug','Send OK');			
				}
				else {
					log::add($this->_log,'debug','Error - No Ack');
				}
			usleep(20000);
		}
		else {
			log::add($this->_log,'debug','Error - No socket');
		}
		//log::add($this->_log,'debug','End control');
		
	}
	
	
	public function OnMax() {
		$this->OnBrightness(100);	
	}

	public function OnMin() {
		$this->OnBrightness(1);		
	}

	public function OnMid() {
		$this->OnBrightness(50);
	}
	public function OnDiscoNext() {
		
	}

	public function DiscoMin() {
		$this->_repeatOK = false;
		for ($iBcl=0;$iBcl<10;$iBcl++) {
			$this->DiscoSlower();
		}
	}

	public function DiscoMid() {
		$this->_repeatOK = false;
		$this->DiscoMax();
		for ($iBcl=0;$iBcl<5;$iBcl++) {
			$this->DiscoSlower();
		}
	}
	
	public function DiscoSpeed($speed,$prog=0) {
		$this->_repeatOK = false;
		if($speed>100) $speed=100;
		if($speed<1) $speed=1;
		$speed= round($speed/11)+1;
		$speed=11-$speed;
		//log::add($this->_log,'debug',"speed".$speed);
		$this->DiscoMax();
		for ($iBcl=0;$iBcl<$speed;$iBcl++) {
			$this->DiscoSlower();
		}
	}
	

	public function DiscoMax() {
		$this->_repeatOK = false;
		for ($iBcl=0;$iBcl<10;$iBcl++) {
			$this->DiscoFaster();
		}
	}
	public function OnWarm() {
		$this->OnKelvin(0);
	}

	public function OnCool() {
		$this->OnKelvin(100);
	}

	public function OnLukeWarm() {
		$this->OnKelvin(50);
	}
	

	public function OnControl($value) {
	}

	
	public function rgbToColor($r,$g,$b) {
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
		//$color = (256 + 171 - round($h / 360.0 * 256.0-0.0001)) % 256;
		$color = (round($h / 360.0 * 256.0-0.0001)+10) % 256;
		//return ($color + 0xfa) & 0xff;
		return ($color) & 0xff;
	}
	
	// common methods
	public function ColorTorgb($Col){ 
		//log::add($this->_log,'debug',"col to convert :".$Col);
		$Colrgb=array(0,0,0);
		//$Hue= (((int)((171.-$Col)*360./256.))+720) % 360;
		$Hue= (((int)((($Col-10))*360./256.))+720) % 360;
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
	
	public function rgb2hex($rgb) {
	   $hex = "#";
	   $hex .= str_pad(dechex($rgb[0]), 2, "0", STR_PAD_LEFT);
	   $hex .= str_pad(dechex($rgb[1]), 2, "0", STR_PAD_LEFT);
	   $hex .= str_pad(dechex($rgb[2]), 2, "0", STR_PAD_LEFT);
	   return $hex; // returns the hex value including the number sign (#)
	}

}

class W2_mylightStripRGBV6 extends W2_milightV6
{
	// start methods for RGBW Lights
	public function On() {
		$this->_CodeToSend = $this->_commandCodes['rgbwAllOn'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;
		$this->send();
	}

	public function Off() {
		//log::add($this->_log,'debug',"Off");
		$this->_CodeToSend = $this->_commandCodes['rgbwAllOff'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;
		$this->send();
	}

	public function OnBrightness($value=50,$color='#000000') {
		if ($value<0) $value=0;
		if ($value>100) $value=100;
		$this->_CodeToSend = $this->_commandCodes['rgbwAllOn'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;				
		$this->send();
		$this->_CodeToSend = $this->_commandCodes['rgbwBrightness'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;
		$this->_CodeToSend[15]=$value;
		$this->send();
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
	public function OnBrightnessWhite($value=50,$color='#000000') {
		if ($value<0) $value=0;
		if ($value>100) $value=100;	
		$this->_CodeToSend = $this->_commandCodes['rgbwAllOn'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;				
		$this->send();
		$this->_CodeToSend = $this->_commandCodes['rgbwBrightness'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;
		$this->_CodeToSend[15]=$value;
		$this->send();

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

	public function OnNight() {
		$this->_CodeToSend = $this->_commandCodes['rgbwAllOn'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;				
		$this->send();
		$this->_CodeToSend = $this->_commandCodes['rgbwAllNightMode'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;
		$this->send();
	}	

	
	public function OnColor($color='Mint',$Bright) {
		$this->_CodeToSend = $this->_commandCodes['rgbwAllOn'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;				
		$this->send();
		$color = (string)$color;
		//log::add($this->_log,'debug','in color : '.$color);
		$this->_CodeToSend = $this->_commandCodes['rgbwColor'];
		$this->_CodeToSend[19]=$this->_ActiveGroup;
		//log::add($this->_log,'debug','Case');
		switch ($color) {
			case 'Random':		$this->_CodeToSend[15] = (int)mt_rand(0,255);  
								$this->_color[$this->_ActiveGroup]= $this->ColorTorgb($this->_CodeToSend[15]);
								break;
			case 'Blue':		$this->_CodeToSend[15] = (-0x00+170+256+30)%256; $this->_color[$this->_ActiveGroup]= '#0000FF' ; break;
			case 'Violet':		$this->_CodeToSend[15] = (-0xeb+170+256+30)%256; $this->_color[$this->_ActiveGroup]= '#7F00FF' ; break;
			case 'BabyBlue':	$this->_CodeToSend[15] = (-0x20+170+256+30)%256; $this->_color[$this->_ActiveGroup]= '#00bbff' ; break;
			case 'Aqua':		$this->_CodeToSend[15] = (-0x30+170+256+30)%256; $this->_color[$this->_ActiveGroup]= '#00FFFF' ; break;
			case 'SpringGreen':	$this->_CodeToSend[15] = (-0x40+170+256+30)%256; $this->_color[$this->_ActiveGroup]= '#00FF7F' ; break;
			case 'Mint':		$this->_CodeToSend[15] = (-0x4A+170+256+30)%256; $this->_color[$this->_ActiveGroup]= '#00FF43' ; break;
			case 'Green':		$this->_CodeToSend[15] = (-0x55+170+256+30)%256; $this->_color[$this->_ActiveGroup]= '#00FF00' ; break;
			case 'LimeGreen':	$this->_CodeToSend[15] = (-0x70+170+256+30)%256; $this->_color[$this->_ActiveGroup]= '#a1FF00' ; break;
			case 'Yellow':		$this->_CodeToSend[15] = (-0x80+170+256+30)%256; $this->_color[$this->_ActiveGroup]= '#FFFF00' ; break;
			case 'YellowOrange':$this->_CodeToSend[15] = (-0x88+170+256+30)%256; $this->_color[$this->_ActiveGroup]= '#FFD000' ; break;
			case 'Orange':		$this->_CodeToSend[15] = (-0x8F+170+256+30)%256; $this->_color[$this->_ActiveGroup]= '#FFA500' ; break;
			case 'Red':			$this->_CodeToSend[15] = (-0xaa+170+256+30)%256; $this->_color[$this->_ActiveGroup]= '#FF0000' ; break;
			case 'Pink':		$this->_CodeToSend[15] = (-0xbd+170+256+30)%256; $this->_color[$this->_ActiveGroup]= '#FF0061' ; break;
			case 'Fuchsia':		$this->_CodeToSend[15] = (-0xd6+170+256+30)%256; $this->_color[$this->_ActiveGroup]= '#FF00FF' ; break;
			case 'Lilac':		$this->_CodeToSend[15] = (-0xE0+170+256+30)%256; $this->_color[$this->_ActiveGroup]= '#D000FF' ; break;
			case 'Lavendar':	$this->_CodeToSend[15] = (-0xF0+170+256+30)%256; $this->_color[$this->_ActiveGroup]= '#6100FF' ; break;
			case ((int)$color > 0x00) && ((int)$color <= 0xff): 
				$x = (int)$color; 
				$this->_color[$this->_ActiveGroup]=ColorTorgb($x);				
				$this->_CodeToSend[15] = $x;  
				break;
			case (substr($color,0,1)== "#"):
				$r = (int)hexdec(substr($color,1,2));
				$g = (int)hexdec(substr($color,3,2));
				$b = (int)hexdec(substr($color,5,2));
				$this->_CodeToSend[15]= $this->rgbToColor($r,$g,$b);
				$this->_color[$this->_ActiveGroup]= $color;
				break;
			default:
				$this->_CodeToSend[15] = 0x40; 
				$this->_color[$this->_ActiveGroup]= $this->ColorTorgb($this->_CodeToSend[15]);
				break;
		}
		$this->_CodeToSend[16]= $this->_CodeToSend[15];
		$this->_CodeToSend[17]= $this->_CodeToSend[15];
		$this->_CodeToSend[18]= $this->_CodeToSend[15];
		$this->send();
		//log::add($this->_log,'debug','put color hex : '.$this->_color[$this->_ActiveGroup]);			
	}

	public function OnDisco($prog,$speed=0) {
	//   1=rainbowSwirl, 2=whiteFade, 3=rgbwFade, 4=rainbowJump, 5=disco, 6=redTwinkle, 7=greenTwinkle, 8=blueTwinkle, 9=allCombined

		if ($prog < 1) $prog=1;
		if ($prog > 9) $prog=9;	
		$this->_CodeToSend = $this->_commandCodes['rgbwAllOn'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;				
		$this->send();
		$this->_CodeToSend = $this->_commandCodes['rgbwDiscoMode'];
		$this->_CodeToSend[19]=$this->_ActiveGroup;
		$this->_CodeToSend[15]=$prog-1;		
		$this->send();
	}

	public function DiscoSlower() {
		$this->_CodeToSend = $this->_commandCodes['rgbwDiscoSlower'];
		$this->_CodeToSend[19]=$this->_ActiveGroup;		
		$this->send();
	}

	public function DiscoFaster() {
		$this->_CodeToSend = $this->_commandCodes['rgbwDiscoFaster'];
		$this->_CodeToSend[19]=$this->_ActiveGroup;		
		$this->send();	
	}
	
}
class W2_mylightRGBWV6 extends W2_milightV6
{

		// start methods for RGBW Lights
	public function On() {
		$this->_CodeToSend = $this->_commandCodes['rgbwAllOn'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;
		$this->send();
	}

	public function Off() {
		//log::add($this->_log,'debug',"Off");
		$this->_CodeToSend = $this->_commandCodes['rgbwAllOff'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;
		$this->send();
	}

	public function OnBrightness($value=50,$color='#000000') {
		if ($value<0) $value=0;
		if ($value>100) $value=100;
		$this->_CodeToSend = $this->_commandCodes['rgbwAllOn'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;				
		$this->send();
		$this->_CodeToSend = $this->_commandCodes['rgbwBrightness'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;
		$this->_CodeToSend[15]=$value;
		$this->send();
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
	public function OnBrightnessWhite($value=50,$color='#000000') {
		if ($value<0) $value=0;
		if ($value>100) $value=100;	
		$this->_CodeToSend = $this->_commandCodes['rgbwAllOn'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;				
		$this->send();
		$this->_CodeToSend = $this->_commandCodes['rgbwBrightness'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;
		$this->_CodeToSend[15]=$value;
		$this->send();

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

	public function OnNight() {
		$this->_CodeToSend = $this->_commandCodes['rgbwAllOn'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;				
		$this->send();
		$this->_CodeToSend = $this->_commandCodes['rgbwAllNightMode'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;
		$this->send();
	}	
	public function OnWhite() {
		$this->_CodeToSend = $this->_commandCodes['rgbwAllOn'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;				
		$this->send();
		$this->_CodeToSend = $this->_commandCodes['rgbwAllWhiteOn'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;
		$this->send();
	}
	
	public function OnColor($color='Mint',$Bright) {
		$this->_CodeToSend = $this->_commandCodes['rgbwAllOn'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;				
		$this->send();
		$color = (string)$color;
		//log::add($this->_log,'debug','in color : '.$color);
		$this->_CodeToSend = $this->_commandCodes['rgbwColor'];
		$this->_CodeToSend[19]=$this->_ActiveGroup;
		//log::add($this->_log,'debug','Case');
		switch ($color) {
			case 'Random':		$this->_CodeToSend[15] = (int)mt_rand(0,255);  
								$this->_color[$this->_ActiveGroup]= $this->ColorTorgb($this->_CodeToSend[15]);
								break;
			case 'Blue':		$this->_CodeToSend[15] = (-0x00+170+256+30)%256; $this->_color[$this->_ActiveGroup]= '#0000FF' ; break;
			case 'Violet':		$this->_CodeToSend[15] = (-0xeb+170+256+30)%256; $this->_color[$this->_ActiveGroup]= '#7F00FF' ; break;
			case 'BabyBlue':	$this->_CodeToSend[15] = (-0x20+170+256+30)%256; $this->_color[$this->_ActiveGroup]= '#00bbff' ; break;
			case 'Aqua':		$this->_CodeToSend[15] = (-0x30+170+256+30)%256; $this->_color[$this->_ActiveGroup]= '#00FFFF' ; break;
			case 'SpringGreen':	$this->_CodeToSend[15] = (-0x40+170+256+30)%256; $this->_color[$this->_ActiveGroup]= '#00FF7F' ; break;
			case 'Mint':		$this->_CodeToSend[15] = (-0x4A+170+256+30)%256; $this->_color[$this->_ActiveGroup]= '#00FF43' ; break;
			case 'Green':		$this->_CodeToSend[15] = (-0x55+170+256+30)%256; $this->_color[$this->_ActiveGroup]= '#00FF00' ; break;
			case 'LimeGreen':	$this->_CodeToSend[15] = (-0x70+170+256+30)%256; $this->_color[$this->_ActiveGroup]= '#a1FF00' ; break;
			case 'Yellow':		$this->_CodeToSend[15] = (-0x80+170+256+30)%256; $this->_color[$this->_ActiveGroup]= '#FFFF00' ; break;
			case 'YellowOrange':$this->_CodeToSend[15] = (-0x88+170+256+30)%256; $this->_color[$this->_ActiveGroup]= '#FFD000' ; break;
			case 'Orange':		$this->_CodeToSend[15] = (-0x8F+170+256+30)%256; $this->_color[$this->_ActiveGroup]= '#FFA500' ; break;
			case 'Red':			$this->_CodeToSend[15] = (-0xaa+170+256+30)%256; $this->_color[$this->_ActiveGroup]= '#FF0000' ; break;
			case 'Pink':		$this->_CodeToSend[15] = (-0xbd+170+256+30)%256; $this->_color[$this->_ActiveGroup]= '#FF0061' ; break;
			case 'Fuchsia':		$this->_CodeToSend[15] = (-0xd6+170+256+30)%256; $this->_color[$this->_ActiveGroup]= '#FF00FF' ; break;
			case 'Lilac':		$this->_CodeToSend[15] = (-0xE0+170+256+30)%256; $this->_color[$this->_ActiveGroup]= '#D000FF' ; break;
			case 'Lavendar':	$this->_CodeToSend[15] = (-0xF0+170+256+30)%256; $this->_color[$this->_ActiveGroup]= '#6100FF' ; break;
			case ((int)$color > 0x00) && ((int)$color <= 0xff): 
				$x = (int)$color; 
				$this->_color[$this->_ActiveGroup]=ColorTorgb($x);				
				$this->_CodeToSend[15] = $x;  
				break;
			case (substr($color,0,1)== "#"):
				$r = (int)hexdec(substr($color,1,2));
				$g = (int)hexdec(substr($color,3,2));
				$b = (int)hexdec(substr($color,5,2));
				$this->_CodeToSend[15]= $this->rgbToColor($r,$g,$b);
				$this->_color[$this->_ActiveGroup]= $color;
				break;
			default:
				$this->_CodeToSend[15] = 0x40; 
				$this->_color[$this->_ActiveGroup]= $this->ColorTorgb($this->_CodeToSend[15]);
				break;
		}
		$this->_CodeToSend[16]= $this->_CodeToSend[15];
		$this->_CodeToSend[17]= $this->_CodeToSend[15];
		$this->_CodeToSend[18]= $this->_CodeToSend[15];
		$this->send();		
	}

	public function OnDisco($prog,$speed=0) {
	//   1=rainbowSwirl, 2=whiteFade, 3=rgbwFade, 4=rainbowJump, 5=disco, 6=redTwinkle, 7=greenTwinkle, 8=blueTwinkle, 9=allCombined

		if ($prog < 1) $prog=1;
		if ($prog > 9) $prog=9;	
		$this->_CodeToSend = $this->_commandCodes['rgbwAllOn'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;				
		$this->send();
		$this->_CodeToSend = $this->_commandCodes['rgbwDiscoMode'];
		$this->_CodeToSend[19]=$this->_ActiveGroup;
		$this->_CodeToSend[15]=$prog-1;		
		$this->send();
	}

	public function DiscoSlower() {
		$this->_CodeToSend = $this->_commandCodes['rgbwDiscoSlower'];
		$this->_CodeToSend[19]=$this->_ActiveGroup;		
		$this->send();
	}

	public function DiscoFaster() {
		$this->_CodeToSend = $this->_commandCodes['rgbwDiscoFaster'];
		$this->_CodeToSend[19]=$this->_ActiveGroup;		
		$this->send();	
	}
}
class W2_mylightBridgeV6 extends W2_milightV6
{

	public function On() {
		$this->_CodeToSend = $this->_commandCodes['rgbwwBridgeOn'];
		$this->send();
	}	
	
	public function Off() {
		$this->_CodeToSend = $this->_commandCodes['rgbwwBridgeOff'];
		$this->send();
	}
	
	public function OnBrightness($value=50,$color='#000000') {
		if ($value<0) $value=0;
		if ($value>100) $value=100;	
		$this->_CodeToSend = $this->_commandCodes['rgbwwBridgeOn'];
		$this->send();
		$this->_CodeToSend = $this->_commandCodes['rgbwwBridgeBrightness'];
		$this->_CodeToSend[15]=$value;
		$this->send();
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
	public function OnBrightnessWhite($value=50,$color='#000000') {
		if ($value<0) $value=0;
		if ($value>100) $value=100;	
		$this->_CodeToSend = $this->_commandCodes['rgbwwBridgeOn'];
		$this->send();
		$this->_CodeToSend = $this->_commandCodes['rgbwwBridgeBrightness'];
		$this->_CodeToSend[15]=$value;
		$this->send();
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
	public function OnNight() {
		$this->OnBrightness(1);
	}
	
	public function OnWhite() {
		//log::add($this->_log,'debug','Start White');
		$this->_CodeToSend = $this->_commandCodes['rgbwwBridgeOn'];
		$this->send();
		$this->_CodeToSend = $this->_commandCodes['rgbwwBridgeSetToWhite'];
		$this->send();	
	}

	public function OnColor($color='Mint',$Bright) {
		$color = (string)$color;
		//log::add($this->_log,'debug','in color : '.$color);
		$this->_CodeToSend = $this->_commandCodes['rgbwwBridgeOn'];
		$this->send();
		$this->_CodeToSend = $this->_commandCodes['rgbwwBridgeColor'];
		//log::add($this->_log,'debug','Case');
		switch ($color) {
			case 'Random':		$this->_CodeToSend[15] = (int)mt_rand(0,255);  
								$this->_color = $this->ColorTorgb($this->_CodeToSend[15]);
								break;
			case 'Blue':		$this->_CodeToSend[15] = (-0x00+170+256+10)%256; $this->_color = '#0000FF' ; break;
			case 'Violet':		$this->_CodeToSend[15] = (-0xeb+170+256+10)%256; $this->_color = '#7F00FF' ; break;
			case 'BabyBlue':	$this->_CodeToSend[15] = (-0x20+170+256+10)%256; $this->_color = '#00bbff' ; break;
			case 'Aqua':		$this->_CodeToSend[15] = (-0x30+170+256+10)%256; $this->_color = '#00FFFF' ; break;
			case 'SpringGreen':	$this->_CodeToSend[15] = (-0x40+170+256+10)%256; $this->_color = '#00FF7F' ; break;
			case 'Mint':		$this->_CodeToSend[15] = (-0x4A+170+256+10)%256; $this->_color = '#00FF43' ; break;
			case 'Green':		$this->_CodeToSend[15] = (-0x55+170+256+10)%256; $this->_color = '#00FF00' ; break;
			case 'LimeGreen':	$this->_CodeToSend[15] = (-0x70+170+256+10)%256; $this->_color = '#a1FF00' ; break;
			case 'Yellow':		$this->_CodeToSend[15] = (-0x80+170+256+10)%256; $this->_color = '#FFFF00' ; break;
			case 'YellowOrange':$this->_CodeToSend[15] = (-0x88+170+256+10)%256; $this->_color = '#FFD000' ; break;
			case 'Orange':		$this->_CodeToSend[15] = (-0x8F+170+256+10)%256; $this->_color = '#FFA500' ; break;
			case 'Red':			$this->_CodeToSend[15] = (-0xaa+170+256+10)%256; $this->_color = '#FF0000' ; break;
			case 'Pink':		$this->_CodeToSend[15] = (-0xbd+170+256+10)%256; $this->_color = '#FF0061' ; break;
			case 'Fuchsia':		$this->_CodeToSend[15] = (-0xd6+170+256+10)%256; $this->_color = '#FF00FF' ; break;
			case 'Lilac':		$this->_CodeToSend[15] = (-0xE0+170+256+10)%256; $this->_color = '#D000FF' ; break;
			case 'Lavendar':	$this->_CodeToSend[15] = (-0xF0+170+256+10)%256; $this->_color = '#6100FF' ; break;
			case ((int)$color > 0x00) && ((int)$color <= 0xff):
				$x = (int)$color; 
				$this->_color=ColorTorgb($x);				
				$this->_CodeToSend[15] = $x;  
				break;
			case (substr($color,0,1)== "#"):
				$r = (int)hexdec(substr($color,1,2));
				$g = (int)hexdec(substr($color,3,2));
				$b = (int)hexdec(substr($color,5,2));
				//log::add($this->_log,'debug','subst3');
				$this->_CodeToSend[15]= $this->rgbToColor($r,$g,$b);
				//log::add($this->_log,'debug','Internal Milight color :'.$this->_commandCodes['rgbwColor'][1]." hex=".dechex($this->_commandCodes['rgbwColor'][1]));
				//$this->_color[$this->_rgbwwActiveGroup]= $this->ColorTorgb($this->_commandCodes['rgbwColor'][1]);
				//log::add($this->_log,'debug','222');
				$this->_color = $color;
				break;
			default:
				$this->_CodeToSend[15] = 0x40; 
				$this->_color= $this->ColorTorgb($this->_CodeToSend[15]);
				break;
		}
		$this->_CodeToSend[16]= $this->_CodeToSend[15];
		$this->_CodeToSend[17]= $this->_CodeToSend[15];
		$this->_CodeToSend[18]= $this->_CodeToSend[15];
		$this->send();
		//log::add($this->_log,'debug','put color hex : '.$this->_color[5]);
				
	}
	public function OnDisco($prog,$speed=0) {
		if ($prog < 1) $prog=1;
		if ($prog > 9) $prog=9;
		$this->_CodeToSend = $this->_commandCodes['rgbwwBridgeOn'];
		$this->send();
		$this->_CodeToSend = $this->_commandCodes['rgbwwBridgeDiscoMode'];
		$this->_CodeToSend[15]=$prog;
		$this->send();	
	}
	
	public function DiscoSlower() {
		$this->_CodeToSend = $this->_commandCodes['rgbwwBridgeOn'];
		$this->send();
		$this->_CodeToSend = $this->_commandCodes['rgbwwBridgeDiscoSlower'];
		$this->send();	
	}

	public function DiscoFaster() {
		$this->_CodeToSend = $this->_commandCodes['rgbwwBridgeOn'];
		$this->send();
		$this->_CodeToSend = $this->_commandCodes['rgbwwBridgeDiscoFaster'];
		$this->send();
	}
	
}


class W2_mylightRGBWWV6 extends W2_milightV6
{
	public function On() {
		$this->_CodeToSend = $this->_commandCodes['rgbwwAllOn'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;
		$this->send();
	}

	public function Off() {
		$this->_CodeToSend = $this->_commandCodes['rgbwwAllOff'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;
		$this->send();
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
	public function OnBrightness($value=50,$color='#000000') {
		if ($value<0) $value=0;
		if ($value>100) $value=100;
		$this->_CodeToSend = $this->_commandCodes['rgbwwAllOn'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;				
		$this->send();
		$this->_CodeToSend = $this->_commandCodes['rgbwwBrightness'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;
		$this->_CodeToSend[15]=$value;
		$this->send();
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
	public function OnBrightnessWhite($value=50,$color='#000000') {
		if ($value<0) $value=0;
		if ($value>100) $value=100;	
		$this->_CodeToSend = $this->_commandCodes['rgbwwAllOn'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;				
		$this->send();
		$this->_CodeToSend = $this->_commandCodes['rgbwwBrightness'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;
		$this->_CodeToSend[15]=$value;
		$this->send();

	}
	public function OnNight() {
		$this->_CodeToSend = $this->_commandCodes['rgbwwAllOn'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;				
		$this->send();
		$this->_CodeToSend = $this->_commandCodes['rgbwwAllNightMode'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;
		$this->send();
	}	
	public function OnWhite() {
		$this->_CodeToSend = $this->_commandCodes['rgbwwAllOn'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;				
		$this->send();
		$this->_CodeToSend = $this->_commandCodes['rgbwwAllWhiteOn'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;
		$this->send();
	}
	public function OnSaturation($value=50) {
		//log::add($this->_log,'debug',"value=".$value);
		if ($value<0) $value=0;
		if ($value>100) $value=100;
		$this->_CodeToSend = $this->_commandCodes['rgbwwAllOn'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;				
		$this->send();
		$this->_CodeToSend = $this->_commandCodes['rgbwwSaturation'];
		$this->_CodeToSend[19]=$this->_ActiveGroup;
		$this->_CodeToSend[15]=$value;
		$this->send();
	}
	
	public function OnKelvin($value=50) {
		if ($value<0) $value=0;
		if ($value>100) $value=100;
		$this->_CodeToSend = $this->_commandCodes['rgbwwAllOn'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;				
		$this->send();
		$this->_CodeToSend = $this->_commandCodes['rgbwwKelvin'];
		$this->_CodeToSend[19]=$this->_ActiveGroup;
		$this->_CodeToSend[15]=$value;
		$this->send();
	}

	public function KelvinIncrease($value=50) {
		$value=$value+10;
		if ($value<0) $value=0;
		if ($value>100) $value=100;
		$this->_CodeToSend = $this->_commandCodes['rgbwwAllOn'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;				
		$this->send();
		$this->_CodeToSend = $this->_commandCodes['rgbwwKelvin'];
		$this->_CodeToSend[19]=$this->_ActiveGroup;
		$this->_CodeToSend[15]=$value;
		$this->send();
		return $value;
	}
	public function KelvinDecrease($value=50) {
		$value=$value-10;
		if ($value<0) $value=0;
		if ($value>100) $value=100;
		$this->_CodeToSend = $this->_commandCodes['rgbwwAllOn'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;				
		$this->send();
		$this->_CodeToSend = $this->_commandCodes['rgbwwKelvin'];
		$this->_CodeToSend[19]=$this->_ActiveGroup;
		$this->_CodeToSend[15]=$value;
		$this->send();
		return $value;
	
	}

	public function OnColor($color='Mint',$Bright) {
		$this->_CodeToSend = $this->_commandCodes['rgbwwAllOn'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;				
		$this->send();
		$color = (string)$color;
		//log::add($this->_log,'debug','in color : '.$color);
		$this->_CodeToSend = $this->_commandCodes['rgbwwColor'];
		$this->_CodeToSend[19]=$this->_ActiveGroup;
		//log::add($this->_log,'debug','Case');
		switch ($color) {
			case 'Random':		$this->_CodeToSend[15] = (int)mt_rand(0,255);  
								$this->_color[$this->_ActiveGroup]= $this->ColorTorgb($this->_CodeToSend[15]);
								break;
			case 'Blue':		$this->_CodeToSend[15] = (-0x00+170+256+10)%256; $this->_color[$this->_ActiveGroup]= '#0000FF' ; break;
			case 'Violet':		$this->_CodeToSend[15] = (-0xeb+170+256+10)%256; $this->_color[$this->_ActiveGroup]= '#7F00FF' ; break;
			case 'BabyBlue':	$this->_CodeToSend[15] = (-0x20+170+256+10)%256; $this->_color[$this->_ActiveGroup]= '#00bbff' ; break;
			case 'Aqua':		$this->_CodeToSend[15] = (-0x30+170+256+10)%256; $this->_color[$this->_ActiveGroup]= '#00FFFF' ; break;
			case 'SpringGreen':	$this->_CodeToSend[15] = (-0x40+170+256+10)%256; $this->_color[$this->_ActiveGroup]= '#00FF7F' ; break;
			case 'Mint':		$this->_CodeToSend[15] = (-0x4A+170+256+10)%256; $this->_color[$this->_ActiveGroup]= '#00FF43' ; break;
			case 'Green':		$this->_CodeToSend[15] = (-0x55+170+256+10)%256; $this->_color[$this->_ActiveGroup]= '#00FF00' ; break;
			case 'LimeGreen':	$this->_CodeToSend[15] = (-0x70+170+256+10)%256; $this->_color[$this->_ActiveGroup]= '#a1FF00' ; break;
			case 'Yellow':		$this->_CodeToSend[15] = (-0x80+170+256+10)%256; $this->_color[$this->_ActiveGroup]= '#FFFF00' ; break;
			case 'YellowOrange':$this->_CodeToSend[15] = (-0x88+170+256+10)%256; $this->_color[$this->_ActiveGroup]= '#FFD000' ; break;
			case 'Orange':		$this->_CodeToSend[15] = (-0x8F+170+256+10)%256; $this->_color[$this->_ActiveGroup]= '#FFA500' ; break;
			case 'Red':			$this->_CodeToSend[15] = (-0xaa+170+256+10)%256; $this->_color[$this->_ActiveGroup]= '#FF0000' ; break;
			case 'Pink':		$this->_CodeToSend[15] = (-0xbd+170+256+10)%256; $this->_color[$this->_ActiveGroup]= '#FF0061' ; break;
			case 'Fuchsia':		$this->_CodeToSend[15] = (-0xd6+170+256+10)%256; $this->_color[$this->_ActiveGroup]= '#FF00FF' ; break;
			case 'Lilac':		$this->_CodeToSend[15] = (-0xE0+170+256+10)%256; $this->_color[$this->_ActiveGroup]= '#D000FF' ; break;
			case 'Lavendar':	$this->_CodeToSend[15] = (-0xF0+170+256+10)%256; $this->_color[$this->_ActiveGroup]= '#6100FF' ; break;
			case ((int)$color > 0x00) && ((int)$color <= 0xff): 				
				$x = (int)$color; 
				$this->_color[$this->_ActiveGroup]=ColorTorgb($x);				
				$this->_CodeToSend[15] = $x;  
				break;
			case (substr($color,0,1)== "#"):
				$r = (int)hexdec(substr($color,1,2));
				$g = (int)hexdec(substr($color,3,2));
				$b = (int)hexdec(substr($color,5,2));
				//log::add($this->_log,'debug','subst');
				$this->_CodeToSend[15]= $this->rgbToColor($r,$g,$b);
				//log::add($this->_log,'debug','subst2');
				//log::add($this->_log,'debug','Internal Milight color :'.$this->_CodeToSend[15]." hex=".dechex($this->_CodeToSend[15]));
				$this->_color[$this->_ActiveGroup]= $color;
				//log::add($this->_log,'debug','222');
				break;
			default:
				$this->_CodeToSend[15] = 0x40; 
				$this->_color[$this->_ActiveGroup]=$this->ColorTorgb($this->_CodeToSend[15]);
				break;
		}
		$this->_CodeToSend[16]= $this->_CodeToSend[15];
		$this->_CodeToSend[17]= $this->_CodeToSend[15];
		$this->_CodeToSend[18]= $this->_CodeToSend[15];
		$this->send();
		//log::add($this->_log,'debug','put color hex : '.$this->_color[$this->_ActiveGroup]);	
	}
	public function OnDisco($prog,$speed=0) {
	//   1=rainbowSwirl, 2=whiteFade, 3=rgbwFade, 4=rainbowJump, 5=disco, 6=redTwinkle, 7=greenTwinkle, 8=blueTwinkle, 9=allCombined

		if ($prog < 1) $prog=1;
		if ($prog > 9) $prog=9;	
		$prog++;
		$this->_CodeToSend = $this->_commandCodes['rgbwwAllOn'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;				
		$this->send();
		$this->_CodeToSend = $this->_commandCodes['rgbwwDiscoMode'];
		$this->_CodeToSend[19]=$this->_ActiveGroup;
		$this->_CodeToSend[15]=$prog;		
		$this->send();
	}
	
	public function DiscoSlower() {
		$this->_CodeToSend = $this->_commandCodes['rgbwwAllOn'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;				
		$this->send();
		$this->_CodeToSend = $this->_commandCodes['rgbwwDiscoSlower'];
		$this->_CodeToSend[19]=$this->_ActiveGroup;		
		$this->send();
	}
	public function DiscoFaster() {
		$this->_CodeToSend = $this->_commandCodes['rgbwwAllOn'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;				
		$this->send();
		$this->_CodeToSend = $this->_commandCodes['rgbwwDiscoFaster'];
		$this->_CodeToSend[19]=$this->_ActiveGroup;		
		$this->send();	
	}
	public function OnDiscoNext() {
		$this->_CodeToSend = $this->_commandCodes['rgbwwAllOn'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;				
		$this->send();
		$this->_CodeToSend = $this->_commandCodes['rgbwwDiscoModeIncrease'];
		$this->_CodeToSend[19]=$this->_ActiveGroup;
		$this->send();
	}

	public function OnDiscoPrev() {
		$this->_CodeToSend = $this->_commandCodes['rgbwwAllOn'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;				
		$this->send();
		$this->_CodeToSend = $this->_commandCodes['rgbwwDiscoModeDecrease'];
		$this->_CodeToSend[19]=$this->_ActiveGroup;	
		$this->send();
	}

}
class W2_mylightRGBWW8bV6 extends W2_milightV6
{


	public function On() {
		$this->_CodeToSend = $this->_commandCodes['rgbwwAllOn8b'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;
		$this->send();
	}

	public function Off() {
		$this->_CodeToSend = $this->_commandCodes['rgbwwAllOff8b'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;
		$this->send();
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
	public function OnBrightness($value=50,$color='#000000') {
		if ($value<0) $value=0;
		if ($value>100) $value=100;
		$this->_CodeToSend = $this->_commandCodes['rgbwwAllOn8b'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;				
		$this->send();
		$this->_CodeToSend = $this->_commandCodes['rgbwwBrightness8b'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;
		$this->_CodeToSend[15]=$value;
		$this->send();
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
	public function OnBrightnessWhite($value=50,$color='#000000') {
		if ($value<0) $value=0;
		if ($value>100) $value=100;	
		$this->_CodeToSend = $this->_commandCodes['rgbwwAllOn8b'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;				
		$this->send();
		$this->_CodeToSend = $this->_commandCodes['rgbwwBrightness8b'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;
		$this->_CodeToSend[15]=$value;
		$this->send();

	}
	public function OnNight() {
		$this->_CodeToSend = $this->_commandCodes['rgbwwAllOn8b'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;				
		$this->send();
		$this->_CodeToSend = $this->_commandCodes['rgbwwAllNightMode8b'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;
		$this->send();
	}	
	public function OnWhite() {
		$this->_CodeToSend = $this->_commandCodes['rgbwwAllOn8b'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;				
		$this->send();
		$this->_CodeToSend = $this->_commandCodes['rgbwwAllWhiteOn8b'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;
		$this->send();
	}
	public function OnSaturation($value=50) {
		//log::add($this->_log,'debug',"value=".$value);
		if ($value<0) $value=0;
		if ($value>100) $value=100;
		$this->_CodeToSend = $this->_commandCodes['rgbwwAllOn8b'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;				
		$this->send();
		$this->_CodeToSend = $this->_commandCodes['rgbwwSaturation8b'];
		$this->_CodeToSend[19]=$this->_ActiveGroup;
		$this->_CodeToSend[15]=$value;
		$this->send();
	}
	public function OnKelvin($value=50) {
		if ($value<0) $value=0;
		if ($value>100) $value=100;
		$this->_CodeToSend = $this->_commandCodes['rgbwwAllOn8b'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;				
		$this->send();
		$this->_CodeToSend = $this->_commandCodes['rgbwwKelvin8b'];
		$this->_CodeToSend[19]=$this->_ActiveGroup;
		$this->_CodeToSend[15]=$value;
		$this->send();
	}

	public function KelvinIncrease($value=50) {
		$value=$value+10;
		if ($value<0) $value=0;
		if ($value>100) $value=100;
		$this->_CodeToSend = $this->_commandCodes['rgbwwAllOn8b'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;				
		$this->send();
		$this->_CodeToSend = $this->_commandCodes['rgbwwKelvin8b'];
		$this->_CodeToSend[19]=$this->_ActiveGroup;
		$this->_CodeToSend[15]=$value;
		$this->send();
		return $value;
	}
	public function KelvinDecrease($value=50) {
		$value=$value-10;
		if ($value<0) $value=0;
		if ($value>100) $value=100;
		$this->_CodeToSend = $this->_commandCodes['rgbwwAllOn8b'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;				
		$this->send();
		$this->_CodeToSend = $this->_commandCodes['rgbwwKelvin8b'];
		$this->_CodeToSend[19]=$this->_ActiveGroup;
		$this->_CodeToSend[15]=$value;
		$this->send();
		return $value;
	
	}

	public function OnColor($color='Mint',$Bright) {
		$this->_CodeToSend = $this->_commandCodes['rgbwwAllOn8b'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;				
		$this->send();
		$color = (string)$color;
		//log::add($this->_log,'debug','in color : '.$color);
		$this->_CodeToSend = $this->_commandCodes['rgbwwColor8b'];
		$this->_CodeToSend[19]=$this->_ActiveGroup;
		//log::add($this->_log,'debug','Case');
		switch ($color) {
			case 'Random':		$this->_CodeToSend[15] = (int)mt_rand(0,255);  
								$this->_color[$this->_ActiveGroup]= $this->ColorTorgb($this->_CodeToSend[15]);
								break;
			case 'Blue':		$this->_CodeToSend[15] = (-0x00+170+256+10)%256; $this->_color[$this->_ActiveGroup]= '#0000FF' ; break;
			case 'Violet':		$this->_CodeToSend[15] = (-0xeb+170+256+10)%256; $this->_color[$this->_ActiveGroup]= '#7F00FF' ; break;
			case 'BabyBlue':	$this->_CodeToSend[15] = (-0x20+170+256+10)%256; $this->_color[$this->_ActiveGroup]= '#00bbff' ; break;
			case 'Aqua':		$this->_CodeToSend[15] = (-0x30+170+256+10)%256; $this->_color[$this->_ActiveGroup]= '#00FFFF' ; break;
			case 'SpringGreen':	$this->_CodeToSend[15] = (-0x40+170+256+10)%256; $this->_color[$this->_ActiveGroup]= '#00FF7F' ; break;
			case 'Mint':		$this->_CodeToSend[15] = (-0x4A+170+256+10)%256; $this->_color[$this->_ActiveGroup]= '#00FF43' ; break;
			case 'Green':		$this->_CodeToSend[15] = (-0x55+170+256+10)%256; $this->_color[$this->_ActiveGroup]= '#00FF00' ; break;
			case 'LimeGreen':	$this->_CodeToSend[15] = (-0x70+170+256+10)%256; $this->_color[$this->_ActiveGroup]= '#a1FF00' ; break;
			case 'Yellow':		$this->_CodeToSend[15] = (-0x80+170+256+10)%256; $this->_color[$this->_ActiveGroup]= '#FFFF00' ; break;
			case 'YellowOrange':$this->_CodeToSend[15] = (-0x88+170+256+10)%256; $this->_color[$this->_ActiveGroup]= '#FFD000' ; break;
			case 'Orange':		$this->_CodeToSend[15] = (-0x8F+170+256+10)%256; $this->_color[$this->_ActiveGroup]= '#FFA500' ; break;
			case 'Red':			$this->_CodeToSend[15] = (-0xaa+170+256+10)%256; $this->_color[$this->_ActiveGroup]= '#FF0000' ; break;
			case 'Pink':		$this->_CodeToSend[15] = (-0xbd+170+256+10)%256; $this->_color[$this->_ActiveGroup]= '#FF0061' ; break;
			case 'Fuchsia':		$this->_CodeToSend[15] = (-0xd6+170+256+10)%256; $this->_color[$this->_ActiveGroup]= '#FF00FF' ; break;
			case 'Lilac':		$this->_CodeToSend[15] = (-0xE0+170+256+10)%256; $this->_color[$this->_ActiveGroup]= '#D000FF' ; break;
			case 'Lavendar':	$this->_CodeToSend[15] = (-0xF0+170+256+10)%256; $this->_color[$this->_ActiveGroup]= '#6100FF' ; break;
			case ((int)$color > 0x00) && ((int)$color <= 0xff): 				
				$x = (int)$color; 
				$this->_color[$this->_ActiveGroup]=ColorTorgb($x);				
				$this->_CodeToSend[15] = $x;  
				break;
			case (substr($color,0,1)== "#"):
				$r = (int)hexdec(substr($color,1,2));
				$g = (int)hexdec(substr($color,3,2));
				$b = (int)hexdec(substr($color,5,2));
				//log::add($this->_log,'debug','subst');
				$this->_CodeToSend[15]= $this->rgbToColor($r,$g,$b);
				//log::add($this->_log,'debug','subst2');
				//log::add($this->_log,'debug','Internal Milight color :'.$this->_CodeToSend[15]." hex=".dechex($this->_CodeToSend[15]));
				$this->_color[$this->_ActiveGroup]= $color;
				//log::add($this->_log,'debug','222');
				break;
			default:
				$this->_CodeToSend[15] = 0x40; 
				$this->_color[$this->_ActiveGroup]=$this->ColorTorgb($this->_CodeToSend[15]);
				break;
		}
		$this->_CodeToSend[16]= $this->_CodeToSend[15];
		$this->_CodeToSend[17]= $this->_CodeToSend[15];
		$this->_CodeToSend[18]= $this->_CodeToSend[15];
		$this->send();
		//log::add($this->_log,'debug','put color hex : '.$this->_color[$this->_ActiveGroup]);	
	}
	public function OnDisco($prog,$speed=0) {
	//   1=rainbowSwirl, 2=whiteFade, 3=rgbwFade, 4=rainbowJump, 5=disco, 6=redTwinkle, 7=greenTwinkle, 8=blueTwinkle, 9=allCombined

		if ($prog < 1) $prog=1;
		if ($prog > 9) $prog=9;	
		$prog++;
		$this->_CodeToSend = $this->_commandCodes['rgbwwAllOn8b'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;				
		$this->send();
		$this->_CodeToSend = $this->_commandCodes['rgbwwDiscoMode8b'];
		$this->_CodeToSend[19]=$this->_ActiveGroup;
		$this->_CodeToSend[15]=$prog;		
		$this->send();
	}
	
	public function DiscoSlower() {
		$this->_CodeToSend = $this->_commandCodes['rgbwwAllOn8b'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;				
		$this->send();
		$this->_CodeToSend = $this->_commandCodes['rgbwwDiscoSlower8b'];
		$this->_CodeToSend[19]=$this->_ActiveGroup;		
		$this->send();
	}
	public function DiscoFaster() {
		$this->_CodeToSend = $this->_commandCodes['rgbwwAllOn8b'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;				
		$this->send();
		$this->_CodeToSend = $this->_commandCodes['rgbwwDiscoFaster8b'];
		$this->_CodeToSend[19]=$this->_ActiveGroup;		
		$this->send();	
	}
}

class W2_mylightWhiteV6 extends W2_milightV6
{

	public function On() {
		$this->_CodeToSend = $this->_commandCodes['whiteAllOn'];
		$this->_CodeToSend[19]=$this->_ActiveGroup;
		$this->send();

	}

	public function Off() {
		$this->_CodeToSend = $this->_commandCodes['whiteAllOff'];
		$this->_CodeToSend[19]=$this->_ActiveGroup;
		$this->send();	

	}

	public function OnNight() {
		//log::add($this->_log,'debug','Night');
		$this->_CodeToSend = $this->_commandCodes['whiteAllOn'];
		$this->_CodeToSend[19]=$this->_ActiveGroup;
		$this->send();
		$this->_CodeToSend = $this->_commandCodes['whiteAllNightMode'];
		$this->_CodeToSend[19]=$this->_ActiveGroup;
		$this->send();	
	}
	public function BrightnessIncrease($Br) {
		$this->_CodeToSend = $this->_commandCodes['whiteBrightnessInc'];
		$this->_CodeToSend[19]=$this->_ActiveGroup;
		$this->send();
		$Br=$Br+10;
		if ($Br > 100) $Br=100;
		return $Br;
	}
	public function BrightnessDecrease($Br) {
		$this->_CodeToSend = $this->_commandCodes['whiteBrightnessDec'];
		$this->_CodeToSend[19]=$this->_ActiveGroup;
		$this->send();
		$Br=$Br-10;
		if ($Br < 0) $Br=0;
		return $Br;
	}
	public function OnBrightness($slider,$color='#000000') {

		if ($value<0) $value=0;
		if ($value>100) $value=100;	
		$value= round($value/11)+1;
		$this->_repeatOK = false;
		$this->_CodeToSend = $this->_commandCodes['whiteAllOn'];
		$this->_CodeToSend[19]=$this->_ActiveGroup;
		$this->send();
		if ($value>5) {
			for ($i = 0; $i < 10; $i++) {
				$this->_CodeToSend = $this->_commandCodes['whiteBrightnessInc'];
				$this->_CodeToSend[19]=$this->_ActiveGroup;
				$this->send();
			}
			for ($i = 0; $i <11-$value; $i++) {
				$this->_CodeToSend = $this->_commandCodes['whiteBrightnessDec'];
				$this->_CodeToSend[19]=$this->_ActiveGroup;
				$this->send();
			}
		}
		else {
			for ($i = 0; $i < 10; $i++) {
				$this->_CodeToSend = $this->_commandCodes['whiteBrightnessDec'];
				$this->_CodeToSend[19]=$this->_ActiveGroup;
				$this->send();
			}
			for ($i = 0; $i <$value-1; $i++) {
				$this->_CodeToSend = $this->_commandCodes['whiteBrightnessInc'];
				$this->_CodeToSend[19]=$this->_ActiveGroup;
				$this->send();
			}
		}

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
	public function OnBrightnessWhite($value=50,$color='#000000') {
		if ($value<0) $value=0;
		if ($value>100) $value=100;	
		$value= round($value/11)+1;
		$this->_repeatOK = false;
		$this->_CodeToSend = $this->_commandCodes['whiteAllOn'];
		$this->_CodeToSend[19]=$this->_ActiveGroup;
		$this->send();
		if ($value>5) {
			for ($i = 0; $i < 10; $i++) {
				$this->_CodeToSend = $this->_commandCodes['whiteBrightnessInc'];
				$this->_CodeToSend[19]=$this->_ActiveGroup;
				$this->send();
			}
			for ($i = 0; $i <11-$value; $i++) {
				$this->_CodeToSend = $this->_commandCodes['whiteBrightnessDec'];
				$this->_CodeToSend[19]=$this->_ActiveGroup;
				$this->send();
			}
		}
		else {
			for ($i = 0; $i < 10; $i++) {
				$this->_CodeToSend = $this->_commandCodes['whiteBrightnessDec'];
				$this->_CodeToSend[19]=$this->_ActiveGroup;
				$this->send();
			}
			for ($i = 0; $i <$value-1; $i++) {
				$this->_CodeToSend = $this->_commandCodes['whiteBrightnessInc'];
				$this->_CodeToSend[19]=$this->_ActiveGroup;
				$this->send();
			}
		}

	}
	public function KelvinIncrease($value=50) {
		$this->_repeatOK = false;

		$this->_CodeToSend = $this->_commandCodes['whiteAllOn'];
		$this->_CodeToSend[19]=$this->_ActiveGroup;
		$this->send();
		$this->_CodeToSend = $this->_commandCodes['whiteKelvinDec'];
		$this->_CodeToSend[19]=$this->_ActiveGroup;
		$this->send();
		$value=$value+10;
		if ($value > 100) $value=100;
		return $value;
	}
	public function KelvinDecrease($value=50) {
	
		$this->_repeatOK = false;
		$this->_CodeToSend = $this->_commandCodes['whiteAllOn'];
		$this->_CodeToSend[19]=$this->_ActiveGroup;
		$this->send();
		$this->_CodeToSend = $this->_commandCodes['whiteKelvinInc'];
		$this->_CodeToSend[19]=$this->_ActiveGroup;
		$this->send();
		$value=$value-10;
		if ($value < 0) $value=0;
		return $value;
	}
	public function OnKelvin($value=50) {
		$this->_repeatOK = false;
		if($value>100) $value=100;
		if($value<1) $value=1;
		$value= round($value/11)+1;
		$this->_CodeToSend = $this->_commandCodes['whiteAllOn'];
		$this->_CodeToSend[19]=$this->_ActiveGroup;
		$this->send();
		if ($value>6) {
			for ($i = 0; $i < 10; $i++) {
				$this->_CodeToSend = $this->_commandCodes['whiteKelvinDec'];
				$this->_CodeToSend[19]=$this->_ActiveGroup;
				$this->send();
			}
			for ($i = 0; $i < 11 - $value; $i++) {
				$this->_CodeToSend = $this->_commandCodes['whiteKelvinInc'];
				$this->_CodeToSend[19]=$this->_ActiveGroup;
				$this->send();
			}
		}
		else {
			for ($i = 0; $i < 10; $i++) {
				$this->_CodeToSend = $this->_commandCodes['whiteKelvinInc'];
				$this->_CodeToSend[19]=$this->_ActiveGroup;
				$this->send();
			}
			for ($i = 0; $i < $value-1; $i++) {
				$this->_CodeToSend = $this->_commandCodes['whiteKelvinDec'];
				$this->_CodeToSend[19]=$this->_ActiveGroup;
				$this->send();
			}
			
		}

	}
}
class W2_mylightWhiteTrkV6 extends W2_milightV6
{
	// 18 : chanel 19: ID LSB (compatibility with others 20: ID MSB

	public function On() {
		$this->_CodeToSend = $this->_commandCodes['rgbwwAllOnTrk'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;
		$this->send();
	}

	public function Off() {
		$this->_CodeToSend = $this->_commandCodes['rgbwwAllOffTrk'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;
		$this->send();
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
	public function OnBrightness($value=50,$color='#000000') {
		if ($value<0) $value=0;
		if ($value>100) $value=100;
		$this->_CodeToSend = $this->_commandCodes['rgbwwAllOnTrk'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;				
		$this->send();
		$this->_CodeToSend = $this->_commandCodes['rgbwwBrightnessTrk'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;
		$this->_CodeToSend[15]=$value;
		$this->send();
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
	public function OnBrightnessWhite($value=50,$color='#000000') {
		if ($value<0) $value=0;
		if ($value>100) $value=100;	
		$this->_CodeToSend = $this->_commandCodes['rgbwwAllOnTrk'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;				
		$this->send();
		$this->_CodeToSend = $this->_commandCodes['rgbwwBrightnessTrk'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;
		$this->_CodeToSend[15]=$value;
		$this->send();

	}
	public function OnNight() {
		$this->_CodeToSend = $this->_commandCodes['rgbwwAllOnTrk'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;				
		$this->send();
		$this->_CodeToSend = $this->_commandCodes['rgbwwAllNightModeTrk'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;
		$this->send();
	}	
	public function OnWhite() {
		$this->_CodeToSend = $this->_commandCodes['rgbwwAllOnTrk'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;				
		$this->send();
		$this->_CodeToSend = $this->_commandCodes['rgbwwAllWhiteOnTrk'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;
		$this->send();
	}
	public function OnSaturation($value=50) {
		//log::add($this->_log,'debug',"value=".$value);
		if ($value<0) $value=0;
		if ($value>100) $value=100;
		$this->_CodeToSend = $this->_commandCodes['rgbwwAllOnTrk'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;				
		$this->send();
		$this->_CodeToSend = $this->_commandCodes['rgbwwSaturationTrk'];
		$this->_CodeToSend[19]=$this->_ActiveGroup;
		$this->_CodeToSend[15]=$value;
		$this->send();
	}
	public function OnKelvin($value=50) {
		if ($value<0) $value=0;
		if ($value>100) $value=100;
		$this->_CodeToSend = $this->_commandCodes['rgbwwAllOnTrk'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;				
		$this->send();
		$this->_CodeToSend = $this->_commandCodes['rgbwwKelvinTrk'];
		$this->_CodeToSend[19]=$this->_ActiveGroup;
		$this->_CodeToSend[15]=$value;
		$this->send();
	}

	public function KelvinIncrease($value=50) {
		$value=$value+10;
		if ($value<0) $value=0;
		if ($value>100) $value=100;
		$this->_CodeToSend = $this->_commandCodes['rgbwwAllOnTrk'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;				
		$this->send();
		$this->_CodeToSend = $this->_commandCodes['rgbwwKelvinTrk'];
		$this->_CodeToSend[19]=$this->_ActiveGroup;
		$this->_CodeToSend[15]=$value;
		$this->send();
		return $value;
	}
	public function KelvinDecrease($value=50) {
		$value=$value-10;
		if ($value<0) $value=0;
		if ($value>100) $value=100;
		$this->_CodeToSend = $this->_commandCodes['rgbwwAllOnTrk'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;				
		$this->send();
		$this->_CodeToSend = $this->_commandCodes['rgbwwKelvinTrk'];
		$this->_CodeToSend[19]=$this->_ActiveGroup;
		$this->_CodeToSend[15]=$value;
		$this->send();
		return $value;
	}
	public function TrkUp($bMode) {
		$this->_CodeToSend = $this->_commandCodes['rgbwwAllOnTrk'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;				
		$this->send();
		if ($bMode==true)
			$this->_CodeToSend = $this->_commandCodes['rgbwwUpOnTrk'];
		else
			$this->_CodeToSend = $this->_commandCodes['rgbwwUpOffTrk'];
		$this->_CodeToSend[19]=$this->_ActiveGroup;		
		$this->send();	
	}
	public function TrkDown($bMode) {
		$this->_CodeToSend = $this->_commandCodes['rgbwwAllOnTrk'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;				
		$this->send();
		if ($bMode==true)
			$this->_CodeToSend = $this->_commandCodes['rgbwwDwOnTrk'];
		else
			$this->_CodeToSend = $this->_commandCodes['rgbwwDwOffTrk'];
		$this->_CodeToSend[19]=$this->_ActiveGroup;		
		$this->send();	
	}
	public function TrkLe($bMode) {
		$this->_CodeToSend = $this->_commandCodes['rgbwwAllOnTrk'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;				
		$this->send();
		if ($bMode==true)
			$this->_CodeToSend = $this->_commandCodes['rgbwwLeOnTrk'];
		else
			$this->_CodeToSend = $this->_commandCodes['rgbwwLeOffTrk'];
		$this->_CodeToSend[19]=$this->_ActiveGroup;		
		$this->send();	
	}
	public function TrkRi($bMode) {
		$this->_CodeToSend = $this->_commandCodes['rgbwwAllOnTrk'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;				
		$this->send();
		if ($bMode==true)
			$this->_CodeToSend = $this->_commandCodes['rgbwwRiOnTrk'];
		else
			$this->_CodeToSend = $this->_commandCodes['rgbwwRiOffTrk'];
		$this->_CodeToSend[19]=$this->_ActiveGroup;		
		$this->send();	
	}
}
class W2_mylightRGBWWTrkV6 extends W2_milightV6
{
	// 18 : chanel 19: ID LSB (compatibility with others 20: ID MSB

	public function On() {
		$this->_CodeToSend = $this->_commandCodes['rgbwwAllOnTrk'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;
		$this->send();
	}

	public function Off() {
		$this->_CodeToSend = $this->_commandCodes['rgbwwAllOffTrk'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;
		$this->send();
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
	public function OnBrightness($value=50,$color='#000000') {
		if ($value<0) $value=0;
		if ($value>100) $value=100;
		$this->_CodeToSend = $this->_commandCodes['rgbwwAllOnTrk'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;				
		$this->send();
		$this->_CodeToSend = $this->_commandCodes['rgbwwBrightnessTrk'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;
		$this->_CodeToSend[15]=$value;
		$this->send();
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
	public function OnBrightnessWhite($value=50,$color='#000000') {
		if ($value<0) $value=0;
		if ($value>100) $value=100;	
		$this->_CodeToSend = $this->_commandCodes['rgbwwAllOnTrk'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;				
		$this->send();
		$this->_CodeToSend = $this->_commandCodes['rgbwwBrightnessTrk'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;
		$this->_CodeToSend[15]=$value;
		$this->send();

	}
	public function OnNight() {
		$this->_CodeToSend = $this->_commandCodes['rgbwwAllOnTrk'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;				
		$this->send();
		$this->_CodeToSend = $this->_commandCodes['rgbwwAllNightModeTrk'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;
		$this->send();
	}	
	public function OnWhite() {
		$this->_CodeToSend = $this->_commandCodes['rgbwwAllOnTrk'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;				
		$this->send();
		$this->_CodeToSend = $this->_commandCodes['rgbwwAllWhiteOnTrk'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;
		$this->send();
	}
	public function OnSaturation($value=50) {
		//log::add($this->_log,'debug',"value=".$value);
		if ($value<0) $value=0;
		if ($value>100) $value=100;
		$this->_CodeToSend = $this->_commandCodes['rgbwwAllOnTrk'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;				
		$this->send();
		$this->_CodeToSend = $this->_commandCodes['rgbwwSaturationTrk'];
		$this->_CodeToSend[19]=$this->_ActiveGroup;
		$this->_CodeToSend[15]=$value;
		$this->send();
	}
	public function OnKelvin($value=50) {
		if ($value<0) $value=0;
		if ($value>100) $value=100;
		$this->_CodeToSend = $this->_commandCodes['rgbwwAllOnTrk'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;				
		$this->send();
		$this->_CodeToSend = $this->_commandCodes['rgbwwKelvinTrk'];
		$this->_CodeToSend[19]=$this->_ActiveGroup;
		$this->_CodeToSend[15]=$value;
		$this->send();
	}

	public function KelvinIncrease($value=50) {
		$value=$value+10;
		if ($value<0) $value=0;
		if ($value>100) $value=100;
		$this->_CodeToSend = $this->_commandCodes['rgbwwAllOnTrk'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;				
		$this->send();
		$this->_CodeToSend = $this->_commandCodes['rgbwwKelvinTrk'];
		$this->_CodeToSend[19]=$this->_ActiveGroup;
		$this->_CodeToSend[15]=$value;
		$this->send();
		return $value;
	}
	public function KelvinDecrease($value=50) {
		$value=$value-10;
		if ($value<0) $value=0;
		if ($value>100) $value=100;
		$this->_CodeToSend = $this->_commandCodes['rgbwwAllOnTrk'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;				
		$this->send();
		$this->_CodeToSend = $this->_commandCodes['rgbwwKelvinTrk'];
		$this->_CodeToSend[19]=$this->_ActiveGroup;
		$this->_CodeToSend[15]=$value;
		$this->send();
		return $value;
	
	}

	public function OnColor($color='Mint',$Bright) {
		$this->_CodeToSend = $this->_commandCodes['rgbwwAllOnTrk'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;				
		$this->send();
		$color = (string)$color;
		//log::add($this->_log,'debug','in color : '.$color);
		$this->_CodeToSend = $this->_commandCodes['rgbwwColorTrk'];
		$this->_CodeToSend[19]=$this->_ActiveGroup;
		//log::add($this->_log,'debug','Case');
		switch ($color) {
			case 'Random':		$this->_CodeToSend[15] = (int)mt_rand(0,255);  
								$this->_color[$this->_ActiveGroup]= $this->ColorTorgb($this->_CodeToSend[15]);
								break;
			case 'Blue':		$this->_CodeToSend[15] = (-0x00+170+256+10)%256; $this->_color[$this->_ActiveGroup]= '#0000FF' ; break;
			case 'Violet':		$this->_CodeToSend[15] = (-0xeb+170+256+10)%256; $this->_color[$this->_ActiveGroup]= '#7F00FF' ; break;
			case 'BabyBlue':	$this->_CodeToSend[15] = (-0x20+170+256+10)%256; $this->_color[$this->_ActiveGroup]= '#00bbff' ; break;
			case 'Aqua':		$this->_CodeToSend[15] = (-0x30+170+256+10)%256; $this->_color[$this->_ActiveGroup]= '#00FFFF' ; break;
			case 'SpringGreen':	$this->_CodeToSend[15] = (-0x40+170+256+10)%256; $this->_color[$this->_ActiveGroup]= '#00FF7F' ; break;
			case 'Mint':		$this->_CodeToSend[15] = (-0x4A+170+256+10)%256; $this->_color[$this->_ActiveGroup]= '#00FF43' ; break;
			case 'Green':		$this->_CodeToSend[15] = (-0x55+170+256+10)%256; $this->_color[$this->_ActiveGroup]= '#00FF00' ; break;
			case 'LimeGreen':	$this->_CodeToSend[15] = (-0x70+170+256+10)%256; $this->_color[$this->_ActiveGroup]= '#a1FF00' ; break;
			case 'Yellow':		$this->_CodeToSend[15] = (-0x80+170+256+10)%256; $this->_color[$this->_ActiveGroup]= '#FFFF00' ; break;
			case 'YellowOrange':$this->_CodeToSend[15] = (-0x88+170+256+10)%256; $this->_color[$this->_ActiveGroup]= '#FFD000' ; break;
			case 'Orange':		$this->_CodeToSend[15] = (-0x8F+170+256+10)%256; $this->_color[$this->_ActiveGroup]= '#FFA500' ; break;
			case 'Red':			$this->_CodeToSend[15] = (-0xaa+170+256+10)%256; $this->_color[$this->_ActiveGroup]= '#FF0000' ; break;
			case 'Pink':		$this->_CodeToSend[15] = (-0xbd+170+256+10)%256; $this->_color[$this->_ActiveGroup]= '#FF0061' ; break;
			case 'Fuchsia':		$this->_CodeToSend[15] = (-0xd6+170+256+10)%256; $this->_color[$this->_ActiveGroup]= '#FF00FF' ; break;
			case 'Lilac':		$this->_CodeToSend[15] = (-0xE0+170+256+10)%256; $this->_color[$this->_ActiveGroup]= '#D000FF' ; break;
			case 'Lavendar':	$this->_CodeToSend[15] = (-0xF0+170+256+10)%256; $this->_color[$this->_ActiveGroup]= '#6100FF' ; break;
			case ((int)$color > 0x00) && ((int)$color <= 0xff): 				
				$x = (int)$color; 
				$this->_color[$this->_ActiveGroup]=ColorTorgb($x);				
				$this->_CodeToSend[15] = $x;  
				break;
			case (substr($color,0,1)== "#"):
				$r = (int)hexdec(substr($color,1,2));
				$g = (int)hexdec(substr($color,3,2));
				$b = (int)hexdec(substr($color,5,2));
				//log::add($this->_log,'debug','subst');
				$this->_CodeToSend[15]= $this->rgbToColor($r,$g,$b);
				//log::add($this->_log,'debug','subst2');
				//log::add($this->_log,'debug','Internal Milight color :'.$this->_CodeToSend[15]." hex=".dechex($this->_CodeToSend[15]));
				$this->_color[$this->_ActiveGroup]= $color;
				//log::add($this->_log,'debug','222');
				break;
			default:
				$this->_CodeToSend[15] = 0x40; 
				$this->_color[$this->_ActiveGroup]=$this->ColorTorgb($this->_CodeToSend[15]);
				break;
		}
		$this->_CodeToSend[16]= $this->_CodeToSend[15];
		$this->_CodeToSend[17]= $this->_CodeToSend[15];
		$this->_CodeToSend[18]= $this->_CodeToSend[15];
		$this->send();
		//log::add($this->_log,'debug','put color hex : '.$this->_color[$this->_ActiveGroup]);	
	}
	public function OnDisco($prog,$speed=0) {
	//   1=rainbowSwirl, 2=whiteFade, 3=rgbwFade, 4=rainbowJump, 5=disco, 6=redTwinkle, 7=greenTwinkle, 8=blueTwinkle, 9=allCombined

		if ($prog < 1) $prog=1;
		if ($prog > 9) $prog=9;	
		$prog++;
		$this->_CodeToSend = $this->_commandCodes['rgbwwAllOnTrk'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;				
		$this->send();
		$this->_CodeToSend = $this->_commandCodes['rgbwwDiscoModeTrk'];
		$this->_CodeToSend[19]=$this->_ActiveGroup;
		$this->_CodeToSend[15]=$prog;		
		$this->send();
	}
	
	public function DiscoSlower() {
		$this->_CodeToSend = $this->_commandCodes['rgbwwAllOnTrk'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;				
		$this->send();
		$this->_CodeToSend = $this->_commandCodes['rgbwwDiscoSlowerTrk'];
		$this->_CodeToSend[19]=$this->_ActiveGroup;		
		$this->send();
	}
	public function DiscoFaster() {
		$this->_CodeToSend = $this->_commandCodes['rgbwwAllOnTrk'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;				
		$this->send();
		$this->_CodeToSend = $this->_commandCodes['rgbwwDiscoFasterTrk'];
		$this->_CodeToSend[19]=$this->_ActiveGroup;		
		$this->send();	
	}
	public function TrkUp($bMode) {
		$this->_CodeToSend = $this->_commandCodes['rgbwwAllOnTrk'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;				
		$this->send();
		if ($bMode==true)
			$this->_CodeToSend = $this->_commandCodes['rgbwwUpOnTrk'];
		else
			$this->_CodeToSend = $this->_commandCodes['rgbwwUpOffTrk'];
		$this->_CodeToSend[19]=$this->_ActiveGroup;		
		$this->send();	
	}
	public function TrkDown($bMode) {
		$this->_CodeToSend = $this->_commandCodes['rgbwwAllOnTrk'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;				
		$this->send();
		if ($bMode==true)
			$this->_CodeToSend = $this->_commandCodes['rgbwwDwOnTrk'];
		else
			$this->_CodeToSend = $this->_commandCodes['rgbwwDwOffTrk'];
		$this->_CodeToSend[19]=$this->_ActiveGroup;		
		$this->send();	
	}
	public function TrkLe($bMode) {
		$this->_CodeToSend = $this->_commandCodes['rgbwwAllOnTrk'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;				
		$this->send();
		if ($bMode==true)
			$this->_CodeToSend = $this->_commandCodes['rgbwwLeOnTrk'];
		else
			$this->_CodeToSend = $this->_commandCodes['rgbwwLeOffTrk'];
		$this->_CodeToSend[19]=$this->_ActiveGroup;		
		$this->send();	
	}
	public function TrkRi($bMode) {
		$this->_CodeToSend = $this->_commandCodes['rgbwwAllOnTrk'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;				
		$this->send();
		if ($bMode==true)
			$this->_CodeToSend = $this->_commandCodes['rgbwwRiOnTrk'];
		else
			$this->_CodeToSend = $this->_commandCodes['rgbwwRiOffTrk'];
		$this->_CodeToSend[19]=$this->_ActiveGroup;		
		$this->send();	
	}
}
class W2_mylightRGBV6b1 extends W2_milightV6
{

	public function On() {
		$this->_CodeToSend = $this->_commandCodes['rgbAllOnb1'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;
		$this->send();
	}

	public function Off() {
		$this->_CodeToSend = $this->_commandCodes['rgbAllOffb1'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;
		$this->send();
	}
	public function BrightnessIncrease($Br) {
		$this->_CodeToSend = $this->_commandCodes['rgbBrightnessIncb1'];
		$this->_CodeToSend[19]=$this->_ActiveGroup;
		$this->send();
		$Br=$Br+10;
		if ($Br > 100) $Br=100;
		return $Br;
	}
	public function BrightnessDecrease($Br) {
		$this->_CodeToSend = $this->_commandCodes['rgbBrightnessDecb1'];
		$this->_CodeToSend[19]=$this->_ActiveGroup;
		$this->send();
		$Br=$Br-10;
		if ($Br < 0) $Br=0;
		return $Br;
	}
	public function OnBrightness($value,$color='#000000') {
		if ($value<0) $value=0;
		if ($value>100) $value=100;	
		$value= round($value/11)+1;
		$this->_repeatOK = false;
		$this->_CodeToSend = $this->_commandCodes['rgbAllOnb1'];
		$this->_CodeToSend[19]=$this->_ActiveGroup;
		$this->send();
		if ($value>5) {
			for ($i = 0; $i < 10; $i++) {
				$this->_CodeToSend = $this->_commandCodes['rgbBrightnessIncb1'];
				$this->_CodeToSend[19]=$this->_ActiveGroup;
				$this->send();
			}
			for ($i = 0; $i <11-$value; $i++) {
				$this->_CodeToSend = $this->_commandCodes['rgbBrightnessDecb1'];
				$this->_CodeToSend[19]=$this->_ActiveGroup;
				$this->send();
			}
		}
		else {
			for ($i = 0; $i < 10; $i++) {
				$this->_CodeToSend = $this->_commandCodes['rgbBrightnessDecb1'];
				$this->_CodeToSend[19]=$this->_ActiveGroup;
				$this->send();
			}
			for ($i = 0; $i <$value-1; $i++) {
				$this->_CodeToSend = $this->_commandCodes['rgbBrightnessIncb1'];
				$this->_CodeToSend[19]=$this->_ActiveGroup;
				$this->send();
			}
		}

	}
	public function OnColor($color='Mint',$Bright) {
		$this->_CodeToSend = $this->_commandCodes['rgbAllOnb1'];
		$this->_CodeToSend[19] = 1;				
		$this->send();
		$color = (string)$color;
		//log::add($this->_log,'debug','in color : '.$color);
		$this->_CodeToSend = $this->_commandCodes['rgbColorb1'];
		$this->_CodeToSend[19]=$this->_ActiveGroup;
		//log::add($this->_log,'debug','Case');
		switch ($color) {
			case 'Random':		$this->_CodeToSend[15] = (int)mt_rand(0,255);  
								$this->_color[$this->_ActiveGroup]= $this->ColorTorgb($this->_CodeToSend[15]);
								break;
			case 'Blue':		$this->_CodeToSend[15] = (-0x00+170+256+10)%256; $this->_color[$this->_ActiveGroup]= '#0000FF' ; break;
			case 'Violet':		$this->_CodeToSend[15] = (-0xeb+170+256+10)%256; $this->_color[$this->_ActiveGroup]= '#7F00FF' ; break;
			case 'BabyBlue':	$this->_CodeToSend[15] = (-0x20+170+256+10)%256; $this->_color[$this->_ActiveGroup]= '#00bbff' ; break;
			case 'Aqua':		$this->_CodeToSend[15] = (-0x30+170+256+10)%256; $this->_color[$this->_ActiveGroup]= '#00FFFF' ; break;
			case 'SpringGreen':	$this->_CodeToSend[15] = (-0x40+170+256+10)%256; $this->_color[$this->_ActiveGroup]= '#00FF7F' ; break;
			case 'Mint':		$this->_CodeToSend[15] = (-0x4A+170+256+10)%256; $this->_color[$this->_ActiveGroup]= '#00FF43' ; break;
			case 'Green':		$this->_CodeToSend[15] = (-0x55+170+256+10)%256; $this->_color[$this->_ActiveGroup]= '#00FF00' ; break;
			case 'LimeGreen':	$this->_CodeToSend[15] = (-0x70+170+256+10)%256; $this->_color[$this->_ActiveGroup]= '#a1FF00' ; break;
			case 'Yellow':		$this->_CodeToSend[15] = (-0x80+170+256+10)%256; $this->_color[$this->_ActiveGroup]= '#FFFF00' ; break;
			case 'YellowOrange':$this->_CodeToSend[15] = (-0x88+170+256+10)%256; $this->_color[$this->_ActiveGroup]= '#FFD000' ; break;
			case 'Orange':		$this->_CodeToSend[15] = (-0x8F+170+256+10)%256; $this->_color[$this->_ActiveGroup]= '#FFA500' ; break;
			case 'Red':			$this->_CodeToSend[15] = (-0xaa+170+256+10)%256; $this->_color[$this->_ActiveGroup]= '#FF0000' ; break;
			case 'Pink':		$this->_CodeToSend[15] = (-0xbd+170+256+10)%256; $this->_color[$this->_ActiveGroup]= '#FF0061' ; break;
			case 'Fuchsia':		$this->_CodeToSend[15] = (-0xd6+170+256+10)%256; $this->_color[$this->_ActiveGroup]= '#FF00FF' ; break;
			case 'Lilac':		$this->_CodeToSend[15] = (-0xE0+170+256+10)%256; $this->_color[$this->_ActiveGroup]= '#D000FF' ; break;
			case 'Lavendar':	$this->_CodeToSend[15] = (-0xF0+170+256+10)%256; $this->_color[$this->_ActiveGroup]= '#6100FF' ; break;
			case ((int)$color > 0x00) && ((int)$color <= 0xff): 				
				$x = (int)$color; 
				$this->_color[$this->_ActiveGroup]=ColorTorgb($x);				
				$this->_CodeToSend[15] = $x;  
				break;
			case (substr($color,0,1)== "#"):
				$r = (int)hexdec(substr($color,1,2));
				$g = (int)hexdec(substr($color,3,2));
				$b = (int)hexdec(substr($color,5,2));
				//log::add($this->_log,'debug','subst');
				$this->_CodeToSend[15]= $this->rgbToColor($r,$g,$b);
				//log::add($this->_log,'debug','subst2');
				//log::add($this->_log,'debug','Internal Milight color :'.$this->_CodeToSend[15]." hex=".dechex($this->_CodeToSend[15]));
				$this->_color[$this->_ActiveGroup]= $color;
				//log::add($this->_log,'debug','222');
				break;
			default:
				$this->_CodeToSend[15] = 0x40; 
				$this->_color[$this->_ActiveGroup]=$this->ColorTorgb($this->_CodeToSend[15]);
				break;
		}
		$this->_CodeToSend[16]= $this->_CodeToSend[15];
		$this->_CodeToSend[17]= $this->_CodeToSend[15];
		$this->_CodeToSend[18]= $this->_CodeToSend[15];
		$this->send();
		//log::add($this->_log,'debug','put color hex : '.$this->_color[$this->_ActiveGroup]);	
	}
	public function OnDisco($prog,$speed=0) {
	//   1=rainbowSwirl, 2=whiteFade, 3=rgbwFade, 4=rainbowJump, 5=disco, 6=redTwinkle, 7=greenTwinkle, 8=blueTwinkle, 9=allCombined

		if ($prog < 1) $prog=1;
		if ($prog > 9) $prog=9;	
		$prog++;
		$this->_CodeToSend = $this->_commandCodes['rgbwwAllOn'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;				
		$this->send();
		$this->_CodeToSend = $this->_commandCodes['rgbwwDiscoMode'];
		$this->_CodeToSend[19]=$this->_ActiveGroup;
		$this->_CodeToSend[15]=$prog;		
		$this->send();
	}
	
	public function DiscoSlower() {
		$this->_CodeToSend = $this->_commandCodes['rgbAllOnb1'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;				
		$this->send();
		$this->_CodeToSend = $this->_commandCodes['rgbDiscoSlowerb1'];
		$this->_CodeToSend[19]=$this->_ActiveGroup;		
		$this->send();
	}
	public function DiscoFaster() {
		$this->_CodeToSend = $this->_commandCodes['rgbAllOnb1'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;				
		$this->send();
		$this->_CodeToSend = $this->_commandCodes['rgbDiscoFasterb1'];
		$this->_CodeToSend[19]=$this->_ActiveGroup;		
		$this->send();	
	}
	public function OnDiscoNext() {
		$this->_CodeToSend = $this->_commandCodes['rgbAllOnb1'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;				
		$this->send();
		$this->_CodeToSend = $this->_commandCodes['rgbDiscoModeIncreaseb1'];
		$this->_CodeToSend[19]=$this->_ActiveGroup;
		$this->send();
	}

	public function OnDiscoPrev() {
		$this->_CodeToSend = $this->_commandCodes['rgbAllOnb1'];
		$this->_CodeToSend[19] = $this->_ActiveGroup;				
		$this->send();
		$this->_CodeToSend = $this->_commandCodes['rgbDiscoModeDecreaseb1'];
		$this->_CodeToSend[19]=$this->_ActiveGroup;	
		$this->send();
	}
}

?>
