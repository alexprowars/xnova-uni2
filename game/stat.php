<?php

/**
 * @author AlexPro
 * @copyright 2008 - 2011 XNova Game Group
 * @var $Display HSTemplateDisplay
 * @var $game_config array
 * @var $user user
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

if(!defined("INSIDE")) die("attemp hacking");

$parse = array();

$who   = (isset($_POST['who']))   ? $_POST['who']   : @$_GET['who'];
if (!isset($who)) {
	$who   = 1;
}
$type  = (isset($_POST['type']))  ? $_POST['type']  : @$_GET['type'];
if (!isset($type)) {
	$type  = 1;
}
$range = (isset($_POST['range'])) ? $_POST['range'] : @$_GET['range'];
if (!isset($range)) {
	$rank = db::query("SELECT total_rank FROM {{table}} WHERE `stat_code` = '1' AND `stat_type` = '1' AND `id_owner` = '". $user->data['id'] ."';", 'statpoints', true);
	$range = $rank['total_rank'];
}
$pid = (isset($_GET['pid'])) ? intval($_GET['pid']) : 0;

$parse['who']    = "<option value=\"1\"". (($who == "1") ? " SELECTED" : "") .">Игрок</option>";
$parse['who']   .= "<option value=\"2\"". (($who == "2") ? " SELECTED" : "") .">Альянс</option>";
$parse['who']   .= "<option value=\"3\"". (($who == "3") ? " SELECTED" : "") .">Фракция</option>";

$parse['type']   = "<option value=\"1\"". (($type == "1") ? " SELECTED" : "") .">Очкам</option>";
$parse['type']  .= "<option value=\"2\"". (($type == "2") ? " SELECTED" : "") .">Флоту</option>";
$parse['type']  .= "<option value=\"5\"". (($type == "5") ? " SELECTED" : "") .">Постройкам</option>";
$parse['type']  .= "<option value=\"3\"". (($type == "3") ? " SELECTED" : "") .">Исследованиям</option>";
$parse['type']  .= "<option value=\"4\"". (($type == "4") ? " SELECTED" : "") .">Обороне</option>";

if ($type == 1) {
	$Order   = "total_rank";
	$Points  = "total_points";
	$Counts  = "total_count";
	$Rank    = "total_rank";
	$OldRank = "total_old_rank";
} elseif ($type == 2) {
	$Order   = "fleet_rank";
	$Points  = "fleet_points";
	$Counts  = "fleet_count";
	$Rank    = "fleet_rank";
	$OldRank = "fleet_old_rank";
} elseif ($type == 3) {
	$Order   = "tech_rank";
	$Points  = "tech_points";
	$Counts  = "tech_count";
	$Rank    = "tech_rank";
	$OldRank = "tech_old_rank";
} elseif ($type == 4) {
	$Order   = "defs_rank";
	$Points  = "defs_points";
	$Counts  = "defs_count";
	$Rank    = "defs_rank";
	$OldRank = "defs_old_rank";
} elseif ($type == 5) {
	$Order   = "build_rank";
	$Points  = "build_points";
	$Counts  = "build_count";
	$Rank    = "build_rank";
	$OldRank = "build_old_rank";
}

$Display->addTemplate('stat', 'stat.php');

$stat = array();

if ($who == 3) 
{
	$Display->addTemplate('stat_info', 'stat_race.php');

	$parse['range'] = "<option value='0'>1-4</option>";

	$query = db::query("SELECT * FROM {{table}} WHERE `stat_type` = '3' AND `stat_code` = '1' ORDER BY `". $Order ."` ASC;", 'statpoints');

	while ($StatRow = db::fetch_assoc($query)) 
	{
		$stats['player_rank']     	= $StatRow[$Order];
		$stats['player_race']		= $StatRow['race'];
		$stats['player_count']		= $StatRow['total_count'];
		$stats['player_points']		= pretty_number( $StatRow[ $Points ] );
		$stats['player_pointatuser']= pretty_number(floor($StatRow[ $Points ] / $StatRow['total_count']));

		$stat[]    = $stats;
	}
}
elseif ($who == 2) 
{
    $Display->addTemplate('stat_info', 'stat_alliance.php');
    $stat = array();

	if ($game_config['active_alliance'] > 100) 
	{
		$LastPage = floor($game_config['active_alliance'] / 100);
	}
	else
		$LastPage = 0;
		
	$parse['range'] = "";
	$start = floor($range / 100 % 100);
	
	for ($Page = 0; $Page <= $LastPage; $Page++) 
	{
		$PageValue      = ($Page * 100) + 1;
		$PageRange      = $PageValue + 99;
		$parse['range'] .= "<option value=\"". $PageValue ."\"". (($start == $Page) ? " SELECTED" : "") .">". $PageValue ."-". $PageRange ."</option>";
	}

	$start *= 100;
	$query = db::query("SELECT s.*, a.`id`, a.`ally_tag`, a.`ally_name`, a.`ally_members` FROM {{table}}statpoints s, {{table}}alliance a WHERE s.`stat_type` = '2' AND s.`stat_code` = '1' AND a.id = s.id_owner ORDER BY s.`". $Order ."` ASC LIMIT ". $start .",100;", '');

	$start++;

	while ($StatRow = db::fetch_assoc($query)) 
	{
		$stats['ally_rank']       = $start;
		$rank_old                 = $StatRow[ $OldRank ];
		$rank_new                 = $start;

		$ranking                  = $rank_old - $rank_new;
		
		if ($ranking == 0)
			$stats['ally_rankplus']   = "<font color=\"#87CEEB\">*</font>";
		if ($ranking < 0)
			$stats['ally_rankplus']   = "<font color=\"red\">".$ranking."</font>";
		if ($ranking > 0)
			$stats['ally_rankplus']   = "<font color=\"green\">+".$ranking."</font>";

		if ($StatRow['ally_name'] == $user->data['ally_name'])
			$stats['ally_name'] = "<font color=\"#33CCFF\">".$StatRow['ally_name']."</font>";
		else
			$stats['ally_name'] = "<a href=\"?set=alliance&mode=ainfo&a=".$StatRow['id']."\">".$StatRow['ally_name']."</a>";

		$stats['ally_mes']        = '';
		$stats['ally_members']    = $StatRow['ally_members'];
		$stats['ally_points']     = pretty_number( $StatRow[ $Points ] );
		$stats['ally_members_points'] =  pretty_number( floor($StatRow[ $Points ] / $StatRow['ally_members']) );

		$stat[]    = $stats;
        
		$start++;
	}
}
else 
{
    $Display->addTemplate('stat_info', 'stat_players.php');
    $stats = array();

	if ($game_config['active_users'] > 100) 
	{
		$LastPage = floor($game_config['active_users'] / 100);
	}
	else
		$LastPage = 0;
		
	$parse['range'] = "";
	$start = floor(($range - 1) / 100 % 100);
	
	for ($Page = 0; $Page <= $LastPage; $Page++) 
	{
		$PageValue      = ($Page * 100) + 1;
		$PageRange      = $PageValue + 99;
		$parse['range'] .= "<option value=\"". $PageValue ."\"". (($start == $Page) ? " SELECTED" : "") .">". $PageValue ."-". $PageRange ."</option>";
	}

	$start *= 100;
	
	$query = db::query("SELECT * FROM {{table}} WHERE `stat_type` = '1' AND `stat_code` = '1' AND `stat_hide` = 0 ORDER BY `". $Order ."` ASC LIMIT ". $start .",100;", 'statpoints');

	$start++;

	while ($StatRow = db::fetch_assoc($query)) 
	{
		$stats['player_rank']     = $start;

		$rank_old                 = $StatRow[ $OldRank ];
		if ( $rank_old == 0) {
			$rank_old             = $start;
		}
		$rank_new                 = $start;
		$ranking                  = $rank_old - $rank_new;
		if ($ranking == 0) {
			$stats['player_rankplus'] = "<font color=\"#87CEEB\">*</font>";
		}
		if ($ranking < 0) {
			$stats['player_rankplus'] = "<font color=\"red\">".$ranking."</font>";
		}
		if ($ranking > 0) {
			$stats['player_rankplus'] = "<font color=\"green\">+".$ranking."</font>";
		}
		if ($StatRow['id_owner'] == $user->data['id'] || $StatRow['id_owner'] == $pid) {
			$stats['player_name']     = "<font color=\"lime\">".$StatRow['username']."</font>";
		} else {
			$stats['player_name']     = "<a href=\"?set=players&id=".$StatRow['id_owner']."\">".$StatRow['username']."</a>";
		}

		if (isset($user->data['id']))
			$stats['player_mes']      = "<a href=\"?set=messages&mode=write&id=" . $StatRow['id_owner'] . "\"><img src=\"" . $dpath . "img/m.gif\" width=\"16\" height=\"16\" alt=\"Сообщение\" /></a>";

		if ($StatRow['ally_name'] == $user->data['ally_name'])
			$stats['player_alliance'] = "<font color=\"#33CCFF\">".$StatRow['ally_name']."</font>";
		elseif ($StatRow['ally_name'] != '')
			$stats['player_alliance'] = "<a href=\"?set=alliance&mode=ainfo&a=".$StatRow['id_ally']."\">".$StatRow['ally_name']."</a>";
		else
			$stats['player_alliance'] = '&nbsp;';

		$stats['player_race'] = $StatRow['race'];

		$stats['player_points']   = pretty_number( $StatRow[ $Points ] );

		$stat[]    = $stats;

		$start++;
	}
}

$parse['stat_date'] = datezone("d M Y - H:i:s", $game_config['stat_update']);

$Display->assign('parse', $parse, 'stat');
$Display->assign('stat', $stat, 'stat_info');

display('', 'Статистика', false);

?>
