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
$n = @intval($_GET['n']);
$lang['Please_Wait'] = "Подождите...";

system::includeLang('notes');

if(isset($_POST["s"]) && ($_POST["s"] == 1 || $_POST["s"] == 2)){

	$time = time();
	$priority = $_POST["u"];
	$title = ($_POST["title"]) ? db::escape_string(strip_tags($_POST["title"])) : $lang['NoTitle'];
	$text = ($_POST["text"]) ? db::escape_string(strip_tags($_POST["text"])) : $lang['NoText'];

	if($_POST["s"] ==1){
		db::query("INSERT INTO {{table}} SET owner={$user->data['id']}, time=$time, priority=$priority, title='$title', text='$text'","notes");
		message($lang['NoteAdded'], $lang['Please_Wait'], '?set=notes',"3");
	}elseif($_POST["s"] == 2){

		$id = intval($_POST["n"]);
		$note_query = db::query("SELECT * FROM {{table}} WHERE id=$id AND owner=".$user->data["id"],"notes");

		if(!$note_query){ message($lang['notpossiblethisway'],$lang['Notes']); }

		db::query("UPDATE {{table}} SET time=$time, priority=$priority, title='$title', text='$text' WHERE id=$id","notes");
		message($lang['NoteUpdated'], $lang['Please_Wait'], '?set=notes', "3");
	}

} elseif($_POST){

	$deleted = 0;

	foreach($_POST as $a => $b){

		if(preg_match("/delmes/iu",$a) && $b == "y"){

			$id = str_replace("delmes","",$a);
			$note_query = db::query("SELECT * FROM {{table}} WHERE id=$id AND owner={$user->data['id']}","notes");
			//comprobamos,
			if($note_query){
				$deleted++;
				db::query("DELETE FROM {{table}} WHERE `id`=$id;","notes");// y borramos
			}
		}
	}
	if($deleted){
		$mes = ($deleted == 1) ? $lang['NoteDeleted'] : $lang['NoteDeleteds'];
		message($mes,$lang['Please_Wait'],'?set=notes',"3");
	}else{system::Redirect("?set=notes");}

}else{
	if(isset($_GET["a"]) && $_GET["a"] == 1){

		$parse = $lang;

		$parse['c_Options'] = "<option value=2 selected=selected>{$lang['Important']}</option>
			  <option value=1>{$lang['Normal']}</option>
			  <option value=0>{$lang['Unimportant']}</option>";

		$parse['cntChars'] = '0';
		$parse['TITLE'] = $lang['Createnote'];
		$parse['text'] = '';
		$parse['title'] = '';
		$parse['inputs'] = '<input type=hidden name=s value=1>';

		$Display->addTemplate('notes', 'notes_form.php');
        $Display->assign('parse', $parse, 'notes');

		display('', 'Создание заметки', false);

	}
	elseif(isset($_GET["a"]) && $_GET["a"] == 2){

		$note = db::query("SELECT * FROM {{table}} WHERE owner={$user->data['id']} AND id=$n",'notes',true);

		if(!$note){ message($lang['notpossiblethisway'],$lang['Error']); }

		$cntChars = strlen($note['text']);

		$SELECTED[0] = '';
		$SELECTED[1] = '';
		$SELECTED[2] = '';
		$SELECTED[$note['priority']] = ' selected="selected"';

		$parse = array_merge($note,$lang);

		$parse['c_Options'] = "<option value=2{$SELECTED[2]}>{$lang['Important']}</option>
			  <option value=1{$SELECTED[1]}>{$lang['Normal']}</option>
			  <option value=0{$SELECTED[0]}>{$lang['Unimportant']}</option>";

		$parse['cntChars'] = $cntChars;
		$parse['TITLE'] = $lang['Editnote'];
		$parse['inputs'] = '<input type=hidden name=s value=2><input type=hidden name=n value='.$note['id'].'>
							<br><table width=660><tr><td class=c>Просмотр заметки</td></tr><tr><th style="text-align:left;font-weight:normal;">
							<span id="um'.$note['id'].'" style="display:none;"></span><span id="m'.$note['id'].'"></span><script>Text(\''.$note['text'].'\', \'m'.$note['id'].'\');</script>
							</th></tr></table><br><br>';

		$Display->addTemplate('notes', 'notes_form.php');
        $Display->assign('parse', $parse, 'notes');

		display('', $lang['Notes'], false);

	}
	else{

		$notes_query = db::query("SELECT * FROM {{table}} WHERE owner={$user->data['id']} ORDER BY time DESC",'notes');
		$parse = $lang;
        $parse['list'] = array();

		while($note = db::fetch_array($notes_query)){

            $list = array();

			if($note["priority"] == 0){ $list['NOTE_COLOR'] = "lime";}//Importante
			elseif($note["priority"] == 1){ $list['NOTE_COLOR'] = "yellow";}//Normal
			elseif($note["priority"] == 2){ $list['NOTE_COLOR'] = "red";}//Sin importancia

			$list['NOTE_ID'] = $note['id'];
			$list['NOTE_TIME'] = datezone("Y-m-d h:i:s",$note["time"]);
			$list['NOTE_TITLE'] = $note['title'];
			$list['NOTE_TEXT'] = strlen($note['text']);

			$parse['list'][] = $list;

		}

        $Display->addTemplate('notes', 'notes.php');
        $Display->assign('parse', $parse, 'notes');

	    display('', 'Заметки', false);
	}
}
?>
