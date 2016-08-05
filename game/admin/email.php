<?php

if(!defined("INSIDE")) die("attemp hacking");

if ($user->data['authlevel'] >= 3) {

	if (isset($_GET['u']) && isset($_GET['email'])) {
		$email = db::query("SELECT user_id FROM {{table}} WHERE user_id = ".intval($_GET['u'])." AND email = '".addslashes($_GET['email'])."' AND ok = 0;", "log_email", true);

		if (isset($email['user_id'])) {
			db::query("UPDATE {{table}} SET email = '".addslashes($_GET['email'])."' WHERE id = ".intval($_GET['u']).";", "users_inf");
			db::query("UPDATE {{table}} SET ok = 1 WHERE user_id = ".intval($_GET['u'])." AND email = '".addslashes($_GET['email'])."' AND ok = 0;", "log_email");
		}
	}

    $planetes = '';
	$query = db::query("SELECT e.*, u.username FROM {{table}}log_email e LEFT JOIN {{table}}users u ON u.id = e.user_id WHERE ok = 0", "");
	$i = 0;
	while ($u = db::fetch_assoc($query)) {
		$planetes .= "<tr>"
		. "<td class=b><center><b>" . $u['username'] . "</center></b></td>"
		. "<td class=b><center><b>" . datezone("d.m H:i", $u['time']) . "</center></b></td>"
		. "<td class=b><center><b>" . $u['email'] . "</center></b></td>"
		. "<td class=b><center><a href=\"?set=admin&mode=email&u=".$u['user_id']."&email=".$u['email']."\">сменить</a></center></td>"
		. "</tr>";
		$i++;
	}

    $Display->addTemplate('emaillist', 'admin/email.php');
    $Display->assign('planetes', $planetes, 'emaillist');

	display('', 'Список email', false, true);
} else {
	message($lang['sys_noalloaw'], $lang['sys_noaccess']);
}
?>