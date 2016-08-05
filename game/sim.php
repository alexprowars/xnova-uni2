<?php

/**
 * @author AlexPro
 * @copyright 2008 - 2011 XNova Game Group
 * @var $Display HSTemplateDisplay
 * @var $reslist array
 * @var $resource array
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

$r = (isset($_GET['r'])) ? $_GET['r'] : '';
$r = explode(";", $r);

$parse = array();
$parse['att'] = array();
$parse['def'] = array();

foreach($r AS $row){
    if ($row != '') {
        @$Element = explode(",", $row);
        @$Count = explode("!", $Element[1]);
        if (isset($Count[1]))
            @$parse['def'][$Element[0]] = array('c' => $Count[0], 'l' => $Count[1]);
    }
}

$res = array_merge($reslist['fleet'], $reslist['defense'], $reslist['tech']);

foreach ($res AS $id) {
    if (isset($planetrow->data[$resource[$id]]) && $planetrow->data[$resource[$id]] > 0)
        $parse['att'][$id] = array('c' => $planetrow->data[$resource[$id]], 'l' => ((isset($user->data['fleet_'.$id])) ? $user->data['fleet_'.$id] : 0));

    if (isset($user->data[$resource[$id]]) && $user->data[$resource[$id]] > 0)
       $parse['att'][$id] = array('c' => $user->data[$resource[$id]]);
}

$Display->addTemplate('sim', 'sim.php');
$Display->assign('parse', $parse, 'sim');

display('', 'Симулятор', false);
 
 ?>
