<?php

/**
 * @author AlexPro
 * @copyright 2008 - 2011 XNova Game Group
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

class session
{
	// Флаг прохождения авторизации
	public $IsUserChecked = false;
	// Массив данных игрока
	public $user;

	function CheckTheUser ()
	{
		$Result = $this->CheckCookies();

		if (isset($Result['id'])) {
			if (!isset($_GET['set']) || $_GET['set'] != 'banned') {
				if ($Result['banaday'] > time()) {
					die('Ваш аккаунт заблокирован. Срок окончания блокировки: '.datezone("d.m.Y H:i:s", $Result['banaday']).'<br>Для получения дополнительной информации зайдите <a href="?set=banned">сюда</a>');
				} elseif ($Result['banaday'] > 0 && $Result['banaday'] < time()) {
					db::query("DELETE FROM {{table}} WHERE `who` = '".$Result['id']."'", 'banned');
					db::query("UPDATE {{table}} SET`banaday` = '0' WHERE `id` = '".$Result['id']."'", "users");
					$Result['banaday'] = 0;
				}
			}
			$this->user = $Result;
		}
	}

	private function CheckCookies ()
	{
		global $UpdateFlyFleet, $set;

		$UserRow = array();

		if (!isset($_SESSION['uid']) && isset($_COOKIE['x_id']) && isset($_COOKIE['x_secret']))
		{
			$UserResult = db::query("SELECT u.*, ui.password, ui.security FROM {{table}}users u, {{table}}users_inf ui WHERE ui.id = u.id AND u.`id` = '".intval($_COOKIE['x_id'])."';", '');

			if (db::num_rows($UserResult) == 0)
				$this->ClearSession();

			$UserRow = db::fetch_assoc($UserResult);

			$password = ($UserRow['security'] == 1) ? md5("".$UserRow['password']."---".$_SERVER['HTTP_X_REAL_IP']."---xNoVasIlko".$UserRow['id']."") : md5("".$UserRow['password']."---NOIPSECURiTy---".$UserRow['id']."");

			if ($password != $_COOKIE['x_secret'])
				$this->ClearSession();

			$_SESSION['uid'] = $UserRow['id'];
			$_SESSION['unm'] = $UserRow['username'];

			$this->IsUserChecked = true;
		}
		elseif (isset($_SESSION['uid']))
		{
			if (!isset($_COOKIE['x_id']) && !isset($_COOKIE['x_secret']))
				$this->ClearSession();

			$UserRow = db::query("SELECT * FROM {{table}} WHERE `id` = '".intval($_SESSION['uid'])."';", 'users', true);

			if (!$UserRow['id'])
				$this->ClearSession();
			else
				$this->IsUserChecked = true;
		}

		if ($this->IsUserChecked == true) {
			if ($UpdateFlyFleet == true && $UserRow['onlinetime'] > (time() - 10))
				$UpdateFlyFleet = false;

			if ($UpdateFlyFleet == true || $UserRow['user_lastip'] != GetIP($_SERVER['HTTP_X_REAL_IP']) || ($set == "chat" && ($UserRow['onlinetime'] < time() - 120 || $UserRow['chat'] == 0)) || ($set != "chat" && $UserRow['chat'] > 0)) {

				$QryUpdateUser  = "UPDATE {{table}} SET `onlinetime` = '". time() ."' ";

				if ($UserRow['user_lastip'] != GetIP($_SERVER['HTTP_X_REAL_IP'])) {
					$QryUpdateUser .= ", `user_lastip` = INET_ATON('". $_SERVER['HTTP_X_REAL_IP'] ."') ";

					db::query("INSERT INTO {{table}} (id, time, ip) VALUES (".$UserRow['id'].", ".time().", INET_ATON('".$_SERVER['HTTP_X_REAL_IP']."'))", "log_ip");
				}

				if ($set == "chat" && $UserRow['chat'] == 0) {
					$QryUpdateUser .= ", `chat` = '1' ";
					$UserRow['chat'] = 1;
				} elseif ($set != "chat" && $UserRow['chat'] > 0)
					$QryUpdateUser .= ", `chat` = '0' ";

				$QryUpdateUser .= "WHERE `id` = '". $_SESSION['uid'] ."' LIMIT 1;";
				db::query( $QryUpdateUser, 'users');
			}
		}

		return $UserRow;
	}

	public function ClearSession()
	{
		session_destroy();

		setcookie("x_id", "", 0, "/", $_SERVER["SERVER_NAME"], 0);
		setcookie("x_secret", "", 0, "/", $_SERVER["SERVER_NAME"], 0);
		setcookie("uni", "", 0, "/", ".xnova.su", 0);

        system::Redirect("?set=login");
	}
}
 
 ?>
