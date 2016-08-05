<?php

/**
 * @author AlexPro
 * @copyright 2008 - 2011 XNova Game Group
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */
 
class building {
	/**
	 * @var $user user
	 */
	public $user;
	/**
	 * @var $planet planet
	 */
	public $planet;

	public function Page_Building ()
	{
		global $lang, $resource, $Display, $planetrow, $reslist;

		$parse = array();

		$this->planet->CheckPlanetUsedFields();

		$Allowed['1'] = array(  1,  2,  3,  4, 6, 12, 14, 15, 21, 22, 23, 24, 31, 33, 34, 44);
		$Allowed['3'] = array( 14, 21, 34, 41, 42, 43);
		$Allowed['5'] = array( 14, 34, 43, 44);

		if (isset($_GET['cmd'])) {

			$bDoItNow 	= false;
			$TheCommand = $_GET['cmd'];
			$Element 	= (isset($_GET['building'])) ? intval($_GET['building']) : 0;
			$ListID 	= (isset($_GET['listid'])) ? intval($_GET['listid']) : 0;

			if (in_array($Element, $Allowed[$this->planet->data['planet_type']])) {
				$bDoItNow = true;
			} elseif ($ListID != 0 && ($TheCommand == 'cancel' || $TheCommand == 'remove')) {
				$bDoItNow = true;
			}

			if ($bDoItNow == true) {
				switch($TheCommand){
					case 'cancel':
						$this->CancelBuildingFromQueue();
						break;
					case 'remove':
						$this->RemoveBuildingFromQueue($ListID);
						break;
					case 'insert':
					case 'destroy':
						$this->AddBuildingToQueue($Element, (($TheCommand == 'insert') ? true : false));
						break;
				}
			}
		}

		$planetrow->SetNextQueueElementOnTop();

		$Queue = $this->ShowBuildingQueue ($this->planet, $this->user);

		$MaxBuidSize = MAX_BUILDING_QUEUE_SIZE;
		if ($this->user->data['rpg_constructeur'] > time()) $MaxBuidSize += 2;

		if ($Queue['lenght'] < $MaxBuidSize) {
			$CanBuildElement = true;
		} else {
			$CanBuildElement = false;
		}

		$parse['BuildingsList'] = array();

		foreach($reslist['build'] as $Element)
		{
			if (in_array($Element, $Allowed[$this->planet->data['planet_type']]))
			{
				$CurrentMaxFields      = CalculateMaxPlanetFields($this->planet->data);
				if ($this->planet->data["field_current"] < ($CurrentMaxFields - $Queue['lenght'])) {
					$RoomIsOk = true;
				} else {
					$RoomIsOk = false;
				}

				if (IsTechnologieAccessible($this->user->data, $this->planet->data, $Element))
				{
					$HaveRessources        = IsElementBuyable ($this->user, $this->planet->data, $Element, true, false);
					$build                 = array();

					$build['i']            = $Element;
					$BuildingLevel         = $this->planet->data[$resource[$Element]];
					$build['nivel']        = ($BuildingLevel == 0) ? "<font color=#FF0000>". $BuildingLevel ."</font>" : "<font color=#00FF00>". $BuildingLevel ."</font>";
					$build['n']            = $lang['tech'][$Element];

					if ($this->user->data['design'] == 1)
						$build['descriptions'] = $lang['res']['descriptions'][$Element];
					else
						$build['descriptions'] = $lang['tech'][$Element];

					$ElementBuildTime      = GetBuildingTime($this->user, $this->planet->data, $Element);
					$build['time']         = ShowBuildTime($ElementBuildTime);
					$build['price']        = GetElementPrice(GetBuildingPrice($this->user, $this->planet->data, $Element), $this->user->data, $this->planet->data);
					$build['add']		   = GetNextProduction ($Element, $BuildingLevel);
					$build['click']        = '';
					$NextBuildLevel        = $this->planet->data[$resource[$Element]] + 1;

					if ($Element == 31) {
						if ($this->user->data["b_tech_planet"] != 0) {
							$build['click'] = "<font color=#FF0000>". $lang['in_working'] ."</font>";
						}
					}

					if (!$build['click']) {
						if ($RoomIsOk && $CanBuildElement) {
							if ($Queue['lenght'] == 0) {
								if ($NextBuildLevel == 1) {
									if ( $HaveRessources == true ) {
										$build['click'] = "<a href=\"?set=buildings&cmd=insert&building=". $Element ."\"><font color=#00FF00>Построить</font></a>";
									} else {
										$build['click'] = "<font color=#FF0000>нет ресурсов</font>";
									}
								} else {
									if ( $HaveRessources == true ) {
										$build['click'] = "<a href=\"?set=buildings&cmd=insert&building=".$Element."\"><font color=#00FF00>Построить уровень ". $NextBuildLevel ."</font></a>";
									} else {
										$build['click'] = "<font color=#FF0000>нет ресурсов</font>";
									}
								}
							} else {
								$build['click'] = "<a href=\"?set=buildings&cmd=insert&building=". $Element ."\"><font color=#00FF00>В очередь</font></a>";
							}
						} elseif ($RoomIsOk && !$CanBuildElement) {
							if ($NextBuildLevel == 1) {
								$build['click'] = "<font color=#FF0000>Построить</font>";
							} else {
								$build['click'] = "<font color=#FF0000>Построить уровень ". $NextBuildLevel ."</font>";
							}
						} else {
							$build['click'] = "<font color=#FF0000>нет места</font>";
						}
					}

					$parse['BuildingsList'][] = $build;
				}
			}
		}

		if ($Queue['lenght'] > 0) {
			$parse['BuildList']        = $Queue['buildlist'];
		} else {
			$parse['BuildListScript']  = "";
			$parse['BuildList']        = "";
		}

		$parse['planet_field_current'] 	= $this->planet->data["field_current"];
		$parse['planet_field_max']     	= CalculateMaxPlanetFields($this->planet->data);
		$parse['field_libre']       	= $parse['planet_field_max']  - $this->planet->data['field_current'];
		$parse['design']				= $this->user->data['design'];

		$Display->addTemplate('build', 'buildings_build.php');
		$Display->assign('parse', $parse, 'build');

		display('', 'Постройки');
	}

