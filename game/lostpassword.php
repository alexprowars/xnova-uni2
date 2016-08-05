<?php

/**
 * @author AlexPro
 * @copyright 2008 - 2011 XNova Game Group
 * @var $Display HSTemplateDisplay
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

if(!defined("INSIDE")) die("attemp hacking");

$step = @intval($_GET['step']);
$login = @addslashes($_POST['login']);

function sendnewpassword($id, $key){

	$Lost = db::query("SELECT * FROM {{table}} WHERE ks = '".$key."' AND u_id = '".$id."' AND time > ".time()."-3600 AND activ = 0 LIMIT 1;", 'lostpwd', true);

	if ($Lost['u_id'] != "")
		$Mail = db::query("SELECT u.username, ui.email FROM {{table}}users u, {{table}}users_inf ui WHERE ui.id = u.id AND u.id = '".$Lost['u_id']."'", '', true);
	else
		message('<font color=red size=3>Действие данной ссылки истекло, попробуйте пройти процедуру заново!</font>', 'Ошипко!!!', '', 0, false);

	if (!preg_match("/^[А-Яа-яЁёa-zA-Z0-9]+$/u", $key)) {
		message('Ошибка выборки E-mail адреса!', 'Ошипко!!!', '', 0, false);
		} elseif (empty($Mail['email'])) {
		message('Ошибка выборки E-mail адреса!', 'Ошипко!!!', '', 0, false);
	} else {
			$Caracters = "aazertyuiopqsdfghjklmwxcvbnAZERTYUIOPQSDFGHJKLMWXCVBN1234567890";

			$Count = strlen($Caracters);

			$NewPass = "";
			$Taille = 6;


			srand((double)microtime()*1000000);

			for ($i=0; $i<$Taille; $i++){

				$CaracterBoucle = rand(0,$Count-1);
				$NewPass=$NewPass.substr($Caracters,$CaracterBoucle,1);
			}

		$mailto = $Mail['email'];
		$headers="Content-Type: text/html; charset=windows-1251\r\n";
		$headers.="From: <alexprowars@gmail.com >\r\n";
		$headers.="X-Mailer: PHP mailer";		
		$body = "Ваш новый пароль от игрового аккаунта: ".$Mail['username'].": ".$NewPass;
		$body = convert_cyr_string (stripslashes($body),'w','w');

		mail($mailto, "Новый пароль в Xnova Game", $body, $headers);

		$NewPass2 = md5($NewPass);

		$QryPassChange = "UPDATE {{table}} SET ";
		$QryPassChange .= "`password` ='". $NewPass2 ."' ";
		$QryPassChange .= "WHERE `id`='". $id ."' LIMIT 1;";
		db::query( $QryPassChange, 'users_inf');
		db::query("DELETE FROM {{table}} WHERE u_id = '".$id."'", 'lostpwd');

		message('<font color=red size=3>Ваш новый пароль: '.$NewPass.'. Копия пароля отправлена на почтовый ящик!</font>', 'OK', '', 0, false);
	}
}


if (isset($_GET['id']) && $_GET['id'] != "" && $_GET['passw'] != "") {
	sendnewpassword(intval($_GET['id']), addslashes($_GET['passw']));
} else {
	if ($step == 1 or $step == 0) {

		$Display->addTemplate('pas', 'lostpassword.php');

		display('', 'Восстановление пароля', false, false);
        
	} else if ($step == 2) {
		if ($login != "") {
			$inf = db::query("SELECT u.id, u.username, ui.email FROM {{table}}users u, {{table}}users_inf ui WHERE ui.id = u.id AND u.username = '".$login."' LIMIT 1;", '', true);
	
			if ($inf['id'] != "") { 
				$ip = GetEnv("HTTP_X_REAL_IP");
	
				$key = md5($inf['id'].date("d-m-Y H:i:s", time())."ыыы");
				db::query("INSERT INTO {{table}} (u_id, ks, time, ip, activ) VALUES (".$inf['id'].",'".$key."',".time().", '".$ip."',0)", 'lostpwd');
				
				// Отправляем письмо
				$mailto = $inf['email'];
	
				$headers="Content-Type: text/html; charset=windows-1251\r\n";
				$headers.="From: <alexprowars@gmail.com >\r\n";
				$headers.="X-Mailer: PHP mailer";
				
				$body = "Доброго времени суток Вам!\nКто то с IP адреса ".$ip." запросил пароль к персонажу ".$inf['username']." в онлайн-игре Xnova.su.\nТак как в анкете у персонажа указан данный e-mail, то именно Вы получили это письмо.\n\n
				Для восстановления пароля перейдите по ссылке: <a href='http://uni2.xnova.su/?set=lostpassword&id=".$inf['id']."&passw=".$key."'>http://uni2.xnova.su/lostpassword.php?id=".$inf['id']."&passw=".$key."</a>";
				$body = convert_cyr_string (stripslashes($body),'w','w');
	
				$sucess = mail($mailto, "Восстановление забытого пароля", $body, $headers);

				message('Ссылка на восстановления пароля отправлена на ваш E-mail', 'OK', '', 0, false);
			}
			else { $step = 1; message('Персонаж не найден в базе', 'Ошипко', '', 0, false); }
		}
		else { $step = 1; message('Персонаж не найден в базе', 'Ошипко', '', 0, false); }
	}
}

?>
