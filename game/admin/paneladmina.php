<?php

if(!defined("INSIDE")) die("attemp hacking");

if ($user->data['authlevel'] >= "1") {
	system::includeLang('admin/adminpanel');

	$Display->addTemplate('main', 'admin/adminpanel.php');

	if (isset($_POST['result']) || isset($_GET['result'])) {

		$result = (isset($_GET['result'])) ? $_GET['result'] : $_POST['result'];

		switch ($result){
			case 'usr_data':

				if ($user->data['authlevel'] >= "2") {

                    $Pattern = (isset($_GET['player'])) ? addslashes($_GET['player']) : addslashes($_POST['player']);
                    $SelUser = db::query("SELECT u.*, ui.* FROM {{table}}users u, {{table}}users_inf ui WHERE ui.id = u.id AND u.`username` = '". $Pattern ."' LIMIT 1;", '', true);

					if (!isset($SelUser['id']))
						message('Такого игрока не существует', 'Ошибка', '?set=admin&mode=paneladmina', 2);

                    $bloc                    = $lang;
                    $bloc['answer1']         = $SelUser['id'];
                    $bloc['answer2']         = $SelUser['username'];
                    $bloc['answer3']         = long2ip($SelUser['user_lastip']);
                    $bloc['answer4']         = $SelUser['email'];
                    $bloc['answer5']         = $lang['adm_usr_level'][ $SelUser['authlevel'] ];
                    $bloc['answer6']         = $lang['adm_usr_genre'][ $SelUser['sex'] ];
                    $bloc['answer8']         = "[".$SelUser['galaxy'].":".$SelUser['system'].":".$SelUser['planet']."] ";

                    $bloc['adm_sub_form2'] 	 = "<table width=100%><tr><td colspan=\"4\" class=\"c\">Планеты игрока</td></tr>";
                    $UsrColo = db::query("SELECT * FROM {{table}} WHERE `id_owner` = '". $SelUser['id'] ." ORDER BY id ASC';", 'planets');
                    while ( $Colo = db::fetch_assoc($UsrColo) ) {
						$bloc['adm_sub_form2'] .= "<tr><th>".$Colo['id']."</th>";
						$bloc['adm_sub_form2'] .= "<th>";
						if($Colo['planet_type'] == 1) {
							if ($SelUser['id_planet'] == $Colo['id'])
								$bloc['adm_sub_form2'] .= 'Главная';
							else
								$bloc['adm_sub_form2'] .= $lang['adm_planet'];
						} else {
							if($Colo['planet_type'] == 1)
								$bloc['adm_sub_form2'] .= $lang['adm_moon'];
							else
								$bloc['adm_sub_form2'] .= "Военная база";
						}
						$bloc['adm_sub_form2'] .= "</th><th>[".$Colo['galaxy'].":".$Colo['system'].":".$Colo['planet']."]</th>";
						$bloc['adm_sub_form2'] .= "<th>".$Colo['name']."</th></tr>";
                    }
                    $bloc['adm_sub_form2'] .= "</table>";

                    $bloc['adm_sub_form3']  = "<table width=100%><tr><td colspan=\"4\" class=\"c\">".$lang['adm_technos']."</td></tr>";
					foreach ($reslist['tech'] AS $Item) {
                        if (isset($resource[$Item])) {
                            $bloc['adm_sub_form3'] .= "<tr><th>".$lang['tech'][$Item]."</th>";
                            $bloc['adm_sub_form3'] .= "<th>".$SelUser[$resource[$Item]]."</th></tr>";
                        }
                    }
                    $bloc['adm_sub_form3'] .= "</table>";

					$logs = db::query("SELECT ip, time FROM {{table}} WHERE id = ".$SelUser['id']." ORDER BY time DESC", "log_ip");

					$bloc['adm_sub_form4']  = "<table width=100%><tr><td colspan=\"4\" class=\"c\">Смены IP</td></tr>";
					while ($log = db::fetch_assoc($logs)) {
                    	$bloc['adm_sub_form4'] .= "<tr><th>".long2ip($log['ip'])."</th>";
						$bloc['adm_sub_form4'] .= "<th>".datezone("d.m.Y H:i", $log['time'])."</th></tr>";
                    }
                    $bloc['adm_sub_form4'] .= "</table>";

					$logs_lang = array('', 'WMR', 'Ресурсы', 'Реферал', 'Уровень', 'Офицер');

					$logs = db::query("SELECT time, credits, type FROM {{table}} WHERE uid = ".$SelUser['id']." ORDER BY time DESC", "log_credits");

					$bloc['adm_sub_form4']  .= "<table width=100%><tr><td colspan=\"4\" class=\"c\">Кредитная история</td></tr>";
					while ($log = db::fetch_assoc($logs)) {
                    	$bloc['adm_sub_form4'] .= "<tr><th width=40%>".datezone("d.m.Y H:i", $log['time'])."</th>";
						$bloc['adm_sub_form4'] .= "<th>".$log['credits']."</th>";
						$bloc['adm_sub_form4'] .= "<th width=40%>".$logs_lang[$log['type']]."</th></tr>";
                    }
                    $bloc['adm_sub_form4'] .= "</table>";

					$logs = db::query("SELECT time, planet_start, planet_end, fleet, battle_log FROM {{table}} WHERE uid = ".$SelUser['id']." ORDER BY time DESC", "log_attack");

					$bloc['adm_sub_form4']  .= "<table width=100%><tr><td colspan=\"4\" class=\"c\">Логи атак</td></tr>";
					while ($log = db::fetch_assoc($logs)) {
                    	$bloc['adm_sub_form4'] .= "<tr><th width=40%>".datezone("d.m.Y H:i", $log['time'])."</th>";
						$bloc['adm_sub_form4'] .= "<th>S:".$log['planet_start']."</th>";
						$bloc['adm_sub_form4'] .= "<th width=30%>E:".$log['planet_end']."</th></tr>";

						$bloc['adm_sub_form4'] .= "<tr><th colspan=3><a href=\"?set=rw&r=".$log['battle_log']."&amp;k=".md5('xnovasuka'.$log['battle_log'])."\" target=\"_blank\">".$log['fleet']."</a></tr>";
                    }
                    $bloc['adm_sub_form4'] .= "</table>";

					$logs = db::query("SELECT ip FROM {{table}} WHERE id = ".$SelUser['id']." GROUP BY ip", "log_ip");

					$bloc['adm_sub_form5']  = "<table width=100%><tr><td colspan=\"3\" class=\"c\">Пересечения по IP</td></tr>";
					while ($log = db::fetch_assoc($logs)) {
						$ips = db::query("SELECT u.id, u.username, l.time FROM {{table}}log_ip l LEFT JOIN {{table}}users u ON u.id = l.id WHERE l.ip = ".$log['ip']." AND l.id != ".$SelUser['id']." GROUP BY l.id;", "");

						while ($ip = db::fetch_assoc($ips)) {
							$bloc['adm_sub_form5'] .= "<tr><th width=40%>".datezone("d.m.Y H:i", $ip['time'])."</th>";
							$bloc['adm_sub_form5'] .= "<th>".long2ip($log['ip'])."</th>";
							$bloc['adm_sub_form5'] .= "<th width=30%><a href='?set=players&id=".$ip['id']."' target='_blank'>".$ip['username']."</a></th></tr>";
						}
                   }
                    $bloc['adm_sub_form5'] .= "</table>";

					$logs = db::query("SELECT u_id, a_id, text, time FROM {{table}} WHERE u_id = ".$SelUser['id']." ORDER BY time DESC", "private");

					$bloc['adm_sub_form5']  .= "<table width=100%><tr><td colspan=\"3\" class=\"c\">Записи в личном деле</td></tr>";
					while ($log = db::fetch_assoc($logs)) {
						$bloc['adm_sub_form5'] .= "<tr><th width=25%>".datezone("d.m.Y H:i", $log['time'])."</th>";
						$bloc['adm_sub_form5'] .= "<th width=20%><a href='?set=players&id=".$log['a_id']."' target='_blank'>".$log['a_id']."</a></th>";
						$bloc['adm_sub_form5'] .= "<th>".$log['text']."</th></tr>";
                   }
                    $bloc['adm_sub_form5'] .= "</table>";

                    $Display->addTemplate('ans', 'admin/adminpanel_ans1.php');
                    $Display->assign('parse', $bloc, 'ans');

				}

				break;

			case 'usr_level':
				if ($user->data['authlevel'] >= "3") {
			
				$Player     = addslashes($_POST['player']);
				$NewLvl     = intval($_POST['authlvl']);

				$QryUpdate  = db::query("UPDATE {{table}} SET `authlevel` = '".$NewLvl."' WHERE `username` = '".$Player."';", 'users');
				$Message    = $lang['adm_mess_lvl1']. " ". $Player ." ".$lang['adm_mess_lvl2'];
				$Message   .= "<font color=\"red\">".$lang['adm_usr_level'][ $NewLvl ]."</font>!";

				message ( $Message, $lang['adm_mod_level'] );

				}
				break;

			case 'ip_search':
				$Pattern    = addslashes($_POST['ip']);
				$SelUser    = db::query("SELECT * FROM {{table}} WHERE `user_lastip` = INET_ATON('". $Pattern ."');", 'users');
				$bloc                   = $lang;
				$bloc['adm_this_ip']    = $Pattern;
				while ( $Usr = db::fetch_assoc($SelUser) ) {
					$UsrMain = db::query("SELECT `name` FROM {{table}} WHERE `id` = '". $Usr['id_planet'] ."';", 'planets', true);
					$bloc['adm_plyer_lst'] .= "<tr><th>".$Usr['username']."</th><th>[".$Usr['galaxy'].":".$Usr['system'].":".$Usr['planet']."] ".$UsrMain['name']."</th></tr>";
				}
                $Display->addTemplate('ans', 'admin/adminpanel_ans2.php');
                $Display->assign('parse', $bloc, 'ans');
				break;
			default:
				break;
		}
	}

	if (isset($_GET['action'])) {
		$bloc                   = $lang;
		switch ($_GET['action']){
			case 'usr_data':
				if ($user->data['authlevel'] >= "2") {
                    $Display->addTemplate('form', 'admin/adminpanel_f4.php');
                    $Display->assign('parse', $bloc, 'form');
				}else {
					 message( $lang['sys_noalloaw'], $lang['sys_noaccess'] );
				}
				break;

			case 'usr_level':
				if ($user->data['authlevel'] >= "3") {
                    $bloc['adm_level_lst'] = '';
                    for ($Lvl = 0; $Lvl < 4; $Lvl++) {
                                $bloc['adm_level_lst'] .= "<option value=\"". $Lvl ."\">". $lang['adm_usr_level'][ $Lvl ] ."</option>";
                    }
                    $Display->addTemplate('form', 'admin/adminpanel_f3.php');
                    $Display->assign('parse', $bloc, 'form');
				}
				 else 
				{
					 message( $lang['sys_noalloaw'], $lang['sys_noaccess'] );
				}
				break;

			case 'ip_search':
                $Display->addTemplate('form', 'admin/adminpanel_f2.php');
                $Display->assign('parse', $bloc, 'form');
				break;

			default:
				break;
		}
	}

	display( '', $lang['panel_mainttl'], false, true);
} else {
	message( $lang['sys_noalloaw'], $lang['sys_noaccess'] );
}
?>