	public function Page_Research ()
	{
		global $lang, $resource, $reslist, $pricelist, $CombatCaps, $Display;

		$TechHandle = $this->HandleTechnologieBuild($this->planet, $this->user);

		$NoResearchMessage = "";
		$bContinue         = true;

		if ($this->planet->data[$resource[31]] == 0) {
			message($lang['no_laboratory'], $lang['Research']);
		}
		if (!CheckLabSettingsInQueue ( $this->planet->data )) {
			$NoResearchMessage = $lang['labo_on_update'];
			$bContinue         = false;
		}

		$space_lab = array();

		if ($this->user->data[$resource[123]] > 0) {
			$empire = db::query("SELECT `laboratory` FROM {{table}} WHERE id_owner='". $this->user->data['id'] ."' AND id <> '".$this->planet->data['id']."' AND planet_type = 1;", 'planets');
			while ($colonie = db::fetch_assoc($empire)) {
				$space_lab[] = $colonie;
			}
		}

		if ($_GET['mode'] == 'research_fleet')
			$res_array = $reslist['tech_f'];
		else
			$res_array = $reslist['tech'];

		$PageParse['mode'] = $_GET['mode'];

		if (isset($_GET['cmd']) AND $bContinue != false) {
			$TheCommand = $_GET['cmd'];
			$Techno = intval($_GET['tech']);
			if ($Techno > 0 && in_array($Techno, $res_array) ) {

				if (is_array ($TechHandle['WorkOn'])) {
					$WorkingPlanet = $TechHandle['WorkOn'];
				} else {
					$WorkingPlanet = $this->planet->data;
				}

				switch($TheCommand)
				{
					case 'cancel':
						if ($TechHandle['OnWork'] && $TechHandle['WorkOn']['b_tech_id'] == $Techno)
						{
							$nedeed = GetBuildingPrice($this->user, $WorkingPlanet, $Techno);

							if ($TechHandle['WorkOn']['id'] == $this->planet->data['id'])
							{
								$this->planet->data['metal']       += $nedeed['metal'];
								$this->planet->data['crystal']     += $nedeed['crystal'];
								$this->planet->data['deuterium']   += $nedeed['deuterium'];
							}

							$WorkingPlanet['metal']       += $nedeed['metal'];
							$WorkingPlanet['crystal']     += $nedeed['crystal'];
							$WorkingPlanet['deuterium']   += $nedeed['deuterium'];
							$WorkingPlanet['b_tech_id']   = 0;
							$WorkingPlanet["b_tech"]      = 0;
							$this->user->data['b_tech_planet'] = $WorkingPlanet["id"];
							$UpdateData                   = 1;
							$TechHandle['OnWork']         = false;
						}

						break;

					case 'search':
						if (IsTechnologieAccessible($this->user->data, $WorkingPlanet, $Techno) && IsElementBuyable($this->user, $WorkingPlanet, $Techno) && $WorkingPlanet['b_tech_id'] == 0 && !(isset($pricelist[$Techno]['max']) && $this->user->data[$resource[$Techno]] >= $pricelist[$Techno]['max']))
						{
							$costs                        = GetBuildingPrice($this->user, $WorkingPlanet, $Techno);
							$WorkingPlanet['metal']      -= $costs['metal'];
							$WorkingPlanet['crystal']    -= $costs['crystal'];
							$WorkingPlanet['deuterium']  -= $costs['deuterium'];
							$WorkingPlanet["b_tech_id"]   = $Techno;
							$WorkingPlanet["b_tech"]      = time() + GetBuildingTime($this->user, $WorkingPlanet, $Techno, $space_lab);
							$this->user->data["b_tech_planet"] = $WorkingPlanet["id"];
							$UpdateData                   = 1;
							$TechHandle['OnWork']         = true;
						} else
							$TechHandle['OnWork'] = 0;

						break;
				}

				if (isset($UpdateData) && $UpdateData == 1)
				{
					$QryUpdatePlanet  = "UPDATE {{table}} SET `b_tech_id` = '".   $WorkingPlanet['b_tech_id']   ."', ";
					$QryUpdatePlanet .= "`b_tech` = '".    $WorkingPlanet['b_tech']    ."', ";
					$QryUpdatePlanet .= "`metal` = '".     $WorkingPlanet['metal']     ."', ";
					$QryUpdatePlanet .= "`crystal` = '".    $WorkingPlanet['crystal']    ."', ";
					$QryUpdatePlanet .= "`deuterium` = '".   $WorkingPlanet['deuterium']   ."' ";
					$QryUpdatePlanet .= "WHERE `id` = '".   $WorkingPlanet['id']  ."';";
					db::query( $QryUpdatePlanet, 'planets');

					db::query("UPDATE {{table}} SET `b_tech_planet` = '". $this->user->data['b_tech_planet'] ."' WHERE `id` = '".$this->user->data['id'] ."';", 'users');
				}

				if (is_array ($TechHandle['WorkOn'])) {
					$TechHandle['WorkOn'] = $WorkingPlanet;
				} else
				{
					$this->planet->data = $WorkingPlanet;
					if ($TheCommand == 'search') {
						$TechHandle['WorkOn'] = $this->planet->data;
					}
				}
			}
		}

		$PageParse['technolist'] = array();

		foreach ($res_array AS $Tech)
		{
			if (IsTechnologieAccessible($this->user->data, $this->planet->data, $Tech))
			{
				$RowParse                	= array();
				$RowParse['tech_id']     	= $Tech;
				$building_level          	= $this->user->data[$resource[$Tech]];
				$RowParse['tech_level']  	= ($building_level == 0) ? "<font color=#FF0000>". $building_level ."</font>" : "<font color=#00FF00>". $building_level ."</font>";
				$RowParse['tech_level']	   .= (isset($pricelist[$Tech]['max'])) ? ' (max <font color=yellow>'. $pricelist[$Tech]['max'] .'</font>)' : '' ;
				$RowParse['tech_name']   	= $lang['tech'][$Tech];

				if ($this->user->data['design'] == 1)
					$RowParse['tech_descr']  	= $lang['res']['descriptions'][$Tech];
				else
					$RowParse['tech_descr']		= $lang['tech'][$Tech];


				if ($Tech > 300 && $Tech < 400) {
					if ($CombatCaps[$Tech-100]['power_up'] > 0) {
						$RowParse['add'] = '+'.($CombatCaps[$Tech-100]['power_up']*$building_level).'% атака<br>';
						$RowParse['add'] .= '+'.($CombatCaps[$Tech-100]['power_up']*$building_level).'% прочность<br>';
				}
				if ($CombatCaps[$Tech-100]['power_consumption'] > 0)
					$RowParse['add'] = '+'.($CombatCaps[$Tech-100]['power_consumption']*$building_level).'% вместимость<br>';
				} elseif ($Tech >= 120 && $Tech <= 122) {
					$RowParse['add'] = '+'.(5*$building_level).'% атака соотв. оружием<br>';
				} elseif ($Tech == 115) {
					$RowParse['add'] = '+'.(10*$building_level).'% скорости соотв. двигателем<br>';
				} elseif ($Tech == 117) {
					$RowParse['add'] = '+'.(20*$building_level).'% скорости соотв. двигателем<br>';
				} elseif ($Tech == 118) {
					$RowParse['add'] = '+'.(30*$building_level).'% скорости соотв. двигателем<br>';
				} elseif ($Tech == 108) {
					$RowParse['add'] = '+'.($building_level+1).' слотов флота<br>';
				} elseif ($Tech == 109) {
					$RowParse['add'] = '+'.(5*$building_level).'% атаки<br>';
				}elseif ($Tech == 110) {
					$RowParse['add'] = '+'.(3*$building_level).'% защиты<br>';
				} elseif ($Tech == 111) {
					$RowParse['add'] = '+'.(5*$building_level).'% прочности<br>';
				} elseif ($Tech == 123) {
					$RowParse['add'] = '+'.($building_level).'% лабораторий<br>';
				}

				$RowParse['tech_price']  	= GetElementPrice(GetBuildingPrice($this->user, $this->planet->data, $Tech), $this->user->data, $this->planet->data);
				$SearchTime              	= GetBuildingTime($this->user, $this->planet->data, $Tech, $space_lab);
				$RowParse['search_time'] 	= ShowBuildTime($SearchTime);
				$CanBeDone               	= IsElementBuyable($this->user, $this->planet->data, $Tech);

				if (!$TechHandle['OnWork'])
				{
					$LevelToDo = 1 + $this->user->data[$resource[$Tech]];
					if (isset($pricelist[$Tech]['max']) && $this->user->data[$resource[$Tech]] >= $pricelist[$Tech]['max']) {
						$TechnoLink = '<font color=#FF0000>максимальный уровень</font>';
					} elseif ($CanBeDone) {
						if (!CheckLabSettingsInQueue ( $this->planet->data))
						{
							if ($LevelToDo == 1) {
								$TechnoLink  = "<font color=#FF0000>Исследовать</font>";
							} else {
								$TechnoLink  = "<font color=#FF0000>Исследовать уровень ".$LevelToDo."</font>";
							}
						}
						else
						{
							$TechnoLink  = "<a href=\"?set=buildings&mode=".$_GET['mode']."&cmd=search&tech=".$Tech."\">";
							if ($LevelToDo == 1) {
								$TechnoLink .= "<font color=#00FF00>Исследовать</font>";
							} else {
								$TechnoLink .= "<font color=#00FF00>Исследовать уровень ".$LevelToDo."</font>";
							}
							$TechnoLink  .= "</a>";
						}
					} else
						$TechnoLink = '<font color=#FF0000>нет ресурсов</font>';

				} else {

					if ($TechHandle['WorkOn']["b_tech_id"] == $Tech)
					{
						$bloc = array();
						if ($TechHandle['WorkOn']['id'] != $this->planet->data['id']) {
							$bloc['tech_time']  = $TechHandle['WorkOn']["b_tech"] - time();
							$bloc['tech_name']  = ' на '.$TechHandle['WorkOn']["name"];
							$bloc['tech_home']  = $TechHandle['WorkOn']["id"];
							$bloc['tech_id']    = $TechHandle['WorkOn']["b_tech_id"];
						} else {
							$bloc['tech_time']  = $this->planet->data["b_tech"] - time();
							$bloc['tech_name']  = "";
							$bloc['tech_home']  = $this->planet->data["id"];
							$bloc['tech_id']    = $this->planet->data["b_tech_id"];
						}
						$TechnoLink = $bloc;
					} else {
						$TechnoLink  = "<center>-</center>";
					}
				}
				$RowParse['tech_link']  = $TechnoLink;

				$PageParse['technolist'][] = $RowParse;
			}
		}

		$PageParse['noresearch']  = $NoResearchMessage;

		$Display->addTemplate('research', 'buildings_research.php');
		$Display->assign('parse', $PageParse, 'research');

		display('', 'Исследования' );
	}

