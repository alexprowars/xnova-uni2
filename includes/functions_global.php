<?php

function GetConfig()
{
	// Открытие файла-кэша настроек
	$game_config = file_get_contents("includes/config.txt");

	// Если кэш пустой, то заполняем его
	if (empty($game_config))
	{
		$game_config = system::CreateConfigCache();
	}

	$game_config = json_decode($game_config, true);

	return $game_config;
}

function datezone($format, $time = 0)
{
	global $user;

	if ($time == 0)
		$time = time();

	if (isset($user->data['timezone']))
		$time += $user->data['timezone'] * 1800;
		
	//$time += 3600;

	return date($format, $time);
}

function is_email($email)
{
	if(preg_match('#^[^\\x00-\\x1f@]+@[^\\x00-\\x1f@]{2,}\.[a-z]{2,}$#iu', $email) == 0)
	{
		return false;
	}
	return true;
}

function message ($mes, $title = 'Ошибка', $dest = "", $time = 3, $left = true)
{
    global $Display;

    $Display->addTemplate('message', 'message.php');
    $Display->assign('parse', array($title, $mes), 'message');

	display ('', $title, false, $left, (($dest != "") ? "<meta http-equiv=\"refresh\" content=\"".$time.";URL=".$dest.((isset($_GET['ajax'])) ? '&ajax' : '')."\">" : ""));
}

function display ($page = '', $title = '', $topnav = true, $left = true, $meta = '')
{
	global $game_config, $user, $Template, $Display;

    if (isset($_GET['set']) && $_GET['set'] == 'admin' && isset($user->data['id']) && $user->data['authlevel'] > 0)
        $admin = true;
	else
		$admin = false;

	$DisplayFrames = & $Template->getDisplay('frame');

	if (!isset($user->data['id']) || isset($_GET['ajax']))
		$left = false;

	if (!isset($_GET['ajax']))
	{
		$DisplayFrames->addTemplate('header', 'header.php');
		$DisplayFrames->assign('title', $title, 'header');
		$DisplayFrames->assign('meta', $meta, 'header');
		$DisplayFrames->assign('design', ((isset($user->data['design'])) ? $user->data['design'] : 1), 'header');
		$DisplayFrames->assign('vk', ((isset($_COOKIE['vkid']) || isset($_GET['api_id'])) ? 1 : 0), 'header');
		$DisplayFrames->assign('timezone', ((isset($user->data['timezone'])) ? $user->data['timezone'] : 0), 'header');
        $DisplayFrames->assign('ajax_nav', ((isset($user->data['ajax_navigation'])) ? $user->data['ajax_navigation'] : 0), 'header');
	}

    if ($left && isset($user->data['id'])) 
	{
        $DisplayFrames->addTemplate('menu', 'menu.php');
        $DisplayFrames->assign('adminlevel', $user->data['authlevel'], 'menu');
		$DisplayFrames->assign('uid', $user->data['id'], 'menu');

		if (isset($_COOKIE['vkid']))
			$DisplayFrames->assign('vk', 1, 'menu');
		else
			$DisplayFrames->assign('vk', 0, 'menu');

		$DisplayFrames->assign('mess', $user->data['new_message'], 'menu');
        $DisplayFrames->assign('admin', $admin, 'menu');
		$DisplayFrames->assign('set', ((isset($_GET['set'])) ? (($_GET['set'] == 'buildings' && isset($_GET['mode'])) ? $_GET['set'].$_GET['mode'] : $_GET['set']) : ''), 'menu');
    }

	if ($topnav) 
	{
		global $planetrow;
		$parse = array();
		// Выводим панель ресурсов
		ShowTopNavigationBar( $user->data, $planetrow->data, $parse );
        $parse['tutorial'] = $user->data['tutorial'];
		// Подключаем шапку игры
        if ($user->data['design'] == 1)
		    $DisplayFrames->addTemplate('top', 'top.php');
        else
            $DisplayFrames->addTemplate('top', 'top_lite.php');

		$DisplayFrames->assign('parse', $parse, 'top');
	}

	$DisplayFrames->display();

	if (!$left && $title != 'login' && !isset($_GET['ajax']))
		echo '<div class="contentBoxBody"><div id="boxBG"><div id="box"><table width="100%"><tr><td><center>';

    if (isset($user->data['id'])) 
	{
        if ($user->data['deltime'] > 0)
		    echo '<table width="700"><tr><td class="c" align="center">Включен режим удаления профиля!<br>Ваш аккаунт будет удалён после '.datezone("d.m.Y", $user->data['deltime']).' в '.datezone("H:i:s", $user->data['deltime']).'. Выключить режим удаления можно в настройках игры.</td></tr></table>';

        if ($user->data['urlaubs_modus_time'] > 0)
		    echo '<table width="700"><tr><td class="c" align="center"><font color="red">Включен режим отпуска! Функциональность игры ограничена.</font></td></tr></table>';

		if (date("G", time()) == 1 && (mktime(2, 0, 0) - 1800) < time() && (!isset($_GET['set']) || (isset($_GET['set']) && $_GET['set'] != "chat")))
			echo '<table width="700"><tr><td class="c" align="center">Время до перезагрузки: '.(mktime(2, 0, 0) - time() - 300).' сек.</td></tr></table>';
    }

	$Display->display();

	if ($page != "")
		echo $page;

	if ($left && isset($user->data['id'])) 
	{
		echo '</td></tr></table></div>';

		if ($user->data['authlevel'] == 3 && $game_config['debug'] == 1)
        	db::echo_log();

		if (isset($_COOKIE['vkid']))
			echo '</div>';

		echo '</center></div></div></div><div id="siteFooter">
			<div class="content"><div class="fleft textLeft"><a href="?set=news" title="Последние изменения">'.VERSION.'</a>';

		if (isset($_COOKIE['vkid']))
			echo '<a onclick=\'this.target="_blank";this.href="?set=overview"\'>Развернуть</a><a onclick=\'this.target="_blank";this.href="?set=overview&vk=1"\'>Полная версия</a>';
		else
			echo '<a onclick=\'this.target="_blank";this.href="http://xnova.su/"\'>© 2008 - 2012 XNova Game Group</a>';

		echo '</div><div class="fright textRight"><a href="?set=contact">Контакты</a>|<a href="?set=agb">Правила</a>
				| <a onclick="" title="Игроков в сети" style="color:green">'.$game_config['online'].'</a>/<a onclick="" title="Всего игроков" style="color:yellow">'.$game_config['users_amount'].'</a></div>
				<br class="clearfloat"/></div></div>';	
    }
	
	if (isset($user->data['id']) && isset($user->data['ajax_navigation']) && $user->data['ajax_navigation'] == 1)
		echo '<script>RebuildHref("'.((isset($_GET['set'])) ? (($_GET['set'] == 'buildings' && isset($_GET['mode'])) ? $_GET['set'].$_GET['mode'] : $_GET['set']) : '').'"); UpdateGameInfo('.$user->data['new_message'].');</script>';

	if (!$left && $title != 'login' && !isset($_GET['ajax']))
		echo '</center></td></tr></table></div></div></div>';

	if (!isset($_GET['ajax']))
		echo '</body></html>';
	
	die();
}

