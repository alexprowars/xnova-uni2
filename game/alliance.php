<?php

/**
 * @author AlexPro
 * @copyright 2008 - 2011 XNova Game Group
 * @var $Display HSTemplateDisplay
 * @var $user user
 * @var $lang array
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

if(!defined("INSIDE")) die("attemp hacking");

$sort1 = (isset($_GET['sort1'])) ? intval($_GET['sort1']) : 0;
$sort2 = (isset($_GET['sort2'])) ? intval($_GET['sort2']) : 0;
$rank  = (isset($_GET['rank']))  ? intval($_GET['rank'])  : 0;

$d = @$_GET['d'];
if ((!is_numeric($d)) || (empty($d) && $d != 0))
	unset($d);

$kick = @intval($_GET['kick']);
if (empty($kick))
	unset($kick);

$id = @intval($_GET['id']);
if (empty($id))
	unset($id);

$mode     = @$_GET['mode'];
$yes      = @$_GET['yes'];
$edit     = @$_GET['edit'];
$show     = @intval($_GET['show']);
$sort     = @intval($_GET['sort']);
$t        = @$_GET['t'];
$a        = @intval($_GET['a']);
$tag      = @db::escape_string($_GET['tag']);

system::includeLang('alliance');

function MessageForm ($Title, $Message, $Goto = '', $Button = ' ok ', $TwoLines = false) {

	$Form = "<form action=\"". $Goto ."\" method=\"post\">";
	$Form .= "<table width=\"100%\"><tr>";
	$Form .= "<td class=\"c\">". $Title ."</td>";
	$Form .= "</tr><tr>";

	if ($TwoLines == true) {
		$Form .= "<th >". $Message ."</th>";
		$Form .= "</tr><tr>";
		$Form .= "<th align=\"center\"><input type=\"submit\" value=\"". $Button ."\"></th>";
	} else {
		$Form .= "<th>". $Message ."<input type=\"submit\" value=\"". $Button ."\"></th>";
	}

	$Form .= "</tr></table></form>";

	return $Form;
}

if ($mode == 'ainfo') {

	if ($tag != "") {
		$allyrow = db::query("SELECT * FROM {{table}} WHERE ally_tag = '".$tag."'", "alliance", true);
	} elseif ($a != 0) {
		$allyrow = db::query("SELECT * FROM {{table}} WHERE id = '".$a."'", "alliance", true);
	} else {
		message("Указанного альянса не существует в игре!", "Информация об альянсе");
	}

	if (!isset($allyrow['id'])) {
		message("Указанного альянса не существует в игре!", "Информация об альянсе");
	}

	if ($allyrow['ally_image'] != "")
		$allyrow['ally_image'] = "<tr><th colspan=2><img src=\"".$allyrow['ally_image']."\" style=\"max-width:650px\"></th></tr>";

	if ($allyrow['ally_description'] == "")
		$allyrow['ally_description'] = "[center]У этого альянса ещё нет описания[/center]";

	if ($allyrow['ally_web'] != "")
		$allyrow['ally_web'] = "<tr><th>Сайт альянса:</th><th><a href=\"".$allyrow['ally_web']."\" target=\"_blank\">".$allyrow['ally_web']."</a></th></tr>";

	$lang['ally_member_scount']     = $allyrow['ally_members'];
	$lang['ally_name']              = $allyrow['ally_name'];
	$lang['ally_tag']               = $allyrow['ally_tag'];
	$lang['ally_description']       = $allyrow['ally_description'];
	$lang['ally_image']             = $allyrow['ally_image'];
	$lang['ally_web']               = $allyrow['ally_web'];
	$lang['bewerbung'] 				= ($user->data['ally_id'] == 0) ? "<tr><th>Вступление</th><th><a href=\"?set=alliance&mode=apply&amp;allyid=".$allyrow['id']."\">Нажмите сюда для подачи заявки</a></th></tr>" : '';

    $Display->addTemplate('info', 'alliance_info.php');
    $Display->assign('parse', $lang, 'info');

	display('', 'Альянс '.$allyrow['ally_name'], false);
}

if (isset($user->data['id'])) {

if ($user->data['ally_id'] == 0) {

	$ally_request = db::query("SELECT COUNT(*) AS num FROM {{table}} WHERE u_id = ".$user->data['id'].";", "alliance_requests", true);
    $ally_request = $ally_request['num'];
	
	if ($mode == 'make' && $ally_request == 0) { // Создание альянса

		if ($yes == 1 && $_POST) {

			if (!$_POST['atag']) {
				message($lang['have_not_tag'], $lang['make_alliance']);
			}
			if (!$_POST['aname']) {
				message($lang['have_not_name'], $lang['make_alliance']);
			}
			if (!preg_match('/^[a-zA-Zа-яА-Я0-9_\.\,\-\!\?\*\ ]+$/u', $_POST['atag'])){
				message("Абревиатура альянса содержит запрещённые символы", $lang['make_alliance']);
			}
			if (!preg_match('/^[a-zA-Zа-яА-Я0-9_\.\,\-\!\?\*\ ]+$/u', $_POST['aname'])){
				message("Название альянса содержит запрещённые символы", $lang['make_alliance']);
			}

			$tagquery = db::query("SELECT * FROM {{table}} WHERE ally_tag = '".addslashes($_POST['atag'])."'", 'alliance', true);

			if ($tagquery) {
				message(str_replace('%s', $_POST['atag'], $lang['always_exist']), $lang['make_alliance']);
			}

			db::query("INSERT INTO {{table}} SET `ally_name` = '".addslashes($_POST['aname'])."', `ally_tag`= '".addslashes($_POST['atag'])."' , `ally_owner` = '".$user->data['id']."', `ally_register_time` = ".time() , "alliance");
			
			$ally_id = db::insert_id();
			
			db::query("UPDATE {{table}} SET `ally_id` = '".$ally_id."', `ally_name` = '".addslashes($_POST['aname'])."' WHERE `id` = '".$user->data['id']."'", "users");
			db::query("INSERT INTO {{table}} (a_id, u_id, time) VALUES (".$ally_id.", ".$user->data['id'].", ".time().")", "alliance_members");

            display(MessageForm(str_replace('%s', $_POST['atag'], $lang['ally_maked']), str_replace('%s', $_POST['atag'], $lang['alliance_has_been_maked']) . "<br><br>", "", $lang['Ok']), $lang['make_alliance'], false);
		} else {
			$Display->addTemplate('make', 'alliance_make.php');
            display('', $lang['make_alliance'], false);
		}
	}

	if ($mode == 'search' && $ally_request == 0) {

		$parse = array();

		if (isset($_POST['searchtext']) && $_POST['searchtext'] != '') {

			if (!preg_match('/^[a-zA-Zа-яА-Я0-9_\.\,\-\!\?\*\ ]+$/u', $_POST['searchtext'])){
				message("Строка поиска содержит запрещённые символы", $lang['make_alliance'], '?set=alliance&mode=search', 2);
			}

			$search = db::query("SELECT * FROM {{table}} WHERE ally_name LIKE '%".$_POST['searchtext']."%' or ally_tag LIKE '%".$_POST['searchtext']."%' LIMIT 30", "alliance");

            $parse['result'] = array();

			if (db::num_rows($search) != 0) {

				while ($s = db::fetch_assoc($search)) {
					$entry = array();
					$entry['ally_tag'] 		= "[<a href=\"?set=alliance&mode=apply&allyid={$s['id']}\">{$s['ally_tag']}</a>]";
					$entry['ally_name'] 	= $s['ally_name'];
					$entry['ally_members'] 	= $s['ally_members'];

					$parse['result'][] 		= $entry;
				}
			}
		}

        $parse['searchtext'] = (isset($_POST['searchtext'])) ? $_POST['searchtext'] : '';

        $Display->addTemplate('search', 'alliance_search.php');
        $Display->assign('parse', $parse, 'search');

		display('', $lang['search_alliance'], false);
	}

	if ($mode == 'apply') {
        
		if (!is_numeric($_GET['allyid']) || !$_GET['allyid']) {
			message($lang['it_is_not_posible_to_apply'], $lang['it_is_not_posible_to_apply']);
		}

		$allyid   = intval($_GET['allyid']);

		$allyrow = db::query("SELECT ally_tag, ally_request, ally_request_notallow FROM {{table}} WHERE id = '".$allyid."'", "alliance", true);

		if (!isset($allyrow['ally_tag'])) {
			message("Альянса не существует!", "Ошибка");
		}
		if ($allyrow['ally_request_notallow'] != 0) {
			message("Данный альянс является закрытым для вступлений новых членов", "Ошибка");
		}

		if (isset($_POST['further'])) {
			$request = db::query("SELECT COUNT(*) AS num FROM {{table}} WHERE a_id = ".$allyid." AND u_id = ".$user->data['id'].";", "alliance_requests", true);
			if ($request['num'] == 0) {	
				db::query("INSERT INTO {{table}} VALUES (".$allyid.", ".$user->data['id'].", ".time().", '".db::escape_string(strip_tags($_POST['text']))."')", "alliance_requests");
				message($lang['apply_registered'], $lang['your_apply'], '?set=alliance', 3);
			} else 
				message('Вы уже отсылали заявку на вступление в этот альянс!', 'Ошибка', '?set=alliance', 3);
		}

		$parse = array();

		$parse['allyid'] 		= $allyid;
		$parse['text_apply'] 	= ($allyrow['ally_request']) ? $allyrow['ally_request'] : '';
		$parse['ally_tag'] 		= $allyrow['ally_tag'];

        $Display->addTemplate('applyform', 'alliance_applyform.php');
        $Display->assign('parse', $parse, 'applyform');

		display('', 'Запрос на вступление', false);
	}

	
	if (isset($_POST['bcancel']) && isset($_POST['r_id'])) {
		db::query("DELETE FROM {{table}} WHERE a_id = ".intval($_POST['r_id'])." AND `u_id` = ".$user->data['id'], "alliance_requests");

		message("Вы отозвали свою заявку на вступление в альянс", "Отзыв заявки", "?set=alliance", 2);
	}
	
	$parse = array();
	
	$parse['list'] = array();

	$requests = db::query("SELECT r.*, a.ally_name, a.ally_tag FROM {{table}}alliance_requests r LEFT JOIN {{table}}alliance a ON a.id = r.a_id WHERE r.u_id = ".$user->data['id'].";", "");
	
	while ($request = db::fetch_assoc($requests)) {
		$parse['list'][] = array($request['a_id'], $request['ally_tag'], $request['ally_name'], $request['time']);
	}
	
	$parse['allys'] = array();
	
	$allys = db::query("SELECT s.total_points, a.`id`, a.`ally_tag`, a.`ally_name`, a.`ally_members` FROM {{table}}statpoints s, {{table}}alliance a WHERE s.`stat_type` = '2' AND s.`stat_code` = '1' AND a.id = s.id_owner ORDER BY s.`total_points` DESC LIMIT 0,15;", '');

	while ($ally = db::fetch_assoc($allys)) {
		$ally['total_points'] = pretty_number($ally['total_points']);
		$parse['allys'][] = $ally;
	}
	
	$Display->addTemplate('default', 'alliance_default.php');
	$Display->assign('parse', $parse, 'default');
	
	display('', $lang['alliance'], false);
}
//---------------------------------------------------------------------------------------------------------------------------------------------------
else {

	$ally = db::query("SELECT * FROM {{table}} WHERE id = '".$user->data['ally_id']."'", "alliance", true);
	
	$ally_member = db::query("SELECT * FROM {{table}} WHERE u_id = ".$user->data['id'].";", "alliance_members", true);

	if ($ally_member['a_id'] != $ally['id'])
		db::query("DELETE FROM {{table}} WHERE u_id = ".$user->data['id'].";", "alliance_members");
	
	if ($ally['ally_ranks'] == NULL)
		$ally['ally_ranks'] = 'a:0:{}';
	
	$ally_ranks = json_decode($ally['ally_ranks'], true);

    if ($ally['ally_owner'] == $user->data['id']) {
        $user_can_watch_memberlist_status 	= true;
        $user_can_watch_memberlist 			= true;
        $user_can_send_mails 				= true;
        $user_can_kick 						= true;
        $user_can_edit_rights 				= true;
        $user_can_exit_alliance 			= true;
        $user_bewerbungen_einsehen 			= true;
        $user_bewerbungen_bearbeiten 		= true;
        $user_admin 						= true;
        $user_onlinestatus 					= true;
        $user_diplomacy 					= true;
    } elseif ($ally_member['rank'] == 0) {
        $user_can_watch_memberlist_status 	= false;
        $user_can_watch_memberlist 			= false;
        $user_can_send_mails 				= false;
        $user_can_kick 						= false;
        $user_can_edit_rights 				= false;
        $user_can_exit_alliance 			= false;
        $user_bewerbungen_einsehen 			= false;
        $user_bewerbungen_bearbeiten 		= false;
        $user_admin 						= false;
        $user_onlinestatus 					= false;
        $user_diplomacy 					= false;
	} else {
        $user_can_watch_memberlist_status 	= ($ally_ranks[$ally_member['rank']-1]['onlinestatus'] == 1) ? true : false;
        $user_can_watch_memberlist 			= ($ally_ranks[$ally_member['rank']-1]['memberlist'] == 1) ? true : false;
        $user_can_send_mails 				= ($ally_ranks[$ally_member['rank']-1]['mails'] == 1) ? true : false;
        $user_can_kick 						= ($ally_ranks[$ally_member['rank']-1]['kick'] == 1) ? true : false;
        $user_can_edit_rights 				= ($ally_ranks[$ally_member['rank']-1]['rechtehand'] == 1) ? true : false;
        $user_can_exit_alliance 			= ($ally_ranks[$ally_member['rank']-1]['delete'] == 1) ? true : false;
        $user_bewerbungen_einsehen 			= ($ally_ranks[$ally_member['rank']-1]['bewerbungen'] == 1) ? true : false;
        $user_bewerbungen_bearbeiten 		= ($ally_ranks[$ally_member['rank']-1]['bewerbungenbearbeiten'] == 1) ? true : false;
        $user_admin 						= ($ally_ranks[$ally_member['rank']-1]['administrieren'] == 1) ? true : false;
        $user_onlinestatus 					= ($ally_ranks[$ally_member['rank']-1]['onlinestatus'] == 1) ? true : false;
        $user_diplomacy 					= ($ally_ranks[$ally_member['rank']-1]['diplomacy'] == 1) ? true : false;
    }

	if (!isset($ally['id'])) {
		db::query("UPDATE {{table}} SET `ally_id` = 0 WHERE `id` = '".$user->data['id']."'", "users");
		db::query("DELETE FROM {{table}} WHERE u_id = ".$user->data['id'].";", "alliance_members");
		message($lang['ally_notexist'], $lang['your_alliance'], '?set=alliance');
	}
	
	if (!isset($ally_member['a_id'])) {
		db::query("INSERT INTO {{table}} (a_id, u_id, time) VALUES (".$ally['id'].", ".$user->data['id'].", ".time().")", "alliance_members");
		system::Redirect("?set=alliance");
	}

	if ($mode == 'exit') {
		if ($ally['ally_owner'] == $user->data['id']) {
			message($lang['Owner_cant_go_out'], $lang['Alliance']);
		}

		if (isset($_GET['yes'])) {
			db::query("UPDATE {{table}} SET `ally_id` = 0, `ally_name` = '' WHERE `id` = '".$user->data['id']."'", "users");
			db::query("DELETE FROM {{table}} WHERE u_id = ".$user->data['id'].";", "alliance_members");
			
			$page = MessageForm($lang['Go_out_welldone'], "<br>", '?set=alliance', $lang['Ok']);
		} else {
			$page = MessageForm($lang['Want_go_out'], "<br>", "?set=alliance&mode=exit&yes=1", "Подтвердить");
		}
		display($page, 'Выход их альянса', false);
	}

	if ($mode == 'diplo') {

		if ($ally['ally_owner'] != $user->data['id'] && !$user_diplomacy) {
			message($lang['Denied_access'], "Дипломатия");
		}

		$parse['DText'] = "";
		$parse['DMyQuery'] = "";
		$parse['DQuery'] = "";

		$status = array(0 => "Нейтральное", 1 => "Перемирие", 2 => "Мир", 3 => "Война");

		if (isset($_GET['edit']) && $_GET['edit'] == "add") {
			$st = intval($_POST['status']);
			$al = db::query("SELECT id, ally_name FROM {{table}} WHERE id = '".intval($_POST['ally'])."'", "alliance", true);
			if (!$al['id'])
				message("Ошибка ввода параметров", "Дипломатия", "?set=alliance&mode=diplo", 3);

			$ad = db::query("SELECT id FROM {{table}} WHERE a_id = ".$ally['id']." AND d_id = ".$al['id'].";", "alliance_diplomacy");
			if (db::num_rows($ad) > 0)
				message("У вас уже есть соглашение с этим альянсом. Разорвите старое соглашения прежде чем создать новое.", "Дипломатия", "?set=alliance&mode=diplo", 3);

			if ($st < 0 || $st > 3) $st = 0;

			db::query("INSERT INTO {{table}} VALUES (NULL, ".$ally['id'].", ".$al['id'].", ".$st.", 0, 1)", "alliance_diplomacy");
			db::query("INSERT INTO {{table}} VALUES (NULL, ".$al['id'].", ".$ally['id'].", ".$st.", 0, 0)", "alliance_diplomacy");

			message("Отношение между вашими альянсами успешно добавлено", "Дипломатия", "?set=alliance&mode=diplo", 3);

		} elseif (isset($_GET['edit']) && $_GET['edit'] == "del") {
			$al = db::query("SELECT a_id, d_id FROM {{table}} WHERE id = '".intval($_GET['id'])."' AND a_id = ".$ally['id'].";", "alliance_diplomacy", true);

			if (!$al['a_id'])
				message("Ошибка ввода параметров", "Дипломатия", "?set=alliance&mode=diplo", 3);

			db::query("DELETE FROM {{table}} WHERE a_id = ".$al['a_id']." AND d_id = ".$al['d_id'].";", "alliance_diplomacy");
			db::query("DELETE FROM {{table}} WHERE a_id = ".$al['d_id']." AND d_id = ".$al['a_id'].";", "alliance_diplomacy");

			message("Отношение между вашими альянсами расторжено", "Дипломатия", "?set=alliance&mode=diplo", 3);

		} elseif (isset($_GET['edit']) && $_GET['edit'] == "suc") {
			$al = db::query("SELECT a_id, d_id FROM {{table}} WHERE id = '".intval($_GET['id'])."' AND a_id = ".$ally['id']."", "alliance_diplomacy", true);

			if (!$al['a_id'])
				message("Ошибка ввода параметров", "Дипломатия", "?set=alliance&mode=diplo", 3);

			db::query("UPDATE {{table}} SET status = 1 WHERE a_id = ".$al['a_id']." AND d_id = ".$al['d_id'].";", "alliance_diplomacy");
			db::query("UPDATE {{table}} SET status = 1 WHERE a_id = ".$al['d_id']." AND d_id = ".$al['a_id'].";", "alliance_diplomacy");

			message("Отношение между вашими альянсами подтверждено", "Дипломатия", "?set=alliance&mode=diplo", 3);
		}

		$dp = db::query("SELECT ad.*, a.ally_name FROM {{table}}alliance_diplomacy ad, {{table}}alliance a WHERE a.id = ad.d_id AND ad.a_id = '".$ally['id']."';", "");

		while ($diplo = db::fetch_assoc($dp)) {
			if ($diplo['status'] == 0) {
				if ($diplo['primary'] == 1) {
					$parse['DMyQuery'] .= "<tr><th>".$diplo['ally_name']."</th><th>".$status[$diplo['type']]."</th><th><a href=\"?set=alliance&mode=diplo&edit=del&id={$diplo['id']}\"><img src=\"{$dpath}pic/abort.gif\" alt=\"Удалить заявку\"></a></th></tr>";
				} else {
					$parse['DQuery'] .= "<tr><th>".$diplo['ally_name']."</th><th>".$status[$diplo['type']]."</th><th><a href=\"?set=alliance&mode=diplo&edit=suc&id={$diplo['id']}\"><img src=\"{$dpath}pic/appwiz.gif\" alt=\"Подтвердить\"></a> <a href=\"?set=alliance&mode=diplo&edit=del&id={$diplo['id']}\"><img src=\"{$dpath}pic/abort.gif\" alt=\"Удалить заявку\"></a></th></tr>";
				}
			} else {
				$parse['DText'] .= "<tr><th>".$diplo['ally_name']."</th><th>".$ally['ally_name']."</th><th>".$status[$diplo['type']]."</th><th><a href=\"?set=alliance&mode=diplo&edit=del&id={$diplo['id']}\"><img src=\"{$dpath}pic/abort.gif\" alt=\"Удалить\"></a></th></tr>";
			}
		}

		if ($parse['DMyQuery'] == "")
			$parse['DMyQuery'] = "<tr><th colspan=3>нет</th></tr>";
		if ($parse['DQuery'] == "")
			$parse['DQuery'] = "<tr><th colspan=3>нет</th></tr>";
		if ($parse['DText'] == "")
			$parse['DText'] = "<tr><th colspan=4>нет</th></tr>";

		$parse['a_list'] = "<option value=\"0\">список альянсов";
		$ally_list = db::query("SELECT id, ally_name, ally_tag FROM {{table}} WHERE id != ".$user->data['ally_id']." AND ally_members > 0", "alliance");
		while( $a_list = db::fetch_assoc($ally_list) ) {
			$parse['a_list'] .= "<option value=\"".$a_list['id']."\">".$a_list['ally_name']." [".$a_list['ally_tag']."]";
		}

        $Display->addTemplate('diplomacy', 'alliance_diplomacy.php');
        $Display->assign('parse', $parse, 'diplomacy');

		display('', "Дипломатия", false);
	}

	if ($mode == 'memberslist' || ($mode == 'admin' && $edit == 'members')) {
	
	    $parse = $lang;
	
		if ($mode == 'admin' && $edit == 'members') {
			if ($ally['ally_owner'] != $user->data['id'] && !$user_can_kick) {
				message($lang['Denied_access'], $lang['Members_list']);
			}
			
			if (isset($kick)) {
				if ($ally['ally_owner'] != $user->data['id'] && !$user_can_kick) {
					message($lang['Denied_access'], $lang['Members_list']);
				}

				$u = db::query("SELECT * FROM {{table}} WHERE id = '".$kick."' LIMIT 1", 'users', true);

				if ($u['ally_id'] == $ally['id'] && $u['id'] != $ally['ally_owner']) {
					db::query("UPDATE {{table}} SET `ally_id` = '0', `ally_name` = '' WHERE `id` = '".$u['id']."'", 'users');
					db::query("DELETE FROM {{table}} WHERE u_id = ".$u['id'].";", "alliance_members");
				}
			} elseif (isset($_POST['newrang']) && $id != 0) {
				$q = db::query("SELECT `id`, `ally_id` FROM {{table}} WHERE id = '".$id."' LIMIT 1", 'users', true);

				if ((isset($ally_ranks[$_POST['newrang']-1]) || $_POST['newrang'] == 0) && $q['id'] != $ally['ally_owner'] && $q['ally_id'] == $ally['id']) {
					db::query("UPDATE {{table}} SET `rank` = '".intval($_POST['newrang'])."' WHERE `u_id` = '".$id."';", 'alliance_members');
				}
			}
		
			$parse['admin'] = true;
		} else {
			if ($ally['ally_owner'] != $user->data['id'] && !$user_can_watch_memberlist) {
				message($lang['Denied_access'], $lang['Members_list']);
			}
		
			$parse['admin'] = false;
		}

		$sort = "";

		if ($sort2) {
			if ($sort1 == 1) {
				$sort = " ORDER BY u.`username`";
			} elseif ($sort1 == 2) {
				$sort = " ORDER BY m.`rank`";
			} elseif ($sort1 == 3) {
				$sort = " ORDER BY s.`total_points`";
			} elseif ($sort1 == 4) {
				$sort = " ORDER BY m.`time`";
			} elseif ($sort1 == 5 && $user_can_watch_memberlist_status) {
				$sort = " ORDER BY u.`onlinetime`";
			} else {
				$sort = " ORDER BY u.`id`";
			}

			if ($sort2 == 1) {
				$sort .= " DESC;";
			} elseif ($sort2 == 2) {
				$sort .= " ASC;";
			}
		}
		$listuser = db::query("SELECT u.id, u.username, u.race, u.galaxy, u.system, u.planet, u.onlinetime, m.rank, m.time, s.total_points FROM {{table}}users u LEFT JOIN {{table}}alliance_members m ON m.u_id = u.id LEFT JOIN {{table}}statpoints s ON s.id_owner = u.id AND stat_type = 1 WHERE u.ally_id = '".$user->data['ally_id']."'".$sort."", '');

		$i = 0;
        $parse['memberslist'] = array();

		while ($u = db::fetch_assoc($listuser)) {
		
			$i++;
			$u['i'] = $i;

			if ($u["onlinetime"] + 60 * 10 >= time() && $user_can_watch_memberlist_status) {
				$u["onlinetime"] = "lime>".$lang['On']."<";
			} elseif ($u["onlinetime"] + 60 * 20 >= time() && $user_can_watch_memberlist_status) {
				$u["onlinetime"] = "yellow>".$lang['15_min']."<";
			} elseif ($user_can_watch_memberlist_status) {
				$hours = floor((time() - $u["onlinetime"]) / 3600);
				$u["onlinetime"] = "red>".$lang['Off']." ".floor($hours / 24)." д. ".($hours % 24)." ч.<";
			}
			
			if ($ally['ally_owner'] == $u['id']) {
				$u["ally_range"] = ($ally['ally_owner_range'] == '') ? "Основатель" : $ally['ally_owner_range'];
			} elseif (isset($ally_ranks[$u['rank']-1]['name'])) {
				$u["ally_range"] = $ally_ranks[$u['rank']-1]['name'];
			} else {
				$u["ally_range"] = $lang['Novate'];
			}

			$u['points'] 	= pretty_number($u['total_points']);
			$u['time'] 		= ($u['time'] > 0) ? datezone("d.m.Y H:i", $u['time']) : '-';

			$parse['memberslist'][] = $u;
				
			if ($rank == $u['id'] && $parse['admin']) {
				$r['Rank_for'] 	= 'Установить ранг для '.$u['username'];
				$r['options'] 	= "<option value=\"0\">Новичок</option>";

				foreach($ally_ranks as $a => $b) {
					$r['options'] .= "<option value=\"".($a + 1)."\"";
					if ($u['rank']-1 == $a) {
						$r['options'] .= ' selected=selected';
					}
					$r['options'] .= ">".$b['name']."</option>";
				}
				$r['id'] = $u['id'];

				$parse['memberslist'][] = $r;
			}
		}

		if ($sort2 == 1) {
			$s = 2;
		} elseif ($sort2 == 2) {
			$s = 1;
		} else {
			$s = 1;
		}

		if ($i != $ally['ally_members']) {
			db::query("UPDATE {{table}} SET `ally_members` = '".$i."' WHERE `id` = '".$ally['id']."'", 'alliance');
		}

		$parse['i'] = $i;
		$parse['s'] = $s;
		$parse['status'] = $user_can_watch_memberlist_status;

        $Display->addTemplate('members', 'alliance_members_admin.php');
        $Display->assign('parse', $parse, 'members');

		display('', $lang['Members_list'], false);
	}

	if ($mode == 'circular') {

		if ($user->data['mnl_alliance'] != 0)
			db::query("UPDATE {{table}} SET `mnl_alliance` = '0' WHERE `id` = '".$user->data['id']."'", "users");

		if ($ally['ally_owner'] != $user->data['id'] && !$user_can_send_mails) {
			message($lang['Denied_access'], $lang['Send_circular_mail']);
		}

		if (isset($_POST['deletemessages']) && $ally['ally_owner'] == $user->data['id']){
			$DeleteWhat = $_POST['deletemessages'];
			if ($DeleteWhat == 'deleteall')
			{
				db::query("DELETE FROM {{table}} WHERE `ally_id` = '". $user->data['ally_id'] ."';", 'chat');
			}
			elseif ($DeleteWhat == 'deletemarked' || $DeleteWhat == 'deleteunmarked')
			{
				$Mess_Array = array();

				foreach($_POST as $Message => $Answer)
				{
					if (preg_match("/delmes/iu", $Message) && $Answer == 'on')
					{
						$MessId   = str_replace("delmes", "", $Message);
						$Mess_Array[] = $MessId;
					}
				}

				$Mess_Array = implode(',', $Mess_Array);

				if ($Mess_Array != '')
					db::query("DELETE FROM {{table}} WHERE `id` ".(($DeleteWhat == 'deleteunmarked') ? 'NOT' : '')." IN (".$Mess_Array.") AND `ally_id` = '". $user->data['ally_id'] ."';", 'chat');
			}
		}

		if (isset($_GET['sendmail']) && isset($_POST['text']) && $_POST['text'] != '')
        {
			$_POST['text'] 	= system::FormatText($_POST['text']);

			db::query("INSERT INTO {{table}} SET `ally_id` = '".$user->data['ally_id']."', `user` = '".$user->data['username']."', user_id = ".$user->data['id'].", `message` = '".$_POST['text']."', `timestamp` = '".time()."'", "chat");
			db::query("UPDATE {{table}} SET `mnl_alliance` = `mnl_alliance` + '1' WHERE `ally_id` = '".$user->data['ally_id']."' AND id != ".$user->data['id']."", "users");
		}

		$parse = array();

		$news_count = db::query("SELECT COUNT(*) AS num FROM {{table}} WHERE `ally_id` = '".$user->data['ally_id']."'", "chat", true);

		if ($news_count['num'] > 0){
			$p = (isset($_GET['p'])) ? intval($_GET['p']) : 1;
			
			$pages = PageSelector($news_count['num'], 20, '?set=alliance&mode=circular', $p);

			$mess = db::query("SELECT * FROM {{table}} WHERE `ally_id` = '".$user->data['ally_id']."' ORDER BY `id` DESC limit ".(($p - 1) * 20).", 20", "chat");

			$parse['messages'] = array();
			while($mes = db::fetch_assoc($mess)){
				$parse['messages'][] = $mes;
			}
		}

		$parse['ally_owner'] 	= ($ally['ally_owner'] == $user->data['id']) ? true : false;
		$parse['pages'] 		= (isset($pages)) ? $pages : '[0]';
		$parse['parser']		= (isset($user->data['bb_parser']) && $user->data['bb_parser'] == 1) ? true : false;

        $Display->addTemplate('chat', 'alliance_chat.php');
        $Display->assign('parse', $parse, 'chat');

		display('', 'Альянс-чат', false);
	}

	if ($mode == 'admin') {

		if ($edit == 'rights')
        {
			if ($ally['ally_owner'] != $user->data['id'] && !$user_can_edit_rights)
            {
				message($lang['Denied_access'], $lang['Members_list']);
			}
            elseif (!empty($_POST['newrangname']))
            {
				$name = db::escape_string(strip_tags($_POST['newrangname']));

				$ally_ranks[] = array('name' => $name, 'mails' => 0, 'delete' => 0, 'kick' => 0, 'bewerbungen' => 0, 'administrieren' => 0, 'bewerbungenbearbeiten' => 0, 'memberlist' => 0, 'onlinestatus' => 0, 'rechtehand' => 0, 'diplomacy' => 0);
				$ranks = json_encode($ally_ranks);

				db::query("UPDATE {{table}} SET `ally_ranks` = '".addslashes($ranks)."' WHERE `id` = " . $ally['id'], "alliance");

			}
            elseif (isset($_POST['id']) && is_array($_POST['id']))
            {
				$ally_ranks_new = array();

				foreach ($_POST['id'] as $id) {
					$name = $ally_ranks[$id]['name'];

					$ally_ranks_new[$id]['name'] = $name;

					$ally_ranks_new[$id]['delete'] 					= (isset($_POST['u' . $id . 'r0']) && $ally['ally_owner'] == $user->data['id']) ? 1 : 0;
					$ally_ranks_new[$id]['kick']					= (isset($_POST['u' . $id . 'r1']) && $ally['ally_owner'] == $user->data['id']) ? 1 : 0;
					$ally_ranks_new[$id]['bewerbungen']				= (isset($_POST['u' . $id . 'r2'])) ? 1 : 0;
					$ally_ranks_new[$id]['memberlist']				= (isset($_POST['u' . $id . 'r3'])) ? 1 : 0;
					$ally_ranks_new[$id]['bewerbungenbearbeiten']	= (isset($_POST['u' . $id . 'r4'])) ? 1 : 0;
					$ally_ranks_new[$id]['administrieren']			= (isset($_POST['u' . $id . 'r5'])) ? 1 : 0;
					$ally_ranks_new[$id]['onlinestatus']			= (isset($_POST['u' . $id . 'r6'])) ? 1 : 0;
					$ally_ranks_new[$id]['mails']					= (isset($_POST['u' . $id . 'r7'])) ? 1 : 0;
					$ally_ranks_new[$id]['rechtehand']				= (isset($_POST['u' . $id . 'r8'])) ? 1 : 0;
					$ally_ranks_new[$id]['diplomacy']				= (isset($_POST['u' . $id . 'r9'])) ? 1 : 0;
				}
				$ally_ranks = $ally_ranks_new;
				$ranks = json_encode($ally_ranks);

				db::query("UPDATE {{table}} SET `ally_ranks` = '".addslashes($ranks)."' WHERE `id` = ".$ally['id'], "alliance");

			}
            elseif (isset($d) && isset($ally_ranks[$d]))
            {
				unset($ally_ranks[$d]);
				$ally['ally_rank'] = json_encode($ally_ranks);

				db::query("UPDATE {{table}} SET `ally_ranks` = '".addslashes($ally['ally_rank'])."' WHERE `id` = ".$ally['id'], "alliance");
			}

			$lang['list'] = array();

			$i = 0;

			if (count($ally_ranks) > 0) {
				foreach($ally_ranks as $a => $b) {
					$list['id'] 	= $a;
					$list['delete'] = "<a href=\"?set=alliance&mode=admin&edit=rights&d={$a}\"><img src=\"".$dpath."pic/abort.gif\" alt=\"Удалить ранг\" border=0></a>";
					$list['r0'] 	= $b['name'];
					$list['a'] 		= $a;

					if ($b['delete'] == 1 || $ally['ally_owner'] == $user->data['id'])
						$list['r1'] = "<input type=checkbox name=\"u{$a}r0\"" . (($b['delete'] == 1)?' checked="checked"':'') . ">";
					else
						$list['r1'] = "<b>-</b>";

					if ($b['kick'] == 1 || $ally['ally_owner'] == $user->data['id'])
						$list['r2'] = "<input type=checkbox name=\"u{$a}r1\"" . (($b['kick'] == 1)?' checked="checked"':'') . ">";
					else
						$list['r2'] = "<b>-</b>";

					$list['r3'] = "<input type=checkbox name=\"u{$a}r2\"" . (($b['bewerbungen'] == 1)?' checked="checked"':'') . ">";
					$list['r4'] = "<input type=checkbox name=\"u{$a}r3\"" . (($b['memberlist'] == 1)?' checked="checked"':'') . ">";
					$list['r5'] = "<input type=checkbox name=\"u{$a}r4\"" . (($b['bewerbungenbearbeiten'] == 1)?' checked="checked"':'') . ">";
					$list['r6'] = "<input type=checkbox name=\"u{$a}r5\"" . (($b['administrieren'] == 1)?' checked="checked"':'') . ">";
					$list['r7'] = "<input type=checkbox name=\"u{$a}r6\"" . (($b['onlinestatus'] == 1)?' checked="checked"':'') . ">";
					$list['r8'] = "<input type=checkbox name=\"u{$a}r7\"" . (($b['mails'] == 1)?' checked="checked"':'') . ">";
					$list['r9'] = "<input type=checkbox name=\"u{$a}r8\"" . (($b['rechtehand'] == 1)?' checked="checked"':'') . ">";
					$list['r10']= "<input type=checkbox name=\"u{$a}r9\"" . (($b['diplomacy'] == 1)?' checked="checked"':'') . ">";

					$lang['list'][] = $list;
				}
			}

			$Display->addTemplate('laws', 'alliance_laws.php');
			$Display->assign('parse', $lang, 'laws');

			display('', $lang['Law_settings'], false);
			
		} elseif ($edit == 'ally') {

			if ($ally['ally_owner'] != $user->data['id'] && !$user_admin) {
				message($lang['Denied_access'], "Меню управления альянсом");
			}

			if ($t != 1 && $t != 2 && $t != 3) {
				$t = 1;
			}

			if (isset($_POST['options'])) {
				$ally['ally_owner_range']       = db::escape_string(htmlspecialchars(strip_tags($_POST['owner_range'])));
				$ally['ally_web']               = db::escape_string(htmlspecialchars(strip_tags($_POST['web'])));
				$ally['ally_image']             = db::escape_string(htmlspecialchars(strip_tags($_POST['image'])));
				$ally['ally_request_notallow']  = intval($_POST['request_notallow']);

				if ($ally['ally_request_notallow'] != 0 && $ally['ally_request_notallow'] != 1) {
					message("Недопустимое значение атрибута!", "Ошибка");
				}

				db::query("UPDATE {{table}} SET `ally_owner_range`='".$ally['ally_owner_range']."', `ally_image`='".$ally['ally_image']."', `ally_web`='".$ally['ally_web']."', `ally_request_notallow`='".$ally['ally_request_notallow']."' WHERE `id`='".$ally['id']."'", "alliance");

			} elseif (isset($_POST['t']))
            {
				if ($t == 3)
                {
					$ally['ally_request'] = system::FormatText($_POST['text']);
					db::query("UPDATE {{table}} SET `ally_request`='".$ally['ally_request']."' WHERE `id`='".$ally['id']."'", "alliance");
				}
                elseif ($t == 2)
                {
					$ally['ally_text'] = system::FormatText($_POST['text']);
					db::query("UPDATE {{table}} SET `ally_text`='".$ally['ally_text']."' WHERE `id`='".$ally['id']."'", "alliance");
				}
                else
                {
					$ally['ally_description'] = system::FormatText($_POST['text']);
					db::query("UPDATE {{table}} SET `ally_description`='" . $ally['ally_description'] . "' WHERE `id`='".$ally['id']."'", "alliance");
				}
			}

			if ($t == 3) {
				$lang['text'] = $ally['ally_request'];
				$lang['Show_of_request_text'] = "Текст заявок альянса";
			} elseif ($t == 2) {
				$lang['text'] = $ally['ally_text'];
				$lang['Show_of_request_text'] = "Внутренний текст альянса";
			} else {
				$lang['text'] = $ally['ally_description'];
			}

			$lang['t'] 							= $t;
			$lang['ally_web'] 					= $ally['ally_web'];
			$lang['ally_image'] 				= $ally['ally_image'];
			$lang['ally_request_notallow_0'] 	= ($ally['ally_request_notallow'] == 1) ? ' SELECTED' : '';
			$lang['ally_request_notallow_1'] 	= ($ally['ally_request_notallow'] == 0) ? ' SELECTED' : '';
			$lang['ally_owner_range'] 			= $ally['ally_owner_range'];
			$lang['Transfer_alliance'] 			= MessageForm("Покинуть / Передать альянс", "", "?set=alliance&mode=admin&edit=give", 'Продолжить');
			$lang['Disolve_alliance'] 			= MessageForm("Расформировать альянс", "", "?set=alliance&mode=admin&edit=exit", 'Продолжить');

			$Display->addTemplate('admin', 'alliance_admin.php');
			$Display->assign('parse', $lang, 'admin');

			display('', $lang['Alliance_admin'], false);

		} elseif ($edit == 'requests') {

			if ($ally['ally_owner'] != $user->data['id'] && !$user_bewerbungen_bearbeiten) {
				message($lang['Denied_access'], $lang['Check_the_requests']);
			}

			if (isset($_POST['action']) && $_POST['action'] == "Принять") {
				if ($_POST['text'] != ''){
					$text_ot = db::escape_string(strip_tags($_POST['text']));
				}

				$check_req = db::query("SELECT a_id FROM {{table}} WHERE a_id = ".$ally['id']." AND u_id = ".intval($show).";", "alliance_requests", true);

				if (isset($check_req['a_id'])) {

					db::query("INSERT INTO {{table}} (a_id, u_id, time) VALUES (".$ally['id'].", ".intval($show).", ".time().")", "alliance_members");
					db::query("DELETE FROM {{table}} WHERE u_id = ".intval($show).";", "alliance_requests");
					db::query("UPDATE {{table}} SET ally_members = ally_members + 1 WHERE id='".$ally['id']."'", 'alliance');
					db::query("UPDATE {{table}} SET ally_name = '".$ally['ally_name']."', ally_id = '".$ally['id']."', new_message = new_message + 1 WHERE id = '".intval($show)."'", 'users');
					db::query("INSERT INTO {{table}} SET `message_owner`='".intval($show)."', `message_sender`='".$user->data['id']."' , `message_time`='".time()."', `message_type`='2', `message_from`='{$ally['ally_tag']}', `message_text`='Привет!<br>Альянс <b>" . $ally['ally_name'] . "</b> принял вас в свои ряды!".((isset($text_ot)) ? "<br>Приветствие:<br>".$text_ot."" : "")."'", "messages");
				}

			} elseif (isset($_POST['action']) && $_POST['action'] == "Отклонить") {
				if ($_POST['text'] != ''){
					$text_ot = db::escape_string(strip_tags($_POST['text']));
				}

				db::query("DELETE FROM {{table}} WHERE u_id = ".intval($show)." AND a_id = ".$ally['id'].";", "alliance_requests");
				db::query("INSERT INTO {{table}} SET `message_owner`='".intval($show)."', `message_sender`='".$user->data['id']."' , `message_time`='".time()."', `message_type`='2', `message_from`='{$ally['ally_tag']}', `message_text`='Привет!<br>Альянс <b>" . $ally['ally_name'] . "</b> отклонил вашу кандидатуру!".((isset($text_ot)) ? "<br>Причина:<br>".$text_ot."" : "")."'", "messages");
			}

			$parse = $lang;
			$parse['list'] = array();

			$query = db::query("SELECT u.id, u.username, r.* FROM {{table}}alliance_requests r LEFT JOIN {{table}}users u ON u.id = r.u_id WHERE a_id = '".$ally['id']."'", '');

			while ($r = db::fetch_assoc($query)) {

				if (isset($show) && $r['id'] == $show) {
					$s['username'] = $r['username'];
					$s['ally_request_text'] = nl2br($r['request']);
					$s['id'] = $r['id'];
				}

				$r['time'] = datezone("Y-m-d H:i:s", $r['time']);

				$parse['list'][] = $r;
			}

			if (isset($show) && $show != 0 && count($parse['list']) > 0) {
				$parse['request'] = $s;
			} else {
				$parse['request'] = '';
			}

			$parse['ally_tag'] = $ally['ally_tag'];

			$Display->addTemplate('request', 'alliance_requests.php');
			$Display->assign('parse', $parse, 'request');

			display('', $lang['Check_the_requests'], false);

		} elseif ($edit == 'name') {

			if ($ally['ally_owner'] != $user->data['id'] && !$user_admin) {
				message($lang['Denied_access'], $lang['Members_list']);
			}

			if (isset($_POST['newname'])) {
				if (!preg_match("/^[a-zA-Zа-яА-Я0-9_\.\,\-\!\?\*\ ]+$/u", $_POST['newname'])){
					message("Название альянса содержит запрещённые символы", $lang['make_alliance']);
				}
				$ally['ally_name'] = addslashes(htmlspecialchars($_POST['newname']));
				db::query("UPDATE {{table}} SET `ally_name` = '". $ally['ally_name'] ."' WHERE `id` = '". $user->data['ally_id'] ."';", 'alliance');
				db::query("UPDATE {{table}} SET `ally_name` = '". $ally['ally_name'] ."' WHERE `ally_id` = '". $ally['id'] ."';", 'users');
			}

			$parse['question']           = 'Введите новое название альянса';
			$parse['name']               = 'newname';
			$parse['form']               = 'name';

			$Display->addTemplate('rename', 'alliance_rename.php');
			$Display->assign('parse', $parse, 'rename');

			display('', 'Управление альянсом', false);

		} elseif ($edit == 'tag') {

			if ($ally['ally_owner'] != $user->data['id'] && !$user_admin) {
				message($lang['Denied_access'], $lang['Members_list']);
			}

			if (isset($_POST['newtag'])) {
				if (!preg_match('/^[a-zA-Zа-яА-Я0-9_\.\,\-\!\?\*\ ]+$/u', $_POST['newtag'])){
					message("Абревиатура альянса содержит запрещённые символы", $lang['make_alliance']);
				}
				$ally['ally_tag'] = addslashes(htmlspecialchars($_POST['newtag']));
				db::query("UPDATE {{table}} SET `ally_tag` = '". $ally['ally_tag'] ."' WHERE `id` = '". $user->data['ally_id'] ."';", 'alliance');
			}

			$parse['question']           = 'Введите новую аббревиатуру альянса';
			$parse['name']               = 'newtag';
			$parse['form']               = 'tag';

			$Display->addTemplate('rename', 'alliance_rename.php');
			$Display->assign('parse', $parse, 'rename');

			display('', 'Управление альянсом', false);

		} elseif ($edit == 'exit') {

			if ($ally['ally_owner'] != $user->data['id'] && !$user_can_exit_alliance) {
				message($lang['Denied_access'], $lang['Members_list']);
			}

			db::query("UPDATE {{table}} SET `ally_id` = '0', `ally_name` = '' WHERE ally_id = '".$ally['id']."'", "users");
			db::query("DELETE FROM {{table}} WHERE id = '".$ally['id']."'", "alliance");
			db::query("DELETE FROM {{table}} WHERE a_id = '".$ally['id']."'", "alliance_members");
			db::query("DELETE FROM {{table}} WHERE a_id = '".$ally['id']."'", "alliance_requests");
			db::query("DELETE FROM {{table}} WHERE a_id = '".$ally['id']."' OR d_id = '".$ally['id']."'", "alliance_diplomacy");

			header('Location: ?set=alliance');
			die();

		} elseif ($edit == 'give') {

			if ($ally['ally_owner'] != $user->data['id']){
				message("Доступ запрещён.", "Ошибка!", "?set=alliance",2);
			}

			if (isset($_POST['newleader']) && $ally['ally_owner'] == $user->data['id']){

				$info = db::query("SELECT id, ally_id FROM {{table}} WHERE id = '".intval($_POST['newleader'])."'", "users", true);

				if (!$info['id'] || $info['ally_id'] != $user->data['ally_id'])
					message("Операция невозможна.", "Ошибка!", "?set=alliance", 2);

				db::query("UPDATE {{table}} SET `ally_owner` = '".$info['id']."' WHERE `id` = ".$user->data['ally_id']." ", 'alliance');
				db::query("UPDATE {{table}} SET `rank` = '0' WHERE `u_id` = '".$info['id']."';", 'alliance_members');

				header('Location: ?set=alliance');
				die;
			}

			$listuser = db::query("SELECT u.username, u.id, m.rank FROM {{table}}users u LEFT JOIN {{table}}alliance_members m ON m.u_id = u.id WHERE u.ally_id = '".$user->data['ally_id']."' AND u.id != ".$ally['ally_owner']." AND m.rank != 0;", '');

			$parse['righthand'] = '';

			while ($u = db::fetch_assoc($listuser)) {
				if ($ally_ranks[$u['rank']-1]['rechtehand'] == 1) {
					$parse['righthand'] .= "<option value=\"" .$u['id']."\">".$u['username']."&nbsp;[".$ally_ranks[$u['rank']-1]['name']."]&nbsp;&nbsp;</option>";
				}
			}

			$parse['id'] = $user->data['id'];

			$Display->addTemplate('transfer', 'alliance_transfer.php');
			$Display->assign('parse', $parse, 'transfer');

			display('', "Передача альянса", false);
		}
	}

    if ($ally['ally_owner'] == $user->data['id']) {
        $range = ($ally['ally_owner_range'] == '') ? 'Основатель' : $ally['ally_owner_range'];
    } elseif ($ally_member['rank'] != 0 && isset($ally_ranks[$ally_member['rank']-1]['name'])) {
        $range = $ally_ranks[$ally_member['rank']-1]['name'];
    } else {
        $range = $lang['member'];
    }

    if ($user_diplomacy) {
        $qq = db::query("SELECT count(id) AS cc FROM {{table}} WHERE d_id = ".$ally['id']." AND status = 0", "alliance_diplomacy", true);
        if ($qq['cc'] > 0)
            $lang['ally_dipl'] = " <a href=\"?set=alliance&mode=diplo\">Просмотр</a> (".$qq['cc']." новых запросов)";
        else
            $lang['ally_dipl'] = " <a href=\"?set=alliance&mode=diplo\">Просмотр</a>";
    }

    $lang['requests'] = '';
    $request = db::query("SELECT COUNT(*) AS num FROM {{table}} WHERE a_id = '".$ally['id']."'", 'alliance_requests', true);
    $request = $request['num'];
    if ($request != 0) {
        if ($ally['ally_owner'] == $user->data['id'] || $ally_ranks[$ally_member['rank']-1]['bewerbungen'] != 0)
            $lang['requests'] = "<tr><th>Заявки</th><th><a href=\"?set=alliance&mode=admin&edit=requests\">".$request." заявок</a></th></tr>";
    }

	$lang['alliance_admin'] 	= ($user_admin) ? '(<a href="?set=alliance&mode=admin&edit=ally">управление альянсом</a>)' : '';
    $lang['send_circular_mail'] = ($user_can_send_mails) ? '<tr><th>Альянс чат ('.$user->data['mnl_alliance'].' новых)</th><th><a href="?set=alliance&mode=circular">Войти в чат</a></th></tr>' : '';
	$lang['members_list'] 		= ($user_can_watch_memberlist) ? ' (<a href="?set=alliance&mode=memberslist">список</a>)' : '';
    $lang['ally_owner'] 		= ($ally['ally_owner'] != $user->data['id']) ? MessageForm($lang['Exit_of_this_alliance'], "", "?set=alliance&mode=exit", $lang['Continue']) : '';
    $lang['ally_image'] 		= ($ally['ally_image'] != '') ? "<tr><th colspan=2><img src=\"".$ally['ally_image']."\" style=\"max-width:650px\"></th></tr>" : '';
    $lang['range'] 				= $range;
    $lang['ally_description'] 	= $ally['ally_description'];
    $lang['ally_text'] 			= $ally['ally_text'];
    $lang['ally_web'] 			= $ally['ally_web'];
    $lang['ally_tag'] 			= $ally['ally_tag'];
    $lang['ally_members'] 		= $ally['ally_members'];
    $lang['ally_name'] 			= $ally['ally_name'];

    $Display->addTemplate('frontpage', 'alliance_frontpage.php');
    $Display->assign('parse', $lang, 'frontpage');

    display('', 'Ваш альянс', false);
}

}

?>
