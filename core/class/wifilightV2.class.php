<?php
/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

/******************************* Includes *******************************/ 
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
require_once dirname(__FILE__) . '/../../3rdparty/include/common.php';

$cfg = dirname(__FILE__) .'/../../3rdparty/';
$dossier = opendir($cfg);
while($fichier = readdir($dossier)){
    if(is_file($cfg.$fichier) && $fichier !='/'){
      if ( pathinfo($fichier, PATHINFO_EXTENSION)== "php")require_once dirname(__FILE__) . '/../../3rdparty//' . $fichier;
	}
}
closedir($dossier);
class wifilightV2 extends eqLogic {
	
	
	public static function deamon_info() {
		$return = array();
		$return['log'] = '';
		$return['state'] = 'nok';
		$cron = cron::byClassAndFunction('wifilightV2', 'daemon');
		if (is_object($cron) && $cron->running()) {
			$return['state'] = 'ok';
		}
		$return['launchable'] = 'ok';
		return $return;
	}

	public static function deamon_start($_debug = false) {
		self::deamon_stop();
		$deamon_info = self::deamon_info();
		if ($deamon_info['launchable'] != 'ok') {
			throw new Exception(__('Veuillez vérifier la configuration', __FILE__));
		}
		$cron = cron::byClassAndFunction('wifilightV2', 'daemon');
		if (!is_object($cron)) {
			throw new Exception(__('Tache cron introuvable', __FILE__));
		}
		log::add('wifilightV2', 'debug', 'start');
		$cron->run();
	}

