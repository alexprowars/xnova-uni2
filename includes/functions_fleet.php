<?php

function GetGameSpeedFactor ()
{
	global $game_config;

	return $game_config['fleet_speed'] / 2500;
}

function GetTargetDistance ($OrigGalaxy, $DestGalaxy, $OrigSystem, $DestSystem, $OrigPlanet, $DestPlanet)
{
	if (($OrigGalaxy - $DestGalaxy) != 0) {
		$distance = abs($OrigGalaxy - $DestGalaxy) * 20000;
	} elseif (($OrigSystem - $DestSystem) != 0) {
		$distance = abs($OrigSystem - $DestSystem) * 5 * 19 + 2700;
	} elseif (($OrigPlanet - $DestPlanet) != 0) {
		$distance = abs($OrigPlanet - $DestPlanet) * 5 + 1000;
	} else {
		$distance = 5;
	}

	return $distance;
}

function GetMissionDuration ($GameSpeed, $MaxFleetSpeed, $Distance, $SpeedFactor)
{

	
	if (!$GameSpeed || !$MaxFleetSpeed || !$SpeedFactor || !$Distance)
	{
		global $user;
	
		SendSimpleMessage ( 1, $user->data['id'], time(), 0, 'cdv', ''.json_encode($_GET).'---'.json_encode($_POST).'');
	}
	
		return round(((35000 / $GameSpeed * sqrt($Distance * 10 / $MaxFleetSpeed) + 10) / $SpeedFactor));
}

/**
 * @param  $FleetArray
 * @param  $Fleet
 * @param  $user user
 * @return array|int
 */
function GetFleetMaxSpeed ($FleetArray, $Fleet, $user)
{
	global $CombatCaps;

	$speedalls = array();

	if ($Fleet != 0) {
		$FleetArray[$Fleet] =  1;
	}

	foreach ($FleetArray as $Ship => $Count)
	{
		switch ($CombatCaps[$Ship]['type_engine'])
		{
			case 1:
				$speedalls[$Ship] = $CombatCaps[$Ship]['speed'] * (1 + ($user->data['combustion_tech'] * 0.1));
				break;
			case 2:
				$speedalls[$Ship] = $CombatCaps[$Ship]['speed'] * (1 + ($user->data['impulse_motor_tech'] * 0.2));
				break;
			case 3:
				$speedalls[$Ship] = $CombatCaps[$Ship]['speed'] * (1 + ($user->data['hyperspace_motor_tech'] * 0.3));
				break;
			default:
				$speedalls[$Ship] = $CombatCaps[$Ship]['speed'];
		}

		if ($user->bonus_fleet_speed != 1)
			$speedalls[$Ship] = round($speedalls[$Ship] * $user->bonus_fleet_speed);
	}

	if ($Fleet != 0)
		$speedalls = $speedalls[$Fleet];

	return $speedalls;
}

function SetShipsEngine ($user)
{
	global $CombatCaps, $reslist;

	foreach ($reslist['fleet'] as $Ship)
	{
		if (isset($CombatCaps[$Ship]) && isset($CombatCaps[$Ship]['engine_up']) && $CombatCaps[$Ship]['engine_up'] > 0)
		{
			if ($CombatCaps[$Ship]['type_engine'] == 1 && $user['impulse_motor_tech'] >= $CombatCaps[$Ship]['engine_up'])
			{
				$CombatCaps[$Ship]['type_engine'] 	= 2;
				$CombatCaps[$Ship]['engine_up']		= 0;
			}
			elseif ($CombatCaps[$Ship]['type_engine'] == 2 && $user['hyperspace_motor_tech'] >= $CombatCaps[$Ship]['engine_up'])
			{
				$CombatCaps[$Ship]['type_engine'] 	= 3;
				$CombatCaps[$Ship]['engine_up']		= 0;
			}
		}
	}
}

/**
 * @param  $Ship
 * @param  $user user
 * @return float
 */
function GetShipConsumption ($Ship, $user)
{
	global $CombatCaps;

	return ceil($CombatCaps[$Ship]['consumption'] * $user->bonus_fleet_fuel);
}

function GetFleetConsumption ($FleetArray, $SpeedFactor, $MissionDuration, $MissionDistance, $FleetMaxSpeed, $Player)
{
	$consumption = 0;

	foreach ($FleetArray as $Ship => $Count)
	{
		if ($Ship > 0)
		{
			$ShipSpeed         = GetFleetMaxSpeed("", $Ship, $Player);
			$ShipConsumption   = GetShipConsumption($Ship, $Player);
			$spd               = 35000 / ($MissionDuration * $SpeedFactor - 10) * sqrt( $MissionDistance * 10 / $ShipSpeed );
			$consumption      += ($ShipConsumption * $Count) * $MissionDistance / 35000 * (($spd / 10) + 1) * (($spd / 10) + 1);
		}
	}

	$consumption = round($consumption) + 1;

	return $consumption;
}

function GetFleetStay ($FleetArray)
{
	global $CombatCaps;

	$stay = 0;
	foreach ($FleetArray as $Ship => $Count) {
		if ($Ship > 0) {
			$stay += $CombatCaps[$Ship]['stay'] * $Count;
		}
	}
	return $stay;
}
 
 ?>
