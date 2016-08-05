<?php

if(!defined("INSIDE")) die("attemp hacking");

system::includeLang('admin');
$parse = $lang;

	if ($user->data['authlevel'] >= 3) {

		if (isset($_GET['delete'])) {
			db::query("DELETE FROM {{table}} WHERE `error_id` = '".intval($_GET['delete'])."'", 'errors');
		} elseif (isset($_GET['deleteall'])) {
			db::query("TRUNCATE TABLE {{table}}", 'errors');
		}

		$parse['errors_list'] = '';
        
		$query = db::query("SELECT * FROM {{table}}", 'errors');
		$i = 0;
		while ($e = db::fetch_array($query)) {
			$i++;
			$parse['errors_list'] .= "
			<tr>
				<th rowspan=2>". $e['error_id'] ."</th>
				<th>". $e['error_type'] ." [<a href=?delete=". $e['error_id'] .">X</a>]</th>
				<th>". $e['error_sender'] ."</th>
				<th>" . datezone('d/m/Y h:i:s', $e['error_time']) . "</th>
			</tr><tr>
				<td class=b colspan=4 width=500>" . htmlspecialchars($e['error_text']) . "</td>
			</tr>";
		}
		$parse['errors_list'] .= "<tr>
			<th class=b colspan=4>". $i ." ". $lang['adm_er_nbs'] ."</th>
		</tr>";

        $Display->addTemplate('errors', 'admin/errors.php');
        $Display->assign('parse', $parse, 'errors');

		display('', "Ошибки SQL", false, true);
	} else {
		message( $lang['sys_noalloaw'], $lang['sys_noaccess'] );
	}
?>
