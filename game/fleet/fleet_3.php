<?php

/**
 * @author AlexPro
 * @copyright 2008 - 2011 XNova Game Group
 * @var $Display HSTemplateDisplay
 * @var $lang array
 * @var $user user
 * @var $resource array
 * @var $reslist array
 * @var $CombatCaps array
 * @var $planetrow planet
 * @var $game_config array
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

if(!defined("INSIDE")) die("attemp hacking");

if ($user->data['urlaubs_modus_time'] > 0) {
	message("Нет доступа!");
}

if ($_POST['crc'] != md5($user->data['id'].'-CHeAT_CoNTROL_Stage_03-'.date("dmY", time()).'-'.$_POST["usedfleet"]))
	message('Ошибка контрольной суммы!');

system::includeLang('fleet');

if (($_POST['mission'] == 1 || $_POST['mission'] == 6 || $_POST['mission'] == 9 || $_POST['mission'] == 2) AND time() < 1325491200)
	message ("<font color=\"red\"><b>Посылать флот в атаку временно запрещено.<br>Дата включения атак ".datezone("d.m.Y H ч. i мин.", 1325491200)."</b></font>", 'Ошибка');


$fleet_group_mr = 0;

if ($_POST['acs'] > 0){
	if ($_POST['mission'] == 2){
		$aks_count_mr = db::query("SELECT a.* FROM game_aks a, game_aks_user au WHERE au.aks_id = a.id AND au.user_id = ".$user->data['id']." AND au.aks_id = ".intval($_POST['acs'])." ;", 'aks');

		if (db::num_rows($aks_count_mr) > 0) {
			$aks_tr = db::fetch_assoc($aks_count_mr);
			if ($aks_tr['galaxy'] == $_POST["galaxy"] && $aks_tr['system'] == $_POST["system"] && $aks_tr['planet'] == $_POST["planet"] && $aks_tr['planet_type'] == $_POST["planettype"]) {
				$fleet_group_mr = $_POST['acs'];
			}
		}
	}
}
if (($_POST['acs'] == 0 || $fleet_group_mr == 0) && ($_POST['mission'] == 2)){
	$_POST['mission'] = 1;
}

$protection      = $game_config['noobprotection'];
$protectiontime  = $game_config['noobprotectiontime'];
$protectionmulti = $game_config['noobprotectionmulti'];
if ($protectiontime < 1) {
	$protectiontime = 9999999999999999;
}

$fleetarray  = json_decode(base64_decode(str_rot13($_POST["usedfleet"])), true);

if (!is_array($fleetarray)) {
	message ("<font color=\"red\"><b>Ошибка в передаче параметров!</b></font>", 'Ошибка', "?set=fleet", 2);
}

foreach ($fleetarray as $Ship => $Count) {
	if ($Count > $planetrow->data[$resource[$Ship]]) {
		message ("<font color=\"red\"><b>Недостаточно флота для отправки на планете!</b></font>", 'Ошибка', "?set=fleet", 2);
	}
}

$error              	= 0;
$galaxy             	= intval($_POST['galaxy']);
$system             	= intval($_POST['system']);
$planet             	= intval($_POST['planet']);
$planettype         	= intval($_POST['planettype']);
$fleetmission       	= intval($_POST['mission']);

if ($planettype != 1 && $planettype != 2 && $planettype != 3 && $planettype != 5) {
	message ("<font color=\"red\"><b>Неизвестный тип планеты!</b></font>", 'Ошибка', "?set=fleet", 2);
}
if ($planetrow->data['galaxy'] == $galaxy && $planetrow->data['system'] == $system && $planetrow->data['planet'] == $planet && $planetrow->data['planet_type'] == $planettype) {
	message ("<font color=\"red\"><b>Невозможно отправить флот на эту же планету!</b></font>", 'Ошибка', "?set=fleet", 2);
}

if ($fleetmission == 8) {
	$YourPlanet = false;
	$UsedPlanet = false;
	$select     = db::query("SELECT * FROM {{table}} WHERE galaxy = '". $galaxy ."' AND system = '". $system ."' AND planet = '". $planet ."' AND (planet_type = 1 OR planet_type = 5)", "planets");
} else {
	$YourPlanet = false;
	$UsedPlanet = false;
	$select     = db::query("SELECT * FROM {{table}} WHERE galaxy = '". $galaxy ."' AND system = '". $system ."' AND planet = '". $planet ."' AND planet_type = '". $planettype ."'", "planets");
}

if ($_POST['mission'] != 15) {
	if (db::num_rows($select) == 0 && $fleetmission != 7 && $fleetmission != 10) {
		message ("<font color=\"red\"><b>Данной планеты не существует!</b></font>", 'Ошибка', "?set=fleet", 2);
	} elseif ($fleetmission == 9 && db::num_rows($select) == 0) {
		message ("<font color=\"red\"><b>Данной планеты не существует!</b></font>", 'Ошибка', "?set=fleet", 2);
	} elseif (db::num_rows($select) == 0 && $fleetmission == 7 && $planettype != 1) {
		message ("<font color=\"red\"><b>Колонизировать можно только планету!</b></font>", 'Ошибка', "?set=fleet", 2);
	}
} else {

	if ($user->data[$resource[124]] >= 1) {
		$maxexp  = db::query("SELECT COUNT(*) AS `expeditions` FROM {{table}} WHERE `fleet_owner` = '".$user->data['id']."' AND `fleet_mission` = '15';", 'fleets', true);
		
		$ExpeditionEnCours  = $maxexp['expeditions'];
		$MaxExpedition = 1 + floor( $user->data[$resource[124]] / 3 );
	} else {
		$MaxExpedition = 0;
		$ExpeditionEnCours = 0;
	}

	if ($user->data[$resource[124]] == 0 ) {
		message ("<font color=\"red\"><b>Вами не изучена \"Экспедиционная технология\"!</b></font>", 'Ошибка', "?set=fleet", 2);
	} elseif ($ExpeditionEnCours >= $MaxExpedition ) {
		message ("<font color=\"red\"><b>Вы уже отправили максимальное количество экспедиций!</b></font>", 'Ошибка', "?set=fleet", 2);
	}

    if (intval($_POST['expeditiontime']) <= 0 || intval($_POST['expeditiontime']) > (round($user->data[$resource[124]] / 2) + 1)) {
        message ("<font color=\"red\"><b>Вы не можете столько времени летать в экспедиции!</b></font>", 'Ошибка', "?set=fleet", 2);    
    }
}

$TargetPlanet = db::fetch_assoc($select);

if ($TargetPlanet['id_owner'] == $user->data['id']) {
	$YourPlanet = true;
	$UsedPlanet = true;
} elseif (!empty($TargetPlanet['id_owner'])) {
	$YourPlanet = false;
	$UsedPlanet = true;
} else {
	$YourPlanet = false;
	$UsedPlanet = false;
}

$missiontype = array();

if ($fleetmission == 15 && $planet == 16) {
	$missiontype[15] = $lang['type_mission'][15];
} else {
	if ($planettype == 2 && ((isset($_POST['ship209']) && $_POST['ship209'] > 0) || (isset($_POST['ship220']) && $_POST['ship220'] > 0)))
		$missiontype[8] = $lang['type_mission'][8]; // Переработка

	elseif ($planettype == 1 || $planettype == 3 || $planettype == 5) {

        if (isset($_POST['ship216']) && $_POST['ship216'] > 0 && !$UsedPlanet && $planettype == 1)
			$missiontype[10] = $lang['type_mission'][10]; // Создать базу

        if (isset($_POST['ship210']) && $_POST['ship210'] > 0 && !$YourPlanet)
			$missiontype[6] = $lang['type_mission'][6]; // Шпионаж

        if (isset($_POST['ship208']) && $_POST['ship208'] > 0 && !$UsedPlanet)
                $missiontype[7] = $lang['type_mission'][7]; // Колонизировать

        if (!$YourPlanet && $UsedPlanet) {
            $missiontype[1] = $lang['type_mission'][1]; // Атаковать
        }

        if ($UsedPlanet && !$YourPlanet) {
            $missiontype[5] = $lang['type_mission'][5]; // Удерживать
        }

        if ((isset($_POST['ship202']) && $_POST['ship202'] > 0) || (isset($_POST['ship203']) && $_POST['ship203'] > 0))
		    $missiontype[3] = $lang['type_mission'][3]; // Транспорт

        if ($YourPlanet || $TargetPlanet['id_owner'] == 1 || $user->data['id'] == 1)
		    $missiontype[4] = $lang['type_mission'][4]; // Оставить

        if ($fleet_group_mr > 0 && $UsedPlanet)
		    $missiontype[2] = $lang['type_mission'][2]; // Объединить

        if ($planettype == 3 && isset($_POST['ship214']) && $_POST['ship214'] > 0 && !$YourPlanet && $UsedPlanet)
		    $missiontype[9] = $lang['type_mission'][9];
	}
}

if (count($missiontype[$fleetmission]) == 0) {
	message ("<font color=\"red\"><b>Миссия неизвестна!</b></font>", 'Ошибка', "?set=fleet", 2);
}

if ($_POST['mission'] == 8) {
	if ($TargetPlanet['debris_metal'] == 0 && $TargetPlanet['debris_crystal'] == 0)
		message ("<font color=\"red\"><b>Нет обломков для сбора.</b></font>", 'Ошибка', "?set=fleet", 2);
}

if (isset($TargetPlanet['id_owner']))
	$HeDBRec = db::query("SELECT * FROM {{table}} WHERE `id` = '". $TargetPlanet['id_owner'] ."';", 'users', true);
else
	$HeDBRec = $user->data;

if (($HeDBRec['id'] == 1 && $user->data['id'] != 1) && ($fleetmission != 4 && $fleetmission != 3))
	message ("<font color=\"red\"><b>На этого игрока запрещено нападать</b></font>", 'Ошибка', "?set=fleet", 2);

if ($user->data['ally_id'] != 0 && $HeDBRec['ally_id'] != 0 && $_POST['mission'] == 1) {
	$ad = db::query("SELECT * FROM {{table}} WHERE (a_id = ".$HeDBRec['ally_id']." AND d_id = ".$user->data['ally_id'].") AND status = 1", "alliance_diplomacy", true);

	if ($ad['id'] != "" && $ad['type'] < 3)
		message ("<font color=\"red\"><b>Заключён мир или перемирие с альянсом атакуемого игрока.</b></font>", "Ошибка дипломатии", "?set=fleet", 2);

}

$UserPoints    = db::query("SELECT total_points FROM {{table}} WHERE `stat_type` = '1' AND `stat_code` = '1' AND `id_owner` = '". $user->data['id'] ."';", 'statpoints', true);
$User2Points   = db::query("SELECT total_points FROM {{table}} WHERE `stat_type` = '1' AND `stat_code` = '1' AND `id_owner` = '". $HeDBRec['id'] ."';", 'statpoints', true);

$MyGameLevel  = $UserPoints['total_points'];
$HeGameLevel  = $User2Points['total_points'];
if (!$HeGameLevel) $HeGameLevel = 0;
$VacationMode = $HeDBRec['urlaubs_modus_time'];
if ($HeDBRec['onlinetime'] < (time()-60 * 60 * 24 * 7) || $HeDBRec['banaday'] != 0){
	$NoobNoActive = 1;
}else{
	$NoobNoActive = 0;
}

if ($user->data['authlevel'] != 3) {
	if (isset($TargetPlanet['id_owner']) AND ($_POST['mission'] == 1 OR $_POST['mission'] == 2 OR $_POST['mission'] == 5 OR $_POST['mission'] == 6 OR $_POST['mission'] == 9)  AND $protection == 1  AND $NoobNoActive == 0 AND $HeGameLevel < ($protectiontime * 1000)) {
		if ($MyGameLevel > ($HeGameLevel * $protectionmulti))
			message("<font color=\"lime\"><b>Игрок находится под защитой новичков!</b></font>", 'Защита новичков', "?set=fleet", 2);
		if (($MyGameLevel * $protectionmulti) < $HeGameLevel)
			message("<font color=\"lime\"><b>Вы слишком слабы для нападения на этого игрока!</b></font>", 'Защита новичков', "?set=fleet", 2);
	}
}

if ($VacationMode AND $_POST['mission'] != 8) {
	message("<font color=\"lime\"><b>Игрок в режиме отпуска!</b></font>", 'Режим отпуска', "?set=fleet", 2);
}

$FlyingFleets = db::fetch_assoc(db::query("SELECT COUNT(fleet_id) as Number FROM {{table}} WHERE `fleet_owner`='{$user->data['id']}'", 'fleets'));
$ActualFleets = $FlyingFleets["Number"];
$fleetmax = $user->data[$resource[108]] + 1;
if ($user->data['rpg_admiral'] > time()) $fleetmax += 2;
if ($fleetmax <= $ActualFleets) {
	message("Все слоты флота заняты", "Ошибка", "?set=fleet", 2);
}

if ($_POST['resource1'] + $_POST['resource2'] + $_POST['resource3'] < 1 AND $_POST['mission'] == 3) {
	message("<font color=\"lime\"><b>Нет сырья для транспорта!</b></font>", $lang['type_mission'][3], "?set=fleet", 2);
}
if ($_POST['mission'] != 15) 
{
	if (!isset($TargetPlanet['id_owner']) AND $_POST['mission'] < 7) {
		message ("<font color=\"red\"><b>Планеты не существует!</b></font>", 'Ошибка', "?set=fleet", 2);
	}
	if (isset($TargetPlanet['id_owner']) AND ($_POST['mission'] == 7 || $_POST['mission'] == 10)) {
		message ("<font color=\"red\"><b>Место занято</b></font>", 'Ошибка', "?set=fleet", 2);
	}
	if (($HeDBRec['id'] != $user->data['id'] && $HeDBRec['id'] != 1 && $user->data['id'] != 1) AND $_POST['mission'] == 4) {
		message ("<font color=\"red\"><b>Выполнение данной миссии невозможно!</b></font>", 'Ошибка', "?set=fleet", 2);
	}
	if ($TargetPlanet['ally_deposit'] == 0 && $HeDBRec['id'] != $user->data['id'] && $_POST['mission'] == 5) {
		message ("<font color=\"red\"><b>На планете нет склада альянса!</b></font>", 'Ошибка', "?set=fleet", 2);
	}
	if ($_POST['mission'] == 5) {
		$friend = db::query("SELECT id FROM {{table}} WHERE (sender = ".$user->data['id']." AND owner = ".$HeDBRec['id'].") OR (owner = ".$user->data['id']." AND sender = ".$HeDBRec['id'].") AND active = 1 LIMIT 1", "buddy", true);
		if ($HeDBRec['ally_id'] != $user->data['ally_id'] && !isset($friend['id'])) {
			message ("<font color=\"red\"><b>Нельзя охранять вражеские планеты!</b></font>", 'Ошибка', "?set=fleet", 2);
		}
	}
	if ($TargetPlanet['id_owner'] == $user->data['id'] && $_POST['mission'] == 1) {
		message ("<font color=\"red\"><b>Невозможно атаковать самого себя!</b></font>", 'Ошибка', "?set=fleet", 2);
	}
	if ($TargetPlanet['id_owner'] == $user->data['id'] && $_POST['mission'] == 6) {
		message ("<font color=\"red\"><b>Невозможно шпионить самого себя!</b></font>", 'Ошибка', "?set=fleet", 2);
	}
	if (($TargetPlanet['id_owner'] != $user->data['id'] && $HeDBRec['id'] != 1 && $user->data['id'] != 1) && $_POST['mission'] == 4) {
		message ("<font color=\"red\"><b>Выполнение данной миссии невозможно!</b></font>", 'Ошибка', "?set=fleet", 2);
	}
}

$missiontype = array(
	1 => $lang['type_mission'][1],
	2 => $lang['type_mission'][2],
	3 => $lang['type_mission'][3],
	4 => $lang['type_mission'][4],
	5 => $lang['type_mission'][5],
	6 => $lang['type_mission'][6],
	7 => $lang['type_mission'][7],
	8 => $lang['type_mission'][8],
	9 => $lang['type_mission'][9],
	10 => $lang['type_mission'][10],
	15 => $lang['type_mission'][15],
);

$speed_possible = array(10, 9, 8, 7, 6, 5, 4, 3, 2, 1);

$AllFleetSpeed  = GetFleetMaxSpeed ($fleetarray, 0, $user);
$GenFleetSpeed  = $_POST['speed'];
$SpeedFactor    = GetGameSpeedFactor();
$MaxFleetSpeed  = min($AllFleetSpeed);

if (!in_array($GenFleetSpeed, $speed_possible)) {
	message ("<font color=\"red\"><b>Читеришь со скоростью?</b></font>", 'Ошибка', "?set=fleet", 2);
}
if (!$planettype) {
	message ("<font color=\"red\"><b>Ошибочный тип планеты!</b></font>", 'Ошибка', "?set=fleet", 2);
}

$error     	= 0;
$errorlist 	= "";
if (!$galaxy || $galaxy > 9 || $galaxy < 1) {
	$error++;
	$errorlist .= $lang['fl_limit_galaxy'];
}
if (!$system || $system > 499 || $system < 1) {
	$error++;
	$errorlist .= $lang['fl_limit_system'];
}
if (!$planet || $planet > 16 || $planet < 1) {
	$error++;
	$errorlist .= $lang['fl_limit_planet'];
}

if ($error > 0) {
	message ("<font color=\"red\"><ul>" . $errorlist . "</ul></font>", 'Ошибка', "?set=fleet", 2);
}

if (!isset($fleetarray)) {
	message ("<font color=\"red\"><b>". $lang['fl_no_fleetarray'] ."</b></font>", 'Ошибка', "?set=fleet", 2);
}

$distance      = GetTargetDistance ( $planetrow->data['galaxy'], $galaxy, $planetrow->data['system'], $system, $planetrow->data['planet'], $planet );
$duration      = GetMissionDuration ( $GenFleetSpeed, $MaxFleetSpeed, $distance, $SpeedFactor );
$consumption   = GetFleetConsumption ( $fleetarray, $SpeedFactor, $duration, $distance, $MaxFleetSpeed, $user );

if ($fleet_group_mr > 0) {
	// Вычисляем время самого медленного флота в совместной атаке
	$flet = db::query("SELECT fleet_id, fleet_start_time, fleet_end_time FROM {{table}} WHERE fleet_group = '".$fleet_group_mr."'", 'fleets');
	$ttt = $duration + time();
	$arrr = array();
	$i = 0;
	while($flt = db::fetch_assoc($flet)){
		$i++;
		if ($flt['fleet_start_time'] > $ttt) $ttt = $flt['fleet_start_time'];
		$arrr[$i]['id'] = $flt['fleet_id'];
		$arrr[$i]['start'] = $flt['fleet_start_time'];
		$arrr[$i]['end'] = $flt['fleet_end_time'];
	}
}

if ($fleet_group_mr > 0)
	$fleet['start_time'] = $ttt;
else
	$fleet['start_time'] = $duration + time();

if ($_POST['mission'] == 15) {
	$StayDuration    = intval($_POST['expeditiontime']) * 3600;
	$StayTime        = $fleet['start_time'] + intval($_POST['expeditiontime']) * 3600;
} else {
	$StayDuration    = 0;
	$StayTime        = 0;
}

$FleetStorage        = 0;
$FleetShipCount      = 0;
$fleet_array         = "";
$FleetSubQRY         = "";

foreach ($fleetarray as $Ship => $Count) {
	$Count = intval($Count);

    if ($Ship == 202 || $Ship == 203)
        $FleetStorage += round($CombatCaps[$Ship]['capacity'] * (1 + $user->data['fleet_'.$Ship] * 0.05)) * $Count;
    else
        $FleetStorage += $CombatCaps[$Ship]['capacity'] * $Count;

	$FleetShipCount  	+= $Count;
	$fleet_array     	.= (isset($user->data['fleet_'.$Ship])) ? $Ship .",". $Count ."!".$user->data['fleet_'.$Ship].";" : $Ship .",". $Count ."!0;";
	$FleetSubQRY     	.= "`".$resource[$Ship] . "` = `" . $resource[$Ship] . "` - " . $Count . " , ";
}

$FleetStorage        	-= $consumption;
$StorageNeeded	= 0;

if ($_POST['resource1'] < 1) {
	$TransMetal	= 0;
} else {
	$TransMetal	= intval($_POST['resource1']);
	$StorageNeeded  	+= $TransMetal;
}
if ($_POST['resource2'] < 1) {
	$TransCrystal    	= 0;
} else {
	$TransCrystal    	= intval($_POST['resource2']);
	$StorageNeeded  	+= $TransCrystal;
}
if ($_POST['resource3'] < 1) {
	$TransDeuterium  	= 0;
} else {
	$TransDeuterium  	= intval($_POST['resource3']);
	$StorageNeeded  	+= $TransDeuterium;
}

$TotalFleetCons = 0;

if ($_POST['mission'] == 5) {

	$StayArrayTime = array(0, 1, 2, 4, 8, 16, 32);

	if (!isset($_POST['holdingtime']) || !in_array($_POST['holdingtime'], $StayArrayTime))
		$_POST['holdingtime'] = 0;

	if ($user->data['rpg_meta'] > time())
		$FleetStayConsumption = ceil(GetFleetStay($fleetarray) * 0.9);
	else
		$FleetStayConsumption = GetFleetStay($fleetarray);
	
	$FleetStayAll = $FleetStayConsumption * intval($_POST['holdingtime']);
	if ($FleetStayAll >= ($planetrow->data['deuterium'] - $TransDeuterium))
		$TotalFleetCons = $planetrow->data['deuterium'] - $TransDeuterium;
	else
		$TotalFleetCons = $FleetStayAll;

	if ($FleetStorage < $TotalFleetCons)  $TotalFleetCons = $FleetStorage;

	$FleetStayTime = round(($TotalFleetCons / $FleetStayConsumption) * 3600);

	$StayDuration    = $FleetStayTime;
	$StayTime        = $fleet['start_time'] + $FleetStayTime;
}
if ($fleet_group_mr > 0)
	$fleet['end_time']   = $StayDuration + $duration + $ttt;
else
	$fleet['end_time']   = $StayDuration + (2 * $duration) + time();

$StockMetal      	= $planetrow->data['metal'];
$StockCrystal    	= $planetrow->data['crystal'];
$StockDeuterium  	= $planetrow->data['deuterium'];
$StockDeuterium 	-= $consumption;

$StockOk         = false;
if ($StockMetal >= $TransMetal) {
	if ($StockCrystal >= $TransCrystal) {
		if ($StockDeuterium >= $TransDeuterium) {
			$StockOk         = true;
		}
	}
}
if ( !$StockOk && $TargetPlanet['id_owner'] != 1) {
	message ("<font color=\"red\"><b>". $lang['fl_noressources'] . pretty_number($consumption) ."</b></font>", 'Ошибка', "?set=fleet", 2);
}
if ( $StorageNeeded > $FleetStorage) {
	message ("<font color=\"red\"><b>". $lang['fl_nostoragespa'] . pretty_number($StorageNeeded - $FleetStorage) ."</b></font>", 'Ошибка', "?set=fleet", 2);
}


// Баш контроль
if ($_POST['mission'] == 1){

	$night_time = mktime (0, 0, 0, date('m', time()), date('d', time()), date('Y', time()) );

	$log = db::query("SELECT kolvo FROM {{table}} WHERE `s_id` = '{$user->data['id']}' AND `mission` = 1 AND e_galaxy = ".$TargetPlanet['galaxy']." AND e_system = ".$TargetPlanet['system']." AND e_planet = ".$TargetPlanet['planet']." AND time > ".$night_time."", "logs", true);

	if ( $log['kolvo'] != "" && $log['kolvo'] > 2 && $ad['type'] != 3 )
		message ("<font color=\"red\"><b>Баш-контроль. Лимит ваших нападений на планету исчерпан.</b></font>", 'Ошибка', "?set=fleet", 2);

	if ( $log['kolvo'] != "" )
		db::query("UPDATE {{table}} SET kolvo = kolvo + 1 WHERE `s_id` = '{$user->data['id']}' AND `mission` = 1 AND e_galaxy = ".$TargetPlanet['galaxy']." AND e_system = ".$TargetPlanet['system']." AND e_planet = ".$TargetPlanet['planet']." AND time > ".$night_time."", "logs");
	else
		db::query("INSERT INTO {{table}} VALUES (1, ".time().", 1, ".$user->data['id'].", ".$planetrow->data['galaxy'].", ".$planetrow->data['system'].", ".$planetrow->data['planet'].", ".$TargetPlanet['id_owner'].", ".$TargetPlanet['galaxy'].", ".$TargetPlanet['system'].", ".$TargetPlanet['planet'].")" , "logs");

}
//

// Увод флота
//$fleets_num = db::query("SELECT fleet_id FROM {{table}} WHERE fleet_mission = '1' AND fleet_end_galaxy = ".$planetrow->data['galaxy']." AND fleet_end_system = ".$planetrow->data['system']." AND fleet_end_planet = ".$planetrow->data['planet']." AND fleet_end_type = ".$planetrow->data['planet_type']." AND fleet_start_time < ".(time() + 6)."", "fleets");

//if (db::num_rows($fleets_num) > 0)
//		message ("<font color=\"red\"><b>Ваш флот не может взлететь из-за находящегося по близости от орбиты планеты атакующего флота.</b></font>", 'Ошибка', "fleet." . $phpEx, 2);
//

if ($fleet_group_mr > 0 && $i > 0 && $ttt >0) {
	foreach ($arrr AS $id => $row){
		$end = $ttt + $row['end'] - $row['start'];
		db::query("UPDATE {{table}} SET fleet_start_time = ".$ttt.", fleet_end_time = ".$end.", fleet_time = ".$ttt." WHERE fleet_id = '".$row['id']."'", 'fleets');
	}
}

//if ($_POST['mission'] == 3) {
//	if ($MyGameLevel < $HeGameLevel && $user->data['id'] != $TargetPlanet['id_owner']) {
//		db::query("INSERT INTO {{table}} VALUES ('".time()."', '".$user->data['id']."', 's:[".$planetrow->data['galaxy'].":".$planetrow->data['system'].":".$planetrow->data['planet']."(".$planetrow->data['planet_type'].")];e:[".$galaxy.":".$system.":".$planet."(".$planettype.")];f:[".$fleet_array."];m:".$TransMetal.";c:".$TransCrystal.";d:".$TransDeuterium.";', '".$TargetPlanet['id_owner']."')", "mults");
//		$str_error = "Сделана попытка прокачки. Данные вашего флота отправлены операторам на рассмотрение.";
//	}
//}

if ($TargetPlanet['id_owner'] == 1) {
	$fleet['start_time'] 	= time() + 30;
	$fleet['end_time'] 		= time() + 60;
	$consumption			= 0;
}

if ($_POST['mission'] == 15 && $user->data['tutorial'] == 7 && $user->data['tutorial_value'] == 0)
    db::query("UPDATE {{table}} SET tutorial_value = 1 WHERE id = ".$user->data['id'].";", "users");
if ($_POST['mission'] == 8 && $user->data['tutorial'] == 9 && $user->data['tutorial_value'] == 0)
    db::query("UPDATE {{table}} SET tutorial_value = 1 WHERE id = ".$user->data['id'].";", "users");
if ($_POST['mission'] == 6 && $user->data['tutorial'] == 6 && $user->data['tutorial_value'] == 0)
    db::query("UPDATE {{table}} SET tutorial_value = 1 WHERE id = ".$user->data['id'].";", "users");

if ($_POST['mission'] == 1) {
    $raunds = (isset($_POST['raunds'])) ? intval($_POST['raunds']) : 6;
    $raunds = ($raunds < 6 || $raunds > 10) ? 6 : $raunds;
} else
    $raunds = 0;

$QryInsertFleet  = "INSERT INTO {{table}} SET ";
$QryInsertFleet .= "`fleet_owner` = '". $user->data['id'] ."', ";
$QryInsertFleet .= "`fleet_owner_name` = '". $planetrow->data['name'] ."', ";
$QryInsertFleet .= "`fleet_mission` = '". $_POST['mission'] ."', ";
$QryInsertFleet .= "`fleet_array` = '". $fleet_array ."', ";
$QryInsertFleet .= "`fleet_start_time` = '". $fleet['start_time'] ."', ";
$QryInsertFleet .= "`fleet_start_galaxy` = '". $planetrow->data['galaxy'] ."', ";
$QryInsertFleet .= "`fleet_start_system` = '". $planetrow->data['system'] ."', ";
$QryInsertFleet .= "`fleet_start_planet` = '". $planetrow->data['planet'] ."', ";
$QryInsertFleet .= "`fleet_start_type` = '". $planetrow->data['planet_type'] ."', ";
$QryInsertFleet .= "`fleet_end_time` = '". $fleet['end_time'] ."', ";
$QryInsertFleet .= "`fleet_end_stay` = '". $StayTime ."', ";
$QryInsertFleet .= "`fleet_end_galaxy` = '". $galaxy ."', ";
$QryInsertFleet .= "`fleet_end_system` = '". $system ."', ";
$QryInsertFleet .= "`fleet_end_planet` = '". $planet ."', ";
$QryInsertFleet .= "`fleet_end_type` = '". $planettype ."', ";
$QryInsertFleet .= "`fleet_resource_metal` = '". $TransMetal ."', ";
$QryInsertFleet .= "`fleet_resource_crystal` = '". $TransCrystal ."', ";
$QryInsertFleet .= "`fleet_resource_deuterium` = '". $TransDeuterium ."', ";
$QryInsertFleet .= "`fleet_target_owner` = '". $TargetPlanet['id_owner'] ."', ";
$QryInsertFleet .= "`fleet_target_owner_name` = '". $TargetPlanet['name'] ."', ";
$QryInsertFleet .= "`fleet_group` = '". $fleet_group_mr ."', ";
$QryInsertFleet .= "`raunds` = '". $raunds ."', ";
$QryInsertFleet .= "`start_time` = '". time() ."', fleet_time = '". $fleet['start_time'] ."';";
db::query( $QryInsertFleet, 'fleets');


$planetrow->data["metal"]     	-= $TransMetal;
$planetrow->data["crystal"]   	-= $TransCrystal;
$planetrow->data["deuterium"] 	-= $TransDeuterium;
$planetrow->data["deuterium"] 	-= $consumption + $TotalFleetCons;

$QryUpdatePlanet  = "UPDATE {{table}} SET ";
$QryUpdatePlanet .= $FleetSubQRY;
$QryUpdatePlanet .= "`metal` = '". $planetrow->data["metal"] ."', ";
$QryUpdatePlanet .= "`crystal` = '". $planetrow->data["crystal"] ."', ";
$QryUpdatePlanet .= "`deuterium` = '". $planetrow->data["deuterium"] ."' ";
$QryUpdatePlanet .= "WHERE ";
$QryUpdatePlanet .= "`id` = '". $planetrow->data['id'] ."'";
db::query ($QryUpdatePlanet, "planets");

if (isset($str_error))
	$lang['fl_fleet_send'] = $str_error;

$page = "<br><div><center>";
$page .= "<table border=\"0\" cellpadding=\"0\" cellspacing=\"1\" width=\"519\">";
$page .= "<tr height=\"20\">";
$page .= "<td class=\"c\" colspan=\"2\"><span class=\"success\">". $lang['fl_fleet_send'] ."</span></td>";
$page .= "</tr><tr height=\"20\">";
$page .= "<th>". $lang['fl_mission'] ."</th>";
$page .= "<th>". $missiontype[$_POST['mission']] ."</th>";
$page .= "</tr><tr height=\"20\">";
$page .= "<th>". $lang['fl_dist'] ."</th>";
$page .= "<th>". pretty_number($distance) ."</th>";
$page .= "</tr><tr height=\"20\">";
$page .= "<th>". $lang['fl_speed'] ."</th>";
$page .= "<th>". pretty_number($MaxFleetSpeed) ."</th>";
$page .= "</tr><tr height=\"20\">";
$page .= "<th>". $lang['fl_deute_need'] ."</th>";
$page .= "<th>". pretty_number($consumption) ."</th>";
$page .= "</tr><tr height=\"20\">";
$page .= "<th>". $lang['fl_from'] ."</th>";
$page .= "<th>". $planetrow->data['galaxy'] .":". $planetrow->data['system']. ":". $planetrow->data['planet'] ."</th>";
$page .= "</tr><tr height=\"20\">";
$page .= "<th>". $lang['fl_dest'] ."</th>";
$page .= "<th>". $galaxy .":". $system .":". $planet ."</th>";
$page .= "</tr><tr height=\"20\">";
$page .= "<th>". $lang['fl_time_go'] ."</th>";
$page .= "<th>". datezone("M D d H:i:s", $fleet['start_time']) ."</th>";
$page .= "</tr><tr height=\"20\">";
$page .= "<th>". $lang['fl_time_back'] ."</th>";
$page .= "<th>". datezone("M D d H:i:s", $fleet['end_time']) ."</th>";
$page .= "</tr><tr height=\"20\">";
$page .= "<td class=\"c\" colspan=\"2\">". $lang['fl_title'] ."</td>";


foreach ($fleetarray as $Ship => $Count) {
	$page .= "</tr><tr height=\"20\">";
	$page .= "<th>". $lang['tech'][$Ship] ."</th>";
	$page .= "<th>". pretty_number($Count) ."</th>";
}
$page .= "</tr></table></div></center>";

message ($page, ''.$lang['fl_title'].'', '?set=fleet', '3')


?>
