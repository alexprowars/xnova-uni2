<?php

//error_reporting(E_ALL);

$maxinfos = array();

function SetMaxInfo($ID, $Count, $Data){
    global $maxinfos;

    if($Data['authlevel'] == 3 || $Data['banaday'] != 0)
        return;

    if(!isset($maxinfos[$ID]))
        $maxinfos[$ID] = array('maxlvl' => 0, 'username' => '');

    if($maxinfos[$ID]['maxlvl'] < $Count)
        $maxinfos[$ID] = array('maxlvl' => $Count, 'username' => $Data['username']);
}

define('INSIDE'  , true);
define('INSTALL' , false);

// Подключаем класс шаблонизатора
include("includes/class/class.HSTemplate.php");
// Создаём сласс шаблонизатора
$Template = new HSTemplate(array('template_path' => 'template', 'cache_path' => 'cache', 'debug' => false));
// Создаём объект шаблона центра игры
$Display = $Template->getDisplay('game');

include("includes/class/class.db.php");
db::init();

include("includes/functions_global.php");
include("includes/vars.php");
include("game/admin/statfunctions.php");

$StatDate   = time();

$Message = "";

$StatRace = array(
		1 => array('count' => 0, 'total' => 0, 'fleet' => 0, 'tech' => 0, 'defs' => 0, 'build' => 0),
		2 => array('count' => 0, 'total' => 0, 'fleet' => 0, 'tech' => 0, 'defs' => 0, 'build' => 0),
		3 => array('count' => 0, 'total' => 0, 'fleet' => 0, 'tech' => 0, 'defs' => 0, 'build' => 0),
		4 => array('count' => 0, 'total' => 0, 'fleet' => 0, 'tech' => 0, 'defs' => 0, 'build' => 0),
);

// Включение режима удаления у неактивных игроков
$Del_TimeS 	= time()+86400*7; // 7 дней на удаление аккаунта
$Time_Online 	= time()-60*60*24*21; // удалять если не активен 21 день
// Удалять если не забанен и не в режиме отпуска
$Spr_Online = db::query("SELECT * FROM {{table}} WHERE `onlinetime` < '{$Time_Online}' AND `onlinetime` > '0' AND (`urlaubs_modus_time` = '0' OR (urlaubs_modus_time < ".time()." - 15184000 AND urlaubs_modus_time > 1)) AND `banaday` = '0' AND `deltime` = '0' ORDER BY onlinetime LIMIT 75", "users");
while ($OnlineS = db::fetch_assoc($Spr_Online)){
	db::query("UPDATE {{table}} SET `deltime` = '".$Del_TimeS."' WHERE `id` = '".$OnlineS['id']."'", "users");
	$Message .= "Включение удаления у ".$OnlineS['username'].": ОК<br>";
}

// Выбираем кандидатов на удаление
$Del_Time = time();
$Spr_Del = db::query("SELECT * FROM {{table}} WHERE `deltime` < '{$Del_Time}' AND `deltime`> '0'","users");

