<?php

/**
 * @author AlexPro
 * @copyright 2008 - 2011 XNova Game Group
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

class planet {

	/**
	 * @var user $user
	 */
	private $user;
	public $data;
	
	public function load_from_id ($planet_id)
	{
		$this->data = db::query("SELECT * FROM {{table}} WHERE `id` = '".$planet_id."';", 'planets', true);
	}
	
	public function load_from_coords ($galaxy, $system, $planet, $type)
	{
		$this->data = db::query("SELECT * FROM {{table}} WHERE 	`galaxy` = '".$galaxy."' AND
																`system` = '".$system."' AND 
																`planet` = '".$planet."' AND 
																`planet_type` = '".$type."';", 'planets', true);
	}

	public function load_from_array ($array)
	{
		$this->data = $array;
	}

	public function load_user_info ($array)
	{
		$this->user = $array;
	}
	
	public function PlanetResourceUpdate ($UpdateTime = 0, $Simul = false)
	{
		global $ProdGrid, $resource, $reslist, $game_config;
	
		if ($this->user->data['urlaubs_modus_time'] != 0)
			$Simul = true;
		
		if ($UpdateTime == 0)
			$UpdateTime = time();
	
		$this->data['metal_max']     	= floor((BASE_STORAGE_SIZE + floor (50000 * round(pow (1.6, $this->data[ $resource[22] ] )))) * $this->user->bonus_storage);
		$this->data['crystal_max']   	= floor((BASE_STORAGE_SIZE + floor (50000 * round(pow (1.6, $this->data[ $resource[23] ] )))) * $this->user->bonus_storage);
		$this->data['deuterium_max'] 	= floor((BASE_STORAGE_SIZE + floor (50000 * round(pow (1.6, $this->data[ $resource[24] ] )))) * $this->user->bonus_storage);
	
		$MaxMetalStorage		= $this->data['metal_max']     * MAX_OVERFLOW;
		$MaxCristalStorage		= $this->data['crystal_max']   * MAX_OVERFLOW;
		$MaxDeuteriumStorage	= $this->data['deuterium_max'] * MAX_OVERFLOW;
		$MaxEnergyStorage		= floor(10000 * pow((1.1), ($this->data['ak_station'])) * $this->data['ak_station']);
	
		$Caps             			= array();
		$Caps['metal_perhour'] 		= 0;
		$Caps['crystal_perhour'] 	= 0;
		$Caps['deuterium_perhour'] 	= 0;
		$Caps['energy_used'] 		= 0;
		$Caps['energy_max'] 		= 0;
		$BuildTemp        			= $this->data['temp_max'];
		$energy_tech				= $this->user->data['energy_tech'];
	
		foreach ($reslist['prod'] AS $ProdID) { 
			$BuildLevelFactor = $this->data[ $resource[$ProdID]."_porcent" ];
			$BuildLevel       = $this->data[ $resource[$ProdID] ];
	
			if ($ProdID == 12 && $this->data['deuterium'] < 10) $BuildLevelFactor = 0;
	
			$Caps['metal_perhour']     	+=  floor(eval($ProdGrid[$ProdID]['metal']) 	* $game_config['resource_multiplier'] * $this->user->bonus_metal);
			$Caps['crystal_perhour']   	+=  floor(eval($ProdGrid[$ProdID]['crystal']) 	* $game_config['resource_multiplier'] * $this->user->bonus_crystal);
			$Caps['deuterium_perhour'] 	+=  floor(eval($ProdGrid[$ProdID]['deuterium']) * $game_config['resource_multiplier'] * $this->user->bonus_deuterium);

			$energy 					 = floor(eval($ProdGrid[$ProdID]['energy']) 	* $game_config['resource_multiplier']);

			if ($ProdID < 4)
				$Caps['energy_used'] += $energy;
			else
			{
				if ($ProdID == 4 || $ProdID == 12)
					$energy  = floor($energy * $this->user->bonus_energy);
				elseif ($ProdID == 212)
					$energy  = floor($energy * $this->user->bonus_solar);

				$Caps['energy_max'] += $energy;
			}
		}
	
		if ($this->data['planet_type'] == 3 || $this->data['planet_type'] == 5) {
			$game_config['metal_basic_income']     = 0;
			$game_config['crystal_basic_income']   = 0;
			$game_config['deuterium_basic_income'] = 0;
			$this->data['metal_perhour']        = 0;
			$this->data['crystal_perhour']      = 0;
			$this->data['deuterium_perhour']    = 0;
			$this->data['energy_used']          = 0;
			$this->data['energy_max']           = 0;
		} else {
			$this->data['metal_perhour']        = $Caps['metal_perhour'];
			$this->data['crystal_perhour']      = $Caps['crystal_perhour'];
			$this->data['deuterium_perhour']    = $Caps['deuterium_perhour'];
			$this->data['energy_used']          = $Caps['energy_used'];
			$this->data['energy_max']           = $Caps['energy_max'];
		}
	
		$ProductionTime = ($UpdateTime - $this->data['last_update']);
		$this->data['last_update'] = $UpdateTime;
		
		$value_energy_ak = $this->data['energy_ak'];
	
		if ($this->data['energy_max'] == 0) {
			$this->data['metal_perhour']     = $game_config['metal_basic_income'];
			$this->data['crystal_perhour']   = $game_config['crystal_basic_income'];
			$this->data['deuterium_perhour'] = $game_config['deuterium_basic_income'];
			$production_level = 0;
		} elseif ($this->data['energy_max'] >= abs($this->data['energy_used'])) {
			$production_level = 100;
			$akk_add = round(($this->data['energy_max'] - abs($this->data['energy_used']))*($ProductionTime/3600), 2);
			if ($MaxEnergyStorage > ($this->data['energy_ak'] + $akk_add))
				$this->data['energy_ak'] += $akk_add;
			else
				$this->data['energy_ak'] = $MaxEnergyStorage;
		} else {
			if ($this->data['energy_ak'] > 0) {
				$need_en = ((abs($this->data['energy_used']) - $this->data['energy_max'])/3600)*$ProductionTime;
				if ($this->data['energy_ak'] > $need_en) {
					$production_level = 100;
					$this->data['energy_ak'] -= round($need_en, 2);
				} else {
					$production_level = round((($this->data['energy_max'] + $this->data['energy_ak']*3600) / abs($this->data['energy_used'])) * 100, 1);
					$this->data['energy_ak'] = 0;
				}
			} else {
				$production_level = round(($this->data['energy_max'] / abs($this->data['energy_used'])) * 100, 1);
			}
		}
	
		if ($production_level > 100) {
			$production_level = 100;
		} elseif ($production_level < 0) {
			$production_level = 0;
		}
	
		$this->data['production_level'] = $production_level;
		
		$Metal_Production 		= 0;
		$Cristal_Production 	= 0;
		$Deuterium_Production 	= 0;
	
		if ( $this->data['metal'] <= $MaxMetalStorage ) {
			$MetalProduction = (($ProductionTime * ($this->data['metal_perhour'] / 3600))) * (0.01 * $production_level);
			$MetalBaseProduc = (($ProductionTime * ($game_config['metal_basic_income'] / 3600 )) * $game_config['resource_multiplier']);
			
			$Metal_Production = round(($MetalProduction  +  $MetalBaseProduc), 4);
			
			if (($this->data['metal'] + $Metal_Production) > $MaxMetalStorage)
				$Metal_Production = $MaxMetalStorage - $this->data['metal'];
		}
	
		if ( $this->data['crystal'] <= $MaxCristalStorage ) {
			$CristalProduction = (($ProductionTime * ($this->data['crystal_perhour'] / 3600))) * (0.01 * $production_level);
			$CristalBaseProduc = (($ProductionTime * ($game_config['crystal_basic_income'] / 3600 )) * $game_config['resource_multiplier']);
			
			$Cristal_Production = round(($CristalProduction  +  $CristalBaseProduc), 4);
			
			if (($this->data['crystal'] + $Cristal_Production) > $MaxCristalStorage)
				$Cristal_Production = $MaxCristalStorage - $this->data['crystal'];
		}
	
		if ( $this->data['deuterium'] <= $MaxDeuteriumStorage ) {
			$DeuteriumProduction = (($ProductionTime * ($this->data['deuterium_perhour'] / 3600))) * (0.01 * $production_level);
			$DeuteriumBaseProduc = (($ProductionTime * ($game_config['deuterium_basic_income'] / 3600 )) * $game_config['resource_multiplier']);
			
			$Deuterium_Production = round(($DeuteriumProduction  +  $DeuteriumBaseProduc), 4);
			
			if (($this->data['deuterium'] + $Deuterium_Production) > $MaxDeuteriumStorage)
				$Deuterium_Production = $MaxDeuteriumStorage - $this->data['deuterium'];
		}
	
		$this->data['metal_perhour'] 	 = round($this->data['metal_perhour']	  * (0.01 * $production_level));
		$this->data['crystal_perhour'] 	 = round($this->data['crystal_perhour']	  * (0.01 * $production_level));
		$this->data['deuterium_perhour'] = round($this->data['deuterium_perhour'] * (0.01 * $production_level));
		
		$this->data['metal'] 		+= $Metal_Production;
		$this->data['crystal'] 		+= $Cristal_Production;
		$this->data['deuterium'] 	+= $Deuterium_Production;
	
		if ($Simul == false) 
		{
			$Builded = $this->HandleElementBuildingQueue($ProductionTime);
	
			$QryUpdatePlanet  = "UPDATE {{table}} SET ";

			if ($this->data['planet_type'] == 1)
			{
				if ($Metal_Production != 0)
					$QryUpdatePlanet .= "`metal` = `metal` + '".$Metal_Production."', ";
					
				if ($Cristal_Production != 0)
					$QryUpdatePlanet .= "`crystal` = `crystal` + '".$Cristal_Production."', ";
					
				if ($Deuterium_Production != 0)	
					$QryUpdatePlanet .= "`deuterium` = `deuterium` + '".$Deuterium_Production."', ";
					
				if ($value_energy_ak != $this->data['energy_ak'])
					$QryUpdatePlanet .= "`energy_ak` = '".$this->data['energy_ak']."', ";
			}

			$QryUpdatePlanet .= "`last_update` = '"      . $this->data['last_update']       ."', ";
			$QryUpdatePlanet .= "`b_hangar_id` = '"      . $this->data['b_hangar_id']       ."', ";

	
			if ( $Builded != '' ) {
				foreach ( $Builded as $Element => $Count ) {
					if ($Element <> '') {
						$QryUpdatePlanet .= "`". $resource[$Element] ."` = '". $this->data[$resource[$Element]] ."', ";
					}
				}
			}
			
			$QryUpdatePlanet .= "`b_hangar` = '". $this->data['b_hangar'] ."' WHERE `id` = '". $this->data['id'] ."';";
			
			db::query($QryUpdatePlanet, 'planets');
		}
	}

	public function CheckPlanetUsedFields ()
	{
		global $resource;

		$cfc  = $this->data[$resource[1]]  + $this->data[$resource[2]]  + $this->data[$resource[3]] ;
		$cfc += $this->data[$resource[4]]  + $this->data[$resource[6]]  + $this->data[$resource[12]] + $this->data[$resource[14]];
		$cfc += $this->data[$resource[15]] + $this->data[$resource[21]] + $this->data[$resource[22]];
		$cfc += $this->data[$resource[23]] + $this->data[$resource[24]] + $this->data[$resource[31]];
		$cfc += $this->data[$resource[33]] + $this->data[$resource[34]] + $this->data[$resource[44]];


		if ($this->data['planet_type'] == '3' || $this->data['planet_type'] == '5') {
			$cfc += $this->data[$resource[41]] + $this->data[$resource[42]] + $this->data[$resource[43]];
		}

		if ($this->data['field_current'] != $cfc) {
			$this->data['field_current'] = $cfc;
			db::query("UPDATE {{table}} SET field_current = ".$cfc." WHERE id = ".$this->data['id']."", 'planets');
		}
	}

	private function HandleElementBuildingQueue ($ProductionTime)
	{
		global $resource;

		if ($this->data['b_hangar_id'])
		{
			$this->data['b_hangar'] += $ProductionTime;
			$BuildQueue              = explode(';', $this->data['b_hangar_id']);

			$MissilesSpace = ($this->data[ $resource[44] ] * 10) - ($this->data['interceptor_misil'] + ( 2 * $this->data['interplanetary_misil'] ));
			$Shield_1 = $this->data['small_protection_shield'];
			$Shield_2 = $this->data['big_protection_shield'];

			$BuildArray = array();
			$Builded    = array ();

			foreach ($BuildQueue as $Node => $Array)
			{
				if ($Array != '') {
					$Item = explode(',', $Array);

					if ($Item[0] == 502 || $Item[0] == 503) {
						if ($Item[0] == 502) {
							if ($Item[1] > $MissilesSpace)
								$Item[1] = $MissilesSpace;
							else
								$MissilesSpace -= $Item[1];
						} else {
							if ($Item[1] > floor($MissilesSpace / 2))
								$Item[1] = floor($MissilesSpace / 2);
							else
								$MissilesSpace -= $Item[1];
						}
					}

					if ($Item[0] == 407 || $Item[0] == 408) {
						if ($Item[1] > 1)
							$Item[1] = 1;

						if ($Item[0] == 407) {
							if ($Shield_1 == 1)
								$Item[1] = 0;
							else
								$Shield_1 = 1;
						} else {
							if ($Shield_2 == 1)
								$Item[1] = 0;
							else
								$Shield_2 = 1;
						}
					}

					$BuildArray[$Node] = array($Item[0], $Item[1], GetBuildingTime ($this->user, $this->data, $Item[0]));
				}
			}

			$this->data['b_hangar_id'] = '';

			$UnFinished = false;

			foreach ($BuildArray as $Item)
			{

				if (!isset($resource[$Item[0]]))
					continue;

				$Element   = $Item[0];
				$Count     = $Item[1];
				$BuildTime = $Item[2];

				if (!isset($Builded[$Element]))
					$Builded[$Element] = 0;

				while ($this->data['b_hangar'] >= $BuildTime && !$UnFinished)
				{

					$this->data['b_hangar'] -= $BuildTime;
					$Builded[$Element]++;
					$this->data[$resource[$Element]]++;
					$Count--;

					if ($Count == 0)
					{
						break;
					}
					elseif ($this->data['b_hangar'] < $BuildTime)
					{
						$UnFinished = true;
					}
				}

				if ($Count > 0)
				{
					$UnFinished = true;
					$this->data['b_hangar_id'] .= $Element.",".$Count.";";
				}
			}
		}
		else
		{
			$Builded                = '';
			$this->data['b_hangar'] = 0;
		}

		return $Builded;
	}

	public function UpdatePlanetBatimentQueueList ()
	{
		$RetValue = false;

		if ($this->data['b_building_id'])
		{
			$build_count = explode(';', $this->data['b_building_id']);
			$build_count = count($build_count);

			for ($i = 0; $i < $build_count; $i++)
			{
				if ($this->data['b_building'] <= time())
				{
					if ($this->CheckPlanetBuildingQueue())
					{
						$this->SetNextQueueElementOnTop();
						$RetValue = true;
					}
					else
						break;
				}
				else
					break;
			}
		}
		return $RetValue;
	}

	private function CheckPlanetBuildingQueue ()
	{
		global $resource;

		$RetValue = false;

		if ($this->data['b_building_id'])
		{
			$QueueArray = explode (";", $this->data['b_building_id']);

			$BuildArray 	= explode (",", $QueueArray[0]);
			$Element 		= $BuildArray[0];

			array_shift($QueueArray);

			$ForDestroy = ($BuildArray[4] == 'destroy') ? true : false;

			if ($BuildArray[3] <= time())
			{
				$Needed = GetBuildingPrice ($this->user, $this->data, $Element, true, $ForDestroy);
				$Units  = $Needed['metal'] + $Needed['crystal'] + $Needed['deuterium'];

				// Мирный опыт за строения
				$XPBuildings  = array(1,  2,  3, 5, 22, 23, 24, 25);
				$XP			  = 0;

				if (in_array($Element, $XPBuildings))
				{
					if (!$ForDestroy)
						$XP += floor($Units / 1500);
					else
						$XP -= floor($Units / 1500);
				}

				if (!$ForDestroy) {
					$this->data['field_current']++;
					$this->data[$resource[$Element]]++;
				} else {
					$this->data['field_current']--;
					$this->data[$resource[$Element]]--;
				}

				$NewQueue = (count($QueueArray) == 0) ? '' : implode(";", $QueueArray);

				$this->data['b_building']     = 0;
				$this->data['b_building_id']  = $NewQueue;
				$this->data['b_building_end'] = $BuildArray[3];

				$QryUpdatePlanet  = "UPDATE {{table}} SET ";
				$QryUpdatePlanet .= "`".$resource[$Element]."` = '".$this->data[$resource[$Element]]."', ";
				$QryUpdatePlanet .= "`b_building` = 0, ";
				$QryUpdatePlanet .= "`b_building_id` = '". $this->data['b_building_id'] ."' , ";
				$QryUpdatePlanet .= "`field_current` = '" . $this->data['field_current'] . "' ";
				$QryUpdatePlanet .= "WHERE `id` = '" . $this->data['id'] . "';";

				db::query( $QryUpdatePlanet, 'planets');

				if ($XP != 0 && $this->user->data['lvl_minier'] < 100)
				{
					$this->user->data['xpminier'] += $XP;
					db::query("UPDATE {{table}} SET `xpminier` = '".$this->user->data['xpminier']."' WHERE `id` = '" .$this->user->data['id']. "';", 'users');
				}

				$RetValue = true;
			}
			else
			{
				$RetValue = false;
			}
		}

		return $RetValue;
	}

	public function SetNextQueueElementOnTop ()
	{
		global $lang, $resource;

		if ($this->data['b_building'] == 0)
		{
			/**
			 * @var $BuildEndTime
			 * @var $NewQueue
			 */
			if ($this->data['b_building_id'])
			{
				$QueueArray = explode ( ";", $this->data['b_building_id'] );

				if (isset($this->data['b_building_end']))
				{
					$BuildEndTime = $this->data['b_building_end'];
					foreach ($QueueArray as $ID => $QueueInfo)
					{
						$ListIDArray          = explode(",", $QueueInfo);

						$ListIDArray[2] = GetBuildingTime($this->user, $this->data, $ListIDArray[0]);
						if ($ListIDArray[4] == 'destroy')
							$ListIDArray[2]   = ceil($ListIDArray[2] / 2);

						$BuildEndTime        += $ListIDArray[2];
						$ListIDArray[3]       = $BuildEndTime;
						$QueueArray[$ID]      = implode ( ",", $ListIDArray );
					}
				}

				$Loop = true;

				while ($Loop)
				{
					$ListIDArray         = explode (",", $QueueArray[0]);
					$Element             = $ListIDArray[0];
					$BuildEndTime        = $ListIDArray[3];
					$BuildMode           = $ListIDArray[4];
					$HaveNoMoreLevel     = false;

					$ForDestroy =  ($BuildMode == 'destroy') ? true : false;

					if ($ForDestroy && $this->data[$resource[$Element]] == 0)
					{
						$HaveRessources  = false;
						$HaveNoMoreLevel = true;
					} else
						$HaveRessources = IsElementBuyable($this->user, $this->data, $Element, true, $ForDestroy);

					if ($HaveRessources && IsTechnologieAccessible($this->user->data, $this->data, $Element))
					{
						$Needed = GetBuildingPrice($this->user, $this->data, $Element, true, $ForDestroy);

						$this->data['metal']       -= $Needed['metal'];
						$this->data['crystal']     -= $Needed['crystal'];
						$this->data['deuterium']   -= $Needed['deuterium'];

						$NewQueue = implode (";", $QueueArray);

						$Loop = false;
					}
					else
					{
						if ($HaveNoMoreLevel)
							$Message     = sprintf ($lang['sys_nomore_level'], $lang['tech'][$Element] );
						elseif (!$HaveRessources)
						{
							$Needed		= GetBuildingPrice($this->user, $this->data, $Element, true, $ForDestroy);

							$Message    = 'У вас недостаточно ресурсов чтобы начать строительство здания '.$lang['tech'][$Element].'.<br>Вам необхолдимо ещё: <br>';
							if ($Needed['metal'] > $this->data['metal'])
								$Message .= pretty_number($Needed['metal'] - $this->data['metal']).' металла<br>';
							if ($Needed['crystal'] > $this->data['crystal'])
								$Message .= pretty_number($Needed['crystal'] - $this->data['crystal']).' кристалла<br>';
							if ($Needed['deuterium'] > $this->data['deuterium'])
								$Message .= pretty_number($Needed['deuterium'] - $this->data['deuterium']).' дейтерия<br>';
							if ($Needed['energy_max'] > $this->data['energy_max'])
								$Message .= pretty_number($Needed['energy_max'] - $this->data['energy_max']).' энергии<br>';
						}

						if (isset($Message))
							SendSimpleMessage( $this->user->data['id'], '', '', 99, $lang['sys_buildlist'], $Message);

						array_shift($QueueArray);

						if (count($QueueArray) == 0)
						{
							$BuildEndTime  = 0;
							$NewQueue      = '';
							$Loop          = false;
						}
					}
				}
			}
			else
			{
				$BuildEndTime  = 0;
				$NewQueue      = '';
			}

			if ($this->data['b_building'] != $BuildEndTime || $this->data['b_building_id'] != $NewQueue)
			{
				$this->data['b_building']    = $BuildEndTime;
				$this->data['b_building_id'] = $NewQueue;

				$QryUpdatePlanet  = "UPDATE {{table}} SET ";
				$QryUpdatePlanet .= "`metal` = '".         $this->data['metal']         ."' , ";
				$QryUpdatePlanet .= "`crystal` = '".       $this->data['crystal']       ."' , ";
				$QryUpdatePlanet .= "`deuterium` = '".     $this->data['deuterium']     ."' , ";
				$QryUpdatePlanet .= "`b_building` = '".    $this->data['b_building']    ."' , ";
				$QryUpdatePlanet .= "`b_building_id` = '". $this->data['b_building_id'] ."' ";
				$QryUpdatePlanet .= "WHERE ";
				$QryUpdatePlanet .= "`id` = '" .           $this->data['id']            . "';";
				db::query( $QryUpdatePlanet, 'planets');
			}
		}
	}
}
 
 ?>
