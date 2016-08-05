<?php

if(!defined("INSIDE")) die("attemp hacking");

if ($user->data['authlevel'] >= "2") {
	system::includeLang('admin/messagelist');

	$Prev       = ( !empty($_POST['prev'])   ) ? true : false;
	$Next       = ( !empty($_POST['next'])   ) ? true : false;
	$DelSel     = ( !empty($_POST['delsel']) ) ? true : false;
	$DelDat     = ( !empty($_POST['deldat']) ) ? true : false;
	$CurrPage   = ( !empty($_POST['curr'])   ) ? intval($_POST['curr']) : 1;
	$Selected   = ( !empty($_POST['type'])   ) ? intval($_POST['type']) : 1;
	$SelPage    = @$_POST['page'];
	
	if ($Selected == 6) $Selected = 0;

	$ViewPage = ( !empty($SelPage) ) ? $SelPage : 1;


	if ($Prev   == true) {
		$CurrPage -= 1;
		if ($CurrPage >= 1) {
			$ViewPage = $CurrPage;
		} else {
			$ViewPage = 1;
		}
	} elseif ($Next == true) {
		$CurrPage += 1;

		$ViewPage = $CurrPage;

	} elseif ($DelSel == true) {
		foreach($_POST['sele_mes'] as $MessId => $Value) {
			if ($Value = "on") {
				db::query ( "DELETE FROM {{table}} WHERE `message_id` = '". $MessId ."';", 'messages');
			}
		}
	} elseif ($DelDat == true) {
		$SelDay    = $_POST['selday'];
		$SelMonth  = $_POST['selmonth'];
		$SelYear   = $_POST['selyear'];
		$LimitDate = mktime (0,0,0, $SelMonth, $SelDay, $SelYear );
		if ($LimitDate != false) {
			db::query ( "DELETE FROM {{table}} WHERE `message_time` <= '". $LimitDate ."';", 'messages');
			db::query ( "DELETE FROM {{table}} WHERE `time` <= '". $LimitDate ."';", 'rw');
		}
	}

	$Mess     = db::query("SELECT COUNT(*) AS `max` FROM {{table}} WHERE `message_type` = '". $Selected ."';", 'messages', true);
	$MaxPage  = ceil ( ($Mess['max'] / 25) );

	$parse                      = $lang;
	$parse['mlst_data_page']    = $ViewPage;
	$parse['mlst_data_pagemax'] = $MaxPage;
	$parse['mlst_data_sele']    = $Selected;

	$parse['mlst_data_types'] = "<option value=\"1\"".  (($Selected == "1")  ? " SELECTED" : "") .">". $lang['mlst_mess_typ__1'] ."</option>";
	$parse['mlst_data_types'] .= "<option value=\"2\"".  (($Selected == "2")  ? " SELECTED" : "") .">". $lang['mlst_mess_typ__2'] ."</option>";
	$parse['mlst_data_types'] .= "<option value=\"3\"".  (($Selected == "3")  ? " SELECTED" : "") .">". $lang['mlst_mess_typ__3'] ."</option>";
	$parse['mlst_data_types'] .= "<option value=\"4\"".  (($Selected == "4")  ? " SELECTED" : "") .">". $lang['mlst_mess_typ__4'] ."</option>";
	$parse['mlst_data_types'] .= "<option value=\"5\"".  (($Selected == "5")  ? " SELECTED" : "") .">". $lang['mlst_mess_typ__5'] ."</option>";
	$parse['mlst_data_types'] .= "<option value=\"6\"".  (($Selected == "6")  ? " SELECTED" : "") .">Прочее</option>";

	$parse['mlst_data_pages']  = "";
	for ( $cPage = 1; $cPage <= $MaxPage; $cPage++ ) {
		$parse['mlst_data_pages'] .= "<option value=\"".$cPage."\"".  (($ViewPage == $cPage)  ? " SELECTED" : "") .">". $cPage ."/". $MaxPage ."</option>";
	}

	$parse['tbl_rows']   = "";
	$parse['mlst_title'] = $lang['mlst_title'];
	
	if(isset($_POST['userid']) && $_POST['userid'] != "") {
		$userid = " AND message_owner = ".intval($_POST['userid'])."";
		$parse['userid'] = intval($_POST['userid']);
	} elseif(isset($_POST['userid_s']) && $_POST['userid_s'] != "") {
		$userid = " AND message_sender = ".intval($_POST['userid_s'])."";
		$parse['userid_s'] = intval($_POST['userid_s']);
	} else
		$userid = "";

	$StartRec           = 0 + (($ViewPage - 1) * 25);
	$Messages           = db::query("SELECT m.*, u.username FROM {{table}}messages m LEFT JOIN {{table}}users u ON u.id = m.message_owner WHERE m.`message_type` = '". $Selected ."' ".$userid." ORDER BY m.`message_time` DESC LIMIT ". $StartRec .",25;", '');
    $parse['mlst_data_rows'] = array();

	while ($row = db::fetch_assoc($Messages)) {
		$bloc['mlst_id']      = $row['message_id'];
		$bloc['mlst_from']    = $row['message_from'];
		$bloc['mlst_to']      = $row['username'] ." ID:". $row['message_owner'];
		$bloc['mlst_text']    = $row['message_text'];
		$bloc['mlst_time']    = date ( "d.m.Y H:i:s", $row['message_time'] );

		$parse['mlst_data_rows'][] = $bloc;
	}

	if (isset($_POST['delit'])) {
		db::query ("DELETE FROM {{table}} WHERE `message_id` = '". $_POST['delit'] ."';", 'messages');
		message ( $lang['mlst_mess_del'] ." ( ". $_POST['delit'] ." )", $lang['mlst_title'], "./messagelist.".$phpEx, 3);
	}

    $Display->addTemplate('messagelist', 'admin/messagelist.php');
    $Display->assign('parse', $parse, 'messagelist');

	display ('', $lang['mlst_title'], false, true);
} else {
	message($lang['sys_noalloaw'], $lang['sys_noaccess']);
}
?>
