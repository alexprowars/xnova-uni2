<?php

if(!defined("INSIDE")) die("attemp hacking");

$ID = @intval($_GET['id']);

if ($user->data['authlevel'] < 3)
	message( $lang['sys_noalloaw'], $lang['sys_noaccess'] );

switch(@$_GET['action'])
{
    case 'send':
        
        $text	= nl2br($_POST['text']);
        if(empty($_POST['text']))
            message('Не заполнены все поля', 'Ошибка', '?set=admin&mode=support', 3);

        $ticket = db::query("SELECT `player_id`, `text` FROM {{table}} WHERE `id` = '".$ID."';", "support", true);
        $newtext = $ticket['text'].'<br><br><hr>'.$user->data['username'].'  ответил в '. date("d.m.Y H:i:s", time()).':<br>'.$text;

        $SQL  = "UPDATE {{table}} SET ";
        $SQL .= "`text` = '".addslashes($newtext)."',";
        $SQL .= "`status` = '2'";
        $SQL .= "WHERE ";
        $SQL .= "`id` = '".$ID."' ";

        db::query($SQL, "support");

        SendSimpleMessage($ticket['player_id'], '', time(), 4, $user->data['username'], 'Ответ на тикет №'.$ID);

    break;
    case 'open':

        $ticket = db::query("SELECT text FROM {{table}} WHERE `id` = '".$ID."';", "support", true);
        $newtext = $ticket['text'].'<br><br><hr>'.$user->data['username'].' открыл тикет в '.date("j. M Y H:i:s", time());

        $SQL  = "UPDATE {{table}} SET ";
        $SQL .= "`text` = '".addslashes($newtext)."',";
        $SQL .= "`status` = '2'";
        $SQL .= "WHERE ";
        $SQL .= "`id` = '".$ID."' ";

        db::query($SQL, "support");

    break;
    case 'close':

        $ticket = db::query("SELECT text FROM {{table}} WHERE `id` = '".$ID."';", "support", true);
        $newtext = $ticket['text'].'<br><br><hr>'.$user->data['username'].' закрыл тикет в '.date("j. M Y H:i:s", time());

        $SQL  = "UPDATE {{table}} SET ";
        $SQL .= "`text` = '".addslashes($newtext)."',";
        $SQL .= "`status` = '0'";
        $SQL .= "WHERE ";
        $SQL .= "`id` = '".$ID."' ";

        db::query($SQL, "support");

    break;
}

$tickets	= array('open' => array(), 'closed' => array());

$query = db::query("SELECT s.*, u.username FROM {{table}}support s, {{table}}users u WHERE u.id = s.player_id AND status != 0 ORDER BY s.time;", "");
while($ticket = db::fetch_assoc($query))
{
    switch($ticket['status']){
        case 0:
            $status = '<font color="red">закрыто</font>';
        break;
        case 1:
            $status = '<font color="green">открыто</font>';
        break;
        case 2:
            $status = '<font color="orange">ответ админа</font>';
        break;
        case 3:
            $status = '<font color="green">ответ игрока</font>';
        break;
    }

    if(isset($_GET['action']) && $_GET['action'] == 'detail' && $ID == $ticket['ID'])
        $TINFO	= $ticket;

    if($ticket['status'] == 0){
        if(isset($_GET['action']) && $_GET['action'] == 'detail')
            continue;

        $tickets['closed'][]	= array(
            'id'		=> $ticket['ID'],
            'username'	=> $ticket['username'],
            'subject'	=> $ticket['subject'],
            'status'	=> $status,
            'date'		=> date("d.m.Y H:i:s",$ticket['time'])
        );
    } else {
        $tickets['open'][]	= array(
            'id'		=> $ticket['ID'],
            'username'	=> $ticket['username'],
            'subject'	=> $ticket['subject'],
            'status'	=> $status,
            'date'		=> date("d.m.Y H:i:s",$ticket['time'])
        );
    }
}

if(isset($_GET['action']) && $_GET['action'] == 'detail' && isset($TINFO))
{
    switch($TINFO['status']){
        case 0:
            $status = '<font color="red">закрыто</font>';
        break;
        case 1:
            $status = '<font color="green">открыто</font>';
        break;
        case 2:
            $status = '<font color="orange">ответ админа</font>';
        break;
        case 3:
            $status = '<font color="green">ответ игрока</font>';
        break;
    }

    $parse = array(
        't_id'			=> $TINFO['ID'],
        't_username'	=> $TINFO['username'],
        't_statustext'	=> $status,
        't_status'		=> $TINFO['status'],
        't_text'		=> $TINFO['text'],
        't_subject'		=> $TINFO['subject'],
        't_date'		=> date("j. M Y H:i:s", $TINFO['time']),
    );

    $Display->assign('parse', $parse, 'support');
}

$Display->addTemplate('support', 'admin/support.php');
$Display->assign('tickets', $tickets, 'support');

display('', 'Техподдержка', false);

 ?>