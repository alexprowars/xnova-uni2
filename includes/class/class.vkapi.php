<?php

/**
 * VKAPI class for vk.com social network
 *
 * @package server API methods
 * @link http://vk.com/developers.php
 * @autor Oleg Illarionov
 * @version 1.0
 */
 
class vkapi {
	var $api_secret;
	var $app_id;
	var $api_url;
	
	public function vkapi ($app_id, $api_secret, $api_url = 'api.vk.com/api.php')
	{
		$this->app_id = $app_id;
		$this->api_secret = $api_secret;
		if (!strstr($api_url, 'http://')) $api_url = 'http://'.$api_url;
		$this->api_url = $api_url;
	}
	
	public function api ($method, $params = false)
	{
		if (!$params) $params = array(); 
		$params['api_id'] = $this->app_id;
		$params['v'] = '3.0';
		$params['method'] = $method;
		$params['timestamp'] = time();
		$params['format'] = 'json';
		$params['random'] = rand(0,10000);
		ksort($params);
		$sig = '';
		foreach($params as $k=>$v) {
			$sig .= $k.'='.$v;
		}
		$sig .= $this->api_secret;
		$params['sig'] = md5($sig);
		$query = $this->api_url.'?'.$this->params($params);
		$res = file_get_contents($query);
		return json_decode($res, true);
	}
	
	public function params ($params)
	{
		$pice = array();
		foreach($params as $k=>$v) {
			$pice[] = $k.'='.urlencode($v);
		}
		return implode('&',$pice);
	}

	public function RegisterUser ($VKID, $refer)
	{
		global $game_config;

		$NewPass = system::CreateRandomPassword();

		db::query("INSERT INTO {{table}} SET `username` = 'id".$VKID."', `sex` = '1', `id_planet` = '0', `user_lastip` = '". $_SERVER['HTTP_X_REAL_IP'] ."', `onlinetime` = '". time() ."';", 'users');

		$iduser = db::insert_id();

		db::query("INSERT INTO {{table}} SET `id` = '".$iduser."', `email` = 'id".$VKID."@vkontakte.ru', vk_reg_id = '".$VKID."', `register_time` = '".time()."', `password` = '".md5($NewPass)."', `password_vk` = '".$NewPass."';", 'users_inf');

		if ($refer != 0){
			$refe = db::query("SELECT id FROM {{table}} WHERE vk_reg_id = '".$refer."'", 'users_inf', true);
			if ($refe['id'] > 0) {
				db::query("INSERT INTO {{table}} (sender, owner, active) VALUES (".$iduser.", ".$refe['id'].", 1)", 'buddy');
			}
		}

		system::CreateRegPlanet($iduser);
		system::UpdateConfig('users_amount', ($game_config['users_amount'] + 1));
		system::ClearConfigCache();

		$passw_string = md5("".md5($NewPass)."---NOIPSECURiTy---".$iduser."");

		setcookie("x_id", 		$iduser, 		0, "/", $_SERVER["SERVER_NAME"], 0);
		setcookie("x_secret", 	$passw_string, 	0, "/", $_SERVER["SERVER_NAME"], 0);
		setcookie("vkid", 		$VKID, 			0, "/", $_SERVER["SERVER_NAME"], 0);

		session_destroy();
	}

	public function login()
	{
		if (md5($this->app_id."_".$_POST['viewer_id']."_".$this->api_secret) != $_POST['auth_key']) {
			message('Параметры авторизации являются некорректными!', 'Ошибка');
		} else {
			$Row = db::query("SELECT u.*, ui.password, ui.security FROM {{table}}users u, {{table}}users_inf ui WHERE ui.id = u.id AND ui.`vk_reg_id` = '".intval($_POST['viewer_id'])."';", '', true);

			if (!isset($Row['id'])) {
				if (isset($_POST['user_id']) && isset($_POST['group_id']) && isset($_POST['viewer_type']) && ($_POST['user_id'] != 0 && $_POST['group_id'] == 0 && $_POST['viewer_type'] == 1)) {
					$refer = intval($_POST['user_id']);
				} else {
					$refer = 0;
				}

				$this->RegisterUser(intval($_POST['viewer_id']), $refer);
			} else {
				$passw_string = ($Row['security'] == 1) ? md5("".$Row['password']."---".$_SERVER['HTTP_X_REAL_IP']."---xNoVasIlko".$Row['id']."") : md5("".$Row['password']."---NOIPSECURiTy---".$Row['id']."");

				setcookie("x_id", $Row['id'], (time() + 2419200), "/", $_SERVER["SERVER_NAME"], 0);
				setcookie("x_secret", $passw_string, (time() + 2419200), "/", $_SERVER["SERVER_NAME"], 0);

				setcookie("vkid", $_POST['viewer_id'], (time() + 2419200), "/", $_SERVER["SERVER_NAME"], 0);

				session_destroy();
			}
			echo '<center>Загрузка...</center><script>parent.location="?set=overview";</script>';
			die();
		}
	}
}
?>
