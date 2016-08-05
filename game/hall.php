<?

/**
 * @author AlexPro
 * @copyright 2008 - 2011 XNova Game Group
 * @var $Display HSTemplateDisplay
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

if(!defined("INSIDE")) die("attemp hacking");

$sab = (!isset($_POST['visible']) || $_POST['visible'] <= 1) ? 0 : 1;

$parse = array();
$parse['hall'] = array();

$halls = db::query("SELECT * FROM {{table}} WHERE time < ".(time() - 3600)." AND sab = ".$sab." ORDER BY debris DESC LIMIT 50;", "hall");

$time = 0;

while ($hall = db::fetch_assoc($halls)) {
	$parse['hall'][] = $hall;

	if ($time < $hall['time'])
		$time = $hall['time'];
}

$parse['time'] = $time;

$Display->addTemplate('hall', 'hall.php');
$Display->assign('parse', $parse, 'hall');

display('', 'Зал славы', false);
?>
