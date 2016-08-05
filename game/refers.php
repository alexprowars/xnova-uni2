<?php

/**
 * @author AlexPro
 * @copyright 2008 - 2011 XNova Game Group
 * @var $Display HSTemplateDisplay
 * @var $user user
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

$refers = db::query("SELECT u.id, u.username, u.lvl_minier, u.lvl_raid, ui.register_time FROM {{table}}refs r LEFT JOIN {{table}}users u ON u.id = r.r_id LEFT JOIN {{table}}users_inf ui ON ui.id = r.r_id WHERE r.u_id = ".$user->data['id']." ORDER BY u.id DESC;", "");

$parse['ref'] = array();

while ($refer = db::fetch_assoc($refers)) {
	$parse['ref'][] = $refer;
}

$refers = db::query("SELECT u.id, u.username FROM {{table}}refs r LEFT JOIN {{table}}users u ON u.id = r.u_id WHERE r.r_id = ".$user->data['id']."", "", true);

if (isset($refers['id']))
	$parse['you'] = $refers;

$Display->addTemplate('refers', 'refer.php');
$Display->assign('parse', $parse, 'refers');

display('', 'Рефералы', false);

 ?>
