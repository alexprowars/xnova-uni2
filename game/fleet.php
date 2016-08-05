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

include('includes/functions_fleet.php');
// Устанавливаем обновлённые двигателя кораблей
SetShipsEngine($user->data);

$page = (isset($_GET['page'])) ? $_GET['page'] : '';

switch ($page)
{
	case 'fleet_1':
		include('game/fleet/fleet_1.php');
		break;
	case 'fleet_2':
		include('game/fleet/fleet_2.php');
		break;
	case 'fleet_3':
		include('game/fleet/fleet_3.php');
		break;
	case 'back':
		include('game/fleet/back.php');
		break;
	case 'quick':
		include('game/fleet/quick.php');
		break;
	case 'shortcut':
		include('game/fleet/shortcut.php');
		break;
	case 'verband':
		include('game/fleet/verband.php');
		break;
	default:
		include('game/fleet/fleet_0.php');
}

?>
