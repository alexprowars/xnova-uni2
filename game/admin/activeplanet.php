<?php

if(!defined("INSIDE")) die("attemp hacking");

if ($user->data['authlevel'] >= 1) {
	system::includeLang('admin');

    $online_list = '';

	$AllActivPlanet = db::query("SELECT `name`, `galaxy`, `system`, `planet`, `last_update` FROM {{table}} WHERE `last_update` >= '". (time()-15 * 60) ."' ORDER BY `id` ASC", 'planets');
	$Count          = 0;

	while ($ActivPlanet = db::fetch_assoc($AllActivPlanet)) {
		$online_list .= "<tr>";
		$online_list .= "<td class=b><center><b>". $ActivPlanet['name'] ."</b></center></td>";
		$online_list .= "<td class=b><center><b>[". $ActivPlanet['galaxy'] .":". $ActivPlanet['system'] .":". $ActivPlanet['planet'] ."]</b></center></td>";
		$online_list .= "<td class=b><center><b>". pretty_time(time() - $ActivPlanet['last_update']) . "</b></center></td>";
		$online_list .= "</tr>";
		$Count++;
	}
	$online_list .= "<tr>";
	$online_list .= "<th class=\"b\" colspan=\"4\">". $lang['adm_pl_they'] ." ". $Count ." ". $lang['adm_pl_apla'] ."</th>";
	$online_list .= "</tr>";

	$Display->addTemplate('activeplanet', 'admin/activeplanet.php');
    $Display->assign('online_list', $online_list, 'activeplanet');
    
	display( '', $lang['adm_pl_title'], false, true);
} else {
	message( $lang['sys_noalloaw'], $lang['sys_noaccess'] );
}
?>
