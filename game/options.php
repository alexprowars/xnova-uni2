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

system::includeLang('options');
   
$inf = db::query("SELECT * FROM {{table}} WHERE id = ".$user->data['id']."", "users_inf", true);

$mode = (isset($_GET['mode'])) ? $_GET['mode'] : '';

if ($mode == 'changeemail')
{
	if (isset($_POST['db_password']) && isset($_POST['email']))
    {
		if (md5($_POST["db_password"]) != $inf["password"])
            message('Heпpaвильный тeкyщий пapoль', 'Hacтpoйки', '?set=options&mode=changeemail', 3);
        else
        {
			$email = db::query("SELECT user_id FROM {{table}} WHERE user_id = ".$user->data['id']." AND ok = 0;", "log_email", true);

			if (isset($email['user_id'])) {
				message('Заявка была отправлена ранее и ожидает модерации.', 'Hacтpoйки', '?set=options', 3);
			} else {
				$email = db::query("SELECT id FROM {{table}} WHERE email = '".addslashes(htmlspecialchars($_POST['email']))."';", "users_inf", true);
				
				if (!isset($email['id'])) {
					db::query("INSERT INTO {{table}} VALUES (".$user->data['id'].", ".time().", '".addslashes(htmlspecialchars($_POST['email']))."', 0);", "log_email");
					message('Заявка отправлена на рассмотрение', 'Hacтpoйки', '?set=options', 3);
				} else
					message('Данный email уже используется в игре.', 'Hacтpoйки', '?set=options', 3);
			}
		}
	}

	$Display->addTemplate('options', 'options_email.php');

    display('', 'Hacтpoйки', false);
}

