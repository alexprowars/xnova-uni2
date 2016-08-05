<br>
<table width="710">
<tr>
	<td class="c" colspan="3"><?=$parse['off_points'] ?> <?=$parse['alv_points'] ?></td>
</tr>
	<? if (!isset($_COOKIE['vkid'])): ?>
<tr>
  	<th><img src="/images/officiers/bigcredits.jpg" width="120" height="120"></th>
  	<th align="left" valign="top" style="text-align:left;"><u>Кредиты</u> (<a href="?set=infokredits"><font color="lime">Получить</font></a>)<br><br>Инженеры берут за свою работу только межгалактичесие кредиты. В зависимости от суммы контракта работают на вас в течении всего времени найма.<table style="font-weight: normal;"><tr><td><img src="/images/officiers/smalcredts.gif"></td><td><br><font color="#84CFEF">При помощи кредитов можно нанять инженеров.</font></td></tr></table></th>
  	<th><a href="?set=infokredits"><font color="lime">Получить</font></a></th>
</tr>
<? endif; ?>
<? foreach($parse['list'] AS $list): ?>
<form method="POST" action="?set=officier">
<tr>
	<th width=120 valign="top">
		<img src="images/officiers/<?=$list['off_id'] ?>.jpg" align="top" width="120" height="120" />
	</th>
	<th align="left" style="text-align:left;" valign="top"><font color="#ff8900"><u><?=$list['off_tx_lvl'] ?></u> (<?=$list['off_lvl'] ?>)</font><?=$list['off_desc'] ?></th>
	<th align=center valign=top><?=$list['off_link'] ?></th>
</tr>
</form>
<? endforeach; ?>
</table>