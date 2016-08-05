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
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

if(!defined("INSIDE")) die("attemp hacking");

$maxfleet  = db::query("SELECT COUNT(fleet_owner) AS `actcnt` FROM {{table}} WHERE `fleet_owner` = '".$user->data['id']."';", 'fleets', true);

$MaxFlyingFleets     = $maxfleet['actcnt'];


$MaxExpedition      = $user->data[$resource[124]];
$ExpeditionEnCours 	= 0;
$EnvoiMaxExpedition	= 0;

if ($MaxExpedition >= 1) {
	$maxexpde  = db::query("SELECT COUNT(fleet_owner) AS `expedi` FROM {{table}} WHERE `fleet_owner` = '".$user->data['id']."' AND `fleet_mission` = '15';", 'fleets', true);

	$ExpeditionEnCours  = $maxexpde['expedi'];
	$EnvoiMaxExpedition = 1 + floor( $MaxExpedition / 3 );
}

$MaxFlottes = 1 + $user->data[$resource[108]];
if ($user->data['rpg_admiral'] > time())
	$MaxFlottes += 2;

system::includeLang('fleet');

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
	15 => $lang['type_mission'][15]
);

$galaxy         = (isset($_GET['galaxy'])) ? intval($_GET['galaxy']) : 0;
$system         = (isset($_GET['system'])) ? intval($_GET['system']) : 0;
$planet         = (isset($_GET['planet'])) ? intval($_GET['planet']) : 0;
$planettype     = (isset($_GET['planettype'])) ? intval($_GET['planettype']) : 0;
$target_mission = (isset($_GET['target_mission'])) ? intval($_GET['target_mission']) : 0;

if (!$galaxy) 
	$galaxy = $planetrow->data['galaxy'];

if (!$system)
	$system = $planetrow->data['system'];
	
if (!$planet)
	$planet = $planetrow->data['planet'];
	
if (!$planettype)
	$planettype = 1;

$page  = "<script language=\"JavaScript\" src=\"scripts/flotten.js\"></script>\n";
//$page .= "<script language=\"JavaScript\" src=\"scripts/ocnt.js\"></script>\n";
$page .= "<br><center>";
$page .= "<table width='690' border='0' cellpadding='0' cellspacing='1'>";
$page .= "<tr height='20'>";
$page .= "<td colspan='9' class='c'>";
$page .= "<table border=\"0\" width=\"100%\">";
$page .= "<tbody><tr>";
$page .= "<td style=\"background-color: transparent;\">";
$page .= $lang['fl_title']." ".$MaxFlyingFleets." ".$lang['fl_sur']." ".$MaxFlottes;
$page .= "</td><td style=\"background-color: transparent;\" align=\"right\">";
$page .= (0+$ExpeditionEnCours)."/".(0+$EnvoiMaxExpedition)." ".$lang['fl_expttl'];
$page .= "</td>";
$page .= "</tr></tbody></table>";
$page .= "</td>";
$page .= "</tr><tr height='20'>";
$page .= "<th width='20'>".$lang['fl_id']."</th>";
$page .= "<th>".$lang['fl_mission']."</th>";
$page .= "<th>".$lang['fl_count']."</th>";
$page .= "<th>".$lang['fl_from']."</th>";
$page .= "<th width='80'>".$lang['fl_start_t']."</th>";
$page .= "<th>".$lang['fl_dest']."</th>";
$page .= "<th width='80'>".$lang['fl_dest_t']."</th>";
$page .= "<th>".$lang['fl_back_in']."</th>";
$page .= "<th width='110'>".$lang['fl_order']."</th>";
$page .= "</tr>";

$fq = db::query("SELECT * FROM {{table}} WHERE fleet_owner=".$user->data['id']."", "fleets");
$i  = 0;