	public static function deamon_stop() {
		$cron = cron::byClassAndFunction('wifilightV2', 'daemon');
		if (!is_object($cron)) {
			throw new Exception(__('Tache cron introuvable', __FILE__));
		}
		$cron->halt();
	}
	public static function daemon() { 
	/*
		$msg="M-SEARCH * HTTP/1.1\r\n"."HOST: 239.255.255.250:1982\r\n"."MAN: \"ssdp:discover\"\r\n"."ST: wifi_bulb\r\n";
		$deviceIp ='239.255.255.250';
		$sock = socket_create( AF_INET, SOCK_DGRAM, 0 );
		$opt_ret = socket_set_option($sock, SOL_SOCKET, SO_BROADCAST, true);
		if (!socket_set_option($sock, SOL_SOCKET, SO_REUSEADDR, 1)) {
			log::add('wifilightV2','debug',socket_strerror(socket_last_error($sock)));
			//exit;
		}
		if (!socket_set_option( $sock, SOL_SOCKET, SO_RCVTIMEO, array( 'sec'=>1, 'usec'=>'0' ))) {
			log::add('wifilightV2','debug',socket_strerror(socket_last_error($sock)));
			exit;
		}
		$send_ret = socket_sendto( $sock, $msg, strlen( $msg ), 0, $deviceIp, 1982);
		if(!($sock = socket_create(AF_INET, SOCK_DGRAM, 0))) {
			$errorcode = socket_last_error();
			$errormsg = socket_strerror($errorcode); 
			log::add('wifilightV2','debug',die("Couldn't create socket: [$errorcode] $errormsg \n"));
		}
		// Bind the source address
		if( !socket_bind($sock, "0.0.0.0" , 1982) )
		{
			$errorcode = socket_last_error();
			$errormsg = socket_strerror($errorcode);
			 
			log::add('wifilightV2','debug',die("Couldn't bind socket: [$errorcode] $errormsg \n"));
		}
		socket_set_nonblock($sock);	
		*/		
		$time=0;
		$devices=array();
		$sockets=array();
		$Ids=array();
		log::add('wifilightV2', 'debug', 'Daemon Started');
		while(true) {	
			if (time() > $time + 300) {
				//log::add('wifilightV2','debug','time');
				foreach ($sockets as $socket){
					socket_close($socket);
				}	
				$time = time();
				$devices=array();
				$classes=array();
				$sockets=array();
				$eqLogics=array();
				foreach (eqLogic::byType('wifilightV2') as $eqLogic){	
					$class = $eqLogic->getConfiguration('WLClass');
					if ($class!='') {
						$classW2 = "W2_".$class;
						$myLight = new $classW2(0,0,0,0,0);
						if (method_exists($myLight,'Create') && $eqLogic->getIsEnable()) {
							$IPaddr = $eqLogic->getConfiguration('addr');
							$ret = $myLight->Create($socket,$IPaddr);					
							//$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
							//if( !socket_connect($socket,$IPaddr,55443) ){
							if ($ret === false) {
								$errorcode = socket_last_error();
								$errormsg = socket_strerror($errorcode);
								log::add('wifilightV2', 'debug', 'Connexion au socket impossible ' . $errorcode . ' : ' . $errormsg);
							}
							else {
								socket_set_nonblock($socket);
								$classes[] = $classW2;
								$devices[] = $IPaddr;
								$eqLogics[] = $eqLogic;
								$sockets[] = $socket;
								//log::add('wifilightV2','debug','Sock OK :'.$IPaddr);
							}	
						}
					}
				}
			}	
			/*			
			//Receive broadcast messages
			$buf="";
			$r = socket_recvfrom($sock, $buf, 512, 0, $remote_ip, $remote_port);
			if ($buf!="") log::add('wifilightV2','debug',"$remote_ip : $remote_port -- " . $buf);
			*/
			// receive unicast messages
			foreach ($sockets as $keySock =>$socket){
				$buf="";
				$buf = socket_read($socket, 1000);
				$errorcode = socket_last_error($socket);
				if ($errorcode!=0) {	 
					$errormsg = socket_strerror($errorcode);
					socket_clear_error();
					//log::add('wifilightV2','debug',"Error :" . $errormsg);
				}
				if ($buf!="") {
					//log::add('wifilightV2','debug',"Rec: ". $buf);
					$myLight = new $classes[$keySock]($devices[$keySock],0,0,0,0);
					$state = $myLight -> Decode($buf);
					/*
					ob_start();
					 var_dump($state);
					 $res = ob_get_clean();
					 log::add('wifilightV2','debug','Vreturn state:'.$res );
					*/
					if ($state !== false) {
						//log::add('wifilightV2','debug','Update');
						self::update($state,$eqLogics[$keySock]);
					}
				}
			}
			usleep(500);
		}
	}
	public function stopDaemon() {
		$cron = cron::byClassAndFunction('wifilightV2', 'daemon');
		$cron->stop();
		$cron->start();
	}
	public static function update($state,$eqLogic) {
		/*				 
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
		'AmbOn' => -1,
		'Eye' => -1,
		'DiscoNum' => -1,
		'NightMode' => -1,
		'EyeNotify' => -1,
		'CCTAuto' => -1,
		'AmbIntensity' => -1,
		'Timer' => -1,
		'Type' => true
		*/	
		if (isset ($state)) {
			if ($state!=NOSOCKET && $state!=NOTCONNECTED && $state!=NORESPONSE && $state!=BADRESPONSE) {				
				if ($state['Type'] === true) {
					//if ($state['White'] != -1) log::add ('wifilightV2','debug',"White:".$state['White']);							
					$monoSlider = $eqLogic->getConfiguration('monoSlider');
					if ($state['White'] != -1 ) {
						$Cmd = $eqLogic->getCmd(null, 'WhiteGet');
						if ($Cmd !== false) {
						
						  $Cmd->event($state['White']);
						  $Cmd->save();
						}
						else {
							$Cmd = $eqLogic->getCmd(null, 'WhiteWarmGet');
							if ($Cmd !== false) {
							  $Cmd->event($state['White']);
							  $Cmd->save();
							}		
						}
					}
				
					//if ($state['White2'] != -1) log::add ('wifilightV2','debug',"White2:".$state['White2']);
					if ($state['White2'] != -1 ) {
						$Cmd = $eqLogic->getCmd(null, 'WhiteCoolGet');
						if ($Cmd !== false) {
						  $Cmd->event($state['White2']);
						  $Cmd->save();
						}
					}
					//if ($state['Prog'] != -1) log::add ('wifilightV2','debug',"prog:".$state['Prog']);
					if ($state['Prog'] != -1 ) {
						$Cmd = $eqLogic->getCmd(null, 'DiscoProgGet');
						if ($Cmd !== false) {
						  $Cmd->event($state['Prog']);
						  $Cmd->save();
						}
					}
					//if ($state['Speed'] != -1) log::add ('wifilightV2','debug',"speed:".$state['Speed']);
					if ($state['Speed'] != -1 ) {
						$Cmd = $eqLogic->getCmd(null, 'DiscoSpeedGet');
						if ($Cmd !== false) {
						  $Cmd->event($state['Speed']);
						  $Cmd->save();
						}
					}
					//if ($state['Sat'] != -1) log::add ('wifilightV2','debug',"Sat:".$state['Sat']);
					if ($state['Sat'] != -1 ) {
						$Cmd = $eqLogic->getCmd(null, 'SaturationGet');
						if ($Cmd !== false) {
						  $Cmd->event($state['Sat']);
						  $Cmd->save();
						}
					}
					//if ($state['Kelvin'] != -1) log::add ('wifilightV2','debug',"Kelvin:".$state['Kelvin']);
					if ($state['Kelvin'] != -1) {
						$Cmd = $eqLogic->getCmd(null, 'KelvinGet');
						if ($Cmd !== false) {
						  $Cmd->event($state['Kelvin']);
						  $Cmd->save();
						}
					}
					//if ($state['On'] != -1) log::add ('wifilightV2','debug',"On:".$state['On']);
					if ($state['On'] != -1 ) {
						$Cmd = $eqLogic->getCmd(null, 'SwOnOffGet');
						if ($Cmd !== false) {
						  $Cmd->event($state['On']);
						  $Cmd->save();
						}
					}

					
					//if ($state['Play'] != -1) log::add ('wifilightV2','debug',"Play:".$state['Play']);
					if ($state['Play'] != -1) {
						$Cmd = $eqLogic->getCmd(null, 'SwPlayRunGet');
						if ($Cmd !== false) {
						  $Cmd->event($state['Play']);
						  $Cmd->save();
						}
					}
					//if ($state['Intensity'] != -1) log::add ('wifilightV2','debug',"Intensity:".$state['Intensity']);
					//if ($state['Color'] != -1) log::add ('wifilightV2','debug',"Color:".$state['Color']);
					if ($state['Intensity'] != -1 && $state['Color'] != -1 ) { // the controller manages both
						$Cmd = $eqLogic->getCmd(null, 'IntensityGet');
						if ($Cmd !== false) {
						  $Cmd->event($state['Intensity']);
						  $Cmd->save();
						}                     
						$Cmd = $eqLogic->getCmd(null, 'ColorGet');
						if ($Cmd !== false) {
						  $Cmd->event($state['Color']);
						  $Cmd->save();
						}
					}
					else {
						if ($state['Color'] != -1) { // go to the complicated sync of jeedom with controller
							// compute rgb color from controller
							$ColCtrl = self::hex2rgb($state['Color']);
							// get jeedom state
							$Cmd = $eqLogic->getCmd(null, 'ColorGet');
							if ($Cmd === false) return false;
							$colhex=$Cmd->execCmd();
							$colJee=self::hex2rgb($colhex);
							$Cmd = $eqLogic->getCmd(null, 'IntensityGet');
							if ($Cmd === false) return false;
							$intensity=$Cmd->execCmd();
							// compute the resultant intensity*color for jeedom
							$colJee[0]=floor($colJee[0]*$intensity/100);
							$colJee[1]=floor($colJee[1]*$intensity/100);
							$colJee[2]=floor($colJee[2]*$intensity/100);
							// log::add('wifilightV2', 'debug', 'Col Jeedom : '.$colJee[0]." ".$colJee[1]." ".$colJee[2]." ");
							// log::add('wifilightV2', 'debug', 'Col Ctrl   : '.$ColCtrl[0]." ".$ColCtrl[1]." ".$ColCtrl[2]." ");			
							if (($ColCtrl[0]!= $colJee[0]) || ($ColCtrl[1]!= $colJee[1]) || ($ColCtrl[2]!= $colJee[2])) {
								// col has been changed from controller
								$max=max($ColCtrl);
								if ($max>2) { // saturates one of the colors as the controller does
									$intensity=$max/255; //(0->1)
									$ColCtrl[0]=round($ColCtrl[0]/$intensity);
									$ColCtrl[1]=round($ColCtrl[1]/$intensity);
									$ColCtrl[2]=round($ColCtrl[2]/$intensity);
									$intensity=round($intensity*100);
								}
								else { // too small then keep jeedom color
									$Cmd = $eqLogic->getCmd(null, 'ColorGet');
									if ($Cmd === false)  return false;
									$colhex=$Cmd->execCmd();
									$ColCtrl=self::hex2rgb($colhex);
									$intensity=round(100*$max/255);				
								}	
								// update jeedom with intensity and color
								$hex = self::rgb2hex($ColCtrl);
								//log::add('wifilightV2', 'debug', 'Color update :'.$hex);
								//log::add('wifilightV2', 'debug', 'Intens col update :'.$intensity);
							  
								$Cmd = $eqLogic->getCmd(null, 'ColorGet');
								if ($Cmd !== false) {
								  $Cmd->event($hex);
								  $Cmd->save();
								}
								$Cmd = $eqLogic->getCmd(null, 'IntensityGet');
								if ($Cmd !== false) {
								  $Cmd->event($intensity);
								  $Cmd->save();
								}
							}   
						}	
					}
					//if ($state['AmbIntensity'] != -1) log::add ('wifilightV2','debug',"AmbIntensity:".$state['AmbIntensity']);
					//if ($state['AmbColor'] != -1) log::add ('wifilightV2','debug',"AmbColor:".$state['AmbColor']);
					if ($state['AmbIntensity'] != -1 && $state['AmbColor'] != -1 ) { // the controller manages both
						$Cmd = $eqLogic->getCmd(null, 'IntensityAmbGet');
						if ($Cmd !== false) {
						  $Cmd->event($state['IntensityAmb']);
						  $Cmd->save();
						}                     
						$Cmd = $eqLogic->getCmd(null, 'ColorAmbGet');
						if ($Cmd !== false) {
						  $Cmd->event($state['ColorAmb']);
						  $Cmd->save();
						}
					}
					else {
						if ($state['AmbColor'] != -1 ) { // go to the complicated sync of jeedom with controller
							// compute rgb color from controller
							$ColCtrl = self::hex2rgb($state['ColorAmb']);
							// get jeedom state
							$Cmd = $eqLogic->getCmd(null, 'ColorAmbGet');
							if ($Cmd === false) return false;
							$colhex=$Cmd->execCmd();
							$colJee=self::hex2rgb($colhex);
							$Cmd = $eqLogic->getCmd(null, 'IntensityAmbGet');
							if ($Cmd === false) return false;
							$intensity=$Cmd->execCmd();
							// compute the resultant intensity*color for jeedom
							$colJee[0]=floor($colJee[0]*$intensity/100);
							$colJee[1]=floor($colJee[1]*$intensity/100);
							$colJee[2]=floor($colJee[2]*$intensity/100);
							// log::add('wifilightV2', 'debug', 'Col Jeedom : '.$colJee[0]." ".$colJee[1]." ".$colJee[2]." ");
							// log::add('wifilightV2', 'debug', 'Col Ctrl   : '.$ColCtrl[0]." ".$ColCtrl[1]." ".$ColCtrl[2]." ");			
							if (($ColCtrl[0]!= $colJee[0]) || ($ColCtrl[1]!= $colJee[1]) || ($ColCtrl[2]!= $colJee[2])) {
								// col has been changed from controller
								$max=max($ColCtrl);
								if ($max>2) { // saturates one of the colors as the controller does
									$intensity=$max/255; //(0->1)
									$ColCtrl[0]=round($ColCtrl[0]/$intensity);
									$ColCtrl[1]=round($ColCtrl[1]/$intensity);
									$ColCtrl[2]=round($ColCtrl[2]/$intensity);
									$intensity=round($intensity*100);
								}
								else { // too small then keep jeedom color
									$Cmd = $eqLogic->getCmd(null, 'ColorAmbGet');
									if ($Cmd === false)  return false;
									$colhex=$Cmd->execCmd();
									$ColCtrl=self::hex2rgb($colhex);
									$intensity=round(100*$max/255);				
								}	
								// update jeedom with intensity and color
								$hex = self::rgb2hex($ColCtrl);
								//log::add('wifilightV2', 'debug', 'Color bg update :'.$hex);
								//log::add('wifilightV2', 'debug', 'Intens bg col update :'.$intensity);
							  
								$Cmd = $eqLogic->getCmd(null, 'ColorAmbGet');
								if ($Cmd !== false) {
								  $Cmd->event($hex);
								  $Cmd->save();
								}
								$Cmd = $eqLogic->getCmd(null, 'IntensityAmbGet');
								if ($Cmd !== false) {
								  $Cmd->event($intensity);
								  $Cmd->save();
								}
							}   
						}	
					}
					//if ($state['AmbKelvin'] != -1) log::add ('wifilightV2','debug',"AmbKelvin:".$state['AmbKelvin']);
					if ($state['AmbKelvin'] != -1) {
						$Cmd = $eqLogic->getCmd(null, 'KelvinGetAmb');
						if ($Cmd !== false) {
						  $Cmd->event($state['AmbKelvin']);
						  $Cmd->save();
						}
					}
					//if ($state['AmbWhite'] != -1) log::add ('wifilightV2','debug',"AmbWhite:".$state['AmbWhite']);
					if ($state['AmbWhite'] != -1) {
						$Cmd = $eqLogic->getCmd(null, 'WhiteAmb');
						if ($Cmd !== false) {
						  $Cmd->event($state['AmbKelvin']);
						  $Cmd->save();
						}
					}
					//if ($state['AmbOn'] != -1) log::add ('wifilightV2','debug',"AmbOn:".$state['AmbOn']);
					if ($state['AmbOn'] != -1 ) {
						$Cmd = $eqLogic->getCmd(null, 'SwAmbGet');
						if ($Cmd !== false) {
						  $Cmd->event($state['AmbOn']);
						  $Cmd->save();
						}
					}							
					//if ($state['Eye'] != -1) log::add ('wifilightV2','debug',"Eye:".$state['Eye']);
					if ($state['Eye'] != -1 ) {
						$Cmd = $eqLogic->getCmd(null, 'SwCareGet');
						if ($Cmd !== false) {
						  $Cmd->event($state['Eye']);
						  $Cmd->save();
						}
					}
					//if ($state['DiscoNum'] != -1) log::add ('wifilightV2','debug',"DiscoNum:".$state['DiscoNum']);
					if ($state['DiscoNum'] != -1 ) {
						$Cmd = $eqLogic->getCmd(null, 'SwCareGet');
						if ($Cmd !== false) {
						  $Cmd->event($state['DiscoProgGet']);
						  $Cmd->save();
						}
					}							
					//if ($state['NightMode'] != -1) log::add ('wifilightV2','debug',"NightMode:".$state['NightMode']);
					if ($state['NightMode'] != -1 ) {
						$Cmd = $eqLogic->getCmd(null, 'SwNightLightGet');
						if ($Cmd !== false) {
						  $Cmd->event($state['SwNightLightGet']);
						  $Cmd->save();
						}
					}							
					//if ($state['EyeNotify'] != -1) log::add ('wifilightV2','debug',"EyeNotify:".$state['EyeNotify']);
					if ($state['EyeNotify'] != -1 ) {
						$Cmd = $eqLogic->getCmd(null, 'SwCareNotGet');
						if ($Cmd !== false) {
						  $Cmd->event($state['EyeNotify']);
						  $Cmd->save();
						}
					}
					//if ($state['CCTAuto'] != -1) log::add ('wifilightV2','debug',"CCTAuto:".$state['CCTAuto']);
					if ($state['CCTAuto'] != -1 ) {
						$Cmd = $eqLogic->getCmd(null, 'SwCCTAutoGet');
						if ($Cmd !== false) {
						  $Cmd->event($state['Eye']);
						  $Cmd->save();
						}
					}	
					//if ($state['AmbOn'] != -1) log::add ('wifilightV2','debug',"AmbOn:".$state['AmbOn']);
					if ($state['AmbOn'] != -1 ) {
						$Cmd = $eqLogic->getCmd(null, 'SwAmbGet');
						if ($Cmd !== false) {
						  $Cmd->event($state['Eye']);
						  $Cmd->save();
						}
					}
					//if ($state['AmbIntensity'] != -1) log::add ('wifilightV2','debug',"AmbIntensity:".$state['AmbIntensity']);
					if ($state['AmbIntensity'] != -1) {
						$Cmd = $eqLogic->getCmd(null, 'IntensityAmbGet');
						if ($Cmd !== false) {
						  $Cmd->event($state['AmbIntensity']);
						  $Cmd->save();
						}
					}							
					//if ($state['Timer'] != -1) log::add ('wifilightV2','debug',"Timer:".$state['Timer']);
					if ($state['Timer'] != -1) {
						$Cmd = $eqLogic->getCmd(null, 'TimerGet');
						if ($Cmd !== false) {
						  $Cmd->event($state['Timer']);
						  $Cmd->save();
						}
					}
					//if ($state['Current'] != -1) log::add ('wifilightV2','debug',"Current:".$state['Current']);
					if ($state['Current'] != -1) {
						$Cmd = $eqLogic->getCmd(null, 'CurrentGet');
						if ($Cmd !== false) {
						  $Cmd->event($state['Current']);
						  $Cmd->save();
						}
					}					
					//if ($state['Power'] != -1) log::add ('wifilightV2','debug',"Power:".$state['Timer']);
					if ($state['Power'] != -1) {
						$Cmd = $eqLogic->getCmd(null, 'PowerGet');
						if ($Cmd !== false) {
						  $Cmd->event($state['Power']);
						  $Cmd->save();
						}
					}
					//if ($state['Voltage'] != -1) log::add ('wifilightV2','debug',"Voltage:".$state['Voltage']);
					if ($state['Voltage'] != -1) {
						$Cmd = $eqLogic->getCmd(null, 'VoltageGet');
						if ($Cmd !== false) {
						  $Cmd->event($state['Voltage']);
						  $Cmd->save();
						}
					}
					if ($state['Consommation'] != -1) {
						$Cmd = $eqLogic->getCmd(null, 'ConsumptionGet');
						if ($Cmd !== false) {
						  $Cmd->event($state['Consommation']);
						  $Cmd->save();
						}
					}
					if ($state['Current'] != -1) {
						$Cmd = $eqLogic->getCmd(null, 'CurrentGet');
						if ($Cmd !== false) {
						  $Cmd->event($state['Current']);
						  $Cmd->save();
						}
					}					
					$Cmd = $eqLogic->getCmd(null, 'ConnectedGet');
					if ($Cmd !== false) {
						$Cmd->event(SUCCESS);
						$Cmd->save();
					}							
				}
				else {
				
					$Cmd = $eqLogic->getCmd(null, 'ConnectedGet');
					if ($Cmd !== false) {
						$Cmd->event(NOSTATE);
						$Cmd->save();
					}							
				}
			}
			else {
				$Cmd = $eqLogic->getCmd(null, 'ConnectedGet');
				if ($Cmd !== false) {
					$Cmd->event($state);
					$Cmd->save();
				}
			}
			
		}
		else {
				//log::add('wifilightV2', 'debug', 'End State update '.$class.': No response from device');
				$Cmd = $eqLogic->getCmd(null, 'ConnectedGet');
				if ($Cmd !== false) {
					$Cmd->event(BADRESPONSE);
					$Cmd->save();
				}
		}
		return true;
	}
	public static function cron() {
		//return;
		log::add('wifilightV2', 'info', '!!************************** Start cron update WifilightV2 *******************************!!');	
        foreach (eqLogic::byType('wifilightV2') as $eqLogic){
			$class = $eqLogic->getConfiguration('WLClass');	
            if (($class!== false) && ($class != '' ) && $eqLogic->getIsEnable()) {
				log::add('wifilightV2', 'info', '<<<<<<< Device: '.$eqLogic->getName().' - Class: '.$class.">>>>>>>");
				$incremV = $eqLogic->getConfiguration('incremV');
				if ($incremV === false){
					$incremV = 10;
				}	
				/*			
					 ob_start();
					 var_dump($state);
					 $res = ob_get_clean();
					 log::add('wifilightV2','debug','Vreturn state:'.$res );
				 */				
				$classW2 = "W2_".$class;
				$myLight = new $classW2($eqLogic->getConfiguration('addr'),$eqLogic->getConfiguration('delai'),$eqLogic->getConfiguration('repetitions'),$incremV,$eqLogic->getConfiguration('macad'),$eqLogic->getConfiguration('identifiant'),$eqLogic->getConfiguration('nbLeds'),$eqLogic->getConfiguration('colorOrder'));
                $state = $myLight->retStatus();					
				if (self::update($state,$eqLogic)) {
					$Cmd = $eqLogic->getCmd(null, 'ConnectedGet');
					if ($Cmd !== false) {
						$Connected = $Cmd->execCmd();
						log::add('wifilightV2', 'debug',"Connected:".$Connected);
					}
				}
            }
		}
		log::add('wifilightV2', 'info', '!!**************************   End cron update WifilightV2 *******************************!!');	
    }
	public static function hex2rgb($hex) {
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
	public static function rgb2hex($rgb) {
	   $hex = "#";
	   $hex .= str_pad(dechex($rgb[0]), 2, "0", STR_PAD_LEFT);
	   $hex .= str_pad(dechex($rgb[1]), 2, "0", STR_PAD_LEFT);
	   $hex .= str_pad(dechex($rgb[2]), 2, "0", STR_PAD_LEFT);
	   return $hex; // returns the hex value including the number sign (#)
	}
	// this is for type
	public  function getModel($type=-1) {
		$return = array();
		if ($type!= -1 && $type!= "") {
			$path = dirname(__FILE__) . '/../config/';
			if (is_dir($path)) {	
				$file = "device".$type.".json";
				try {
					$content = file_get_contents($path.$file);
					if (is_json($content)) {
						$info = json_decode($content, true);
											
					 // ob_start();
					 // var_dump($info);
					 // $res = ob_get_clean();
					 // log::add('wifilightV2','debug','Vreturn state:'.$res );
					
						// get important informations in the config
						$type = $info["type"];
						$subtype = $info["subtype"];
						$name = $info["name"];
						$return['canal']= $info["canal"];
						if (isset($info["Mincanal"])) {
							$return['Mincanal']= $info["Mincanal"];
						}
						else {
							$return['Mincanal']=0;
						}
						if (isset($info["mac"])) {
							$return['macad']= $info["mac"];
						}
						else {
							$return['macad']="";
						}
						if (isset($info["identifiant"])) {
							$return['identifiant']= $info["identifiant"];
						}
						else {
							$return['identifiant']="";
						}
						if (isset($info["nbLeds"])) {
							$return['nbLeds']= $info["nbLeds"];
						}
						else {
							$return['nbLeds']="";
						}
						if (isset($info["colorOrder"])) {
							$return['colorOrder']= $info["colorOrder"];
						}
						else {
							$return['colorOrder']="";
						}						
                      	//log::add ('wifilightV2','debug',"identifiant return getmodel:".$return['identifiant']);
					}
				} 
				catch (Exception $e) {
				}					
			}
		}
		return $return;
	}
	// this is for type
	public function getDevices($typeN = '') {
		$return = array();	
		if (isset($this)) {
			try {
				$typedev = $this->getConfiguration('type');
			}
			catch (Exception $e) {
				log::add ('wifilightV2','debug',"io :".$e->getMessage());
			}
			$typedev = substr($typedev,2,2);
		}
		else $typedev ="00";
		$path = dirname(__FILE__) . '/../config/';
		if (is_dir($path)) {	
			$files = scandir($path);
			foreach ($files as $file) {
				$extension = pathinfo($file, PATHINFO_EXTENSION);
				$filename = pathinfo($file,  PATHINFO_FILENAME);
				$searchBox = substr( $filename,6,2)."00";
				$searchDevice = substr($filename,8,2);
				if (($typeN == '' &&  $searchDevice == "00")  || ( $typeN != '' && $searchBox == $typeN && $searchDevice != "00")) {	
					//log::add ('wifilightV2','debug',"searchDevice:".$searchDevice);							
					if ($extension=="json" && substr($filename,0,6)=="device" ) {
						try {
							$content = file_get_contents($path.$file);
							if (is_json($content)) {
								$info = json_decode($content, true);
								$type = $info["type"];
								$subtype = $info["subtype"];
								$name = $info["name"];
								// device channel or model channel
								
								if (isset($info["canal"])) {
									$canal = $info["canal"];
								}
								else {
									$canal = 1;
								}
								
								if (isset($info["instructions"])) {
									$instructions = $info["instructions"];
								}
								else {
									$instructions = "";
								}
								if (isset($info["image"])) {
									$image = $info["image"];
								}
								else {
									$image = "";
								}
							
								if (isset($info["mac"])) {	
									$macad = $info["mac"];
								}
								else {
									$macad = "";
								}
								if (isset($info["identifiant"])) {	
									$identifiant = $info["identifiant"];
								}
								else {
									$identifiant = "";
								}
								if (isset($info["colorOrder"])) {	
									$colorOrder = $info["colorOrder"];
								}
								else {
									$colorOrder = "";
								}
								if (isset($info["nbLeds"])) {	
									$nbLeds = $info["nbLeds"];
								}
								else {
									$nbLeds = "";
								}
								if (isset($info["incremV"])) {	
									$incremV = $info["incremV"];
									if ($incremV <1)
											$incremV = 10;
								}
								else {
									$incremV = "";
								}
								
								
								if ($searchDevice == $typedev) $selected = 1;
								else $selected = 0;
								// tout est utile maintenant on prend les données dans le subtype
								$return[sprintf("%04d", $type*100 + $subtype)] = array('image' => $image, 'name' => $name, 'subtype' => $subtype,'selected' => $selected,'instructions' => $instructions,'canal' => $canal,'macad' => $macad,'identifiant' => $identifiant,'colorOrder' => $colorOrder,'nbLeds' => $nbLeds,'incremV' => $incremV);
							}
						} 
						catch (Exception $e) {
						}
					}	
				}	
			}
		}
		//log::add ('wifilightV2','debug',"GetDevices End ");
		return $return;
	}
	public function FindDevice($type) {
		$return = array();
		$path = dirname(__FILE__) . '/../config/';
		if (is_dir($path)) {		
			$file = 'device'.ltrim(rtrim($type)).".json";
			if (file_exists($path . $file)) {
				try {
					$content = file_get_contents($path . $file);
					if (is_json($content)) {
						// log::add ('wifilightV2','debug',"Json OK");
						$return['device'] = json_decode($content, true);
					}
				} 
				catch (Exception $e) {
				}
			}
			$file = 'boutons.json';
			if (file_exists($path . $file)) {
				try {
					$content = file_get_contents($path . $file);
					if (is_json($content)) {
						// log::add ('wifilightV2','debug',"Json button OK");
						$return['buttons'] = json_decode($content, true);
					}
				} 
				catch (Exception $e) {
				}
			}
		}
		// log::add ('wifilightV2','debug',"End find device");
		return $return;
	}	
	public function preInsert()
	{
		$this->setIsEnable(1);
		$this->setIsVisible(1);
		$this->setCategory('light', 1);
	}
    public function postSave() {
		// log::add ('wifilightV2','debug',"******************** Start create **************");
		$typeN = $this->getConfiguration('typeN');
		if ($typeN !== null && $typeN != "") {
			$type = $this->getConfiguration('type');
			$class = $this->getConfiguration('WLClass');
			if (filter_var($this->getConfiguration('addr'), FILTER_VALIDATE_IP) === false) {
				throw new Exception(__('Le format de l\'adresse IP n\'est pas valide', __FILE__));
			}	
			$return = self::FindDevice($typeN);
			$device = $return['device'];
			$save = false;
			if (!isset($device['commands'])) {
				// no command -> has a child
				$type = $this->getConfiguration('subtype');			
				$return = self::FindDevice($type);
				$device = $return['device'];	
				ob_start();
				var_dump($device);
				$res = ob_get_clean();
			}
			else {	
				$type = $this->getConfiguration('typeN');
			}
			$NbCanal =  $device['canal'];
			if (isset($device['Mincanal']) && $device['Mincanal']!="")
				$MinCanal =  $device['Mincanal'];
			else
				$MinCanal = 0;
			$class = $this->getConfiguration('WLClass');
			$macad = $this->getConfiguration('macad');
			//log::add('wifilightV2','debug','MinCanal:'.$MinCanal);
			if (isset($device['class'])) {
				$canal = $this->getConfiguration('canal');
				$saveq = false;
				if ( $this->getConfiguration('monoSlider') === false || $this->getConfiguration('monoSlider') == "")
					$saveq = true;
				if ($class != $device['class'])
					$saveq = true;
				if ($NbCanal >1 && (($canal >$NbCanal) || ($canal <$MinCanal) || (strval($canal)==""))) {
					throw new Exception(__('Le canal peut varier seulement de '.$MinCanal.' à '.$NbCanal, __FILE__));
				}

				if ($this->getConfiguration('group') >100) {
					$this->setConfiguration('group',100);
					$saveq = true;
				}
				if ($this->getConfiguration('group') <0) {
					$this->setConfiguration('group',0);
					$saveq = true;
				}	
				if (strval($this->getConfiguration('group')) == "") {
					$this->setConfiguration('group',0);
					$saveq = true;
				}
				if (strval($this->getConfiguration('incremV')) == "") {
					$this->setConfiguration('incremV',10);
					$saveq = true;
				}
				if ($this->getConfiguration('incremV') >25) {
					$this->setConfiguration('incremV',25);
					$saveq = true;
				}				
				if ($this->getConfiguration('incremV') <1 ) {
					$this->setConfiguration('incremV',1);
					$saveq = true;
				}
				if (strval($this->getConfiguration('incremV')) == "") {
					$this->setConfiguration('incremV',10);
					$saveq = true;
				}
				if ($this->getConfiguration('delai') >100) {
					$this->setConfiguration('delai',100);
					$saveq = true;
				}				
				if ($this->getConfiguration('delai') <0 ) {
					$this->setConfiguration('delai',0);
					$saveq = true;
				}
				if (strval($this->getConfiguration('delai')) == "") {
					$this->setConfiguration('delai',0);
					$saveq = true;
				}
				if ( $this->getConfiguration('repetitions') == "") {
					$this->setConfiguration('repetitions',1);
					$saveq = true;
				}	
				if ( $this->getConfiguration('repetitions') < 1) {
					$this->setConfiguration('repetitions',1);
					$saveq = true;
				}
				if ( $this->getConfiguration('repetitions') > 6 ) {
					$this->setConfiguration('repetitions',6);
					$saveq = true;
				}
				if ( $this->getConfiguration('nbLeds') == "") {
					$this->setConfiguration('nbLeds',60);
					$saveq = true;
				}	
				if ( $this->getConfiguration('nbLeds') < 1) {
					$this->setConfiguration('nbLeds',1);
					$saveq = true;
				}
				if ( $this->getConfiguration('nbLeds') > 65535 ) {
					$this->setConfiguration('nbLeds',65535);
					$saveq = true;
				}
				if ($this->getConfiguration('colorOrder') == '' ) {
					$colorOrder = 0;
					$this->setConfiguration('colorOrder',0);
				} else	
					$colorOrder = $this->getConfiguration('colorOrder');
				

				$oldType = $this->getConfiguration('type');

				if ($saveq === true || !isset($class) || $class === null || $class == "" || $type != $oldType  ) {
					$class = $device['class'];
					$this->setConfiguration('type',$type);
					$this->setConfiguration('WLClass',$class);
					$this->setConfiguration('NbChan',$NbCanal);
					$this->setConfiguration('monoSlider',$device['monoSlider']);
					$this->setConfiguration('icon',"icon".$type.".png");
					$this->setIsEnable(1);
					$this->setIsVisible(1);
					$this->save();
				}	
				$classW2 = "W2_".$class;
				$myLight = new $classW2($this->getConfiguration('addr'),$this->getConfiguration('delai'),$this->getConfiguration('repetitions'),$this->getConfiguration('incremV'),$this->getConfiguration('macad'),$this->getConfiguration('identifiant'),$this->getConfiguration('nbLeds'),$this->getConfiguration('colorOrder'));
				if (method_exists($myLight,'config')){
					$myLight->config();
				}

				if (isset ($return['buttons'])){
					$buttons=$return['buttons'];
					// available commands for this device
					$Com=$device['commands'];
					//create standard control if available for this device
					$cmd_order = 0;
					$link_cmds = array();
					$link_actions = array();
					if (strval($this->getConfiguration('controles')) == '' )
						$level = 0;
					else	
						$level = $this->getConfiguration('controles');
					$level = $level +1;
					foreach ($buttons['commands'] as $command) {
						if ($command['configuration']['level'] <= $level) {						
							if (in_array($command['logicalId'] , $Com )) {
								$cmd = null;
								foreach ($this->getCmd() as $liste_cmd) {
									if (isset($command['logicalId']) && $liste_cmd->getLogicalId() == $command['logicalId']) {
										$cmd = $liste_cmd;
										break;
									}
								}
								try {
									if ($cmd == null || !is_object($cmd)) {
										$cmd = new wifilightV2Cmd();
										$cmd->setOrder($cmd_order);
										$cmd->setEqLogic_id($this->getId());
										utils::a2o($cmd, $command);	
										if ($command['logicalId'] == 'Color' || $command['logicalId'] == 'ColorAmb') {
											$cmd->setTemplate('dashboard','Color_default_no_off');
										}
									} else {
										$command['name'] = $cmd->getName();
										if (isset($command['display'])) {
											unset($command['display']);
										}
										$histo = $cmd->getIsHistorized();
										$visible = $cmd->getIsVisible();
										utils::a2o($cmd, $command);
										$cmd->setIsHistorized($histo);
										$cmd->setIsVisible($visible);									
									}
									$cmd->setConfiguration('request', $command['logicalId']);
									if ($command['logicalId'] == 'DiscoProg') {
										if (isset($device['nbDisco'])) {
											$cmd->setConfiguration("maxValue",$device['nbDisco']);
										}
									}
									$cmd->save();
									if (isset($command['configuration']) && isset($command['configuration']['updateCmdId'])) {
										$link_actions[$cmd->getId()] = $command['configuration']['updateCmdId'];
									}
									$cmd_order++;
								} 
								catch (Exception $exc) {

								}
							}
						}				
					}
				}
				if (count($link_actions) > 0) {
					foreach ($this->getCmd() as $eqLogic_cmd) {
						foreach ($link_actions as $cmd_id => $link_cmd) {
							if ($link_actions[$cmd_id]== $eqLogic_cmd->getLogicalId()) {
								$cmd = cmd::byId($cmd_id);
								if (is_object($cmd)) {
									$cmd->setValue($eqLogic_cmd->getId());
									$cmd->save();
								}
							}
						}
					}
				}
				
				// boutons disco si existent
				$cmd_order = 100;
				if (isset($device['disco'])) {
					$Disco=$device['disco'];
				}
				else {
					$Disco = array();
				}
				if ( $level == 3) {
					foreach ($Disco as $id => $disco) {
						$Cmd = $this->getCmd(null, $disco['logicalId']);
						if (!is_object($Cmd)) {
							$Cmd = new wifilightV2Cmd();
						}
						//log::add('wifilightV2','debug','logical:'.$disco['logicalId'].'  name:'.$disco['name']);
						$Cmd->setOrder($cmd_order);
						$Cmd->setName($disco['name']);
						$Cmd->setLogicalId($disco['logicalId']);
						$Cmd->setConfiguration('request', $disco['logicalId']);
						$Cmd->setEqLogic_id($this->getId());
						$Cmd->setConfiguration('parameters', $id + 500);
						$Cmd->setType('action');
						$Cmd->setSubType('other');	
						$Cmd->save();
						$cmd_order++;
					}
				}
				$cmd_order = 200;
				// boutons couleurs si existent
				if ($level == 3) {
					$Comm=$device['commands'];
					$col_Array=array(
						'randomColor' => 'Couleur aléatoire',
						'blueColor' => 'Bleu',
						'violetColor' => 'Violet',
						'babyblueColor' => 'Bleu ciel',
						'aquaColor' => 'Aquatique',
						'mintColor' => 'Menthe',
						'springGreenColor' => 'Vert printemps',
						'greenColor' => 'Vert',
						'limeGreenColor' => 'Vert citron',
						'yellowColor' => 'Jaune',
						'yellowOrangeColor' => 'Jaune orangé',
						'orangeColor' => 'Orange',
						'redColor' => 'Rouge',
						'pinkColor' => 'Rose',
						'fuchsiaColor' => 'Fuchia',
						'lilacColor' => 'Lilas',
						'lavendarColor' => 'Lavande'
					);
					if (in_array('Color',$Comm)) {
						$para=400;
						foreach ($col_Array as $Id => $color) {
							$Cmd = $this->getCmd(null, $Id);
							if (!is_object($Cmd)) {
								$Cmd = new wifilightV2Cmd();
							}
							//log::add('wifilightV2','debug','logical:'.$Id.'  name:'.$color);
							$Cmd->setName($color);
							$Cmd->setOrder($cmd_order);
							$Cmd->setLogicalId($Id);
							$Cmd->setEqLogic_id($this->getId());
							$Cmd->setConfiguration('parameters', $para);
							$Cmd->setConfiguration('request', $Id);
							$Cmd->setType('action');
							$Cmd->setSubType('other');	
							$Cmd->save();
							$para++;
							$cmd_order++;
						}
					}
					if (in_array('ColorAmb',$Comm)) {
						$para=450;
						foreach ($col_Array as $Id => $color) {
							$Cmd = $this->getCmd(null, $Id);
							if (!is_object($Cmd)) {
								$Cmd = new wifilightV2Cmd();
							}
							//log::add('wifilightV2','debug','logical:'.$Id.'  name:'.$color);
							$Cmd->setName($color);
							$Cmd->setOrder($cmd_order);
							$Cmd->setLogicalId($Id);
							$Cmd->setEqLogic_id($this->getId());
							$Cmd->setConfiguration('parameters', $para);
							$Cmd->setConfiguration('request', $Id);
							$Cmd->setType('action');
							$Cmd->setSubType('other');	
							$Cmd->save();
							$para++;
							$cmd_order++;
						}
					}
					
				}
				// boutons customs si existent
				if ($level == 3) {
					$cmd_order = 300;
					if (isset($device['Custom'])) {
						$Customs=$device['Custom'];
					}
					else
						$Customs=array();
					foreach ($Customs as $id => $Custom) {
						$Cmd = $this->getCmd(null, $Custom['logicalId']);
						if (!is_object($Cmd)) {
							$Cmd = new wifilightV2Cmd();
						}
						$Cmd->setOrder($cmd_order);
						$Cmd->setName($Custom['name']);
						$Cmd->setLogicalId($Custom['logicalId']);
						$Cmd->setConfiguration('request', $Custom['logicalId']);
						$Cmd->setEqLogic_id($this->getId());
						$Cmd->setConfiguration('parameters',$Custom['parameters']);
						$Cmd->setType('action');
						$Cmd->setSubType('other');	
						$Cmd->save();
						$cmd_order++;
					}
				}
				// new connected state
				$Cmd = $this->getCmd(null, 'ConnectedGet');
				if (!is_object($Cmd)) {
					$Cmd = new wifilightV2Cmd();
				}
				$Cmd->setName('Connecté');
				$Cmd->setOrder(500);
				
				$Cmd->setLogicalId('ConnectedGet');
				$Cmd->setEqLogic_id($this->getId());
				$Cmd->setConfiguration('level', 1);
				
				$Cmd->setConfiguration('request','ConnectedGet');
				$Cmd->setType('info');
				$Cmd->setSubType('numeric');
				$Cmd->setDisplay("generic_type" , "DONT");
				
				$Cmd->setIsVisible(0);	
				$Cmd->setIsHistorized(0);
				$Cmd->setUnite("");				
				$Cmd->save();
			}
		}
    }
}

class wifilightV2Cmd extends cmd {

