<?php

if(!defined("INSIDE")) die("attemp hacking");

if ($user->data['authlevel'] >= "2") {

	$parse = $lang;
    $parse['moon'] = '';
	$query = db::query("SELECT * FROM {{table}} WHERE planet_type='3'", "planets");
	$i = 0;
	while ($u = db::fetch_array($query)) {
		$parse['moon'] .= "<tr>"
		. "<td class=b><center><b>" . $u[0] . "</center></b></td>"
		. "<td class=b><center><b>" . $u[1] . "</center></b></td>"
		. "<td class=b><center><b>" . $u[2] . "</center></b></td>"
		. "<td class=b><center><b>" . $u[4] . "</center></b></td>"
		. "<td class=b><center><b>" . $u[5] . "</center></b></td>"
		. "<td class=b><center><b>" . $u[6] . "</center></b></td>"
		. "</tr>";
		$i++;
	}

	if ($i == "1")
		$parse['moon'] .= "<tr><th class=b colspan=6>В игре одна луна</th></tr>";
	else
		$parse['moon'] .= "<tr><th class=b colspan=6>В игре {$i} лун</th></tr>";

    $Display->addTemplate('moonlist', 'admin/moonlist.php');
    $Display->assign('moon', $parse['moon'], 'moonlist');

	display('', 'Lunalist' , false, true);
} else {
	message( $lang['sys_noalloaw'], $lang['sys_noaccess'] );
}
?>