	public function Page_Fleet ()
	{
		global $lang, $resource, $reslist, $Display;

		if ($this->planet->data[$resource[21]] == 0) {
			message($lang['need_hangar'], $lang['tech'][21]);
		}

		if (isset($_POST['fmenge']))
		{
			foreach($_POST['fmenge'] as $Element => $Count) {

				$Element = intval($Element);
				$Count   = intval($Count);

				if (in_array($Element, $reslist['fleet']) && $Count > 0 && IsTechnologieAccessible ($this->user->data, $this->planet->data, $Element))
				{
					$MaxElements   = $this->GetMaxConstructibleElements ( $Element, $this->planet->data );

					if ($Count > $MaxElements)
						$Count = $MaxElements;

					$Ressource = $this->GetElementRessources($Element, $Count);

					if ($Count >= 1)
					{
						$this->planet->data['metal']          -= round($Ressource['metal'] * $this->user->bonus_res_fleet);
						$this->planet->data['crystal']        -= round($Ressource['crystal'] * $this->user->bonus_res_fleet);
						$this->planet->data['deuterium']      -= round($Ressource['deuterium'] * $this->user->bonus_res_fleet);
						$this->planet->data['b_hangar_id']    .= "". $Element .",". $Count .";";

						db::query("UPDATE {{table}} SET metal = '".$this->planet->data['metal']."', crystal = '".$this->planet->data['crystal']."', deuterium = '".$this->planet->data['deuterium']."', b_hangar_id = '".$this->planet->data['b_hangar_id']."' WHERE id = ".$this->planet->data['id'].";", "planets");
					}
				}
			}
		}

		$parse = array();
		$parse['buildlist'] = array();

		foreach($lang['tech'] as $Element => $ElementName)
		{
			if ($Element > 201 && $Element < 300)
			{
				if (IsTechnologieAccessible($this->user->data, $this->planet->data, $Element))
				{
					$build              = array();

					$build['i']			= $Element;
					$build['n']			= $ElementName;
					$build['desc']		= ($this->user->data['design'] == 0) ? $ElementName : $lang['res']['descriptions'][$Element];

					$build['count']     = ($this->planet->data[$resource[$Element]] == 0) ? "<font color=#00FF00>0</font>" : "<font color=#00FF00>". pretty_number($this->planet->data[$resource[$Element]]) . "</font>";

					$build['time'] 		= ShowBuildTime(GetBuildingTime($this->user, $this->planet->data, $Element));
					$build['can_build'] = IsElementBuyable($this->user, $this->planet->data, $Element, false);

					if ($build['can_build'])
						$build['max'] 	= $this->GetMaxConstructibleElements($Element, $this->planet->data);

					$build['price'] = GetElementPrice(GetBuildingPrice($this->user, $this->planet->data, $Element, false), $this->user->data, $this->planet->data);

					$parse['buildlist'][] = $build;
				}
			}
		}

		$Display->addTemplate('fleet', 'buildings_fleet.php');
		$Display->assign('parse', $parse, 'fleet');

		if ($this->planet->data['b_hangar_id'] != '')
			$this->ElementBuildListBox();

		display('', 'Верфь');
	}

