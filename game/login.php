<?php

/**
 * @author AlexPro
 * @copyright 2008 - 2011 XNova Game Group
 * @var $Display HSTemplateDisplay
 * @var $game_config array
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

if(!defined("INSIDE")) die("attemp hacking");

if (isset($_POST['emails'])) {
	$login = db::query("SELECT u.id, ui.security, ui.password FROM {{table}}users u, {{table}}users_inf ui WHERE ui.id = u.id AND ui.`email` = '" . db::escape_string($_POST['emails']) . "' LIMIT 1", "", true);

	if (isset($login['id'])) {
		if ($login['password'] == md5($_POST['password'])) { 

			$expiretime = (isset($_POST["rememberme"])) ? (time() + 2419200) : 0;
			$passw_string = ($login['security'] == 1) ? md5("".$login['password']."---".$_SERVER['HTTP_X_REAL_IP']."---xNoVasIlko".$login['id']."") : md5("".$login['password']."---NOIPSECURiTy---".$login['id']."");

			setcookie("x_id", $login['id'], $expiretime, "/", $_SERVER["SERVER_NAME"], 0);
			setcookie("x_secret", $passw_string, $expiretime, "/", $_SERVER["SERVER_NAME"], 0);

			setcookie("uni", "uni2", $expiretime, "/", ".xnova.su", 0);

            system::Redirect("?set=overview");
		} else
			message('Неверное E-mail и/или пароль<br><br><a href=?set=login>Назад</a>', 'Ошибка', '', 0, false);
	} else
		message('Такого игрока не существует<br><br><a href=?set=login>Назад</a>', 'Ошибка', '', 0, false);
} else {

    $Display->addTemplate('login', 'login.php');

	$parse = array();
	$parse['online_users'] = $game_config['online'];
	$parse['users_amount'] = $game_config['users_amount'];

	$Display->assign('parse', $parse, 'login');

    unset($_GET['set']);

	display('', 'login', false, false);
}
?>
