<?php

/**
 * @author AlexPro
 * @copyright 2008 - 2011 XNova Game Group
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

if(!defined("INSIDE")) die("attemp hacking");

setcookie("x_id", 		"", -1, "/", $_SERVER["SERVER_NAME"], 0);
setcookie("x_secret", 	"", -1, "/", $_SERVER["SERVER_NAME"], 0);
setcookie("uni", 		"", -1, "/", ".xnova.su", 0);
session_destroy();

if ((isset($_GET['mode']) && $_GET['mode'] == 'vk') || isset($_COOKIE['vkid']))
{
	setcookie("vkid", 	"", -1, "/", $_SERVER["SERVER_NAME"], 0);

	if (isset($_GET['mode']) && $_GET['mode'] == 'vk')
	{
		echo '<script>top.location="http://uni2.xnova.su";</script>';
		die();
	}
	else
	{
		message ('Вы вышли из игры, для того чтобы зайти в игру достаточно обновить страничку с приложением', 'Сообщение', '', 0, false);
	}
	
}
else 
{
	message ( 'Выход', 'Сессия закрыта', "http://xnova.su", 3);
}

?>
