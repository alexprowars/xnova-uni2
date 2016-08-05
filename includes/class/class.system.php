<?php

/**
 * @author AlexPro
 * @copyright 2008 - 2011 XNova Game Group
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

class system
{
	static function CheckReferLink()
	{
		if (!isset($_SESSION['uid']) && is_numeric($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0) {
			$id = intval($_SERVER['QUERY_STRING']);

			$login = db::query("SELECT `id` FROM {{table}} WHERE `id` = '".$id."'", 'users', true);

			if (isset($login['id'])) {

				$ip 	= $_SERVER['HTTP_X_REAL_IP'];

				$res = db::query("SELECT `id` FROM {{table}} where `ip` = '".$ip."' AND `time` > '".(time() - 86400)."'", 'moneys', true);

				if (!isset($res['id'])) {
					db::query("INSERT INTO {{table}} values ('".$login['id']."', '".$ip."','".time()."','".addslashes($_SERVER['HTTP_REFERER'])."', '".addslashes($_SERVER['HTTP_USER_AGENT'])."')", 'moneys');
					db::query("UPDATE {{table}} SET links = links + 1, refers = refers + 1 WHERE id = '".$login['id']."'", 'users');
				}
				$_SESSION['ref'] = $login['id'];
			}
		}
	}

	static function includeLang ($filename)
	{
		global $lang;

		include ("language/". $filename . ".php");
	}

	static function GetMissileRange ()
	{
		global $resource, $user;

		if ($user->data[$resource[117]] > 0) {
			$MissileRange = ($user->data[$resource[117]] * 5) - 1;
		} else {
			$MissileRange = 0;
		}

		return $MissileRange;
	}

	static function GetPhalanxRange ($PhalanxLevel)
	{
		$PhalanxRange = 0;
		if ($PhalanxLevel > 1) {
			for ($Level = 2; $Level < $PhalanxLevel + 1; $Level++) {
				$lvl           = ($Level * 2) - 1;
				$PhalanxRange += $lvl;
			}
		}

		return $PhalanxRange;
	}

	static function PlanetSizeRandomiser ($Position, $HomeWorld = false, $Base = false)
	{
		global $game_config;

		if (!$HomeWorld && !$Base) {
			$ClassicBase      = 163;
			$PlanetRatio      = floor ( ($ClassicBase / $game_config['initial_fields']) * 10000 ) / 100;
			$RandomMin        = array (  40,  50,  55, 100,  95,  80, 115, 120, 125,  75,  80,  85,  60,  40,  50);
			$RandomMax        = array (  90,  95,  95, 240, 240, 230, 180, 180, 190, 125, 120, 130, 160, 300, 150);
			$CalculMin        = floor ( $RandomMin[$Position - 1] + ( $RandomMin[$Position - 1] * $PlanetRatio ) / 100 );
			$CalculMax        = floor ( $RandomMax[$Position - 1] + ( $RandomMax[$Position - 1] * $PlanetRatio ) / 100 );
			$PlanetFields     = mt_rand($CalculMin, $CalculMax);
		} else {
			$PlanetFields = ($HomeWorld) ? $game_config['initial_fields'] : 10;
		}

		$PlanetSize           = ($PlanetFields ^ (14 / 1.5)) * 75;

		$return['diameter']   = $PlanetSize;
		$return['field_max']  = $PlanetFields;
		return $return;
	}

	static function CreateOnePlanetRecord ($Galaxy, $System, $Position, $PlanetOwnerID, $PlanetName = '', $HomeWorld = false, $Base = false)
	{
		global $lang;

		$QrySelectPlanet  = "SELECT	`id` ";
		$QrySelectPlanet .= "FROM {{table}} ";
		$QrySelectPlanet .= "WHERE ";
		$QrySelectPlanet .= "`galaxy` = '". $Galaxy ."' AND ";
		$QrySelectPlanet .= "`system` = '". $System ."' AND ";
		$QrySelectPlanet .= "`planet` = '". $Position ."';";
		$PlanetExist = db::query( $QrySelectPlanet, 'planets', true);

		if (!$PlanetExist) {
			$planet                      = self::PlanetSizeRandomiser ($Position, $HomeWorld, $Base);
			$planet['metal']             = BUILD_METAL;
			$planet['crystal']           = BUILD_CRISTAL;
			$planet['deuterium']         = BUILD_DEUTERIUM;

			$planet['galaxy'] = $Galaxy;
			$planet['system'] = $System;
			$planet['planet'] = $Position;

			if ($Position == 1 || $Position == 2 || $Position == 3) {
				$PlanetType         = array('trocken');
				$PlanetClass        = array('planet');
				$PlanetDesign       = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20');
				$planet['temp_min'] = rand(0, 100);
				$planet['temp_max'] = $planet['temp_min'] + 40;
			} elseif ($Position == 4 || $Position == 5 || $Position == 6) {
				$PlanetType         = array('dschjungel');
				$PlanetClass        = array('planet');
				$PlanetDesign       = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19');
				$planet['temp_min'] = rand(-25, 75);
				$planet['temp_max'] = $planet['temp_min'] + 40;
			} elseif ($Position == 7 || $Position == 8 || $Position == 9) {
				$PlanetType         = array('normaltemp');
				$PlanetClass        = array('planet');
				$PlanetDesign       = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14', '15');
				$planet['temp_min'] = rand(-50, 50);
				$planet['temp_max'] = $planet['temp_min'] + 40;
			} elseif ($Position == 10 || $Position == 11 || $Position == 12) {
				$PlanetType         = array('wasser');
				$PlanetClass        = array('planet');
				$PlanetDesign       = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18');
				$planet['temp_min'] = rand(-75, 25);
				$planet['temp_max'] = $planet['temp_min'] + 40;
			} elseif ($Position == 13 || $Position == 14 || $Position == 15) {
				$PlanetType         = array('eis');
				$PlanetClass        = array('planet');
				$PlanetDesign       = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20');
				$planet['temp_min'] = rand(-100, 10);
				$planet['temp_max'] = $planet['temp_min'] + 40;
			} else {
				$PlanetType         = array('dschjungel', 'gas', 'normaltemp', 'trocken', 'wasser', 'wuesten', 'eis');
				$PlanetClass        = array('planet');
				$PlanetDesign       = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10');
				$planet['temp_min'] = rand(-120, 10);
				$planet['temp_max'] = $planet['temp_min'] + 40;
			}

			$planet['planet_type'] = 1;

			if ($Base == true){
				$PlanetType         = array('base');
				$PlanetClass        = array('planet');
				$PlanetDesign       = array('01');
				$planet['planet_type'] = 5;
			}

			$planet['image']       = $PlanetType[ rand( 0, count( $PlanetType ) -1 ) ];
			$planet['image']      .= $PlanetClass[ rand( 0, count( $PlanetClass ) - 1 ) ];
			$planet['image']      .= $PlanetDesign[ rand( 0, count( $PlanetDesign ) - 1 ) ];
			$planet['id_owner']    = $PlanetOwnerID;
			$planet['last_update'] = time();
			$planet['name']        = ($PlanetName == '') ? $lang['sys_colo_defaultname'] : $PlanetName;

			$QryInsertPlanet  = "INSERT INTO {{table}} SET ";
			$QryInsertPlanet .= "`name` = '".              $planet['name']              ."', ";
			$QryInsertPlanet .= "`id_owner` = '".          $planet['id_owner']          ."', ";
			$QryInsertPlanet .= "`galaxy` = '".            $planet['galaxy']            ."', ";
			$QryInsertPlanet .= "`system` = '".            $planet['system']            ."', ";
			$QryInsertPlanet .= "`planet` = '".            $planet['planet']            ."', ";
			$QryInsertPlanet .= "`last_update` = '".       $planet['last_update']       ."', ";
			$QryInsertPlanet .= "`planet_type` = '".       $planet['planet_type']       ."', ";
			$QryInsertPlanet .= "`image` = '".             $planet['image']             ."', ";
			$QryInsertPlanet .= "`diameter` = '".          $planet['diameter']          ."', ";
			$QryInsertPlanet .= "`field_max` = '".         $planet['field_max']         ."', ";
			$QryInsertPlanet .= "`temp_min` = '".          $planet['temp_min']          ."', ";
			$QryInsertPlanet .= "`temp_max` = '".          $planet['temp_max']          ."'; ";
			db::query( $QryInsertPlanet, 'planets');

			if (isset($_SESSION['fleet_shortcut']))
				unset($_SESSION['fleet_shortcut']);

			$RetValue = true;
		} else {

			$RetValue = false;
		}

		return $RetValue;
	}

	static function CreateRandomPassword ()
	{
		$Caracters	= "aazertyuiopqsdfghjklmwxcvbnAZERTYUIOPQSDFGHJKLMWXCVBN1234567890";
		$Count		= strlen($Caracters);
		$NewPass 	= "";

		for ($i = 0; $i < 6; $i++)
		{
			$CaracterBoucle	 = rand(0, $Count-1);
			$NewPass		.= substr($Caracters, $CaracterBoucle, 1);
		}

		return $NewPass;
	}

	static function CreateRegPlanet ($user_id)
	{
		global $game_config;

		$LastSettedGalaxyPos  = $game_config['LastSettedGalaxyPos'];
		$LastSettedSystemPos  = $game_config['LastSettedSystemPos'];
		$LastSettedPlanetPos  = $game_config['LastSettedPlanetPos'];

		$newpos_checked = false;

		$Galaxy = 1;
		$System = 1;
		$Planet = 1;

		while (!$newpos_checked)
		{
			for ($Galaxy = $LastSettedGalaxyPos; $Galaxy <= MAX_GALAXY_IN_WORLD; $Galaxy++)
			{
				for ($System = $LastSettedSystemPos; $System <= MAX_SYSTEM_IN_GALAXY; $System++)
				{
					for ($Posit = $LastSettedPlanetPos; $Posit <= 4; $Posit++)
					{
						$Planet = round (rand ( 4, 12) );

						switch ($LastSettedPlanetPos) {
							case 1:
								$LastSettedPlanetPos += 1;
								break;
							case 2:
								$LastSettedPlanetPos += 1;
								break;
							case 3:
								if ($LastSettedSystemPos == MAX_SYSTEM_IN_GALAXY)
								{
									$LastSettedGalaxyPos += 1;
									$LastSettedSystemPos  = 1;
									$LastSettedPlanetPos  = 1;
									break;
								}
								else
								{
									$LastSettedPlanetPos  = 1;
								}
								$LastSettedSystemPos += 1;
								break;
						}
						break;
					}
					break;
				}
				break;
			}

			$QrySelectGalaxy  = "SELECT `id` FROM {{table}} ";
			$QrySelectGalaxy .= "WHERE ";
			$QrySelectGalaxy .= "`galaxy` = '". $Galaxy ."' AND ";
			$QrySelectGalaxy .= "`system` = '". $System ."' AND ";
			$QrySelectGalaxy .= "`planet` = '". $Planet ."' ";
			$QrySelectGalaxy .= "LIMIT 1;";
			$GalaxyRow = db::query( $QrySelectGalaxy, 'planets', true);

			if (!isset($GalaxyRow['id'])) {
				system::CreateOnePlanetRecord ($Galaxy, $System, $Planet, $user_id, 'Главная планета', true);
				$newpos_checked = true;
			}

			if ($newpos_checked) {
				system::UpdateConfig('LastSettedGalaxyPos', $LastSettedGalaxyPos);
				system::UpdateConfig('LastSettedSystemPos', $LastSettedSystemPos);
				system::UpdateConfig('LastSettedPlanetPos', $LastSettedPlanetPos);
			}
		}

		$PlanetID = db::query("SELECT `id` FROM {{table}} WHERE `id_owner` = '". $user_id ."' LIMIT 1;", 'planets', true);

		$QryUpdateUser  = "UPDATE {{table}} SET ";
		$QryUpdateUser .= "`id_planet` = '". $PlanetID['id'] ."', ";
		$QryUpdateUser .= "`current_planet` = '". $PlanetID['id'] ."', ";
		$QryUpdateUser .= "`galaxy` = '". $Galaxy ."', ";
		$QryUpdateUser .= "`system` = '". $System ."', ";
		$QryUpdateUser .= "`planet` = '". $Planet ."' ";
		$QryUpdateUser .= "WHERE ";
		$QryUpdateUser .= "`id` = '". $user_id ."' ";
		$QryUpdateUser .= "LIMIT 1;";
		db::query( $QryUpdateUser, 'users');
	}

	static function UpdateConfig ($key, $value)
	{
		db::query("UPDATE {{table}} SET `config_value` = '". $value ."' WHERE `config_name` = '".$key."';", 'config');
	}

	static function ClearConfigCache ()
	{
		$f = fopen("includes/config.txt", "r+");
		ftruncate($f, 0);
		fclose($f);
	}

	static function CreateConfigCache ()
	{
		$query = db::query("SELECT * FROM {{table}}", 'config');

		$g_c = array();

		while ($row = db::fetch_assoc($query))
		{
			$g_c[$row['config_name']] = $row['config_value'];
		}

		$result = json_encode($g_c);

		$fp = fopen("includes/config.txt", "w");
		flock($fp, LOCK_EX);
		fwrite($fp, $result);
		flock($fp,LOCK_UN);
		fclose($fp);

		return $result;
	}

    static function FormatText ($text)
   	{
   		$text = htmlspecialchars(str_replace("'", '&#39;', $text));
   		$text = trim ( nl2br ( strip_tags ( $text, '<br>' ) ) );
   		$text = str_replace(array("\r\n", "\n", "\r"), '', $text);

   		return $text;
   	}

    static function Redirect ($url)
   	{
   		header("Location: ".$url.((isset($_GET['ajax'])) ? '&ajax' : ''));
   		die();
   	}
}
 
?>