	public function Page_Defense ()
	{
		global $lang, $resource, $reslist, $Display;
		
		if ($this->planet->data[$resource[21]] == 0 && $this->planet->data['planet_type'] != 5) {
			message($lang['need_hangar'], $lang['tech'][21]);
		}

		if (isset($_POST['fmenge'])) {

			$Missiles[502] = $this->planet->data[ $resource[502] ];
			$Missiles[503] = $this->planet->data[ $resource[503] ];
			$SiloSize      = $this->planet->data[ $resource[44] ];
			$MaxMissiles   = $SiloSize * 10;
			$BuildQueue    = $this->planet->data['b_hangar_id'];
			$BuildArray    = explode (";", $BuildQueue);
			for ($QElement = 0; $QElement < count($BuildArray); $QElement++) {

				$ElmentArray = explode (",", $BuildArray[$QElement] );

				if ($ElmentArray[0] == 502 && $ElmentArray[1] != 0) {
					$Missiles[502] += $ElmentArray[1];
				} elseif ($ElmentArray[0] == 503 && $ElmentArray[1] != 0) {
					$Missiles[503] += $ElmentArray[1];
				}
			}

			foreach($_POST['fmenge'] as $Element => $Count) {

				$Element = intval($Element);
				$Count   = abs(intval($Count));

				if (in_array($Element, $reslist['defense'])) {

					// Проверка наличия куполов
					if (($Element == 407 || $Element == 408) && $Count > 0) {

						$InQueue 	= strpos ( $this->planet->data['b_hangar_id'], $Element.",");
						$IsBuild1 	= ($this->planet->data[$resource[407]] > 0) ? true : false;
						$IsBuild2 	= ($this->planet->data[$resource[408]] > 0) ? true : false;

						if ($Element == 407 && !$IsBuild1 && $InQueue === false)
							$Count = 1;

						if ($Element == 408 && !$IsBuild2 && $InQueue === false)
							$Count = 1;

						if ($InQueue === true) $Count = 0;
						if ($Count > 1) $Count = 1;
					}
					//

					if ($Count > 0) {

						if ( IsTechnologieAccessible ($this->user->data, $this->planet->data, $Element) ) {

							$MaxElements   = $this->GetMaxConstructibleElements ( $Element, $this->planet->data );

							if ($Element == 502 || $Element == 503) {
								$ActuMissiles  = $Missiles[502] + ( 2 * $Missiles[503] );
								$MissilesSpace = $MaxMissiles - $ActuMissiles;

								if ($MissilesSpace > 0) {
									if ($Element == 502) {
										if ( $Count > $MissilesSpace )
											$Count = $MissilesSpace;
									} else {
										if ( $Count > floor( $MissilesSpace / 2 ) )
											$Count = floor( $MissilesSpace / 2 );
									}
								} else
									$Count = 0;
							}

							if ($Count > $MaxElements)
								$Count = $MaxElements;

							if ($Count > 0) {
								$Ressource = $this->GetElementRessources ($Element, $Count);

								$this->planet->data['metal']          -= round($Ressource['metal'] * $this->user->bonus_res_defence);
								$this->planet->data['crystal']        -= round($Ressource['crystal'] * $this->user->bonus_res_defence);
								$this->planet->data['deuterium']      -= round($Ressource['deuterium'] * $this->user->bonus_res_defence);
								$this->planet->data['b_hangar_id']    .= "". $Element .",". $Count .";";

								db::query("UPDATE {{table}} SET metal = '".$this->planet->data['metal']."', crystal = '".$this->planet->data['crystal']."', deuterium = '".$this->planet->data['deuterium']."', b_hangar_id = '".$this->planet->data['b_hangar_id']."' WHERE id = ".$this->planet->data['id'].";", "planets");
							}
						}
					}
				}
			}
		}

		$parse = array();
		$parse['buildlist'] = array();

		foreach($lang['tech'] as $Element => $ElementName) {
			if ($Element > 400 && $Element <= 599) {
				if (IsTechnologieAccessible($this->user->data, $this->planet->data, $Element)) {

					$build              = array();

					$build['i']			= $Element;
					$build['n']			= $ElementName;
					$build['desc']		= ($this->user->data['design'] == 0) ? $ElementName : $lang['res']['descriptions'][$Element];

					$build['count']     = ($this->planet->data[$resource[$Element]] == 0) ? "<font color=#00FF00>0</font>" : "<font color=#00FF00>". pretty_number($this->planet->data[$resource[$Element]]) . "</font>";

					$build['time'] 		= ShowBuildTime(GetBuildingTime($this->user, $this->planet->data, $Element));
					$build['can_build'] = IsElementBuyable($this->user, $this->planet->data, $Element, false);

					if ($build['can_build']) {

						$BuildIt = true;

						if ($Element == 407 || $Element == 408) {

							$InQueue = strpos ( $this->planet->data['b_hangar_id'], $Element.",");
							$IsBuild1 = ($this->planet->data[$resource[407]] > 0) ? true : false;
							$IsBuild2 = ($this->planet->data[$resource[408]] > 0) ? true : false;

							$BuildIt = false;

							if ( $InQueue === false && !$IsBuild1 && $Element == 407)
								$BuildIt = true;
							if ( $InQueue === false && !$IsBuild2 && $Element == 408)
								$BuildIt = true;
						}

						$build['only_one'] = $BuildIt;

						if ($build['only_one'])
							$build['max'] = $this->GetMaxConstructibleElements($Element, $this->planet->data);
					}

					$build['price'] = GetElementPrice(GetBuildingPrice($this->user, $this->planet->data, $Element, false), $this->user->data, $this->planet->data);

					$parse['buildlist'][] = $build;
				}
			}
		}

		$Display->addTemplate('fleet', 'buildings_defense.php');
		$Display->assign('parse', $parse, 'fleet');

		if ($this->planet->data['b_hangar_id'] != '')
			$this->ElementBuildListBox();

		display('', 'Оборона');
	}

