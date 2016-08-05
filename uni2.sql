-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Хост: 127.0.0.1
-- Время создания: Дек 29 2013 г., 22:11
-- Версия сервера: 5.5.25
-- Версия PHP: 5.3.13

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `uni2`
--

-- --------------------------------------------------------

--
-- Структура таблицы `game_aks`
--

CREATE TABLE IF NOT EXISTS `game_aks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `fleet_id` int(32) DEFAULT NULL,
  `galaxy` int(2) DEFAULT NULL,
  `system` int(4) DEFAULT NULL,
  `planet` int(2) DEFAULT NULL,
  `planet_type` tinyint(1) NOT NULL DEFAULT '1',
  `user_id` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `game_aks_user`
--

CREATE TABLE IF NOT EXISTS `game_aks_user` (
  `aks_id` int(11) unsigned NOT NULL DEFAULT '0',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  KEY `aks_id` (`aks_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `game_alliance`
--

CREATE TABLE IF NOT EXISTS `game_alliance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ally_name` varchar(32) NOT NULL,
  `ally_tag` varchar(8) NOT NULL,
  `ally_owner` int(11) NOT NULL DEFAULT '0',
  `ally_register_time` int(11) NOT NULL DEFAULT '0',
  `ally_description` text NOT NULL,
  `ally_web` varchar(255) NOT NULL,
  `ally_text` text NOT NULL,
  `ally_image` varchar(255) NOT NULL,
  `ally_request` text NOT NULL,
  `ally_request_notallow` tinyint(1) NOT NULL DEFAULT '0',
  `ally_owner_range` varchar(32) NOT NULL,
  `ally_ranks` text NOT NULL,
  `ally_members` tinyint(3) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `game_alliance_diplomacy`
--

