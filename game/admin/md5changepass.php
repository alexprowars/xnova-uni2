<?php

if(!defined("INSIDE")) die("attemp hacking");

if ($user->data['authlevel'] == 3) {

    if ($_POST){

        if ($_POST['md5q'] != "" || $_POST['user'] != "") {

            $user_ch = db::query("SELECT `id` FROM {{table}} WHERE `username` = '".$_POST['user']."'", 'users', true);
            if (isset($user_ch['id'])){

            db::query ("UPDATE {{table}} SET `password` = '".md5($_POST['md5q'])."' WHERE `id` = '".$user_ch['id']."';", 'users_inf');
            message('Пароль успешно изменён.'  , 'Ошибка', 'md5changepass.php', 3);

            }else{
            message('Такого игрока несуществует.'  , 'Ошибка', 'md5changepass.php', 3);
            }
        } else {
            message('Не введён логин игрока или новый пароль.'  , 'Ошибка', 'md5changepass.php', 3);
        }
    }
	
	$Display->addTemplate('pass', 'admin/changepass.php');

	display( '', 'Смена пароля', false, true);
} else {
	message( $lang['sys_noalloaw'], $lang['sys_noaccess'] );
}

?>
