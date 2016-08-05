<?php

/**
 * @author AlexPro
 * @copyright 2008 - 2011 XNova Game Group
 * @var $Display HSTemplateDisplay
 * @var $user user
 * @var $game_config array
 * @var $planetrow planet
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

if(!defined("INSIDE")) die("attemp hacking");

$g = intval($_GET['galaxy']);
$s = intval($_GET['system']);
$i = intval($_GET['planet']);
$anz = intval($_POST['SendMI']);
$pziel = $_POST['Target'];

$tempvar1 = (($s - $planetrow->data['system']) * (-1));
$tempvar2 = ($user->data['impulse_motor_tech'] * 5) - 1;
$tempvar3 = db::query("SELECT * FROM {{table}} WHERE galaxy = ".$g." AND system = ".$s." AND planet = ".$i." AND planet_type = 1", 'planets', true);

$error = 0;

if ($planetrow->data['silo'] < 4) {
	$error = 1;
} elseif ($user->data['impulse_motor_tech'] == 0) {
	$error = 2;
} elseif ($tempvar1 >= $tempvar2 || $g != $planetrow->data['galaxy']) {
	$error = 3;
} elseif (!isset($tempvar3['id'])) {
	$error = 4;
} elseif ($anz > $planetrow->data['interplanetary_misil']) {
	$error = 5;
} elseif ((!is_numeric($pziel) && $pziel != "all") OR ($pziel < 0 && $pziel > 7 && $pziel != "all")) {
	$error = 6;
}

if ($error != 0)
	message('Возможно у вас нет столько межпланетных ракет, или вы не имеете достоточно развитую технологию импульсного двигателя, или вводите неккоректные данные при отправке.', 'Ошибка '.$error.'');

if ($pziel == "all")
	$pziel = 0;
else
	$pziel = intval($pziel);

$select = db::query("SELECT id, urlaubs_modus_time FROM {{table}} WHERE id = ".$tempvar3['id_owner'], 'users', true);

if (!isset($select['id']))
	message('Игрока не существует');

if ($select['urlaubs_modus_time'] > 0)
	message('Игрок в режиме отпуска');

if ($user->data['urlaubs_modus_time'] > 0)
	message('Вы в режиме отпуска');

$flugzeit = round(((30 + (60 * $tempvar1)) * 2500) / $game_config['game_speed']);

$QryInsertFleet  = "INSERT INTO {{table}} SET ";
$QryInsertFleet .= "`fleet_owner` = '". $user->data['id'] ."', ";
$QryInsertFleet .= "`fleet_owner_name` = '". $planetrow->data['name'] ."', ";
$QryInsertFleet .= "`fleet_mission` = '20', ";
$QryInsertFleet .= "`fleet_array` = '503,".$anz."!".$pziel."', ";
$QryInsertFleet .= "`fleet_start_time` = '". (time() + $flugzeit) ."', ";
$QryInsertFleet .= "`fleet_start_galaxy` = '". $planetrow->data['galaxy'] ."', ";
$QryInsertFleet .= "`fleet_start_system` = '". $planetrow->data['system'] ."', ";
$QryInsertFleet .= "`fleet_start_planet` = '". $planetrow->data['planet'] ."', ";
$QryInsertFleet .= "`fleet_start_type` = '1', ";
$QryInsertFleet .= "`fleet_end_time` = '". (time() + $flugzeit + 3600) ."', ";
$QryInsertFleet .= "`fleet_end_galaxy` = '". $g ."', ";
$QryInsertFleet .= "`fleet_end_system` = '". $s ."', ";
$QryInsertFleet .= "`fleet_end_planet` = '". $i ."', ";
$QryInsertFleet .= "`fleet_end_type` = '1', ";
$QryInsertFleet .= "`fleet_target_owner` = '". $tempvar3['id_owner'] ."', ";
$QryInsertFleet .= "`fleet_target_owner_name` = '". $tempvar3['name'] ."', ";
$QryInsertFleet .= "`start_time` = '". time() ."', fleet_time = '". (time() + $flugzeit) ."';";
db::query( $QryInsertFleet, 'fleets');

db::query("UPDATE {{table}} SET interplanetary_misil = interplanetary_misil - ".$anz." WHERE id = '".$user->data['current_planet']."'", 'planets');

$Display->addTemplate('rak', 'rak.php');
$Display->assign('anz', $anz, 'rak');

display('','Межпланетная атака', false);

?>
