<?php

if(!defined("INSIDE")) die("attemp hacking");

if ($user->data['authlevel'] >= 2) {


	$query = db::query("SELECT a.`id`, a.`ally_name`, a.`ally_tag`,  a.`ally_owner`, a.`ally_register_time`, a.`ally_description`, a.`ally_text`, a.`ally_members`, u.`username` FROM {{table}}alliance a, {{table}}users u WHERE u.id = a.ally_owner", "");

	$i = 0;
    $parse = array();
    $parse['alliance'] = '';
    $parse['allianz'] = '';

	while ($u = db::fetch_assoc($query)) {

		$leader = $u['username'];

		$ally_register_time = date ( "d/m/Y H:i:s", $u['ally_register_time']);
			
		$parse['alliance'] .= "<tr>"
			. "<td class=b><center><b>" . $u['id'] . "</center></b></td>"
			. "<td class=b><center><b><a href=?set=admin&mode=alliancelist&allyname=" . $u['id'] . ">" . $u['ally_name'] . "</a></center></b></td>"
			. "<td class=b><center><b><a href=?set=admin&mode=alliancelist&allyname=" . $u['id'] . ">" . $u['ally_tag'] . "</a></center></b></td>"
			. "<td class=b><center><b><a href=?set=admin&mode=alliancelist&leader=" . $u['id'] . "><b>" . $leader . "</center></b></a></td>"
			. "<td class=b><center><b>" . $ally_register_time . "</center></b></td>"
			. "<td class=b><center><b><a href=?set=admin&mode=alliancelist&desc=" . $u['id'] . ">Смотреть</a>/<a href=?set=admin&mode=alliancelist&edit=" . $u['id'] . ">Редактировать</a></center></b></td>"
			. "<td class=b><center><b><a href=?set=admin&mode=alliancelist&mitglieder=". $u['id'] .">" . $u['ally_members'] . "</a></center></b></td>"
			. "<td class=b><center><b><a href=?set=admin&mode=alliancelist&mail=" . $u['id'] . "><img src=../images/r5.png></a></center></b></td>"
			. "<td class=b><center><b><a href=?set=admin&mode=alliancelist&del=" . $u['id'] . ">X</a></center></b></td>"
		. "</tr>";
		
		$i++; 
	}
	if ($i == "1")
		$parse['allianz'] .= "<tr><th class=b colspan=9>Существующие альянсы</th></tr>";
	else
		$parse['allianz'] .= "<tr><th class=b colspan=9>Всего {$i} альянса</th></tr>";

			if (isset($_GET['desc'])) {
	
		$ally_id = intval($_GET['desc']);
		$info = db::query("SELECT `ally_description` FROM {{table}} WHERE id='". $ally_id ."'", "alliance");
				$ally_text = db::fetch_assoc($info);
	
		$parse['desc'] = "<tr>"
				. "<th colspan=9>Описание альянса</th></tr>"
				. "<tr>"
			. "<td class=b colspan=9><center><b>" . $ally_text['ally_description'] . "</center></b></td>"
			. "</tr>";
	}
	
	if (isset($_GET['edit'])) {	
			
		$ally_id = intval($_GET['edit']);
		$info = db::query("SELECT `ally_description` FROM {{table}} WHERE id='". $ally_id ."'", "alliance");
			$ally_text = db::fetch_assoc($info);
		
		$parse['desc'] = "<tr>"
				. "<th colspan=9>Реактирование описание альянса</th></tr>"
				. "<tr>"
				. "<form action=?set=admin&mode=alliancelist&edit=" . $ally_id  . " method=POST>"
			. "<td class=b colspan=9><center><b><textarea name=desc cols=50 rows=10 >" . $ally_text['ally_description'] . "</textarea></center></b></td>"
			. "</tr>"		    
			. "<tr>"
			. "<td class=b colspan=9><center><b><input type=submit value=Speichern></center></b></td>"
				. "</form></tr>";
	
		if (isset($_POST['desc'])) {
			$query = db::query("UPDATE {{table}} SET `ally_description` = '". addslashes($_POST['desc']) ."' WHERE `id` = '" . intval($_GET['edit']) . "'",'alliance');
			message ('<meta http-equiv="refresh" content="1; url=alliancelist.php">Редактирование описания альянса', 'Выполняется. Подождите.');
		} 
	}
	
	
	if(isset($_GET['allyname'])) {
		$ally_id = intval($_GET['allyname']);
		$query = db::query("SELECT `ally_image`, `ally_web`, `ally_name`, `ally_tag` FROM {{table}} WHERE `id` = '". intval($_GET['allyname']) ."'", "alliance");
				$u = db::fetch_assoc($query);
	
		$parse['name'] = "<tr>"
				. "<td colspan=9 class=c>Название / обозначение / лого / сайт</td></tr>"
				. "<form action=?set=admin&mode=alliancelist&allyname=" . $ally_id  . " method=POST>"
				. "<tr>"
			. "<th colspan=4><center><b>Название альянса</center></b></th>   <th colspan=5><center><b><input type=text size=38 name=name value=".$u['ally_name']."></center></b></th>"
			. "</tr>"	
				. "<tr>"
			. "<th colspan=4><center><b>Обозначение</center></b></th>   <th colspan=5><center><b><input type=text size=38 name=tag value=".$u['ally_tag']."></center></b></th>"
			. "</tr>"	
				. "<tr>"
			. "<th colspan=3><center><b>Логотип альянса</center></b></th>   <th colspan=3><center><b><input type=text size=38 name=image value=".$u['ally_image']."></center></b></th>  <th colspan=3><center><b><a href=". $u['ally_image'] .">Смотреть</a></center></b></th>"
			. "</tr>"
				. "<tr>"
			. "<th colspan=3><center><b>Сайт альянса</center></b></th>   <th colspan=3><center><b><input type=text size=38 name=web value=".$u['ally_web']."></center></b></th>  <th colspan=3><center><b><a href=". $u['ally_web'] .">Смотреть</a></center></b></th>"
			. "</tr>"			
			. "<tr>"
			. "<td class=b colspan=9><center><b><input type=submit value=Сохранить></center></b></td>"
				. "</form></tr>";
	
		if(isset($_POST['name'])) {
			$query = db::query("UPDATE {{table}} SET `ally_name` = '". addslashes($_POST['name']) ."', `ally_tag` = '". addslashes($_POST['tag']) ."', `ally_image` = '". addslashes($_POST['image']) ."', `ally_web` = '". addslashes($_POST['web']) ."' WHERE `id` = '" . intval($_GET['allyname']) . "'",'alliance');
			message ('<meta http-equiv="refresh" content="1; url=alliancelist.php">Редактирование альянса', 'Выполняется. Подождите.');
		}
		
	}
	/*
	if(isset($_GET['del'])) {
		$ally_id = intval($_GET['del']);
				
		$parse['name'] .= "<tr>"
				. "<th colspan=9>Удаление альянса</th></tr>"
				. "<form action=alliancelist.php?del=" . $ally_id  . " method=POST>"
				. "<tr>"
			. "<th colspan=9><center><b>Является ли твое решение обдуманным и окончательным?<br>Подумай еще раз, обратного пути уже не будет! </b></center></b></th>"
			. "</tr>"	
			. "<td class=b colspan=9><center><b><input type=submit value=Удалить name=del></center></b></td>"
				. "</form></tr>";
	
		if(isset($_POST['del'])) {
			db::query("DELETE FROM {{table}} WHERE id = '" . intval($_GET['del']) . "'",'alliance');
					db::query("UPDATE {{table}} SET `ally_id`=0, `ally_name` = '' WHERE `ally_id`='".intval($_GET['del'])."'", "users");
			AdminMessage ('<meta http-equiv="refresh" content="1; url=alliancelist.php">Удаление альянса', 'Выполняется. Подождите.');
			}
	}
	*/

		if (isset($_GET['mitglieder'])) {
		    $ally_id = intval($_GET['mitglieder']);
 
		    $users = db::query("SELECT `id`, `username` FROM {{table}} WHERE ally_id='". $ally_id ."'", "users");

            $parse['member_row'] = '';
 
			$i = 0;
            while ($u = db::fetch_assoc($users)) {
                $parse['member_row'] .= "<tr>"
                . "<td class=b colspan=2><center><b>" . $u['id'] . "</center></b></td>"
                . "<td class=b  colspan=5><center><b><a href=?set=messages&mode=write&id=" . $u['id'] . ">". $u['username'] ."</a></center></b></td>"
                . "<td class=b  colspan=2><center><b><a href=?set=admin&mode=alliancelist&ent=". $u['id'] ."> X </a></center></b></td>"
                . "</tr>";
                $i++;
            }
	}
 
	if(isset($_GET['ent'])) {
		$user_id = intval($_GET['ent']);
 
		$parse['name'] .= "<tr>"
		. "<th colspan=9>Удаление участника из альянса</th></tr>"
		. "<form action=?set=admin&mode=alliancelist&ent=" . $user_id  . " method=POST>"
		. "<tr>"
		. "<th colspan=9><center><b>После нажатия кнопки Удалить, выбранный вами участник выйдет из альянса. <br>Ты действительно хочешь это сделать?</center></b></th>"
		. "</tr>"	
		. "<td class=b colspan=9><center><b><input type=submit value=Удалить name=ent></center></b></td>"
			. "</form></tr>";
	
		if (isset($_POST['ent'])) {
			$user_id = $_GET['ent'];
					db::query("UPDATE {{table}} SET `ally_id`=0, `ally_name` = '' WHERE `id`='".$user_id."'", "users");
			message ('<meta http-equiv="refresh" content="1; url=alliancelist.php">Удаление участника из альянса', 'Выполняется. Подождите.');
				}
 
	}
 
	if(isset($_GET['mail'])) {
		$ally_id = $_GET['mail'];
	
		$parse['mail'] = "<tr>"
		. "<th colspan=9>Собщение участникам альянса</th></tr>"
		. "<tr>"
		. "<form action=?set=admin&mode=alliancelist&mail=" . $ally_id  . " method=POST>"
		. "<tr>"		
		. "<td class=b colspan=9><center><b><textarea name=text cols=50 rows=10 ></textarea></center></b></td>"
		. "</tr>"		    
		. "<tr>"
		. "<td class=b colspan=9><center><b><input type=submit value=Отправить></center></b></td>"
		. "</form></tr>";
	
				if(isset($_POST['text'])) {
			$ally_id = intval($_GET['mail']);
					$sq = db::query("SELECT id FROM {{table}} WHERE ally_id='". $ally_id ."'", "users");
								while ($u = db::fetch_array($sq)) {
										db::query("INSERT INTO {{table}} SET
										`message_owner`='{$u['id']}',
										`message_sender`='Администрация' ,
										`message_time`='" . time() . "',
										`message_type`='2',
										`message_from`='Сообщение альянса (Admin)',
										`message_text`='". addslashes($_POST['text']) ."'
										", "messages");
								}
			message ('<meta http-equiv="refresh" content="1; url=?set=admin&mode=alliancelist">Сообщение отправляетсяt!', 'Подождите.');
		}		
	}
					
	if(isset($_GET['leader'])) {
		$ally_id = intval($_GET['leader']);
	
		$query = db::query("SELECT `ally_owner` FROM {{table}}", "alliance");
		$u = db::fetch_array($query);
		$users = db::query("SELECT `username` FROM {{table}} WHERE id='". $u['ally_owner'] ."'", "users");
		$a = db::fetch_array($users);
		$leader = $a['username'];
	
		$parse['leader'] = "<tr>"
			. "<td colspan=9 class=c>Смена лидера альянса</td></tr>"
			. "<form action=?set=admin&mode=alliancelist&leader=" . $ally_id  . " method=POST>"
			. "<tr>"
		. "<th colspan=4><center><b>Сейчас лидер:</center></b></th>   <th colspan=5><center><b>$leader</center></b></th>"
		. "</tr>"	
			. "<tr>"
		. "<th colspan=4><center><b><u>ID</u> нового лидера</center></b></th>   <th colspan=5><center><b><input type=text size=8 name=leader></center></b></th>"
		. "</tr>"		
		. "<tr>"
		. "<td class=b colspan=9><center><b><input type=submit value=Сохранить></center></b></td>"
			. "</form></tr>";
	
		if(isset($_POST['leader'])) {
			$sq = db::query("SELECT ally_id FROM {{table}} WHERE id='". intval($_POST['leader']) ."'", "users");
			$a = db::fetch_array($sq);

			if($a['ally_id'] == $_GET['leader']) {
						$query = db::query("UPDATE {{table}} SET `ally_owner` = '". intval($_POST['leader']) ."' WHERE `id` = '" . intval($_GET['leader']) . "'",'alliance');
				message ('<meta http-equiv="refresh" content="1; url=?set=admin&mode=alliancelist">Смена лидера альянса!', 'Успешно');
			} else {
				message ('<meta http-equiv="refresh" content="1; url=?set=admin&mode=alliancelist">Пользователь не находиться в этом союзе!', 'Ошибка');
					}
		}
	}

    $Display->addTemplate('list', 'admin/alliance.php');
    $Display->assign('parse', $parse, 'list');
 
	display('', 'Список альянсов', false, true);

} else {
	message( $lang['sys_noalloaw'], $lang['sys_noaccess'] );
}
?>
