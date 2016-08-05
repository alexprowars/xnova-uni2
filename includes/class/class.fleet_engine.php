<?php

/**
 * @author AlexPro
 * @copyright 2008 - 2011 XNova Game Group
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

class fleet_engine {

	public function FleetHandler()
	{
		$_fleets = db::query("SELECT * FROM {{table}} WHERE (`fleet_start_time` <= '".time()."' AND `fleet_mess` = '0') OR (`fleet_end_stay` <= '".time()."' AND `fleet_mess` != '1' AND `fleet_end_stay` != '0') OR (`fleet_end_time` < '". time() ."' AND `fleet_mess` != '0') ORDER BY fleet_time LIMIT 3;", 'fleets');

		//$_fleets = db::query("SELECT * FROM {{table}} WHERE fleet_time < '". time() ."' ORDER BY fleet_time LIMIT 3;", 'fleets');

		if (db::num_rows($_fleets) > 0)
		{
			system::includeLang ("system");

			while ($CurrentFleet = db::fetch_assoc($_fleets))
			{
				switch ($CurrentFleet["fleet_mission"])
				{
					case 1:
						$this->MissionCaseAttack ( $CurrentFleet );
						break;
					case 3:
						$this->MissionCaseTransport ( $CurrentFleet );
						break;
					case 6:
						$this->MissionCaseSpy ( $CurrentFleet );
						break;
					case 2:
						if ($CurrentFleet['fleet_mess'] == 0 && $CurrentFleet['fleet_start_time'] <= time()) {
							$QryUpdateFleet  = "UPDATE {{table}} SET `fleet_mess` = 1, fleet_time = fleet_end_time WHERE `fleet_id` = '". $CurrentFleet['fleet_id'] ."' LIMIT 1 ;";
							db::query( $QryUpdateFleet, 'fleets');
						}
						if ($CurrentFleet['fleet_end_time'] <= time()) {
							$this->RestoreFleetToPlanet ( $CurrentFleet, true );
							db::query("DELETE FROM {{table}} WHERE `fleet_id` = ". $CurrentFleet["fleet_id"], 'fleets');
						}
						break;
					case 5:
						$this->MissionCaseStayAlly ( $CurrentFleet );
						break;
					case 4:
						$this->MissionCaseStay ( $CurrentFleet );
						break;
					case 7:
						$this->MissionCaseColonisation ( $CurrentFleet );
						break;
					case 8:
						$this->MissionCaseRecycling ( $CurrentFleet );
						break;
					case 9:
						$this->MissionCaseDestruction ( $CurrentFleet );
						break;
					case 10:
						$this->MissionCaseCreateBase ( $CurrentFleet );
						break;
					case 15:
						$this->MissionCaseExpedition ( $CurrentFleet );
						break;
					case 20:
						$this->MissionCaseRak ( $CurrentFleet );
						break;
					default:
						db::query("DELETE FROM {{table}} WHERE `fleet_id` = '". $CurrentFleet['fleet_id'] ."';", 'fleets');
				}
			}
		}
	}

	private function CreateOneMoonRecord ( $Galaxy, $System, $Planet, $Owner, $Chance )
	{
		global $lang;

		$QryGetMoonPlanetData  = "SELECT * FROM {{table}} ";
		$QryGetMoonPlanetData .= "WHERE ";
		$QryGetMoonPlanetData .= "`galaxy` = '". $Galaxy ."' AND ";
		$QryGetMoonPlanetData .= "`system` = '". $System ."' AND ";
		$QryGetMoonPlanetData .= "`planet` = '". $Planet ."' AND planet_type = 1;";
		$MoonPlanet = db::query ( $QryGetMoonPlanetData, 'planets', true);

		if ($MoonPlanet['parent_planet'] == 0 && $MoonPlanet['id'] != 0) {
			$SizeMin	= 2000 + ( $Chance * 100 );
			$SizeMax	= 6000 + ( $Chance * 200 );

			$maxtemp	= $MoonPlanet['temp_max'] - rand(10, 45);
			$mintemp    = $MoonPlanet['temp_min'] - rand(10, 45);
			$size		= rand ($SizeMin, $SizeMax);

			$QryInsertMoonInPlanet  = "INSERT INTO {{table}} SET ";
			$QryInsertMoonInPlanet .= "`name` = '" .$lang['sys_moon'] ."', ";
			$QryInsertMoonInPlanet .= "`id_owner` = '". $Owner ."', ";
			$QryInsertMoonInPlanet .= "`galaxy` = '". $Galaxy ."', ";
			$QryInsertMoonInPlanet .= "`system` = '". $System ."', ";
			$QryInsertMoonInPlanet .= "`planet` = '". $Planet ."', ";
			$QryInsertMoonInPlanet .= "`last_update` = '". time() ."', ";
			$QryInsertMoonInPlanet .= "`planet_type` = '3', ";
			$QryInsertMoonInPlanet .= "`image` = 'mond', ";
			$QryInsertMoonInPlanet .= "`diameter` = '". $size ."', ";
			$QryInsertMoonInPlanet .= "`field_max` = '1', ";
			$QryInsertMoonInPlanet .= "`temp_min` = '". $maxtemp ."', ";
			$QryInsertMoonInPlanet .= "`temp_max` = '". $mintemp ."', ";
			$QryInsertMoonInPlanet .= "`metal` = '0', ";
			$QryInsertMoonInPlanet .= "`crystal` = '0', ";
			$QryInsertMoonInPlanet .= "`deuterium` = '0'; ";
			db::query( $QryInsertMoonInPlanet , 'planets');

			$QryGetMoonId = db::insert_id();

			db::query("UPDATE {{table}} SET `parent_planet` = '".$QryGetMoonId."' WHERE `id` = '". $MoonPlanet['id'] ."';", 'planets');

			return true;
		} else
			return false;
	}

	private function RestoreFleetToPlanet ( $FleetRow, $Start = true )
	{
		global $resource;

		if (!isset($FleetRow["fleet_id"]))
			return;

		if ($Start == true && $FleetRow['fleet_start_type'] == 3) {
			$CheckFleet = db::query("SELECT destruyed FROM {{table}} WHERE `galaxy` = '". $FleetRow['fleet_start_galaxy'] ."' AND `system` = '". $FleetRow['fleet_start_system'] ."' AND `planet` = '". $FleetRow['fleet_start_planet'] ."' AND `planet_type` = '". $FleetRow['fleet_start_type'] ."'", "planets", true);

			if ($CheckFleet['destruyed'] != 0) {
				$FleetRow['fleet_start_type'] = 1;
			}
		} elseif ($FleetRow['fleet_end_type'] == 3) {
			$CheckFleet = db::query("SELECT destruyed FROM {{table}} WHERE `galaxy` = '". $FleetRow['fleet_end_galaxy'] ."' AND `system` = '". $FleetRow['fleet_end_system'] ."' AND `planet` = '". $FleetRow['fleet_end_planet'] ."' AND `planet_type` = '". $FleetRow['fleet_end_type'] ."'", "planets", true);

			if ($CheckFleet['destruyed'] != 0) {
				$FleetRow['fleet_end_type'] = 1;
			}
		}

		$QryUpdatePlanet = 'UPDATE {{table}} SET ';

		$FleetRecord       = explode(";", $FleetRow['fleet_array']);

		foreach ($FleetRecord as $Group) {
			if ($Group != '') {
				$Class = explode (",", $Group);
				$Fleet = explode ("!", $Class[1]);
				$QryUpdatePlanet .= "`". $resource[$Class[0]] ."` = `".$resource[$Class[0]]."` + '".$Fleet[0]."', ";
			}
		}

		$QryUpdatePlanet  .= "`metal` = `metal` + '". $FleetRow['fleet_resource_metal'] ."', ";
		$QryUpdatePlanet  .= "`crystal` = `crystal` + '". $FleetRow['fleet_resource_crystal'] ."', ";
		$QryUpdatePlanet  .= "`deuterium` = `deuterium` + '". $FleetRow['fleet_resource_deuterium'] ."' ";

		$QryUpdatePlanet  .= "WHERE ";
		if ($Start == true) {
			$QryUpdatePlanet  .= "`galaxy` = '". $FleetRow['fleet_start_galaxy'] ."' AND ";
			$QryUpdatePlanet  .= "`system` = '". $FleetRow['fleet_start_system'] ."' AND ";
			$QryUpdatePlanet  .= "`planet` = '". $FleetRow['fleet_start_planet'] ."' AND ";
			$QryUpdatePlanet  .= "`planet_type` = '". $FleetRow['fleet_start_type'] ."' ";
		} else {
			$QryUpdatePlanet  .= "`galaxy` = '". $FleetRow['fleet_end_galaxy'] ."' AND ";
			$QryUpdatePlanet  .= "`system` = '". $FleetRow['fleet_end_system'] ."' AND ";
			$QryUpdatePlanet  .= "`planet` = '". $FleetRow['fleet_end_planet'] ."' AND ";
			$QryUpdatePlanet  .= "`planet_type` = '". $FleetRow['fleet_end_type'] ."' ";
		}
		$QryUpdatePlanet  .= "LIMIT 1;";

		db::query( $QryUpdatePlanet, 'planets');
	}

	private function SpyTarget ( $TargetPlanet, $Mode, $TitleString )
	{
		global $lang, $resource;

		$LookAtLoop = true;
		$String 	= '';
		$Loops 		= 0;
		$ResFrom	= array();
		$ResTo		= array();

		if ($Mode == 0) {
			$String .= "<table width=\"100%\"><tr><td class=\"c\" colspan=\"4\">";
			$String .= $TitleString ." ". $TargetPlanet['name'];
			$String .= " <a href=\"?set=galaxy&mode=3&galaxy=". $TargetPlanet["galaxy"] ."&system=". $TargetPlanet["system"]. "\">";
			$String .= "[". $TargetPlanet["galaxy"] .":". $TargetPlanet["system"] .":". $TargetPlanet["planet"] ."]</a>";
			$String .= "<br>на <script>print_date(".time().");</script></td>";
			$String .= "</tr><tr>";
			$String .= "<th width=220>металла:</th><th width=220 align=right>".pretty_number($TargetPlanet['metal'])."</th>";
			$String .= "<th width=220>кристалла:</th><th width=220 align=right>".pretty_number($TargetPlanet['crystal'])."</th>";
			$String .= "</tr><tr>";
			$String .= "<th width=220>дейтерия:</th><th width=220 align=right>".pretty_number($TargetPlanet['deuterium'])."</th>";
			$String .= "<th width=220>энергии:</th><th width=220 align=right>".pretty_number($TargetPlanet['energy_max'])."</th>";
			$String .= "</tr>";
			$LookAtLoop = false;
		} elseif ($Mode == 1) {
			$ResFrom[0] = 200;
			$ResTo[0]   = 299;
			$Loops      = 1;
		} elseif ($Mode == 2) {
			$ResFrom[0] = 400;
			$ResTo[0]   = 499;
			$ResFrom[1] = 500;
			$ResTo[1]   = 599;
			$Loops      = 2;
		} elseif ($Mode == 3) {
			$ResFrom[0] = 1;
			$ResTo[0]   = 99;
			$Loops      = 1;
		} elseif ($Mode == 4) {
			$ResFrom[0] = 100;
			$ResTo[0]   = 199;
			$Loops      = 1;
		} elseif ($Mode == 5) {
			$ResFrom[0] = 300;
			$ResTo[0]   = 325;
			$Loops      = 1;
		} elseif ($Mode == 6) {
			$ResFrom[0] = 600;
			$ResTo[0]   = 607;
			$Loops      = 1;
		}

		if ($LookAtLoop == true) {
			$String  = "<table width=\"100%\" cellspacing=\"1\"><tr><td class=\"c\" colspan=\"". ((2 * SPY_REPORT_ROW) + (SPY_REPORT_ROW - 2))."\">". $TitleString ."</td></tr>";
			$Count       = 0;
			$CurrentLook = 0;
			while ($CurrentLook < $Loops) {
				$row     = 0;
				for ($Item = $ResFrom[$CurrentLook]; $Item <= $ResTo[$CurrentLook]; $Item++) {
					if ( isset($resource[$Item]) && (($TargetPlanet[$resource[$Item]] > 0 && $Item < 600) || ($TargetPlanet[$resource[$Item]] > time() && $Item > 600))) {
						if ($row == 0) {
							$String  .= "<tr>";
						}
						$String  .= "<th width=40%>".$lang['tech'][$Item]."</th><th width=10%>".(($Item < 600) ? $TargetPlanet[$resource[$Item]] : '+')."</th>";

						$Count   += $TargetPlanet[$resource[$Item]];
						$row++;
						if ($row == SPY_REPORT_ROW) {
							$String  .= "</tr>";
							$row      = 0;
						}
					}
				}

				while ($row != 0) {
					$String  .= "<th width=40%>&nbsp;</th><th width=10%>&nbsp;</th>";
					$row++;
					if ($row == SPY_REPORT_ROW) {
						$String  .= "</tr>";
						$row      = 0;
					}
				}
				$CurrentLook++;
			}

			if ($Count == 0) {
				$String  .= "<tr><th>нет данных</th></tr>";
			}
		} else
			$Count = 0;

		$String .= "</table>";

		$return['String'] = $String;
		$return['Count']  = $Count;
		
		return $return;
	}
	
	private function ReturnFleet ($FleetRow)
	{
		db::query("UPDATE {{table}} SET `fleet_mess` = 1, fleet_time = fleet_end_time WHERE `fleet_id` = '". $FleetRow['fleet_id'] ."';", 'fleets');
		if ($FleetRow['fleet_group'] != 0) {
			db::query("DELETE FROM {{table}} WHERE id = ".$FleetRow['fleet_group'].";", "aks");
			db::query("DELETE FROM {{table}} WHERE aks_id = ".$FleetRow['fleet_group'].";", "aks_user");
		}
	}

	// Атака
	private function MissionCaseAttack ( $FleetRow )
	{
		global $lang, $resource, $CombatCaps;

		if ($FleetRow['fleet_start_time'] <= time() && $FleetRow['fleet_mess'] == 0)
		{
			$TargetPlanet = new planet();
			$TargetPlanet->load_from_coords($FleetRow['fleet_end_galaxy'], $FleetRow['fleet_end_system'], $FleetRow['fleet_end_planet'], $FleetRow['fleet_end_type']);

			if (!isset($TargetPlanet->data['id']) || !$TargetPlanet->data['id_owner']) {
				$this->ReturnFleet($FleetRow);
			}

			$CurrentUser = new user();
			$CurrentUser->load_from_id($FleetRow['fleet_owner'], 'id, username, military_tech, defence_tech, shield_tech, laser_tech, ionic_tech, buster_tech, rpg_admiral, rpg_komandir', false);

			if (!isset($CurrentUser->data['id']))
			{
				$this->ReturnFleet($FleetRow);
			}
			
			$TargetUser = new user();
			$TargetUser->load_from_id($TargetPlanet->data['id_owner']);
			
			if (!isset($TargetUser->data['id']))
			{
				$this->ReturnFleet($FleetRow);
			}

			$TargetPlanet->load_user_info($TargetUser);

			// =============================================================================
			$TargetPlanet->PlanetResourceUpdate();
			// =============================================================================

			$attackUsers  = array();
			$attackFleets = array();

			if ($FleetRow['fleet_group'] != 0) {
				$fleets = db::query('SELECT * FROM {{table}} WHERE fleet_group = '.$FleetRow['fleet_group'], 'fleets');
				while ($fleet = db::fetch_assoc($fleets)) {
					$attackUsers[$fleet['fleet_id']]['fleet'] 	= array($fleet['fleet_start_galaxy'], $fleet['fleet_start_system'], $fleet['fleet_start_planet']);
					$a_user = db::query('SELECT `id`, `username`, `military_tech`, `defence_tech`, `shield_tech`, `laser_tech`, `ionic_tech`, `buster_tech`, `rpg_admiral`, `rpg_komandir` FROM {{table}} WHERE id='.$fleet['fleet_owner'],'users', true);
					$attackUsers[$fleet['fleet_id']]['tech'] 	= array('id' => $a_user['id'], 'military_tech' => $a_user['military_tech'], 'shield_tech' => $a_user['shield_tech'], 'defence_tech' => $a_user['defence_tech'], 'laser_tech' => $a_user['laser_tech'], 'ionic_tech' => $a_user['ionic_tech'], 'buster_tech' => $a_user['buster_tech']);
					$attackUsers[$fleet['fleet_id']]['flvl'] 	= array();
					$attackUsers[$fleet['fleet_id']]['username']= base64_encode($a_user['username']);

					if ($a_user['rpg_komandir'] > time()) {
						$attackUsers[$fleet['fleet_id']]['tech']['military_tech'] 	+= 2;
						$attackUsers[$fleet['fleet_id']]['tech']['defence_tech'] 	+= 2;
						$attackUsers[$fleet['fleet_id']]['tech']['shield_tech'] 	+= 2;
					}

					$attackFleets[$fleet['fleet_id']] = array();
					$temp = explode(';', $fleet['fleet_array']);
					foreach ($temp as $temp2) {
						$temp2 = explode(',', $temp2);

						if ($temp2[0] < 100 || $temp2[0] > 300) continue;

						$temp3 = explode('!', $temp2[1]);

						$attackFleets[$fleet['fleet_id']][$temp2[0]] = $temp3[0];
						$attackUsers[$fleet['fleet_id']]['flvl'][$temp2[0]] = $temp3[1];
					}
				}
			} else {
				$attackUsers[$FleetRow['fleet_id']]['fleet']	= array($FleetRow['fleet_start_galaxy'], $FleetRow['fleet_start_system'], $FleetRow['fleet_start_planet']);
				$attackUsers[$FleetRow['fleet_id']]['tech'] 	= array('id' => $CurrentUser->data['id'], 'military_tech' => $CurrentUser->data['military_tech'], 'shield_tech' => $CurrentUser->data['shield_tech'], 'defence_tech' => $CurrentUser->data['defence_tech'], 'laser_tech' => $CurrentUser->data['laser_tech'], 'ionic_tech' => $CurrentUser->data['ionic_tech'], 'buster_tech' => $CurrentUser->data['buster_tech']);
				$attackUsers[$FleetRow['fleet_id']]['flvl'] 	= array();
				$attackUsers[$FleetRow['fleet_id']]['username']	= base64_encode($CurrentUser->data['username']);

				if ($CurrentUser->data['rpg_komandir'] > time()) {
					$attackUsers[$FleetRow['fleet_id']]['tech']['military_tech'] 	+= 2;
					$attackUsers[$FleetRow['fleet_id']]['tech']['defence_tech'] 	+= 2;
					$attackUsers[$FleetRow['fleet_id']]['tech']['shield_tech'] 	+= 2;
				}

				$attackFleets[$FleetRow['fleet_id']] = array();
				$temp = explode(';', $FleetRow['fleet_array']);
				foreach ($temp as $temp2) {
					$temp2 = explode(',', $temp2);

					if ($temp2[0] < 100 || $temp2[0] > 300) continue;

					$temp3 = explode('!', $temp2[1]);

					$attackFleets[$FleetRow['fleet_id']][$temp2[0]] = $temp3[0];
					$attackUsers[$FleetRow['fleet_id']]['flvl'][$temp2[0]] = $temp3[1];
				}
			}

			$defenseUsers = array();
			$def = db::query('SELECT * FROM {{table}} WHERE `fleet_end_galaxy` = '.$FleetRow['fleet_end_galaxy'].' AND `fleet_end_system` = '.$FleetRow['fleet_end_system'].' AND `fleet_end_type` = '.$FleetRow['fleet_end_type'].' AND `fleet_end_planet` = '.$FleetRow['fleet_end_planet'].' AND fleet_mess = 3', 'fleets');
			while ($defRow = db::fetch_assoc($def)) {

				$defenseUsers[$defRow['fleet_id']]['fleet'] 	= array($FleetRow['fleet_end_galaxy'], $FleetRow['fleet_end_system'], $FleetRow['fleet_end_planet']);
				$a_user = db::query('SELECT `id`, `username`, `military_tech`, `defence_tech`, `shield_tech`, `laser_tech`, `ionic_tech`, `buster_tech`, `rpg_admiral`, `rpg_komandir` FROM {{table}} WHERE id = '.$defRow['fleet_owner'],'users', true);
				$defenseUsers[$defRow['fleet_id']]['tech'] 		= array('id' => $a_user['id'], 'military_tech' => $a_user['military_tech'], 'shield_tech' => $a_user['shield_tech'], 'defence_tech' => $a_user['defence_tech'], 'laser_tech' => $a_user['laser_tech'], 'ionic_tech' => $a_user['ionic_tech'], 'buster_tech' => $a_user['buster_tech']);
				$defenseUsers[$defRow['fleet_id']]['flvl'] 		= array();
				$defenseUsers[$defRow['fleet_id']]['username']	= base64_encode($a_user['username']);

				if ($a_user['rpg_komandir'] > time()) {
					$defenseUsers[$defRow['fleet_id']]['tech']['military_tech'] += 2;
					$defenseUsers[$defRow['fleet_id']]['tech']['defence_tech'] 	+= 2;
					$defenseUsers[$defRow['fleet_id']]['tech']['shield_tech'] 	+= 2;
				}

				$defenseFleets[$defRow['fleet_id']] = array();
				$temp = explode(';', $defRow['fleet_array']);
				foreach ($temp as $temp2) {
					$temp2 = explode(',', $temp2);

					if ($temp2[0] < 100 || $temp2[0] > 300) continue;

					$temp3 = explode('!', $temp2[1]);

					$defenseFleets[$defRow['fleet_id']][$temp2[0]] = $temp3[0];
					$defenseUsers[$defRow['fleet_id']]['flvl'][$temp2[0]] = $temp3[1];
				}
			}
			$defenseUsers[0]['fleet'] 	= array($FleetRow['fleet_end_galaxy'], $FleetRow['fleet_end_system'], $FleetRow['fleet_end_planet']);
			$defenseUsers[0]['flvl'] 	= array();
			$defenseUsers[0]['username']= base64_encode($TargetUser->data['username']);
			$defenseUsers[0]['tech'] 	= array('id' => $TargetUser->data['id'], 'military_tech' => $TargetUser->data['military_tech'], 'shield_tech' => $TargetUser->data['shield_tech'], 'defence_tech' => $TargetUser->data['defence_tech'], 'laser_tech' => $TargetUser->data['laser_tech'], 'ionic_tech' => $TargetUser->data['ionic_tech'], 'buster_tech' => $TargetUser->data['buster_tech']);

			if ($TargetUser->data['rpg_komandir'] > time()) {
				$defenseUsers[0]['tech']['military_tech'] 	+= 2;
				$defenseUsers[0]['tech']['defence_tech'] 	+= 2;
				$defenseUsers[0]['tech']['shield_tech'] 	+= 2;
			}

			for ($i = 200; $i < 500; $i++) {
				if (isset($resource[$i]) && isset($TargetPlanet->data[$resource[$i]])) {
					$defenseFleets[0][$i] = $TargetPlanet->data[$resource[$i]];
					if (isset($TargetUser->data['fleet_'.$i]) && $i < 300)
						$defenseUsers[0]['flvl'][$i] = $TargetUser->data['fleet_'.$i];
					else
						$defenseUsers[0]['flvl'][$i] = 0;
				}
			}

			include_once('includes/ataki.php');

			$result = calculateAttack($attackFleets, $defenseFleets, $attackUsers, $defenseUsers, $TargetUser->data['rpg_ingenieur'], $FleetRow['raunds']);

			$steal = array('metal' => 0, 'crystal' => 0, 'deuterium' => 0);
			if ($result['won'] == 1) {
				$max_resources = 0;
				$max_fleet_res = array();
				foreach ($attackFleets AS $fleet => $arr) {
					$max_fleet_res[$fleet] = 0;
					foreach ($arr as $Element => $amount) {
						if ($Element != 210) {
							if ($Element == 202 || $Element == 203) {
								$max_resources 			+= $CombatCaps[$Element]['capacity'] * $amount * (1 + $attackUsers[$fleet]['flvl'][$Element] * 0.05);
								$max_fleet_res[$fleet] 	+= $CombatCaps[$Element]['capacity'] * $amount * (1 + $attackUsers[$fleet]['flvl'][$Element] * 0.05);
							} else {
								$max_resources 			+= $CombatCaps[$Element]['capacity'] * $amount;
								$max_fleet_res[$fleet] 	+= $CombatCaps[$Element]['capacity'] * $amount;
							}
						}
					}
				}

				$res_correction 	= $max_resources;
				$res_procent 		= array();

				if ($max_resources > 0) {
					$metal   = $TargetPlanet->data['metal'] / 2;
					$crystal = $TargetPlanet->data['crystal'] / 2;
					$deuter  = $TargetPlanet->data['deuterium'] / 2;
					if ($metal > $max_resources / 3) {
						$steal['metal']		 = $max_resources / 3;
						$max_resources		-= $steal['metal'];
					} else {
						$steal['metal']		 = $metal;
						$max_resources		-= $steal['metal'];
					}

					if ($crystal > $max_resources / 2) {
						$steal['crystal'] 		 = $max_resources / 2;
						$max_resources   		-= $steal['crystal'];
					} else {
						$steal['crystal'] 		 = $crystal;
						$max_resources   		-= $steal['crystal'];
					}

					if ($deuter > $max_resources) {
						$steal['deuterium']	 	 = $max_resources;
						$max_resources		-= $steal['deuterium'];
					} else {
						$steal['deuterium']	 	 = $deuter;
						$max_resources		-= $steal['deuterium'];
					}
					if ($max_resources > 0) {
						if (($metal - $steal['metal']) > $max_resources / 2) {
							$steal['metal']		+= $max_resources / 2;
							$max_resources		-= $max_resources / 2;
						} else {
							$steal['metal']		+= $metal - $steal['metal'];
							$max_resources		-= $metal - $steal['metal'];
						}

						if (($crystal - $steal['crystal']) > $max_resources / 2) {
							$steal['crystal'] 		+= $max_resources / 2;
						} else {
							$steal['crystal'] 		+= $crystal - $steal['crystal'];
						}
					}

					foreach ($max_fleet_res AS $id => $res) {
						$res_procent[$id] = $max_fleet_res[$id] / $res_correction;
					}
				}

				if ($steal['metal'] < 0) $steal['metal'] = 0;
				if ($steal['crystal'] < 0) $steal['crystal'] = 0;
				if ($steal['deuterium'] < 0) $steal['deuterium'] = 0;

				$steal = array_map('round', $steal);
			}

			$totalDebree = $result['debree']['def'][0] + $result['debree']['def'][1] + $result['debree']['att'][0] + $result['debree']['att'][1];

			if ($totalDebree > 0)
				db::query('UPDATE {{table}} SET debris_metal = debris_metal + '.($result['debree']['att'][0]+$result['debree']['def'][0]).' , debris_crystal = debris_crystal + '.($result['debree']['att'][1]+$result['debree']['def'][1]).' WHERE galaxy = '.$TargetPlanet->data['galaxy'].' AND system = '.$TargetPlanet->data['system'].' AND planet = '.$TargetPlanet->data['planet'].' AND planet_type != 3;', 'planets');

			foreach ($attackFleets as $fleetID => $attacker) {
				$fleetArray = '';
				$totalCount = 0;
				foreach ($attacker as $element => $amount) {
					if ($amount) $fleetArray .= $element.','.$amount.'!0;';
					$totalCount += $amount;
				}

				if ($totalCount <= 0) {
					db::query ('DELETE FROM {{table}} WHERE `fleet_id`='.$fleetID,'fleets');
				} else {
					$query = 'UPDATE {{table}} SET fleet_array="'.substr($fleetArray, 0, -1).'", fleet_time = fleet_end_time, fleet_mess=1, fleet_group = 0, won='.$result['won'].'';
					if ($result['won'] == 1 && ($steal['metal'] > 0 || $steal['crystal'] > 0 || $steal['deuterium'] > 0)) {
						if (isset($res_procent[$fleetID])) {
							$query .= ', `fleet_resource_metal` = `fleet_resource_metal` + '. round($res_procent[$fleetID] * $steal['metal']) .', ';
							$query .= '`fleet_resource_crystal` = `fleet_resource_crystal` +'. round($res_procent[$fleetID] * $steal['crystal']) .', ';
							$query .= '`fleet_resource_deuterium` = `fleet_resource_deuterium` +'. round($res_procent[$fleetID] * $steal['deuterium']) .'';
						}
					}
					$query .= ' WHERE fleet_id='.$fleetID;
					db::query($query, 'fleets');
				}
			}

			foreach ($defenseFleets as $fleetID => $defender) {
				if ($fleetID != 0) {
					$fleetArray = '';
					$totalCount = 0;
					foreach ($defender as $element => $amount) {
						if ($amount) $fleetArray .= $element.','.$amount.'!0;';
						$totalCount += $amount;
					}

					if ($totalCount <= 0) {
						db::query ('DELETE FROM {{table}} WHERE `fleet_id`='.$fleetID,'fleets');

					} else {
						db::query('UPDATE {{table}} SET fleet_array="'.$fleetArray.'", fleet_time = fleet_end_time WHERE fleet_id='.$fleetID,'fleets');
					}

				} else {
					$fleetArray = '';
					for ($i = 200; $i < 500; $i++) {
						if (isset($resource[$i]) && isset($TargetPlanet->data[$resource[$i]])) {
							if (isset($defender[$i]))
								$fleetArray .= '`'.$resource[$i].'` = '.$defender[$i].', ';
							elseif ($TargetPlanet->data[$resource[$i]] != 0)
								$fleetArray .= '`'.$resource[$i].'`= 0, ';
						}
					}
					db::query('UPDATE {{table}} SET '.$fleetArray.'metal=metal-'.$steal['metal'].', crystal=crystal-'.$steal['crystal'].', deuterium=deuterium-'.$steal['deuterium'].' WHERE id='.$TargetPlanet->data['id'],'planets');
				}
			}

			$FleetDebris      = $result['debree']['att'][0] + $result['debree']['def'][0] + $result['debree']['att'][1] + $result['debree']['def'][1];

			$MoonChance  = round($FleetDebris / 100000);
			if ($FleetDebris > 2000000) {
				$MoonChance = 20;
			}
			if ($FleetDebris < 100000) {
				$UserChance = 0;
			} else {
				$UserChance = mt_rand(1, 100);
			}

			if ($FleetRow['fleet_end_type'] == 5) $UserChance = 0;

			if (($UserChance > 0) and ($UserChance <= $MoonChance)) {
				$TargetPlanetName = $this->CreateOneMoonRecord ( $FleetRow['fleet_end_galaxy'], $FleetRow['fleet_end_system'], $FleetRow['fleet_end_planet'], $TargetPlanet->data['id_owner'], $MoonChance);
				if ($TargetPlanetName)
					$GottenMoon = sprintf ($lang['sys_moonbuilt'], $FleetRow['fleet_end_galaxy'], $FleetRow['fleet_end_system'], $FleetRow['fleet_end_planet']);
				else
					$GottenMoon = 'Предпринята попытка образования луны, но данные координаты уже заняты другой луной';
			} else {
				$GottenMoon = "";
			}
			// Очки военного опыта
			$AddWarPoints = ($result['won'] != 2) ? ($MoonChance * 4) : 0;
			// Сборка массива ID участников боя
			$FleetsUsers = array();
			$str = "";
			foreach ($attackUsers AS $info) {
				if (!in_array($info['tech']['id'], $FleetsUsers)) {
					$FleetsUsers[] = $info['tech']['id'];

					if ($FleetRow['fleet_mission'] != 6) {
						if ($result['won'] == 1)
							$str = ", `raids_win` =  `raids_win` + 1";
						elseif ($result['won'] == 2)
							$str = ", `raids_lose` =  `raids_lose` + 1";

						if ($AddWarPoints > 0)
							$str .= ", `xpraid` = `xpraid` + ".ceil($AddWarPoints / count($attackUsers))."";

						db::query("UPDATE {{table}} SET `raids` = `raids` + 1".$str." WHERE id = '".$info['tech']['id']."';", "users");
					}
				}
			}
			foreach ($defenseUsers AS $info) {
				if (!in_array($info['tech']['id'], $FleetsUsers))
					$FleetsUsers[] = $info['tech']['id'];
			}

			// Упаковка в строку
			$users = json_encode($FleetsUsers);
			// Сборка боевого доклада
			$results = array($result, $attackUsers, $defenseUsers, $steal, $MoonChance, base64_encode($GottenMoon));
			// Упаковка в строку
			$raport = json_encode($results);
			// Уничтожен в первой волне
			if (count($result['rw']) <= 2 && $result['won'] == 2)
				$no_contact = 1;
			else
				$no_contact = 0;
			// Добавление в базу
			db::query("INSERT INTO {{table}} SET `time` = ".time().", `id_users` = '".$users."', `no_contact` = '".$no_contact."', `raport` = '".$raport."';", 'rw');
			// Ключи авторизации доклада
			$ids = db::insert_id();
			$key = md5('xnovasuka'.$ids);

			if ($FleetRow['fleet_group'] != 0) {
				db::query("DELETE FROM {{table}} WHERE id = ".$FleetRow['fleet_group'].";", "aks");
				db::query("DELETE FROM {{table}} WHERE aks_id = ".$FleetRow['fleet_group'].";", "aks_user");
			}

			$lost = $result['lost']['att'] + $result['lost']['def'];

			if ($lost >= 1000000) {
				$title_1 = '';
				$title_2 = '';

				$sab = 0;

				$UserList = array();

				foreach ($attackUsers AS $info) {
					if (!in_array($info['username'], $UserList))
						$UserList[] = $info['username'];
				}

				if (count($UserList) > 1)
					$sab = 1;

				foreach ($UserList AS $info) {
					if ($title_1 != '')
						$title_1 .= ',';

					$title_1 .= base64_decode($info);
				}

				$UserList = array();

				foreach ($defenseUsers AS $info) {
					if (!in_array($info['username'], $UserList))
						$UserList[] = $info['username'];
				}

				if (count($UserList) > 1)
					$sab = 1;

				foreach ($UserList AS $info) {
					if ($title_2 != '')
						$title_2 .= ',';

					$title_2 .= base64_decode($info);
				}

				$title = ''.$title_1.' vs '.$title_2.' (П: '.pretty_number($lost).')';

				db::query("INSERT INTO {{table}} (`user`, `title`, `log`) VALUES (0, '".$title."', '".$raport."')", "savelog");
				$id = db::insert_id();
				db::query("INSERT INTO {{table}} (title, debris, time, won, sab, log) VALUES ('".$title."', ".floor($lost / 1000).", ".time().", ".$result['won'].", ".$sab.", ".$id.")", "hall");
			}

			$raport  = "<center><a href=\"?set=rw&r=".$ids."&amp;k=".$key."\" target=\"_blank\">";
			if ($result['won'] == 1)
				$raport .= "<font color=\"green\">";
			elseif ($result['won'] == 0)
				$raport .= "<font color=\"orange\">";
			elseif ($result['won'] == 2)
				$raport .= "<font color=\"red\">";

			$raport .= $lang['sys_mess_attack_report'] ." [". $FleetRow['fleet_end_galaxy'] .":". $FleetRow['fleet_end_system'] .":". $FleetRow['fleet_end_planet'] ."]</font></a>";

			$raport2  = $raport.'<br><br><font color=\'red\'>'. $lang['sys_perte_attaquant'] .': '.pretty_number($result['lost']['att']).'</font><font color=\'green\'>   '. $lang['sys_perte_defenseur'] .': '.pretty_number($result['lost']['def']).'</font><br>';
			$raport2 .= $lang['sys_gain'] .' м: <font color=\'#adaead\'>'.pretty_number($steal['metal']).'</font>, к: <font color=\'#ef51ef\'>'.pretty_number($steal['crystal']).'</font>, д: <font color=\'#f77542\'>'.pretty_number($steal['deuterium']).'</font><br>';
			$raport2 .= $lang['sys_debris'] .' м: <font color=\'#adaead\'>'.pretty_number($result['debree']['att'][0]+$result['debree']['def'][0]).'</font>, к: <font color=\'#ef51ef\'>'.pretty_number($result['debree']['att'][1]+$result['debree']['def'][1]).'</font></center>';

			$UserList = array();

			foreach ($attackUsers AS $info) {
				if (!in_array($info['tech']['id'], $UserList))
					$UserList[] = $info['tech']['id'];
			}

			foreach ($UserList AS $info) {
				SendSimpleMessage ( $info, '', time(), 3, 'Боевой доклад', $raport2 );
			}

			$UserList = array();

			foreach ($defenseUsers AS $info) {
				if (!in_array($info['tech']['id'], $UserList))
					$UserList[] = $info['tech']['id'];
			}

			foreach ($UserList AS $info) {
				SendSimpleMessage ( $info, '', time(), 3, 'Боевой доклад', $raport );
			}

			db::query("INSERT INTO {{table}} (uid, time, planet_start, planet_end, fleet, battle_log) VALUES (".$FleetRow['fleet_owner'].", ".time().", 0, ".$TargetPlanet->data['id'].", '".$FleetRow['fleet_array']."', ".$ids.")", "log_attack");
		}
		elseif ($FleetRow['fleet_end_time'] <= time() && $FleetRow['fleet_mess'] != 0)
		{
			$this->RestoreFleetToPlanet ( $FleetRow, true );
			db::query ("DELETE FROM {{table}} WHERE `fleet_id` = " . $FleetRow["fleet_id"], 'fleets');
		}
	}

	// Колонизация
	private function MissionCaseColonisation ( $FleetRow )
	{
		global $lang;

		if ($FleetRow['fleet_mess'] == 0) {

			if ($FleetRow['fleet_start_time'] <= time()) {

				$MaxColo = db::query("SELECT `colonisation_tech` FROM {{table}} WHERE id={$FleetRow['fleet_owner']}",'users',true);
				$iMaxColo = $MaxColo['colonisation_tech'] + 1;
				if ($iMaxColo > MAX_PLAYER_PLANETS) $iMaxColo = MAX_PLAYER_PLANETS;

				$iPlanetCount = db::query ("SELECT count(*) as num FROM {{table}} WHERE `id_owner` = '". $FleetRow['fleet_owner'] ."' AND `planet_type` = '1'", 'planets', true);
				$iPlanetCount = $iPlanetCount['num'];

				$iGalaxyPlace = db::query ("SELECT count(*) as num FROM {{table}} WHERE `galaxy` = '". $FleetRow['fleet_end_galaxy']."' AND `system` = '". $FleetRow['fleet_end_system']."' AND `planet` = '". $FleetRow['fleet_end_planet']."';", 'planets', true);
				$iGalaxyPlace = $iGalaxyPlace['num'];

				$TargetAdress = sprintf ($lang['sys_adress_planet'], $FleetRow['fleet_end_galaxy'], $FleetRow['fleet_end_system'], $FleetRow['fleet_end_planet']);
				if ($iGalaxyPlace == 0) {
					if ($iPlanetCount >= $iMaxColo) {
						$TheMessage = $lang['sys_colo_arrival'] . $TargetAdress . $lang['sys_colo_maxcolo'] . $iMaxColo . $lang['sys_colo_planet'];
						SendSimpleMessage ( $FleetRow['fleet_owner'], '', $FleetRow['fleet_start_time'], 0, $lang['sys_colo_mess_from'], $TheMessage);
						db::query("UPDATE {{table}} SET fleet_time = fleet_end_time, `fleet_mess` = '1' WHERE `fleet_id` = ". $FleetRow["fleet_id"], 'fleets');
					} else {
						$NewOwnerPlanet = system::CreateOnePlanetRecord($FleetRow['fleet_end_galaxy'], $FleetRow['fleet_end_system'], $FleetRow['fleet_end_planet'], $FleetRow['fleet_owner'], $lang['sys_colo_defaultname'], false);
						if ( $NewOwnerPlanet == true ) {
							$TheMessage = $lang['sys_colo_arrival'] . $TargetAdress . $lang['sys_colo_allisok'];
							SendSimpleMessage ( $FleetRow['fleet_owner'], '', $FleetRow['fleet_start_time'], 0, $lang['sys_colo_mess_from'], $TheMessage);

							$CurrentFleet = explode(";", $FleetRow['fleet_array']);
							$NewFleet     = "";

							foreach ($CurrentFleet as $Group) {
								if ($Group != '') {
									$Class 	= explode (",", $Group);
									$Lvl 	= explode ("!", $Class[1]);
									if ($Class[0] == 208 && $Lvl[0] > 0)
											$NewFleet  .= $Class[0].",".($Lvl[0] - 1)."!0;";
									elseif ($Lvl[0] > 0)
											$NewFleet  .= $Class[0].",".$Lvl[0]."!0;";
								}
							}

							$FleetRow['fleet_array'] = $NewFleet;
							$this->RestoreFleetToPlanet ( $FleetRow, false );
							db::query("DELETE FROM {{table}} WHERE fleet_id=" . $FleetRow["fleet_id"], 'fleets');
						} else {
							db::query("UPDATE {{table}} SET fleet_time = fleet_end_time, `fleet_mess` = '1' WHERE `fleet_id` = ". $FleetRow["fleet_id"], 'fleets');
							$TheMessage = $lang['sys_colo_arrival'] . $TargetAdress . $lang['sys_colo_badpos'];
							SendSimpleMessage ( $FleetRow['fleet_owner'], '', $FleetRow['fleet_start_time'], 0, $lang['sys_colo_mess_from'], $TheMessage);
						}
					}
				} else {
					db::query("UPDATE {{table}} SET fleet_time = fleet_end_time, `fleet_mess` = '1' WHERE `fleet_id` = ". $FleetRow["fleet_id"], 'fleets');
					$TheMessage = $lang['sys_colo_arrival'] . $TargetAdress . $lang['sys_colo_notfree'];
					SendSimpleMessage ( $FleetRow['fleet_owner'], '', $FleetRow['fleet_end_time'], 0, $lang['sys_colo_mess_from'], $TheMessage);
				}
			}
		} elseif ($FleetRow['fleet_end_time'] <= time()) {
			$this->RestoreFleetToPlanet ( $FleetRow, true );
			db::query("DELETE FROM {{table}} WHERE fleet_id=" . $FleetRow["fleet_id"], 'fleets');
		}
	}

	// Уничтожить
	private function MissionCaseDestruction ( $FleetRow )
	{
		global $lang;

		if ($FleetRow['fleet_start_time'] <= time()) {
			if ($FleetRow['fleet_mess'] == 0) {

				// Проводим бой
				$this->MissionCaseAttack ( $FleetRow );

				$CheckFleet = db::query("SELECT fleet_array, won FROM {{table}} WHERE fleet_id = ".$FleetRow['fleet_id'].";", "fleets", true);

				if (isset($CheckFleet['fleet_array']) && $CheckFleet['won'] == 1) {

					$TargetMoon = db::query("SELECT id, id_owner, diameter FROM {{table}} WHERE `galaxy` = '". $FleetRow['fleet_end_galaxy'] ."' AND `system` = '". $FleetRow['fleet_end_system'] ."' AND `planet` = '". $FleetRow['fleet_end_planet'] ."' AND `planet_type` = '3';", 'planets', true);

					$CurrentUser = db::query("SELECT `id`, `username`, `rpg_meta` FROM {{table}} WHERE `id` = '". $FleetRow['fleet_owner'] ."';", 'users', true);

					$RipsKilled     = 0;
					$MoonDestroyed  = 0;
					$Rips           = 0;

					$temp = explode(';', $CheckFleet['fleet_array']);
					foreach ($temp as $temp2) {
						$temp2 = explode(',', $temp2);

						if ($temp2[0] < 100 || $temp2[0] > 300) continue;

						$temp3 = explode('!', $temp2[1]);

						if ($temp2[0] == 214) $Rips += $temp3[0];
					}

					if ($CurrentUser['rpg_meta'] > time()) $Rips = $Rips * 1.25;

					$MoonDestChance = round((100 - sqrt($TargetMoon['diameter'])) * (sqrt($Rips)));
					if ($MoonDestChance > 99)
						$MoonDestChance = 99;
					if ($MoonDestChance < 0)
						$MoonDestChance = 0;
					$RipDestChance = round((sqrt($TargetMoon['diameter'])) / 2);

					if ($CurrentUser['rpg_meta'] > time())
						$RipDestChance *= 0.75;

					if ($Rips > 0){
						$UserChance = mt_rand(1, 100);

						if (($UserChance > 0) AND ($UserChance <= $MoonDestChance)) {
							$RipsKilled = 0;
							$MoonDestroyed = 1;
						} elseif (($UserChance > 0) AND ($UserChance <= $RipDestChance)) {
							$RipsKilled = 1;
							$MoonDestroyed = 0;
						}
					}

					if ($MoonDestroyed == 1){
						db::query("UPDATE {{table}} SET destruyed = ".(time() + 60 * 60 * 24).", id_owner = 0 WHERE `id` = '".$TargetMoon['id']."';", 'planets');
						db::query("UPDATE {{table}} SET current_planet = id_planet WHERE id = ".$CurrentUser['id'].";", "users");

						db::query("UPDATE {{table}} SET fleet_start_type = 1 WHERE fleet_start_galaxy = ".$FleetRow['fleet_end_galaxy']." AND fleet_start_system = ".$FleetRow['fleet_end_system']." AND fleet_start_planet = ".$FleetRow['fleet_end_planet']." AND fleet_start_type = 3;", "fleets");
						db::query("UPDATE {{table}} SET fleet_end_type = 1 WHERE fleet_end_galaxy = ".$FleetRow['fleet_end_galaxy']." AND fleet_end_system = ".$FleetRow['fleet_end_system']." AND fleet_end_planet = ".$FleetRow['fleet_end_planet']." AND fleet_end_type = 3;", "fleets");

						$message  = $lang['sys_moon_destroyed']."<br><br>".$lang['sys_chance_moon_destroy'].$MoonDestChance."%. <br>".$lang['sys_chance_rips_destroy'].$RipDestChance."%";

						SendSimpleMessage ( $CurrentUser['id'], '', $FleetRow['fleet_start_time'], 3, $lang['sys_mess_tower'], $message );
						SendSimpleMessage ( $TargetMoon['id_owner'], '', $FleetRow['fleet_start_time'], 3, $lang['sys_mess_tower'], $message );
					} elseif ($RipsKilled == 1) {
						db::query("DELETE FROM {{table}} WHERE `fleet_id` = '". $FleetRow["fleet_id"] ."';", 'fleets');
						$message  = $lang['sys_rips_destroyed']."<br><br>".$lang['sys_chance_moon_destroy'].$MoonDestChance."%. <br>".$lang['sys_chance_rips_destroy'].$RipDestChance."%";

						SendSimpleMessage ( $CurrentUser['id'], '', $FleetRow['fleet_start_time'], 3, $lang['sys_mess_tower'], $message );
						SendSimpleMessage ( $TargetMoon['id_owner'], '', $FleetRow['fleet_start_time'], 3, $lang['sys_mess_tower'], $message );
					} else {
						$message  = $lang['sys_rips_come_back']."<br>".$lang['sys_chance_moon_destroy'].$MoonDestChance."%. <br>".$lang['sys_chance_rips_destroy'].$RipDestChance;

						SendSimpleMessage ( $CurrentUser['id'], '', $FleetRow['fleet_start_time'], 3, $lang['sys_mess_tower'], $message );
						SendSimpleMessage ( $TargetMoon['id_owner'], '', $FleetRow['fleet_start_time'], 3, $lang['sys_mess_tower'], $message );
					}
				}

			} elseif ($FleetRow['fleet_end_time'] <= time()) {
				$this->RestoreFleetToPlanet ( $FleetRow, true );
				db::query ("DELETE FROM {{table}} WHERE `fleet_id` = " . $FleetRow["fleet_id"], 'fleets');
			}
		}
	}

	// Экспедиция
	private function MissionCaseExpedition ( $FleetRow )
	{
		global $lang, $pricelist, $CombatCaps, $reslist, $game_config;

		if ($FleetRow['fleet_mess'] == 0) {
			if ($FleetRow['fleet_start_time'] < time()) {
				$QryUpdateFleet  = "UPDATE {{table}} SET fleet_time = fleet_end_stay, `fleet_mess` = 3 WHERE `fleet_id` = '". $FleetRow['fleet_id'] ."' LIMIT 1 ;";
				db::query( $QryUpdateFleet, 'fleets');
			}
		} elseif ($FleetRow['fleet_mess'] == 3) {
			if ($FleetRow['fleet_end_stay'] < time()) {

				foreach($reslist['fleet'] as $ID) {
					$Expowert[$ID]	= ($pricelist[$ID]['metal'] + $pricelist[$ID]['crystal']) / 1000;
				}

				$Expowert[202] = 12;
				$Expowert[203] = 47;
				$Expowert[204] = 12;
				$Expowert[205] = 110;
				$Expowert[206] = 47;
				$Expowert[207] = 160;

				$farray 			= explode(";", $FleetRow['fleet_array']);
				$FleetPoints 		= 0;
				$FleetCapacity		= 0;
				$FleetCount			= array();

				foreach ($farray as $Group) {

					if (empty($Group)) continue;

					$Class 						= explode (",", $Group);
					$Fleet                      = explode ("!", $Class[1]);
					$FleetCount[$Class[0]]		= $Fleet[0];
					$FleetCapacity 			   += $Fleet[0] * $CombatCaps[$Class[0]]['capacity'];
					$FleetPoints   			   += $Fleet[0] * $Expowert[$Class[0]];
				}

				$FleetCapacity	-= $FleetRow['fleet_resource_metal'] + $FleetRow['fleet_resource_crystal'] + $FleetRow['fleet_resource_deuterium'];
				$GetEvent		= mt_rand(1, 10);

				switch($GetEvent) {
					case 1:

						$WitchFound	= mt_rand(1,3);
						$FindSize 	= mt_rand(0, 100);

						if (10 < $FindSize) {
							$Factor 	= (mt_rand(10, 50) / $WitchFound) * $game_config['resource_multiplier'];
							$Message	= $lang['sys_expe_found_ress_1_'.mt_rand(1,4)];
						} elseif (0 < $FindSize && 10 >= $FindSize) {
							$Factor 	= (mt_rand(52, 100) / $WitchFound) * $game_config['resource_multiplier'];
							$Message	= $lang['sys_expe_found_ress_2_'.mt_rand(1,3)];
						} else {
							$Factor 	= (mt_rand(102, 200) / $WitchFound) * $game_config['resource_multiplier'];
							$Message	= $lang['sys_expe_found_ress_3_'.mt_rand(1,2)];
						}

						$StatFactor = db::query("SELECT MAX(total_points) as total FROM {{table}} WHERE `stat_type` = 1;", "statpoints", true);

						$MaxPoints	= ($StatFactor['total'] < 5000000) ? 9000 : 12000;
						$Size		= min($Factor * MAX(MIN($FleetPoints / 1000, $MaxPoints), 200), $FleetCapacity);

						switch($WitchFound)
						{
							case 1:
								db::query("UPDATE {{table}} SET `fleet_resource_metal` = `fleet_resource_metal` + '". $Size ."', fleet_time = fleet_end_time, `fleet_mess` = '1' WHERE `fleet_id` = '". $FleetRow["fleet_id"] ."';", 'fleets');
							break;
							case 2:
								db::query("UPDATE {{table}} SET `fleet_resource_crystal` = `fleet_resource_crystal` + '". $Size ."', fleet_time = fleet_end_time, `fleet_mess` = '1' WHERE `fleet_id` = '". $FleetRow["fleet_id"] ."';", 'fleets');
							break;
							case 3:
								db::query("UPDATE {{table}} SET `fleet_resource_deuterium` = `fleet_resource_deuterium` + '". $Size ."', fleet_time = fleet_end_time, `fleet_mess` = '1' WHERE `fleet_id` = '". $FleetRow["fleet_id"] ."';", 'fleets');
							break;
						}

					break;

					case 2:

						//$FindSize = mt_rand(0, 100);
						//if(10 < $FindSize) {
						//    $Size		= mt_rand(100, 300);
						//    $Message	= $lang['sys_expe_found_dm_1_'.mt_rand(1,5)];
						//} elseif(0 < $FindSize && 10 >= $FindSize) {
						//    $Size		= mt_rand(301, 600);
						//    $Message	= $lang['sys_expe_found_dm_2_'.mt_rand(1,4)];
						//} elseif(0 == $FindSize) {
						//    $Size	 	= mt_rand(601, 3000);
						//    $Message	= $lang['sys_expe_found_dm_3_'.mt_rand(1,2)];
						//}

						db::query("UPDATE {{table}} SET fleet_time = fleet_end_time, `fleet_mess` = '1' WHERE `fleet_id` = ". $FleetRow["fleet_id"], 'fleets');
						$Message	= $lang['sys_expe_nothing_'.mt_rand(1,8)];

					break;

					case 3:
						unset($FleetCount[208]);
						unset($FleetCount[209]);
						unset($FleetCount[214]);

						$FindSize = mt_rand(0, 100);
						if(10 < $FindSize) {
							$Size		= mt_rand(2, 50);
							$Message	= $lang['sys_expe_found_ships_1_'.mt_rand(1,4)];
						} elseif(0 < $FindSize && 10 >= $FindSize) {
							$Size		= mt_rand(51, 100);
							$Message	= $lang['sys_expe_found_ships_2_'.mt_rand(1,2)];
						} else {
							$Size	 	= mt_rand(101, 200);
							$Message	= $lang['sys_expe_found_ships_3_'.mt_rand(1,2)];
						}

						$StatFactor 	= db::query("SELECT MAX(total_points) as total FROM {{table}} WHERE `stat_type` = 1;", "statpoints", true);

						$MaxPoints 		= ($StatFactor['total'] < 5000000) ? 4500 : 6000;

						$FoundShips		= max(min(round($Size * $FleetPoints), $MaxPoints), 10000);

						$FoundShipMess	= "";
						$NewFleetArray 	= "";

						foreach($reslist['fleet'] as $ID)
						{
							if(!isset($FleetCount[$ID])) continue;

							if ($FoundShips > 0)
							{
								$Count				= mt_rand(1, floor($FoundShips / ($pricelist[$ID]['metal'] + $pricelist[$ID]['crystal'])));
								$FleetCount[$ID]	+= $Count;
								$NewFleetArray  	.= $ID.",".floor($Count + $FleetCount[$ID])."!0;";
								$FoundShips	 		-= $Count * ($pricelist[$ID]['metal'] + $pricelist[$ID]['crystal']);
								$FoundShipMess   	.= '<br>'.$lang['tech'][$ID].': '.pretty_number($Count);
							}
							else
								$NewFleetArray  	.= $ID.",".$FleetCount[$ID]."!0;";
						}

						$Message	.= $FoundShipMess;

						foreach ($FleetCount as $Count)
						{
							if (empty($Count)) continue;

						}

						$QryUpdateFleet  = "UPDATE {{table}} SET ";
						$QryUpdateFleet .= "`fleet_array` = '". $NewFleetArray ."' ";
						$QryUpdateFleet .= ", fleet_time = fleet_end_time, `fleet_mess` = '1'  ";
						$QryUpdateFleet .= "WHERE ";
						$QryUpdateFleet .= "`fleet_id` = '". $FleetRow["fleet_id"] ."';";
						db::query( $QryUpdateFleet, 'fleets');

					break;

					case 4:
						$Chance	= mt_rand(1,2);
						if($Chance == 1) {
							$Points	= array(-3,-5,-8);
							$Which	= 1;
							$Def	= -3;
							$Name	= $lang['sys_expe_attackname_1'];
							$Add	= 0;
							$Rand	= array(5,3,2);
							$DefenderFleetArray	= "204,5!0;206,3!0;207,2!0;";
						} else {
							$Points	= array(-4,-6,-9);
							$Which	= 2;
							$Def	= 3;
							$Name	= $lang['sys_expe_attackname_2'];
							$Add	= 0.1;
							$Rand	= array(4,3,2);
							$DefenderFleetArray	= "205,5!0;207,5!0;213,2!0;";
						}

						$FindSize = mt_rand(0, 100);
							
						if (10 < $FindSize) {
							$Message			= $lang['sys_expe_attack_'.$Which.'_1_'.$Rand[0]];
							$MaxAttackerPoints	= 0.3 + $Add + (mt_rand($Points[0], abs($Points[0])) * 0.01);
						} elseif (0 < $FindSize && 10 >= $FindSize) {
							$Message			= $lang['sys_expe_attack_'.$Which.'_2_'.$Rand[1]];
							$MaxAttackerPoints	= 0.3 + $Add + (mt_rand($Points[1], abs($Points[1])) * 0.01);
						} else {
							$Message			= $lang['sys_expe_attack_'.$Which.'_3_'.$Rand[2]];
							$MaxAttackerPoints	= 0.3 + $Add + (mt_rand($Points[2], abs($Points[2])) * 0.01);
						}

						foreach($FleetCount as $ID => $count)
						{
							$DefenderFleetArray	.= $ID.",".round($count * $MaxAttackerPoints)."!0;";
						}

						$AttackerTechno	= db::query('SELECT `id`, `username`, `military_tech`, `defence_tech`, `shield_tech`, `laser_tech`, `ionic_tech`, `buster_tech`, `rpg_admiral`, `rpg_komandir` FROM {{table}} WHERE id = '.$FleetRow['fleet_owner'].";", "users", true);
						$DefenderTechno	= array('id' => 0, 'username' => $Name, 'military_tech' => (mt_rand(abs($AttackerTechno['military_tech'] + $Def),0)), 'defence_tech' => (mt_rand(abs($AttackerTechno['defence_tech'] + $Def),0)), 'shield_tech' => (mt_rand(abs($AttackerTechno['shield_tech'] + $Def),0)), 'laser_tech' => (mt_rand(abs($AttackerTechno['laser_tech'] + $Def),0)), 'ionic_tech' => (mt_rand(abs($AttackerTechno['ionic_tech'] + $Def),0)), 'buster_tech' => (mt_rand(abs($AttackerTechno['buster_tech'] + $Def),0)), 'rpg_admiral' => 0);

						$attackUsers[$FleetRow['fleet_id']]['fleet']	= array($FleetRow['fleet_end_galaxy'], $FleetRow['fleet_end_system'], $FleetRow['fleet_end_planet']);
						$attackUsers[$FleetRow['fleet_id']]['tech'] 	= array('id' => $AttackerTechno['id'], 'military_tech' => $AttackerTechno['military_tech'], 'shield_tech' => $AttackerTechno['shield_tech'], 'defence_tech' => $AttackerTechno['defence_tech'], 'laser_tech' => $AttackerTechno['laser_tech'], 'ionic_tech' => $AttackerTechno['ionic_tech'], 'buster_tech' => $AttackerTechno['buster_tech']);
						$attackUsers[$FleetRow['fleet_id']]['flvl'] 	= array();
						$attackUsers[$FleetRow['fleet_id']]['username']	= base64_encode($AttackerTechno['username']);

						if ($AttackerTechno['rpg_komandir'] > time()) {
							$attackUsers[$FleetRow['fleet_id']]['tech']['military_tech'] 	+= 2;
							$attackUsers[$FleetRow['fleet_id']]['tech']['defence_tech'] 	+= 2;
							$attackUsers[$FleetRow['fleet_id']]['tech']['shield_tech'] 	    += 2;
						}

						$attackFleets[$FleetRow['fleet_id']] = array();
						$temp = explode(';', $FleetRow['fleet_array']);
						foreach ($temp as $temp2) {
							$temp2 = explode(',', $temp2);

							if ($temp2[0] < 100 || $temp2[0] > 300) continue;

							$temp3 = explode('!', $temp2[1]);

							$attackFleets[$FleetRow['fleet_id']][$temp2[0]] = $temp3[0];
							$attackUsers[$FleetRow['fleet_id']]['flvl'][$temp2[0]] = $temp3[1];
						}


						$defenseUsers[0]['fleet'] 		= array($FleetRow['fleet_start_galaxy'], $FleetRow['fleet_start_system'], $FleetRow['fleet_start_planet']);
						$defenseUsers[0]['tech'] 		= array('id' => $DefenderTechno['id'], 'military_tech' => $DefenderTechno['military_tech'], 'shield_tech' => $DefenderTechno['shield_tech'], 'defence_tech' => $DefenderTechno['defence_tech'], 'laser_tech' => $DefenderTechno['laser_tech'], 'ionic_tech' => $DefenderTechno['ionic_tech'], 'buster_tech' => $DefenderTechno['buster_tech']);
						$defenseUsers[0]['flvl'] 		= array();
						$defenseUsers[0]['username']	= base64_encode($DefenderTechno['username']);

						$defenseFleets[0] = array();
						$temp = explode(';', $DefenderFleetArray);
						foreach ($temp as $temp2) {
							$temp2 = explode(',', $temp2);

							if ($temp2[0] < 100 || $temp2[0] > 300) continue;

							$temp3 = explode('!', $temp2[1]);

							$defenseFleets[0][$temp2[0]] = $temp3[0];
							$defenseUsers[0]['flvl'][$temp2[0]] = $temp3[1];
						}

						include_once('includes/ataki.php');

						$result = calculateAttack($attackFleets, $defenseFleets, $attackUsers, $defenseUsers, 0, 6);

						foreach ($attackFleets as $fleetID => $attacker) {
							$fleetArray = '';
							$totalCount = 0;
							foreach ($attacker as $element => $amount) {
								if ($amount) $fleetArray .= $element.','.$amount.'!0;';
								$totalCount += $amount;
							}

							if ($totalCount <= 0) {
								db::query ('DELETE FROM {{table}} WHERE `fleet_id`='.$fleetID,'fleets');
							} else {
								$query = 'UPDATE {{table}} SET fleet_array="'.substr($fleetArray, 0, -1).'", fleet_time = fleet_end_time, fleet_mess=1, won='.$result['won'].'';
								$query .= ' WHERE fleet_id='.$fleetID;
								db::query($query, 'fleets');
							}
						}

						$FleetsUsers = array();

						foreach ($attackUsers AS $info) {
							$FleetsUsers[] = $info['tech']['id'];
						}

						// Упаковка в строку
						$users = json_encode($FleetsUsers);
						// Сборка боевого доклада
						$results = array($result, $attackUsers, $defenseUsers, array('metal' => 0, 'crystal' => 0, 'deuterium' => 0), 0, '');
						// Упаковка в строку
						$raport = json_encode($results);
						// Уничтожен в первой волне
						$no_contact = 0;
						// Добавление в базу
						db::query("INSERT INTO {{table}} SET `time` = ".time().", `id_users` = '".$users."', `no_contact` = '".$no_contact."', `raport` = '".$raport."';", 'rw');
						// Ключи авторизации доклада
						$ids = db::insert_id();
						$key = md5('xnovasuka'.$ids);

						switch($result['won'])
						{
							case 2:
								$ColorAtt = "red";
								$ColorDef = "green";
							break;
							case 0:
								$ColorAtt = "orange";
								$ColorDef = "orange";
							break;
							case 1:
								$ColorAtt = "green";
								$ColorDef = "red";
							break;
						}
						$MessageAtt = sprintf('<a href="#" onclick="f(\'?set=rw&r=%s&amp;k=%s\');return false" target="_blank"><center><font color="%s">%s %s</font></a><br><br><font color="%s">%s: %s</font> <font color="%s">%s: %s</font><br>%s %s:<font color="#adaead">%s</font> %s:<font color="#ef51ef">%s</font> %s:<font color="#f77542">%s</font><br>%s %s:<font color="#adaead">%s</font> %s:<font color="#ef51ef">%s</font><br></center>', $ids, $key, $ColorAtt, 'Боевой доклад', sprintf($lang['sys_adress_planet'], $FleetRow['fleet_end_galaxy'], $FleetRow['fleet_end_system'], $FleetRow['fleet_end_planet']), $ColorAtt, $lang['sys_perte_attaquant'], pretty_number($result['lost']['att']), $ColorDef, $lang['sys_perte_defenseur'], pretty_number($result['lost']['def']), $lang['sys_gain'], $lang['Metal'], 0, $lang['Crystal'], 0, $lang['Deuterium'], 0, $lang['sys_debris'], $lang['Metal'], 0, $lang['Crystal'], 0);

						SendSimpleMessage($FleetRow['fleet_owner'], '', $FleetRow['fleet_start_time'], 3, $lang['sys_mess_tower'], $MessageAtt);

					break;

					case 5:

						db::query ("DELETE FROM {{table}} WHERE `fleet_id` = ". $FleetRow["fleet_id"], 'fleets');
						$Message	= $lang['sys_expe_lost_fleet_'.mt_rand(1,4)];

					break;

					default:

						db::query("UPDATE {{table}} SET fleet_time = fleet_end_time, `fleet_mess` = '1' WHERE `fleet_id` = ". $FleetRow["fleet_id"], 'fleets');
						$Message	= $lang['sys_expe_nothing_'.mt_rand(1,8)];
				}

				SendSimpleMessage($FleetRow['fleet_owner'], '', $FleetRow['fleet_end_stay'], 15, $lang['sys_mess_tower'], $Message);
			}
		} elseif ($FleetRow['fleet_end_time'] < time()) {
			$this->RestoreFleetToPlanet ( $FleetRow, true );
			db::query ("DELETE FROM {{table}} WHERE `fleet_id` = ". $FleetRow["fleet_id"], 'fleets');
		}
	}

	// Переработка
	private function MissionCaseRecycling ($FleetRow)
	{
		global $CombatCaps, $lang;

		if ($FleetRow["fleet_mess"] == "0") {
			if ($FleetRow['fleet_start_time'] <= time()) {

				$TargetGalaxy     = db::query("SELECT id, debris_metal, debris_crystal FROM {{table}} WHERE `galaxy` = '".$FleetRow['fleet_end_galaxy']."' AND `system` = '".$FleetRow['fleet_end_system']."' AND `planet` = '".$FleetRow['fleet_end_planet']."' AND `planet_type` != 3 LIMIT 1;", 'planets', true);

				$FleetRecord         = explode(";", $FleetRow['fleet_array']);
				$RecyclerCapacity    = 0;
				$OtherFleetCapacity  = 0;
				foreach ($FleetRecord as $Group) {
					if ($Group != '') {
						$Class        = explode (",", $Group);
						$ClassLVL     = explode ("!", $Class[1]);
						if ($Class[0] == 209 || $Class[0] == 220) {
							$RecyclerCapacity   += $CombatCaps[$Class[0]]["capacity"] * $ClassLVL[0];
						} else {
							if ($Class[0] == 202 || $Class[0] == 203)
								$OtherFleetCapacity += round($CombatCaps[$Class[0]]["capacity"] * (1 + $ClassLVL[1] * 0.05)) * $ClassLVL[0];
							else
								$OtherFleetCapacity += $CombatCaps[$Class[0]]["capacity"] * $ClassLVL[0];
						}
					}
				}

				$IncomingFleetGoods = $FleetRow["fleet_resource_metal"] + $FleetRow["fleet_resource_crystal"] + $FleetRow["fleet_resource_deuterium"];
				// Если часть ресурсов хранится в переработчиках
				if ($IncomingFleetGoods > $OtherFleetCapacity) {
					$RecyclerCapacity -= ($IncomingFleetGoods - $OtherFleetCapacity);
				}

				if (($TargetGalaxy["debris_metal"] + $TargetGalaxy["debris_crystal"]) <= $RecyclerCapacity) {
					$RecycledGoods["metal"]   = $TargetGalaxy["debris_metal"];
					$RecycledGoods["crystal"] = $TargetGalaxy["debris_crystal"];
				} else {
					if (($TargetGalaxy["debris_metal"]   > $RecyclerCapacity / 2) AND ($TargetGalaxy["debris_crystal"] > $RecyclerCapacity / 2)) {
						$RecycledGoods["metal"]   = $RecyclerCapacity / 2;
						$RecycledGoods["crystal"] = $RecyclerCapacity / 2;
					} else {
						if ($TargetGalaxy["debris_metal"] > $TargetGalaxy["debris_crystal"]) {
							$RecycledGoods["crystal"] = $TargetGalaxy["debris_crystal"];
							if ($TargetGalaxy["debris_metal"] > ($RecyclerCapacity - $RecycledGoods["crystal"])) {
								$RecycledGoods["metal"] = $RecyclerCapacity - $RecycledGoods["crystal"];
							} else {
								$RecycledGoods["metal"] = $TargetGalaxy["debris_metal"];
							}
						} else {
							$RecycledGoods["metal"] = $TargetGalaxy["debris_metal"];
							if ($TargetGalaxy["debris_crystal"] > ($RecyclerCapacity - $RecycledGoods["metal"])) {
								$RecycledGoods["crystal"] = $RecyclerCapacity - $RecycledGoods["metal"];
							} else {
								$RecycledGoods["crystal"] = $TargetGalaxy["debris_crystal"];
							}
						}
					}
				}

				db::query("UPDATE {{table}} SET `debris_metal` = `debris_metal` - '".$RecycledGoods["metal"]."', `debris_crystal` = `debris_crystal` - '".$RecycledGoods["crystal"]."' WHERE `id` = '".$TargetGalaxy['id']."' LIMIT 1;", 'planets');
				db::query("UPDATE {{table}} SET `fleet_resource_metal` = `fleet_resource_metal` + '".$RecycledGoods["metal"]."', `fleet_resource_crystal` = `fleet_resource_crystal` + '".$RecycledGoods["crystal"]."', fleet_time = fleet_end_time, `fleet_mess` = '1' WHERE `fleet_id` = '".$FleetRow['fleet_id']."'", 'fleets');

				$Message = sprintf($lang['sys_recy_gotten'], pretty_number($RecycledGoods["metal"]), $lang['Metal'], pretty_number($RecycledGoods["crystal"]), $lang['Crystal']);
				SendSimpleMessage ( $FleetRow['fleet_owner'], '', $FleetRow['fleet_start_time'], 4, $lang['sys_mess_spy_control'], $Message);
			}
		} elseif ($FleetRow['fleet_end_time'] <= time()) {
			$this->RestoreFleetToPlanet ( $FleetRow, true );
			db::query("DELETE FROM {{table}} WHERE `fleet_id` = '". $FleetRow["fleet_id"] ."';", 'fleets');
		}
	}

	// Шпионаж
	private function MissionCaseSpy ( $FleetRow )
	{
		global $lang, $resource, $reslist;

		if ($FleetRow['fleet_mess'] == 0 && $FleetRow['fleet_start_time'] <= time()) {

			$CurrentUser = db::query("SELECT `spy_tech`, `rpg_technocrate` FROM {{table}} WHERE `id` = '".$FleetRow['fleet_owner']."';", 'users', true);
			
			$TargetPlanet = new planet();
			$TargetPlanet->load_from_coords($FleetRow['fleet_end_galaxy'], $FleetRow['fleet_end_system'], $FleetRow['fleet_end_planet'], $FleetRow['fleet_end_type']);
			
			if ($TargetPlanet->data['id_owner'] == 0)
			{
				$this->ReturnFleet($FleetRow);
			}
			
			$TargetUser = new user();
			$TargetUser->load_from_id($TargetPlanet->data['id_owner']);
			
			if (!isset($TargetUser->data['id']))
			{
				$this->ReturnFleet($FleetRow);
			}
			
			$TargetPlanet->load_user_info($TargetUser);

			$CurrentSpyLvl = $CurrentUser['spy_tech'];
			if ($CurrentUser['rpg_technocrate'] > time())
				$CurrentSpyLvl += 2;

			$TargetSpyLvl = $TargetUser->data['spy_tech'];
			if ($TargetUser->data['rpg_technocrate'] > time())
				$TargetSpyLvl += 2;

			// Обновление производства на планете
			// =============================================================================
			$TargetPlanet->PlanetResourceUpdate();
			// =============================================================================

			$LS = 0;

			$fleet = explode(";", $FleetRow['fleet_array']);

			foreach ($fleet as $b) {
				if ($b != '') {
					$a = explode(",", $b);
					$l = explode("!", $a[1]);

					if ($a[0] == 210)
						$LS    = $l[0];
				}
			}

			if ($LS > 0) {

				$def = db::query('SELECT fleet_array FROM {{table}} WHERE `fleet_end_galaxy` = '.$FleetRow['fleet_end_galaxy'].' AND `fleet_end_system` = '.$FleetRow['fleet_end_system'].' AND `fleet_end_type` = '.$FleetRow['fleet_end_type'].' AND `fleet_end_planet` = '.$FleetRow['fleet_end_planet'].' AND fleet_mess = 3', 'fleets');
				while ($defRow = db::fetch_assoc($def)) {
					$defRowDef = explode(';', $defRow['fleet_array']);
					foreach ($defRowDef as $Element) {
						if ($Element != '') {
							$Element = explode(',', $Element);
							$Fleet   = explode('!', $Element[1]);

							if ($Element[0] < 100) continue;

							$TargetPlanet->data[$resource[$Element[0]]] += $Fleet[0];
						}
					}
				}

				$ST = 0;

				$pT = ($TargetSpyLvl - $CurrentSpyLvl);
				$pW = ($CurrentSpyLvl - $TargetSpyLvl);

				if ($TargetSpyLvl > $CurrentSpyLvl)
					$ST = ($LS - pow($pT, 2));
				if ($CurrentSpyLvl > $TargetSpyLvl)
					$ST = ($LS + pow($pW, 2));
				if ($TargetSpyLvl == $CurrentSpyLvl)
					$ST = $CurrentSpyLvl;

				$MaterialsInfo    = $this->SpyTarget ($TargetPlanet->data, 0, $lang['sys_spy_maretials']);
				$SpyMessage       = $MaterialsInfo['String'];

				$PlanetFleetInfo  = $this->SpyTarget ($TargetPlanet->data, 1, $lang['sys_spy_fleet']);

				if ($ST >= 2) {
					$SpyMessage      	.= $PlanetFleetInfo['String'];
				}
				if ($ST >= 3) {
					$PlanetDefenInfo 	 = $this->SpyTarget ($TargetPlanet->data, 2, $lang['sys_spy_defenses']);
					$SpyMessage      	.= $PlanetDefenInfo['String'];
				}
				if ($ST >= 5) {
					$PlanetBuildInfo 	 = $this->SpyTarget ($TargetPlanet->data, 3, $lang['tech'][0]);
					$SpyMessage 	 	.= $PlanetBuildInfo['String'];
				}
				if ($ST >= 7) {
					$TargetTechnInfo 	 = $this->SpyTarget ($TargetUser->data, 4, $lang['tech'][100] );
					$SpyMessage      	.= $TargetTechnInfo['String'];
				}
				if ($ST >= 8) {
					$TargetFleetLvlInfo  = $this->SpyTarget ($TargetUser->data, 5, $lang['tech'][300] );
					$SpyMessage   		.= $TargetFleetLvlInfo['String'];
				}
				if ($ST >= 9) {
					$TargetOfficierLvlInfo  = $this->SpyTarget ($TargetUser->data, 6, $lang['tech'][600] );
					$SpyMessage   		.= $TargetOfficierLvlInfo['String'];
				}

				$TargetForce      = ($PlanetFleetInfo['Count'] * $LS) / 4;

				if ($TargetForce > 100) $TargetForce = 100;
				if ($TargetForce < 0) 	$TargetForce = 0;

				$TargetChances = rand(0, $TargetForce);
				$SpyerChances  = rand(0, 100);

				if ($TargetChances <= $SpyerChances) {
					$DestProba = sprintf( $lang['sys_mess_spy_lostproba'], $TargetChances);
				} else {
					$DestProba = "<font color=\"red\">".$lang['sys_mess_spy_destroyed']."</font>";
				}

				$AttackLink = "<center>";
				$AttackLink .= "<a href=\"?set=fleet&galaxy=". $FleetRow['fleet_end_galaxy'] ."&system=". $FleetRow['fleet_end_system'] ."";
				$AttackLink .= "&planet=".$FleetRow['fleet_end_planet']."&planettype=".$FleetRow['fleet_end_type']."";
				$AttackLink .= "&target_mission=". $FleetRow['fleet_end_type'] ."";
				$AttackLink .= " \">". $lang['type_mission'][1] ."";
				$AttackLink .= "</a></center>";

				$MessageEnd = "<center>".$DestProba."</center>";

				$fleet_link = '';

				if ($ST == 2)
					$res = $reslist['fleet'];
				elseif ($ST >= 3 && $ST <= 6)
					$res = array_merge($reslist['fleet'], $reslist['defense']);
				elseif ($ST >= 7)
					$res = array_merge($reslist['fleet'], $reslist['defense'], $reslist['tech']);
				else
					$res = array();

				foreach ($res AS $id) {
					if ( isset($TargetPlanet->data[$resource[$id]]) && $TargetPlanet->data[$resource[$id]] > 0)
						$fleet_link .= $id.','.$TargetPlanet->data[$resource[$id]].'!'.((isset($TargetUser->data['fleet_'.$id]) && $ST >= 8) ? $TargetUser->data['fleet_'.$id] : 0).';';

					if (isset($TargetUser->data[$resource[$id]]) && $TargetUser->data[$resource[$id]] > 0)
						$fleet_link .= $id.','.$TargetUser->data[$resource[$id]].'!0;';
				}

				$MessageEnd .= "<center><a href=\"/?set=sim&r=".$fleet_link."\" target=\"_blank\">Симуляция</a></center>";
				$MessageEnd .= "<center><a href=\"#\" onclick=\"raport_to_bb('sp".time()."')\">BB-код</a></center>";

				$SpyMessage  = "<div id=\"sp".time()."\">".$SpyMessage."</div><br />".$MessageEnd.$AttackLink;

				SendSimpleMessage ($FleetRow['fleet_owner'], '', $FleetRow['fleet_start_time'], 0, $lang['sys_mess_qg'], $SpyMessage);

				$TargetMessage  = $lang['sys_mess_spy_ennemyfleet'] ." ".$FleetRow['fleet_owner_name'];
				$TargetMessage .= "<a href=\"?set=galaxy&mode=3&galaxy=".$FleetRow["fleet_start_galaxy"]."&system=".$FleetRow["fleet_start_system"]."\">";
				$TargetMessage .= "[".$FleetRow["fleet_start_galaxy"].":".$FleetRow["fleet_start_system"].":".$FleetRow["fleet_start_planet"]."]</a>";
				$TargetMessage .= $lang['sys_mess_spy_seen_at'] ." ". $TargetPlanet->data['name'];
				$TargetMessage .= " [". $TargetPlanet->data["galaxy"] .":". $TargetPlanet->data["system"] .":". $TargetPlanet->data["planet"] ."]. ";
				$TargetMessage .= sprintf($lang['sys_mess_spy_lostproba'], $TargetChances).".";

				SendSimpleMessage ($TargetPlanet->data['id_owner'], '', $FleetRow['fleet_start_time'], 0, $lang['sys_mess_spy_control'], $TargetMessage);

				if ($TargetChances > $SpyerChances) {
					$this->MissionCaseAttack ($FleetRow);
				} else {
					db::query("UPDATE {{table}} SET fleet_time = fleet_end_time, `fleet_mess` = '1' WHERE `fleet_id` = '". $FleetRow["fleet_id"] ."';", 'fleets');
				}
			} else
				db::query("UPDATE {{table}} SET fleet_time = fleet_end_time, `fleet_mess` = '1' WHERE `fleet_id` = '". $FleetRow["fleet_id"] ."';", 'fleets');

		} elseif ($FleetRow['fleet_end_time'] <= time()) {
			$this->RestoreFleetToPlanet ( $FleetRow, true );
			db::query("DELETE FROM {{table}} WHERE `fleet_id` = ". $FleetRow["fleet_id"], 'fleets');
		}
	}

	// Оставить
	private function MissionCaseStay ( $FleetRow )
	{
		global $lang;

		if ($FleetRow['fleet_mess'] == 0) {

			if ($FleetRow['fleet_start_time'] <= time()) {

				$TargetUserID         = $FleetRow['fleet_target_owner'];

				$QryGetTargetPlanet  = "SELECT id_owner FROM {{table}} WHERE `galaxy` = '".$FleetRow['fleet_end_galaxy']."' AND `system` = '".$FleetRow['fleet_end_system']."' AND `planet` = '".$FleetRow['fleet_end_planet']."' AND `planet_type` = '".$FleetRow['fleet_end_type']."';";
				$TargetPlanet        = db::query( $QryGetTargetPlanet, 'planets', true);

				if ($TargetPlanet['id_owner'] != $TargetUserID) {
					db::query("UPDATE {{table}} SET fleet_time = fleet_end_time, `fleet_mess` = '1' WHERE `fleet_id` = '". $FleetRow["fleet_id"] ."';", 'fleets');
				} else {
					$this->RestoreFleetToPlanet ( $FleetRow, false );
					db::query("DELETE FROM {{table}} WHERE `fleet_id` = '". $FleetRow["fleet_id"] ."';", 'fleets');

					$SourceAdress         = sprintf ($lang['sys_adress_planet'], $FleetRow['fleet_start_galaxy'], $FleetRow['fleet_start_system'], $FleetRow['fleet_start_planet']);
					$TargetAdress         = sprintf ($lang['sys_adress_planet'], $FleetRow['fleet_end_galaxy'], $FleetRow['fleet_end_system'], $FleetRow['fleet_end_planet']);
					$TargetAddedGoods     = sprintf ($lang['sys_stay_mess_goods'], $lang['Metal'], pretty_number($FleetRow['fleet_resource_metal']), $lang['Crystal'], pretty_number($FleetRow['fleet_resource_crystal']), $lang['Deuterium'], pretty_number($FleetRow['fleet_resource_deuterium']));

					$temp_1 = explode(';', $FleetRow['fleet_array']);

					foreach ($temp_1 AS $a) {
						if (!$a) continue;

						$temp_2 = explode(',', $a);
						$temp_3 = explode('!', $temp_2[1]);

						$TargetAddedGoods .= ', '.$lang['tech'][$temp_2[0]].' : '.$temp_3[0];
					}

					$TargetMessage        = 'Ваш флот, отправленный с планеты  ';
					$TargetMessage       .= '<a href="?set=galaxy&mode=3&galaxy='. $FleetRow['fleet_start_galaxy'] .'&system='. $FleetRow['fleet_start_system'] .'">'.$SourceAdress;
					$TargetMessage       .= '</a>, достигает планеты ';
					$TargetMessage       .= "<a href=\"?set=galaxy&mode=3&galaxy=". $FleetRow['fleet_end_galaxy'] ."&system=". $FleetRow['fleet_end_system'] ."\">";
					$TargetMessage       .= $TargetAdress. "</a>". $lang['sys_stay_mess_end'] ."<br />". $TargetAddedGoods;

					SendSimpleMessage ( $TargetUserID, '', $FleetRow['fleet_start_time'], 5, $lang['sys_mess_qg'], $TargetMessage);
				}
			}
		} elseif ($FleetRow['fleet_end_time'] <= time()) {

			$QryGetTargetPlanet  = "SELECT id_owner FROM {{table}} WHERE `galaxy` = '".$FleetRow['fleet_start_galaxy']."' AND `system` = '".$FleetRow['fleet_start_system']."' AND `planet` = '".$FleetRow['fleet_start_planet']."' AND `planet_type` = '".$FleetRow['fleet_start_type']."';";
			$TargetPlanet        = db::query( $QryGetTargetPlanet, 'planets', true);

			if ($TargetPlanet['id_owner'] != $FleetRow['fleet_owner']) {
				db::query("DELETE FROM {{table}} WHERE `fleet_id` = '". $FleetRow["fleet_id"] ."';", 'fleets');
			} else {
				$this->RestoreFleetToPlanet ( $FleetRow, true );
				db::query("DELETE FROM {{table}} WHERE `fleet_id` = '". $FleetRow["fleet_id"] ."';", 'fleets');

				$TargetAdress         = sprintf ($lang['sys_adress_planet'], $FleetRow['fleet_start_galaxy'], $FleetRow['fleet_start_system'], $FleetRow['fleet_start_planet']);
				$TargetAddedGoods     = sprintf ($lang['sys_stay_mess_goods'], $lang['Metal'], pretty_number($FleetRow['fleet_resource_metal']), $lang['Crystal'], pretty_number($FleetRow['fleet_resource_crystal']), $lang['Deuterium'], pretty_number($FleetRow['fleet_resource_deuterium']));

				$temp_1 = explode(';', $FleetRow['fleet_array']);

				foreach ($temp_1 AS $a) {
					if (!$a) continue;

					$temp_2 = explode(',', $a);
					$temp_3 = explode('!', $temp_2[1]);

					$TargetAddedGoods .= ', '.$lang['tech'][$temp_2[0]].' : '.$temp_3[0];
				}

				$TargetMessage        = $lang['sys_stay_mess_back'] ."<a href=\"?set=galaxy&mode=3&galaxy=". $FleetRow['fleet_start_galaxy'] ."&system=". $FleetRow['fleet_start_system'] ."\">";
				$TargetMessage       .= $TargetAdress. "</a>". $lang['sys_stay_mess_bend'] ."<br />". $TargetAddedGoods;

				SendSimpleMessage ( $FleetRow['fleet_owner'], '', $FleetRow['fleet_end_time'], 5, $lang['sys_mess_qg'], $TargetMessage);
			}
		}
	}

	// Удержание
	private function MissionCaseStayAlly ( $FleetRow )
	{
		global $lang;

		$StartName        = $FleetRow['fleet_owner_name'];
		$StartOwner       = $FleetRow['fleet_owner'];
		$TargetName       = $FleetRow['fleet_target_owner_name'];

		if ($FleetRow['fleet_mess'] == 0) {
			if ($FleetRow['fleet_start_time'] <= time()) {

				$QryUpdateFleet  = "UPDATE {{table}} SET fleet_time = fleet_end_stay, `fleet_mess` = 3 WHERE `fleet_id` = '". $FleetRow['fleet_id'] ."' LIMIT 1 ;";
				db::query( $QryUpdateFleet, 'fleets');

				$Message         = sprintf( $lang['sys_stay_mess_user'], $StartName, GetStartAdressLink($FleetRow, ''), $TargetName, GetTargetAdressLink($FleetRow, '') );
				SendSimpleMessage ( $StartOwner, '', $FleetRow['fleet_start_time'], 0, $lang['sys_mess_tower'], $Message);
			}
		} elseif ($FleetRow['fleet_mess'] == 3) {
			if ($FleetRow['fleet_end_stay'] <= time()){
				$QryUpdateFleet  = "UPDATE {{table}} SET fleet_time = fleet_end_time, `fleet_mess` = 1 WHERE `fleet_id` = '". $FleetRow['fleet_id'] ."' LIMIT 1 ;";
				db::query( $QryUpdateFleet, 'fleets');
			}
		} else {
			if ($FleetRow['fleet_end_time'] < time()) {
				$this->RestoreFleetToPlanet ( $FleetRow, true );
				db::query("DELETE FROM {{table}} WHERE fleet_id=".$FleetRow["fleet_id"], 'fleets');
			}
		}
	}

	// Транспорт
	private function MissionCaseTransport ( $FleetRow )
	{
		global $lang;

		$StartName        = $FleetRow['fleet_owner_name'];
		$StartOwner       = $FleetRow['fleet_owner'];
		$TargetName       = $FleetRow['fleet_target_owner_name'];
		$TargetOwner      = $FleetRow['fleet_target_owner'];

		if ($FleetRow['fleet_mess'] == 0 && $FleetRow['fleet_start_time'] < time()) {

				$QryUpdatePlanet   = "UPDATE {{table}} SET ";
				$QryUpdatePlanet  .= "`metal` = `metal` + '".$FleetRow['fleet_resource_metal']."', ";
				$QryUpdatePlanet  .= "`crystal` = `crystal` + '".$FleetRow['fleet_resource_crystal']."', ";
				$QryUpdatePlanet  .= "`deuterium` = `deuterium` + '".$FleetRow['fleet_resource_deuterium']."' ";
				$QryUpdatePlanet  .= "WHERE ";
				$QryUpdatePlanet  .= "`galaxy` = '".$FleetRow['fleet_end_galaxy']."' AND ";
				$QryUpdatePlanet  .= "`system` = '".$FleetRow['fleet_end_system']."' AND ";
				$QryUpdatePlanet  .= "`planet` = '".$FleetRow['fleet_end_planet']."' AND ";
				$QryUpdatePlanet  .= "`planet_type` = '".$FleetRow['fleet_end_type']."' ";
				$QryUpdatePlanet  .= "LIMIT 1;";
				db::query( $QryUpdatePlanet, 'planets');

				$Message = sprintf( $lang['sys_tran_mess_owner'], $TargetName, GetTargetAdressLink($FleetRow, ''), $FleetRow['fleet_resource_metal'], $lang['Metal'], $FleetRow['fleet_resource_crystal'], $lang['Crystal'], $FleetRow['fleet_resource_deuterium'], $lang['Deuterium'] );

				SendSimpleMessage ( $StartOwner, '', $FleetRow['fleet_start_time'], 5, $lang['sys_mess_tower'], $Message);

				if ($TargetOwner <> $StartOwner) {
					$Message = sprintf( $lang['sys_tran_mess_user'], $StartName, GetStartAdressLink($FleetRow, ''), $TargetName, GetTargetAdressLink($FleetRow, ''), $FleetRow['fleet_resource_metal'], $lang['Metal'], $FleetRow['fleet_resource_crystal'], $lang['Crystal'], $FleetRow['fleet_resource_deuterium'], $lang['Deuterium'] );
					SendSimpleMessage ( $TargetOwner, '', $FleetRow['fleet_start_time'], 5, $lang['sys_mess_tower'], $Message);
				}

				db::query("UPDATE {{table}} SET `fleet_resource_metal` = '0' , `fleet_resource_crystal` = '0' , `fleet_resource_deuterium` = '0' , fleet_time = fleet_end_time, `fleet_mess` = '1' WHERE `fleet_id` = '".$FleetRow['fleet_id']."';", 'fleets');

		} elseif ($FleetRow['fleet_end_time'] < time()) {
			$this->RestoreFleetToPlanet ( $FleetRow, true );
			db::query("DELETE FROM {{table}} WHERE fleet_id=" . $FleetRow["fleet_id"], 'fleets');
		}
	}

	// Создать базу
	private function MissionCaseCreateBase ( $FleetRow )
	{
		global $lang;

		if ($FleetRow['fleet_mess'] == 0) {

			if ($FleetRow['fleet_start_time'] <= time()) {
				// Определяем максимальное колличество баз
				$MaxBase = db::query("SELECT `fleet_base_tech` FROM {{table}} WHERE id = ".$FleetRow['fleet_owner']."",'users',true);
				$iMaxBase = $MaxBase['fleet_base_tech'];
				// Получение общего колличества построенных баз
				$iPlanetCount = db::query("SELECT count(*) as num FROM {{table}} WHERE `id_owner` = '". $FleetRow['fleet_owner'] ."' AND `planet_type` = '5'", 'planets', true);
				$iPlanetCount = $iPlanetCount['num'];

				$iGalaxyPlace = db::query("SELECT count(*) as num FROM {{table}} WHERE `galaxy` = '". $FleetRow['fleet_end_galaxy']."' AND `system` = '". $FleetRow['fleet_end_system']."' AND `planet` = '". $FleetRow['fleet_end_planet']."';", 'planets', true);
				$iGalaxyPlace = $iGalaxyPlace['num'];

				$TargetAdress = sprintf ($lang['sys_adress_planet'], $FleetRow['fleet_end_galaxy'], $FleetRow['fleet_end_system'], $FleetRow['fleet_end_planet']);
				// Если в галактике пусто (планета не заселена)
				if ($iGalaxyPlace == 0) {
					// Если лимит баз исчерпан
					if ($iPlanetCount >= $iMaxBase) {
						$TheMessage = $lang['sys_colo_arrival'] . $TargetAdress . $lang['sys_colo_maxcolo'] . $iMaxBase . $lang['sys_base_planet'];
						SendSimpleMessage ( $FleetRow['fleet_owner'], '', $FleetRow['fleet_start_time'], 0, $lang['sys_base_mess_from'], $TheMessage);
						db::query("UPDATE {{table}} SET fleet_time = fleet_end_time, `fleet_mess` = '1' WHERE `fleet_id` = ". $FleetRow["fleet_id"], 'fleets');
					} else {
						// Создание планеты-базы
						$NewOwnerPlanet = system::CreateOnePlanetRecord($FleetRow['fleet_end_galaxy'], $FleetRow['fleet_end_system'], $FleetRow['fleet_end_planet'], $FleetRow['fleet_owner'], $lang['sys_base_defaultname'], false, true);
						// Если планета-база создана
						if ( $NewOwnerPlanet == true ) {
							$TheMessage = $lang['sys_colo_arrival'] . $TargetAdress . $lang['sys_base_allisok'];
							SendSimpleMessage ( $FleetRow['fleet_owner'], '', $FleetRow['fleet_start_time'], 0, $lang['sys_base_mess_from'], $TheMessage);

							$CurrentFleet = explode(";", $FleetRow['fleet_array']);
							$NewFleet     = "";
							foreach ($CurrentFleet as $Group) {
								if ($Group != '') {
									$Class 	= explode (",", $Group);
									$Lvl 	= explode ("!", $Class[1]);
									if ($Class[0] == 216 && $Lvl[0] > 0)
											$NewFleet  .= $Class[0].",".($Lvl[0] - 1)."!0;";
									elseif ($Lvl[0] > 0)
											$NewFleet  .= $Class[0].",".$Lvl[0]."!;";
								}
							}

							$FleetRow['fleet_array'] 	= $NewFleet;
							$FleetRow['fleet_end_type'] = 5;
							$this->RestoreFleetToPlanet ( $FleetRow, false );
							db::query("DELETE FROM {{table}} WHERE fleet_id=" . $FleetRow["fleet_id"], 'fleets');
						} else {
							db::query("UPDATE {{table}} SET fleet_time = fleet_end_time, `fleet_mess` = '1' WHERE `fleet_id` = ". $FleetRow["fleet_id"], 'fleets');
							$TheMessage = $lang['sys_colo_arrival'] . $TargetAdress . $lang['sys_base_badpos'];
							SendSimpleMessage ( $FleetRow['fleet_owner'], '', $FleetRow['fleet_start_time'], 0, $lang['sys_base_mess_from'], $TheMessage);
						}
					}
				} else {
					db::query("UPDATE {{table}} SET fleet_time = fleet_end_time, `fleet_mess` = '1' WHERE `fleet_id` = ". $FleetRow["fleet_id"], 'fleets');
					$TheMessage = $lang['sys_colo_arrival'] . $TargetAdress . $lang['sys_base_notfree'];
					SendSimpleMessage ( $FleetRow['fleet_owner'], '', $FleetRow['fleet_end_time'], 0, $lang['sys_base_mess_from'], $TheMessage);
				}
			}
		} elseif ($FleetRow['fleet_end_time'] <= time()) {
			$this->RestoreFleetToPlanet ( $FleetRow, true );
			db::query("DELETE FROM {{table}} WHERE fleet_id=".$FleetRow["fleet_id"], 'fleets');
		}
	}

	// Ракетная атака
	private function MissionCaseRak ( $FleetRow )
	{
		global $lang, $resource, $reslist;

		if ($FleetRow['fleet_start_time'] <= time()) {

			db::query("DELETE FROM {{table}} WHERE fleet_id = '" . $FleetRow['fleet_id'] . "'", 'fleets');

			$PlanetRow = db::query("SELECT * FROM {{table}} WHERE galaxy = '" . $FleetRow['fleet_end_galaxy'] . "' AND system = '" . $FleetRow['fleet_end_system'] . "' AND planet = '" . $FleetRow['fleet_end_planet'] . "' AND planet_type = 1", 'planets', true);

			$Defender = db::query("SELECT `defence_tech`  FROM {{table}} WHERE id = '" . $FleetRow['fleet_target_owner'] . "'", 'users', true);
			$Attacker = db::query("SELECT `military_tech` FROM {{table}} WHERE id = '" . $FleetRow['fleet_owner'] . "'", 'users', true);

			if (isset($PlanetRow['id']) && isset($Defender['defence_tech'])) {
				// Массивы параметров
				$ids  = array(0 => 401, 1 => 402, 2 => 403, 3 => 404, 4 => 405, 5 => 406, 6 => 407, 7 => 408, 8 => 503, 9 => 502);

				$message = '';

				$Raks 		= 0;
				$Primary 	= 401;

				$temp = explode(';', $FleetRow['fleet_array']);
				foreach ($temp as $temp2) {
					$temp2 = explode(',', $temp2);
					$temp3 = explode('!', $temp2[1]);

					if ($temp2[0] == 503) {
						$Raks		= $temp3[0];
						$Primary 	= $ids[$temp3[1]];
					}
				}

				$TargetDefensive = array();

				foreach($reslist['defense'] as $Element)
				{
					$TargetDefensive[$Element]	= $PlanetRow[$resource[$Element]];
				}

				if ($PlanetRow['interceptor_misil'] >= $Raks) 
				{
					$message .= 'Вражеская ракетная атака была отбита ракетами-перехватчиками<br>';
					db::query("UPDATE {{table}} SET interceptor_misil = interceptor_misil - ".$Raks." WHERE id = ".$PlanetRow['id'], 'planets');
				}
				else 
				{
					$message_vorlage  = 'Произведена межпланетная атака (' . $Raks . ' ракет) с ' . $FleetRow['fleet_owner_name'] . ' <a href="?set=galaxy&mode=3&galaxy=' . $FleetRow['fleet_start_galaxy'] . '&system=' . $FleetRow['fleet_start_system'] . '">[' . $FleetRow['fleet_start_galaxy'] . ':' . $FleetRow['fleet_start_system'] . ':' . $FleetRow['fleet_start_planet'] . ']</a>';
					$message_vorlage .= ' на планету ' . $FleetRow['fleet_target_owner_name'] . ' <a href="?set=galaxy&mode=3&galaxy=' . $FleetRow['fleet_end_galaxy'] . '&system=' . $FleetRow['fleet_end_system'] . '">[' . $FleetRow['fleet_end_galaxy'] . ':' . $FleetRow['fleet_end_system'] . ':' . $FleetRow['fleet_end_planet'] . ']</a>.<br><br>';
				
					if ($PlanetRow['interceptor_misil'] > 0) 
					{
						$message_vorlage .= $PlanetRow['interceptor_misil'] . " ракеты-перехватчика частично отбили атаку вражеских межпланетных ракет.<br>";
						db::query("UPDATE {{table}} SET interceptor_misil = 0 WHERE id = ".$PlanetRow['id'], 'planets');
					}

					$Raks -= $PlanetRow['interceptor_misil'];

					$irak = $this->raketenangriff($Defender['defence_tech'], $Attacker['military_tech'], $Raks, $TargetDefensive, $Primary);

					ksort($irak, SORT_NUMERIC);
					$sql = '';

					foreach ($irak as $Element => $destroy) 
					{
						if(empty($Element) || $destroy == 0)
							continue;

						$message .= $lang['tech'][$Element]." (".$destroy." уничтожено)<br>";

						if ($sql != '')
							$sql .= ', ';

						$sql .= $resource[$Element].' = '.$resource[$Element].' - '.$destroy.' ';
					}

					if ($sql != '')
						db::query("UPDATE {{table}} SET ".$sql." WHERE id = ".$PlanetRow['id'], 'planets');
				}

				if (empty($message))
					$message = "Нет обороны для разрушения!";

				$message_vorlage .= $message;

				SendSimpleMessage ( $FleetRow['fleet_target_owner'], '', $FleetRow['fleet_start_time'], 3, 'Ракетная атака', $message_vorlage);
			}
		}
	}

	// Расчет ракетной атаки
	private function raketenangriff($TargetDefTech, $OwnerAttTech, $ipm, $TargetDefensive, $pri_target = 0)
	{
		global $pricelist, $CombatCaps;

		unset($TargetDefensive[502]);

		$life_fac		= $TargetDefTech / 10 + 1;
		$life_fac_a 	= $CombatCaps[503]['attack'] * ($OwnerAttTech / 10 + 1);

		$max_dam = $ipm * $life_fac_a;
		$i = 0;

		$ship_res = array();

		foreach ($TargetDefensive as $Element => $Count) {

			if($i == 0)
				$target = $pri_target;
			elseif($Element <= $pri_target)
				$target = $Element - 1;
			else
				$target = $Element;

			$Dam = $max_dam - ($pricelist[$target]['metal'] + $pricelist[$target]['crystal']) / 10 * $TargetDefensive[$target] * $life_fac;

			if($Dam > 0) {
				$dest = $TargetDefensive[$target];
				$ship_res[$target] = $dest;
			} else {
				$dest = floor($max_dam / (($pricelist[$target]['metal'] + $pricelist[$target]['crystal']) / 10 * $life_fac));
				$ship_res[$target] = $dest;
			}
			$max_dam -= $dest * round(($pricelist[$target]['metal'] + $pricelist[$target]['crystal']) / 10 * $life_fac);
			$i++;
		}

		return $ship_res;
	}
}

 ?>