while ($f = db::fetch_assoc($fq)) {
	$i++;
	$page .= "<tr height=20>";
	$page .= "<th>".$i."</th>";
	$page .= "<th>";
	$page .= "<a>". $missiontype[$f['fleet_mission']] ."</a>";
	if (($f['fleet_start_time'] + 1) == $f['fleet_end_time']) {
		$page .= "<br><a title=\"".$lang['fl_back_to_ttl']."\">".$lang['fl_back_to']."</a>";
	} else {
		$page .= "<br><a title=\"".$lang['fl_get_to_ttl']."\">".$lang['fl_get_to']."</a>";
	}
	$page .= "</th>";
	$page .= "<th><a onmouseover=\"return overlib('<center>";

	$fleet 			= explode(";", $f['fleet_array']);
	$fleet_count 	= 0;

	foreach ($fleet as $a => $b) {
		if ($b != '') {
			$a = explode(",", $b);
			$c = explode("!", $a[1]);
			$page .= $lang['tech'][$a[0]]. ": ". $c[0] ."<br>";

			$fleet_count += $c[0];
		}
	}
	$page .= "</center>',WIDTH,200,CENTER);\" onmouseout=\"return nd();\">". pretty_number($fleet_count) ."</a></th>";
	$page .= "<th><a href=\"?set=galaxy&mode=0&galaxy=".$f['fleet_start_galaxy']."&system=".$f['fleet_start_system']."\">[".$f['fleet_start_galaxy'].":".$f['fleet_start_system'].":".$f['fleet_start_planet']."]</a></th>";
	$page .= "<th>". datezone("d M y H:i:s", $f['fleet_start_time']) ."</th>";
	$page .= "<th><a href=\"?set=galaxy&mode=0&galaxy=".$f['fleet_end_galaxy']."&system=".$f['fleet_end_system']."\">[".$f['fleet_end_galaxy'].":".$f['fleet_end_system'].":".$f['fleet_end_planet']."]</a></th>";
	$page .= "<th>". datezone("d M y H:i:s", $f['fleet_end_time']) ."</th>";
	$page .= "<th><font color=\"lime\">".pretty_time(floor($f['fleet_end_time'] + 1 - time()))."</font></th>";
	$page .= "<th>";
	if ($f['fleet_mess'] == 0 && $f['fleet_mission'] != 20 && $f['fleet_target_owner'] != 1) {
			$page .= "<form action=\"?set=fleet&page=back\" method=\"post\">";
			$page .= "<input name=\"fleetid\" value=\"". $f['fleet_id'] ."\" type=\"hidden\">";
			$page .= "<input value=\" ".$lang['fl_back_to_ttl']." \" type=\"submit\" name=\"send\" style=\"width:110px\">";
			$page .= "</form>";
		if ($f['fleet_mission'] == 1) {
			$page .= "<form action=\"?set=fleet&page=verband\" method=\"post\">";
			$page .= "<input name=\"fleetid\" value=\"". $f['fleet_id'] ."\" type=\"hidden\">";
			$page .= "<input value=\" ".$lang['fl_associate']." \" type=\"submit\" style=\"width:110px\">";
			$page .= "</form>";
		}

	} elseif ($f['fleet_mess'] == 3 && $f['fleet_mission'] != 15) {
			$page .= "<form action=\"?set=fleet&page=back\" method=\"post\">";
			$page .= "<input name=\"fleetid\" value=\"". $f['fleet_id'] ."\" type=\"hidden\">";
			$page .= "<input value=\" Отозвать \" type=\"submit\" name=\"send\" style=\"width:110px\">";
			$page .= "</form>";
	} else {
		$page .= "&nbsp;-&nbsp;";
	}
	$page .= "</th>";
	$page .= "</tr>";
}


if ($i == 0) {
	$page .= "<tr>";
	$page .= "<th>-</th>";
	$page .= "<th>-</th>";
	$page .= "<th>-</th>";
	$page .= "<th>-</th>";
	$page .= "<th>-</th>";
	$page .= "<th>-</th>";
	$page .= "<th>-</th>";
	$page .= "<th>-</th>";
	$page .= "<th>-</th>";
	$page .= "</tr>";
}

if ($MaxFlottes == $MaxFlyingFleets) {
	$page .= "<tr height=\"20\"><th colspan=\"9\"><font color=\"red\">".$lang['fl_noslotfree']."</font></th></tr>";
}

$page .= "</table></center>";

$page .= "<center>";

