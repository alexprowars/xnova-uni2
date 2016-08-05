<?php

/**
 * @author AlexPro
 * @copyright 2008 - 2011 XNova Game Group
 * @var $Display HSTemplateDisplay
 * @var $user user
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

if(!defined("INSIDE")) die("attemp hacking");

$a = @$_GET['a'];
$e = @$_GET['e'];
$s = @$_GET['s'];
$u = @intval( $_GET['u'] );

if ( $s == 1 && isset( $_GET['bid'] ) ) {
	$bid = intval( $_GET['bid'] );

	$buddy = db::query( "SELECT * FROM {{table}} WHERE `id` = '".$bid."';", 'buddy', true );
	if ($buddy['owner'] == $user->data['id']) {
		if ($buddy['active'] == 0 && $a == 1) {
			db::query("DELETE FROM {{table}} WHERE `id` = '".$bid."';", 'buddy');
		} elseif ($buddy['active'] == 1) {
			db::query("DELETE FROM {{table}} WHERE `id` = '".$bid."';", 'buddy');
		} elseif ($buddy['active'] == 0) {
			db::query("UPDATE {{table}} SET `active` = '1' WHERE `id` = '".$bid."';", 'buddy');
		}
	} elseif ( $buddy['sender'] == $user->data['id'] ) {
		db::query( "DELETE FROM {{table}} WHERE `id` = '".$bid."';", 'buddy' );
	}
} elseif (isset($_POST["s"]) && $_POST["s"] == 3 && $_POST["a"] == 1 && $_POST["e"] == 1 && isset( $_POST["u"] ) ) {

	$uid = $user->data["id"];
	$u = intval( $_POST["u"] );

	$buddy = db::query( "SELECT * FROM {{table}} WHERE sender={$uid} AND owner={$u} OR sender={$u} AND owner={$uid}", 'buddy', true );

	if ( !$buddy ) {
		if ( strlen( $_POST['text'] ) > 5000 ) {
			message( "Максимальная длинна сообщения 5000 семволов!", "Ошибка" );
		}
		$text = db::escape_string(strip_tags($_POST['text']));
		db::query("INSERT INTO {{table}} SET sender={$uid}, owner={$u}, active=0, text='{$text}'", 'buddy');
		SendSimpleMessage ($u, '', time(), 1, 'Запрос дружбы', 'Вам отправлен запрос на дружбу.');

		message('Запрос отправлен', 'Предложение дружбы', '?set=buddy');
	} else {
		message('Запрос дружбы был уже отправлен ранее', 'Предложение дружбы');
	}
}

$page = "";

if ($a == 2 && isset($u)) {

	$u = db::query("SELECT id, username FROM {{table}} WHERE id='".$u."'", "users", true);
	if (isset($u) && $u['id'] != $user->data['id']) {

		$parse['id'] 		= $u['id'];
		$parse['username'] 	= $u['username'];

		$Display->addTemplate('buddy', 'buddy_new.php');
		$Display->assign('parse', $parse, 'buddy');

		display('', 'Друзья', false);

	} elseif ($u['id'] == $user->data['id']) {
		message('Нельзя дружить сам с собой', 'Предложение дружбы');
	}
}

$TableTitle = ($a == 1) ? (($e == 1) ? 'Мои запросы' : 'Другие запросы') : 'Список друзей';

$parse['title'] = $TableTitle;
$parse['a']		= (!isset($a)) ? false : true;
$parse['list']	= array();


$query = ($a == 1) ? (($e == 1) ? "WHERE active=0 AND ignor=0 AND sender=".$user->data["id"] : "WHERE active=0 AND ignor=0 AND owner=".$user->data["id"]) : "WHERE active = 1 AND ignor=0 AND (sender = ".$user->data["id"]." OR owner = ".$user->data["id"].")";

$buddyrow = db::query( "SELECT * FROM {{table}} " . $query, 'buddy' );

$i = 0;

while ( $b = db::fetch_assoc( $buddyrow ) ) {

	$q = array();

	$i++;
	$uid = ($b["owner"] == $user->data["id"]) ? $b["sender"] : $b["owner"];

	$u = db::query( "SELECT id, username, galaxy, system, planet, onlinetime, ally_id, ally_name FROM {{table}} WHERE id=".$uid, "users", true);

	$UserAlly = ($u["ally_id"] != 0) ? "<a href=?set=alliance&mode=ainfo&a=" . $u["ally_id"] . ">" . $u["ally_name"] . "</a>" : "";

	if (isset($a)) {
		$LastOnline = $b["text"];
	} else {
		$LastOnline = "<font color=";
		if ($u["onlinetime"] + 60 * 10 >= time()) {
			$LastOnline .= "lime>В игре";
		} elseif ($u["onlinetime"] + 60 * 20 >= time()) {
			$LastOnline .= "yellow>15 мин.";
		} else {
			$LastOnline .= "red>Не в игре";
		}
		$LastOnline .= "</font>";
	}

	if (isset($a) && isset($e)) {
		$UserCommand = "<a href=?set=buddy&s=1&bid=".$b["id"].">Удалить запрос</a>";
	} elseif (isset($a)) {
		$UserCommand = "<a href=?set=buddy&s=1&bid=".$b["id"].">Применить</a><br/>";
		$UserCommand .= "<a href=?set=buddy&a=1&s=1&bid=".$b["id"].">Отклонить</a></a>";
	} else {
		$UserCommand = "<a href=?set=buddy&s=1&bid=".$b["id"].">Удалить</a>";
	}

	$q['id'] 		= $u["id"];
	$q['username'] 	= $u["username"];
	$q['ally'] 		= $UserAlly;
	$q['g'] 		= $u["galaxy"];
	$q['s'] 		= $u["system"];
	$q['p'] 		= $u["planet"];
	$q['online'] 	= $LastOnline;
	$q['c'] 		= $UserCommand;

	$parse['list'][] = $q;
}

$Display->addTemplate('buddy', 'buddy_list.php');
$Display->assign('parse', $parse, 'buddy');

display ($page, 'Список друзей', false);

?>