function exception_handler($exception)
{
    global $CONF;


    @session_write_close();
    if($_SERVER['SERVER_PROTOCOL'] == 'HTTP/1.1' && !headers_sent())
            header('HTTP/1.1 503 Service Unavailable');

    echo '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">';
    echo '<html>';
    echo '<head>';
    echo '<meta http-equiv="content-type" content="text/html; charset=UTF-8">';
    echo '<meta http-equiv="content-script-type" content="text/javascript">';
    echo '<meta http-equiv="content-style-type" content="text/css">';
    echo '<meta http-equiv="content-language" content="de">';
    echo '<title>'.$CONF['game_name'].' - FATAL ERROR</title>';
    echo '<link rel="shortcut icon" href="'.(defined('INSTALL') ? '..':'.').'/favicon.ico">';
    echo '<link rel="stylesheet" type="text/css" href="'.(defined('INSTALL') ? '..':'.').'/styles/css/ingame.css">';
    echo '<link rel="stylesheet" type="text/css" href="'.(defined('INSTALL') ? '..':'.').'/styles/theme/'.DEFAULT_THEME.'/formate.css">';
    echo '</head>';
    echo '<body style="margin-top:30px;">';
    echo '<table width="80%">';
    echo '<tr>';
    echo '<th>';
    echo 'Error:';
    echo '</th>';
    echo '</tr>';
    echo '<tr>';
    echo '<td class="left"><b>Message: </b>'.$exception->getMessage().'<br>';
    echo '<b>File: </b>'.$exception->getFile().'<br>';
    echo '<b>Line: </b>'.$exception->getLine().'<br>';
    echo '<b>URL: </b>'.PROTOCOL.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].(!empty($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING']: '').'<br>';
    echo '<b>PHP-Version: </b>'.PHP_VERSION.'<br>';
    echo '<b>PHP-API: </b>'.php_sapi_name().'<br>';
    echo '<b>2Moons Version: </b>'.$CONF['VERSION'].'<br>';
    echo '<b>Debug Backtrace:</b><br>'.makebr(str_replace($_SERVER['DOCUMENT_ROOT'], '.', htmlspecialchars($exception->getTraceAsString()))).'</th>';
    echo '</tr>';
    echo '</table>';
    echo '</body>';
    echo '</html>';
    ini_set('display_errors', 0);
    trigger_error("Exception: ".str_replace("<br>", "\r\n", $exception->getMessage())."\r\n\r\n".$exception->getTraceAsString(), E_USER_ERROR);
    exit;
}

function GetRankId ($lvl)
{

	if ($lvl == 1)
		$lvl = 0;

	if ($lvl <= 80)
		return (ceil($lvl / 4) + 1);
	else
		return 22;
}

function CalculateMaxPlanetFields (&$planet)
{
	global $resource;

	return $planet["field_max"] + ($planet[$resource[33]] * 5) + (FIELDS_BY_MOONBASIS_LEVEL * $planet[$resource[41]]);
}

function SetSelectedPlanet ( &$CurrentUser )
{

	if (isset($_GET['cp'])  && is_numeric($_GET['cp']) && isset($_GET['re']) && intval($_GET['re']) == 0) {
	
		$SelectPlanet  	= intval($_GET['cp']);
	
		$IsPlanetMine   = db::query("SELECT `id` FROM {{table}} WHERE `id` = '". $SelectPlanet ."' AND `id_owner` = '". $CurrentUser['id'] ."';", 'planets', true);
		if ($IsPlanetMine) {
			$CurrentUser['current_planet'] = $SelectPlanet;
			db::query("UPDATE {{table}} SET `current_planet` = '". $SelectPlanet ."' WHERE `id` = '".$CurrentUser['id']."';", 'users');
		}
	}
}

/**
 * @param  $user array
 * @param bool $Moons
 * @return array
 */
function SortUserPlanets ( $user, $Moons = true )
{
	$Order = ( $user['planet_sort_order'] == 1 ) ? "DESC" : "ASC" ;

	$QryPlanets  = "SELECT `id`, `name`, `galaxy`, `system`, `planet`, `planet_type`, `destruyed` FROM {{table}} WHERE `id_owner` = '". $user['id'] ."' ";

	if (!$Moons)
		$QryPlanets .= " AND planet_type != 3 ";

	$QryPlanets .= "ORDER BY ";

	if ( $user['planet_sort'] == 0 ) {
		$QryPlanets .= "`id` ". $Order;
	} elseif ( $user['planet_sort'] == 1 ) {
		$QryPlanets .= "`galaxy`, `system`, `planet`, `planet_type` ". $Order;
	} elseif ( $user['planet_sort'] == 2 ) {
		$QryPlanets .= "`name` ". $Order;
	} elseif ( $user['planet_sort'] == 3 ) {
		$QryPlanets .= "`planet_type` ". $Order;
	} else
		$QryPlanets .= "`id` ". $Order;

	$Planets = db::query ( $QryPlanets, 'planets');

	return $Planets;
}

function GetIP ( $ip )
{
    return (int) sprintf("%u", ip2long($ip));
}

function PageSelector ($count, $per_page, $link, $page = 0)
{
    $pages_count = @ceil($count / $per_page);

    if ($page == 0 || $page > $pages_count)
        $page = 1;
    
    $pages = "";
    $end = 0;

    if ($pages_count > 1) {
        for($i = 1; $i <= $pages_count; $i++) {
            if (($page <= $i + 3 && $page >= $i - 3) || $i == 1 || $i == $pages_count || $pages_count <= 6) {
                $end = 0;

                if ($i == $page)
                    $pages .= "<b style=\"color:green\">".$i."</b>";
                else
                    $pages .= "<a href=\"".$link."&p=".$i."\">".$i."</a>";

                if ($i < $pages_count)
                    $pages .= " | ";
            } else {
                if ($end == 0)
                    $pages .= "... | ";

                $end = 1;
            }
        }
    } else
        $pages = '1';

    return $pages;
}

function SendSimpleMessage ( $Owner, $Sender, $Time, $Type, $From, $Message)
{
	if (!$Time)
		$Time = time();

	if ($Owner == $_SESSION['uid'])
	{
		global $user;

		$user->data['new_message']++;
	}

	$QryInsertMessage  = "INSERT INTO {{table}} SET ";
	$QryInsertMessage .= "`message_owner` = '". $Owner ."', ";
	$QryInsertMessage .= "`message_sender` = '". $Sender ."', ";
	$QryInsertMessage .= "`message_time` = '" . $Time . "', ";
	$QryInsertMessage .= "`message_type` = '". $Type ."', ";
	$QryInsertMessage .= "`message_from` = '". addslashes( $From ) ."', ";
	$QryInsertMessage .= "`message_text` = '". addslashes( $Message ) ."';";
	db::query( $QryInsertMessage, 'messages');

	db::query("UPDATE {{table}} SET `new_message` = `new_message` + 1 WHERE `id` = '". $Owner ."';", 'users');
}

function ShowTopNavigationBar ($CurrentUser, $CurrentPlanet, &$parse)
{
	global $game_config;

	$parse['image']      = $CurrentPlanet['image'];
	$parse['time']		 = time();

	$parse['planetlist'] = '';
	$ThisUsersPlanets    = SortUserPlanets ( $CurrentUser );
	
	while ($CurPlanet = db::fetch_assoc($ThisUsersPlanets))
	{
		if ($CurPlanet['destruyed'] == 0)
		{
			$parse['planetlist'] .= "\n<option ";

			if ($CurPlanet['planet_type'] == 3)
				$parse['planetlist'] .= "style=\"color:red;\" ";
			elseif ($CurPlanet['planet_type'] == 5)
				$parse['planetlist'] .= "style=\"color:yellow;\" ";

			if ($CurPlanet['id'] == $CurrentUser['current_planet']) {
				$parse['planetlist'] .= "selected=\"selected\" ";
			}
			if (isset($_GET['set']))
				$parse['planetlist'] .= "value=\"?set=".$_GET['set']."";
			else
				$parse['planetlist'] .= "value=\"?set=overview";
			if (isset($_GET['mode']))
				$parse['planetlist'] .= "&amp;mode=".$_GET['mode'];

			$parse['planetlist'] .= "&amp;cp=".$CurPlanet['id']."&amp;re=0\">";

			$parse['planetlist'] .= "".$CurPlanet['name'];
			$parse['planetlist'] .= "&nbsp;[".$CurPlanet['galaxy'].":".$CurPlanet['system'].":".$CurPlanet['planet'];
			$parse['planetlist'] .= "]&nbsp;&nbsp;</option>";
		}
	}


	$metal = round($CurrentPlanet["metal"]);
	$parse['metal'] = $metal;

	$crystal = round($CurrentPlanet["crystal"]);
	$parse['crystal'] = $crystal;

	$deuterium = round($CurrentPlanet["deuterium"]);
	$parse['deuterium'] = $deuterium;

	$energy_max= pretty_number($CurrentPlanet["energy_max"]);
	if (($CurrentPlanet["energy_max"] > $CurrentPlanet["energy_max"])) {
		$parse['energy_max'] = colorRed($energy_max);
	} else {
		$parse['energy_max'] = $energy_max;
	}

	$parse['energy_total'] = colorNumber(pretty_number($CurrentPlanet['energy_max'] + $CurrentPlanet["energy_used"]));

	if ($CurrentPlanet["metal_max"] <= $CurrentPlanet["metal"]) {
		$parse['metal_max'] = '<font color="#ff0000">';
	} else {
		$parse['metal_max'] = '<font color="#00ff00">';
	}
	$parse['metal_m'] = $CurrentPlanet["metal_max"];
	$parse['metal_pm'] = ($CurrentPlanet["metal_perhour"] + floor($game_config['metal_basic_income'] * $game_config['resource_multiplier'])) / 3600;
	$parse['metal_mp'] = $CurrentPlanet['metal_mine_porcent']*10;
	$parse['metal_ph'] = pretty_number($CurrentPlanet["metal_perhour"] + floor($game_config['metal_basic_income'] * $game_config['resource_multiplier']));
	$parse['metal_pd'] = pretty_number(($CurrentPlanet["metal_perhour"] + floor($game_config['metal_basic_income'] * $game_config['resource_multiplier'])) * 24);

	$parse['metal_max'] .= pretty_number($CurrentPlanet["metal_max"])."</font>";


	if ($CurrentPlanet["crystal_max"] <= $CurrentPlanet["crystal"]) {
		$parse['crystal_max'] = '<font color="#ff0000">';
	} else {
		$parse['crystal_max'] = '<font color="#00ff00">';
	}
	$parse['crystal_m'] = $CurrentPlanet["crystal_max"];
	$parse['crystal_pm'] = ($CurrentPlanet["crystal_perhour"] + floor($game_config['crystal_basic_income'] * $game_config['resource_multiplier'])) / 3600;
	$parse['crystal_mp'] = $CurrentPlanet['crystal_mine_porcent']*10;
	$parse['crystal_ph'] = pretty_number($CurrentPlanet["crystal_perhour"] + floor($game_config['crystal_basic_income'] * $game_config['resource_multiplier']));
	$parse['crystal_pd'] = pretty_number(($CurrentPlanet["crystal_perhour"] + floor($game_config['crystal_basic_income'] * $game_config['resource_multiplier'])) * 24);
	$parse['crystal_max'] .= pretty_number($CurrentPlanet["crystal_max"])."</font>";


	if ($CurrentPlanet["deuterium_max"] <= $CurrentPlanet["deuterium"]) {
		$parse['deuterium_max'] = '<font color="#ff0000">';
	} else {
		$parse['deuterium_max'] = '<font color="#00ff00">';
	}
	$parse['deuterium_m'] = $CurrentPlanet["deuterium_max"];
	$parse['deuterium_pm'] = ($CurrentPlanet["deuterium_perhour"] + floor($game_config['deuterium_basic_income'] * $game_config['resource_multiplier'])) / 3600;
	$parse['deuterium_mp'] = $CurrentPlanet['deuterium_sintetizer_porcent']*10;
	$parse['deuterium_ph'] = pretty_number($CurrentPlanet["deuterium_perhour"] + floor($game_config['deuterium_basic_income'] * $game_config['resource_multiplier']));
	$parse['deuterium_pd'] = pretty_number(($CurrentPlanet["deuterium_perhour"] + floor($game_config['deuterium_basic_income'] * $game_config['resource_multiplier'])) * 24);
	$parse['deuterium_max'] .= pretty_number($CurrentPlanet["deuterium_max"])."</font>";

	$now = time();
	$parse['credits'] = pretty_number($CurrentUser['credits']);


	if ($CurrentUser['design'] == 1) {
		if ($CurrentUser['rpg_admiral'] > $now){
			$parse['admiral_ikon'] = "_ikon";
			$parse['admiral'] = "<br>Истекает:</font><br><font color=lime>".datezone("d.m.Y H:i", $CurrentUser['rpg_admiral'])."</font>";
		} else {
			$parse['admiral_ikon'] = "";
			$parse['admiral'] = "</font><br><font color=lime>Нанять</font>";
		}

		if ($CurrentUser['rpg_ingenieur'] > $now){
			$parse['ingenieur_ikon'] = "_ikon";
			$parse['ingenieur'] = "<br>Истекает:</font><br><font color=lime>".datezone("d.m.Y H:i", $CurrentUser['rpg_ingenieur'])."</font>";
		} else {
			$parse['ingenieur_ikon'] = "";
			$parse['ingenieur'] = "</font><br><font color=lime>Нанять</font>";
		}

		if ($CurrentUser['rpg_geologue'] > $now){
			$parse['geologe_ikon'] = "_ikon";
			$parse['geologe'] = "<br>Истекает:</font><br><font color=lime>".datezone("d.m.Y H:i", $CurrentUser['rpg_geologue'])."</font>";
		} else {
			$parse['geologe_ikon'] = "";
			$parse['geologe'] = "</font><br><font color=lime>Нанять</font>";
		}

		if ($CurrentUser['rpg_technocrate'] > $now){
			$parse['technokrat_ikon'] = "_ikon";
			$parse['technokrat'] = "<br>Истекает:</font><br><font color=lime>".datezone("d.m.Y H:i", $CurrentUser['rpg_technocrate'])."</font>";
		} else {
			$parse['technokrat_ikon'] = "";
			$parse['technokrat'] = "</font><br><font color=lime>Нанять</font>";
		}

		if ($CurrentUser['rpg_constructeur'] > $now){
			$parse['architector_ikon'] = "_ikon";
			$parse['architector'] = "<br>Истекает:</font><br><font color=lime>".datezone("d.m.Y H:i", $CurrentUser['rpg_constructeur'])."</font>";
		} else {
			$parse['architector_ikon'] = "";
			$parse['architector'] = "</font><br><font color=lime>Нанять</font>";
		}

		if ($CurrentUser['rpg_meta'] > $now){
			$parse['meta_ikon'] = "_ikon";
			$parse['rpgmeta'] = "<br>Истекает:</font><br><font color=lime>".datezone("d.m.Y H:i", $CurrentUser['rpg_meta'])."</font>";
		} else {
			$parse['meta_ikon'] = "";
			$parse['rpgmeta'] = "</font><br><font color=lime>Нанять</font>";
		}

		if ($CurrentUser['rpg_komandir'] > $now){
			$parse['komandir_ikon'] = "_ikon";
			$parse['komandir'] = "<br>Истекает:</font><br><font color=lime>".datezone("d.m.Y H:i", $CurrentUser['rpg_komandir'])."</font>";
		} else {
			$parse['komandir_ikon'] = "";
			$parse['komandir'] = "</font><br><font color=lime>Нанять</font>";
		}
	}

	$parse['energy_ak'] = round($CurrentPlanet['energy_ak'] / ( 10000 * pow((1.1), $CurrentPlanet['ak_station'])  * $CurrentPlanet['ak_station'] + 1), 2) * 100;

	if ($parse['energy_ak'] == 0) $parse['energy'] = "batt0.png";
	elseif ($parse['energy_ak'] >= 100) $parse['energy'] = "batt100.png";
	else $parse['energy'] = "batt.php?p=".$parse['energy_ak'];

	$parse['ak'] = round($CurrentPlanet['energy_ak'])." / ".round(10000 * pow((1.1), $CurrentPlanet['ak_station']) * $CurrentPlanet['ak_station']);

	if ($parse['energy_ak'] > 0 && $parse['energy_ak'] < 100) {
		if (($CurrentPlanet['energy_max'] + $CurrentPlanet["energy_used"]) > 0)
			$parse['ak'] .= '<br>Заряд: '.pretty_time(round(((round(10000 * pow((1.1), $CurrentPlanet['ak_station']) * $CurrentPlanet['ak_station']) - $CurrentPlanet['energy_ak']) / ($CurrentPlanet['energy_max'] + $CurrentPlanet["energy_used"])) * 3600)).'';
		elseif (($CurrentPlanet['energy_max'] + $CurrentPlanet["energy_used"]) < 0)
			$parse['ak'] .= '<br>Разряд: '.pretty_time(round(($CurrentPlanet['energy_ak'] / abs($CurrentPlanet['energy_max'] + $CurrentPlanet["energy_used"])) * 3600)).'';
	}

	if ($CurrentUser['new_message'] > 0) {
		$parse['message'] = "<a href=\"?set=messages\">[ ". $CurrentUser['new_message'] ." ]</a>";
	} else {
		$parse['message'] = "0";
	}
	if ($CurrentUser['mnl_alliance'] > 0 && $CurrentUser['ally_id'] == 0) {
		db::query("UPDATE {{table}} SET mnl_alliance = 0 WHERE id = ".$CurrentUser['id']."", "users");
		$CurrentUser['mnl_alliance'] = 0;
	}
	if ($CurrentUser['ally_id'] != 0)
		$parse['message'] .= " <a href=\"?set=alliance&mode=circular\">[ ". $CurrentUser['mnl_alliance'] ." ]</a>";
}

/**
 * @param  $CurrentUser user
 * @param  $CurrentPlanet
 * @param  $Element
 * @param bool $Incremental
 * @param bool $ForDestroy
 * @return bool
 */
function IsElementBuyable ($CurrentUser, $CurrentPlanet, $Element, $Incremental = true, $ForDestroy = false)
{
	$RetValue = true;

	$cost = GetBuildingPrice($CurrentUser, $CurrentPlanet, $Element, $Incremental, $ForDestroy);

	foreach ($cost AS $ResType => $ResCount)
	{
		if ($ResCount > $CurrentPlanet[$ResType])
		{
			$RetValue = false;
			break;
		}
	}

	return $RetValue;
}

function IsTechnologieAccessible($user, $planet, $Element)
{
	global $requeriments, $resource;

	if (isset($requeriments[$Element]))
	{
		$enabled = true;
		foreach($requeriments[$Element] as $ReqElement => $EleLevel)
		{
			if ($ReqElement == 700 && $user[$resource[$ReqElement]] != $EleLevel) {
				return false;
			} elseif (isset($user[$resource[$ReqElement]]) && $user[$resource[$ReqElement]] >= $EleLevel) {
				// break;
			} elseif (isset($planet[$resource[$ReqElement]]) && $planet[$resource[$ReqElement]] >= $EleLevel) {
				$enabled = true;
			} elseif (isset($planet['planet_type']) && $planet['planet_type'] == 5 && ($Element == 43 || $Element == 502 || $Element == 503) && ($ReqElement == 21 || $ReqElement == 41)) {
				$enabled = true;
			} else {
				return false;
			}
		}
		return $enabled;
	} else
		return true;
}

function CheckLabSettingsInQueue ( $CurrentPlanet )
{
	global $game_config;

	if ($CurrentPlanet['b_building_id'] != '')
	{
		$BuildQueue = $CurrentPlanet['b_building_id'];
		if (strpos ($BuildQueue, ";")) {
			$Queue = explode (";", $BuildQueue);
			$CurrentBuilding = $Queue[0];
		} else {
			$CurrentBuilding = $BuildQueue;
		}

		if ($CurrentBuilding == 31 && $game_config['BuildLabWhileRun'] != 1) {
			$return = false;
		} else {
			$return = true;
		}

	} else {
		$return = true;
	}

	return $return;
}

function ShowBuildTime ($time)
{
	return "<br><b>Время</b>: " . pretty_time($time);
}

function GetStartAdressLink ( $FleetRow, $FleetType )
{
	$Link  = "<a href=\"?set=galaxy&amp;mode=3&amp;galaxy=".$FleetRow['fleet_start_galaxy']."&amp;system=".$FleetRow['fleet_start_system']."\" ". $FleetType ." >";
	$Link .= "[".$FleetRow['fleet_start_galaxy'].":".$FleetRow['fleet_start_system'].":".$FleetRow['fleet_start_planet']."]</a>";
	return $Link;
}

function GetTargetAdressLink ( $FleetRow, $FleetType )
{
	$Link  = "<a href=\"?set=galaxy&amp;mode=3&amp;galaxy=".$FleetRow['fleet_end_galaxy']."&amp;system=".$FleetRow['fleet_end_system']."\" ". $FleetType ." >";
	$Link .= "[".$FleetRow['fleet_end_galaxy'].":".$FleetRow['fleet_end_system'].":".$FleetRow['fleet_end_planet']."]</a>";
	return $Link;
}

function BuildPlanetAdressLink ( $CurrentPlanet )
{
	$Link  = "<a href=\"?set=galaxy&amp;mode=3&amp;galaxy=".$CurrentPlanet['galaxy']."&amp;system=".$CurrentPlanet['system']."\">";
	$Link .= "[".$CurrentPlanet['galaxy'].":".$CurrentPlanet['system'].":".$CurrentPlanet['planet']."]</a>";
	return $Link;
}

function BuildHostileFleetPlayerLink ( $FleetRow )
{
	global $lang, $dpath;

	$PlayerName = db::query ("SELECT `username` FROM {{table}} WHERE `id` = '". $FleetRow['fleet_owner']."';", 'users', true);
	$Link  = $PlayerName['username']. " ";
	$Link .= "<a href=\"?set=messages&amp;mode=write&amp;id=".$FleetRow['fleet_owner']."\">";
	$Link .= "<img src=\"".$dpath."/img/m.gif\" alt=\"". $lang['ov_message']."\" title=\"". $lang['ov_message']."\" border=\"0\"></a>";
	return $Link;
}

function GetNextJumpWaitTime ( $CurMoon )
{
	global $resource;

	$JumpGateLevel  = $CurMoon[$resource[43]];
	$LastJumpTime   = $CurMoon['last_jump_time'];
	if ($JumpGateLevel > 0) {
		$WaitBetweenJmp = (60 * 60) * (1 / $JumpGateLevel);
		$NextJumpTime   = $LastJumpTime + $WaitBetweenJmp;
		if ($NextJumpTime >= time()) {
			$RestWait   = $NextJumpTime - time();
			$RestString = " ". pretty_time($RestWait);
		} else {
			$RestWait   = 0;
			$RestString = "";
		}
	} else {
		$RestWait   = 0;
		$RestString = "";
	}
	$RetValue['string'] = $RestString;
	$RetValue['value']  = $RestWait;

	return $RetValue;
}

function InsertJavaScriptChronoApplet ( $Type, $Ref, $Value )
{

	$JavaString  = "<script>FlotenTime('bxx". $Type . $Ref ."', ". $Value .");</script>";

	return $JavaString;
}

function CreateFleetPopupedFleetLink ( $FleetRow, $Texte, $FleetType )
{
	global $lang, $user;

	$FleetRec    = explode(";", $FleetRow['fleet_array']);

	$FleetPopup  = "<table width=200>";
	$r			 = '#';
	$Total 		 = 0;

	if ($FleetRow['fleet_owner'] != $user->data['id'] && $user->data['spy_tech'] < 2)
	{
		$FleetPopup .= "<tr><td width=100% align=center><font color=white>Нет информации<font></td></tr>";
	}
	elseif ($FleetRow['fleet_owner'] != $user->data['id'] && $user->data['spy_tech'] < 4)
	{
		foreach($FleetRec as $Group) {
			if ($Group  != '') {
				$Ship     = explode(",", $Group);
				$Count    = explode("!", $Ship[1]);
				$Total = $Total + $Count[0];
			}
		}
		$FleetPopup .= "<tr><td width=50% align=left><font color=white>Численность:<font></td><td width=50% align=right><font color=white>". pretty_number($Total) ."<font></td></tr>";
	}
	elseif ($FleetRow['fleet_owner'] != $user->data['id'] && $user->data['spy_tech'] < 8)
	{
		foreach($FleetRec as $Group) {
			if ($Group  != '') {
				$Ship     = explode(",", $Group);
				$Count    = explode("!", $Ship[1]);
				$Total = $Total + $Count[0];
				$FleetPopup .= "<tr><td width=100% align=center colspan=2><font color=white>". $lang['tech'][$Ship[0]] ."<font></td></tr>";
			}
		}
		$FleetPopup .= "<tr><td width=50% align=left><font color=white>Численность:<font></td><td width=50% align=right><font color=white>". pretty_number($Total) ."<font></td></tr>";
	}
	else
	{
		if ($FleetRow['fleet_target_owner'] == $user->data['id'] && $FleetRow['fleet_mission'] == 1)
			$r = '?set=sim&r=';

		foreach($FleetRec as $Group) {
			if ($Group  != '') {
				$Ship     = explode(",", $Group);
				$Count    = explode("!", $Ship[1]);
				$FleetPopup .= "<tr><td width=75% align=left><font color=white>". $lang['tech'][$Ship[0]] .":<font></td><td width=25% align=right><font color=white>". pretty_number($Count[0]) ."<font></td></tr>";

				if ($r != '#')
					$r .= $Group.';';
			}
		}
	}

	$FleetPopup  .= "</table>";
	$FleetPopup  .= "');\" onmouseout=\"return nd();\" class=\"". $FleetType ."\">". $Texte ."</a>";

	$FleetPopup   = "<a href='".$r."' onmouseover=\"return overlib('".$FleetPopup;

	return $FleetPopup;

}

function CreateFleetPopupedMissionLink ( $FleetRow, $Texte, $FleetType )
{
	global $lang;

	$FleetTotalC  = $FleetRow['fleet_resource_metal'] + $FleetRow['fleet_resource_crystal'] + $FleetRow['fleet_resource_deuterium'];
	if ($FleetTotalC != 0) {
		$FRessource   = "<table width=200>";
		$FRessource  .= "<tr><td width=50% align=left><font color=white>". $lang['Metal'] ."<font></td><td width=50% align=right><font color=white>". pretty_number($FleetRow['fleet_resource_metal']) ."<font></td></tr>";
		$FRessource  .= "<tr><td width=50% align=left><font color=white>". $lang['Crystal'] ."<font></td><td width=50% align=right><font color=white>". pretty_number($FleetRow['fleet_resource_crystal']) ."<font></td></tr>";
		$FRessource  .= "<tr><td width=50% align=left><font color=white>". $lang['Deuterium'] ."<font></td><td width=50% align=right><font color=white>". pretty_number($FleetRow['fleet_resource_deuterium']) ."<font></td></tr>";
		$FRessource  .= "</table>";
	} else {
		$FRessource   = "";
	}

	if ($FRessource <> "") {
		$MissionPopup  = "<a href='#' onmouseover=\"return overlib('". $FRessource ."');";
		$MissionPopup .= "\" onmouseout=\"return nd();\" class=\"". $FleetType ."\">" . $Texte ."</a>";
	} else {
		$MissionPopup  = $Texte ."";
	}

	return $MissionPopup;
}

/**
 * @param  $user user
 * @param  $planet array
 * @param  $Element integer
 * @param array $space_lab
 * @return float|int
 */
function GetBuildingTime ($user, $planet, $Element, $space_lab = array()) {
	global $pricelist, $resource, $reslist, $game_config;

	$time = 0;

	$level = (isset($planet[$resource[$Element]])) ? $planet[$resource[$Element]] : $user->data[$resource[$Element]];
	if (in_array($Element, $reslist['build']))
	{
		$cost_metal   = floor($pricelist[$Element]['metal']   * pow($pricelist[$Element]['factor'], $level));
		$cost_crystal = floor($pricelist[$Element]['crystal'] * pow($pricelist[$Element]['factor'], $level));
		$time         = (($cost_crystal + $cost_metal) / $game_config['game_speed']) * (1 / ($planet[$resource['14']] + 1)) * pow(0.5, $planet[$resource['15']]);
		$time         = floor(($time * 60 * 60) * $user->bonus_time_building);
	}
	elseif (in_array($Element, $reslist['tech']) || in_array($Element, $reslist['tech_f']))
	{
		$cost_metal   = floor($pricelist[$Element]['metal']   * pow($pricelist[$Element]['factor'], $level));
		$cost_crystal = floor($pricelist[$Element]['crystal'] * pow($pricelist[$Element]['factor'], $level));
		$intergal_lab = $user->data[$resource[123]];

		if ($intergal_lab < 1) {
			$lablevel = $planet[$resource['31']];
		} else {

			$NbLabs 	= 0;
			$techlevel 	= array();

			if (count($space_lab) > 0)
			{
				foreach ($space_lab AS $colonie)
				{
					if ( IsTechnologieAccessible($user->data, $colonie, $Element) ) {
						$techlevel[$NbLabs] = $colonie[$resource['31']];
						$NbLabs++;
					}
				}
			}

			if ($NbLabs >= 1)
			{
				$lablevel = $planet[$resource['31']];
				$c = ($intergal_lab > count($techlevel)) ? count($techlevel) : $intergal_lab;
				for ($lab = 1; $lab <= $c; $lab++)
				{
					asort($techlevel);
					$lablevel += $techlevel[$lab - 1];
				}
			} else
				$lablevel = $planet[$resource['31']];
		}

		$time         = (($cost_metal + $cost_crystal) / $game_config['game_speed']) / (($lablevel + 1) * 2);
		$time         = floor(($time * 60 * 60) * $user->bonus_time_research);
	}
	elseif (in_array($Element, $reslist['defense']))
	{
		$time         = (($pricelist[$Element]['metal'] + $pricelist[$Element]['crystal']) / $game_config['game_speed']) * (1 / ($planet[$resource['21']] + 1)) * pow(1 / 2, $planet[$resource['15']]);
		$time         = floor(($time * 60 * 60) * $user->bonus_time_defence);
	}
	elseif (in_array($Element, $reslist['fleet']))
	{
		$time         = (($pricelist[$Element]['metal'] + $pricelist[$Element]['crystal']) / $game_config['game_speed']) * (1 / ($planet[$resource['21']] + 1)) * pow(1 / 2, $planet[$resource['15']]);
		$time         = floor(($time * 60 * 60) * $user->bonus_time_fleet);
	}

	if ($time < 1) $time = 1;

	return $time;
}

/**
 * @param $cost array
 * @param  $user user
 * @param  $planet array
 * @return string
 */
function GetElementPrice ($cost, $user, $planet)
{
	global $lang, $dpath;

	$array = array(
		'metal'      	=> array($lang["Metal"], 'metall'),
		'crystal'    	=> array($lang["Crystal"], 'kristall'),
		'deuterium'  	=> array($lang["Deuterium"], 'deuterium'),
		'energy_max' 	=> array($lang["Energy"], 'energie')
	);

	$text = "<table width='100%'><tr>";
	foreach ($array as $ResType => $ResTitle) {
		if (isset($cost[$ResType]) && $cost[$ResType] != 0) {
			if ($user['design'] == 1)
				$text .= "<td align='center'><img src='".$dpath."images/".$ResTitle[1].".gif' onmouseover=\"return overlib('<center>".$ResTitle[0]."</center>',LEFT,WIDTH,75,FGCOLOR,'#465673')\" onmouseout=\"nd()\"><br>";
			else
				$text .= "<td align='center'><b>".$ResTitle[0]."</b><br>";

			if ($cost[$ResType] > $planet[$ResType]) {
				$text .= "<b style=\"color:red;\"> <span class=\"noresources\">" . pretty_number($cost[$ResType]) . "</span></b> ";
			} else {
				$text .= "<b style=\"color:lime;\"> <span class=\"noresources\">" . pretty_number($cost[$ResType]) . "</span></b> ";
			}
			$text .= "</td>";
		}
	}
	$text .= "</table>";

	return $text;
}

/**
 * @param  $user user
 * @param  $planet array
 * @param  $Element
 * @param bool $Incremental
 * @param bool $ForDestroy
 * @return array
 */
function GetBuildingPrice ($user, $planet, $Element, $Incremental = true, $ForDestroy = false)
{
	global $pricelist, $resource;

	if ($Incremental)
		$level = (isset($planet[$resource[$Element]])) ? $planet[$resource[$Element]] : $user->data[$resource[$Element]];
	else
		$level = 0;

	$array = array('metal', 'crystal', 'deuterium', 'energy_max');
	$cost  = array();

	foreach ($array as $ResType)
	{
		if (!isset($pricelist[$Element][$ResType]))
			break;

		if ($Incremental)
		{
			$cost[$ResType] = floor($pricelist[$Element][$ResType] * pow($pricelist[$Element]['factor'], $level));
			if ($Element > 0 && $Element < 100)
				$cost[$ResType] = round($cost[$ResType] * $user->bonus_res_building);
			elseif ($Element > 100 && $Element < 199)
				$cost[$ResType] = round($cost[$ResType] * $user->bonus_res_research);
		}
		else
		{
			$cost[$ResType] = floor($pricelist[$Element][$ResType]);
			if ($Element > 200 && $Element < 300)
				$cost[$ResType] = round($cost[$ResType] * $user->bonus_res_fleet);
			elseif ($Element > 300 && $Element < 400)
				$cost[$ResType] = round($cost[$ResType] * $user->bonus_res_levelup);
			elseif ($Element > 400 && $Element < 504)
				$cost[$ResType] = round($cost[$ResType] * $user->bonus_res_defence);
		}

		if ($ForDestroy)
		{
			$cost[$ResType] = floor($cost[$ResType] / 2);
		}
	}

	return $cost;
}

/**
 * @param  $Element
 * @param  $Level
 * @return string
 */
function GetNextProduction ($Element, $Level)
{
	global $ProdGrid, $game_config, $user, $planetrow;

	$Res = array();

	if (isset($ProdGrid[$Element]))
	{
		$energy_tech 	= $user->data['energy_tech'];
		$BuildTemp 		= $planetrow->data['temp_max'];

		$BuildLevelFactor 	= 10;
		$BuildLevel      	= $Level + 1;

		$Res['m']   =  floor(eval($ProdGrid[$Element]['metal']) * $game_config['resource_multiplier'] * $user->bonus_metal);
		$Res['c']   =  floor(eval($ProdGrid[$Element]['crystal']) * $game_config['resource_multiplier'] * $user->bonus_crystal);
		$Res['d'] 	=  floor(eval($ProdGrid[$Element]['deuterium']) * $game_config['resource_multiplier'] * $user->bonus_deuterium);
		if ($Element < 4)
			$Res['e'] =  floor(eval($ProdGrid[$Element]['energy']) * $game_config['resource_multiplier']);
		elseif ($Element >= 4)
			$Res['e'] =  floor(eval($ProdGrid[$Element]['energy']) * $game_config['resource_multiplier'] * $user->bonus_energy);

		$BuildLevel      	= $Level;

		$Res['m']   -=  floor(eval($ProdGrid[$Element]['metal']) * $game_config['resource_multiplier'] * $user->bonus_metal);
		$Res['c']   -=  floor(eval($ProdGrid[$Element]['crystal']) * $game_config['resource_multiplier'] * $user->bonus_crystal);
		$Res['d'] 	-=  floor(eval($ProdGrid[$Element]['deuterium']) * $game_config['resource_multiplier'] * $user->bonus_deuterium);
		if ($Element < 4)
			$Res['e'] -=  floor(eval($ProdGrid[$Element]['energy']) * $game_config['resource_multiplier']);
		elseif ($Element >= 4)
			$Res['e'] -=  floor(eval($ProdGrid[$Element]['energy']) * $game_config['resource_multiplier'] * $user->bonus_energy);
	}

	$text = '';

	if (isset($Res['m']) && $Res['m'] != 0) {
		$text .= "<br>Металл: <font color=#".(($Res['m'] > 0) ? '00FF' : 'FF00')."00>".(($Res['m'] > 0) ? '+' : '').$Res['m']."</font>";
	}
	if (isset($Res['c']) && $Res['c'] != 0) {
		$text .= "<br>Кристалл:  <font color=#".(($Res['c'] > 0) ? '00FF' : 'FF00')."00>".(($Res['c'] > 0) ? '+' : '').$Res['c']."</font>";
	}
	if (isset($Res['d']) && $Res['d'] != 0) {
		$text .= "<br>Дейтерий:  <font color=#".(($Res['d'] > 0) ? '00FF' : 'FF00')."00>".(($Res['d'] > 0) ? '+' : '').$Res['d']."</font>";
	}
	if (isset($Res['e']) && $Res['e'] != 0) {
		$text .= "<br>Энергия:  <font color=#".(($Res['e'] > 0) ? '00FF' : 'FF00')."00>".(($Res['e'] > 0) ? '+' : '').$Res['e']."</font>";
	}

	return $text;
}

?>
