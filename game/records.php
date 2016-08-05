<?php

/**
 * @author AlexPro
 * @copyright 2008 - 2011 XNova Game Group
 * @var $Display HSTemplateDisplay
 * @var $lang array
 * @var $game_config array
 * @var $user user
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

if(!defined("INSIDE")) die("attemp hacking");

if(file_exists("cache/CacheRecords.php"))
    require_once("cache/CacheRecords.php");
else
    $RecordsArray	= array();

$Builds         = array();
$MoonsBuilds    = array();
$Techno         = array();
$Fleet          = array();
$Defense        = array();

foreach($RecordsArray as $ElementID => $ElementIDArray) {
    if ($ElementID >=   1 && $ElementID <=  39 || $ElementID == 44) {
        $Builds[$lang['tech'][$ElementID]]	= array(
            'winner'	=> ($ElementIDArray['maxlvl'] != 0) ? $ElementIDArray['username'] : '-',
            'count'		=> ($ElementIDArray['maxlvl'] != 0) ? pretty_number( $ElementIDArray['maxlvl'] ) : '-',
        );
    } elseif ($ElementID >=  41 && $ElementID <= 99 && $ElementID != 44) {
        $MoonsBuilds[$lang['tech'][$ElementID]]	= array(
            'winner'	=> ($ElementIDArray['maxlvl'] != 0) ? $ElementIDArray['username'] : '-',
            'count'		=> ($ElementIDArray['maxlvl'] != 0) ? pretty_number( $ElementIDArray['maxlvl'] ) : '-',
        );
    } elseif ($ElementID >= 101 && $ElementID <= 199) {
        $Techno[$lang['tech'][$ElementID]]	= array(
            'winner'	=> ($ElementIDArray['maxlvl'] != 0) ? $ElementIDArray['username'] : '-',
            'count'		=> ($ElementIDArray['maxlvl'] != 0) ? pretty_number( $ElementIDArray['maxlvl'] ) : '-',
        );
    } elseif ($ElementID >= 201 && $ElementID <= 399) {
        $Fleet[$lang['tech'][$ElementID]]	= array(
            'winner'	=> ($ElementIDArray['maxlvl'] != 0) ? $ElementIDArray['username'] : '-',
            'count'		=> ($ElementIDArray['maxlvl'] != 0) ? pretty_number( $ElementIDArray['maxlvl'] ) : '-',
        );
    } elseif ($ElementID >= 401 && $ElementID <= 599) {
        $Defense[$lang['tech'][$ElementID]]	= array(
            'winner'	=> ($ElementIDArray['maxlvl'] != 0) ? $ElementIDArray['username'] : '-',
            'count'		=> ($ElementIDArray['maxlvl'] != 0) ? pretty_number( $ElementIDArray['maxlvl'] ) : '-',
        );
    }
}

$Records	= array(
    'Постройки'	        => $Builds,
    'Лунные постройки'	=> $MoonsBuilds,
    'Исследования'	    => $Techno,
    'Флот'	            => $Fleet,
    'Оборона'	        => $Defense,
);

$parse = array(
    'Records'	 	=> $Records,
    'update'		=> $game_config['stat_update'],
);

$Display->addTemplate('records', 'records.php');
$Display->assign('parse', $parse, 'records');

display('', 'Таблица рекордов', false);
 
 ?>