// Полное очищение игры от удалённого аккаунта
while ($TheUser = db::fetch_assoc($Spr_Del)){
	$UserID = $TheUser['id'];

	$Message .= "Удаление аккаунта ".$TheUser['username'].": ОК<br>";

	if ( $TheUser['ally_id'] != 0 ) {
		$TheAlly = db::query ( "SELECT * FROM {{table}} WHERE `id` = '" . $TheUser['ally_id'] . "';", 'alliance', true );
		$TheAlly['ally_members'] -= 1;
		if ( $TheAlly['ally_members'] > 0 && $TheAlly['ally_owner'] != $UserID ) {
			db::query ( "UPDATE {{table}} SET `ally_members` = '" . $TheAlly['ally_members'] . "' WHERE `id` = '" . $TheAlly['id'] . "';", 'alliance' );
			db::query ( "DELETE FROM {{table}} WHERE `u_id` = '" . $UserID . "';", 'alliance_members' );
		} else {
			if ($TheAlly['ally_members'] > 1) {
				db::query("UPDATE {{table}} SET `ally_id` = '0', `ally_name` = '' WHERE ally_id = '".$TheAlly['id']."' AND id != ".$UserID."", "users");
			}
			db::query ( "DELETE FROM {{table}} WHERE `id` = '" . $TheAlly['id'] . "';", 'alliance' );
			db::query ( "DELETE FROM {{table}} WHERE a_id = '".$TheAlly['id']."'", "alliance_members");
			db::query ( "DELETE FROM {{table}} WHERE a_id = '".$TheAlly['id']."'", "alliance_requests");
			db::query ( "DELETE FROM {{table}} WHERE a_id = '".$TheAlly['id']."' OR d_id = '".$TheAlly['id']."';", 'alliance_diplomacy' );
			db::query ( "DELETE FROM {{table}} WHERE `stat_type` = '2' AND `id_owner` = '" . $TheAlly['id'] . "';", 'statpoints' );
		}
	}

	db::query ( "DELETE FROM {{table}} WHERE `u_id` = '" . $UserID . "';", 'alliance_requests' );
	db::query ( "DELETE FROM {{table}} WHERE `stat_type` = '1' AND `id_owner` = '" . $UserID . "';", 'statpoints' );
	db::query ( "DELETE FROM {{table}} WHERE `id_owner` = '" . $UserID . "';", 'planets' );
	db::query ( "DELETE FROM {{table}} WHERE `message_sender` = '" . $UserID . "';", 'messages' );
	db::query ( "DELETE FROM {{table}} WHERE `message_owner` = '" . $UserID . "';", 'messages' );
	db::query ( "DELETE FROM {{table}} WHERE `owner` = '" . $UserID . "';", 'notes' );
	db::query ( "DELETE FROM {{table}} WHERE `fleet_owner` = '" . $UserID . "';", 'fleets' );
	db::query ( "DELETE FROM {{table}} WHERE `sender` = '" . $UserID . "';", 'buddy' );
	db::query ( "DELETE FROM {{table}} WHERE `owner` = '" . $UserID . "';", 'buddy' );
	db::query ( "DELETE FROM {{table}} WHERE `r_id` = '" . $UserID . "' OR `u_id` = '" . $UserID . "';", 'refs' );
	db::query ( "DELETE FROM {{table}} WHERE `id` = '" . $UserID . "';", 'users' );
	db::query ( "DELETE FROM {{table}} WHERE `id` = '" . $UserID . "';", 'users_inf' );
	db::query ( "DELETE FROM {{table}} WHERE `who` = '" . $UserID . "';", 'banned' );
	db::query ( "DELETE FROM {{table}} WHERE `uid` = '" . $UserID . "';", 'log_attack' );
	db::query ( "DELETE FROM {{table}} WHERE `uid` = '" . $UserID . "';", 'log_credits' );
	db::query ( "DELETE FROM {{table}} WHERE `id` = '" . $UserID . "';", 'log_ip' );
	db::query ( "DELETE FROM {{table}} WHERE `s_id` = '" . $UserID . "' || `e_id` = '" . $UserID . "';", 'logs' );
	//db::query("UPDATE {{table}} SET `config_value`=`config_value`-1 WHERE `config_name` = 'users_amount';", 'config');

}

// Чистим старьё
db::query ( "DELETE FROM {{table}} WHERE `stat_code` = '2';" , 'statpoints');
db::query ( "UPDATE {{table}} SET `stat_code` = `stat_code` + '1';" , 'statpoints');

$active_users 		= 0;
$active_alliance 	= 0;

// Делаем выборку игрока и его очков в статистике
$GameUsers  = db::query("SELECT u.*, ui.records, s.total_rank, s.tech_rank, s.fleet_rank, s.build_rank, s.defs_rank FROM ({{table}}users u, {{table}}users_inf ui) LEFT JOIN {{table}}statpoints s ON s.id_owner = u.id AND s.stat_type = 1 WHERE ui.id = u.id AND u.authlevel < 3 AND u.banaday = 0 AND (u.onlinetime > ui.register_time + 1800)", '');
// Удаляем статистику игроков
db::query ("DELETE FROM {{table}} WHERE `stat_type` = '1';",'statpoints');
// Делаем выборку флотов и расчитываем очки
$FleetPoints 	= array();
$UsrFleets 		= db::query("SELECT * FROM {{table}}", 'fleets');

while ($CurFleet = db::fetch_assoc($UsrFleets)) {
	$Points = GetFleetPointsOnTour ( $CurFleet['fleet_array'] );

	if (!isset($FleetPoints[$CurFleet['fleet_owner']])) {
		$FleetPoints[$CurFleet['fleet_owner']]			 = array();
		$FleetPoints[$CurFleet['fleet_owner']]['points'] = 0;
		$FleetPoints[$CurFleet['fleet_owner']]['count']  = 0;
		$FleetPoints[$CurFleet['fleet_owner']]['array']  = array();
	}

	$FleetPoints[$CurFleet['fleet_owner']]['points'] += ($Points['FleetPoint'] / 1000);
	$FleetPoints[$CurFleet['fleet_owner']]['count']  += $Points['FleetCount'];
	$FleetPoints[$CurFleet['fleet_owner']]['array'][] = $Points['fleet_array'];
}

