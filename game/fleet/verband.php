<?php

/**
 * @author AlexPro
 * @copyright 2008 - 2011 XNova Game Group
 * @var $Display HSTemplateDisplay
 * @var $user user
 * @var $lang array
 * @var $page string
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

if(!defined("INSIDE")) die("attemp hacking");

system::includeLang('fleet');

$fleetid = intval($_POST['fleetid']);

if (!is_numeric($fleetid) || empty($fleetid)) {
    system::Redirect("?set=overview");
}

$query = db::query("SELECT * FROM {{table}} WHERE fleet_id = '".$fleetid."' AND fleet_owner = ".$user->data['id']." AND fleet_mission=1", 'fleets');

if (db::num_rows($query) != 1) {
	message('Этот флот не существует или его больше чем 1!', 'Ошибка');
}

$fleet = db::fetch_array($query);
$aks = db::query("SELECT * FROM {{table}} WHERE id = '".$fleet['fleet_group']."' LIMIT 1", 'aks', true);

if ($fleet['fleet_start_time'] <= time() || $fleet['fleet_end_time'] < time() || $fleet['fleet_mess'] == 1) {
	message('Ваш флот возвращается на планету!', 'Ошибка');
}

if (!isset($_POST['send'])) {

	if (isset($_POST['action']) && $_POST['action'] == 'addaks'){

		if (empty($fleet['fleet_group'])) {
			$rand = mt_rand(100000, 999999999);

			db::query("INSERT INTO {{table}} SET
			`name` = '".addslashes($_POST['groupname'])."',
			`fleet_id` = ".$fleetid.",
			`galaxy` = '" . $fleet['fleet_end_galaxy'] . "',
			`system` = '" . $fleet['fleet_end_system'] . "',
			`planet` = '" . $fleet['fleet_end_planet'] . "',
			`planet_type` = '" . $fleet['fleet_end_type'] . "',
			`user_id` = '" . $user->data['id'] . "'", 'aks');

			$aksid = db::insert_id();

			if (empty($aksid))
				message('Невозможно получить идентификатор САБ', 'Ошибка');

			$aks = db::query("SELECT * FROM {{table}} WHERE id = '".$aksid."' LIMIT 1", 'aks', true);

			db::query("UPDATE {{table}} SET fleet_group = '".$aksid."' WHERE fleet_id = '".$fleetid."'", 'fleets');
			$fleet['fleet_group'] = $aksid;

		} else {
				message('Для этого флота уже задана ассоциация!', 'Ошибка');
		}
	} elseif (isset($_POST['action']) && $_POST['action'] == 'adduser') {

		if ($aks['fleet_id'] != $fleetid)
			message("Вы не можете менять имя ассоциации", 'Ошибка');

		$addtogroup = db::escape_string($_POST['addtogroup']);

		$user_ = db::query("SELECT * FROM {{table}} WHERE username = '".$addtogroup."'", 'users');

		if (db::num_rows($user_) != 1)
			message("Игрок не найден", 'Ошибка');

		$user_data = db::fetch_array($user_);
		$aks_user = db::query("SELECT * FROM {{table}} WHERE aks_id = ".$aks['id']." AND user_id = ".$user_data['id']."", 'aks_user');

		if (db::num_rows($aks_user) > 0)
			message("Игрок уже приглашён для нападения", 'Ошибка');

		db::query("INSERT INTO {{table}} VALUES (".$aks['id'].", ".$user_data['id'].")", 'aks_user');

		$planet_daten = db::query("SELECT `id_owner`, `name` FROM {{table}} WHERE galaxy = '".$aks['galaxy']."' AND system = '".$aks['system']."' AND planet = '".$aks['planet']."' AND planet_type = '".$aks['planet_type']."'", 'planets', true);
		$owner = db::query("SELECT username FROM {{table}} WHERE id = '".$planet_daten['id_owner']."'", 'users', true);

		$message = "Игрок ".$user->data['username']." приглашает вас произвести совместное нападение на планету ".$planet_daten['name']." [".$aks['galaxy'].":".$aks['system'].":".$aks['planet']."] игрока ".$owner['username'].". Имя ассоциации: ".$aks['name'].". Если вы отказываетесь, то просто проигнорируйте данной сообщение.";

		db::query("INSERT INTO {{table}} SET
		`message_owner`='".$user_data['id']."',
		`message_sender`='".$user->data['id']."',
		`message_time`='".time()."',
		`message_type`='0',
		`message_from`='Флот',
		`message_text`='".addslashes($message)."'",'messages');
		db::query("UPDATE {{table}} SET new_message = new_message+1 WHERE id='".$user_data['id']."'",'users');

	} elseif (isset($_POST['action']) && $_POST['action'] == "changename") {

		if ($aks['fleet_id'] != $fleetid)
			message("Вы не можете менять имя ассоциации", 'Ошибка');

		$name = $_POST['groupname'];

		if (strlen($name) > 20)
			message("Слишком длинное имя ассоциации", 'Ошибка');

		if (!preg_match("/^[a-zA-Zа-яА-Я0-9_\.\,\-\!\?\*\ ]+$/u", $name))
			message("Имя ассоциации содержит запрещённые символы", $lang['error']);

		$name = db::escape_string(strip_tags($name));

		$x = db::query("SELECT * FROM {{table}} WHERE name = '".$name."'", 'aks');
		
		if (db::num_rows($x) >= 1)
			message("Имя уже зарезервировано другим игроком", 'Ошибка');

		$aks['name'] = $name;

		db::query("UPDATE {{table}} SET name = '".$name."' WHERE id = '".$aks['id']."'", 'aks');
	}

	$missiontype = array(
		1 => 'Атаковать',
		2 => 'Объединить',
		3 => 'Транспорт',
		4 => 'Оставить',
		5 => 'Удерживать',
		6 => 'Шпионаж',
		7 => 'Колонизировать',
		8 => 'Переработать',
		9 => 'Уничтожить',
	);

	$page = '<script language="JavaScript" src="scripts/flotten.js"></script>
	<script language="JavaScript" src="scripts/ocnt.js"></script>
	<center>
	<table width="710" border="0" cellpadding="0" cellspacing="1">
	<tr height="20">
	<td colspan="9" class="c">Флоты в совместной атаке</td>
	</tr>
	<tr height="20">
	<th>ID</th>
	<th>Задание</th>
	<th> Кол-во</th>
	<th>Отправлен</th>
	<th>Прибытие (цель)</th>
	<th>Цель</th>
	<th>Прибытие (возврат)</th>
	<th>Прибудет через</th>
	<th>Планета старта</th>
	</tr>';

	if ($fleet['fleet_group'] == 0)
		$fq = db::query("SELECT * FROM {{table}} WHERE fleet_id = ".$fleetid."", 'fleets');
	else
		$fq = db::query("SELECT * FROM {{table}} WHERE fleet_group = ".$fleet['fleet_group']."", 'fleets');

	$i = 0;
	while ($f = db::fetch_array($fq)) {
		$i++;

		$page .= "<tr height=20><th>$i</th><th>";

		$page .= "<a title=\"\">{$missiontype[$f['fleet_mission']]}</a>";
		if (($f['fleet_start_time'] + 1) == $f['fleet_end_time'])
			$page .= " <a title=\"R&uuml;ckweg\">(F)</a>";
		$page .= "</th><th><a title=\"";

		$fleets 		= explode(";", $f['fleet_array']);
		$fleets_count 	= 0;
		$e = 0;

		foreach($fleets as $a => $b) {
			if ($b != '') {
				$e++;
				$a = explode(",", $b);
				$b = explode("!", $a[1]);

				$page .= "{$lang['tech']{$a[0]}}: {$b[0]}\n";
				if ($e > 1) {
					$page .= "\t";
				}

				$fleets_count += $b[0];
			}
		}

		$page .= "\">" . pretty_number($fleets_count) . "</a></th>";
		$page .= "<th>[{$f['fleet_start_galaxy']}:{$f['fleet_start_system']}:{$f['fleet_start_planet']}]</th>";
		$page .= "<th>" . datezone("d. M Y H:i:s", $f['fleet_start_time']) . "</th>";
		$page .= "<th>[{$f['fleet_end_galaxy']}:{$f['fleet_end_system']}:{$f['fleet_end_planet']}]</th>";
		$page .= "<th>" . datezone("d. M Y H:i:s", $f['fleet_end_time']) . "</th>";
		$page .= " </form>";

		$page .= "<th><font color=\"lime\"><div id=\"time_0\"><font>" . pretty_time(floor($f['fleet_end_time'] + 1 - time())) . "</font></th><th>";
		$page .= $f['fleet_owner_name']."</th>";
		$page .= "</div></font></tr>";
	}

	if ($i == 0) {
		$page .= "<th>-</th><th>-</th><th>-</th><th>-</th><th>-</th><th>-</th><th>-</th><th>-</th><th>-</th>";
	}
	$page .= '</table></center>';

	if ($fleet['fleet_group'] == 0) {
		$rand = mt_rand(100000, 999999999);
		$page .= '<table width="710" border="0" cellpadding="0" cellspacing="1">
		<tr height="20">
			<td class="c" colspan="2">Создание ассоциации флота</td>
		</tr>
		<form action="?set=fleet&page=verband" method="POST">
		<input type="hidden" name="fleetid" value="'.$fleetid.'" />
		<input type="hidden" name="action" value="addaks" />
		<tr>
				<th colspan="2"><input name="groupname" value="AKS'.$rand.'" size=50 /> <br /> <input type="submit" value="Создать" /></th>
		</tr>
		</form>
		</table>';
	} elseif ($fleetid == $aks['fleet_id']) {
		$page .= '<table width="710" border="0" cellpadding="0" cellspacing="1">
		<tr height="20">
		<td class="c" colspan="2">Ассоциация флота '.$aks['name'].'</td>
		</tr>
		<form action="?set=fleet&page=verband" method="POST">
		<input type="hidden" name="fleetid" value="'.$fleetid.'" />
		<input type="hidden" name="action" value="changename" />
		<tr>
		<th colspan="2"><input name="groupname" value="'.$aks['name'].'" size=50 /> <br /> <input type="submit" value="Изменить" /></th>
		</tr>
		</form>
		<tr>
		<th>
		<table width="100%" border="0" cellpadding="0" cellspacing="1">
		<tr height="20">
		<td class="c">Приглашенные участники</td>
		<td class="c">Пригласить участников</td>
		</tr>
		<tr>
		<th width="50%">
		<select size="5">';

		$query = db::query("SELECT game_users.username FROM game_users, game_aks_user WHERE game_users.id = game_aks_user.user_id AND game_aks_user.aks_id = ".$fleet['fleet_group']."", '');
		if (db::num_rows($query) == 0) $page .= "<option>нет участников</option>";
		while ($us = db::fetch_assoc($query)) {
			$page .= "<option>".$us['username']."</option>";
		}

		$page .= '</select>
		</th>
		<form action="?set=fleet&page=verband" method="POST">
		<input type="hidden" name="fleetid" value="'.$fleetid.'" />
		<input type="hidden" name="action" value="adduser" />
		<td><input name="addtogroup" size="40" />&nbsp;<input type="submit" value="OK" /></td>
		</form>
		</tr>
		</table>
		</th>
		</tr><tr></tr>
		</table>';
	}
}

display($page, "Совместная атака");

?>
