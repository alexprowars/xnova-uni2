<br>
<form action="?set=admin&mode=options" method="post">
<input type="hidden" name="opt_save" value="1">
<table width="519" style="color:#FFFFFF">
<tbody>
<tr>
	<td class="c" colspan="2">Стартовая позиция нового игрока</td>
</tr><tr>
	<th>Галактика</th>
	<th><input name="LastSettedGalaxyPos" maxlength="1" size="5" value="<?=$parse['LastSettedGalaxyPos'] ?>" type="text"></th>
</tr><tr>
	<th>Система</th>
	<th><input name="LastSettedSystemPos" maxlength="1" size="5" value="<?=$parse['LastSettedSystemPos'] ?>" type="text"></th>
</tr><tr>
	<th colspan="2"><input value="Сохранить" type="submit"></th>
</tr>
</tbody>
</table>
</form>
