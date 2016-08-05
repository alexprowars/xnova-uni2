<?php

/**
 * @author AlexPro
 * @copyright 2008 - 2011 XNova Game Group
 * @var $planetrow planet
 * @var $user user
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

if(!defined("INSIDE")) die("attemp hacking");

function BuildFleetListRows ( $CurrentPlanet )
{
	global $resource, $lang;

	$CurrIdx  = 1;
	$Result   = array();
	for ($Ship = 300; $Ship > 200; $Ship-- ) {
		if (isset($resource[$Ship]) && $CurrentPlanet[$resource[$Ship]] > 0) {
            $bloc                    = array();
            $bloc['idx']             = $CurrIdx;
            $bloc['fleet_id']        = $Ship;
            $bloc['fleet_name']      = $lang['tech'][$Ship];
            $bloc['fleet_max']       = pretty_number ( $CurrentPlanet[$resource[$Ship]] );
            $Result[]                = $bloc;
            $CurrIdx++;
		}
	}
	return $Result;
}

function BuildJumpableMoonCombo ( $CurrentUser, $CurrentPlanet )
{
	global $resource;
	$QrySelectMoons  = "SELECT `id`, `name`, `system`, `galaxy`, `planet`, `sprungtor`, `last_jump_time` FROM {{table}} WHERE (`planet_type` = '3' OR `planet_type` = '5') AND `id_owner` = '". $CurrentUser['id'] ."';";
	$MoonList        = db::query ( $QrySelectMoons, 'planets');
	$Combo           = "";
	while ( $CurMoon = db::fetch_assoc($MoonList) ) {
		if ( $CurMoon['id'] != $CurrentPlanet['id'] ) {
			$RestString = GetNextJumpWaitTime ( $CurMoon );
			if ( $CurMoon[$resource[43]] >= 1) {
				$Combo .= "<option value=\"". $CurMoon['id'] ."\">[". $CurMoon['galaxy'] .":". $CurMoon['system'] .":". $CurMoon['planet'] ."] ". $CurMoon['name'] . $RestString['string'] ."</option>\n";
			}
		}
	}
	return $Combo;
}

function BuildFleetCombo ( $CurrentUser, $CurrentPlanet )
{

	$QrySelectMoons  = "SELECT * FROM {{table}} WHERE `fleet_end_galaxy` = ".$CurrentPlanet['galaxy']." AND `fleet_end_system` = ".$CurrentPlanet['system']." AND `fleet_end_planet` = ".$CurrentPlanet['planet']." AND `fleet_end_type` = ".$CurrentPlanet['planet_type']." AND `fleet_mess` = 3 AND `fleet_owner` = '". $CurrentUser['id'] ."';";
	$MoonList        = db::query ( $QrySelectMoons, 'fleets');
	$Combo           = "";
	while ( $CurMoon = db::fetch_assoc($MoonList) ) {
		$Combo .= "<option value=\"". $CurMoon['fleet_id'] ."\">[". $CurMoon['fleet_start_galaxy'] .":". $CurMoon['fleet_start_system'] .":". $CurMoon['fleet_start_planet'] ."] ". $CurMoon['fleet_owner_name']."</option>\n";
	}
	return $Combo;
}

/**
 * @param  $CurrentUser user
 * @param  $CurrentPlanet
 * @param  $BuildID
 * @return array
 */
