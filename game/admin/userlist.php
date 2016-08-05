<?php

if(!defined("INSIDE")) die("attemp hacking");

if ($user->data['authlevel'] > 2) {

    system::includeLang('admin');

    if (isset($_GET['cmd']) && $_GET['cmd'] == 'sort') {
        if ($_GET['type'] == 'id')
            $TypeSort = "u.id";
        elseif ($_GET['type'] == 'username')
            $TypeSort = "u.username";
        elseif ($_GET['type'] == 'email')
            $TypeSort = "ui.email";
        elseif ($_GET['type'] == 'user_lastip')
            $TypeSort = "u.user_lastip";
        elseif ($_GET['type'] == 'register_time')
            $TypeSort = "ui.register_time";
        elseif ($_GET['type'] == 'onlinetime')
            $TypeSort = "u.onlinetime";
        elseif ($_GET['type'] == 'banaday')
            $TypeSort = "u.banaday";
        else
            $TypeSort = "u.id";
    } else {
        $TypeSort = "u.id";
    }

    $page = @intval($_GET['p']);
	if ($page < 1) $page = 1;

    $query   = db::query("SELECT u.`id`, u.`username`, ui.`email`, u.`user_lastip`, ui.`register_time`, u.`onlinetime`, u.`banaday` FROM {{table}}users u, {{table}}users_inf ui WHERE ui.id = u.id ORDER BY ".$TypeSort." LIMIT ".(($page - 1) * 25).", 25", '');

    $parse                 = $lang;
    $parse['adm_ul_table'] = array();
    $Color                 = "lime";
    $PrevIP                = '';

    while ($u = db::fetch_assoc ($query) ) {
        if ($PrevIP != "") {
            if ($PrevIP == $u['user_lastip']) {
                $Color = "red";
            } else {
                $Color = "lime";
            }
        }
        $Bloc['adm_ul_data_id']     = $u['id'];
        $Bloc['adm_ul_data_name']   = $u['username'];
        $Bloc['adm_ul_data_mail']   = $u['email'];
        $Bloc['adm_ul_data_adip']   = "<font color=\"".$Color."\">". long2ip($u['user_lastip']) ."</font>";
        $Bloc['adm_ul_data_regd']   = date ( "d.m.Y H:i:s", $u['register_time'] );
        $Bloc['adm_ul_data_lconn']  = date ( "d.m.Y", $u['onlinetime'] )."<br>".date ( "H:i:s", $u['onlinetime'] );
        $Bloc['adm_ul_data_banna']  = ( $u['banaday'] > 0 ) ? "<a href=\"#\" title=\"". date ( "d.m.Y H:i:s", $u['banaday']) ."\">". $lang['adm_ul_yes'] ."</a>" : $lang['adm_ul_no'];
        $Bloc['adm_ul_data_detai']  = "";//<a href='?set=admin&mode=userlist&cmd=dele&user=".$u['id']."'><img src=\"/images/r1.png\"></a>";
        $Bloc['adm_ul_data_actio']  = "<img src=\"/images/r1.png\">";
        $PrevIP                     = $u['user_lastip'];
        $parse['adm_ul_table'][]    = $Bloc ;
    }

    $parse['adm_ul_count'] = PageSelector ($game_config['users_amount'], 25, '?set=admin&mode=userlist', $page);

    $Display->addTemplate('userlist', 'admin/userlist.php');
    $Display->assign('parse', $parse, 'userlist');

    display( '', $lang['adm_ul_title'], false, true);
} else {
    message( $lang['sys_noalloaw'], $lang['sys_noaccess'] );
}

?>