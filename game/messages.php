<?php

/**
 * @author AlexPro
 * @copyright 2008 - 2011 XNova Game Group
 * @var $Display HSTemplateDisplay
 * @var $lang array
 * @var $user user
 * @var $dpath string
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

if(!defined("INSIDE")) die("attemp hacking");

system::includeLang('messages');

$page  = "<br><script language=\"JavaScript\" src=\"/scripts/smiles_v2.js\"></script>\n";
$page  .= "<script language=\"JavaScript\">\n";
$page .= "function f(target_url, win_name) {\n";
$page .= "var new_win = window.open(target_url,win_name,'resizable=yes,scrollbars=yes,menubar=no,toolbar=no,width=550,height=280,top=0,left=0');\n";
$page .= "new_win.focus();\n";
$page .= "}\n";
$page .= "</script>\n";

if (isset($_GET['abuse'])) {
	$mes = db::query("SELECT * FROM {{table}} WHERE message_id = ".intval($_GET['abuse'])." AND message_owner = ".$user->data['id'].";", "messages", true);
	if (isset($mes['message_id'])) {
		$c = db::query("SELECT `id` FROM {{table}} WHERE `authlevel` != 0", "users");
		while ($cc = db::fetch_assoc($c)) {
			SendSimpleMessage ( $cc['id'], $user->data['id'], '', 1, '<font color=red>'.$user->data['username'].'</font>', 'От кого: '.$mes['message_from'].'<br>Дата отправления: '.date("d-m-Y H:i:s", $mes['message_time']).'<br>Текст сообщения: '.$mes['message_text']);
		}
		$page .= "<script>alert('Жалоба отправлена администрации игры');</script>";
	}
}

$OwnerID = (!isset($_GET['id'])) ? 0 : intval($_GET['id']);
$MessCategory  = (!isset($_POST['messcat'])) ? 100 : intval($_POST['messcat']);
$lim = (!isset($_POST['show_by']) || intval($_POST['show_by']) > 50) ? 10 : intval($_POST['show_by']);
$MessPageMode  = (!isset($_GET['id'])) ? '' : $_GET["mode"];
$start  = (!isset($_POST['start'])) ? 0 : intval($_POST["start"]);
if (isset($_POST['deletemessages'])) {
	$MessPageMode = "delete";
}

$MessageType   = array ( 0, 1, 2, 3, 4, 5, 15, 99, 100 );
$TitleColor    = array ( 0 => '#FFFF00', 1 => '#FF6699', 2 => '#FF3300', 3 => '#FF9900', 4 => '#773399', 5 => '#009933', 15 => '#030070', 99 => '#007070', 100 => '#ABABAB'  );
$BackGndColor  = array ( 0 => '#663366', 1 => '#336666', 2 => '#000099', 3 => '#061B2D', 4 => '#999999', 5 => '#999999', 15 => '#999999', 99 => '#999999', 100 => '#999999'  );

if ($MessCategory == 101) $MessPageMode = '';

switch ($MessPageMode) {
	case 'write':

		if ( !is_numeric( $OwnerID ) )
        {
			message ($lang['mess_no_ownerid'], $lang['mess_error']);
		}

		$OwnerRecord = db::query("SELECT `username`, `galaxy`, `system`, `planet` FROM {{table}} WHERE `id` = '".$OwnerID."';", 'users', true);

		if (!$OwnerRecord)
        {
			message ($lang['mess_no_owner']  , $lang['mess_error']);
		}

		$msg = '';

		if (isset($_POST['text']))
        {
			$error = 0;
			if (!$_POST["text"])
            {
				$error++;
				$msg = "<center><br><font color=#FF0000>".$lang['mess_no_text']."<br></font></center>";
			}
			if ($error == 0)
            {
				$msg = "<center><font color=#00FF00>".$lang['mess_sended']."<br></font></center>";

				$Owner   = $OwnerID;
				$Sender  = $user->data['id'];
				$From    = $user->data['username'] ." [".$user->data['galaxy'].":".$user->data['system'].":".$user->data['planet']."]";
				$Message = system::FormatText($_POST['text']);
				SendSimpleMessage ( $Owner, $Sender, '', 1, $From, $Message);
			}
		}

		$Display->addTemplate('message', 'message_new.php');
		$Display->assign('msg', $msg, 'message');
		$Display->assign('id', $OwnerID, 'message');
		$Display->assign('to', $OwnerRecord['username'] ." [".$OwnerRecord['galaxy'].":".$OwnerRecord['system'].":".$OwnerRecord['planet']."]", 'message');

		if (isset($_GET['quote']))
        {
			$mes = db::query("SELECT message_id, message_text FROM {{table}} WHERE message_id = ".intval($_GET['quote'])." AND (message_owner = ".$user->data['id']." || message_sender = ".$user->data['id'].");", "messages", true);
			if (isset($mes['message_id']))
            {
				$Display->assign('text', '[quote]'.preg_replace('/\<br(\s*)?\/?\>/iu', "", $mes['message_text']).'[/quote]', 'message');
			}
		}

	break;

	case 'delete':

		$Mess_Array = array();
	
		foreach($_POST as $Message => $Answer)
		{
			if (preg_match("/delmes/iu", $Message) && $Answer == 'on')
			{
				$Mess_Array[] = str_replace("delmes", "", $Message);
			}
		}
		
		$Mess_Array = implode(',', $Mess_Array);

		if ($Mess_Array != '')
			db::query("UPDATE {{table}} SET message_deleted = 1 WHERE `message_id` IN (".$Mess_Array.") AND `message_owner` = ".$user->data['id'].";", 'messages');

	default:

		if ($user->data['new_message'] > 0) {
			db::query ("UPDATE {{table}} SET `new_message` = 0 WHERE `id` = ".$user->data['id']."", 'users' );
			$user->data['new_message'] = 0;
		}

		if ($MessCategory < 100)
			$UsrMess1 = db::query("SELECT COUNT(message_id) as kol FROM {{table}} WHERE `message_owner` = '".$user->data['id']."' AND message_type = ".$MessCategory." AND message_deleted = 0", 'messages', true);
		elseif ($MessCategory == 101)
			$UsrMess1 = db::query("SELECT COUNT(message_id) as kol FROM {{table}} WHERE `message_sender` = '".$user->data['id']."'", 'messages', true);
		else
			$UsrMess1 = db::query("SELECT COUNT(message_id) as kol FROM {{table}} WHERE `message_owner` = '".$user->data['id']."' AND message_deleted = 0", 'messages', true);

		$pages = round($UsrMess1['kol'] / $lim) + 1;
		if (!$start) $start = 1;

		$limits = "".(($start-1)*$lim).",".$lim."";

		$page .= "<form action=\"?set=messages\" name=\"mes_form\" method=\"post\"><table width=\"700\">";
		$page .= "<tr><th>Показывать: <select name=\"messcat\" onChange=\"document.mes_form.submit();\"><option value=\"100\">Все";
		for ($MessType = 0; $MessType < 100; $MessType++) {
			if ( in_array($MessType, $MessageType) ) {
				$page .= "<option value=\"".$MessType."\""; if ($MessType == $MessCategory) $page .= " selected"; $page .= ">".$lang['type'][$MessType]."";
			}
		}
		$page .= "<option value=\"101\"";
		if ($MessCategory == 101) $page .= " selected";
		$page .= ">Исходящие</select>";
		$page .= "&nbsp;&nbsp;&nbsp;по: <select name=\"show_by\" onChange=\"document.mes_form.submit();\"><option value=\"5\"";
		if ($lim == 5)
			$page .= " selected";
		$page .= ">5<option value=\"10\"";
		if ($lim == 10)
			$page .= " selected";
		$page .= ">10<option value=\"25\"";
		if ($lim == 25)
			$page .= " selected";
		$page .= ">25<option value=\"50\"";
		if ($lim == 50)
			$page .= " selected";
		$page .= ">50</select>&nbsp;&nbsp;&nbsp;на странице</th><th>Перейти на страницу: <select name=\"start\" onChange=\"document.mes_form.submit();\">";
		for ($Me = 1; $Me <= $pages; $Me++) {
			$page .= "<option value=\"".$Me."\"";
			if ($Me == $start)
				$page .= " SELECTED";
			$page .= ">".$Me."</option>";
		}
		$page .= "</select></th></tr><tr><th colspan=\"2\"><table width=\"100%\">";
		$page .= "<tr><th width=50><input type=\"checkbox\" onChange=\"SelectAll()\" style='width:14px;'><input name=\"category\" value=\"".$MessCategory."\" type=\"hidden\"></th><th width=150>Дата</th><th>От</th><th width=60>&nbsp;</th></tr>";

		if ($MessCategory < 100)
			$UsrMess = db::query("SELECT * FROM {{table}} WHERE `message_owner` = '".$user->data['id']."' AND message_type = ".$MessCategory." AND message_deleted = 0 ORDER BY `message_time` DESC LIMIT ".$limits.";", 'messages');
		elseif ($MessCategory == 101)
			$UsrMess = db::query("SELECT m.*, CONCAT(u.username, ' [', u.galaxy,':', u.system,':',u.planet, ']') AS message_from, m.message_owner AS message_sender FROM {{table}}messages m LEFT JOIN {{table}}users u ON u.id = m.message_owner WHERE m.`message_sender` = '".$user->data['id']."' ORDER BY m.`message_time` DESC LIMIT ".$limits.";", '');
		else
			$UsrMess = db::query("SELECT * FROM {{table}} WHERE `message_owner` = '".$user->data['id']."' AND message_deleted = 0 ORDER BY `message_time` DESC LIMIT ".$limits.";", 'messages');

		while ($CurMess = db::fetch_assoc($UsrMess)) {

			$page .= "\n<tr>";

			//if ($CurMess['message_type'] == 0) {
				$page .= "<th><input name=\"showmes".$CurMess['message_id']."\" type=\"hidden\" value=\"1\"><input name=\"delmes".$CurMess['message_id']."\" type=\"checkbox\" style='width:14px;'></th>";
			//} else
			//	$page .= "<th>&nbsp;</th>";

			$page .= "<th>". datezone("d-m H:i:s", $CurMess['message_time']) ."</th>";
			//$page .= "<th><script>print_date(".$CurMess['message_time'].");</script></th>";
			$page .= "<th>". $CurMess['message_from']."</th>";

			if ($CurMess['message_type'] == 1) {
				$page .= "<th><a href=\"?set=messages&mode=write&amp;id=". $CurMess['message_sender'] ."\"><img src=\"". $dpath ."img/m.gif\" alt=\"Ответить\"></a>";
				$page .= "&nbsp;<a href=\"?set=messages&mode=write&amp;id=". $CurMess['message_sender'] ."&quote=".$CurMess['message_id']."\"><img src=\"". $dpath ."img/z.gif\" title='Цитировать сообщение'></a>";
				$page .= "&nbsp;<a href=\"?set=messages&amp;abuse=". $CurMess['message_id'] . "\" onclick='return confirm(\"Вы уверены что хотите отправить жалобу на это сообщение?\");'><img src=\"". $dpath ."img/s.gif\" title='Отправить жалобу'></a></th>";
			} else
				$page .= "<th>&nbsp;</th>";

			$page .= "</tr><tr><td style=\"background-color: ".$BackGndColor[$CurMess['message_type']]."; background-image: none;\"; colspan=\"4\" class=\"b\">";
			if ($CurMess['message_type'] == 1 && isset($user->data['bb_parser']) && $user->data['bb_parser'] == 1) {
				$page .= "<span id=\"m".$CurMess['message_id']."\"></span><script>Text('".str_replace(array("\r\n", "\n", "\r"), '', stripslashes($CurMess['message_text']))."', 'm".$CurMess['message_id']."');</script>";
			} else
				$page .= stripslashes( nl2br ($CurMess['message_text'] ) );
			$page .= "</td></tr>";
		}
		if ($UsrMess1['kol'] == 0)
			$page .= "<tr><td colspan=\"4\" align=center>нет сообщений</td></tr>";

		$page .= "<tr><td colspan=\"4\"></td></tr></table></th></tr><tr>";
		$page .= "<th colspan=\"4\"><input name=\"deletemessages\" value=\"Удалить отмеченные сообщения\" type=\"submit\" style='width:230px !important'></th>";
		$page .= "</tr></table></form>\n";
}

display($page, 'Сообщения', false);

?>
