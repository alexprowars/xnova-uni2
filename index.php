<?php

function __autoload($classname)
{
	include_once('includes/class/class.'.$classname.'.php');
}

/**
 * @author AlexPro
 * @copyright 2008 - 2011 XNova Game Group
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */
header("Content-type: text/html; charset=utf-8");
header('P3P: CP="NOI ADM DEV PSAi COM NAV OUR OTRo STP IND DEM"');
// Часовой пояс по умолчанию
date_default_timezone_set("Europe/Moscow");
// Установка домена кук для сессии
session_set_cookie_params(0, '/', $_SERVER["SERVER_NAME"]);
// Старт системы кэша
cache::init();
// Старт сессии
session_start();
// Анти-баг
error_reporting(E_ALL);
if (!isset($_SERVER['HTTP_X_REAL_IP']))
    $_SERVER['HTTP_X_REAL_IP'] = '127.0.0.1';
// Флаг защиты подключаемых модулей
define('INSIDE', true);
// Системные переменные
$server_load 	= (function_exists('sys_getloadavg')) ? sys_getloadavg() : array(0, 0, 0);
$lang 			= array();
$set 			= (isset($_GET['set'])) ? $_GET['set'] : '';

include('includes/constants.php');		  // Игровые константы
include('includes/vars.php');			  // Игровые параметры
include('includes/functions_global.php'); // Игровые функции
include('includes/functions_string.php'); // Строковые функции

//set_exception_handler('exception_handler');

//echo $a;

if (!isset($game_modules))
	die('Повреждение модулей игры');

// Создаём сласс шаблонизатора
$Template = new HSTemplate(array('template_path' => 'template', 'cache_path' => 'cache', 'debug' => false));
// Создаём объект шаблона центра игры
$Display = $Template->getDisplay('game');

$game_config = array_merge($game_config, GetConfig());

if (isset($_GET['set']) && $_GET['set'] == 'login' && isset($_POST['auth_key']) && isset($_POST['viewer_id']))
{
	$vkapi = new vkapi(APPID, APPKEY);
	$vkapi->login();
}

if ($set == "overview" || $set == "fleet" || !$set)
	$UpdateFlyFleet = true;

$session = new session();
$session->CheckTheUser();

if ($session->IsUserChecked)
{
	$user = new user();
	$user->load_from_array($session->user);
	system::includeLang('tech');
}
else
{
	system::CheckReferLink();
}

//if (isset($user->data['id']))
//	die('Запуск в пятницу в 12:00 по МСК');

if ($UpdateFlyFleet == true)
{
    if (!$session->IsUserChecked)
        $UpdateFlyFleet = false;
    else
	{
        if (time() - cache::get('fleet_time') < 5)
        	$UpdateFlyFleet = false;

        if ($UpdateFlyFleet == true && (mktime(2, 0, 0) - 900 < time() && mktime(2, 0, 0) + 1800 > time()))
        	$UpdateFlyFleet = false;
    }
}
//print_r($server_load);
//if ($server_load[0] >= 0.5)
//	$UpdateFlyFleet = false;

if ($UpdateFlyFleet == true)
{
	cache::set('fleet_time', (time() + 60));

	$_fleets = new fleet_engine();
	$_fleets->FleetHandler();

	unset($_fleets);
	
    cache::set('fleet_time', time());
}

//$dpath = (!isset($user->data["dpath"]) || !$user->data["dpath"]) ? DEFAULT_SKINPATH : 'c://xnova//';
$dpath = DEFAULT_SKINPATH;
// Заносим глобальные переменные
$Template->assignGlobal('dpath', $dpath);

if (isset($user->data['id']))
{
	// Кэшируем настройки профиля в сессию
	if (!isset($_SESSION['config']) || strlen($_SESSION['config']) < 10)
	{
		$inf = db::query("SELECT dpath, design, security, widescreen, planet_sort, planet_sort_order, color, timezone, bb_parser, ajax_navigation FROM {{table}} WHERE id = ".$user->data['id'].";", "users_inf", true);
		$_SESSION['config'] = json_encode($inf);
	}
	// Заносим настройки профиля в основной массив
	$inf = json_decode($_SESSION['config'], true);
	$user->data = array_merge($user->data, $inf);
	// Заносим ид игрока в глобальную видимость шаблонизатора
	$Template->assignGlobal('user_id', $user->data['id']);
	// Если находимся не в чате, то получаем информацию о планете
	if ($set != "chat")
	{
		// Выставляем планету выбранную игроком из списка планет
		SetSelectedPlanet($user->data);
		// Убираем лишнюю нагрузку на вывод
		if ($set != "officier" && $set != "alliance" && $set != "buddy" && $set != "notes" && $set != "admin" && $set != "news" && $set != "stat" && $set != "support" && $set != "messages" && $set != "records" && $set != "hall" && $set != "banned" && $set != "log" && $set != "rw" && $set != "options" && $set != "players") {
			// Выбираем информвцию о планете
			$planetrow = new planet();
			$planetrow->load_from_id($user->data['current_planet']);
			$planetrow->load_user_info($user);
			// Проверяем корректность заполненных полей
			$planetrow->CheckPlanetUsedFields();
		}
		if (isset($planetrow->data['id'])) {
			// Обновляем ресурсы на планете когда это необходимо
			if ($UpdateFlyFleet || (($set == "overview" || ($set == "fleet" && @$_GET['page'] != 'fleet_3') || $set == "galaxy" || $set == "resources" || $set == "imperium" || $set == "infokredits" || $set == "tutorial" || $set == "techtree" || $set == "search" || $set == "support" || $set == "sim" || !$set) && $planetrow->data['last_update'] > (time() - 60)))
				$planetrow->PlanetResourceUpdate(time(), true);
			else {
				$planetrow->PlanetResourceUpdate();
				// Обновляем постройки на планете
				if ($planetrow->UpdatePlanetBatimentQueueList())
					$planetrow->PlanetResourceUpdate(time(), true);
			}
		}
	}

	if ($user->data['race'] == 0 && $set != 'infos')
		$set = 'race';
}

if (!isset($game_modules[$set]))
{
	$set = ($session->IsUserChecked) ? 'overview' : 'login';
}

if ($game_modules[$set] == 1 && !$session->IsUserChecked)
	$set = 'login';
elseif ($game_modules[$set] == 2 && $session->IsUserChecked)
	$set = 'overview';

if (file_exists('game/'.$set.'.php'))
	include('game/'.$set.'.php');


?>
