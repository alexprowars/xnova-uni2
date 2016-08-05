<?=$parse['BuildListScript'] ?>
<table width=700>
	<?=$parse['BuildList'] ?>
	<tr>
		<th>Занятость полей</th>
		<th>
			<font color="#00FF00"><?=$parse['planet_field_current'] ?></font> / <font color="#FF0000"><?=$parse['planet_field_max'] ?></font> Осталось <?=$parse['field_libre'] ?> свободных полей
		</th>
	</tr>

 <? $i = 0; foreach($parse['BuildingsList'] AS $build): $i++; ?>
<?=(($i%2 == 1) ? '<tr>' : '') ?>
<td class="j">
	<table width="350" style="border-spacing:0;<? if($parse['design'] == 0): ?>height:150px;<? endif; ?>"><tr>
	<? if($parse['design'] != 0): ?>
	<td class="l" width="120">
		<a href="?set=infos&gid=<?=$build['i'] ?>"><img src="<?=$dpath ?>gebaeude/<?=$build['i'] ?>.gif" align="top" width="120" height="120"  onmouseover="return overlib('<center><?=$build['descriptions'] ?></center>',LEFT,WIDTH,150,FGCOLOR,'#465673');" onmouseout="nd()"></a>
	</td>
	<? endif; ?>
	<th style="text-align:left;vertical-align:top;">
		<a href="?set=infos&gid=<?=$build['i'] ?>"><?=$build['n'] ?></a><br>
		<b>Уровень:</b> <u><?=$build['nivel'] ?></u><br>
		<?=$build['time'] ?>
		<br><?=$build['add'] ?>
	</th>
	</tr><tr>
	<td colspan="2" class="c" align="center"<? if($parse['design'] == 0): ?> style="height:26px;"<? endif; ?>><?=$build['price'] ?></td>
	</tr><tr>
	<td colspan="2" class="k" align="center" height="30"><?=$build['click'] ?></td>
	</tr></table>
</td>
<?=(($i%2 == 0) ? '</tr>' : '') ?>
<? endforeach; ?>
<? if($i%2 == 1): ?>
	<th style="border-spacing:0;height:100%;width:100%">&nbsp;</th></tr>
<? endif; ?>
</table>