function ShowProductionTable ($CurrentUser, $CurrentPlanet, $BuildID)
{
	global $ProdGrid, $resource, $game_config;

    $CurrentBuildtLvl = $CurrentPlanet[ $resource[$BuildID] ];
	
	$energy_tech = $CurrentUser->data['energy_tech'];

    if ($BuildID != 42 && !($BuildID >= 22 && $BuildID <= 24)) {

        $BuildLevelFactor = $CurrentPlanet[$resource[$BuildID]."_porcent"];
        $BuildTemp        = $CurrentPlanet['temp_max'];

        $BuildLevel       = ($CurrentBuildtLvl > 0) ? $CurrentBuildtLvl : 1;
        $Prod[1]          = (floor(eval($ProdGrid[$BuildID]['metal'])     * $game_config['resource_multiplier']) * $CurrentUser->bonus_metal);
        $Prod[2]          = (floor(eval($ProdGrid[$BuildID]['crystal'])   * $game_config['resource_multiplier']) * $CurrentUser->bonus_crystal);
        $Prod[3]          = (floor(eval($ProdGrid[$BuildID]['deuterium']) * $game_config['resource_multiplier']) * $CurrentUser->bonus_deuterium);
        $Prod[4]          = (floor(eval($ProdGrid[$BuildID]['energy'])    * $game_config['resource_multiplier']) * $CurrentUser->bonus_energy);

        if ($BuildID != 12) {
            $ActualNeed       = floor($Prod[4]);
            $ActualProd       = floor($Prod[$BuildID]);
        } else {
            $ActualNeed       = floor($Prod[3]);
            $ActualProd       = floor($Prod[4]);
        }
    }

    $BuildStartLvl    = $CurrentBuildtLvl - 2;
    if ($BuildStartLvl < 1) {
        $BuildStartLvl = 1;
    }

	$Table = array();

	$ProdFirst = 0;
	for ($BuildLevel = $BuildStartLvl; $BuildLevel < $BuildStartLvl + 10; $BuildLevel++)
	{
		if ($BuildID != 42 && !($BuildID >= 22 && $BuildID <= 24))
		{
			$Prod[1] = (floor(eval($ProdGrid[$BuildID]['metal'])     * $game_config['resource_multiplier']) * $CurrentUser->bonus_metal);
			$Prod[2] = (floor(eval($ProdGrid[$BuildID]['crystal'])   * $game_config['resource_multiplier']) * $CurrentUser->bonus_crystal);
			$Prod[3] = (floor(eval($ProdGrid[$BuildID]['deuterium']) * $game_config['resource_multiplier']) * $CurrentUser->bonus_deuterium);

			if ($BuildID == 4 || $BuildID == 12)
				$Prod[4] = (floor(eval($ProdGrid[$BuildID]['energy'])    * $game_config['resource_multiplier']) * $CurrentUser->bonus_energy);
			else
				$Prod[4] = (floor(eval($ProdGrid[$BuildID]['energy'])    * $game_config['resource_multiplier']));

			$bloc['build_lvl'] = ($CurrentBuildtLvl == $BuildLevel) ? "<font color=\"#ff0000\">".$BuildLevel."</font>" : $BuildLevel;

			if ($BuildID != 12) {
				$bloc['build_prod']      = pretty_number(floor($Prod[$BuildID]));
				$bloc['build_prod_diff'] = colorNumber( pretty_number(floor($Prod[$BuildID] - $ActualProd)) );
				$bloc['build_need']      = colorNumber( pretty_number(floor($Prod[4])) );
				$bloc['build_need_diff'] = colorNumber( pretty_number(floor($Prod[4] - $ActualNeed)) );
			} else {
				$bloc['build_prod']      = pretty_number(floor($Prod[4]));
				$bloc['build_prod_diff'] = colorNumber( pretty_number(floor($Prod[4] - $ActualProd)) );
				$bloc['build_need']      = colorNumber( pretty_number(floor($Prod[3])) );
				$bloc['build_need_diff'] = colorNumber( pretty_number(floor($Prod[3] - $ActualNeed)) );
			}
			if ($ProdFirst == 0) {
				if ($BuildID != 12) {
					$ProdFirst = floor($Prod[$BuildID]);
				} else {
					$ProdFirst = floor($Prod[4]);
				}
			}
		}
		elseif ($BuildID >= 22 && $BuildID <= 24)
		{
			$bloc['build_lvl']       = ($CurrentBuildtLvl == $BuildLevel) ? "<font color=\"#ff0000\">".$BuildLevel."</font>" : $BuildLevel;
			$bloc['build_range']     = floor((BASE_STORAGE_SIZE + floor (50000 * round(pow (1.6, $BuildLevel )))) * $CurrentUser->bonus_storage) / 1000;
		}
		else
		{
			$bloc['build_lvl']       = ($CurrentBuildtLvl == $BuildLevel) ? "<font color=\"#ff0000\">".$BuildLevel."</font>" : $BuildLevel;
			$bloc['build_range']     = ($BuildLevel * $BuildLevel) - 1;
		}
		$Table[]    = $bloc;
	}

	return $Table;
}