	private function HandleTechnologieBuild ()
	{
		global $resource;

		if ($this->user->data['b_tech_planet'] != 0) {

			if ($this->user->data['b_tech_planet'] != $this->planet->data['id'])
				$WorkingPlanet = db::query("SELECT * FROM {{table}} WHERE `id` = '". $this->user->data['b_tech_planet'] ."';", 'planets', true);

			if (isset($WorkingPlanet)) {
				$ThePlanet = $WorkingPlanet;
			} else {
				$ThePlanet = $this->planet->data;
			}

			if ($ThePlanet['b_tech'] <= time() && $ThePlanet['b_tech_id'] != 0) {
				$this->user->data[$resource[$ThePlanet['b_tech_id']]]++;
				db::query("UPDATE {{table}} SET `b_tech` = '0', `b_tech_id` = '0' WHERE `id` = '". $ThePlanet['id'] ."';", 'planets');
				db::query("UPDATE {{table}} SET `".$resource[$ThePlanet['b_tech_id']]."` = '". $this->user->data[$resource[$ThePlanet['b_tech_id']]] ."', `b_tech_planet` = '0' WHERE `id` = '". $this->user->data['id'] ."';", 'users');

				$ThePlanet["b_tech_id"] = 0;
				if (!isset($WorkingPlanet))
					$this->planet->data = $ThePlanet;

				$Result['WorkOn'] = "";
				$Result['OnWork'] = false;

			} elseif ($ThePlanet["b_tech_id"] == 0) {
				db::query("UPDATE {{table}} SET `b_tech_planet` = '0'  WHERE `id` = '". $this->user->data['id'] ."';", 'users');
				$Result['WorkOn'] = "";
				$Result['OnWork'] = false;

			} else {
				$Result['WorkOn'] = $ThePlanet;
				$Result['OnWork'] = true;
			}
		} else {
			$Result['WorkOn'] = "";
			$Result['OnWork'] = false;
		}

		return $Result;
	}

