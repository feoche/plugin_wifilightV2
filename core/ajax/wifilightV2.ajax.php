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

try {
	require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
	include_file('core', 'authentification', 'php');

	if (!isConnect('admin')) {
		throw new Exception('401 Unauthorized');
	}

	ajax::init();

    if (init('action') == 'getAll') {
        $eqLogics = eqLogic::byType('wifilightV2');
        // la liste des équipements
        foreach ($eqLogics as $eqLogic) {
            $data['id'] = $eqLogic->getId();
            $data['humanSidebar'] = $eqLogic->getHumanName(true, false);
            $data['humanContainer'] = $eqLogic->getHumanName(true, true);
            $return[] = $data;
        }
        ajax::success($return);
    }
    // action qui permet d'effectuer la sauvegarde des donéée en asynchrone
    if (init('action') == 'saveStack') {
        $params = init('params');
        ajax::success(wifilightV2::saveStack($params));
    }
	if (init('action') == 'getDevices') {
		$wifilightV2 = wifilightV2::byId(init('id'));
		if (!is_object($wifilightV2)) {
			ajax::success(array());
		}
		ajax::success($wifilightV2->GetDevices(init('conf')));
	}
	if (init('action') == 'getModel') {
		$wifilightV2 = wifilightV2::byId(init('id'));
		if (!is_object($wifilightV2)) {
			ajax::success(array());
		}
		ajax::success($wifilightV2->getModel(init('conf')));
	
	}


    throw new Exception(__('Aucune methode correspondante à : ', __FILE__) . init('action'));
    /*     * *********Catch exeption*************** */
} catch (Exception $e) {
    ajax::error(displayExeption($e), $e->getCode());
}
?>