	public function preSave() {
		

    }
    public function execute($_options = array()) {
	
		$wifilight = $this->getEqLogic();
		$IdEq=$wifilight->getId();
		$parameters = $this->getConfiguration('parameters');
        if (isset($_options['slider']))
			$options= $_options['slider'];
        else
            $options=0;
     
        if (isset($_options['color']))
			$color= $_options['color'];
        else
            $color='#000000';
		//log::add("wifilightV2","debug","param:".$parameters);
		$Id = $this->getLogicalId();
		//log::add("wifilightV2","debug","Id:".$Id."<");
		$group = $wifilight->getConfiguration('group');
		
		foreach (eqLogic::byType('wifilightV2') as $eqLogic){
			if (((($group!="") && ($group!=0)) || ($IdEq==$eqLogic->getId() )) && ($eqLogic->getConfiguration('group')== $group)) {
				$Cmd=$eqLogic->getCmd(null,$Id);
				if ($Cmd!=null) {
					$Cmd->UpdateCmd($parameters,$options,$color);
				}	
			}
		}
    }
	public function UpdateCmd($parameters,$options,$color) {
		$wifilight = $this->getEqLogic();
		$class = $wifilight->getConfiguration('WLClass');
		$monoSlider = $wifilight->getConfiguration('monoSlider');
		// $wifilight->setConfiguration('monoSlider',1);
		// $wifilight->save();
		// $monoSlider = $wifilight->getConfiguration('monoSlider');

		//log::add("wifilightV2","debug","incremV:".$incremV);
		
		$incremV = $wifilight->getConfiguration('incremV');
		if ($incremV===false){
			$incremV = 10;
			$wifilight->setConfiguration('incremV',$incremV);
			$wifilight->save();
		}
		$classW2 = "W2_".$class;
		$myLight = new $classW2($wifilight->getConfiguration('addr'),$wifilight->getConfiguration('delai'),$wifilight->getConfiguration('repetitions'),$incremV,$wifilight->getConfiguration('macad'),$wifilight->getConfiguration('identifiant'),$wifilight->getConfiguration('nbLeds'),$wifilight->getConfiguration('colorOrder'));
		//$parameters = $this->getConfiguration('parameters');
		$NbCanal = $wifilight->getConfiguration('NbChan');
		// log::add("wifilightV2","debug","canal:".$NbCanal);
		if ($NbCanal>1) $myLight->SetGroup($wifilight->getConfiguration('canal'));
		if ($this->type == 'action'){
			// Mi.Light V6 need to retreive 2 id's	
			if (method_exists($myLight,'getId')){
				// needed informations are associated to On cmd  ***** to verify that it works *****
				$Cmd = $wifilight->getCmd(null, 'On');
				if (isset($Cmd)) {
					$Time = $Cmd->getConfiguration('time');
					if (!isset($Time)) $Time =0; // first time
					$TimeNew = time();
					if ($TimeNew>$Time) {
						$Cmd->setConfiguration('time',$TimeNew); // timestamp mem					
						$Id = $myLight->getId();
						$myLight->setId($Id);
						//log::add('wifilightV2','debug','ID:'.$Id[1].":".$Id[2]);
						$Cmd->setConfiguration('Id1',$Id[1] ); // id1 mem
						$Cmd->setConfiguration('Id2',$Id[2] ); // id2 mem
						// log::add('wifilightV2','debug','ID:'.$Id[1].":".$Id[2]);
						$Cmd->save();
						//log::add('wifilightV2','debug','ID:'.$Id[1].":".$Id[2]);
					}
					else {
						$Id1 = $Cmd->getConfiguration('Id1'); 
						$Id2 = $Cmd->getConfiguration('Id2');
						//log::add('wifilightV2','debug','ID:'.$Id1.":".$Id2);
						$Ret=array(0x28,$Id1,$Id2);
						$myLight->setId($Ret);
					}
				}
			}
			switch ($this->subType) {
				case 'slider':
					$parameters = str_replace('#slider#', $options, $parameters);
					switch ($this->getLogicalId()) {						
						case 'White' :	
							// some devices need to send white and color at the same time
							if (method_exists($myLight,'OnBrightnessWhite')) {
								// maj type du curseur quand unique curseur pour plusieurs actions
								//log::add('wifilightV2', 'info', 'WmonoSlider:'.$monoSlider);
								if ($monoSlider == 1) {
									$Cmd = $wifilight->getCmd(null, 'On');
									//log::add('wifilightV2', 'info', 'Wcmd:'.$cmd);
									if (isset($Cmd)) {
										$Type = $Cmd->getConfiguration('Type'); // type mem	
										//log::add('wifilightV2', 'info', 'WType:'.$Type);											
										if ($Type == 'Col')	{								
											$Cmd->setConfiguration('ColMem',$parameters);
											$Cmd->save();
										}
										if ($Type == 'W1')	{								
											$Cmd->setConfiguration('W1Mem',$parameters);
											$Cmd->save();
										}
									}
								}
								
								$Cmd = $wifilight->getCmd(null, 'ColorGet');
								if ($Cmd !== false) {
									$Col = $Cmd->execCmd();
								}
								else 
									$Col='#000000';
								$myLight->OnBrightnessWhite($parameters,$Col,0);							
								$Cmd = $wifilight->getCmd(null, 'WhiteGet');
								$Cmd->event($parameters);
								$this->OnStatus($wifilight,$myLight);
								$Cmd->save();
							}
							break;
						case 'WhiteWarm' :	
						// some devices need to send white and color at the same time
							if (method_exists($myLight,'OnBrightnessWhite')) {
								$Cmd = $wifilight->getCmd(null, 'ColorGet');
								if ($Cmd !== false) {
									$Col = $Cmd->execCmd();
								}
								else $Col='#000000';
								$Cmd = $wifilight->getCmd(null, 'WhiteCoolGet');
								if ($Cmd !== false) {
									$White = $Cmd->execCmd();
								}
								else $White=0;									
								// log::add('wifilightV2','debug','white: '.$White);
								$myLight->OnBrightnessWhite($parameters,$Col,$White);						
								$Cmd = $wifilight->getCmd(null, 'WhiteWarmGet');
								$Cmd->event($parameters);
								$this->OnStatus($wifilight,$myLight);
								$Cmd->save();
							}
							break;	
						case 'WhiteCool' :	
							// some devices need to send white and color at the same time							
							if (method_exists($myLight,'OnBrightnessWhite2')) {
								$Cmd = $wifilight->getCmd(null, 'ColorGet');
								if ($Cmd !== false) {
									$Col = $Cmd->execCmd();
								}
								else 
									$Col='#000000';
								$Cmd = $wifilight->getCmd(null, 'WhiteWarmGet');
								if ($Cmd !== false) {
									$White = $Cmd->execCmd();
								}
								else 
									$White=0;	
								// log::add('wifilightV2','debug','white: '.$White);
								$myLight->OnBrightnessWhite2($parameters,$Col,$White);							
								$Cmd = $wifilight->getCmd(null, 'WhiteCoolGet');
								$Cmd->event($parameters);
								$this->OnStatus($wifilight,$myLight);
								$Cmd->save();
							}
							break;	
						case 'Intensity' :		// for colored led	
							// some devices do not have intensity as command
							if (method_exists($myLight,'OnBrightness')) {
								$Cmd = $wifilight->getCmd(null, 'WhiteWarmGet');
								$White1=0;
								$White2=0;
								if ($Cmd !== false){
									$White1 = $Cmd->execCmd();
									$Cmd = $wifilight->getCmd(null, 'WhiteCoolGet');
									if ($Cmd !== false) {
										$White2 = $Cmd->execCmd();
									}
								}
								else {
									$Cmd = $wifilight->getCmd(null, 'WhiteGet');
									if ($Cmd !== false) {
										$White1 = $Cmd->execCmd();
									}
								}	
								$Cmd = $wifilight->getCmd(null, 'ColorGet');
								$Colhex=$Cmd->execCmd();
								$myLight->OnBrightness($parameters,$Colhex,$White1,$White2);
								$Cmd = $wifilight->getCmd(null, 'IntensityGet');
								$Cmd->event($parameters);
								$Cmd->save();
								$this->OnStatus($wifilight,$myLight);
							}
							break;
						case 'moonLight' :
							if (method_exists($myLight,'OnMoon')) {							
								$myLight->OnMoon($parameters);							
								$this->OnStatus($wifilight,$myLight);
							}
							break;
						case 'DiscoProg' :
							if (method_exists($myLight,'OnDisco')) {	
								$NbMode= $this->getConfiguration("maxValue");
								if ($parameters > $NbMode) $parameters=$NbMode;
								if ($parameters <1) $parameters = 1;							
								// disco send speed and mode at the same time
								$Cmd= $wifilight->getCmd(null, 'DiscoSpeedGet'); 
								if ($Cmd !== false)	
									$speed=$Cmd->execCmd();
								else 
									$speed = 0;
								$myLight->OnDisco($parameters,$speed); // second parameter is used when state return fails
								$this->DiscoStatus($wifilight,$parameters);
							}
							break;					
						case 'DiscoSpeed' :
							if (method_exists($myLight,'DiscoSpeed')) {	
								if ($parameters >100) $parameters=100;
								if ($parameters <1) $parameters=1;
								// disco send speed and mode at the same time
								$Cmd = $wifilight->getCmd(null,'DiscoProgGet');
								if ($Cmd !== false)	
									$Prog=$Cmd->execCmd();
								else 
									$Prog = 0;
								$myLight->DiscoSpeed($parameters,$Prog);// second parameter is used when state return fails
								$Cmd = $wifilight->getCmd(null, 'DiscoSpeedGet');
								$Cmd->event($parameters);
								$Cmd->save();
							}
							break;	
						case 'Kelvin' :	
							if (method_exists($myLight,'OnKelvin')) {								
								$myLight->OnKelvin($parameters);		
								$Cmd = $wifilight->getCmd(null, 'KelvinGet');
								$Cmd->event($parameters);
								$Cmd->save();
								$this->OnStatus($wifilight,$myLight);
							}
							break;						
						case 'Saturation' :	
							if (method_exists($myLight,'OnSaturation')) {							
								$myLight->OnSaturation($parameters);
								$Cmd = $wifilight->getCmd(null, 'SaturationGet');
								$Cmd->event($parameters);
								$Cmd->save();								
								$this->OnStatus($wifilight,$myLight);
							}
							break;
						case 'IntensityAmb' :	
							if (method_exists($myLight,'OnBrightnessAmb')) {							
								$Colhex ="#000000";
								$Cmd = $wifilight->getCmd(null, 'ColorAmbGet');	
								if ($Cmd !== false )
									$Colhex=$Cmd->execCmd();
								$myLight->OnBrightnessAmb($parameters,$Colhex);
								$Cmd = $wifilight->getCmd(null, 'IntensityAmbGet');
								$Cmd->event($parameters);
								$Cmd->save();								
								$this->OnAmbStatus($wifilight,$myLight);
							}
							break;							
						case 'Timer' :	
							// only memorize timer
							$Cmd = $wifilight->getCmd(null, 'TimerGet');
							$Cmd->event($parameters);
							$Cmd->save();	
							break;	
					}
					break;
				case 'color':
					switch ($this->getLogicalId()) {
						case 'Color' :
							if (method_exists($myLight,'OnColor')) {
								// 1 seul curseur pour intensité couleur et intensité blanc
								//log::add('wifilightV2', 'info', 'CmonoSlider:'.$monoSlider);
								if ($monoSlider == 1) {
									$Cmd = $wifilight->getCmd(null, 'On');
									if (isset($Cmd)) {
										$Cmd->setConfiguration('Type','Col'); // type mem	
										$Cmd->save();
										$ColMem = $Cmd->getConfiguration('ColMem');
										//log::add('wifilightV2', 'info', 'CColMem:'.$ColMem);
										if (($ColMem===false) || ($ColMem == '' )) {	
											$ColMem = 100;
										}										
										$Cmd = $wifilight->getCmd(null, 'WhiteGet');
										$Cmd->event($ColMem); // maj curseur intensité
										$Cmd->save();
									}
								}
								
								$parameters = str_replace('#color#', $color, $parameters);
								$White1=0;
								$White2=0;
								$Bright=100;
								$Cmd = $wifilight->getCmd(null, 'WhiteWarmGet');
								if ($Cmd !== false){
									$White1 = $Cmd->execCmd();
									$Cmd = $wifilight->getCmd(null, 'WhiteCoolGet');
									if ($Cmd !== false) {
										$White2 = $Cmd->execCmd();
									}
								}
								else {
									$Cmd = $wifilight->getCmd(null, 'WhiteGet');
									if ($Cmd !== false) {
										$White1 = $Cmd->execCmd();
									}
								}								
								$Cmd = $wifilight->getCmd(null, 'IntensityGet');
								if ($Cmd !== false){
									$Bright = $Cmd->execCmd();
								}
								//log::add('wifilightV2', 'info', 'parm:'.$parameters);
								switch ($parameters) {
									case '#000000' :
										//$myLight->Off();
										//$this->OffStatus($wifilight,$myLight);
										break;
									case '#ffffff' :									
										$myLight->OnColor($parameters,$Bright,$White1,$White2);
										$this->OnStatus($wifilight,$myLight);
										break;
									default :										
										$myLight->OnColor($parameters,$Bright,$White1,$White2);
										$this->OnStatus($wifilight,$myLight);
										break;
								}	      
								$Cmd = $wifilight->getCmd(null, 'ColorGet');
								$Cmd->event($parameters);
								$Cmd->save();
							}
							break;
						case 'ColorAmb' :
							if (method_exists($myLight,'OnColorAmb')) {
								// 1 seul curseur pour intensité couleur et intensité blanc
								//log::add('wifilightV2', 'info', 'CmonoSlider:'.$monoSlider);									
								$parameters = str_replace('#color#', $color, $parameters);
								$White1=0;
								$White2=0;
								$Bright=100;
								$Cmd = $wifilight->getCmd(null, 'WhiteWarmAmbGet');
								if ($Cmd !== false){
									$White1 = $Cmd->execCmd();
									$Cmd = $wifilight->getCmd(null, 'WhiteCoolAmbGet');
									if ($Cmd !== false) {
										$White2 = $Cmd->execCmd();
									}
								}
								else {
									$Cmd = $wifilight->getCmd(null, 'WhiteAmbGet');
									if ($Cmd !== false) {
										$White1 = $Cmd->execCmd();
									}
								}								
								$Cmd = $wifilight->getCmd(null, 'IntensityAmbGet');
								if ($Cmd !== false){
									$Bright = $Cmd->execCmd();
								}
								switch ($parameters) {
									case '#000000' :
										//$myLight->OffBg();
										//$this->OffBgStatus($wifilight,$myLight;
										break;
									case '#ffffff' :									
										$myLight->OnColorAmb($parameters,$Bright,$White1,$White2);
										$this->OnAmbStatus($wifilight,$myLight);
										break;
									default :										
										$myLight->OnColorAmb($parameters,$Bright,$White1,$White2);
										$this->OnAmbStatus($wifilight,$myLight);
										break;
								}	      
								$Cmd = $wifilight->getCmd(null, 'ColorAmbGet');
								$Cmd->event($parameters);
								$Cmd->save();
							}
							break;
					}
					break;
	
					
				case 'other':
				//log::add('wifilightV2','debug',"param:".$parameters);
					switch($parameters) {
						case '200' : // RGBW on
							if (method_exists($myLight,'On')) {
								$myLight->On();
								$this->OnStatus($wifilight,$myLight);
							};	
							break;
						case '201' : // RGBW off
							if (method_exists($myLight,'Off')) {
								$myLight->Off();
								$this->OffStatus($wifilight,$myLight);
							}
							break;
						case '202' : // RGBW Max
							if (method_exists($myLight,'OnMax')) {
								$myLight->OnMax();						
								$data = 100;
								$Cmd = $wifilight->getCmd(null, 'WhiteGet');
								if ($Cmd !== false) {									
									$Cmd->event($data);
									$Cmd->save();
								}
								else {
									$Cmd = $wifilight->getCmd(null, 'WhiteWarmGet');
									if ($Cmd !== false) {									
										$Cmd->event($data);
										$Cmd->save();
									}	
								}
								$this->OnStatus($wifilight,$myLight);
							}
							break;
						case '203' : //RGBW Min
							if (method_exists($myLight,'OnMin')) {
								$myLight->OnMin();						
								$data =5;
								$Cmd = $wifilight->getCmd(null, 'WhiteGet');
								if ($Cmd !== false) {									
									$Cmd->event($data);
									$Cmd->save();
								}
								else {
									$Cmd = $wifilight->getCmd(null, 'WhiteWarmGet');
									if ($Cmd !== false) {									
										$Cmd->event($data);
										$Cmd->save();
									}	
								}
								$this->OnStatus($wifilight,$myLight);
							}
							break;						
						case '204' : // RGBW Max middle	
							if (method_exists($myLight,'OnMid')) {							
								$myLight->OnMid();					
								$data = 50;
								$Cmd = $wifilight->getCmd(null, 'WhiteGet');
								if ($Cmd !== false) {									
									$Cmd->event($data);
									$Cmd->save();
								}
								else {
									$Cmd = $wifilight->getCmd(null, 'WhiteWarmGet');
									if ($Cmd !== false) {									
										$Cmd->event($data);
										$Cmd->save();
									}	
								}							
								$this->OnStatus($wifilight,$myLight);
							}
							break;
						case '205' : // Night	
							if (method_exists($myLight,'OnNight')) {
								$myLight->OnNight();
								$Cmd = $wifilight->getCmd(null, 'SwNightLightGet'); // state return
								if ($Cmd !== false) {									
									$Cmd->event(1);
									$Cmd->save();
								}
								else {
									$Cmd = $wifilight->getCmd(null, 'WhiteGet');
									if ($Cmd !== false) {									
										$Cmd->event(1);
										$Cmd->save();
									}
									else {
										$Cmd = $wifilight->getCmd(null, 'WhiteWarmGet');
										if ($Cmd !== false) {									
											$Cmd->event(1);
											$Cmd->save();
										}	
									}
								}
								$this->OnStatus($wifilight,$myLight);
							}
							break;
						case '206' : // go to White	
							if (method_exists($myLight,'OnWhite')) {							
								$data =$myLight->OnWhite();
								//log::add('wifilightV2', 'debug', '2WmonoSlider:'.$monoSlider);
								if ($monoSlider == 1) {
									$Cmd = $wifilight->getCmd(null, 'On');
									if (isset($Cmd)) {
										$Cmd->setConfiguration('Type','W1'); // type mem	
										$Cmd->save();	
										$W1Mem = $Cmd->getConfiguration('W1Mem');
										//log::add('wifilightV2', 'debug', '2WMem:'.$W1Mem);
										if (($W1Mem === false) || ($W1Mem == '' )) {	
											$W1Mem = 100;
										}											
										$Cmd = $wifilight->getCmd(null, 'WhiteGet');
										$Cmd->event($W1Mem); // maj curseur intensité
										$Cmd->save();
									}
								}										
								$this->OnStatus($wifilight,$myLight);
							}
							break;
						case '207' : // go to White	2  for strips with 2 channels (no CTT slider)
							if (method_exists($myLight,'OnWhite2')) {							
								$data =$myLight->OnWhite2();
								//log::add('wifilightV2', 'debug', '2WmonoSlider:'.$monoSlider);
								$this->OnStatus($wifilight,$myLight);
							}
							break;

						case '208' : // Disco Min Speed
							if (method_exists($myLight,'DiscoMin')) {	
								$Cmd = $wifilight->getCmd(null,'DiscoProgGet');
								$Prog=$Cmd->execCmd();							
								$myLight->DiscoMin($Prog);
								$this->DiscoStatusSpeed($wifilight,0);
							}
							break;
						case '209' : //Disco Max Speed
							if (method_exists($myLight,'DiscoMax')) {
								$Cmd = $wifilight->getCmd(null,'DiscoProgGet');
								$Prog=$Cmd->execCmd();							
								$myLight->DiscoMax($Prog);
								$this->DiscoStatusSpeed($wifilight,100);
							}
							break;
						case '210' : //Disco Middle Speed
							if (method_exists($myLight,'DiscoMid')) {
								$Cmd = $wifilight->getCmd(null,'DiscoProgGet');
								$Prog=$Cmd->execCmd();							
								$myLight->DiscoMid($Prog);
								$this->DiscoStatusSpeed($wifilight,50);
							}
							break;
						case '211' : //Disco Faster	
							if (method_exists($myLight,'DiscoFaster')) {
								$Cmd = $wifilight->getCmd(null,'DiscoSpeedGet');
								$Speed=$Cmd->execCmd();
								$Speed=$Speed+10;
								if ($Speed >100) $Speed=100;
								if ($Speed <1) $Speed=1;					
								$myLight->DiscoFaster();
								$Cmd = $wifilight->getCmd(null, 'DiscoSpeedGet');
								$Cmd->event($Speed);
								$Cmd->save();
								$this->DiscoStatusSpeed($wifilight,$Speed);
							}
							break;
						case '212' : //Disco Slower
							if (method_exists($myLight,'DiscoSlower')) {
								$Cmd = $wifilight->getCmd(null,'DiscoSpeedGet');
								$Speed=$Cmd->execCmd();
								//$Speed=$Cmd->getValue();
								$Speed=$Speed-10;
								if ($Speed >100) $Speed=100;
								if ($Speed <1) $Speed=1;
								$myLight->DiscoSlower();
								$Cmd = $wifilight->getCmd(null, 'DiscoSpeedGet');
								$Cmd->event($Speed);
								$Cmd->save();
								$this->DiscoStatusSpeed($wifilight,$Speed);
							}
							break;
						case '213' : // Pause disco
							if (method_exists($myLight,'Pause')) {
								$myLight->Pause();
								// update state
								$Cmd = $wifilight->getCmd(null,'SwPlayRunGet');	
								$Cmd->event(0);
								$Cmd->save();
							}
							break;
						case '214' : // Play disco
							if (method_exists($myLight,'Play')) {
								$myLight->Play();
								// update state
								$Cmd = $wifilight->getCmd(null,'SwPlayRunGet');	
								$Cmd->event(1);
								$Cmd->save();
							}
						case '215' : // OnColor
							if (method_exists($myLight,'OnColor')) {
								// 1 seul curseur pour intensité couleur et intensité blanc
								//log::add('wifilightV2', 'info', 'CmonoSlider:'.$monoSlider);
								if ($monoSlider == 1) {
									$Cmd = $wifilight->getCmd(null, 'On');
									if (isset($Cmd)) {
										$Cmd->setConfiguration('Type','Col'); // type mem	
										$Cmd->save();
										$ColMem = $Cmd->getConfiguration('ColMem');
										//log::add('wifilightV2', 'info', 'CColMem:'.$ColMem);
										if (($ColMem===false) || ($ColMem == '' )) {	
											$ColMem = 100;
										}										
										$Cmd = $wifilight->getCmd(null, 'WhiteGet');
										$Cmd->event($ColMem); // maj curseur intensité
										$Cmd->save();
									}
									$Cmd = $wifilight->getCmd(null, 'ColorGet');
									if ($Cmd !== false) {
										$parameters = $Cmd->execCmd();
									}
									else 
										$parameters='#ff0000';
									$myLight->OnColor($parameters,$ColMem);
									$this->OnStatus($wifilight,$myLight);  
								}	  
							}
							break;
						case '250' : //white warm
							if (method_exists($myLight,'OnWarm')) {
								$myLight->OnWarm();
								$Cmd = $wifilight->getCmd(null,'KelvinGet');	
								$Kelvin=1;
								$Cmd->event($Kelvin);
								$Cmd->save();
								$this->OnStatus($wifilight,$myLight);	
							}								
							break;
						case '251' : //white cool
							if (method_exists($myLight,'OnCool')) {
								$myLight->OnCool();
								$Cmd = $wifilight->getCmd(null,'KelvinGet');	
								$Kelvin=100;
								$Cmd->event($Kelvin);
								$Cmd->save();
								$this->OnStatus($wifilight,$myLight);
							}								
							break;
						case '252' : //white middle warm
							if (method_exists($myLight,'OnLukeWarm')) {
								$myLight->OnLukeWarm();
								$Cmd = $wifilight->getCmd(null,'KelvinGet');	
								$Kelvin=50;
								$Cmd->event($Kelvin);
								$Cmd->save();
								$this->OnStatus($wifilight,$myLight);
							}								
							break;
						case '253' : //white temp up
							if (method_exists($myLight,'KelvinIncrease')) {
								$Cmd = $wifilight->getCmd(null,'KelvinGet');
								$Kelvin=$Cmd->execCmd();							
								$Kelvinr = $myLight->KelvinIncrease($Kelvin);
								if ($Kelvinr !== false) { // case of state return
									$Cmd->setConfiguration('parameters',$Kelvinr);
									$Cmd->event($Kelvinr);
									$Cmd->save();
								}
								$this->OnStatus($wifilight,$myLight);	
							}								
							break;
						case '254' : //white temp down
							if (method_exists($myLight,'KelvinDecrease')) {
								$Cmd = $wifilight->getCmd(null,'KelvinGet');
								$Kelvin=$Cmd->execCmd();							
								$Kelvinr = $myLight->KelvinDecrease($Kelvin);
								if ($Kelvinr !== false) { // case of state return
									$Cmd->setConfiguration('parameters',$Kelvinr);
									$Cmd->event($Kelvinr);
									$Cmd->save();
								}
								$this->OnStatus($wifilight,$myLight);
							}
							break;
						case '255' : //white brightness up or color brightness
							if (method_exists($myLight,'BrightnessIncrease')) {
								$Cmd2 = $wifilight->getCmd(null, 'ColorGet');
								if ($Cmd2 !== false) {
									$Col = $Cmd2->execCmd();
								}
								else 
									$Col='#000000';
								$Cmd = $wifilight->getCmd(null, 'IntensityGet');
								if ($Cmd === false)
									$Cmd = $wifilight->getCmd(null,'WhiteGet');
								$Br=$Cmd->execCmd();	
								$Brr= $myLight->BrightnessIncrease($Br,$Col);
								if ($Brr !== false) { // case of state return
									$Cmd->setConfiguration('parameters',$Brr);
									$Cmd->event($Brr);
									$Cmd->save();
								}
								$this->OnStatus($wifilight,$myLight);
							}
							break;
						case '256' : //white brightness down or color brightness
							if (method_exists($myLight,'BrightnessDecrease')) {
								$Cmd2 = $wifilight->getCmd(null, 'ColorGet');
								if ($Cmd2 !== false) {
									$Col = $Cmd2->execCmd();
								}
								else 
									$Col='#000000';
								$Cmd = $wifilight->getCmd(null, 'IntensityGet');
								if ($Cmd === false)
									$Cmd = $wifilight->getCmd(null,'WhiteGet');
								$Br=$Cmd->execCmd();	
								$Brr=$myLight->BrightnessDecrease($Br,$Col);
								if ($Brr !== false) { // case of state return
									$Cmd->setConfiguration('parameters',$Brr);
									$Cmd->event($Brr);
									$Cmd->save();
								}
								$this->OnStatus($wifilight,$myLight);
							}
							break;
						case '257' : //white1 brightness up
							if (method_exists($myLight,'BrightnessW1Increase')) {
								$Cmd = $wifilight->getCmd(null,'WhiteWarmGet');
								$Br=$Cmd->execCmd();	
								$Brr= $myLight->BrightnessW1Increase($Br);
								if ($Brr !== false) { // case of state return
									$Cmd->setConfiguration('parameters',$Brr);
									$Cmd->event($Brr);
									$Cmd->save();
								}
								$this->OnStatus($wifilight,$myLight);
							}
							break;
						case '258' : //white1 brightness down
							if (method_exists($myLight,'BrightnessW1Decrease')) {
								$Cmd = $wifilight->getCmd(null,'WhiteWarmGet');
								$Br=$Cmd->execCmd();	
								$Brr=$myLight->BrightnessW1Decrease($Br);
								if ($Brr !== false) { // case of state return
									$Cmd->setConfiguration('parameters',$Brr);
									$Cmd->event($Brr);
									$Cmd->save();
								}
								$this->OnStatus($wifilight,$myLight);
							}
							break;
						case '259' : //white2 brightness up
							if (method_exists($myLight,'BrightnessW2Increase')) {
								$Cmd = $wifilight->getCmd(null,'WhiteCoolGet');
								$Br=$Cmd->execCmd();	
								$Brr= $myLight->BrightnessW2Increase($Br);
								if ($Brr !== false) { // case of state return
									$Cmd->setConfiguration('parameters',$Brr);
									$Cmd->event($Brr);
									$Cmd->save();
								}
								$this->OnStatus($wifilight,$myLight);
							}
							break;
						case '260' : //white2 brightness down
							if (method_exists($myLight,'BrightnessW2Decrease')) {
								$Cmd = $wifilight->getCmd(null,'WhiteCoolGet');
								$Br=$Cmd->execCmd();	
								$Brr=$myLight->BrightnessW2Decrease($Br);
								if ($Brr !== false) { // case of state return
									$Cmd->setConfiguration('parameters',$Brr);
									$Cmd->event($Brr);
									$Cmd->save();
								}
								$this->OnStatus($wifilight,$myLight);
							}
							break;

						case '262' : //Random Color
							if (method_exists($myLight,'OnColor')) {	
								$White1=0;
								$White2=0;
								$Bright=100;
								$Cmd = $wifilight->getCmd(null, 'WhiteWarmGet');
								if ($Cmd !== false){
									$White1 = $Cmd->execCmd();
									$Cmd = $wifilight->getCmd(null, 'WhiteCoolGet');
									if ($Cmd !== false) {
										$White2 = $Cmd->execCmd();
									}
								}
								else {
									$Cmd = $wifilight->getCmd(null, 'WhiteGet');
									if ($Cmd !== false) {
										$White1 = $Cmd->execCmd();
									}
								}	
								$Cmd = $wifilight->getCmd(null, 'IntensityGet');
								if ($Cmd !== false){
									$Bright = $Cmd->execCmd();
								}
								$Col[0] = (int)mt_rand(0,0xFF); 
								$Col[1] = (int)mt_rand(0,0xFF);
								$Col[2] = (int)mt_rand(0,0xFF);
								$hex = wifilightV2::rgb2hex($Col);								
								$myLight->OnColor($hex,$Bright,$White1,$White2);
								$Cmd = $wifilight->getCmd(null, 'ColorGet');
								$Cmd->event($hex);
								$Cmd->save();
								$this->OnStatus($wifilight,$myLight);
							}
							break;
						case '265' : //ambiance intensity increase
							if (method_exists($myLight,'BrightnessIncreaseAmb')) {
								$Cmd = $wifilight->getCmd(null,'IntensityAmbGet');
								$Br=$Cmd->execCmd();	
								$Brr= $myLight->BrightnessIncreaseAmb($Br);
								if ($Brr !== false) { // case of state return
									$Cmd->setConfiguration('parameters',$Brr);
									$Cmd->event($Brr);
									$Cmd->save();
								}
								$this->OnStatus($wifilight,$myLight);
							}
							break;
						case '266' : //white brightness down or color brightness
							if (method_exists($myLight,'BrightnessDecreaseAmb')) {
								$Cmd = $wifilight->getCmd(null,'IntensityAmbGet');
								$Br=$Cmd->execCmd();	
								$Brr=$myLight->BrightnessDecreaseAmb($Br);
								if ($Brr !== false) { // case of state return
									$Cmd->setConfiguration('parameters',$Brr);
									$Cmd->event($Brr);
									$Cmd->save();
								}
								$this->OnStatus($wifilight,$myLight);
							}
							break;
						case '300' : //CCT Auto
							if (method_exists($myLight,'EnableAC')) {
								$myLight->EnableAC();
								$Cmd = $wifilight->getCmd(null,'SwCCTAutoGet');
								$Cmd->event("1");
								$Cmd->save();
								$this->OnStatus($wifilight,$myLight);
							}
							break;
						case '301' : //CCT Auto
							if (method_exists($myLight,'DisableAC')) {
								$myLight->DisableAC();
								$Cmd = $wifilight->getCmd(null,'SwCCTAutoGet');	
								$Cmd->event("0");
								$Cmd->save();
								$this->OnStatus($wifilight,$myLight);
							}
							break;
						case '302' : //Eye Care On
							if (method_exists($myLight,'EyeCare')) {
								$myLight->EyeCare("on");
								$Cmd = $wifilight->getCmd(null,'SwCareGet');	
								$Cmd->event("1");
								$Cmd->save();
								$this->OnStatus($wifilight,$myLight);
							}
							break;
						case '303' : //Eye Care Off
							if (method_exists($myLight,'EyeCare')) {
								$myLight->EyeCare("off");
								$Cmd = $wifilight->getCmd(null,'SwCareGet');	
								$Cmd->event("0");
								$Cmd->save();
								$this->OnStatus($wifilight,$myLight);
							}
							break;
						 case '304' : //ambiance On
							if (method_exists($myLight,'OnAmb')) {
								$myLight->OnAmb();
								$Cmd = $wifilight->getCmd(null,'SwAmbGet');	
								$Cmd->event("1");
								$Cmd->save();
								$this->OnAmbStatus($wifilight,$myLight);
							}
							break;
						case '305' : //ambiance Off
							if (method_exists($myLight,'OffAmb')) {
								$myLight->OffAmb();
								$Cmd = $wifilight->getCmd(null,'SwAmbGet');	
								$Cmd->event("0");
								$Cmd->save();
								$this->OffAmbStatus($wifilight,$myLight);
							}
							break;
						case '306' : //Eye Care notify On
							if (method_exists($myLight,'EyeCareNot')) {
								$myLight->EyeCareNot("on");
								$Cmd = $wifilight->getCmd(null,'SwCareNotGet');	
								
								$Cmd->event("1");
								$Cmd->save();
								$this->OnStatus($wifilight,$myLight);
							}
							break;
						case '307' : //Eye Care notify Off
							if (method_exists($myLight,'EyeCareNot')) {
								$myLight->EyeCareNot("off");
								$Cmd = $wifilight->getCmd(null,'SwCareNotGet');	
								$Cmd->event("0");
								$Cmd->save();
								$this->OnStatus($wifilight,$myLight);
							}
							break;
						case '308' : // OFF + timer
							if (method_exists($myLight,'OnTimerOff')) {	
								$Cmd = $wifilight->getCmd(null, 'TimerGet');
								if ($Cmd !== false) {
									$value = $Cmd->execCmd();
								}	
								else
									$value=0;
								$myLight->OnTimerOff($value);							
							}
							break;
						case '309' : // Led on
							if (method_exists($myLight,'OnLed')) {
								$myLight->OnLed();
								$ProgCmd = $wifilight->getCmd(null, 'LedState');
								$ProgCmd->event(1);
								$ProgCmd->save();
							};	
							break;
						case '310' : // led off
							if (method_exists($myLight,'OffLed')) {
								$myLight->OffLed();
								$ProgCmd = $wifilight->getCmd(null, 'LedState');
								$ProgCmd->event(0);
								$ProgCmd->save();	
							}
							break;	
						case '311' : // RAZ consumption
							if (method_exists($myLight,'ErasePow')) {
								$myLight->ErasePow();
							}
							break;		
						case '350' : // up track on
							if (method_exists($myLight,'TrkUp')) {	
								$myLight->TrkUp(true);							
							}
							break;	
						case '351' : // up track on
							if (method_exists($myLight,'TrkUp')) {	
								$myLight->TrkUp(false);							
							}
							break;
						case '352' : // dw track on
							if (method_exists($myLight,'TrkDown')) {	
								$myLight->TrkDown(true);							
							}
							break;	
						case '353' : // dw track on
							if (method_exists($myLight,'TrkDown')) {	
								$myLight->TrkDown(false);							
							}
							break;	
						case '354' : // le track on
							if (method_exists($myLight,'TrkLe')) {	
								$myLight->TrkLe(true);							
							}
							break;	
						case '355' : // le track on
							if (method_exists($myLight,'TrkLe')) {	
								$myLight->TrkLe(false);							
							}
							break;	
						case '356' : // up track on
							if (method_exists($myLight,'TrkRi')) {	
								$myLight->TrkRi(true);							
							}
							break;	
						case '357' : // up track on
							if (method_exists($myLight,'TrkRi')) {	
								$myLight->TrkRi(false);							
							}
							break;	
						case '358' : // disco mode inc
							if (method_exists($myLight,'OnDiscoNext')) {	
								$myLight->OnDiscoNext();							
							}
							break;
						case '359' : // disco mode prev
							if (method_exists($myLight,'OnDiscoPrev')) {	
								$myLight->OnDiscoPrev();							
							}
							break;							
						case ($parameters >=400 && $parameters<=416):
							if (method_exists($myLight,'OnColor')) {	
								$col_array=array(
								0 => 'Random',
								1 => 'Blue',
								2 => 'Violet',
								3 => 'BabyBlue', 
								4 => 'Aqua',
								5 => 'SpringGreen',
								6 => 'Mint',
								7 => 'Green',
								8 => 'LimeGreen',
								9 => 'Yellow',
								10 => 'YellowOrange',
								11 => 'Orange',
								12 => 'Red',
								13 => 'Pink',
								14 => 'Fuchsia',
								15 => 'Lilac',
								16 => 'Lavendar'		
								);
								$White1=0;
								$White2=0;
								$Bright=100;
								if ($monoSlider == 1) {
									$Cmd = $wifilight->getCmd(null, 'On');
									if (isset($Cmd)) {
										$Cmd->setConfiguration('Type','Col'); // type mem	
										$Cmd->save();
										$ColMem = $Cmd->getConfiguration('ColMem');
										//log::add('wifilightV2', 'info', 'CColMem:'.$ColMem);
										if (($ColMem === false) || ($ColMem == '' )) {	
											$ColMem = 100;
										}										
										$Cmd = $wifilight->getCmd(null, 'WhiteGet');
										$Cmd->event($ColMem); // maj curseur intensité
									}
								}
								$Cmd = $wifilight->getCmd(null, 'WhiteWarmGet');
								if ($Cmd !== false){
									$White1 = $Cmd->execCmd();
									$Cmd = $wifilight->getCmd(null, 'WhiteCoolGet');
									if ($Cmd !== false) {
										$White2 = $Cmd->execCmd();
									}
								}
								else {
									$Cmd = $wifilight->getCmd(null, 'WhiteGet');
									if ($Cmd !== false) {
										$White1 = $Cmd->execCmd();
									}
								}	
								$Cmd = $wifilight->getCmd(null, 'IntensityGet');
								if ($Cmd !== false){
									$Bright = $Cmd->execCmd();
								}								
								$myLight->OnColor($col_array[$parameters-400],$Bright,$White1,$White2);
								$rgb = $myLight->GetColor();
								$Cmd = $wifilight->getCmd(null, 'ColorGet');
								$Cmd->event($rgb);
								$Cmd->save();
								$this->OnStatus($wifilight,$myLight);
							}
							break;
						case ($parameters >=450 && $parameters<=466):
							if (method_exists($myLight,'OnColorAmb')) {	
								$col_array=array(
								0 => 'Random',
								1 => 'Blue',
								2 => 'Violet',
								3 => 'BabyBlue', 
								4 => 'Aqua',
								5 => 'SpringGreen',
								6 => 'Mint',
								7 => 'Green',
								8 => 'LimeGreen',
								9 => 'Yellow',
								10 => 'YellowOrange',
								11 => 'Orange',
								12 => 'Red',
								13 => 'Pink',
								14 => 'Fuchsia',
								15 => 'Lilac',
								16 => 'Lavendar'		
								);
								$White1=0;
								$White2=0;
								$Bright=100;
								$myLight->OnColorAmb($col_array[$parameters-450],$Bright,$White1,$White2);
								$rgb = $myLight->GetColorAmb();
								$Cmd = $wifilight->getCmd(null, 'ColorAmbGet');
								$Cmd->event($rgb);
								$Cmd->save();
								$this->OnAmbStatus($wifilight,$myLight);
							}
							break;
		
						case ($parameters >=500 && $parameters<600):
							if (method_exists($myLight,'OnDisco')) {	
								$mode=$parameters-499;
								$Cmd = $wifilight->getCmd(null,'DiscoProg');
								$NbMode= $Cmd->getConfiguration("maxValue");
								if ($mode > $NbMode) $mode=$NbMode;
								if ($mode <1) $mode = 1;					
								// disco send speed and mode at the same time
								$Cmd= $wifilight->getCmd(null, 'DiscoSpeedGet'); 
								$speed =1;
								if ($Cmd !== false)	$speed=$Cmd->execCmd();
								$myLight->OnDisco($mode,$speed); // second parameter is used when state return fails
								$this->DiscoStatus($wifilight,$mode);
							}
							break;		
						default:	
							if (method_exists($myLight,'OnControl')) {						
								$ret = $myLight->OnControl($parameters);
								//log::add('wifilightV2','debug','ret='.$ret);								
								if ($ret == MODEON) 
									$this->OnStatus($wifilight,$myLight);
								if ($ret == MODEONBG) {
									$this->OnAmbStatus($wifilight,$myLight);
								}
							}
							break;
					}	
					break;
			}		
		} 
	}