function ShowBuildingInfoPage ($CurrentUser, $CurrentPlanet, $BuildID)
{
	global $lang, $resource, $pricelist, $CombatCaps, $Display;
	
	system::includeLang('infos');

	$parse = array();

    if (!isset($lang['tech'][$BuildID]))
        message('Мы не сможем дать вам эту информацию', 'Ошибка', '?set=overview', 2);

	$parse['name']        = $lang['tech'][$BuildID];
	$parse['image']       = $BuildID;
	$parse['description'] = $lang['info'][$BuildID];

	if (($BuildID >= 1 && $BuildID <= 4) || $BuildID == 12 || $BuildID == 42 || ($BuildID >= 22 && $BuildID <= 24)) {
        $Display->addTemplate('info_buildings_table', 'info_buildings_table.php');
        $parse['table_data']  = ShowProductionTable ($CurrentUser, $CurrentPlanet, $BuildID);
        $Display->assign('parse', $parse, 'info_buildings_table');
	} elseif (($BuildID >=  14 && $BuildID <=  34) || $BuildID == 6 || $BuildID == 43 || $BuildID == 44 || $BuildID == 41 || ($BuildID >= 106 && $BuildID <= 199)) {
		$Display->addTemplate('info_buildings', 'info_buildings.php');

        if ($BuildID == 34) {
            if (isset($_POST['send']) && isset($_POST['jmpto'])){
                $flid = intval($_POST['jmpto']);
                $query = db::query("SELECT * FROM {{table}} WHERE fleet_id = '".$flid."' AND fleet_end_galaxy = ".$CurrentPlanet['galaxy']." AND fleet_end_system = ".$CurrentPlanet['system']." AND fleet_end_planet = ".$CurrentPlanet['planet']." AND fleet_end_type = ".$CurrentPlanet['planet_type']." AND `fleet_mess` = 3", 'fleets', true);
                if (!$query['fleet_id'])
                    $parse['msg'] = "<font color=red>Флот отсутствует у планеты</font>";
                else {
                    $tt = 0;
                    $temp = explode(';', $query['fleet_array']);
                    foreach ($temp as $temp2) {
                        $temp2 = explode(',', $temp2);
                        if ($temp2[0] > 100) {
                            $tt += $pricelist[$temp2[0]]['stay'] * $temp2[1];
                        }
                    }
                    $max = $CurrentPlanet[$resource[$BuildID]]*10000;
                    if ($max > $CurrentPlanet['deuterium'])
                        $cur = $CurrentPlanet['deuterium'];
                    else
                        $cur = $max;

                    $times = round(($cur / $tt) * 3600);
                    $CurrentPlanet['deuterium'] -= $cur;
                    db::query("UPDATE {{table}} SET fleet_end_stay = fleet_end_stay + ".$times.", fleet_end_time = fleet_end_time + ".$times." WHERE fleet_id = '".$flid."'", 'fleets');

                    $parse['msg'] = "<font color=red>Ракета с дейтерием отправлена на орбиту вашей планете</font>";
                }
            }

            if ($CurrentPlanet[$resource[$BuildID]] > 0) {

			    if (!$parse['msg'])
                    $parse['msg'] = "Выберите флот для отправки дейтерия";

			    $Display->addTemplate('info_buildings_ally', 'info_buildings_ally.php');
                $Display->assign('parse', array(BuildFleetCombo ( $CurrentUser->data, $CurrentPlanet ), ($CurrentPlanet[$resource[$BuildID]]*10000), $parse['msg']), 'info_buildings_ally');
		    }
        }

        if ($BuildID == 43 && $CurrentPlanet[$resource[$BuildID]] > 0) {
            $RestString               = GetNextJumpWaitTime ( $CurrentPlanet );
            $gate = array();
            $gate['gate_start_link'] = BuildPlanetAdressLink ( $CurrentPlanet );
            if ($RestString['value'] != 0) {
                $gate['gate_time_script'] = InsertJavaScriptChronoApplet ( "Gate", "1", $RestString['value'], true );
                $gate['gate_wait_time']   = "<div id=\"bxx". "Gate" . "1" ."\"></div>";
                $gate['gate_script_go']   = InsertJavaScriptChronoApplet ( "Gate", "1", $RestString['value'], false );
            } else {
                $gate['gate_time_script'] = "";
                $gate['gate_wait_time']   = "";
                $gate['gate_script_go']   = "";
            }
            $gate['gate_dest_moons'] = BuildJumpableMoonCombo ( $CurrentUser->data, $CurrentPlanet );
            $gate['gate_fleet_rows'] = BuildFleetListRows ( $CurrentPlanet );

            $Display->addTemplate('info_gate', 'info_gate.php');
            $Display->assign('parse', $gate, 'info_gate');
        }

        $Display->assign('parse', $parse, 'info_buildings');

	} elseif ($BuildID >= 202 && $BuildID <= 223) {
		$Display->addTemplate('info_buildings_fleet', 'info_building_fleet.php');
        
		$parse['element_typ'] = $lang['tech'][200];
		$parse['hull_pt']     = $pricelist[$BuildID]['metal'] + $pricelist[$BuildID]['crystal'] + $pricelist[$BuildID]['deuterium'];
		$parse['hull_pt']     = pretty_number ($parse['hull_pt']).' ('.pretty_number(round($parse['hull_pt'] * (1 + $CurrentUser->data['defence_tech'] * 0.05 + (($CombatCaps[$BuildID]['power_up'] * ((isset($CurrentUser->data['fleet_'.$BuildID])) ? $CurrentUser->data['fleet_'.$BuildID] : 0)) / 100)))).')';

		$attTech = 1 + (((isset($CurrentUser->data['fleet_'.$BuildID])) ? $CurrentUser->data['fleet_'.$BuildID] : 0) * ($CombatCaps[$BuildID]['power_up'] / 100)) + $CurrentUser->data['military_tech'] * 0.05;

		if ($CombatCaps[$BuildID]['type_gun'] == 1)
			$attTech += $CurrentUser->data['laser_tech'] * 0.05;
		elseif ($CombatCaps[$BuildID]['type_gun'] == 2)
			$attTech += $CurrentUser->data['ionic_tech'] * 0.05;
		elseif ($CombatCaps[$BuildID]['type_gun'] == 3)
			$attTech += $CurrentUser->data['buster_tech'] * 0.05;

		include('includes/functions_fleet.php');
		// Устанавливаем обновлённые двигателя кораблей
		SetShipsEngine($CurrentUser->data);

		$parse['attack_pt']   = pretty_number ($CombatCaps[$BuildID]['attack']).' ('.pretty_number(round($CombatCaps[$BuildID]['attack'] * $attTech)).')';
		$parse['capacity_pt'] = pretty_number ($CombatCaps[$BuildID]['capacity']);
		$parse['base_speed']  = pretty_number ($CombatCaps[$BuildID]['speed']).' ('.pretty_number(GetFleetMaxSpeed ('', $BuildID, $CurrentUser)).')';
		$parse['base_conso']  = pretty_number ($CombatCaps[$BuildID]['consumption']);
        $parse['block']       = $CombatCaps[$BuildID]['power_armour'];
        $parse['upgrade']     = $CombatCaps[$BuildID]['power_up'];
		$parse['met']		  = pretty_number ($pricelist[$BuildID]['metal']).' ('.pretty_number ($pricelist[$BuildID]['metal'] * $CurrentUser->bonus_res_fleet).')';
		$parse['cry']		  = pretty_number ($pricelist[$BuildID]['crystal']).' ('.pretty_number ($pricelist[$BuildID]['crystal'] * $CurrentUser->bonus_res_fleet).')';
		$parse['deu']		  = pretty_number ($pricelist[$BuildID]['deuterium']).' ('.pretty_number ($pricelist[$BuildID]['deuterium'] * $CurrentUser->bonus_res_fleet).')';

        $engine     = array('', 'Ракетный', 'Импульсный', 'Гиперпространственный');
        $gun        = array('', 'Лазерное', 'Ионное', 'Плазменное');
        $armour     = array('', 'Легкая', 'Средняя', 'Тяжелая', 'Монолитная');

		$parse['base_engine'] 	= $engine[$CombatCaps[$BuildID]['type_engine']];
	    $parse['gun']       	= $gun[$CombatCaps[$BuildID]['type_gun']];
        $parse['armour']    	= $armour[$CombatCaps[$BuildID]['type_armour']];

        global $gun_armour;

        $parse['soprot']    = array();
        $parse['soprot_2']  = array();

	    for ($Type = 200; $Type <= 406; $Type++) {
		    if (isset($CombatCaps[$Type])) {
			    $parse['soprot'][]      = array($lang['tech'][$Type], $gun_armour[$CombatCaps[$BuildID]['type_gun']][$CombatCaps[$Type]['type_armour']]);
                $parse['soprot_2'][]    = array($lang['tech'][$Type], $gun_armour[$CombatCaps[$Type]['type_gun']][$CombatCaps[$BuildID]['type_armour']]);
		    }
	    }

        $Display->assign('parse', $parse, 'info_buildings_fleet');

	} elseif (($BuildID >= 401 && $BuildID <= 408) || ($BuildID >= 502 && $BuildID <= 503)) {
		$Display->addTemplate('info_buildings_defence', 'info_building_defence.php');

		$parse['element_typ'] = $lang['tech'][400];
		$parse['hull_pt']     = pretty_number ($pricelist[$BuildID]['metal'] + $pricelist[$BuildID]['crystal'] + $pricelist[$BuildID]['deuterium']);
		$parse['shield_pt']   = pretty_number ($CombatCaps[$BuildID]['shield']);
		$parse['attack_pt']   = pretty_number ($CombatCaps[$BuildID]['attack']);
		$parse['met']		  = pretty_number ($pricelist[$BuildID]['metal']);
		$parse['cry']		  = pretty_number ($pricelist[$BuildID]['crystal']);
		$parse['deu']		  = pretty_number ($pricelist[$BuildID]['deuterium']);

		if ($BuildID >= 401 && $BuildID <= 408) {

			$gun        = array('', 'Лазерное', 'Ионное', 'Плазменное');
			$armour     = array('', 'Легкая', 'Средняя', 'Тяжелая', 'Монолитная');

			$parse['gun']       = $gun[$CombatCaps[$BuildID]['type_gun']];
			$parse['armour']    = $armour[$CombatCaps[$BuildID]['type_armour']];

			global $gun_armour;

			$parse['soprot']    = array();
			$parse['soprot_2']  = array();

			for ($Type = 200; $Type <= 406; $Type++) {
				if (isset($CombatCaps[$Type])) {
					$parse['soprot'][]      = array($lang['tech'][$Type], $gun_armour[$CombatCaps[$BuildID]['type_gun']][$CombatCaps[$Type]['type_armour']]);
					$parse['soprot_2'][]    = array($lang['tech'][$Type], $gun_armour[$CombatCaps[$Type]['type_gun']][$CombatCaps[$BuildID]['type_armour']]);
				}
			}

		}

        $Display->assign('parse', $parse, 'info_buildings_defence');

        if ($BuildID >= 502 && $BuildID <= 503) {
            if(isset($_POST['form']) ){
                $_POST['502'] = abs(intval($_POST['502']));
                $_POST['503'] = abs(intval($_POST['503']));

                if($_POST['502'] > $CurrentPlanet[$resource[502]]){
                    $_POST['502'] = $CurrentPlanet[$resource[502]];
                }
                if($_POST['503'] > $CurrentPlanet[$resource[503]]){
                    $_POST['503'] = $CurrentPlanet[$resource[503]];
                }
                db::query("UPDATE {{table}} SET `".$resource[502]."` = `".$resource[502]."` - ".$_POST['502']." , `".$resource[503]."` = `".$resource[503]."` - ".$_POST['503']." WHERE `id` = ".$CurrentPlanet['id'].";", 'planets');
                $CurrentPlanet[$resource[502]] -= $_POST['502'];
                $CurrentPlanet[$resource[503]] -= $_POST['503'];
            }
            $pars                  = array();
            $pars['max_mis']       = $CurrentPlanet[$resource[44]] * 10;
            $pars['int_miss']      = $lang['tech'][502].': '.$CurrentPlanet[$resource[502]];
            $pars['plant_miss']    = $lang['tech'][503].': '.$CurrentPlanet[$resource[503]];

            $Display->addTemplate('info_missile', 'info_missile.php');
            $Display->assign('parse', $pars, 'info_missile');
        }

	} elseif ($BuildID >= 601 && $BuildID <= 615) {
		$Display->addTemplate('info_officier', 'info_officier.php');
        $Display->assign('parse', $parse, 'info_officier');
	} elseif ($BuildID >= 701 && $BuildID <= 704) {

		$parse['image'] = $BuildID - 700;

		$Display->addTemplate('info_race', 'info_race.php');
        $Display->assign('parse', $parse, 'info_race');
	}

	if ($BuildID <= 44 && $BuildID != 33 && $BuildID != 41 && !($BuildID >= 601 && $BuildID <= 615) && !($BuildID >= 502 && $BuildID <= 503)) {
		if ($CurrentPlanet[$resource[$BuildID]] > 0)
		{
			$DestroyTime          = GetBuildingTime  ($CurrentUser, $CurrentPlanet, $BuildID) / 2;

			$parse['levelvalue']  = $CurrentPlanet[$resource[$BuildID]];
			$parse['destroy']	  = GetElementPrice(GetBuildingPrice($CurrentUser, $CurrentPlanet, $BuildID, true, true), $CurrentUser->data, $CurrentPlanet);
			$parse['destroytime'] = pretty_time($DestroyTime);
			$Display->addTemplate('destroy', 'info_buildings_destroy.php');
            $Display->assign('parse', $parse, 'destroy');
		}
	}

	return $parse['name'];
}

$gid  = @intval($_GET['gid']);
$page = ShowBuildingInfoPage ($user, $planetrow->data, $gid);

display ('', $page, false);

?>
