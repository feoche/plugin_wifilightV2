<?php
/*
*
* Copyright (c) 2014 Bernard caron
* adapted from
* Thomas Martinez's plugin for Karotz under Jeedom
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
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
* IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
* FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
* AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
* LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
* OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
* THE SOFTWARE.
*/

if (!isConnect('admin')) {
    throw new Exception('401 - Accès non autorisé');
}

global $listCmdWifilightV2;

//include_file('core', 'wifilightV2', 'config', 'wifilightV2');
//sendVarToJS('eqType', 'wifilightV2');
//$eqLogics = eqLogic::byType('wifilightV2');
$plugin = plugin::byId('wifilightV2');
sendVarToJS('eqType', $plugin->getId());
$eqLogics = eqLogic::byType($plugin->getId());
function sortByOption($a, $b) {
	return strcmp($a['name'], $b['name']);
}
?>

<div class="row row-overflow">

    <div class="col-lg-2 col-md-3 col-sm-4">
        <div class="bs-sidebar">
            <ul id="ul_eqLogic" class="nav nav-list bs-sidenav">
                <a class="btn btn-default eqLogicAction" style="width : 100%;margin-top : 5px;margin-bottom: 5px;" data-action="add"><i class="fa fa-plus-circle"></i>{{Ajout module wifi}}</a>
                <li class="filter" style="margin-bottom: 5px;"><input class="filter form-control input-sm" placeholder="{{Rechercher}}" style="width: 100%"/></li>
                <?php
                foreach (eqLogic::byType('wifilightV2') as $eqLogic) {
                    echo '<li class="cursor li_eqLogic" data-eqLogic_id="' . $eqLogic->getId() . '"><a>' . $eqLogic->getHumanName(true) . '</a></li>';
                }
                ?>
            </ul>
        </div>
    </div>


    <div class="col-lg-10 col-md-9 col-sm-8 eqLogicThumbnailDisplay" style="border-left: solid 1px #EEE; padding-left: 25px;">

		<legend><i class="fa fa-cog"></i>  {{Gestion}}</legend>
		 <div class="eqLogicThumbnailContainer">
			 <div class="cursor eqLogicAction" data-action="add" style="background-color : #ffffff; height : 120px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >
				 <center>
					<i class="fa fa-plus-circle" style="font-size : 5em;color:#ffa435;"></i>
				</center>
				<span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#ffa435"><center>Ajouter</center></span>
			</div>
			<div class="cursor eqLogicAction" data-action="gotoPluginConf" style="background-color : #ffffff; height : 120px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;">
				<center>
				  <i class="fa fa-wrench" style="font-size : 5em;color:#767676;"></i>
			  </center>
			  <span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#767676"><center>{{Configuration}}</center></span>
			</div>

		</div>




        <legend><i class="jeedom2 jeedom2-bright4"></i> {{Mes WifiLights}}</legend>



		</br>

        <?php
        if (count($eqLogics) == 0) {
            echo "<br/><br/><br/><center><span style='color:#767676;font-size:1.2em;font-weight: bold;'>{{Aucun WifiLight, ajouter un module}}</span></center>";
        } else {
            ?>
            <div class="eqLogicThumbnailContainer">
                <?php
                $dir = dirname(__FILE__) . '/../../core/config/images/';
                $files = scandir($dir);
                foreach ($eqLogics as $eqLogic) {
                    echo '<div class="eqLogicDisplayCard cursor" data-eqLogic_id="' . $eqLogic->getId() . '" style="background-color : #ffffff; height : 200px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >';
                    echo "<center>";
                    $path = $eqLogic->getConfiguration('icon');
					//log::add("wifilightV2","debug","img=".$path);
                    if (!in_array($path, $files)) {
                        $path = 'wifilightV2_icon.png';
                    }
                    echo '<img src="plugins/wifilightV2/core/config/images/' . $path.'" height="100" />';
                    echo "</center>";
                    echo '<span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;"><center>' . $eqLogic->getHumanName(true, true) . '</center></span>';
                    echo '</div>';
                }
                ?>
          </div>
        <?php } ?>
    </div>
 <div class="col-lg-10 col-md-9 col-sm-8 eqLogic" style="border-left: solid 1px #EEE; padding-left: 25px;display: none;">

 <a class="btn btn-success eqLogicAction pull-right" data-action="save"><i class="fa fa-check-circle"></i> {{Sauvegarder}}</a>
 <a class="btn btn-danger eqLogicAction pull-right" data-action="remove"><i class="fa fa-minus-circle"></i> {{Supprimer}}</a>
 <a class="btn btn-default eqLogicAction pull-right" data-action="configure"><i class="fa fa-cogs"></i> {{Configuration avancée}}</a>
 <a class="btn btn-default eqLogicAction pull-right" data-action="copy"><i class="fa fa-files-o"></i> {{Dupliquer}}</a>

 <ul class="nav nav-tabs" role="tablist">
  <li role="presentation"><a href="#" class="eqLogicAction" aria-controls="home" role="tab" data-toggle="tab" data-action="returnToThumbnailDisplay"><i class="fa fa-arrow-circle-left"></i></a></li>
  <li role="presentation" class="active"><a href="#eqlogictab" aria-controls="home" role="tab" data-toggle="tab"><i class="fa fa-tachometer"></i> {{Equipement}}</a></li>
  <li role="presentation"><a href="#commandtab" aria-controls="profile" role="tab" data-toggle="tab"><i class="fa fa-list-alt"></i> {{Commandes}}</a></li>
