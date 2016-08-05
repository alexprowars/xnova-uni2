<?

/**
 * @author AlexPro
 * @copyright 2008 - 2011 XNova Game Group
 * @var $user user
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

if(!defined("INSIDE")) die("attemp hacking");

if (isset($_GET['sets'])) {
	if ($user->data['credits'] > 5) {
		$id = intval($_POST['avatar']);
		if ($id < 1 || $id > 56)
			message("У вас нет подписки на этот аватар.", "Ошибка", "?set=avatar", 3);

		db::query("UPDATE {{table}} SET avatar = '".$id."', credits = credits - 5 WHERE id = ".$user->data['id']."", "users");

		message("Аватар успешно установлен.", "ОК", "?set=options", 3);

	} else {
		message("У вас не хватает средств для смены аватара.", "Ошибка", "?set=avatar", 3);
	}
}

if (isset($_POST['type']) && $_POST['type'] == 'image') {
	$upload = new upload($_FILES['image']);

	if ($upload->uploaded)
	{
		$upload->dir_auto_create 	= false;
		$upload->dir_auto_chmod 	= false;
		$upload->file_overwrite 	= true;
		$upload->file_max_size		= 102400;
		$upload->mime_check			= true;
		$upload->allowed			= array('image/*');
		$upload->image_convert		= 'jpg';
		$upload->image_resize		= true;
		$upload->image_x			= 100;
		$upload->image_y			= 100;
		$upload->file_new_name_body = 'upload_'.$user->data['id'];

		$upload->Process('images/avatars/upload/');

		if ($user->data['credits'] > 5)
		{
			if ($upload->processed)
			{
				db::query("UPDATE {{table}} SET avatar = '99', credits = credits - 5 WHERE id = ".$user->data['id']."", "users");
				message("Аватар успешно установлен.", "ОК", "?set=options", 3);
			}
			else
			{
				message($upload->error, "Ошибка", "?set=avatar", 3);
			}
		}
		else
		{
			message("У вас не хватает средств для смены аватара.", "Ошибка", "?set=avatar", 3);
		}

		$upload->Clean();
	}
}
 
$page = "<script>function av(id){document.ava.src = '/images/avatars/'+id+'.jpg';}</script>";

$page .= "<br><br><form action=\"?set=avatar&sets=1\" method=\"POST\"><table width=500><tr><td class=c colspan=2>Выбор аватара</td></tr>";
$page .= "<tr><th colspan=2>Стоимость смены аватара - 5 кр.</th></tr>";
$page .= "<tr><th width=30%><select name=avatar onchange=\"av(this.value)\">";

for ($i = 1; $i < 57; $i++) {
	$page .= "<option value=".$i.""; if ($user->data['avatar'] == $i) $page .= " selected"; $page .= ">№ ".$i."";
}

$page .= "</select></th><th><img src=\"/images/avatars/"; if ($user->data['avatar'] != 0 && $user->data['avatar'] != 99) $page .= $user->data['avatar']; else $page .= "1"; $page .= ".jpg\" name=ava width=100 height=100></th></tr><tr><td class=c colspan=2><input type=submit value=\"Сменить аватар\"></td></tr></table></form>";

$page .= '<br><form name="form2" enctype="multipart/form-data" method="post" action="?set=avatar" />
		<table width=500><tr><td class=c>Загрузка аватара</td></tr>
		<tr><th>Картинки уменьшаются до размера 100 на 100 пикселей<br><br>
            <input type="file" size="32" name="image" value="" />
            <input type="hidden" name="type" value="image" />
            <input type="submit" name="Submit" value="Загрузить" /></th></tr></table>
        </form>';

display($page, "Выбор аватара", false);

?>
