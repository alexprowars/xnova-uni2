<?php

/**
 * @author AlexPro
 * @copyright 2008 - 2011 XNova Game Group
 * @var $lang array
 * @var $user user
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

if(!defined("INSIDE")) die("attemp hacking");

system::includeLang('fleet');

$BoxTitle   = $lang['fl_error'];
$TxtColor   = "red";
$BoxMessage = $lang['fl_notback'];

if ( is_numeric($_POST['fleetid']) ) {
	$fleetid  = intval($_POST['fleetid']);

	$FleetRow = db::query("SELECT * FROM {{table}} WHERE `fleet_id` = '". $fleetid ."';", 'fleets', true);
	$i = 0;

	if ($FleetRow['fleet_owner'] == $user->data['id']) {
		if (($FleetRow['fleet_mess'] == 0 || ($FleetRow['fleet_mess'] == 3 && $FleetRow['fleet_mission'] != 15) && $FleetRow['fleet_mission'] != 20  && $FleetRow['fleet_target_owner'] != 1)) {
			if ($FleetRow['fleet_end_stay'] != 0) {

				if ($FleetRow['fleet_start_time'] > time()) {

					$CurrentFlyingTime = time() - $FleetRow['start_time'];
				} else {

					$CurrentFlyingTime = $FleetRow['fleet_start_time'] - $FleetRow['start_time'];
				}
			} else {

				$CurrentFlyingTime = time() - $FleetRow['start_time'];
			}

			$ReturnFlyingTime  = $CurrentFlyingTime + time();

			$QryUpdateFleet  = "UPDATE {{table}} SET ";
			$QryUpdateFleet .= "`fleet_start_time` = '". (time() - 1) ."', ";
			$QryUpdateFleet .= "`fleet_end_stay` = '0', ";
			$QryUpdateFleet .= "`fleet_end_time` = '". ($ReturnFlyingTime + 1) ."', ";
			$QryUpdateFleet .= "`fleet_target_owner` = '". $user->data['id'] ."', ";
			$QryUpdateFleet .= "`fleet_group` = 0, ";
			$QryUpdateFleet .= "fleet_time = fleet_end_time, `fleet_mess` = '1' ";
			$QryUpdateFleet .= "WHERE ";
			$QryUpdateFleet .= "`fleet_id` = '" . $fleetid . "';";
			db::query( $QryUpdateFleet, 'fleets');

			if ($FleetRow['fleet_group'] != 0 && $FleetRow['fleet_mission'] == 1) {
				db::query("DELETE FROM {{table}} WHERE id = ".$FleetRow['fleet_group'].";", "aks");
				db::query("DELETE FROM {{table}} WHERE aks_id = ".$FleetRow['fleet_group'].";", "aks_user");
			}

			$BoxTitle   = $lang['fl_sback'];
			$TxtColor   = "lime";
			$BoxMessage = $lang['fl_isback'];
		} else {
			$BoxMessage = $lang['fl_notback'];
		}
	} else {
		$BoxMessage = $lang['fl_onlyyours'];
	}
}

message ("<font color=\"".$TxtColor."\">". $BoxMessage ."</font>", $BoxTitle, "?set=fleet", 2);


?>
