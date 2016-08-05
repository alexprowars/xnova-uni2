<?php

/**
 * @author AlexPro
 * @copyright 2008 - 2011 XNova Game Group
 * @var $Display HSTemplateDisplay
 * @var $user user
 * @var $lang array
 * @var $game_config array
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

if(!defined("INSIDE")) die("attemp hacking");

system::includeLang('reg');

if ($_POST) {
	$errors    = 0;
	$errorlist = "";

	$_POST['email'] = strip_tags($_POST['email']);
	if (!is_email($_POST['email'])) {
		$errorlist .= "\"" . $_POST['email'] . "\" " . $lang['error_mail'];
		$errors++;
	}

	$girilen = $_REQUEST["captcha"]; 
	if(!isset($_SESSION['captcha']) || ($_SESSION['captcha'] != $girilen && $_SESSION['captcha'] != "")){
		$errorlist .= $lang['error_captcha']; 
		$errors++;    
	}

	if (!$_POST['character']) {
		$errorlist .= $lang['error_character'];
		$errors++;
	}

	if (strlen($_POST['passwrd']) < 4) {
		$errorlist .= $lang['error_password'];
		$errors++;
	}

	if (!preg_match("/^[А-Яа-яЁёa-zA-Z0-9_\-\!\~\.@ ]+$/u", $_POST['character'])) {
		$errorlist .= $lang['error_charalpha'];
		$errors++;
	}

	if ($_POST['rgt'] != 'on' || $_POST['sogl'] != 'on') {
		$errorlist .= $lang['error_rgt'];
		$errors++;
	}

	$ExistUser = db::query("SELECT `username` FROM {{table}} WHERE `username` = '". db::escape_string($_POST['character']) ."' LIMIT 1;", 'users', true);
	if ($ExistUser) {
		$errorlist .= $lang['error_userexist'];
		$errors++;
	}

	$ExistMail = db::query("SELECT `email` FROM {{table}} WHERE `email` = '". db::escape_string($_POST['email']) ."' LIMIT 1;", 'users_inf', true);
	if ($ExistMail) {
		$errorlist .= $lang['error_emailexist'];
		$errors++;
	}

	if ($_POST['sex'] != 'F' && $_POST['sex'] != 'M') {
		$errorlist .= $lang['error_sex'];
		$errors++;
	}

	if ($errors != 0) {
		message ($errorlist, $lang['Register']);
	} else {
		$newpass        = $_POST['passwrd'];
		$UserName       = $_POST['character'];
		$UserEmail      = $_POST['email'];

		$md5newpass     = md5($newpass);

		$sex = ($_POST['sex'] == 'F') ? 2 : 1;

		$QryInsertUser  = "INSERT INTO {{table}} SET ";
		$QryInsertUser .= "`username` = '". db::escape_string(strip_tags( $UserName )) ."', ";
		$QryInsertUser .= "`sex` = '".$sex."', ";
		$QryInsertUser .= "`id_planet` = '0', ";
		$QryInsertUser .= "`user_lastip` = '". $_SERVER['HTTP_X_REAL_IP'] ."', ";
		$QryInsertUser .= "`bonus` = ".time()." , ";
		$QryInsertUser .= "`onlinetime` = '". time() ."';";
		db::query( $QryInsertUser, 'users');

		$iduser         = db::insert_id();

		$QryInsertUser  = "INSERT INTO {{table}} SET ";
		$QryInsertUser .= "`id` = '".    $iduser            ."', ";
		$QryInsertUser .= "`email` = '".    db::escape_string($UserEmail )            ."', ";
		$QryInsertUser .= "`register_time` = '". time() ."', ";
		$QryInsertUser .= "`password`='". $md5newpass ."';";
		db::query( $QryInsertUser, 'users_inf');

		if (isset($_SESSION['ref'])){
			$refe = db::query("SELECT id FROM {{table}} WHERE id = ".$_SESSION['ref']."", 'users', true);
			if ($refe['id'] > 0) {
				db::query("INSERT INTO {{table}} VALUES (".$iduser.", ".$_SESSION['ref'].")", 'refs');
			}
		}

		system::CreateRegPlanet($iduser);
		system::UpdateConfig('users_amount', ($game_config['users_amount'] + 1));
		system::ClearConfigCache();

		$mail = new mail();
		$mail->SetFrom(ADMINEMAIL, 'XNova Game');
		$mail->AddAddress($UserEmail, $UserName);
		$mail->IsHTML(true);
		$mail->Subject = "Регистрация в игре XNova";
		$mail->Body    = "Вы успешно зарегистрировались в игре XNova.<br>Ваши данные для входа в игру:<br>Email: ".$UserEmail."<br>Пароль:".$newpass."";
		$mail->Send();

		$passw_string = md5("".$md5newpass."---NOIPSECURiTy---".$iduser."");
		
		setcookie("x_id", 		$iduser, 		0, "/", $_SERVER["SERVER_NAME"], 0);
		setcookie("x_secret", 	$passw_string, 	0, "/", $_SERVER["SERVER_NAME"], 0);
			
        system::Redirect("?set=overview");
	}
} else {

    $Display->addTemplate('reg', 'reg.php');

	display('', $lang['registry'], false, false);
}

?>
