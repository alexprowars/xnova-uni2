<?php

if(!defined("INSIDE")) die("attemp hacking");

if ($user->data['authlevel'] >= "2") {

    $planetes = '';
	$query = db::query("SELECT `id`, `name`, `galaxy`, `system`, `planet` FROM {{table}} WHERE planet_type='1' ORDER by id", "planets");
	$i = 0;
	while ($u = db::fetch_array($query)) {
		$planetes .= "<tr>"
		. "<td class=b><center><b>" . $u[0] . "</center></b></td>"
		. "<td class=b><center><b>" . $u[1] . "</center></b></td>"
		. "<td class=b><center><b>" . $u[2] . "</center></b></td>"
		. "<td class=b><center><b>" . $u[3] . "</center></b></td>"
		. "<td class=b><center><b>" . $u[4] . "</center></b></td>"
		. "</tr>";
		$i++;
	}

	if ($i == "1")
		$planetes .= "<tr><th class=b colspan=5>В игре одна планета</th></tr>";
	else
		$planetes .= "<tr><th class=b colspan=5>В игре {$i} планеты</th></tr>";

    $Display->addTemplate('planetlist', 'admin/planetlist.php');
    $Display->assign('planetes', $planetes, 'planetlist');

	display('', 'Список планет', false, true);
} else {
	message($lang['sys_noalloaw'], $lang['sys_noaccess']);
}
?>
