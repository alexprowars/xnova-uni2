<?php

if(!defined("INSIDE")) die("attemp hacking");

if ($user->data['authlevel'] >= "2") {

	$mode = @$_GET['modes'];

	if ($mode != 'change') {
		$parse['Name'] = "Введите логин игрока";
	} elseif ($mode == 'change') {
		$nam = $_POST['nam'];
		
		$us = db::query("SELECT id, username, banaday, urlaubs_modus_time FROM {{table}} WHERE username = '".addslashes($nam)."';", "users", true);
		if ($us['id']) {
			db::query("DELETE FROM {{table}} WHERE who = '".$us['id']."'", 'banned');
			db::query("UPDATE {{table}} SET banaday = 0 WHERE username='".$us['username']."'", "users");
			if ($us['urlaubs_modus_time'] == 1)
				db::query("UPDATE {{table}} SET urlaubs_modus_time = 0 WHERE username='".$us['username']."'", "users");
				
			message("Игрок {$nam} разбанен!", 'Информация');
		} else
			message("Игрок {$nam} не найден!", 'Информация');
	}

    $Display->addTemplate('unbanned', 'admin/unbanned.php');

	display('', "Разбан", false, true);
} else {
	message( $lang['sys_noalloaw'], $lang['sys_noaccess'] );
}
?>
