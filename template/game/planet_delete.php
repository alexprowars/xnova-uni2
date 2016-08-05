<br>
<form action="?set=overview&mode=renameplanet&pl=<?=$parse['planet_id'] ?>" method=POST>
<table width=519>
<tr>
	<td class=c colspan=3>Система безопасности</td>
</tr><tr>
	<th colspan=3>Подтвердите удаление планеты <?=$parse['galaxy_galaxy'] ?>:<?=$parse['galaxy_system'] ?>:<?=$parse['galaxy_planet'] ?> вводом пароля</th>
</tr><tr>
	<th>Пароль</th>
	<th><input type=password name=pw></th>
	<th><input type=submit name=action value="Удалить колонию"></th><input type="hidden" name="kolonieloeschen" value="1">
</tr>
</table>
<input type=hidden name=deleteid value ="<?=$parse['planet_id'] ?>">
</form>