if ($_POST && $mode == "change")
{
	if (isset($_POST["db_character"]) && $_POST["db_character"] != '' && $_POST["db_character"] != $user->data['username'])
    {
		$_POST["db_character"] = preg_replace("/([\s\x{0}\x{0B}]+)/iu", " ", trim($_POST["db_character"]));
		
		if (preg_match("/^[А-Яа-яЁёa-zA-Z0-9_\-\!\~\.@ ]+$/u", $_POST['db_character']))
			$username = addslashes($_POST['db_character']);
		else
			$username = $user->data['username'];
	}
    else
		$username = $user->data['username'];

	$design 	= (isset($_POST["design"]) && $_POST["design"] == 'on') ? 1 : 0;
	$records 	= (isset($_POST["records"]) && $_POST["records"] == 'on') ? 1 : 0;
    $dpath 		= (isset($_POST["skin"]) && $_POST["skin"] == 'on') ? 1 : 0;
	$security 	= (isset($_POST["security"]) && $_POST["security"] == 'on') ? 1 : 0;
	$bbcode 	= (isset($_POST["bbcode"]) && $_POST["bbcode"] == 'on') ? 1 : 0;
	$icq 		= (isset($_POST["icq"]) && $_POST["icq"] != '') ? intval($_POST['icq']) : $inf['icq'];
	$vkontakte 	= (isset($_POST["vkontakte"]) && $_POST["vkontakte"] != '') ? intval($_POST['vkontakte']) : $inf['vkontakte'];
	$sex 		= ($_POST['sex'] == 'F') ? 2 : 1;

	$color = intval($_POST['color']);
	if ($color < 1 || $color > 13) $color = 1;

	$timezone = intval($_POST['timezone']);
	if ($timezone < -32 || $timezone > 16) $timezone = 0;

	if ($user->data['urlaubs_modus_time'] > time())
    {
		$urlaubs_modus_time = $user->data['urlaubs_modus_time'];
	}
    else
    {
		$urlaubs_modus_time = 0;

		if (isset($_POST["urlaubs_modus"]) && $_POST["urlaubs_modus"] == 'on')
        {
			$BuildOnPlanet = db::query("SELECT `id` FROM {{table}} WHERE (`b_building` != 0 OR `b_tech` != 0) AND `id_owner` = '".$user->data['id']."'", "planets");
			$UserFlyingFleets = db::query("SELECT `fleet_id` FROM {{table}} WHERE `fleet_owner` = '".$user->data['id']."'", "fleets");
			if (db::num_rows($BuildOnPlanet) > 0)
            {
				message('Heвoзмoжнo включить peжим oтпycкa. Для включeния y вac нe дoлжнo идти cтpoитeльcтвo или иccлeдoвaниe нa плaнeтe.', "Oшибкa", "?set=overview", 5);
			}
            elseif (db::num_rows($UserFlyingFleets) > 0)
            {
				message('Heвoзмoжнo включить peжим oтпycкa. Для включeния y вac нe дoлжeн нaxoдитьcя флoт в пoлeтe.', "Oшибкa", "?set=overview", 5);
			}
            else
            {
				if($user->data['urlaubs_modus_time'] == 0)
                {
					$urlaubs_modus_time = time() + 172800;
				}
                else
                {
					$urlaubs_modus_time = $user->data['urlaubs_modus_time'];
				}
				db::query("UPDATE {{table}} SET `metal_mine_porcent` = '0', `crystal_mine_porcent` = '0', `deuterium_sintetizer_porcent` = '0', `solar_plant_porcent` = '0', `fusion_plant_porcent` = '0', `solar_satelit_porcent` = '0' WHERE `id_owner` = '".$user->data['id']."'", "planets");
			}
		}
	}

   	$Del_Time 	= (isset($_POST["db_deaktjava"]) && $_POST["db_deaktjava"] == 'on') ? (time() + 604800) : 0;
   	$SetSort  	= intval($_POST['settings_sort']);
   	$SetOrder 	= intval($_POST['settings_order']);
	$about 		= system::FormatText($_POST['text']);

    if ($user->data['urlaubs_modus_time'] == 0)
    {
        db::query("UPDATE {{table}} SET sex = '".$sex."', `urlaubs_modus_time` = '".$urlaubs_modus_time."', `deltime` = '".$Del_Time."' WHERE `id` = '".$user->data['id']."'", "users");

		$ui_query = '';

		if ($icq != $inf['icq']) {
			$ui_query .= ", `icq` = '".$icq."'";
		}
		if ($vkontakte != $inf['vkontakte']) {
			$ui_query .= ", `vkontakte` = '".$vkontakte."'";
		}
		if ($design != $inf['design']) {
			$ui_query .= ", `design` = '".$design."'";
		}
		if ($dpath != $inf['dpath']) {
			$ui_query .= ", `dpath` = '".$dpath."'";
		}
		if ($records != $inf['records']) {
			$ui_query .= ", `records` = '".$records."'";
		}
		if ($security != $inf['security']) {
			$ui_query .= ", `security` = '".$security."'";
		}
		if ($SetSort != $inf['planet_sort']) {
			$ui_query .= ", `planet_sort` = '".$SetSort."'";
		}
		if ($SetOrder != $inf['planet_sort_order']) {
			$ui_query .= ", `planet_sort_order` = '".$SetOrder."'";
		}
		if ($color != $inf['color']) {
			$ui_query .= ", `color` = '".$color."'";
		}
		if ($timezone != $inf['timezone']) {
			$ui_query .= ", `timezone` = '".$timezone."'";
		}
		if ($bbcode != $inf['bb_parser']) {
			$ui_query .= ", `bb_parser` = '".$bbcode."'";
		}
		if ($about != $inf['about'])
			$ui_query .= ", `about` = '".$about."'";


		if ($ui_query != '') {
			if ($ui_query != '')
				$ui_query[0] = ' ';

			db::query("UPDATE {{table}} SET".$ui_query." WHERE `id` = '".$user->data['id']."'", "users_inf");
		}

		unset($_SESSION['config']);
    } else {
        db::query("UPDATE {{table}} SET `urlaubs_modus_time` = '".$urlaubs_modus_time."', `deltime` = '".$Del_Time."' WHERE `id` = '".$user->data['id']."' LIMIT 1", "users");
    }

    if ($_POST["db_password"] != "" && $_POST["newpass1"] != "") {
        if (md5($_POST["db_password"]) != $inf["password"])
            message('Heпpaвильный тeкyщий пapoль', 'Cмeнa пapoля', '?set=options', 3);
        elseif ($_POST["newpass1"] == $_POST["newpass2"]) {
            $newpass = md5($_POST["newpass1"]);
            db::query("UPDATE {{table}} SET `password` = '".$newpass."' WHERE `id` = '".$user->data['id']."' LIMIT 1", "users_inf");

			setcookie("x_id", "", 0, "/", "uni2.xnova.su", 0);
			setcookie("x_secret", "", 0, "/", "uni2.xnova.su", 0);
			setcookie("uni", "", 0, "/", ".xnova.su", 0);
            session_destroy();

            message('Уcпeшнo', 'Cмeнa пapoля', '?set=login', 2);
        } else
            message('Bвeдeнныe пapoли нe coвпaдaют', 'Cмeнa пapoля', '?set=options', 3);
    }
    if ($user->data['username'] != $username) {
		if ($inf['username_last'] > (time() - 86400)) {
			message('Смена игрового имени возможна лишь раз в сутки.', 'Cмeнa имeни', '?set=options', 3);
		} else {
			$query = db::query("SELECT id FROM {{table}} WHERE username = '".$username."'", 'users');
			if (db::num_rows($query) == 0) {
				if (preg_match("/^[a-zA-Za-яA-Я0-9_\.\,\-\!\?\*\ ]+$/u", $username) && strlen($username) >= 5){
					db::query("UPDATE {{table}} SET username = '".$username."' WHERE id = '".$user->data['id']."' LIMIT 1", "users");
					db::query("UPDATE {{table}} SET username_last = '".time()."' WHERE id = '".$user->data['id']."' LIMIT 1", "users_inf");
					db::query("INSERT INTO {{table}} VALUES (".$user->data['id'].", ".time().", '".$username."');", "log_username");

					message('Уcпeшнo', 'Cмeнa имeни', '?set=login', 2);
				} else
					message('Дaннoe имя aккayнтa cлишкoм кopoткoe или имeeт зaпpeщeнныe cимвoлы', 'Cмeнa имeни', '?set=options', 3);
			} else
				message('Дaннoe имя aккayнтa yжe иcпoльзyeтcя в игpe', 'Cмeнa имeни', '?set=options', 3);
		}
    }

    message($lang['succeful_save'], "Hacтpoйки игpы", '?set=options', 3);
} elseif ($_POST && $mode == 'ld') {
	if (!isset($_POST['text']) || $_POST['text'] == '') {
		message('Ввведите текст сообщения', 'Ошибка', '?set=options', 3);
	} else {
		db::query("INSERT INTO {{table}} (u_id, text, time) VALUES (".$user->data['id'].", '".addslashes(htmlspecialchars($_POST['text']))."', ".time().")", "private");
		message('Запись добавлена в личное дело', 'Успешно', '?set=options', 3);
	}
} elseif ($_POST && $mode == 'vk') {
	if ($_POST['mail'] == '' || $_POST['password'] == '') {
		message('Заполните все поля', 'Ошибка', '?set=options', 3);
	} else {
		$user_inf = db::query("SELECT id, password, vk_reg_id FROM {{table}} WHERE email = '".addslashes($_POST['mail'])."';", "users_inf", true);

		if (!isset($user_inf['id']))
			message('Аккаун не существует', 'Ошибка', '?set=options', 3);
		elseif ($user_inf['vk_reg_id'] != 0)
			message('Аккаун уже привязан к странице Вконтакте', 'Ошибка', '?set=options', 3);
		elseif ($inf['vk_reg_id'] == 0)
			message('Действие не осуществимо из под обычного профиля', 'Ошибка', '?set=options', 3);
		else {
			db::query("UPDATE {{table}} SET vk_reg_id = 0 WHERE id = ".$user->data['id'].";", "users_inf");
			db::query("UPDATE {{table}} SET vk_reg_id = ".$inf['vk_reg_id']." WHERE id = ".$user_inf['id'].";", "users_inf");
			db::query("UPDATE {{table}} SET deltime = 5 WHERE id = ".$user->data['id'].";", "users");
			
			echo '<script>top.location="http://vkontakte.ru/app1798249";</script>';
			die();
		}
	}
} else {
    $parse = $lang;

    if ($user->data['urlaubs_modus_time'] > 0) {

        $parse['um_end_date']       = datezone("H:i:s d.m.Y", $user->data['urlaubs_modus_time']);
        $parse['opt_delac_data']    = ($user->data['deltime'] > 0) ? " checked='checked'/":'';
        $parse['opt_modev_data']    = ($user->data['urlaubs_modus_time'] > 0)?" checked='checked'/":'';
        $parse['opt_usern_data']    = $user->data['username'];

        $Display->addTemplate('options', 'options_um.php');
        $Display->assign('parse', $parse, 'options');

        display('', 'Hacтpoйки', false);
    } else {
        $parse['opt_lst_ord_data']   = "<option value =\"0\"". (($inf['planet_sort'] == 0) ? " selected": "") .">". $lang['opt_lst_ord0'] ."</option>";
        $parse['opt_lst_ord_data']  .= "<option value =\"1\"". (($inf['planet_sort'] == 1) ? " selected": "") .">". $lang['opt_lst_ord1'] ."</option>";
        $parse['opt_lst_ord_data']  .= "<option value =\"2\"". (($inf['planet_sort'] == 2) ? " selected": "") .">". $lang['opt_lst_ord2'] ."</option>";
		$parse['opt_lst_ord_data']  .= "<option value =\"3\"". (($inf['planet_sort'] == 3) ? " selected": "") .">Типу</option>";

        $parse['opt_lst_cla_data']   = "<option value =\"0\"". (($inf['planet_sort_order'] == 0) ? " selected": "") .">". $lang['opt_lst_cla0'] ."</option>";
        $parse['opt_lst_cla_data']  .= "<option value =\"1\"". (($inf['planet_sort_order'] == 1) ? " selected": "") .">". $lang['opt_lst_cla1'] ."</option>";

        $parse['opt_lst_color_data']  = "<option value=1 style='color:White'";
        if ($inf['color']==1) $parse['opt_lst_color_data']  .= " selected>"; else $parse['opt_lst_color_data']  .= ">"; $parse['opt_lst_color_data']  .= "Бeлый";
        $parse['opt_lst_color_data']  .= "<option value=2 style='color:navy'";
        if ($inf['color']==2) $parse['opt_lst_color_data']  .= " selected>"; else $parse['opt_lst_color_data']  .= ">"; $parse['opt_lst_color_data']  .= "Teмнocиний";
        $parse['opt_lst_color_data']  .= "<option value=3 style='color:blue'";
        if ($inf['color']==3) $parse['opt_lst_color_data']  .= " selected>"; else $parse['opt_lst_color_data']  .= ">"; $parse['opt_lst_color_data']  .= "Cиний";
        $parse['opt_lst_color_data']  .= "<option value=4 style='color:#0046D5'";
        if ($inf['color']==4) $parse['opt_lst_color_data']  .= " selected>"; else $parse['opt_lst_color_data']  .= ">"; $parse['opt_lst_color_data']  .= "Гoлyбoй";
        $parse['opt_lst_color_data']  .= "<option value=5 style='color:teal'";
        if ($inf['color']==5) $parse['opt_lst_color_data']  .= " selected>"; else $parse['opt_lst_color_data']  .= ">"; $parse['opt_lst_color_data']  .= "Mopcкoй вoлны";
        $parse['opt_lst_color_data']  .= "<option value=6 style='color:Red'";
        if ($inf['color']==6) $parse['opt_lst_color_data']  .= " selected>"; else $parse['opt_lst_color_data']  .= ">"; $parse['opt_lst_color_data']  .= "Kpacный";
        $parse['opt_lst_color_data']  .= "<option value=7 style='color:fuchsia'";
        if ($inf['color']==7) $parse['opt_lst_color_data']  .= " selected>"; else $parse['opt_lst_color_data']  .= ">"; $parse['opt_lst_color_data']  .= "Poзoвый";
        $parse['opt_lst_color_data']  .= "<option value=8 style='color:gray'";
        if ($inf['color']==8) $parse['opt_lst_color_data']  .= " selected>"; else $parse['opt_lst_color_data']  .= ">"; $parse['opt_lst_color_data']  .= "Cepый";
        $parse['opt_lst_color_data']  .= "<option value=9 style='color:green'";
        if ($inf['color']==9) $parse['opt_lst_color_data']  .= " selected>"; else $parse['opt_lst_color_data']  .= ">"; $parse['opt_lst_color_data']  .= "Зeлeный";
        $parse['opt_lst_color_data']  .= "<option value=10 style='color:maroon'";
        if ($inf['color']==10) $parse['opt_lst_color_data']  .= " selected>"; else $parse['opt_lst_color_data']  .= ">"; $parse['opt_lst_color_data']  .= "Teмнoкpacный";
        $parse['opt_lst_color_data']  .= "<option value=11 style='color:orange'";
        if ($inf['color']==11) $parse['opt_lst_color_data']  .= " selected>"; else $parse['opt_lst_color_data']  .= ">"; $parse['opt_lst_color_data']  .= "Opaнжeвый";
        $parse['opt_lst_color_data']  .= "<option value=13 style='color:darkkhaki'";
        if ($inf['color']==13) $parse['opt_lst_color_data']  .= " selected>"; else $parse['opt_lst_color_data']  .= ">"; $parse['opt_lst_color_data']  .= "Teмный xaки";

        if ($user->data['avatar'] != 0) {
			if ($user->data['avatar'] != 99)
           		$parse['avatar'] = "<img src=/images/avatars/".$user->data['avatar'].".jpg height='100'><br>";
			else
				$parse['avatar'] = "<img src=/images/avatars/upload/upload_".$user->data['id'].".jpg height='100'><br>";
		}

        if ($inf['icq'] != 0)
            $parse['opt_icq_data'] = $inf['icq'];

        $parse['opt_vkontakte_data'] = ($inf['vkontakte'] != 0) ? $inf['vkontakte'] : '';

		$parse['opt_usern_datatime']  = $inf['username_last'];
        $parse['opt_usern_data']      = $user->data['username'];
        $parse['opt_mail_data']       = $inf['email'];
		$parse['password_vk']         = $inf['password_vk'];
        $parse['opt_sec_data']        = ($inf['security'] == 1) ? " checked='checked'":'';
		$parse['opt_record_data']     = ($inf['records'] == 1) ? " checked='checked'":'';
        $parse['opt_sskin_data']      = ($inf['design'] == 1) ? " checked='checked'":'';
        $parse['opt_skin_data']       = ($inf['dpath'] == 1) ? " checked='checked'/":'';
		$parse['opt_bbcode_data']     = ($inf['bb_parser'] == 1) ? " checked='checked'/":'';
		$parse['opt_ajax_data']       = ($inf['ajax_navigation'] == 1) ? " checked='checked'/":'';
        $parse['opt_delac_data']      = ($user->data['deltime'] > 0) ? " checked='checked'/":'';
        $parse['opt_modev_data']      = ($user->data['urlaubs_modus_time'] > 0)?" checked='checked'/":'';
		$parse['sex']				  = $user->data['sex'];
		$parse['about']				  = $inf['about'];
		$parse['timezone']			  = $inf['timezone'];

        $Display->addTemplate('options', 'options.php');
        $Display->assign('parse', $parse, 'options');

        display('', 'Hacтpoйки', false);
    }
}
?>