	private function BuildingSavePlanetRecord ()
	{
		$QryUpdatePlanet  = "UPDATE {{table}} SET ";
		$QryUpdatePlanet .= "`b_building_id` = '". $this->planet->data['b_building_id'] ."', ";
		$QryUpdatePlanet .= "`b_building` = '".    $this->planet->data['b_building']    ."' ";
		$QryUpdatePlanet .= "WHERE ";
		$QryUpdatePlanet .= "`id` = '".            $this->planet->data['id']            ."';";
		db::query( $QryUpdatePlanet, 'planets');
	}

	private function ShowBuildingQueue ()
	{
		global $lang;

		$CurrentQueue  = $this->planet->data['b_building_id'];

		if ($CurrentQueue != 0) {
			$QueueArray    = explode ( ";", $CurrentQueue );
			$ActualCount   = count ( $QueueArray );
		} else {
			$QueueArray    = "0";
			$ActualCount   = 0;
		}

		$ListIDRow    = "";
		if ($ActualCount != 0) {
			$PlanetID     = $this->planet->data['id'];
			for ($QueueID = 0; $QueueID < $ActualCount; $QueueID++) {

				$BuildArray   = explode (",", $QueueArray[$QueueID]);
				$BuildEndTime = floor($BuildArray[3]);
				$CurrentTime  = floor(time());
				if ($BuildEndTime >= $CurrentTime) {
					$ListID       = $QueueID + 1;
					$Element      = $BuildArray[0];
					$BuildLevel   = $BuildArray[1];
					$BuildMode    = $BuildArray[4];
					$BuildTime    = $BuildEndTime - time();
					$ElementTitle = $lang['tech'][$Element];

					if ($this->user->data['design'] == 1) {
						if ($ListID > 0) {
							$ListIDRow .= "<tr>";
							if ($BuildMode == 'build') {
								$ListIDRow .= "<td class=\"c\">". $ListID .".: ". $ElementTitle ." ". $BuildLevel ."</td>";
							} else {
								$ListIDRow .= "<td class=\"c\" >". $ListID .".: ". $ElementTitle ." ". $BuildLevel ." ". $lang['destroy'] ."</td>";
							}
							$ListIDRow .= "<td class=\"k\">";
							if ($ListID == 1) {
								$ListIDRow .= "<div id=\"blc\" class=\"z\">". $BuildTime ."<br>";
								$ListIDRow .= "<a href=\"?set=buildings&listid=". $ListID ."&amp;cmd=cancel&amp;planet=". $PlanetID ."\">". $lang['DelFirstQueue'] ."</a></div>";
								$ListIDRow .= "<script language=\"JavaScript\">";
								$ListIDRow .= "BuildTimeout(". $BuildTime .", ". $ListID .", ". $PlanetID .");\n";
								$ListIDRow .= "</script>";
								$ListIDRow .= "<strong color=\"lime\"><br><font color=\"lime\">". datezone("j/m H:i:s" ,$BuildEndTime) ."</font></strong>";
							} else {
								$ListIDRow .= "<a href=\"?set=buildings&listid=". $ListID ."&amp;cmd=remove&amp;planet=". $PlanetID ."\">Удалить</a>";
							}
							$ListIDRow .= "</td></tr>";
						}
					} else {
						if ($ListID > 0) {
							$ListIDRow .= "<tr><th>";
							if ($BuildMode == 'build') {
								$ListIDRow .= "	". $ListID .".: ". $ElementTitle ." ". $BuildLevel ."";
							} else {
								$ListIDRow .= "	". $ListID .".: ". $ElementTitle ." ". $BuildLevel ." ". $lang['destroy'] ."";
							}
							$ListIDRow .= "</th><td class=\"k\">";
							if ($ListID == 1) {
								$ListIDRow .= "<a href=\"?set=buildings&listid=". $ListID ."&amp;cmd=cancel&amp;planet=". $PlanetID ."\">". $lang['DelFirstQueue'] ."</a> ";
								$ListIDRow .= "<font color=\"lime\">". datezone("j/m H:i:s" ,$BuildEndTime) ."</font>";
							} else {
								$ListIDRow .= "<a href=\"?set=buildings&listid=". $ListID ."&amp;cmd=remove&amp;planet=". $PlanetID ."\">Удалить</a>";
							}
							$ListIDRow .= "</td></tr>";
						}
					}
				}
			}
		}

		$RetValue['lenght']    = $ActualCount;
		$RetValue['buildlist'] = $ListIDRow;

		return $RetValue;
	}

