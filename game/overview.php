<?php

/**
 * @author AlexPro
 * @copyright 2008 - 2011 XNova Game Group
 * @var $Display HSTemplateDisplay
 * @var $user user
 * @var $planetrow planet
 * @var $lang array
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

if(!defined("INSIDE")) die("attemp hacking");

$mode = (isset($_GET['mode'])) ? $_GET['mode'] : '';

if (isset($_GET['vk']) && isset($_COOKIE['vkid'])) {
	setcookie("vkid", "", -1, "/", "uni2.xnova.su", 0);
	unset($_COOKIE['vkid']);
}

system::includeLang('overview');

function BuildFleetEventTable ( $FleetRow, $Status, $Owner, $Label, $Record )
{
	global $lang;

	$FleetStyle  = array (
		 1 => 'attack',
		 2 => 'federation',
		 3 => 'transport',
		 4 => 'deploy',
		 5 => 'transport',
		 6 => 'espionage',
		 7 => 'colony',
		 8 => 'harvest',
		 9 => 'destroy',
		10 => 'missile',
		15 => 'transport',
		20 => 'attack'
	);
	$FleetStatus = array ( 0 => 'flight', 1 => 'holding', 2 => 'return' );
	if ( $Owner == true ) {
		$FleetPrefix = 'own';
	} else {
		$FleetPrefix = '';
	}

	$MissionType    = $FleetRow['fleet_mission'];
	$FleetContent   = CreateFleetPopupedFleetLink ( $FleetRow, $lang['ov_fleet'], $FleetPrefix . $FleetStyle[ $MissionType ] );
	$FleetCapacity  = CreateFleetPopupedMissionLink ( $FleetRow, $lang['type_mission'][ $MissionType ], $FleetPrefix . $FleetStyle[ $MissionType ] );

	$StartPlanet    = $FleetRow['fleet_owner_name'];
	$StartType      = $FleetRow['fleet_start_type'];
	$TargetPlanet   = $FleetRow['fleet_target_owner_name'];
	$TargetType     = $FleetRow['fleet_end_type'];

	if ($Status != 2) {

        if ($StartPlanet == '')
            $StartID = ' с координат ';
        else {
            if($StartType == 1) {
                $StartID  = $lang['ov_planet_to'];
            } elseif ($StartType == 3) {
                $StartID  = $lang['ov_moon_to'];
            } elseif ($StartType == 5) {
                $StartID  = ' с военной базы ';
            }
            $StartID .= $StartPlanet." ";
        }
		$StartID .= GetStartAdressLink ( $FleetRow, $FleetPrefix . $FleetStyle[ $MissionType ] );

        if ($TargetPlanet == '')
            $TargetID = ' координаты ';
        else {
            if ( $MissionType != 15 && $MissionType != 5 ) {
                if ($TargetType == 1) {
                    $TargetID  = $lang['ov_planet_to_target'];
                } elseif ($TargetType == 2) {
                    $TargetID  = $lang['ov_debris_to_target'];
                } elseif ($TargetType == 3) {
                    $TargetID  = $lang['ov_moon_to_target'];
                } elseif ($TargetType == 5) {
                    $TargetID  = ' военной базе ';
                }
            } else {
                $TargetID  = $lang['ov_explo_to_target'];
            }
            $TargetID .= $TargetPlanet." ";
        }
		$TargetID .= GetTargetAdressLink ( $FleetRow, $FleetPrefix . $FleetStyle[ $MissionType ] );
	} else {
         if ($StartPlanet == '')
            $StartID = ' на координаты ';
        else {
		    if ($StartType == 1) {
                $StartID  = $lang['ov_back_planet'];
            } elseif ($StartType == 3) {
                $StartID  = $lang['ov_back_moon'];
            }
            $StartID .= $StartPlanet." ";
        }
		$StartID .= GetStartAdressLink ( $FleetRow, $FleetPrefix . $FleetStyle[ $MissionType ] );

        if ($TargetPlanet == '')
            $TargetID = ' с координат ';
        else {
            if ( $MissionType != 15 ) {
                    if ($TargetType == 1) {
                        $TargetID  = $lang['ov_planet_from'];
                    } elseif ($TargetType == 2) {
                        $TargetID  = $lang['ov_debris_from'];
                    } elseif ($TargetType == 3) {
                        $TargetID  = $lang['ov_moon_from'];
                    } elseif ($TargetType == 5) {
                        $TargetID  = ' с военной базы ';
                    }
            } else {
                $TargetID  = $lang['ov_explo_from'];
            }
            $TargetID .= $TargetPlanet." ";
        }
		$TargetID .= GetTargetAdressLink ( $FleetRow, $FleetPrefix . $FleetStyle[ $MissionType ] );
	}

	if ($Owner == true) {
		$EventString  = $lang['ov_une'];
		$EventString .= $FleetContent;
	} else {
		$EventString  = ($FleetRow['fleet_group'] != 0) ? 'Союзный ' : $lang['ov_une_hostile'];
		$EventString .= $FleetContent;
		$EventString .= $lang['ov_hostile'];
		$EventString .= BuildHostileFleetPlayerLink ( $FleetRow );
	}

	if ($Status == 0) {
		$Time         = $FleetRow['fleet_start_time'];
		$Rest         = $Time - time();
		$EventString .= $lang['ov_vennant'];
		$EventString .= $StartID;
		$EventString .= $lang['ov_atteint'];
		$EventString .= $TargetID;
		$EventString .= $lang['ov_mission'];
	} elseif ($Status == 1) {
		$Time         = $FleetRow['fleet_end_stay'];
		$Rest         = $Time - time();
		$EventString .= $lang['ov_vennant'];
		$EventString .= $StartID;

		if ($MissionType == 5)
			$EventString .= ' защищает ';
		else
			$EventString .= $lang['ov_explo_stay'];

		$EventString .= $TargetID;
		$EventString .= $lang['ov_explo_mission'];
	} elseif ($Status == 2) {
		$Time         = $FleetRow['fleet_end_time'];
		$Rest         = $Time - time();
		$EventString .= $lang['ov_rentrant'];
		$EventString .= $TargetID;
		$EventString .= $StartID;
		$EventString .= $lang['ov_mission'];
	}
	$EventString .= $FleetCapacity;

	$bloc['fleet_status']       = $FleetStatus[ $Status ];
	$bloc['fleet_prefix']       = $FleetPrefix;
	$bloc['fleet_style']        = $FleetStyle[ $MissionType ];
	$bloc['fleet_order']        = $Label . $Record;
	$bloc['fleet_time']         = datezone("H:i:s", $Time);
    $bloc['fleet_count_time']   = pretty_time($Rest, ':');
	$bloc['fleet_descr']        = $EventString;
	$bloc['fleet_javas']        = InsertJavaScriptChronoApplet ( $Label, $Record, $Rest );

	return $bloc;
}

switch ($mode)
{
	case 'renameplanet':
		if (isset($_POST['action']) && $_POST['action'] == $lang['namer'])
		{
			$UserPlanet = $_POST['newname'];

			if (trim($UserPlanet) != "")
			{
				if (preg_match("/^[a-zA-Zа-яА-Я0-9_\.\,\-\!\?\*\ ]+$/u", $UserPlanet))
				{
					if (strlen($UserPlanet) > 1 && strlen($UserPlanet) < 20)
					{
						$newname = db::escape_string(strip_tags(trim( $UserPlanet )));
						$planetrow->data['name'] = $newname;
						db::query("UPDATE {{table}} SET `name` = '".$newname."' WHERE `id` = '". $user->data['current_planet'] ."' LIMIT 1;", "planets");
						if (isset($_SESSION['fleet_shortcut']))
							unset($_SESSION['fleet_shortcut']);
					} else
						message('Введённо слишком длинное или короткое имя планеты'  , 'Ошибка', '?set=overview&mode=renameplanet', 5);
				} else
					message('Введённое имя содержит недопустимые символы'  , 'Ошибка', '?set=overview&mode=renameplanet', 5);
			}
		}
		elseif (isset($_POST['action']) && $_POST['action'] == $lang['colony_abandon'])
		{

			$parse                   = array();
			$parse['planet_id']      = $planetrow->data['id'];
			$parse['galaxy_galaxy']  = $planetrow->data['galaxy'];
			$parse['galaxy_system']  = $planetrow->data['system'];
			$parse['galaxy_planet']  = $planetrow->data['planet'];
			$parse['planet_name']    = $planetrow->data['name'];

			$Display->addTemplate('delete', 'planet_delete.php');
            $Display->assign('parse', $parse, 'delete');
			
			display('', 'Покинуть колонию', false);

		}
		elseif (isset($_POST['kolonieloeschen']) && $_POST['deleteid'] == $user->data['current_planet'])
		{
			$pass = db::query("SELECT password FROM {{table}} WHERE id = ".$user->data['id']."", "users_inf", true);

			if (md5($_POST['pw']) == $pass["password"] && $user->data['id_planet'] != $user->data['current_planet'])
			{
                $checkFleets = db::query("SELECT COUNT(*) AS num FROM {{table}} WHERE (fleet_start_galaxy = ".$planetrow->data['galaxy']." AND fleet_start_system = ".$planetrow->data['system']." AND fleet_start_planet = ".$planetrow->data['planet']." AND fleet_start_type = ".$planetrow->data['planet_type'].") OR (fleet_end_galaxy = ".$planetrow->data['galaxy']." AND fleet_end_system = ".$planetrow->data['system']." AND fleet_end_planet = ".$planetrow->data['planet']." AND fleet_end_type = ".$planetrow->data['planet_type'].")", "fleets", true);

                if ($checkFleets['num'] > 0)
                    message('Нельзя удалять планету если с/на неё летит флот', $lang['colony_abandon'], '?set=overview&mode=renameplanet');
                else
				{
                    $destruyed = time() + 60 * 60 * 24;

                    db::query("UPDATE {{table}} SET `destruyed` = '".$destruyed."', `id_owner` = '0' WHERE `id` = '".$user->data['current_planet']."' LIMIT 1;", 'planets');
					db::query("UPDATE {{table}} SET `current_planet` = `id_planet` WHERE `id` = '". $user->data['id'] ."' LIMIT 1", "users");

                    if ($planetrow->data['parent_planet'] != 0)
                        db::query("UPDATE {{table}} SET `destruyed` = '".$destruyed."', `id_owner` = '0' WHERE `id` = '".$planetrow->data['parent_planet']."' LIMIT 1;", 'planets');

					if (isset($_SESSION['fleet_shortcut']))
						unset($_SESSION['fleet_shortcut']);

                    message($lang['deletemessage_ok']   , $lang['colony_abandon'], '?set=overview&mode=renameplanet');
                }

			}
			elseif ($user->data['id_planet'] == $user->data["current_planet"])
				message($lang['deletemessage_wrong'], $lang['colony_abandon'], '?set=overview&mode=renameplanet');
			else
				message($lang['deletemessage_fail'] , $lang['colony_abandon'], '?set=overview&mode=renameplanet');
		}

		$parse = array();

		$parse['planet_id']     = $planetrow->data['id'];
		$parse['galaxy_galaxy'] = $planetrow->data['galaxy'];
		$parse['galaxy_system'] = $planetrow->data['system'];
		$parse['galaxy_planet'] = $planetrow->data['planet'];
		$parse['planet_name']   = $planetrow->data['name'];

		$Display->addTemplate('rename', 'planet_rename.php');
        $Display->assign('parse', $parse, 'rename');

		display('', 'Переименовать планету', false);
		break;

	default:

		if ($mode == 'bonus' && $user->data['bonus'] < time())
		{
			$multi = ($user->data['bonus_multi'] < 50) ? ($user->data['bonus_multi'] + 1) : 50;

			if ($user->data['bonus'] < (time() - 86400))
				$multi = 1;

			$add = $multi * 1000;

			db::query("UPDATE {{table}} SET metal = metal + ".$add.", crystal = crystal + ".$add.", deuterium = deuterium + ".$add." WHERE id = ".$user->data['current_planet'].";", "planets");
			db::query("UPDATE {{table}} SET bonus = ".(time() + 86400).", bonus_multi = ".$multi." WHERE id = ".$user->data['id'].";", "users");
			
			message('Спасибо за поддержку!<br>Вы получили в качестве бонуса по <b>'.($multi*1000).'</b> Металла, Кристаллов и Дейтерия.', 'Ежедневный бонус', '?set=overview', 2);
		}

		$XpMinierUp  = pow($user->data['lvl_minier'], 3);
		$XpRaidUp    = pow($user->data['lvl_raid'], 2);
		$XpMinier    = $user->data['xpminier'];
		$XPRaid      = $user->data['xpraid'];

		$LvlUpMinier = $user->data['lvl_minier'] + 1;
		$LvlUpRaid   = $user->data['lvl_raid']   + 1;
		
		$up = 0;
		$HaveNewLevel = "";

		if ($XpMinier >= $XpMinierUp && $user->data['lvl_minier'] < 100)
		{
			$up = 10;
			db::query("UPDATE {{table}} SET `lvl_minier` = '".$LvlUpMinier."', `credits` = `credits` + ".$up.", `xpminier` = `xpminier` - ".$XpMinierUp." WHERE `id` = '". $user->data['id'] ."';", 'users');
			$HaveNewLevel 		.= "<tr><th colspan=4><a href=?set=officier>Получен новый промышленный уровень</a></th></tr>";
			$user->data['lvl_minier'] = $LvlUpMinier;
			$user->data['xpminier'] 	-= $XpMinierUp;
		}
		if ($XPRaid >= $XpRaidUp && $user->data['lvl_raid'] < 100)
		{
			$up = 10;
			db::query("UPDATE {{table}} SET `lvl_raid` = '".$LvlUpRaid."', `credits` = `credits` + ".$up.", `xpraid` = `xpraid` - ".$XpRaidUp." WHERE `id` = '". $user->data['id'] ."';", 'users');
			$HaveNewLevel 		.= "<tr><th colspan=4><a href=?set=officier>Получен новый военный уровень</a></th></tr>";
			$user->data['lvl_raid'] 	= $LvlUpRaid;
			$user->data['xpraid'] 	-= $XpRaidUp;
		}

		if ($up != 0)
		{
			db::query("INSERT INTO {{table}} (uid, time, credits, type) VALUES (".$user->data['id'].", ".time().", ".$up.", 4)", "log_credits");
			
			$ref_array = db::query("SELECT u_id FROM {{table}} WHERE r_id = ".$user->data['id']."", "refs", true);

			if (isset($ref_array['u_id']))
			{
				db::query("UPDATE {{table}} SET credits = credits + ".round($up / 2)." WHERE id = ".$ref_array['u_id']."", 'users');
				db::query("INSERT INTO {{table}} (uid, time, credits, type) VALUES (".$ref_array['u_id'].", ".time().", ".round($up / 2).", 3)", "log_credits");
			}
		}

		$OwnFleets       = db::query("SELECT * FROM {{table}} WHERE `fleet_owner` = '". $user->data['id'] ."' OR `fleet_target_owner` = '".$user->data['id']."';", 'fleets');
		$Record          = 0;
		$fpage			 = array();
		$aks 			 = array();

		while ($FleetRow = db::fetch_array($OwnFleets))
		{
			$Record++;

			if ($FleetRow['fleet_owner'] == $user->data['id'])
			{
				$StartTime   = $FleetRow['fleet_start_time'];
				$StayTime    = $FleetRow['fleet_end_stay'];
				$EndTime     = $FleetRow['fleet_end_time'];

				if ($StartTime > time()) {
					$fpage[$StartTime][$FleetRow['fleet_id']] = BuildFleetEventTable ( $FleetRow, 0, true, "fs", $Record );
				}

				if ($StayTime > time()) {
					$fpage[$StayTime][$FleetRow['fleet_id']] = BuildFleetEventTable ( $FleetRow, 1, true, "ft", $Record );
				}

				if (!($FleetRow['fleet_mission'] == 7 && $FleetRow['fleet_mess'] == 0))
				{
					if (($EndTime > time() AND $FleetRow['fleet_mission'] != 4) OR ($FleetRow['fleet_mess'] == 1 AND $FleetRow['fleet_mission'] == 4)) {
						 $fpage[$EndTime][$FleetRow['fleet_id']]  = BuildFleetEventTable ( $FleetRow, 2, true, "fe", $Record );
					}
				}

				if ($FleetRow['fleet_group'] != 0 && !in_array($FleetRow['fleet_group'], $aks))
				{
					$AKSFleets       = db::query("SELECT * FROM {{table}} WHERE fleet_group = ".$FleetRow['fleet_group']." AND `fleet_owner` != '". $user->data['id'] ."' AND fleet_mess = 0;", 'fleets');

					while ($AKFleet = db::fetch_assoc($AKSFleets))
					{
						$Record++;
						$fpage[$FleetRow['fleet_start_time']][$AKFleet['fleet_id']] = BuildFleetEventTable ( $AKFleet, 0, false, "fs", $Record );
					}

					$aks[] = $FleetRow['fleet_group'];
				}

			}
			elseif ($FleetRow['fleet_mission'] != 8)
			{
				$Record++;
				$StartTime = $FleetRow['fleet_start_time'];
				$StayTime  = $FleetRow['fleet_end_stay'];

				if ($StartTime > time())
				{
					$fpage[$StartTime][$FleetRow['fleet_id']] = BuildFleetEventTable ( $FleetRow, 0, false, "ofs", $Record );
				}
				if ($FleetRow['fleet_mission'] == 5 && $StayTime > time())
				{
					$fpage[$StayTime][$FleetRow['fleet_id']] = BuildFleetEventTable ( $FleetRow, 1, false, "oft", $Record );
				}
			}
		}

		if ($planetrow->data['parent_planet'] != 0 && $planetrow->data['planet_type'] != '3' && $planetrow->data['id'])
		{
			$lune = db::query("SELECT `id`, `name`, `image`, `destruyed` FROM {{table}} WHERE galaxy = ".$planetrow->data['galaxy']." AND system = ".$planetrow->data['system']." AND planet = ".$planetrow->data['planet']." AND planet_type='3';", 'planets', true);
			$parse['moon_img'] 	= "<a href=\"?set=overview&amp;cp=".$lune['id']."&amp;re=0\" title=\"".$lune['name']."\"><img src=\"".$dpath."planeten/".$lune['image'].".jpg\" height=\"50\" width=\"50\"></a>";
			$parse['moon'] 		= ($lune['destruyed'] == 0) ? $lune['name'] : 'Фантом';
		}
		else
		{
			$parse['moon_img'] 	= "";
			$parse['moon'] 		= "";
		}

		$Order = ($user->data['planet_sort_order'] == 1) ? "DESC" : "ASC";
		$Sort  = $user->data['planet_sort'];

		if ( $Sort == 0 ) {
			$QryPlanets = "`id` ". $Order;
		} elseif ( $Sort == 1 ) {
			$QryPlanets = "`galaxy`, `system`, `planet`, `planet_type` ". $Order;
		} elseif ( $Sort == 2 ) {
			$QryPlanets = "`name` ". $Order;
		} elseif ( $Sort == 3 ) {
			$QryPlanets = "`planet_type` ". $Order;
		} else
			$QryPlanets = "`id` ". $Order;

		$planets_query = db::query("SELECT * FROM {{table}} WHERE id_owner='".$user->data['id']."' AND `planet_type` != '3' AND id != ".$user->data["current_planet"]." ORDER BY ".$QryPlanets.";", "planets");
		$Colone  = 1;

		$build_list = array();
		$build		= new planet();
		$build->load_user_info($user);

		$AllPlanets = "";

		while ($UserPlanet = db::fetch_assoc($planets_query))
		{
			$AllPlanets .= "<th valign=\"top\"><a href=\"?set=overview&amp;cp=". $UserPlanet['id'] ."&amp;re=0\" title=\"". $UserPlanet['name'] ."\"><img src=\"". $dpath ."planeten/small/s_". $UserPlanet['image'] .".jpg\" height=\"50\" width=\"50\" alt=\"\"></a><br>". $UserPlanet['name'] ."</th>";

			if ($UserPlanet['b_building'] != 0)
			{
				$build->load_from_array($UserPlanet);
				$build->UpdatePlanetBatimentQueueList();

				if ($UserPlanet['b_building'] != 0)
				{
					$QueueArray = explode (";", $UserPlanet['b_building_id']);

					foreach ($QueueArray AS $Queue)
					{
						$CurrBuild  = explode (",", $Queue);
						$build_list[$UserPlanet['b_building']][] = array($CurrBuild[3], "<a href=\"?set=buildings&amp;cp=". $UserPlanet['id'] ."&amp;re=0\" style=\"color:#33ff33;\">".$UserPlanet['name']."</a>: </span><span class=\"holding colony\"> ".$lang['tech'][$CurrBuild[0]] .' ('. ($CurrBuild[1]) .')');
					}
				}
			}

			if ($UserPlanet['b_tech'] != 0)
				$build_list[$UserPlanet['b_tech']][] = array($UserPlanet['b_tech'], "<a href=\"?set=buildings&amp;mode=research".(($UserPlanet['b_tech_id'] > 300) ? '_fleet' : '')."&amp;cp=". $UserPlanet['id'] ."&amp;re=0\" style=\"color:#33ff33;\">".$UserPlanet['name']."</a>: </span><span class=\"holding colony\"> ".$lang['tech'][$UserPlanet['b_tech_id']] .' ('. ($user->data[$resource[$UserPlanet['b_tech_id']]] + 1) .')');

			if ($Colone <= 6) {
				$Colone++;
			} else {
				$AllPlanets .= "</tr><tr>";
				$Colone      = 1;
			}

			if ($planetrow->data['id'] == $UserPlanet['parent_planet'])
			{
				$parse['moon_img'] 	= "<a href=\"?set=overview&amp;cp=".$UserPlanet['id']."&amp;re=0\" title=\"".$UserPlanet['name']."\"><img src=\"".$dpath."planeten/".$UserPlanet['image'].".jpg\" height=\"50\" width=\"50\"></a>";
				$parse['moon'] 		= $UserPlanet['name'];
			}
		}

		if (!$user->data['design'])
			$AllPlanets = '';

		$parse['planet_type'] 		   = $lang['type_planet'][$planetrow->data['planet_type']];
		$parse['planet_name']          = $planetrow->data['name'];
		$parse['planet_diameter']      = pretty_number($planetrow->data['diameter']);
		$parse['planet_field_current'] = $planetrow->data['field_current'];
		$parse['planet_field_max']     = CalculateMaxPlanetFields($planetrow->data);
		$parse['planet_temp_min']      = $planetrow->data['temp_min'];
		$parse['planet_temp_max']      = $planetrow->data['temp_max'];
		$parse['galaxy_galaxy']        = $planetrow->data['galaxy'];
		$parse['galaxy_planet']        = $planetrow->data['planet'];
		$parse['galaxy_system']        = $planetrow->data['system'];
        
		$StatRecord = db::query("SELECT `build_points`, `tech_points`, `fleet_points`, `defs_points`, `total_points`, `total_old_rank`, `total_rank` FROM {{table}} WHERE `stat_type` = '1' AND `stat_code` = '1' AND `id_owner` = '". $user->data['id'] ."';", 'statpoints', true);

		$parse['user_points']          = pretty_number( $StatRecord['build_points'] );
		$parse['player_points_tech']   = pretty_number( $StatRecord['tech_points'] );
		$parse['total_points']         = pretty_number( $StatRecord['total_points'] );
		$parse['user_fleet']           = pretty_number( $StatRecord['fleet_points'] );
		$parse['user_defs']            = pretty_number( $StatRecord['defs_points'] );

		$parse['user_rank']            = $StatRecord['total_rank'] + 0;

		$ile = $StatRecord['total_old_rank'] - $StatRecord['total_rank'];

		if ($ile >= 1) {
			$parse['ile']              = "<font color=lime>+".$ile."</font>";
		} elseif ($ile < 0) {
			$parse['ile']              = "<font color=red>".$ile."</font>";
		} elseif ($ile == 0) {
			$parse['ile']              = "<font color=lightblue>".$ile."</font>";
		}
		$parse['user_username']        = $user->data['username'];

		$flotten = array();
		
		if (count($fpage) > 0)
		{
			ksort($fpage);
			foreach ($fpage as $time => $content)
			{
				foreach ($content AS $flid => $text)
				{
					$flotten[] = $text;
				}
			}
		}

		$parse['fleet_list']  = $flotten;

		$parse['Have_new_level'] 		= $HaveNewLevel;
		$parse['time']                  = datezone("d-m-Y H:i:s", time());
		$parse['planet_image']          = $planetrow->data['image'];
		$parse['anothers_planets']      = $AllPlanets;
		$parse['max_users']             = $game_config['users_amount'];

		$parse['metal_debris']          = pretty_number($planetrow->data['debris_metal']);
		$parse['crystal_debris']        = pretty_number($planetrow->data['debris_crystal']);
			
		if (($planetrow->data['debris_metal'] != 0 || $planetrow->data['debris_crystal'] != 0) && $planetrow->data[$resource[209]] != 0) {
			$parse['get_link'] = " (<a href=\"#\" onclick=\"QuickFleet(8, ".$planetrow->data['galaxy'].", ".$planetrow->data['system'].", ".$planetrow->data['planet'].", 2)\">переработать</a>)";
		} else {
			$parse['get_link'] = '';
		}

		if ($planetrow->data['b_building'] != 0)
		{
			$planetrow->UpdatePlanetBatimentQueueList();
			if ($planetrow->data['b_building'] != 0)
			{
				$BuildQueue = explode (";", $planetrow->data['b_building_id']);

				foreach ($BuildQueue AS $Queue)
				{
					$CurrBuild  = explode (",", $Queue);
					$build_list[$planetrow->data['b_building']][] = array($CurrBuild[3], $planetrow->data['name'].": </span><span class=\"holding colony\"> ".$lang['tech'][$CurrBuild[0]] .' ('. ($CurrBuild[1]) .')');
				}
			}
		}

		if ($planetrow->data['b_tech'] != 0)
			$build_list[$planetrow->data['b_tech']][] = array($planetrow->data['b_tech'], $planetrow->data['name'].": </span><span class=\"holding colony\"> ".$lang['tech'][$planetrow->data['b_tech_id']] .' ('. ($user->data[$resource[$planetrow->data['b_tech_id']]] + 1) .')');

		if (count($build_list) > 0)
		{
			$parse['build_list'] = array();
			ksort($build_list);

			foreach ($build_list as $time => $planet)
			{
				foreach ($planet AS $flid => $text)
				{
					$parse['build_list'][] = $text;
				}
			}
		}

		$parse['case_pourcentage'] = floor($planetrow->data["field_current"] / CalculateMaxPlanetFields($planetrow->data) * 100);

		if ($parse['case_pourcentage'] > 80) {
			$parse['case_barre_barcolor'] = '#C00000';
		} elseif ($parse['case_pourcentage'] > 60) {
			$parse['case_barre_barcolor'] = '#C0C000';
		} else {
			$parse['case_barre_barcolor'] = '#00C000';
		}

		$parse['race'] = $lang['race'][$user->data['race']];

		$parse['xpminier']= pretty_number($user->data['xpminier']);
		$parse['xpraid']= pretty_number($user->data['xpraid']);
		$parse['lvl_minier'] = $user->data['lvl_minier'];
		$parse['lvl_raid'] = $user->data['lvl_raid'];
		$parse['user_id']= $user->data['id'];
		$parse['links'] = $user->data['links'];

		$parse['raids_win'] = $user->data['raids_win'];
		$parse['raids_lose'] = $user->data['raids_lose'];
		$parse['raids'] = $user->data['raids'];

		$LvlMinier = $user->data['lvl_minier'];
		$LvlRaid = $user->data['lvl_raid'];
		$parse['lvl_up_minier'] = pretty_number($XpMinierUp);
		$parse['lvl_up_raid']   = pretty_number($XpRaidUp);
		
		$parse['vk'] = isset($_COOKIE['vkid']);

		if (!isset($_COOKIE['vkid'])) 
		{
			$my_ip = explode(".", $_SERVER['HTTP_X_REAL_IP']);
			if ($my_ip['0'] == "10" || $my_ip['0'] == "172" || $_SERVER["SERVER_NAME"] == 'xnova')
				$parse['banner'] = "";
			else
				$parse['banner'] = "<a target=\"_blank\" href=\"http://top.mail.ru/jump?from=1436203\"><img src=\"http://da.ce.b5.a1.top.mail.ru/counter?id=1436203;t=82\" border=\"0\" height=\"18\" width=\"88\" alt=\"Рейтинг@Mail.ru\"/></a>";
		} 
		else 
		{
			$parse['banner'] 	= '';
		}
		
		$parse['bonus'] 	= ($user->data['bonus'] < time()) ? true : false;
		if ($parse['bonus']) {
			$parse['bonus_multi'] = $user->data['bonus_multi'] + 1;

			if ($user->data['bonus'] < (time() - 86400))
				$parse['bonus_multi'] = 1;
		}

		$parse['refers'] = $user->data['refers'];

		$Display->addTemplate('overview', 'overview.php');
		$Display->assign('parse', $parse, 'overview');

		display('', 'Обзор');
}

?>
