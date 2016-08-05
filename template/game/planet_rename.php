<br />
<form action="?set=overview&mode=renameplanet&pl=<?=$parse['planet_id'] ?>" method="POST">
<table width=519>
<tr>
	<td class="c" colspan=3>Переименовать или покинуть планету</td>
</tr><tr>
	<th>Координаты</th>
	<th>Название</th>
	<th>Функции</th>
</tr><tr>
	<th><?=$parse['galaxy_galaxy'] ?>:<?=$parse['galaxy_system'] ?>:<?=$parse['galaxy_planet'] ?></th>
	<th><?=$parse['planet_name'] ?></th>
	<th><input type="submit" name="action" value="Покинуть колонию" alt="Покинуть колонию"></th>
</tr><tr>
	<th>Сменить название</th>
	<th><input type="text" name="newname" size=25 maxlength=20></th>
	<th><input type="submit" name="action" value="Сменить название"></th>
</tr>
</table>
</form>