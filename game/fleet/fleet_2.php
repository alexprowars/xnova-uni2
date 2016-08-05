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
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

if(!defined("INSIDE")) die("attemp hacking");

if ($user->data['urlaubs_modus_time'] > 0) {
	message("Нет доступа!");
}

if ($_POST['crc'] != md5($user->data['id'].'-CHeAT_CoNTROL_Stage_02-'.date("dmY", time()).'-'.$_POST["usedfleet"]))
	message('Ошибка контрольной суммы!');

system::includeLang('fleet');

$galaxy     = intval($_POST['galaxy']);
$system     = intval($_POST['system']);
$planet     = intval($_POST['planet']);
$planettype = intval($_POST['planettype']);
$acs 	    = intval($_POST['acs']);
$YourPlanet = false;
$UsedPlanet = false;
$select       = db::query("SELECT * FROM {{table}} WHERE `galaxy` = '".$galaxy."' AND `system` = '".$system."' AND `planet` = '".$planet."' AND `planet_type` = '".$planettype."'", "planets");

while ($row = db::fetch_array($select)) {
	if ($galaxy == $row['galaxy'] && $system == $row['system'] && $planet == $row['planet'] && $planettype == $row['planet_type']) {
		if ($row['id_owner'] == $user->data['id'] || $row['id_owner'] == 1) {
			$YourPlanet = true;
			$UsedPlanet = true;
		} else {
			$UsedPlanet = true;
		}
		break;
	}
}

$mission       = intval($_POST['target_mission']);
$missiontype 	= array();

if ($planet == 16) {
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

		if ($YourPlanet || $user->data['id'] == 1)
			$missiontype[4] = $lang['type_mission'][4]; // Оставить

		if ($acs > 0 && $UsedPlanet)
			$missiontype[2] = $lang['type_mission'][2]; // Объединить

		if ($planettype == 3 && isset($_POST['ship214']) && $_POST['ship214'] > 0 && !$YourPlanet && $UsedPlanet)
			$missiontype[9] = $lang['type_mission'][9];
	}
}

$fleetarray    = json_decode(base64_decode(str_rot13($_POST["usedfleet"])), true);
$SpeedFactor   = GetGameSpeedFactor();
$AllFleetSpeed = GetFleetMaxSpeed ($fleetarray, 0, $user);
$GenFleetSpeed = intval($_POST['speed']);
$MaxFleetSpeed = min($AllFleetSpeed);

$distance      = GetTargetDistance ( $planetrow->data['galaxy'], $_POST['galaxy'], $planetrow->data['system'], $_POST['system'], $planetrow->data['planet'], $_POST['planet'] );
$duration      = GetMissionDuration ( $GenFleetSpeed, $MaxFleetSpeed, $distance, $SpeedFactor );
$consumption   = GetFleetConsumption ( $fleetarray, $SpeedFactor, $duration, $distance, $MaxFleetSpeed, $user );

$MissionSelector  = "";
if (count($missiontype) > 0) {
	if ($planet == 16) {
		$MissionSelector .= "<tr height=\"20\">";
		$MissionSelector .= "<th style=\"text-align: left !important\">";
		$MissionSelector .= "<input type=\"radio\" name=\"mission\" id=\"m_15\" value=\"15\" checked=\"checked\">". $lang['type_mission'][15] ."<br /><br />";
		$MissionSelector .= "<center><font color=\"red\">". $lang['fl_expe_warning'] ."</font></center>";
		$MissionSelector .= "</th>";
		$MissionSelector .= "</tr>";
	} else {
		foreach ($missiontype as $a => $b) {
			$MissionSelector .= "<tr height=\"20\">";
			$MissionSelector .= "<th style=\"text-align: left !important\">";
			$MissionSelector .= "<input id=\"m_".$a."\" type=\"radio\" name=\"mission\" value=\"".$a."\"". ($mission == $a ? " checked=\"checked\"":"") .">";
			$MissionSelector .= "<label for=\"m_".$a."\">".$b."</label>";
			$MissionSelector .= "</th>";
			$MissionSelector .= "</tr>";
		}
	}
} else
	$MissionSelector .= "<tr height=\"20\"><th><font color=\"red\">". $lang['fl_bad_mission'] ."</font></th></tr>";

$TableTitle = "". $_POST['galaxy'] .":". $_POST['system'] .":". $_POST['planet'] ." - ";

if ($_POST["planettype"] == 1) {
	$TableTitle .= "Планета";
} elseif ($_POST["planettype"] == 2) {
	$TableTitle .= "Поле обломков";
} elseif ($_POST["planettype"] == 3) {
	$TableTitle .= "Луна";
} elseif ($_POST["planettype"] == 5) {
	$TableTitle .= "База";
}

