<?php

/**
 * @author AlexPro
 * @copyright 2008 - 2011 XNova Game Group
 * @var $Display HSTemplateDisplay
 * @var $lang array
 * @var $user user
 * @var $requeriments array
 * @var $resource array
 * @var $planetrow planet
 * ICQ: 8696096, Skype: alexprowars, Email: alexprowars@gmail.com
 */

if(!defined("INSIDE")) die("attemp hacking");

$parse = array();

if (isset($_GET['id'])) {

	system::includeLang('techtree');

	$Element = intval($_GET['id']);

	if (isset($lang['arrow'][$Element])) {

		$parse['tree'] = array();

		foreach ($lang['arrow'][$Element] AS $arrow => $coords) {
			$req = 'R';

			$techs = explode('_', $arrow);

			if ($techs[0] != $techs[1]) {

				$Level 	= $requeriments[$techs[1]][$techs[0]];
				$lvl	= 0;

				if (isset($user->data[$resource[$techs[0]]])) {
					if ($user->data[$resource[$techs[0]]] >= $Level)
						$req = 'G';
					$lvl = $user->data[$resource[$techs[0]]];
				} elseif (isset($planetrow->data[$resource[$techs[0]]])) {
					if ($planetrow->data[$resource[$techs[0]]] >= $Level)
					$req = 'G';
					$lvl = $planetrow->data[$resource[$techs[0]]];
				}

				if ($lvl > $Level)
					$lvl = $Level;

			} else {
				if (IsTechnologieAccessible($user->data, $planetrow->data, $techs[0]))
					$req = 'G';
			}

			foreach ($coords AS $ar) {
				if ($ar[2] == 0)
					$parse['tree'][$ar[0]][$ar[1]]['element'] = array($techs[0],$req);
				else {
					if (!isset($parse['tree'][$ar[0]][$ar[1]]['arrow']))
						$parse['tree'][$ar[0]][$ar[1]]['arrow'] = '';

					$parse['tree'][$ar[0]][$ar[1]]['arrow'] .= (($ar[3] == 1) ? 'A' : 'L').$ar[2].$req.';';
				}

				if (isset($ar[4]))
					$parse['tree'][$ar[0]][$ar[1]]['level'] = array($ar[4],''.$lvl.'/'.$Level.'',$req);
			}


		}

		$parse['cols'] = $lang['arrow']['cols'][$Element];

		$Display->addTemplate('techtree', 'techtree_element.php');
	} else
		message('Данные не найдены!', 'Ошибка');

} else {

	foreach($lang['tech'] as $Element => $ElementName) {

		if ($Element >= 300 && $Element < 400)
			continue;

		if ($Element < 600){
			$pars            = array();
			$pars['tt_name'] = $ElementName;

			if (!isset($resource[$Element])) {
				$parse[]               = $pars;
			} else {
				if (isset($requeriments[$Element])) {
					$pars['required_list'] = "";

					foreach($requeriments[$Element] as $ResClass => $Level) {
						if ($ResClass != 700){
							if ( isset( $user->data[$resource[$ResClass]] ) && $user->data[$resource[$ResClass]] >= $Level) {
								$pars['required_list'] .= "<font color=\"#00ff00\">";
							} elseif ( isset($planetrow->data[$resource[$ResClass]] ) && $planetrow->data[$resource[$ResClass]] >= $Level) {
								$pars['required_list'] .= "<font color=\"#00ff00\">";
							} else {
								$pars['required_list'] .= "<font color=\"#ff0000\">";
							}
							$pars['required_list'] .= $lang['tech'][$ResClass] ." (". $lang['level'] ." ". $Level ."";

							if ( isset( $user->data[$resource[$ResClass]] ) && $user->data[$resource[$ResClass]] < $Level) {
								$minus = $Level - $user->data[$resource[$ResClass]];
								$pars['required_list'] .= " + <b>".$minus."</b>";
							} elseif ( isset($planetrow->data[$resource[$ResClass]] ) && $planetrow->data[$resource[$ResClass]] < $Level) {
								$minus = $Level - $planetrow->data[$resource[$ResClass]];
								$pars['required_list'] .= " + <b>".$minus."</b>";
							}
						} else {
							$pars['required_list'] .= $lang['tech'][$ResClass] ." (";
							if ($user->data['race'] != $Level)
								$pars['required_list'] .= "<font color=\"#ff0000\">".$lang['race'][$Level];
							else
								$pars['required_list'] .= "<font color=\"#00ff00\">".$lang['race'][$Level];
						}

						$pars['required_list'] .= ")</font><br>";
					}
				} else {
					$pars['required_list'] = "";
				}
				$pars['tt_info']   = $Element;
				$parse[]           = $pars;
			}
		}
	}

	$Display->addTemplate('techtree', 'techtree.php');
}

$Display->assign('parse', $parse, 'techtree');

display('', $lang['Tech'], false);

?>