CREATE TABLE IF NOT EXISTS `game_alliance_diplomacy` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `a_id` int(11) NOT NULL DEFAULT '0',
  `d_id` int(11) NOT NULL DEFAULT '0',
  `type` tinyint(1) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `primary` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `a_id` (`a_id`,`d_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `game_alliance_members`
--

CREATE TABLE IF NOT EXISTS `game_alliance_members` (
  `a_id` int(11) NOT NULL DEFAULT '0',
  `u_id` int(11) NOT NULL DEFAULT '0',
  `rank` tinyint(2) NOT NULL DEFAULT '0',
  `time` int(11) NOT NULL DEFAULT '0',
  UNIQUE KEY `u_id` (`u_id`),
  KEY `a_id` (`a_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `game_alliance_requests`
--

CREATE TABLE IF NOT EXISTS `game_alliance_requests` (
  `a_id` int(11) NOT NULL DEFAULT '0',
  `u_id` int(11) NOT NULL DEFAULT '0',
  `time` int(11) NOT NULL DEFAULT '0',
  `request` varchar(255) NOT NULL,
  UNIQUE KEY `a_id` (`a_id`,`u_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `game_banned`
--

CREATE TABLE IF NOT EXISTS `game_banned` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `who` int(11) NOT NULL DEFAULT '0',
  `theme` text NOT NULL,
  `time` int(11) NOT NULL DEFAULT '0',
  `longer` int(11) NOT NULL DEFAULT '0',
  `author` int(11) NOT NULL DEFAULT '0',
  KEY `ID` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `game_buddy`
--

CREATE TABLE IF NOT EXISTS `game_buddy` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `sender` int(11) NOT NULL DEFAULT '0',
  `owner` int(11) NOT NULL DEFAULT '0',
  `ignor` tinyint(1) NOT NULL DEFAULT '0',
  `active` tinyint(3) NOT NULL DEFAULT '0',
  `text` varchar(250) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sender` (`sender`),
  KEY `owner` (`owner`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `game_chat`
--

CREATE TABLE IF NOT EXISTS `game_chat` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ally_id` int(11) unsigned NOT NULL DEFAULT '0',
  `user` varchar(50) NOT NULL,
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `message` text NOT NULL,
  `timestamp` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `ally_id` (`ally_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `game_config`
--

CREATE TABLE IF NOT EXISTS `game_config` (
  `config_name` varchar(64) NOT NULL,
  `config_value` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `game_config`
--

INSERT INTO `game_config` (`config_name`, `config_value`) VALUES
('users_amount', 2),
('LastSettedGalaxyPos', 1),
('LastSettedSystemPos', 1),
('LastSettedPlanetPos', 2),
('online', 0),
('stat_update', 0),
('active_users', 0),
('active_alliance', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `game_errors`
--

CREATE TABLE IF NOT EXISTS `game_errors` (
  `error_id` bigint(11) NOT NULL AUTO_INCREMENT,
  `error_sender` varchar(32) NOT NULL DEFAULT '0',
  `error_time` int(11) NOT NULL DEFAULT '0',
  `error_type` varchar(32) NOT NULL DEFAULT 'unknown',
  `error_text` text,
  PRIMARY KEY (`error_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `game_fleets`
--

CREATE TABLE IF NOT EXISTS `game_fleets` (
  `fleet_id` bigint(11) NOT NULL AUTO_INCREMENT,
  `fleet_owner` int(11) NOT NULL DEFAULT '0',
  `fleet_owner_name` varchar(35) NOT NULL,
  `fleet_mission` int(11) NOT NULL DEFAULT '0',
  `fleet_amount` bigint(11) NOT NULL DEFAULT '0',
  `fleet_array` text,
  `fleet_start_time` int(11) NOT NULL DEFAULT '0',
  `fleet_start_galaxy` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `fleet_start_system` smallint(6) unsigned NOT NULL DEFAULT '0',
  `fleet_start_planet` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `fleet_start_type` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `fleet_end_time` int(11) NOT NULL DEFAULT '0',
  `fleet_end_stay` int(11) NOT NULL DEFAULT '0',
  `fleet_end_galaxy` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `fleet_end_system` smallint(6) unsigned NOT NULL DEFAULT '0',
  `fleet_end_planet` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `fleet_end_type` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `fleet_resource_metal` bigint(11) unsigned NOT NULL DEFAULT '0',
  `fleet_resource_crystal` bigint(11) unsigned NOT NULL DEFAULT '0',
  `fleet_resource_deuterium` bigint(11) unsigned NOT NULL DEFAULT '0',
  `fleet_target_owner` int(11) NOT NULL DEFAULT '0',
  `fleet_target_owner_name` varchar(35) NOT NULL,
  `fleet_group` int(11) NOT NULL DEFAULT '0',
  `fleet_mess` int(11) NOT NULL DEFAULT '0',
  `start_time` int(11) NOT NULL DEFAULT '0',
  `fleet_time` int(11) NOT NULL DEFAULT '0',
  `raunds` tinyint(1) NOT NULL DEFAULT '6',
  `won` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`fleet_id`),
  KEY `fleet_owner` (`fleet_owner`),
  KEY `fleet_target_owner` (`fleet_target_owner`),
  KEY `fleet_time` (`fleet_time`),
  KEY `fleet_end_system` (`fleet_end_system`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `game_hall`
--

CREATE TABLE IF NOT EXISTS `game_hall` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(150) NOT NULL,
  `debris` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  `won` tinyint(1) NOT NULL,
  `sab` tinyint(1) NOT NULL DEFAULT '0',
  `log` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sab` (`sab`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `game_logs`
--

CREATE TABLE IF NOT EXISTS `game_logs` (
  `mission` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `time` int(11) unsigned NOT NULL DEFAULT '0',
  `kolvo` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `s_id` int(11) unsigned NOT NULL DEFAULT '0',
  `s_galaxy` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `s_system` smallint(5) unsigned NOT NULL DEFAULT '0',
  `s_planet` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `e_id` int(11) unsigned NOT NULL DEFAULT '0',
  `e_galaxy` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `e_system` smallint(5) unsigned NOT NULL DEFAULT '0',
  `e_planet` tinyint(2) unsigned NOT NULL DEFAULT '0',
  KEY `time` (`time`),
  KEY `s_id` (`s_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `game_log_attack`
--

CREATE TABLE IF NOT EXISTS `game_log_attack` (
  `uid` int(11) unsigned NOT NULL DEFAULT '0',
  `time` int(11) unsigned NOT NULL DEFAULT '0',
  `planet_start` int(11) unsigned NOT NULL DEFAULT '0',
  `planet_end` int(11) unsigned NOT NULL DEFAULT '0',
  `fleet` varchar(255) NOT NULL,
  `battle_log` int(11) unsigned NOT NULL DEFAULT '0',
  KEY `uid` (`uid`,`time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `game_log_credits`
--

CREATE TABLE IF NOT EXISTS `game_log_credits` (
  `uid` int(11) NOT NULL DEFAULT '0',
  `time` int(11) NOT NULL DEFAULT '0',
  `credits` smallint(6) NOT NULL DEFAULT '0',
  `type` tinyint(1) NOT NULL DEFAULT '0',
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `game_log_email`
--

CREATE TABLE IF NOT EXISTS `game_log_email` (
  `user_id` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  `email` varchar(35) NOT NULL,
  `ok` tinyint(1) NOT NULL DEFAULT '0',
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `game_log_ip`
--

CREATE TABLE IF NOT EXISTS `game_log_ip` (
  `id` int(11) NOT NULL DEFAULT '0',
  `time` int(11) NOT NULL DEFAULT '0',
  `ip` int(11) unsigned NOT NULL DEFAULT '0',
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `game_log_ip`
--

INSERT INTO `game_log_ip` (`id`, `time`, `ip`) VALUES
(1, 1388339735, 2130706433),
(2, 1388340417, 2130706433);

-- --------------------------------------------------------

--
-- Структура таблицы `game_log_username`
--

CREATE TABLE IF NOT EXISTS `game_log_username` (
  `user_id` int(11) NOT NULL DEFAULT '0',
  `time` int(11) NOT NULL DEFAULT '0',
  `username` varchar(35) NOT NULL,
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `game_lostpwd`
--

CREATE TABLE IF NOT EXISTS `game_lostpwd` (
  `u_id` int(11) NOT NULL DEFAULT '0',
  `ks` char(32) NOT NULL,
  `time` int(11) NOT NULL DEFAULT '0',
  `ip` varchar(35) NOT NULL,
  `activ` tinyint(1) NOT NULL DEFAULT '0',
  KEY `u_id` (`u_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `game_messages`
--

CREATE TABLE IF NOT EXISTS `game_messages` (
  `message_id` bigint(11) NOT NULL AUTO_INCREMENT,
  `message_owner` int(11) NOT NULL DEFAULT '0',
  `message_sender` int(11) NOT NULL DEFAULT '0',
  `message_time` int(11) NOT NULL DEFAULT '0',
  `message_type` int(11) NOT NULL DEFAULT '0',
  `message_from` varchar(48) DEFAULT NULL,
  `message_text` text,
  `message_deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`message_id`),
  KEY `message_owner` (`message_owner`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `game_moneys`
--

CREATE TABLE IF NOT EXISTS `game_moneys` (
  `id` bigint(20) NOT NULL DEFAULT '0',
  `ip` varchar(50) NOT NULL,
  `time` bigint(20) NOT NULL DEFAULT '0',
  `referer` varchar(250) NOT NULL,
  `user_agent` varchar(250) NOT NULL,
  KEY `ip` (`ip`),
  KEY `id` (`id`),
  KEY `time` (`time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `game_notes`
--

CREATE TABLE IF NOT EXISTS `game_notes` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `owner` int(11) DEFAULT NULL,
  `time` int(11) DEFAULT NULL,
  `priority` tinyint(1) DEFAULT NULL,
  `title` varchar(32) DEFAULT NULL,
  `text` text,
  PRIMARY KEY (`id`),
  KEY `owner` (`owner`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `game_planets`
--

CREATE TABLE IF NOT EXISTS `game_planets` (
  `id` bigint(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `id_owner` int(11) unsigned DEFAULT NULL,
  `id_level` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `galaxy` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `system` smallint(5) unsigned NOT NULL DEFAULT '0',
  `planet` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `last_update` int(11) DEFAULT NULL,
  `planet_type` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `destruyed` int(11) unsigned NOT NULL DEFAULT '0',
  `b_building` int(11) NOT NULL DEFAULT '0',
  `b_building_id` text NOT NULL,
  `b_tech` int(11) NOT NULL DEFAULT '0',
  `b_tech_id` int(11) NOT NULL DEFAULT '0',
  `b_hangar` int(11) NOT NULL DEFAULT '0',
  `b_hangar_id` text NOT NULL,
  `b_hangar_plus` int(11) NOT NULL DEFAULT '0',
  `image` varchar(32) NOT NULL DEFAULT 'normaltempplanet01',
  `diameter` smallint(6) unsigned NOT NULL DEFAULT '12800',
  `field_current` smallint(6) unsigned NOT NULL DEFAULT '0',
  `field_max` smallint(6) unsigned NOT NULL DEFAULT '163',
  `temp_min` smallint(3) NOT NULL DEFAULT '-17',
  `temp_max` smallint(3) NOT NULL DEFAULT '23',
  `metal` double(32,4) NOT NULL DEFAULT '500.0000',
  `crystal` double(32,4) NOT NULL DEFAULT '500.0000',
  `deuterium` double(32,4) NOT NULL DEFAULT '0.0000',
  `people` double(32,4) NOT NULL DEFAULT '100.0000',
  `energy_ak` double(11,2) NOT NULL DEFAULT '0.00',
  `metal_mine` smallint(6) unsigned NOT NULL DEFAULT '0',
  `crystal_mine` smallint(6) unsigned NOT NULL DEFAULT '0',
  `deuterium_sintetizer` smallint(6) unsigned NOT NULL DEFAULT '0',
  `solar_plant` smallint(6) unsigned NOT NULL DEFAULT '0',
  `ak_station` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `fusion_plant` smallint(6) unsigned NOT NULL DEFAULT '0',
  `robot_factory` smallint(6) unsigned NOT NULL DEFAULT '0',
  `nano_factory` smallint(6) unsigned NOT NULL DEFAULT '0',
  `hangar` smallint(6) unsigned NOT NULL DEFAULT '0',
  `metal_store` smallint(6) unsigned NOT NULL DEFAULT '0',
  `crystal_store` smallint(6) unsigned NOT NULL DEFAULT '0',
  `deuterium_store` smallint(6) unsigned NOT NULL DEFAULT '0',
  `laboratory` smallint(6) unsigned NOT NULL DEFAULT '0',
  `terraformer` smallint(6) unsigned NOT NULL DEFAULT '0',
  `ally_deposit` smallint(6) unsigned NOT NULL DEFAULT '0',
  `silo` smallint(6) unsigned NOT NULL DEFAULT '0',
  `small_ship_cargo` int(11) NOT NULL DEFAULT '0',
  `big_ship_cargo` int(11) NOT NULL DEFAULT '0',
  `light_hunter` int(11) NOT NULL DEFAULT '0',
  `heavy_hunter` int(11) NOT NULL DEFAULT '0',
  `crusher` int(11) NOT NULL DEFAULT '0',
  `battle_ship` int(11) NOT NULL DEFAULT '0',
  `colonizer` int(11) NOT NULL DEFAULT '0',
  `recycler` int(11) NOT NULL DEFAULT '0',
  `spy_sonde` smallint(6) NOT NULL DEFAULT '0',
  `bomber_ship` int(11) NOT NULL DEFAULT '0',
  `solar_satelit` int(11) NOT NULL DEFAULT '0',
  `destructor` int(11) NOT NULL DEFAULT '0',
  `dearth_star` int(11) NOT NULL DEFAULT '0',
  `fly_base` int(11) NOT NULL DEFAULT '0',
  `big_recycler` int(11) NOT NULL DEFAULT '0',
  `interceptor` int(11) NOT NULL DEFAULT '0',
  `dreadnought` int(11) NOT NULL DEFAULT '0',
  `corsair` int(11) NOT NULL DEFAULT '0',
  `misil_launcher` int(11) NOT NULL DEFAULT '0',
  `small_laser` int(11) NOT NULL DEFAULT '0',
  `big_laser` int(11) NOT NULL DEFAULT '0',
  `gauss_canyon` int(11) NOT NULL DEFAULT '0',
  `ionic_canyon` int(11) NOT NULL DEFAULT '0',
  `buster_canyon` int(11) NOT NULL DEFAULT '0',
  `small_protection_shield` int(11) NOT NULL DEFAULT '0',
  `big_protection_shield` int(11) NOT NULL DEFAULT '0',
  `interceptor_misil` int(11) NOT NULL DEFAULT '0',
  `interplanetary_misil` smallint(6) unsigned NOT NULL DEFAULT '0',
  `metal_mine_porcent` tinyint(2) unsigned NOT NULL DEFAULT '10',
  `crystal_mine_porcent` tinyint(2) unsigned NOT NULL DEFAULT '10',
  `deuterium_sintetizer_porcent` tinyint(2) unsigned NOT NULL DEFAULT '10',
  `solar_plant_porcent` tinyint(2) unsigned NOT NULL DEFAULT '10',
  `fusion_plant_porcent` tinyint(2) unsigned NOT NULL DEFAULT '10',
  `solar_satelit_porcent` tinyint(2) unsigned NOT NULL DEFAULT '10',
  `darkmat_mine_porcent` tinyint(2) unsigned NOT NULL DEFAULT '10',
  `mondbasis` smallint(6) unsigned NOT NULL DEFAULT '0',
  `phalanx` smallint(6) unsigned NOT NULL DEFAULT '0',
  `sprungtor` smallint(6) unsigned NOT NULL DEFAULT '0',
  `last_jump_time` int(11) unsigned NOT NULL DEFAULT '0',
  `parent_planet` int(11) NOT NULL DEFAULT '0',
  `debris_metal` int(11) NOT NULL DEFAULT '0',
  `debris_crystal` int(11) NOT NULL DEFAULT '0',
  `merchand` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id_owner` (`id_owner`),
  KEY `galaxy` (`galaxy`),
  KEY `system` (`system`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Дамп данных таблицы `game_planets`
--

INSERT INTO `game_planets` (`id`, `name`, `id_owner`, `id_level`, `galaxy`, `system`, `planet`, `last_update`, `planet_type`, `destruyed`, `b_building`, `b_building_id`, `b_tech`, `b_tech_id`, `b_hangar`, `b_hangar_id`, `b_hangar_plus`, `image`, `diameter`, `field_current`, `field_max`, `temp_min`, `temp_max`, `metal`, `crystal`, `deuterium`, `people`, `energy_ak`, `metal_mine`, `crystal_mine`, `deuterium_sintetizer`, `solar_plant`, `ak_station`, `fusion_plant`, `robot_factory`, `nano_factory`, `hangar`, `metal_store`, `crystal_store`, `deuterium_store`, `laboratory`, `terraformer`, `ally_deposit`, `silo`, `small_ship_cargo`, `big_ship_cargo`, `light_hunter`, `heavy_hunter`, `crusher`, `battle_ship`, `colonizer`, `recycler`, `spy_sonde`, `bomber_ship`, `solar_satelit`, `destructor`, `dearth_star`, `fly_base`, `big_recycler`, `interceptor`, `dreadnought`, `corsair`, `misil_launcher`, `small_laser`, `big_laser`, `gauss_canyon`, `ionic_canyon`, `buster_canyon`, `small_protection_shield`, `big_protection_shield`, `interceptor_misil`, `interplanetary_misil`, `metal_mine_porcent`, `crystal_mine_porcent`, `deuterium_sintetizer_porcent`, `solar_plant_porcent`, `fusion_plant_porcent`, `solar_satelit_porcent`, `darkmat_mine_porcent`, `mondbasis`, `phalanx`, `sprungtor`, `last_jump_time`, `parent_planet`, `debris_metal`, `debris_crystal`, `merchand`) VALUES
(1, 'Главная планета', 1, 0, 1, 1, 1, 1388340457, 1, 0, 0, '', 0, 0, 0, '', 0, 'wasserplanet06', 12750, 3, 163, 10, 50, 489.8242, 523.8166, 0.0000, 100.0000, 0.00, 1, 1, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 10, 10, 10, 10, 10, 10, 10, 0, 0, 0, 0, 0, 0, 0, 0),
(2, 'Главная планета', 2, 0, 1, 1, 10, 1388340430, 1, 0, 0, '', 0, 0, 0, '', 0, 'wasserplanet09', 12750, 0, 163, 11, 51, 501.1667, 500.5833, 0.0000, 100.0000, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 10, 10, 10, 10, 10, 10, 10, 0, 0, 0, 0, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `game_private`
--

CREATE TABLE IF NOT EXISTS `game_private` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `u_id` int(11) NOT NULL DEFAULT '0',
  `a_id` int(11) NOT NULL DEFAULT '0',
  `text` varchar(255) NOT NULL,
  `time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `u_id` (`u_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `game_refs`
--

CREATE TABLE IF NOT EXISTS `game_refs` (
  `r_id` int(11) unsigned NOT NULL DEFAULT '0',
  `u_id` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`r_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `game_rw`
--

CREATE TABLE IF NOT EXISTS `game_rw` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_users` varchar(255) NOT NULL,
  `raport` text NOT NULL,
  `no_contact` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `game_savelog`
--

CREATE TABLE IF NOT EXISTS `game_savelog` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL,
  `log` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user` (`user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `game_statpoints`
--

CREATE TABLE IF NOT EXISTS `game_statpoints` (
  `id_owner` int(11) NOT NULL DEFAULT '0',
  `username` varchar(35) NOT NULL,
  `race` tinyint(1) NOT NULL DEFAULT '0',
  `id_ally` int(11) NOT NULL DEFAULT '0',
  `ally_name` varchar(50) NOT NULL,
  `stat_type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `stat_code` int(11) NOT NULL DEFAULT '0',
  `tech_rank` smallint(6) unsigned NOT NULL DEFAULT '0',
  `tech_old_rank` smallint(6) unsigned NOT NULL DEFAULT '0',
  `tech_points` bigint(20) NOT NULL DEFAULT '0',
  `tech_count` int(11) NOT NULL DEFAULT '0',
  `build_rank` smallint(6) unsigned NOT NULL DEFAULT '0',
  `build_old_rank` smallint(6) unsigned NOT NULL DEFAULT '0',
  `build_points` bigint(20) NOT NULL DEFAULT '0',
  `build_count` int(11) NOT NULL DEFAULT '0',
  `defs_rank` smallint(6) unsigned NOT NULL DEFAULT '0',
  `defs_old_rank` smallint(6) unsigned NOT NULL DEFAULT '0',
  `defs_points` bigint(20) NOT NULL DEFAULT '0',
  `defs_count` int(11) NOT NULL DEFAULT '0',
  `fleet_rank` smallint(6) unsigned NOT NULL DEFAULT '0',
  `fleet_old_rank` smallint(6) unsigned NOT NULL DEFAULT '0',
  `fleet_points` bigint(20) NOT NULL DEFAULT '0',
  `fleet_count` int(11) NOT NULL DEFAULT '0',
  `total_rank` smallint(6) unsigned NOT NULL DEFAULT '0',
  `total_old_rank` smallint(6) unsigned NOT NULL DEFAULT '0',
  `total_points` bigint(20) NOT NULL DEFAULT '0',
  `total_count` int(11) NOT NULL DEFAULT '0',
  `stat_hide` tinyint(1) NOT NULL DEFAULT '0',
  KEY `stat_type` (`stat_type`),
  KEY `id_owner` (`id_owner`),
  KEY `total_rank` (`total_rank`),
  KEY `tech_rank` (`tech_rank`),
  KEY `defs_rank` (`defs_rank`),
  KEY `fleet_rank` (`fleet_rank`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `game_support`
--

CREATE TABLE IF NOT EXISTS `game_support` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `player_id` int(11) NOT NULL DEFAULT '9',
  `time` int(11) NOT NULL DEFAULT '0',
  `subject` varchar(255) NOT NULL,
  `text` text NOT NULL,
  `status` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`ID`),
  KEY `player_id` (`player_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `game_users`
--

CREATE TABLE IF NOT EXISTS `game_users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL,
  `authlevel` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `sex` tinyint(1) NOT NULL DEFAULT '0',
  `race` tinyint(1) NOT NULL DEFAULT '0',
  `id_planet` int(11) unsigned NOT NULL DEFAULT '0',
  `galaxy` int(11) unsigned NOT NULL DEFAULT '0',
  `system` int(11) unsigned NOT NULL DEFAULT '0',
  `planet` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `current_planet` int(11) NOT NULL DEFAULT '0',
  `user_lastip` int(11) unsigned NOT NULL DEFAULT '0',
  `onlinetime` int(11) unsigned NOT NULL DEFAULT '0',
  `new_message` smallint(6) unsigned NOT NULL DEFAULT '0',
  `mnl_alliance` smallint(5) unsigned NOT NULL DEFAULT '0',
  `b_tech_planet` int(11) NOT NULL DEFAULT '0',
  `spy_tech` smallint(5) unsigned NOT NULL DEFAULT '0',
  `computer_tech` smallint(5) unsigned NOT NULL DEFAULT '0',
  `military_tech` smallint(5) unsigned NOT NULL DEFAULT '0',
  `defence_tech` smallint(5) unsigned NOT NULL DEFAULT '0',
  `shield_tech` smallint(5) unsigned NOT NULL DEFAULT '0',
  `energy_tech` smallint(5) unsigned NOT NULL DEFAULT '0',
  `hyperspace_tech` smallint(5) unsigned NOT NULL DEFAULT '0',
  `combustion_tech` smallint(5) unsigned NOT NULL DEFAULT '0',
  `impulse_motor_tech` smallint(5) unsigned NOT NULL DEFAULT '0',
  `hyperspace_motor_tech` smallint(5) unsigned NOT NULL DEFAULT '0',
  `laser_tech` smallint(5) unsigned NOT NULL DEFAULT '0',
  `ionic_tech` smallint(5) unsigned NOT NULL DEFAULT '0',
  `buster_tech` smallint(5) unsigned NOT NULL DEFAULT '0',
  `intergalactic_tech` smallint(5) unsigned NOT NULL DEFAULT '0',
  `expedition_tech` smallint(5) unsigned NOT NULL DEFAULT '0',
  `colonisation_tech` smallint(5) unsigned NOT NULL DEFAULT '0',
  `graviton_tech` smallint(5) unsigned NOT NULL DEFAULT '0',
  `fleet_base_tech` smallint(6) unsigned NOT NULL DEFAULT '0',
  `fleet_202` tinyint(1) NOT NULL DEFAULT '0',
  `fleet_203` tinyint(1) NOT NULL DEFAULT '0',
  `fleet_204` tinyint(1) NOT NULL DEFAULT '0',
  `fleet_205` tinyint(1) NOT NULL DEFAULT '0',
  `fleet_206` tinyint(1) NOT NULL DEFAULT '0',
  `fleet_207` tinyint(1) NOT NULL DEFAULT '0',
  `fleet_211` tinyint(1) NOT NULL DEFAULT '0',
  `fleet_213` tinyint(1) NOT NULL DEFAULT '0',
  `fleet_214` tinyint(1) NOT NULL DEFAULT '0',
  `fleet_221` tinyint(1) NOT NULL DEFAULT '0',
  `fleet_222` tinyint(1) NOT NULL DEFAULT '0',
  `fleet_223` tinyint(1) NOT NULL DEFAULT '0',
  `ally_id` int(11) NOT NULL DEFAULT '0',
  `ally_name` varchar(32) NOT NULL,
  `rpg_geologue` int(11) unsigned NOT NULL DEFAULT '0',
  `rpg_admiral` int(11) unsigned NOT NULL DEFAULT '0',
  `rpg_ingenieur` int(11) unsigned NOT NULL DEFAULT '0',
  `rpg_technocrate` int(11) unsigned NOT NULL DEFAULT '0',
  `rpg_constructeur` int(11) unsigned NOT NULL DEFAULT '0',
  `rpg_meta` int(11) unsigned NOT NULL DEFAULT '0',
  `rpg_komandir` int(11) NOT NULL,
  `lvl_minier` smallint(6) unsigned NOT NULL DEFAULT '1',
  `lvl_raid` smallint(6) unsigned NOT NULL DEFAULT '1',
  `xpraid` int(11) NOT NULL DEFAULT '0',
  `xpminier` int(11) NOT NULL DEFAULT '0',
  `raids_win` smallint(6) unsigned NOT NULL DEFAULT '0',
  `raids_lose` smallint(6) unsigned NOT NULL DEFAULT '0',
  `raids` int(11) NOT NULL DEFAULT '0',
  `credits` smallint(6) NOT NULL DEFAULT '0',
  `urlaubs_modus_time` int(11) NOT NULL DEFAULT '0',
  `deltime` int(11) NOT NULL DEFAULT '0',
  `banaday` int(11) unsigned NOT NULL DEFAULT '0',
  `links` int(11) unsigned NOT NULL DEFAULT '0',
  `chat` tinyint(1) NOT NULL DEFAULT '0',
  `avatar` smallint(6) NOT NULL DEFAULT '0',
  `tutorial` tinyint(2) NOT NULL DEFAULT '0',
  `tutorial_value` tinyint(2) NOT NULL DEFAULT '0',
  `bonus` int(11) unsigned NOT NULL DEFAULT '0',
  `bonus_multi` tinyint(2) NOT NULL DEFAULT '0',
  `refers` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `username` (`username`),
  KEY `ally_id` (`ally_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Дамп данных таблицы `game_users`
--

INSERT INTO `game_users` (`id`, `username`, `authlevel`, `sex`, `race`, `id_planet`, `galaxy`, `system`, `planet`, `current_planet`, `user_lastip`, `onlinetime`, `new_message`, `mnl_alliance`, `b_tech_planet`, `spy_tech`, `computer_tech`, `military_tech`, `defence_tech`, `shield_tech`, `energy_tech`, `hyperspace_tech`, `combustion_tech`, `impulse_motor_tech`, `hyperspace_motor_tech`, `laser_tech`, `ionic_tech`, `buster_tech`, `intergalactic_tech`, `expedition_tech`, `colonisation_tech`, `graviton_tech`, `fleet_base_tech`, `fleet_202`, `fleet_203`, `fleet_204`, `fleet_205`, `fleet_206`, `fleet_207`, `fleet_211`, `fleet_213`, `fleet_214`, `fleet_221`, `fleet_222`, `fleet_223`, `ally_id`, `ally_name`, `rpg_geologue`, `rpg_admiral`, `rpg_ingenieur`, `rpg_technocrate`, `rpg_constructeur`, `rpg_meta`, `rpg_komandir`, `lvl_minier`, `lvl_raid`, `xpraid`, `xpminier`, `raids_win`, `raids_lose`, `raids`, `credits`, `urlaubs_modus_time`, `deltime`, `banaday`, `links`, `chat`, `avatar`, `tutorial`, `tutorial_value`, `bonus`, `bonus_multi`, `refers`) VALUES
(1, 'admin', 3, 1, 1, 1, 1, 1, 1, 1, 2130706433, 1388340615, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1388339413, 0, 0),
(2, 'test', 0, 1, 1, 2, 1, 1, 10, 2, 2130706433, 1388340417, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1388340416, 0, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `game_users_inf`
--

CREATE TABLE IF NOT EXISTS `game_users_inf` (
  `id` int(11) NOT NULL DEFAULT '0',
  `password` char(32) NOT NULL,
  `password_vk` varchar(10) NOT NULL,
  `email` varchar(35) NOT NULL,
  `vk_reg_id` int(11) NOT NULL DEFAULT '0',
  `register_time` int(11) NOT NULL DEFAULT '0',
  `vkontakte` int(11) NOT NULL DEFAULT '0',
  `icq` int(11) NOT NULL DEFAULT '0',
  `fleet_shortcut` text NOT NULL,
  `about` text NOT NULL,
  `dpath` tinyint(1) NOT NULL DEFAULT '0',
  `design` tinyint(1) NOT NULL DEFAULT '1',
  `security` tinyint(1) NOT NULL DEFAULT '0',
  `widescreen` tinyint(1) NOT NULL DEFAULT '0',
  `planet_sort` tinyint(1) NOT NULL DEFAULT '0',
  `planet_sort_order` tinyint(1) NOT NULL DEFAULT '0',
  `color` tinyint(2) NOT NULL DEFAULT '1',
  `records` tinyint(1) NOT NULL DEFAULT '1',
  `username_last` int(11) NOT NULL DEFAULT '0',
  `free_race_change` tinyint(1) NOT NULL DEFAULT '1',
  `timezone` tinyint(2) NOT NULL DEFAULT '0',
  `bb_parser` tinyint(1) NOT NULL DEFAULT '1',
  `ajax_navigation` tinyint(1) NOT NULL DEFAULT '0',
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `game_users_inf`
--

INSERT INTO `game_users_inf` (`id`, `password`, `password_vk`, `email`, `vk_reg_id`, `register_time`, `vkontakte`, `icq`, `fleet_shortcut`, `about`, `dpath`, `design`, `security`, `widescreen`, `planet_sort`, `planet_sort_order`, `color`, `records`, `username_last`, `free_race_change`, `timezone`, `bb_parser`, `ajax_navigation`) VALUES
(1, '21232f297a57a5a743894a0e4a801fc3', '', 'admin', 0, 1388339413, 0, 0, '', '', 0, 1, 0, 0, 0, 0, 1, 1, 0, 1, 0, 1, 0),
(2, '098f6bcd4621d373cade4e832627b4f6', '', 'test@test.ru', 0, 1388340416, 0, 0, '', '', 0, 1, 0, 0, 0, 0, 1, 1, 0, 1, 0, 1, 0);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