$page .= "<script>";
$page .= "function chShipCount(id, diff){";
$page .= "	diff = 1 * diff;";
$page .= "	var ncur = 1 * document.getElementsByName(\"ship\" + id)[0].value;";
$page .= "	count = ncur + diff;";
$page .= "	if(count < 0){";
$page .= "		count = 0;";
$page .= "	};";
$page .= "	if(count > document.getElementsByName(\"maxship\" + id)[0].value){";
$page .= "		count = document.getElementsByName(\"maxship\" + id)[0].value;";
$page .= "	};";
$page .= "	document.getElementsByName(\"ship\" + id)[0].value = count;";
$page .= "}";
$page .= "</script>";

$page .= "<br><form action=\"?set=fleet&page=fleet_1\" method=\"post\">";
$page .= "<table width=\"519\" border=\"0\" cellpadding=\"0\" cellspacing=\"1\">";
$page .= "<tr height=\"20\">";
$page .= "<td colspan=\"4\" class=\"c\">Выбрать корабли";

if ($target_mission > 0)
    $page .= ' для миссии "'.$lang['type_mission'][$target_mission].'"';
if (($system > 0 && $galaxy > 0 && $planet > 0) && ($galaxy != $planetrow->data['galaxy'] || $system != $planetrow->data['system'] || $planet != $planetrow->data['planet']))
    $page .= ' на координаты ['.$galaxy.':'.$system.':'.$planet.']';

$page .= ":</td>";
$page .= "</tr>";
$page .= "<tr height=\"20\">";
$page .= "<th>".$lang['fl_fleet_typ']."</th>";
$page .= "<th>".$lang['fl_fleet_disp']."</th>";
$page .= "<th>-</th>";
$page .= "<th>-</th>";
$page .= "</tr>";

if (!$planetrow) {
	message($lang['fl_noplanetrow'], $lang['fl_error']);
}

$ShipData       = "";
$have_ships		= false;

foreach ($reslist['fleet'] as $n => $i) {
	if ($planetrow->data[$resource[$i]] > 0) {
		$page .= "<tr height=\"20\">\n";
		$page .= "<th><a title=\"". $lang['tech'][$i] ."\">" . $lang['tech'][$i] . "</a></th>\n";
		$page .= "<th>". pretty_number ($planetrow->data[$resource[$i]]);
		$ShipData .= "<input type=\"hidden\" name=\"maxship". $i ."\" value=\"". $planetrow->data[$resource[$i]] ."\" />\n";
		$ShipData .= "<input type=\"hidden\" name=\"consumption". $i ."\" value=\"". GetShipConsumption ( $i, $user ) ."\" />\n";
		$ShipData .= "<input type=\"hidden\" name=\"speed" .$i ."\" value=\"" . GetFleetMaxSpeed ("", $i, $user) . "\" />\n";
		$ShipData .= "<input type=\"hidden\" name=\"capacity". $i ."\" value=\"";

        if ($i == 202 || $i == 203)
            $ShipData .= round($CombatCaps[$i]['capacity'] * (1 + $user->data['fleet_'.$i] * 0.05));
        else
            $ShipData .= $CombatCaps[$i]['capacity'];

        $ShipData .= "\" />\n";

		$page .= "</th>\n";

		if ($i == 212) {
			$page .= "<th></th><th></th>\n";
		} else {
			$page .= "<th><a href=\"javascript:noShip('ship". $i ."'); calc_capacity();\">min</a> / <a href=\"javascript:maxShip('ship". $i ."'); calc_capacity();\">max</a></th>\n";
			$page .= "<th><a href=\"javascript:chShipCount('". $i ."', '-1'); calc_capacity();\" title=\"Уменьшить на 1 ед.\" style=\"color:#FFD0D0\">- </a><input name=\"ship". $i ."\" size=\"10\" value=\"0\" onfocus=\"javascript:if(this.value == '0') this.value='';\" onblur=\"javascript:if(this.value == '') this.value='0';\" alt=\"". $lang['tech'][$i] . $planetrow->data[$resource[$i]] ."\" onChange=\"calc_capacity()\" onKeyUp=\"calc_capacity()\" /><a href=\"javascript:chShipCount('". $i ."', '1'); calc_capacity();\" title=\"Увеличить на 1 ед.\" style=\"color:#D0FFD0\"> +</a></th>\n";
		}
		$page .= "</tr>\n";
	}
	$have_ships = true;
}