	private function AddBuildingToQueue ($Element, $AddMode = true)
	{
		global $resource;

		$CurrentQueue  = $this->planet->data['b_building_id'];
		if ($CurrentQueue != 0) {
			$QueueArray    = explode ( ";", $CurrentQueue );
			$ActualCount   = count ( $QueueArray );
		} else {
			$QueueArray    = "";
			$ActualCount   = 0;
		}

		if ($AddMode == true) {
			$BuildMode = 'build';
		} else {
			$BuildMode = 'destroy';
		}

		$MaxBuidSize = MAX_BUILDING_QUEUE_SIZE;
		if ($this->user->data['rpg_constructeur'] > time()) $MaxBuidSize += 2;

		if ( $ActualCount < $MaxBuidSize ) {
			$QueueID      = $ActualCount + 1;
		} else {
			$QueueID      = false;
		}

		$CurrentMaxFields      = CalculateMaxPlanetFields($this->planet->data);
		if ($this->planet->data["field_current"] < ($CurrentMaxFields - $ActualCount) || $BuildMode == 'destroy') {
			$RoomIsOk = true;
		} else {
			$RoomIsOk = false;
		}

		if ( $QueueID != false && $RoomIsOk ) {
			if ($QueueID > 1) {
				$InArray = 0;
				for ( $QueueElement = 0; $QueueElement < $ActualCount; $QueueElement++ ) {
					$QueueSubArray = explode ( ",", $QueueArray[$QueueElement] );
					if ($QueueSubArray[0] == $Element) {
						$InArray++;
					}
				}
			} else {
				$InArray = 0;
			}

			if ($InArray != 0) {
				$ActualLevel  = $this->planet->data[$resource[$Element]];
				if ($AddMode == true) {
					$BuildLevel   = $ActualLevel + 1 + $InArray;
					$this->planet->data[$resource[$Element]] += $InArray;
					$BuildTime    = GetBuildingTime($this->user, $this->planet->data, $Element);
					$this->planet->data[$resource[$Element]] -= $InArray;
				} else {
					$BuildLevel   = $ActualLevel - 1 + $InArray;
					$this->planet->data[$resource[$Element]] -= $InArray;
					$BuildTime    = GetBuildingTime($this->user, $this->planet->data, $Element) / 2;
					$this->planet->data[$resource[$Element]] += $InArray;
				}
			} else {
				$ActualLevel  = $this->planet->data[$resource[$Element]];
				if ($AddMode == true) {
					$BuildLevel   = $ActualLevel + 1;
					$BuildTime    = GetBuildingTime($this->user, $this->planet->data, $Element);
				} else {
					$BuildLevel   = $ActualLevel - 1;
					$BuildTime    = GetBuildingTime($this->user, $this->planet->data, $Element) / 2;
				}
			}

			if ($QueueID == 1) {
				$BuildEndTime = time() + $BuildTime;
			} else {
				$PrevBuild = explode (",", $QueueArray[$ActualCount - 1]);
				$BuildEndTime = $PrevBuild[3] + $BuildTime;
			}
			$QueueArray[$ActualCount]       = $Element .",". $BuildLevel .",". $BuildTime .",". $BuildEndTime .",". $BuildMode;
			$NewQueue                       = implode ( ";", $QueueArray );
			$this->planet->data['b_building_id'] = $NewQueue;
		}

		$this->BuildingSavePlanetRecord( $this->planet->data );

        system::Redirect("?set=buildings");
	}

