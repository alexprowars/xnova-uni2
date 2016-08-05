<?php

/**
 * @author AlexPro
 * @copyright 2008 - 2011 XNova Game Group
 * @var $Display HSTemplateDisplay
 * @var $lang array
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

if(!defined("INSIDE")) die("attemp hacking");

system::includeLang('contact');

$parse   = array();

$GameOps  = db::query("SELECT u.`username`, ui.`email`, u.`authlevel` FROM {{table}}users u, {{table}}users_inf ui WHERE ui.id = u.id AND u.`authlevel` != '0' ORDER BY u.`authlevel` DESC;", '');

while( $Ops = db::fetch_assoc($GameOps) ) {
	$bloc['ctc_data_name']    = $Ops['username'];
	$bloc['ctc_data_auth']    = $lang['user_level'][$Ops['authlevel']];
	$bloc['ctc_data_mail']    = "<a href=mailto:".$Ops['email'].">".$Ops['email']."</a>";
	$parse[] = $bloc;
}

$Display->addTemplate('contact', 'contact.php');
$Display->assign('parse', $parse, 'contact');

if (isset($user->data['id']))
	display('', $lang['ctc_title'], false);
else
	display('', $lang['ctc_title'], false, false);
	
?>
