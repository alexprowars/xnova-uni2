<?php

/**
 * @author AlexPro
 * @copyright 2008 - 2011 XNova Game Group
 * @var $Display HSTemplateDisplay
 * @var $dpath string
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

if(!defined("INSIDE")) die("attemp hacking");

if (isset($_GET['online'])) {

	header('Content-type: text/html; charset=windows-1251');

	$online = db::query("SELECT id, username, authlevel, sex FROM {{table}} WHERE chat = 1 AND onlinetime > ".(time() - 300)." ORDER BY username", "users");

	echo "<table width=100% align=left valign=top>";

	while ($u = db::fetch_assoc($online)) {

		echo "<tr><td width=22><a href=\"#\" onclick=\"pp('".$u['username']."')\"><img src=/images/private.gif></a></td><td><a href=\"#\" onclick=\"to('".$u['username']."')\">";

		if ($u['authlevel'] > 0)
			echo"<font color=red>".$u['username']."</font";
		else
			echo $u['username'];

		echo"</a></td><td align=right>";

		if ($u['sex'] == 1)
			echo "<img src=/images/male.gif alt=\"МужиГ\" border=0 width=10 height=10>";
		elseif ($u['sex'] == 2)
			echo "<img src=/images/female.gif alt=\"Девушко\" border=0 width=10 height=10>";

		echo"<a href=?set=players&id=".$u['id']." target=\"_blank\"><img src=".$dpath."img/s.gif alt=\"Информация об игроке\" border=0 width=13 height=13></a></td></tr>";

	}

	echo "</table>";
	die();
}

$Display->addTemplate('chat', 'chat.php');

display('', "Межгалактический чат", false);

?>
