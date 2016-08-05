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
	<td width="20%"><select onChange="eval('location=\''+this.options[this.selectedIndex].value+'\'');"><?=$parse['planetlist'] ?></select></td>
	<td width="10%"><a onmouseover="return overlib('<table width=150><tr><td width=50%>КПД:</td><td align=right><?=$parse['metal_mp'] ?>%</td></tr><tr><td>Производство:</td><td align=right><?=$parse['metal_ph'] ?></td></tr><tr><td>День:</td><td align=right><?=$parse['metal_pd'] ?></td></tr></table>');" onmouseout="return nd();"><img src="<?=$dpath ?>images/metall.gif" width="42"></a></td>
	<td width="10%"><a onmouseover="return overlib('<table width=150><tr><td width=50%>КПД:</td><td align=right><?=$parse['crystal_mp'] ?>%</td></tr><tr><td>Производство:</td><td align=right><?=$parse['crystal_ph'] ?></td></tr><tr><td>День:</td><td align=right><?=$parse['crystal_pd'] ?></td></tr></table>');" onmouseout="return nd();"><img src="<?=$dpath ?>images/kristall.gif" width="42"></a></td>
	<td width="10%"><a onmouseover="return overlib('<table width=150><tr><td width=50%>КПД:</td><td align=right><?=$parse['deuterium_mp'] ?>%</td></tr><tr><td>Производство:</td><td align=right><?=$parse['deuterium_ph'] ?></td></tr><tr><td>День:</td><td align=right><?=$parse['deuterium_pd'] ?></td></tr></table>');" onmouseout="return nd();"><img src="<?=$dpath ?>images/deuterium.gif" width="42"></a></td>
	<td width="10%"><img src="<?=$dpath ?>images/energie.gif" width="42"></td>
	<td width="10%"><a onmouseover="return overlib('<center>Вместимость:<br><?=$parse['ak'] ?></center>',WIDTH,150);" onmouseout="return nd();"><img src="/images/<?=$parse['energy'] ?>" width="42"></a></td>
	<td width="10%"><a href="<? if (!isset($_COOKIE['vkid'])): ?>?set=infokredits<? else: ?>#<? endif; ?>" onmouseover="return overlib('<table width=550><tr><td align=center width=14%>Адмирал<br><img src=/images/admiral<?=$parse['admiral_ikon'] ?>.gif></td><td align=center width=14%>Инженер<br><img src=/images/ingenieur<?=$parse['ingenieur_ikon'] ?>.gif></td><td align=center width=14%>Геолог<br><img src=/images/geologe<?=$parse['geologe_ikon'] ?>.gif></td><td align=center width=14%>Технократ<br><img src=/images/technokrat<?=$parse['technokrat_ikon'] ?>.gif></td><td align=center width=14%>Архитектор<br><img src=/images/architector<?=$parse['architector_ikon'] ?>.gif></td><td align=center width=14%>Метафизик<br><img src=/images/meta<?=$parse['meta_ikon'] ?>.gif></td><td align=center width=14%>Наёмник<br><img src=/images/komandir<?=$parse['komandir_ikon'] ?>.gif></td></tr><tr><td align=center><?=$parse['admiral'] ?></td><td align=center><?=$parse['ingenieur'] ?></td><td align=center><?=$parse['geologe'] ?></td><td align=center><?=$parse['technokrat'] ?></td><td align=center><?=$parse['architector'] ?></td><td align=center><?=$parse['rpgmeta'] ?></td><td align=center><?=$parse['komandir'] ?></td></tr></table>',LEFT,WIDTH,450,FGCOLOR,'#465673');" onmouseout="return nd();"><img src="<?=$dpath ?>images/kredits.gif" width="42" alt="Получить кредиты"></a></td>
	<td width="10%"><img src="<?=$dpath ?>images/message.gif" width="42"></td>
</tr>
<tr align="center">
	<td>&nbsp;</td>
	<td><font color="#FFFF00">Металл</font></td>
	<td><font color="#FFFF00">Кристалл</font></td>
	<td><font color="#FFFF00">Дейтерий</font></td>
	<td><font color="#FFFF00">Энергия</font></td>
	<td><font color="#FFFF00">Заряд</font></td>
	<td><font color="#FFFF00">Кредиты</font></td>
	<td><font color="#FFFF00">Сообщения</font></td>
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