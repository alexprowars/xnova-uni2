<?php

/**
 * @author AlexPro
 * @copyright 2008 - 2011 XNova Game Group
 * @var $Display HSTemplateDisplay
 * @var $user user
 * @var $lang array
 * @var $planetrow planet
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

if(!defined("INSIDE")) die("attemp hacking");

//if ($user->data['username'] != "AlexPro") die();

	system::includeLang('marchand');

	$parse   = $lang;
	$Message = '';

	if (isset($_POST['ress']) && $user->data['credits'] > 0) {
		$Error   = false;

		$metal      = (isset($_POST['metal'])) ? intval($_POST['metal']) : 0;
		$cristal    = (isset($_POST['cristal'])) ? intval($_POST['cristal']) : 0;
		$deut       = (isset($_POST['deut'])) ? intval($_POST['deut']) : 0;

		switch ($_POST['ress']) {
			case 'metal':
				$Necessaire   = ($cristal * 2) + ($deut * 4);
				if ($cristal < 0 || $deut < 0 || $metal != 0 || $Necessaire == 0){
					$Message 	= "Failed";
					$Error   	= true;
				} elseif ($planetrow->data['metal'] > $Necessaire) {
					$planetrow->data['metal'] -= $Necessaire;
				} else {
					$Message 	= $lang['mod_ma_noten'] ." ". $lang['Metal'] ."! ";
					$Error   	= true;
				}
				break;

			case 'cristal':
				$Necessaire   = ($metal * 0.5) + ($deut * 2);
				if($metal < 0 || $deut < 0 || $cristal != 0 || $Necessaire == 0){
					$Message 	= "Failed";
					$Error   	= true;
				} elseif ($planetrow->data['crystal'] > $Necessaire) {
					$planetrow->data['crystal'] -= $Necessaire;
				} else {
					$Message 	= $lang['mod_ma_noten'] ." ". $lang['Crystal'] ."! ";
					$Error   	= true;
				}
				break;

			case 'deuterium':
				$Necessaire   = ($metal * 0.25) + ($cristal * 0.5);
				if($metal < 0 || $cristal < 0 || $deut != 0 || $Necessaire == 0){
					$Message 	= "Failed";
					$Error   	= true;
				} elseif ($planetrow->data['deuterium'] > $Necessaire) {
					$planetrow->data['deuterium'] -= $Necessaire;
				} else {
					$Message 	= $lang['mod_ma_noten'] ." ". $lang['Deuterium'] ."! ";
					$Error   	= true;
				}
				break;

			default :
				$Message = "Ошибочная операция";
				$Error   = true;
			break;

		}
		if ($Error == false) {
			if ($_POST['ress'] != "metal") 	$planetrow->data['metal']     += $metal;
			if ($_POST['ress'] != "cristal") 	$planetrow->data['crystal']   += $cristal;
			if ($_POST['ress'] != "deuterium") $planetrow->data['deuterium'] += $deut;

			$QryUpdatePlanet  = "UPDATE {{table}} SET ";
			$QryUpdatePlanet .= "`metal` = '".     $planetrow->data['metal']     ."', ";
			$QryUpdatePlanet .= "`crystal` = '".   $planetrow->data['crystal']   ."', ";
			$QryUpdatePlanet .= "`deuterium` = '". $planetrow->data['deuterium'] ."' ";
			$QryUpdatePlanet .= "WHERE ";
			$QryUpdatePlanet .= "`id` = '".        $planetrow->data['id']        ."';";
			db::query ( $QryUpdatePlanet , 'planets');
			
			db::query("UPDATE {{table}} SET `credits` = `credits` - 1 WHERE id = ".$user->data['id']."", "users");
			$user->data['credits'] -= 1;
			
			if ($user->data['tutorial'] == 5 && $user->data['tutorial_value'] == 0)
				db::query("UPDATE {{table}} SET tutorial_value = 1 WHERE id = ".$user->data['id'].";", "users");
			
			$Message = $lang['mod_ma_done'];
		}
		if ($Error == true) {
			$parse['title'] = $lang['mod_ma_error'];
		} else {
			$parse['title'] = $lang['mod_ma_donet'];
		}
		$parse['mes']   = $Message;

        message($parse['mes']  , $parse['title'], '?set=marchand', 2);
	} elseif (isset($_POST['choix'])) {
		$parse['mod_ma_res']   = "1";
		$parse['type'] = $_POST['choix'];
		switch ($_POST['choix']) {
			case 'metal':
				$parse['mod_ma_res_a'] = "2";
				$parse['mod_ma_res_b'] = "4";
			break;
			case 'cristal':
				$parse['mod_ma_res_a'] = "0.5";
				$parse['mod_ma_res_b'] = "2";
			break;
			case 'deut':
				$parse['mod_ma_res_a'] = "0.25";
				$parse['mod_ma_res_b'] = "0.5";
			break;
			default:
				message('Злобный читер!'  , 'Ошибка', '?set=marchand', 2);
			break;
		}
	} else
		$parse['type'] = 'main';

	$Display->addTemplate('merchand', 'merchand.php');
    $Display->assign('parse', $parse, 'merchand');

	display ( '', 'Торговец' );

?>
