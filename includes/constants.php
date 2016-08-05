<?php

if (!defined('INSIDE'))
	die("Hacking attempt");

// Список констант игры
define('VERSION'				  , '1.5 REV188');				// Номер версии игры
define('APPID'					  , '');					// ID приложения vkontakte.ru
define('APPKEY'					  , '');	// Секретный ключ приложения vkontakte.ru
define('DEFAULT_SKINPATH'		  , '/skins/default/');			// Путь до скина по умолчанию
define('ADMINEMAIL'               , "alexprowars@gmail.com");
define('GAMEURL'                  , "http://uni2.xnova.su/");
define('MAX_GALAXY_IN_WORLD'      , 9);
define('MAX_SYSTEM_IN_GALAXY'     , 499);
define('MAX_PLANET_IN_SYSTEM'     , 15);
define('SPY_REPORT_ROW'           , 2);
define('FIELDS_BY_MOONBASIS_LEVEL', 4);
define('MAX_PLAYER_PLANETS'       , 9);
define('MAX_BUILDING_QUEUE_SIZE'  , 1);
define('MAX_FLEET_OR_DEFS_PER_ROW', 99999);
define('MAX_OVERFLOW'             , 1);
define('BASE_STORAGE_SIZE'        , 50000);
define('BUILD_METAL'              , 500);
define('BUILD_CRISTAL'            , 500);
define('BUILD_DEUTERIUM'          , 500);
define('DEBUG'					  , 0);

define('PREFIX'                   , 'game_');

// Массив настроек игры
$game_config = array(
	'game_name' 			=> 'XNova Game',				// Название игры
	'COOKIE_NAME'			=> 'XNova',						// Настройки куков
	'secretword'			=> 'XNova1192354697',
	'forum_url'				=> 'http://forum.xnova.su/',	// УРЛ форума
	'noobprotection'		=> 1,							// Защита новичков
	'noobprotectiontime'	=> 50,
	'noobprotectionmulti'	=> 5,
	'Fleet_Cdr'				=> 30,							// Флот в обломки (процент)
	'Defs_Cdr'				=> 0,							// Оборона в обломки (процент)
	'initial_fields'		=> 163,							// Поля на главной планете
	'debug'					=> 0,							// Режим отладки
	'BuildLabWhileRun'		=> 0,
	'metal_basic_income'	=> 20,							// Базовое производство на планете
	'crystal_basic_income'	=> 10,
	'deuterium_basic_income'=> 0,
	'energy_basic_income'	=> 0,
	'game_speed'			=> 250000,						// Скорость игры
	'fleet_speed'			=> 250000,
	'resource_multiplier'	=> 15
);

?>