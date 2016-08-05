<?php

if(!defined("INSIDE")) die("attemp hacking");

system::includeLang('admin/settings');

if ($user->data['authlevel'] >= 3)
{
	if (isset($_POST['opt_save']))
	{
		if (isset($_POST['LastSettedGalaxyPos']) && is_numeric($_POST['LastSettedGalaxyPos'])) {
			system::UpdateConfig('LastSettedGalaxyPos', intval($_POST['LastSettedGalaxyPos']));
		}
		if (isset($_POST['LastSettedSystemPos']) && is_numeric($_POST['LastSettedSystemPos'])) {
			system::UpdateConfig('LastSettedSystemPos', intval($_POST['LastSettedSystemPos']));
		}

		message ('Настройки игры успешно сохранены!', 'Выполнено', '?');
	}
	else
	{
		$parse                           = array();

		$parse['LastSettedGalaxyPos']    = $game_config['LastSettedGalaxyPos'];
		$parse['LastSettedSystemPos']    = $game_config['LastSettedSystemPos'];

		$Display->addTemplate('settings', 'admin/options.php');
		$Display->assign('parse', $parse, 'settings');

		display ( '', $lang['adm_opt_title'], false, true);
	}
}
else
{
	message ( $lang['sys_noalloaw'], $lang['sys_noaccess'] );
}


?>
