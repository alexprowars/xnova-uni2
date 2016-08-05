<?php

/**
 * @author AlexPro
 * @copyright 2008 - 2011 XNova Game Group
 * @var $Display HSTemplateDisplay
 * @var $planetrow planet
 * @var $resource array
 * @var $user user
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

if(!defined("INSIDE")) die("attemp hacking");

$requer = 0;

$_GET['p'] = (isset($_GET['p'])) ? $_GET['p'] : '';

if ($_GET['p'] > $user->data['tutorial'] + 1)
    $_GET['p'] = $user->data['tutorial'] + 1;

switch ($_GET['p']){
    case 'exit':
        db::query("UPDATE {{table}} SET tutorial = 10, tutorial_value = 0 WHERE id = ".$user->data['id']."", "users");
        $user->data['tutorial'] = 10;
        message('Вы отказались от прохождения обучения. Данное действие необратимо.', 'Обучение', '?set=overview');
        break;

    case 'finish':
        message('Вы завершили обучение. Удачной игры!', 'Обучение', '?set=overview');
        break;

    case 1:
        if($planetrow->data[$resource[1]] >= 4){
            $parse['met_4'] = 'check';
            $requer++;
        } else {
            $parse['met_4'] = 'none';
        }
        if($planetrow->data[$resource[2]] >= 2){
            $parse['cris_2'] = 'check';
            $requer++;
        } else {
            $parse['cris_2'] = 'none';
        }
        if($planetrow->data[$resource[4]] >= 4){
            $parse['sol_4'] = 'check';
            $requer++;
        } else {
            $parse['sol_4'] = 'none';
        }
        if(isset($_GET['continue']) and $requer == 3 and $user->data['tutorial'] == 0){
            $planetrow->data['metal']     += 1000;
            $planetrow->data['crystal']   += 500;
            db::query("UPDATE {{table}} SET `tutorial` = '1' WHERE `id` = '".$user->data['id']."';", 'users');
			db::query("UPDATE {{table}} SET metal = metal + 1000, crystal = crystal + 500 WHERE `id` = '".$planetrow->data['id']."';", 'planets');
            $user->data['tutorial'] = 1;
            message('<ul><li>Следите чтобы ваши шахты были снабжены достаточным колличеством энергии. Если её не будет, то они не будут работать в полную силу.</li><li>В начале игры солнечная электростанция является основным источником энергии.</li><li>В начале игры важно развивать шахты металла и кристалла. Шахта дейтерия вам понадобиться позже, когда вы построите исследовательскую лабораторию и приступите к изучению вселенной.</li></ul>', '<p style="color:lime;">Поздравляем! Вы добились успеха в снабжении вашей планеты необходимыми ресурсами.</p>', '?set=tutorial&p=2', 20);
        }
        
        if ($requer == 3 and $user->data['tutorial'] == 0){
            $parse['button'] = '<input type="button" onclick="window.location = \'?set=tutorial&p=1&continue=1\'" value="Закончить" style="width:150px;height:30px;color:orange;"/></th>';
        }

        break;

    case 2:
        if($planetrow->data[$resource[3]] >= 2){
            $parse['deu_4'] = 'check';
            $requer++;
        }else{
            $parse['deu_4'] = 'none';
        }
        if($planetrow->data[$resource[14]] >= 2){
            $parse['robot_2'] = 'check';
            $requer++;
        }else{
            $parse['robot_2'] = 'none';
        }
        if($planetrow->data[$resource[21]] >= 1){
            $parse['han_1'] = 'check';
            $requer++;
        }else{
            $parse['han_1'] = 'none';
        }
        if($planetrow->data[$resource[401]] >= 1){
            $parse['lanz_1'] = 'check';
            $requer++;
        }else{
            $parse['lanz_1'] = 'none';
        }
        if(isset($_GET['continue']) and $requer == 4 and $user->data['tutorial'] == 1){
            db::query("UPDATE {{table}} SET `".$resource[401]."` = `".$resource[401]."` + 3 WHERE `id` = '".$planetrow->data['id']."';", 'planets');
            db::query("UPDATE {{table}} SET `tutorial` = '2' WHERE `id` = '".$user->data['id']."';", 'users');
            $user->data['tutorial'] = 2;

            header('Location: ?set=tutorial&p=3');
            die();
        }
        if ($requer == 4 and $user->data['tutorial'] == 1){
            $parse['button'] = '<input type="button" onclick="window.location = \'?set=tutorial&p=2&continue=1\'" value="Закончить" style="width:150px;height:30px;color:orange;"/></th>';
        }
        
        break;

    case 3:
        if($planetrow->data[$resource[1]] >= 10){
            $parse['met_10'] = 'check';
            $requer++;
        }else{
            $parse['met_10'] = 'none';
        }
        if($planetrow->data[$resource[2]] >= 7){
            $parse['cris_7'] = 'check';
            $requer++;
        }else{
            $parse['cris_7'] = 'none';
        }
        if($planetrow->data[$resource[3]] >= 5){
            $parse['deut_5'] = 'check';
            $requer++;
        }else{
            $parse['deut_5'] = 'none';
        }
        if(isset($_GET['continue']) and $requer == 3 and $user->data['tutorial'] == 2){
            $planetrow->data['metal'] += 5000;
            $planetrow->data['crystal'] += 2500;
            db::query("UPDATE {{table}} SET `tutorial` = '3' WHERE `id` = '".$user->data['id']."';", 'users');
            db::query("UPDATE {{table}} SET metal = metal + 5000, crystal = crystal + 2500 WHERE `id` = '".$planetrow->data['id']."';", 'planets');
            $user->data['tutorial'] = 3;
            header('Location: ?set=tutorial&p=4');
        }
        if($requer == 3 and $user->data['tutorial'] == 2){
            $parse['button'] = '<input type="button" onclick="window.location = \'?set=tutorial&p=3&continue=1\'" value="Закончить" style="width:150px;height:30px;color:orange;"/></th>';
        }

        break;

    case 4:
        if($planetrow->data[$resource[31]] >= 1){
            $parse['inv_1'] = 'check';
            $requer++;
        }else{
            $parse['inv_1'] = 'none';
        }
        if($user->data[$resource[115]] >= 2){
            $parse['comb_2'] = 'check';
            $requer++;
        }else{
            $parse['comb_2'] = 'none';
        }
        if($planetrow->data[$resource[202]] >= 1){
            $parse['navp_1'] = 'check';
            $requer++;
        }else{
            $parse['navp_1'] = 'none';
        }
        if(isset($_GET['continue']) and $requer == 3 and $user->data['tutorial'] == 3){
            $planetrow->data['deuterium'] += 2000;
            $user->data['credits']        += 10;
            db::query("UPDATE {{table}} SET `tutorial` = '4', credits = credits + 10 WHERE `id` = '".$user->data['id']."';", 'users');
            db::query("UPDATE {{table}} SET deuterium = deuterium + 2000 WHERE `id` = '".$planetrow->data['id']."';", 'planets');
            $user->data['tutorial'] = 4;

            header('Location: ?set=tutorial&p=5');
        }
        if($requer == 3 and $user->data['tutorial'] == 3){
            $parse['button'] = '<input type="button" onclick="window.location = \'?set=tutorial&p=4&continue=1\'" value="Закончить" style="width:150px;height:30px;color:orange;"/></th>';
        }

        break;

    case 5:
        if(isset($_POST['forum_content']) and $user->data['tutorial_value'] == 0 and strpos($_POST['forum_content'], 'forum.xnova.su') !== false && $user->data['tutorial'] == 4){
            db::query("UPDATE {{table}} SET `tutorial_value` = '1' WHERE `id` = '".$user->data['id']."';", 'users');
            $user->data['tutorial_value'] = 1;
        }
        if($planetrow->data['name'] != 'Главная планета' and $planetrow->data['name'] != 'Колония'){
            $parse['planet'] = 'check';
            $requer++;
        }else{
            $parse['planet'] = 'none';
        }
        if($user->data['tutorial_value'] == 1){
            $parse['forum'] = 'check';
            $requer++;
        }else{
            $parse['forum'] = 'none';
        }
        $buddyrow = db::query( "SELECT count(*) AS `total` FROM {{table}} WHERE (`sender` = '" . $user->data["id"]."' OR `owner` = '" . $user->data["id"]."') AND active = 1;", 'buddy', true );
        if($buddyrow['total'] >= 1){
            $parse['buddy'] = 'check';
            $requer++;
        }else{
            $parse['buddy'] = 'none';
        }
		$allyrow = db::query( "SELECT count(*) AS `total` FROM {{table}} WHERE `ally_id` = '" . $user->data["ally_id"]."';", 'users', true );
		if($user->data['ally_id'] != 0 and $allyrow['total'] >= 3){
			$parse['ally'] = 'check';
			$requer++;
        }else{
            $parse['ally'] = 'none';
        }
        if(isset($_GET['continue']) and $requer == 4 and $user->data['tutorial'] == 4){
            $user->data['credits'] += 10;
            db::query("UPDATE {{table}} SET `tutorial` = '5', tutorial_value = 0, `credits` = credits + 10 WHERE `id` = '".$user->data['id']."';", 'users');
            $user->data['tutorial'] = 5;

            header('Location: ?set=tutorial&p=6');
        }
        if($requer == 4 and $user->data['tutorial'] == 4){
            $parse['button'] = '<input type="button" onclick="window.location = \'?set=tutorial&p=5&continue=1\'" value="Закончить" style="width:150px;height:30px;color:orange;"/></th>';
        }

        break;

    case 6:
        if($planetrow->data[$resource[22]] >= 1 or $planetrow->data[$resource[23]] >= 1 or $planetrow->data[$resource[24]] >= 1){
            $parse['alm'] = 'check';
            $requer++;
        }else{
            $parse['alm'] = 'none';
        }
        if($user->data['tutorial_value'] > 0){
            $parse['mer'] = 'check';
            $requer++;
        }else{
            $parse['mer'] = 'none';
        }
        if(isset($_GET['continue']) and $requer == 2 and $user->data['tutorial'] == 5){
            $rand = mt_rand(22, 24);
            $planetrow->data[$resource[$rand]] += 1;
            db::query("UPDATE {{table}} SET `".$resource[$rand]."` = '".$planetrow->data[$resource[$rand]]."' WHERE `id` = '".$planetrow->data['id']."';", 'planets');
            db::query("UPDATE {{table}} SET `tutorial` = '6', tutorial_value = 0 WHERE `id` = '".$user->data['id']."';", 'users');
            $user->data['tutorial'] = 6;

            header('Location: ?set=tutorial&p=7');
        }
        if($requer == 2 and $user->data['tutorial'] == 5){
            $parse['button'] = '<input type="button" onclick="window.location = \'?set=tutorial&p=6&continue=1\'" value="Закончить" style="width:150px;height:30px;color:orange;"/></th>';
        }

        break;

    case 7:
        if($planetrow->data[$resource[210]] >= 1){
            $parse['sond'] = 'check';
            $requer++;
        }else{
            $parse['sond'] = 'none';
        }
        if($user->data['tutorial_value'] >= 1){
            $parse['esp'] = 'check';
            $requer++;
        }else{
            $parse['esp'] = 'none';
        }
        if(isset($_GET['continue']) and $requer == 2 and $user->data['tutorial'] == 6){
            db::query("UPDATE {{table}} SET `".$resource[210]."` = `".$resource[210]."` + 5 WHERE `id` = '".$planetrow->data['id']."';", 'planets');
            db::query("UPDATE {{table}} SET `tutorial` = '7', tutorial_value = 0 WHERE `id` = '".$user->data['id']."';", 'users');
            $user->data['tutorial'] = 1;

            header('Location: ?set=tutorial&p=8');
        }
        if($requer == 2 and $user->data['tutorial'] == 6){
            $parse['button'] = '<input type="button" onclick="window.location = \'?set=tutorial&p=7&continue=1\'" value="Закончить" style="width:150px;height:30px;color:orange;"/></th>';
        }

        break;

    case 8:

        if($user->data['tutorial_value'] >= 1){
            $parse['exp'] = 'check';
            $requer++;
        }else{
            $parse['exp'] = 'none';
        }
        if(isset($_GET['continue']) and $requer == 1 and $user->data['tutorial'] == 7){
            db::query("UPDATE {{table}} SET `".$resource[202]."` = `".$resource[202]."` + 5 , `".$resource[205]."` = `".$resource[205]."` + 3 WHERE `id` = '".$planetrow->data['id']."';", 'planets');
            db::query("UPDATE {{table}} SET `tutorial` = '8', tutorial_value = 0 WHERE `id` = '".$user->data['id']."';", 'users');
            $user->data['tutorial'] = 8;

            header('Location: ?set=tutorial&p=9');
        }
        if($requer == 1 and $user->data['tutorial'] == 7){
            $parse['button'] = '<input type="button" onclick="window.location = \'?set=tutorial&p=8&continue=1\'" value="Закончить" style="width:150px;height:30px;color:orange;"/></th>';
        }

        break;

    case 9:
        $planets = db::query( "SELECT count(*) AS `total` FROM {{table}} WHERE `id_owner` = '" . $user->data["id"]."';", 'planets', true );
        if($planets['total'] >= 2){
            $parse['colonia'] = 'check';
            $requer++;
        }else{
            $parse['colonia'] = 'none';
        }
        if(isset($_GET['continue']) and $requer == 1 and $user->data['tutorial'] == 8){
			if ($user->data['rpg_constructeur'] > time())
				$user->data['rpg_constructeur'] += 259200;
			else
				$user->data['rpg_constructeur'] = time() + 259200;

            db::query("UPDATE {{table}} SET `tutorial` = '9', rpg_constructeur = ".$user->data['rpg_constructeur']." WHERE `id` = '".$user->data['id']."';", 'users');
            $user->data['tutorial'] = 9;

            header('Location: ?set=tutorial&p=10');
        }
        if($requer == 1 and $user->data['tutorial'] == 8){
            $parse['button'] = '<input type="button" onclick="window.location = \'?set=tutorial&p=9&continue=1\'" value="Закончить" style="width:150px;height:30px;color:orange;"/></th>';
        }

        break;


    case 10:
        if($user->data['tutorial_value'] >= 1){
            $parse['rec'] = 'check';
            $requer++;
        }else{
            $parse['rec'] = 'none';
        }
        if(isset($_GET['continue']) and $requer == 1 and $user->data['tutorial'] == 9){
            db::query("UPDATE {{table}} SET `".$resource[209]."` = `".$resource[209]."` + 3 WHERE `id` = '".$planetrow->data['id']."';", 'planets');
            db::query("UPDATE {{table}} SET `tutorial` = '10', tutorial_value = 0 WHERE `id` = '".$user->data['id']."';", 'users');
            $user->data['tutorial'] = 10;

            header('Location: ?set=tutorial&p=finish');
        }
        if($requer == 1 and $user->data['tutorial'] == 9){
            $parse['button'] = '<input type="button" onclick="window.location = \'?set=tutorial&p=10&continue=1\'" value="Закончить" style="width:150px;height:30px;color:orange;"/></th>';
        }

        break;
}

for($e = 1; $e <= 10; $e++ ){
    if ($user->data['tutorial'] >= $e) {
        $parse['tut_'.$e ] = 'check';
    } else {
        $parse['tut_'.$e ] = 'none';
    }
}

$parse['p'] = $_GET['p'];
$parse['t'] = $user->data['tutorial'];

$Display->addTemplate('tutorial', 'tutorial.php');
$Display->assign('parse', $parse, 'tutorial');

display('', 'Обучение', false);
?>
