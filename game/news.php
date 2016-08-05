<?php

/**
 * @author AlexPro
 * @copyright 2008 - 2011 XNova Game Group
 * @var $Display HSTemplateDisplay
 * @var $lang arraay
 * @var $user user
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

if(!defined("INSIDE")) die("attemp hacking");

system::includeLang('news');

$news = array();

foreach($lang['news'] as $a => $b){
	$news[] = array($a, nl2br($b));
}

$Display->addTemplate('news', 'news.php');
$Display->assign('parse', $news, 'news');

if (isset($user->data['id']))
	display('', 'Новости', false);
else
	display('', 'Новости', false, false);
	
?>