// Просчитываем очки каждого игрока
while ($CurUser = db::fetch_assoc($GameUsers)) 
{
    if ($CurUser['banaday'] != 0 || ($CurUser['urlaubs_modus_time'] != 0 && $CurUser['urlaubs_modus_time'] < (time() - 1036800)))
        $hide = 1;
    else
        $hide = 0;

	if ($hide == 0)
		$active_users++;

	// Запоминаем старое место в стате
	if ($CurUser['total_rank'] != "") {
		$OldTotalRank = $CurUser['total_rank'];
		$OldTechRank  = $CurUser['tech_rank'];
		$OldFleetRank = $CurUser['fleet_rank'];
		$OldBuildRank = $CurUser['build_rank'];
		$OldDefsRank  = $CurUser['defs_rank'];
	} else {
		$OldTotalRank = 0;
		$OldTechRank  = 0;
		$OldBuildRank = 0;
		$OldDefsRank  = 0;
		$OldFleetRank = 0;
	}

	// Вычисляем очки исследований
	$Points         = GetTechnoPoints ( $CurUser );
	$TTechCount     = $Points['TechCount'];
	$TTechPoints    = ($Points['TechPoint'] / 1000);

	$TBuildCount    = 0;
	$TBuildPoints   = 0;
	$TDefsCount     = 0;
	$TDefsPoints    = 0;
	$TFleetCount    = 0;
	$TFleetPoints   = 0;
	$GCount         = $TTechCount;
	$GPoints        = $TTechPoints;
	$UsrPlanets     = db::query("SELECT * FROM {{table}} WHERE `id_owner` = '". $CurUser['id'] ."';", 'planets');

	$RecordArray = array();

	while ($CurPlanet = db::fetch_assoc($UsrPlanets) ) {
		$Points           = GetBuildPoints ( $CurPlanet, $CurUser );
		$TBuildCount     += $Points['BuildCount'];
		$GCount          += $Points['BuildCount'];
		$PlanetPoints     = ($Points['BuildPoint'] / 1000);
		$TBuildPoints    += ($Points['BuildPoint'] / 1000);

		$Points           = GetDefensePoints ( $CurPlanet, $RecordArray );
		$TDefsCount      += $Points['DefenseCount'];;
		$GCount          += $Points['DefenseCount'];
		$PlanetPoints    += ($Points['DefensePoint'] / 1000);
		$TDefsPoints     += ($Points['DefensePoint'] / 1000);

		$Points           = GetFleetPoints ( $CurPlanet, $RecordArray );
		$TFleetCount     += $Points['FleetCount'];
		$GCount          += $Points['FleetCount'];
		$PlanetPoints    += ($Points['FleetPoint'] / 1000);
		$TFleetPoints    += ($Points['FleetPoint'] / 1000);

		$GPoints         += $PlanetPoints;
	}

	// Складываем очки флота
	if ( isset($FleetPoints[$CurUser['id']]['points']) ) {
		$TFleetCount     += $FleetPoints[$CurUser['id']]['count'];
		$GCount          += $FleetPoints[$CurUser['id']]['count'];
		$TFleetPoints    += $FleetPoints[$CurUser['id']]['points'];
		$PlanetPoints     = $FleetPoints[$CurUser['id']]['points'];
		$GPoints         += $PlanetPoints;

		foreach ($FleetPoints[$CurUser['id']]['array'] AS $fleet) {
			foreach ($fleet AS $id => $amount) {
				if (isset($RecordArray[$id]))
					$RecordArray[$id] += $amount;
				else
					$RecordArray[$id]  = $amount;
			}
		}
	}

	if ($CurUser['records'] == 1) 
	{
		foreach ($RecordArray AS $id => $amount) 
		{
			SetMaxInfo($id, $amount, $CurUser);
		}
	}

	if ($CurUser['race'] != 0) 
	{
		$StatRace[$CurUser['race']]['count'] 	+= 1;
		$StatRace[$CurUser['race']]['total'] 	+= $GPoints;
		$StatRace[$CurUser['race']]['fleet'] 	+= $TFleetPoints;
		$StatRace[$CurUser['race']]['tech'] 	+= $TTechPoints;
		$StatRace[$CurUser['race']]['build'] 	+= $TBuildPoints;
		$StatRace[$CurUser['race']]['defs'] 	+= $TDefsPoints;
	}

	// Заносим данные в таблицу
	$QryInsertStats  = "INSERT INTO {{table}} SET ";
	$QryInsertStats .= "`id_owner` = '". $CurUser['id'] ."', ";
	$QryInsertStats .= "`username` = '". $CurUser['username'] ."', ";
	$QryInsertStats .= "`race` = '". $CurUser['race'] ."', ";
	$QryInsertStats .= "`id_ally` = '". $CurUser['ally_id'] ."', ";
	$QryInsertStats .= "`ally_name` = '". $CurUser['ally_name'] ."', ";
	$QryInsertStats .= "`stat_type` = '1', ";
	$QryInsertStats .= "`stat_code` = '1', ";
	$QryInsertStats .= "`tech_points` = '". $TTechPoints ."', ";
	$QryInsertStats .= "`tech_count` = '". $TTechCount ."', ";
	$QryInsertStats .= "`tech_old_rank` = '". $OldTechRank ."', ";
	$QryInsertStats .= "`build_points` = '". $TBuildPoints ."', ";
	$QryInsertStats .= "`build_count` = '". $TBuildCount ."', ";
	$QryInsertStats .= "`build_old_rank` = '". $OldBuildRank ."', ";
	$QryInsertStats .= "`defs_points` = '". $TDefsPoints ."', ";
	$QryInsertStats .= "`defs_count` = '". $TDefsCount ."', ";
	$QryInsertStats .= "`defs_old_rank` = '". $OldDefsRank ."', ";
	$QryInsertStats .= "`fleet_points` = '". $TFleetPoints ."', ";
	$QryInsertStats .= "`fleet_count` = '". $TFleetCount ."', ";
	$QryInsertStats .= "`fleet_old_rank` = '". $OldFleetRank ."', ";
	$QryInsertStats .= "`total_points` = '". $GPoints ."', ";
	$QryInsertStats .= "`total_count` = '". $GCount ."', ";
	$QryInsertStats .= "`total_old_rank` = '". $OldTotalRank ."', ";
	$QryInsertStats .= "`stat_hide` = '". $hide ."';";
	db::query ( $QryInsertStats , 'statpoints');
}