$btncontinue = "<tr height=\"20\"><th colspan=\"4\"><input type=\"submit\" value=\" ".$lang['fl_continue']." \" /></th>\n";
$page .= "<tr height=\"20\">\n";
if (!$have_ships) {
	$page .= "<th colspan=\"4\">". $lang['fl_noships'] ."</th>\n";
	$page .= "</tr>\n";
	$page .= $btncontinue;
} else {
	$page .= "<th colspan=\"2\"><a href=\"javascript:noShips(); calc_capacity();\" >". $lang['fl_unselectall'] ."</a></th>\n";
	$page .= "<th colspan=\"2\"><a href=\"javascript:maxShips(); calc_capacity();\" >". $lang['fl_selectall'] ."</a></th>\n";
	$page .= "</tr>\n";
	$page .= "<tr height=\"20\">\n";
	$page .= "	<th colspan=\"2\">-</th>\n";
	$page .= "	<th colspan=\"1\">Вместимость</th>\n";
	$page .= "	<th colspan=\"1\"><div id=\"allcapacity\">-</div></th>\n";
	$page .= "</tr>\n";
	$page .= "<tr height=\"20\">\n";
	$page .= "	<th colspan=\"2\">-</th>\n";
	$page .= "	<th colspan=\"1\">Скорость</th>\n";
	$page .= "	<th colspan=\"1\"><div id=\"allspeed\">-</div></th>\n";
	$page .= "</tr>\n";

	if ($MaxFlottes > $MaxFlyingFleets) {
		$page .= $btncontinue;
	}
}
$page .= "</tr>";
$page .= "</table>";
$page .= $ShipData;
$page .= "<input type=\"hidden\" name=\"galaxy\" value=\"". $galaxy ."\" />";
$page .= "<input type=\"hidden\" name=\"system\" value=\"". $system ."\" />";
$page .= "<input type=\"hidden\" name=\"planet\" value=\"". $planet ."\" />";
$page .= "<input type=\"hidden\" name=\"planet_type\" value=\"". $planettype ."\" />";
$page .= "<input type=\"hidden\" name=\"mission\" value=\"". $target_mission ."\" />";
$page .= "<input type=\"hidden\" name=\"maxepedition\" value=\"". $EnvoiMaxExpedition ."\" />";
$page .= "<input type=\"hidden\" name=\"curepedition\" value=\"". $ExpeditionEnCours ."\" />";
$page .= "<input type=\"hidden\" name=\"target_mission\" value=\"". $target_mission ."\" />";
$page .= "<input type=\"hidden\" name=\"crc\" value=\"". md5($user->data['id'].'-CHeAT_CoNTROL_Stage_01-'.date("dmY", time())) ."\" />";
$page .= "</form>";

$page .= "<table width='710' border='0' cellpadding='0' cellspacing='1'><tr><td class='c'>F.A.Q. по флотам</tr>
		<tr><th style='text-align:left;'>Вопрос: У меня отображается минусовое время прилёта флота. Что мне делать?<br><div style='font-weight:normal;'>Ответ: Не паникуйте. Ваш флот всё еще в пути, но пока не обработался сервером. Запомните что в период с 1:48 до 2:30 обработка флота не осуществляется по техническим причинам.</div></th></tr>
		<tr><th style='text-align:left;'>Вопрос: Что такое перезагрузка и зачем она нужна?<br><div style='font-weight:normal;'>Ответ: Перезагрузка это программное выключение сервера игры. Оно нужно для того чтобы избежать последствий при зависании сервера в 2 часа ночи по мск.</div></th></tr>
		<tr><th style='text-align:left;'>Вопрос: У меня \"склонировался\" флот. Что мне делать?<br><div style='font-weight:normal;'>Ответ: Отослать лишний флот администратору на координаты [1:1:1] или [1:1:4], такой флот будет лететь всего 30 секунд и с вас не будет списано дейтерия на полёт</div></th></tr>
				</table>";

$page .= "</center>";

display($page, $lang['fl_title']);

?>