$page  = "<script type=\"text/javascript\" src=\"scripts/flotten.js\">\n</script>";
$page .= "<br><center>";
$page .= "<form action=\"?set=fleet&page=fleet_3\" method=\"post\">\n";
$page .= "<input type=\"hidden\" name=\"thisresource1\"  value=\"". floor($planetrow->data["metal"]) ."\" />\n";
$page .= "<input type=\"hidden\" name=\"thisresource2\"  value=\"". floor($planetrow->data["crystal"]) ."\" />\n";
$page .= "<input type=\"hidden\" name=\"thisresource3\"  value=\"". floor($planetrow->data["deuterium"]) ."\" />\n";
$page .= "<input type=\"hidden\" name=\"consumption\"    value=\"". $consumption ."\" />\n";
$page .= "<input type=\"hidden\" name=\"dist\"           value=\"". $distance ."\" />\n";
$page .= "<input type=\"hidden\" name=\"acs\"            value=\"". $acs ."\" />\n";
$page .= "<input type=\"hidden\" name=\"thisgalaxy\"     value=\"". $planetrow->data['galaxy'] ."\" />";
$page .= "<input type=\"hidden\" name=\"thissystem\"     value=\"". $planetrow->data['system'] ."\" />";
$page .= "<input type=\"hidden\" name=\"thisplanet\"     value=\"". $planetrow->data['planet'] ."\" />";
$page .= "<input type=\"hidden\" name=\"galaxy\"         value=\"". $_POST["galaxy"] ."\" />\n";
$page .= "<input type=\"hidden\" name=\"system\"         value=\"". $_POST["system"] ."\" />\n";
$page .= "<input type=\"hidden\" name=\"planet\"         value=\"". $_POST["planet"] ."\" />\n";
$page .= "<input type=\"hidden\" name=\"planettype\"     value=\"". $_POST["planettype"] ."\" />\n";
$page .= "<input type=\"hidden\" name=\"speed\"          value=\"". $_POST['speed'] ."\" />\n";
$page .= "<input type=\"hidden\" name=\"speedfactor\"    value=\"". $_POST["speedfactor"] ."\" />\n";
$page .= "<input type=\"hidden\" name=\"usedfleet\"      value=\"". $_POST["usedfleet"] ."\" />\n";
$page .= "<input type=\"hidden\" name=\"crc\"            value=\"". md5($user->data['id'].'-CHeAT_CoNTROL_Stage_03-'.date("dmY", time()).'-'.$_POST["usedfleet"]) ."\" />\n";
$page .= "<input type=\"hidden\" name=\"maxepedition\"   value=\"". $_POST['maxepedition'] ."\" />\n";
$page .= "<input type=\"hidden\" name=\"curepedition\"   value=\"". $_POST['curepedition'] ."\" />\n";
foreach ($fleetarray as $Ship => $Count) {
	$page .= "<input type=\"hidden\" name=\"ship". $Ship ."\"        value=\"". $Count ."\" />\n";
	$page .= "<input type=\"hidden\" name=\"stay". $Ship ."\"        value=\"". $CombatCaps[$Ship]['stay'] ."\" />\n";
	$page .= "<input type=\"hidden\" name=\"capacity". $Ship ."\"    value=\"";

    if ($Ship == 202 || $Ship == 203)
        $page .= round($CombatCaps[$Ship]['capacity'] * (1 + $user->data['fleet_'.$Ship] * 0.05));
    else
        $page .= $CombatCaps[$Ship]['capacity'];

	$page .= "\" />\n<input type=\"hidden\" name=\"consumption". $Ship ."\" value=\"". GetShipConsumption ( $Ship, $user ) ."\" />\n";
	$page .= "<input type=\"hidden\" name=\"speed". $Ship ."\"       value=\"". GetFleetMaxSpeed ( "", $Ship, $user ) ."\" />\n";
}
$page .= "<table border=\"0\" cellpadding=\"0\" cellspacing=\"1\" width=\"519\">\n";
$page .= "<tbody>\n";
$page .= "<tr align=\"left\" height=\"20\">\n";
$page .= "<td class=\"c\" colspan=\"2\">". $TableTitle ."</td>\n";
$page .= "</tr>\n";
$page .= "<tr align=\"left\" valign=\"top\">\n";
$page .= "<th width=\"50%\">\n";
$page .= "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"259\">\n";
$page .= "<tbody>\n";
$page .= "<tr height=\"20\">\n";
$page .= "<td class=\"c\" colspan=\"2\">". $lang['fl_mission'] ."</td>\n";
$page .= "</tr>\n";
$page .= $MissionSelector;
$page .= "</tbody>\n";
$page .= "</table>\n";
$page .= "</th>\n";
$page .= "<th>\n";
$page .= "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"259\">\n";
$page .= "<tbody>\n";
$page .= "<tr height=\"20\">\n";
$page .= "<td colspan=\"3\" class=\"c\">". $lang['fl_ressources'] ."</td>\n";
$page .= "</tr><tr height=\"20\">\n";
$page .= "<th>". $lang['Metal'] ."</th>\n";
$page .= "<th><a href=\"javascript:maxResource('1');\">". $lang['fl_selmax'] ."</a></th>\n";
$page .= "<th><input name=\"resource1\" alt=\"". $lang['Metal'] ." ". floor($planetrow->data["metal"]) ."\" size=\"10\" onchange=\"calculateTransportCapacity();\" type=\"text\"></th>\n";
$page .= "</tr><tr height=\"20\">\n";
$page .= "<th>". $lang['Crystal'] ."</th>\n";
$page .= "<th><a href=\"javascript:maxResource('2');\">". $lang['fl_selmax'] ."</a></th>\n";
$page .= "<th><input name=\"resource2\" alt=\"". $lang['Crystal'] ." ". floor($planetrow->data["crystal"]) ."\" size=\"10\" onchange=\"calculateTransportCapacity();\" type=\"text\"></th>\n";
$page .= "</tr><tr height=\"20\">\n";
$page .= "<th>". $lang['Deuterium'] ."</th>\n";
$page .= "<th><a href=\"javascript:maxResource('3');\">". $lang['fl_selmax'] ."</a></th>\n";
$page .= "<th><input name=\"resource3\" alt=\"". $lang['Deuterium'] ." ". floor($planetrow->data["deuterium"]) ."\" size=\"10\" onchange=\"calculateTransportCapacity();\" type=\"text\"></th>\n";
$page .= "</tr><tr height=\"20\">\n";
$page .= "<th>". $lang['fl_space_left'] ."</th>\n";
$page .= "<th colspan=\"2\"><div id=\"remainingresources\">-</div></th>\n";
$page .= "</tr><tr height=\"20\">\n";
$page .= "<th colspan=\"3\"><a href=\"javascript:maxResources()\">". $lang['fl_allressources'] ."</a></th>\n";
$page .= "</tr><tr height=\"20\">\n";
$page .= "<th colspan=\"3\">&nbsp;</th>\n";
$page .= "</tr>\n";
if ($planet == 16) {
	$page .= "<tr height=\"20\">";
	$page .= "<td class=\"c\" colspan=\"3\">Время экспедиции</td>";
	$page .= "</tr>";
	$page .= "<tr height=\"20\">";
	$page .= "<th colspan=\"3\">";
	$page .= "<select name=\"expeditiontime\" >";
    for($i = 1; $i <= round($user->data[$resource[124]] / 2) + 1; $i++){
        $page .= "<option value=\"".$i."\">".$i." ч.</option>";
    }
	$page .= "</select></th></tr>";
} elseif (isset($missiontype[5])) {
	$page .= "<tr height=\"20\">";
	$page .= "<td class=\"c\" colspan=\"3\">Оставаться часов на орбите</td>";
	$page .= "</tr>";
	$page .= "<tr height=\"20\">";
	$page .= "<th colspan=\"3\">";
	$page .= "<select name=\"holdingtime\" >";
	$page .= "<option value=\"0\">0</option>";
	$page .= "<option value=\"1\">1</option>";
	$page .= "<option value=\"2\">2</option>";
	$page .= "<option value=\"4\">4</option>";
	$page .= "<option value=\"8\">8</option>";
	$page .= "<option value=\"16\">16</option>";
	$page .= "<option value=\"32\">32</option>";
	$page .= "</select>";
	$page .= "</th>";
	$page .= "</tr>";
}
if (isset($missiontype[1])) {
	$page .= "<tr height=\"20\"><td class=\"c\" colspan=\"3\">Кол-во раундов боя</td></tr>";
	$page .= "<tr height=\"20\">";
	$page .= "<th colspan=\"3\">";
	$page .= "<select name=\"raunds\" >";
	$page .= "<option value=\"6\" selected>6</option>";
	$page .= "<option value=\"7\">7</option>";
	$page .= "<option value=\"8\">8</option>";
	$page .= "<option value=\"9\">9</option>";
	$page .= "<option value=\"10\">10</option>";
	$page .= "</select></th></tr>";
}
$page .= "</tbody>\n";
$page .= "</table>\n";
$page .= "</th>\n";
if (count($missiontype) > 0) {
    $page .= "</tr><tr height=\"20\">\n";
    $page .= "<th colspan=\"2\"><input accesskey=\"z\" value=\"". $lang['fl_continue'] ."\" type=\"submit\"></th>\n";
    $page .= "</tr>\n";
}
$page .= "</tbody>\n";
$page .= "</table>\n";
$page .= "</form></center>\n";

display($page, $lang['fl_title']);

?>