	private function CancelBuildingFromQueue ()
	{
		if ($this->planet->data['b_building_id'] != '')
		{
			$QueueArray          = explode ( ";", $this->planet->data['b_building_id'] );
			$ActualCount         = count ( $QueueArray );

			$CanceledIDArray     = explode ( ",", $QueueArray[0] );
			$Element             = $CanceledIDArray[0];
			$BuildMode           = $CanceledIDArray[4];

			if ($ActualCount > 1)
			{
				array_shift($QueueArray);
				$NewCount     = count($QueueArray);

				$BuildEndTime = time();

				for ($ID = 0; $ID < $NewCount ; $ID++ )
				{
					$ListIDArray         = explode ( ",", $QueueArray[$ID] );

					$ListIDArray[2]      = GetBuildingTime($this->user, $this->planet->data, $ListIDArray[0]);

					if ($ListIDArray[4] == 'destroy')
						$ListIDArray[2]  = ceil($ListIDArray[2] / 2);

					$BuildEndTime       += $ListIDArray[2];
					$ListIDArray[3]      = $BuildEndTime;
					$QueueArray[$ID]     = implode ( ",", $ListIDArray );
				}

				$NewQueue        = implode(";", $QueueArray );
				$BuildEndTime    = '0';
			}
			else
			{
				$NewQueue        = '';
				$BuildEndTime    = 0;
			}

			$ForDestroy = ($BuildMode == 'destroy') ? true : false;

			if ($Element)
			{
				$Needed                        		= GetBuildingPrice ($this->user, $this->planet->data, $Element, true, $ForDestroy);
				$this->planet->data['metal']       += $Needed['metal'];
				$this->planet->data['crystal']     += $Needed['crystal'];
				$this->planet->data['deuterium']   += $Needed['deuterium'];

				db::query("UPDATE {{table}} SET metal = '".$this->planet->data['metal']."', crystal = '".$this->planet->data['crystal']."', deuterium = '".$this->planet->data['deuterium']."' WHERE id = ".$this->planet->data['id'].";", "planets");
			}
		}
		else
		{
			$NewQueue          = '';
			$BuildEndTime      = 0;
		}

		$this->planet->data['b_building_id']  = $NewQueue;
		$this->planet->data['b_building']     = $BuildEndTime;

		$this->BuildingSavePlanetRecord($this->planet->data);

        system::Redirect("?set=buildings");
	}

	private function RemoveBuildingFromQueue ($QueueID)
	{
		if (empty($this->planet->data['b_building_id']))
			return false;

		$CurrentQueue  = $this->planet->data['b_building_id'];

		$QueueArray    = explode (";", $CurrentQueue);
		$ActualCount   = count ($QueueArray);

		if ($ActualCount < $QueueID)
			return false;

		if ($ActualCount <= 1 || $QueueID <= 1)
			$this->CancelBuildingFromQueue();

		unset($QueueArray[$QueueID - 1]);

		$ListIDArray   = explode (",", $QueueArray[0]);
		$BuildEndTime  = $ListIDArray[3];

		foreach ($QueueArray as $ID => $QueueInfo)
		{
			if ($ID == 0) continue;

			$ListIDArray         = explode(",", $QueueInfo);
			$ListIDArray[2]      = GetBuildingTime($this->user, $this->planet->data, $ListIDArray[0]);

			if ($ListIDArray[4] == 'destroy')
				$ListIDArray[2]  = ceil($ListIDArray[2] / 2);

			$BuildEndTime		+= $ListIDArray[2];

			$ListIDArray[3]      = $BuildEndTime;
			$QueueArray[$ID]  	 = implode (",", $ListIDArray);
		}

		$NewQueue = implode (";", $QueueArray);

		$this->planet->data['b_building_id'] = $NewQueue;

		$this->BuildingSavePlanetRecord($this->planet->data);

        system::Redirect("?set=buildings");
	}

	private function ElementBuildListBox ()
	{
		global $lang, $Display;

		$ElementQueue = explode(';', $this->planet->data['b_hangar_id']);
		$NbrePerType  = "";
		$NamePerType  = "";
		$TimePerType  = "";
		$QueueTime    = 0;

		foreach($ElementQueue as $Element)
		{
			if ($Element != '')
			{
				$Element = explode(',', $Element);
				$ElementTime  = GetBuildingTime( $this->user, $this->planet->data, $Element[0] );
				$QueueTime   += $ElementTime * $Element[1];
				$TimePerType .= "".$ElementTime.",";
				$NamePerType .= "'". html_entity_decode($lang['tech'][$Element[0]]) ."',";
				$NbrePerType .= "".$Element[1].",";
			}
		}

		$parse = array();
		$parse['a'] = $NbrePerType;
		$parse['b'] = $NamePerType;
		$parse['c'] = $TimePerType;
		$parse['b_hangar_id_plus'] = $this->planet->data['b_hangar'];

		$parse['pretty_time_b_hangar'] = pretty_time($QueueTime - $this->planet->data['b_hangar']);

		$Display->addTemplate('script', 'buildings_script.php');
		$Display->assign('parse', $parse, 'script');
	}

	private function GetElementRessources ( $Element, $Count )
	{
		global $pricelist;

		$ResType['metal']     = ($pricelist[$Element]['metal']     * $Count);
		$ResType['crystal']   = ($pricelist[$Element]['crystal']   * $Count);
		$ResType['deuterium'] = ($pricelist[$Element]['deuterium'] * $Count);

		return $ResType;
	}

	private function GetMaxConstructibleElements ($Element, $Ressources)
	{
		global $pricelist;

		$MaxElements = -1;

		foreach ($pricelist[$Element] AS $need_res => $need_count)
		{
			if (in_array($need_res, array('metal', 'crystal', 'deuterium', 'energy_max')))
			{
				if ($need_count != 0)
				{
					$count = 0;

					if ($Element > 200 && $Element < 300)
						$count = round($need_count * $this->user->bonus_res_fleet);
					elseif ($Element > 400 && $Element < 504)
						$count = round($need_count * $this->user->bonus_res_defence);

					$count = floor($Ressources[$need_res] / $count);

					if ($MaxElements == -1) {
						$MaxElements = $count;
					} elseif ($MaxElements > $count) {
						$MaxElements = $count;
					}
				}
			}
		}

		return $MaxElements;
	}

}

?>