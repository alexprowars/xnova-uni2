<?php

if(!defined("INSIDE")) die("attemp hacking");

if ($user->data['authlevel'] >= 1) {
	system::includeLang('admin');

	$mode      = @$_POST['modes'];

	if ($mode == 'banit') {
		$name              = htmlspecialchars($_POST['name']);
		$reas              = htmlspecialchars($_POST['why']);
		$days              = intval($_POST['days']);
		$hour              = intval($_POST['hour']);
		$mins              = intval($_POST['mins']);
		
		$userz = db::query("SELECT id FROM {{table}} WHERE username = '".$name."';", "users", true);
		if (!isset($userz['id']))
			message ($lang['sys_noalloaw'], 'Игрок не найден');

		$Now               = time();
		$BanTime           = $days * 86400;
		$BanTime          += $hour * 3600;
		$BanTime          += $mins * 60;
		$BannedUntil       = $Now + $BanTime;

		$QryInsertBan      = "INSERT INTO {{table}} SET ";
		$QryInsertBan     .= "`who` = \"". $userz['id'] ."\", ";
		$QryInsertBan     .= "`theme` = '". $reas ."', ";
		$QryInsertBan     .= "`time` = '". $Now ."', ";
		$QryInsertBan     .= "`longer` = '". $BannedUntil ."', ";
		$QryInsertBan     .= "`author` = '". $user->data['id'] ."';";
		db::query( $QryInsertBan, 'banned');

		$QryUpdateUser     = "UPDATE {{table}} SET ";
		$QryUpdateUser    .= "`banaday` = '". $BannedUntil ."' ";
		
		if (isset($_POST['ro']) && $_POST['ro'] == "1") {
			$QryUpdateUser    .= ", `urlaubs_modus_time` = '1' ";
			db::query("UPDATE {{table}} SET `metal_mine_porcent` = '0', `crystal_mine_porcent` = '0', `deuterium_sintetizer_porcent` = '0', `solar_plant_porcent` = '0', `fusion_plant_porcent` = '0', `solar_satelit_porcent` = '0' WHERE `id_owner` = '".$userz['id']."'", "planets");
		}
		
		$QryUpdateUser    .= "WHERE ";
		$QryUpdateUser    .= "`username` = \"". $name ."\";";
		db::query( $QryUpdateUser, 'users');

		$DoneMessage       = $lang['adm_bn_thpl'] ." ". $name ." ". $lang['adm_bn_isbn'];
		message ($DoneMessage, $lang['adm_bn_ttle']);
	}

	$Display->addTemplate('banned', 'admin/banned.php');

	display( '', $lang['adm_bn_ttle'], false, true);
} else {
	message ($lang['sys_noalloaw'], $lang['sys_noaccess']);
}
?>
