<br>
<form action="?set=options&mode=change" method="post">
<table width="519">
<tr>
<td class="c" colspan="2">Режим отпуска</td>
</tr><tr>
</tr><tr>
<th colspan=2>Режим отпуска включён. Отпуск минимум до:<br /><?=$parse['um_end_date'] ?></th>
</tr>
<tr>
	<th><?=$parse['username'] ?></th>
	<th><input name="db_character" size="20" value="<?=$parse['opt_usern_data'] ?>" type="hidden"><?=$parse['opt_usern_data'] ?></th>
</tr><tr>
	<th><a title="<?=$parse['vacations_tip'] ?>"><?=$parse['mode_vacations'] ?></a></th>
	<th><input name="urlaubs_modus"<?=$parse['opt_modev_data'] ?> type="checkbox" /></th>
</tr><tr>
	<th><a title="<?=$parse['deleteaccount_tip'] ?>"><?=$parse['deleteaccount'] ?></a></th>
	<th><input name="db_deaktjava"<?=$parse['opt_delac_data'] ?> type="checkbox" /></th>
</tr><tr>
<th colspan=2><input type="submit" value="Сохранить изменения" /></th>
</tr>
</table>
</form>