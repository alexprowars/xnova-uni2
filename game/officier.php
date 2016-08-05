<?php

/**
 * @author AlexPro
 * @copyright 2008 - 2011 XNova Game Group
 * @var $Display HSTemplateDisplay
 * @var $lang array
 * @var $user user
 * @var $reslist array
 * @var $resource array
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

if(!defined("INSIDE")) die("attemp hacking");

if ($user->data['urlaubs_modus_time'] > 0) {
	message("Нет доступа!");
}

system::includeLang('officier');

$now = time();

if (isset($_POST['buy'])) {

	$need_c = 0;
	$times = 0;
	if ($_POST['week'] != "") { $need_c = 20; $times = 604800;
	} elseif ($_POST['2week'] != "") { $need_c = 40; $times = 1209600;
	} elseif ($_POST['month'] != "") { $need_c = 80; $times = 2592000; }

	if ($need_c > 0 && $times > 0 && $user->data['credits'] >= $need_c) {
		$Selected = intval($_POST['buy']);
		//if ($Selected == 605) die('Услуга недоступна');
		if ( in_array($Selected, $reslist['officier']) ) {
			if ( $user->data[$resource[$Selected]] > $now ) {
				$user->data[$resource[$Selected]] = $user->data[$resource[$Selected]] + $times;
			} else {
				$user->data[$resource[$Selected]] = $now + $times;
			}
				$user->data['credits'] -= $need_c;

				$QryUpdateUser  = "UPDATE {{table}} SET ";
				$QryUpdateUser .= "`credits` = '".$user->data['credits']."', ";
				$QryUpdateUser .= "`".$resource[$Selected]."` = '".$user->data[$resource[$Selected]]."' ";
				$QryUpdateUser .= "WHERE ";
				$QryUpdateUser .= "`id` = '".$user->data['id']."';";
				db::query( $QryUpdateUser, 'users' );

				db::query("INSERT INTO {{table}} (uid, time, credits, type) VALUES (".$user->data['id'].", ".time().", ".($need_c * (-1)).", 5)", "log_credits");

				$Message = $lang['OffiRecrute'];
		} else
			$Message = "НУ ТЫ И ЧИТАК!!!!!!";
	} else
		$Message = $lang['NoPoints'];

	message($Message, $lang['Officier'], '?set=officier', 2);
} else {
	$parse['off_points']   = $lang['off_points'];
	$parse['alv_points']   = pretty_number($user->data['credits']);
	$parse['list']         = array();

	for ( $Officier = 601; $Officier <= 607; $Officier++ ) {
		$bloc['off_id']       = $Officier;
		$bloc['off_tx_lvl']   = $lang['ttle'][$Officier];
		if ($user->data[$resource[$Officier]] > time()) {
			$bloc['off_lvl'] = "<font color=\"#00ff00\">Нанят до : ".datezone("d.m.Y H:i", $user->data[$resource[$Officier]])."</font>";
			$bloc['off_link'] = "<font color=\"red\">Продлить</font>";
		} else {
			$bloc['off_lvl'] = "<font color=\"#ff0000\">Не оплачено</font>";
			$bloc['off_link'] = "<font color=\"red\">Нанять</font>";
		}
		$bloc['off_desc']     = $lang['Desc'][$Officier];

		$bloc['off_link'] .= "<br><br><input type=\"hidden\" name=\"buy\" value=\"".$Officier."\"><input type=\"submit\" name=\"week\" value=\"на неделю\"><br>Стоимость:&nbsp;<font color=\"lime\">20</font>&nbsp;кр.<br><input type=\"submit\" name=\"2week\" value=\"на 2 недели\"><br>Стоимость:&nbsp;<font color=\"lime\">40</font>&nbsp;кр.<br><input type=\"submit\" name=\"month\" value=\"на месяц\"><br>Стоимость:&nbsp;<font color=\"lime\">80</font>&nbsp;кр.";
		$parse['list'][] = $bloc;
	}

	$Display->addTemplate('officier', 'officier.php');
	$Display->assign('parse', $parse, 'officier');
}



display('', 'Офицеры', false);

?>