	public function OffStatus($wifilight,$myLight) {
		if ($myLight!=NULL) {
			//usleep(50000);
			$state = $myLight->retStatus();
			if (isset($state['On'])) {
				if ($state['On'] == 1) {
					$ProgCmd = $wifilight->getCmd(null, 'SwOnOffGet');
					$ProgCmd->event(1);
					$ProgCmd->save();
				}
				else if ($state['On'] == 0) {
					$ProgCmd = $wifilight->getCmd(null, 'SwOnOffGet');
					$ProgCmd->event(0);
					$ProgCmd->save();
				}
				else {
					$ProgCmd = $wifilight->getCmd(null, 'SwOnOffGet');
					$ProgCmd->event(0);
					$ProgCmd->save();	
				}
				$Cmd = $wifilight->getCmd(null, 'ConnectedGet');
				if ($Cmd !== false) {
					$Cmd->event(SUCCESS);
					$Cmd->save();
				}
			}
			else {
				if (isset($state)&& ( $state==NOSOCKET || $state==NOTCONNECTED || $state==NORESPONSE || $state==BADRESPONSE || $state==NOSTATE )){
					$Cmd = $wifilight->getCmd(null, 'ConnectedGet');
					if ($Cmd !== false) {
						$Cmd->event($state);
						$Cmd->save();
					}
				}				
				$ProgCmd = $wifilight->getCmd(null, 'SwOnOffGet');
				$ProgCmd->event(0);
				$ProgCmd->save();	
			}
			if (isset($state['AmbOn'])) {
					/*			
					 ob_start();
					 var_dump($state);
					 $res = ob_get_clean();
					 log::add('wifilightV2','debug','Vreturn state:'.$res );
					 */
					
				if ($state['AmbOn'] == 1) {
					$ProgCmd = $wifilight->getCmd(null, 'SwAmbGet');
					$ProgCmd->event(1);
					$ProgCmd->save();
				}
				if ($state['AmbOn'] == 0) {
					$ProgCmd = $wifilight->getCmd(null, 'SwAmbGet');
					$ProgCmd->event(0);
					$ProgCmd->save();
				}
			}
		}
		else {	
			$ProgCmd = $wifilight->getCmd(null, 'SwOnOffGet');
			$ProgCmd->event(1);
			$ProgCmd->save();	
		}		
	}	
	public function OnStatus($wifilight, $myLight=NULL) {
		if ($myLight!=NULL) {
			//usleep(50000);
			$state = $myLight->retStatus();
			if (isset($state['On'])) {
				if ($state['On'] == 1) {
					$ProgCmd = $wifilight->getCmd(null, 'SwOnOffGet');
					$ProgCmd->event(1);
					$ProgCmd->save();
				}
				else if ($state['On'] == 0) {
					$ProgCmd = $wifilight->getCmd(null, 'SwOnOffGet');
					$ProgCmd->event(0);
					$ProgCmd->save();
				}
				else {
					$ProgCmd = $wifilight->getCmd(null, 'SwOnOffGet');
					$ProgCmd->event(1);
					$ProgCmd->save();	
				}
				$Cmd = $wifilight->getCmd(null, 'ConnectedGet');
				if ($Cmd !== false) {
					$Cmd->event(SUCCESS);
					$Cmd->save();
				}
			}
			else {
				if (isset($state)&& ( $state==NOSOCKET || $state==NOTCONNECTED || $state==NORESPONSE || $state==BADRESPONSE || $state==NOSTATE )){
					$Cmd = $wifilight->getCmd(null, 'ConnectedGet');
					if ($Cmd !== false) {
						$Cmd->event($state);
						$Cmd->save();
					}
				}				
				$ProgCmd = $wifilight->getCmd(null, 'SwOnOffGet');
				$ProgCmd->event(1);
				$ProgCmd->save();	
			}
			if (isset($state['AmbOn'])) {
					/*			
					 ob_start();
					 var_dump($state);
					 $res = ob_get_clean();
					 log::add('wifilightV2','debug','Vreturn state:'.$res );
					 */
					
				if ($state['AmbOn'] == 1) {
					$ProgCmd = $wifilight->getCmd(null, 'SwAmbGet');
					$ProgCmd->event(1);
					$ProgCmd->save();
				}
				if ($state['AmbOn'] == 0) {
					$ProgCmd = $wifilight->getCmd(null, 'SwAmbGet');
					$ProgCmd->event(0);
					$ProgCmd->save();
				}
			}
		}
		else {	
			$ProgCmd = $wifilight->getCmd(null, 'SwOnOffGet');
			$ProgCmd->event(1);
			$ProgCmd->save();	
		}		
	}
	public function OffAmbStatus($wifilight,$myLight) {
		if ($myLight!=NULL) {
			$state = $myLight->retStatus();
			if (isset($state['On'])) {
				if ($state['On'] == 1) {
					$ProgCmd = $wifilight->getCmd(null, 'SwOnOffGet');
					$ProgCmd->event(1);
					$ProgCmd->save();
				}
				else if ($state['On'] == 0) {
					$ProgCmd = $wifilight->getCmd(null, 'SwOnOffGet');
					$ProgCmd->event(0);
					$ProgCmd->save();
				}
			}
			if (isset($state['AmbOn'])) {
				if ($state['AmbOn'] == 1) {
					$ProgCmd = $wifilight->getCmd(null, 'SwAmbGet');
					$ProgCmd->event(1);
					$ProgCmd->save();
				}
				else if ($state['AmbOn'] == 0) {
					$ProgCmd = $wifilight->getCmd(null, 'SwAmbGet');
					$ProgCmd->event(0);
					$ProgCmd->save();
				}else {
					$ProgCmd = $wifilight->getCmd(null, 'SwAmbGet');
					$ProgCmd->event(0);
					$ProgCmd->save();	
				}
			}
			else {	
				$ProgCmd = $wifilight->getCmd(null, 'SwAmbGet');
				$ProgCmd->event(1);
				$ProgCmd->save();	
			}
		}
		else {	
			$ProgCmd = $wifilight->getCmd(null, 'SwAmbGet');
			$ProgCmd->event(0);
			$ProgCmd->save();	
		}
	}	
	public function OnAmbStatus($wifilight,$myLight) {
		if ($myLight!=NULL) {
			$state = $myLight->retStatus();
			if (isset($state['On'])) {
				if ($state['On'] == 1) {
					$ProgCmd = $wifilight->getCmd(null, 'SwOnOffGet');
					$ProgCmd->event(1);
					$ProgCmd->save();
				}
				else if ($state['On'] == 0) {
					$ProgCmd = $wifilight->getCmd(null, 'SwOnOffGet');
					$ProgCmd->event(0);
					$ProgCmd->save();
				}
			}
					
			if (isset($state['AmbOn'])) {
				if ($state['AmbOn'] == 1) {
					$ProgCmd = $wifilight->getCmd(null, 'SwAmbGet');
					$ProgCmd->event(1);
					$ProgCmd->save();
				}
				else if ($state['AmbOn'] == 0) {
					$ProgCmd = $wifilight->getCmd(null, 'SwAmbGet');
					$ProgCmd->event(0);
					$ProgCmd->save();
				}else {
					$ProgCmd = $wifilight->getCmd(null, 'SwAmbGet');
					$ProgCmd->event(1);
					$ProgCmd->save();	
				}
			}
			else {	
				$ProgCmd = $wifilight->getCmd(null, 'SwAmbGet');
				$ProgCmd->event(1);
				$ProgCmd->save();	
			}
		}
		else {	
			$ProgCmd = $wifilight->getCmd(null, 'SwAmbGet');
			$ProgCmd->event(1);
			$ProgCmd->save();	
		}		
	}
	public function DiscoStatus($wifilight,$Prog) {
		$Cmd = $wifilight->getCmd(null, 'DiscoProgGet');	
		if ($Cmd !== false) {	
			$Cmd->event($Prog);
			$Cmd->save();
		}
		$this->OnStatus($wifilight);
	}
	public function DiscoStatusSpeed($wifilight,$Speed) {
		$Cmd = $wifilight->getCmd(null, 'DiscoSpeedGet');					
		if ($Cmd !== false)	{
			$Cmd->event($Speed);
			$Cmd->save();
		}
		$this->OnStatus($wifilight);
	}	
}

?>