</ul>





<div class="tab-content" style="height:calc(100% - 50px);overflow:auto;overflow-x: hidden;">
	<div role="tabpanel" class="tab-pane active" id="eqlogictab">
	<br/>
        <div class="row">
            <div class="col-sm-7">
                <form class="form-horizontal">
            <fieldset>
                <div class="form-group">
                    <label class="col-md-4 control-label">{{Nom du module Wifi qui gère l'éclairage}}</label>
                    <div class="col-md-4">
                        <input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display : none;" />
                        <input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="{{Nom de l'équipement WifiLight}}"/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-4 control-label" >{{Objet parent}}</label>
                    <div class="col-md-6">
                        <select id="sel_object" class="eqLogicAttr form-control" data-l1key="object_id">
                            <option value="">{{Aucun}}</option>
                            <?php
                            foreach (jeeObject::all() as $object) {
                                echo '<option value="' . $object->getId() . '">' . $object->getName() . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-4 control-label">{{Catégorie}}</label>
                    <div class="col-md-6">
                        <?php
                        foreach (jeedom::getConfiguration('eqLogic:category') as $key => $value) {
                            echo '<label class="checkbox-inline">';
                            echo '<input type="checkbox" class="eqLogicAttr" data-l1key="category" data-l2key="' . $key . '" />' . $value['name'];
                            echo '</label>';
                        }
                        ?>

                    </div>
                </div>

                <div class="form-group">
					<label class="col-md-4 control-label" ></label>
					<div class="col-md-5">
					 <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isEnable"  checked />{{Activer}}</label>
					 <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isVisible"  checked />{{Visible}}</label>
					</div>
                </div>
                <div class="form-group">
                    <label class="col-md-4 control-label help" data-help="{{Attribuer une adresse IP fixe au module wifi qui pilote l'éclairage ou à l'ampoule wifi à l'aide du DHCP de votre routeur. Elle doit ressembler à 192.168.1.20}}">{{Adresse IP du module wifi :}}</label>
                    <div class="col-md-4">
                        <input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="addr" placeholder="{{Adresse IP}}"/>
                    </div>
                </div>

				<div class="form-group">
                    <label class="col-md-4 control-label help" data-help="{{Indiquer la marque de l'ampoule ou de contrôleur afin de créer automatiquement les commandes correspondantes. En cas de modification, supprimer toutes les commandes et sauvegarder 2 fois.}}">{{Type d'ampoule ou de contrôleur:}}</label>
                    <div class="col-md-6">
						<select id="" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="typeN">
							<option value="">{{Aucun}}</option>
								<?php
								$devices = wifilightV2::GetDevices();
								//natcasesort($devices);
								foreach ($devices as $Id => $device) {
									$name = $device['name'];
									//$type = $device['type'];
									//$canal = $device['canal'];
									$instruction = '';
									if (isset($device['instructions'])) {
										$instruction = $device['instructions'];
									}
									if( $name!= "") {
										//echo '<option value="' . $Id. '" data-instruction="' . $instruction . '"> {{' .$name . '}} </option>';
										$tabAff[$name] = '<option value="' . $Id. '" data-instruction="' . $instruction . '"> {{' .$name . '}} </option>';
									}
								}
								ksort($tabAff);
								foreach ($tabAff as $name => $html) {
										echo $html;
								}
								?>
						</select>
						<center>
						</br>
						<img src="core/img/no_image.gif" data-original=".png" id="img_type" class="img-responsive" style="max-height : 200px;"  onerror="this.src='plugins/wifilightV2/core/config/images/wifilightV2_icon.png'"/>
						</center>
						</br>
					</div>
				</div>
				<div class="form-group subtype" style="display:none;" >
					<label class="col-lg-4 control-label help" data-help="{{Choisir l'ampoule.  En cas de modification, supprimer toutes les commandes et sauvegarder 2 fois.}}">{{Ampoule}}</label>
					<div class="col-lg-6">
						<select class="eqLogicAttr form-control listModel" data-l1key="configuration" data-l2key="subtype">
						</select>
						<center>
							</br>
							<img  data-original=".png" id="img_subtype" class="img-responsive" style="max-height : 200px;"  />
						</center>
					</div>
				</div>
				<div class="form-group canal">
					<label  class="col-lg-4 control-label help" data-help="{{Créer un module wifilight pour chaque canal.}}">{{Canal du module wifi :}}</label>
					<div class="col-lg-4">
						<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="canal" placeholder="{{Canal}}"/>
					</div>
				</div>
				<div class="form-group macad2">
					<label  class="col-lg-4 control-label help" data-help="{{Ce périphérique nécessite un jeton. Consulter l'aide ci-contre.}}">{{Jeton :}}</label>
					<div class="col-lg-4">
						<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="macad" placeholder="{{Jeton}}"/>
					</div>
				</div>
				<div class="form-group identifiant">
					<label  class="col-lg-4 control-label help" data-help="{{Ce périphérique nécessite un identifiant local. Consulter l'aide ci-contre.}}">{{Identifiant :}}</label>
					<div class="col-lg-4">
						<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="identifiant" placeholder="{{Identifiant}}"/>
					</div>
				</div>
				<div class="form-group nbLeds">
					<label  class="col-lg-4 control-label help" data-help="{{Indiquer le nombre de leds connectées au contrôleur}}">{{Nbre de Leds :}}</label>
					<div class="col-lg-4">
						<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="nbLeds" placeholder="{{1-600 - Défaut : 60}}"/>
					</div>
				</div>


					<div class="form-group colorOrder">
						<label  class="col-lg-4 control-label help" data-help="{{Indiquer l'ordre des couleurs, si les couleurs sont mélangées}}">{{Ordre des couleurs :}}</label>
						<div class="col-lg-4">
							<select id="" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="colorOrder" >
								<option value="" >{{RGB}}</option>
								<option value="1">{{RBG}}</option>
								<option value="2">{{GRB}}</option>
								<option value="3">{{GBR}}</option>
								<option value="4">{{BRG}}</option>
								<option value="5">{{BGR}}</option>
							</select>
						</div>
					</div>


				<div class="form-group">
					<label  class="col-lg-4 control-label help" data-help="{{Nombre de fois où chaque commande vers les ampoules est envoyé (1 à 6) permet de palier les mauvaises transmissions.}}">{{Nombre d'envois de chaque commande :}}</label>
					<div class="col-lg-4">
						<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="repetitions" placeholder="{{1 à 6 - Défaut:1}}"/>
					</div>
				</div>
				<div class="form-group">
					<label  class="col-lg-4 control-label help" data-help="{{Permet d'introduire une pause entre chaque envoi de commande pour améliorer les transmissions.}}">{{Délai entre chaque envoi (ms) :}}</label>
					<div class="col-lg-4">
						<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="delai" placeholder="{{0 à 100 ms - Defaut:0}}"/>
					</div>
				</div>
				<div class="form-group">
					<label  class="col-lg-4 control-label help" data-help="{{Pourcentage d'incrémentation de l'intensité lors de la modification incrémentale de l'intensité lumineuse.}}">{{Incrémentation de l'intensité (%) :}}</label>
					<div class="col-lg-4">
						<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="incremV" placeholder="{{1 à 25 %  - Defaut:10}}"/>
					</div>
				</div>
				<div id="controles">
				   <div class="form-group">
					  <label class="col-lg-4 control-label help" data-help="{{Permet de créer les commandes indispensables du périphérique ou l'ensemble des commandes (attention : création très lente).}}">{{Création des commandes :}}</label>
						<div class="col-lg-4">
						<select id="" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="controles">
							 <option value="">{{Minimale}}</option>
							 <option value="1">{{Etendue}}</option>
							 <option value="2">{{+Disco+Couleurs}}</option>
						 </select>
						 </div>
				   </div>
				</div>

			   <div class="form-group">
				  <label class="col-lg-4 control-label help" data-help="{{Permet de grouper plusieurs périphériques, une commande appliquée sur un périphérique le sera sur tous les périphériques du même groupe. Mettre 0 pour ne pas grouper.}}">{{Groupe :}}</label>
					<div class="col-lg-4">
						<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="group" placeholder="{{0 à 100 - Defaut:0}}"/>
					</div>
			   </div>

            </fieldset>
        </form>
      </div>


	  <div id="infoNode" class="col-sm-4">

                    <fieldset>
                        <legend>{{Informations}}</legend>
                	<div class="form-group">
						<div class="alert alert-info">
							{{Prérequis : }}</div>
						<div id="div_instruction"></div>


                    </div>
					</fieldset>

		</div>
     </div>
</div>



<div role="tabpanel" class="tab-pane" id="commandtab">






        <legend>Commandes</legend>
        <a class="btn btn-success btn-sm cmdAction help" data-action="add" data-help="{{Les commandes seront ajoutées automatiquement en cliquant sur Sauvegarder. Si une commande est supprimée, elle est automatiquement recréée en cliquant sur Sauvegarder. Décocher -Afficher- pour les commandes qui ne sont jamais utilisées. Il est possible de modifier le nom des commandes ajoutées automatiquement.}}"><i class="fa fa-plus-circle"></i>{{Ajouter une commande WifiLight}}</a><br/><br/>

        <table id="table_cmd" class="table table-bordered table-condensed">
            <thead>
                <tr>
                    <th style="width: 60px;" >{{N°}}</th>
                    <th style="width: 330px;" class="help"  data-help="{{Permet de modifier le nom du bouton de la commande ou d'utiliser une icône à la place.}}">{{Interface}}</th>
                    <th style="width: 220px;" class="help"  data-help="{{Indique si la commande est une action (actionneur) ou une info (capteur) ainsi que le format de l'information.}}">{{Paramètres}}</th>
				    <th class="help"  data-help="{{Ne pas modifier ces informations pour les commandes créées automatiquement.}}">{{Nom interne et n° de commande}}</th>
					<th style="width: 150px;" class="help"  data-help="{{Permet de supprimer l'affichage de la commande correspondante}}">{{Affichage}}</th>
					<th style="width: 180px;" class="help"  data-help="{{Permet de modifier le pictogramme associé à la commande et de tester la commande.}}">{{Configuration widget}}</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>

	   </div>
    </div>
</div>



<?php include_file('desktop', 'wifilightV2', 'js', 'wifilightV2'); ?>
<?php include_file('core', 'plugin.template', 'js'); ?>
