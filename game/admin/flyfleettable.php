<?php

if(!defined("INSIDE")) die("attemp hacking");

if ($user->data['authlevel'] < 3)
	message( $lang['sys_noalloaw'], $lang['sys_noaccess'] );

	system::includeLang('admin/fleets');

    function BuildFlyingFleetTable () {
        global $lang;

        $table = array();

        $FlyingFleets = db::query ("SELECT * FROM {{table}} ORDER BY `fleet_end_time` ASC;", 'fleets');
        while ( $CurrentFleet = db::fetch_assoc( $FlyingFleets ) ) {

            $Bloc['Id']       = $CurrentFleet['fleet_id'];
            $Bloc['Mission']  = CreateFleetPopupedMissionLink ( $CurrentFleet, $lang['type_mission'][ $CurrentFleet['fleet_mission'] ], '' );
            $Bloc['Mission'] .= "<br>". (($CurrentFleet['fleet_mess'] == 1) ? "R" : "A" );

            $Bloc['Fleet']    = CreateFleetPopupedFleetLink ( $CurrentFleet, $lang['tech'][200], '' );
            $Bloc['St_Owner'] = "[". $CurrentFleet['fleet_owner'] ."]<br>". $CurrentFleet['fleet_owner_name'];
            $Bloc['St_Posit'] = "[".$CurrentFleet['fleet_start_galaxy'] .":". $CurrentFleet['fleet_start_system'] .":". $CurrentFleet['fleet_start_planet'] ."]<br>". ( ($CurrentFleet['fleet_start_type'] == 1) ? "[P]": (($CurrentFleet['fleet_start_type'] == 2) ? "D" : "L"  )) ."";
            $Bloc['St_Time']  = datezone('H:i:s d/n/Y', $CurrentFleet['fleet_start_time']);
            if (!empty($CurrentFleet['fleet_target_owner'])) {
                $Bloc['En_Owner'] = "[". $CurrentFleet['fleet_target_owner'] ."]<br>". $CurrentFleet['fleet_target_owner_name'];
            } else {
                $Bloc['En_Owner'] = "";
            }
            $Bloc['En_Posit'] = "[".$CurrentFleet['fleet_end_galaxy'] .":". $CurrentFleet['fleet_end_system'] .":". $CurrentFleet['fleet_end_planet'] ."]<br>". ( ($CurrentFleet['fleet_end_type'] == 1) ? "[P]": (($CurrentFleet['fleet_end_type'] == 2) ? "D" : "L"  )) ."";
            if ($CurrentFleet['fleet_mission'] == 15) {
                $Bloc['Wa_Time']  = datezone('H:i:s d/n/Y', $CurrentFleet['fleet_stay_time']);
            } else {
                $Bloc['Wa_Time']  = "";
            }
            $Bloc['En_Time']  = datezone('H:i:s d/n/Y', $CurrentFleet['fleet_end_time']);

            $table[] = $Bloc;
        }
        return $table;
    }

    $Display->addTemplate('fleets', 'admin/fleets.php');
    $Display->assign('flt_table', BuildFlyingFleetTable(), 'fleets');

	display ( '', $lang['flt_title'], false, true);
?>