<?php

/**
 * @author AlexPro
 * @copyright 2008 - 2011 XNova Game Group
 * @var $Display HSTemplateDisplay
 * @var $user user
 * @var $planetrow planet
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

if(!defined("INSIDE")) die("attemp hacking");

if ($user->data['urlaubs_modus_time'] > 0) {
	message("Нет доступа!");
}

system::includeLang('buildings');

$building = new building();
$building->planet = $planetrow;
$building->user = $user;

$mode = (isset($_GET['mode'])) ? $_GET['mode'] : '';

switch ($mode) {
	case 'fleet':
		$building->Page_Fleet();
		break;
	case 'research':
	case 'research_fleet':
		$building->Page_Research();
		break;
	case 'defense':
		$building->Page_Defense();
		break;
	default:
		$building->Page_Building();
		break;
}

?>
