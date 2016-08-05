<?php

/**
 * @author AlexPro
 * @copyright 2008 - 2011 XNova Game Group
 * @var $Display HSTemplateDisplay
 * @var $user user
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

if(!defined("INSIDE")) die("attemp hacking");

$banned = '';


$query = db::query('SELECT u.username AS user_1, u2.username AS user_2, b.* FROM {{table}}banned b LEFT JOIN {{table}}users u ON u.id = b.who LEFT JOIN {{table}}users u2 ON u2.id = b.author ORDER BY b.`id` DESC;', '');

$i = 0;
while($u = db::fetch_assoc($query)){
	$banned .="<tr align='center'><td class=\"b\"><a href='?set=players&id=".$u['who']."'>".$u['user_1']."</a></td>".
	"<td class=\"b\"><small>".datezone("d/m/Y H:m:s",$u['time'])."</small></td>".
	"<td class=\"b\"><small>".datezone("d/m/Y H:m:s",$u['longer'])."</small></td>".
	"<td class=\"b\">".$u['theme']."</td>".
	"<td class=\"b\"><a href='?set=players&id=".$u['author']."'>".$u['user_2']."</a></td></tr>";

	$i++;
}

if ($i=="0")
 $banned .= "<tr><th class=b colspan=5>Нет заблокированных игроков</th></tr>";
else
  $banned .= "<tr><th class=b colspan=5>Всего {$i} аккаунтов заблокировано</th></tr>";

$Display->addTemplate('banned', 'banned.php');
$Display->assign('banned', $banned, 'banned');

if ($user->data['id'] && $user->data['banaday'] == 0)
	display('','Список заблокированных игроков', false);
else
	display('','Список заблокированных игроков', false, false);

?>
