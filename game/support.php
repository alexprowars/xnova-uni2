<?php

/**
 * @author AlexPro
 * @copyright 2008 - 2011 XNova Game Group
 * @var $Display HSTemplateDisplay
 * @var $user user
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

if(!defined("INSIDE")) die("attemp hacking");

$action = (isset($_GET['action'])) ? $_GET['action'] : '';

switch($action){
    case 'newticket':

        if(empty($_POST['text']) || empty($_POST['subject']))
            message('Не заполнены все поля', 'Ошибка', '?set=support', 3);

        $SQL  = "INSERT {{table}} SET ";
        $SQL .= "`player_id` = '". $user->data['id'] ."',";
        $SQL .= "`subject` = '".htmlspecialchars(addslashes($_POST['subject'])) ."',";
        $SQL .= "`text` = '" .htmlspecialchars(addslashes($_POST['text'])) ."',";
        $SQL .= "`time` = ".time().",";
        $SQL .= "`status` = '1';";
        db::query($SQL, "support");

        message('Задача добавлена', 'Успех', '?set=support', 3);

    break;

    case 'send':

        if (isset($_GET['id'])) {

            $TicketID = intval($_GET['id']);

            if(empty($_POST['text']))
                message('Не заполнены все поля', 'Ошибка', '?set=support', 3);


            $ticket = db::query("SELECT text FROM {{table}} WHERE `id` = '".$TicketID."';", "support", true);

            $text 	= $ticket['text'].'<hr>'.$user->data['username'].' ответил в '.date("d.m.Y H:i:s", time()).':<br>'.htmlspecialchars(addslashes($_POST['text'])).'';

            db::query("UPDATE {{table}} SET `text` = '".addslashes($text) ."',`status` = '3' WHERE `id` = '". $TicketID ."';", "support");

            message('Задача обновлена', 'Успех', '?set=support', 3);
        }

    break;

    default:

        $parse = array();

        $supports = db::query("SELECT ID, time, text, subject, status FROM {{table}} WHERE (`player_id` = '".$user->data['id']."') ORDER BY time DESC;", "support");

        $parse['TicketsList'] = array();

        while($ticket = db::fetch_assoc($supports)){
            $parse['TicketsList'][$ticket['ID']]	= array(
                'status'	=> $ticket['status'],
                'subject'	=> $ticket['subject'],
                'date'		=> datezone("d.m.Y H:i:s", $ticket['time']),
                'text'		=> html_entity_decode($ticket['text'], ENT_NOQUOTES, "CP1251"),
            );
        }

        $Display->addTemplate('support', 'support.php');
        $Display->assign('parse', $parse, 'support');

        display('', 'Техподдержка', false);

}

 ?>
