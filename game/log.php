<?php

/**
 * @author AlexPro
 * @copyright 2008 - 2011 XNova Game Group
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

if(!defined("INSIDE")) die("attemp hacking");

if (isset($user->data['id']) && !isset($_GET['id'])) {

    $message = "";

    if (isset($_GET['mysql']) && $_GET['mysql'] == 'new'){

        if (!$_POST['title'])
            $message = '<h1><font color=red>Введите название для боевого отчёта.</h1>';
        elseif (!$_POST['code'])
            $message = '<h1><font color=red>Введите ID боевого отчёта.</h1>';
        else {

            $key    = substr($_POST['code'], 0, 32);
            $id     = substr($_POST['code'], 32, (strlen($_POST['code']) - 32));

            if (md5('xnovasuka'.$id) != $key)
	            message('Не правильный ключ', 'Ошибка', '', 0, false);
            else {

                $log = db::query("SELECT * FROM {{table}} WHERE `id` = '".$id."';", 'rw', true);
                if (isset($log['id'])){

                    $user_list = json_decode($log['id_users']);

                    if ($user_list[0] == $user->data['id'] && $log['no_contact'] == 1) {
                        $SaveLog = "Контакт с флотом потерян.<br>(Флот был уничтожен в первой волне атаки.)";
                    } else {
                        $SaveLog = json_decode($log['raport'], true);

                        foreach( $SaveLog[0]['rw'] as $round => $data1){
                            unset($SaveLog[0]['rw'][$round]['logA']);
                            unset($SaveLog[0]['rw'][$round]['logD']);
                        }

						$SaveLog = json_encode($SaveLog);						
                    }

                    db::query("INSERT INTO {{table}} (`user`, `title`, `log`) VALUES ('".$user->data['id']."', '".addslashes(htmlspecialchars($_POST['title']))."', '".$SaveLog."')", "savelog");
                    $message = 'Боевой отчёт успешно сохранён.';
                } else
                    $message = 'Боевой отчёт не найден в базе';
            }
        }
        message($message, "Логовница", "?set=log", 2);
    }



    $mode = (isset($_GET['mode'])) ? $_GET['mode'] : '';

    switch ($mode) {

        case 'new':

            $page = "<br><br><br><table width=\"600\"><tr><td class=\"c\"><h1>Сохранение боевого доклада</h1></td></tr>";
            $page .="<tr><th><form action=?set=log&mysql=new method=POST>";
            $page .="Название:<br>";
            $page .="<input type=text name=title size=50 maxlength=100><br>";
            $page .="ID боевого доклада:<br>";
            $page .="<input type=text name=code size=50 maxlength=40 ".((isset($_GET['save'])) ? 'value="'.$_GET['save'].'"' : '').">";
            $page .="<br>";
            $page .="<br><input type=submit value='Сохранить'>";
            $page .="</form></th></tr></table>";

            display($page, "Логовница", false);

            break;

        case 'delete':

            if (isset($_GET['id_l'])){

                $id = intval($_GET['id_l']);

                $sql = db::query("SELECT * FROM {{table}} WHERE id = '".$id."' ","savelog");
                $raportrow = db::fetch_assoc($sql);

                if ($user->data['id'] == $raportrow['user']) {
                    db::query("DELETE FROM {{table}} WHERE `id` = ".$id." ","savelog");
                    system::Redirect("?set=log");
                } else {
                    $message = "Ошибка удаления.";
                    message($message, "Логовница", "?set=log", 1);
                }
            }

            break;

        default:

            $ksql = db::query("SELECT `id`, `user`, `title` FROM {{table}} WHERE `user` = '".$user->data['id']."' ","savelog");

            $page ="<table width=600>";
            $page .="<tr><th colspan=4><h1>Логовница XNova Game</h1></th></tr>";
            $page .="<tr>";
            $page .="<td class=c colspan=4>Ваши сохранённые логи</td>";
            $page .="</tr>";
            $page .="<tr><td class=c>№</td><td class=c>Название</td><td class=c>Ссылка</td><td class=c>Управление логом</td></tr>";
            $i = 0;
                while($krow = db::fetch_array($ksql)){
                $i++;
                    $page .= "<tr><td class=b align=center>".$i."</td><td class=b align=center>".$krow['title']."</td><td class=b align=center><a href=?set=log&id=".$krow['id']." target=_new>Открыть</a></td><td class=b align=center><a href='?set=log&mode=delete&id_l=".$krow['id']."'>Удалить лог</a></td></tr>";
                }
            if ($i == 0) $page .= "<tr align=center><td  class=b colspan=4>У вас пока нет сохранённых логов.</td></tr>";

            $page .= "<tr><td class=c colspan=4><a href=?set=log&mode=new>Создать новый лог боя</a></td></tr></table>";

            display($page, "Логовница", false);
    }
}

if (isset($_GET['id'])){
	$raportrow = db::query("SELECT * FROM {{table}} WHERE id = '".intval($_GET['id'])."' ","savelog", true);
	if ($raportrow) {
		$Page  = "<html><head><title>".stripslashes($raportrow["title"])."</title>";
		$Page .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"xnsim/report.css\">";
		$Page .= "<meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\" />";
		$Page .= "</head><body><script>function show(id){if(document.getElementById(id).style.display==\"block\")document.getElementById(id).style.display=\"none\"; else document.getElementById(id).style.display=\"block\";}</script>";
		$Page .= "<table width=\"99%\"><tr><td>";

        if (substr($raportrow['log'], 0, 1) == 'К')
            echo "<center>".$raportrow['log']."</center>";
        else {
            include("includes/CreateRaportHTML.php");
			
            $result = json_decode($raportrow['log'], true);

			if ($raportrow['user'] == 0 && $result[0]['time'] > (time() - 7200))
				echo "<center>Данный лог боя пока недоступен для просмотра!</center>";
			else {
           	 	$formatted_cr = formatCR($result[0], $result[1], $result[2], $result[3], $result[4], $result[5]);
            	$Page .= $formatted_cr['html'];
			}
        }

        $Page .= "</td></tr></table>";
		$Page .= "</body></html>";
	} else {
		$Page  = "<html><head><link rel=\"stylesheet\" type=\"text/css\" href=\"xnsim/report.css\">";
		$Page .= "<meta http-equiv=\"content-type\" content=\"text/html; charset=windows-1251\" />";
		$Page .= "</head><body><center>Запрашиваемого лога не существует в базе данных</center></body></html>";
	}

	echo $Page;
}

?>
