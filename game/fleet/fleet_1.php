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
	
	if ($_POST['crc'] != md5($user->data['id'].'-CHeAT_CoNTROL_Stage_01-'.date("dmY", time())))
		message('Ошибка контрольной суммы!');

	system::includeLang('fleet');

	$speed = array(
		10 => 100,
		9 => 90,
		8 => 80,
		7 => 70,
		6 => 60,
		5 => 50,
		4 => 40,
		3 => 30,
		2 => 20,
		1 => 10,
	);

	$g 	= intval($_POST['galaxy']);
	$s 	= intval($_POST['system']);
	$p 	= intval($_POST['planet']);
	$t 	= intval($_POST['planet_type']);

	if (!$g) {
		$g = $planetrow->data['galaxy'];
	}
	if (!$s) {
		$s = $planetrow->data['system'];
	}
	if (!$p) {
		$p = $planetrow->data['planet'];
	}
	if (!$t) {
		$t = 1;
	}

	$FleetHiddenBlock  		= "";
 	$fleet['fleetlist'] 	= "";
	$fleet['amount'] 		= 0;

	foreach ($reslist['fleet'] as $n => $i) {
		if (isset($_POST["ship".$i]) && $i > 200 && $i < 300 && intval($_POST["ship".$i]) > 0) {
			if (intval($_POST["ship".$i]) > $planetrow->data[$resource[$i]]) {
				$speedalls[$i] = GetFleetMaxSpeed ( "", $i, $user );
			} else {
				$fleet['fleetarray'][$i]   = intval($_POST["ship".$i]);
				$fleet['fleetlist']       .= $i . "," .intval($_POST["ship".$i]) . ";";
				$fleet['amount']       	  += intval($_POST["ship".$i]);
				$FleetHiddenBlock         .= "<input type=\"hidden\" name=\"consumption". $i ."\" value=\"". GetShipConsumption ( $i, $user ) ."\" />";
				$FleetHiddenBlock         .= "<input type=\"hidden\" name=\"speed". $i ."\"       value=\"". GetFleetMaxSpeed ( "", $i, $user ) ."\" />";
				$FleetHiddenBlock         .= "<input type=\"hidden\" name=\"capacity". $i ."\"    value=\"";

                if ($i == 202 || $i == 203)
                    $FleetHiddenBlock         .= round($CombatCaps[$i]['capacity'] * (1 + $user->data['fleet_'.$i] * 0.05));
                else
                    $FleetHiddenBlock         .= $CombatCaps[$i]['capacity'];

                $FleetHiddenBlock         .= "\" />";
				$FleetHiddenBlock         .= "<input type=\"hidden\" name=\"ship". $i ."\"        value=\"". intval($_POST["ship".$i]) ."\" />";
				$speedalls[$i]             = GetFleetMaxSpeed ( "", $i, $user );
			}
		}
	}

	if (!$fleet['fleetlist']) {
		message($lang['fl_unselectall'], $lang['fl_error'], "?set=fleet", 1);
	}

	$page = "<script type=\"text/javascript\" src=\"scripts/flotten.js\"></script>";
	$page .= "<form action=\"?set=fleet&page=fleet_2\" method=\"post\">";
	$page .= $FleetHiddenBlock;
	$page .= "<input type=\"hidden\" name=\"usedfleet\"      value=\"". str_rot13(base64_encode(json_encode($fleet['fleetarray']))) ."\" />";
	$page .= "<input type=\"hidden\" name=\"thisgalaxy\"     value=\"". $planetrow->data['galaxy'] ."\" />";
	$page .= "<input type=\"hidden\" name=\"thissystem\"     value=\"". $planetrow->data['system'] ."\" />";
	$page .= "<input type=\"hidden\" name=\"thisplanet\"     value=\"". $planetrow->data['planet'] ."\" />";
	$page .= "<input type=\"hidden\" name=\"galaxyend\"      value=\"". intval($_POST['galaxy']) ."\" />";
	$page .= "<input type=\"hidden\" name=\"systemend\"      value=\"". intval($_POST['system']) ."\" />";
	$page .= "<input type=\"hidden\" name=\"planetend\"      value=\"". intval($_POST['planet']) ."\" />";
	$page .= "<input type=\"hidden\" name=\"speedfactor\"    value=\"". GetGameSpeedFactor () ."\" />";
	$page .= "<input type=\"hidden\" name=\"thisresource1\"  value=\"". floor($planetrow->data['metal']) ."\" />";
	$page .= "<input type=\"hidden\" name=\"thisresource2\"  value=\"". floor($planetrow->data['crystal']) ."\" />";
	$page .= "<input type=\"hidden\" name=\"thisresource3\"  value=\"". floor($planetrow->data['deuterium']) ."\" />";

	$page .= "<br><div><center>";
	$page .= "<table width=\"519\" border=\"0\" cellpadding=\"0\" cellspacing=\"1\">";
	$page .= "<tr height=\"20\">";
	$page .= "<td colspan=\"2\" class=\"c\">". $lang['fl_floten1_ttl'] ."</td>";
	$page .= "</tr>";
	$page .= "<tr height=\"20\">";
	$page .= "<th width=\"50%\">". $lang['fl_dest'] ."</th>";
	$page .= "<th>";
	$page .= "<input name=\"galaxy\" size=\"3\" maxlength=\"2\" onChange=\"shortInfo()\" onKeyUp=\"shortInfo()\" value=\"". $g ."\" />";
	$page .= "<input name=\"system\" size=\"3\" maxlength=\"3\" onChange=\"shortInfo()\" onKeyUp=\"shortInfo()\" value=\"". $s ."\" />";
	$page .= "<input name=\"planet\" size=\"3\" maxlength=\"2\" onChange=\"shortInfo()\" onKeyUp=\"shortInfo()\" value=\"". $p ."\" />";
	$page .= "<select name=\"planettype\" onChange=\"shortInfo()\" onKeyUp=\"shortInfo()\">";
	$page .= "<option value=\"1\"". (($t == 1) ? " SELECTED" : "" ) .">". $lang['fl_planet'] ." </option>";
	$page .= "<option value=\"2\"". (($t == 2) ? " SELECTED" : "" ) .">". $lang['fl_ruins']  ." </option>";
	$page .= "<option value=\"3\"". (($t == 3) ? " SELECTED" : "" ) .">". $lang['fl_moon'] ." </option>";
	$page .= "<option value=\"5\"". (($t == 5) ? " SELECTED" : "" ) .">". $lang['fl_base'] ." </option>";
	$page .= "</select>";
	$page .= "</th>";
	$page .= "</tr>";
	$page .= "<tr height=\"20\">";
	$page .= "<th>". $lang['fl_speed'] ."</th>";
	$page .= "<th>";
	$page .= "<select name=\"speed\" onChange=\"shortInfo()\" onKeyUp=\"shortInfo()\">";
	foreach ($speed as $a => $b) {
		$page .= "<option value=\"".$a."\">".$b."</option>";
	}
	$page .= "</select> %";
	$page .= "</th>";
	$page .= "</tr>";

	$page .= "<tr height=\"20\">";
	$page .= "<th>". $lang['fl_dist'] ."</th>";
	$page .= "<th><div id=\"distance\">-</div></th>";
	$page .= "</tr><tr height=\"20\">";
	$page .= "<th>". $lang['fl_fltime'] ."</th>";
	$page .= "<th><div id=\"duration\">-</div></th>";
	$page .= "</tr><tr height=\"20\">";
	$page .= "<th>". $lang['fl_deute_need'] ."</th>";
	$page .= "<th><div id=\"consumption\">-</div></th>";
	$page .= "</tr><tr height=\"20\">";
	$page .= "<th>". $lang['fl_speed_max'] ."</th>";
	$page .= "<th><div id=\"maxspeed\">-</div></th>";
	$page .= "</tr><tr height=\"20\">";
	$page .= "<th>". $lang['fl_max_load'] ."</th>";
	$page .= "<th><div id=\"storage\">-</div></th>";
	$page .= "</tr>";


	$page .= "<tr height=\"20\">";
	$page .= "<td colspan=\"2\" class=\"c\">". $lang['fl_shortcut'] ." <a href=\"?set=fleet&page=shortcut\">". $lang['fl_shortlnk'] ."</a></td>";
	$page .= "</tr>";
	
	$inf = db::query("SELECT fleet_shortcut FROM {{table}} WHERE id = ".$user->data['id'].";", "users_inf", true);
	
	if ($inf['fleet_shortcut']) {
		$scarray = explode("\r\n", $inf['fleet_shortcut']);
		$i = 0;
		foreach ($scarray as $a => $b) {
			if ($b != "") {
				$c = explode(',', $b);
				if ($i == 0) {
					$page .= "<tr height=\"20\">";
				}
				$page .= "<th><a href=\"javascript:setTarget(". $c[1] .",". $c[2] .",". $c[3] .",". $c[4] ."); shortInfo();\"";
				$page .= ">". $c[0] ." ". $c[1] .":". $c[2] .":". $c[3] ." ";

				if ($c[4] == 1) {
					$page .= $lang['fl_shrtcup1'];
				} elseif ($c[4] == 2) {
					$page .= $lang['fl_shrtcup2'];
				} elseif ($c[4] == 3) {
					$page .= $lang['fl_shrtcup3'];
				}
				$page .= "</a></th>";
				if ($i == 1) {
					$page .= "</tr>";
				}
				if ($i == 1) {
					$i = 0;
				} else {
					$i = 1;
				}
			}
		}
		if ($i == 1) {
			$page .= "<th></th></tr>";
		}
	} else {
		$page .= "<tr height=\"20\">";
		$page .= "<th colspan=\"2\">". $lang['fl_noshortc'] ."</th>";
		$page .= "</tr>";
	}

	$page .= "<tr height=\"20\">";
	$page .= "<td colspan=\"2\" class=\"c\">". $lang['fl_myplanets'] ."</td>";
	$page .= "</tr>";

	$kolonien      = SortUserPlanets ( $user->data );
	$currentplanet = db::query("SELECT * FROM {{table}} WHERE id = '" . $user->data['current_planet'] . "'", 'planets', true);

	if (db::num_rows($kolonien) > 1) {
		$i = 0;
		$w = 0;
		$tr = true;
		while ($row = db::fetch_array($kolonien)) {
			if ($w == 0 && $tr) {
				$page .= "<tr height=\"20\">";
				$tr = false;
			}
			if ($w == 2) {
				$page .= "</tr>";
				$w = 0;
				$tr = true;
			}

			if ($row['planet_type'] == 3) {
				$row['name'] .= " ". $lang['fl_shrtcup3'];
			}

			if ($currentplanet['galaxy']      == $row['galaxy'] &&
				$currentplanet['system']      == $row['system'] &&
				$currentplanet['planet']      == $row['planet'] &&
				$currentplanet['planet_type'] == $row['planet_type'] ) {
//				$page .= '<th><a href="javascript:setTarget('.$row['galaxy'].','.$row['system'].','.$row['planet'].','.$row['planet_type'].'); shortInfo();">'.$row['name'].' '.$row['galaxy'].':'.$row['system'].':'.$row['planet'].'</a></th>';
			} else {
				$page .= "<th><a href=\"javascript:setTarget(". $row['galaxy'] .",". $row['system'] .",". $row['planet'] .",". $row['planet_type'] ."); shortInfo();\">". $row['name'] ." ". $row['galaxy'] .":". $row['system'] .":". $row['planet'] ."</a></th>";
				$w++;
				$i++;
			}
		}

		if ($i % 2 != 0) {
			$page .= "<th>&nbsp;</th></tr>";
		} elseif ($w == 2) {
			$page .= "</tr>";
		}
	} else {
		$page .= "<th colspan=\"2\">". $lang['fl_nocolonies'] ."</th>";
	}

	$page .= "</tr>";
	$page .= "<tr height=\"20\">";
	$page .= "<td colspan=\"2\" class=\"c\">". $lang['fl_grattack'] ."</td>";
	$page .= "</tr>";

	$aks_madnessred = db::query("SELECT a.* FROM game_aks a, game_aks_user au WHERE au.aks_id = a.id AND au.user_id = ".$user->data['id']." ;", '');

	while($row = db::fetch_array($aks_madnessred))
	{		
		$page .= "<tr height=\"20\">";
		$page .= "<th colspan=\"2\">";
		$page .= "<a href=\"javascript:";
		$page .= "setTarget(". $row['galaxy'] .",". $row['system'] .",". $row['planet'] .",". $row['planet_type'] ."); ";
		$page .= "shortInfo(); ACS(".$row['id'].");";
		$page .= "\">";
		$page .= "(".$row['name'].")";
		$page .= "</a>";
		$page .= "</th>";
		$page .= "</tr>";
	}
	if (!$aks_madnessred) {
		$page .= "<tr height=\"20\">";
		$page .= "<th colspan=\"2\">-</th>";
		$page .= "</tr>";
	}

	$page .= "<tr height=\"20\">";
	$page .= "<th colspan=\"2\"><input type=\"submit\" value=\"". $lang['fl_continue'] ."\" /></th>";
	$page .= "</tr>";
	$page .= "</table>";
	$page .= "</div></center>";
	$page .= "<input type=\"hidden\" name=\"acs\" value=\"0\" />";
	$page .= "<input type=\"hidden\" name=\"maxepedition\" value=\"". intval($_POST['maxepedition']) ."\" />";
	$page .= "<input type=\"hidden\" name=\"curepedition\" value=\"". intval($_POST['curepedition']) ."\" />";
	$page .= "<input type=\"hidden\" name=\"target_mission\" value=\"". intval($_POST['target_mission']) ."\" />";
	$page .= "<input type=\"hidden\" name=\"crc\" value=\"". md5($user->data['id'].'-CHeAT_CoNTROL_Stage_02-'.date("dmY", time()).'-'.str_rot13(base64_encode(json_encode($fleet['fleetarray'])))) ."\" />";
	$page .= "</form>";
	$page .= "<script>javascript:shortInfo(); </script>";

	display($page, $lang['fl_title']);

?>
