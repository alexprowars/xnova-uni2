<?php

/**
 * @author AlexPro
 * @copyright 2008 - 2011 XNova Game Group
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

if(!defined("INSIDE")) die("attemp hacking");

include("includes/CreateRaportHTML.php");

$raportrow = db::query("SELECT * FROM {{table}} WHERE `id` = '".intval($_GET['r'])."';", 'rw', true);

if (!isset($raportrow['id']))
	message('Данный боевой отчет удалён с сервера', 'Ошибка', '', 0, false);

$user_list = json_decode($raportrow['id_users'], true);

if (isset($raportrow['id']) && md5('xnovasuka'.$raportrow['id']) != $_GET['k'])
	message('Не правильный ключ', 'Ошибка', '', 0, false);
elseif (!in_array($user->data['id'], $user_list) && $user->data['authlevel'] != 3)
	message('Вы не можете просматривать этот боевой доклад', 'Ошибка', '', 0, false);
else {
	$Page  = "<html><head><title>Боевой доклад</title>";
	$Page .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"xnsim/report.css\">";
	$Page .= "<meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\" />";
	$Page .= "</head><body><script>function show(id){if(document.getElementById(id).style.display==\"block\")document.getElementById(id).style.display=\"none\"; else document.getElementById(id).style.display=\"block\";}</script>";
	$Page .= "<table width=\"99%\"><tr><td><center>";

	if ($user_list[0] == $user->data['id'] && $raportrow['no_contact'] == 1) {
		$Page .= "Контакт с вашим флотом потерян.<br>(Ваш флот был уничтожен в первой волне атаки.)";
	} else {
		$result = json_decode($raportrow['raport'], true);
		
		$formatted_cr = formatCR($result[0], $result[1], $result[2], $result[3], $result[4], $result[5]);
		$Page .= $formatted_cr['html'];
	}

	$Page .= "</center></td></tr><tr align=center><td>ID боевого доклада: <a href=\"?set=log&mode=new&save=".md5('xnovasuka'.$raportrow['id']).$raportrow['id']."\"><font color=red>".md5('xnovasuka'.$raportrow['id']).$raportrow['id']."</font></a></td></tr></table></body></html>";

	echo $Page;
}

?>