$qryResetRowNum     = 'SET @rownum=0;';
$qryFormat          = 'UPDATE {{table}} SET `%1$s_rank` = (SELECT @rownum:=@rownum+1) WHERE `stat_type` = %2$d AND `stat_code` = 1 AND stat_hide = 0 ORDER BY `%1$s_points` DESC, `id_owner` ASC;';
$rankNames          = array( 'tech', 'fleet', 'defs', 'build', 'total');

foreach($rankNames as $rankName)
{
    db::query ($qryResetRowNum, '');
    db::query (sprintf($qryFormat, $rankName, 1), 'statpoints');
}

$Message .= "Обновление статистики игроков: ОК<br>";

db::query("INSERT INTO {{table}}statpoints
      (`tech_points`, `tech_count`, `build_points`, `build_count`, `defs_points`, `defs_count`,
        `fleet_points`, `fleet_count`, `total_points`, `total_count`, `id_owner`, `id_ally`, `stat_type`, `stat_code`,
        `tech_old_rank`, `build_old_rank`, `defs_old_rank`, `fleet_old_rank`, `total_old_rank`
      )
      SELECT
        SUM(u.`tech_points`), SUM(u.`tech_count`), SUM(u.`build_points`), SUM(u.`build_count`), SUM(u.`defs_points`),
        SUM(u.`defs_count`), SUM(u.`fleet_points`), SUM(u.`fleet_count`), SUM(u.`total_points`), SUM(u.`total_count`),
        u.`id_ally`, 0, 2, 1,
        a.tech_rank, a.build_rank, a.defs_rank, a.fleet_rank, a.total_rank
      FROM {{table}}statpoints as u
        LEFT JOIN {{table}}statpoints as a ON a.id_owner = u.id_ally AND a.stat_code = 2 AND a.stat_type = 2
      WHERE u.`stat_type` = 1 AND u.stat_code = 1 AND u.id_ally<>0
      GROUP BY u.`id_ally`", "");

  db::query ("UPDATE {{table}} as new
      LEFT JOIN {{table}} as old ON old.id_owner = new.id_owner AND old.stat_code = 2 AND old.stat_type = 1
    SET
      new.tech_old_rank = old.tech_rank,
      new.build_old_rank = old.build_rank,
      new.defs_old_rank  = old.defs_rank ,
      new.fleet_old_rank = old.fleet_rank,
      new.total_old_rank = old.total_rank
    WHERE
      new.stat_type = 2 AND new.stat_code = 2;", 'statpoints' );

db::query ("DELETE FROM {{table}} WHERE `stat_code` = '2' OR `stat_code` = '3';",'statpoints');

foreach($rankNames as $rankName)
{
    db::query ($qryResetRowNum, '');
    db::query (sprintf($qryFormat, $rankName, 2), 'statpoints');
}

foreach($StatRace AS $race => $arr) 
{
	$QryInsertStats  = "INSERT INTO {{table}} SET ";
	$QryInsertStats .= "`race` = '". $race ."', ";
	$QryInsertStats .= "`stat_type` = '3', ";
	$QryInsertStats .= "`stat_code` = '1', ";
	$QryInsertStats .= "`tech_points` = '". $arr['tech'] ."', ";
	$QryInsertStats .= "`build_points` = '". $arr['build'] ."', ";
	$QryInsertStats .= "`defs_points` = '". $arr['defs'] ."', ";
	$QryInsertStats .= "`fleet_points` = '". $arr['fleet'] ."', ";
	$QryInsertStats .= "`total_count` = '". $arr['count'] ."', ";
	$QryInsertStats .= "`total_points` = '". $arr['total'] ."';";
	db::query ( $QryInsertStats , 'statpoints');
}

foreach($rankNames as $rankName)
{
    db::query ($qryResetRowNum, '');
    db::query (sprintf($qryFormat, $rankName, 3), 'statpoints');
}

db::query("OPTIMIZE TABLE {{table}}", "statpoints");

$al = db::query("SELECT COUNT(*) AS num FROM {{table}} WHERE `stat_type` = '2' AND `stat_hide` = 0;", "statpoints", true);

$active_alliance = $al['num'];

$Message .= "Обновление статистики альянсов: ОК<br>";

// Чистим старые логи
db::query ( "DELETE FROM {{table}} WHERE `message_time` <= '". (time() - 432000) ."';", 'messages');
db::query ( "DELETE FROM {{table}} WHERE `time` <= '". (time() - 172800) ."';", 'rw');
db::query ( "DELETE FROM {{table}} WHERE `time` <= '". (time() - 86400) ."';", 'moneys');
db::query ( "DELETE FROM {{table}} WHERE `timestamp` <= '". (time() - 604800) ."';", 'chat');
db::query ( "DELETE FROM {{table}} WHERE `time` <= '". (time() - 86400) ."';", 'lostpwd');
db::query ( "DELETE FROM {{table}} WHERE `time` <= '". (time() - 259200) ."';", 'logs');
db::query ( "DELETE FROM {{table}} WHERE `time` <= '". (time() - 604800) ."';", 'log_attack');
db::query ( "DELETE FROM {{table}} WHERE `time` <= '". (time() - 604800) ."';", 'log_credits');
db::query ( "DELETE FROM {{table}} WHERE `time` <= '". (time() - 604800) ."';", 'log_ip');

$Message .= "Удаление старых логов: ОК<br>";

db::query("UPDATE {{table}} SET config_value = ".time()." WHERE config_name = 'stat_update';", "config");
db::query("UPDATE {{table}} SET config_value = ".$active_users." WHERE config_name = 'active_users';", "config");
db::query("UPDATE {{table}} SET config_value = ".$active_alliance." WHERE config_name = 'active_alliance';", "config");

$f = fopen ("includes/config.txt", "w");
fclose($f);

$Message .= "Обновление конфигурации: ОК<br>";

$Elements	= array_merge($reslist['build'], $reslist['tech'], $reslist['fleet'], $reslist['defense']);

$array = "";
foreach ($Elements as $ElementID) {
	if ($ElementID != 407 && $ElementID != 408)
		$array	.= $ElementID." => array('username' => '".(isset($maxinfos[$ElementID]['username']) ? $maxinfos[$ElementID]['username'] : '-')."', 'maxlvl' => '".(isset($maxinfos[$ElementID]['maxlvl']) ? $maxinfos[$ElementID]['maxlvl'] : '-')."'),\n";
}
$file = "<?php \n//The File is created on ".date("d. M y H:i:s", time())."\n$"."RecordsArray = array(\n".$array."\n);\n?>";
file_put_contents("cache/CacheRecords.php", $file);

$Message .= "Обновление рекордов: ОК<br>";

echo $Message;

?>
