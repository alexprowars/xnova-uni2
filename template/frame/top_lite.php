<script type="text/javascript" language="javascript">
var ress = new Array(<?=$parse['metal'] ?>, <?=$parse['crystal'] ?>, <?=$parse['deuterium'] ?>);
var max = new Array(<?=$parse['metal_m'] ?>,<?=$parse['crystal_m'] ?>,<?=$parse['deuterium_m'] ?>);
var production = new Array(<?=$parse['metal_pm'] ?>, <?=$parse['crystal_pm'] ?>, <?=$parse['deuterium_pm'] ?>);
timeouts['res_count'] = window.setInterval("Res_count()",1000);
var serverTime = <?=$parse['time'] ?>000 - Djs + (timezone+8)*1800000;
</script>
<form name="ress" id="ress" style="display:none">
<input type="hidden" id="metall" value="0">
<input type="hidden" id="crystall" value="0">
<input type="hidden" id="deuterium" value="0">
<input type="hidden" id="bmetall" value="0">
<input type="hidden" id="bcrystall" value="0">
<input type="hidden" id="bdeuterium" value="0">
</form>
<table cellpadding="0" cellspacing="0" style="width:722px;border-spacing:3px;margin:3px 0 3px 0;">
<tr align="center">
	<td width="23%"><select size="1" onChange="eval('location=\''+this.options[this.selectedIndex].value+'\'');"><?=$parse['planetlist'] ?></select></td>
	<td width="11%"><font color="#FFFF00">Металл</font></td>
	<td width="11%"><font color="#FFFF00">Кристалл</font></td>
	<td width="11%"><font color="#FFFF00">Дейтерий</font></td>
	<td width="11%"><font color="#FFFF00">Энергия</font></td>
	<td width="11%"><font color="#FFFF00">Заряд</font></td>
	<td width="11%"><font color="#FFFF00">Кредиты</font></td>
	<td width="11%"><font color="#FFFF00">Сообщения</font></td>
</tr>
<tr align="center">
	<td><font color="#FFFF00">На складе</font></td>
	<td><div id="met">-</div></td>
	<td><div id="cry">-</div></td>
	<td><div id="deu">-</div></td>
	<td><?=$parse['energy_total'] ?></td>
	<td><?=$parse['energy_ak'] ?>%</td>
	<td><?=$parse['credits'] ?></td>
	<td><?=$parse['message'] ?></td>
</tr>
<tr align="center">
	<td><font color="#FFFF00">Вместимость</font></td>
	<td><?=$parse['metal_max'] ?></td>
	<td><?=$parse['crystal_max'] ?></td>
	<td><?=$parse['deuterium_max'] ?></td>
	<td><font color="#00ff00"><?=$parse['energy_max'] ?></font></td>
	<td colspan="3"><? if(isset($parse['tutorial']) && $parse['tutorial'] < 10): ?><a href="?set=tutorial" style="color:#FFFF00">[Обучение]</a><? endif; ?></td>
</tr>
</table>
<script>
$("#met").html(format(<?=$parse['metal'] ?>));
$("#cry").html(format(<?=$parse['crystal'] ?>));
$("#deu").html(format(<?=$parse['deuterium'] ?>));
Res_count();
</script>