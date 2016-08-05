<?php

if(!defined("INSIDE")) die("attemp hacking");

if ($user->data['authlevel'] < 2)
	message($lang['sys_noalloaw'], $lang['sys_noaccess']);
	
if ($_POST && $_GET['modes'] == "change") { 			
		if ($user->data['authlevel'] == 3) {
			$kolor = 'yellow';
			$ranga = 'Администратор'; 			
		} elseif ($user->data['authlevel'] == 1) {
			$kolor = 'skyblue'; 				
			$ranga = 'Оператор'; 			
		} elseif ($user->data['authlevel'] == 2) {
			$kolor = 'yellow'; 				
			$ranga = 'Супер оператор'; 
		}
	
	if ((isset($_POST["tresc"]) && $_POST["tresc"] != '') && (isset($_POST["temat"]) && $_POST["temat"] != '')) { 
					
					$sq  = db::query("SELECT `id` FROM {{table}}", "users");
	 				$Time    = time(); 				
					$From    = "<font color=\"". $kolor ."\">". $ranga ." ".$user->data['username']."</font>";
					$Subject = "<font color=\"". $kolor ."\">". $_POST['temat'] ."</font>"; 				
					$Message = "<font color=\"". $kolor ."\"><b>". $_POST['tresc'] ."</b></font>"; 	
				
			while ($u = db::fetch_array($sq)) {
						
			SendSimpleMessage ( $u['id'], $user->data['id'], $Time, 1, $From, $Message);
			} 
					
			message("<font color=\"lime\">Сообщение успешно отправлено всем игрокам!</font>", "Выполнено", "overview." . $phpEx, 3); 	
			
	} else {
	 		message("<font color=\"red\">Не все поля заполнены!</font>", "Ошибка", "overview." . $phpEx, 3);
	} 		
} else {
            $Display->addTemplate('mess', 'admin/messtoall.php');

			display('', '', false, true);
} 	

?>