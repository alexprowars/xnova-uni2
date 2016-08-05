<?php
session_set_cookie_params(0, '/', $_SERVER["SERVER_NAME"]);
session_start();
date_default_timezone_set("Europe/Moscow");

include ("includes/class/class.cache.php");

cache::init();

if (isset($_POST["msg"]))
{
	$msg_text = trim(htmlspecialchars(addslashes($_POST['msg'])));
	
	if ($msg_text == '')
		die();
		
	$msg_text = str_replace('\\\'','\'', $msg_text);
	$msg_text = str_replace('\\\\','\\', $msg_text);
	$msg_text = str_replace('\\&quot;','&quot;', $msg_text);

	$now = time();

	if (preg_match("/приватно \[(.*?)\]/u", $msg_text, $private)) {
		$msg_text = str_replace('приватно ['.$private['1'].']',' ', $msg_text);
	}elseif (preg_match("/для \[(.*?)\]/u", $msg_text, $to_login)) {
		$msg_text = str_replace('для ['.$to_login['1'].']',' ', $msg_text);
	}

	$chat = json_decode(cache::get("game_chat"), true);

	if (count($chat) > 0)
    {
		foreach ($chat AS $id => $message)
        {
			if ((time() - $message[0]) > 900)
				unset($chat[$id]);

			if ($message[0] == $now) $now++;
		}
	}

	if (!isset($to_login['1']))
		$to_login['1'] = '';
	if (!isset($private['1']))
		$private['1'] = '';
		
	$config = json_decode($_SESSION['config'], true);

	$chat[] = array($now, base64_encode($_SESSION['unm']), base64_encode($to_login['1']), base64_encode($private['1']), base64_encode($msg_text), ($config['color']+0));

	cache::set("game_chat", json_encode($chat), 7200);
	
	die();
}

if (isset($_GET['message_id']))
{
	$room_messages = json_decode(cache::get("game_chat"));
	
	$mess_id = intval($_GET['message_id']);
	$mess_id_t = $mess_id;
	
	if (count($room_messages) > 0)
    {
		$now = time();

		$color_massive = array('white','white','navy','blue','0046D5','teal','red','fuchsia','gray','green','maroon','orange','сhocolate','darkkhaki');

		foreach ($room_messages as $id => $message)
        {
			$message[4] = preg_replace("[\n\r]", "", base64_decode($message[4]));
			$message[4] = nl2br($message[4]);

			$message[4] = "<font color=\"".$color_massive[$message[5]]."\">".$message[4]."</font>";

			$message[1] = base64_decode($message[1]);
			$message[2] = base64_decode($message[2]);
			$message[3] = base64_decode($message[3]);

			if ($message[0] > $mess_id) {

				if ($message[2] <> "") {
					if ($message[1] == $_SESSION['unm'])
						print "ChatMsg(".$message[0].",'".$message[1]."','<FONT class=player onclick=\'to(\"".$message[2]."\");\'>для [".$message[2]."]</FONT> ".$message[4]."', 0, 1);\n";
					elseif ($message[2] == $_SESSION['unm'])
						print "ChatMsg(".$message[0].",'".$message[1]."','<FONT class=player onclick=\'to(\"".$message[1]."\");\'>для [".$message[2]."]</FONT> ".$message[4]."', 1, 0);\n";
				} elseif (!empty($message[3]) && ($message[1] == $user->data['username'] || $message[3] == $user->data['username'])){

						if ($message[1] == $_SESSION['unm'])
							print "ChatMsg(".$message[0].",'".$message[1]."','<FONT class=private onclick=\'pp(\"".$message[3]."\");\'>приватно [".$message[3]."]</FONT> ".$message[4]."', 0, 1);\n";
						else
							print "ChatMsg(".$message[0].",'".$message[1]."','<FONT class=private onclick=\'pp(\"".$message[1]."\");\'>приватно [".$message[3]."]</FONT> ".$message[4]."', 1, 0);\n";
				} elseif ($message[3] == "" && $message[2] == "" ) {

						if ($message[1] == $_SESSION['unm'])
							print "ChatMsg(".$message[0].",'".$message[1]."','".$message[4]."', 0, 1);\n";
						else
							print "ChatMsg(".$message[0].",'".$message[1]."','".$message[4]."', 0, 0);\n";
				}
				$mess_id_t = $message[0];
			}
		}
	}

	print"MsgSent('".$mess_id_t."');";
	
	die();
}

//eaccelerator_rm("game_chat");

?>