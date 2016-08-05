<?

/**
 * @author AlexPro
 * @copyright 2008 - 2011 XNova Game Group
 * @var $Display HSTemplateDisplay
 * @var $user user
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

if(!defined("INSIDE")) die("attemp hacking");

	$userinf = db::query("SELECT email FROM {{table}} WHERE id = ".$user->data['id'].";", "users_inf", true);

    $Display->addTemplate('credits', 'credits.php');
	$Display->assign('userid', $user->data['id'], 'credits');
 	$Display->assign('useremail', $userinf['email'], 'credits');
    display('', 'Покупка кредитов', false);

?>
