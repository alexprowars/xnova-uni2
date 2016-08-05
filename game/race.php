<?php

/**
 * @author AlexPro
 * @copyright 2008 - 2011 XNova Game Group
 * @var $Display HSTemplateDisplay
 * @var $user user
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

	$ui = db::query('SELECT free_race_change FROM {{table}} WHERE id = '.$user->data['id'].';', 'users_inf', true);

	if (isset($_GET['sel']) && $user->data['race'] == 0) {
		$r = intval($_GET['sel']);
		$r = ($r < 1 || $r > 4) ? 0 : $r;

		if ($r != 0) {
			db::query("UPDATE {{table}} SET race = ".$r." WHERE id = ".$user->data['id'].";", "users");

            system::Redirect("?set=overview");
		}
	}

	if (isset($_GET['mode']) && isset($_POST['race']) && $user->data['race'] != 0 && $ui['free_race_change'] > 0) {
		$r = intval($_POST['race']);
		$r = ($r < 1 || $r > 4) ? 0 : $r;

		if ($r != 0) {
			$BuildOnPlanet = db::query("SELECT `id` FROM {{table}} WHERE (`b_building` != 0 OR `b_tech` != 0 OR `b_hangar_id` != '') AND `id_owner` = '".$user->data['id']."'", "planets");
			$UserFlyingFleets = db::query("SELECT `fleet_id` FROM {{table}} WHERE `fleet_owner` = '".$user->data['id']."'", "fleets");
			if (db::num_rows($BuildOnPlanet) > 0){
				message('Для смены фракции y вac нe дoлжнo идти cтpoитeльcтвo или иccлeдoвaниe нa плaнeтe.', "Oшибкa", "?set=race", 5);
			} elseif (db::num_rows($UserFlyingFleets) > 0) {
				message('Для смены фракции y вac нe дoлжeн нaxoдитьcя флoт в пoлeтe.', "Oшибкa", "?set=race", 5);
			} else {
				db::query("UPDATE {{table}} SET race = ".$r." WHERE id = ".$user->data['id'].";", "users");
				db::query("UPDATE {{table}} SET free_race_change = 0 WHERE id = ".$user->data['id'].";", "users_inf");
				db::query("UPDATE {{table}} SET big_recycler = 0, interceptor = 0, dreadnought = 0, corsair = 0 WHERE id_owner = ".$user->data['id'].";", "planets");

                system::Redirect("?set=overview");
			}
		}
	}

	$Display->addTemplate('race', 'race.php');

	$Display->assign('race', $user->data['race'], 'race');
	$Display->assign('free_race_change', $ui['free_race_change'], 'race');

	if ($user->data['race'] == 0)
		display('', 'Фракции', false, false);
	else
		display('', 'Фракции', false);

 ?>
