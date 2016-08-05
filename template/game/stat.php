<br>
<form method="post" action="?set=stat">
<table width="650">
<tr>
	<td class="c">Статистика: <?=$parse['stat_date'] ?></td>
</tr><tr>
	<th align="center">
	<table>
	<tr>
		<th width="16%" style="background-color: transparent;">&nbsp;</th>
		<th width="8%" style="background-color: transparent;">Какой</th>
		<th style="background-color: transparent;"><select name="who" onChange="javascript:document.forms[0].submit()"><?=$parse['who'] ?></select></th>
		<th width="8%" style="background-color: transparent;">по</th>
		<th style="background-color: transparent;"><select name="type" onChange="javascript:document.forms[0].submit()"><?=$parse['type'] ?></select></th>
		<th width="8%" style="background-color: transparent;">на месте</th>
		<th style="background-color: transparent;"><select name="range" onChange="javascript:document.forms[0].submit()"><?=$parse['range'] ?></select></th>
		<th width="16%" style="background-color: transparent;">&nbsp;</th>
	<tr>
	</table>
	</th>
</tr>
</table>
</form>
<table width="650">