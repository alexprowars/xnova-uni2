<?php

if(!defined("INSIDE")) die("attemp hacking");

if ($user->data['authlevel'] >= 1) {
	system::includeLang('admin');

	if (isset($_GET['cmd']) && $_GET['cmd'] == 'sort') {
		$TypeSort = $_GET['type'];
	} else {
		$TypeSort = "user_lastip";
	}

	$parse                      = $lang;
	$parse['adm_ov_data_yourv'] = colorRed(VERSION);
    $parse['adm_ov_data_table'] = array();

	$Last15Mins = db::query("SELECT `id`, `username`, `user_lastip`, `ally_name`, `onlinetime` FROM {{table}} WHERE `onlinetime` >= '". (time() - 15 * 60) ."' ORDER BY `". $TypeSort ."` ASC;", 'users');
	$Count      = 0;
	$Color      = "lime";
    $PrevIP     = '';
	while ( $TheUser = db::fetch_array($Last15Mins) ) {
		if ($PrevIP != "") {
			if ($PrevIP == $TheUser['user_lastip']) {
				$Color = "red";
			} else {
				$Color = "lime";
			}
		}

		$PrevIP = $TheUser['user_lastip'];

		$Bloc['adm_ov_altpm']        = $lang['adm_ov_altpm'];
		$Bloc['adm_ov_wrtpm']        = $lang['adm_ov_wrtpm'];
		$Bloc['adm_ov_data_id']      = $TheUser['id'];
		$Bloc['adm_ov_data_name']    = $TheUser['username'];
		$Bloc['adm_ov_data_clip']    = $Color;
		$Bloc['adm_ov_data_adip']    = long2ip($TheUser['user_lastip']);
		$Bloc['adm_ov_data_ally']    = $TheUser['ally_name'];
		$Bloc['adm_ov_data_activ']   = pretty_time ( time() - $TheUser['onlinetime'] );
		$Bloc['adm_ov_data_pict']    = "m.gif";

		$parse['adm_ov_data_table'][] = $Bloc;
		$Count++;
	}

	$parse['adm_ov_data_count']  = $Count;

    $Display->addTemplate('overview', 'admin/overview.php');
    $Display->assign('parse', $parse, 'overview');

	display ( '', 'Активность на сервере', false, true);
} else {
	message( $lang['sys_noalloaw'], $lang['sys_noaccess'] );
}
?>