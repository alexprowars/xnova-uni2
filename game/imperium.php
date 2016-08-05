<?php

/**
 * @author AlexPro
 * @copyright 2008 - 2011 XNova Game Group
 * @var $Display HSTemplateDisplay
 * @var $lang array
 * @var $user user
 * @var $planetrow planet
 * @var $resource array
 * @var $reslist array
 * @var $dpath string
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

if(!defined("INSIDE")) die("attemp hacking");

system::includeLang('imperium');

$planetsrow = db::query("SELECT * FROM {{table}} WHERE `id_owner` = '".$user->data['id']."';",'planets');

$planet = array();
$r		= array();
$r1		= array();
$parse  = $lang;

while ($p = db::fetch_array($planetsrow)) {
	$planet[] = $p;
}

$parse['mount'] = count($planet) + 3;
$parse['mount1'] = $parse['mount'] - 1;

$build_hangar_full = array();

$fleet_fly = array();

$fleets = db::query("SELECT * FROM {{table}} WHERE fleet_owner = ".$user->data['id']."", "fleets");

while ($fleet = db::fetch_assoc($fleets)) {

	if (!isset($fleet_fly[$fleet['fleet_start_galaxy'].':'.$fleet['fleet_start_system'].':'.$fleet['fleet_start_planet'].':'.$fleet['fleet_start_type']]))
		$fleet_fly[$fleet['fleet_start_galaxy'].':'.$fleet['fleet_start_system'].':'.$fleet['fleet_start_planet'].':'.$fleet['fleet_start_type']] = array();

	if ($fleet['fleet_target_owner'] == $user->data['id'] && !isset($fleet_fly[$fleet['fleet_end_galaxy'].':'.$fleet['fleet_end_system'].':'.$fleet['fleet_end_planet'].':'.$fleet['fleet_end_type']]))
		$fleet_fly[$fleet['fleet_end_galaxy'].':'.$fleet['fleet_end_system'].':'.$fleet['fleet_end_planet'].':'.$fleet['fleet_end_type']] = array();

	$temp_1 = explode(';', $fleet['fleet_array']);

	foreach ($temp_1 AS $a) {
		if (!$a) continue;
		
		$temp_2 = explode(',', $a);
		$temp_3 = explode('!', $temp_2[1]);

		if (!isset($fleet_fly[$fleet['fleet_start_galaxy'].':'.$fleet['fleet_start_system'].':'.$fleet['fleet_start_planet'].':'.$fleet['fleet_start_type']][$temp_2[0]])) {
			$fleet_fly[$fleet['fleet_start_galaxy'].':'.$fleet['fleet_start_system'].':'.$fleet['fleet_start_planet'].':'.$fleet['fleet_start_type']][$temp_2[0]] = 0;

			if ($fleet['fleet_target_owner'] == $user->data['id'])
				$fleet_fly[$fleet['fleet_end_galaxy'].':'.$fleet['fleet_end_system'].':'.$fleet['fleet_end_planet'].':'.$fleet['fleet_end_type']][$temp_2[0]] = 0;
		}

		$fleet_fly[$fleet['fleet_start_galaxy'].':'.$fleet['fleet_start_system'].':'.$fleet['fleet_start_planet'].':'.$fleet['fleet_start_type']][$temp_2[0]] -= $temp_3[0];

		if ($fleet['fleet_target_owner'] == $user->data['id'])
			$fleet_fly[$fleet['fleet_end_galaxy'].':'.$fleet['fleet_end_system'].':'.$fleet['fleet_end_planet'].':'.$fleet['fleet_end_type']][$temp_2[0]] += $temp_3[0];



		if ($fleet['fleet_target_owner'] == $user->data['id']) {
			if (!isset($build_hangar_full[$temp_2[0]]))
				$build_hangar_full[$temp_2[0]] = 0;
			
			$build_hangar_full[$temp_2[0]] += $temp_3[0];
		}
	}
}

$imperium = new planet();
$imperium->load_user_info($user);

foreach ($planet as $p) {

	$imperium->load_from_array($p);
	$imperium->PlanetResourceUpdate (time(), true);

	$p = $imperium->data;

	$p['field_max'] = CalculateMaxPlanetFields($p);

	@$parse['file_images'] 		.= '<th width=75><a href="?set=overview&cp=' . $p['id'] . '&amp;re=0"><img src="' . $dpath . 'planeten/small/s_' . $p['image'] . '.jpg" border="0" height="75" width="75"></a></th>';
	@$parse['file_names'] 		.= "<th>".$p['name']."</th>";
	@$parse['file_coordinates'] .= "<th>[<a href=\"?set=galaxy&mode=3&galaxy={$p['galaxy']}&system={$p['system']}\">{$p['galaxy']}:{$p['system']}:{$p['planet']}</a>]</th>";
	@$parse['file_fields'] 		.= '<th>'.$p['field_current'] . '/' . $p['field_max'].'</th>';
	@$parse['file_metal'] 		.= '<th>'. pretty_number($p['metal']) .'</th>';
	@$parse['file_crystal'] 	.= '<th>'. pretty_number($p['crystal']) .'</th>';
	@$parse['file_deuterium'] 	.= '<th>'. pretty_number($p['deuterium']) .'</th>';
	@$parse['file_energy'] 		.= '<th>'. pretty_number($p['energy_max'] - abs($p['energy_used'])) .'</th>';
	@$parse['file_zar'] 		.= '<th><font color="#00ff00">'.(round($p['energy_ak'] / ( 10000 * pow((1.1), $p['ak_station'])  * $p['ak_station'] + 1), 2) * 100).'</font>%</th>';

	@$parse['file_fields_c'] 	+= $p['field_current'];
	@$parse['file_fields_t'] 	+= $p['field_max'];
	@$parse['file_metal_t'] 	+= $p['metal'];
	@$parse['file_crystal_t'] 	+= $p['crystal'];
	@$parse['file_deuterium_t'] += $p['deuterium'];
	@$parse['file_energy_t'] 	+= $p['energy_max'] - abs($p['energy_used']);

	@$parse['file_metal_ph'] 	.= '<th>'.pretty_number($p['metal_perhour']).'</th>';
	@$parse['file_crystal_ph'] 	.= '<th>'.pretty_number($p['crystal_perhour']).'</th>';
	@$parse['file_deuterium_ph'].= '<th>'.pretty_number($p['deuterium_perhour']).'</th>';

	@$parse['file_metal_ph_t'] 	+= $p['metal_perhour'];
	@$parse['file_crystal_ph_t'] += $p['crystal_perhour'];
	@$parse['file_deuterium_ph_t'] += $p['deuterium_perhour'];

	@$parse['file_metal_p'] 	.= '<th><font color="#00FF00">'.($p['metal_mine_porcent']*10).'</font>%</th>';
	@$parse['file_crystal_p'] 	.= '<th><font color="#00FF00">'.($p['crystal_mine_porcent']*10).'</font>%</th>';
	@$parse['file_deuterium_p'] .= '<th><font color="#00FF00">'.($p['deuterium_sintetizer_porcent']*10).'</font>%</th>';
	@$parse['file_solar_p'] 	.= '<th><font color="#00FF00">'.($p['solar_plant_porcent']*10).'</font>%</th>';
	@$parse['file_fusion_p'] 	.= '<th><font color="#00FF00">'.($p['fusion_plant_porcent']*10).'</font>%</th>';
	@$parse['file_solar2_p'] 	.= '<th><font color="#00FF00">'.($p['solar_satelit_porcent']*10).'</font>%</th>';

	$build_hangar = array();

	if ($p['b_building'] != 0) {
		$build_hangar_id = explode(';', $p['b_building_id']);

		foreach ($build_hangar_id AS $arr) {
			$temp = explode(',', $arr);

			$build_hangar[$temp[0]] = $temp[1];

			if (!isset($build_hangar_full[$temp[0]]))
				$build_hangar_full[$temp[0]]  = $temp[1];
			else
				$build_hangar_full[$temp[0]] += $temp[1];
		}
	}

	if ($p['b_hangar_id'] != '') {

		$build_hangar_id = explode(';', $p['b_hangar_id']);

		foreach ($build_hangar_id AS $arr) {
			if (!$arr)
				continue;
			
			$temp = explode(',', $arr);
			
			if (!isset($build_hangar[$temp[0]]))
				$build_hangar[$temp[0]]  = $temp[1];
			else
				$build_hangar[$temp[0]] += $temp[1];

			if (!isset($build_hangar_full[$temp[0]]))
				$build_hangar_full[$temp[0]]  = $temp[1];
			else
				$build_hangar_full[$temp[0]] += $temp[1];
		}
	}

	if ($p['b_tech_id'] != 0) {
		$build_hangar_full[$p['b_tech_id']] = $user->data[$resource[$p['b_tech_id']]] + 1;
	}

	foreach ($resource as $i => $res) {

        if (!isset($r[$i]))
            $r[$i] = '';
        if (!isset($r1[$i]))
            $r1[$i] = 0;

		if (in_array($i, $reslist['build'])) {
			$r[$i] .= ($p[$resource[$i]] == 0) ? '<th>'.((isset($build_hangar[$i])) ? ' <font color=#00FF00>'.$build_hangar[$i].'</font>' : '-').'</th>' : '<th>'.$p[$resource[$i]].''.((isset($build_hangar[$i])) ? ' <font color=#00FF00>-> '.$build_hangar[$i].'</font>' : '').'</th>';
			if ($r1[$i] < $p[$resource[$i]])
				$r1[$i] = $p[$resource[$i]];
		} elseif (in_array($i, $reslist['fleet'])) {

			$r[$i] .= '<th>';

			if ($p[$resource[$i]] == 0 && !isset($build_hangar[$i]) && !isset($fleet_fly[$p['galaxy'].':'.$p['system'].':'.$p['planet'].':'.$p['planet_type']][$i]))
				$r[$i] .= '-';
			else {
				if ($p[$resource[$i]] >= 0)
					$r[$i] .= $p[$resource[$i]];
				if (isset($build_hangar[$i]))
					$r[$i] .= ' <font color=#00FF00>+'.$build_hangar[$i].'</font>';
				if (isset($fleet_fly[$p['galaxy'].':'.$p['system'].':'.$p['planet'].':'.$p['planet_type']][$i]))
					$r[$i] .= ' <font color=yellow>'.(($fleet_fly[$p['galaxy'].':'.$p['system'].':'.$p['planet'].':'.$p['planet_type']][$i] > 0) ? '+' : '').''.$fleet_fly[$p['galaxy'].':'.$p['system'].':'.$p['planet'].':'.$p['planet_type']][$i].'</font>';
				$r[$i] .= '</th>';
			}

			$r1[$i] += $p[$resource[$i]];
		} elseif (in_array($i, $reslist['defense'])) {
			$r[$i] .= ($p[$resource[$i]] == 0) ? '<th>'.((isset($build_hangar[$i])) ? ' <font color=#00FF00>+'.$build_hangar[$i].'</font>' : '-').'</th>' : '<th>'.$p[$resource[$i]].''.((isset($build_hangar[$i])) ? ' <font color=#00FF00>+'.$build_hangar[$i].'</font>' : '').'</th>';
			$r1[$i] += $p[$resource[$i]];
		}
	}
}

	$parse['file_metal_t']          = pretty_number($parse['file_metal_t']);
	$parse['file_crystal_t']        = pretty_number($parse['file_crystal_t']);
	$parse['file_deuterium_t']      = pretty_number($parse['file_deuterium_t']);
	$parse['file_energy_t']         = pretty_number($parse['file_energy_t']);

	$parse['file_metal_ph_t']       = pretty_number($parse['file_metal_ph_t']);
	$parse['file_crystal_ph_t']     = pretty_number($parse['file_crystal_ph_t']);
	$parse['file_deuterium_ph_t']   = pretty_number($parse['file_deuterium_ph_t']);

	$parse['file_kredits']          = pretty_number($user->data['credits']);

    $parse['building_row']          = '';
    $parse['fleet_row']             = '';
    $parse['defense_row']           = '';
    $parse['technology_row']        = '';

foreach ($reslist['build'] as $a => $i)
{
	$parse['building_row'] .= "<tr><th colspan=\"2\">".$lang['tech'][$i]."</th>".$r[$i]."<th>".$planetrow->data[$resource[$i]]." (".$r1[$i].")</th></tr>";
}

foreach ($reslist['fleet'] as $a => $i)
{
	$parse['fleet_row'] .= "<tr><th colspan=\"2\">".$lang['tech'][$i]."</th>".$r[$i]."<th>".$r1[$i]."".((isset($build_hangar_full[$i])) ? ' <font color=#00FF00>+'.$build_hangar_full[$i].'</font>' : '')."</th></tr>";
}

foreach ($reslist['defense'] as $a => $i)
{
	$parse['defense_row'] .= "<tr><th colspan=\"2\">".$lang['tech'][$i]."</th>".$r[$i]."<th>".$r1[$i]."".((isset($build_hangar_full[$i])) ? ' <font color=#00FF00>+'.$build_hangar_full[$i].'</font>' : '')."</th></tr>";
}

foreach ($reslist['tech'] as $a => $i) 
{
	$parse['technology_row'] .= "<tr><th colspan=\"".($parse['mount']-1)."\">".$lang['tech'][$i]."</th><th><font color=#FFFF00>".$user->data[$resource[$i]]. "</font>".((isset($build_hangar_full[$i])) ? ' <font color=#00FF00>-> '.$build_hangar_full[$i].'</font>' : '')."</th></tr>";
}

$Display->addTemplate('imperium', 'imperium.php');
$Display->assign('parse', $parse, 'imperium');

display('', 'Империя', false);

?>
