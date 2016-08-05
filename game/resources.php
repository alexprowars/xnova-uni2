<?php

/**
 * @author AlexPro
 * @copyright 2008 - 2011 XNova Game Group
 * @var $Display HSTemplateDisplay
 * @var $game_config array
 * @var $user user
 * @var $planetrow planet
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

if(!defined("INSIDE")) die("attemp hacking");

	if ($planetrow->data['planet_type'] == 3 || $planetrow->data['planet_type'] == 5) 
	{
		$game_config['metal_basic_income']     = 0;
		$game_config['crystal_basic_income']   = 0;
		$game_config['deuterium_basic_income'] = 0;
	}

    $CurrentUser['energy_tech'] = $user->data['energy_tech'];
	$ValidList['percent'] = array (  0,  1,  2,  3,  4,  5,  6,  7,  8,  9, 10 );
	$SubQry               = "";
	
	if (isset($_GET['production_full']) || isset($_GET['production_empty'])) {
	
	    if ($user->data['urlaubs_modus_time'] > 0) {
            message("Включен режим отпуска!");
        }
		
		$planets = db::query("SELECT * FROM {{table}} WHERE `id_owner` = '".$user->data['id']."'", "planets");
		
		$pl_class = new planet();
		
		while ($planet = db::fetch_assoc($planets)) 
		{
			$pl_class->load_from_array($planet);
			$pl_class->load_user_info($user);
			$pl_class->PlanetResourceUpdate();
		}

		if (isset($_GET['production_full']))
		{
			db::query("UPDATE {{table}} SET `metal_mine_porcent` = '10', `crystal_mine_porcent` = '10', `deuterium_sintetizer_porcent` = '10', `solar_plant_porcent` = '10', `fusion_plant_porcent` = '10', `solar_satelit_porcent` = '10' WHERE `id_owner` = '".$user->data['id']."'", "planets");

			$planetrow->data['metal_mine_porcent'] 			 = 10;
			$planetrow->data['crystal_mine_porcent'] 		 = 10;
			$planetrow->data['deuterium_sintetizer_porcent'] = 10;
			$planetrow->data['solar_plant_porcent'] 		 = 10;
			$planetrow->data['fusion_plant_porcent'] 		 = 10;
			$planetrow->data['solar_satelit_porcent'] 		 = 10;
		}
		else
		{
			db::query("UPDATE {{table}} SET `metal_mine_porcent` = '0', `crystal_mine_porcent` = '0', `deuterium_sintetizer_porcent` = '0', `solar_plant_porcent` = '0', `fusion_plant_porcent` = '0', `solar_satelit_porcent` = '0' WHERE `id_owner` = '".$user->data['id']."'", "planets");

			$planetrow->data['metal_mine_porcent'] 			 = 0;
			$planetrow->data['crystal_mine_porcent'] 		 = 0;
			$planetrow->data['deuterium_sintetizer_porcent'] = 0;
			$planetrow->data['solar_plant_porcent'] 		 = 0;
			$planetrow->data['fusion_plant_porcent'] 		 = 0;
			$planetrow->data['solar_satelit_porcent'] 		 = 0;
		}

		$planetrow->PlanetResourceUpdate (time(), true);
	}
 
	if ($_POST) {

        if ($user->data['urlaubs_modus_time'] > 0) {
            message("Включен режим отпуска!");
        }

		foreach($_POST as $Field => $Value) {
			$FieldName = $Field."_porcent";
			if (isset($planetrow->data[$FieldName]) && in_array($Value, $ValidList['percent'])) {
				$planetrow->data[$FieldName]  = $Value;
				$SubQry                .= ", `".$FieldName."` = '".$Value."'";
			}
		}

        if ($SubQry != '') {
            $QryUpdatePlanet  = "UPDATE {{table}} SET ";
            $QryUpdatePlanet .= "`name` = '".$planetrow->data['name']."'";
            $QryUpdatePlanet .= $SubQry;
            $QryUpdatePlanet .= "WHERE ";
            $QryUpdatePlanet .= "`id` = '". $planetrow->data['id'] ."';";
            db::query( $QryUpdatePlanet, 'planets');
        }

		$planetrow->PlanetResourceUpdate(time(), true);
	}

	$parse              = $lang;
    $ProductionTime     = (time() - $planetrow->data['last_update']);

	$production_level = $planetrow->data['production_level'];

    $parse['bonus_h'] = ($user->bonus_storage - 1) * 100;

	// -------------------------------------------------------------------------------------------------------
	$parse['resource_row']          = array();
	$BuildTemp                      = $planetrow->data['temp_max'];
	$energy_tech					= $user->data['energy_tech'];

    foreach($reslist['prod'] as $ProdID)
	{
        if ($planetrow->data[$resource[$ProdID]] > 0 && isset($ProdGrid[$ProdID]))
		{
			$BuildLevelFactor                    = $planetrow->data[ $resource[$ProdID]."_porcent" ];
			$BuildLevel                          = $planetrow->data[ $resource[$ProdID] ];

			$metal     = floor( eval ( $ProdGrid[$ProdID]['metal']     ) * ( $game_config['resource_multiplier'] ) * $user->bonus_metal);
			$crystal   = floor( eval ( $ProdGrid[$ProdID]['crystal']   ) * ( $game_config['resource_multiplier'] ) * $user->bonus_crystal);
			$deuterium = floor( eval ( $ProdGrid[$ProdID]['deuterium'] ) * ( $game_config['resource_multiplier'] ) * $user->bonus_deuterium);
			$energy    = floor( eval ( $ProdGrid[$ProdID]['energy']    ) * ( $game_config['resource_multiplier'] ) );

			$metal 		= round($metal     * 0.01 * $production_level);
			$crystal 	= round($crystal   * 0.01 * $production_level);
			$deuterium	= round($deuterium * 0.01 * $production_level);

			if ($ProdID == 4 || $ProdID == 12)
				$energy  = floor($energy * $user->bonus_energy);
			elseif ($ProdID == 212)
				$energy  = floor($energy * $user->bonus_solar);

			$Field                               = $resource[$ProdID] ."_porcent";
			$CurrRow                             = array();
			$CurrRow['name']                     = $resource[$ProdID];
			$CurrRow['porcent']                  = $planetrow->data[$Field];

			$CurrRow['option']                   = '';
			for ($Option = 10; $Option >= 0; $Option--) {
				$CurrRow['option'] .= "<option value=\"".$Option."\"".((($Option == $CurrRow['porcent'])) ? ' selected=selected' : '').">".($Option * 10)."%</option>";
			}

			$CurrRow['type']                     = $lang['tech'][$ProdID];
			$CurrRow['bonus']                    = ($ProdID == 4 || $ProdID == 12 || $ProdID == 212) ? (($ProdID == 212) ? $user->bonus_solar : $user->bonus_energy) : (($ProdID == 1) ? $user->bonus_metal : (($ProdID == 2) ? $user->bonus_crystal : (($ProdID == 3) ? $user->bonus_deuterium : 0)));

			$CurrRow['bonus']					 = ($CurrRow['bonus'] - 1) * 100;
			$CurrRow['level_type']               = $planetrow->data[ $resource[$ProdID] ];

			$CurrRow['metal_type']               = colorNumber(pretty_number(abs($metal)));
			$CurrRow['crystal_type']             = colorNumber(pretty_number(abs($crystal)));
			$CurrRow['deuterium_type']           = colorNumber(pretty_number($deuterium));
			$CurrRow['energy_type']              = colorNumber(pretty_number($energy));

			$parse['resource_row'][]            = $CurrRow;
        }
    }

	$parse['Production_of_resources_in_the_planet'] = 'Производство на планете '.$planetrow->data['name'];

	$parse['metal_basic_income']     = $game_config['metal_basic_income']     * $game_config['resource_multiplier'];
	$parse['crystal_basic_income']   = $game_config['crystal_basic_income']   * $game_config['resource_multiplier'];
	$parse['deuterium_basic_income'] = $game_config['deuterium_basic_income'] * $game_config['resource_multiplier'];
	$parse['energy_basic_income']    = $game_config['energy_basic_income']    * $game_config['resource_multiplier'];

	$parse['metal_max']         = '<font color="#'.(($planetrow->data['metal_max'] < $planetrow->data['metal']) ? 'ff00' : '00ff').'00">';
	$parse['metal_max']        .= pretty_number($planetrow->data['metal_max'] / 1000) ." k</font>";

	$parse['crystal_max']       = '<font color="#'.(($planetrow->data['crystal_max'] < $planetrow->data['crystal']) ? 'ff00' : '00ff').'00">';
	$parse['crystal_max']      .= pretty_number($planetrow->data['crystal_max'] / 1000) ." k</font>";

	$parse['deuterium_max']     = '<font color="#'.(($planetrow->data['deuterium_max'] < $planetrow->data['deuterium']) ? 'ff00' : '00ff').'00">';
    $parse['deuterium_max']    .= pretty_number($planetrow->data['deuterium_max'] / 1000) ." k</font>";

	$metal_total                = $planetrow->data['metal_perhour'] + $parse['metal_basic_income'];
	$crystal_total              = $planetrow->data['crystal_perhour'] + $parse['crystal_basic_income'];
	$deuterium_total            = $planetrow->data['deuterium_perhour'] + $parse['deuterium_basic_income'];
	
	if (isset($_GET['buy']) && $planetrow->data['id'] > 0) {
	
	    if ($user->data['urlaubs_modus_time'] > 0) {
            message("Включен режим отпуска!");
        }
	
		if ($user->data['credits'] >= 10) {
			if ($planetrow->data['merchand'] < time()) {
				db::query('UPDATE {{table}} SET metal = metal + '.($metal_total * 8).', crystal = crystal + '.($crystal_total * 8).', deuterium = deuterium + '.($deuterium_total * 8).', merchand = '.(time() + 172800).' WHERE id = '.$planetrow->data['id'].';', 'planets');
				db::query('UPDATE {{table}} SET credits = credits - 10 WHERE id = '.$user->data['id'].';', 'users');
				db::query("INSERT INTO {{table}} (uid, time, credits, type) VALUES (".$user->data['id'].", ".time().", ".(10 * (-1)).", 2)", "log_credits");

				message('Вы успешно купили '.($metal_total * 8).' металла, '.($crystal_total * 8).' кристалла, '.($deuterium_total * 8).' дейтерия', 'Успешная покупка', '?set=resources', 2);
			} else
				message('Покупать ресурсы можно только раз в 48 часов', 'Ошибка', '?set=resources', 2);
		} else
			message('Для покупки вам необходимо еще '.(10 - $user->data['credits']).' кредитов', 'Ошибка', '?set=resources', 2);
	}
	
	$parse['energy_total']          = colorNumber( pretty_number( floor( ( $planetrow->data['energy_max'] + $parse['energy_basic_income'] ) + $planetrow->data['energy_used'])));
	$parse['energy_max']            = pretty_number( floor($planetrow->data['energy_max']));
	
	$parse['metal_total']           = colorNumber(pretty_number($metal_total));
	$parse['crystal_total']         = colorNumber(pretty_number($crystal_total));
	$parse['deuterium_total']       = colorNumber(pretty_number($deuterium_total));

	$parse['daily_metal']           = colorNumber(pretty_number($metal_total * 24));
	$parse['weekly_metal']          = colorNumber(pretty_number($metal_total * 24 * 7));
	$parse['monthly_metal']         = colorNumber(pretty_number($metal_total * 24 * 30));

	$parse['daily_crystal']         = colorNumber(pretty_number($crystal_total * 24));
	$parse['weekly_crystal']        = colorNumber(pretty_number($crystal_total * 24 * 7));
	$parse['monthly_crystal']       = colorNumber(pretty_number($crystal_total * 24 * 30));

	$parse['daily_deuterium']       = colorNumber(pretty_number($deuterium_total * 24));
	$parse['weekly_deuterium']      = colorNumber(pretty_number($deuterium_total * 24 * 7));
	$parse['monthly_deuterium']     = colorNumber(pretty_number($deuterium_total * 24 * 30));
	
	$parse['buy_metal']				= colorNumber(pretty_number($metal_total * 8));
	$parse['buy_crystal']			= colorNumber(pretty_number($crystal_total * 8));
	$parse['buy_deuterium']			= colorNumber(pretty_number($deuterium_total * 8));
	$parse['merchand']				= $planetrow->data['merchand'];

	$parse['metal_storage']         = floor($planetrow->data['metal']     / $planetrow->data['metal_max']     * 100);
	$parse['crystal_storage']       = floor($planetrow->data['crystal']   / $planetrow->data['crystal_max']   * 100);
	$parse['deuterium_storage']     = floor($planetrow->data['deuterium'] / $planetrow->data['deuterium_max'] * 100);

	$parse['metal_storage_bar']     = floor(($planetrow->data['metal']     / $planetrow->data['metal_max']     * 100) * 4.25);
	$parse['crystal_storage_bar']   = floor(($planetrow->data['crystal']   / $planetrow->data['crystal_max']   * 100) * 4.25);
	$parse['deuterium_storage_bar'] = floor(($planetrow->data['deuterium'] / $planetrow->data['deuterium_max'] * 100) * 4.25);

	if ($parse['metal_storage_bar'] > (100 * 4.25)) {
		$parse['metal_storage_bar'] = 425;
		$parse['metal_storage_barcolor'] = '#C00000';
	} elseif ($parse['metal_storage_bar'] > (80 * 4.25)) {
		$parse['metal_storage_barcolor'] = '#C0C000';
	} else {
		$parse['metal_storage_barcolor'] = '#00C000';
	}

	if ($parse['crystal_storage_bar'] > (100 * 4.25)) {
		$parse['crystal_storage_bar'] = 425;
		$parse['crystal_storage_barcolor'] = '#C00000';
	} elseif ($parse['crystal_storage_bar'] > (80 * 4.25)) {
		$parse['crystal_storage_barcolor'] = '#C0C000';
	} else {
		$parse['crystal_storage_barcolor'] = '#00C000';
	}

	if ($parse['deuterium_storage_bar'] > (100 * 4.25)) {
		$parse['deuterium_storage_bar'] = 425;
		$parse['deuterium_storage_barcolor'] = '#C00000';
	} elseif ($parse['deuterium_storage_bar'] > (80 * 4.25)) {
		$parse['deuterium_storage_barcolor'] = '#C0C000';
	} else {
		$parse['deuterium_storage_barcolor'] = '#00C000';
	}

	$parse['production_level_bar'] = $production_level * 3.5;
	$parse['production_level']     = "{$production_level}%";
	$parse['production_level_barcolor'] = '#00ff00';

	$parse['et'] = $user->data['energy_tech'];

    $Display->addTemplate('resource', 'resources.php');
    $Display->assign('parse', $parse, 'resource');

	display('', 'Сырьё');
?>
