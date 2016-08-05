<?php

/**
 * @author AlexPro
 * @copyright 2008 - 2011 XNova Game Group
 * @var $Display HSTemplateDisplay
 * @var $user user
 * @var $game_config array
 * @var $CombatCaps array
 * @var $lang array
 * @var $resource array
 * @var $planetrow planet
 * @var $HeDBRec array
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

if(!defined("INSIDE")) die("attemp hacking");

	if ($user->data['urlaubs_modus_time'] > 0) {
		die("Нет доступа!");
	}

	system::includeLang('fleet');

	$maxfleet  = db::query("SELECT COUNT(fleet_owner) AS `actcnt` FROM {{table}} WHERE `fleet_owner` = '".$user->data['id']."';", 'fleets', true);

	$MaxFlottes = 1 + $user->data[$resource[108]];
	if ($user->data['rpg_admiral'] > time())
		$MaxFlottes += 2;

	if ($MaxFlottes <= $maxfleet['actcnt']) {
		die('Все слоты флота заняты');
	}

	$Mode   = intval($_GET['mode']);
	$Galaxy = intval($_GET['g']);
	$System = intval($_GET['s']);
	$Planet = intval($_GET['p']);
	$TypePl = intval($_GET['t']);
	$num 	= intval($_GET['count']);

	if ($Galaxy > 9 || $Galaxy < 1)
		die('Ошибочная галактика!');
	if ($System > 499 || $System < 1)
		die('Ошибочная система!');
	if ($Planet > 16 || $Planet < 1)
		die('Ошибочная планета!');
	if ($TypePl != 1 && $TypePl != 2 && $TypePl != 3 && $TypePl != 5)
		die('Ошибочный тип планеты!');

	if ($planetrow->data['galaxy'] == $Galaxy && $planetrow->data['system'] == $System && $planetrow->data['planet'] == $Planet && ($planetrow->data['planet_type'] == $TypePl || $TypePl == 2))
		$target = $planetrow->data;
	else {
		$target = db::query("SELECT * FROM {{table}} WHERE galaxy = ".$Galaxy." AND system = ".$System." AND planet = ".$Planet." AND planet_type = ".(($TypePl == 2) ? 1 : $TypePl)."", "planets", true);

		if (!isset($target['id']))
			die('Цели не существует!');
	}

	$FleetArray = array();

	$FleetSpeed = 0;

	if ($Mode == 6 && ($TypePl == 1 || $TypePl == 5)) {
		if ($num <= 0)
			die('Вы были забанены за читерство!');
		if ($planetrow->data['spy_sonde'] == 0)
			die('Нет шпионских зондо вля отправки!');
		if ($target['id_owner'] == $user->data['id'])
			die('Невозможно выполнить задание!');

		$HeDBRec = db::query("SELECT id, onlinetime, urlaubs_modus_time FROM {{table}} WHERE `id` = '". $target['id_owner'] ."';", 'users', true);

		$UserPoints    = db::query("SELECT total_points FROM {{table}} WHERE `stat_type` = '1' AND `stat_code` = '1' AND `id_owner` = '". $user->data['id'] ."';", 'statpoints', true);
		$User2Points   = db::query("SELECT total_points FROM {{table}} WHERE `stat_type` = '1' AND `stat_code` = '1' AND `id_owner` = '". $HeDBRec['id'] ."';", 'statpoints', true);

		$MyGameLevel  = $UserPoints['total_points'];
		$HeGameLevel  = $User2Points['total_points'];

		if (!$HeGameLevel)
			$HeGameLevel = 0;

		$VacationMode = $HeDBRec['urlaubs_modus_time'];

		if ($HeDBRec['onlinetime'] < (time()-60 * 60 * 24 * 7)){
			$NoobNoActive = 1;
		}else{
			$NoobNoActive = 0;
		}

		if ($user->data['authlevel'] != 3) {
			if (isset($TargetPlanet['id_owner'])  AND $NoobNoActive == 0 AND $HeGameLevel < ($game_config['noobprotectiontime'] * 1000)) {
				if ($MyGameLevel > ($HeGameLevel * $game_config['noobprotectionmulti']))
					die('Игрок находится под защитой новичков!');
				if (($MyGameLevel * $game_config['noobprotectionmulti']) < $HeGameLevel)
					die('Вы слишком слабы для нападения на этого игрока!');
			}
		}

		if ($VacationMode) {
			die('Игрок в режиме отпуска!');
		}

		if ($planetrow->data['spy_sonde'] < $num)
			$num = $planetrow->data['spy_sonde'];

		$FleetArray[210] = $num;

		$SpySpeed = GetFleetMaxSpeed ($FleetArray, 0, $user);
		$FleetSpeed  = min($SpySpeed);

	} elseif ($Mode == 8 && $TypePl == 2) {
		$DebrisSize = $target['debris_metal'] + $target['debris_crystal'];

		if ($DebrisSize == 0)
			die('Нет обломков для сбора!');
		if ($planetrow->data['big_recycler'] == 0 && $planetrow->data['recycler'] == 0)
			die('Нет переработчиков для сбора обломков!');

		$RecyclerNeeded_1 = 0;
		$RecyclerNeeded_2 = 0;

		if ($planetrow->data['big_recycler'] > 0) {
			$RecyclerNeeded_1 	= floor($DebrisSize / ($CombatCaps[220]['capacity'])) + 1;

			if ($RecyclerNeeded_1 > $planetrow->data['big_recycler'])
				$RecyclerNeeded_1 = $planetrow->data['big_recycler'];

			$DebrisSize			-= $RecyclerNeeded_1 * $CombatCaps[220]['capacity'];

			$FleetArray[220] = $RecyclerNeeded_1;
		}

		if ($planetrow->data['recycler'] > 0 && $DebrisSize > 0) {
			$RecyclerNeeded_2 	= floor($DebrisSize / ($CombatCaps[209]['capacity'])) + 1;

			if ($RecyclerNeeded_2 > $planetrow->data['recycler'])
				$RecyclerNeeded_2 = $planetrow->data['recycler'];

			$DebrisSize			-= $RecyclerNeeded_2 * $CombatCaps[209]['capacity'];

			$FleetArray[209] = $RecyclerNeeded_2;
		}

		$RecyclerSpeed = GetFleetMaxSpeed ($FleetArray, 0, $user);
		$FleetSpeed  = min($RecyclerSpeed);
	} else
		die('Такой миссии не существует!');
		
	$SpeedFactor   = $game_config['fleet_speed'] / 2500;
	$distance      = GetTargetDistance($planetrow->data['galaxy'], $Galaxy, $planetrow->data['system'], $System, $planetrow->data['planet'], $Planet);
	$duration      = GetMissionDuration(10, $FleetSpeed, $distance, $SpeedFactor);
		
	$consumption   = GetFleetConsumption ($FleetArray, $SpeedFactor, $duration, $distance, $FleetSpeed, $user);

	$ShipCount 		= 0;
	$ShipArray 		= '';
	$FleetSubQRY 	= '';
	$FleetStorage 	= 0;

	foreach ($FleetArray as $Ship => $Count) {
		$FleetSubQRY     .= "`".$resource[$Ship] . "` = `" . $resource[$Ship] . "` - " . $Count . " , ";
		$ShipArray       .= (isset($resource['lvl_'.$Ship])) ? $Ship .",". $Count ."!".$resource['lvl_'.$Ship].";" : $Ship .",". $Count ."!0;";
		$ShipCount       += $Count;
		
		if ($Ship == 202 || $Ship == 203)
			$FleetStorage += round($CombatCaps[$Ship]['capacity'] * (1 + $user->data['fleet_'.$Ship] * 0.05)) * $Count;
		else
			$FleetStorage += $CombatCaps[$Ship]['capacity'] * $Count;
	}
	
	if ($FleetStorage < $consumption)
		die('Не хватает места в трюме для топлива! (необходимо еще '.($consumption - $FleetStorage).')');
	if ($planetrow->data['deuterium'] < $consumption)
		die('Не хватает топлива на полёт! (необходимо еще '.($consumption - $planetrow->data['deuterium']).')');

	if ($FleetSubQRY != '') {
		$QryInsertFleet  = "INSERT INTO {{table}} SET ";
		$QryInsertFleet .= "`fleet_owner` = '". $user->data['id'] ."', ";
		$QryInsertFleet .= "`fleet_owner_name` = '". $planetrow->data['name'] ."', ";
		$QryInsertFleet .= "`fleet_mission` = '". $Mode ."', ";
		$QryInsertFleet .= "`fleet_array` = '". $ShipArray ."', ";
		$QryInsertFleet .= "`fleet_start_time` = '". ($duration + time()) ."', ";
		$QryInsertFleet .= "`fleet_start_galaxy` = '". $planetrow->data['galaxy'] ."', ";
		$QryInsertFleet .= "`fleet_start_system` = '". $planetrow->data['system'] ."', ";
		$QryInsertFleet .= "`fleet_start_planet` = '". $planetrow->data['planet'] ."', ";
		$QryInsertFleet .= "`fleet_start_type` = '". $planetrow->data['planet_type'] ."', ";
		$QryInsertFleet .= "`fleet_end_time` = '". (($duration * 2) + time()) ."', ";
		$QryInsertFleet .= "`fleet_end_galaxy` = '". $Galaxy ."', ";
		$QryInsertFleet .= "`fleet_end_system` = '". $System ."', ";
		$QryInsertFleet .= "`fleet_end_planet` = '". $Planet ."', ";
		$QryInsertFleet .= "`fleet_end_type` = '". $TypePl ."', ";

		if ($Mode == 6) {
			$QryInsertFleet .= "`fleet_target_owner` = '".$HeDBRec['id']."', ";
 			$QryInsertFleet .= "`fleet_target_owner_name` = '".$target['name']."', ";
		}

		$QryInsertFleet .= "`start_time` = '". time() ."', `fleet_time` = '".($duration + time())."';";
		db::query( $QryInsertFleet, 'fleets');

		db::query ("UPDATE {{table}} SET ".$FleetSubQRY." deuterium = deuterium - ".$consumption." WHERE `id` = '". $planetrow->data['id'] ."'", "planets");

		if ($Mode == 8 && $user->data['tutorial'] == 9 && $user->data['tutorial_value'] == 0)
    		db::query("UPDATE {{table}} SET tutorial_value = 1 WHERE id = ".$user->data['id'].";", "users");
		if ($Mode == 6 && $user->data['tutorial'] == 6 && $user->data['tutorial_value'] == 0)
    		db::query("UPDATE {{table}} SET tutorial_value = 1 WHERE id = ".$user->data['id'].";", "users");

		die("Флот отправлен на координаты [". $Galaxy .":". $System .":". $Planet ."] с миссией ".$lang['type_mission'][$Mode]." и прибудет к цели в ".datezone("H:i:s", ($duration + time()))."");
	}
?>
