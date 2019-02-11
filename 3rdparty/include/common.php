<?php
if (!defined('SUCCESS')) define('SUCCESS',"-1");
if (!defined('NOSOCKET'))define('NOSOCKET',"-2");
if (!defined('NOTCONNECTED')) define('NOTCONNECTED',"-3");
if (!defined('NORESPONSE')) define('NORESPONSE',"-4");
if (!defined('BADRESPONSE')) define('BADRESPONSE',"-5");
if (!defined('NOSTATE')) define('NOSTATE',"-6");
if (!defined('MODEON')) define('MODEON',"-7");
if (!defined('MODEONBG')) define('MODEONBG',"-8");
class wifilightV2c {
	public $_return_WFL = array(
			'Intensity' => -1,
			'White' => -1,
			'White2' => -1,
			'Color' => -1,
			'Prog' => -1,
			'Speed' => -1,
			'On' => -1,
			'Play' => -1,
			'Sat' => -1,
			'Kelvin' => -1,
			'Connected' => -1,
			'AmbColor' => -1,
			'AmbKelvin' => -1,
			'AmbWhite' => -1,
			'Eye' => -1,
			'DiscoNum' => -1,
			'NightMode' => -1,
			'EyeNotify' => -1,
			'CCTAuto' => -1,
			'AmbIntensity' => -1,
			'AmbOn' => -1,
			'Timer' => -1,
			'Current' => -1,
			'Power' => -1,
			'Voltage' => -1,
			'Consommation' => -1,
			'Type' => 'No state return for this bulb'
		);
	public $_log = "wifilightV2";
}
?>