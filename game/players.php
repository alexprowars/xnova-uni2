<?php

/**
 * @author AlexPro
 * @copyright 2008 - 2011 XNova Game Group
 * @var $Display HSTemplateDisplay
 * @var $user user
 * @var $lang array
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

if(!defined("INSIDE")) die("attemp hacking");

$parse   = $lang;
	
$playerid  = (isset($_POST['id']))  ? $_POST['id']  : $_GET['id'];
if (!isset($playerid)) {
	$playerid  = 0;
}
$ownid  = ($session->IsUserChecked) ? $user->data['id'] : 0;

$PlayerCard = db::query("SELECT u.*, ui.vkontakte, ui.icq, ui.about FROM {{table}}users u LEFT JOIN {{table}}users_inf ui ON ui.id = u.id WHERE u.id = '". intval($playerid) ."';", '');
if ($daten = db::fetch_array($PlayerCard)){

	if ($daten['avatar'] != 0) {
		if ($daten['avatar'] != 99) {
			$parse['avatar'] = "/images/avatars/".$daten['avatar'].".jpg";
		} else {
			$parse['avatar'] = "/images/avatars/upload/upload_".$daten['id'].".jpg";
		}
	}

	$gesamtkaempfe = 0;
	$gesamtkaempfe = $daten['raids_win'] + $daten['raids_lose'];
	if ($gesamtkaempfe ==0) {
		$siegprozent	=0;
		$loosprozent	=0;
	} else {
		$siegprozent	= 100 / $gesamtkaempfe * $daten['raids_win'];
		$loosprozent	= 100 / $gesamtkaempfe * $daten['raids_lose'];
	}

	if (!$daten['ally_id'])   	$daten['ally_id'] 	= "- - -";
	if (!$daten['ally_name']) 	$daten['ally_name'] 	= "- - -";

	$planets = db::query("SELECT * FROM {{table}} WHERE `galaxy` = '". $daten['galaxy'] ."' and `system` = '". $daten['system'] ."' and `planet_type` = '1' and `planet` = '". $daten['planet'] ."';", 'planets', true);
	$parse['userplanet'] = $planets['name'];

	$points = db::query("SELECT * FROM {{table}} WHERE `stat_type` = '1' AND `stat_code` = '1' AND `id_owner` = '". $daten['id'] ."';", 'statpoints', true);
	$parse['tech_rank']      	= pretty_number( $points['tech_rank'] );
	$parse['tech_points']      	= pretty_number( $points['tech_points'] );
	$parse['build_rank']      	= pretty_number( $points['build_rank'] );
	$parse['build_points']     	= pretty_number( $points['build_points'] );
	$parse['fleet_rank']      	= pretty_number( $points['fleet_rank'] );
	$parse['fleet_points']      = pretty_number( $points['fleet_points'] );
	$parse['defs_rank']      	= pretty_number( $points['defs_rank'] );
	$parse['defs_points']       = pretty_number( $points['defs_points'] );
	$parse['total_rank']      	= pretty_number( $points['total_rank'] );
	$parse['total_points']     	= pretty_number( $points['total_points'] );
	
	if ($ownid	!= 0)							
		$parse['player_buddy'] = "<a href=\"?set=buddy&a=2&amp;u=" . $playerid . "\" title=\"Добавить в друзья\">Добавить в друзья</a>";
	else
		$parse['player_buddy'] = "";

	if ($ownid	!= 0)
		$parse['player_mes'] = "<a href=\"?set=messages&mode=write&id=" . $playerid . "\">Написать сообщение</a>";
	else
		$parse['player_mes'] = "";

	if ($daten['icq'] != 0)
		$parse['icq'] = $daten['icq'];
	else
		$parse['icq'] = "нет";

	if ($daten['sex'] == 2)
		$parse['sex'] = "Женский";
	else
		$parse['sex'] = "Мужской";

	if ($daten['vkontakte'] != 0) {
		$parse['vkontakte'] = "<a href=\"http://vkontakte.ru/id".$daten['vkontakte']."\" target=_blank>Профиль</a>";
	} else
		$parse['vkontakte'] = "нет";

	$parse['ingame']			= ($ownid != 0) ? true : false;
	$parse['id']				= $daten['id'];
	$parse['username']			= $daten['username'];
	$parse['race']				= $daten['race'];
	$parse['galaxy']            = $daten['galaxy'];
	$parse['system']           	= $daten['system'];
	$parse['planet']           	= $daten['planet'];	
	$parse['ally_id']          	= $daten['ally_id'];
	$parse['ally_name']        	= $daten['ally_name'];
	$parse['about']        		= $daten['about'];
	$parse['wons']             	= pretty_number( $daten['raids_win'] );
	$parse['loos']             	= pretty_number( $daten['raids_lose'] );
	$parse['siegprozent']      	= round($siegprozent, 2);
	$parse['loosprozent']      	= round($loosprozent, 2);
	$parse['total']				= $daten['raids'];
	$parse['totalprozent']     	= 100;
	$parse['m']					= GetRankId($daten['lvl_minier']);
	$parse['f']					= GetRankId($daten['lvl_raid']);
} else 
	message('Параметр задан неверно', 'Ошибка');

$Display->addTemplate('player', 'player.php');
$Display->assign('parse', $parse, 'player');

if ($session->IsUserChecked)
	display('', "Информация о игроке", false);
else
	display('', "Информация о игроке", false, false);
?>
