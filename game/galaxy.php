<?php

/**
 * @author AlexPro
 * @copyright 2008 - 2011 XNova Game Group
 * @var $user user
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

if(!defined("INSIDE")) die("attemp hacking");

system::includeLang('galaxy');

$fleetmax      = $user->data['computer_tech'] + 1;
if ($user->data['rpg_admiral'] > time())
	$fleetmax += 2; 

$maxfleet_count	= db::query("SELECT COUNT(*) AS num FROM {{table}} WHERE `fleet_owner` = '". $user->data['id'] ."';", 'fleets', true);
$maxfleet_count = $maxfleet_count['num'];

$UserPoints    = db::query("SELECT `total_points` FROM {{table}} WHERE `stat_type` = '1' AND `stat_code` = '1' AND `id_owner` = '". $user->data['id'] ."';", 'statpoints', true);

function CheckAbandonMoonState (&$lunarow) {
	if ($lunarow['luna_destruyed'] <= time()) {
		db::query("DELETE FROM {{table}} WHERE `id` = ".$lunarow['luna_id']."", 'planets');
		db::query("UPDATE {{table}} SET parent_planet = '0' WHERE `parent_planet` = ".$lunarow['luna_id'].";", "planets");
		$lunarow['id_luna'] = 0;
	}
}

function CheckAbandonPlanetState (&$planet) {
	if ($planet['destruyed'] <= time()) {
		db::query("DELETE FROM {{table}} WHERE id = ".$planet['id_planet'].";", 'planets');
		if ($planet['parent_planet'] != 0)
			db::query("DELETE FROM {{table}} WHERE id = ".$planet['parent_planet'].";", 'planets');
	}
}

function ShowGalaxyMISelector ( $Galaxy, $System, $Planet, $Current, $MICount ) {
	global $lang;

	$Result  = "<form action=\"?set=raketenangriff&c=".$Current."&mode=2&galaxy=".$Galaxy."&system=".$System."&planet=".$Planet."\" method=\"POST\">";
	$Result .= "<table border=\"0\">";
	$Result .= "<tr>";
	$Result .= "<td class=\"c\" colspan=\"3\">";
	$Result .= $lang['gm_launch'] ." [".$Galaxy.":".$System.":".$Planet."]";
	$Result .= "</td>";
	$Result .= "</tr>";
	$Result .= "<tr>";
	$String  = sprintf($lang['gm_restmi'], $MICount);
	$Result .= "<td class=\"c\">".$String." <input type=\"text\" name=\"SendMI\" size=\"2\" maxlength=\"7\" /></td>";
	$Result .= "<td class=\"c\">".$lang['gm_target']." <select name=\"Target\">";
	$Result .= "<option value=\"all\" selected>".$lang['gm_all']."</option>";
	$Result .= "<option value=\"0\">".$lang['tech'][401]."</option>";
	$Result .= "<option value=\"1\">".$lang['tech'][402]."</option>";
	$Result .= "<option value=\"2\">".$lang['tech'][403]."</option>";
	$Result .= "<option value=\"3\">".$lang['tech'][404]."</option>";
	$Result .= "<option value=\"4\">".$lang['tech'][405]."</option>";
	$Result .= "<option value=\"5\">".$lang['tech'][406]."</option>";
	$Result .= "<option value=\"6\">".$lang['tech'][407]."</option>";
	$Result .= "<option value=\"7\">".$lang['tech'][408]."</option>";
	$Result .= "</select>";
	$Result .= "</td>";
	$Result .= "</tr>";
	$Result .= "<tr>";
	$Result .= "<td class=\"c\" colspan=\"2\"><input type=\"submit\" name=\"aktion\" value=\"".$lang['gm_send']."\"></td>";
	$Result .= "</tr>";
	$Result .= "</table>";
	$Result .= "</form>";

	return $Result;
}

if (!isset($mode)) {
	if (isset($_GET['mode'])) {
		$mode          = intval($_GET['mode']);
	} else {
		$mode          = 0;
	}
}

$check_center 	= md5($user->data['id'].$planetrow->data['id'].'C');
$check_left 	= md5($user->data['id'].$planetrow->data['id'].'L');
$check_right 	= md5($user->data['id'].$planetrow->data['id'].'R');

if ($mode == 0) 
{
	$galaxy 	= $planetrow->data['galaxy'];
	$system 	= $planetrow->data['system'];
	$planet 	= $planetrow->data['planet'];
}
elseif ($mode == 1) 
{
	if (isset($_POST["galaxyLeft"])) {
		if ($_POST["galaxy"] < 1) {
			$galaxy = 1;
		} elseif ($_POST["galaxy"] == 1) {
			$galaxy = 1;
		} else {
			$galaxy = intval($_POST["galaxy"]) - 1;
		}
	} elseif (isset($_POST["galaxyRight"])) {
		if ($_POST["galaxy"] > MAX_GALAXY_IN_WORLD OR $_POST["galaxyRight"] > MAX_GALAXY_IN_WORLD) {
			$galaxy = MAX_GALAXY_IN_WORLD;
		} elseif ($_POST["galaxy"] == MAX_GALAXY_IN_WORLD) {
			$galaxy = MAX_GALAXY_IN_WORLD;
		} else {
			$galaxy = intval($_POST["galaxy"]) + 1;
		}
	} else {
		if ($_POST["galaxy"] < 1)
			$galaxy = 1;
		elseif ($_POST["galaxy"] > MAX_GALAXY_IN_WORLD)
			$galaxy = MAX_GALAXY_IN_WORLD;
		else
			$galaxy = intval($_POST["galaxy"]);
	}

	if (isset($_POST["systemLeft"])) {
		if ($_POST["system"] < 1) {
			$system = 1;
		} elseif ($_POST["system"] == 1) {
			$system = 1;
		} else {
			$system = intval($_POST["system"]) - 1;
		}
	} elseif (isset($_POST["systemRight"])) {
		if ($_POST["system"]      > MAX_SYSTEM_IN_GALAXY OR $_POST["systemRight"] > MAX_SYSTEM_IN_GALAXY) {
			$system = MAX_SYSTEM_IN_GALAXY;
		} elseif ($_POST["system"] == MAX_SYSTEM_IN_GALAXY) {
			$system = MAX_SYSTEM_IN_GALAXY;
		} else {
			$system = intval($_POST["system"]) + 1;
		}
	} else {
		if ($_POST["system"] < 1)
			$system = 1;
		elseif ($_POST["system"] > MAX_SYSTEM_IN_GALAXY)
			$system = MAX_SYSTEM_IN_GALAXY;
		else
			$system = intval($_POST["system"]);
	}
} elseif ($mode == 2) {
	$galaxy 	= intval($_GET['galaxy']);
	$system 	= intval($_GET['system']);
	$planet 	= intval($_GET['planet']);
} elseif ($mode == 3) {
	$galaxy 	= intval($_GET['galaxy']);
	$system 	= intval($_GET['system']);
} else {
	$galaxy 	= 1;
	$system 	= 1;
}

if ((isset($_POST["galaxyLeft"]) || isset($_POST["systemLeft"])) && (!isset($_POST['left']) || $_POST['left'] != $check_left))
	message('Режим бога включен! Приятной игры!', 'Ошибка');
	
if ((isset($_POST["galaxyRight"]) || isset($_POST["systemRight"])) && (!isset($_POST['right']) || $_POST['right'] != $check_right))
	message('Режим бога включен! Приятной игры!', 'Ошибка');

if ($galaxy != $planetrow->data['galaxy'] || $system != $planetrow->data['system']) 
{
    if ($planetrow->data['deuterium'] >= 10) 
	{
        db::query("UPDATE {{table}} SET deuterium = deuterium - 10 WHERE id = ".$planetrow->data['id']."", "planets");
		$planetrow->data['deuterium'] -= 10;
    }
	else
        message('Недостаточно дейтерия для просмотра галактики!', 'Ошибка', '?set=overwiev', 3);
}

if (!isset($_SESSION['fleet_shortcut'])) {

	$array 	= SortUserPlanets($user->data, false);
	$j 		= array();

	while ($a = db::fetch_assoc($array)) {
		$j[] = array(base64_encode($a['name']), $a['galaxy'], $a['system'], $a['planet']);
	}

	$shortcuts = db::query("SELECT fleet_shortcut FROM {{table}} WHERE id = ".$user->data['id'].";", "users_inf", true);

	if (isset($shortcuts['fleet_shortcut'])) {
		$scarray = explode("\r\n", $shortcuts['fleet_shortcut']);

		foreach ($scarray as $a => $b) {
			if ($b != "") {
				$c = explode(',', $b);
				$j[] = array(base64_encode($c[0]), intval($c[1]), intval($c[2]), intval($c[3]));
			}
		}
	}

	$_SESSION['fleet_shortcut'] = json_encode($j);
}

$Phalanx = 0;

if ($planetrow->data['phalanx'] <> 0) {
	$Range = system::GetPhalanxRange ( $planetrow->data['phalanx'] );
	$SystemLimitMin = $planetrow->data['system'] - $Range;
	if ($SystemLimitMin < 1) $SystemLimitMin = 1;
	$SystemLimitMax = $planetrow->data['system'] + $Range;

	if ($system <= $SystemLimitMax && $system >= $SystemLimitMin)
		$Phalanx = 1;
}
	
if ($planetrow->data['interplanetary_misil'] <> 0) {
	if ($galaxy == $planetrow->data['galaxy']) {
		$Range = system::GetMissileRange();
		$SystemLimitMin = $planetrow->data['system'] - $Range;
		if ($SystemLimitMin < 1) {
			$SystemLimitMin = 1;
		}
		$SystemLimitMax = $planetrow->data['system'] + $Range;
		if ($system <= $SystemLimitMax) {
			if ($system >= $SystemLimitMin) {
				$MissileBtn = 1;
			} else {
				$MissileBtn = 0;
			}
		} else {
			$MissileBtn = 0;
		}
	} else {
		$MissileBtn = 0;
	}
} else {
	$MissileBtn = 0;
}

$Destroy = 0;

if ($planetrow->data['dearth_star'] > 0)
	$Destroy = 1;

$page = '';

if ($mode == 2)
{
	$planetrowID = intval($_GET['current']);
	$page .= ShowGalaxyMISelector ( $galaxy, $system, $planet, $planetrow->data['id'], $planetrow->data['interplanetary_misil'] );
}

$page .= "<script type=\"text/javascript\" src=\"scripts/universe_full.js?2\"></script><div id='galaxy'></div>";

$page .= "<script>var Deuterium = '".pretty_number($planetrow->data['deuterium'])."'; var time = ".time()."; var dpath = '".$dpath."'; var user = {id:".$user->data['id'].", phalanx:".$Phalanx.", destroy:".$Destroy.", missile:".$MissileBtn.", total_points:".($UserPoints['total_points']+0).", ally_id:".$user->data['ally_id'].", current_planet:".$user->data['current_planet'].", colonizer:".$planetrow->data['colonizer'].", spy_sonde:".$planetrow->data['spy_sonde'].", recycler:".$planetrow->data['recycler'].", interplanetary_misil:".$planetrow->data['interplanetary_misil'].", fleets: ".$maxfleet_count.", max_fleets: ".$fleetmax."}; var galaxy = ".$galaxy."; var system = ".$system."; var row = new Array(); ";

$page  .= " var fleet_shortcut = new Array(); ";
$array 	= json_decode($_SESSION['fleet_shortcut'], true);

foreach ($array AS $id => $a) {
	$page .= " fleet_shortcut[".$id."] = new Array('".base64_decode($a[0])."', ".$a[1].", ".$a[2].", ".$a[3].", ".(($a[1] == $galaxy && $a[2] == $system) ? 1 : 0)."); ";
}

$page .= "$('#galaxy').append(PrintSelector(fleet_shortcut, '".$check_right."', '".$check_left."', '".$check_center."')); ";

$GalaxyRow = db::query("SELECT p.planet, p.id AS id_planet, p.debris_metal AS metal, p.debris_crystal AS crystal, p.name, p.planet_type, p.destruyed, p.image, p.last_update, p.parent_planet, p2.id AS luna_id, p2.name AS luna_name, p2.destruyed AS luna_destruyed, p2.last_update AS luna_update, p2.diameter AS luna_diameter, p2.temp_min AS luna_temp, u.id AS user_id, u.username, u.race, u.ally_id, u.authlevel, u.onlinetime, u.urlaubs_modus_time, u.banaday, u.avatar, a.ally_name, a.ally_members, a.ally_web, a.ally_tag, ad.type, s.total_rank, s.total_points
		FROM game_planets p 
		LEFT JOIN game_planets p2 ON (p.parent_planet = p2.id AND p.parent_planet != 0) 
		LEFT JOIN game_users u ON (u.id = p.id_owner AND p.id_owner != 0) 
		LEFT JOIN game_alliance a ON (a.id = u.ally_id AND u.ally_id != 0) 
		LEFT JOIN game_alliance_diplomacy ad ON ((ad.a_id = u.ally_id AND ad.d_id = ".$user->data['ally_id'].") AND ad.status = 1 AND u.ally_id != 0)
		LEFT JOIN game_statpoints s ON (s.id_owner = u.id AND s.stat_type = '1' AND s.stat_code = '1') 
		WHERE p.planet_type <> 3 AND p.`galaxy` = '".$galaxy."' AND p.`system` = '".$system."';", '');

while ($row = db::fetch_assoc($GalaxyRow)) {

	if ($row['luna_update'] != "" && $row['luna_update'] > $row['last_update'])
		$row['last_update'] = $row['luna_update'];

	unset($row['luna_update']);
	
	if ($row['destruyed'] != 0 && $row["id_planet"] != '') {
		CheckAbandonPlanetState ($row);
	}
	if ($row["luna_id"] != "" && $row["luna_destruyed"] != 0) {
		CheckAbandonMoonState ($row);
	}

	$online = $row['onlinetime'];

	if ($online < (time()-60 * 60 * 24 * 7) && $online > (time()-60 * 60 * 24 * 28))
		$row['onlinetime'] = 1;
	elseif ($online < (time()-60 * 60 * 24 * 28))
		$row['onlinetime'] = 2;
	else
		$row['onlinetime'] = 0;

	if ($row['urlaubs_modus_time'] > 0)
		$row['urlaubs_modus_time'] = 1;

	if ($row['last_update']  > (time()-59 * 60)) {
		$row['last_update'] = floor((time() - $row['last_update'])/60);
	} else
		$row['last_update'] = 60;
		
	$page .= 'row['.$row['planet'].'] = {';
	foreach ($row AS $key => $value) {
		$page .= $key.':';
		if (is_numeric($value))
			$page .= $value;
		else
			$page .= '\''.$value.'\'';
			
		$page .= ', ';
	}
	$page .= '\'end\': 1}; ';
}
	
$page .= "$('#galaxy').append(PrintRow());</script>";

display ($page, 'Галактика', false);

?>
